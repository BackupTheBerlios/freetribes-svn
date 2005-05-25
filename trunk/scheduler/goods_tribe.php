<?php
$pos = (strpos($_SERVER['PHP_SELF'], "/goods_tribe.php"));
if ($pos !== false)
{
    die("You cannot access this page directly!");
}

$res = $db->Execute("SELECT * FROM $dbtables[tribes]");
 db_op_result($res,__LINE__,__FILE__);
while( !$res->EOF )
{
    $tribe = $res->fields;
    $goods = $db->Execute("SELECT * FROM $dbtables[tribes] "
                         ."WHERE tribeid = '$tribe[goods_tribe]'");
       db_op_result($goods,__LINE__,__FILE__);
    $gtinfo = $goods->fields;
    if( !$gtinfo['hex_id'] == $tribe['hex_id'] )
    {
        $result = $db->Execute("UPDATE $dbtables[tribes] "
                    ."SET goods_tribe = '$tribe[tribeid]' "
                    ."WHERE tribeid = '$tribe[tribeid]'");
         db_op_result($result,__LINE__,__FILE__);
    }
    $res->MoveNext();
}

?>
