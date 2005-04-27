<?php
 $time_start = getmicrotime();
require_once("../config.php");
#include("../header.php");  Huh?? what do we need to include this for??
include("game_time.php");
connectdb();
  $res = $db->Execute("SELECT * FROM $dbtables[tribes]");
   db_op_result($res,__LINE__,__FILE__);
  while(!$res->EOF)
   {
    $tribe = $res->fields;
$act = $db->Execute("SELECT * FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' AND skill_abbr = 'hunt' LIMIT 1");
 db_op_result($act,__LINE__,__FILE__);
$act_do = $act->fields;

if($act_do[skill_abbr] == 'hunt'){

$season = $db->Execute("SELECT count from $dbtables[game_date] WHERE type = 'season'");
 db_op_result($season,__LINE__,__FILE__);
$seasoninfo = $season->fields;
$seasonbonus = (6 - $seasoninfo[count]);
$weather = $db->Execute("SELECT * FROM $dbtables[game_date] WHERE type = 'weather'");
  db_op_result($weather,__LINE__,__FILE__);
$weatherinfo = $weather->fields;
$hex = $db->Execute("SELECT * from $dbtables[hexes] WHERE hex_id = $tribe[hex_id]");
 db_op_result($hex,__LINE__,__FILE__);
$hexinfo = $hex->fields;

if($hexinfo[terrain] == 'pr'){
$terrain_bonus = 3;
}
elseif($hexinfo[terrain] == 'df'){
$terrain_bonus = 7;
}
elseif($hexinfo[terrain] == 'cf'){
$terrain_bonus = 6;
}
elseif($hexinfo[terrain] == 'dh'){
$terrain_bonus = 5;
}
elseif($hexinfo[terrain] == 'ch'){
$terrain_bonus = 4;
}
elseif($hexinfo[terrain] == 'lcm'){
$terrain_bonus = 2;
}
elseif($hexinfo[terrain] == 'ljm'){
$terrain_bonus = 4;
}
elseif($hexinfo[terrain] == 'hsm'){
$terrain_bonus = 0;
}
elseif($hexinfo[terrain] == 'jh'){
$terrain_bonus = 8;
}
elseif($hexinfo[terrain] == 'jg'){
$terrain_bonus = 10;
}
elseif($hexinfo[terrain] == 'de'){
$terrain_bonus = 0;
}
elseif($hexinfo[terrain] == 'tu'){
$terrain_bonus = 1;
}
elseif($hexinfo[terrain] == 'sw'){
$terrain_bonus = 0;
}
elseif($hexinfo[terrain] == 'gh'){
$terrain_bonus = 5;
}
else {
$terrain_bonus == 0;
}

$hunter = $db->Execute("SELECT * FROM $dbtables[skills] WHERE abbr = 'hunt' AND tribeid = '$tribe[tribeid]'");
db_op_result($hunter,__LINE__,__FILE__);
$hunterinfo = $hunter->fields;

$trap = $db->Execute("SELECT * FROM $dbtables[products] WHERE tribeid = '$tribe[tribeid]' and proper = 'Traps' and amount > 0 OR tribeid = '$tribe[tribeid]' and proper = 'Snares' AND amount > 0");
db_op_result($trap,__LINE__,__FILE__);

$imp = $db->Execute("SELECT * FROM $dbtables[weapons] WHERE hunting > 0 AND dbname != 'traps' AND dbname != 'snares' order by hunting desc");
db_op_result($imp,__LINE__,__FILE__);
$weap_bonus = 0;
$traps_used = 0;

while(!$trap->EOF){
$trapinfo = $trap->fields;
$traps = $act_do[actives] * 5;
if($traps > $trapinfo[amount]){
$traps_used += $trapinfo[amount];
}
else{
$traps_used += $traps;
}
$trap->MoveNext();
}
$weap_bonus += $traps_used * .1;


$weapons = 0;
$weapons_needed = $act_do[actives] - ($traps_used/5);
while($weapons_needed > 0){
        while(!$imp->EOF){
        $impinfo = $imp->fields;
        $wepstock = $db->Execute("SELECT * FROM $dbtables[products] WHERE tribeid = '$tribe[tribeid]' and proper = '$impinfo[proper]' and amount > 0");
          db_op_result($wepstock,__LINE__,__FILE__);
        $wepinfo = $wepstock->fields;
        if($wepinfo[amount] > $weapons_needed){
        $wepinfo[amount] = $weapons_needed;
        }
        $weapons_needed -= $wepinfo[amount];
        $weap_bonus += $wepinfo[amount] * $impinfo[hunting];
        $imp->MoveNext();
        }
$weapons_needed = 0;
}

if($weap_bonus < 0){
$weap_bonus = 0;
}
$hunter_ability = round($hunterinfo[level] * $weap_bonus);
$hunter_produce = round($act_do[actives] + $hunter_ability);
if($seasonbonus == '0'){
$seasonbonus = 1;
}
if($terrain_bonus == '0'){
$terrain_bonus = 1;
}
$hunter_produce = round(((($hunter_produce + $bonuses + $terrain_bonus) * $seasonbonus) * $tribe[morale]) - $weatherinfo[count]);

if($hunter_produce < 0){
$hunter_produce = 0;
}
if($hexinfo[game] < $hunter_produce){
$hunter_produce = $hexinfo[game];
}
$query = $db->Execute("UPDATE $dbtables[hexes] set game = game - $hunter_produce WHERE hex_id = '$hexinfo[hex_id]'");
db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[products] set amount = amount + '$hunter_produce' WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'provs'");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("DELETE FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' AND product = 'provs'");
db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Hunting: $hunter_produce Provisions hunted by $act_do[actives] hunters.')");
db_op_result($query,__LINE__,__FILE__);
if($hexinfo[game] <= $hunter_produce){
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Hunting: Hunters report very little game in the area now.')");
db_op_result($query,__LINE__,__FILE__);
}

}
$res->MoveNext();
}

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
