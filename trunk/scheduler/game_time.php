<?php

error_reporting  (E_ALL);
$pos = (strpos($_SERVER['PHP_SELF'], "/mysqlt-common.php"));
if ($pos !== false)
{
    die("You cannot access this page directly!");
}
require_once("config.php");
connectdb();
/////////////////////////////////Establish Game Time//////////////////////////////////////////

  $gy = $db->Execute("SELECT * FROM $dbtables[game_date] WHERE type = 'year'");
  db_op_result($gy,__LINE__,__FILE__);
  $year = $gy->fields;
  $gm = $db->Execute("SELECT * FROM $dbtables[game_date] WHERE type = 'month'");
  db_op_result($gm,__LINE__,__FILE__);
  $month = $gm->fields;
  $gd = $db->Execute("SELECT * FROM $dbtables[game_date] WHERE type = 'day'");
  db_op_result($gd,__LINE__,__FILE__);
  $day = $gd->fields;
$stamp = date("Y-m-d H:i:s");
$turn_over = 23;

?>
