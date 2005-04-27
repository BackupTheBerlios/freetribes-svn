<?php
require_once("../config.php");
$time_start = getmicrotime();
include("game_time.php");
connectdb();
$res = $db->Execute("SELECT * FROM $dbtables[tribes]");
 db_op_result($res,__LINE__,__FILE__);
while( !$res->EOF )
{
    $tribe = $res->fields;
    $goods = $db->Execute("SELECT * FROM $dbtables[tribes] "
                         ."WHERE tribeid = '$tribe[goods_tribe]'");
       db_op_result($goods,__LINE__,__FILE__);
    $gtinfo = $goods->fields;
    if( !$gtinfo[hex_id] == $tribe[hex_id] )
    {
        $result = $db->Execute("UPDATE $dbtables[tribes] "
                    ."SET goods_tribe = '$tribe[tribeid]' "
                    ."WHERE tribeid = '$tribe[tribeid]'");
         db_op_result($result,__LINE__,__FILE__);
    }
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
