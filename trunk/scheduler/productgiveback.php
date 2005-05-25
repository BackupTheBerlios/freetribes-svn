<?php
$pos = (strpos($_SERVER['PHP_SELF'], "/productgiveback.php"));
if ($pos !== false)
{
    die("You cannot access this page directly!");
}

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

?>
