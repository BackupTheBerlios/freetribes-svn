<?php
require_once("../config.php");
$time_start = getmicrotime();
include("game_time.php");
connectdb();
$res = $db->Execute("SELECT * FROM $dbtables[tribes] "
                   ."WHERE activepop < 1 "
                   ."AND slavepop < 1 "
                   ."AND inactivepop < 1 "
                   ."AND warpop < 1 ");
  db_op_result($res,__LINE__,__FILE__);


while( !$res->EOF )
{
    $tribe = $res->fields;
    $res = $db->Execute("DELETE FROM $dbtables[livestock] "
                ."WHERE tribeid = '$tribe[tribeid]'");
       db_op_result($res,__LINE__,__FILE__);
    $res = $db->Execute("DELETE FROM $dbtables[products] "
                ."WHERE tribeid = '$tribe[tribeid]'");
       db_op_result($res,__LINE__,__FILE__);
    $res = $db->Execute("DELETE FROM $dbtables[resources] "
                ."WHERE tribeid = '$tribe[tribeid]'");
      db_op_result($res,__LINE__,__FILE__);
    $res = $db->Execute("DELETE FROM $dbtables[scouts] "
                ."WHERE tribeid = '$tribe[tribeid]'");
      db_op_result($res,__LINE__,__FILE__);
    $res = $db->Execute("DELETE FROM $dbtables[seeking] "
                ."WHERE tribeid = '$tribe[tribeid]'");
        db_op_result($res,__LINE__,__FILE__);
    $res = $db->Execute("DELETE FROM $dbtables[activities] "
                ."WHERE tribeid = '$tribe[tribeid]'");
         db_op_result($res,__LINE__,__FILE__);
    $res = $db->Execute("DELETE FROM $dbtables[farm_activities] "
                ."WHERE tribeid = '$tribe[tribeid]'");
        db_op_result($res,__LINE__,__FILE__);
    $res = $db->Execute("DELETE FROM $dbtables[fair_tribe] "
                ."WHERE tribeid = '$tribe[tribeid]'");
        db_op_result($res,__LINE__,__FILE__);
    $res = $db->Execute("DELETE FROM $dbtables[garrisons] "
                ."WHERE tribeid = '$tribe[tribeid]'");
         db_op_result($res,__LINE__,__FILE__);
    $res = $db->Execute("DELETE FROM $dbtables[tribes] "
                ."WHERE tribeid = '$tribe[tribeid]'");
          db_op_result($res,__LINE__,__FILE__);
    if( $tribe[tribeid] == $tribe[clanid] )
    {
        $res = $db->Execute("DELETE FROM $dbtables[chiefs] "
                    ."WHERE clanid = '$tribe[clanid]'");
            db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("DELETE FROM $dbtables[clans] "
                    ."WHERE clanid = '$tribe[clanid]'");
            db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("DELETE FROM $dbtables[religions] "
                    ."WHERE clanid = '$tribe[clanid]'");
           db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("ALTER TABLE $dbtables[mapping] "
                    ."DROP `$tribe[clanid]`");
           db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("INSERT INTO $dbtables[logs] "
                    ."VALUES("
                    ."'',"
                    ."'$month[count]',"
                    ."'$year[count]',"
                    ."'0000',"
                    ."'0000.00',"
                    ."'CLEANUP',"
                    ."'$stamp',"
                    ."'Clan Cleanup: $tribe[clanid] has been removed.')");
          db_op_result($res,__LINE__,__FILE__);
    }
    $res->MoveNext();
}


$res = $db->Execute("DELETE FROM $dbtables[garrisons] WHERE `force` < 3");
 db_op_result($res,__LINE__,__FILE__);
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
