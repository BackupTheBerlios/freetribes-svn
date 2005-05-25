<?php
$pos = (strpos($_SERVER['PHP_SELF'], "/furrier.php"));
if ($pos !== false)
{
    die("You cannot access this page directly!");
}

  $res = $db->Execute("SELECT * FROM $dbtables[tribes]");
   db_op_result($res,__LINE__,__FILE__);
  while(!$res->EOF)
   {
    $tribe = $res->fields;
$act = $db->Execute("SELECT * FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]'");
  db_op_result($act,__LINE__,__FILE__);
while(!$act->EOF){
$act_do = $act->fields;

if($act_do[skill_abbr] == 'fur'){

$fur = $db->Execute("SELECT * FROM $dbtables[skills] WHERE abbr = 'fur' AND tribeid = '$tribe[tribeid]'");
  db_op_result($fur,__LINE__,__FILE__);
$weap = $db->Execute("SELECT * FROM $dbtables[products] WHERE tribeid = '$tribe[goods_tribe]' AND weapon = 'Y'");
  db_op_result($weap,__LINE__,__FILE__);
$trap = $db->Execute("SELECT * FROM $dbtables[products] WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'traps'");
 db_op_result($trap,__LINE__,__FILE__);
$hunterinfo = $fur->fields;

if(!$trap->EOF){
$trapinfo = $trap->fields;
$weap_bonus = $trapinfo[amount];
}
while(!$weap->EOF){
$weapinfo = $weap->fields;
$weap_bonus = $weap_bonus + round($weapinfo[amount]/2);
$weap->MoveNext();
}
$hex = $db->Execute("SELECT * FROM $dbtables[hexes] WHERE hex_id = '$tribe[hex_id]'");
  db_op_result($hex,__LINE__,__FILE__);
$hexinfo = $hex->fields;

$hunter_ability = round($hunterinfo[level] * $weap_bonus);
if($hunter_ability > $hexinfo[game]){
$hunter_ability = $hexinfo[game];
}
$provs_produce = round(($act_do[actives] + $hunter_ability) * .033);
$skins_produce = round(($act_do[actives] + $hunter_ability) * .015);
$furs_produce = round(($act_do[actives] + $hunter_ability) * .017);

$result = $db->Execute("DELETE FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' AND skill_abbr = 'fur'");
db_op_result($result,__LINE__,__FILE__);
$result = $db->Execute("UPDATE $dbtables[products] set amount = amount + '$provs_produce' WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'provs'");
  db_op_result($result,__LINE__,__FILE__);
$result = $db->Execute("UPDATE $dbtables[products] set amount = amount + '$skins_produce' WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'skins'");
  db_op_result($result,__LINE__,__FILE__);
$result = $db->Execute("UPDATE $dbtables[products] set amount = amount + '$furs_produce' WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'furs'");
   db_op_result($result,__LINE__,__FILE__);
$result = $db->Execute("UPDATE $dbtables[hexes] set game = game - $hunter_ability WHERE hex_id = '$tribe[hex_id]'");
  db_op_result($result,__LINE__,__FILE__);
$result = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Furrier: $provs_produce Provisions, $skins_produce Skins, $furs_produce Furs Furried.')");
    db_op_result($result,__LINE__,__FILE__);
}


$act->MoveNext();
}
$res->MoveNext();
}

?>
