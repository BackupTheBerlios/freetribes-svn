<?php
require_once("../config.php");
$time_start = getmicrotime();
connectdb();
  $res = $db->Execute("SELECT * FROM $dbtables[tribes]");
  db_op_result($res,__LINE__,__FILE__);
  while(!$res->EOF)
   {
    $tribe = $res->fields;

$act = $db->Execute("SELECT * FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]'");
 db_op_result($act,__LINE__,__FILE__);
if($act->EOF){


if($tribe[tribeid] == $tribe[goods_tribe]){
    $liv1 = $db->Execute("SELECT * FROM $dbtables[livestock] WHERE tribeid = '$tribe[tribeid]' AND type = 'Cattle'");
    db_op_result($liv1,__LINE__,__FILE__);
    $liv2 = $db->Execute("SELECT * FROM $dbtables[livestock] WHERE tribeid = '$tribe[tribeid]' AND type = 'Horses'");
    db_op_result($liv2,__LINE__,__FILE__);
    $liv3 = $db->Execute("SELECT * FROM $dbtables[livestock] WHERE tribeid = '$tribe[tribeid]' AND type = 'Elephants'");
    db_op_result($liv3,__LINE__,__FILE__);
    $liv4 = $db->Execute("SELECT * FROM $dbtables[livestock] WHERE tribeid = '$tribe[tribeid]' AND type = 'Goats'");
    db_op_result($liv4,__LINE__,__FILE__);
    $liv5 = $db->Execute("SELECT * FROM $dbtables[livestock] WHERE tribeid = '$tribe[tribeid]' AND type = 'Dogs'");
    db_op_result($liv5,__LINE__,__FILE__);
    $liv6 = $db->Execute("SELECT * FROM $dbtables[livestock] WHERE tribeid = '$tribe[tribeid]' AND type = 'Pigs'");
    db_op_result($liv6,__LINE__,__FILE__);
    $liv7 = $db->Execute("SELECT * FROM $dbtables[livestock] WHERE tribeid = '$tribe[tribeid]' AND type = 'Sheep'");
     db_op_result($liv7,__LINE__,__FILE__);
    $mounts1 = $liv1->fields;
    $mounts2 = $liv2->fields;
    $mounts3 = $liv3->fields;
    $mounts4 = $liv4->fields;
    $mounts5 = $liv5->fields;
    $mounts6 = $liv6->fields;
    $mounts7 = $liv7->fields;
    $skill = $db->Execute("SELECT * FROM $dbtables[skills] WHERE tribeid = '$tribe[tribeid]' AND abbr = 'herd'");
    db_op_result($skill,__LINE__,__FILE__);
    $skillinfo = $skill->fields;
    $denominator = 10 + $skillinfo[level];
    $denominator2 = 5 + $skillinfo[level];
    $denominator3 = 20 + $skillinfo[level];
$total_herders = ceil(($mounts1[amount]/$denominator) + ($mounts2[amount]/$denominator) + ($mounts3[amount]/$denominator2) + ($mounts4[amount]/$denominator3) + ($mounts5[amount]/$denominator) + ($mounts6[amount]/$denominator3) + ($mounts7[amount]/$denominator3));
}

$default_activity = $tribe[activepop] - $total_herders;
if($default_activity < 0){
$default_activity = 0;
}
if($total_herders > $tribe[curam]){
$total_herders = $tribe[curam];
}
$res = $db->Execute("INSERT INTO $dbtables[activities] VALUES('','$tribe[tribeid]','hunt','provs','$default_activity')");
 db_op_result($res,__LINE__,__FILE__);
$res = $db->Execute("INSERT INTO $dbtables[activities] VALUES('','$tribe[tribeid]','herd','livestock','$total_herders')");
db_op_result($res,__LINE__,__FILE__);
$act = $db->Execute("SELECT * FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]'");
db_op_result($act,__LINE__,__FILE__);
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
