<?php
session_start();
header("Cache-control: private");

$admincheck = $_SESSION['username'];
//here we should query the db to get admin's username or id or some such to auth
if($admincheck !== 'admin')
{
die("You Do Not Have permissions to view this page!");
}
include("config.php");


page_header("SQL Report");

$perf =& NewPerfMonitor($db);
$perf->UI($pollsecs=5);
page_footer();
?>
