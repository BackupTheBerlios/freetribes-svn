<?php
error_reporting  (E_ALL);


include_once('config.php');

 $time_start = getmicrotime();
 $sched_starttime = getmicrotime();
include("scheduler/game_time.php");
connectdb();
  /*
  CREATE TABLE `tstr_scheduler` (
`id` TINYINT( 3 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
`type` ENUM( 'H', 'D' ) DEFAULT 'H' NOT NULL ,
`frequency` INT( 6 ) UNSIGNED DEFAULT '60' NOT NULL ,
`script` VARCHAR( 15 ) NOT NULL ,
`last_run` DATETIME NOT NULL ,
PRIMARY KEY ( `id` ) ,
INDEX ( `type` )
);
*/
$sched_run = $db->Execute("select script,last_run,frequency,date_add(last_run,interval (frequency-1) minute) as runtime from $dbtables[scheduler] order by sequence,type,last_run");
db_op_result($sched_run,__LINE__,__FILE__);
while(!$sched_run->EOF)
{
         //loop through every file, check last run against frequency and current date/time, execute if allowed
    $sched_info = $sched_run->fields;
    $script = $sched_info['script'];
    $interval = $sched_info['frequency'];
    $last_run = $sched_info['last_run'];
    $runtime = $sched_info['runtime'];
    $now = date("Y-m-d H:i:s");
    if($now > $runtime )
    {
        include_once('./scheduler/'.$script);
        echo "NEW EVENT! : <br>\n";
        echo "$script executed at $now. Interval is $interval minutes last run at $last_run runtime scheduled for $runtime <br><br>\n\n";
        $upd = $db->Execute("update $dbtables[scheduler] SET last_run = now() where script='$script'");
    }
    //echo $script;
    $sched_run->MoveNext();
}

$time_end = getmicrotime();
$time = $time_end - $time_start;
echo "<br>Scheduler completed in $time seconds at ".date("Y-m-d H:i:s")."\n Game Time is (d m y) $day[count] $month[count] $year[count]<br>";

//$test = $db->Execute("SELECT * frmo tstr_loopus WHERE id=3");
//db_op_result($test,__LINE__,__FILE__);

exit();
?>