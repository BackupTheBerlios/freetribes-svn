<?
session_start();
header("Cache-control: private");

include("config.php");
connectdb();

page_header("Account Options Review");

//-------------------------------------------------------------------------------------------------
$oldpass     = preg_replace ("/[^\w\s\']/","",$_REQUEST['oldpass']);

$newpass     = preg_replace ("/[^\w\s\']/","",$_REQUEST['newpass1']);
$newpass     = addslashes($_REQUEST['newpass1']);
$newpass_md5 = md5($newpass);

$newpass2    = preg_replace ("/[^\w\s\']/","",$_REQUEST['newpass2']);
$newpass2    = addslashes($_REQUEST['newpass2']);

$clanname  = preg_replace ("/[^\w\s\']/","",$_REQUEST['clanname']);
$clanname  = htmlspecialchars($clanname);

$tribename = preg_replace ("/[^\w\s\']/","",$_REQUEST['tribename']);
$tribename = htmlspecialchars($tribename);

//$minimap = $_POST['minimap'];

$delete = $_REQUEST['delete'];


$theme = $_REQUEST['theme'];

	echo "<P>";

if	(
		(	($oldpass<>"" || $newpass<>"" || $newpass2<>"")
		&&	($oldpass=="" || $newpass=="" || $newpass2=="")
		)
		&&	$delete<>"1"
	)
{
	echo "To change your password you must fill in all three password fields.<BR>";
	page_footer();
}
elseif( $oldpass<>"" && $delete<>"1")
{
    if( $newpass == $newpass2 && $_SESSION['password'] == $oldpass)
    {
		echo "Changing password<br>";
        $username = $_SESSION['username'];

        $db->Execute("UPDATE $dbtables[chiefs] "
                    ."SET password = '$newpass_md5' WHERE username = '$username'");

        $_SESSION['password'] = $newpass;

        $clan = $db->Execute("SELECT * FROM $dbtables[chiefs] "
                            ."WHERE clanid = '$_SESSION[clanid]'");
        $claninfo = $clan->fields;

        $email = $claninfo[email];
        $l_new_message = 'Greetings Chief ';
        $l_new_message .= "$claninfo[chiefname]";
        $l_new_message .= ",\n\n";
        $l_new_message .= 'Someone from IP address '.$ip." \n";
        $l_new_message .= 'requested that your password for TribeStrive be sent to you.';
        $l_new_message .= "\n\n";
        $l_new_message .= 'Your Username is: '.$claninfo['username'];
        $l_new_message .= "\n";
        $l_new_message .= 'Your Password is: '.$newpass;
        $l_new_message .= "\n\n";
        $l_new_message .= 'This will be the only time your password is recoverable, ';
        $l_new_message .= 'so please keep a record of it.';
        $l_new_message .= "\n\n";
        $l_new_message .= 'Thank you';
        $l_new_message .= "\n\n";
        $l_new_message .= "The TribeStrive web team @ ".$game_url.$game_url_path;

        $l_new_topic = 'TribeStrive Password';
/*
		echo "<PRE>".$l_new_message."</pre><P>";
		echo "$l_new_topic<P>";
		echo "$gamedomain<P>";
		echo "$admin_mail<p>";
*/
        mail("$email", "$l_new_topic", "$l_new_message\n\n","From: $admin_mail\nReply-To: $admin_mail\nX-Mailer: PHP/" . phpversion());

        echo '<p>Password Updated.<BR>';
    }
    elseif( !$newpass == "" && !$newpass == $newpass2 )
    {
        echo '<p>Please verify that you typed your desired password twice.';
    }
    elseif( !md5($_SESSION['password']) == $oldpass )
    {
        echo '<p>Please verify your existing password.<BR><BR>';
        echo "\n"; 
    }
    elseif( $newpass == "" && $newpass2 == "" )
    {
        echo '<p>Password unchanged<br>';
        echo "\n";
    }
    else
    {
        echo "<PRE>";
        //print_r($_REQUEST);
        echo "</PRE>";
    }
}
$username = $_SESSION['username'];

if($_REQUEST[tooltip] == '1' )
{
	$db->Execute("UPDATE $dbtables[chiefs] SET tooltip ='1' WHERE username ='$username'");
        $_SESSION[tooltip] = 1;
	echo "<BR>Tooltips set to 'on'.<BR>";
}
else
{
	$res = $db->Execute("UPDATE $dbtables[chiefs] SET tooltip = '0' WHERE username = '$username'");
        $_SESSION[tooltip] = 0;
	echo "<BR>Tooltips set to 'off'.<BR>";
}

if($delete == '1')
{
        if($delete == '1' && !$oldpass == md5($_SESSION[password])){
        echo "Delete Tribe Command Rejected.<BR>";
        echo "<FONT SIZE=+2>You must enter your current password to issue this command.</FONT><BR>";
        page_footer();
        }
        $stamp = date("Y-m-d H:i:s");
        $m = $db->Execute("SELECT * FROM $dbtables[game_date] WHERE type = 'month'");
        $month = $m->fields;
        $y = $db->Execute("SELECT * FROM $dbtables[game_date] WHERE type = 'year'");
        $year = $y->fields;
        $tribe = $db->Execute("SELECT * FROM $dbtables[tribes] WHERE clanid = '$_SESSION[clanid]'");
	$db->Execute("DELETE FROM $dbtables[chiefs] WHERE clanid = '$_SESSION[clanid]'");
        $db->Execute("DELETE FROM $dbtables[clans] WHERE clanid = '$_SESSION[clanid]'");
        $db->Execute("DELETE FROM $dbtables[tribes] WHERE clanid = '$_SESSION[clanid]'");
        $db->Execute("DELETE FROM $dbtables[logs] WHERE clanid = '$_SESSION[clanid]' AND type = 'UPDATE'");
        $db->Execute("DELETE FROM $dbtables[alliances] WHERE offerer_id = '$_SESSION[clanid]' OR receipt_id = '$_SESSION[clanid]'");
        $db->Execute("DELETE FROM $dbtables[fair_tribe] WHERE clan_id = '$_SESSION[clanid]'");
        $db->Execute("DELETE FROM $dbtables[messages] WHERE clanid = '$_SESSION[clanid]'");
        $db->Execute("DELETE FROM $dbtables[outbox] WHERE clanid = '$_SESSION[clanid]'");
        while(!$tribe->EOF){
        $tribeinfo = $tribe->fields;
        $db->Execute("DELETE FROM $dbtables[livestock] WHERE tribeid = '$tribeinfo[tribeid]'");
        $db->Execute("DELETE FROM $dbtables[logs] WHERE tribeid = '$tribeinfo[tribeid]'");
        $db->Execute("DELETE FROM $dbtables[map_table] WHERE tribeid = '$tribeinfo[tribeid]'");
        $db->Execute("DELETE FROM $dbtables[movement_log] WHERE tribeid = '$tribeinfo[tribeid]'");
        $db->Execute("DELETE FROM $dbtables[products] WHERE tribeid = '$tribeinfo[tribeid]'");
        $db->Execute("DELETE FROM $dbtables[resources] WHERE tribeid = '$tribeinfo[tribeid]'");
        $db->Execute("DELETE FROM $dbtables[skills] WHERE tribeid = '$tribeinfo[tribeid]'");
        $db->Execute("DELETE FROM $dbtables[activities] WHERE tribeid = '$tribeinfo[tribeid]'");
        $db->Execute("DELETE FROM $dbtables[structures] WHERE tribeid = '$tribeinfo[tribeid]'");
        $db->Execute("DELETE FROM $dbtables[garrisons] WHERE tribeid = '$tribeinfo[tribeid]'");
        $db->Execute("DELETE FROM $dbtables[scouts] WHERE tribeid = '$tribeinfo[tribeid]'");
        $db->Execute("ALTER TABLE $dbtables[mapping] DROP `clanid_$tribeinfo[clanid]`");
        $tribe->MoveNext();
	}
        $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','0000','0000','DELETE','$stamp','$_SESSION[clanid] has deleted themselves. Command given from $ip ')");
        $_SESSION[delete] = 1;
        echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=logout.php\">";
	}
        

$clanid = $_SESSION['clanid'];
if(!$clanname == '' && $delete<>"1")
{
	$res = $db->Execute("UPDATE $dbtables[clans] SET clanname = '$clanname' WHERE clanid = '$clanid'");
	echo "<p>Clan name changed to $clanname.<BR>";
}
if(!$tribename == '' && $delete<>"1")
{
	$res = $db->Execute("UPDATE $dbtables[tribes] SET tribename = '$tribename' WHERE tribeid = '$_SESSION[current_unit]'");
	echo "<P>Tribe name changed to $tribename.<BR>";
}
if(!$theme == '' && $delete<>"1")
{
	$res = $db->Execute("UPDATE $dbtables[chiefs] SET theme = '$theme' WHERE clanid = '$clanid'");
	echo "<p>Interface theme changed to $theme.<BR>";
	$_SESSION['theme'] = $theme;
}

//-------------------------------------------------------------------------------------------------

page_footer();

?>
