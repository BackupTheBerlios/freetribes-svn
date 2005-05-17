<?php
$pos = (strpos($_SERVER['PHP_SELF'], "/mysqlt-common.php"));
if ($pos !== false)
{
    die("You cannot access this page directly!");
}

require_once("../config.php");
$time_start = getmicrotime();
include("game_time.php");
connectdb();

$old = '/dev/null';
$new = 'dbschemafull.sql';
$backuppath = $gameroot . "scheduler/";
copy( $old, $new );
exec("chmod g+w dbschemafull.sql");
$res = $db->Execute("SHOW TABLES");
db_op_result($res,__LINE__,__FILE__);
while( !$res->EOF )
{
    $table = $res->fields;
    $sql = "mysqldump --allow-keywords -c ";
    $sql .= $dbname;
    $sql .= " -u ";
    $sql .= $dbuname;
    $sql .= " ";
    $sql .= $table[Tables_in_tribe];
    $sql .= " >> ";
    $sql .= $backuppath;
    $sql .= "dbschemafull.sql";
    exec("$sql");
    $res->MoveNext();
}
exec("gzip dbschemafull.sql");
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
