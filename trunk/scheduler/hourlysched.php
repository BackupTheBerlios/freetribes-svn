<?php
error_reporting  (E_ALL);
$pos = (strpos($_SERVER['PHP_SELF'], "/hourlysched.php"));
if ($pos !== false)
{
    die("You cannot access this page directly!");
}

///HMMM!!!
//      NEW EVENT! : <br>
//hourlysched.php executed in 23.6696419716 seconds at 2005-05-24 20:00:01. Interval is 12 minutes last run at 2005-05-24 19:48:02 runtime scheduled for 2005-05-24 19:59:02 <br><br>
//
//<br> Scheduler completed in 23.6785838604 seconds at 2005-05-24 20:00:25<br>
//WHY??!!

include("scheduler/reportprod.php");
include("scheduler/weight.php");

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
$res = $db->Execute("UPDATE $dbtables[game_date] SET count = count +1 WHERE type = 'day'");
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
if( $month['count'] < 3 || $month['count'] > 10 )
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
if( $season['count'] == '1' )
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

$res = $db->Execute("DELETE FROM $dbtables[logs] WHERE time < date_sub(NOW(),INTERVAL 7 day)");
db_op_result($res,__LINE__,__FILE__);
//echo "DONE!";
?>

