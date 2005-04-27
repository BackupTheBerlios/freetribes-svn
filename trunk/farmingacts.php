<?
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: farmingacts.php

session_start();
header("Cache-control: private");

include("config.php");
include("game_time.php");

page_header("Farming Activities");

connectdb();

$username = $_SESSION['username'];

echo "<BR>Click <A HREF=farmingacts.php>Here</A> to return to the farming main screen.<br><p>";
$module = $_POST['action'];


    $deva = $db->Execute("SELECT * FROM $dbtables[tribes] "
                        ."WHERE tribeid = '$_SESSION[current_unit]'");
    $devainfo = $deva->fields;

    if( !$devainfo[DeVA] == '0000.00' )
    {
        echo 'You are under DeVA, and must break the seige before conducting activities.<BR>';
        page_footer();
    }
    if( !ISSET( $module ) && ISSET( $_REQUEST[cancel] ) )
    {
        $db->Execute("DELETE FROM $dbtables[farm_activities] "
                    ."WHERE tribeid = '$_SESSION[current_unit]' "
                    ."AND id = '$_REQUEST[id]' "
                    ."AND crop = '$_REQUEST[cancel]'");

        $db->Execute("UPDATE $dbtables[tribes] "
                    ."SET curam = curam + '$_REQUEST[actives]' "
                    ."WHERE tribeid = '$_SESSION[current_unit]'");
    }
    if( !ISSET( $module ) )
    {
        if( !empty( $job[0] ) && !empty( $job[1] ) )
        {
            echo "$job[0] actives assigned to $job[1].<BR><P>";
        }
        echo "<TABLE BORDER=0 WIDTH=\"100%\">";
        echo "<TR BGCOLOR=$color_header ALIGN=CENTER><TD COLSPAN=2>";
        echo '<A HREF=activities.php>Activities</A> | <A HREF=scouting.php>Scouting</A> | <A HREF=garrisons.php>Garrisons</A> | <A HREF=goodstribe.php>Change Goods Tribe</A></TD></TR>';
        echo '<TR><TD VALIGN=TOP>';
        echo "<TABLE BORDER=0 ALIGN=CENTER><TR bgcolor=$color_header><TD colspan=4 ALIGN=CENTER>";
        echo '<FONT SIZE=+2>Select an activity from the list below:</FONT></TD></TR><TR><TD COLSPAN=4 ALIGN=CENTER>';
        echo '<FORM ACTION=farmingacts.php METHOD=POST>';
        echo '<SELECT NAME=action>';

        $act_do = $db->Execute("SELECT * FROM $dbtables[farm_activities] "
                              ."WHERE tribeid = '$_SESSION[current_unit]'");
        $tribe = $db->Execute("SELECT * FROM $dbtables[tribes] "
                             ."WHERE tribeid = '$_SESSION[current_unit]'");
        $tribeinfo = $tribe->fields;

        echo "<OPTION VALUE=\"\" SELECTED></OPTION>";
        $display = false;
        if( $month[count] == 3 | $month[count] == 4 | $month[count] == 5 | $month[count] == 6 )
        {
            echo '<OPTION VALUE=plow>Plow</OPTION>';
            echo '<OPTION VALUE=plant>Plant</OPTION>';
            $display = true;
        }
        else
        {
            echo '<OPTION VALUE=plant>Plant</OPTION>';
            $display = true;
        }
        $ready = $db->Execute("SELECT * FROM $dbtables[farming] "
                             ."WHERE hex_id = '$tribeinfo[hex_id]' "
                             ."AND clanid = '$tribeinfo[clanid]' "
                             ."AND status = 'Ready' "
                             ."OR hex_id = '$tribeinfo[hex_id]' "
                             ."AND clanid = '$tribeinfo[clanid]' "
                             ."AND status = 'Seed'");
        if( !$ready->EOF )
        {
			echo '<OPTION VALUE=harvest>Harvest</OPTION>';
			$display = true;
        }
        if( !$display == true )
        {
            echo '<OPTION VALUE=none>No Farming Available</OPTION>';
        } 
        echo '</SELECT>';
        echo '&nbsp;<INPUT TYPE=SUBMIT VALUE=Submit>';
        echo '</FORM></TD></TR><TR><TD ';
        echo "COLSPAN=4 bgcolor=$color_header ";
        echo 'align=center>Allocated Actives</TD></TR>';
        echo "<TR bgcolor=$color_header><TD ALIGN=CENTER>";
        echo 'Crop</TD><TD ALIGN=CENTER>Actives</TD><TD ';
        echo 'ALIGN=CENTER>Activity</TD><TD ALIGN=CENTER>Cancel</TD></TR>';

        if( $act_do->EOF )
        {
            $line_col = $color_line1;
            echo "<TR bgcolor=$line_col><TD COLSPAN=4 ALIGN=CENTER>None</TD></TR>";
        }
    $line_col = $color_line1;
    while(!$act_do->EOF)
	{
		$act_do_info = $act_do->fields;
		if($line_col == $color_line1)
		{
			$line_col = $color_line2;
		}
		elseif($line_col == $color_line2)
		{
			$line_col = $color_line1;
		}
		echo "<TR bgcolor=$line_col><TD>$act_do_info[crop]</TD><TD>$act_do_info[actives]</TD><TD>$act_do_info[action]</TD><TD ALIGN=CENTER><FORM ACTION=farmingacts.php METHOD=POST><INPUT TYPE=HIDDEN NAME=actives VALUE=\"$act_do_info[actives]\"><INPUT TYPE=HIDDEN NAME=id VALUE=\"$act_do_info[id]\"><INPUT TYPE=HIDDEN NAME=cancel VALUE=\"$act_do_info[crop]\"><INPUT TYPE=SUBMIT VALUE=CANCEL></FORM></TD></TR>";
		$act_do->MoveNext();
		}
		echo "</TABLE>";
		echo '<P>';
		echo "<CENTER><TABLE BORDER=0 width=48%><TR><TD bgcolor=$color_header COLSPAN=4 ALIGN=CENTER>Current Crops</TD></TR>";
		$fm = $db->Execute("SELECT * FROM $dbtables[farming] WHERE hex_id = '$tribeinfo[hex_id]' AND clanid = '$tribeinfo[clanid]'");
		if($fm->EOF)
		{
			echo "<TR bgcolor=$color_line1><TD COLSPAN=4 ALIGN=CENTER>None</TD></TR>";
		}
		else
		{
			echo "<TR bgcolor=$color_header ALIGN=CENTER><TD>Tile</TD><TD>Crop</TD><TD>Status</TD><TD>Acres</TD></TR>";
			$line_col = $color_line1;
			while(!$fm->EOF)
			{
				$farm = $fm->fields;
				echo "<TR BGCOLOR=$line_col ALIGN=CENTER><TD>$farm[hex_id]</TD><TD>$farm[crop]</TD><TD>$farm[status]</TD><TD>$farm[acres]</TD></TR>";
				if($line_col == $color_line1)
					{
					$line_col = $color_line2;
					}
				else
					{
					$line_col = $color_line1;
					}
				$fm->MoveNext();
			}
		}
		echo '</TABLE></CENTER>';


		echo "</TD><TD><TABLE BORDER=0><TR><TD bgcolor=$color_header COLSPAN=2>Farming Tools Available</TD></TR>";


		$tribe = $db->Execute("SELECT * FROM $dbtables[tribes] WHERE tribeid = '$_SESSION[current_unit]'");
		$tribeinfo = $tribe->fields;
		$stuff = $db->Execute("SELECT * FROM $dbtables[products] "
							 ."WHERE tribeid = '$tribeinfo[goods_tribe]' "
							 ."AND long_name = 'plow' "
							 ."AND amount > 0 OR "
							 ."tribeid = '$tribeinfo[goods_tribe]' "
							 ."AND long_name = 'rake' "
							 ."AND amount > 0 "
							 ."OR tribeid = '$tribeinfo[goods_tribe]' "
							 ."AND long_name = 'hoe' "
							 ."AND amount > 0");
		$totalstuff = 0;
		$linecolor = $color_line2;

		while(!$stuff->EOF){
				$stuffinfo = $stuff->fields;
		if($linecolor == $color_line1){
		$linecolor = $color_line2;
		}
		elseif($linecolor == $color_line2){
		$linecolor = $color_line1;
		}

				echo "<TR bgcolor=$linecolor><TD>$stuffinfo[proper]</TD><TD>$stuffinfo[amount]</TD></TR>";
				$totalstuff++;
				$stuff->MoveNext();
		}

		if($totalstuff < 1){
				echo "<TR><TD COLSPAN=2>None</TD></TR>";
		}
				echo "</TABLE></TD></TR></TABLE>";

	  }
	  elseif(ISSET($module) && !ISSET($_REQUEST[actives]))
	  {
		echo "<TABLE BORDER=0 WIDTH=\"100%\">";
		echo "<TR BGCOLOR=$color_header ALIGN=CENTER><TD COLSPAN=3>";
		echo "<A HREF=activities.php>Activities</A> | <A HREF=scouting.php>Scouting</A> | <A HREF=garrisons.php>Garrisons</A> | <A HREF=goodstribe.php>Change Goods Tribe</A></TD></TR>";

		echo "<TR><TD COLSPAN=2 ALIGN=CENTER>";
		echo "<TABLE BORDER=0>";
		echo "<TR bgcolor=$color_line1><TD>Select the crop desired.</TD><TD>";
		echo "<FORM ACTION=farmingacts.php METHOD=POST>";
		echo "<SELECT NAME=produce>";
		$skill = $db->Execute("SELECT * FROM $dbtables[skills] "
							 ."WHERE tribeid = '$_SESSION[current_unit]' "
							 ."AND abbr = 'farm'");
		$skillinfo = $skill->fields;
		if( $_REQUEST[action] == 'harvest' )
		{
		$res = $db->Execute("SELECT * FROM $dbtables[tribes] WHERE tribeid = '$_SESSION[current_unit]'");
		$tribe = $res->fields;
		$crop = $db->Execute("SELECT * FROM $dbtables[farming] "
							."WHERE clanid = '$tribe[clanid]' "
							."AND hex_id = '$tribe[hex_id]' "
							."AND status = 'Ready'");
		while( !$crop->EOF )
		{
			$croptype = $crop->fields;
			echo "<OPTION VALUE=$croptype[crop]>$croptype[crop]</OPTION>";
			$crop->MoveNext();
		}
		}
		else
		{
		$crop = $db->Execute("SELECT * FROM $dbtables[product_table] "
							."WHERE skill_abbr = 'farm' "
							."AND skill_level <= '$skillinfo[level]'");
		while( !$crop->EOF )
		{
			$croptype = $crop->fields;
			echo "<OPTION VALUE=$croptype[long_name]>$croptype[proper]</OPTION>";
			$crop->MoveNext();
		}
    }

    
    echo "</SELECT></TD></TR>";
    $active = $db->Execute("SELECT * FROM $dbtables[tribes] where tribeid = '$_SESSION[current_unit]'"); 
    $activeinfo = $active->fields;
    echo "<TR bgcolor=$color_line2><TD>Allocate</TD><TD><INPUT CLASS=edit_area NAME=actives TYPE=TEXT SIZE=5 MAXLENGTH=7 VALUE=''></TD></TR><TR bgcolor=$color_line1><TD>Actives remaining:</TD><TD ALIGN=CENTER>$activeinfo[curam]</TD></TR>";
    echo "<TR><TD ALIGN=CENTER COLSPAN=2><INPUT TYPE=HIDDEN NAME=action VALUE=\"$_REQUEST[action]\"><BR><INPUT TYPE=SUBMIT VALUE=ALLOCATE></FORM></TD></TR></TABLE>";



    echo "</TD><TD><TABLE BORDER=0><TR><TD bgcolor=$color_header COLSPAN=2>Farming Tools Available</TD></TR>";


    $tribe = $db->Execute("SELECT * FROM $dbtables[tribes] WHERE tribeid = '$_SESSION[current_unit]'");
    $tribeinfo = $tribe->fields;
    $stuff = $db->Execute("SELECT * FROM $dbtables[products] "
                         ."WHERE tribeid = '$tribeinfo[goods_tribe]' "
                         ."AND long_name = 'plow' "
                         ."AND amount > 0 OR "
                         ."tribeid = '$tribeinfo[goods_tribe]' "
                         ."AND long_name = 'rake' "
                         ."AND amount > 0 "
                         ."OR tribeid = '$tribeinfo[goods_tribe]' "
                         ."AND long_name = 'hoe' "
                         ."AND amount > 0");

    $totalstuff = 0;
    $linecolor = $color_line2;

    while(!$stuff->EOF){
	    $stuffinfo = $stuff->fields;
    if($linecolor == $color_line1){
    $linecolor = $color_line2;
    }
    elseif($linecolor == $color_line2){
    $linecolor = $color_line1;
    }

	    echo "<TR bgcolor=$linecolor><TD>$stuffinfo[proper]</TD><TD>$stuffinfo[amount]</TD></TR>";
	    $totalstuff++;
	    $stuff->MoveNext();
    }

    if($totalstuff < 1){
	    echo "<TR><TD COLSPAN=2>None</TD></TR>";
    }
	    echo "</TABLE></TD></TR></TABLE>";
  }





   elseif( ISSET($module) && ISSET($_REQUEST[actives]) && $_REQUEST[actives] > 0 )
   {
        $check = $db->Execute("SELECT * FROM $dbtables[tribes] WHERE tribeid = $_SESSION[current_unit]");
        $checkinfo = $check->fields;
        if( $checkinfo[curam] >= $_REQUEST[actives] )
        {
            $skill = $db->Execute("SELECT * FROM $dbtables[skills] "
                                 ."WHERE tribeid = '$checkinfo[tribeid]' "
                                 ."AND abbr = 'farm'");
            $skillinfo = $skill->fields;
            $db->Execute("INSERT INTO $dbtables[farm_activities] "
                        ."VALUES("
                        ."'',"
                        ."'$_SESSION[clanid]',"
                        ."'$_SESSION[current_unit]',"
                        ."'$checkinfo[hex_id]',"
                        ."'$_REQUEST[produce]',"
                        ."'$_REQUEST[action]',"
                        ."'$_REQUEST[actives]',"
                        ."'$skillinfo[level]')");
            $db->Execute("UPDATE $dbtables[tribes] "
                        ."SET curam = curam - $_REQUEST[actives] "
                        ."WHERE tribeid = '$_SESSION[current_unit]'");
        }
        else
        {
            echo "You do not have that many actives unallocated.<BR>Click <a href=farmingacts.php>here</a> to try again.<BR>";
        }
            echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=farmingacts.php\">";;
    }
    else
    {
        echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=farmingacts.php\">";;
    }

  
page_footer();

?> 
