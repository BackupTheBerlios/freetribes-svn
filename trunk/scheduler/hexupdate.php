<?php
$pos = (strpos($_SERVER['PHP_SELF'], "/mysqlt-common.php"));
if ($pos !== false)
{
    die("You cannot access this page directly!");
}
require_once("config.php");
$time_start = getmicrotime();
include("scheduler/game_time.php");
connectdb();


// Prairie max = 30,000
// Grassy hills max = 35,000
// Decid Forest max = 40,000
// Decid Hills max = 45,000
// Conif Forest max = 35,000
// Conif Hills max = 40,000
// Low Conf Mnts = 25,000
// Jungle = 55,000
// Jungle Hills = 60,000
// Low Jungle Mnts = 50,000
// Swamps = 15,000
// High Snwy Mnts = 2,000
// Tundra = 10,000
// Desert = 1,000

$query = $db->Execute("UPDATE $dbtables[hexes] "
            ."SET game = 1000 "
            ."WHERE game < 10");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[hexes] "
            ."SET game = game * 1.035 "
            ."WHERE game < 30000 "
            ."AND terrain = 'pr'");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[hexes] "
            ."SET game = game * 1.04 "
            ."WHERE game < 35000 "
            ."AND terrain = 'gh'");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[hexes] "
            ."SET game = game * 1.045 "
            ."WHERE game < 40000 "
            ."AND terrain = 'df'");
db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[hexes] "
            ."SET game = game * 1.0455 "
            ."WHERE game < 45000 "
            ."AND terrain = 'dh'");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[hexes] "
            ."SET game = game * 1.039 "
            ."WHERE game < 35000 "
            ."AND terrain = 'cf'");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[hexes] "
            ."SET game = game * 1.035 "
            ."WHERE game < 40000 "
            ."AND terrain = 'ch'");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[hexes] "
            ."SET game = game * 1.019 "
            ."WHERE game < 25000 "
            ."AND terrain = 'lcm'");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[hexes] "
            ."SET game = game * 1.06 "
            ."WHERE game < 55000 "
            ."AND terrain = 'jg'");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[hexes] "
            ."SET game = game * 1.065 "
            ."WHERE game < 60000 "
            ."AND terrain = 'jh'");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[hexes] "
            ."SET game = game * 1.05 "
            ."WHERE game < 50000 "
            ."AND terrain = 'ljm'");
db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[hexes] "
            ."SET game = game * 1.015 "
            ."WHERE game < 15000 "
            ."AND terrain = 'sw'");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[hexes] "
            ."SET game = game * 1.01 "
            ."WHERE game < 2000 "
            ."AND terrain = 'hsm'");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[hexes] "
            ."SET game = game * 1.005 "
            ."WHERE game < 10000 "
            ."AND terrain = 'tu'");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[hexes] "
            ."SET game = game * 1.005 "
            ."WHERE game < 1000 "
            ."AND terrain = 'de'");
  db_op_result($query,__LINE__,__FILE__);




$time_end = getmicrotime();
$time = $time_end - $time_start;
$page_name =   str_replace($game_root."scheduler/",'',__FILE__);// get the name of the file being viewed
$res = $db->Execute("INSERT INTO $dbtables[logs] "
            ."VALUES("
            ."'',"
            ."'$month[count]',"
            ."'$year[count]',"
            ."'0000',"
            ."'0000.00',"
            ."'BENCHMARK',"
            ."'$stamp',"
            ."'$page_name completed in $time seconds.')");
    db_op_result($res,__LINE__,__FILE__);
?>