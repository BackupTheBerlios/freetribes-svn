<?php
$pos = (strpos($_SERVER['PHP_SELF'], "/prospecting.php"));
if ($pos !== false)
{
    die("You cannot access this page directly!");
}
//This file is not yet implemented in the game-
$res = $db->Execute("SELECT * FROM $dbtables[tribes]");
 db_op_result($res,__LINE__,__FILE__);
while( !$res->EOF )
{
    $tribe = $res->fields;
    $skill = $db->Execute("SELECT * FROM $dbtables[skills] "
                         ."WHERE tribeid = '$tribe[tribeid]' "
                         ."AND abbr = 'sct'");
     db_op_result($skill,__LINE__,__FILE__);
    $skillinfo = $skill->fields;
    $movement = $skillinfo[level];
    $nsct = $db->Execute("SELECT * FROM $dbtables[scouts] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND direction = 'n' "
                        ."AND orders = 'M' "
                        ."LIMIT 1");
     db_op_result($nsct,__LINE__,__FILE__);
    $moved = 0;
    while( !$nsct->EOF )
    {
        $scout = $nsct->fields;
        if( $scout[mounted] == 'Y' )
        {
            $movepts = (7 + $movement)/2;
        }
        else
        {
            $movepts = (3 + ($movement/2))/2;
        }
        $scoutedhex = $tribe[hex_id];
        $direction = $scout[direction];
        $move = 0;
        while( $movepts > 0 )
        {
            $hex = $db->Execute("SELECT * FROM $dbtables[hexes] "
                               ."WHERE hex_id = '$scoutedhex'");
            db_op_result($hex,__LINE__,__FILE__);
            $hexinfo = $hex->fields;
            $hex = $db->Execute("SELECT * FROM $dbtables[hexes] "
                               ."WHERE hex_id = '$hexinfo[$direction]'");
             db_op_result($hex,__LINE__,__FILE__);
            $hexinfo = $hex->fields;
            if( $movepts < $hexinfo[move] )
            {
                $movepts = 0;
                $moved = 1;
            }
            if( $move > 0 )
            {
                $movepts -= $hexinfo[move];
            }
            else
            {
                $move++;
            }
            /////////////move the scouts 1 tile//////////////
            $scoutedhex = $hexinfo[hex_id];
            //////////////add the tile to the map/////////////////
            if( !$moved )
            {
                $scoutfind = rand( 1,500 );
                if( $scoutfind < $skillinfo[level] )
                {
                        $query = $db->Execute("INSERT INTO $dbtables[logs] "
                                    ."VALUES("
                                    ."'',"
                                    ."'$month[count]',"
                                    ."'$year[count]',"
                                    ."'$tribe[clanid]',"
                                    ."'$tribe[tribeid]',"
                                    ."'SCOUT',"
                                    ."'$stamp',"
                                    ."'North Scouting: We have found $many $foundwhat[proper].')");
                    db_op_result($query,__LINE__,__FILE__);
                    $query = $db->Execute("UPDATE $dbtables[mapping] "
                                    ."SET clanid_$tribe[clanid] = $hexinfo[prospect] "
                                    ."WHERE hex_id = '$scoutedhex'");
                        db_op_result($query,__LINE__,__FILE__);
                    }
                    else
                    {
                        $what = rand( 0, $findwhat[count] );
                        $many = rand( 1, $skillinfo[level] + 5 );
                        $found = $db->Execute("SELECT * FROM $dbtables[livestock] "
                                             ."WHERE tribeid = '$tribe[goods_tribe]' "
                                             ."LIMIT $what, 1");
                         db_op_result($found,__LINE__,__FILE__);
                        $findwhat = $found->fields;
                        $query = $db->Execute("INSERT INTO $dbtables[logs] "
                                    ."VALUES("
                                    ."'',"
                                    ."'$month[count]',"
                                    ."'$year[count]',"
                                    ."'$tribe[clanid]',"
                                    ."'$tribe[tribeid]',"
                                    ."'SCOUT',"
                                    ."'$stamp',"
                                    ."'North Scouting: We have found $many $findwhat[type].')");
                   db_op_result($query,__LINE__,__FILE__);
                    $query = $db->Execute("UPDATE $dbtables[livestock] "
                                    ."SET amount = amount + $many "
                                    ."WHERE type = '$findwhat[type]' "
                                    ."AND tribeid = '$tribe[goods_tribe]'");
                  db_op_result($query,__LINE__,__FILE__);
                }
                }
                    $query = $db->Execute("UPDATE $dbtables[mapping] "
                                ."SET `clanid_$tribe[clanid]` = '1' "
                                ."WHERE hex_id = '$hexinfo[hex_id]' "
                                ."AND `clanid_$tribe[clanid]` < 1");
                    db_op_result($query,__LINE__,__FILE__);
                    $query = $db->Execute("UPDATE $dbtables[mapping] "
                                ."SET `admin_0000` = '1' "
                                ."WHERE hex_id = '$hexinfo[hex_id]' "
                                ."AND `admin_0000` < 1");
                    db_op_result($query,__LINE__,__FILE__);
                    ///////////////check to see if there's anyone there//////////////
                    $ct = $db->Execute("SELECT COUNT(*) as count FROM $dbtables[tribes] "
                                      ."WHERE hex_id = '$scoutedhex' "
                                      ."AND clanid != '$tribe[clanid]'");
                     db_op_result($ct,__LINE__,__FILE__);
                    $count = $ct->fields;
                    if( $count[count] > 0 )
                    {
                        $squat = $db->Execute("SELECT * FROM $dbtables[tribes] "
                                             ."WHERE hex_id = '$scoutedhex' "
                                             ."AND clanid != '$tribe[clanid]'");
                        db_op_result($squat,__LINE__,__FILE__);
                        $logtext = "North Scouting: $tribe[tribeid]'s $scout[direction] scouts detected ";
                        while( !$squat->EOF )
                        {
                            $squatters = $squat->fields;
                            $logtext .= "$squatters[tribeid] ";
                            $squat->MoveNext();
                        }
                        $query = $db->Execute("INSERT INTO $dbtables[logs] "
                                    ."VALUES("
                                    ."'',"
                                    ."'$month[count]',"
                                    ."'$year[count]',"
                                    ."'$tribe[clanid]',"
                                    ."'$tribe[tribeid]',"
                                    ."'SCOUT',"
                                    ."'$stamp',"
                                    ."'$logtext')");
                         db_op_result($query,__LINE__,__FILE__);
                    }
                }
            }
            $nsct->MoveNext();
        }

        $nesct = $db->Execute("SELECT * FROM $dbtables[scouts] "
                             ."WHERE tribeid = '$tribe[tribeid]' "
                             ."AND direction = 'ne' "
                             ."AND orders = 'M' "
                             ."LIMIT 1");
         db_op_result($nesct,__LINE__,__FILE__);
        $moved = 0;
        while( !$nesct->EOF )
        {
            $scout = $nesct->fields;
            if( $scout[mounted] == 'Y' )
            {
                $movepts = 7 + $movement;
            }
            else
            {
                $movepts = 3 + ($movement/2);
            }
            $scoutedhex = $tribe[hex_id];
            $direction = $scout[direction];
            $move = 0;
            while( $movepts > 0 )
            {
                $hex = $db->Execute("SELECT * FROM $dbtables[hexes] "
                                   ."WHERE hex_id = '$scoutedhex'");
                db_op_result($hex,__LINE__,__FILE__);
                $hexinfo = $hex->fields;
                $hex = $db->Execute("SELECT * FROM $dbtables[hexes] "
                                   ."WHERE hex_id = '$hexinfo[$direction]'");
                 db_op_result($hex,__LINE__,__FILE__);
                $hexinfo = $hex->fields;
                if( $movepts < $hexinfo[move] )
                {
                    $movepts = 0;
                    $moved = 1;
                }
                if( $move > 0 )
                {
                    $movepts -= $hexinfo[move];
                }
                else
                {
                    $move++;
                }
                /////////////move the scouts 1 tile//////////////
                $scoutedhex = $hexinfo[hex_id];
                //////////////add the tile to the map/////////////////
                if( !$moved )
                {
                    $scoutfind = rand( 1, 500 );
                    if( $scoutfind >  ( 490 + $skillinfo[level] ) )
                    {
                        $numbermissed = rand( 1, $scout[actives] );
                        if( $scout[actives] == $numbermissed )
                        {
                            $query = $db->Execute("DELETE FROM $dbtables[scouts] "
                                        ."WHERE scoutid = '$scout[scoutid]'");
                            db_op_result($query,__LINE__,__FILE__);
                            $query = $db->Execute("INSERT INTO $dbtables[logs] "
                                        ."VALUES("
                                        ."'',"
                                        ."'$month[count]',"
                                        ."'$year[count]',"
                                        ."'0000',"
                                        ."'0000.00',"
                                        ."'SCOUT',"
                                        ."'$stamp',"
                                        ."'Scouting: $tribe[tribeid] lost $numbermissed scouts from $scout[scoutid].')");
                               db_op_result($query,__LINE__,__FILE__);
                        }
                        else
                        {
                            $query = $db->Execute("UPDATE $dbtables[scouts] "
                                        ."SET actives = actives - $numbermissed "
                                        ."WHERE scoutid = '$scout[scoutid]'");
                            db_op_result($query,__LINE__,__FILE__);
                            $query = $db->Execute("INSERT INTO $dbtables[logs] "
                                        ."VALUES("
                                        ."'',"
                                        ."'$month[count]',"
                                        ."'$year[count]',"
                                        ."'$tribe[clanid]',"
                                        ."'$tribe[tribeid]',"
                                        ."'SCOUT',"
                                        ."'$stamp',"
                                        ."'Scouting: It appears that $numbermissed scouts did not return.')");
                             db_op_result($query,__LINE__,__FILE__);
                            $query = $db->Execute("INSERT INTO $dbtables[logs] "
                                        ."VALUES("
                                        ."'',"
                                        ."'$month[count]',"
                                        ."'$year[count]',"
                                        ."'0000',"
                                        ."'0000.00',"
                                        ."'SCOUT',"
                                        ."'$stamp',"
                                        ."'Scouting: $tribe[tribeid] lost $numbermissed scouts from $scout[scoutid].')");
                           db_op_result($query,__LINE__,__FILE__);
                        }
                    }
                    elseif( $scoutfind < $skillinfo[level] )
                    {
                        $whatfind = ( rand( 1, 100 ) + $skillinfo[level] );
                        if( $whatfind > 75 )
                        {
                            $find = $db->Execute("SELECT COUNT(distinct long_name) AS count FROM $dbtables[product_table] "
                                                ."WHERE skill_abbr != 'shw' "
                                                ."AND long_name != 'totem' "
                                                ."AND skill_level < '$skillinfo[level]' "
                                                ."AND include = 'Y'");
                            db_op_result($find,__LINE__,__FILE__);
                            $findwhat = $find->fields;
                            $what = rand( 0, $findwhat[count] );
                            $many = rand( 1, $skillinfo[level] );
                            $found = $db->Execute("SELECT * FROM $dbtables[product_table] "
                                                 ."WHERE skill_abbr != 'shw' "
                                                 ."AND long_name != 'totem' "
                                                 ."AND skill_level < '$skillinfo[level]' "
                                                 ."AND include = 'Y' "
                                                 ."LIMIT $what, 1");
                             db_op_result($found,__LINE__,__FILE__);
                            $foundwhat = $found->fields;
           $query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','SCOUT','$stamp','Northeast Scouting: We have found $many $foundwhat[proper].')");
           db_op_result($query,__LINE__,__FILE__);
           $query = $db->Execute("UPDATE $dbtables[products] SET amount = amount + $many WHERE long_name = '$foundwhat[long_name]' AND tribeid = '$tribe[goods_tribe]'");
           db_op_result($query,__LINE__,__FILE__);
           }
         else{
           $what = rand( 0, 6 );
           $many = rand( 1, $skillinfo[level] + 5 );
           $found = $db->Execute("SELECT * FROM $dbtables[livestock] WHERE tribeid = '$tribe[goods_tribe]' LIMIT $what, 1");
           db_op_result($found,__LINE__,__FILE__);
           $findwhat = $found->fields;
           $query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','SCOUT','$stamp','Northeast Scouting: We have found $many $findwhat[type].')");
            db_op_result($query,__LINE__,__FILE__);
           $query = $db->Execute("UPDATE $dbtables[livestock] SET amount = amount + $many WHERE type = '$findwhat[type]' AND tribeid = '$tribe[goods_tribe]'");
          db_op_result($query,__LINE__,__FILE__);
           }
    }

  $query = $db->Execute("INSERT INTO $dbtables[map_table] VALUES('','$tribe[tribeid]','$tribe[clanid]','$hexinfo[hex_id]','$stamp','$tribe[tribeid]')");
 db_op_result($query,__LINE__,__FILE__);
  $query = $db->Execute("INSERT INTO $dbtables[movement_log] VALUES('','$tribe[tribeid]','$tribe[clanid]','$hexinfo[hex_id]','$stamp')");
 db_op_result($query,__LINE__,__FILE__);
  $query = $db->Execute("UPDATE $dbtables[mapping] "
                                ."SET `clanid_$tribe[clanid]` = '1' "
                                ."WHERE hex_id = '$hexinfo[hex_id]' "
                                ."AND `clanid_$tribe[clanid]` < 1");
        db_op_result($query,__LINE__,__FILE__);
                    $query = $db->Execute("UPDATE $dbtables[mapping] "
                                ."SET `admin_0000` = '1' "
                                ."WHERE hex_id = '$hexinfo[hex_id]' "
                                ."AND `admin_0000` < 1");  ///////////////check to see if there's anyone there//////////////
   db_op_result($query,__LINE__,__FILE__);
  $ct = $db->Execute("SELECT COUNT(*) as count FROM $dbtables[tribes] WHERE hex_id = '$scoutedhex' AND clanid != '$tribe[clanid]'");
   db_op_result($ct,__LINE__,__FILE__);
  $count = $ct->fields;
  if($count[count] > 0){
  $squat = $db->Execute("SELECT * FROM $dbtables[tribes] WHERE hex_id = '$scoutedhex' AND clanid != '$tribe[clanid]'");
    db_op_result($squat,__LINE__,__FILE__);
  $logtext = "Northeast Scouting: $tribe[tribeid]'s $scout[direction] scouts detected ";
  while(!$squat->EOF){
  $squatters = $squat->fields;
  $logtext .= "$squatters[tribeid] ";
  $squat->MoveNext();
  }
  $query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','SCOUT','$stamp','$logtext')");
   db_op_result($query,__LINE__,__FILE__);
  }
  }
  }
  $nesct->MoveNext();
  }

  $esct = $db->Execute("SELECT * FROM $dbtables[scouts] "
                      ."WHERE tribeid = '$tribe[tribeid]' "
                      ."AND direction = 'e' "
                      ."AND orders = 'M' "
                      ."LIMIT 1");
  db_op_result($esct,__LINE__,__FILE__);
 $moved = 0;
  while(!$esct->EOF){
  $scout = $esct->fields;
    if($scout[mounted] == 'Y'){
     $movepts = 7 + $movement;
     }
     else{
     $movepts = 3 + ($movement/2);
     }
 $scoutedhex = $tribe[hex_id];
 $direction = $scout[direction];
 $move = 0;
 while($movepts > 0){
  $hex = $db->Execute("SELECT * FROM $dbtables[hexes] WHERE hex_id = '$scoutedhex'");
  db_op_result($hex,__LINE__,__FILE__);
  $hexinfo = $hex->fields;
  $hex = $db->Execute("SELECT * FROM $dbtables[hexes] WHERE hex_id = '$hexinfo[$direction]'");
  db_op_result($hex,__LINE__,__FILE__);
  $hexinfo = $hex->fields;
  if($movepts < $hexinfo[move]){
  $movepts = 0;
  $moved = 1;
   }
  if($move > 0){
   $movepts -= $hexinfo[move];
   }
  else{
   $move++;
   }
  /////////////move the scouts 1 tile//////////////
  $scoutedhex = $hexinfo[hex_id];
  //////////////add the tile to the map/////////////////
  if(!$moved){
   $scoutfind = rand(1,500);
      if( $scoutfind >  ( 490 + $skillinfo[level] ) )
   {
       $numbermissed = rand( 1, $scout[actives] );
       if( $scout[actives] == $numbermissed )
       {
           $query = $db->Execute("DELETE FROM $dbtables[scouts] "
                       ."WHERE scoutid = '$scout[scoutid]'");
           db_op_result($query,__LINE__,__FILE__);
           $query = $db->Execute("INSERT INTO $dbtables[logs] "
                       ."VALUES("
                       ."'',"
                       ."'$month[count]',"
                       ."'$year[count]',"
                       ."'0000',"
                       ."'0000.00',"
                       ."'SCOUT',"
                       ."'$stamp',"
                       ."'Scouting: $tribe[tribeid] lost $numbermissed scouts from $scout[scoutid].')");
                  db_op_result($query,__LINE__,__FILE__);
       }
       else
       {
           $query = $db->Execute("UPDATE $dbtables[scouts] "
                       ."SET actives = actives - $numbermissed "
                       ."WHERE scoutid = '$scout[scoutid]'");
            db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("INSERT INTO $dbtables[logs] "
                        ."VALUES("
                        ."'',"
                        ."'$month[count]',"
                        ."'$year[count]',"
                        ."'$tribe[clanid]',"
                        ."'$tribe[tribeid]',"
                        ."'SCOUT',"
                        ."'$stamp',"
                        ."'Scouting: It appears that $numbermissed scouts did not return.')");
                    db_op_result($query,__LINE__,__FILE__);
           $query = $db->Execute("INSERT INTO $dbtables[logs] "
                       ."VALUES("
                       ."'',"
                       ."'$month[count]',"
                       ."'$year[count]',"
                       ."'0000',"
                       ."'0000.00',"
                       ."'SCOUT',"
                       ."'$stamp',"
                       ."'Scouting: $tribe[tribeid] lost $numbermissed scouts from $scout[scoutid].')");
              db_op_result($query,__LINE__,__FILE__);
       }
   }
   elseif($scoutfind < $skillinfo[level]){
         $whatfind = (rand(1,100) + $skillinfo[level]);
         if($whatfind > 75){
           $find = $db->Execute("SELECT COUNT(distinct long_name) AS count FROM $dbtables[product_table] "
                               ."WHERE skill_abbr != 'shw' "
                               ."AND long_name != 'totem' "
                               ."AND skill_level < '$skillinfo[level]' "
                               ."AND include = 'Y'");
             db_op_result($find,__LINE__,__FILE__);
           $findwhat = $find->fields;
             $what = rand(0, $findwhat[count] );
             $many = rand(1, $skillinfo[level]);
           $found = $db->Execute("SELECT * FROM $dbtables[product_table] "
                                ."WHERE skill_abbr != 'shw' "
                                ."AND long_name != 'totem' "
                               ."AND skill_level < '$skillinfo[level]' "
                                ."AND include = 'Y' "
                                ."LIMIT $what, 1");
                db_op_result($found,__LINE__,__FILE__);
           $foundwhat = $found->fields;
           $query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','SCOUT','$stamp','East Scouting: We have found $many $foundwhat[proper].')");
             db_op_result($query,__LINE__,__FILE__);
           $query = $db->Execute("UPDATE $dbtables[products] SET amount = amount + $many WHERE long_name = '$foundwhat[long_name]' AND tribeid = '$tribe[goods_tribe]'");
           db_op_result($query,__LINE__,__FILE__);
           }
         else{
           $what = rand( 0, 6 );
           $many = rand( 1, $skillinfo[level] + 5 );
           $found = $db->Execute("SELECT * FROM $dbtables[livestock] WHERE tribeid = '$tribe[goods_tribe]' LIMIT $what, 1");
            db_op_result($found,__LINE__,__FILE__);
           $findwhat = $found->fields;
           $query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','SCOUT','$stamp','East Scouting: We have found $many $findwhat[type].')");
          db_op_result($query,__LINE__,__FILE__);
           $query = $db->Execute("UPDATE $dbtables[livestock] SET amount = amount + $many WHERE type = '$findwhat[type]' AND tribeid = '$tribe[goods_tribe]'");
           db_op_result($query,__LINE__,__FILE__);
           }
       }


  $query = $db->Execute("INSERT INTO $dbtables[map_table] VALUES('','$tribe[tribeid]','$tribe[clanid]','$hexinfo[hex_id]','$stamp','$tribe[tribeid]')");
 db_op_result($query,__LINE__,__FILE__);
  $query = $db->Execute("INSERT INTO $dbtables[movement_log] VALUES('','$tribe[tribeid]','$tribe[clanid]','$hexinfo[hex_id]','$stamp')");
db_op_result($query,__LINE__,__FILE__);
 $query = $db->Execute("UPDATE $dbtables[mapping] "
                                ."SET `clanid_$tribe[clanid]` = '1' "
                                ."WHERE hex_id = '$hexinfo[hex_id]' "
                                ."AND `clanid_$tribe[clanid]` < 1");
    db_op_result($query,__LINE__,__FILE__);
                    $query = $db->Execute("UPDATE $dbtables[mapping] "
                                ."SET `admin_0000` = '1' "
                                ."WHERE hex_id = '$hexinfo[hex_id]' "
                                ."AND `admin_0000` < 1");  ///////////////check to see if there's anyone there//////////////
     db_op_result($query,__LINE__,__FILE__);
  $ct = $db->Execute("SELECT COUNT(*) as count FROM $dbtables[tribes] WHERE hex_id = '$scoutedhex' AND clanid != '$tribe[clanid]'");
  db_op_result($ct,__LINE__,__FILE__);
  $count = $ct->fields;
  if($count[count] > 0){
  $squat = $db->Execute("SELECT * FROM $dbtables[tribes] WHERE hex_id = '$scoutedhex' AND clanid != '$tribe[clanid]'");
    db_op_result($squat,__LINE__,__FILE__);
  $logtext = "East Scouting: $tribe[tribeid]'s $scout[direction] scouts detected ";
  while(!$squat->EOF){
  $squatters = $squat->fields;
  $logtext .= "$squatters[tribeid] ";
  $squat->MoveNext();
  }
  $query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','SCOUT','$stamp','$logtext')");
     db_op_result($query,__LINE__,__FILE__);
  }
  }
  }
  $esct->MoveNext();
  }

  $sesct = $db->Execute("SELECT * FROM $dbtables[scouts] "
                       ."WHERE tribeid = '$tribe[tribeid]' "
                       ."AND direction = 'se' "
                       ."AND orders = 'M' "
                       ."LIMIT 1");
     db_op_result($sesct,__LINE__,__FILE__);
 $moved = 0;
  while(!$sesct->EOF){
  $scout = $sesct->fields;
    if($scout[mounted] == 'Y'){
     $movepts = 7 + $movement;
     }
     else{
     $movepts = 3 + ($movement/2);
     }
 $scoutedhex = $tribe[hex_id];
 $direction = $scout[direction];
 $move = 0;
 while($movepts > 0){
  $hex = $db->Execute("SELECT * FROM $dbtables[hexes] WHERE hex_id = '$scoutedhex'");
  db_op_result($hex,__LINE__,__FILE__);
  $hexinfo = $hex->fields;
  $hex = $db->Execute("SELECT * FROM $dbtables[hexes] WHERE hex_id = '$hexinfo[$direction]'");
  db_op_result($hex,__LINE__,__FILE__);
  $hexinfo = $hex->fields;
  if($movepts < $hexinfo[move]){
  $movepts = 0;
  $moved = 1;
   }
  if($move > 0){
   $movepts -= $hexinfo[move];
   }
  else{
   $move++;
   }
  /////////////move the scouts 1 tile//////////////
  $scoutedhex = $hexinfo[hex_id];
  //////////////add the tile to the map/////////////////
  if(!$moved){
   $scoutfind = rand(1,500);
      if( $scoutfind >  ( 490 + $skillinfo[level] ) )
   {
       $numbermissed = rand( 1, $scout[actives] );
       if( $scout[actives] == $numbermissed )
       {
           $query = $db->Execute("DELETE FROM $dbtables[scouts] "
                       ."WHERE scoutid = '$scout[scoutid]'");
           db_op_result($query,__LINE__,__FILE__);
           $query = $db->Execute("INSERT INTO $dbtables[logs] "
                       ."VALUES("
                       ."'',"
                       ."'$month[count]',"
                       ."'$year[count]',"
                       ."'0000',"
                       ."'0000.00',"
                       ."'SCOUT',"
                       ."'$stamp',"
                       ."'Scouting: $tribe[tribeid] lost $numbermissed scouts from $scout[scoutid].')");
          db_op_result($query,__LINE__,__FILE__);
       }
       else
       {
           $query = $db->Execute("UPDATE $dbtables[scouts] "
                       ."SET actives = actives - $numbermissed "
                       ."WHERE scoutid = '$scout[scoutid]'");
             db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("INSERT INTO $dbtables[logs] "
                        ."VALUES("
                        ."'',"
                        ."'$month[count]',"
                        ."'$year[count]',"
                        ."'$tribe[clanid]',"
                        ."'$tribe[tribeid]',"
                        ."'SCOUT',"
                        ."'$stamp',"
                        ."'Scouting: It appears that $numbermissed scouts did not return.')");
                     db_op_result($query,__LINE__,__FILE__);
           $query = $db->Execute("INSERT INTO $dbtables[logs] "
                       ."VALUES("
                       ."'',"
                       ."'$month[count]',"
                       ."'$year[count]',"
                       ."'0000',"
                       ."'0000.00',"
                       ."'SCOUT',"
                       ."'$stamp',"
                       ."'Scouting: $tribe[tribeid] lost $numbermissed scouts from $scout[scoutid].')");
             db_op_result($query,__LINE__,__FILE__);
       }
   }
   elseif($scoutfind < $skillinfo[level]){
         $whatfind = (rand(1,100) + $skillinfo[level]);
         if($whatfind > 75){
           $find = $db->Execute("SELECT COUNT(distinct long_name) AS count FROM $dbtables[product_table] "
                               ."WHERE skill_abbr != 'shw' "
                               ."AND long_name != 'totem' "
                               ."AND skill_level < '$skillinfo[level]' "
                               ."AND include = 'Y'");
             db_op_result($find,__LINE__,__FILE__);
           $findwhat = $find->fields;
             $what = rand(0, $findwhat[count] );
             $many = rand(1, $skillinfo[level]);
           $found = $db->Execute("SELECT * FROM $dbtables[product_table] "
                                ."WHERE skill_abbr != 'shw' "
                                ."AND include = 'Y' "
                                ."AND long_name != 'totem' "
                               ."AND skill_level < '$skillinfo[level]' "
                                ."LIMIT $what, 1");
             db_op_result($found,__LINE__,__FILE__);
           $foundwhat = $found->fields;
           $query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','SCOUT','$stamp','Southeast Scouting: We have found $many $foundwhat[proper].')");
             db_op_result($query,__LINE__,__FILE__);
           $query = $db->Execute("UPDATE $dbtables[products] SET amount = amount + $many WHERE long_name = '$foundwhat[long_name]' AND tribeid = '$tribe[goods_tribe]'");
            db_op_result($query,__LINE__,__FILE__);
           }
         else{
           $what = rand( 0, 6 );
           $many = rand( 1, $skillinfo[level] + 5 );
           $found = $db->Execute("SELECT * FROM $dbtables[livestock] WHERE tribeid = '$tribe[goods_tribe]' LIMIT $what, 1");
            db_op_result($found,__LINE__,__FILE__);
           $findwhat = $found->fields;
           $query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','SCOUT','$stamp','Southeast Scouting: We have found $many $findwhat[type].')");
           db_op_result($query,__LINE__,__FILE__);
           $query = $db->Execute("UPDATE $dbtables[livestock] SET amount = amount + $many WHERE type = '$findwhat[type]' AND tribeid = '$tribe[goods_tribe]'");
           db_op_result($query,__LINE__,__FILE__);
           }
       }
  $query = $db->Execute("INSERT INTO $dbtables[map_table] VALUES('','$tribe[tribeid]','$tribe[clanid]','$hexinfo[hex_id]','$stamp','$tribe[tribeid]')");
  db_op_result($query,__LINE__,__FILE__);
  $query = $db->Execute("INSERT INTO $dbtables[movement_log] VALUES('','$tribe[tribeid]','$tribe[clanid]','$hexinfo[hex_id]','$stamp')");
 db_op_result($query,__LINE__,__FILE__);
  $query = $db->Execute("UPDATE $dbtables[mapping] "
                                ."SET `clanid_$tribe[clanid]` = '1' "
                                ."WHERE hex_id = '$hexinfo[hex_id]' "
                                ."AND `clanid_$tribe[clanid]` < 1");
          db_op_result($query,__LINE__,__FILE__);
                    $query = $db->Execute("UPDATE $dbtables[mapping] "
                                ."SET `admin_0000` = '1' "
                                ."WHERE hex_id = '$hexinfo[hex_id]' "
                                ."AND `admin_0000` < 1");  ///////////////check to see if there's anyone there//////////////
   db_op_result($query,__LINE__,__FILE__);
  $ct = $db->Execute("SELECT COUNT(*) as count FROM $dbtables[tribes] WHERE hex_id = '$scoutedhex' AND clanid != '$tribe[clanid]'");
  db_op_result($ct,__LINE__,__FILE__);
  $count = $ct->fields;
  if($count[count] > 0){
  $squat = $db->Execute("SELECT * FROM $dbtables[tribes] WHERE hex_id = '$scoutedhex' AND clanid != '$tribe[clanid]'");
   db_op_result($squat,__LINE__,__FILE__);
  $logtext = "Southeast Scouting: $tribe[tribeid]'s $scout[direction] scouts detected ";
  while(!$squat->EOF){
  $squatters = $squat->fields;
  $logtext .= "$squatters[tribeid] ";
  $squat->MoveNext();
  }
  $query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','SCOUT','$stamp','$logtext')");
   db_op_result($query,__LINE__,__FILE__);
  }
  }
  }
  $sesct->MoveNext();
  }



  $ssct = $db->Execute("SELECT * FROM $dbtables[scouts] "
                      ."WHERE tribeid = '$tribe[tribeid]' "
                      ."AND direction = 's' "
                      ."AND orders = 'M' "
                      ."LIMIT 1");
   db_op_result($ssct,__LINE__,__FILE__);
 $moved = 0;
  while(!$ssct->EOF){
  $scout = $ssct->fields;
    if($scout[mounted] == 'Y'){
     $movepts = 7 + $movement;
     }
     else{
     $movepts = 3 + ($movement/2);
     }
 $scoutedhex = $tribe[hex_id];
 $direction = $scout[direction];
 $move = 0;
 while($movepts > 0){
  $hex = $db->Execute("SELECT * FROM $dbtables[hexes] WHERE hex_id = '$scoutedhex'");
   db_op_result($hex,__LINE__,__FILE__);
  $hexinfo = $hex->fields;
  $hex = $db->Execute("SELECT * FROM $dbtables[hexes] WHERE hex_id = '$hexinfo[$direction]'");
  db_op_result($hex,__LINE__,__FILE__);
  $hexinfo = $hex->fields;
  if($movepts < $hexinfo[move]){
  $movepts = 0;
  $moved = 1;
   }
  if($move > 0){
   $movepts -= $hexinfo[move];
   }
  else{
   $move++;
   }
  /////////////move the scouts 1 tile//////////////
  $scoutedhex = $hexinfo[hex_id];
  //////////////add the tile to the map/////////////////
  if(!$moved){
   $scoutfind = rand(1,500);
      if( $scoutfind >  ( 490 + $skillinfo[level] ) )
   {
       $numbermissed = rand( 1, $scout[actives] );
       if( $scout[actives] == $numbermissed )
       {
           $query = $db->Execute("DELETE FROM $dbtables[scouts] "
                       ."WHERE scoutid = '$scout[scoutid]'");
            db_op_result($query,__LINE__,__FILE__);
           $query = $db->Execute("INSERT INTO $dbtables[logs] "
                       ."VALUES("
                       ."'',"
                       ."'$month[count]',"
                       ."'$year[count]',"
                       ."'0000',"
                       ."'0000.00',"
                       ."'SCOUT',"
                       ."'$stamp',"
                       ."'Scouting: $tribe[tribeid] lost $numbermissed scouts from $scout[scoutid].')");
                    db_op_result($query,__LINE__,__FILE__);
       }
       else
       {
           $query = $db->Execute("UPDATE $dbtables[scouts] "
                       ."SET actives = actives - $numbermissed "
                       ."WHERE scoutid = '$scout[scoutid]'");
            db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("INSERT INTO $dbtables[logs] "
                        ."VALUES("
                        ."'',"
                        ."'$month[count]',"
                        ."'$year[count]',"
                        ."'$tribe[clanid]',"
                        ."'$tribe[tribeid]',"
                        ."'SCOUT',"
                        ."'$stamp',"
                        ."'Scouting: It appears that $numbermissed scouts did not return.')");
                  db_op_result($query,__LINE__,__FILE__);
           $query = $db->Execute("INSERT INTO $dbtables[logs] "
                       ."VALUES("
                       ."'',"
                       ."'$month[count]',"
                       ."'$year[count]',"
                       ."'0000',"
                       ."'0000.00',"
                       ."'SCOUT',"
                       ."'$stamp',"
                       ."'Scouting: $tribe[tribeid] lost $numbermissed scouts from $scout[scoutid].')");
           db_op_result($query,__LINE__,__FILE__);
       }
   }
   elseif($scoutfind < $skillinfo[level]){
         $whatfind = (rand(1,100) + $skillinfo[level]);
         if($whatfind > 75){
           $find = $db->Execute("SELECT COUNT(distinct long_name) AS count FROM $dbtables[product_table] "
                               ."WHERE skill_abbr != 'shw' "
                               ."AND long_name != 'totem' "
                               ."AND skill_level < '$skillinfo[level]' "
                               ."AND include = 'Y'");
            db_op_result($find,__LINE__,__FILE__);
           $findwhat = $find->fields;
             $what = rand(0, $findwhat[count] );
             $many = rand(1, $skillinfo[level]);
           $found = $db->Execute("SELECT * FROM $dbtables[product_table] "
                                ."WHERE skill_abbr != 'shw' "
                                ."AND include = 'Y' "
                               ."AND skill_level < '$skillinfo[level]' "
                                ."AND long_name != 'totem' "
                                ."LIMIT $what, 1");
                  db_op_result($found,__LINE__,__FILE__);
           $foundwhat = $found->fields;
           $query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','SCOUT','$stamp','South Scouting: We have found $many $foundwhat[proper].')");
           db_op_result($query,__LINE__,__FILE__);
           $query = $db->Execute("UPDATE $dbtables[products] SET amount = amount + $many WHERE long_name = '$foundwhat[long_name]' AND tribeid = '$tribe[goods_tribe]'");
           db_op_result($query,__LINE__,__FILE__);
           }
         else{
           $what = rand( 0, 6 );
           $many = rand( 1, $skillinfo[level] + 5 );
           $found = $db->Execute("SELECT * FROM $dbtables[livestock] WHERE tribeid = '$tribe[goods_tribe]' LIMIT $what, 1");
           db_op_result($found,__LINE__,__FILE__);
           $findwhat = $found->fields;
           $query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','SCOUT','$stamp','South Scouting: We have found $many $findwhat[type].')");
           db_op_result($query,__LINE__,__FILE__);
           $query = $db->Execute("UPDATE $dbtables[livestock] SET amount = amount + $many WHERE type = '$findwhat[type]' AND tribeid = '$tribe[goods_tribe]'");
           db_op_result($query,__LINE__,__FILE__);
           }
          }
  $query = $db->Execute("INSERT INTO $dbtables[map_table] VALUES('','$tribe[tribeid]','$tribe[clanid]','$hexinfo[hex_id]','$stamp','$tribe[tribeid]')");
  db_op_result($query,__LINE__,__FILE__);
  $query = $db->Execute("INSERT INTO $dbtables[movement_log] VALUES('','$tribe[tribeid]','$tribe[clanid]','$hexinfo[hex_id]','$stamp')");
 db_op_result($query,__LINE__,__FILE__);
  $query = $db->Execute("UPDATE $dbtables[mapping] "
                                ."SET `clanid_$tribe[clanid]` = '1' "
                                ."WHERE hex_id = '$hexinfo[hex_id]' "
                                ."AND `clanid_$tribe[clanid]` < 1");
          db_op_result($query,__LINE__,__FILE__);
                    $query = $db->Execute("UPDATE $dbtables[mapping] "
                                ."SET `admin_0000` = '1' "
                                ."WHERE hex_id = '$hexinfo[hex_id]' "
                                ."AND `admin_0000` < 1");  ///////////////check to see if there's anyone there//////////////
    db_op_result($query,__LINE__,__FILE__);
  $ct = $db->Execute("SELECT COUNT(*) as count FROM $dbtables[tribes] WHERE hex_id = '$scoutedhex' AND clanid != '$tribe[clanid]'");
  $count = $ct->fields;
  if($count[count] > 0){
  $squat = $db->Execute("SELECT * FROM $dbtables[tribes] WHERE hex_id = '$scoutedhex' AND clanid != '$tribe[clanid]'");
  db_op_result($squat,__LINE__,__FILE__);
  $logtext = "South Scouting: $tribe[tribeid]'s $scout[direction] scouts detected ";
  while(!$squat->EOF){
  $squatters = $squat->fields;
  $logtext .= "$squatters[tribeid] ";
  $squat->MoveNext();
  }
  $query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','SCOUT','$stamp','$logtext')");
    db_op_result($query,__LINE__,__FILE__);
  }
  }
  }
  $ssct->MoveNext();
  }
  $swsct = $db->Execute("SELECT * FROM $dbtables[scouts] "
                       ."WHERE tribeid = '$tribe[tribeid]' "
                       ."AND direction = 'sw' "
                       ."AND orders = 'M' "
                       ."LIMIT 1");
  db_op_result($swsct,__LINE__,__FILE__);
 $moved = 0;
  while(!$swsct->EOF){
  $scout = $swsct->fields;
    if($scout[mounted] == 'Y'){
     $movepts = 7 + $movement;
     }
     else{
     $movepts = 3 + ($movement/2);
     }
 $scoutedhex = $tribe[hex_id];
 $direction = $scout[direction];
 $move = 0;
 while($movepts > 0){
  $hex = $db->Execute("SELECT * FROM $dbtables[hexes] WHERE hex_id = '$scoutedhex'");
  db_op_result($hex,__LINE__,__FILE__);
  $hexinfo = $hex->fields;
  $hex = $db->Execute("SELECT * FROM $dbtables[hexes] WHERE hex_id = '$hexinfo[$direction]'");
  db_op_result($hex,__LINE__,__FILE__);
  $hexinfo = $hex->fields;
  if($movepts < $hexinfo[move]){
  $movepts = 0;
  $moved = 1;
   }
  if($move > 0){
   $movepts -= $hexinfo[move];
   }
  else{
   $move++;
   }
  /////////////move the scouts 1 tile//////////////
  $scoutedhex = $hexinfo[hex_id];
  //////////////add the tile to the map/////////////////
  if(!$moved){
   $scoutfind = rand(1,500);
      if( $scoutfind >  ( 490 + $skillinfo[level] ) )
   {
       $numbermissed = rand( 1, $scout[actives] );
       if( $scout[actives] == $numbermissed )
       {
           $query = $db->Execute("DELETE FROM $dbtables[scouts] "
                       ."WHERE scoutid = '$scout[scoutid]'");
           db_op_result($query,__LINE__,__FILE__);
           $query = $db->Execute("INSERT INTO $dbtables[logs] "
                       ."VALUES("
                       ."'',"
                       ."'$month[count]',"
                       ."'$year[count]',"
                       ."'0000',"
                       ."'0000.00',"
                       ."'SCOUT',"
                       ."'$stamp',"
                       ."'Scouting: $tribe[tribeid] lost $numbermissed scouts from $scout[scoutid].')");
              db_op_result($query,__LINE__,__FILE__);
       }
       else
       {
           $query = $db->Execute("UPDATE $dbtables[scouts] "
                       ."SET actives = actives - $numbermissed "
                       ."WHERE scoutid = '$scout[scoutid]'");
             db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("INSERT INTO $dbtables[logs] "
                        ."VALUES("
                        ."'',"
                        ."'$month[count]',"
                        ."'$year[count]',"
                        ."'$tribe[clanid]',"
                        ."'$tribe[tribeid]',"
                        ."'SCOUT',"
                        ."'$stamp',"
                        ."'Scouting: It appears that $numbermissed scouts did not return.')");
                db_op_result($query,__LINE__,__FILE__);
           $query = $db->Execute("INSERT INTO $dbtables[logs] "
                       ."VALUES("
                       ."'',"
                       ."'$month[count]',"
                       ."'$year[count]',"
                       ."'0000',"
                       ."'0000.00',"
                       ."'SCOUT',"
                       ."'$stamp',"
                       ."'Scouting: $tribe[tribeid] lost $numbermissed scouts from $scout[scoutid].')");
          db_op_result($query,__LINE__,__FILE__);
       }
   }
   elseif($scoutfind < $skillinfo[level]){
         $whatfind = (rand(1,100) + $skillinfo[level]);
         if($whatfind > 75){
           $find = $db->Execute("SELECT COUNT(distinct long_name) AS count FROM $dbtables[product_table] "
                               ."WHERE skill_abbr != 'shw' "
                               ."AND long_name != 'totem' "
                               ."AND skill_level < '$skillinfo[level]' "
                               ."AND include = 'Y'");
               db_op_result($find,__LINE__,__FILE__);
           $findwhat = $find->fields;
             $what = rand(0, $findwhat[count] );
             $many = rand(1, $skillinfo[level]);
           $found = $db->Execute("SELECT * FROM $dbtables[product_table] "
                                ."WHERE skill_abbr != 'shw' "
                                ."AND include = 'Y' "
                                ."AND long_name != 'totem' "
                               ."AND skill_level < '$skillinfo[level]' "
                                ."LIMIT $what, 1");
                 db_op_result($found,__LINE__,__FILE__);
           $foundwhat = $found->fields;
           $query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','SCOUT','$stamp','Southwest Scouting: We have found $many $foundwhat[proper].')");
          db_op_result($query,__LINE__,__FILE__);
           $query = $db->Execute("UPDATE $dbtables[products] SET amount = amount + $many WHERE long_name = '$foundwhat[long_name]' AND tribeid = '$tribe[goods_tribe]'");
           db_op_result($query,__LINE__,__FILE__);
           }
         else{
           $what = rand( 0, 6 );
           $many = rand( 1, $skillinfo[level] + 5 );
           $found = $db->Execute("SELECT * FROM $dbtables[livestock] WHERE tribeid = '$tribe[goods_tribe]' LIMIT $what, 1");
            db_op_result($found,__LINE__,__FILE__);
           $findwhat = $found->fields;
           $query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','SCOUT','$stamp','Southwest Scouting: We have found $many $findwhat[type].')");
           db_op_result($query,__LINE__,__FILE__);
           $query = $db->Execute("UPDATE $dbtables[livestock] SET amount = amount + $many WHERE type = '$findwhat[type]' AND tribeid = '$tribe[goods_tribe]'");
           db_op_result($query,__LINE__,__FILE__);
           }
    }
  $query = $db->Execute("INSERT INTO $dbtables[map_table] VALUES('','$tribe[tribeid]','$tribe[clanid]','$hexinfo[hex_id]','$stamp','$tribe[tribeid]')");
  db_op_result($query,__LINE__,__FILE__);
  $query = $db->Execute("INSERT INTO $dbtables[movement_log] VALUES('','$tribe[tribeid]','$tribe[clanid]','$hexinfo[hex_id]','$stamp')");
  db_op_result($query,__LINE__,__FILE__);
  $query = $db->Execute("UPDATE $dbtables[mapping] "
                                ."SET `clanid_$tribe[clanid]` = '1' "
                                ."WHERE hex_id = '$hexinfo[hex_id]' "
                                ."AND `clanid_$tribe[clanid]` < 1");
        db_op_result($query,__LINE__,__FILE__);
                    $query = $db->Execute("UPDATE $dbtables[mapping] "
                                ."SET `admin_0000` = '1' "
                                ."WHERE hex_id = '$hexinfo[hex_id]' "
                                ."AND `admin_0000` < 1");  ///////////////check to see if there's anyone there//////////////
       db_op_result($query,__LINE__,__FILE__);
  $ct = $db->Execute("SELECT COUNT(*) as count FROM $dbtables[tribes] WHERE hex_id = '$scoutedhex' AND clanid != '$tribe[clanid]'");
  db_op_result($ct,__LINE__,__FILE__);
  $count = $ct->fields;
  if($count[count] > 0){
  $squat = $db->Execute("SELECT * FROM $dbtables[tribes] WHERE hex_id = '$scoutedhex' AND clanid != '$tribe[clanid]'");
   db_op_result($squat,__LINE__,__FILE__);
  $logtext = "Southwest Scouting: $tribe[tribeid]'s $scout[direction] scouts detected ";
  while(!$squat->EOF){
  $squatters = $squat->fields;
  $logtext .= "$squatters[tribeid] ";
  $squat->MoveNext();
  }
  $query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','SCOUT','$stamp','$logtext')");
        db_op_result($query,__LINE__,__FILE__);
  }
  }
  }
  $swsct->MoveNext();
  }

  $wsct = $db->Execute("SELECT * FROM $dbtables[scouts] "
                      ."WHERE tribeid = '$tribe[tribeid]' "
                      ."AND direction = 'w' "
                      ."AND orders = 'M' "
                      ."LIMIT 1");
     db_op_result($wsct,__LINE__,__FILE__);
  $moved = 0;
  while(!$wsct->EOF){
  $scout = $wsct->fields;
    if($scout[mounted] == 'Y'){
     $movepts = 7 + $movement;
     }
     else{
     $movepts = 3 + ($movement/2);
     }
 $scoutedhex = $tribe[hex_id];
 $direction = $scout[direction];
 $move = 0;
 while($movepts > 0){
  $hex = $db->Execute("SELECT * FROM $dbtables[hexes] WHERE hex_id = '$scoutedhex'");
  db_op_result($hex,__LINE__,__FILE__);
  $hexinfo = $hex->fields;
  $hex = $db->Execute("SELECT * FROM $dbtables[hexes] WHERE hex_id = '$hexinfo[$direction]'");
  db_op_result($hex,__LINE__,__FILE__);
  $hexinfo = $hex->fields;
  if($movepts < $hexinfo[move]){
  $movepts = 0;
  $moved = 1;
   }
  if($move > 0){
   $movepts -= $hexinfo[move];
   }
  else{
   $move++;
   }
  /////////////move the scouts 1 tile//////////////
  $scoutedhex = $hexinfo[hex_id];
  //////////////add the tile to the map/////////////////
  if(!$moved){
   $scoutfind = rand(1,500);
      if( $scoutfind >  ( 490 + $skillinfo[level] ) )
   {
       $numbermissed = rand( 1, $scout[actives] );
       if( $scout[actives] == $numbermissed )
       {
           $query = $db->Execute("DELETE FROM $dbtables[scouts] "
                       ."WHERE scoutid = '$scout[scoutid]'");
               db_op_result($query,__LINE__,__FILE__);
           $query = $db->Execute("INSERT INTO $dbtables[logs] "
                       ."VALUES("
                       ."'',"
                       ."'$month[count]',"
                       ."'$year[count]',"
                       ."'0000',"
                       ."'0000.00',"
                       ."'SCOUT',"
                       ."'$stamp',"
                       ."'Scouting: $tribe[tribeid] lost $numbermissed scouts from $scout[scoutid].')");
                db_op_result($query,__LINE__,__FILE__);
       }
       else
       {
           $query = $db->Execute("UPDATE $dbtables[scouts] "
                       ."SET actives = actives - $numbermissed "
                       ."WHERE scoutid = '$scout[scoutid]'");
              db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("INSERT INTO $dbtables[logs] "
                        ."VALUES("
                        ."'',"
                        ."'$month[count]',"
                        ."'$year[count]',"
                        ."'$tribe[clanid]',"
                        ."'$tribe[tribeid]',"
                        ."'SCOUT',"
                        ."'$stamp',"
                        ."'Scouting: It appears that $numbermissed scouts did not return.')");
               db_op_result($query,__LINE__,__FILE__);
           $query = $db->Execute("INSERT INTO $dbtables[logs] "
                       ."VALUES("
                       ."'',"
                       ."'$month[count]',"
                       ."'$year[count]',"
                       ."'0000',"
                       ."'0000.00',"
                       ."'SCOUT',"
                       ."'$stamp',"
                       ."'Scouting: $tribe[tribeid] lost $numbermissed scouts from $scout[scoutid].')");
            db_op_result($query,__LINE__,__FILE__);
       }
   }
   elseif($scoutfind < $skillinfo[level]){
         $whatfind = (rand(1,100) + $skillinfo[level]);
         if($whatfind > 75){
           $find = $db->Execute("SELECT COUNT(distinct long_name) AS count FROM $dbtables[product_table] "
                               ."WHERE skill_abbr != 'shw' "
                               ."AND long_name != 'totem' "
                               ."AND skill_level < '$skillinfo[level]' "
                               ."AND include = 'Y'");
               db_op_result($find,__LINE__,__FILE__);
           $findwhat = $find->fields;
             $what = rand(0, $findwhat[count] );
             $many = rand(1, $skillinfo[level]);
           $found = $db->Execute("SELECT * FROM $dbtables[product_table] "
                                ."WHERE skill_abbr != 'shw' "
                                ."AND include = 'Y' "
                                ."AND long_name != 'totem' "
                               ."AND skill_level < '$skillinfo[level]' "
                                ."LIMIT $what, 1");
                  db_op_result($found,__LINE__,__FILE__);
           $foundwhat = $found->fields;
           $query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','SCOUT','$stamp','West Scouting: We have found $many $foundwhat[proper].')");
            db_op_result($query,__LINE__,__FILE__);
           $query = $db->Execute("UPDATE $dbtables[products] SET amount = amount + $many WHERE long_name = '$foundwhat[long_name]' AND tribeid = '$tribe[goods_tribe]'");
                   db_op_result($query,__LINE__,__FILE__);
           }
         else{
           $what = rand( 0, 6 );
           $many = rand( 1, $skillinfo[level] + 5 );
           $found = $db->Execute("SELECT * FROM $dbtables[livestock] WHERE tribeid = '$tribe[goods_tribe]' LIMIT $what, 1");
             db_op_result($found,__LINE__,__FILE__);
           $findwhat = $found->fields;
           $query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','SCOUT','$stamp','West Scouting: We have found $many $findwhat[type].')");
           db_op_result($query,__LINE__,__FILE__);
           $query = $db->Execute("UPDATE $dbtables[livestock] SET amount = amount + $many WHERE type = '$findwhat[type]' AND tribeid = '$tribe[goods_tribe]'");
           db_op_result($query,__LINE__,__FILE__);
           }
    }
  $query = $db->Execute("INSERT INTO $dbtables[map_table] VALUES('','$tribe[tribeid]','$tribe[clanid]','$hexinfo[hex_id]','$stamp','$tribe[tribeid]')");
    db_op_result($query,__LINE__,__FILE__);
  $query = $db->Execute("INSERT INTO $dbtables[movement_log] VALUES('','$tribe[tribeid]','$tribe[clanid]','$hexinfo[hex_id]','$stamp')");
db_op_result($query,__LINE__,__FILE__);
  $query = $db->Execute("UPDATE $dbtables[mapping] "
                                ."SET `clanid_$tribe[clanid]` = '1' "
                                ."WHERE hex_id = '$hexinfo[hex_id]' "
                                ."AND `clanid_$tribe[clanid]` < 1");
              db_op_result($query,__LINE__,__FILE__);
                    $query = $db->Execute("UPDATE $dbtables[mapping] "
                                ."SET `admin_0000` = '1' "
                                ."WHERE hex_id = '$hexinfo[hex_id]' "
                                ."AND `admin_0000` < 1");  ///////////////check to see if there's anyone there//////////////
         db_op_result($query,__LINE__,__FILE__);
  $ct = $db->Execute("SELECT COUNT(*) as count FROM $dbtables[tribes] WHERE hex_id = '$scoutedhex' AND clanid != '$tribe[clanid]'");
   db_op_result($ct,__LINE__,__FILE__);
  $count = $ct->fields;
  if($count[count] > 0){
  $squat = $db->Execute("SELECT * FROM $dbtables[tribes] WHERE hex_id = '$scoutedhex' AND clanid != '$tribe[clanid]'");
  db_op_result($squat,__LINE__,__FILE__);
  $logtext = "West Scouting: $tribe[tribeid]'s $scout[direction] scouts detected ";
  while(!$squat->EOF){
  $squatters = $squat->fields;
  $logtext .= "$squatters[tribeid] ";
  $squat->MoveNext();
  }
  $query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','SCOUT','$stamp','$logtext')");
    db_op_result($query,__LINE__,__FILE__);
  }
  }
  }
  $wsct->MoveNext();
  }





  $nwsct = $db->Execute("SELECT * FROM $dbtables[scouts] "
                       ."WHERE tribeid = '$tribe[tribeid]' "
                       ."AND direction = 'nw' "
                       ."AND orders = 'M' "
                       ."LIMIT 1");
     db_op_result($nwsct,__LINE__,__FILE__);
  $moved = 0;
  while(!$nwsct->EOF){
  $scout = $nwsct->fields;
    if($scout[mounted] == 'Y'){
     $movepts = 7 + $movement;
     }
     else{
     $movepts = 3 + ($movement/2);
     }
 $scoutedhex = $tribe[hex_id];
 $direction = $scout[direction];
 $move = 0;
 while($movepts > 0){
  $hex = $db->Execute("SELECT * FROM $dbtables[hexes] WHERE hex_id = '$scoutedhex'");
      db_op_result($hex,__LINE__,__FILE__);
  $hexinfo = $hex->fields;
  $hex = $db->Execute("SELECT * FROM $dbtables[hexes] WHERE hex_id = '$hexinfo[$direction]'");
   db_op_result($hex,__LINE__,__FILE__);
  $hexinfo = $hex->fields;
  if($movepts < $hexinfo[move]){
  $movepts = 0;
  $moved = 1;
   }
  if($move > 0){
   $movepts -= $hexinfo[move];
   }
  else{
   $move++;
   }
  /////////////move the scouts 1 tile//////////////
  $scoutedhex = $hexinfo[hex_id];
  //////////////add the tile to the map/////////////////
  if(!$moved){
   $scoutfind = rand(1,500);
      if( $scoutfind >  ( 490 + $skillinfo[level] ) )
   {
       $numbermissed = rand( 1, $scout[actives] );
       if( $scout[actives] == $numbermissed )
       {
           $query = $db->Execute("DELETE FROM $dbtables[scouts] "
                       ."WHERE scoutid = '$scout[scoutid]'");
           db_op_result($query,__LINE__,__FILE__);

           $query = $db->Execute("INSERT INTO $dbtables[logs] "
                       ."VALUES("
                       ."'',"
                       ."'$month[count]',"
                       ."'$year[count]',"
                       ."'0000',"
                       ."'0000.00',"
                       ."'SCOUT',"
                       ."'$stamp',"
                       ."'Scouting: $tribe[tribeid] lost $numbermissed scouts from $scout[scoutid].')");
              db_op_result($query,__LINE__,__FILE__);
       }
       else
       {
           $query = $db->Execute("UPDATE $dbtables[scouts] "
                       ."SET actives = actives - $numbermissed "
                       ."WHERE scoutid = '$scout[scoutid]'");
            db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("INSERT INTO $dbtables[logs] "
                        ."VALUES("
                        ."'',"
                        ."'$month[count]',"
                        ."'$year[count]',"
                        ."'$tribe[clanid]',"
                        ."'$tribe[tribeid]',"
                        ."'SCOUT',"
                        ."'$stamp',"
                        ."'Scouting: It appears that $numbermissed scouts did not return.')");
              db_op_result($query,__LINE__,__FILE__);
           $query = $db->Execute("INSERT INTO $dbtables[logs] "
                       ."VALUES("
                       ."'',"
                       ."'$month[count]',"
                       ."'$year[count]',"
                       ."'0000',"
                       ."'0000.00',"
                       ."'SCOUT',"
                       ."'$stamp',"
                       ."'Scouting: $tribe[tribeid] lost $numbermissed scouts from $scout[scoutid].')");
            db_op_result($query,__LINE__,__FILE__);
       }
   }
   elseif($scoutfind < $skillinfo[level]){
         $whatfind = (rand(1,100) + $skillinfo[level]);
         if($whatfind > 75){
           $find = $db->Execute("SELECT COUNT(distinct long_name) AS count FROM $dbtables[product_table] "
                               ."WHERE skill_abbr != 'shw' "
                               ."AND long_name != 'totem' "
                               ."AND skill_level < '$skillinfo[level]' "
                               ."AND include = 'Y'");
               db_op_result($find,__LINE__,__FILE__);
           $findwhat = $find->fields;
             $what = rand(0, $findwhat[count] );
             $many = rand(1, $skillinfo[level]);
           $found = $db->Execute("SELECT * FROM $dbtables[product_table] "
                                ."WHERE skill_abbr != 'shw' "
                                ."AND include = 'Y' "
                                ."AND long_name != 'totem' "
                               ."AND skill_level < '$skillinfo[level]' "
                                ."LIMIT $what, 1");
                         db_op_result($found,__LINE__,__FILE__);
           $foundwhat = $found->fields;

           $query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','SCOUT','$stamp','Northwest Scouting: We have found $many $foundwhat[proper].')");
             db_op_result($query,__LINE__,__FILE__);
           $query = $db->Execute("UPDATE $dbtables[products] SET amount = amount + $many WHERE long_name = '$foundwhat[long_name]' AND tribeid = '$tribe[goods_tribe]'");
               db_op_result($query,__LINE__,__FILE__);
           }
         else{
           $what = rand( 0, 6 );
           $many = rand( 1, $skillinfo[level] + 5 );
           $found = $db->Execute("SELECT * FROM $dbtables[livestock] WHERE tribeid = '$tribe[goods_tribe]' LIMIT $what, 1");
            db_op_result($found,__LINE__,__FILE__);
           $findwhat = $found->fields;
           $query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','SCOUT','$stamp','Northwest Scouting: We have found $many $findwhat[type].')");
           db_op_result($query,__LINE__,__FILE__);
           $query = $db->Execute("UPDATE $dbtables[livestock] SET amount = amount + $many WHERE type = '$findwhat[type]' AND tribeid = '$tribe[goods_tribe]'");
           db_op_result($query,__LINE__,__FILE__);
           }
    }
  $query = $db->Execute("INSERT INTO $dbtables[map_table] VALUES('','$tribe[tribeid]','$tribe[clanid]','$hexinfo[hex_id]','$stamp','$tribe[tribeid]')");
  db_op_result($query,__LINE__,__FILE__);
  $query = $db->Execute("INSERT INTO $dbtables[movement_log] VALUES('','$tribe[tribeid]','$tribe[clanid]','$hexinfo[hex_id]','$stamp')");
  db_op_result($query,__LINE__,__FILE__);
  $query = $db->Execute("UPDATE $dbtables[mapping] "
                                ."SET `clanid_$tribe[clanid]` = '1' "
                                ."WHERE hex_id = '$hexinfo[hex_id]' "
                                ."AND `clanid_$tribe[clanid]` < 1");
                    db_op_result($query,__LINE__,__FILE__);
                    $query = $db->Execute("UPDATE $dbtables[mapping] "
                                ."SET `admin_0000` = '1' "
                                ."WHERE hex_id = '$hexinfo[hex_id]' "
                                ."AND `admin_0000` < 1"); ///////////////check to see if there's anyone there//////////////
      db_op_result($query,__LINE__,__FILE__);
  $ct = $db->Execute("SELECT COUNT(*) as count FROM $dbtables[tribes] WHERE hex_id = '$scoutedhex' AND clanid != '$tribe[clanid]'");
     db_op_result($ct,__LINE__,__FILE__);
  $count = $ct->fields;
  if($count[count] > 0){
  $squat = $db->Execute("SELECT * FROM $dbtables[tribes] WHERE hex_id = '$scoutedhex' AND clanid != '$tribe[clanid]'");
    db_op_result($squat,__LINE__,__FILE__);
  $logtext = "Northwest Scouting: $tribe[tribeid]'s $scout[direction] scouts detected ";
  while(!$squat->EOF){
  $squatters = $squat->fields;
  $logtext .= "$squatters[tribeid] ";
  $squat->MoveNext();
  }
  $query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','SCOUT','$stamp','$logtext')");
   db_op_result($query,__LINE__,__FILE__);
  }
  }
  }
  $nwsct->MoveNext();
  }



$res->MoveNext();
}

?>
