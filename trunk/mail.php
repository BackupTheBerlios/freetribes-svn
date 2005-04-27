<?
session_start();
header("Cache-control: private");

	include("config.php");

if(!$_SESSION['password'])
{
	echo "There has been some sort of error, please report this, unless you are just screwing around.";
	die();
}

page_header($l_mail_title);

  connectdb();

	$result = $db->Execute ("select email, password from $dbtables[chiefs] where email='$mail'");

	if(!$result->EOF) {
	$playerinfo=$result->fields;
	$l_mail_message=str_replace("[pass]",$playerinfo[password],$l_mail_message);
	mail("$mail", "$l_mail_topic", "$l_mail_message\r\n\r\nhttp://$SERVER_NAME","From: $admin_mail\r\nReply-To: $admin_mail\r\nX-Mailer: PHP/" . phpversion());
	echo "$l_mail_sent $mail.";
        } else {
                echo "<b>$l_mail_noplayer</b><br>";
        }

page_footer();
?>

