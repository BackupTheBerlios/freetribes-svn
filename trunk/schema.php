<?
require_once("config.php");
$time_start = getmicrotime();
include("game_time.php");
connectdb();

$old = '/dev/null';
$new = 'schema.sql';
copy( $old, $new );
$res = $db->Execute("SHOW TABLES");
while( !$res->EOF )
{
            $table = $res->fields;
                $sql = "mysqldump -Q -c ";
                $sql .= $dbname;
                $sql .= " -u ";
                $sql .= $dbuname;
                $sql .= " ";
                $sql .= $table[Tables_in_tribe];
                $sql .= " >> ";
                $sql .= $gameroot;
                $sql .= "schema.sql";
                exec("$sql");

    $res->MoveNext();
}

$time_end = getmicrotime();
$time = $time_end - $time_start;
$file = __FILE__;
$db->Execute("INSERT INTO $dbtables[logs] "
            ."VALUES("
            ."'',"
            ."'$month[count]',"
            ."'$year[count]',"
            ."'0000',"
            ."'0000.00',"
            ."'BENCHMARK',"
            ."'$stamp',"
            ."'$file completed in $time seconds.')");

?>
