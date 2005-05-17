<?php
$pos = (strpos($_SERVER['PHP_SELF'], "/bonework.php"));
if ($pos !== false)
{
    die("You cannot access this page directly!");
}
require_once("config.php");
$time_start = getmicrotime();
include("scheduler/game_time.php");
connectdb();
  $res = $db->Execute("SELECT * FROM $dbtables[tribes]");
   db_op_result($res,__LINE__,__FILE__);
  while(!$res->EOF)
   {
    $tribe = $res->fields;
$act = $db->Execute("SELECT * FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]'");
db_op_result($act,__LINE__,__FILE__);
while(!$act->EOF){
$act_do = $act->fields;

if($act_do['skill_abbr'] == 'bnw'){
$bone = $db->Execute("SELECT * FROM $dbtables[products] WHERE tribeid = '$tribe[goods_tribe]' AND proper = 'Bones'");
db_op_result($bone,__LINE__,__FILE__);
$bones = $bone->fields;
$leat = $db->Execute("SELECT * FROM $dbtables[products] WHERE tribeid = '$tribe[goods_tribe]' AND proper = 'Leather'");
db_op_result($leat,__LINE__,__FILE__);
$leather = $leat->fields;
$club = $db->Execute("SELECT * FROM $dbtables[products] WHERE tribeid = '$tribe[goods_tribe]' AND proper = 'Club'");
 db_op_result($club,__LINE__,__FILE__);
$clubinfo = $club->fields;
$shaft = $db->Execute("SELECT * FROM $dbtables[products] WHERE tribeid = '$tribe[goods_tribe]' AND proper = 'Shaft'");
db_op_result($shaft,__LINE__,__FILE__);
$shaftinfo = $shaft->fields;

$boneaxe = 0;
if($act_do['product'] == 'boneaxe'){

while($bones['amount'] > 0 & $act_do['actives'] > 1 & $clubinfo['amount'] > 0 & $leather['amount'] > 0){
$bones['amount'] -= 1;
$act_do['actives'] -= 2;
$clubinfo['amount'] -= 1;
$leather['amount'] -= 1;
$boneaxe += 1;
}
$query = $db->Execute("UPDATE $dbtables[products] SET amount = '$bones[amount]' WHERE tribeid = '$tribe[goods_tribe]' AND proper = 'Bones'");
db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[products] SET amount = '$clubinfo[amount]' WHERE tribeid = '$tribe[goods_tribe]' AND proper = 'Club'");
db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[products] SET amount = '$leather[amount]' WHERE tribeid = '$tribe[goods_tribe]' AND proper = 'Leather'");
db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount + '$boneaxe' WHERE tribeid = '$tribe[goods_tribe]' AND proper = 'Bone Axe'");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("DELETE FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' AND skill_abbr = 'bnw' AND product = '$act_do[product]'");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Bonework: $boneaxe Bone Axes made.')");
db_op_result($query,__LINE__,__FILE__);
}

if($act_do['product'] == 'bonespear'){
$bonespear = 0;
while ($bones['amount'] > 0 & $act_do['actives'] > 0 & $shaftinfo['amount'] > 0){
$bones['amount'] -= 1;
$act_do['actives'] -= 1;
$shaftinfo['amount'] -= 1;
$bonespear += 1;
}

$query = $db->Execute("UPDATE $dbtables[products] SET amount = '$bones[amount]' WHERE tribeid = '$tribe[goods_tribe]' AND proper = 'Bones'");
db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[products] SET amount = '$shaftinfo[amount]' WHERE tribeid = '$tribe[goods_tribe]' AND proper = 'Shaft'");
db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount + '$bonespear' WHERE tribeid = '$tribe[goods_tribe]' AND proper = 'Bone Spear'");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("DELETE FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' AND skill_abbr = 'bnw' AND product = '$act_do[product]'");
db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Bonework: $bonespear Bone Spears made.')");
db_op_result($query,__LINE__,__FILE__);
}

if($act_do['product'] == 'boneframe'){
$boneframe = 0;
while($bones['amount'] > 2 & $act_do['actives'] > 1){
$bones['amount'] -= 3;
$act_do['actives'] -= 2;
$boneframe += 1;
}
$query = $db->Execute("UPDATE $dbtables[products] SET amount = '$bones[amount]' WHERE tribeid = '$tribe[goods_tribe]' AND proper = 'Bones'");
db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount + '$boneframe' WHERE tribeid = '$tribe[goods_tribe]' AND proper = 'Bone Frame'");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("DELETE FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' AND skill_abbr = 'bnw' AND product = '$act_do[product]'");
db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Bonework: $boneframe Bone Frames made.')");
db_op_result($query,__LINE__,__FILE__);
}

if($act_do['product'] == 'bonearmor'){
$bonearmor = 0;
while($bones['amount'] > 9 & $act_do['actives'] > 3 & $leather['amount'] > 1){
$bones['amount'] -= 10;
$act_do['actives'] -= 4;
$leather['amount'] -= 2;
$bonearmor += 1;
}
$query = $db->Execute("UPDATE $dbtables[products] SET amount = '$bones[amount]' WHERE tribeid = '$tribe[goods_tribe]' AND proper = 'Bones'");
db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount + '$bonearmor' WHERE tribeid = '$tribe[goods_tribe]' AND proper = 'Bone Armor'");
db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[products] SET amount = '$leather[amount]' WHERE tribeid = '$tribe[goods_tribe]' AND proper = 'Leather'");
db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("DELETE FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' AND skill_abbr = 'bnw' AND product = '$act_do[product]'");
db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Bonework: $bonearmor Bone Armor made.')");
db_op_result($query,__LINE__,__FILE__);
}


if( $act_do['product'] == 'cuirboillibone' )
{
    $bonearmor = 0;
    $startbone = $bones['amount'];
    $startltr = $leather['amount'];
    while( $bones['amount'] > 19 && $act_do['actives'] > 3 && $leather['amount'] > 3 )
    {
        $bones['amount'] -= 20;
        $act_do['actives'] -= 4;
        $leather['amount'] -= 4;
        $bonearmor += 1;
    }
    $deltabone = $startbone - $bones['amount'];
    $deltaltr = $startltr - $leather['amount'];
    $query = $db->Execute("UPDATE $dbtables[products] "
                ."SET amount = amount - $deltabone "
                ."WHERE tribeid = '$tribe[goods_tribe]' "
                ."AND proper = 'Bones'");
       db_op_result($query,__LINE__,__FILE__);
    $query = $db->Execute("UPDATE $dbtables[products] "
                ."SET amount = amount - $deltaltr "
                ."WHERE tribeid = '$tribe[goods_tribe]' "
                ."AND proper = 'Leather'");
      db_op_result($query,__LINE__,__FILE__);
    $query = $db->Execute("UPDATE $dbtables[products] "
                ."SET amount = amount + $bonearmor "
                ."WHERE tribeid = '$tribe[goods_tribe]' "
                ."AND long_name = 'cuirboillibone'");
      db_op_result($query,__LINE__,__FILE__);
    $query = $db->Execute("DELETE FROM $dbtables[activities] "
                ."WHERE tribeid = '$tribe[tribeid]' "
                ."AND skill_abbr = '$act_do[skill_abbr]' "
                ."AND product = '$act_do[product]'");
     db_op_result($query,__LINE__,__FILE__);
    $query = $db->Execute("INSERT INTO $dbtables[logs] "
                ."VALUES("
                ."'',"
                ."'$month[count]',"
                ."'$year[count]',"
                ."'$tribe[clanid]',"
                ."'$tribe[tribeid]',"
                ."'UPDATE',"
                ."'$stamp',"
                ."'Bonework: $bonearmor Bone Cuirboilli made using $deltabone bones and $deltaltr leather.')");
      db_op_result($query,__LINE__,__FILE__);
}





$query = $db->Execute("DELETE FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' AND skill_abbr = 'bnw' AND product = '$act_do[product]'");
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
