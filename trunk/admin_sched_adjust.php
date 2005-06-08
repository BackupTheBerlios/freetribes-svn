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

page_header("Admin DB - Database Conversion");

connectdb();

$username = $_SESSION['username'];
$admin = $db->Execute("SELECT * FROM $dbtables[chiefs] WHERE username = '$username'");
$admininfo = $admin->fields;


$module = $_POST['menu'];

if(!$admininfo['admin'] >= $privilege['adm_tables'])
{
echo "You must be an administrator to use this tool.<BR>\n";
page_footer();
}

echo "\nAdjust the scheduler Settings as required\n";
echo "<br><br>\n";
echo "<table border=1><tr><th>File</th><th>Last Run</th><th>Type(D=Daily)</th><th>Interval</th></tr>\n";
$res =$db->Execute("SELECT * from $dbtables[scheduler] ORDER BY type desc");
db_op_result($res,__LINE__,__FILE__);
while(!$res->EOF)
{
  $data = $res->fields;
  echo "<tr><td>$data[script]</td><td>$data[last_run]</td><td>$data[type]</td><td>$data[frequency]</td></tr>\n";
  $res->MoveNext();
}
echo "</table>\n";
if(!empty($_POST['set']))
{
    $type = $_POST['type'];
    $time = $_POST['time'];
    $interval = $_POST['interval'];
    $intval = $_POST['intval'];
    $first_part = date("Y-m-d");
    $seond_part = $time.":00";
    $lastrun = $first_part." ".$second_part;
    if($intval == "H")
    {
        $interval = $interval*60;
    }

    $query = $db->Prepare("UPDATE $dbtables[scheduler] SET last_run = ?,frequency=? where type=?");
    $finis = $db->Execute($query,array($lastrun,$interval,$type));
    db_op_result($finis,__LINE__,__FILE__);

}

echo "<br><form method='post'>";
echo"Set Last Run Time to:<select name='time'>\n";
$i=23;
while($i >= 0)
{
   echo "<option value='{$i}:00'>{$i}:00</option>\n";
   $i--;
}
echo "</select> Hours<br>\n";
echo "Set Interval to: <input type='text' name='interval' value='' size='3' maxlength='3'>\n";
echo "<select name='intval'><option value='H'>Hours</option><option value='M'>Minutes</option></select>\n";
echo "<br>WHERE TYPE == :<select name='type'><option value='D'>Daily</option><option value='H'>Hourly</option></select>\n";
echo "<br><br><input type='submit' name='set' value='RESET SCHEDULER'><br>\n";


page_footer();

?>
