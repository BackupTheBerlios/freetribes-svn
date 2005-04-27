<?
global $time_start;
$time_start = getmicrotime();
global $theme_default, $game_root, $game_url_path;
if (!ISSET($_SESSION['theme']) && $page_name <> "main.php")
{
	$_SESSION['theme'] = "";
	if(empty($theme_default))
	{
		$theme_default = "Original";
	}
	$_SESSION['theme'] = $theme_default;
}
$theme = $game_url_path . "/themes/" . $_SESSION['theme'] . "/style.css";

global $page_name;
ereg("([^/]*).php", $_SERVER['PHP_SELF'], $page_name); // get the name of the file being viewed

$page_name = $page_name[0];

global $page_title, $game_name;


global $use_local;
if (!function_exists("navbar_open"))
{
	include("gui/navbar.php");
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Pragma" content="no-cache">
<meta name="Keywords" content="strategy,roll play,multi player,mmporg,civilization,warcraft,blacknova traders,conquest,nations,online gaming,starcraft,massive multiplayer online roleplay game,war game,combat,history,ancient,ceasar,vikings,tournament,battle,technology,research,apiarism,blacksmith,armor,weapons,swords,arrows,shields,metal refining">
<title><? echo $game_name." TS: ".$page_title; ?></title>

<link rel="stylesheet" type="text/css" href="<? echo $theme; ?>">

<LINK REL="SHORTCUT ICON" HREF="favicon.ico">
<?
if( !$_SESSION[current_unit] || $_SESSION[tooltip] == '1' )
{
    echo "<SCRIPT LANGUAGE=\"JavaScript\" src=\"overlib/overlib.js\" type=\"text/javascript\"></SCRIPT>";
    echo "<SCRIPT LANGUAGE=\"JavaScript\" src=\"overlib/overlib_hideform.js\" type=\"text/javascript\"></SCRIPT>";
    echo "<SCRIPT LANGUAGE=\"JavaScript\" src=\"overlib/overlib_shadow.js\" type=\"text/javascript\"></SCRIPT>";
    echo "<SCRIPT LANGUAGE=\"JavaScript\" src=\"overlib/overlib_exclusive.js\" type=\"text/javascript\"></SCRIPT>";
}
?>
<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
    function placeFocus() 
    {
        if(document.forms.length > 0) 
        {
            var field = document.forms[0];
            for (i = 0; i < field.length; i++) 
            {
                if ((field.elements[i].type == "text") 
                    || (field.elements[i].type == "textarea") 
                    || (field.elements[i].type.toString().charAt(0) == "s")) 
                {
                    document.forms[0].elements[i].focus();
                    break;
                }
            }
        }
    }
</SCRIPT>
<SCRIPT TYPE="text/javascript">
<!--
overlib_pagedefaults(FGCOLOR, '#0f6e3f', TEXTCOLOR, '#f4d7a4', RELX, 25, RELY, 143, SHADOW, SHADOWIMAGE, 'images/parchment_bg.png', SHADOWOPACITY, 60, SHADOWY, 10);
//-->
</SCRIPT>
<?

if( $page_name == "new.php" )
{
    echo "<!-- GR - Adding javascript code here to caclulate points used -->\n";
    echo "<SCRIPT type=\"text/javascript\">\n";
    echo "<!--\n";
        echo "function calcPoints () { \n"
                ."var f = document.newClanForm;\n"
                ."var pointTotal = new Number(0);\n"
                ."pointTotal += f.armor.selectedIndex     + f.bonework.selectedIndex      + f.boning.selectedIndex                + f.curing.selectedIndex;\n"
                ."pointTotal += f.dressing.selectedIndex  + f.fishing.selectedIndex       + f.fletching.selectedIndex     + f.forestry.selectedIndex;\n"
                ."pointTotal += f.gutting.selectedIndex   + f.herding.selectedIndex       + f.hunting.selectedIndex               + f.jewelery.selectedIndex;\n"
                ."pointTotal += f.quarry.selectedIndex    + f.salting.selectedIndex       + f.sewing.selectedIndex                + f.siege.selectedIndex;\n"
                ."pointTotal += f.leather.selectedIndex   + f.metalwork.selectedIndex + f.mining.selectedIndex            + f.pottery.selectedIndex;\n"
                ."pointTotal += f.skinning.selectedIndex  + f.tanning.selectedIndex       + f.waxworking.selectedIndex    + f.weapons.selectedIndex;\n"
                ."pointTotal += f.weaving.selectedIndex   + f.whaling.selectedIndex       + f.woodwork.selectedIndex              + f.furrier.selectedIndex;\n"
                ."pointTotal += (f.leadership.selectedIndex + f.administration.selectedIndex + f.scouting.selectedIndex + f.economics.selectedIndex)*3;\n"
                ."document.newClanForm.points.value = pointTotal;\n"
        ."}\n";

        echo "function doSubmit () {\n"
                ."var pointsUsed = new Number(document.newClanForm.points.value);\n"
                ."if( pointsUsed > 50 ) {\n"
                        ."alert(\"You have allocated more than 50 points.  [\"+pointsUsed+\"] used.\");\n"
                ."} else {\n"
                        ."document.newClanForm.submit();\n"
                ."}\n"
        ."}\n"
    ."//-->\n"
    ."</SCRIPT>\n"
    ."<!-- GR End -->\n";
}
?>
</HEAD>
<?
if( $page_name == "index.php" || $page_name == "new.php" )
{
    echo "<BODY OnLoad=\"placeFocus()\">";
}
else
{
    echo "<BODY><div id=\"overDiv\" style=\"position:absolute; visibility:hidden; z-index:1000;\"></div>";
}


global $privilege, $db, $dbtables;

connectdb();
$username = $_SESSION['username'];
$user = $db->Execute("SELECT * FROM $dbtables[chiefs] WHERE username = '$username'");
$userinfo = $user->fields;
$_SESSION['clanid'] = $userinfo['clanid'];

echo "<TABLE CLASS=page_header BORDER=0 WIDTH=\"100%\">"
	."<TR>";

if ($page_name=="main.php" && $_SESSION[username])
{
	if (ISSET($_REQUEST['id']))
	{
		$_SESSION['current_unit'] = $_REQUEST['id'];
		$curr_unit = $_SESSION['current_unit'];
	}
	echo "<TD CLASS=page_title ALIGN=CENTER WIDTH=\"100%\"> Chief " . $_SESSION['chiefname'];

	if( !$_SESSION[clanname] == '' )
	{
		echo " of the " . $_SESSION['clanname'];
	}

	$vil = $db->Execute("SELECT * FROM $dbtables[structures] "
					   ."WHERE tribeid = '$_SESSION[current_unit]' "
					   ."AND long_name = 'meetinghouse' "
					   ."AND hex_id = '$_SESSION[hex_id]'");
	if( $vil->EOF )
	{
		$unittype = 'tribe';
		$verb = 'Travelling';
	}
	else
	{
		$unittype = 'village';
		$verb = 'Staying';
	}

	echo "<BR><FONT CLASS=page_subtitle>";
	if($tribeinfo[tribename] == '')
	{
		echo "$verb with $unittype $_SESSION[current_unit]\n";
	}
	else
	{
		echo "$verb with the $unittype of the $tribeinfo[tribename]\n";
	}	
	echo "</FONT>";
}
else
{
	echo "<TD ALIGN=LEFT WIDTH=\"100%\">"
		."<FONT class=page_title>".$page_title."</FONT>"
		."</TD>";
}

echo "<TD ALIGN=RIGHT>";

	if( $userinfo['admin'] >= $privilege['adm_access'] )
	{
		navbar_open();
		navbar_link($game_url_path."admin.php", "", "Admin");
		navbar_close();
	}

help_page_link();
echo "</TD>"
	."</TR>"
	."<TR>"
	."<TD COLSPAN=2>";

global $gamedomain;
if ($page_name<>"index.php"
	&& $page_name<>"credits.php"
	&& $page_name<>"faq.php"
	&& $page_name<>"help.php"
	&& $page_name<>"help_basics.php"
	&& $page_name<>"help_faq.php"
	&& $page_name<>"help_skills.php"
	&& $page_name<>"help_rsrc.php"
	&& $page_name<>"help_combat.php"
	&& $page_name<>"help_maps.php"
	&& $page_name<>"help_map_editor.php"
	&& $page_name<>"heraldry.php"
	&& $page_name<>"new.php"
	&& $page_name<>"new2.php"
	)
{


	if(!ISSET($_SESSION['username']))
	{
	  echo "<BR><BR><FONT CLASS=page_header_title>You must <a href=\"".$game_url_path."index.php\">log in</a> to view this page.</FONT><BR><BR>\n";
	  die();
	}
	else
	{
		navbar_general($page_name);
	}

}
elseif ($page=="main")
{
	if(!ISSET($_SESSION['username']))
	{
		echo "</TD>"
			."</TR>"
			."</TABLE>"
			."<P>";		
	  echo "You must <a href=\"".$game_url_path."index.php\">log in</a> to view this page.<br>\n";
	  die();
	}
}


echo "</TD>"
	."</TR>"
	."</TABLE>"
	."<P>";
?>
