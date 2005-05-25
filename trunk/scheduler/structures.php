<?php
$pos = (strpos($_SERVER['PHP_SELF'], "/structures.php"));
if ($pos !== false)
{
    die("You cannot access this page directly!");
}

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
        if( !$structinfo['hex_id'] == $tribe['hex_id'] )
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

?>
