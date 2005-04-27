<?php
require_once("../config.php");
$time_start = getmicrotime();
include("game_time.php");
connectdb();
$res = $db->Execute("SELECT * FROM $dbtables[products_used]");
 db_op_result($res,__LINE__,__FILE__);
while( !$res->EOF )
{
    $prodinfo = $res->fields;
    $query = $db->Execute("UPDATE $dbtables[products] "
                ."SET amount = amount + '$prodinfo[amount]' "
                ."WHERE tribeid = '$prodinfo[tribeid]' "
                ."AND long_name = '$prodinfo[long_name]'");
     db_op_result($query,__LINE__,__FILE__);

    if( ISSET( $game_product_debug ) )
    {
        $query = $db->Execute("INSERT INTO $dbtables[logs] "
                    ."VALUES("
                    ."'',"
                    ."'$month[count]',"
                    ."'$year[count]',"
                    ."'0000',"
                    ."'0000.00',"
                    ."'GIVEBACK',"
                    ."'$stamp',"
                    ."'Product Giveback: $prodinfo[tribeid] used $prodinfo[amount] "
                    ."$prodinfo[long_name]"
                    ." which have now been returned.')");
          db_op_result($query,__LINE__,__FILE__);
    }


    $res->MoveNext();
    if( $res->EOF )
    {
        $query = $db->Execute("DELETE FROM $dbtables[products_used]");
           db_op_result($query,__LINE__,__FILE__);
    }

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
