<?php
session_start();
header("Cache-control: private");
include("config.php");
connectdb();
$timedt = array();
$timedata = get_game_time($timedt);
page_header("Logging Out");



$result = $db->Execute("SELECT * FROM $dbtables[chiefs] WHERE clanid ='$_SESSION[clanid]'");
$playerinfo = $result->fields;

if($_SESSION['delete'] == '1')
{
    $title = "Clan Deleted";
    bigtitle();
    echo "Clan $playerinfo[clanid] successfully deleted.<BR>";
    $month = $timedata['month'];
    $year = $timedata['year'];
    $data = "Chief Committed Suicide $_SESSION[clanid] FROM $ip";
    playerlog($_SESSION['current_unit'],$_SESSION['clanid'],'SUICIDE',$month['count'],$year['count'],$data,$dbtables);
    adminlog('SUICIDE', $data);
    //var_dump($timedata);
    $_SESSION = array();
    session_destroy();
    TEXT_GOTOLOGIN();
}
else
{
    //playerlog($playerinfo[clanid], LOG_LOGOUT, $ip);
    $month = $timedata['month'];
    $year = $timedata['year'];
    $data = "Logged out at ".date("Y-m-d H:i:s")." from IP $ip";
    playerlog($_SESSION['current_unit'],$_SESSION['clanid'],'LOGOUT',$month['count'],$year['count'],$data,$dbtables);
    //var_dump($timedata);
    $_SESSION = array();
    session_destroy();
    bigtitle();
    echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=index.php\">";;
}


page_footer();

?>
