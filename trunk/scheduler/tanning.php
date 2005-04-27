<?php
require_once("../config.php");
$time_start = getmicrotime();
include("game_time.php");
connectdb();
  $res = $db->Execute("SELECT * FROM $dbtables[tribes]");
   db_op_result($res,__LINE__,__FILE__);
  while(!$res->EOF)
   {
    $tribe = $res->fields;
$act = $db->Execute("SELECT * FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' and skill_abbr = 'tan'");
  db_op_result($act,__LINE__,__FILE__);
$cnt = $db->Execute("SELECT actives FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' AND skill_abbr = 'tan'");
 db_op_result($cnt,__LINE__,__FILE__);
$tanners = 0;
while(!$cnt->EOF){
$count = $cnt->fields;
$tanners += $count[actives];
$cnt->MoveNext();
}

while(!$act->EOF){
$act_do = $act->fields;

if($act_do[skill_abbr] == 'tan'){
$tanskl = $db->Execute("SELECT * FROM $dbtables[skills] WHERE tribeid = '$tribe[tribeid]' AND abbr = 'tan' LIMIT 1");
db_op_result($tanskl,__LINE__,__FILE__);
$skill = $tanskl->fields;
$act_do[actives] == $tanners;
$curtanners = $act_do[actives];
$maxtanners = $act_do[actives];
if($skill[level] < 10){
$maxtanners = $skill[level] * 10;
}
if($maxtanners < $curtanners){
$curtanners = $maxtanners;
}
$skn = $db->Execute("SELECT * FROM $dbtables[products] WHERE long_name = 'skins' AND tribeid = '$tribe[goods_tribe]'");
db_op_result($skn,__LINE__,__FILE__);
$skininfo = $skn->fields;
$skins = $skininfo[amount];
$brk = $db->Execute("SELECT * FROM $dbtables[products] WHERE long_name = 'bark' AND tribeid = '$tribe[goods_tribe]'");
 db_op_result($brk,__LINE__,__FILE__);
$barkinfo = $brk->fields;
$bark = $barkinfo[amount];
$startskins = $skins;
$startbark = $bark;
$leather = 0;
while($curtanners > 0 & $bark > 9 & $skins > 3){
$leather += 4;
$curtanners -= 1;
$bark -= 10;
$skins -= 4;
}
$deltaskins = $startskins - $skins;
$fur = $db->Execute("SELECT * FROM $dbtables[products] WHERE long_name = 'furs' AND tribeid = '$tribe[goods_tribe]'");
 db_op_result($fur,__LINE__,__FILE__);
$furinfo = $fur->fields;
$furs = $furinfo[amount];
$startfurs = $furs;
while($curtanners > 0 & $bark > 9 & $furs > 3){
$leather += 4;
$curtanners -= 1;
$bark -= 10;
$furs -= 4;
}
$deltafurs = $startfurs - $furs;
$deltabark = $startbark - $bark;
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount + $leather WHERE proper = 'Leather' AND tribeid = '$tribe[goods_tribe]'");
db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[products] SET amount = $skins WHERE proper = 'Skins' AND tribeid = '$tribe[goods_tribe]'");
db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[products] SET amount = $furs WHERE proper = 'Furs' AND tribeid = '$tribe[goods_tribe]'");
db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[products] SET amount = $bark WHERE proper = 'Bark' AND tribeid = '$tribe[goods_tribe]'");
db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Tanning: $leather leather tanned using $deltabark bark, $deltaskins skins, $deltafurs furs.')");
db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("DELETE FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' AND skill_abbr = 'tan'");
db_op_result($query,__LINE__,__FILE__);
}


$act->MoveNext();
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
