<?php
error_reporting  (E_ALL);
$pos = (strpos($_SERVER['PHP_SELF'], "/reportprod.php"));
if ($pos !== false)
{
    die("You cannot access this page directly!");
}
    //SCHEDULED TO DELETE!!

    $check = $db->Execute("SELECT tribeid,clanid FROM $dbtables[products] WHERE amount < 0 ");
    db_op_result($check,__LINE__,__FILE__);
    while( !$check->EOF )
    {
        $info = $check->fields;
        $message = "Negative Value found in products table for $info[tribeid] at $stamp";
        adminlog('NEGATIVES', $message)
    }
    $check = array();
    $res->MoveNext();


?>
