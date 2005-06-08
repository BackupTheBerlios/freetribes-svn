<?php
session_start();
header("Cache-control: private");

if (!ISSET($_SESSION['theme']))
{
    $_SESSION['theme'] = $theme_default;  // This must be set on this page before the header is included
}

include("config.php");

$time_start = getmicrotime();

page_header("Geographic Points of Interest");

connectdb();

echo "<CENTER>";

navbar_help();

echo "<BR><BR><CENTER>";
echo "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=\"100%\"><TR ALIGN=CENTER>";

$hex = $db->Execute("SELECT COUNT(*) as count FROM $dbtables[hexes]");
$hexinfo = $hex->fields;
$total = NUMBER($hexinfo[count]);
echo "<TD>There are $total map tiles in the game.</TD>";
$res = $db->Execute("SELECT COUNT(*) as count FROM $dbtables[hexes] WHERE resource = 'Y'");
$resinfo = $res->fields;
$total = NUMBER($resinfo[count]);
echo "<TD>$total of those contain resources.</TD>";
echo "</TR><TR ALIGN=CENTER>";
$res = $db->Execute("SELECT COUNT(*) as count FROM $dbtables[hexes] WHERE terrain = 'o' OR terrain = 'l'");
$resinfo = $res->fields;
$total = NUMBER($resinfo[count]);
echo "<TD>$total are Ocean or Lakes.</TD>";
$res = $db->Execute("SELECT COUNT(*) as count FROM $dbtables[hexes] WHERE terrain = 'sw'");
$resinfo = $res->fields;
$total = NUMBER($resinfo[count]);
echo "<TD>$total are Swamps.</TD>";
echo "</TR><TR ALIGN=CENTER>";
$res = $db->Execute("SELECT COUNT(*) as count FROM $dbtables[hexes] WHERE terrain = 'pr' OR terrain = 'gh'");
$resinfo = $res->fields;
$total = NUMBER($resinfo[count]);
echo "<TD>$total are Prairie or Grassy Hills.</TD>";
$res = $db->Execute("SELECT COUNT(*) as count FROM $dbtables[hexes] WHERE terrain = 'cf' OR terrain = 'df' OR terrain = 'jg'");
$resinfo = $res->fields;
$total = NUMBER($resinfo[count]);
echo "<TD>$total are Coniferous, Deciduous, or Jungle.</TD>";
echo "</TR><TR ALIGN=CENTER>";
$res = $db->Execute("SELECT COUNT(*) as count from $dbtables[hexes] WHERE terrain = 'ch' OR terrain = 'dh'");
$resinfo = $res->fields;
$total = NUMBER($resinfo[count]);
echo "<TD>$total are Coniferous hills, or Deciduous hills.</TD>";
$res = $db->Execute("SELECT COUNT(*) as count from $dbtables[hexes] WHERE terrain = 'hsm' OR terrain = 'lcm'");
$resinfo = $res->fields;
$total = NUMBER($resinfo[count]);
echo "<TD>$total are High Snowy Mountains or Low Coniferous Mountains.</TD>";
echo "</TR><TR ALIGN=CENTER>";
$res = $db->Execute("SELECT COUNT(*) as count from $dbtables[hexes] WHERE res_type = 'lead'");
$resinfo = $res->fields;
$total = NUMBER($resinfo[count]);
echo "<TD>There are $total Lead resources.</TD>";
$res = $db->Execute("SELECT COUNT(*) as count from $dbtables[hexes] WHERE res_type = 'salt'");
$resinfo = $res->fields;
$total = NUMBER($resinfo[count]);
echo "<TD>There are $total Salt resources.</TD>";
echo "</TR><TR ALIGN=CENTER>";
$res = $db->Execute("SELECT COUNT(*) as count from $dbtables[hexes] WHERE res_type = 'tin'");
$resinfo = $res->fields;
echo "<TD>There are $resinfo[count] Tin resources.</TD>";
$res = $db->Execute("SELECT COUNT(*) as count from $dbtables[hexes] WHERE res_type = 'zinc'");
$resinfo = $res->fields;
echo "<TD>There are $resinfo[count] Zinc resources.</TD>";
echo "</TR><TR ALIGN=CENTER>";
$res = $db->Execute("SELECT COUNT(*) as count from $dbtables[hexes] WHERE res_type = 'coal'");
$resinfo = $res->fields;
echo "<TD>There are $resinfo[count] Coal resources.</TD>";
$res = $db->Execute("SELECT COUNT(*) as count from $dbtables[hexes] WHERE res_type = 'copper'");
$resinfo = $res->fields;
echo "<TD>There are $resinfo[count] Copper resources.</TD>";
echo "</TR><TR ALIGN=CENTER>";
$res = $db->Execute("SELECT COUNT(*) as count from $dbtables[hexes] WHERE res_type = 'iron'");
$resinfo = $res->fields;
echo "<TD>There are $resinfo[count] Iron resources.</TD>";
$res = $db->Execute("SELECT COUNT(*) as count from $dbtables[hexes] WHERE res_type = 'silver'");
$resinfo = $res->fields;
echo "<TD>There are $resinfo[count] Silver resources.</TD>";
echo "</TR><TR ALIGN=CENTER>";
$res = $db->Execute("SELECT COUNT(*) as count from $dbtables[hexes] WHERE res_type = 'gold'");
$resinfo = $res->fields;
$total = NUMBER($resinfo[count]);
echo "<TD>There are $total Gold resources.</TD>";
$res = $db->Execute("SELECT COUNT(*) as count from $dbtables[hexes] WHERE res_type = 'gems'");
$resinfo = $res->fields;
$total = NUMBER($resinfo[count]);
echo "<TD>There are $total Gem resources.</TD>";




echo "</TR>";
$res = $db->Execute("SELECT COUNT(*) as count from $dbtables[mapping] WHERE `admin_0000` = '1'");
$row = $res->fields;
$percent = round($row[count]/37500, 2);
$percent = $percent * 100;
$total = NUMBER($row['count']);
echo "<TR ALIGN=CENTER><TD COLSPAN=2>And $total ($percent%) of them have been explored to date.</TD><TR></TABLE><BR></CENTER>";


navbar_help();

if( $_SESSION[clanid] )
{
    TEXT_GOTOMAIN();
}
echo "</CENTER>";

page_footer();

?>
