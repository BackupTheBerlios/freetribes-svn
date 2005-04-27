<?php
function navbar_open ()
{
	echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=\"100%\">"
		."<TR>"
		."<TD CLASS=link_navbar_sep WIDTH=5>&nbsp;</TD>";
}

function navbar_link ($href, $target, $caption)
{
	echo "<TD CLASS=link_navbar><A HREF=\"$href\" TARGET=\"$target\">$caption</A></TD>"
		."<TD CLASS=link_navbar_sep></TD>";
}

function navbar_close()
{
	echo "<TD CLASS=link_navbar_sep WIDTH=5>&nbsp;</TD>"
		."</TR>"
		."</TABLE>";
}

function navbar_general($page_name)
{
	global $game_url_path, $link_forums;

	navbar_open();
	navbar_link($game_url_path."mailto.php", "", "Diplomacy");
	navbar_link($game_url_path."heraldry.php", "", "Heraldry");
        if( $page_name <> "mapping.php" )
        {
	    navbar_link($game_url_path."mapping.php", "", "Maps");
        }
        else
        {
            navbar_link($game_url_path."bigmap.php", "", "Big Map");
        } 
	navbar_link($game_url_path."religion.php", "", "Religion");
	navbar_link($game_url_path."helper.php", "ts_helper", "Helper");
	navbar_link($link_forums, "ts_forums", "Forums");
	navbar_close();
	navbar_open();
	navbar_link($game_url_path."main.php", "", "<B>Clan Overview</B>");
	navbar_link($game_url_path."activities.php", "", "Activities");
	navbar_link($game_url_path."garrisons.php", "", "Garrisons");
	navbar_link($game_url_path."report.php", "", "Reports");
	navbar_link($game_url_path."scouting.php", "", "Scouts");
	navbar_link($game_url_path."newtribe.php", "", "Subtribes");
	navbar_link($game_url_path."transfer.php", "", "Transfers");
	navbar_close();
}

function navbar_help()
{
	global $game_url_path, $link_forums;

navbar_open();
navbar_link("help.php", "", "Intro");
navbar_link("help_basics.php", "", "Basics");
navbar_link("help_faq.php", "", "FAQ");
navbar_link("help_skills.php", "", "Skills");
navbar_link("help_rsrc.php", "", "Resources &amp; Production");
navbar_link("help_combat.php", "", "Combat");
navbar_link("help_map_editor.php", "", "Map Editor");
navbar_link("help_maps.php", "", "Map Info");
navbar_link("forum/", "", "Forums");
navbar_link("bugtracker.php", "", "Bug Reports");
navbar_link("index.php", "", "Login");
navbar_close();
}

?>
