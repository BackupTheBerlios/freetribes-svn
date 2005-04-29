<?php
require_once("../config.php");
$time_start = getmicrotime();
include("game_time.php");
connectdb();
$clan = array();
$tribe = array();
$clan = $db->Execute("SELECT * FROM $dbtables[clans]");
 db_op_result($clan,__LINE__,__FILE__);
while(!$clan->EOF){
$score = 0;
$mapscore = 0;
$garscore = 0;
$tribescore = 0;
$allyscore = 0;
$resscore = 0;
$prodscore = 0;
$livscore = 0;
$claninfo = $clan->fields;
$map = $db->Execute("SELECT COUNT(*) as mapped FROM $dbtables[mapping] WHERE `clanid_{$claninfo[clanid]}` > 0");
 db_op_result($map,__LINE__,__FILE__);
$mapinfo = $map->fields;
$mapscore = $mapinfo[mapped] * 1000;
$score += $mapscore;
$ally = $db->Execute("SELECT COUNT(*) as allies FROM $dbtables[alliances] "
                    ."WHERE offerer_id = '$claninfo[clanid]' "
                    ."AND accept = 'Y'"
                    ."OR receipt_id = '$claninfo[clanid]' "
                    ."AND accept = 'Y'");
 db_op_result($ally,__LINE__,__FILE__);
$allyinfo = $ally->fields;
$allyscore += $allyinfo[allies] * 10000;
$tribe = $db->Execute("SELECT * FROM $dbtables[tribes] WHERE clanid = '$claninfo[clanid]'");
  db_op_result($tribe,__LINE__,__FILE__);
while(!$tribe->EOF){
$tribeinfo = $tribe->fields;
$tribescore += 10000;
$score += $tribeinfo[activepop] * 1000;
$score += $tribeinfo[slavepop] * 1500;
$score += $tribeinfo[inactivepop] * 500;
$skill = $db->Execute("SELECT * FROM $dbtables[skills] WHERE tribeid = '$tribeinfo[tribeid]' AND level > 0");
db_op_result($skill,__LINE__,__FILE__);
while(!$skill->EOF){
$skillinfo = $skill->fields;
$score += $skillinfo[level] * 1000;
$skill->MoveNext();
}
$score += $tribescore;
$score += $allyscore;
$resources = $db->Execute("SELECT * FROM $dbtables[resources] WHERE tribeid = '$tribeinfo[tribeid]'");
 db_op_result($resources,__LINE__,__FILE__);
while(!$resources->EOF){
$resinfo = $resources->fields;
$price = $db->Execute("SELECT * FROM $dbtables[fair] WHERE proper_name = '$resinfo[long_name]' AND price_buy > 0");
 db_op_result($price,__LINE__,__FILE__);
$priceinfo = $price->fields;
$increase = $priceinfo[price_buy] * $resinfo[amount];
$resscore += $increase;
$resources->MoveNext();
}
$score += $resscore;
$products = $db->Execute("SELECT * FROM $dbtables[products] WHERE tribeid = '$tribeinfo[tribeid]' AND amount > 0");
db_op_result($products,__LINE__,__FILE__);
while(!$products->EOF){
$prodinfo = $products->fields;
$price = array();
$price = $db->Execute("SELECT * FROM $dbtables[fair] WHERE proper_name = '$prodinfo[proper]' AND price_buy > 0");
 db_op_result($price,__LINE__,__FILE__);
$priceinfo = $price->fields;
$increase = $priceinfo[price_buy] * $prodinfo[amount];
$prodscore += $increase;
$products->MoveNext();
}
$score += $prodscore;
$livestock = $db->Execute("SELECT * FROM $dbtables[products] WHERE tribeid = '$tribeinfo[tribeid]'");
 db_op_result($livestock,__LINE__,__FILE__);
while(!$livestock->EOF){
$livinfo = $livestock->fields;
$price = array();
$price = $db->Execute("SELECT * FROM $dbtables[fair] WHERE proper_name = '$livinfo[type]' AND price_buy > 0");
db_op_result($price,__LINE__,__FILE__);
$priceinfo = $price->fields;
$increase = $priceinfo[price_buy] * $prodinfo[amount];
$livscore += $increase;
$livestock->MoveNext();
}
$score += $livscore;
$garrisons = $db->Execute("SELECT * FROM $dbtables[garrisons] WHERE tribeid = '$tribeinfo[tribeid]'");
 db_op_result($garrisons,__LINE__,__FILE__);
while(!$garrisons->EOF){
$garinfo = $garrisons->fields;
$score += 1000;
$equip = $db->Execute("SELECT * FROM $dbtables[fair] WHERE proper_name = '$garinfo[weapon1]' OR proper_name = '$garinfo[weapon2]' OR proper_name = '$garinfo[head_armor]' OR proper_name = '$garinfo[torso_armor]' OR proper_name = '$garinfo[otorso_armor]' OR proper_name = '$garinfo[legs_armor]' OR proper_name = '$garinfo[shield]' OR proper_name = '$garinfo[horse_armor]'");
db_op_result($equip,__LINE__,__FILE__);
while(!$equip->EOF){
$equipinfo = $equip->fields;
$garscore += $equipinfo[price_buy] * $garinfo[force];
$equip->MoveNext();
}
$garscore += $garinfo[horses] * 5000;
$garrisons->MoveNext();
}
$score += $garscore;
$struct = $db->Execute("SELECT count(*) as count FROM $dbtables[structures] WHERE tribeid = '$tribeinfo[tribeid]' and complete = 'Y'");
 db_op_result($struct,__LINE__,__FILE__);
$structures = $struct->fields;
$structscore += $structures[count] * 10000;
$tribe->MoveNext();
}
$score += $structscore;
$score = $score/1000;
if($score < 0){
$score = 1;
}
$query = $db->Execute("UPDATE $dbtables[chiefs] SET score = '$score' WHERE clanid = '$claninfo[clanid]'");
 db_op_result($query,__LINE__,__FILE__);
$clan->MoveNext();
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
