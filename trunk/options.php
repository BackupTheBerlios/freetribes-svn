<?
session_start();
header("Cache-control: private");
include("config.php");


page_header("Account Options"); 

connectdb();

//-------------------------------------------------------------------------------------------------
$username = $_SESSION['username'];
$res = $db->Execute("SELECT * FROM $dbtables[chiefs] WHERE username='$username'");
$playerinfo = $res->fields;
//-------------------------------------------------------------------------------------------------

echo "<FORM ACTION=option2.php METHOD=POST>";
echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=2 ALIGN=CENTER>";
echo "<TR BGCOLOR=\"$color_header\">";
echo "<TD COLSPAN=3  onmouseover=\"return overlib('You only need to submit your current password when you are changing it, or when you delete your account.');\" onmouseout=\"nd();\"><B>Change Password</B> <I>Only necessary when changing password.</I></TD>";
echo "</TR>";
echo "<TR BGCOLOR=\"$color_line1\" onmouseover=\"return overlib('You only need to submit your current password when you are changing it, or when you delete your account.');\" onmouseout=\"nd();\">";
echo "<TD>Current Password</TD>";
echo "<TD><INPUT CLASS=edit_area TYPE=PASSWORD NAME=oldpass SIZE=16 MAXLENGTH=16 VALUE=\"\"></TD><TD></TD>";
echo "</TR>";
echo "<TR BGCOLOR=\"$color_line2\" onmouseover=\"return overlib('You only need to submit your current password when you are changing it, or when you delete your account.');\" onmouseout=\"nd();\">";
echo "<TD>New Password</TD>";
echo "<TD><INPUT CLASS=edit_area TYPE=PASSWORD NAME=newpass1 SIZE=16 MAXLENGTH=16 VALUE=\"\"></TD><TD></TD>";
echo "</TR>";
echo "<TR BGCOLOR=\"$color_line1\" onmouseover=\"return overlib('You only need to submit your current password when you are changing it, or when you delete your account.');\" onmouseout=\"nd();\">";
echo "<TD>Confirm New Password</TD>";
echo "<TD><INPUT CLASS=edit_area TYPE=PASSWORD NAME=newpass2 SIZE=16 MAXLENGTH=16 VALUE=\"\"></TD><TD></TD>";
echo "</TR>";
echo "<TR BGCOLOR=\"$color_header\" onmouseover=\"return overlib('Here is where you can change the details of both your clan, and the specific tribe you are currently focused on.');\" onmouseout=\"nd();\">";
echo "<TD COLSPAN=3><B>Change Clan Details</B></TD>";
echo "</TR>";
echo "<TR BGCOLOR=\"$color_line2\" onmouseover=\"return overlib('This will change the name of your entire clan. This name will be what is displayed on the heraldry page as well as the diplomacy page.');\" onmouseout=\"nd();\"><TD>Change Clan Name</TD>";
echo "<TD><INPUT CLASS=edit_area TYPE=TEXT NAME=clanname MAXLENGTH=16 VALUE=\"\"></TD><TD></TD></TR>";
echo "<TR BGCOLOR=\"$color_line1\" onmouseover=\"return overlib('This will only change the name of the specific tribe you are currently focused on and will not be displayed to anyone else. Generally, tribe names are typically used to remind chiefs what purpose the tribe is for.');\" onmouseout=\"nd();\"><TD>Change Tribe Name ($_SESSION[current_unit])</TD>";
echo "<TD><INPUT CLASS=edit_area TYPE=TEXT NAME=tribename MAXLENGTH=30 VALUE=\"\"></TD><TD></TD></TR>";
echo "<TR BGCOLOR=\"$color_header\">";
echo "<TD COLSPAN=3><B>Change Interface</B></TD>";
echo "</TR>";
if( $_SESSION[tooltip] == '1' )
{
    $checked = "0";
    $checktext = "Off";
echo "<TR BGCOLOR=\"$color_line2\" onmouseover=\"return overlib('Check to turn off tooltips.');\" onmouseout=\"nd();\">";
}
else
{
    $checked = "1";
    $checktext = "On";
echo "<TR BGCOLOR=\"$color_line2\">";
}
echo "<TD>"
    ."Tool Tips"
    ."</TD>"
    ."<TD COLSPAN=2>"
    ."<INPUT TYPE=CHECKBOX NAME=tooltip VALUE=$checked>" 
    ."&nbsp;$checktext"
    ."</TD>"
    ."</TR>";
echo "<TR BGCOLOR=\"$color_line1\">"
	."<TD>"
	."Change Theme"
	."</TD>"
	."<TD>";
form_select_theme($playerinfo);
echo "</TD><TD></TD>"
	."</TR>";
/*
echo "<TR BGCOLOR=\"$color_header\"><TD COLSPAN=2>MiniMap Size</TD></TR>";
echo "<TR BGCOLOR=\"$color_line1\"><TD><INPUT TYPE=radio name=minimap value=\"1\" checked></TD>";
echo "<TD>3 x 3 Map</TD></TR>";
echo "<TR BGCOLOR=\"$color_line2\"><TD><INPUT TYPE=radio name=minimap value=\"2\"></TD>";
echo "<TD>5 x 5 Map <FONT SIZE=-1>(not coded yet)</FONT></TD></TR>";
*/
echo "<TR BGCOLOR=\"$color_line1\"><TD COLSPAN=3>&nbsp;</TD></TR>"
	."<TR BGCOLOR=\"$color_line1\">"
	."<TD COLSPAN=2>"
	."</TD>"
	."<TD ALIGN=RIGHT>"
	."<INPUT TYPE=SUBMIT value=\"Save\">"
	."</TD>"
	."</TR>"
	."<TR BGCOLOR=\"$color_line1\"><TD COLSPAN=3>&nbsp;</TD></TR>"
	."<TR BGCOLOR=\"$color_line2\" onmouseover=\"return overlib('If checked, you must provide your password, and this will delete your account.');\" onmouseout=\"nd();\">"
	."<TD COLSPAN=3>"
	."<INPUT TYPE=checkbox NAME=delete VALUE='1'>"
	." Abdicate your throne? (Delete Account)"
	."</TD>"
	."</TR>"
	."</TABLE>";
echo "<BR>";
echo "<P>";
echo "<BR><BR><BR>";
echo "</FORM>";

TEXT_GOTOMAIN();
echo "<p><br><br>";
page_footer();


function form_select_theme($playerinfo)
{
	global $theme_default;

	echo "<SELECT NAME=theme>";
    $handle=opendir('themes');                    // get list of valid themes
    while ($file = readdir($handle))
	{
		if ( !ereg("[.]",$file) && file_exists("themes/$file/style.css") )
			{
			$themelist .= "$file ";
			}
    }
    closedir($handle);

    $themelist = explode(" ", $themelist);
    sort($themelist);
    for ($i=0; $i < sizeof($themelist); $i++)
	{
    	if($themelist[$i]!="")
		{
    	    echo "<option value=\"$themelist[$i]\"";
			if( (	($playerinfo[theme]=="") && ($themelist[$i]=="$theme_default")) || ($playerinfo[theme]==$themelist[$i]))
			{
				echo " selected";
			}
	    echo ">$themelist[$i]\n";
		}
    }

	if($playerinfo[theme]=="")
	{
		$playerinfo[theme] = "$theme_default";
	}
    echo "</select><br>";
}


?>

