<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: convert.php

session_start();
header("Cache-control: private");

include("config.php");

page_header("Admin News ");

connectdb();

$username = $_SESSION['username'];
$admin = $db->Execute("SELECT * FROM $dbtables[chiefs] WHERE username = '$username'");
$admininfo = $admin->fields;


$module = $_REQUEST[menu];

if(!$admininfo[admin] >= $privilege['adm_tables'])
{
echo "You must be an administrator to use this tool.<BR>\n";
page_footer();
}

 if(!empty($_POST['submitnews']))
 {
      //days, headline,article
     $input = array((int)$_POST['days'],$_POST['headline'],$_POST['article']);
     $sql = $db->Prepare("insert into $dbtables[news] (created,expire,headline,news) VALUES (now(),date_add(now(),interval ? day),?,?)");
     $res = $db->Execute($sql,$input);
     db_op_result($res,__LINE__,__FILE__);
 }
 if(!empty($_POST['delete']))
 {
     $id = $_POST['newsid'];
     $sql = $db->Prepare("DELETE FROM $dbtables[news] WHERE id=?");
     $res = $db->Execute($sql,array($id));
     db_op_result($res,__LINE__,__FILE__);
 }
 echo "<div align='left'><br><form action='' method='post'>EXPIRES IN: <input type='text' name='days' value='' size=2 maxlength=2> Days<br><br>";
 echo "Headline:<input type='text' name='headline' value='' size='60'><br><br>Article:<textarea name='article' rows=5 cols=50></textarea><br>";
 echo "<input type='submit' name='submitnews' value='Submit News'></form>";
 echo "</div>";
 echo "<table border=1 width='100%'><tr>";
 echo "<th> Delete</th><th>Created</th><th>Expires</th><th>Article</th></tr>";

$sql = "SELECT * from $dbtables[news] order by expire";
$logs = $db->Execute($sql);
db_op_result($logs,__LINE__,__FILE__);
while(!$logs->EOF)
{
   $row = $logs->fields;
   echo "<tr><td>";
   echo "<form action='' method='post'><input type='hidden' name='newsid' value='$row[id]'><input type='submit' name='delete' value='Delete'></form>";
   echo "</td><td>$row[created]</td><td>$row[expire]</td><td width='400'><strong>$row[headline]</strong><br>$row[news]</td></tr>";
   $logs->MoveNext();
}
echo "</table><br><a href='admin.php'>RETURN TO ADMIN</a><br>";


page_footer();
?>
