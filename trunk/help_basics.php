<?
session_start();
header("Cache-control: private");

if (!ISSET($_SESSION['theme']))
{
	$_SESSION['theme'] = $theme_default;  // This must be set on this page before the header is included
}

include("config.php");

page_header("Basic Help");

connectdb();

echo "<CENTER>";

navbar_help();

echo "<BR><BR>";
echo "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=\"70%\"><TR><TD>";

echo "<FONT COLOR=WHITE>Font Face:</FONT><BR>";
echo "If you do not have the Papyrus true type font installed, you may download it ";
echo "<A HREF=../confed/downloads/papyrus.ttf>here</A>. Just be sure to right click and select ";
echo "\"save target as\" or else you will get a browser full of garbage.<BR>";

echo "<FONT COLOR=WHITE>Basic Concepts:</FONT><BR>";
echo "You play Chief of the clan you create. Your clan, when you begin, will be comprised of a single tribe, but can grow ";
echo "into a nation of up to ten tribes total.<BR>";
echo "<FONT COLOR=WHITE>Game Time:</FONT><BR>";
echo "Each day represents a month in game reckoning, or a 'turn'. Each hour will see a chance for the weather to change.<BR>";
echo "<FONT COLOR=WHITE>Movement and Mapping:</FONT><BR>";
echo "You may move immediately. At the start of each turn, you will be given 18 or 27 movement points depending on how many horses ";
echo "and elephants you have in relation to your population. Each horse can carry 1 person, each elephant may carry 4. If your entire ";
echo "tribe has room, you will receive 27 move, otherwise you get 18. Different terrain require different amounts of movement points to ";
echo "enter. Prairie only require 3, Low Coniferous Mountains require a lot more. You move by clicking on the mini map at the top of the ";
echo "main tribe screen. You can see a larger map of your area by going to the maps screen. Your tribe will always be located in the center ";
echo "square on the big map. Only those map squares that you have visited, or visited by others who have sent you thier mapping will be ";
echo "revealed to you. Otherwise you will only see question marks.<br>";
echo "<FONT COLOR=WHITE>Skill Attempts:</FONT><BR>";
echo "Each turn, your tribes are allowed to make attempts at improving their skills, or acquiring new skills. Depending on the skills ";
echo "your tribe possesses, you will be able to conduct activities. Some activities relate to skills in how well you may perform them, ";
echo "other activities limit the amount of people you may assign to them based on your skill. Other skills allow you to build or make ";
echo "different things as your rise in level, and still other skills are background skills that are automatic and not tied to any specific ";
echo "activity that you notice (security for example). <BR>";
echo "<FONT COLOR=WHITE>Activities:</FONT><BR>";
echo "Activities are the tasks that you set your tribe to do each day. Do not fear missing a day or two and finding your tribe has gone ";
echo "hungry and half dead when you return. There are default activities that kick in if the game sees that nothing has been done with ";
echo "your tribe that turn, and will see to it that they do some minimal tasks. This does not guarantee that your tribe will not starve, ";
echo "depending on your hunting skill and the season, your tribe may still come up short of food.<BR> ";
echo "<FONT COLOR=WHITE>Goods Tribe:</FONT><BR>";
echo "Each tribe has a designated \"Goods Tribe\" assigned. At the start, your tribe is it's own goods tribe. This basically means that ";
echo "any food eaten and resources needed for activities will be removed from your goods tribe. Any products made, or any resources gathered ";
echo "will likewise be deposited into the goods tribe for future use. You can go to the reports page to view the results of each turn. ";
echo "As you spawn subtribes, your main tribe will become the goods tribe for the newer tribes, so that you may nurture the growth of ";
echo "these fledgling tribes. But when they are ready, you may go to the transfers screen and change this. If your subtribe moves to a ";
echo "different map location than that of the goods tribe, the subtribe will automatically be considered it's own goods tribe. ";
echo "This may be a problem if the subtribe was not prepared to be left alone.<BR>";
echo "<FONT COLOR=WHITE>Score:</FONT><BR>";
echo "Every game month (once per real day) the following will be measured and scored:<P>";
echo "<UL>For each map hex explored: 1000pts</UL>";
echo "<UL>For each ally: 10000pts</UL>";
echo "<UL>For each subtribe: 100000pts</UL>";
echo "<UL>For each active: 1000pts</UL>";
echo "<UL>For each inactive: 500pts</UL>";
echo "<UL>For each skill: 1000pts/level</UL>";
echo "<UL>For each product: Fair Price Equivalent</UL>";
echo "<UL>For each resource: Fair Price Equivalent</UL>";
echo "<UL>For each livestock: Fair Price Equivalent</UL>";
echo "<UL>For each garrison: 1000pts</UL>";
echo "<UL>For each piece of equipment in garrison: Fair Price Equivalent * Garrison Force</UL>";
echo "<UL>For each garrisoned Horse: 5000</UL>";
echo "<UL>For each structure/building: 10000</UL>";
echo "<UL>Total Number is then Divided by 10,000</UL>";


echo "</TD></TR></TABLE><BR>";

navbar_help();

if( $_SESSION[clanid] )
{
    TEXT_GOTOMAIN();
}
echo "</CENTER>";

page_footer();

?> 
