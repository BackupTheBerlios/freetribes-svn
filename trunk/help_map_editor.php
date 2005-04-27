<?
session_start();
header("Cache-control: private");

if (!ISSET($_SESSION['theme']))
{
	$_SESSION['theme'] = $theme_default;  // This must be set on this page before the header is included
}

include("config.php");

page_header("Map Editing Help");

connectdb();

echo "<CENTER>";

navbar_help();

echo "<BR><BR>";
echo "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=\"70%\"><TR><TD>";

echo "Here are the directions for the map editor. I have also included a screen shot of the editor itself for your ";
echo "reference. Please note that terrain types only can be edited via this tool, any changes in resources must be ";
echo "manually edited by myself for the time being. <P>";
echo "To edit the map, you need to be a 'builder' which is a junior admin, sort of. But, once I have given you the ";
echo "permissions, you will see the admin link appear on your main screen. From there, you will be able to select from ";
echo "a drop down box. You will want to select the 'hex edit' option and hit 'submit'. <P>";

echo "You will then find yourself on the Map Editor page. Now, when you first get to the page, you have no 'terrain type' ";
echo "set. You choose that from the dropdown list in the center of the page, and hit 'set'. If you refresh the page once ";
echo "or twice, you will see your terrain type selected appear above the dropdown list as the terrain that is currently set. ";
echo "Pay attention to that, as this is the terrain that the map tiles will change to as you click on them. ";
echo "Find a tile you wish to change to the terrain type you've set, and click on it. Refresh the page and you will see that ";
echo "the map centers on that tile and it changes to the terrain you set. Be patient, as this is a very clunky editor and ";
echo "not for the impatient. ";
echo "<P>If there is a specific location you wish to work on, you can enter the tile id in the box and hit enter. The map will ";
echo "center on that map tile. You may also navigate around the map by clicking on the map tiles, and bringing that map tile ";
echo "to the center of the map. Be careful, and only click on tiles that are already the terrain you have set, or else you may ";
echo "inadvertantly change a tile when you might not have wished to. ";

echo "NOTE: If you stumble upon a broken image in the map, it means that someone has assigned a non-hills terrain to a map tile that ";
echo "contains a resource. If you change the tile to a hilly resource (by following the steps above) you will have fixed the problem.";

echo "<P><BR>It is VERY easy, and it would help me out a lot. Besides, you get to see the world while you're doing it.";





echo "</TD></TR></TABLE><BR>";

echo "<IMG SRC=images/editor.png><P><BR>";

navbar_help();

if( $_SESSION[clanid] )
{
    TEXT_GOTOMAIN();
}
echo "</CENTER>";

page_footer();

?> 
