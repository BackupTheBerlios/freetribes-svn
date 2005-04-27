<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: religion.php

session_start();
header("Cache-control: private");
include("config.php");
include("game_time.php");

page_header("Religion");

connectdb();

$res = $db->Execute("SELECT * FROM $dbtables[chiefs] "
                   ."WHERE clanid = '$_SESSION[clanid]'");
$playerinfo = $res->fields;

$_REQUEST[description] = addslashes($_REQUEST[description]);

if( $_REQUEST[join] == 'Join' | $_REQUEST[leave] == 'Leave' )
{
    if ( $_REQUEST[myid] && $_REQUEST[join] )
    {
        echo "You cannot join another religion until you leave your current religion.<BR>";
        echo "Return to the <A HREF=religion.php>religion</A> page.<BR>";
        page_footer();
    }
    $res = $db->Execute("SELECT * FROM $dbtables[tribes] "
                       ."WHERE clanid = '$_SESSION[clanid]'");
    $ath_count = 0;
    $rel_count = 0;
    while( !$res->EOF )
    {
        $tribe = $res->fields;
        $rel = $db->Execute("SELECT * FROM $dbtables[skills] "
                           ."WHERE tribeid = '$tribe[tribeid]' "
                           ."AND abbr = 'rel' AND level > 0");
        $religion = $rel->fields;
        $ath = $db->Execute("SELECT * FROM $dbtables[skills] "
                           ."WHERE tribeid = '$tribe[tribeid]' "
                           ."AND abbr = 'ath' AND level > 0");
        $atheism = $ath->fields;
        $rel_count += $religion[level];
        $ath_count += $atheism[level];
        $res->MoveNext();
    }
    if( $_REQUEST[relid] == '' && $_REQUEST[join] == 'Join' )
    {
        echo "<CENTER><FONT COLOR=WHITE>You have not selected a religion to join.<BR>";
        echo "Please <a href=religion.php>try again</A>.</FONT>";
        $filename = __FILE__;
        $filename = explode('/', $filename);
        $extension = 'txt';
        $linkname = str_replace('php', $extension, $filename[5]);
        $filename = $linkname;
        page_footer();
    }
    if( $rel_count > $ath_count && !$_REQUEST[relid] == '' && $_REQUEST['join'] == 'Join' )
    {
        $join = $db->Execute("SELECT * FROM $dbtables[religions] "
                            ."WHERE relid = '$_REQUEST[relid]'");
        $joininfo = $join->fields;
        echo "<FORM ACTION=religion.php METHOD=POST>";
        echo "Please select one of each of the following:<BR>";
        echo "<FONT COLOR=WHITE>Exclusivity: </FONT><SELECT NAME=exclusivity>";
        echo "<OPTION VALUE=Pluralism>Pluralism</OPTION><OPTION VALUE=Inclusivism>";
        echo "Inclusivism</OPTION><OPTION VALUE=Exclusivism>Exclusivism</OPTION>";
        echo "</SELECT><BR><FONT COLOR=WHITE>Proslytizing: </FONT><SELECT NAME=prostlytizing>";
        echo "<OPTION VALUE=None>None</OPTION><OPTION VALUE=Mild>Mild</OPTION>";
        echo "<OPTION VALUE=Strong>Strong</OPTION></SELECT><BR>";
        echo "Please Fill in a description on how your clan will practice ";
        echo "$joininfo[rel_display]";
        echo ".<BR>";
        echo "You may use HTML. You may choose not to change it or, <BR>";
        echo "you may modify to suit your clans approach to worship. <BR>";
        echo "But please remember to modify where ";
        echo "appropriate any changes that <BR>your clan will be making in observances.<BR>";
        echo "<TEXTAREA NAME=description ROWS=8 COLS=80>$joininfo[description]</TEXTAREA><P>";
        echo "&nbsp;<INPUT TYPE=HIDDEN NAME=relid VALUE=";
        echo "$_REQUEST[relid]";
        echo "><INPUT TYPE=HIDDEN NAME=relid VALUE=$_REQUEST[relid]>";
        echo "<INPUT TYPE=SUBMIT NAME=join2 VALUE=Continue></FORM><BR><P>";
    }
    elseif( $ath_count > $rel_count && $_REQUEST[join] == 'Join' )
    {
        echo "Youre not able to join a religion because of your peoples Atheism.";
    }
    elseif( $rel_count < 1 && $_REQUEST[join] == 'Join' )
    {
        echo "You do not have enough religious inspiration (skill) to join a religion at present.";
    }
    elseif( $rel_count > $ath_count && $_REQUEST[leave] == 'Leave' )
    {
        echo "You need to have more total athiesm than you have total religion skill.<BR>";
    }
    elseif( $ath_count > $rel_count && $_REQUEST[leave] == 'Leave' )
    {
        $cur = $db->Execute("SELECT * FROM $dbtables[religions] "
                           ."WHERE clanid = '$tribe[clanid]'");
        $currel = $cur->fields;
        echo "You have now left the $currel[rel_display]!";
        $db->Execute("DELETE FROM $dbtables[religions] "
                    ."WHERE clanid = '$tribe[clanid]' "
                    ."AND relid = '$currel[relid]'");
        $db->Execute("UPDATE $dbtables[clans] "
                    ."SET religion = 'None' "
                    ."WHERE clanid = '$tribe[clanid]'");
    }
}
if( $_REQUEST[join2] )
{
    echo "<FORM ACTION=religion.php METHOD=POST>";

	if( $_REQUEST[exclusivity] == 'Pluralism' )
    {
        $exc = $db->Execute("SELECT * FROM $dbtables[skill_table] "
                            ."WHERE exc_plural = 'Y'");
        echo "<SELECT NAME=exclude_skill>";
        while( !$exc->EOF )
        {
            $exc_skill = $exc->fields;
            echo "<OPTION VALUE=$exc_skill[abbr]>$exc_skill[long_name]</OPTION>";
            $exc->MoveNext();
        }
        echo "</SELECT>";
    }
    elseif( $_REQUEST[exclusivity] == 'Inclusivisim' )
    {
        $exc = $db->Execute("SELECT * FROM $dbtables[skill_table] "
                           ."WHERE exc_inclusive = 'Y'");
        echo "<SELECT NAME=exclude_skill>";
        while( !$exc->EOF )
        {
            $exc_skill = $exc->fields;
            echo "<OPTION VALUE=$exc_skill[abbr]>$exc_skill[long_name]</OPTION>";
            $exc->MoveNext();
        }
        echo "</SELECT>";

    }
    elseif( $_REQUEST[exclusivity] == 'Exclusivism' )
    {
        $exc = $db->Execute("SELECT * FROM $dbtables[skill_table] "
                           ."WHERE exc_exclusive = 'Y'");
        echo "<SELECT NAME=exclude_skill>";
        while( !$exc->EOF )
        {
            $exc_skill = $exc->fields;
            echo "<OPTION VALUE=$exc_skill[abbr]>$exc_skill[long_name]</OPTION>";
            $exc->MoveNext();
        }
        echo "</SELECT>";

    }

    echo "<SELECT NAME=exclude_skill_type>";
    echo "<OPTION VALUE=1>Skill Attempts</OPTION>";
    echo "<OPTION VALUE=2>Activity Bonus</OPTION>";
    echo "</SELECT>";
    echo "<SELECT NAME=exclude_skill_amount>";
    echo "<OPTION VALUE=.05>5%</OPTION>";
    echo "<OPTION VALUE=.1>10%</OPTION>";
    echo "<OPTION VALUE=.15>15%</OPTION>";
    echo "</SELECT>";
    $prost = 0;

    if( $_REQUEST[proslytizing] == 'None' )
    {
        $pros = $db->Execute("SELECT * FROM $dbtables[skill_table] "
                            ."WHERE prost_none = 'Y'");
        if( !$pros->EOF )
        {
            echo "<SELECT NAME=pros_skill>";
            while( !$pros->EOF )
            {
                $pros_skill = $pros->fields;
                echo "<OPTION VALUE=$pros_skill[abbr]>$pros_skill[long_name]</OPTION>";
                $prost++;
                $pros->MoveNext();
            }
            echo "</SELECT>";
        }
    }
    elseif( $_REQUEST[proslytizing] == 'Mild' )
    {
        $pros = $db->Execute("SELECT * FROM $dbtables[skill_table] "
                            ."WHERE prost_mild = 'Y'");
        if( !$pros->EOF )
        {
            echo "<SELECT NAME=pros_skill>";
            while( !$pros->EOF )
            {
                $pros_skill = $pros->fields;
                echo "<OPTION VALUE=$pros_skill[abbr]>$pros_skill[long_name]</OPTION>";
                $prost++;
                $pros->MoveNext();
            }
            echo "</SELECT>";
        }

    }
    elseif( $_REQUEST[proslytizing] == 'Strong' )
    {
        $pros = $db->Execute("SELECT * FROM $dbtables[skill_table] "
                            ."WHERE prost_mild = 'Y'");
        if( !$pros->EOF )
        {
            echo "<SELECT NAME=pros_skill>";
            while( !$pros->EOF )
            {
                $pros_skill = $pros->fields;
                echo "<OPTION VALUE=$pros_skill[abbr]>$pros_skill[long_name]</OPTION>";
                $prost++;
                $pros->MoveNext();
            }
            echo "</SELECT>";
        }
    }
    if( $prost > 0 )
    {
    echo "<SELECT NAME=pros_skill_type>";
    echo "<OPTION VALUE=1>Skill Attempts</OPTION>";
    echo "<OPTION VALUE=2>Activity Bonus</OPTION>";
    echo "</SELECT>";
    echo "<SELECT NAME=pros_skill_amount>";
    echo "<OPTION VALUE=.05>5%</OPTION>";
    echo "<OPTION VALUE=.1>10%</OPTION>";
    echo "<OPTION VALUE=.15>15%</OPTION>";
    echo "</SELECT>";
    }

    $rel = $db->Execute("SELECT * FROM $dbtables[religions] "
                       ."WHERE relid = '$_REQUEST[relid]'");
    $relinfo = $rel->fields;
    if( $relinfo[holidays] > 0 )
    {
        echo "<BR>Please select the first month you will declare as holidays.&nbsp;";
        echo "<SELECT NAME=holiday1>";
        echo "<OPTION VALUE=1>Month 1</OPTION>";
        echo "<OPTION VALUE=2>Month 2</OPTION>";
        echo "<OPTION VALUE=3>Month 3</OPTION>";
        echo "<OPTION VALUE=5>Month 5</OPTION>";
        echo "<OPTION VALUE=6>Month 6</OPTION>";
        echo "<OPTION VALUE=7>Month 7</OPTION>";
        echo "<OPTION VALUE=8>Month 8</OPTION>";
        echo "<OPTION VALUE=9>Month 9</OPTION>";
        echo "<OPTION VALUE=11>Month 11</OPTION>";
        echo "<OPTION VALUE=12>Month 12</OPTION>";
        echo "</SELECT>";
    }
    if( $relinfo[holidays] > 1 )
    {
        echo "<BR>Please select the second month you will declare as holidays.&nbsp;";
        echo "<SELECT NAME=holiday2>";
        echo "<OPTION VALUE=1>Month 1</OPTION>";
        echo "<OPTION VALUE=2>Month 2</OPTION>";
        echo "<OPTION VALUE=3>Month 3</OPTION>";
        echo "<OPTION VALUE=5>Month 5</OPTION>";
        echo "<OPTION VALUE=6>Month 6</OPTION>";
        echo "<OPTION VALUE=7>Month 7</OPTION>";
        echo "<OPTION VALUE=8>Month 8</OPTION>";
        echo "<OPTION VALUE=9>Month 9</OPTION>";
        echo "<OPTION VALUE=11>Month 11</OPTION>";
        echo "<OPTION VALUE=12>Month 12</OPTION>";
        echo "</SELECT>";
    }
    if( $relinfo[holidays] > 2 )
    {
        echo "<BR>Please select the third month you will declare as holidays.&nbsp;";
        echo "<SELECT NAME=holiday3>";
        echo "<OPTION VALUE=1>Month 1</OPTION>";
        echo "<OPTION VALUE=2>Month 2</OPTION>";
        echo "<OPTION VALUE=3>Month 3</OPTION>";
        echo "<OPTION VALUE=5>Month 5</OPTION>";
        echo "<OPTION VALUE=6>Month 6</OPTION>";
        echo "<OPTION VALUE=7>Month 7</OPTION>";
        echo "<OPTION VALUE=8>Month 8</OPTION>";
        echo "<OPTION VALUE=9>Month 9</OPTION>";
        echo "<OPTION VALUE=11>Month 11</OPTION>";
        echo "<OPTION VALUE=12>Month 12</OPTION>";
        echo "</SELECT>";
    }
    echo "<INPUT TYPE=HIDDEN NAME=exclusivity VALUE=$_REQUEST[exclusivity]>";
    echo "<INPUT TYPE=HIDDEN NAME=relid VALUE=$_REQUEST[relid]>";
    echo "<INPUT TYPE=HIDDEN NAME=prostlytizing VALUE=$_REQUEST[prostlytizing]>";
    echo "<INPUT TYPE=HIDDEN NAME=description VALUE=\"$_REQUEST[description]\">";
    echo "<BR>";
    echo "<INPUT TYPE=SUBMIT NAME=join3 VALUE=Continue></FORM>";
    echo "<P>";
} 

if( $_REQUEST[join3] )
{
    
    $clan = $db->Execute("SELECT * FROM $dbtables[tribes] "
                        ."WHERE tribeid = '$_SESSION[current_unit]'");
    $claninfo = $clan->fields;
    $rel = $db->Execute("SELECT * FROM $dbtables[religions] "
                       ."WHERE clanid = '$claninfo[clanid]'");
    if( !$rel->EOF )
    {
        $relinfo = $rel->fields;
        $rel = $db->Execute("SELECT * FROM $dbtables[religions] "
                           ."WHERE relid = '$relinfo[relid]'");
        $relinfo = $rel->fields;
        echo "You are already a member of the ";
        echo "$relinfo[rel_display]";
        echo ".<BR>";
    }
    elseif( $_REQUEST[holiday1] == $_REQUEST[holiday2] | $_REQUEST[holiday1] == $_REQUEST[holiday3] )
    {
        echo "You cannot select the same month for multiple holidays.<BR>";
    }
    elseif( $_REQUEST[holiday2] == $_REQUEST[holiday3] && $_REQUEST[holiday2] > 0 )
    {
        echo "You cannot select the same month for multiple holidays.<BR>";
    }
    else
    {
        $joininfo = $rel->fields;
        $rel = $db->Execute("SELECT * FROM $dbtables[religions] "
                           ."WHERE relid = '$_REQUEST[relid]'");
        $relinfo = $rel->fields;
        $relinfo[generation] += 1;
        $debug = $db->Execute("INSERT INTO $dbtables[religions] "
                             ."VALUES("
                             ."'',"
                             ."'$relinfo[family]',"
                             ."'$relinfo[generation]',"
                             ."'$_SESSION[clanid]',"
                             ."'$relinfo[cannibal]',"
                             ."'$relinfo[rel_abbr]',"
                             ."'$relinfo[rel_display]',"
                             ."'$relinfo[holidays]',"
                             ."'$_REQUEST[holiday1]',"
                             ."'$_REQUEST[holiday2]',"
                             ."'$_REQUEST[holiday3]',"
                             ."'$relinfo[rel_arch]',"
                             ."'$relinfo[arch_skill1]',"
                             ."'$relinfo[arch_skill1_type]',"
							."'$relinfo[arch_skill1_amount]',"
							."'$relinfo[arch_skill2]',"
							."'$relinfo[arch_skill2_type]',"
							."'$relinfo[arch_skill2_amount]',"
							."'$relinfo[arch_pen1]',"
							."'$relinfo[arch_pen1_type]',"
							."'$relinfo[arch_pen1_amount]',"
							."'$relinfo[arch_pen2]',"
							."'$relinfo[arch_pen2_type]',"
							."'$relinfo[arch_pen2_amount]',"
							."'$relinfo[healers]',"
							."'$relinfo[healer_name]',"
							."'$relinfo[infantry]',"
							."'$relinfo[infantry_name]',"
							."'$relinfo[calvalry]',"
							."'$relinfo[calvalry_name]',"
							."'$_REQUEST[exclusivity]',"
							."'$_REQUEST[exclude_skill]',"
							."'$_REQUEST[exclude_skill_type]',"
							."'$_REQUEST[exclude_skill_amount]',"
							."'$_REQUEST[prostlytizing]',"
							."'$_REQUEST[pros_skill]',"
							."'$_REQUEST[pros_skill_type]',"
							."'$_REQUEST[pros_skill_amount]',"
							."'$_REQUEST[description]',"
							."'$relinfo[inf_weapon1]',"
							."'$relinfo[inf_head_armor]',"
							."'$relinfo[inf_torso_armor]',"
							."'$relinfo[inf_otorso_armor]',"
							."'$relinfo[inf_legs_armor]',"
							."'$relinfo[inf_shield]',"
							."'$relinfo[cav_weapon1]',"
							."'$relinfo[cav_head_armor]',"
							."'$relinfo[cav_torso_armor]',"
							."'$relinfo[cav_otorso_armor]',"
							."'$relinfo[cav_legs_armor]',"
							."'$relinfo[cav_shield]',"
							."'$relinfo[cav_horse_armor]')");
        $db->Execute("UPDATE $dbtables[clans] "
                    ."SET religion = '$relinfo[rel_display]' "
                    ."WHERE clanid = '$_SESSION[clanid]'");
                
        echo "You have just joined the ";
        echo "$relinfo[rel_display]";
        echo ".<BR>";

		echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=religion.php\">";
		page_footer();
    }
}


	// CREATE RELIGION


if($_REQUEST[create] ){
$res = $db->Execute("SELECT * FROM $dbtables[tribes] WHERE clanid = '$_SESSION[clanid]'");
$ath_count = 0;
$rel_count = 0;
while(!$res->EOF)
{
$tribe = $res->fields;
$rel = $db->Execute("SELECT * FROM $dbtables[skills] WHERE tribeid = '$tribe[tribeid]' AND abbr = 'rel' AND level > 0");
$religion = $rel->fields;
$ath = $db->Execute("SELECT * FROM $dbtables[skills] WHERE tribeid = '$tribe[tribeid]' AND abbr = 'ath' AND level > 0");
$atheism = $ath->fields;
$rel_count += $religion[level];
$ath_count += $atheism[level];
$res->MoveNext();
}
$atheistic = $ath_count + 5;
if($rel_count < 10){
echo "Your clan must have a combined Religion skill of at least 10 to found a new religion.<BR>";
}
elseif($rel_count <= $atheistic){
echo "Your clan must have 5 more combined levels in Religion than it has in Athiesm.<BR>";
}
else{
echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=religion2.php\">";
page_footer();
}

}


///////////////////////////////////View Religions///////////////////////////////////////////
$hasrel = $db->Execute("SELECT * FROM $dbtables[religions] "
                      ."WHERE clanid = '$_SESSION[clanid]'");
$hasinfo = $hasrel->fields;

if( !$hasrel->EOF )
{
    $disabled = " DISABLED";
}

echo "<TABLE BORDER=0>"
	."<TR>"
	."<FORM ACTION=religion.php METHOD=POST>"
	."<TD>"
	."<INPUT TYPE=SUBMIT NAME=create VALUE=\"Create Religion\"$disabled>"
	."</TD>"
	."</FORM>"
	."<TD>To create a new religion, you have to leave your current religion."
	."</TR>"
	."</TABLE>";

echo "<P>";

echo "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=\"80%\" ALIGN=CENTER>";


$relstart = $db->Execute("SELECT rel_display, relid FROM $dbtables[religions] "
                        ."WHERE generation = 0");


if( $relstart->EOF )
{
    echo "<TR CLASS=color_header>";
    echo "<TD>&nbsp;</TD><TD>Religion Name:</TD><TD>Religion Type:</TD></TR>";
    echo "<TR CLASS=color_row0 ALIGN=CENTER><TD COLSPAN=3>No Religions Created</TD></TR>";
    $filename = __FILE__;
    $filename = explode('/', $filename);
    $extension = 'txt';
    $linkname = str_replace('php', $extension, $filename[5]);
    $filename = $linkname;
    page_footer();
}

echo "<TR CLASS=color_header>";
echo "<TD>&nbsp;</TD><TD><B>Religion Name</B></TD>";
echo "<TD><B>Religion Type</B></TD></TR>";
while( !$relstart->EOF )
{
    $relist = $relstart->fields;
    $rel = $db->Execute("SELECT * FROM $dbtables[religions] "
                       ."WHERE rel_display = '$relist[rel_display]' "
                       ."AND relid = '$relist[relid]'");
    if( $rel->EOF )
    {
        echo "<TR CLASS=color_row0 ALIGN=CENTER>"
			."<TD COLSPAN=3>No Religions Created</TD>"
			."</TR>";
    }


	// OWN RELIGION


	$mine = $db->Execute("SELECT * FROM $dbtables[religions] "
                        ."WHERE clanid = '$_SESSION[clanid]'");
    $myrel = $mine->fields;
    if( !$mine->EOF )
    {
        echo "<TR CLASS=color_row0>"
			."<FORM METHOD=POST ACTION=religion.php>"
			."<TD>"
			
			."<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0>"
			."<TR>";

        if( !$_REQUEST[myid] == $myrel[relid] )
        {
            echo "<TD>"
				."<INPUT TYPE=HIDDEN NAME=myid VALUE=$myrel[relid]>"
				."<INPUT TYPE=SUBMIT NAME=op VALUE=View>"
				."</TD>";
        }
        else
        {
            echo "<TD>"
				."<INPUT TYPE=SUBMIT VALUE=Hide>&nbsp;"
				."</TD>"
				."</FORM>"
				."<FORM METHOD=POST ACTION=religion.php>"
				."<TD>"
				."<INPUT TYPE=HIDDEN NAME=relid VALUE=$myrel[relid]>&nbsp;"
				."<INPUT TYPE=SUBMIT NAME=leave VALUE=Leave>"
				."</TD>";
        }

		echo "</TR>"
			."</TABLE>"

			."<TD>$myrel[rel_display]</TD>"
			."<TD><B>$myrel[rel_arch]</B> (our religion)</TD>"
			."</TD>"
			."</FORM>"
			."</TR>";

		if( $_REQUEST[myid] == $myrel[relid] )
		{
			display_religion($myrel, true);
			$got_religion = true;
		}

	}


	// OTHER RELIGIONS


	$r = 1;
	while(!$rel->EOF)
	{
		$rc = $r % 2;
		$r++;

		$religion = $rel->fields;


		if(empty($religion[rel_display]))
		{
			$religion[rel_display] = 'Heathenism';
		}

        echo "<TR CLASS=color_row$rc>"
			."<FORM METHOD=POST ACTION=religion.php>"
			."<TD>"
			
			."<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0>"
			."<TR>";

		if(!$_REQUEST[relid] == $religion[relid])
		{
            echo "<TD>"
				."<INPUT TYPE=HIDDEN NAME=relid VALUE=$religion[relid]>"
				."<INPUT TYPE=SUBMIT NAME=op VALUE=View>"
				."</TD>";
		}
		else
		{
            echo "<TD>"
				."<INPUT TYPE=SUBMIT VALUE=Hide>&nbsp;"
				."</TD>";

			if ($religion['rel_display'] <> $myrel['rel_display'])
			{
			echo "</FORM>"
				."<FORM METHOD=POST ACTION=religion.php>"
				."<TD>"
				."<INPUT TYPE=HIDDEN NAME=relid VALUE=$religion[relid]>&nbsp;"
				."<INPUT TYPE=SUBMIT NAME=join VALUE=Join>"
				."</TD>";
				
			}
		}

		echo "</TR>"
			."</TABLE>"

			."<TD>$religion[rel_display]</TD>"
			."<TD><B>$religion[rel_arch]</B> (our religion)</TD>"
			."</TD>"
			."</FORM>"
			."</TR>";

		if($_REQUEST[relid] == $religion[relid])
		{
			display_religion($religion, false);
		}

		$rel->MoveNext();
	}


	$relstart->MoveNext();
}

echo "</TABLE>";

page_footer();



function display_religion ($myrel, $show_local_info)
{
	global $db, $dbtables;
	//DISPLAY DETAILS OF RELIGION

	echo "<TR CLASS=table1_td_cc>"
		."<TD COLSPAN=3 WIDTH=\"100%\">";

	// Religion Name

	echo "<TABLE ALIGN=CENTER WIDTH=\"100%\" "
		."BORDER=0 CELLPADDING=4 CELLSPACING=0>"
		."<TR WIDTH=\"100%\">"
		."<TD VALIGN=TOP ALIGN=CENTER WIDTH=\"100%\">"
		."<FONT COLOR=BLACK CLASS=page_subtitle>"
		."<B>$myrel[rel_display]</B></FONT>"
		."</TD>"
		."</TR>"
		."</TABLE>";

	echo "<TR CLASS=table1_td_cc>"
		."<TD COLSPAN=3>";

	echo "<TABLE ALIGN=CENTER WIDTH=\"100%\" style=\"border: thin inset;\""
		."CELLPADDING=4 CELLSPACING=0>"

		// Archetype, Holidays & Global Skills

		."<TR>"
		."<TD VALIGN=TOP WIDTH=\"33%\">"
		."<FONT COLOR=BLACK CLASS=text_medium>"
		."<B>Archetype</B><BR>$myrel[rel_arch]</FONT>"
		."</TD>"
		."<TD VALIGN=TOP WIDTH=\"33%\">"
		."<FONT COLOR=BLACK CLASS=text_medium><B>Holidays</B>"
		."<BR>";

		if( $myrel[holiday1] > 0 )
		{
			echo "$myrel[holiday1] ";
		}
		if( $myrel[holiday2] > 0 )
		{
			echo ", $myrel[holiday2] ";
		}
		if( $myrel[holiday3] > 0 )
		{
			echo ", $myrel[holiday3]";
		}

	echo "</FONT></TD>"
		."<TD VALIGN=TOP WIDTH=\"33%\">"
		."<FONT COLOR=BLACK CLASS=text_medium>"
		."<B>Global Skills</B>"
		."<BR>";

		if( !$myrel[arch_skill1] == '' )
		{
			$skill = $db->Execute ("SELECT long_name FROM skill_table "
									."WHERE abbr='".$myrel[arch_skill1]."'");
			$skill_name = $skill->fields[long_name];
			$percent = 100 * $myrel[arch_skill1_amount];
			echo "$skill_name $percent%, ";
		}
		if( !$myrel[arch_skill2] == '' )
		{
			$skill = $db->Execute ("SELECT long_name FROM skill_table "
									."WHERE abbr='".$myrel[arch_skill2]."'");
			$skill_name = $skill->fields[long_name];
			$percent = 100 * $myrel[arch_skill2_amount];
			echo "$skill_name $percent%";
		}
		if( !$myrel[arch_pen1] == '' )
		{
			$skill = $db->Execute ("SELECT long_name FROM skill_table "
									."WHERE abbr='".$myrel[arch_pen1]."'");
			$skill_name = $skill->fields[long_name];
			$percent = 100 * $myrel[arch_pen1_amount];
			echo "<BR>"
				."<FONT COLOR=RED CLASS=text_medium>$skill_name $percent%</FONT>";
		}
		if( !$myrel[arch_pen2] == '' )
		{
			$skill = $db->Execute ("SELECT long_name FROM skill_table "
									."WHERE abbr='".$myrel[arch_pen2]."'");
			$skill_name = $skill->fields[long_name];
			$percent = 100 * $myrel[arch_pen2_amount];
			echo ", <FONT COLOR=RED CLASS=text_medium>$skill_name $percent%</FONT>";
		}

	echo "<BR></FONT>"
		."</TD>"
		."</TR>";

	// Cannibalism

	if( $myrel[cannibal] == 'Y' )
	{
		echo "<TR>"
			."<TD COLSPAN=3>"
			."<FONT COLOR=BLACK CLASS=text_medium>Cannibals<BR></FONT>"
			."</TD>"
			."</TR>";
	}

	// Exclusivity, Proselytisation & Local Skills

	if ( $show_local_info )
	{
		echo "<TR>"
			."<TD VALIGN=TOP WIDTH=\"33%\">"
			."<FONT COLOR=BLACK CLASS=text_medium>"
			."<B>Exclusivity</B><BR>$myrel[rel_exclude]</FONT>"
			."</TD>"
			."<TD VALIGN=TOP WIDTH=\"33%\">"
			."<FONT COLOR=BLACK CLASS=text_medium>"
			."<B>Prostlytize</B><BR>$myrel[rel_prostlytize]</FONT>"
			."</TD>"
			."<TD VALIGN=TOP WIDTH=\"33%\">"
			."<FONT COLOR=BLACK CLASS=text_medium>"
			."<B>Local Skills</B>"
			."<BR>";

		if( !$myrel[exclude_skill] == '' )
		{
			$skill = $db->Execute ("SELECT long_name FROM skill_table "
									."WHERE abbr='".$myrel[exclude_skill]."'");
			$skill_name = $skill->fields[long_name];
			$percent = 100 * $myrel[exclude_skill_amount];
			echo "$skill_name $percent% ";
		}
		if( !$myrel[pros_skill] == '' )
		{
			$skill = $db->Execute ("SELECT long_name FROM skill_table "
									."WHERE abbr='".$myrel[pros_skill]."'");
			$skill_name = $skill->fields[long_name];
			$percent = 100 * $myrel[pros_skill_amount];
			echo "$skill_name $percent% ";
		}

		echo "</FONT><BR>"
			."</TD>";
	}



	// Religious Combat Units


	echo "<TR>";
	echo "<TD COLSPAN=3>"
		."<FONT COLOR=BLACK CLASS=text_medium>";

		echo "<TABLE WIDTH=\"100%\" ALIGN=CENTER>"
			."<TR>";
		if( $myrel[healers] == 'Y' )
		{
			echo "<TD VALIGN=TOP WIDTH=\"33%\">"
				."<FONT COLOR=BLACK CLASS=text_medium>"
				."<B>$myrel[healer_name]</B><BR>(Level 5 healers)"
				."</TD>";
		}
		if( $myrel[infantry] == 'Y' )
		{
			echo "<TD VALIGN=TOP WIDTH=\"33%\">"
				."<FONT COLOR=BLACK CLASS=text_medium>"
				."<B>$myrel[infantry_name]</B>"
				."<BR>(Level 10 infantry)<BR>"
				."$myrel[inf_weapon1], "
				."$myrel[inf_head_armor], "
				."$myrel[inf_torso_armor],"
				." <BR>$myrel[inf_otorso_armor], "
				."$myrel[inf_legs_armor], "
				."$myrel[inf_shield]."
				."<BR></FONT></TD>";
		}
		if( $myrel[calvalry] == 'Y' )
		{
			echo "<TD VALIGN=TOP WIDTH=\"33%\">"
				."<FONT COLOR=BLACK CLASS=text_medium>"
				."<B>$myrel[calvalry_name]</B>"
				."<BR>(Level 15 calvalry)<BR>"
				."$myrel[cav_weapon1], "
				."$myrel[cav_head_armor], "
				."$myrel[cav_torso_armor],"
				." <BR>$myrel[cav_otorso_armor], "
				."$myrel[cav_legs_armor], "
				."$myrel[cav_shield], "
				."$myrel[cav_horse_armor]."
				."<BR></FONT></TD>";
		}
		echo "</TR>"
			."</TABLE>";
	
	echo "<P>"
		."</TD>"
		."</TR>";


	// Display Religious Doctrine


	echo "<TR>"
		."<TD COLSPAN=3>";

	echo "<TABLE WIDTH=\"100%\" BORDER=0 CELLPADDING=0 CELLSPACING=0>"
		."<TR>"
		."<TD>";

		echo "<TABLE WIDTH=\"80%\" style=\"border: thin outset;\" "
			."BORDER=0 CELLPADDING=8 CELLSPACING=0 ALIGN=CENTER>"
			."<TR>"
			."<TD>"
			."<FONT COLOR=black>"
			."<FONT CLASS=page_subtitle><B>Religious Doctrine</B></FONT>"
			."<p>"
			."<P ALIGN=JUSTIFY>$myrel[description]</FONT>"
			."</TD>"
			."</TR>"
			."</TABLE>";

	echo "</TD>"
		."</TR>"
		."</FORM>"
		."</TABLE>"
		."<BR>"
		."<BR>"
		."</TD>"
		."</TR>"
		."</TABLE>";
//	}
}
?>
