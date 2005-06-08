<?php

require_once("config.php");
$time_start = getmicrotime();
connectdb();
$perf =& NewPerfMonitor($db);
$perf->UI($pollsecs=5);

?>
