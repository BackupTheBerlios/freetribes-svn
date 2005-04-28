<?php
require_once("../config.php"); //we dont need THESE do we? this stuff is already included in the calling file.. but oh well
$time_start = getmicrotime();
include("game_time.php");
connectdb();
$tattletale = false;
$res = $db->Execute("SELECT * FROM $dbtables[tribes]");
  db_op_result($res,__LINE__,__FILE__);
while( !$res->EOF )
{
    $tribe = $res->fields;
    $check = $db->Execute("SELECT * FROM $dbtables[products] WHERE amount < 0 and tribeid = '$tribe[tribeid]'");
      db_op_result($check,__LINE__,__FILE__);
    if( !$check->EOF )
    {
        $query = $db->Execute("INSERT INTO $dbtables[logs] "
                    ."VALUES("
                    ."'',"
                    ."'$month[count]',"
                    ."'$year[count]',"
                    ."'$tribe[clanid]',"
                    ."'$tribe[tribeid]',"
                    ."'REPORT',"
                    ."'$stamp',"
                    ."'$tribe[tribeid] has been detected with a negative number in its inventory!')");
            db_op_result($query,__LINE__,__FILE__);
        $tattletale = true;
    }
    $check = array();
    $res->MoveNext();
}

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
