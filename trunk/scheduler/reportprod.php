<?php
error_reporting  (E_ALL);
$pos = (strpos($_SERVER['PHP_SELF'], "/reportprod.php"));
if ($pos !== false)
{
    die("You cannot access this page directly!");
}

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


?>
