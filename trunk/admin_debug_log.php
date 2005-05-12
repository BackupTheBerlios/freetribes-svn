<?
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: convert.php

session_start();
header("Cache-control: private");

include("config.php");

page_header("Admin DB - Debug Log");

connectdb();

$username = $_SESSION['username'];
$admin = $db->Execute("SELECT * FROM $dbtables[chiefs] WHERE username = '$username'");
$admininfo = $admin->fields;


$module = $_REQUEST[menu];

if(!$admininfo[admin] >= $privilege['adm_tables']){
echo "You must be an administrator to use this tool.<BR>\n";
page_footer();
}
 echo "<table border=1 width='100%'><tr>";
 echo "<th> TIME</th><th>TYPE</th><th>DATA</th></tr>";
$in_array = "('DBERROR','DEBUG','')";
$sql = "SELECT * from $dbtables[logs] WHERE type in $in_array";
$logs = $db->Execute($sql);
db_op_result($logs,__LINE__,__FILE__);
while(!$logs->EOF)
{
   $row = $logs->fields;
   echo "<tr><td>$row[time]</td><td>$row[type]</td><td width='400'>$row[data]</td></tr>";
   $logs->MoveNext();
}
echo "</table><br><a href='admin.php'>RETURN TO ADMIN</a><br>";


page_footer();
?>
