<?php
session_start();
header("Cache-control: private");
include("config.php");
include("game_time.php");

connectdb();

page_header("TribeStrive Spy Results Page");

//-------------------------------------------------------------------------------------------------
$clanid = $_SESSION['clanid'];
$curr_unit = $_REQUEST['id'];
$db->Execute("UPDATE $dbtables[tribes] set curam = curam + 25 WHERE tribeid = '$curr_unit'");
$ch = $db->Execute("SELECT * FROM $dbtables[chiefs] WHERE clanid = '$clanid'");
$chiefinfo = $ch->fields;
$res = $db->Execute("SELECT clanname FROM $dbtables[clans] WHERE clanid = '$clanid'");
$resclan = $res->fields;
$_SESSION['clanname'] = $resclan[clanname];
$restrib = $db->Execute("SELECT * FROM $dbtables[tribes] WHERE clanid = '$clanid' AND tribeid = '$_GET[id]'");
$tribeinfo = $restrib->fields;
if(ISSET($_GET[id]) & ISSET($tribeinfo[tribeid])){
$_SESSION[current_unit] = $_GET[id];
}
if(!ISSET($tribeinfo[tribeid])){
$res2 = $db->Execute("SELECT * FROM $dbtables[tribes] WHERE tribeid = '$curr_unit' ");
$tribeinfo = $res2->fields;
}
$_SESSION['hex_id'] = $tribeinfo[hex_id];

$spy = $db->Execute("SELECT * FROM $dbtables[skills] WHERE abbr = 'spy' AND tribeid = '$_SESSION[current_unit]'");
$spy_chance = $spy->fields;
$security = $db->Execute("SELECT * FROM $dbtables[skills] WHERE abbr = 'sec' AND tribeid = '$curr_unit'");
$sec_force = $security->fields;
$guards = $sec_force[level] * 10;
$spies = $spy_chance[level] * 10;
$spy_chance[level] = $spy_chance[level] - $sec_force[level];
$catch_chance = rand(1,100) + $guards;
$away_chance = rand(1,100) + $spies;

if($catch_chance > ($away_chance + 20)){
           $db->Execute("INSERT INTO $dbtables[logs] "
                       ."VALUES("
                       ."'',"
                       ."'$month[count]',"
                       ."'$year[count]',"
                       ."'$tribeinfo[clanid]',"
                       ."'$tribeinfo[tribeid]',"
                       ."'SPY',"
                       ."'$stamp',"
                       ."'Security: $tribeinfo[tribeid] has encountered enemy spying activity from $_SESSION[current_unit].')");

}
elseif($catch_chance < ($away_chance - 20)){
           $db->Execute("INSERT INTO $dbtables[logs] "
                       ."VALUES("
                       ."'',"
                       ."'$month[count]',"
                       ."'$year[count]',"
                       ."'$tribeinfo[clanid]',"
                       ."'$tribeinfo[tribeid]',"
                       ."'SPY',"
                       ."'$stamp',"
                       ."'Security: $tribeinfo[tribeid] has detected enemy spying activity but could not determine from whom.')");

}


$res4 = $db->Execute("SELECT * FROM $dbtables[hexes] WHERE hex_id = '$tribeinfo[hex_id]'");
$hexinfo = $res4->fields;

$stamp = date("Y-m-d H:i:s");

echo "<TABLE BORDER=0 CELLPADDING=0 align=center width=\"100%\">";
echo "<TR>";
echo "<TD ALIGN=CENTER><FONT SIZE=+2 COLOR=white> Chief " . $_SESSION['chiefname'];

if(!$_SESSION[clanname] == ''){
echo " of the " . $_SESSION['clanname'];
}
echo "</FONT></TD></TR><TR>";
echo "<TD ALIGN=CENTER><FONT SIZE=+1 COLOR=white> Spying on tribe " . $curr_unit . "</FONT></TD></TR>";
echo "<TR><TD ALIGN=CENTER>";
echo "<hr width=\"80%\" align=center>";
echo "<TABLE BORDER=0 CELLPADDING=0 WIDTH=\"100%\"><TR><TD WIDTH =\"33%\">";


echo "<TABLE BORDER=0 CELLPADDING=0 WIDTH=\"100%\"><TR><TD>";
if($hexinfo[terrain] == "pr"){
$hextype = "Prairie";
}
elseif($hexinfo[terrain] == "df"){
$hextype = "Deciduous Forest";
}
elseif($hexinfo[terrain] == "dh"){
$hextype = "Deciduous Hills";
}
elseif($hexinfo[terrain] == "cf"){
$hextype = "Coniferous Forest";
}
elseif($hexinfo[terrain] == "ch"){
$hextype = "Coniferous Hills";
}
elseif($hexinfo[terrain] == "jg"){
$hextype = "Jungle";
}
elseif($hexinfo[terrain] == "jh"){
$hextype = "Jungle Hills";
}
elseif($hexinfo[terrain] == "lcm"){
$hextype = "Low Coniferous Mountains";
}
elseif($hexinfo[terrain] == "ljm"){
$hextype = "Low Jungle Mountains";
}
elseif($hexinfo[terrain] == "hsm"){
$hextype = "High Snowy Mountains";
}
elseif($hexinfo[terrain] == "gh"){
$hextype = "Grassy Hills";
}
elseif($hexinfo[terrain] == "de"){
$hextype = "Desert";
}
elseif($hexinfo[terrain] == "tu"){
$hextype = "Tundra";
}
elseif($hexinfo[terrain] == "o"){
$hextype = "Ocean";
}
elseif($hexinfo[terrain] == "l"){
$hextype = "Lake";
}
elseif($hexinfo[terrain] == "sw"){
$hextype = "Swamps";
}
echo "<CENTER><FONT COLOR=white>$hextype</FONT></CENTER></TD></TR>";

echo "<TR><TD>";
if($hexinfo[res_type] == ''){
$reshex = 'No Minerals';
}
elseif($hexinfo[res_type] == 'silver'){
$reshex = 'Silver';
}
elseif($hexinfo[res_type] == 'lead'){
$reshex = 'Lead Ore';
}
elseif($hexinfo[res_type] == 'iron'){
$reshex = 'Iron Ore';
}
elseif($hexinfo[res_type] == 'copper'){
$reshex = 'Copper Ore';
}
elseif($hexinfo[res_type] == 'tin'){
$reshex = 'Tin Ore';
}
elseif($hexinfo[res_type] == 'zinc'){
$reshex = 'Zinc Ore';
}
elseif($hexinfo[res_type] == 'salt'){
$reshex = 'Salt';
}
elseif($hexinfo[res_type] == 'gold'){
$reshex = 'Gold';
}
elseif($hexinfo[res_type] == 'gems'){
$reshex = 'Gems';
}
elseif($hexinfo[res_type] == 'coal'){
$reshex = 'Coal';
}
echo "<CENTER><FONT COLOR=white>$reshex</FONT></CENTER></TD></TR>";

echo "</TABLE>";



echo "</TD><TD ALIGN=CENTER><A HREF=combat.php?target=$curr_unit>Attack!</A></TD><TD>";
echo "<TABLE BORDER=0 CELLPADDING=0 WIDTH=\"100%\"><TR><TD>"; 
$gtmonth = $db->Execute("SELECT * FROM $dbtables[game_date] WHERE type = 'month'");
$gm = $gtmonth->fields;
$gtyear = $db->Execute("SELECT * FROM $dbtables[game_date] WHERE type = 'year'");
$gy = $gtyear->fields;
$gtseason = $db->Execute("SELECT * FROM $dbtables[game_date] WHERE type = 'season'");
$gs = $gtseason->fields;
$gtweather = $db->Execute("SELECT * FROM $dbtables[weather] WHERE current_type = 'Y'");
$gw = $gtweather->fields;

if($gs[count] == '1'){
 $season = "Spring";
 }
elseif($gs[count] == '2'){
 $season = "Summer";
 }
elseif($gs[count] == '3'){
 $season = "Autumn";
 }
else{
 $season = "Winter";
 }
echo "<CENTER><FONT COLOR=white> $gm[count] / $gy[count] </FONT></CENTER></TD></TR><TR><TD>";
echo "<CENTER><FONT COLOR=white>$season (" . $gw[long_name] . ")</TR></FONT></CENTER></TABLE>";
echo "</TD></TR></TD></TR></TABLE>";


echo "<TABLE BORDER=1 CELLPADDING=0 WIDTH=\"100%\" VALIGN=TOP><TR><TD WIDTH=\"10%\" VALIGN=TOP style=\"background-image: url(images/parchment_bg.png);\">"; // Table3

/////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////Navigation Bar////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////

echo "<TABLE BORDER=0 CELLPADDING=0 WIDTH=\"5%\">"; // Table4
echo "<TR><TD ALIGN=LEFT><B><FONT COLOR=#936f19>Navigation</FONT></B></TD></TR>";
echo "<TR><TD ALIGN=LEFT><A HREF=mapping.php>Maps</A></TD></TR>";
// echo "<TR><TD ALIGN=LEFT><FONT COLOR=black>Religion</FONT></TD></TR>";
echo "<TR><TD ALIGN=LEFT><FONT COLOR=black>Help</FONT></TD></TR>";
echo "<TR><TD ALIGN=LEFT><A HREF=transfer.php>Transfers</A></TD></TR>";
echo "<TR><TD ALIGN=LEFT><A HREF=activities.php>Activities</A></TD></TR>";
// echo "<TR><TD ALIGN=LEFT><FONT COLOR=black>Scouts</FONT></TD></TR>";
echo "<TR><TD ALIGN=LEFT><A HREF=report.php>Reports</A></TD></TR>";
echo "<TR><TD ALIGN=LEFT><A HREF=newtribe.php>Subtribes</A></TD></TR>";
echo "<TR><TD ALIGN=LEFT><A HREF=mailto.php>Diplomacy</A></TD></TR>";
echo "<TR><TD ALIGN=LEFT><A HREF=heraldry.php>Heraldry</A></TD></TR>";
echo "<TR><TD ALIGN=LEFT><A HREF=options.php>Options</A></TD></TR>";
echo "<TR><TD ALIGN=LEFT><A HREF=logout.php>Logout</A></TD></TR>";
if($chiefinfo[admin] == 'Y'){
echo "<TR><TD>&nbsp;</TD></TR>";
echo "<TR><TD ALIGN=LEFT><A HREF=admin.php>Admin</A></TD></TR>";
}
echo "<TR><TD>&nbsp;</TD></TR></TABLE>";


echo "</TD><TD bgcolor=#997637>";



///////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////Center Tables///////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////



echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=\"100%\"><TR><TD>"; // Table 5
///////////////////////////////above is the big center table////////////////////////////

echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=\"100%\"><TR><TD ALIGN=RIGHT>";
echo "<FONT COLOR=white>Goods Tribe: ";

$goods_rand = rand(1,100);
if($spy_chance[level] >= $goods_rand){
echo "$tribeinfo[goods_tribe]</TD></TR></TABLE>";
}
else{
echo "Unknown</TD></TR></TABLE>";
}

/////////////////////////////////////first table in the center done////////////////////
echo "</TD></TR><TR><TD>";
//////////////////////////////////////////////////////////////////////////////////////
$struct = $db->Execute("SELECT * FROM $dbtables[structures] WHERE tribeid = '$tribeinfo[tribeid]' AND hex_id = '$tribeinfo[hex_id]'");
if(!$struct->EOF){
echo "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=\"100%\"><TR>";
echo "<TD><FONT COLOR=white><B>Buildings:</B></FONT></TD></TR><TR>";
while(!$struct->EOF){
$i = 0;
while($i < 6){
$structinfo = $struct->fields;
if(!$structinfo[subunit] == ''){
$building_chance = rand(1,100);
if($spy_chance[level] >= $building_chance){
echo "<TD>$structinfo[proper] ($structinfo[number])</TD>";
}
else{
echo "<TD>Unknown</TD>";
}
}
elseif($structinfo[proper] == 'Moat'){
$building_chance = rand(1,100);
if($spy_chance[level] >= $building_chance){
echo "<TD>$structinfo[proper] ($structinfo[struct_pts] sq. yds)</TD>";
}
else{
echo "<TD>Unknown</TD>";
}
}
else{
$building_chance = rand(1,100);
if($spy_chance[level] >= $building_chance){
echo "<TD>$structinfo[proper]</TD>";
}
else{
echo "<TD>Unknown</TD>";
}
}
$struct->MoveNext();
$i++;
}
echo "</TR>";
}
echo "</TR></TABLE>";
echo "</TD></TR></TR><TD>";
}
//////////////////////////////////////////////////////////////////////////////////



echo "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=\"100%\"><TR>";

echo "<TD><FONT COLOR=white><B>Population:</B></FONT></TD></TR><TR>";
echo "<TD><FONT COLOR=white>Total:</FONT></TD><TD>";

$pop_chance = rand(1,100);
if($spy_chance[level] >= $pop_chance){
echo "$tribeinfo[totalpop]</TD>";
}
else{
echo "Unknown</TD>";
}
if($tribeinfo[warpop] > 0){
echo "<TD><FONT COLOR=white>Warriors:</FONT></TD><TD> ";

$war_chance = rand(1,100);
if($spy_chance[level] >= $war_chance){
echo "$tribeinfo[warpop]</TD>";
}
else{
echo "Unknown</TD>";
}
}

if($tribeinfo[activepop] > 0){

echo "<TD><FONT COLOR=white>Actives:</FONT></TD><TD> ";

$active_chance = rand(1,100);
if($spy_chance[level] >= $active_chance){
echo "$tribeinfo[activepop]</TD>";
}
else{
echo "Unknown</TD>";
}
}

if($tribeinfo[inactivepop] > 0){
echo "<TD><FONT COLOR=white>Inactives:</FONT></TD><TD> ";

$inactive_chance = rand(1,100);
if($spy_chance[level] >= $inactive_chance){
echo "$tribeinfo[inactivepop]</TD>";
}
else{
echo "Unknown</TD>";
}
}

if($tribeinfo[slavepop] > 0){
echo "<TD><FONT COLOR=white>Slaves:</FONT></TD><TD> ";

$slave_chance = rand(1,100);
if($spy_chance[level] >= $slave_chance){
echo "$tribeinfo[slavepop]</TD>";
}
else{
echo "Unknown</TD>";
}
}

if($tribeinfo[specialpop] > 0){
echo "<TD><FONT COLOR=white>Special:</FONT></TD><TD> ";
$special_chance = rand(1,100);
if($spy_chance[level] >= $special_chance){
echo "$tribeinfo[specialpop]</TD>";
}
else{
echo "Unknown</TD>";
}
}

echo "</TR></TABLE>";

//////////////////////////////////second table in the center done//////////////////////
echo "</TD></TR><TR><TD>";
//////////////////////////////////////////////////////////////////////////////////////


echo "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=\"100%\"><TR><TD>";
echo "<FONT COLOR=white>Resources:</FONT></TD></TR><TR>";
$resource = $db->Execute("SELECT long_name, amount FROM $dbtables[resources] WHERE tribeid = '$tribeinfo[tribeid]' AND amount > '0'");

while(!$resource->EOF){
$unit_res = $resource->fields;
$i = 0;
while($i < 6){
$unit_res = $resource->fields;
if($unit_res[amount] > 0){
$res_chance = rand(1,100);
if($spy_chance[level] >= $res_chance){
echo "<TD>$unit_res[long_name]: " . $unit_res[amount] . "</TD>";
}
else{
echo "<TD>Unknown</TD>";
}
}
$i++;
$resource->MoveNext();
}
echo "</TR><TR>";
}

echo "</TR></TABLE><TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=100%>";
echo "<FONT COLOR=white>Stores:</FONT></TD></TR><TR>";

$production = $db->Execute("SELECT proper,long_name, amount FROM $dbtables[products] WHERE tribeid = '$tribeinfo[tribeid]' AND amount > '0'");

while(!$production->EOF){
$unit_prod = $production->fields;
$i = 0;
while($i < 6){
$unit_prod = $production->fields;
if($unit_prod[amount] > 0){
$prod_chance = rand(1,100);
if($spy_chance[level] >= $prod_chance){
echo "<TD>$unit_prod[proper]: " . $unit_prod[amount] . "</TD>";
}
else{
echo "<TD>Unknown</TD>";
}
}
$i++;
$production->MoveNext();
}
echo "</TR><TR>";
}

echo "</TR></TABLE>";
echo "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=100%>";
$animals = $db->Execute("SELECT * FROM $dbtables[livestock] WHERE tribeid = '$tribeinfo[tribeid]' AND amount > '0'");

if($animals){
echo "<TD><FONT COLOR=white>Livestock:</FONT></TD></TR><TR>";
}
while(!$animals->EOF){
$unit_liv = $animals->fields;
$i = 0;
while($i < 6){
$unit_liv = $animals->fields;
if($unit_liv[amount] > 0){
$liv_chance = rand(1,100);
if($spy_chance[level] >= $liv_chance){
echo "<TD>$unit_liv[type]: " . $unit_liv[amount] . "</TD>";
}
else {
echo "<TD>Unknown</TD>";
}
}
$i++;
$animals->MoveNext();
}
echo "</TR><TR>";
}
echo "</TR></TABLE>";


////////////////////////////////////third table in the center done/////////////////////////////
echo "</TD></TR><TR><TD>";
//////////////////////////////////////////////////////////////////////////////////////////////
echo "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=\"100%\">";  
echo "<TR><TD>";
echo "&nbsp;</TD></TR><TR><TD>";
echo "<FONT COLOR=white><B>Skills:</B></FONT></TD>";

echo "</TR><TR>";

$skill = $db->Execute("SELECT long_name, level FROM $dbtables[skills] WHERE tribeid = '$tribeinfo[tribeid]' AND level > '0'");

while(!$skill->EOF){
$skillsinfo = $skill->fields;
$i = 0;
while($i < 6){
$skillsinfo = $skill->fields;
if($skillsinfo[level] > 0){
$skill_chance = rand(1,100);
if($spy_chance[level] >= $skill_chance){
echo "<TD>$skillsinfo[long_name]: " . $skillsinfo[level] . "</TD>";
}
else{
echo "<TD>Unknown</TD>";
}
}
$i++;
$skill->MoveNext();
}
echo "</TR>";
}


echo "</TD></TR></TABLE>";

/////////////////////////////////////////////////fourth table in the center done///////////////////////
echo "</TD></TR>";
////////////////////////////////////////////////table in the center done///////////////////////////////
echo "</TABLE></TD>";
/////////////////////////////////////////////////////////////////////////////////////////////////////

echo "<TD VALIGN=TOP HEIGHT=100% style=\"background-image: url(images/parchment_bg.png);\"><TABLE BORDER=1 CELLSPACING=0 CELLPADDING=0 WIDTH=100% HEIGHT=100% VALIGN=TOP><TR WIDTH=\"100%\" VALIGN=top><TD WIDTH=\"100%\" align=top><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=\"100%\" ALIGN=TOP>";
echo "<TR VALIGN=TOP><TD VALIGN=TOP><FONT COLOR=#936f19>Units:</FONT></TD></TR>";
$clanid = $_SESSION['clanid'];
$res = $db->Execute("SELECT tribeid FROM $dbtables[tribes] WHERE clanid = '$clanid' ORDER BY tribeid ASC");

if(!$res){
echo "<TR><TD>None</TD></TR>";
}
else{
while(!$res->EOF){
$row = $res->fields;
echo "<TR><TD><a href=main.php?id=$row[tribeid]>" . $row[tribeid] . "</A></TD></TR>";
$res->MoveNext();
}
}

echo "<TR><TD>&nbsp;</TD></TR>";
echo "<TR><TD><FONT COLOR=#936f19>Nearby:</FONT></TD></TR>";
$res = $db->Execute("SELECT tribeid FROM $dbtables[tribes] WHERE hex_id = '$tribeinfo[hex_id]' AND clanid <> '$_SESSION[clanid]'");
$row = $res->fields;
if(!$row[tribeid]){
echo "<TR><TD><FONT COLOR=black>None</FONT></TD></TR>";
}
else{
while(!$res->EOF){
$row = $res->fields;
echo "<TR><TD><a href=spy.php?id=$row[tribeid]>" . $row[tribeid] . "</A></TD></TR>";
$res->MoveNext();
}
}
echo "</TD></TR></TABLE></TD></TR></TABLE>";





echo "</TD></TR></TABLE></TD></TR></TABLE>\n";

page_footer();

?>
