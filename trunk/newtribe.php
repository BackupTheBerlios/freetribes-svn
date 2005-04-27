<?php
session_start();
header("Cache-control: private");

include("config.php");

page_header("Tribe Management");

connectdb();


///////////////////////////////////////////////////////////////////////////////


$username = $_SESSION[username];
$chief = $db->Execute("SELECT * FROM $dbtables[chiefs] WHERE username = '$username'");
$chiefinfo = $chief->fields;

$clan = $db->Execute("SELECT * FROM $dbtables[clans] WHERE clanid = '$chiefinfo[clanid]'");
$claninfo = $clan->fields;

$current_unit = $_SESSION[current_unit];

$tribe = $db->Execute("SELECT * FROM $dbtables[tribes] WHERE tribeid = '$current_unit'");
$tribeinfo = $tribe->fields;

$numb = $db->Execute("SELECT * FROM $dbtables[tribes] WHERE clanid = '$_SESSION[clanid]'");
$tribenum = $numb->RecordCount();

$skill = $db->Execute("SELECT * FROM $dbtables[skills] WHERE tribeid = '$current_unit' ORDER BY long_name");
$skillinfo = $skill->fields;

$admin = $db->Execute("SELECT * FROM $dbtables[skills] WHERE tribeid = '$current_unit' AND abbr = 'adm'");
$admininfo = $admin->fields;

$diplo = $db->Execute("SELECT * FROM $dbtables[skills] WHERE tribeid = '$current_unit' AND abbr = 'dip'");
$diploinfo = $diplo->fields;

$monthinfo = $db->Execute("SELECT * FROM $dbtables[game_date] WHERE type = 'month'");
$month = $monthinfo->fields;
$gm = $month[count];

$yearinfo = $db->Execute("SELECT * FROM $dbtables[game_date] WHERE type = 'year'");
$year = $yearinfo->fields;
$gy = $year[count];

$unique = uniqid(microtime(),1);

$timestamp = date("Y-m-d H:i:s");


echo "<BR><BR><CENTER><FONT SIZE=+1 COLOR=white>Tribe Management</FONT></CENTER>";


if($tribe->RecordCount() >= 1)
{
    if(!ISSET($_REQUEST[action]))
	{
        echo "<CENTER><FORM ACTION=newtribe.php METHOD=POST>";
        echo "<TABLE><TR><TD>Action:</TD><TD><SELECT NAME=action>";
        echo "<OPTION SELECTED>Create</OPTION>";
        echo "<OPTION>Destroy</OPTION>";
        echo "</SELECT>";
        echo "</TD><TD><INPUT TYPE=SUBMIT VALUE=Command>";
        echo "</TR></TABLE></FORM></CENTER><BR><BR>";
	page_footer();
    }
}


if($_REQUEST[action] == 'Create')
{
	echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=newtribe2.php?unit=$_SESSION[current_unit]&unique=$unique\">";
}

if($_REQUEST[action] == 'Destroy' & !ISSET($_REQUEST[destroy]))
{
	echo "Your current tribe (" . $_SESSION[current_unit] . ") will be destroyed.<BR>";
	echo "<FORM ACTION=newtribe.php METHOD=POST>";
	echo "<INPUT TYPE=RADIO NAME=destroy VALUE=1 CHECKED>Cancel</INPUT><BR>";
	echo "<INPUT TYPE=RADIO NAME=destroy VALUE=2>Confirm</INPUT><BR><BR><BR>";
	echo "<INPUT TYPE=HIDDEN NAME=action VALUE=Destroy>";
	echo "<INPUT TYPE=SUBMIT VALUE=Submit>";
	echo "</FORM>";
}
if($_REQUEST[action] == 'Destroy')
{ 
	if($_REQUEST[destroy]  == '1')
	{
	    echo "Action Canceled.<BR><BR>";
	}
    elseif($_REQUEST[destroy] == '2')
	{
		if($_SESSION[current_unit] == $_SESSION[clanid])
		{
		echo "You cannot disband your main tribe.<BR><BR>";
	    }
        else
		{
			$maintribe = $_SESSION[clanid];
			$byetribe = $_SESSION[current_unit];
			$db->Execute("DELETE FROM $dbtables[tribes] WHERE tribeid = '$byetribe'");
			$db->Execute("DELETE FROM $dbtables[skills] WHERE tribeid = '$byetribe'");
			$db->Execute("DELETE FROM $dbtables[livestock] WHERE tribeid = '$byetribe'");
			$db->Execute("DELETE FROM $dbtables[resources] WHERE tribeid = '$byetribe'");
			$db->Execute("DELETE FROM $dbtables[products] WHERE tribeid = '$byetribe'");
			$db->Execute("INSERT INTO $dbtables[logs] "
						." VALUES("
						."'',"
						."'$_SESSION[clanid]',"
						."'$byetribe',"
						."'TRIBEDELETE',"
						."'$timestamp',"
						."'$gm/$gy $byetribe disbanded.')");

			echo "Tribe $byetribe disbanded.<BR><BR>";

			include("weight.php");

			$db->Execute("UPDATE $dbtables[chiefs] "
						."SET current_unit = '$maintribe' "
						."WHERE clanid = '$_SESSION[clanid]'");
			$_SESSION[current_unit] = $maintribe;
		}
	}
}

page_footer();
?>
