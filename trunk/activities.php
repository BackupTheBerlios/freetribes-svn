<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: activities.php
session_start();
header("Cache-control: private");
include("config.php");
include("game_time.php");
//include("mstsck_list.php");

$time_start = getmicrotime();

page_header("Tribe Activities");

connectdb();

    $job = explode( '.', $_REQUEST['job'] );
    $module = $_POST['skilltype'];
/*
    echo "<PRE>";
    print_r($_POST);
    print_r($_REQUEST);
    echo "</PRE>";
*/
    $deva = $db->Execute("SELECT * FROM $dbtables[tribes] WHERE tribeid = '$_SESSION[current_unit]'");
    db_op_result($deva,__LINE__,__FILE__);
    $devainfo = $deva->fields;

    if( !$devainfo[DeVA] == '0000.00' )
    {
        echo 'You are under DeVA, and must break the seige before conducting activities.<BR>';
        page_footer();
    }


echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0>"
    ."<TR>"
    ."<TD>&nbsp;</TD>";
if ( $month['count'] > 2 && $month['count'] < 12 )
{
    echo "<FORM ACTION=seeking.php METHOD=POST>"
        ."<TD>"
        ."<INPUT TYPE=SUBMIT VALUE=\"Seeking\">"
        ."&nbsp;</TD>"
        ."</FORM>";
}
echo "</FORM>"
    ."</TR>"
    ."</TABLE>"
    ."<BR>";




    if( ISSET( $_REQUEST['repeat'] ) )
    {
        ////////////FIRST, get how many actives we have to duplicate///////////
        $tribe = $db->Execute("SELECT * FROM $dbtables[tribes] WHERE tribeid = '$_SESSION[current_unit]'");
        db_op_result($tribe,__LINE__,__FILE__);
        $actives = $tribe->fields;
        echo "$actives[curam] actives available.<BR>";
        ////////////Now, let's figure out what they did last turn/////////
        $act = $db->Execute("SELECT * FROM $dbtables[last_turn] WHERE tribeid = '$_SESSION[current_unit]'");
        db_op_result($act,__LINE__,__FILE__);
        while( !$act->EOF  && $actives['curam'] > 0 )
        {
            $act_do = $act->fields;
            if( ( $act_do['actives'] + $actives['curam'] ) > 0 )
            {
                if( $act_do['skill_abbr'] == 'herd' )
                {
                $sql = $db->Prepare("SELECT type,amount from $dbtables[livestock] WHERE tribeid=?");
                $animals = $db->Execute($sql,array($_SESSION['current_unit']));
                db_op_result($animals,__LINE__,__FILE__);
                while(!$animals->EOF)
                {
                    $info = $animals->fields;
                    $name = $info['type'];
                    $amt = $info['amount'];
                    $$name = $amt;
                    //above is a variable variable, resulting in something like $Catttle = 3724;
                    //NOTE- animal names appear to be capitalized letters, this likely will be important
                    $animals->MoveNext();
                }
                $skills = $db->Prepare("SELECT level FROM $dbtables[skills] WHERE tribeid = ? AND abbr = 'herd'");
                $skill = $db->Execute($skills,array($_SESSION['current_unit']));
                db_op_result($skill,__LINE__,__FILE__);
                $skillinfo = $skill->fields;
                    //now we calculate herders required in total  and see if we have enough actives assigned
                    $cat_hors_dog_herd = 10 + $skillinfo['level'];
                    $eleph_herd = 5 + $skillinfo['level'];
                    $goat_pig_shp_herd = 20 + $skillinfo['level'];
                    $required_herders = 0;
                    $cattle = abs(round($Cattle/$cat_hors_dog_herd));
                    $horse = abs(round($Horses/$cat_hors_dog_herd));
                    $dogs = abs(round($Dogs/$cat_hors_dog_herd));
                    $elephants = abs(round($Elephants/$eleph_herd));
                    $goat = abs(round($Goats/$goat_pig_shp_herd));
                    $sheep = abs(round($Sheep/$goat_pig_shp_herd));
                    $pigs = abs(round($Pigs/$goat_pig_shp_herd));
                    $required_herders = abs($cattle+$horse+$dogs+$elephants+$goat+$sheep+$pigs);
                    $act_do['actives'] = $required_herders;
                }
                $query = $db->Execute("INSERT INTO $dbtables[activities] "
                            ."VALUES("
                            ."'',"
                            ."'$act_do[tribeid]',"
                            ."'$act_do[skill_abbr]',"
                            ."'$act_do[product]',"
                            ."'$act_do[actives]')");
               db_op_result($query,__LINE__,__FILE__);
            }
            $actives['curam'] -= $act_do['actives'];
            $query = $db->Execute("UPDATE $dbtables[tribes] SET curam = $actives[curam] WHERE tribeid = '$_SESSION[current_unit]'");
            db_op_result($query,__LINE__,__FILE__);
            $act->MoveNext();
        }
    }
    if( !ISSET( $module ) && ISSET( $_REQUEST['cancel'] ) )
    {
        $query = $db->Execute("DELETE FROM $dbtables[activities] WHERE tribeid = '$_SESSION[current_unit]' AND id = '$_REQUEST[id]' AND skill_abbr = '$_REQUEST[skill_abbr]' AND product = '$_REQUEST[cancel]'");
       db_op_result($query,__LINE__,__FILE__);
        $query = $db->Execute("UPDATE $dbtables[tribes] SET curam = curam + '$_REQUEST[actives]' WHERE tribeid = '$_SESSION[current_unit]'");
        db_op_result($query,__LINE__,__FILE__);
    }
    if( !ISSET( $module ) )
    {
        if( !empty( $job[0] ) && !empty( $job[1] ) )
        {
            echo "$job[0] actives assigned to $job[1].<BR><P>";
        }
        echo "<TABLE BORDER=0 WIDTH=\"100%\">";
        echo "<TR CLASS=color_header ALIGN=CENTER><TD COLSPAN=2>";
        echo '<A HREF=farmingacts.php>Farming</A> | ';
        echo '<A HREF=scouting.php>Scouting</A> | ';
        echo '<A HREF=garrisons.php>Garrisons</A> | ';
        echo '<A HREF=goodstribe.php>Change Goods Tribe</A></TD></TR>';
        echo '<TR><TD VALIGN=TOP>';
        echo "<TABLE BORDER=0 ALIGN=CENTER><TR CLASS=color_header VALIGN=TOP><TD colspan=4 ALIGN=CENTER>";
        echo '<FONT SIZE=+2>Select an activity from the list below:</FONT></TD></TR><TR><TD COLSPAN=4 ALIGN=CENTER>';
        echo '<FORM ACTION=activities.php METHOD=POST>';
        echo '<SELECT NAME=skilltype>';

        $skill = $db->Execute("SELECT * from $dbtables[skills] WHERE tribeid = '$_SESSION[current_unit]' ORDER BY long_name ");
        db_op_result($skill,__LINE__,__FILE__);
        $act_do = $db->Execute("SELECT * FROM $dbtables[activities] WHERE tribeid = '$_SESSION[current_unit]'");
        db_op_result($act_do,__LINE__,__FILE__);
        echo "<OPTION VALUE=\"\" SELECTED></OPTION>";

        while( !$skill->EOF )
        {
            $skillinfo = $skill->fields;
            $act = $db->Execute("SELECT * FROM $dbtables[skill_table] WHERE abbr = '$skillinfo[abbr]' AND min_level <= '$skillinfo[level]' AND auto = 'N'");
            db_op_result($act,__LINE__,__FILE__);
            $actinfo = $act->fields;

            if( ISSET( $actinfo['display'] ) )
            {
                echo "<OPTION VALUE=$actinfo[abbr]>$actinfo[display]</OPTION>";
            }
            $skill->MoveNext();
        }
        echo '</SELECT>';
        echo '&nbsp;<INPUT TYPE=SUBMIT VALUE=Submit>';
        echo '</FORM></TD></TR><TR><TD ';
        echo "COLSPAN=4 CLASS=color_header ";
        echo 'align=center>Allocated Actives</TD></TR>';
        echo "<TR CLASS=color_header><TD ALIGN=CENTER>";
        echo 'Skill Abbr</TD><TD ALIGN=CENTER>Product</TD><TD ';
        echo 'ALIGN=CENTER>Actives</TD><TD ALIGN=CENTER>Cancel</TD></TR>';

        if( $act_do->EOF )
        {
            $r = 0;
            echo "<TR CLASS=color_row$rc><TD COLSPAN=4 ALIGN=CENTER>None</TD></TR>";
            $rc = $r % 2;
            $r++;

            echo "<TR CLASS=color_row$rc><TD COLSPAN=4 ALIGN=CENTER>";
            echo "<FORM ACTION=activities.php METHOD=POST>";
            echo "<INPUT TYPE=SUBMIT NAME=repeat VALUE=\"Repeat Last Turn's Activities\"></FORM></TD></TR>";
        }
        $r = 0;
        while( !$act_do->EOF )
        {
            $act_do_info = $act_do->fields;
            $rc = $r % 2;
            $r++;

            echo "<TR CLASS=color_row$rc><TD>";
            echo "$act_do_info[skill_abbr]</TD>";
            echo "<TD>$act_do_info[product]</TD>";
            echo "<TD>$act_do_info[actives]</TD>";
            echo "<TD ALIGN=CENTER><FORM ACTION=activities.php METHOD=POST>";
            echo "<INPUT TYPE=HIDDEN NAME=actives VALUE=\"$act_do_info[actives]\">";
            echo "<INPUT TYPE=HIDDEN NAME=id VALUE=\"$act_do_info[id]\">";
            echo "<INPUT TYPE=HIDDEN NAME=cancel VALUE=\"$act_do_info[product]\">";
            echo "<INPUT TYPE=HIDDEN NAME=skill_abbr VALUE=\"$act_do_info[skill_abbr]\">";
            echo "<INPUT TYPE=SUBMIT VALUE=CANCEL></FORM></TD></TR>";
            $act_do->MoveNext();
        }
        echo "</TABLE>";

        echo '<P>';
        $tribe = $db->Execute("SELECT * FROM $dbtables[tribes] WHERE tribeid = '$_SESSION[current_unit]'");
        db_op_result($tribe,__LINE__,__FILE__);
        $tribeinfo = $tribe->fields;
        echo "<CENTER><TABLE BORDER=0 width=48%><TR>";
        echo "<TD CLASS=color_header COLSPAN=4 ALIGN=CENTER>Current Crops</TD></TR>";
        $fm = $db->Execute("SELECT * FROM $dbtables[farming] WHERE hex_id = '$tribeinfo[hex_id]' AND clanid = '$tribeinfo[clanid]'");
        db_op_result($fm,__LINE__,__FILE__);
        if( $fm->EOF )
        {
            echo "<TR CLASS=color_row0><TD COLSPAN=4 ALIGN=CENTER>None</TD></TR>";
        }
        else
        {
            echo "<TR CLASS=color_header ALIGN=CENTER>";
            echo "<TD>Tile</TD>";
            echo "<TD>Crop</TD>";
            echo "<TD>Status</TD>";
            echo "<TD>Acres</TD></TR>";
            $r = 0;
            while( !$fm->EOF )
            {
                $farm = $fm->fields;
                $rc = $r % 2;
                $r++;
                echo "<TR CLASS=color_row$rc ALIGN=CENTER>";
                echo "<TD>$farm[hex_id]</TD>";
                echo "<TD>$farm[crop]</TD>";
                echo "<TD>$farm[status]</TD>";
                echo "<TD>$farm[acres]</TD></TR>";
                $fm->MoveNext();
            }
        }
        echo '</TABLE></CENTER>';

        echo '<P>';

        //mstsck_list($_SESSION['current_unit']);

        echo "</TD>";

        echo "<TD VALIGN=TOP><TABLE BORDER=0 VALIGN=TOP><TR>";
        echo "<TD CLASS=color_header COLSPAN=2>Resources And Products Available</TD></TR>";
        $stuff = $db->Execute("SELECT * FROM $dbtables[products] WHERE tribeid = '$tribeinfo[goods_tribe]' AND amount > 0 ORDER BY long_name");
        db_op_result($stuff,__LINE__,__FILE__);
        $totalstuff = 0;
        $r = 0;
        while( !$stuff->EOF )
        {
            $stuffinfo = $stuff->fields;
            $rc =$r % 2;
            $r++;
            echo "<TR CLASS=color_row$rc>";
            echo "<TD>$stuffinfo[proper]</TD>";
            echo "<TD>$stuffinfo[amount]</TD></TR>";
            $totalstuff++;
            $stuff->MoveNext();
        }
        $stuff = array();
        $stuff = $db->Execute("SELECT * FROM $dbtables[resources] WHERE tribeid = '$tribeinfo[goods_tribe]' AND amount > 0 ORDER BY long_name");
        db_op_result($stuff,__LINE__,__FILE__);
        while( !$stuff->EOF )
        {
            $stuffinfo = $stuff->fields;
            $rc =$r % 2;
            $r++;
            echo "<TR CLASS=color_row$rc>";
            echo "<TD>$stuffinfo[long_name]</TD>";
            echo "<TD>$stuffinfo[amount]</TD></TR>";
            $totalstuff++;
            $stuff->MoveNext();
        }
        $stuff = array();
        $stuff = $db->Execute("SELECT * FROM $dbtables[livestock] WHERE tribeid = '$tribeinfo[goods_tribe]' AND amount > 0 ORDER BY type");
         db_op_result($stuff,__LINE__,__FILE__);
        while( !$stuff->EOF )
        {
            $stuffinfo = $stuff->fields;
            $rc = $r % 2;
            $r++;
            echo "<TR CLASS=color_row$rc>";
            echo "<TD>$stuffinfo[type]</TD>";
            echo "<TD>$stuffinfo[amount]</TD></TR>";
            $totalstuff++;
            $stuff->MoveNext();
        }
        if( $totalstuff < 1 )
        {
            echo "<TR><TD COLSPAN=2>None</TD></TR>";
        }
            echo "</TABLE></TD></TR></TABLE>";

    }
    elseif( ISSET( $module ) && !ISSET( $_REQUEST['actives'] ) )
    {
        echo "<TABLE BORDER=0 WIDTH=\"100%\">";
        echo "<TR CLASS=color_header ALIGN=CENTER><TD COLSPAN=3>";
        echo "<A HREF=farmingacts.php>Farming</A> | ";
        echo "<A HREF=scouting.php>Scouting</A> | ";
        echo "<A HREF=garrisons.php>Garrisons</A> | ";
        echo "<A HREF=transfer.php?op=tribe>Change Goods Tribe</A></TD></TR>";
        echo "<TR VALIGN=TOP><TD COLSPAN=2 ALIGN=CENTER>";
        echo "<TABLE BORDER=0>";
        echo "<TR CLASS=color_row0><TD>Select the product desired.</TD><TD>";
        echo "<FORM ACTION=activities.php METHOD=POST>";
        echo "<SELECT NAME=produce>";
        foreach( $_REQUEST as $abbr )
        {
            $skill = $db->Execute("SELECT * FROM $dbtables[skills] WHERE tribeid = '$_SESSION[current_unit]' AND abbr = '$abbr'");
             db_op_result($skill,__LINE__,__FILE__);
            $skillinfo = $skill->fields;
            $res1 = $db->Execute("SELECT * FROM $dbtables[product_table] WHERE skill_abbr = '$abbr' AND skill_level <= '$skillinfo[level]' ORDER BY proper");
            db_op_result($res1,__LINE__,__FILE__);
            while( !$res1->EOF )
            {
                $resinfo = $res1->fields;
                $tribe = $db->Execute("SELECT * FROM $dbtables[tribes] WHERE tribeid = '$_SESSION[current_unit]'");
                db_op_result($tribe,__LINE__,__FILE__);
                $tribeinfo = $tribe->fields;
                $option++;
                echo "<OPTION VALUE=$resinfo[long_name].$resinfo[prod_id]>$resinfo[proper]</OPTION>";
                $res1->MoveNext();
            }
        }
        echo "</SELECT></TD></TR>";
        if( $resinfo['skill_abbr'] == 'seek' )
        {
            echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=seeking.php\">";
            page_footer();
        }
        if( $resinfo['skill_abbr'] == 'farm' )
        {
            echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=farmingacts.php\">";
            page_footer();
        }

        $active = $db->Execute("SELECT * FROM $dbtables[tribes] WHERE tribeid = '$_SESSION[current_unit]'");
        db_op_result($active,__LINE__,__FILE__);
        $activeinfo = $active->fields;
        if( $resinfo['skill_abbr'] == 'herd' )
        {
                $sql = $db->Prepare("SELECT type,amount from $dbtables[livestock] WHERE tribeid=?");
                $animals = $db->Execute($sql,array($_SESSION['current_unit']));
                db_op_result($animals,__LINE__,__FILE__);
                while(!$animals->EOF)
                {
                    $info = $animals->fields;
                    $name = $info['type'];
                    $amt = $info['amount'];
                    $$name = $amt;
                    //above is a variable variable, resulting in something like $Catttle = 3724;
                    //NOTE- animal names appear to be capitalized letters, this likely will be important
                    $animals->MoveNext();
                }
                $skills = $db->Prepare("SELECT level FROM $dbtables[skills] WHERE tribeid = ? AND abbr = 'herd'");
                $skill = $db->Execute($skills,array($_SESSION['current_unit']));
                db_op_result($skill,__LINE__,__FILE__);
                $skillinfo = $skill->fields;
            //now we calculate herders required in total  and see if we have enough actives assigned
            $cat_hors_dog_herd = 10 + $skillinfo['level'];
            $eleph_herd = 5 + $skillinfo['level'];
            $goat_pig_shp_herd = 20 + $skillinfo['level'];
            $required_herders = 0;
            $cattle = abs(round($Cattle/$cat_hors_dog_herd));
            $horse = abs(round($Horses/$cat_hors_dog_herd));
            $dogs = abs(round($Dogs/$cat_hors_dog_herd));
            $elephants = abs(round($Elephants/$eleph_herd));
            $goat = abs(round($Goats/$goat_pig_shp_herd));
            $sheep = abs(round($Sheep/$goat_pig_shp_herd));
            $pigs = abs(round($Pigs/$goat_pig_shp_herd));
            $required_herders = abs($cattle+$horse+$dogs+$elephants+$goat+$sheep+$pigs);

            echo "<TR bgcolor=$color_line2>";
            echo "<TD>Allocate </TD>";
            echo "<TD><INPUT CLASS=edit_area NAME=actives TYPE=TEXT SIZE=5 MAXLENGTH=7 VALUE=''></TD>";
            echo "</TR>";
            echo "<TR CLASS=color_row0><TD>Actives remaining:</TD>";
            echo "<TD ALIGN=CENTER>$activeinfo[curam]</TD></TR>";
            echo "<TR bgcolor=$color_line2><TD>Herders Required:</TD>";
            echo "<TD ALIGN=CENTER>$required_herders</TD></TR>";
        }
        else
        {
            echo "<TR bgcolor=$color_line2><TD>Allocate</TD>";
            echo "<TD><INPUT CLASS=edit_area NAME=actives TYPE=TEXT SIZE=5 MAXLENGTH=7 VALUE=''></TD>";
            echo "</TR>";
            echo "<TR CLASS=color_row0><TD>Actives remaining:</TD>";
            echo "<TD ALIGN=CENTER>$activeinfo[curam]</TD></TR>";
        }
        $cap = $db->Execute("SELECT * FROM $dbtables[skill_table] WHERE abbr = '$_REQUEST[skilltype]'");
        db_op_result($cap,__LINE__,__FILE__);
        $capinfo = $cap->fields;
        $skill = $db->Execute("SELECT * FROM $dbtables[skills] WHERE tribeid = '$_SESSION[current_unit]' AND abbr = '$_POST[skilltype]'");
         db_op_result($skill,__LINE__,__FILE__);
        $skillinfo = $skill->fields;
        if( $capinfo['level_cap'] == 'Y' && $skillinfo['level'] < 10 )
        {
            $maxallowed = $skillinfo['level'] * 10;
            if( $maxallowed > $tribeinfo['curam'] )
            {
                $maxallowed = $tribeinfo['curam'];
            }
        }
        else
        {
            $maxallowed = $tribeinfo['curam'];
        }
        echo "<TR CLASS=color_row0><TD>Max Assignable:</TD>";
        echo "<TD ALIGN=CENTER>$maxallowed</TD></TR>";
        echo "<TR><TD ALIGN=CENTER COLSPAN=2><INPUT TYPE=HIDDEN NAME=skilltype VALUE=1><BR>";
        echo "<INPUT TYPE=SUBMIT VALUE=ALLOCATE></FORM></TD></TR></TABLE>";
        echo "</TD><TD><TABLE BORDER=0>";
        echo "<TR><TD CLASS=color_header COLSPAN=2>Resources And Products Available</TD></TR>";
        $tribe = $db->Execute("SELECT * FROM $dbtables[tribes] WHERE tribeid = '$_SESSION[current_unit]'");
        db_op_result($tribe,__LINE__,__FILE__);
        $tribeinfo = $tribe->fields;
        $stuff = $db->Execute("SELECT * FROM $dbtables[products] WHERE tribeid = '$tribeinfo[goods_tribe]' AND amount > 0 ORDER BY long_name");
         db_op_result($stuff,__LINE__,__FILE__);
        $totalstuff = 0;
        $r = 0;
        while( !$stuff->EOF )
        {
            $stuffinfo = $stuff->fields;
            $rc = $r % 2;
            $r++;
            echo "<TR CLASS=color_row$rc><TD>$stuffinfo[proper]</TD>";
            echo "<TD>$stuffinfo[amount]</TD></TR>";
            $totalstuff++;
        $stuff->MoveNext();
        }
        $stuff = array();
        $stuff = $db->Execute("SELECT * FROM $dbtables[resources] WHERE tribeid = '$tribeinfo[goods_tribe]' AND amount > 0 ORDER BY long_name");
        db_op_result($stuff,__LINE__,__FILE__);
        while( !$stuff->EOF )
        {
            $stuffinfo = $stuff->fields;
            if( $linecolor == $color_line1 )
            {
                $r = 0;
            }
            elseif( $linecolor == $color_line2 )
            {
                $linecolor = $color_line1;
            }
            echo "<TR CLASS=color_row$rc><TD>$stuffinfo[long_name]</TD>";
            echo "<TD>$stuffinfo[amount]</TD></TR>";
        $totalstuff++;
        $stuff->MoveNext();
        }
        if( $totalstuff < 1 )
        {
            echo "<TR><TD COLSPAN=2>None</TD></TR>";
        }
        echo "</TABLE></TD></TR></TABLE>";
    }
    elseif( ISSET( $module ) && ISSET( $_REQUEST['actives'] ) && $_REQUEST['actives'] > 0 )
    {
        foreach( $_REQUEST as $long_name )
        {
            $group = explode( '.', $long_name );
            $abb = $db->Execute("SELECT skill_abbr FROM $dbtables[product_table] WHERE long_name = '$group[0]' AND prod_id = '$group[1]'");
            db_op_result($abb,__LINE__,__FILE__);
            if( !$abb->EOF )
            {
                $abbinfo = $abb->fields;
            }
        }
        $check = $db->Execute("SELECT curam FROM $dbtables[tribes] WHERE tribeid = $_SESSION[current_unit]");
         db_op_result($check,__LINE__,__FILE__);
        $checkinfo = $check->fields;
        if( $checkinfo['curam'] >= $_REQUEST['actives'] )
        {
            $group = explode( '.', $_REQUEST['produce'] );
            if( $abbinfo['skill_abbr'] == '' && $group[0] == '' )
            {
                $abbinfo['skill_abbr'] = 'Relax';
                $group[0] = 'Meditation';
            }
            $here = $db->Execute("SELECT * FROM $dbtables[activities] WHERE skill_abbr = '$abbinfo[skill_abbr]' AND product = '$group[0]' AND tribeid = '$_SESSION[current_unit]'");
             db_op_result($here,__LINE__,__FILE__);
            if( $here->EOF )
            {
                $query = $db->Execute("INSERT INTO $dbtables[activities] "
                            ."VALUES("
                            ."'',"
                            ."'$_SESSION[current_unit]',"
                            ."'$abbinfo[skill_abbr]',"
                            ."'$group[0]',"
                            ."'$_REQUEST[actives]')");
                 db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("UPDATE $dbtables[tribes] SET curam = curam - $_REQUEST[actives] WHERE tribeid = '$_SESSION[current_unit]'");
                 db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("UPDATE $dbtables[skills] SET turn_done = 'Y' WHERE tribeid = '$_SESSION[current_unit]' AND abbr = '$abbinfo[skill_abbr]'");
                 db_op_result($query,__LINE__,__FILE__);
                $job = $_REQUEST['actives'] . '.' . $group[0];
            }
            else
            {
                $job = false;
            }
            echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=activities.php?job=$job\">";
        }
        else
        {
            echo "You do not have that many actives unallocated.<BR>";
            echo "Click <a href=activities.php>here</a> to try again.<BR>";
            TEXT_GOTOMAIN();
        }
    }


page_footer();
?>
