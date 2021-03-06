<?php
$pos = (strpos($_SERVER['PHP_SELF'], "/cleanup.php"));
if ($pos !== false)
{
    die("You cannot access this page directly!");
}

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
    if( $tribe['tribeid'] == $tribe['clanid'] )
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
                    ."WHERE clanid = '0000' and type in ('HOURLYTICK','BENCHMARK','UPDATE') and time < date_sub(now(),INTERVAL 4 day)");
           db_op_result($query,__LINE__,__FILE__);
        $query = $db->Execute("ALTER TABLE $dbtables[mapping] DROP `clanid_$tribe[clanid]`");
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

$res = $db->Execute("TRUNCATE TABLE $dbtables[poptrans]");
db_op_result($res,__LINE__,__FILE__);
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$res = $db->Execute("DELETE FROM $dbtables[activities] WHERE skill_abbr = 'Relax'");
db_op_result($res,__LINE__,__FILE__);
$res = $db->Execute("UPDATE $dbtables[chiefs] SET active = active + 1");
db_op_result($res,__LINE__,__FILE__);
$res = $db->Execute("TRUNCATE TABLE $dbtables[map_view]");
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

if($month['count'] == '3' || $month['count'] == '4' || $month['count'] == '5')
{
    $season = 1;
    $res = $db->Execute("UPDATE $dbtables[hexes] SET move = 2 WHERE terrain = 'pr' OR terrain = 'tu' OR terrain = 'de'");
    db_op_result($res,__LINE__,__FILE__);
    $res = $db->Execute("UPDATE $dbtables[hexes] SET move = 4 WHERE terrain = 'gh'");
    db_op_result($res,__LINE__,__FILE__);
    $res = $db->Execute("UPDATE $dbtables[hexes] SET move = 5 WHERE terrain = 'df' OR terrain = 'cf'");
    db_op_result($res,__LINE__,__FILE__);
    $res = $db->Execute("UPDATE $dbtables[hexes] SET move = 6 WHERE terrain = 'dh' OR terrain = 'ch' OR terrain = 'sw' OR terrain = 'jg'");
    db_op_result($res,__LINE__,__FILE__);
    $res = $db->Execute("UPDATE $dbtables[hexes] SET move = 7 WHERE terrain = 'jh'");
    db_op_result($res,__LINE__,__FILE__);
    $res = $db->Execute("UPDATE $dbtables[hexes] SET move = 9 WHERE terrain = 'lcm'");
    db_op_result($res,__LINE__,__FILE__);
    $res = $db->Execute("UPDATE $dbtables[hexes] SET move = 10 WHERE terrain = 'ljm'");
    db_op_result($res,__LINE__,__FILE__);
    $res = $db->Execute("UPDATE $dbtables[hexes] SET move = 30 WHERE terrain = 'o' OR terrain = 'l' OR terrain = 'hsm'");
    db_op_result($res,__LINE__,__FILE__);
    $res = $db->Execute("UPDATE $dbtables[hexes] SET resource = 'N' WHERE resource = 'Y' AND res_type = ''");
    db_op_result($res,__LINE__,__FILE__);
    $res = $db->Execute("UPDATE $dbtables[skill_table] SET auto = 'N' WHERE abbr = 'seek'");
    db_op_result($res,__LINE__,__FILE__);
}
elseif($month['count'] == '6' || $month['count'] == '7' || $month['count'] == '8')
{
    $season = 2;
    $res = $db->Execute("UPDATE $dbtables[hexes] SET move = 3 WHERE terrain = 'pr' OR terrain = 'tu' OR terrain = 'de'");
    db_op_result($res,__LINE__,__FILE__);
    $res = $db->Execute("UPDATE $dbtables[hexes] SET move = 5 WHERE terrain = 'gh'");
    db_op_result($res,__LINE__,__FILE__);
    $res = $db->Execute("UPDATE $dbtables[hexes] SET move = 6 WHERE terrain = 'df' OR terrain = 'cf'");
    db_op_result($res,__LINE__,__FILE__);
    $res = $db->Execute("UPDATE $dbtables[hexes] SET move = 7 WHERE terrain = 'dh' OR terrain = 'ch' OR terrain = 'sw' OR terrain = 'jg'");
    db_op_result($res,__LINE__,__FILE__);
    $res = $db->Execute("UPDATE $dbtables[hexes] SET move = 8 WHERE terrain = 'jh'");
    db_op_result($res,__LINE__,__FILE__);
    $res = $db->Execute("UPDATE $dbtables[hexes] SET move = 10 WHERE terrain = 'lcm'");
    db_op_result($res,__LINE__,__FILE__);
    $res = $db->Execute("UPDATE $dbtables[hexes] SET move = 11 WHERE terrain = 'ljm'");
    db_op_result($res,__LINE__,__FILE__);
    $res = $db->Execute("UPDATE $dbtables[hexes] SET move = 30 WHERE terrain = 'o' OR terrain = 'l' OR terrain = 'hsm'");
    db_op_result($res,__LINE__,__FILE__);
    $res = $db->Execute("UPDATE $dbtables[hexes] SET resource = 'N' WHERE resource = 'Y' AND res_type = ''");
    db_op_result($res,__LINE__,__FILE__);
    $res = $db->Execute("UPDATE $dbtables[skill_table] SET auto = 'N' WHERE abbr = 'seek'");
    db_op_result($res,__LINE__,__FILE__);
}
elseif($month['count'] == '9' || $month['count'] == '10' || $month['count'] == '11' )
{
    $season = 3;
    $res = $db->Execute("UPDATE $dbtables[hexes] SET move = 3 WHERE terrain = 'pr' OR terrain = 'tu' OR terrain = 'de'");
    db_op_result($res,__LINE__,__FILE__);
    $res = $db->Execute("UPDATE $dbtables[hexes] SET move = 5 WHERE terrain = 'gh'");
    db_op_result($res,__LINE__,__FILE__);
    $res = $db->Execute("UPDATE $dbtables[hexes] SET move = 6 WHERE terrain = 'df' OR terrain = 'cf'");
    db_op_result($res,__LINE__,__FILE__);
    $res = $db->Execute("UPDATE $dbtables[hexes] SET move = 7 WHERE terrain = 'dh' OR terrain = 'ch' OR terrain = 'sw' OR terrain = 'jg'");
    db_op_result($res,__LINE__,__FILE__);
    $res = $db->Execute("UPDATE $dbtables[hexes] SET move = 8 WHERE terrain = 'jh'");
    db_op_result($res,__LINE__,__FILE__);
    $res = $db->Execute("UPDATE $dbtables[hexes] SET move = 10 WHERE terrain = 'lcm'");
    db_op_result($res,__LINE__,__FILE__);
    $res = $db->Execute("UPDATE $dbtables[hexes] SET move = 11 WHERE terrain = 'ljm'");
    db_op_result($res,__LINE__,__FILE__);
    $res = $db->Execute("UPDATE $dbtables[hexes] SET move = 30 WHERE terrain = 'o' OR terrain = 'l' OR terrain = 'hsm'");
    db_op_result($res,__LINE__,__FILE__);
    $res = $db->Execute("UPDATE $dbtables[hexes] SET resource = 'N' WHERE resource = 'Y' AND res_type = ''");
    db_op_result($res,__LINE__,__FILE__);
    $res = $db->Execute("UPDATE $dbtables[skill_table] SET auto = 'N' WHERE abbr = 'seek'");
    db_op_result($res,__LINE__,__FILE__);
}
elseif($month['count'] == '12' || $month['count'] == '1' || $month['count'] == '2')
{
    $season = 4;
    $res = $db->Execute("UPDATE $dbtables[hexes] SET move = 4 WHERE terrain = 'pr' OR terrain = 'tu' OR terrain = 'de'");
    db_op_result($res,__LINE__,__FILE__);
    $res = $db->Execute("UPDATE $dbtables[hexes] SET move = 6 WHERE terrain = 'gh'");
    db_op_result($res,__LINE__,__FILE__);
    $res = $db->Execute("UPDATE $dbtables[hexes] SET move = 7 WHERE terrain = 'df' OR terrain = 'cf'");
    db_op_result($res,__LINE__,__FILE__);
    $res = $db->Execute("UPDATE $dbtables[hexes] SET move = 8 WHERE terrain = 'dh' OR terrain = 'ch' OR terrain = 'sw' OR terrain = 'jg'");
    db_op_result($res,__LINE__,__FILE__);
    $res = $db->Execute("UPDATE $dbtables[hexes] SET move = 9 WHERE terrain = 'jh'");
    db_op_result($res,__LINE__,__FILE__);
    $res = $db->Execute("UPDATE $dbtables[hexes] SET move = 11 WHERE terrain = 'lcm'");
    db_op_result($res,__LINE__,__FILE__);
    $res = $db->Execute("UPDATE $dbtables[hexes] SET move = 12 WHERE terrain = 'ljm'");
    db_op_result($res,__LINE__,__FILE__);
    $res = $db->Execute("UPDATE $dbtables[hexes] SET move = 30 WHERE terrain = 'o' OR terrain = 'l' OR terrain = 'hsm'");
    db_op_result($res,__LINE__,__FILE__);
    $res = $db->Execute("UPDATE $dbtables[hexes] SET resource = 'N' WHERE resource = 'Y' AND res_type = ''");
    db_op_result($res,__LINE__,__FILE__);
    $res = $db->Execute("UPDATE $dbtables[skill_table] SET auto = 'Y' WHERE abbr = 'seek'");
    db_op_result($res,__LINE__,__FILE__);
}

$res = $db->Execute("UPDATE $dbtables[game_date] SET count = '$season' WHERE type = 'season'");
db_op_result($res,__LINE__,__FILE__);

$res = $db->Execute("TRUNCATE TABLE $dbtables[activities]");
db_op_result($res,__LINE__,__FILE__);
?>