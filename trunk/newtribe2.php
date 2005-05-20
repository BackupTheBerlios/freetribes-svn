<?php
session_start();
header("Cache-control: private");
include("config.php");
page_header("Tribe Creation");
include("game_time.php");
connectdb();

//PLACEHOLDER - Here, we're gonna do a check_form_submit($_SESSION['session_id']) function
//they can get around it by creating a second new session, but we'll handle that once we revamp
//the login system to allow only a unique user_id/session_id at a time, so one login will kill the old session
//and a login check at every page :)  trukfixer = evil, mean and nasty

$username = $_SESSION['username'];
$current_unit = $_SESSION['current_unit'];

$tribe = $db->Execute("SELECT b.level,a.totalpop,a.activepop,a.inactivepop,a.warpop,a.hex_id,a.tribeid FROM $dbtables[tribes] as a,$dbtables[skills] as b WHERE a.tribeid = '$current_unit' AND b.abbr = 'dip' AND a.tribeid = b.tribeid" );
db_op_result($tribe,__LINE__,__FILE__);
$tribeinfo = $tribe->fields;
$diplomacy_level = $tribeinfo['level'];
//we need totalpop,activepop,inactivepop,warpop,hex_id,tribeid,level gotten via skills table (a join)

$numb = $db->Execute("SELECT count(*) as count FROM $dbtables[tribes] WHERE clanid = '$_SESSION[clanid]'");
db_op_result($numb,__LINE__,__FILE__);
$count  = $numb->fields;
$tribenumb = $count['count'];
//change this to select count as count- we only need to know how many subtribes they already have vs diplomacy skill

$skill = $db->Execute("SELECT long_name,abbr,level FROM $dbtables[skills] WHERE tribeid = '$current_unit' AND level > 0 ORDER BY long_name");
db_op_result($skill,__LINE__,__FILE__);
$skillinfo = $skill->fields;
//needs long_name, abbr,level for drop select box.. lets just list *only* the skills they actually have, less processor use


//race condition where they can submit same form multiple times and cause issues.
//to eliminate this, we have to be able to check things -
//we'll deal with it later after optimizing this shit and getting it to work right
//TODO - sessions table, use form submit table to store session, timestamp and
//if that session exists, kill the remaining submissions  with a die() and clear the session when that session
//ID is seen in the main menu again - even if they have multiple windows open, ideally even a script wont be fast enough to break this, because sql queries would be queue'd
//and the first query (very fast) will be that session query
//so subsequent "race" submits will just die because that is gonna be the *first* query executed in this script


if( $diplo->EOF | $diplomacy_level <= $tribenum && empty( $_POST['skills'] ) )
{
    echo "You do not have the required amount of Diplomacy skill.<BR><BR>";
    echo "Diplomacy skill: $diplomacy_level <BR>Current Tribes: " . $tribenum . "<BR><BR>";
    page_footer();
}
else
{
    if( empty( $_POST['skills'] ) )
    {
        echo "<CENTER><FORM ACTION=newtribe2.php METHOD=POST>\n";
        echo "<TABLE WIDTH=\"100%\" BORDER=0 CELLPADDING=0 CELLSPACING=0>"
            ."<TR BGCOLOR=\"$color_header\">"
            ."<TD ALIGN=LEFT COLSPAN=12>"
            ."<FONT class=page_subtitle>Skills Transfer"
            ."</TD>"
            ."</TR>";

        echo "<TR CLASS=row_color0>";
        $n = 0;
        $m = 0;
        while( !$skill->EOF )
        {
            $skillinfo = $skill->fields;
            if(!empty($skillinfo['long_name']))
            {
                $n++;
                $i = 0;
                echo "<TD>&nbsp;$skillinfo[long_name]</TD><TD><SELECT NAME=$skillinfo[abbr]>";
                while( $i <= $skillinfo['level'] )
                {
                    echo "<OPTION>$i</OPTION>";
                    $i++;
                }
                $skill->MoveNext();
                echo "</SELECT></TD>";
                if( $n >= 6 )
                {
                    $n=0;
                    $m++;
                    $rc = $m % 2;
                    echo "</TR><TR CLASS=row_color$rc>\n";
                }
            }
        }
        $cols = 12 - $n*2;
        echo "<TD COLSPAN=$cols>&nbsp;<INPUT TYPE=HIDDEN NAME=newtribe VALUE=$newtribe>";
        echo "<INPUT TYPE=HIDDEN NAME=skills VALUE=1></TD></TR><TR>\n";
        echo "<TD ALIGN=CENTER COLSPAN=12><INPUT TYPE=HIDDEN NAME=unique VALUE='$_POST[unique]'>";
        echo "<INPUT TYPE=SUBMIT VALUE=NEXT></TD></TR>";
        echo "</TABLE></FORM>";
    }
}

if(!empty( $_POST['skills']))
{
    $temp = $db->Execute("SELECT tribeid FROM $dbtables[tribes] "
                        ."WHERE clanid = '$_SESSION[clanid]' "
                        ."ORDER BY tribeid "
                        ."DESC "
                        ."LIMIT 1");
      db_op_result($temp,__LINE__,__FILE__);
    $temptribe = $temp->fields;
    $newtribe = $temptribe['tribeid'] + .01;
    $_SESSION['newtribe'] = $newtribe;
    $adm = $db->Execute("SELECT level FROM $dbtables[skills] "
                       ."WHERE abbr = 'adm' "
                       ."AND tribeid = '$_SESSION[current_unit]'");
       db_op_result($adm,__LINE__,__FILE__);
    $adm_percent = $adm->fields;
    $start_total_trans_percent = ($adm_percent['level'] + 5) * .01;
    $newtribe_totalpop = $tribeinfo['totalpop'] * $start_total_trans_percent;
    $newtribe_warpop = 0;
    $newtribe_actives = $tribeinfo['activepop'] * $start_total_trans_percent;
    $newtribe_inactives = $tribeinfo['inactivepop'] * $start_total_trans_percent;
    $tribeinfo['totalpop'] -= $tribeinfo['totalpop'] * $start_total_trans_percent;
    $tribeinfo['activepop'] -= $tribeinfo['activepop'] * $start_total_trans_percent;
    $tribeinfo['inactivepop'] -= $tribeinfo['inactivepop'] * $start_total_trans_percent;
    $insquery = $db->Execute("INSERT INTO $dbtables[tribes] "
                ."VALUES("
                ."'$_SESSION[clanid]',"
                ."'$newtribe',"
                ."'',"
                ."'Y',"
                ."'$newtribe_totalpop',"
                ."'$newtribe_warpop',"
                ."'$newtribe_actives',"
                ."'$newtribe_inactives',"
                ."'0',"
                ."'0',"
                ."'0',"
                ."'0',"
                ."'1.0',"
                ."'0',"
                ."'0',"
                ."'$tribeinfo[hex_id]',"
                ."'',"
                ."'',"
                ."'',"
                ."'18',"
                ."'$_SESSION[current_unit]')");
         db_op_result($insquery,__LINE__,__FILE__);
    $insquery = $db->Execute("UPDATE $dbtables[tribes] "
                ."SET totalpop = '$tribeinfo[totalpop]', "
                ."warpop = '$tribeinfo[warpop]', "
                ."activepop = '$tribeinfo[activepop]', "
                ."inactivepop = '$tribeinfo[inactivepop]' "
                ."WHERE tribeid = '$_SESSION[current_unit]'");
        db_op_result($insquery,__LINE__,__FILE__);
    $subres = $db->Execute("SELECT * FROM $dbtables[resources] "
                          ."WHERE tribeid = '$newtribe'");
           db_op_result($subres,__LINE__,__FILE__);
    if( $subres->EOF )
    {
   $query = $db->Execute("INSERT INTO $dbtables[resources] VALUES('$newtribe','Bronze','0','bronze')");
      db_op_result($query,__LINE__,__FILE__);
   $query = $db->Execute("INSERT INTO $dbtables[resources] VALUES('$newtribe','Iron','0','iron')");
     db_op_result($query,__LINE__,__FILE__);
   $query = $db->Execute("INSERT INTO $dbtables[resources] VALUES('$newtribe','Coal','0','coal')");
     db_op_result($query,__LINE__,__FILE__);
   $query = $db->Execute("INSERT INTO $dbtables[resources] VALUES('$newtribe','Zinc','0','zinc')");
     db_op_result($query,__LINE__,__FILE__);
   $query = $db->Execute("INSERT INTO $dbtables[resources] VALUES('$newtribe','Tin','0','tin')");
     db_op_result($query,__LINE__,__FILE__);
   $query = $db->Execute("INSERT INTO $dbtables[resources] VALUES('$newtribe','Silver','0','silver')");
      db_op_result($query,__LINE__,__FILE__);
   $query = $db->Execute("INSERT INTO $dbtables[resources] VALUES('$newtribe','Brass','0','brass')");
     db_op_result($query,__LINE__,__FILE__);
   $query = $db->Execute("INSERT INTO $dbtables[resources] VALUES('$newtribe','Lead','0','lead')");
     db_op_result($query,__LINE__,__FILE__);
   $query = $db->Execute("INSERT INTO $dbtables[resources] VALUES('$newtribe','Salt','0','salt')");
    db_op_result($query,__LINE__,__FILE__);
   $query = $db->Execute("INSERT INTO $dbtables[resources] VALUES('$newtribe','Stones','0','stones')");
     db_op_result($query,__LINE__,__FILE__);
   $query = $db->Execute("INSERT INTO $dbtables[resources] VALUES('$newtribe','Gold','0','gold')");
     db_op_result($query,__LINE__,__FILE__);
   $query = $db->Execute("INSERT INTO $dbtables[resources] VALUES('$newtribe','Copper','0','copper')");
      db_op_result($query,__LINE__,__FILE__);
   $query = $db->Execute("INSERT INTO $dbtables[resources] VALUES('$newtribe','Steel','0','steel')");
      db_op_result($query,__LINE__,__FILE__);
   $query = $db->Execute("INSERT INTO $dbtables[resources] VALUES('$newtribe','Steel_1','0','steel_1')");
     db_op_result($query,__LINE__,__FILE__);
   $query = $db->Execute("INSERT INTO $dbtables[resources] VALUES('$newtribe','Steel_2','0','steel_2')");
    db_op_result($query,__LINE__,__FILE__);
   $query = $db->Execute("INSERT INTO $dbtables[resources] VALUES('$newtribe','Coke','0','coke')");
     db_op_result($query,__LINE__,__FILE__);
   $query = $db->Execute("INSERT INTO $dbtables[resources] VALUES('$newtribe','Gems','0','gems')");
     db_op_result($query,__LINE__,__FILE__);
   $query = $db->Execute("INSERT INTO $dbtables[resources] VALUES('$newtribe','Iron Ore','0','iron.ore')");
     db_op_result($query,__LINE__,__FILE__);
   $query = $db->Execute("INSERT INTO $dbtables[resources] VALUES('$newtribe','Copper Ore','0','copper.ore')");
     db_op_result($query,__LINE__,__FILE__);
   $query = $db->Execute("INSERT INTO $dbtables[resources] VALUES('$newtribe','Zinc Ore','0','zinc.ore')");
     db_op_result($query,__LINE__,__FILE__);
   $query = $db->Execute("INSERT INTO $dbtables[resources] VALUES('$newtribe','Tin Ore','0','tin.ore')");
     db_op_result($query,__LINE__,__FILE__);
   $query = $db->Execute("INSERT INTO $dbtables[resources] VALUES('$newtribe','Silver Ore','0','silver.ore')");
     db_op_result($query,__LINE__,__FILE__);
   $query = $db->Execute("INSERT INTO $dbtables[resources] VALUES('$newtribe','Lead Ore','0','lead.ore')");
     db_op_result($query,__LINE__,__FILE__);
   $query = $db->Execute("INSERT INTO $dbtables[resources] VALUES('$newtribe','Gold Ore','0','gold.ore')");
     db_op_result($query,__LINE__,__FILE__);
   $query = $db->Execute("INSERT INTO $dbtables[resources] VALUES('$newtribe','Raw Gems','0','raw.gems')");
     db_op_result($query,__LINE__,__FILE__);
   $query = $db->Execute("INSERT INTO $dbtables[livestock] VALUES('','$newtribe','Cattle','0')");
     db_op_result($query,__LINE__,__FILE__);
   $query = $db->Execute("INSERT INTO $dbtables[livestock] VALUES('','$newtribe','Horses','0')");
    db_op_result($query,__LINE__,__FILE__);
   $query = $db->Execute("INSERT INTO $dbtables[livestock] VALUES('','$newtribe','Elephants','0')");
     db_op_result($query,__LINE__,__FILE__);
   $query = $db->Execute("INSERT INTO $dbtables[livestock] VALUES('','$newtribe','Goats','0')");
    db_op_result($query,__LINE__,__FILE__);
   $query = $db->Execute("INSERT INTO $dbtables[livestock] VALUES('','$newtribe','Sheep','0')");
    db_op_result($query,__LINE__,__FILE__);
   $query = $db->Execute("INSERT INTO $dbtables[livestock] VALUES('','$newtribe','Pigs','0')");
   db_op_result($query,__LINE__,__FILE__);
   $query = $db->Execute("INSERT INTO $dbtables[livestock] VALUES('','$newtribe','Dogs','0')");
   db_op_result($query,__LINE__,__FILE__);
    }

    db_op_result($query,__LINE__,__FILE__);
    $table = $db->Execute("SELECT * from $dbtables[skill_table]");
    db_op_result($table,__LINE__,__FILE__);
    $subskill = $db->Execute("SELECT * FROM $dbtables[skills] "
                            ."WHERE tribeid = '$newtribe'");
    db_op_result($subskill,__LINE__,__FILE__);
    if( $subskill->EOF )
    {
        while( !$table->EOF )
        {
            $tableinfo = $table->fields;
       $query = $db->Execute("INSERT INTO $dbtables[skills] "
                        ."VALUES("
                        ."'',"
                        ."'$tableinfo[abbr]',"
                        ."'$tableinfo[long_name]',"
                        ."'$tableinfo[group]',"
                        ."'$newtribe',"
                        ."'0',"
                        ."'')");
            db_op_result($query,__LINE__,__FILE__);
            $table->MoveNext();
        }
    }

    $prod = $db->Execute("SELECT DISTINCT proper, long_name, weapon, armor "
                        ."FROM $dbtables[product_table] "
                        ."WHERE include = 'Y'");
         db_op_result($prod,__LINE__,__FILE__);
    $subprod = $db->Execute("SELECT * FROM $dbtables[products] WHERE tribeid = '$newtribe'");
    db_op_result($subprod,__LINE__,__FILE__);
    if($subprod->EOF)
    {
        while(!$prod->EOF)
        {
            $prodinfo = $prod->fields;
       $query = $db->Execute("INSERT INTO $dbtables[products] (tribeid,proper,long_name,amount,weapon,armor)"
                        ."VALUES("
                        ."'$newtribe',"
                        ."'$prodinfo[proper]',"
                        ."'$prodinfo[long_name]',"
                        ."'0',"
                        ."'$prodinfo[weapon]',"
                        ."'$prodinfo[armor]')");
             db_op_result($query,__LINE__,__FILE__);
            $prod->MoveNext();
        }
    }

    echo "<CENTER><BR><BR><FONT SIZE=+1>New Subtribe $newtribe Created</CENTER></FONT><BR><BR>";
     echo "<a href=transfer.php>Click Here to transfer stuff</a> to your new tribe, if desired or<br><br><a href='main.php'>Click Here to Return to main menu</a><br>";
    include("weight.php");

    foreach($_POST as $key => $value)
    {
        $new = $db->Execute("SELECT * FROM $dbtables[skill_table] WHERE abbr = '$key'");
        db_op_result($new,__LINE__,__FILE__);
        $newskill = $new->fields;
        if(ISSET($newskill['long_name']))
        {
       $query = $db->Execute("UPDATE $dbtables[skills] "
                        ."SET level = level + '$value' "
                        ."WHERE abbr = '$newskill[abbr]' "
                        ."AND tribeid = '$newtribe'");
            db_op_result($query,__LINE__,__FILE__);
            $old = $db->Execute("SELECT * FROM $dbtables[skills] "
                                ."WHERE tribeid = '$_SESSION[current_unit]' "
                                ."AND abbr = '$newskill[abbr]'");
             db_op_result($old,__LINE__,__FILE__);
            $oldskill = $old->fields;

       $query = $db->Execute("UPDATE $dbtables[skills] "
                        ."SET level = level - '$value' "
                        ."WHERE abbr = '$oldskill[abbr]' "
                        ."AND tribeid = '$tribeinfo[tribeid]'");
            db_op_result($query,__LINE__,__FILE__);
         }
    }
}


page_footer();

?>
