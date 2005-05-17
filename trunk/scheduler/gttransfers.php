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
$result = $db->Execute("SELECT * FROM $dbtables[tribes] "
                      ."WHERE tribeid <> goods_tribe");
         db_op_result($result,__LINE__,__FILE__);
while( !$result->EOF )
{
    $tribe = $result->fields;
    $res = $db->Execute("SELECT * FROM $dbtables[structures] "
                       ."WHERE tribeid = '$tribe[tribeid]'");
      db_op_result($res,__LINE__,__FILE__);
    while ( !$res->EOF )
    {
        $structinfo = $res->fields;
        $query = $db->Execute("UPDATE $dbtables[structures] "
                    ."SET tribeid = '$tribe[goods_tribe]' "
                    ."WHERE tribeid = '$tribe[tribeid]' "
                    ."AND hex_id = '$tribe[hex_id]'");
          db_op_result($query,__LINE__,__FILE__);
        $res->MoveNext();
    }


    $res = $db->Execute("SELECT * FROM $dbtables[resources] "
                       ."WHERE tribeid = '$tribe[tribeid]' "
                       ."AND amount > 0");
        db_op_result($res,__LINE__,__FILE__);
    while( !$res->EOF )
    {
        $resinfo = $res->fields;
        $query = $db->Execute("UPDATE $dbtables[resources] "
                    ."SET amount = amount + $resinfo[amount] "
                    ."WHERE tribeid = '$tribe[goods_tribe]' "
                    ."AND long_name = '$resinfo[long_name]'");
            db_op_result($query,__LINE__,__FILE__);

        $query = $db->Execute("UPDATE $dbtables[resources] "
                    ."SET amount = amount - $resinfo[amount] "
                    ."WHERE tribeid = '$tribe[tribeid]' "
                    ."AND long_name = '$resinfo[long_name]'");
           db_op_result($query,__LINE__,__FILE__);
        $res->MoveNext();
    }

    $prod = $db->Execute("SELECT * FROM $dbtables[products] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND amount > 0 "
                        ."AND long_name != 'totem'");
       db_op_result($prod,__LINE__,__FILE__);
    while( !$prod->EOF )
    {
        $prodinfo = $prod->fields;
        $query = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount + $prodinfo[amount] "
                    ."WHERE tribeid = '$tribe[goods_tribe]' "
                    ."AND long_name = '$prodinfo[long_name]' "
                    ."AND long_name != 'totem'");
          db_op_result($query,__LINE__,__FILE__);
        $query = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount - $prodinfo[amount] "
                    ."WHERE tribeid = '$tribe[tribeid]' "
                    ."AND long_name = '$prodinfo[long_name]' "
                    ."AND long_name != 'totem'");
           db_op_result($query,__LINE__,__FILE__);
        $prod->MoveNext();
    }

    $liv = $db->Execute("SELECT * FROM $dbtables[livestock] "
                       ."WHERE tribeid = '$tribe[tribeid]' "
                       ."AND amount > 0");
      db_op_result($liv,__LINE__,__FILE__);
    while( !$liv->EOF )
    {
        $livinfo = $liv->fields;
        $query = $db->Execute("UPDATE $dbtables[livestock] "
                    ."SET amount = amount + $livinfo[amount] "
                    ."WHERE tribeid = '$tribe[goods_tribe]' "
                    ."AND type = '$livinfo[type]'");
         db_op_result($query,__LINE__,__FILE__);
        $query = $db->Execute("UPDATE $dbtables[livestock] "
                    ."SET amount = amount - '$livinfo[amount]' "
                    ."WHERE tribeid = '$tribe[tribeid]' "
                    ."AND type = '$livinfo[type]'");
          db_op_result($query,__LINE__,__FILE__);
        $liv->MoveNext();
    }

    $result->MoveNext();
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
