<?php
$pos = (strpos($_SERVER['PHP_SELF'], "/scouting.php"));
if ($pos !== false)
{
    die("You cannot access this page directly!");
}

#OK. total overhaul, hitting this one first . let's use some real logic.
#Basically what we want to do is loop through every scouting activity in activities table
# and handle just those- why loop tribes table? just get the ones where activity = scheduler event
#Next up, we want to determine what direction we are scouting in, see how far our scouts can move
# and reveal squares that they are capable of getting to, then we return scout to base
# and update map table to uncover squares, determine if anything is found..
# do away with checking each direction individually, just loop on each row from scouts where that order is set.
#scouts table contains scoutid,tribeid,actives(integer),direction(n.s.e.w.ne,nw,se,sw),mounted (Y,N),orders(P,L,M)
$res = $db->Execute("SELECT * from $dbtables[scouts]");
db_op_result($res,__LINE__,__FILE__);
while(!$res->EOF)
{
    $scouting = $res->fields;
    $tribe_id = $scouting['tribeid'];
    $num_scouts = $scouting['actives'];
    $direction = $scouting['direction'];
    $mounted = $scouting['mounted'];
    $orders = $scouting['orders']; //P,L,M and I guess we'll switch it when we need . right now they just map so 'M'
    $party = $scouting['scoutid'];
    //TEMPORARY FOR NOW
    $orders = 'M'; //mappers - db table needs this added to the enum

    //OK let's find out what hex this scouting party is on
    $sql = $db->Prepare("SELECT hex_id,tribeid,clanid,goods_tribe from $dbtables[tribes] WHERE tribeid =?");
    $info = $db->Execute($sql,array($tribe_id));
    db_op_result($info,__LINE__,__FILE__);
    $tribeinfo = $info->fields;
    $cur_hex = $tribeinfo['hex_id'];
    $clan_id = $tribeinfo['clanid'];
    $goods_tribe = $tribeinfo['goods_tribe'];
    //we set them to recognizable vars so we dont have to carry the load of an array, and we can recognize vars where needed
   //OK we got it all ready, now lets get the skill information and see how far we can go..
    $sql = $db->Prepare("SELECT level FROM $dbtables[skills] WHERE abbr = 'sct' and tribeid =?");
    //note- we go where abbr = sct cause it's fewer rows to check once sql indexed properly
    $info = $db->Execute($sql,array($tribe_id));
    db_op_result($info,__LINE__,__FILE__);
    $skl = $info->fields;
    $skill_level = $skl['level'];
    //OK, let's see how many move points we have available, then we loop through hexes until we're outta moves
     //handle bonus  mapping gets 7 mounted 3 on foot
     //prospectors and spies take longer, once we have this coded up.
     $bonus = array('mount'=>7,'foot'=>3);
     if($orders == 'P')
     {
        $bonus = array('mount'=>5,'foot'=>2); //prospectors
     }
     if($orders == 'L')
     {
        $bonus = array('mount'=>4,'foot'=>1);//L?? Spies? who knows?
     }
     if($mounted == 'Y')
     {
         $movepts = $bonus['mount'] + $skill_level; //riding horsies, lose carry cap, move much faster
     }
     else
     {
         $movepts = $bonus['foot'] + ($skill_level/2); //walk out and walk back
     }
     //OK Now we start the main loop and handle events if a move occurs for each square (finding shit)
     while($movepts > 0)
     {
        $sql = $db->Prepare("SELECT * from $dbtables[hexes] WHERE hex_id =?");
        $query = $db->Execute($sql,array($cur_hex));
        db_op_result($query,__LINE__,__FILE__);
        $hexinfo = $query->fields;
        $move_to = $hexinfo[$direction];//hex id we're gonna deal with
        $move_cost = $hexinfo['move'];
        if($move_cost <= $movepts)
        {
             //we move to this hex, deduct cost from points
             $movepts = $movepts - $move_cost;
            // echo "Scouts for $tribe_id moving $direction checking $cur_hex<br>";
             //update the map
             $query = $db->Execute("UPDATE $dbtables[mapping] SET clanid_{$clan_id} = '1' WHERE"
                           ." hex_id = '$move_to' AND clanid_{$clan_id} < 1");
             db_op_result($query,__LINE__,__FILE__);
             $query = $db->Execute("UPDATE $dbtables[mapping] SET admin_0000 = '1' WHERE"
                                ." hex_id = '$move_to'");
             db_op_result($query,__LINE__,__FILE__);
             //check to see if there's anyone there//////////////
             $ct = $db->Execute("SELECT tribeid FROM $dbtables[tribes] WHERE "
                                ."hex_id = '$move_to' AND clanid != '$clan_id'");
             db_op_result($ct,__LINE__,__FILE__);
             $count = $ct->fields;
             if($count['tribeid'] > 0)
             {
                 $logtext = "Scouting: $tribe_id's scouting party $party headed $direction detected ";
                 $logtext .= "$count[tribeid] ";
                 playerlog($tribeid,,$clan_id,'SCOUTING',$month['count'],$year['count'],$logtext,$dbtables);
              }//end scouts found other player
              //OK scout attrition - do we lose any? lets calculate
              $scoutfind = abs(ceil(mt_rand( 1,500 )));//whole positive integer
              if($scoutfind >  (490 + $skill_level))// level 10 scouts never desert or get lost
              {
                  $numbermissed = abs(floor(mt_rand( 1, $num_scouts)));//get a minimum number
                  if($num_scouts <= $numbermissed)
                  {
                      $sql = $db->Prepare("DELETE FROM $dbtables[scouts] WHERE scoutid = ?");
                      $logmessage = "Scouting Party LOST! All scouts from $direction Party ID $party have been lost or deserted you!";
                      playerlog($tribeid,,$clan_id,'SCOUTING',$month['count'],$year['count'],$logmessage,$dbtables);
                  }
                  elseif($numbermissed > 0)
                  {
                      $sql = $db->Prepare("UPDATE $dbtables[scouts] SET actives = actives - $numbermissed WHERE scoutid = ?");
                      $logmessage = "It appears that $numbermissed scouts did not return from $direction Party ID $party.";
                      playerlog($tribeid,,$clan_id,'SCOUTING',$month['count'],$year['count'],$logmessage,$dbtables);
                  }

               }
               elseif($scoutfind < $skill_level)//we find something
               {
                   $whatfind = (rand(1,100) + $skill_level);
                    if( $whatfind > 75 )
                    {
                        $find = $db->Execute("SELECT COUNT(*) AS count,long_name,proper FROM $dbtables[product_table] "
                                            ."WHERE skill_abbr != 'shw' "
                                            ."AND long_name != 'totem' "
                                            ."AND skill_level < '$skill_level' "
                                            ."AND include = 'Y' group by long_name");
                        db_op_result($find,__LINE__,__FILE__);
                        $findwhat = $find->fields;
                        $what = abs(ceil(mt_rand( 0, $findwhat['count'])));
                        $many = abs(ceil(mt_rand( 1, $skill_level )));
                        $logmessage = "$direction Scouting: We have found $many $findwhat[proper].";
                        playerlog($tribeid,,$clan_id,'SCOUTING',$month['count'],$year['count'],$logmessage,$dbtables);
                        $query = $db->Execute("UPDATE $dbtables[products] "
                                    ."SET amount = amount + $many "
                                    ."WHERE long_name = '$findwhat[long_name]' "
                                    ."AND tribeid = '$goods_tribe'");
                        db_op_result($query,__LINE__,__FILE__);
                    }
                    else
                    {

                        $what = abs(ceil(mt_rand( 0, $whatfind)));
                        $many = abs(ceil(mt_rand( 1, $skill_level + 5)));
                        $found = $db->Execute("SELECT type FROM $dbtables[livestock] "
                                             ."WHERE tribeid = '$goods_tribe' "
                                             ."LIMIT $what, 1");
                        db_op_result($found,__LINE__,__FILE__);
                        $findwhat = $found->fields;
                        $logmessage = "$direction Scouting: We have found $many $findwhat[proper].";
                        playerlog($tribeid,,$clan_id,'SCOUTING',$month['count'],$year['count'],$logmessage,$dbtables);
                        $query = $db->Execute("UPDATE $dbtables[livestock] "
                                    ."SET amount = amount + $many "
                                    ."WHERE type = '$findwhat[type]' "
                                    ."AND tribeid = '$goods_tribe'");
                       db_op_result($query,__LINE__,__FILE__);
                    }
               }//end scouts find something
           $cur_hex = $move_to;   //set up the next move to hex and re-query
        }//end handling this hex
        else
        {
           $movepts = 0;
        }
     }//end hex move loop

     $res->MoveNext();
}//End of main loop


?>