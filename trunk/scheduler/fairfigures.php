<?php
require_once("../config.php");
$time_start = getmicrotime();
include("game_time.php");
connectdb();
$reslt = $db->Execute("SELECT * FROM $dbtables[tribes]");
  db_op_result($reslt,__LINE__,__FILE__);
while( !$reslt->EOF )
{
    $tribe = $reslt->fields;


    if( $month[count] == '4' | $month[count] == '10' )
    {
        /////////////////////Tribes participating in the fair can only conduct limited activities.///////
        $involve = $db->Execute("SELECT * FROM $dbtables[fair_tribe] "
                               ."WHERE tribeid = '$tribe[tribeid]'");
                 db_op_result($involve,__LINE__,__FILE__);
        if( !$involve->EOF )
        {
            $res = $db->Execute("DELETE FROM $dbtables[activities] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND skill_abbr != 'hunt' "
                        ."AND skill_abbr != 'herd'");
                db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("DELETE FROM $dbtables[farm_activities] "
                        ."WHERE tribeid = '$tribe[tribeid]'");
              db_op_result($res,__LINE__,__FILE__);
            $seek = $db->Execute("SELECT * FROM $dbtables[seeking] "
                                ."WHERE tribeid = '$tribe[tribeid]'");
                db_op_result($seek,__LINE__,__FILE__);
            $res = $db->Execute("DELETE FROM $dbtables[seeking] "
                        ."WHERE tribeid = '$tribe[tribeid]'");
                 db_op_result($res,__LINE__,__FILE__);
            $act_do = $seek->fields;
            $res = $db->Execute("UPDATE $dbtables[livestock] "
                        ."SET amount = amount + '$act_do[horses]' "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND type = 'Horses'");
                 db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("UPDATE $dbtables[livestock] "
                        ."SET amount = amount + '$act_do[burden_beasts]' "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND type = 'Cattle'");
                db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + '$act_do[backpacks]' "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'backpack'");
               db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + '$act_do[saddlebags]' "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'saddlebags'");
                db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + '$act_do[wagons]' "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'wagon'");
               db_op_result($res,__LINE__,__FILE__);
        }
        /////////////////////////////////////////////////////////////////////////////////////////////////

        $cult = $db->Execute("SELECT * FROM $dbtables[fair_tribe] "
                            ."WHERE tribeid = '$tribe[tribeid]' "
                            ."AND buy_sell = 'C' "
                            ."AND quantity > 0");
            db_op_result($cult,__LINE__,__FILE__);
        $logculture = "Fair Activities: ";
        $cultcount = 0;

        while( !$cult->EOF )
        {
            $cultinfo = $cult->fields;
            $res = $db->Execute("UPDATE $dbtables[resources] "
                        ."SET amount = amount + $cultinfo[price] "
                        ."WHERE long_name = 'Silver' "
                        ."AND tribeid = '$tribe[tribeid]'");
               db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("DELETE FROM $dbtables[fair_tribe] "
                        ."WHERE clan_id = '$tribe[clanid]' "
                        ."AND product = '$cultinfo[product]'");
                db_op_result($res,__LINE__,__FILE__);
            $logculture = $logculture . "$cultinfo[price] silver from $cultinfo[product], ";
            $cultcount++;
            $cult->MoveNext();
        }

        if( $cultcount > 0 )
        {
            $res = $db->Execute("INSERT INTO $dbtables[logs] "
                        ."VALUES("
                        ."'',"
                        ."'$month[count]',"
                        ."'$year[count]',"
                        ."'$tribe[clanid]',"
                        ."'$tribe[tribeid]',"
                        ."'FAIR',"
                        ."'$stamp',"
                        ."'$logculture')");
              db_op_result($res,__LINE__,__FILE__);
        }

        $sell = $db->Execute("SELECT * FROM $dbtables[fair_tribe] "
                            ."WHERE tribeid = '$tribe[tribeid]' "
                            ."AND buy_sell = 'S' "
                            ."AND quantity > 0");
               db_op_result($sell,__LINE__,__FILE__);
        $logsell = "Fair Sells: ";
        $sellcount = 0;

        while( !$sell->EOF )
        {
            $sellinfo = $sell->fields;
            if( $sellinfo[product] == 'Slaves' )
            {
                $slave = $db->Execute("SELECT * FROM $dbtables[tribes] "
                                     ."WHERE tribeid = '$tribe[tribeid]'");
                     db_op_result($slave,__LINE__,__FILE__);
                $slaveinfo = $slave->fields;
                if( $slaveinfo[slavepop] < $sellinfo[quantity] )
                {
                    $sellinfo[quantity] = $slaveinfo[slavepop];
                }
                $slaveinfo[slavepop] -= $sellinfo[quantity];
                $sp = $db->Execute("SELECT * FROM $dbtables[fair] "
                                  ."WHERE proper_name = 'Slaves'");
                    db_op_result($sp,__LINE__,__FILE__);
                $slaveprice = $sp->fields;
                $totalprice = $slaveprice[price_sell] * $sellinfo[quantity];
                $res = $db->Execute("UPDATE $dbtables[tribes] "
                            ."SET slavepop = '$slaveinfo[slavepop]' "
                            ."WHERE tribeid = '$tribe[tribeid]'");
                  db_op_result($res,__LINE__,__FILE__);
            }

            $product = $db->Execute("SELECT * FROM $dbtables[products] "
                                   ."WHERE tribeid = '$tribe[tribeid]' "
                                   ."AND proper = '$sellinfo[product]'");
                        db_op_result($product,__LINE__,__FILE__);

            if( !$product->EOF )
            {
                $productinfo = $product->fields;
                $admin_logs .= "$productinfo[amount] $productinfo[long_name] $productinfo[tribeid]";
                if( $productinfo[amount] < $sellinfo[quantity] )
                {
                    $sellinfo[quantity] = $productinfo[amount];
                }
            }
            $resource = $db->Execute("SELECT * FROM $dbtables[resources] "
                                    ."WHERE tribeid = '$tribe[tribeid]' "
                                    ."AND long_name = '$sellinfo[product]'");
                db_op_result($resource,__LINE__,__FILE__);
            if( !$resource->EOF )
            {
                $productinfo = $resource->fields;
                $admin_logs .= "$productinfo[amount] $productinfo[long_name] $productinfo[tribeid]";
                if( $productinfo[amount] < $sellinfo[quantity] )
                {
                    $sellinfo[quantity] = $productinfo[amount];
                }
            }
            $livestock = $db->Execute("SELECT * FROM $dbtables[livestock] "
                                     ."WHERE tribeid = '$tribe[tribeid]' "
                                     ."AND type = '$sellinfo[product]'");
                  db_op_result($livestock,__LINE__,__FILE__);
            if( !$livestock->EOF )
            {
                $productinfo = $livestock->fields;
                $admin_logs .= "$productinfo[amount] $productinfo[long_name] $productinfo[tribeid]";
                if( $productinfo[amount] < $sellinfo[quantity] )
                {
                    $sellinfo[quantity] = $productinfo[amount];
                }
            }
            $price = $db->Execute("SELECT * FROM $dbtables[fair] "
                                 ."WHERE proper_name = '$sellinfo[product]'");
                  db_op_result($price,__LINE__,__FILE__);
            $priceinfo = $price->fields;
            $res = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount - $sellinfo[quantity] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND proper = '$sellinfo[product]'");
                db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("UPDATE $dbtables[resources] "
                        ."SET amount = amount - $sellinfo[quantity] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND long_name = '$sellinfo[product]'");
                db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("UPDATE $dbtables[livestock] "
                        ."SET amount = amount - $sellinfo[quantity] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND type = '$sellinfo[product]'");
               db_op_result($res,__LINE__,__FILE__);
            $silver = $sellinfo[quantity] * $priceinfo[price_sell];
            $res = $db->Execute("UPDATE $dbtables[resources] "
                        ."SET amount = amount + $silver "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND long_name = 'Silver'");
                db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("UPDATE $dbtables[fair] "
                        ."SET amount = amount + $sellinfo[quantity] "
                        ."WHERE proper_name = '$sellinfo[product]'");
                db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("DELETE FROM $dbtables[fair_tribe] "
                        ."WHERE clan_id = '$tribe[clanid]' "
                        ."AND product = '$sellinfo[product]'");
                db_op_result($res,__LINE__,__FILE__);
            $logsell = $logsell . "$silver silver from $sellinfo[product], ";
            $sellcount++;
            $sell->MoveNext();
        }
        if( $sellcount > 0 )
        {
            $res = $db->Execute("INSERT INTO $dbtables[logs] "
                        ."VALUES("
                        ."'',"
                        ."'$month[count]',"
                        ."'$year[count]',"
                        ."'$tribe[clanid]',"
                        ."'$tribe[tribeid]',"
                        ."'FAIR',"
                        ."'$stamp',"
                        ."'$logsell')");
             db_op_result($res,__LINE__,__FILE__);
        }
        $res = $db->Execute("INSERT INTO $dbtables[logs] "
                    ."VALUES("
                    ."'',"
                    ."'$month[count]',"
                    ."'$year[count]',"
                    ."'0000',"
                    ."'0000.00',"
                    ."'ADMINFAIR',"
                    ."'$stamp',"
                    ."'Fair Debug: $admin_logs')");
            db_op_result($res,__LINE__,__FILE__);
        $admin_logs = '';
        $buy = $db->Execute("SELECT * FROM $dbtables[fair_tribe] "
                           ."WHERE tribeid = '$tribe[tribeid]' "
                           ."AND buy_sell = 'B' "
                           ."AND quantity > 0");
           db_op_result($buy,__LINE__,__FILE__);
        $logbuy = "Fair Buys: ";
        $buycount = 0;

        while( !$buy->EOF )
        {
            $buyinfo = $buy->fields;
            $product = $db->Execute("SELECT * FROM $dbtables[products] "
                                   ."WHERE tribeid = '$tribe[tribeid]' "
                                   ."AND proper = '$buyinfo[product]'");
               db_op_result($product,__LINE__,__FILE__);
            $sil = $db->Execute("SELECT * FROM $dbtables[resources] "
                               ."WHERE tribeid = '$tribe[tribeid]' "
                               ."AND long_name = 'Silver'");
               db_op_result($silver,__LINE__,__FILE__);
            $silinfo = $sil->fields;
            $price = $db->Execute("SELECT * FROM $dbtables[fair] "
                                 ."WHERE proper_name = '$buyinfo[product]' "
                                 ."AND amount > 0");
                db_op_result($price,__LINE__,__FILE__);
            $priceinfo = $price->fields;

            $admin_logs .= "$buyinfo[amount] $buyinfo[long_name] $buyinfo[tribeid] $silinfo[amount] silver ";
            if( $buyinfo[quantity] > $priceinfo[amount] )
            {
                $buyinfo[quantity] = $priceinfo[amount];
            }
            if( $buyinfo[quantity] < 0 )
            {
                $buyinfo[quantity] = 0;
            }

            $totalcost = $priceinfo[price_buy] * $buyinfo[quantity];
            $afford = round($silinfo[amount]/$priceinfo[price_buy]);

            if( $totalcost > $silinfo[amount] )
            {
                $buyinfo[quantity] = $afford;
                $totalcost = $priceinfo[price_buy] * $buyinfo[quantity];
            }

            if( $buyinfo[product] == 'Slaves' )
            {
                $slave = $db->Execute("SELECT slavepop FROM $dbtables[tribes] "
                                     ."WHERE tribeid = '$tribe[tribeid]'");
                   db_op_result($slave,__LINE__,__FILE__);
                $slaveinfo = $slave->fields;
                $slaves = $slaveinfo[slavepop];
                $slaves += $buyinfo[quantity];
                $res = $db->Execute("UPDATE $dbtables[tribes] "
                            ."SET slavepop = '$slaves' "
                            ."WHERE tribeid = '$tribe[tribeid]'");
                  db_op_result($res,__LINE__,__FILE__);
                $res = $db->Execute("UPDATE $dbtables[resources] "
                            ."SET amount = amount - $totalcost "
                            ."WHERE tribeid = '$tribe[tribeid]' "
                            ."AND long_name = 'Silver'");
                    db_op_result($res,__LINE__,__FILE__);
                $res = $db->Execute("UPDATE $dbtables[fair] "
                            ."SET amount = amount - $buyinfo[quantity] "
                            ."WHERE proper_name = 'Slaves'");
                    db_op_result($res,__LINE__,__FILE__);
                $res = $db->Execute("INSERT INTO $dbtables[logs] "
                            ."VALUES("
                            ."'',"
                            ."'$month[count]',"
                            ."'$year[count]',"
                            ."'$tribe[clanid]',"
                            ."'$tribe[tribeid]',"
                            ."'FAIR',"
                            ."'$stamp',"
                            ."'Fair Activity: Bought $buyinfo[quantity] "
                            ."slaves for $totalcost silver.')");
                   db_op_result($res,__LINE__,__FILE__);
                $res = $db->Execute("DELETE FROM $dbtables[fair_tribe] "
                            ."WHERE clan_id = '$tribe[clanid]' "
                            ."AND product = '$buyinfo[product]'");
                 db_op_result($res,__LINE__,__FILE__);
            }
if(!$product->EOF){
$productinfo = $product->fields;
$check = $db->Execute("SELECT * FROM $dbtables[products] WHERE proper = '$buyinfo[product]' AND tribeid = '$tribe[tribeid]'");
 db_op_result($check,__LINE__,__FILE__);
if(!$check->EOF){
$res = $db->Execute("UPDATE $dbtables[products] SET amount = amount + $buyinfo[quantity] WHERE tribeid = '$tribe[tribeid]' AND proper = '$buyinfo[product]'");
 db_op_result($res,__LINE__,__FILE__);
}
elseif($check->EOF){
$product = $db->Execute("SELECT * from $dbtables[product_table] WHERE proper = '$buyinfo[product]' LIMIT 1'");
 db_op_result($product,__LINE__,__FILE__);
$productinfo = $product->fields;
$res = $db->Execute("INSERT INTO $dbtables[products] VALUES('$tribe[tribeid]','$productinfo[proper]','$productinfo[long_name]','$buyinfo[quantity]','$productinfo[weapon]','$productinfo[armor]')");
 db_op_result($res,__LINE__,__FILE__);
}
$logbuy = $logbuy . "$buyinfo[quantity] $buyinfo[product] for $totalcost silver, ";
$buycount++;
$res = $db->Execute("UPDATE $dbtables[fair] SET amount = amount - $buyinfo[quantity] WHERE proper_name = '$buyinfo[product]'");
 db_op_result($res,__LINE__,__FILE__);
$res = $db->Execute("UPDATE $dbtables[resources] SET amount = amount - $totalcost WHERE tribeid = '$tribe[tribeid]' AND long_name = 'Silver'");
 db_op_result($res,__LINE__,__FILE__);
$res = $db->Execute("DELETE FROM $dbtables[fair_tribe] WHERE product = '$buyinfo[product]' AND clan_id = '$tribe[clanid]' AND buy_sell = 'B'");
 db_op_result($res,__LINE__,__FILE__);
}

$resource = $db->Execute("SELECT * FROM $dbtables[resources] WHERE tribeid = '$tribe[tribeid]' and long_name = '$buyinfo[product]'");
db_op_result($resource,__LINE__,__FILE__);
if(!$resource->EOF){
$resinfo = $resource->fields;
$res = $db->Execute("UPDATE $dbtables[resources] SET amount = amount + $buyinfo[quantity] WHERE tribeid = '$tribe[tribeid]' AND long_name = '$buyinfo[product]'");
 db_op_result($res,__LINE__,__FILE__);
$logbuy = $logbuy . "$buyinfo[quantity] $buyinfo[product] for $totalcost silver, ";
$buycount++;
$res = $db->Execute("UPDATE $dbtables[fair] SET amount = amount - $buyinfo[quantity] WHERE proper_name = '$buyinfo[product]'");
 db_op_result($res,__LINE__,__FILE__);
$res = $db->Execute("UPDATE $dbtables[resources] SET amount = amount - $totalcost WHERE tribeid = '$tribe[tribeid]' AND long_name = 'Silver'");
 db_op_result($res,__LINE__,__FILE__);
$res = $db->Execute("DELETE FROM $dbtables[fair_tribe] WHERE product = '$buyinfo[product]' AND clan_id = '$tribe[clanid]' AND buy_sell = 'B'");
db_op_result($res,__LINE__,__FILE__);
}

$liv = $db->Execute("SELECT * FROM $dbtables[livestock] WHERE tribeid = '$tribe[tribeid]' AND type = '$buyinfo[product]'");
 db_op_result($liv,__LINE__,__FILE__);
if(!$liv->EOF){
$livinfo = $liv->fields;
$res = $db->Execute("UPDATE $dbtables[livestock] SET amount = amount + $buyinfo[quantity] WHERE tribeid = '$tribe[tribeid]' AND type = '$buyinfo[product]'");
db_op_result($res,__LINE__,__FILE__);
$logbuy = $logbuy . "$buyinfo[quantity] $buyinfo[product] for $totalcost silver, ";
$buycount++;
$res = $db->Execute("UPDATE $dbtables[fair] SET amount = amount - $buyinfo[quantity] WHERE proper_name = '$buyinfo[product]'");
 db_op_result($res,__LINE__,__FILE__);
$res = $db->Execute("UPDATE $dbtables[resources] SET amount = amount - $totalcost WHERE tribeid = '$tribe[tribeid]' AND long_name = 'Silver'");
 db_op_result($res,__LINE__,__FILE__);
$res = $db->Execute("DELETE FROM $dbtables[fair_tribe] WHERE product = '$buyinfo[product]' AND clan_id = '$tribe[clanid]' AND buy_sell = 'B'");
db_op_result($res,__LINE__,__FILE__);
}
$buy->MoveNext();
}
if($buycount > 0){
$res = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','FAIR','$stamp','$logbuy')");
  db_op_result($res,__LINE__,__FILE__);
}
$res = $db->Execute("INSERT INTO $dbtables[logs] "
            ."VALUES("
            ."'',"
            ."'$month[count]',"
            ."'$year[count]',"
            ."'0000',"
            ."'0000.00',"
            ."'ADMINFAIR',"
            ."'$stamp',"
            ."'Fair Debug: $admin_logs')");
   db_op_result($res,__LINE__,__FILE__);

$admin_logs = '';



}


$reslt->MoveNext();
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
