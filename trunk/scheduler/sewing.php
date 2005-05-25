<?php
$pos = (strpos($_SERVER['PHP_SELF'], "/sewing.php"));
if ($pos !== false)
{
    die("You cannot access this page directly!");
}

  $res = $db->Execute("SELECT * FROM $dbtables[tribes]");
    db_op_result($res,__LINE__,__FILE__);
  while(!$res->EOF)
   {
    $tribe = $res->fields;


$act = $db->Execute("SELECT * FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' AND skill_abbr = 'sew'");
 db_op_result($act,__LINE__,__FILE__);
$act_do = $act->fields;

if($act_do['product'] == 'bladder'){

$gut = $db->Execute("SELECT * FROM $dbtables[products] WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'gut'");
db_op_result($gut,__LINE__,__FILE__);
$gutinfo = $gut->fields;
$leather = $db->Execute("SELECT * FROM $dbtables[products] WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'leather'");
 db_op_result($leather,__LINE__,__FILE__);
$leatherinfo = $leather->fields;
$startgut = $gutinfo['amount'];
$startltr = $leatherinfo['amount'];

$bladder = 0;

while($act_do['actives'] > 0 & $leatherinfo['amount'] > 0 & $gutinfo['amount'] > 1){
$gutinfo['amount'] -= 2;
$act_do['actives'] -= 1;
$leatherinfo['amount'] -= 1;
$bladder += 2;
}
$deltagut = $startgut - $gutinfo['amount'];
$deltaltr = $startltr - $leatherinfo['amount'];

$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount + $bladder WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'bladder'");
   db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount - $deltagut WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'gut'");
   db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount - $deltaltr WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'leather'");
   db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Sewing: $bladder Bladders made using $deltagut gut and $deltaltr leather.')");
   db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("DELETE FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' AND skill_abbr = 'sew' AND product = '$act_do[product]'");
   db_op_result($query,__LINE__,__FILE__);
if($game_debug){
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','0000','0000.00','DEBUG','$stamp','DEBUG: Sewing: $tribe[tribeid] $bladder bladders made using $deltagut gut and $deltaltr leather.')");
  db_op_result($query,__LINE__,__FILE__);
}
}

if( $act_do['product'] == 'scalebarding' )
{
    $arm = $db->Execute("SELECT * FROM $dbtables[skills] "
                       ."WHERE tribeid = '$tribe[tribeid]' "
                       ."AND abbr = 'arm' AND level > 6");
          db_op_result($arm,__LINE__,__FILE__);
    if( !$arm->EOF )
    {
        $jerk = $db->Execute("SELECT * FROM $dbtables[products] "
                            ."WHERE tribeid = '$tribe[goods_tribe]' "
                            ."AND long_name = 'leatherbarding'");
           db_op_result($jerk,__LINE__,__FILE__);
        $coal = $db->Execute("SELECT * FROM $dbtables[resources] "
                            ."WHERE tribeid = '$tribe[goods_tribe]' "
                            ."AND long_name = 'Coal'");
            db_op_result($coal,__LINE__,__FILE__);
        $mtl = $db->Execute("SELECT * FROM $dbtables[resources] "
                           ."WHERE tribeid = '$tribe[goods_tribe]' "
                           ."AND long_name = 'Bronze' "
                           ."AND amount > '14'");
        db_op_result($mtl,__LINE__,__FILE__);
        if( $mtl->EOF )
        {
            $mtl = $db->Execute("SELECT * FROM $dbtables[resources] "
                               ."WHERE tribeid = '$tribe[goods_tribe]' "
                               ."AND long_name = 'Iron'");
           db_op_result($mtl,__LINE__,__FILE__);
        }
        $jerkinfo = $jerk->fields;
        $coalinfo = $coal->fields;
        $mtlinfo = $mtl->fields;
        $startjerk = $jerkinfo['amount'];
        $startcoal = $coalinfo['amount'];
        $startmtl = $mtlinfo['amount'];
        $scale = 0;
        while( $act_do['actives'] > 3 && $jerkinfo['amount'] > 0 && $coalinfo['amount'] > 19 && $mtlinfo['amount'] > 14 )
        {
            $act_do['actives'] -= 4;
            $jerkinfo['amount'] -= 1;
            $coalinfo['amount'] -= 20;
            $mtlinfo['amount'] -= 15;
            $scale += 1;
        }
        $deltajerk = $startjerk - $jerkinfo['amount'];
        $deltacoal = $startcoal - $coalinfo['amount'];
        $deltamtl = $startmtl - $mtlinfo['amount'];
        $query = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount + $scale "
                    ."WHERE tribeid = '$tribe[goods_tribe]' "
                    ."AND long_name = '$act_do[product]'");
         db_op_result($query,__LINE__,__FILE__);
        $query = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount - $deltajerk "
                    ."WHERE tribeid = '$tribe[goods_tribe]' "
                    ."AND long_name = 'leatherbarding'");
         db_op_result($query,__LINE__,__FILE__);
        $query = $db->Execute("UPDATE $dbtables[resources] "
                    ."SET amount = amount - $deltacoal "
                    ."WHERE tribeid = '$tribe[goods_tribe]' "
                    ."AND long_name = 'Coal'");
        db_op_result($query,__LINE__,__FILE__);
        $query = $db->Execute("UPDATE $dbtables[resources] "
                    ."SET amount = amount - $deltamtl "
                    ."WHERE tribeid = '$tribe[goods_tribe]' "
                    ."AND long_name = '$mtlinfo[long_name]'");
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
                    ."'Sewing: $scale Scale Barding made using $deltajerk leather barding"
                    .", $deltacoal coal, $deltamtl $mtlinfo[long_name].')");
       db_op_result($query,__LINE__,__FILE__);
        $query = $db->Execute("DELETE FROM $dbtables[activities] "
                    ."WHERE tribeid = '$tribe[tribeid]' "
                    ."AND skill_abbr = 'sew' "
                    ."AND product = '$act_do[product]'");
         db_op_result($query,__LINE__,__FILE__);
    }
    else
    {
        $query = $db->Execute("INSERT INTO $dbtables[logs] "
                    ."VALUES("
                    ."'',"
                    ."'$month[count]',"
                    ."'$year[count]',"
                    ."'$tribe[clanid]',"
                    ."'$tribe[tribeid]',"
                    ."'UPDATE',"
                    ."'$stamp',"
                    ."'Sewing: We do not have enough skill in armor making to produce any scale barding.')");
        db_op_result($query,__LINE__,__FILE__);
        $query = $db->Execute("DELETE FROM $dbtables[activities] "
                    ."WHERE tribeid = '$tribe[tribeid]' "
                    ."AND skill_abbr = 'sew' "
                    ."AND product = '$act_do[product]'");
        db_op_result($query,__LINE__,__FILE__);
    }
}



if($act_do['product'] == 'scale'){
$arm = $db->Execute("SELECT * FROM $dbtables[skills] WHERE tribeid = '$tribe[tribeid]' AND abbr = 'arm' AND level > 2");
 db_op_result($arm,__LINE__,__FILE__);
if(!$arm->EOF){
$jerk = $db->Execute("SELECT * FROM $dbtables[products] WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'jerkin'");
 db_op_result($jerk,__LINE__,__FILE__);
$coal = $db->Execute("SELECT * FROM $dbtables[resources] WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'Coal'");
 db_op_result($coal,__LINE__,__FILE__);
$mtl = $db->Execute("SELECT * FROM $dbtables[resources] WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'Bronze' AND amount > '9'");
 db_op_result($mtl,__LINE__,__FILE__);
if( $mtl->EOF )
{
    $mtl = $db->Execute("SELECT * FROM $dbtables[resources] WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'Iron'");
      db_op_result($mtl,__LINE__,__FILE__);
}
$jerkinfo = $jerk->fields;
$coalinfo = $coal->fields;
$mtlinfo = $mtl->fields;
$startjerk = $jerkinfo['amount'];
$startcoal = $coalinfo['amount'];
$startmtl = $mtlinfo['amount'];
$scale = 0;
while($act_do['actives'] > 1 & $jerkinfo['amount'] > 0 & $coalinfo['amount'] > 14 & $mtlinfo['amount'] > 9){
$act_do['actives'] -= 2;
$jerkinfo['amount'] -= 1;
$coalinfo['amount'] -= 15;
$mtlinfo['amount'] -= 10;
$scale += 1;
}
$deltajerk = $startjerk - $jerkinfo['amount'];
$deltacoal = $startcoal - $coalinfo['amount'];
$deltamtl = $startmtl - $mtlinfo['amount'];

$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount + $scale WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'scale'");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount - $deltajerk WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'jerkin'");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = amount - $deltacoal WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'Coal'");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = amount - $deltamtl WHERE tribeid = '$tribe[goods_tribe]' AND long_name = '$mtlinfo[long_name]'");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Sewing: $scale Scale Armor made using $deltajerk jerkins, $deltacoal coal, $deltamtl $mtlinfo[long_name].')");
db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("DELETE FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' AND skill_abbr = 'sew' AND product = '$act_do[product]'");
 db_op_result($query,__LINE__,__FILE__);
if($game_debug){
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','0000','0000.00','DEBUG','$stamp','DEBUG: Sewing: $tribe[tribeid] $scale Scale armor made using $deltajerk jerkins, $deltacoal coal, $deltamtl $mtlinfo[long_name].')");
 db_op_result($query,__LINE__,__FILE__);
}
}
else{
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Sewing: We do not have enough skill in armor making to produce any scale armor.')");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("DELETE FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' AND skill_abbr = 'sew' AND product = '$act_do[product]'");
 db_op_result($query,__LINE__,__FILE__);
}
}
if( $act_do['product'] == 'ringbarding' )
{
    $arm = $db->Execute("SELECT * FROM $dbtables[skills] "
                       ."WHERE tribeid = '$tribe[tribeid]' "
                       ."AND abbr = 'arm' AND level > 7");
    db_op_result($arm,__LINE__,__FILE__);
    if( !$arm->EOF )
    {
        $jerk = $db->Execute("SELECT * FROM $dbtables[products] "
                            ."WHERE tribeid = '$tribe[goods_tribe]' "
                            ."AND long_name = 'leatherbarding'");
        db_op_result($jerk,__LINE__,__FILE__);
        $coal = $db->Execute("SELECT * FROM $dbtables[resources] "
                            ."WHERE tribeid = '$tribe[goods_tribe]' "
                            ."AND long_name = 'Coal'");
          db_op_result($coal,__LINE__,__FILE__);
         $mtl = $db->Execute("SELECT * FROM $dbtables[resources] "
                            ."WHERE tribeid = '$tribe[goods_tribe]' "
                            ."AND long_name = 'Iron' "
                            ."AND amount > '11'");
             db_op_result($mtl,__LINE__,__FILE__);
         if( $mtl->EOF )
         {
              $mtl = $db->Execute("SELECT * FROM $dbtables[resources] "
                                 ."WHERE tribeid = '$tribe[goods_tribe]' "
                                 ."AND long_name = 'Bronze'");
               db_op_result($mtl,__LINE__,__FILE__);
         }

$jerkinfo = $jerk->fields;
$coalinfo = $coal->fields;
$mtlinfo = $mtl->fields;
$startjerk = $jerkinfo['amount'];
$startcoal = $coalinfo['amount'];
$startmtl = $mtlinfo['amount'];
$ring = 0;
while( $act_do['actives'] > 3 && $jerkinfo['amount'] > 0 && $coalinfo['amount'] > 29 && $mtlinfo['amount'] > 11 ){
$act_do['actives'] -= 4;
$jerkinfo['amount'] -= 1;
$coalinfo['amount'] -= 30;
$mtlinfo['amount'] -= 12;
$ring += 1;
}
$deltajerk = $startjerk - $jerkinfo['amount'];
$deltacoal = $startcoal - $coalinfo['amount'];
$deltamtl = $startmtl - $mtlinfo['amount'];

$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount + $ring WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'ringbarding'");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount - $deltajerk WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'leatherbarding'");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = amount - $deltacoal WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'Coal'");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = amount - $deltamtl WHERE tribeid = '$tribe[goods_tribe]' AND long_name = '$mtlinfo[long_name]'");
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
            ."'Sewing: $ring Ring barding made using $deltajerk leather barding, $deltacoal coal, and $deltamtl $mtlinfo[long_name].')");
   db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("DELETE FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' AND skill_abbr = 'sew' AND product = '$act_do[product]'");
 db_op_result($query,__LINE__,__FILE__);
}
else{
$query = $db->Execute("INSERT INTO $dbtables[logs] "
            ."VALUES("
            ."'',"
            ."'$month[count]',"
            ."'$year[count]',"
            ."'$tribe[clanid]',"
            ."'$tribe[tribeid]',"
            ."'UPDATE',"
            ."'$stamp',"
            ."'Sewing: We do not have enough skill in armor making to produce any ring mail barding.')");
    db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("DELETE FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' AND skill_abbr = 'sew' AND product = '$act_do[product]'");
 db_op_result($query,__LINE__,__FILE__);
}
}


if($act_do['product'] == 'ring')
{
    $arm = $db->Execute("SELECT * FROM $dbtables[skills] "
                       ."WHERE tribeid = '$tribe[tribeid]' "
                       ."AND abbr = 'arm' AND level > 3");
      db_op_result($arm,__LINE__,__FILE__);
    if( !$arm->EOF)
    {
        $jerk = $db->Execute("SELECT * FROM $dbtables[products] "
                            ."WHERE tribeid = '$tribe[goods_tribe]' "
                            ."AND long_name = 'jerkin'");
         db_op_result($jerk,__LINE__,__FILE__);
        $coal = $db->Execute("SELECT * FROM $dbtables[resources] "
                            ."WHERE tribeid = '$tribe[goods_tribe]' "
                            ."AND long_name = 'Coal'");
          db_op_result($coal,__LINE__,__FILE__);
         $mtl = $db->Execute("SELECT * FROM $dbtables[resources] "
                            ."WHERE tribeid = '$tribe[goods_tribe]' "
                            ."AND long_name = 'Iron' "
                            ."AND amount > '7'");
         db_op_result($mtl,__LINE__,__FILE__);
         if( $mtl->EOF )
         {
              $mtl = $db->Execute("SELECT * FROM $dbtables[resources] "
                                 ."WHERE tribeid = '$tribe[goods_tribe]' "
                                 ."AND long_name = 'Bronze'");
            db_op_result($mtl,__LINE__,__FILE__);
         }

$jerkinfo = $jerk->fields;
$coalinfo = $coal->fields;
$mtlinfo = $mtl->fields;
$startjerk = $jerkinfo['amount'];
$startcoal = $coalinfo['amount'];
$startmtl = $mtlinfo['amount'];
$ring = 0;
while($act_do['actives'] > 1 & $jerkinfo['amount'] > 0 & $coalinfo['amount'] > 19 & $mtlinfo['amount'] > 7){
$act_do['actives'] -= 2;
$jerkinfo['amount'] -= 1;
$coalinfo['amount'] -= 20;
$mtlinfo['amount'] -= 8;
$ring += 1;
}
$deltajerk = $startjerk - $jerkinfo['amount'];
$deltacoal = $startcoal - $coalinfo['amount'];
$deltamtl = $startmtl - $mtlinfo['amount'];
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount + $ring WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'ring'");
     db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount - $deltajerk WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'jerkin'");
   db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = amount - $deltacoal WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'Coal'");
   db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = amount - $deltamtl WHERE tribeid = '$tribe[goods_tribe]' AND long_name = '$mtlinfo[long_name]'");
   db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Sewing: $ring Ring mail made using $deltajerk jerkins, $deltacoal coal, and $deltamtl $mtlinfo[long_name].')");
   db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("DELETE FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' AND skill_abbr = 'sew' AND product = '$act_do[product]'");
   db_op_result($query,__LINE__,__FILE__);
if($game_debug){
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','0000','0000.00','DEBUG','$stamp','DEBUG: Sewing: $tribe[tribeid] $ring Ring mail made using $deltajerk jerkins, $deltacoal coal, $deltamtl $mtlinfo[long_name].')");
 db_op_result($query,__LINE__,__FILE__);
}
}
else{
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Sewing: We do not have enough skill in armor making to produce any ring mail armor.')");
     db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("DELETE FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' AND skill_abbr = 'sew' AND product = '$act_do[product]'");
   db_op_result($query,__LINE__,__FILE__);
}
}

if($act_do['product'] == 'cloth'){
$parch = $db->Execute("SELECT * FROM $dbtables[products] WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'parchment'");
  db_op_result($parch,__LINE__,__FILE__);
$parchment = $parch->fields;
$startparch = $parchment['amount'];
$cloth = 0;
while($parchment['amount'] > 19 & $act_do['actives'] > 4){
$cloth += 1;
$parchment['amount'] -= 20;
$act_do['actives'] -= 5;
}
$deltaparch = $startparch - $parchment['amount'];
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount + $cloth WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'cloth'");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount - $deltaparch WHERE tribeid = '$tribe[goods_tribe]' AND long_name = 'parchment'");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("DELETE FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' AND skill_abbr = 'sew' AND product = '$act_do[product]'");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Sewing: $cloth Cloth made using $deltaparch parchment.')");
  db_op_result($query,__LINE__,__FILE__);
if($game_debug){
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','0000','0000.00','DEBUG','$stamp','DEBUG: Sewing: $tribe[tribeid] $cloth Cloth made using $deltaparch parchment.')");
 db_op_result($query,__LINE__,__FILE__);
}




$act->MoveNext();
}
$res->MoveNext();
}

?>
