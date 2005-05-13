<?php
error_reporting  (E_ALL);
include_once('../config.php');
$time_start = getmicrotime();
include("game_time.php");
connectdb();
$res = $db->Execute("OPTIMIZE TABLE products_used, farming, farm_activities, mapping, outbox, alliances, activities, armor, chiefs, game_date, hexes, ip_bans, livestock, logs, messages, product_table, products, resources, skills, seeking, scouts, poptrans, skill_table, structures, tribes, weather, fair, fair_tribe, clans");
db_op_result($res,__LINE__,__FILE__);
include("reportprod.php");
include("weight.php");
if( $_SERVER[REMOTE_ADDR] != $_SERVER[SERVER_ADDR] )
{
    die("You cannot access this page directly!");
}

$now = date("i");//minutes leading 0
if($now == "00")//only on the hour
{
 //JUST FOR DEVELOPMENT
 if($day['count'] == 6 || $day['count'] == 12 || $day['count'] == 18)
 {
    $day['count'] = 0;
 }
  //REMOVE IN RELEASE CODE
 //$day['count'] gotten from game_time

if( $day['count'] == 0)
{
    $month['count']++;
}
if( $month['count'] > 12 )
{
    $month['count'] = 1;
    $year['count']++;
}
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
$res = $db->Execute("UPDATE $dbtables[game_date] SET count = '$day[count]' WHERE type = 'day'");
db_op_result($res,__LINE__,__FILE__);
$res = $db->Execute("UPDATE $dbtables[game_date] SET count = '$month[count]' WHERE type = 'month'");
db_op_result($res,__LINE__,__FILE__);
$res = $db->Execute("UPDATE $dbtables[game_date] SET count = '$year[count]' WHERE type = 'year'");
db_op_result($res,__LINE__,__FILE__);
$res = $db->Execute("UPDATE $dbtables[game_date] SET count = '$season' WHERE type = 'season'");
db_op_result($res,__LINE__,__FILE__);
$seasons = $db->Execute("Select * from $dbtables[game_date] WHERE type = 'season'");
db_op_result($seasons,__LINE__,__FILE__);
$season = $seasons->fields;
$weather_roll = rand(1,100);
$months = $db->Execute("SELECT * FROM $dbtables[game_date] WHERE type = 'month'");
db_op_result($months,__LINE__,__FILE__);
$month = $months->fields;
if( $month[count] < 3 || $month['count'] > 10 )
{
    $weather_roll = $weather_roll - 10;
}
elseif( $month['count'] < 5 || $month['count'] > 8 )
{
    $weather_roll = $weather_roll - 5;
}
elseif( $weather_roll < 0 )
{
    $weather_roll = 0;
}

$res = $db->Execute("UPDATE $dbtables[weather] SET current_type = 'N'");
db_op_result($res,__LINE__,__FILE__);
if( $season[count] == '1' )
{ ///Spring
    if( $weather_roll < 10 )
    {
        $res = $db->Execute("UPDATE $dbtables[weather] SET current_type = 'Y' WHERE weather_id = '8'"); // Heavy Snow
        db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("UPDATE $dbtables[game_date] SET count = count + 8 WHERE type = 'weather'");
        db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("UPDATE $dbtables[farming] "
                    ."SET harvest = harvest * .33 "
                    ."WHERE crop != 'NONE'");
        db_op_result($res,__LINE__,__FILE__);
    }
    elseif( $weather_roll < 25 )
    {
        $res = $db->Execute("UPDATE $dbtables[weather] SET current_type = 'Y' WHERE weather_id = '7'"); // Snow
        db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("UPDATE $dbtables[game_date] SET count = count + 6 WHERE type = 'weather'");
        db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("UPDATE $dbtables[farming] "
                    ."SET harvest = harvest * .75 "
                    ."WHERE crop != 'NONE'");
        db_op_result($res,__LINE__,__FILE__);
    }
    elseif( $weather_roll < 40 )
    {
        $res = $db->Execute("UPDATE $dbtables[weather] SET current_type = 'Y' WHERE weather_id = '5'"); // Heavy Rain
        db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("UPDATE $dbtables[game_date] SET count = count + 4 WHERE type = 'weather'");
        db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("UPDATE $dbtables[farming] "
                    ."SET harvest = harvest * .90 "
                    ."WHERE crop != 'NONE'");
        db_op_result($res,__LINE__,__FILE__);
    }
    elseif( $weather_roll < 55 )
    {
        $res = $db->Execute("UPDATE $dbtables[weather] SET current_type = 'Y' WHERE weather_id = '2'"); // Rain
        db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("UPDATE $dbtables[game_date] SET count = count + 2 WHERE type = 'weather'");
        db_op_result($res,__LINE__,__FILE__);
    }
    elseif( $weather_roll < 60 )
    {
        $res = $db->Execute("UPDATE $dbtables[weather] SET current_type = 'Y' WHERE weather_id = '4'"); // Wind
        db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("UPDATE $dbtables[game_date] SET count = count + 2 WHERE type = 'weather'");
        db_op_result($res,__LINE__,__FILE__);
    }
    else
    {
        $res = $db->Execute("UPDATE $dbtables[weather] SET current_type = 'Y' WHERE weather_id = '1'"); // Fine
        db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("UPDATE $dbtables[game_date] SET count = count + 1 WHERE type = 'weather'");
        db_op_result($res,__LINE__,__FILE__);
    }
}
elseif( $season['count'] == '2' )
{ ///Summer
    if( $weather_roll < 40 )
    {
        $res = $db->Execute("UPDATE $dbtables[weather] SET current_type = 'Y' WHERE weather_id = '1'");  // Fine
        db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("UPDATE $dbtables[game_date] SET count = count + 1 WHERE type = 'weather'");
        db_op_result($res,__LINE__,__FILE__);
    }
    elseif( $weather_roll < 60 )
    {
        $res = $db->Execute("UPDATE $dbtables[weather] SET current_type = 'Y' WHERE weather_id = '1'"); // Fine
        db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("UPDATE $dbtables[game_date] SET count = count + 0 WHERE type = 'weather'");
        db_op_result($res,__LINE__,__FILE__);
    }
    elseif( $weather_roll < 85 )
    {
        $res = $db->Execute("UPDATE $dbtables[weather] SET current_type = 'Y' WHERE weather_id = '2'"); // Rain
        db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("UPDATE $dbtables[game_date] SET count = count + 2 WHERE type = 'weather'");
        db_op_result($res,__LINE__,__FILE__);
    }
    elseif( $weather_roll < 95 )
    {
        $res = $db->Execute("UPDATE $dbtables[weather] SET current_type = 'Y' WHERE weather_id = '5'"); // Heavy Rain
        db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("UPDATE $dbtables[game_date] SET count = count + 3 WHERE type = 'weather'");
        db_op_result($res,__LINE__,__FILE__);
    }
    else
    {
        $res = $db->Execute("UPDATE $dbtables[weather] SET current_type = 'Y' WHERE weather_id = '4'"); // Wind
        db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("UPDATE $dbtables[game_date] SET count = count + 3 WHERE type = 'weather'");
        db_op_result($res,__LINE__,__FILE__);
    }
}
elseif( $season['count'] == '3' )
{ ///Fall
    if( $weather_roll < 40 )
    {
        $res = $db->Execute("UPDATE $dbtables[weather] SET current_type = 'Y' WHERE weather_id = '1'"); // Fine
        db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("UPDATE $dbtables[game_date] SET count = count + 2 WHERE type = 'weather'");
        db_op_result($res,__LINE__,__FILE__);
    }
    elseif( $weather_roll < 60 )
    {
        $res = $db->Execute("UPDATE $dbtables[weather] SET current_type = 'Y' WHERE weather_id = '1'"); // Fine
        db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("UPDATE $dbtables[game_date] SET count = count + 3 WHERE type = 'weather'");
        db_op_result($res,__LINE__,__FILE__);
    }
    elseif( $weather_roll < 80 )
    {
        $res = $db->Execute("UPDATE $dbtables[weather] SET current_type = 'Y' WHERE weather_id = '2'"); // Rain
        db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("UPDATE $dbtables[game_date] SET count = count + 4 WHERE type = 'weather'");
        db_op_result($res,__LINE__,__FILE__);
    }
    elseif( $weather_roll < 95 )
    {
        $res = $db->Execute("UPDATE $dbtables[weather] SET current_type = 'Y' WHERE weather_id = '4'"); // Wind
        db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("UPDATE $dbtables[game_date] SET count = count + 5  WHERE type = 'weather'");
        db_op_result($res,__LINE__,__FILE__);
    }
    else
    {
        $res = $db->Execute("UPDATE $dbtables[weather] SET current_type = 'Y' WHERE weather_id = '5'"); // Snow
        db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("UPDATE $dbtables[game_date] SET count = count + 6 WHERE type = 'weather'");
        db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("UPDATE $dbtables[farming] "
                    ."SET harvest = harvest * .75 "
                    ."WHERE crop != 'NONE'");
        db_op_result($res,__LINE__,__FILE__);
    }
}
elseif( $season['count'] == '4' )
{ ///Winter
    if( $weather_roll < 15 )
    {
        $res = $db->Execute("UPDATE $dbtables[weather] SET current_type = 'Y' WHERE weather_id = '1'"); // Fine
        db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("UPDATE $dbtables[game_date] SET count = count + 2 WHERE type = 'weather'");
        db_op_result($res,__LINE__,__FILE__);
    }
    elseif( $weather_roll < 40 )
    {
        $res = $db->Execute("UPDATE $dbtables[weather] SET current_type = 'Y' WHERE weather_id = '7'"); // Snow
        db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("UPDATE $dbtables[game_date] SET count = count + 6 WHERE type = 'weather'");
        db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("UPDATE $dbtables[farming] "
                    ."SET harvest = harvest * .60 "
                    ."WHERE crop != 'NONE'");
        db_op_result($res,__LINE__,__FILE__);
    }
    elseif( $weather_roll < 70 )
    {
        $res = $db->Execute("UPDATE $dbtables[weather] SET current_type = 'Y' WHERE weather_id = '4'"); // Wind
        db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("UPDATE $dbtables[game_date] SET count = count + 5 WHERE type = 'weather'");
        db_op_result($res,__LINE__,__FILE__);
    }
    elseif( $weather_roll < 90 )
    {
        $res = $db->Execute("UPDATE $dbtables[weather] SET current_type = 'Y' WHERE weather_id = '8'"); // Heavy Snow
        db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("UPDATE $dbtables[game_date] SET count = count + 7 WHERE type = 'weather'");
        db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("UPDATE $dbtables[farming] "
                    ."SET harvest = harvest * .25 "
                    ."WHERE crop != 'NONE'");
        db_op_result($res,__LINE__,__FILE__);
    }
    elseif( $weather_roll < 96 )
    {
        $res = $db->Execute("UPDATE $dbtables[weather] SET current_type = 'Y' WHERE weather_id = '2'"); // Rain
        db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("UPDATE $dbtables[game_date] SET count = count + 6 WHERE type = 'weather'");
        db_op_result($res,__LINE__,__FILE__);
    }
    elseif( $weather_roll < 98 )
    {
        $res = $db->Execute("UPDATE $dbtables[weather] SET current_type = 'Y' WHERE weather_id = '8'"); // Heavy Snow
        db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("UPDATE $dbtables[game_date] SET count = count + 9 WHERE type = 'weather'");
        db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("UPDATE $dbtables[farming] "
                    ."SET harvest = harvest * .10 "
                    ."WHERE crop != 'NONE'");
        db_op_result($res,__LINE__,__FILE__);
    }
    else
    {
        $res = $db->Execute("UPDATE $dbtables[weather] SET current_type = 'Y' WHERE weather_id = '8'"); // Blizzard
        db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("UPDATE $dbtables[game_date] SET count = count + 11 WHERE type = 'weather'");
        db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("UPDATE $dbtables[farming] "
                    ."SET harvest = 0 "
                    ."WHERE crop != 'NONE'");
        db_op_result($res,__LINE__,__FILE__);
    }
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
            ."'HOURLYTICK',"
            ."'$stamp',"
            ."'$page_name completed in $time seconds.')");
db_op_result($res,__LINE__,__FILE__);

$res = $db->Execute("DELETE FROM $dbtables[logs] WHERE time < date_sub(NOW(),INTERVAL 1 day)");
db_op_result($res,__LINE__,__FILE__);
//echo "DONE!";
?>

