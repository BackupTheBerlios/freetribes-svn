<?php
$pos = (strpos($_SERVER['PHP_SELF'], "/garexp.php"));
if ($pos !== false)
{
    die("You cannot access this page directly!");
}
require_once("config.php");
$time_start = getmicrotime();
include("scheduler/game_time.php");
connectdb();
$res = $db->Execute("SELECT * FROM $dbtables[tribes]");
  db_op_result($res,__LINE__,__FILE__);
while( !$res->EOF )
{
    $tribe = $res->fields;
    $result = $db->Execute("UPDATE $dbtables[garrisons] "
                ."SET experience = experience + 1 "
                ."WHERE tribeid = '$tribe[tribeid]'");
      db_op_result($result,__LINE__,__FILE__);
    $exp = $db->Execute("SELECT experience FROM $dbtables[garrisons] "
                       ."WHERE tribeid = '$tribe[tribeid]'");
        db_op_result($exp,__LINE__,__FILE__);
    while( !$exp->EOF )
    {
        $expts = $exp->fields;
        if( $expts['experience'] == 6 )
        {
            $result = $db->Execute("UPDATE $dbtables[garrisons] SET exp = (exp + .01) WHERE garid = '$exp[garid]'");
              db_op_result($result,__LINE__,__FILE__);
        }
        elseif( $expts['experience'] == 12 )
        {
            $result = $db->Execute("UPDATE $dbtables[garrisons] "
                        ."SET exp = (exp + .01)  "
                        ."WHERE garid = '$exp[garid]'");
             db_op_result($result,__LINE__,__FILE__);
        }
        elseif( $expts['experience'] == 24 )
        {
            $result = $db->Execute("UPDATE $dbtables[garrisons] "
                        ."SET exp = (exp + .01)  "
                        ."WHERE garid = '$exp[garid]'");
              db_op_result($result,__LINE__,__FILE__);
        }
        elseif( $expts['experience'] == 48 )
        {
            $result = $db->Execute("UPDATE $dbtables[garrisons] "
                        ."SET exp = (exp + .01)  "
                        ."WHERE garid = '$exp[garid]'");
             db_op_result($result,__LINE__,__FILE__);
        }
        elseif( $expts['experience'] == 78 )
        {
            $result = $db->Execute("UPDATE $dbtables[garrisons] "
                        ."SET exp = (exp + .01)  "
                        ."WHERE garid = '$exp[garid]'");
            db_op_result($result,__LINE__,__FILE__);
        }
        elseif( $expts['experience'] == 100 )
        {
            $result = $db->Execute("UPDATE $dbtables[garrisons] "
                        ."SET exp = (exp + .01)  "
                        ."WHERE garid = '$exp[garid]'");
            db_op_result($result,__LINE__,__FILE__);
        }
        $exp->MoveNext();
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
