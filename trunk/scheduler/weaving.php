<?php
$pos = (strpos($_SERVER['PHP_SELF'], "/weaving.php"));
if ($pos !== false)
{
    die("You cannot access this page directly!");
}

  $res = $db->Execute("SELECT * FROM $dbtables[tribes]");
   db_op_result($res,__LINE__,__FILE__);
  while(!$res->EOF)
   {
    $tribe = $res->fields;
  $act = $db->Execute("SELECT * FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' AND skill_abbr = 'wv'");
  db_op_result($act,__LINE__,__FILE__);
while(!$act->EOF){
$act_do = $act->fields;



if($act_do['product'] == 'rope'){
$gutbark = 0;
$deltacotton = 0;
$gutdelta = 0;
$barkdelta = 0;
$cot = $db->Execute("SELECT * FROM $dbtables[products] WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'cotton' and amount > 19");
db_op_result($cot,__LINE__,__FILE__);
if($cot->EOF){
$gut = $db->Execute("SELECT * FROM $dbtables[products] WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'gut'");
db_op_result($gut,__LINE__,__FILE__);
$bark = $db->Execute("SELECT * FROM $dbtables[products] WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'bark'");
 db_op_result($bark,__LINE__,__FILE__);
$gutbark = 1;
$gutinfo = $gut->fields;
$barkinfo = $bark->fields;
$startgut = $gutinfo['amount'];
$startbark = $barkinfo['amount'];
}
$cotton = $cot->fields;
$startcot = $cotton['amount'];

$rope = 0;
if($gutbark > 0){
while($act_do['actives'] > 1 & $gutinfo['amount'] > 9 & $barkinfo['amount'] > 9){
$rope += 1;
$act_do['actives'] -= 2;
$gutinfo['amount'] -= 10;
$barkinfo['amount'] -= 10;
}
$gutdelta = $startgut - $gutinfo['amount'];
$barkdelta = $startbark - $barkinfo['amount'];
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount - $gutdelta WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'gut'");
db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount - $barkdelta WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'bark'");
db_op_result($query,__LINE__,__FILE__);
}
else{
while($act_do['actives'] > 0 & $cotton['amount'] > 19){
$rope += 2;
$act_do['actives'] -= 1;
$cotton['amount'] -= 20;
}
$deltacotton = $startcot - $cotton['amount'];
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount - $deltacotton WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'cotton'");
db_op_result($query,__LINE__,__FILE__);
}
if($gutbark > 0){
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Weaving: $rope ropes made using $gutdelta gut and $barkdelta bark.')");
db_op_result($query,__LINE__,__FILE__);
}
else{
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Weaving: $rope ropes made from $deltacotton cotton.')");
db_op_result($query,__LINE__,__FILE__);
}
$query = $db->Execute("DELETE FROM $dbtables[activities] WHERE product = '$act_do[product]' AND skill_abbr = 'wv' AND tribeid = '$tribe[tribeid]'");
db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount + $rope WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'rope'");
db_op_result($query,__LINE__,__FILE__);
if($game_debug){
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','0000','0000.00','DEBUG','$stamp','DEBUG: Weaving: $rope rope should have been made by $tribe[tribeid] using $gutdelta gut, $barkdelta bark or $deltacotton cotton.')");
db_op_result($query,__LINE__,__FILE__);
}
}

if($act_do['product'] == 'sling')
{
$cot = $db->Execute("SELECT * FROM $dbtables[products] WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'cotton'");
db_op_result($cot,__LINE__,__FILE__);
$cotton = $cot->fields;
$startcot = $cotton['amount'];

$sling = 0;
while($act_do['actives'] > 0 & $cotton['amount'] > 1){
$sling += 2;
$act_do['actives'] -= 1;
$cotton['amount'] -= 1;
}
$deltacotton = $startcot - $cotton['amount'];
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount + $sling WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'sling'");
db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount - $deltacotton WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'cotton'");
db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("DELETE FROM $dbtables[activities] WHERE product = '$act_do[product]' AND skill_abbr = 'wv' AND tribeid = '$tribe[tribeid]'");
db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Weaving: $sling slings made from $deltacotton cotton.')");
db_op_result($query,__LINE__,__FILE__);
}


if($act_do['product'] == 'snares'){
$rop = $db->Execute("SELECT * FROM $dbtables[products] WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'rope'");
db_op_result($rop,__LINE__,__FILE__);
$rope = $rop->fields;
$startrop = $rope['amount'];

$snares = 0;
while($act_do['actives'] > 0 & $rope['amount'] > 0){
$snares += 2;
$act_do['actives'] -= 1;
$rope['amount'] -= 1;
}
$ropedelta = $startrop - $rope['amount'];
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount - $ropedelta WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'rope'");
db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount + $snares WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'snares'");
db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("DELETE FROM $dbtables[activities] WHERE product = '$act_do[product]' AND skill_abbr = 'wv' AND tribeid = '$tribe[tribeid]'");
db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Weaving: $snares Snares made from $ropedelta ropes.')");
db_op_result($query,__LINE__,__FILE__);
}

if($act_do['product'] == 'net'){
$cot = $db->Execute("SELECT * FROM $dbtables[products] WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'cotton'");
 db_op_result($cot,__LINE__,__FILE__);
$cotton = $cot->fields;
$startcot = $cotton['amount'];

$net = 0;

while($act_do['actives'] > 1 & $cotton['amount'] > 9){
$net += 1;
$act_do['actives'] -= 2;
$cotton['amount'] -= 10;
}
$cottondelta = $startcot - $cotton['amount'];

$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount - $cottondelta WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'cotton'");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount + $net WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'net'");
db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("DELETE FROM $dbtables[activities] WHERE product = '$act_do[product]' AND skill_abbr = 'wv' AND tribeid = '$tribe[tribeid]'");
db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Weaving: $net Nets made from $cottondelta Cotton.')");
db_op_result($query,__LINE__,__FILE__);
}

if($act_do['product'] == 'rug'){
$cot = $db->Execute("SELECT * FROM $dbtables[products] WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'cotton'");
db_op_result($cot,__LINE__,__FILE__);
$cotton = $cot->fields;
$startcot = $cotton['amount'];

$rug = 0;

while($act_do['actives'] > 4 & $cotton['amount'] > 19){
$rug += 1;
$act_do['actives'] -= 5;
$cotton['amount'] -= 20;
}
$cottondelta = $startcot - $cotton['amount'];
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount - $cottondelta WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'cotton'");
db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount + $rug WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'rug'");
db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("DELETE FROM $dbtables[activities] WHERE product = '$act_do[product]' AND skill_abbr = 'wv' AND tribeid = '$tribe[tribeid]'");
db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Weaving: $rug Rugs made from $cottondelta Cotton.')");
db_op_result($query,__LINE__,__FILE__);
}


if($act_do['product'] == 'cloth'){
$cot = $db->Execute("SELECT * FROM $dbtables[products] WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'cotton'");
db_op_result($cot,__LINE__,__FILE__);
$cotton = $cot->fields;
$startcot = $cotton['amount'];

$cloth = 0;

while($act_do['actives'] > 4 & $cotton['amount'] > 14){
$cloth += 1;
$act_do['actives'] -= 5;
$cotton['amount'] -= 15;
}
$cottondelta = $startcot - $cotton['amount'];
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount - $cottondelta WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'cotton'");
db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount + $cloth WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'cloth'");
db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("DELETE FROM $dbtables[activities] WHERE product = '$act_do[product]' AND skill_abbr = 'wv' AND tribeid = '$tribe[tribeid]'");
db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Weaving: $cloth Cloth made from $cottondelta Cotton.')");
db_op_result($query,__LINE__,__FILE__);
}

if($act_do['product'] == 'carpet'){
$cot = $db->Execute("SELECT * FROM $dbtables[products] WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'cotton'");
db_op_result($cot,__LINE__,__FILE__);
$cotton = $cot->fields;
$startcot = $cotton['amount'];

$carpet = 0;

while($act_do['actives'] > 9 & $cotton['amount'] > 49){
$carpet += 1;
$act_do['actives'] -= 10;
$cotton['amount'] -= 50;
}
$cottondelta = $startcot - $cotton['amount'];
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount - $cottondelta WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'cotton'");
db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount + $carpet WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'carpet'");
db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("DELETE FROM $dbtables[activities] WHERE product = '$act_do[product]' AND skill_abbr = 'wv' AND tribeid = '$tribe[tribeid]'");
db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Weaving: $carpet Carpet made using $cottondelta Cotton.')");
db_op_result($query,__LINE__,__FILE__);
}

if($act_do['product'] == 'tapestry'){
$cot = $db->Execute("SELECT * FROM $dbtables[products] WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'cotton'");
 db_op_result($cot,__LINE__,__FILE__);
$cotton = $cot->fields;
$startcot = $cotton['amount'];

$tapestry = 0;

while($act_do['actives'] > 19 & $cotton['amount'] > 99){
$tapestry += 1;
$act_do['actives'] -= 20;
$cotton['amount'] -= 100;
}
$cottondelta = $startcot - $cotton['amount'];
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount - $cottondelta WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'cotton'");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount + $tapestry WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'tapestry'");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("DELETE FROM $dbtables[activities] WHERE product = '$act_do[product]' AND skill_abbr = 'wv' AND tribeid = '$tribe[tribeid]'");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Weaving: $tapestry Tapestry made using $cottondelta Cotton.')");
 db_op_result($query,__LINE__,__FILE__);
}


$act->MoveNext();
}
$res->MoveNext();
}

?>
