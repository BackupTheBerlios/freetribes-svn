<?
session_start();
header("Cache-control: private");
include("config.php");

page_header("Logging Out");

connectdb();

$result = $db->Execute("SELECT * FROM $dbtables[chiefs] WHERE clanid ='$_SESSION[clanid]'");
$playerinfo = $result->fields;

if($_SESSION[delete] == '1')
{
	$title = "Clan Deleted";
	bigtitle();
	echo "Clan $playerinfo[clanid] successfully deleted.<BR>";
	playerlog($playerinfo[clanid], LOG_DELETE, $ip);
	$_SESSION = array();
	session_destroy();
	TEXT_GOTOLOGIN();
}
else
{
	playerlog($playerinfo[clanid], LOG_LOGOUT, $ip);
	$_SESSION = array();
	session_destroy();
	bigtitle();
	echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=index.php\">";;
}


page_footer();

?>
