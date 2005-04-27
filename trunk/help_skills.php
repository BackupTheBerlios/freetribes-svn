<?
session_start();
header("Cache-control: private");

if (!ISSET($_SESSION['theme']))
{
	$_SESSION['theme'] = $theme_default;  // This must be set on this page before the header is included
}

include("config.php");

page_header("Skills Help");

connectdb();

echo "<CENTER>";

navbar_help();

echo "<BR><BR>";
echo "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=\"70%\"><TR><TD>";


echo "<DT><FONT COLOR=WHITE>Skill attempts:</FONT></DT><DD>Every tribe is allowed two (2) skill attempts per turn. The chance of success for the tribe ";
echo "is 110%-(10% * <I>skill level attempted</I>) for the primary attempt, and half this for the second. You may not attempt ";
echo "two skills from the same group per turn.</DD>";

echo "<BR><BR>";
echo "<DD><FONT COLOR=RED>Red</FONT> skills have not yet been fully coded. If a skill does not appear here, it is also likely not to have been ";
echo "coded either.</DD>";
echo "<BR><BR>";

echo "<DT><FONT COLOR=RED>Armor</FONT> (arm):</DT>";
echo "<DD>This is the skill in which you may make certain types of armor depending upon your expertise.</DD>";

echo "<DT><FONT COLOR=WHITE>Administration</FONT> (adm):</DT>";
echo "<DD>This skill determines how much population you may split off when creating a subtribe. At adm0, you may split off a ";
echo "maximum of 5% of your population evenly divided among warriors, actives, and inactives. You may add 1% per adm skill level. ";
echo "Gains in this skill affect your morale.</DD>";

echo "<DT><FONT COLOR=RED>Alchemy</FONT> (alc):</DT>";
echo "<DD>The mixing of different chemicals and herbs to create useful elixirs and salves.</DD>"; 

echo "<DT><FONT COLOR=RED>Art</FONT> (art):</DT>";
echo "<DD>The ability of your tribe to express themselves in painting and sculpting. Useful for some religions. Gains in this skill ";
echo "affect your morale.</DD>";

echo "<DT><FONT COLOR=RED>Astronomy</FONT> (astr):</DT>";
echo "<DD>Used by some religions. Gains in this skill affect morale.</DD>";

echo "<DT><FONT COLOR=RED>Atheism</FONT> (ath):</DT>";
echo "<DD>This skill is attempted by chiefs who wish to remove themselves from a religion. When a tribe's atheism skill exceeds that of ";
echo "it's religion skill, it is considered to have \"lost the faith\". </DD>";

echo "<DT><FONT COLOR=White>Bonework</FONT> (bnw):</DT>";
echo "<DD>The ability to use discarded animal bones to make tools, weapons, and armor.</DD>";

echo "<DT><FONT COLOR=White>Boning</FONT> (bon):</DT>";
echo "<DD>The skill needed to extract bones from slaughtered livestock. For every level of boning skill achieved, a tribe may allocate ";
echo "ten (10) actives to the activity of boning. Returns from boning depend upon the type of livestock being slaughtered.</DD>";

echo "<DT><FONT COLOR=RED>Brick Making</FONT> (brk):</DT>";
echo "<DD>The skill of making bricks, to be used in construction.</DD>";

echo "<DT><FONT COLOR=WHITE>Dressing</FONT> (dre):</DT>";
echo "<DD>A skill of dressing furs and skins into leathers, using salt. For each level of dressing skill achieved, a tribe may allocate ";
echo "ten (10) actives to the activity of dressing. Returns from dressing are 4 skins/furs per 1 active using 1 salt.</DD>";

echo "<DT><FONT COLOR=WHITE>Economics</FONT> (eco):</DT>";
echo "<DD>Economics is the skill required to participate in the biannual fairs. On turns 4 and 10, your main tribe (no subtribes) may ";
echo "participate in buying and selling resources, products, or slaves if your skill in economics is greater than 5, or if your main ";
echo "tribe has constructed a trade post. One transaction per skill level is allowed.</DD>";

echo "<DT><FONT COLOR=WHITE>Forestry</FONT> (for):</DT>";
echo "<DD>Forestry is the skill which provides logs or bark. For each level of forestry skill achieved, a tribe may allocate ";
echo "ten (10) actives to the activity of forestry. Obviously a forested location is required. Returns for forestry are 10 logs or 20lbs ";
echo "of bark per person per turn.</DD>";

echo "<DT><FONT COLOR=WHITE>Furrier</FONT> (fur):</DT>";
echo "<DD>A form of hunting which specialises in the killing of animals whilst leaving their hides intact. Furriers produce far less provisions ";
echo "than hunters but also provide skins and furs.</DD>";

echo "<DT><FONT COLOR=WHITE>Healing</FONT> (heal):</DT>";
echo "<DD>Healing is an automatic skill that you use in conjunction with herbs to reduce your casualties during combat. All warriors healed during ";
echo "combat will be placed back into your active population.</DD>";

echo "<DT><FONT COLOR=WHITE>Herding</FONT> (herd):</DT>";
echo "<DD>Looking after your animals. 1 person is required per 10 horses, cattle, dogs (or 20 goats or 5 elephants). Breeding rates are affected ";
echo "by herding skill, season, weather, terrain and crossbreeding, the effects of which will last for several months. If you assign less than the ";
echo "required number of herders for your livestock, there is a chance that you will lose any excess.</DD>";

echo "<DT><FONT COLOR=WHITE>Hunting</FONT> (hunt):</DT>";
echo "<DD>Helps feed the Tribe - and in the early days is the tribe's main source of food. The number of provisions (provs) taken is affected by the ";
echo "number of hunters, skill, terrain, season, weather. Hunters can use missile weapons or traps or snares (up to 5 snares or traps per hunter). Food ";
echo "is limited in an area, large numbers of hunters could cause diminishing rates of return (this is not presently coded, but planned for). Meandering ";
echo "herds can sometimes be found wandering the world, these may also increase the hunting return.</DD>";

echo "<DT><FONT COLOR=WHITE>Leatherwork</FONT> (ltr):</DT>";
echo "<DD>This is the skill in which you may make certain types of leather goods depending upon your expertise.</DD>";

echo "<DT><FONT COLOR=WHITE>Mining</FONT> (min):</DT>";
echo "<DD>Map locations with deposits will only have one of coal, iron ore, copper ore, tin ore, zinc ore, lead ore, salt, silver, gold, gems. ";
echo "Output is influenced by the number of miners, skill and weather. Mining is very dangerous, higher skill reduces this. Mines are inexhaustible ";
echo "except for silver, gold, and gem mines.</DD>";

echo "<DT><FONT COLOR=WHITE>Quarrying</FONT> (qry):</DT>";
echo "<DD>Provides stones for walls etc. Each person provides 5 stones in any mountain or hill area (each stone is a cubic foot). For every level of ";
echo "quarrying achieved, a tribe may assign ten (10) actives to the task of quarrying.</DD>";

echo "<DT><FONT COLOR=RED>Engineering</FONT> (eng): (partially coded)</DT>";
echo "<DD>Provides the ability to construct buildings and defenses. The higher a tribe's engineering skill, the more structures that will become ";
echo "available to them. For structures that are non-defensive in nature (eg: refineries, bakeries, mills) a meeting house and trade post must first ";
echo "be constructed. The following structures have been coded: </DD>";
echo "<UL>Meeting House</UL>";
echo "<UL>Trade Post</UL>";
echo "<UL>Refineries</UL>";
echo "<UL>Moats</UL>";
echo "<UL>Smelters</UL>";

echo "<DT><FONT COLOR=WHITE>Spying</FONT> (spy):</DT>";
echo "<DD>Provides a tribe with the ability to 'peek' at other nearby tribes which do not belong to thier clan. This skill also provides a tribe ";
echo "with the ability to 'intercept' couriers which is represented by the ability to view other chiefs messages. This last ability is difficult ";
echo "to accomplish, but can be done. So it is best to have a good security skill to defend against it.</DD>";

echo "<DT><FONT COLOR=WHITE>Security</FONT> (sec):</DT>";
echo "<DD>Provides the tribe with a defense against spies and raiders from other tribes.</DD>";

echo "<DT><FONT COLOR=WHITE>Refining</FONT> (ref):</DT>";
echo "<DD>Using smelters, refining skill allows a tribe to turn ore into useable metals to make tools and weapons. For each smelter, a tribe may ";
echo "assign 10 tribesmen to the task of refining.</DD>";

echo "<DT><FONT COLOR=WHITE>Diplomacy</FONT> (dip):</DT>";
echo "<DD>Diplomacy enables your tribe to create subtribes. You may not create subtribes unless your diplomacy level exceeds the number of tribes ";
echo "you currently own. A high diplomacy level also allows you to know what clans your allies are also allied with, this is useful in cases where ";
echo "you suspect someone is about to betray your trust.</DD>";

echo "<DT><FONT COLOR=WHITE>Metalworking</FONT> (mtl):</DT>";
echo "<DD>Metalworking is the skill in which you may make certain types of metal tools depending on your expertise. You of course need to have the ";
echo "required materials. If you have any Brass or Bronze, those metals will be used first, Iron being used only for those items that require the ";
echo "use of iron or steel only. Steel is not used by this skill, since it is assumed that a tribe will reserve steel for armor and weapon production.</DD>"; 

echo "<DT><FONT COLOR=WHITE>Literacy</FONT> (lit):</DT>";
echo "<DD>Developing your literacy skill improves the intelligence of your tribe, and is reflected in a 1% bonus chance of success in your skill ";
echo "attempts each turn. A maximum bonus of +20% can be achieved. Without literacy, your tribe will find it difficult, if not impossible to gain ";
echo "skills past 10th level.</DD>";

echo "<DT><FONT COLOR=WHITE>Research</FONT> (res):</DT>";
echo "<DD>Research improves your tribe's chances of success in skill attempts much like literacy, except it is handled differently. Unlike literacy, ";
echo "the research skill is only useable for skill attempts of level 11 or greater. Also unlike literacy, upon each succesful skill attempt, ";
echo "your research skill will fall back to zero and will need to be developed once again. The bonus from research is +1% per level. Literacy ";
echo "and research can be used together for a maximum of +30% (research skill would be needed to gain more than 10% in research).</DD>";

echo "<DT><FONT COLOR=WHITE>Scouting</FONT> (sct): <FONT COLOR=RED>(partially coded)</FONT></DT>";
echo "<DD>Scouting skill allows you to be more aware of your surroundings. With scouting skill, you will be able to unveil obscured map hexes ";
echo "without having to move your tribe, you will be able to unveil much more of the map at a much greater rate. Also with a higher scouting ";
echo "skill, you will be aware when tribes pass through your current hex, even higher skill allows you to know if a tribe is in a neighboring ";
echo "map hex, and even higher scouting skill will allow you to know when a tribe passes through a neighboring map hex.</DD>";

echo "<DT><FONT COLOR=WHITE>Skinning</FONT> (skn): </DT>";
echo "<DD>Skill needed to disrobe an animal. Usually a messy sight.</DD>";

echo "<DT><FONT COLOR=WHITE>Gutting</FONT> (gut): </DT>";
echo "<DD>Skill needed to remove the intestines from animals. Gut is a very useful resource.</DD>";

echo "<DT><FONT COLOR=WHITE>Tanning</FONT> (tan): </DT>";
echo "<DD>Skill that turns furs and skins into leather suitable for clothing, armor, or items.</DD>";

echo "<DT><FONT COLOR=WHITE>Curing</FONT> (cur):</DT>";
echo "<DD>Another technique for turning furs and skins into leather.</DD>";

echo "<DT><FONT COLOR=WHITE>Dressing</FONT> (dre):</DT>";
echo "<DD>Third technique for turning furs and skins into leather using salt.</DD>";

echo "<DT><FONT COLOR=WHITE>Weapon Making</FONT> (wpn):</DT>";
echo "<DD>Skill used to make sharp pointed things to stick into your enemy.</DD>";







echo "</TD></TR></TABLE><BR>";


navbar_help();

if( $_SESSION[clanid] )
{
    TEXT_GOTOMAIN();
}
echo "</CENTER>";

page_footer();

?> 
