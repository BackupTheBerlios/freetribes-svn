<?
session_start();
header("Cache-control: private");
include("config.php");

connectdb();

$username = $_REQUEST['username'];
$_SESSION['username'] = $username;
$password = $_REQUEST['password'];
$md5password = md5($password);

$res = $db->Execute("SELECT * FROM $dbtables[chiefs] WHERE username='$username' LIMIT 1");
$playerinfo = $res->fields;
$_SESSION['tooltip'] = $playerinfo[tooltip];
$_SESSION['theme'] = $theme_default;  // This must be set on this page before the header is included

page_header("Login Pass On");

if($playerinfo['password'] == $md5password & !$server_closed == 'true')
{
	$title="Login Successful";
	bigtitle();
	echo "Your tribe welcomes you.";
	$sessvars = $db->Execute("SELECT * FROM $dbtables[chiefs] WHERE username = '$username'");
	$vars = $sessvars->fields;

	$_SESSION['clanid'] = $vars[clanid];
	$db->Execute("ALTER TABLE $dbtables[mapping] "
                    ."ADD `clanid_$_SESSION[clanid]` smallint(2) DEFAULT '0' NOT NULL");
	$_SESSION['chiefname'] = $vars[chiefname];
	$_SESSION['current_unit'] = $vars[current_unit];
	if ($vars[theme]<>"")
	{
		$_SESSION[theme] = $vars[theme];
	}
	else
	{
		$_SESSION[theme] = $theme_default;
	}

	echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=main.php?id=" . $vars[current_unit] . "\">";
	$stamp = date("Y-m-d H:i:s");
	$gm = $db->Execute("SELECT * FROM $dbtables[game_date] WHERE type = 'month'");
	$month = $gm->fields;
	$gy = $db->Execute("SELECT * FROM $dbtables[game_date] WHERE type = 'year'");
	$year = $gy->fields;
	$gd = $db->Execute("SELECT * FROM $dbtables[game_date] WHERE type = 'day'");
	$day = $gd->fields;
	$time = time();
	$db->Execute("UPDATE $dbtables[chiefs] set ipaddr = '$ip', lastseen_month = $month[count], lastseen_year =  $year[count], hour = $time where username = '$username'");
}
elseif($server_closed == 'true')
{
	$sessvars = $db->Execute("SELECT * FROM $dbtables[chiefs] WHERE username = '$username'");
	$vars = $sessvars->fields;
	if($playerinfo['password'] == $md5password & $playerinfo[admin] > 1)
	{
		$_SESSION['clanid'] = $vars[clanid];
		$_SESSION['chiefname'] = $vars[chiefname];
		$_SESSION['current_unit'] = $vars[current_unit];
                $_SESSION['tooltip'] = $vars[tooltip];
		echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=main.php?id=" . $vars[current_unit] . "\">";
		$stamp = date("Y-m-d H:i:s");
		$gm = $db->Execute("SELECT * FROM $dbtables[game_date] WHERE type = 'month'");
		$month = $gm->fields;
		$gy = $db->Execute("SELECT * FROM $dbtables[game_date] WHERE type = 'year'");
		$year = $gy->fields;
		$gd = $db->Execute("SELECT * FROM $dbtables[game_date] WHERE type = 'day'");
		$day = $gd->fields;
	}
	else
	{
		$title = "Server Closed";
		bigtitle();
		echo "Please stand by. We're doing something just now. Shouldn't be a moment.<BR>";
		echo "If you want, you can hang out and chat <A HREF=$link_forums>here</A>.<BR>";
		TEXT_GOTOLOGIN();
	}
}
else
{
	$title="Login Failed";
	bigtitle();
	echo "Username or Password incorrect. Click <a href=index.php>here</a> to try again.<br>Or click <a href=new.php>here</a> if you are a new chief <p><br>";
}


page_footer();

?>
