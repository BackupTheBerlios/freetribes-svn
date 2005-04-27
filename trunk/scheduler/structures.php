<?php
require_once("../config.php");
$time_start = getmicrotime();
connectdb();
$res = $db->Execute("SELECT * FROM $dbtables[tribes]");
db_op_result($res,__LINE__,__FILE__);
while( !$res->EOF )
{
    $tribe = $res->fields;
    $struct = $db->Execute("SELECT * FROM $dbtables[structures] "
                          ."WHERE tribeid = '$tribe[tribeid]'");
      db_op_result($struct,__LINE__,__FILE__);
    while( !$struct->EOF )
    {
        $structinfo = $struct->fields;
        if( !$structinfo[hex_id] == $tribe[hex_id] )
        {
            $abandon = rand(1,100);
            if( $abandon < 21 | $structinfo[long_name] == 'tradepost' )
            {
                $query = $db->Execute("DELETE FROM $dbtables[structures] "
                            ."WHERE long_name = '$structinfo[long_name]' "
                            ."AND tribeid = '$tribe[tribeid]' "
                            ."AND hex_id = '$structinfo[hex_id]' "
                            ."AND struct_id = '$structinfo[struct_id]'");
                 db_op_result($query,__LINE__,__FILE__);
            }
        }
        $struct->MoveNext();
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
