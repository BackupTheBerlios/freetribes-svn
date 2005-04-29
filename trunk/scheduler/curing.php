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
$act = $db->Execute("SELECT * FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' AND skill_abbr = 'cur' LIMIT 1");
 db_op_result($act,__LINE__,__FILE__);
while(!$act->EOF){
$act_do = $act->fields;

if($act_do[skill_abbr] == 'cur'){
$curskl = $db->Execute("SELECT * FROM $dbtables[skills] WHERE tribeid = '$tribe[tribeid]' AND abbr = 'cur'");
db_op_result($curskl,__LINE__,__FILE__);
$skill = $curskl->fields;

$curcurers = $act_do[actives];
$maxcurers = $act_do[actives];
if($skill[level] < 10){
$maxcurers = $skill[level] * 10;
}
if($maxcurers < $curcurers){
$curcurers = $maxcurers;
}
$skn = $db->Execute("SELECT * FROM $dbtables[products] WHERE long_name = 'skins' AND tribeid = '$tribe[goods_tribe]'");
db_op_result($skn,__LINE__,__FILE__);
$skininfo = $skn->fields;
$skins = $skininfo[amount];
$gt = $db->Execute("SELECT * FROM $dbtables[products] WHERE long_name = 'gut' AND tribeid = '$tribe[goods_tribe]'");
 db_op_result($gt,__LINE__,__FILE__);
$gutinfo = $gt->fields;
$gut = $gutinfo[amount];
$startskins = $skins;
$startgut = $gut;
$leather = 0;
while($curcurers > 0 & $gut > 4 & $skins > 1){
$leather += 2;
$curcurers -= 1;
$gut -= 5;
$skins -= 2;
}
$deltaskins = $startskins - $skins;
$fur = $db->Execute("SELECT * FROM $dbtables[products] WHERE long_name = 'furs' AND tribeid = '$tribe[goods_tribe]'");
 db_op_result($fur,__LINE__,__FILE__);
$furinfo = $fur->fields;
$furs = $furinfo[amount];
$startfurs = $furs;
while($curcurers > 0 & $gut > 4 & $furs > 1){
$leather += 2;
$curcurers -= 1;
$gut -= 5;
$furs -= 2;
}
$deltafurs = $startfurs - $furs;
$deltagut = $startgut - $gut;
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount + $leather WHERE proper = 'Leather' AND tribeid = '$tribe[goods_tribe]'");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[products] SET amount = $skins WHERE proper = 'Skins' AND tribeid = '$tribe[goods_tribe]'");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[products] SET amount = $furs WHERE proper = 'Furs' AND tribeid = '$tribe[goods_tribe]'");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[products] SET amount = $gut WHERE proper = 'Gut' AND tribeid = '$tribe[goods_tribe]'");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Curing: $leather leather cured using $deltagut gut, $deltaskins skins, $deltafurs furs.')");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("DELETE FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' AND skill_abbr = 'cur'");
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
