<?
session_start();
header("Cache-control: private");

if (!ISSET($_SESSION['theme']))
{
	$_SESSION['theme'] = $theme_default;  // This must be set on this page before the header is included
}

include("config.php");

page_header("Greetings and welcome to TribeStrive!");

connectdb();

echo "<CENTER>";

navbar_help();

echo "<BR><BR>";
echo "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=\"70%\"><TR><TD>";
echo "<P>Of the innumerable simulations in the world, most can be easily classified.  There are wargames, there are ";
echo "diplomatic games, and there are games of civilization.  And among these games there are obvious trade-offs. ";
echo "The wargame has the war already going while the civilization game treats war as an abstraction. Very few games ";
echo "have managed to merge the three types and still kept the flavor of each fully intact. Tribe Net blends the best ";
echo "and essential features of these three genres.</P>";

echo "<P>In Tribe Net a player operates his Clan to the point of micro-management. He assigns people to feed the Clan, ";
echo "make weapons, make tools, and scout the land. He has no information other than what he can gather himself. He is ";
echo "limited in his knowledge of terrain. He is limited in his knowledge of the reality of other Clans. The fog of war ";
echo "can be truly of Pea Soup proportions. Decisions have far reaching consequences. Assign too few people to hunt or ";
echo "farm or fish and your people will starve. Fail to arm and they are vulnerable to bandits and aggressors. Fail to ";
echo "improve their ability to perform tasks, and they remain primitive and inconsequential.</P>";

echo "<P>The Clan's abilities are categorised by certain tasks, make armor, make weapons, farm, hunt, herd, etc. And to ";
echo "allow these task abilities to be simulated they are graded on a scale of 0 to 20, with 0 being the most basic. ";
echo "The higher the skill the more difficult to attain it, and skills are obtained sequentially. You must have farming ";
echo "5 to go to Farming 6.</P>";

echo "<P>Clans may be comprised of multiple tribes, and tribes contain animals, goods, and people. All are precious and ";
echo "usefull. You must have horses for your cavalry and your scouts; you must have elephants for transport. Cattle ";
echo "and goats are food on the hoof. You need traps and spears and bows for hunting. You need hoes or plows for farming. ";
echo "And all this stuff has to be made. Furthermore, you have to locate the materials to make the stuff. The drive is ";
echo "always on to find minerals and coal, and to protect your supplies once you find them. And you must balance the work ";
echo "of your people. You have warriors, who can fight, Actives who work. And Inactives (women and children) who ";
echo "do no work but are essential for reproduction. And all of them eat.</P>";

echo "<P>Operating a Tribe, getting it to grow and to become more powerful, may seem to be a worthy end in itself. But this ";
echo "is only the tip of the iceberg. Where TribeStrive makes it's greatest deviation from the run-of-the-mill civilization ";
echo "game is that the player determines what his Tribe is actually like, and interacts freely with the rest of the ";
echo "continent through in-game messenger courier. This is diplomacy in it's purest form. Your knowledge of your colleagues ";
echo "is extremely limited, and the power that you can project can be real or illusory. You can chart your course as a lone ";
echo "wolf and speak to no one, or become a major player and be involved heavily in the discourse of the game. The diplomatic ";
echo "traffic is intense, and the impact is great. Through this a player can inject as much or as little color into the game ";
echo "as he wishes. The only limitation being those that the player imposes on himself. There are evil tribes and peaceful tribes, ";
echo "slavers and freemen, religious blocs and xenophobic loners. Those tribes who make the effort to speak up and involve ";
echo "themselves in the game find it rich and complex.</P>";

echo "<P>One factor that Tribe Net has that is almost totally absent anywhere else is Religion. In the days before nationalism ";
echo "(and indeed in many places in the world today) it is the religion by which the person identifies him or herself. Getting ";
echo "this to be realistically simulated can be almost impossible, but Tribe Net has managed to do it. Religions are invented ";
echo "by the players, and with the Administrative Staff's guidance, the religion's beliefs, and effects, are inventions of the ";
echo "players. The blocs that ideologies create in the modern world exist in Tribe Net with these religions, and whether you are ";
echo "a friend or an enemy can be dtermined solely by the religion of the Clan. Religions can be warlike or peaceful, benign or cruel, ";
echo "and all the shaded areas in between. Religions may range from active cannibalism to fundamental anti-slavery.</P>";

echo "<P>The turn cycle is 1-month game time every day. A player logs in and manages his clan, which entails allocating people ";
echo "to specific activities of the tribe, movement, attacks, and skill attempts for that turn. Each night, the activities are ";
echo "calculated and tribes are updated to reflect any changes and to prepare them for the next turn. Diplomatic intercourse ";
echo "may occur at any time of the player's convenience.</P>";

echo "<P>The map that TribeStrive operates on is impressive. As the tribes search out their surroundings it becomes quickly obvious ";
echo "that no matter how far they go, there is more farther on. Mapping is a precious commodity, and Clan chiefs have the ability ";
echo "within the game to exchange thier map information with one another.</P>";

echo "<P>Clans and tribes are identified by the game with a unique number. Players, however, name their Clan whatever they want. ";
echo "Some may change the name of their Tribe more than once, which is why unique numbers are used by the game. Tribal names can ";
echo "be rich with meaning, or humorous. Examples are Hailong, the Chinese name of the Black Dragon, Yamato, a poetic term for Japan ";
echo "and a word embodying the Japanese Spirit. Kung Sah, the name of an infamous Malayan drug lord. The Oxwind, the Heck'r'we, the ";
echo "Sbaras, the GrossartigBastarde. Where a reference to \"Tribe449\" inspires little, the same reference to the \"Velvet Glove\" tells you ";
echo "something about what you are facing.</P>";

echo "<P>But again, that is in the hands of the players.</P>";

echo "<P>A game with this many facets runs a high risk of being addictive, and it truly is. With it's long time span and need for ";
echo "thorough planning and persistence it has tremendous appeal for the player who wishes to immerse himself in the game. But for ";
echo "the very casual player it will have little appeal. The game will return effort abundantly, but if little effort is made then ";
echo "the player simply drifts at the mercy of those players who do involve themselves. It can then be about as much fun as being a ";
echo "tennis ball. In order to get the most out of the game, the player must involve himself in it, and the more involved the player ";
echo "is the more fun the game becomes. So if a person is a casual gamer, who likes beer-and-pretzel games that can be picked up and ";
echo "completed in a single hour or two, then Tribe Net is not a good idea.</P>";

echo "<P>But if a player likes to immerse himself in a game and thinks that real-world simulations are for them, then Tribe Net would be ";
echo "a good investment.</P>";
echo "</TD></TR></TABLE>";


navbar_help();

if( $_SESSION[clanid] )
{
    TEXT_GOTOMAIN();
}
echo "</CENTER>";

page_footer();

?> 
