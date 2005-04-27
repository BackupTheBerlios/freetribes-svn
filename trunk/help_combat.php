<?
session_start();
header("Cache-control: private");

if (!ISSET($_SESSION['theme']))
{
	$_SESSION['theme'] = $theme_default;  // This must be set on this page before the header is included
}

include("config.php");

page_header("Combat Help");

connectdb();

echo "<CENTER>";

navbar_help();

echo "<BR><BR>";
echo "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=\"70%\"><TR><TD>";

echo "<DT><FONT SIZE=+1 COLOR=WHITE>Garrisons</FONT>:</DT>";
echo "<DD>Garrisons are the warrior units you recruit and equip to defend your village and attack other villages. Some things to consider ";
echo "before recruiting your entire population into a garrison is that your warriors do not conduct any activities. They are considered to only ";
echo "perform training, drilling, excersising, patrolling, etc. They do, however, eat. The longer any specific garrison exists, the more experience ";
echo "they acquire to represent the time spent working together. You create garrisons by going to the garrisons page from the menu on the left of the ";
echo "main page.</DD><BR>";
echo "<DD>There are some explanations needed for the garrisons page, as it is not entirely self evident. The secondary weapons are for horse archers only. ";
echo "If you use the secondary weapons for non-archer garrisons, you will wind up wasting those weapons.</DD>";

echo "<DT><FONT SIZE=+1 COLOR=WHITE>Newbie Nice</FONT>:</DT>";
echo "<DD>When you first create your clan, you are immune to attacks (both DeVA and assaults) for 24 months (24 real days). This does not mean that ";
echo "you are unable to attack anyone. However, be warned that once you attack someone, you will be automatically removed from Newbie Nice, and open ";
echo "to retaliation from older tribes.</DD>";

echo "<DT><FONT SIZE=+1 COLOR=WHITE>Combat Phases</FONT>:</DT>";
echo "Combat occurs as a series of phases. Archers shoot missiles first, calvalry charges then melee second, infantry then goes last. Defenders go first, ";
echo "then Attackers. When selecting targets, archers consider all enemies as valid targets. Calvalry and Infantry first attempt to select enemy infantry, ";
echo "enemy calvalry, and finally enemy archers.</DD><BR>";
echo "The battlefield is broken up into three sectors. Each sector operates as it's own independent battlefield in regards to casualties, deaths, winner, ";
echo "etc... so that even if a garrison runs away in one sector, it could possibly be standing firm in the other two sectors. If you have no remaining warriors ";
echo "in any specific sector, the enemy wins that sector. If you lose two or more sectors, the enemy wins the battle. The victor of each sector gets to ";
echo "loot the corpses of that sector.</DD>";

echo "<DT><FONT COLOR=WHITE>D</FONT>eny <FONT COLOR=WHITE>V</FONT>illage <FONT COLOR=WHITE>A</FONT>ctivities</FONT> (DeVA):</DT>";
echo "<DD>This is the tactic of laying seige to a village, tribe, or subtribe. No activities may be conducted by the target tribe while ";
echo "DeVA is in effect. In order to lay DeVA on a tribe, your total number of warriors, your garrisons must be more than the total ";
echo "number of defending warriors. There are some modifiers to consider, such as any allied tribes in the hex will lend 25% of thier ";
echo "warriors to the defending tribe, and any tribes of the same clan of the defender who are also in the hex will lend 50% of thier ";
echo "warriors as well. As long as your total number of warriors is greater than the total number of warriors that the defender can ";
echo "muster, you can enact DeVA.</DD>";

echo "<DT><FONT COLOR=WHITE>Cancelling DeVA</FONT>:</DT>";
echo "<DD>An attacker may lift the seige by moving out of the hex. Or disbanding enough of his garrisons to number less warriors than the defender.</DD>";

echo "<DT><FONT COLOR=WHITE>Breaking DeVA</FONT>:</DT>";
echo "<DD>A defender may lift the seige by several ways. First, they may recruit enough warriors to numbers greater than those of the attacker. ";
echo "Second, they may attack the tribe laying seige and kill off enough warriors to reduce the attacker's warriors to less than the defender. ";
echo "Third, they may move subtribes or allied tribes into the hex with garrisons of warriors in sufficient number to be greater than the attacker. ";
echo "Fourth, they may simply move to another hex themselves. Regardless of how DeVA is lifted, the effects of DeVA are felt until the end of the ";
echo "game month.</DD>";




echo "</TD></TR></TABLE><BR>";


navbar_help();

if( $_SESSION[clanid] )
{
    TEXT_GOTOMAIN();
}
echo "</CENTER>";

page_footer();

?> 
