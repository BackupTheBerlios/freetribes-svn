<?php
$pos = (strpos($_SERVER['PHP_SELF'], "/cleanup.php"));
if ($pos !== false)
{
    die("You cannot access this page directly!");
}
require_once("config.php");
$time_start = getmicrotime();
include("scheduler/game_time.php");
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
    $query = $db->Execute("DELETE FROM $dbtables[livestock] "
                ."WHERE tribeid = '$tribe[tribeid]'");
       db_op_result($query,__LINE__,__FILE__);
    $query = $db->Execute("DELETE FROM $dbtables[products] "
                ."WHERE tribeid = '$tribe[tribeid]'");
       db_op_result($query,__LINE__,__FILE__);
    $query = $db->Execute("DELETE FROM $dbtables[resources] "
                ."WHERE tribeid = '$tribe[tribeid]'");
      db_op_result($query,__LINE__,__FILE__);
    $query = $db->Execute("DELETE FROM $dbtables[scouts] "
                ."WHERE tribeid = '$tribe[tribeid]'");
      db_op_result($query,__LINE__,__FILE__);
    $query = $db->Execute("DELETE FROM $dbtables[seeking] "
                ."WHERE tribeid = '$tribe[tribeid]'");
        db_op_result($query,__LINE__,__FILE__);
    $query = $db->Execute("DELETE FROM $dbtables[activities] "
                ."WHERE tribeid = '$tribe[tribeid]'");
         db_op_result($query,__LINE__,__FILE__);
    $query = $db->Execute("DELETE FROM $dbtables[farm_activities] "
                ."WHERE tribeid = '$tribe[tribeid]'");
        db_op_result($query,__LINE__,__FILE__);
    $query = $db->Execute("DELETE FROM $dbtables[fair_tribe] "
                ."WHERE tribeid = '$tribe[tribeid]'");
        db_op_result($query,__LINE__,__FILE__);
    $query = $db->Execute("DELETE FROM $dbtables[garrisons] "
                ."WHERE tribeid = '$tribe[tribeid]'");
         db_op_result($query,__LINE__,__FILE__);
    $query = $db->Execute("DELETE FROM $dbtables[tribes] "
                ."WHERE tribeid = '$tribe[tribeid]'");
          db_op_result($query,__LINE__,__FILE__);
    if( $tribe[tribeid] == $tribe[clanid] )
    {
        $query = $db->Execute("DELETE FROM $dbtables[chiefs] "
                    ."WHERE clanid = '$tribe[clanid]'");
            db_op_result($query,__LINE__,__FILE__);
        $query = $db->Execute("DELETE FROM $dbtables[clans] "
                    ."WHERE clanid = '$tribe[clanid]'");
            db_op_result($query,__LINE__,__FILE__);
        $query = $db->Execute("DELETE FROM $dbtables[religions] "
                    ."WHERE clanid = '$tribe[clanid]'");
           db_op_result($query,__LINE__,__FILE__);
//Below is a cleanup function to help un-clutter the admin logs - they should be more sortable
//and less verbose.....
        $query = $db->Execute("DELETE FROM $dbtables[logs] "
                    ."WHERE clanid = '0000' and type in ('HOURLYTICK','BENCHMARK','UPDATE') and time < date_sub(now(),INTERVAL 2 day)");
           db_op_result($query,__LINE__,__FILE__);
        $query = $db->Execute("ALTER TABLE $dbtables[mapping] "
                    ."DROP `$tribe[clanid]`");
           db_op_result($query,__LINE__,__FILE__);
        $query = $db->Execute("INSERT INTO $dbtables[logs] "
                    ."VALUES("
                    ."'',"
                    ."'$month[count]',"
                    ."'$year[count]',"
                    ."'0000',"
                    ."'0000.00',"
                    ."'CLEANUP',"
                    ."'$stamp',"
                    ."'Clan Cleanup: $tribe[clanid] has been removed.')");
          db_op_result($query,__LINE__,__FILE__);
    }
    $res->MoveNext();
}


$query = $db->Execute("DELETE FROM $dbtables[garrisons] WHERE `force` < 3");
 db_op_result($query,__LINE__,__FILE__);
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
$res = $db->Execute("DELETE FROM $dbtables[poptrans]");
db_op_result($res,__LINE__,__FILE__);
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$res = $db->Execute("DELETE FROM $dbtables[activities] WHERE skill_abbr = 'Relax'");
db_op_result($res,__LINE__,__FILE__);
$res = $db->Execute("UPDATE $dbtables[chiefs] SET active = active + 1");
db_op_result($res,__LINE__,__FILE__);
$res = $db->Execute("DELETE FROM $dbtables[map_view]");
db_op_result($res,__LINE__,__FILE__);
$weather = $db->Execute("SELECT * FROM $dbtables[game_date] where type = 'weather'");
db_op_result($weather,__LINE__,__FILE__);
$weatherinfo = $weather->fields;
$res = $db->Execute("UPDATE $dbtables[game_date] set count = 0 WHERE type = 'weather'");
db_op_result($res,__LINE__,__FILE__);
$res = $db->Execute("UPDATE $dbtables[structures] SET used = 'N' WHERE used = 'Y'");
db_op_result($res,__LINE__,__FILE__);
$time_update = $db->Execute("SELECT * FROM $dbtables[game_date] where type='month'");
db_op_result($time_update,__LINE__,__FILE__);
$data = $time_update->fields;
if($data['count'] == 12)
{
   $newmonth = 1;
   $years = $db->Execute("select count from $dbtables[game_date] where type='year'");
   db_op_result($years,__LINE__,__FILE__);
   $yearinfo = $years->fields;
   $newyear = $yearinfo['count'] + 1;
}
else
{
   $newmonth = $data['count'] + 1;
}

$gameupdate = $db->Execute("UPDATE $dbtables[game_date] SET count=1 where type='day'");
db_op_result($gameupdate,__LINE__,__FILE__);
$gameupdate = $db->Execute("UPDATE $dbtables[game_date] SET count=$newmonth where type='month'");
db_op_result($gameupdate,__LINE__,__FILE__);
if($newmonth == 1)
{
    $gameupdate = $db->Execute("UPDATE $dbtables[game_date] SET count=$newyear where type='year'");
    db_op_result($gameupdate,__LINE__,__FILE__);
}

$endtime = time();
$diff_seconds = $endtime - $sched_starttime;
$diff_minutes = floor($diff_seconds/60);
$diff_seconds -= $diff_minutes * 60;

$result = $db->Execute("INSERT INTO $dbtables[logs] "
            ."VALUES("
            ."'',"
            ."'$month[count]',"
            ."'$year[count]',"
            ."'0000',"
            ."'0000',"
            ."'SYSTEMSTAT',"
            ."'$stamp',"
            ."'Update completed in $diff_minutes minutes, "
            ."$diff_seconds seconds, the weather count "
            ."reached $weatherinfo[count]')");

db_op_result($result,__LINE__,__FILE__);


?>
