<?
$pos = (strpos($_SERVER['PHP_SELF'], "/mysqlt-common.php"));
if ($pos !== false)
{
    die("You cannot access this page directly!");
}
//FILE SCHEDULED FOR DELETION
session_start();
header("Cache-control: private");

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

page_header("System Update");

$username = $_SESSION['username'];
$admin = $db->Execute("SELECT * FROM $dbtables[chiefs] WHERE username = '$username'");
$admininfo = $admin->fields;

if(!$admininfo['admin'] >= $privilege['adm_sched'])
{
    echo "<BR>You must have privilege to run the scheduler to use this tool.<BR>\n";
    page_footer();
}


$starttime = time();


// Optimize the tables each night...
// If adding additional tables, add them here as well.
$db->Execute("UPDATE $dbtables[products] SET amount = 0 WHERE amount < 0");

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


// End of optimization ----------------<)E

// Undo the Transfers table
$db->Execute("DELETE FROM $dbtables[poptrans]");

// Establish Game Time
include("game_time.php");

// DeVA Determination
include("deva.php");

// Goods Tribe Determination
include("goods_tribe.php");

// Get rid of abandoned structures
include("structures.php");

// Primary Skill Attempts
include("primaryskill.php");

// Secondary Skill Attempts
include("secondaryskill.php");

$db->Execute("DELETE FROM $dbtables[last_turn]");

// Copy this turn's into last_turn
// Not including defaults added by the game
$last = $db->Execute("SELECT * FROM $dbtables[activities]");
while( !$last->EOF )
{
    $last_turn = $last->fields;
    $db->Execute("INSERT INTO $dbtables[last_turn] "
                ."VALUES("
                ."'',"
                ."'$last_turn[tribeid]',"
                ."'$last_turn[skill_abbr]',"
                ."'$last_turn[product]',"
                ."'$last_turn[actives]')");
    $last->MoveNext();
}

include("defaultactivities.php");
////////////////////////////////////////End of default///////////////////////////////////////////////

// Fair Code

// Culture Transactions
if($month[count] == '4' | $month[count] == '10'){
include("fairfigures.php");
}

// Fair Price Adjustment
if($month[count] == '5' | $month[count] == '11'){
include("fairfigures2.php");
}

// Fair Price List
if($month[count] == '6' | $month[count] == '12'){
include("fairpricelist.php");
}

include("mining.php");

include("quarry.php");

include("huntfur.php");

include("skingutbone.php");

include("bonework.php");

include("forestry.php");

include("tanning.php");

include("curing.php");

include("dressing.php");

include("woodworking.php");

include("leatherworking.php");

include("weaving.php");

include("sewing.php");

include("waxwork.php");

include("refining.php");

include("metalworking.php");

include("armormaking.php");

include("weaponmaking.php");

include("hvywpns.php");

include("stonework.php");

include("herding.php");

include("engineering.php");

include("farming.php");
include("farmgrowth.php");

include("apiarism.php");

include("distilling.php");

include("baking.php");

include("fletching.php");

include("seeking.php");

include("population.php");

include("garexp.php");

include("scouting.php");

// Generate Scores For the Month
include("scores.php");

// Move everything to the goods tribes
include("gttransfers.php");

include("weight.php");

// Hex Game Repopulation
include("hexupdate.php");

// Give back products used in activities
include("productgiveback.php");

// Clean Up any dead subtribes
include("cleanup.php");

//Reset Poptransfer table
$db->Execute("DELETE FROM $dbtables[poptrans]");

$db->Execute("DELETE FROM $dbtables[activities] WHERE skill_abbr = 'Relax'");
$db->Execute("UPDATE $dbtables[chiefs] SET active = active + 1");
$db->Execute("DELETE FROM $dbtables[map_view]");
$weather = $db->Execute("SELECT * FROM $dbtables[game_date] where type = 'weather'");
$weatherinfo = $weather->fields;
$db->Execute("UPDATE $dbtables[game_date] set count = 0 WHERE type = 'weather'");
$db->Execute("UPDATE $dbtables[structures] SET used = 'N' WHERE used = 'Y'");

$endtime = time();
$diff_seconds = $endtime - $starttime;
$diff_minutes = floor($diff_seconds/60);
$diff_seconds -= $diff_minutes * 60;
$db->Execute("INSERT INTO $dbtables[logs] "
            ."VALUES("
            ."'',"
            ."'$month[count]',"
            ."'$year[count]',"
            ."'0000',"
            ."'0000',"
            ."'SYSTEMSTAT',"
            ."'$stamp',"
            ."'Update completed in $diff_minutes minutes, "
            ."$diff_seconds seconds, the weather count "
            ."reached $weatherinfo[count]')");

echo "<P>The System Update has been completed.";


    if (ISSET($_REQUEST['chain']))
    {
        echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=sched_time.php?force=1\">";
    }

page_footer();
?>
