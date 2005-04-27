<?
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// Modifications Contributed by Erik (erik@payz.co.uk)
// File: adminreport.php

session_start();
header("Cache-control: private");

$admincheck = $_SESSION['username'];
//here we should query the db to get admin's username or id or some such to auth
if($admincheck !== 'admin')
{
die("You Do Not	Have permissions to view this page!");
}
include("config.php");


page_header("Clan Report");

connectdb();
$username = $_SESSION['username'];
$admin = $db->Execute("SELECT * FROM $dbtables[chiefs] "
                     ."WHERE username = '$username'");
$admininfo = $admin->fields;

if( !$admininfo[admin] >= $privilege['adm_logging'] )
{
    echo 'You must have log viewer privilege to use this tool.<BR>';
    page_footer();
}
$result = $db->Execute("SELECT * FROM $dbtables[chiefs] WHERE username='$_SESSION[username]'");
$playerinfo=$result->fields;


$gm = $db->Execute("SELECT * FROM $dbtables[game_date] WHERE type = 'month'");
$month = $gm->fields;
$gy = $db->Execute("SELECT * FROM $dbtables[game_date] WHERE type = 'year'");
$year = $gy->fields;

$ty = $db->Execute("SELECT DISTINCT year FROM $dbtables[logs] WHERE clanid = '0000'");

$viewmonth = $month[count];

$month_flag = "";
if (!ISSET($_REQUEST[month]))                     //set default report to last month
{
	$month_flag=" (last month)";
	$last_month = $viewmonth - 1;
	if ($last_month < 1)
	{
		$last_month = 12;
	}
	$_REQUEST[month] = $last_month;
	$_REQUEST[year] = $year[count];
	if ($last_month == 12)
	{
		$_REQUEST[year] = ($year[count]) - 1;
	}
}

if (ISSET($_REQUEST[month_next]))                 //move to next month
{
	$_REQUEST[month] = $_REQUEST[month] + 1;
	if ($_REQUEST[month] == 13)
	{
		$_REQUEST[month] = 1;
		$_REQUEST[year] = $_REQUEST[year] + 1;
	}
}

if (ISSET($_REQUEST[month_prev]))                //move to previous month
{
	$_REQUEST[month] = $_REQUEST[month] - 1;
	if ($_REQUEST[month] == 0)
	{
		$_REQUEST[month] = 12;
		$_REQUEST[year] = $_REQUEST[year] - 1;
	}
}

$display_month = $_REQUEST[month];
$display_year = $_REQUEST[year];


echo "<FORM ACTION=adminreport.php METHOD=POST>";

echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=\"100%\">";
echo "<TR BGCOLOR=\"$color_header\">"
	."<TD>It is now Year <b>$year[count]</b> Month <b>$month[count]</b></TD>"
	."<TD>&nbsp;</TD>"
	."<TD ALIGN=RIGHT>";

echo "<INPUT NAME=month_prev TYPE=SUBMIT VALUE=\" < \">"       //prev month
	."<INPUT NAME=month_next TYPE=SUBMIT VALUE=\" > \"> ";     //next month

echo "<SELECT NAME=year>";                                     //year selector
while(!$ty->EOF)
{
	$tribe_year = $ty->fields;
	echo "<OPTION ";
	if ($tribe_year[year] == $display_year)
	{
		echo "SELECTED ";
	}
	echo "VALUE=$tribe_year[year]>$tribe_year[year]</OPTION>";
	$ty->MoveNext();
}
echo "</SELECT>";


echo "<SELECT NAME=month>";                                   //month selector
for ($i=1; $i<=12; $i++)
{
	echo "<OPTION ";
	if ($i == $display_month)
	{
		echo "SELECTED ";
	}
	echo "VALUE=$i>$i</OPTION>";
}
echo "</SELECT>&nbsp;";

echo "<INPUT TYPE=SUBMIT VALUE=VIEW>";
echo "</TD></TR>";

echo "<TR BGCOLOR=\"$color_header\">"
	."<TD ALIGN=LEFT><FONT COLOR=WHITE><B>Reporting Year $display_year Month $display_month$month_flag</B></FONT></TD>"
	."<TD ALIGN=CENTER><B>System Logs</B> (Chief $playerinfo[chiefname])</TD>"
	."<TD></TD>"
	."</TR>";
echo "<TR BGCOLOR=\"$color_header\">"
	."<TD COLSPAN=6><HR COLOR=$color_line1></TD>"
	."</TR>";
echo "</TABLE>";


echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=\"100%\">";


if(!ISSET($_REQUEST[month]) & !ISSET($_REQUEST[year])){
$result2 = $db->Execute("SELECT * FROM $dbtables[logs] WHERE clanid = 0000 AND month = '$month[count]' AND year = '$year[count]' ORDER BY logid desc");
}
else {
$result2 = $db->Execute("SELECT * FROM $dbtables[logs] WHERE clanid = 0000 AND month = '$_REQUEST[month]' AND year = '$_REQUEST[year]' ORDER BY logid desc");
}
if($result2->RecordCount() < 1){
echo "<TABLE WIDTH=\"100%\" BGCOLOR=$color_line1><TR><TD>Nothing to report so far this month, sire.</TD></TR></TABLE><BR><BR><P>";
page_footer();
}
else{
//echo "<TR BGCOLOR=\"$color_header\"><TD ALIGN=LEFT colspan=5><FONT COLOR=WHITE><B>News:</B></FONT></TD></TR>";
$line_color = $color_line1;
while(!$result2->EOF){
$newsinfo = $result2->fields;
echo "<TR BGCOLOR=\"$line_color\"><TD align=center>$newsinfo[tribeid]</TD><TD align=right>$newsinfo[month]</TD><TD align=center>/</TD><TD align=left>$newsinfo[year]</TD><TD WIDTH=\"85%\">$newsinfo[data]</TD></TR>";
$result2->MoveNext();
if($line_color == $color_line1){
$line_color = $color_line2;
}
else{
$line_color = $color_line1;
}
}
echo "</TABLE>";
}

echo "</FORM>";

echo "<p align=center>";

page_footer();

?>

