<?php
$pos = (strpos($_SERVER['PHP_SELF'], "/metalworking.php"));
if ($pos !== false)
{
    die("You cannot access this page directly!");
}

$res = $db->Execute("SELECT * FROM $dbtables[tribes]");
    db_op_result($res,__LINE__,__FILE__);
while( !$res->EOF )
{
    $tribe = $res->fields;
    $act = $db->Execute("SELECT * FROM $dbtables[activities] "
                       ."WHERE tribeid = '$tribe[tribeid]'");
         db_op_result($act,__LINE__,__FILE__);
    while( !$act->EOF )
    {
        $act_do = $act->fields;
        if( $act_do['product'] == 'barrels' )
        {
            $coal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                ."WHERE long_name = 'Coal' "
                                ."AND tribeid = '$tribe[goods_tribe]'");
                  db_op_result($coal,__LINE__,__FILE__);
            $coalinfo = $coal->fields;
            $metal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                 ."WHERE long_name = 'Bronze' "
                                 ."AND tribeid = '$tribe[goods_tribe]' "
                                 ."AND amount > 1");
                  db_op_result($metal,__LINE__,__FILE__);
            if( $metal->EOF )
            {
                $metal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                     ."WHERE long_name = 'Brass' "
                                     ."AND tribeid = '$tribe[goods_tribe]' "
                                     ."AND amount > 1");
                  db_op_result($metal,__LINE__,__FILE__);
            }
            if( $metal->EOF )
            {
                $metal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                     ."WHERE long_name = 'Iron' "
                                     ."AND tribeid = '$tribe[goods_tribe]'");
                 db_op_result($metal,__LINE__,__FILE__);
            }
            $metalinfo = $metal->fields;
            $wood = $db->Execute("SELECT * FROM $dbtables[products] "
                                ."WHERE long_name = 'logs' "
                                ."AND tribeid = '$tribe[goods_tribe]'");
               db_op_result($wood,__LINE__,__FILE__);
            $woodinfo = $wood->fields;
            $barrels_made = 0;
            $startcoal = $coalinfo['amount'];
            $startmtl = $metalinfo['amount'];
            $startlogs = $woodinfo['amount'];
            while( $act_do['actives'] > 1 && $metalinfo['amount'] > 1 && $woodinfo['amount'] > 0 && $coalinfo['amount'] > 3 )
            {
                $barrels_made++;
                $act_do['actives'] -= 2;
                $metalinfo['amount'] -= 2;
                $woodinfo['amount'] -= 1;
                $coalinfo['amount'] -= 4;
            }
            $deltacoal = $startcoal - $coalinfo['amount'];
            $deltamtl = $startmtl - $metalinfo['amount'];
            $deltalogs = $startlogs - $woodinfo['amount'];
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + '$barrels_made' "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'barrels'");
            db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[resources] "
                        ."SET amount = amount - $deltamtl "
                        ."WHERE tribeid = $tribe[goods_tribe] "
                        ."AND long_name = '$metalinfo[long_name]'");
            db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[resources] "
                        ."SET amount = amount - $deltacoal "
                        ."WHERE tribeid = $tribe[goods_tribe] "
                        ."AND long_name = 'Coal'");
            db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount - $deltalogs "
                        ."WHERE tribeid = $tribe[goods_tribe] "
                        ."AND long_name = 'logs'");
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
                        ."'Metalworking: $barrels_made Barrels "
                        ."made using $deltacoal coal, $deltamtl "
                        ."$metalinfo[long_name], $deltalogs logs.')");
              db_op_result($query,__LINE__,__FILE__);
        }

        if( $act_do['product'] == 'picks' )
        {
            $coal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                ."WHERE long_name = 'Coal' "
                                ."AND tribeid = '$tribe[goods_tribe]'");
                   db_op_result($coal,__LINE__,__FILE__);
            $coalinfo = $coal->fields;
            $metal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                 ."WHERE long_name = 'Bronze' "
                                 ."AND tribeid = '$tribe[goods_tribe]' "
                                 ."AND amount > 2");
                 db_op_result($metal,__LINE__,__FILE__);
            if( $metal->EOF )
            {
                $metal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                     ."WHERE long_name = 'Brass' "
                                     ."AND tribeid = '$tribe[goods_tribe]' "
                                     ."AND amount > 2");
                         db_op_result($metal,__LINE__,__FILE__);
            }
            if( $metal->EOF )
            {
                $metal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                     ."WHERE long_name = 'Iron' "
                                     ."AND tribeid = '$tribe[goods_tribe]'");
                   db_op_result($metal,__LINE__,__FILE__);
            }
            $metalinfo = $metal->fields;
            $startcoal = $coalinfo['amount'];
            $startmtl = $metalinfo['amount'];
            $product_made = 0;
            while( $act_do['actives'] > 1 && $metalinfo['amount'] > 2 && $coalinfo['amount'] > 14 )
            {
                $product_made++;
                $act_do['actives'] -= 2;
                $metalinfo['amount'] -= 3;
                $coalinfo['amount'] -= 15;
            }
            $deltacoal = $startcoal - $coalinfo['amount'];
            $deltamtl = $startmtl - $metalinfo['amount'];
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + '$product_made' "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = '$act_do[product]'");
            db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[resources] "
                        ."SET amount = amount - $deltamtl "
                        ."WHERE tribeid = $tribe[goods_tribe] "
                        ."AND long_name = '$metalinfo[long_name]'");
            db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[resources] "
                        ."SET amount = amount - $deltacoal "
                        ."WHERE tribeid = $tribe[goods_tribe] "
                        ."AND long_name = 'Coal'");
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
                        ."'Metalworking: $product_made $act_do[product] "
                        ."made using $deltacoal coal and $deltamtl $metalinfo[long_name].')");
            db_op_result($query,__LINE__,__FILE__);
        }
        if( $act_do['product'] == 'traps' )
        {
            $coal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                ."WHERE long_name = 'Coal' "
                                ."AND tribeid = '$tribe[goods_tribe]'");
              db_op_result($coal,__LINE__,__FILE__);
            $coalinfo = $coal->fields;
            $metal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                 ."WHERE long_name = 'Bronze' "
                                 ."AND tribeid = '$tribe[goods_tribe]' "
                                 ."AND amount > 0");
                      db_op_result($metal,__LINE__,__FILE__);
            if( $metal->EOF )
            {
                $metal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                     ."WHERE long_name = 'Brass' "
                                     ."AND tribeid = '$tribe[goods_tribe]' "
                                     ."AND amount > 0");
                    db_op_result($metal,__LINE__,__FILE__);
            }
            if( $metal->EOF )
            {
                $metal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                     ."WHERE long_name = 'Iron' "
                                     ."AND tribeid = '$tribe[goods_tribe]'");
                  db_op_result($metal,__LINE__,__FILE__);
            }
            $metalinfo = $metal->fields;
            $startcoal = $coalinfo['amount'];
            $startmtl = $metalinfo['amount'];
            $product_made = 0;
            while( $act_do['actives'] > 0 && $metalinfo['amount'] > 0 && $coalinfo['amount'] > 3 )
            {
                $product_made++;
                $act_do['actives'] -= 1;
                $metalinfo['amount'] -= 1;
                $coalinfo['amount'] -= 4;
            }
            $deltacoal = $startcoal - $coalinfo['amount'];
            $deltamtl = $startmtl - $metalinfo['amount'];
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + '$product_made' "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = '$act_do[product]'");
             db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[resources] "
                        ."SET amount = amount - $deltamtl "
                        ."WHERE tribeid = $tribe[goods_tribe] "
                        ."AND long_name = '$metalinfo[long_name]'");
             db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[resources] "
                        ."SET amount = amount - $deltacoal "
                        ."WHERE tribeid = $tribe[goods_tribe] "
                        ."AND long_name = 'Coal'");
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
                        ."'Metalworking: $product_made $act_do[product] "
                        ."made using $deltacoal coal, $deltamtl $metalinfo[long_name].')");
              db_op_result($query,__LINE__,__FILE__);
        }
        if( $act_do['product'] == 'shovel' )
        {
            $coal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                ."WHERE long_name = 'Coal' "
                                ."AND tribeid = '$tribe[goods_tribe]'");
                 db_op_result($coal,__LINE__,__FILE__);
            $coalinfo = $coal->fields;
            $metal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                 ."WHERE long_name = 'Bronze' "
                                 ."AND tribeid = '$tribe[goods_tribe]' "
                                 ."AND amount > 1");
                   db_op_result($metal,__LINE__,__FILE__);
            if( $metal->EOF )
            {
                $metal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                     ."WHERE long_name = 'Brass' "
                                     ."AND tribeid = '$tribe[goods_tribe]' "
                                     ."AND amount > 1");
                  db_op_result($metal,__LINE__,__FILE__);
            }
            if( $metal->EOF )
            {
                $metal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                     ."WHERE long_name = 'Iron' "
                                     ."AND tribeid = '$tribe[goods_tribe]'");
                 db_op_result($metal,__LINE__,__FILE__);
            }
            $metalinfo = $metal->fields;
            $startcoal = $coalinfo['amount'];
            $startmtl = $metalinfo['amount'];
            $product_made = 0;
            while( $act_do['actives'] > 1 && $metalinfo['amount'] > 1 && $coalinfo['amount'] > 9 )
            {
                $product_made++;
                $act_do['actives'] -= 2;
                $metalinfo['amount'] -= 2;
                $coalinfo['amount'] -= 10;
            }
            $deltacoal = $startcoal - $coalinfo['amount'];
            $deltamtl = $startmtl - $metalinfo['amount'];
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + '$product_made' "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = '$act_do[product]'");
             db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[resources] "
                        ."SET amount = amount - $deltamtl "
                        ."WHERE tribeid = $tribe[goods_tribe] "
                        ."AND long_name = '$metalinfo[long_name]'");
             db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[resources] "
                        ."SET amount = amount - $deltacoal "
                        ."WHERE tribeid = $tribe[goods_tribe] "
                        ."AND long_name = 'Coal'");
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
                        ."'Metalworking: $product_made $act_do[product] made "
                        ."using $deltacoal coal, $deltamtl $metalinfo[long_name].')");
              db_op_result($query,__LINE__,__FILE__);
        }


        if( $act_do['product'] == 'plow' )
        {
            $coal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                ."WHERE long_name = 'Coal' "
                                ."AND tribeid = '$tribe[goods_tribe]'");
               db_op_result($coal,__LINE__,__FILE__);
            $coalinfo = $coal->fields;
            $coalstart = $coalinfo['amount'];
            $metal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                 ."WHERE long_name = 'Iron' "
                                 ."AND tribeid = '$tribe[goods_tribe]' "
                                 ."AND amount > 9");
                db_op_result($metal,__LINE__,__FILE__);
            if( $metal->EOF )
            {
                $metal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                     ."WHERE long_name = 'Bronze' "
                                     ."AND tribeid = '$tribe[goods_tribe]'");
                 db_op_result($metal,__LINE__,__FILE__);
            }
            $metalinfo = $metal->fields;
            $metalstart = $metalinfo['amount'];
            $wood = $db->Execute("SELECT * FROM $dbtables[products] "
                                ."WHERE long_name = 'logs' "
                                ."AND tribeid = '$tribe[goods_tribe]'");
               db_op_result($wood,__LINE__,__FILE__);
            $woodinfo = $wood->fields;
            $woodstart = $woodinfo['amount'];
            $product_made = 0;
            while( $act_do['actives'] > 4 && $metalinfo['amount'] > 9 && $coalinfo['amount'] > 24 && $woodinfo['amount'] > 0 )
            {
                $product_made++;
                $act_do['actives'] -= 5;
                $metalinfo['amount'] -= 10;
                $woodinfo['amount'] -= 1;
                $coalinfo['amount'] -= 25;
            }
            $deltacoal = $coalstart - $coalinfo['amount'];
            $deltametal = $metalstart - $metalinfo['amount'];
            $deltawood = $woodstart - $woodinfo['amount'];
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + '$product_made' "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = '$act_do[product]'");
               db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[resources] "
                        ."SET amount = amount - $deltametal "
                        ."WHERE tribeid = $tribe[goods_tribe] "
                        ."AND long_name = '$metalinfo[long_name]'");
               db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[resources] "
                        ."SET amount = amount - $deltacoal "
                        ."WHERE tribeid = $tribe[goods_tribe] "
                        ."AND long_name = 'Coal'");
              db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount - $deltawood "
                        ."WHERE tribeid = $tribe[goods_tribe] "
                        ."AND long_name = 'logs'");
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
                        ."'Metalworking: $product_made "
                        ."$act_do[product] made using $deltawood logs, "
                        ."$deltacoal coal, and $deltametal $metalinfo[long_name].')");
              db_op_result($query,__LINE__,__FILE__);
        }


        if( $act_do['product'] == 'scrapers' )
        {
            $coal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                ."WHERE long_name = 'Coal' "
                                ."AND tribeid = '$tribe[goods_tribe]'");
             db_op_result($coal,__LINE__,__FILE__);
            $coalinfo = $coal->fields;
            $metal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                 ."WHERE long_name = 'Bronze' "
                                 ."AND tribeid = '$tribe[goods_tribe]' "
                                 ."AND amount > 1");
              db_op_result($metal,__LINE__,__FILE__);
            if( $metal->EOF )
            {
                $metal = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Brass' AND tribeid = '$tribe[goods_tribe]' AND amount > 1");
                 db_op_result($metal,__LINE__,__FILE__);
            }
            if( $metal->EOF )
            {
                $metal = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Iron' AND tribeid = '$tribe[goods_tribe]'");
                db_op_result($metal,__LINE__,__FILE__);
            }
            $metalinfo = $metal->fields;
            $startmetal = $metalinfo['amount'];
            $startcoal = $coalinfo['amount'];
            $product_made = 0;
            while( $act_do['actives'] > 0 && $metalinfo['amount'] > 0 && $coalinfo['amount'] > 3 )
            {
                $product_made++;
                $act_do['actives'] -= 1;
                $metalinfo['amount'] -= 1;
                $coalinfo['amount'] -= 4;
            }
            $metaldelta = $startmetal - $metalinfo['amount'];
            $coaldelta = $startcoal - $coalinfo['amount'];

            $query = $db->Execute("UPDATE $dbtables[products] set amount = amount + '$product_made' WHERE tribeid = '$tribe[goods_tribe]' and long_name = '$act_do[product]'");
             db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[resources] SET amount = amount - $metaldelta WHERE tribeid = $tribe[goods_tribe] AND long_name = '$metalinfo[long_name]'");
             db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[resources] SET amount = amount - $coaldelta WHERE tribeid = $tribe[goods_tribe] AND long_name = 'Coal'");
             db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Metalworking: $product_made $act_do[product]  made using $coaldelta coal and $metaldelta $metalinfo[long_name].')");
             db_op_result($query,__LINE__,__FILE__);
        }

        if( $act_do['product'] == 'scythe' )
        {
            $coal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                ."WHERE long_name = 'Coal' "
                                ."AND tribeid = '$tribe[goods_tribe]'");
              db_op_result($coal,__LINE__,__FILE__);
            $coalinfo = $coal->fields;
            $metal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                 ."WHERE long_name = 'Bronze' "
                                 ."AND tribeid = '$tribe[goods_tribe]' "
                                 ."AND amount > 2");
                db_op_result($metal,__LINE__,__FILE__);
            if( $metal->EOF )
            {
                $metal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                     ."WHERE long_name = 'Brass' "
                                     ."AND tribeid = '$tribe[goods_tribe]' "
                                     ."AND amount > 2");
                db_op_result($metal,__LINE__,__FILE__);
            }
            if( $metal->EOF )
            {
                $metal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                     ."WHERE long_name = 'Iron' "
                                     ."AND tribeid = '$tribe[goods_tribe]'");
                db_op_result($metal,__LINE__,__FILE__);
            }
            $metalinfo = $metal->fields;
            $startcoal = $coalinfo['amount'];
            $startmtl = $metalinfo['amount'];
            $product_made = 0;
            while( $act_do['actives'] > 1 && $metalinfo['amount'] > 2 && $coalinfo['amount'] > 14 )
            {
                $product_made++;
                $act_do['actives'] -= 2;
                $metalinfo['amount'] -= 3;
                $coalinfo['amount'] -= 15;
            }
            $deltacoal = $startcoal - $coalinfo['amount'];
            $deltamtl = $startmtl - $metalinfo['amount'];
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + $product_made "
                        ."WHERE long_name = '$act_do[product]' "
                        ."AND tribeid = '$tribe[goods_tribe]'");
             db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[resources] "
                        ."SET amount = amount - $deltamtl "
                        ."WHERE tribeid = $tribe[goods_tribe] "
                        ."AND long_name = '$metalinfo[long_name]'");
             db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[resources] "
                        ."SET amount = amount - $deltacoal "
                        ."WHERE tribeid = $tribe[goods_tribe] "
                        ."AND long_name = 'Coal'");
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
                        ."'Metalworking: $product_made $act_do[product] made "
                        ."using $deltacoal coal, $deltamtl $metalinfo[long_name].')");
             db_op_result($query,__LINE__,__FILE__);
        }

        if( $act_do['product'] == 'mattock' )
        {
$coal = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Coal' AND tribeid = '$tribe[goods_tribe]'");
 db_op_result($coal,__LINE__,__FILE__);
$coalinfo = $coal->fields;
$metal = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Bronze' AND tribeid = '$tribe[goods_tribe]' AND amount > 7");
  db_op_result($metal,__LINE__,__FILE__);
if($metal->EOF){
$metal = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Iron' AND tribeid = '$tribe[goods_tribe]'");
  db_op_result($metal,__LINE__,__FILE__);
}
$metalinfo = $metal->fields;

$product_made = 0;
while($act_do['actives'] > 1 & $metalinfo['amount'] > 7 & $coalinfo['amount'] > 24){
$product_made++;
$act_do['actives'] -= 2;
$metalinfo['amount'] -= 8;
$coalinfo['amount'] -= 25;
}
$query = $db->Execute("UPDATE $dbtables[products] set amount = amount + '$product_made' WHERE tribeid = '$tribe[goods_tribe]' and long_name = '$act_do[product]'");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = $metalinfo[amount] WHERE tribeid = $tribe[goods_tribe] AND long_name = '$metalinfo[long_name]'");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = $coalinfo[amount] WHERE tribeid = $tribe[goods_tribe] AND long_name = 'Coal'");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Metalworking: $product_made $act_do[product] made.')");
  db_op_result($query,__LINE__,__FILE__);
}


if($act_do['product'] == 'shackles'){
$coal = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Coal' AND tribeid = '$tribe[goods_tribe]'");
  db_op_result($coal,__LINE__,__FILE__);
$coalinfo = $coal->fields;
$metal = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Bronze' AND tribeid = '$tribe[goods_tribe]' AND amount > 1");
 db_op_result($metal,__LINE__,__FILE__);
if($metal->EOF){
$metal = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Iron' AND tribeid = '$tribe[goods_tribe]'");
 db_op_result($metal,__LINE__,__FILE__);
}
$metalinfo = $metal->fields;

$product_made = 0;
while($act_do['actives'] > 0 & $metalinfo['amount'] > 1 & $coalinfo['amount'] > 14){
$product_made++;
$act_do['actives'] -= 1;
$metalinfo['amount'] -= 2;
$coalinfo['amount'] -= 15;
}

$query = $db->Execute("UPDATE $dbtables[products] set amount = amount + '$product_made' WHERE tribeid = '$tribe[goods_tribe]' and long_name = '$act_do[product]'");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = $metalinfo[amount] WHERE tribeid = $tribe[goods_tribe] AND long_name = '$metalinfo[long_name]'");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = $coalinfo[amount] WHERE tribeid = $tribe[goods_tribe] AND long_name = 'Coal'");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Metalworking: $product_made $act_do[product] made.')");
 db_op_result($query,__LINE__,__FILE__);
}

if($act_do['product'] == 'adze'){
$coal = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Coal' AND tribeid = '$tribe[goods_tribe]'");
  db_op_result($coal,__LINE__,__FILE__);
$coalinfo = $coal->fields;
$metal = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Bronze' AND tribeid = '$tribe[goods_tribe]' AND amount > 3");
  db_op_result($metal,__LINE__,__FILE__);
if( $metal->EOF )
{
    $metal = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Iron' AND tribeid = '$tribe[goods_tribe]'");
   db_op_result($metal,__LINE__,__FILE__);
}
$metalinfo = $metal->fields;
$startcoal = $coalinfo['amount'];
$startmetal = $metalinfo['amount'];
$product_made = 0;
while($act_do['actives'] > 1 & $metalinfo['amount'] > 3 & $coalinfo['amount'] > 19){
$product_made++;
$act_do['actives'] -= 2;
$metalinfo['amount'] -= 4;
$coalinfo['amount'] -= 20;
}
$deltacoal = $startcoal - $coalinfo['amount'];
$deltametal = $startmetal - $metalinfo['amount'];

$query = $db->Execute("UPDATE $dbtables[products] set amount = amount + '$product_made' WHERE tribeid = '$tribe[goods_tribe]' and long_name = '$act_do[product]'");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = amount - $deltametal WHERE tribeid = $tribe[goods_tribe] AND long_name = '$metalinfo[long_name]'");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = amount - $deltacoal WHERE tribeid = $tribe[goods_tribe] AND long_name = 'Coal'");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Metalworking: $product_made $act_do[product] made using $deltacoal $coalinfo[long_name] and $deltametal $metalinfo[long_name].')");
 db_op_result($query,__LINE__,__FILE__);
}


if($act_do['product'] == 'hoe'){
$coal = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Coal' AND tribeid = '$tribe[goods_tribe]'");
 db_op_result($coal,__LINE__,__FILE__);
$coalinfo = $coal->fields;
$metal = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Bronze' AND tribeid = '$tribe[goods_tribe]' AND amount > 2");
  db_op_result($metal,__LINE__,__FILE__);
if($metal->EOF){
$metal = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Iron' AND tribeid = '$tribe[goods_tribe]'");
 db_op_result($metal,__LINE__,__FILE__);
}
$metalinfo = $metal->fields;

$product_made = 0;
while($act_do['actives'] > 1 & $metalinfo['amount'] > 2 & $coalinfo['amount'] > 9){
$product_made++;
$act_do['actives'] -= 2;
$metalinfo['amount'] -= 3;
$coalinfo['amount'] -= 10;
}
$query = $db->Execute("UPDATE $dbtables[products] set amount = amount + '$product_made' WHERE tribeid = '$tribe[goods_tribe]' and long_name = '$act_do[product]'");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = $metalinfo[amount] WHERE tribeid = $tribe[goods_tribe] AND long_name = '$metalinfo[long_name]'");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = $coalinfo[amount] WHERE tribeid = $tribe[goods_tribe] AND long_name = 'Coal'");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Metalworking: $product_made $act_do[product] made.')");
 db_op_result($query,__LINE__,__FILE__);
}


if($act_do['product'] == 'cauldron'){
$coal = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Coal' AND tribeid = '$tribe[goods_tribe]'");
  db_op_result($coal,__LINE__,__FILE__);
$coalinfo = $coal->fields;
$metal = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Bronze' AND tribeid = '$tribe[goods_tribe]'");
  db_op_result($metal,__LINE__,__FILE__);
$metalinfo = $metal->fields;

$product_made = 0;
while($act_do['actives'] > 3 & $metalinfo['amount'] > 19 & $coalinfo['amount'] > 99){
$product_made++;
$act_do['actives'] -= 4;
$metalinfo['amount'] -= 20;
$coalinfo['amount'] -= 100;
}

$query = $db->Execute("UPDATE $dbtables[products] set amount = amount + '$product_made' WHERE tribeid = '$tribe[goods_tribe]' and long_name = '$act_do[product]'");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = $metalinfo[amount] WHERE tribeid = $tribe[goods_tribe] AND long_name = 'Bronze'");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = $coalinfo[amount] WHERE tribeid = $tribe[goods_tribe] AND long_name = 'Coal'");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Metalworking: $product_made $act_do[product] made.')");
 db_op_result($query,__LINE__,__FILE__);
}


if($act_do['product'] == 'glasspipe'){
$coal = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Coal' AND tribeid = '$tribe[goods_tribe]'");
 db_op_result($coal,__LINE__,__FILE__);
$coalinfo = $coal->fields;
$metal = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Bronze' AND tribeid = '$tribe[goods_tribe]'");
 db_op_result($metal,__LINE__,__FILE__);
$metalinfo = $metal->fields;

$product_made = 0;
while($act_do['actives'] > 2 & $metalinfo['amount'] > 1 & $coalinfo['amount'] > 39){
$product_made++;
$act_do['actives'] -= 3;
$metalinfo['amount'] -= 2;
$coalinfo['amount'] -= 40;
}

$query = $db->Execute("UPDATE $dbtables[products] set amount = amount + '$product_made' WHERE tribeid = '$tribe[goods_tribe]' and long_name = '$act_do[product]'");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = $metalinfo[amount] WHERE tribeid = $tribe[goods_tribe] AND long_name = 'Bronze'");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = $coalinfo[amount] WHERE tribeid = $tribe[goods_tribe] AND long_name = 'Coal'");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Metalworking: $product_made $act_do[product] made.')");
  db_op_result($query,__LINE__,__FILE__);
}

if($act_do['product'] == 'quarrels'){
$coal = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Coal' AND tribeid = '$tribe[goods_tribe]'");
 db_op_result($coal,__LINE__,__FILE__);
$coalinfo = $coal->fields;

$metal = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Iron' AND tribeid = '$tribe[goods_tribe]'");
db_op_result($metal,__LINE__,__FILE__);
$metalinfo = $metal->fields;
$metal_long_name = "Iron";

while($act_do['actives'] > 0 & $metalinfo['amount'] > 0 & $coalinfo['amount'] > 9){
$product_made += 10;
$act_do['actives'] -= 1;
$metalinfo['amount'] -= 1;
$coalinfo['amount'] -= 10;
}
$query = $db->Execute("UPDATE $dbtables[products] set amount = amount + '$product_made' WHERE tribeid = '$tribe[goods_tribe]' and long_name = '$act_do[product]'");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = $metalinfo[amount] WHERE tribeid = $tribe[goods_tribe] AND long_name = '$metal_long_name'");
db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = $coalinfo[amount] WHERE tribeid = $tribe[goods_tribe] AND long_name = 'Coal'");
db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Metalworking: $product_made $act_do[product] made.')");
db_op_result($query,__LINE__,__FILE__);
}


if($act_do['product'] == 'pellets'){
$coal = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Coal' AND tribeid = '$tribe[goods_tribe]'");
 db_op_result($coal,__LINE__,__FILE__);
$coalinfo = $coal->fields;

$metal = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Lead' AND tribeid = '$tribe[goods_tribe]'");
 db_op_result($metal,__LINE__,__FILE__);
$metalinfo = $metal->fields;
$metal_long_name = "Lead";

$product_made = 0;
while($act_do['actives'] > 0 & $metalinfo['amount'] > 9 & $coalinfo['amount'] > 0){
$product_made += 20;
$act_do['actives'] -= 1;
$metalinfo['amount'] -= 10;
$coalinfo['amount'] -= 1;
}

$query = $db->Execute("UPDATE $dbtables[products] set amount = amount + '$product_made' WHERE tribeid = '$tribe[goods_tribe]' and long_name = '$act_do[product]'");
db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = $metalinfo[amount] WHERE tribeid = $tribe[goods_tribe] AND long_name = '$metal_long_name'");
db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = $coalinfo[amount] WHERE tribeid = $tribe[goods_tribe] AND long_name = 'Coal'");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Metalworking: $product_made $act_do[product] made.')");
db_op_result($query,__LINE__,__FILE__);
}



$query = $db->Execute("DELETE FROM $dbtables[activities] WHERE skill_abbr = 'mtl' AND tribeid = '$tribe[tribeid]'");
db_op_result($query,__LINE__,__FILE__);

$act->MoveNext();
}
$res->MoveNext();
}

?>
