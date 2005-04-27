<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: religion2.php

session_start();
header("Cache-control: private");
include("config.php");
page_header("Religion");

include("game_time.php");

connectdb();


$res = $db->Execute("SELECT * FROM $dbtables[chiefs] "
                   ."WHERE clanid = '$_SESSION[clanid]'");
$playerinfo = $res->fields;


echo "Click <A HREF=religion.php>here </A>to go back to the main religion page.<BR>";
echo "<P>";



//////Check to make sure they don't have a religion already//////

$rel = $db->Execute("SELECT * FROM $dbtables[religions] "
                   ."WHERE clanid = '$_SESSION[clanid]'");
if( !$rel->EOF )
{
    $relinfo = $rel->fields;
    echo "You already belong to the $relinfo[rel_display] religion.<BR>";
    echo "You must leave that religion first, before you can found your own.<BR>";
    $filename = __FILE__;
    $filename = explode('/', $filename);
    $extension = 'txt';
    $linkname = str_replace('php', $extension, $filename[5]);
    $filename = $linkname;
    page_footer();
}

//////Check to make sure they've got enough religion skill./////

$res = $db->Execute("SELECT * FROM $dbtables[tribes] "
                   ."WHERE clanid = '$_SESSION[clanid]'");
$minreq = 0;
while( !$res->EOF )
{
    $tribe = $res->fields;
    $rel = $db->Execute("SELECT * FROM $dbtables[skills] "
                       ."WHERE abbr = 'rel' "
                       ."AND tribeid = '$tribe[tribeid]' "
                       ."AND level > 4");
    if( !$rel->EOF )
    {
        $minreq++;
    }
    $res->MoveNext();
}
if( $minreq < 1 )
{
    echo "You need at least one tribe in your clan with a religion skill of 5 or more<BR>";
    echo "to found a new religion.<BR>";
    $filename = __FILE__;
    $filename = explode('/', $filename);
    $extension = 'txt';
    $linkname = str_replace('php', $extension, $filename[5]);
    $filename = $linkname;
    page_footer();
} 
$res = array();
/////Check to make sure they've got more religion than atheism////

$res = $db->Execute("SELECT * FROM $dbtables[tribes] "
                   ."WHERE clanid = '$_SESSION[clanid]'");
$ath_count = 0;
$rel_count = 0;

while( !$res->EOF )
{
    $tribe = $res->fields;
    $rel = $db->Execute("SELECT * FROM $dbtables[skills] "
                       ."WHERE tribeid = '$tribe[tribeid]' "
                       ."AND abbr = 'rel' "
                       ."AND level > 0");
    $religion = $rel->fields;
    $ath = $db->Execute("SELECT * FROM $dbtables[skills] "
                       ."WHERE tribeid = '$tribe[tribeid]' "
                       ."AND abbr = 'ath' "
                       ."AND level > 0");
    $atheism = $ath->fields;
    $rel_count += $religion[level];
    $ath_count += $atheism[level];
    $res->MoveNext();
}
$atheism_show = $ath_count;
$ath_count += 5;
if( $ath_count >= $rel_count )
{
    echo "You need to have at least 5 levels of religion more than your combined atheism skill<BR>";
    echo "to found a new religion. <BR>";
    echo "Combined Religion: ($rel_count)<BR>";
    echo "Combined Atheism: ($atheism_show)<BR>";
    $filename = __FILE__;
    $filename = explode('/', $filename);
    $extension = 'txt';
    $linkname = str_replace('php', $extension, $filename[5]);
    $filename = $linkname;
    page_footer();
}

//////////////////////////////////////////////////////////////////////////////////

if( !$_REQUEST[create1] && !$_REQUEST[create2] )
{
    echo "<CENTER>";
    echo "<FORM ACTION=religion2.php METHOD=POST>";
    echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0>";
    echo "<TR BGCOLOR=$color_header><TD COLSPAN=2 ALIGN=CENTER>Global Settings</TD></TR>";
    echo "<TR>";
    echo "<TD>Enter a name for your religion:</TD>";
    echo "<TD><INPUT class=edit_area TYPE=TEXT SIZE=35 WIDTH=35 MAXLENGTH=35 NAME=rel_display VALUE=''></TD>";
    echo "</TR><TR>";
    echo "<TD>Enter the number of holidays you will observe:</TD>";
    echo "<TD>";
    echo "<SELECT NAME=holidays>";
    echo "<OPTION VALUE=0>None</OPTION>";
    echo "<OPTION VALUE=1>One</OPTION>";
    echo "<OPTION VALUE=2>Two</OPTION>";
    echo "<OPTION VALUE=3>Three</OPTION>";
    echo "</SELECT></TD>";
    echo "</TR><TR>";
    echo "<TD>Select the Archtype of your religion:</TD>";
    echo "<TD><SELECT NAME=rel_arch>";
    echo "<OPTION VALUE=Animism>Animism</OPTION>";
    echo "<OPTION VALUE=Totemism>Totemism</OPTION>";
    echo "<OPTION VALUE=Pantheism>Pantheism</OPTION>";
    echo "<OPTION VALUE=Polytheism>Polytheism</OPTION>";
    echo "<OPTION VALUE=Henotheism>Henotheism</OPTION>";
    echo "<OPTION VALUE=Duotheism>Duotheism</OPTION>";
    echo "<OPTION VALUE=Monotheism>Monotheism</OPTION>";
    echo "<OPTION VALUE=Panentheism>Panentheism</OPTION>";
    echo "</SELECT></TD></TR><TR>";
    echo "<TD>Is your religion cannibalistic?</TD>";
    echo "<TD><SELECT NAME=cannibal>";
    echo "<OPTION VALUE=N SELECTED>No</OPTION>";
    echo "<OPTION VALUE=Y>Yes</OPTION>";
    echo "</TD></TR><TR>";
    echo "<TD>Will your religion have healers/monks/clerics at level 5?</TD>";
    echo "<TD><SELECT NAME=healers>";
    echo "<OPTION VALUE=Y>Y</OPTION>";
    echo "<OPTION VALUE=N>N</OPTION>";
    echo "</SELECT></TD></TR><TR>";
    echo "<TD>Will your religion have religious infantry at level 10?</TD>";
    echo "<TD><SELECT NAME=infantry>";
    echo "<OPTION VALUE=Y>Y</OPTION>";
    echo "<OPTION VALUE=N>N</OPTION>";
    echo "</SELECT></TD></TR><TR>";
    echo "<TD>Will your religion have religious calvalry/knights at level 15?</TD>";
    echo "<TD><SELECT NAME=calvalry>";
    echo "<OPTION VALUE=Y>Y</OPTION>";
    echo "<OPTION VALUE=N>N</OPTION>";
    echo "</SELECT></TD></TR><TR>";
    echo "<TD COLSPAN=2 ALIGN=CENTER>Please enter a description of your religion.</TD></TR>";
    echo "<TR><TD COLSPAN=2 ALIGN=CENTER><TEXTAREA NAME=description ROWS=8 COLS=80></TEXTAREA></TD></TR>";
    echo "<TR><TD COLSPAN=2 ALIGN=CENTER><INPUT TYPE=SUBMIT NAME=create1 VALUE=Continue></TD></TR></TABLE></FORM>";
}

if( $_REQUEST[create1] && !$_REQUEST[create2] && $_REQUEST[rel_display] && $_REQUEST[description] )
{
    $_REQUEST[description] = addslashes($_REQUEST[description]);
    echo "<CENTER>";
    echo "<FORM ACTION=religion2.php METHOD=POST>";
    echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0>";
    echo "<TR BGCOLOR=$color_header><TD COLSPAN=2 ALIGN=CENTER>Global Settings</TD></TR>";
    echo "<TR>";
    echo "<TD><FONT COLOR=WHITE><B>Religion Name:</B></FONT> </TD>";
    echo "<TD>$_REQUEST[rel_display]<INPUT TYPE=HIDDEN NAME=rel_display VALUE=\"$_REQUEST[rel_display]\"></TD>";
    echo "</TR><TR>";
    if( $_REQUEST[cannibal] == 'Y' )
    {
        echo "<TD><FONT COLOR=WHITE><B>Cannibal:</B></TD>";
        echo "<TD>$_REQUEST[cannibal]<INPUT TYPE=HIDDEN NAME=cannibal VALUE=\"$_REQUEST[cannibal]\"></TD></TR><TR>";
    }
    echo "<TD><FONT COLOR=WHITE><B>Holidays:<B></FONT></TD>";
    echo "<TD>$_REQUEST[holidays]<INPUT TYPE=HIDDEN NAME=holidays VALUE=\"$_REQUEST[holidays]\"></TD>";
    echo "</TR><TR>";
    echo "<TD COLSPAN=2 ALIGN=CENTER>";
    if( $_REQUEST[holidays] > 0 )
    {
        echo "<FONT COLOR=WHITE><I>What Months will you observe?</I></FONT></TD></TR>";
        echo "<TD COLSPAN=2 ALIGN=CENTER>";
        echo "<SELECT NAME=holiday1>";
        echo "<OPTION VALUE=3>Month 3</OPTION>";
        echo "<OPTION VALUE=5>Month 5</OPTION>";
        echo "<OPTION VALUE=6>Month 6</OPTION>";
        echo "</SELECT>";
    }
    if( $_REQUEST[holidays] > 1 )
    {
        echo "<SELECT NAME=holiday2>";
        echo "<OPTION VALUE=7>Month 7</OPTION>";
        echo "<OPTION VALUE=8>Month 8</OPTION>";
        echo "<OPTION VALUE=9>Month 9</OPTION>";
        echo "</SELECT>";
    }
    if( $_REQUEST[holidays] > 2 )
    {
        echo "<SELECT NAME=holiday3>";
        echo "<OPTION VALUE=11>Month 11</OPTION>";
        echo "<OPTION VALUE=12>Month 12</OPTION>";
        echo "<OPTION VALUE=1>Month 1</OPTION>";
        echo "</SELECT>";
    }
    echo "</TD></TR><TR>";
    echo "<TD><FONT COLOR=WHITE><B>Archtype:</B></FONT></TD>";
    echo "<TD>$_REQUEST[rel_arch]<INPUT TYPE=HIDDEN NAME=rel_arch VALUE=\"$_REQUEST[rel_arch]\"></TD></TR>";
    echo "<TR><TD COLSPAN=2 ALIGN=CENTER><FONT COLOR=WHITE><I>";
    echo "Select your archtype skill and bonus, and select what skills you <B>DO NOT</B> want penalized.</I></FONT></TD></TR>";
    echo "<TR><TD COLSPAN=2 ALIGN=CENTER><SELECT NAME=arch_skill1>";
    /////Figure out which list of skills to present to the user//////
    $arch = 'arch_';

    if( $_REQUEST[rel_arch] == 'Animism' )
    {
        $arch .= 'animism';
    }
    elseif( $_REQUEST[rel_arch] == 'Totemism' )
    {
        $arch .= 'totemism';
    }
    elseif( $_REQUEST[rel_arch] == 'Pantheism' )
    {
        $arch .= 'pantheism';
    }
    elseif( $_REQUEST[rel_arch] == 'Polytheism' )
    {
        $arch .= 'polytheism';
    }
    elseif( $_REQUEST[rel_arch] == 'Henotheism' )
    {
        $arch .= 'henotheism';
    }
    elseif( $_REQUEST[rel_arch] == 'Duotheism' )
    {
        $arch .= 'dualism';
    }
    elseif( $_REQUEST[rel_arch] == 'Monotheism' )
    {
        $arch .= 'monotheism';
    }
    elseif( $_REQUEST[rel_arch] == 'Panentheism' )
    {
        $arch .= 'panentheism';
    }
    $arch_skll = $db->Execute("SELECT * FROM $dbtables[skill_table] "
                             ."WHERE $arch = 'Y'");
    while( !$arch_skll->EOF )
    {
        $arch_skill = $arch_skll->fields;
        echo "<OPTION VALUE=$arch_skill[abbr]>$arch_skill[long_name]</OPTION>";
        $arch_skll->MoveNext();
    }
    echo "</SELECT>";
    echo "<SELECT NAME=arch_skill1_amount>";
    echo "<OPTION VALUE=\".05\">5%</OPTION>";
    echo "<OPTION VALUE=\".1\">10%</OPTION>";
    echo "<OPTION VALUE=\".15\">15%</OPTION>";
    echo "</SELECT>&nbsp;&nbsp;&nbsp;";
    $arch_skll = $db->Execute("SELECT * FROM $dbtables[skill_table] "
                             ."WHERE $arch = 'Y'");
    echo "<SELECT NAME=arch_skill2>";
    while( !$arch_skll->EOF )
    {
        $arch_skill = $arch_skll->fields;
        echo "<OPTION VALUE=$arch_skill[abbr]>$arch_skill[long_name]</OPTION>";
        $arch_skll->MoveNext();
    }
    echo "</SELECT>";
    echo "<SELECT NAME=arch_skill2_amount>";
    echo "<OPTION VALUE=\".05\">5%</OPTION>";
    echo "<OPTION VALUE=\".1\">10%</OPTION>";
    echo "<OPTION VALUE=\".15\">15%</OPTION>";
    echo "</SELECT>";

    $pen_skll = $db->Execute("SELECT * FROM $dbtables[skill_table] "
                            ."WHERE $arch = 'Y'");
    echo "&nbsp;&nbsp;&nbsp;<SELECT NAME=arch_pen1>";
    while( !$pen_skll->EOF )
    {
        $arch_pen1 = $pen_skll->fields;
        echo "<OPTION VALUE=$arch_pen1[abbr]>No $arch_pen1[long_name] penalty</OPTION>";
        $pen_skll->MoveNext();
    }
    echo "</SELECT>&nbsp;&nbsp;&nbsp;";
    
    $pen_skll = $db->Execute("SELECT * FROM $dbtables[skill_table] "
                            ."WHERE $arch = 'Y'");
    echo "<SELECT NAME=arch_pen2>";
    while( !$pen_skll->EOF )
    {
        $arch_pen2 = $pen_skll->fields;
        echo "<OPTION VALUE=$arch_pen2[abbr]>No $arch_pen2[long_name] penalty</OPTION>";
        $pen_skll->MoveNext();
    }
    echo "</SELECT>";
        
    echo "</TD></TR>";
    echo "<TR><TD COLSPAN=2 ALIGN=CENTER>";
    echo "<FONT COLOR=WHITE><B>Healers: </B></FONT>$_REQUEST[healers]&nbsp; ";
    echo "<FONT COLOR=WHITE><B>Infantry:</B></FONT> $_REQUEST[infantry]&nbsp; ";
    echo "<FONT COLOR=WHITE><B>Calvalry:</B></FONT> $_REQUEST[calvalry]";
    echo "<INPUT TYPE=HIDDEN NAME=healers VALUE=$_REQUEST[healers]>";
    echo "<INPUT TYPE=HIDDEN NAME=infantry VALUE=$_REQUEST[infantry]>";
    echo "<INPUT TYPE=HIDDEN NAME=calvalry VALUE=$_REQUEST[calvalry]></TD></TR>";
    if( $_REQUEST[healers] == 'Y' )
    {
        echo "<TR><TD><FONT COLOR=WHITE>What are your healers/monks/clerics called:</FONT></TD>";
        echo "<TD><INPUT class=edit_area TYPE=TEXT SIZE=35 WIDTH=25 MAXLENGTH=25 NAME=healer_name VALUE=''></TD></TR>";
    }
    if( $_REQUEST[infantry] == 'Y' )
    {
        echo "<TR><TD><FONT COLOR=WHITE>Infantry Unit's Name:</FONT></TD>";
        echo "<TD><INPUT class=edit_area TYPE=TEXT SIZE=35 WIDTH=25 MAXLENGTH=25 NAME=infantry_name VALUE=''></TD></TR>";
        echo "<TR BGCOLOR=$color_line1><TD COLSPAN=2 ALIGN=LEFT><FONT COLOR=WHITE>";
        echo "<I>Select your religious infantry's armor and equipment:</I></FONT></TD></TR>";
        echo "<TR><TD COLSPAN=2><SELECT NAME=inf_weapon1>";
        $weap = $db->Execute("SELECT * FROM $dbtables[product_table] "
                            ."WHERE weapon = 'Y'");
        echo "<OPTION VALUE=''></OPTION>";
        while( !$weap->EOF )
        {
            $weapon = $weap->fields;
            echo "<OPTION VALUE=$weapon[long_name]>$weapon[proper]</OPTION>";
            $weap->MoveNext();
        }
        echo "</SELECT>";
        echo "<SELECT NAME=inf_head_armor>";
        $head = $db->Execute("SELECT * FROM $dbtables[armor] "
                            ."WHERE type = 'head'");
        echo "<OPTION VALUE=''></OPTION>";
        while( !$head->EOF )
        {
            $helm = $head->fields;
            echo "<OPTION VALUE=$helm[long_name]>$helm[proper]</OPTION>";
            $head->MoveNext();
        }
        echo "</SELECT>";
        echo "<SELECT NAME=inf_torso_armor>";
        $tors = $db->Execute("SELECT * FROM $dbtables[armor] "
                            ."WHERE type = 'torso'");
        echo "<OPTION VALUE=''></OPTION>";
        while( !$tors->EOF )
        {
            $torso = $tors->fields;
            echo "<OPTION VALUE=$torso[long_name]>$torso[proper]</OPTION>";
            $tors->MoveNext();
        }
        echo "</SELECT>";
        echo "<SELECT NAME=inf_otorso_armor>";
        $otors = $db->Execute("SELECT * FROM $dbtables[armor] "
                             ."WHERE type = 'overtorso'");
        echo "<OPTION VALUE=''></OPTION>";
        while( !$otors->EOF )
        {
            $otorso = $otors->fields;
            echo "<OPTION VALUE=$otorso[long_name]>$otorso[proper]</OPTION>";
            $otors->MoveNext();
        }
        echo "</SELECT>";
        echo "<SELECT NAME=inf_legs_armor>";
        $leg = $db->Execute("SELECT * FROM $dbtables[armor] "
                           ."WHERE type = 'leg'");
        echo "<OPTION VALUE=''></OPTION>";
        while( !$leg->EOF )
        {
            $legs = $leg->fields;
            echo "<OPTION VALUE=$legs[long_name]>$legs[proper]</OPTION>";
            $leg->MoveNext();
        }
        echo "</SELECT>";
        echo "<SELECT NAME=inf_shield>";
        $shld = $db->Execute("SELECT * FROM $dbtables[armor] "
                            ."WHERE type = 'shield'");
        echo "<OPTION VALUE=''></OPTION>";
        while( !$shld->EOF )
        {
            $shield = $shld->fields;
            echo "<OPTION VALUE=$shield[long_name]>$shield[proper]</OPTION>";
            $shld->MoveNext();
        }
        echo "</SELECT>";
        
        echo "</TD></TR>";
    }
    if( $_REQUEST[calvalry] == 'Y' )
    {
        echo "<TR><TD><FONT COLOR=WHITE>Calvalry Unit's Name:</FONT></TD>";
        echo "<TD><INPUT class=edit_area TYPE=TEXT SIZE=35 WIDTH=25 MAXLENGTH=25 NAME=calvalry_name VALUE=''></TD></TR>";
        echo "<TR BGCOLOR=$color_line1><TD COLSPAN=2 ALIGN=LEFT><FONT COLOR=WHITE>";
        echo "<I>Select your religious cavalry's armor and equipment:</I></FONT></TD></TR>";
        echo "<TR><TD COLSPAN=2><SELECT NAME=cav_weapon1>";
        $weap = $db->Execute("SELECT * FROM $dbtables[product_table] "
                            ."WHERE weapon = 'Y'");
        echo "<OPTION VALUE=''></OPTION>";
        while( !$weap->EOF )
        {
            $weapon = $weap->fields;
            echo "<OPTION VALUE=$weapon[long_name]>$weapon[proper]</OPTION>";
            $weap->MoveNext();
        }
        echo "</SELECT>";
        echo "<SELECT NAME=cav_head_armor>";
        $head = $db->Execute("SELECT * FROM $dbtables[armor] "
                            ."WHERE type = 'head'");
        echo "<OPTION VALUE=''></OPTION>";
        while( !$head->EOF )
        {
            $helm = $head->fields;
            echo "<OPTION VALUE=$helm[long_name]>$helm[proper]</OPTION>";
            $head->MoveNext();
        }
        echo "</SELECT>";
        echo "<SELECT NAME=cav_torso_armor>";
        $tors = $db->Execute("SELECT * FROM $dbtables[armor] "
                            ."WHERE type = 'torso'");
        echo "<OPTION VALUE=''></OPTION>";
        while( !$tors->EOF )
        {
            $torso = $tors->fields;
            echo "<OPTION VALUE=$torso[long_name]>$torso[proper]</OPTION>";
            $tors->MoveNext();
        }
        echo "</SELECT>";
        echo "<SELECT NAME=cav_otorso_armor>";
        $otors = $db->Execute("SELECT * FROM $dbtables[armor] "
                             ."WHERE type = 'overtorso'");
        echo "<OPTION VALUE=''></OPTION>";
        while( !$otors->EOF )
        {
            $otorso = $otors->fields;
            echo "<OPTION VALUE=$otorso[long_name]>$otorso[proper]</OPTION>";
            $otors->MoveNext();
        }
        echo "</SELECT>";
        echo "<SELECT NAME=cav_legs_armor>";
        $leg = $db->Execute("SELECT * FROM $dbtables[armor] "
                           ."WHERE type = 'leg'");
        echo "<OPTION VALUE=''></OPTION>";
        while( !$leg->EOF )
        {
            $legs = $leg->fields;
            echo "<OPTION VALUE=$legs[long_name]>$legs[proper]</OPTION>";
            $leg->MoveNext();
        }
        echo "</SELECT>";
        echo "<SELECT NAME=cav_shield>";
        $shld = $db->Execute("SELECT * FROM $dbtables[armor] "
                            ."WHERE type = 'shield'");
        echo "<OPTION VALUE=''></OPTION>";
        while( !$shld->EOF )
        {
            $shield = $shld->fields;
            echo "<OPTION VALUE=$shield[long_name]>$shield[proper]</OPTION>";
            $shld->MoveNext();
        }
        echo "</SELECT>";
        echo "<SELECT NAME=cav_horse_armor>";
        $hor = $db->Execute("SELECT * FROM $dbtables[armor] "
                           ."WHERE type = 'horse'");
        echo "<OPTION VALUE=''></OPTION>";
        if( $hor->EOF )
        {
            echo "<OPTION VALUE=''>None</OPTION>";
        }
        while( !$hor->EOF )
        {
            $horse = $hor->fields;
            echo "<OPTION VALUE=$horse[long_name]>$horse[proper]</OPTION>";
            $hor->MoveNext();
        }
        echo "</SELECT>";

        echo "</TD></TR>";

    }
    echo "<TR><TD BGCOLOR=$color_line1 COLSPAN=2 ALIGN=LEFT><FONT COLOR=WHITE><B>Description:</B></FONT></TD></TR>";
    echo "<TR><TD COLSPAN=2 ALIGN=CENTER>$_REQUEST[description]<INPUT TYPE=HIDDEN NAME=description VALUE=\"$_REQUEST[description]\">";
    echo "</TR>";
    echo "<TR BGCOLOR=$color_header><TD COLSPAN=2>&nbsp;</TD></TR>";
    echo "<TR><TD COLSPAN=2 ALIGN=CENTER><INPUT TYPE=SUBMIT NAME=create2 VALUE=Continue></TD></TR></TABLE></FORM>";
}
if( $_REQUEST[create2] && !$_REQUEST[create3] )
{
    echo "<FORM ACTION=religion2.php METHOD=POST>";
    echo "<CENTER><TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0>";
    echo "<TR BGCOLOR=$color_header ALIGN=CENTER><TD COLSPAN=2>Global Settings</TD></TR>";
    echo "<TR><TD><FONT COLOR=WHITE>Religion Name:</FONT></TD><TD>$_REQUEST[rel_display]";
    echo "<INPUT TYPE=HIDDEN NAME=rel_display VALUE=\"$_REQUEST[rel_display]\"></TD></TR>";
    echo "<TR><TD><FONT COLOR=WHITE>Archtype:</FONT></TD><TD>$_REQUEST[rel_arch]";
    echo "<INPUT TYPE=HIDDEN NAME=rel_arch VALUE=$_REQUEST[rel_arch]></TD></TR>";
    if( $_REQUEST[cannibal] == 'Y' )
    {
        echo "<TR><TD><FONT COLOR=WHITE>Cannibal:</FONT></TD>";
        echo "<TD>$_REQUEST[cannibal]<INPUT TYPE=HIDDEN NAME=cannibal VALUE=$_REQUEST[cannibal]></TD></TR>";
    }
    echo "<TR><TD><FONT COLOR=WHITE>Holidays:</FONT></TD>";
    echo "<TD>($_REQUEST[holidays])";
    if( $_REQUEST[holidays] > 0 )
    {
        echo " Month $_REQUEST[holiday1]";
    }
    if( $_REQUEST[holidays] > 1 )
    {
        echo ", Month $_REQUEST[holiday2]";
    }
    if( $_REQUEST[holidays] > 2 )
    {
        echo ", and Month $_REQUEST[holiday3]";
    }
    echo "<INPUT TYPE=HIDDEN NAME=holidays VALUE=$_REQUEST[holidays]>";
    echo "<INPUT TYPE=HIDDEN NAME=holiday1 VALUE=$_REQUEST[holiday1]>";
    echo "<INPUT TYPE=HIDDEN NAME=holiday2 VALUE=$_REQUEST[holiday2]>";
    echo "<INPUT TYPE=HIDDEN NAME=holiday3 VALUE=$_REQUEST[holiday3]>";
    echo "</TD></TR>";
    echo "<TR><TD><FONT COLOR=WHITE>Global Skill Bonuses:</FONT></TD>";
    $percent = 100 * $_REQUEST[arch_skill1_amount];
    echo "<TD>$_REQUEST[arch_skill1] + $percent%";
    $percent = 100 * $_REQUEST[arch_skill2_amount];
    echo ", $_REQUEST[arch_skill2] + $percent%";
    echo "<INPUT TYPE=HIDDEN NAME=arch_skill1 VALUE=$_REQUEST[arch_skill1]>";
    echo "<INPUT TYPE=HIDDEN NAME=arch_skill1_amount VALUE=$_REQUEST[arch_skill1_amount]>";
    echo "<INPUT TYPE=HIDDEN NAME=arch_skill2 VALUE=$_REQUEST[arch_skill2]>";
    echo "<INPUT TYPE=HIDDEN NAME=arch_skill2_amount VALUE=$_REQUEST[arch_skill2_amount]>";
    echo "</TD></TR>";
    echo "<TR><TD><FONT COLOR=WHITE>Global Skill Penalty Immunity:</FONT></TD>";
    echo "<TD>$_REQUEST[arch_pen1], $_REQUEST[arch_pen2]";
    echo "<INPUT TYPE=HIDDEN NAME=arch_pen1 VALUE=$_REQUEST[arch_pen1]>";
    echo "<INPUT TYPE=HIDDEN NAME=arch_pen2 VALUE=$_REQUEST[arch_pen2]>";
    echo "</TD></TR>";
    echo "<TR><TD><FONT COLOR=WHITE>Healers/Clerics/Monks @ 5th Level:</FONT></TD>";
    echo "<TD>$_REQUEST[healers] ";
    if( $_REQUEST[healers] == 'Y' )
    {
    echo "($_REQUEST[healer_name])";
    }
    echo "<INPUT TYPE=HIDDEN NAME=healers VALUE=$_REQUEST[healers]>";
    echo "<INPUT TYPE=HIDDEN NAME=healer_name VALUE=\"$_REQUEST[healer_name]\">";
    echo "</TD></TR>";
    echo "<TR><TD><FONT COLOR=WHITE>Religious Infantry @ 10th Level:</FONT></TD>";
    echo "<TD>$_REQUEST[infantry]";
    echo "<INPUT TYPE=HIDDEN NAME=infantry VALUE=$_REQUEST[infantry]>";
    echo "</TD></TR>";
    echo "<TR><TD><FONT COLOR=WHITE>Religious Calvalry @ 15th Level:</FONT></TD>";
    echo "<TD>$_REQUEST[calvalry]";
    echo "<INPUT TYPE=HIDDEN NAME=calvalry VALUE=$_REQUEST[calvalry]>";
    echo "</TD></TR>";
    echo "<TR><TD COLSPAN=2>&nbsp;</TD></TR>";
    echo "<TR><TD VALIGN=TOP><FONT COLOR=WHITE>Infantry Weapons & Armor:</FONT></TD>";
    echo "<TD>$_REQUEST[infantry_name]<INPUT TYPE=HIDDEN NAME=infantry_name VALUE=\"$_REQUEST[infantry_name]\"><BR>";
    echo "$_REQUEST[inf_weapon1]<INPUT TYPE=HIDDEN NAME=inf_weapon1 VALUE=$_REQUEST[inf_weapon1]><BR>";
    echo "$_REQUEST[inf_head_armor]<INPUT TYPE=HIDDEN NAME=inf_head_armor VALUE=$_REQUEST[inf_head_armor]><BR>";
    echo "$_REQUEST[inf_torso_armor]<INPUT TYPE=HIDDEN NAME=inf_torso_armor VALUE=$_REQUEST[inf_torso_armor]><BR>";
    echo "$_REQUEST[inf_otorso_armor]<INPUT TYPE=HIDDEN NAME=inf_otorso_armor VALUE=$_REQUEST[inf_otorso_armor]><BR>";
    echo "$_REQUEST[inf_legs_armor]<INPUT TYPE=HIDDEN NAME=inf_legs_armor VALUE=$_REQUEST[inf_legs_armor]><BR>";
    echo "$_REQUEST[inf_shield]<INPUT TYPE=HIDDEN NAME=inf_shield VALUE=$_REQUEST[inf_shield]><BR>";
    echo "</TD></TR><TR><TD COLSPAN=2>&nbsp;</TD></TR>";
    echo "<TR><TD VALIGN=TOP><FONT COLOR=WHITE>Calvalry Weapons & Armor:</FONT></TD>";
    echo "<TD>$_REQUEST[calvalry_name]<INPUT TYPE=HIDDEN NAME=calvalry_name VALUE=\"$_REQUEST[calvalry_name]\"><BR>";
    echo "$_REQUEST[cav_weapon1]<INPUT TYPE=HIDDEN NAME=cav_weapon1 VALUE=$_REQUEST[cav_weapon1]><BR>";
    echo "$_REQUEST[cav_head_armor]<INPUT TYPE=HIDDEN NAME=cav_head_armor VALUE=$_REQUEST[cav_head_armor]><BR>";
    echo "$_REQUEST[cav_torso_armor]<INPUT TYPE=HIDDEN NAME=cav_torso_armor VALUE=$_REQUEST[cav_torso_armor]><BR>";
    echo "$_REQUEST[cav_otorso_armor]<INPUT TYPE=HIDDEN NAME=cav_otorso_armor VALUE=$_REQUEST[cav_otorso_armor]><BR>";
    echo "$_REQUEST[cav_legs_armor]<INPUT TYPE=HIDDEN NAME=cav_legs_armor VALUE=$_REQUEST[cav_legs_armor]><BR>";
    echo "$_REQUEST[cav_shield]<INPUT TYPE=HIDDEN NAME=cav_shield VALUE=$_REQUEST[cav_shield]><BR>";
    echo "$_REQUEST[cav_horse_armor]<INPUT TYPE=HIDDEN NAME=cav_horse_armor VALUE=$_REQUEST[cav_horse_armor]>";
    echo "</TD></TR>";
    echo "<TR><TD><FONT COLOR=WHITE>Religion Description:</FONT></TD><TD>&nbsp;</TD></TR>";
    echo "<TR><TD COLSPAN=2>$_REQUEST[description]<INPUT TYPE=HIDDEN NAME=description VALUE=\"$_REQUEST[description]\">";
    echo "</TD></TR>";
    echo "<TR BGCOLOR=$color_header ALIGN=CENTER><TD COLSPAN=2>Local Settings:</TD></TR>";
    echo "<TR><TD><FONT COLOR=WHITE>Exlusivity:</FONT></TD>";
    echo "<TD><SELECT NAME=rel_exclude>";
    echo "<OPTION VALUE=Inclusivism>Inclusivism</OPTION>";
    echo "<OPTION VALUE=Pluralism>Pluralism</OPTION>";
    echo "<OPTION VALUE=Exclusivism>Exclusivism</OPTION>";
    echo "</SELECT></TD></TR>";
    echo "<TR><TD><FONT COLOR=WHITE>Proslytizing:</FONT></TD>";
    echo "<TD><SELECT NAME=rel_prostlytize>";
    echo "<OPTION VALUE=None>None</OPTION>";
    echo "<OPTION VALUE=Mild>Mild</OPTION>";
    echo "<OPTION VALUE=Strong>Strong</OPTION>";
    echo "</SELECT></TD></TR>";
    echo "<INPUT TYPE=HIDDEN NAME=create VALUE=Continue>";
    echo "<INPUT TYPE=HIDDEN NAME=create2 VALUE=Continue>";
    echo "<TR><TD COLSPAN=2 ALIGN=CENTER><INPUT TYPE=SUBMIT NAME=create3 VALUE=Continue></TD></TR>";
    echo "</FORM>";
    echo "</TABLE>";
}

if( $_REQUEST[create3] && !$_REQUEST[found])
{
    echo "<FORM ACTION=religion2.php METHOD=POST>";
    echo "<CENTER><TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0>";
    echo "<TR BGCOLOR=$color_header ALIGN=CENTER><TD COLSPAN=2>Global Settings</TD></TR>";
    echo "<TR><TD><FONT COLOR=WHITE>Religion Name:</FONT></TD><TD>$_REQUEST[rel_display]";
    echo "<INPUT TYPE=HIDDEN NAME=rel_display VALUE=\"$_REQUEST[rel_display]\"></TD></TR>";
    echo "<TR><TD><FONT COLOR=WHITE>Archtype:</FONT></TD><TD>$_REQUEST[rel_arch]";
    echo "<INPUT TYPE=HIDDEN NAME=rel_arch VALUE=$_REQUEST[rel_arch]></TD></TR>";
    if( $_REQUEST[cannibal] == 'Y' )
    {
        echo "<TR><TD><FONT COLOR=WHITE>Cannibals:</FONT></TD>";
        echo "<TD>$_REQUEST[cannibal]<INPUT TYPE=HIDDEN NAME=cannibal VALUE=$_REQUEST[cannibal]></TD></TR>";
    }
    echo "<TR><TD><FONT COLOR=WHITE>Holidays:</FONT></TD>";
    echo "<TD>($_REQUEST[holidays])";
    if( $_REQUEST[holidays] > 0 )
    {
        echo " Month $_REQUEST[holiday1]";
    }
    if( $_REQUEST[holidays] > 1 )
    {
        echo ", Month $_REQUEST[holiday2]";
    }
    if( $_REQUEST[holidays] > 2 )
    {
        echo ", and Month $_REQUEST[holiday3]";
    }
    echo "<INPUT TYPE=HIDDEN NAME=holidays VALUE=$_REQUEST[holidays]>";
    echo "<INPUT TYPE=HIDDEN NAME=holiday1 VALUE=$_REQUEST[holiday1]>";
    echo "<INPUT TYPE=HIDDEN NAME=holiday2 VALUE=$_REQUEST[holiday2]>";
    echo "<INPUT TYPE=HIDDEN NAME=holiday3 VALUE=$_REQUEST[holiday3]>";
    echo "</TD></TR>";
    echo "<TR><TD><FONT COLOR=WHITE>Global Skill Bonuses:</FONT></TD>";
    $percent = 100 * $_REQUEST[arch_skill1_amount];
    echo "<TD>$_REQUEST[arch_skill1] + $percent%";
    $percent = 100 * $_REQUEST[arch_skill2_amount];
    echo ", $_REQUEST[arch_skill2] + $percent%";
    echo "<INPUT TYPE=HIDDEN NAME=arch_skill1 VALUE=$_REQUEST[arch_skill1]>";
    echo "<INPUT TYPE=HIDDEN NAME=arch_skill1_amount VALUE=$_REQUEST[arch_skill1_amount]>";
    echo "<INPUT TYPE=HIDDEN NAME=arch_skill2 VALUE=$_REQUEST[arch_skill2]>";
    echo "<INPUT TYPE=HIDDEN NAME=arch_skill2_amount VALUE=$_REQUEST[arch_skill2_amount]>";
    echo "</TD></TR>";
    echo "<TR><TD><FONT COLOR=WHITE>Global Skill Penalty Immunity:</FONT></TD>";
    echo "<TD>$_REQUEST[arch_pen1], $_REQUEST[arch_pen2]";
    echo "<INPUT TYPE=HIDDEN NAME=arch_pen1 VALUE=$_REQUEST[arch_pen1]>";
    echo "<INPUT TYPE=HIDDEN NAME=arch_pen2 VALUE=$_REQUEST[arch_pen2]>";
    echo "</TD></TR>";
    echo "<TR><TD><FONT COLOR=WHITE>Healers/Clerics/Monks @ 5th Level:</FONT></TD>";
    echo "<TD>$_REQUEST[healers]";
    if( $_REQUEST[healers] == 'Y' )
    {
        echo " ($_REQUEST[healer_name])";
    }
    echo "<INPUT TYPE=HIDDEN NAME=healers VALUE=$_REQUEST[healers]>";
    echo "<INPUT TYPE=HIDDEN NAME=healer_name VALUE=\"$_REQUEST[healer_name]\">";
    echo "</TD></TR>";
    echo "<TR><TD><FONT COLOR=WHITE>Religious Infantry @ 10th Level:</FONT></TD>";
    echo "<TD>$_REQUEST[infantry]";
    echo "<INPUT TYPE=HIDDEN NAME=infantry VALUE=$_REQUEST[infantry]>";
    echo "</TD></TR>";
    echo "<TR><TD><FONT COLOR=WHITE>Religious Calvalry @ 15th Level:</FONT></TD>";
    echo "<TD>$_REQUEST[calvalry]";
    echo "<INPUT TYPE=HIDDEN NAME=calvalry VALUE=$_REQUEST[calvalry]>";
    echo "</TD></TR>";
    echo "<TR><TD COLSPAN=2>&nbsp;</TD></TR>";
    echo "<TR><TD VALIGN=TOP><FONT COLOR=WHITE>Infantry Weapons & Armor:</FONT></TD>";
    echo "<TD>$_REQUEST[infantry_name]<INPUT TYPE=HIDDEN NAME=infantry_name VALUE=\"$_REQUEST[infantry_name]\"><BR>";
    echo "$_REQUEST[inf_weapon1]<INPUT TYPE=HIDDEN NAME=inf_weapon1 VALUE=$_REQUEST[inf_weapon1]><BR>";
    echo "$_REQUEST[inf_head_armor]<INPUT TYPE=HIDDEN NAME=inf_head_armor VALUE=$_REQUEST[inf_head_armor]><BR>";
    echo "$_REQUEST[inf_torso_armor]<INPUT TYPE=HIDDEN NAME=inf_torso_armor VALUE=$_REQUEST[inf_torso_armor]><BR>";
    echo "$_REQUEST[inf_otorso_armor]<INPUT TYPE=HIDDEN NAME=inf_otorso_armor VALUE=$_REQUEST[inf_otorso_armor]><BR>";
    echo "$_REQUEST[inf_legs_armor]<INPUT TYPE=HIDDEN NAME=inf_legs_armor VALUE=$_REQUEST[inf_legs_armor]><BR>";
    echo "$_REQUEST[inf_shield]<INPUT TYPE=HIDDEN NAME=inf_shield VALUE=$_REQUEST[inf_shield]><BR>";
    echo "</TD></TR><TR><TD COLSPAN=2>&nbsp;</TD></TR>";
    echo "<TR><TD VALIGN=TOP><FONT COLOR=WHITE>Calvalry Weapons & Armor:</FONT></TD>";
    echo "<TD>$_REQUEST[calvalry_name]<INPUT TYPE=HIDDEN NAME=calvalry_name VALUE=\"$_REQUEST[calvalry_name]\"><BR>";
    echo "$_REQUEST[cav_weapon1]<INPUT TYPE=HIDDEN NAME=cav_weapon1 VALUE=$_REQUEST[cav_weapon1]><BR>";
    echo "$_REQUEST[cav_head_armor]<INPUT TYPE=HIDDEN NAME=cav_head_armor VALUE=$_REQUEST[cav_head_armor]><BR>";
    echo "$_REQUEST[cav_torso_armor]<INPUT TYPE=HIDDEN NAME=cav_torso_armor VALUE=$_REQUEST[cav_torso_armor]><BR>";
    echo "$_REQUEST[cav_otorso_armor]<INPUT TYPE=HIDDEN NAME=cav_otorso_armor VALUE=$_REQUEST[cav_otorso_armor]><BR>";
    echo "$_REQUEST[cav_legs_armor]<INPUT TYPE=HIDDEN NAME=cav_legs_armor VALUE=$_REQUEST[cav_legs_armor]><BR>";
    echo "$_REQUEST[cav_shield]<INPUT TYPE=HIDDEN NAME=cav_shield VALUE=$_REQUEST[cav_shield]><BR>";
    echo "$_REQUEST[cav_horse_armor]<INPUT TYPE=HIDDEN NAME=cav_horse_armor VALUE=$_REQUEST[cav_horse_armor]>";
    echo "</TD></TR>";
    echo "<TR><TD><FONT COLOR=WHITE>Religion Description:</FONT></TD><TD>&nbsp;</TD></TR>";
    echo "<TR><TD COLSPAN=2>$_REQUEST[description]<INPUT TYPE=HIDDEN NAME=description VALUE=\"$_REQUEST[description]\">";
    echo "</TD></TR>";
    echo "<TR BGCOLOR=$color_header ALIGN=CENTER><TD COLSPAN=2>Local Settings:</TD></TR>";
    echo "<TR><TD><FONT COLOR=WHITE>Exlusivity:</FONT></TD>";
    echo "<TD>$_REQUEST[rel_exclude]<INPUT TYPE=HIDDEN NAME=rel_exclude VALUE=$_REQUEST[rel_exclude]></TD></TR>";
    echo "<TR><TD COLSPAN=2><FONT COLOR=WHITE>Select your exclusivity bonus:</FONT>&nbsp;&nbsp;";
    if( $_REQUEST[rel_exclude] == 'Pluralism' )
    {
        $exc = $db->Execute("SELECT * FROM $dbtables[skill_table] "
                            ."WHERE exc_plural = 'Y'");
        echo "<FORM ACTION=religion.php METHOD=POST>";
        echo "<SELECT NAME=exclude_skill>";
        while( !$exc->EOF )
        {
            $exc_skill = $exc->fields;
            echo "<OPTION VALUE=$exc_skill[abbr]>$exc_skill[long_name]</OPTION>";
            $exc->MoveNext();
        }
        echo "</SELECT>";
    }
    elseif( $_REQUEST[rel_exclude] == 'Inclusivisim' )
    {
        $exc = $db->Execute("SELECT * FROM $dbtables[skill_table] "
                           ."WHERE exc_inclusive = 'Y'");
        echo "<FORM ACTION=religion.php METHOD=POST>";
        echo "<SELECT NAME=exclude_skill>";
        while( !$exc->EOF )
        {
            $exc_skill = $exc->fields;
            echo "<OPTION VALUE=$exc_skill[abbr]>$exc_skill[long_name]</OPTION>";
            $exc->MoveNext();
        }
        echo "</SELECT>";

    }
    elseif( $_REQUEST[rel_exclude] == 'Exclusivism' )
    {
        $exc = $db->Execute("SELECT * FROM $dbtables[skill_table] "
                           ."WHERE exc_exclusive = 'Y'");
        echo "<FORM ACTION=religion.php METHOD=POST>";
        echo "<SELECT NAME=exclude_skill>";
        while( !$exc->EOF )
        {
            $exc_skill = $exc->fields;
            echo "<OPTION VALUE=$exc_skill[abbr]>$exc_skill[long_name]</OPTION>";
            $exc->MoveNext();
        }
        echo "</SELECT>";

    }
    echo "&nbsp;&nbsp;<SELECT NAME=exclude_skill_type>";
    echo "<OPTION VALUE=1>Skill Attempts</OPTION>";
    echo "<OPTION VALUE=2>Activity Bonus</OPTION>";
    echo "</SELECT>";
    echo "&nbsp;&nbsp;<SELECT NAME=exclude_skill_amount>";
    echo "<OPTION VALUE=.05>5%</OPTION>";
    echo "<OPTION VALUE=.1>10%</OPTION>";
    echo "<OPTION VALUE=.15>15%</OPTION>";
    echo "</SELECT>";
    echo "</TD></TR>";
    echo "<TR><TD COLSPAN=2><FONT COLOR=WHITE>Select your prostlytizing bonus:</FONT>";
    $prost = 0;

    if( $_REQUEST[rel_prostlytize] == 'None' )
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
    elseif( $_REQUEST[rel_prostlytize] == 'Mild' )
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
    elseif( $_REQUEST[rel_prostlytize] == 'Strong' )
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
    else
    {
        echo "&nbsp;&nbsp;&nbsp;None available.";
    }
    echo "</TD></TR>";
    echo "<TR><TD COLSPAN=2>&nbsp;</TD></TR>";
    echo "<TR><TD COLSPAN=2 ALIGN=CENTER>";
    $unique = uniqid(microtime(),1);
    echo "<INPUT TYPE=HIDDEN NAME=rel_prostlytize VALUE=$_REQUEST[rel_prostlytize]>";
    echo "<INPUT TYPE=HIDDEN NAME=create VALUE=Continue>";
    echo "<INPUT TYPE=HIDDEN NAME=create2 VALUE=Continue>";
    echo "<INPUT TYPE=HIDDEN NAME=create3 VALUE=Continue>";
    echo "<INPUT TYPE=HIDDEN NAME=unique VALUE='$unique'>";
    echo "<INPUT TYPE=SUBMIT NAME=found VALUE=\"Found it\">";
    echo "</TD></TR>";

    echo "</FORM></TABLE>";
}


if( $_REQUEST[found] )
{
    $req = $db->Execute("SELECT * FROM $dbtables[subtribe_id] "
                       ."WHERE unique_id = '$_REQUEST[unique]'");
    if( !$req->EOF )
    {
        echo "You cannot resubmit this form.<BR>";
        echo "Click <A HREF=religion.php>here</A> to return to the religion main page.</BR>";
        $filename = __FILE__;
        $filename = explode('/', $filename);
        $extension = 'txt';
        $linkname = str_replace('php', $extension, $filename[5]);
        $filename = $linkname;
        page_footer();
    }
    /////Calculate how much penalty they're gonna get tagged with/////
    $penalty = 0;
    if( $_REQUEST[healers] == 'Y' )
    {
        $penalty += .05;
    }
    if( $_REQUEST[infantry] == 'Y' )
    {
        $penalty += .1;
    }
    if( $_REQUEST[calvalry] == 'Y' )
    {
        $penalty += .15;
    }
    $penalty += $_REQUEST[arch_skill1_amount];
    $penalty += $_REQUEST[arch_skill2_amount];
    $penalty += $_REQUEST[exclude_skill_amount];
    $penalty += $_REQUEST[pros_skill_amount];
    $percent = 100 * $penalty;
    echo"Your subtotal penalty comes to $percent%.<BR>";
    
    /////////Calculate how much they've earned through holidays///////
    $contrition = 0;
    if( $_REQUEST[holidays] > 0 )
    {
        $contrition += .1;
    }
    if( $_REQUEST[holidays] > 1 )
    {
        $contrition += .1;
    }
    if( $_REQUEST[holidays] > 2 )
    {
        $contrition += .1;
    }
    if( $_REQUEST[cannibal] == 'Y' )
    {
        $contrition += .2;
    }
    $penalty -= $contrition;
    $percent = 100 * $penalty;
    echo "You have lowered your possible penalty to $percent%.<BR>";

    if( $penalty > 0 )
    {
        $halfpen = $penalty / 2;
        //////Select the first skill to be penalized!////////////
        $pen1 = $db->Execute("SELECT COUNT(*) AS count FROM $dbtables[skill_table] "
                            ."WHERE abbr != '$_REQUEST[arch_pen1]' "
                            ."AND abbr != '$_REQUEST[arch_pen2]' "
                            ."AND abbr != '$_REQUEST[arch_skill1]' "
                            ."AND abbr != '$_REQUEST[arch_skill2]'");
        $penalty1 = $pen1->fields;
        $penalty1 = rand(0, $penalty1[count]);
        $pen1 = $db->Execute("SELECT * FROM $dbtables[skill_table] "
                            ."WHERE abbr != '$_REQUEST[arch_pen1]' "
                            ."AND abbr != '$_REQUEST[arch_pen2]' "
                            ."AND abbr != '$_REQUEST[arch_skill1]' "
                            ."AND abbr != '$_REQUEST[arch_skill2]' "
                            ."LIMIT $penalty1, 1");
        $pen_skill1 = $pen1->fields;
        $percent = 100 * $halfpen;
        echo "You will have $pen_skill1[long_name] penalized by - $percent%.<BR>";

        ////////Now get the second skill to be penalized!////////////
        $pen2 = $db->Execute("SELECT COUNT(*) AS count FROM $dbtables[skill_table] "
                            ."WHERE abbr != '$_REQUEST[arch_pen1]' "
                            ."AND abbr != '$_REQUEST[arch_pen2]'");
        $penalty2 = $pen2->fields;
        $penalty2 = rand(0, $penalty2[count]);
        $pen2 = $db->Execute("SELECT * FROM $dbtables[skill_table] "
                            ."WHERE abbr != '$_REQUEST[arch_pen1]' "
                            ."AND abbr != '$_REQUEST[arch_pen2]' "
                            ."LIMIT $penalty2, 2");
        $pen_skill2 = $pen2->fields;
        $percent = 100 * $halfpen;
        echo "You will also have $pen_skill2[long_name] penalized by - $percent%.<BR>";
    }
    $db->Execute("UPDATE $dbtables[clans] "
                ."SET religion = '$_REQUEST[rel_display]' "
                ."WHERE clanid = '$_SESSION[clanid]'");

    $db->Execute("INSERT INTO $dbtables[subtribe_id] "
                ."(`unique_id`) "
                ."VALUES("
                ."'$_REQUEST[unique]')");
    $fam = $db->Execute("SELECT family FROM $dbtables[religions] "
                       ."ORDER BY family DESC LIMIT 1");
    $family = $fam->fields;
    $family = $family[family]++;

    $debug = $db->Execute("INSERT INTO $dbtables[religions] "
                ."VALUES("
                ."'',"
                ."'$family',"
                ."'0',"
                ."'$_SESSION[clanid]',"
                ."'$_REQUEST[cannibal]',"
                ."'',"
                ."'$_REQUEST[rel_display]',"
                ."'$_REQUEST[holidays]',"
                ."'$_REQUEST[holiday1]',"
                ."'$_REQUEST[holiday2]',"
                ."'$_REQUEST[holiday3]',"
                ."'$_REQUEST[rel_arch]',"
                ."'$_REQUEST[arch_skill1]',"
                ."'$_REQUEST[arch_skill1_type]',"
                ."'$_REQUEST[arch_skill1_amount]',"
                ."'$_REQUEST[arch_skill2]',"
                ."'$_REQUEST[arch_skill2_type]',"
                ."'$_REQUEST[arch_skill2_amount]',"
                ."'$pen_skill1[abbr]',"
                ."'',"
                ."'$halfpen',"
                ."'$pen_skill2[abbr]',"
                ."'',"
                ."'$halfpen',"
                ."'$_REQUEST[healers]',"
                ."'$_REQUEST[healer_name]',"
                ."'$_REQUEST[infantry]',"
                ."'$_REQUEST[infantry_name]',"
                ."'$_REQUEST[calvalry]',"
                ."'$_REQUEST[calvalry_name]'," 
                ."'$_REQUEST[rel_exclude]',"
                ."'$_REQUEST[exclude_skill]',"
                ."'$_REQUEST[exclude_skill_type]',"
                ."'$_REQUEST[exclude_skill_amount]',"
                ."'$_REQUEST[rel_prostlytize]',"
                ."'$_REQUEST[pros_skill]',"
                ."'$_REQUEST[pros_skill_type]',"
                ."'$_REQUEST[pros_skill_amount]',"
                ."'$_REQUEST[description]',"
                ."'$_REQUEST[inf_weapon1]',"
                ."'$_REQUEST[inf_head_armor]',"
                ."'$_REQUEST[inf_torso_armor]',"
                ."'$_REQUEST[inf_otorso_armor]',"
                ."'$_REQUEST[inf_legs_armor]',"
                ."'$_REQUEST[inf_shield]',"
                ."'$_REQUEST[cav_weapon1]',"
                ."'$_REQUEST[cav_head_armor]',"
                ."'$_REQUEST[cav_torso_armor]',"
                ."'$_REQUEST[cav_otorso_armor]',"
                ."'$_REQUEST[cav_legs_armor]',"
                ."'$_REQUEST[cav_shield]',"
                ."'$_REQUEST[cav_horse_armor]')");

    if( !$debug )
    {
        echo $db->ErrorMsg() . "<BR>";
    }

}


page_footer();
?>
