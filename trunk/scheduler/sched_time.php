<?php
session_start();
header("Cache-control: private");

error_reporting  (E_ALL);

require_once("../config.php");
if ($sched_type == 1) ob_start();

global $db, $dbtables;
connectdb();


if ( !ISSET($_SESSION['username']) || !ISSET($_SESSION['password']) )
{
    $username = $_REQUEST['username'];
    $_SESSION['username'] = $username;
    $password = $_REQUEST['password'];
    $md5password = md5($password);
    $_SESSION['password'] = $password;
}

$res = $db->Execute("SELECT * FROM $dbtables[chiefs] "
                    ."WHERE username='$_SESSION[username]' "
                    ."AND password='$_SESSION[password]' "
                    ."LIMIT 1");
$playerinfo = $res->fields;

page_header("Seasonal & Weather Update");


$username = $_SESSION['username'];
$admin = $db->Execute("SELECT * FROM $dbtables[chiefs] WHERE username = '$username'");
$admininfo = $admin->fields;

if(!$admininfo['admin'] >= $privilege['adm_sched'] || $admin->EOF)
{
    echo "<BR>You must have privilege to run the scheduler to use this tool.<BR>\n";
    page_footer();
}

$dirname = dirname($_SERVER['PHP_SELF']);
echo "<P>Your cron jobs on Windows should be something like ... "
    ."<P CLASS=text_small><B>"
    ."00 0 * * * start /high /min /dPATH_TO_LYNX\ LYNX.EXE -dump http://localhost$dirname/sched_skill.php?username=$_SESSION[username]^&amp;password=$_SESSION[password] >NUL"
    ."<BR>05 * * * * start /high /min /dPATH_TO_LYNX\ LYNX.EXE -dump http://localhost$dirname/sched_time.php?username=$_SESSION[username]^&amp;password=$_SESSION[password]^&force=1 >NUL";

echo "</B><P>Your cron jobs on Linux/Unix should be something like ... "
    ."<P CLASS=text_small><B>"
    ."00 0 * * * PATH_TO_LYNX\lynx -dump http://localhost$dirname/sched_skill.php?username=$_SESSION[username]&amp;password=$_SESSION[password] >/dev/null"
    ."<BR>05 * * * * PATH_TO_LYNX\lynx -dump http://localhost$dirname/sched_time.php?username=$_SESSION[username]&amp;password=$_SESSION[password]&force=1 >/dev/null"
    ."</B>";

echo "<P>You may have to find a way to quote the &amp; immediately before <i>password</i> in the Unix lines since the shell may interpret it as the intention to have the command run in the background.";


include("game_time.php");

// Optimize the tables each night...
// If we add additional tables, we need to add them here as well.

$sql = "OPTIMIZE TABLE ";
$i = 1;
foreach ($dbtables AS $key => $value)
{
    $sql .= "\$dbtables[$value]";
    if ($i <> count($dbtables))
    {
        $sql .= ", ";
    }
    $i++;
}

$opti = $db->Execute($sql);

include("reportprod.php");
include("weight.php");

// End of optimization ----------------<)E

$day[count] = date("H");

if($day[count] == 0 || $_REQUEST['force']==1) //Only run from 00:00 to 00:59
{
    $log_type = "HSCHED_START";
    $log_entry = "Hourly Scheduler started.";
    $month[count]++;
}
else
{
    $log_type = "SCHED_FAIL";
    $log_entry = "Attempt to execute $file at $stamp failed due to wrong time of day.";

    echo "<P>You can only run this file between midnight and 1am.";
    echo "<BR>You can get around this problem by adding &force=1 to the end of the URL.";
}

$db->Execute("INSERT INTO $dbtables[logs] "
            ."VALUES("
            ."'',"
            ."'$month[count]',"
            ."'$year[count]',"
            ."'0000',"
            ."'0000.00',"
            ."'$log_type',"
            ."'$stamp',"
            ."'$log_entry')");
if ($log_type == "SCHED_FAIL")
{
    page_footer();
}


if($month[count] > 12)
{
    $month[count] = 1;
    $year[count]++;
}

if($month[count] == '3' | $month[count] == '4' | $month[count] == '5')
{
    $season = 1;
    $db->Execute("UPDATE $dbtables[hexes] SET move = 2 WHERE terrain = 'pr' OR terrain = 'tu' OR terrain = 'de'");
    $db->Execute("UPDATE $dbtables[hexes] SET move = 4 WHERE terrain = 'gh'");
    $db->Execute("UPDATE $dbtables[hexes] SET move = 5 WHERE terrain = 'df' OR terrain = 'cf'");
    $db->Execute("UPDATE $dbtables[hexes] SET move = 6 WHERE terrain = 'dh' OR terrain = 'ch' OR terrain = 'sw' OR terrain = 'jg'");
    $db->Execute("UPDATE $dbtables[hexes] SET move = 7 WHERE terrain = 'jh'");
    $db->Execute("UPDATE $dbtables[hexes] SET move = 9 WHERE terrain = 'lcm'");
    $db->Execute("UPDATE $dbtables[hexes] SET move = 10 WHERE terrain = 'ljm'");
    $db->Execute("UPDATE $dbtables[hexes] SET move = 30 WHERE terrain = 'o' OR terrain = 'l' OR terrain = 'hsm'");
    $db->Execute("UPDATE $dbtables[hexes] SET resource = 'N' WHERE resource = 'Y' AND res_type = ''");
    $db->Execute("UPDATE $dbtables[skill_table] SET auto = 'N' WHERE abbr = 'seek'");
}

if($month[count] == '6' | $month[count] == '7' | $month[count] == '8')
{
    $season = 2;
    $db->Execute("UPDATE $dbtables[hexes] SET move = 3 WHERE terrain = 'pr' OR terrain = 'tu' OR terrain = 'de'");
    $db->Execute("UPDATE $dbtables[hexes] SET move = 5 WHERE terrain = 'gh'");
    $db->Execute("UPDATE $dbtables[hexes] SET move = 6 WHERE terrain = 'df' OR terrain = 'cf'");
    $db->Execute("UPDATE $dbtables[hexes] SET move = 7 WHERE terrain = 'dh' OR terrain = 'ch' OR terrain = 'sw' OR terrain = 'jg'");
    $db->Execute("UPDATE $dbtables[hexes] SET move = 8 WHERE terrain = 'jh'");
    $db->Execute("UPDATE $dbtables[hexes] SET move = 10 WHERE terrain = 'lcm'");
    $db->Execute("UPDATE $dbtables[hexes] SET move = 11 WHERE terrain = 'ljm'");
    $db->Execute("UPDATE $dbtables[hexes] SET move = 30 WHERE terrain = 'o' OR terrain = 'l' OR terrain = 'hsm'");
    $db->Execute("UPDATE $dbtables[hexes] SET resource = 'N' WHERE resource = 'Y' AND res_type = ''");
    $db->Execute("UPDATE $dbtables[skill_table] SET auto = 'N' WHERE abbr = 'seek'");
}

if($month[count] == '9' | $month[count] == '10' | $month[count] == '11' )
{
    $season = 3;
    $db->Execute("UPDATE $dbtables[hexes] SET move = 3 WHERE terrain = 'pr' OR terrain = 'tu' OR terrain = 'de'");
    $db->Execute("UPDATE $dbtables[hexes] SET move = 5 WHERE terrain = 'gh'");
    $db->Execute("UPDATE $dbtables[hexes] SET move = 6 WHERE terrain = 'df' OR terrain = 'cf'");
    $db->Execute("UPDATE $dbtables[hexes] SET move = 7 WHERE terrain = 'dh' OR terrain = 'ch' OR terrain = 'sw' OR terrain = 'jg'");
    $db->Execute("UPDATE $dbtables[hexes] SET move = 8 WHERE terrain = 'jh'");
    $db->Execute("UPDATE $dbtables[hexes] SET move = 10 WHERE terrain = 'lcm'");
    $db->Execute("UPDATE $dbtables[hexes] SET move = 11 WHERE terrain = 'ljm'");
    $db->Execute("UPDATE $dbtables[hexes] SET move = 30 WHERE terrain = 'o' OR terrain = 'l' OR terrain = 'hsm'");
    $db->Execute("UPDATE $dbtables[hexes] SET resource = 'N' WHERE resource = 'Y' AND res_type = ''");
    $db->Execute("UPDATE $dbtables[skill_table] SET auto = 'N' WHERE abbr = 'seek'");
}

if($month[count] == '12' | $month[count] == '1' | $month[count] == '2')
{
    $season = 4;
    $db->Execute("UPDATE $dbtables[hexes] SET move = 4 WHERE terrain = 'pr' OR terrain = 'tu' OR terrain = 'de'");
    $db->Execute("UPDATE $dbtables[hexes] SET move = 6 WHERE terrain = 'gh'");
    $db->Execute("UPDATE $dbtables[hexes] SET move = 7 WHERE terrain = 'df' OR terrain = 'cf'");
    $db->Execute("UPDATE $dbtables[hexes] SET move = 8 WHERE terrain = 'dh' OR terrain = 'ch' OR terrain = 'sw' OR terrain = 'jg'");
    $db->Execute("UPDATE $dbtables[hexes] SET move = 9 WHERE terrain = 'jh'");
    $db->Execute("UPDATE $dbtables[hexes] SET move = 11 WHERE terrain = 'lcm'");
    $db->Execute("UPDATE $dbtables[hexes] SET move = 12 WHERE terrain = 'ljm'");
    $db->Execute("UPDATE $dbtables[hexes] SET move = 30 WHERE terrain = 'o' OR terrain = 'l' OR terrain = 'hsm'");
    $db->Execute("UPDATE $dbtables[hexes] SET resource = 'N' WHERE resource = 'Y' AND res_type = ''");
    $db->Execute("UPDATE $dbtables[skill_table] SET auto = 'Y' WHERE abbr = 'seek'");

}


$db->Execute("UPDATE $dbtables[game_date] SET count = '$day[count]' WHERE type = 'day'");
$db->Execute("UPDATE $dbtables[game_date] SET count = '$month[count]' WHERE type = 'month'");
$db->Execute("UPDATE $dbtables[game_date] SET count = '$year[count]' WHERE type = 'year'");
$db->Execute("UPDATE $dbtables[game_date] SET count = '$season' WHERE type = 'season'");

$seasons = $db->Execute("SELECT * from $dbtables[game_date] WHERE type = 'season'");
$season = $seasons->fields;
$weather_roll = rand(1,100);
$months = $db->Execute("SELECT * FROM $dbtables[game_date] WHERE type = 'month'");
$month = $months->fields;
if($month[count] < 3 | $month[count] > 10){
$weather_roll = $weather_roll - 10;
}
elseif($month[count] < 5 | $month[count] > 8){
$weather_roll = $weather_roll - 5;
}
elseif($weather_roll < 0){
$weather_roll = 0;
}

  $db->Execute("UPDATE $dbtables[weather] SET current_type = 'N'");


if($season[count] == '1'){ ///Spring
  if($weather_roll < 10){
    $db->Execute("UPDATE $dbtables[weather] SET current_type = 'Y' WHERE weather_id = '8'"); // Heavy Snow
    $db->Execute("UPDATE $dbtables[game_date] SET count = count + 8 WHERE type = 'weather'");
    $db->Execute("UPDATE $dbtables[farming] "
                ."SET harvest = harvest * .33 "
                ."WHERE crop != 'NONE'");
    complete();
  }
  elseif($weather_roll < 25){
    $db->Execute("UPDATE $dbtables[weather] SET current_type = 'Y' WHERE weather_id = '7'"); // Snow
    $db->Execute("UPDATE $dbtables[game_date] SET count = count + 6 WHERE type = 'weather'");
    $db->Execute("UPDATE $dbtables[farming] "
                ."SET harvest = harvest * .75 "
                ."WHERE crop != 'NONE'");
    complete();
  }
  elseif($weather_roll < 40){
    $db->Execute("UPDATE $dbtables[weather] SET current_type = 'Y' WHERE weather_id = '5'"); // Heavy Rain
    $db->Execute("UPDATE $dbtables[game_date] SET count = count + 4 WHERE type = 'weather'");
    $db->Execute("UPDATE $dbtables[farming] "
                ."SET harvest = harvest * .90 "
                ."WHERE crop != 'NONE'");
  }
  elseif($weather_roll < 55){
    $db->Execute("UPDATE $dbtables[weather] SET current_type = 'Y' WHERE weather_id = '2'"); // Rain
    $db->Execute("UPDATE $dbtables[game_date] SET count = count + 2 WHERE type = 'weather'");
  }
  elseif($weather_roll < 60){
    $db->Execute("UPDATE $dbtables[weather] SET current_type = 'Y' WHERE weather_id = '4'"); // Wind
    $db->Execute("UPDATE $dbtables[game_date] SET count = count + 2 WHERE type = 'weather'");
  }
  else{
    $db->Execute("UPDATE $dbtables[weather] SET current_type = 'Y' WHERE weather_id = '1'"); // Fine
    $db->Execute("UPDATE $dbtables[game_date] SET count = count + 1 WHERE type = 'weather'");
  }
  }

if($season[count] == '2'){ ///Summer
  if($weather_roll < 40){
    $db->Execute("UPDATE $dbtables[weather] SET current_type = 'Y' WHERE weather_id = '1'");  // Fine
    $db->Execute("UPDATE $dbtables[game_date] SET count = count + 1 WHERE type = 'weather'");
    complete();
  }
  elseif($weather_roll < 60){
    $db->Execute("UPDATE $dbtables[weather] SET current_type = 'Y' WHERE weather_id = '1'"); // Fine
    $db->Execute("UPDATE $dbtables[game_date] SET count = count + 0 WHERE type = 'weather'");
    complete();
  }
  elseif($weather_roll < 85){
    $db->Execute("UPDATE $dbtables[weather] SET current_type = 'Y' WHERE weather_id = '2'"); // Rain
    $db->Execute("UPDATE $dbtables[game_date] SET count = count + 2 WHERE type = 'weather'");
    complete();
  }
  elseif($weather_roll < 95){
    $db->Execute("UPDATE $dbtables[weather] SET current_type = 'Y' WHERE weather_id = '5'"); // Heavy Rain
    $db->Execute("UPDATE $dbtables[game_date] SET count = count + 3 WHERE type = 'weather'");
    complete();
  }
  else{
    $db->Execute("UPDATE $dbtables[weather] SET current_type = 'Y' WHERE weather_id = '4'"); // Wind
    $db->Execute("UPDATE $dbtables[game_date] SET count = count + 3 WHERE type = 'weather'");
    complete();
  }
  }

if($season[count] == '3'){ ///Fall
  if($weather_roll < 40){
    $db->Execute("UPDATE $dbtables[weather] SET current_type = 'Y' WHERE weather_id = '1'"); // Fine
    $db->Execute("UPDATE $dbtables[game_date] SET count = count + 2 WHERE type = 'weather'");
    complete();
  }
  elseif($weather_roll < 60){
    $db->Execute("UPDATE $dbtables[weather] SET current_type = 'Y' WHERE weather_id = '1'"); // Fine
    $db->Execute("UPDATE $dbtables[game_date] SET count = count + 3 WHERE type = 'weather'");
    complete();
  }
  elseif($weather_roll < 80){
    $db->Execute("UPDATE $dbtables[weather] SET current_type = 'Y' WHERE weather_id = '2'"); // Rain
    $db->Execute("UPDATE $dbtables[game_date] SET count = count + 4 WHERE type = 'weather'");
    complete();
  }
  elseif($weather_roll < 95){
    $db->Execute("UPDATE $dbtables[weather] SET current_type = 'Y' WHERE weather_id = '4'"); // Wind
    $db->Execute("UPDATE $dbtables[game_date] SET count = count + 5  WHERE type = 'weather'");
    complete();
  }
  else{
    $db->Execute("UPDATE $dbtables[weather] SET current_type = 'Y' WHERE weather_id = '5'"); // Snow
    $db->Execute("UPDATE $dbtables[game_date] SET count = count + 6 WHERE type = 'weather'");
    $db->Execute("UPDATE $dbtables[farming] "
                ."SET harvest = harvest * .75 "
                ."WHERE crop != 'NONE'");
    complete();
  }
  }

if($season[count] == '4'){ ///Winter
  if($weather_roll < 15){
    $db->Execute("UPDATE $dbtables[weather] SET current_type = 'Y' WHERE weather_id = '1'"); // Fine
    $db->Execute("UPDATE $dbtables[game_date] SET count = count + 2 WHERE type = 'weather'");
    complete();
  }
  elseif($weather_roll < 40){
    $db->Execute("UPDATE $dbtables[weather] SET current_type = 'Y' WHERE weather_id = '7'"); // Snow
    $db->Execute("UPDATE $dbtables[game_date] SET count = count + 6 WHERE type = 'weather'");
    $db->Execute("UPDATE $dbtables[farming] "
                ."SET harvest = harvest * .60 "
                ."WHERE crop != 'NONE'");
    complete();
  }
  elseif($weather_roll < 70){
    $db->Execute("UPDATE $dbtables[weather] SET current_type = 'Y' WHERE weather_id = '4'"); // Wind
    $db->Execute("UPDATE $dbtables[game_date] SET count = count + 5 WHERE type = 'weather'");
    complete();
  }
  elseif($weather_roll < 90){
    $db->Execute("UPDATE $dbtables[weather] SET current_type = 'Y' WHERE weather_id = '8'"); // Heavy Snow
    $db->Execute("UPDATE $dbtables[game_date] SET count = count + 7 WHERE type = 'weather'");
    $db->Execute("UPDATE $dbtables[farming] "
                ."SET harvest = harvest * .25 "
                ."WHERE crop != 'NONE'");
    complete();
  }
  elseif($weather_roll < 96){
    $db->Execute("UPDATE $dbtables[weather] SET current_type = 'Y' WHERE weather_id = '2'"); // Rain
    $db->Execute("UPDATE $dbtables[game_date] SET count = count + 6 WHERE type = 'weather'");
    complete();
  }
  elseif($weather_roll < 98){
    $db->Execute("UPDATE $dbtables[weather] SET current_type = 'Y' WHERE weather_id = '8'"); // Heavy Snow
    $db->Execute("UPDATE $dbtables[game_date] SET count = count + 9 WHERE type = 'weather'");
    $db->Execute("UPDATE $dbtables[farming] "
                ."SET harvest = harvest * .10 "
                ."WHERE crop != 'NONE'");
    complete();
  }
  else{
    $db->Execute("UPDATE $dbtables[weather] SET current_type = 'Y' WHERE weather_id = '8'"); // Blizzard
    $db->Execute("UPDATE $dbtables[game_date] SET count = count + 11 WHERE type = 'weather'");
    $db->Execute("UPDATE $dbtables[farming] "
                ."SET harvest = 0 "
                ."WHERE crop != 'NONE'");
    complete();
  }
  }

complete();

function complete()
{
    global $db, $dbtables, $time_start, $month, $year, $stamp;

    echo "<P>The Seasonal Scheduler has completed its work.";

    $time_end = getmicrotime();
    $time = $time_end - $time_start;

    $db->Execute("INSERT INTO $dbtables[logs] "
                ."VALUES("
                ."'',"
                ."'$month[count]',"
                ."'$year[count]',"
                ."'0000',"
                ."'0000.00',"
                ."'HSCHED_END',"
                ."'$stamp',"
                ."'Seasonal scheduler completed in $time seconds.')");


    page_footer();
}

?>
