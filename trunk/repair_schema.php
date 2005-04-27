<?
require_once("config.php");
$time_start = getmicrotime();
include("game_time.php");
connectdb();

$res = $db->Execute("SHOW TABLES");
while( !$res->EOF )
{
            $table = $res->fields;
                $out = $db->Execute("REPAIR TABLE $table[Tables_in_tribe]");
                if( !$output )
                {
                    echo "Fixing $table[Tables_in_tribe]: $db->ErrorMsg() ";
                }
                else
                {
                    echo "Fixing $table[Tables_in_tribe]:  $output->fields ";
                }

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
