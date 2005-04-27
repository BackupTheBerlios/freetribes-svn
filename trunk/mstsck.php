<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: main.php
error_reporting  (E_ERROR | E_WARNING | E_PARSE | !E_NOTICE);

session_start();
header("Cache-control: private");
include("config.php");

include("gui/pad_row.php");
include("gui/menu_option.php");

include("game_time.php");


page_header("Admin - MSTSCK");

connectdb();

if (ISSET($_REQUEST['show_intro']))
{
	$show_intro = $_REQUEST['show_intro'];
}
else
{
	$show_intro = '-1';
}
$show_intro++;
$show_intro = $show_intro % 2;

if (ISSET($_REQUEST['show_usage']))
{
	$show_usage = $_REQUEST['show_usage'];
}
else
{
	$show_usage = '-1';
}
$show_usage++;
$show_usage = $show_usage % 2;

echo "<P>"
	."<TABLE BORDER=0>"
	."<TR>"
	."<TD>"
	."<FORM METHOD=post action=mstsck.php>"
	."<INPUT TYPE=HIDDEN NAME=show_intro VALUE=$show_intro> "
	."<INPUT TYPE=SUBMIT VALUE=Intro>"
	."</FORM>"
	."</TD>"
	."<TD>"
	."<FORM METHOD=post action=mstsck.php>"
	."<INPUT TYPE=HIDDEN NAME=show_usage VALUE=$show_usage> "
	."<INPUT TYPE=SUBMIT VALUE=Usage>"
	."</FORM>"
	."</TD>"
	."</TR>"
	."</TABLE>";

echo "<TABLE BORDER=0>"
	."<TR>"
/*
	."<TD>"
	."<FORM METHOD=post action=mstsck_intrinsic.php>"
	."<INPUT TYPE=SUBMIT VALUE=Intrinsic DISABLED>"
	."</FORM>"
	."</TD>"
	."<TD>"
	."<FORM METHOD=post action=mstsck_live.php>"
	."<INPUT TYPE=SUBMIT VALUE=livestock DISABLED>"
	."</FORM>"
	."</TD>"
*/
	."<TD>"
	."<FORM METHOD=post action=mstsck_prod.php>"
	."<INPUT TYPE=HIDDEN NAME=res_type VALUE=Product>"
	."<INPUT TYPE=SUBMIT VALUE=Product>"
	."</FORM>"
	."</TD>"
/*
	."<TD>"
	."<FORM METHOD=post action=mstsck_skill.php>"
	."<INPUT TYPE=SUBMIT VALUE=Skills>"
	."</FORM>"
	."</TD>"
	."<TD>"
	."<FORM METHOD=post action=mstsck_struct.php>"
	."<INPUT TYPE=SUBMIT VALUE=Structures DISABLED>"
	."</FORM>"
*/
	."</TD>"
	."</TR>"
	."</TABLE>";

if ($show_intro=='1')
{
	echo "Welcome to My 'Snazzy' TS Construction Kit, referred to from now on as MSTSCK, meaning it Must Suck ;)"
		."<P>Okay, more to the point, this page is for populating the gd_rq table with the requiremnts that clans/tribes will need to meet in order to achive particular goals, such as learning skills producing goods and so on."
		."What's the point of having a page to do that when we can just type the info straight into to table?"
		."At present, gd_rq is not going to be capabale of much more than the tables we already have but, in the future, we will be able to use gd_rq to develop code much faster and to add new features to the game more easily."
		."Exactly how that is undecided as yet, but some of the possible adavantages of doing things this way are ..."
		."<ul>"
		."<li>The ability to rewrite the scheduler code (the bit that works out what happens between turns) in way that means that we will have much less code to write."
		."<li>The ability to basically have '1' file to do most the things that the 60 scheduler files do currently."
		."<li>The ability to autogenerate basic source code for parts of the game scheduler that will not quite follow the same basic pattern of everything else."
		."<li>The ability to automatically check out that parts of the game system actually work properly"
		."<li>Less chance of bugs creeping into the game program as the result of typo's or basic mistakes in arithmetic or program logic (the most common causes of bugs in any program)"
		."</ul>";
}

if ($show_usage=='1')
{
	echo "This part of MSTSCK is pretty simple. Just pick what type of table entry you want to add or edit and click the button with the appropriate name"
		."<P>So, to edit or add details to gd_rq about what is required to make a particular product, you would click the Product button."
		."<BR>And, to add or edit deatils about what is required for a skill you would click the Skill button."
		."<P>Note that most entries in gd_rq are for how to make products, though obviously a product might require a Skill in order to make it. You will be able to define what is required after you click one of the buttons below."
		."<P>";
}



?>
