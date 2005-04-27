<?
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: admin.php
session_start();
header("Cache-control: private");
$admincheck = $_SESSION['username'];
//here we should query the db to get admin's username or id or some such to auth
if($admincheck !== 'admin')
{
die("You Do Not	Have permissions to view this page!");
}
include("config.php");

page_header("Game Admin");

connectdb();

$username = $_SESSION['username'];
$admin = $db->Execute("SELECT * FROM $dbtables[chiefs] WHERE username = '$username'");
$admininfo = $admin->fields;

$module = $_POST['menu'];

if(!$admininfo[admin] >= $privilege['adm_access'])
{
	echo "<BR>You must be an administrator to use this tool.<BR>\n";
	page_footer();
}

$md5password = md5($_REQUEST['password']);


  if(!ISSET($_REQUEST[menu]))
  {
    echo "<BR>Welcome to the TribeStrive administration module<BR><BR>"
		."Select a function from the list below:<BR>";
    echo "<FORM ACTION=admin.php METHOD=POST>"
		."<SELECT NAME=menu>";
    if($admininfo[admin] >= $privilege['adm_mapping'])
	{
		echo "<OPTION VALUE=hexedit SELECTED>Edit World</OPTION>"
			."<OPTION VALUE=known>Known World</OPTION>"
			."<OPTION VALUE=bigmap>Entire World</OPTION>";
	}
    if($admininfo[admin] >= $privilege['adm_accounts'])
	{
		echo "<OPTION VALUE=report>Admin Logs</OPTION>"
			."<OPTION VALUE=passreset>Password Reset</OPTION>"
			."<OPTION VALUE=adminmove>Move Tribes</OPTION>"
			."<OPTION VALUE=reimburse>Reimbursements</OPTION>";
	}
    if($admininfo[admin] >= $privilege['adm_sched'])
	{
		echo "<OPTION VALUE=run_turn>Run Turn</OPTION>";
    }
    if($admininfo[admin] >= $privilege['adm_tracking'])
	{
	    echo "<OPTION VALUE=listing>Chief List</OPTION>"
			."<OPTION VALUE=multis>Multiplayer Detection</OPTION>";
	}
    if($admininfo[admin] >= $privilege['adm_dev'])
	{
		echo "<OPTION VALUE=upload>File Upload</OPTION>";
    }
    if($admininfo[admin] >= $privilege['adm_backup'])
	{
		echo "<OPTION VALUE=convert>DB Converter</OPTION>";
    }
    if($admininfo[admin] >= $privilege['adm_reset'])
	{
		echo "<OPTION VALUE=restart>Game Reset</OPTION>";
	}
    echo "</SELECT>";
    echo "<INPUT TYPE=HIDDEN NAME=login VALUE=1>";
    echo "&nbsp;<INPUT TYPE=SUBMIT VALUE=Submit>";
    echo "</FORM>";
  }
    if($_REQUEST[menu] == "passreset"){
    if($_REQUEST[menu] == "passreset" & !ISSET($_REQUEST[resettribe]))
	{
        $tribe = $db->Execute("SELECT * FROM $dbtables[chiefs]");
        echo "<FORM ACTION=admin.php METHOD=POST><SELECT NAME=resettribe>";
        while(!$tribe->EOF)
		{
			$tribes = $tribe->fields;
			echo "<OPTION VALUE=$tribes[clanid]>$tribes[clanid]</OPTION>";
			$tribe->MoveNext();
        }
        echo "</SELECT><INPUT TYPE=HIDDEN NAME=menu VALUE=\"passreset\"><INPUT TYPE=SUBMIT VALUE=\"RESET PASSWD\">";
    }
    else
	{
        $makepass="";
        $syllables="er,in,tia,wol,fe,pre,vet,jo,nes,al,len,son,cha,ir,ler,bo,ok,tio,nar,sim,ple,bla,ten,toe,cho,co,lat,spe,ak,er,po,co,lor,pen,cil,li,ght,wh,at,the,he,ck,is,mam,bo,no,fi,ve,any,way,pol,iti,cs,ra,dio,sou,rce,sea,rch,pa,per,com,bo,sp,eak,st,fi,rst,gr,oup,boy,ea,gle,tr,ail,bi,ble,brb,pri,dee,kay,en,be,se";
        $syllable_array=explode(",", $syllables);
        srand((double)microtime()*1000000);
          for ($count=1;$count<=4;$count++)
		  {
	        if (rand()%10 == 1)
			{
	        $makepass .= sprintf("%0.0f",(rand()%50)+1);
		    }
			else
			{
	        $makepass .= sprintf("%s",$syllable_array[rand()%62]);
		    }
		  }
        $hashed_pass = md5($makepass);
        $db->Execute("UPDATE $dbtables[chiefs] SET password = '$hashed_pass' WHERE clanid = '$_REQUEST[resettribe]'");
        $clan = $db->Execute("SELECT * FROM $dbtables[chiefs] WHERE clanid = '$_REQUEST[resettribe]'");
        $claninfo = $clan->fields;
        $email = $claninfo[email];       
    $l_new_message = "Greetings Chief $claninfo[chiefname],\n\nSomeone from the IP address $ip \nrequested that your password for TribeStrive be sent to you.\n\nYour Username is: [user]\n\nYour password is: [pass]\n\nThank you\n\nThe TribeStrive web team. \n\n";
    $l_new_message = str_replace("[pass]", $makepass, $l_new_message);
    $l_new_message = str_replace("[user]", $claninfo[username], $l_new_message);
    $l_new_topic = "TribeStrive Password";
    mail("$email", "$l_new_topic", "$l_new_message\r\n\r\nhttp://$gamedomain","From: $admin_mail\r\nReply-To: $admin_mail\r\nX-Mailer: PHP/" . phpversion());
    }
    }

	//Admin run turn function
	if($_REQUEST[menu] == "run_turn")
    {
        echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=scheduler/sched_skill.php?force=1&chain=1\">";
    }


    //Admin map functions
	if($_REQUEST[menu] == "hexedit")
    {
        echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=admin_map_edit.php?hex_id=$_SESSION[hex_id]\">";
    }
    if($_REQUEST[menu] == "known")
    {
	    echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=admin_map_known.php\">";
    }
    if($_REQUEST[menu] == "bigmap")
    {
	    echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=admin_map_world.php\">";
    }

    //Admin tribe/account functions
    if($_REQUEST[menu] == 'report')
    {
		echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=admin_tribe_report.php\">";	//probably not really account function, but ...
    }
	if($_REQUEST[menu] == 'adminmove')
    {
	    echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=admin_tribe_move.php\">";
    }
    if($_REQUEST[menu] == 'reimburse')
    {
		echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=admin_tribe_reimburse.php\">";
    }

    //Admin basic table manipulation functions
	if($_REQUEST[menu] == "upload")
    {
	    echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=admin_upload.php\">";
    }
	if($_REQUEST[menu] == "convert")
    {
	    echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=admin_db_convert.php\">";
    }

	//Admin player tracking functions
	if( $_REQUEST[menu] == "listing")
    {
        echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=admin_track_ip.php\">";
    }
    if($_REQUEST[menu] == "multis")
    {
	    echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=admin_track_multi.php\">";
    }

	if($_REQUEST[menu] == "restart")
    {
        echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=install/install_script.php\">"; 
    }
    if($module == "zoneedit")
    {
    }
    if($module == "ipedit")
    {
    }    
    if($module == "logview")
    {
    }

    if($button_main)
    {
      echo "<p>";
      echo "<FORM ACTION=admin.php METHOD=POST>";
      echo "<INPUT TYPE=SUBMIT VALUE=\"Return to main menu\">";
      echo "</FORM>";
    }
  
page_footer();

?> 
