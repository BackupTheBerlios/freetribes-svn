<?php
error_reporting  (E_ALL);
$pos = (strpos($_SERVER['PHP_SELF'], "/hourlysched.php"));
if ($pos !== false)
{
    die("You cannot access this page directly!");
}

//cheater catcher was reportprod.php
 $check = $db->Execute("SELECT tribeid FROM $dbtables[products] WHERE amount < 0 ");
 db_op_result($check,__LINE__,__FILE__);
 while(!$check->EOF)
 {
     $info = $check->fields;

     $message = "Negative Value found in products table for $info[tribeid] at $stamp";
     adminlog('NEGATIVES', $message);

     $check->MoveNext();
 }

    //end cheater catcher

include("scheduler/weight.php");      //this outta be a function instead
//update_weight();
//update_carry_capacity();

$res = $db->Execute("UPDATE $dbtables[game_date] SET count = count +1 WHERE type = 'day'");
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
$res = $db->Execute("DELETE FROM adodb_logsql WHERE timer < '0.0005' AND tracer NOT LIKE '%ERROR%'");
db_op_result($res,__LINE__,__FILE__);

$res = $db->Execute("DELETE FROM $dbtables[logs] WHERE time < date_sub(NOW(),INTERVAL 7 day)");
db_op_result($res,__LINE__,__FILE__);
//echo "DONE!";
?>

