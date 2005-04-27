<?php
require_once("../config.php");
$time_start = getmicrotime();
include("game_time.php");
connectdb();
$res = $db->Execute("SELECT * FROM $dbtables[tribes]");
db_op_result($res,__LINE__,__FILE__);
while( !$res->EOF )
{
    $tribe = $res->fields;

    $act = $db->Execute("SELECT * FROM $dbtables[activities] "
                       ."WHERE skill_abbr = 'seq' "
                       ."AND tribeid = '$tribe[tribeid]'");
      db_op_result($act,__LINE__,__FILE__);
    while( !$act->EOF )
    {
        $act_do = $act->fields;

        if( $act_do[product] == 'ballistae' )
        {
            $wood = $db->Execute("SELECT * FROM $dbtables[products] "
                                ."WHERE long_name = 'logs' "
                                ."AND tribeid = '$tribe[goods_tribe]'");
                db_op_result($wood,__LINE__,__FILE__);
            $woodinfo = $wood->fields;
            $startwood = $woodinfo[amount];
            $coal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                ."WHERE long_name = 'Coal' "
                                ."AND tribeid = '$tribe[goods_tribe]'");
               db_op_result($coal,__LINE__,__FILE__);
            $coalinfo = $coal->fields;
            $startcoal = $coalinfo[amount];
            $metal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                 ."WHERE long_name = 'Iron' "
                                 ."AND tribeid = '$tribe[goods_tribe]' "
                                 ."AND amount > 2");
                db_op_result($metal,__LINE__,__FILE__);
            if( $metal->EOF )
            {
                $metal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                     ."WHERE long_name = 'Bronze' "
                                     ."AND tribeid = '$tribe[goods_tribe]'");
                   db_op_result($metal,__LINE__,__FILE__);
            }
            $metalinfo = $metal->fields;
            $startmtl = $metalinfo[amount];
            $wagon = $db->Execute("SELECT * FROM $dbtables[products] "
                                ."WHERE long_name = 'wagon' "
                                ."AND tribeid = '$tribe[goods_tribe]'");
               db_op_result($metal,__LINE__,__FILE__);
            $wagoninfo = $wagon->fields;
            $startwagon = $wagoninfo[amount];
            $product = 0;
            while( $woodinfo[amount] > 2 && $metalinfo[amount] > 2 && $coalinfo[amount] > 19 && $wagoninfo[amount] > 0 && $act_do[actives] > 9 )
            {
               $woodinfo[amount] -= 3;
               $metalinfo[amount] -= 3;
               $coalinfo[amount] -= 20;
               $wagoninfo[amount] -= 1;
               $act_do[actives] -= 10;
               $product += 1;
            }
            $deltawood = $startwood - $woodinfo[amount];
            $deltacoal = $startcoal - $coalinfo[amount];
            $deltamtl = $startmtl - $metalinfo[amount];
            $deltawagon = $startwagon - $wagoninfo[amount];
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount - $deltawood "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'logs'");
               db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount - $deltawagon "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'wagon'");
               db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[resources] "
                        ."SET amount = amount - $deltacoal "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'Coal'");
               db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[resources] "
                        ."SET amount = amount - $deltamtl "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = '$metalinfo[long_name]'");
              db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + $product "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = '$act_do[product]'");
              db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("INSERT INTO $dbtables[logs] "
                        ."VALUES("
                        ."'',"
                        ."'$month[count]',"
                        ."'$year[count]',"
                        ."'$tribe[clanid]',"
                        ."'$tribe[tribeid]',"
                        ."'SEIGEEQ',"
                        ."'$stamp',"
                        ."'Heavy Weapons: $product $act_do[product] "
                        ."made using $deltacoal coal, $deltamtl "
                        ."$metalinfo[long_name], $deltawood logs, "
                        ."$deltawagon wagon.')");
               db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("DELETE FROM $dbtables[activities] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND product = '$act_do[product]'");

        }


        if( $act_do[product] == 'catapult' )
        {
            $wood = $db->Execute("SELECT * FROM $dbtables[products] "
                                ."WHERE long_name = 'logs' "
                                ."AND tribeid = '$tribe[goods_tribe]'");
                 db_op_result($wood,__LINE__,__FILE__);
            $woodinfo = $wood->fields;
            $startwood = $woodinfo[amount];
            $coal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                ."WHERE long_name = 'Coal' "
                                ."AND tribeid = '$tribe[goods_tribe]'");
               db_op_result($coal,__LINE__,__FILE__);
            $coalinfo = $coal->fields;
            $startcoal = $coalinfo[amount];
            $metal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                 ."WHERE long_name = 'Iron' "
                                 ."AND tribeid = '$tribe[goods_tribe]' "
                                 ."AND amount > 1");
              db_op_result($metal,__LINE__,__FILE__);
            if( $metal->EOF )
            {
                $metal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                     ."WHERE long_name = 'Bronze' "
                                     ."AND tribeid = '$tribe[goods_tribe]'");
                   db_op_result($metal,__LINE__,__FILE__);
            }
            $metalinfo = $metal->fields;
            $startmtl = $metalinfo[amount];
            $rope = $db->Execute("SELECT * FROM $dbtables[products] "
                                ."WHERE long_name = 'rope' "
                                ."AND tribeid = '$tribe[goods_tribe]'");
                db_op_result($rope,__LINE__,__FILE__);
            $ropeinfo = $rope->fields;
            $startrope = $ropeinfo[amount];
            $product = 0;
            while( $woodinfo[amount] > 9 && $metalinfo[amount] > 1 && $coalinfo[amount] > 29 && $ropeinfo[amount] > 3 && $act_do[actives] > 14 )
            {
               $woodinfo[amount] -= 10;
               $metalinfo[amount] -= 2;
               $coalinfo[amount] -= 30;
               $ropeinfo[amount] -= 4;
               $act_do[actives] -= 15;
               $product += 1;
            }
            $deltawood = $startwood - $woodinfo[amount];
            $deltacoal = $startcoal - $coalinfo[amount];
            $deltamtl = $startmtl - $metalinfo[amount];
            $deltarope = $startrope - $ropeinfo[amount];
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount - $deltawood "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'logs'");
             db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount - $deltarope "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'rope'");
              db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[resources] "
                        ."SET amount = amount - $deltacoal "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'Coal'");
             db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[resources] "
                        ."SET amount = amount - $deltamtl "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = '$metalinfo[long_name]'");
             db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + $product "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = '$act_do[product]'");
             db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("INSERT INTO $dbtables[logs] "
                        ."VALUES("
                        ."'',"
                        ."'$month[count]',"
                        ."'$year[count]',"
                        ."'$tribe[clanid]',"
                        ."'$tribe[tribeid]',"
                        ."'SEIGEEQ',"
                        ."'$stamp',"
                        ."'Heavy Weapons: $product $act_do[product] "
                        ."made using $deltacoal coal, $deltamtl "
                        ."$metalinfo[long_name], $deltawood logs, "
                        ."$deltarope ropes.')");
              db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("DELETE FROM $dbtables[activities] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND product = '$act_do[product]'");
            db_op_result($query,__LINE__,__FILE__);
        }


        if( $act_do[product] == 'ladder' )
        {
            $wood = $db->Execute("SELECT * FROM $dbtables[products] "
                                ."WHERE tribeid = '$tribe[goods_tribe]' "
                                ."AND long_name = 'logs'");
              db_op_result($wood,__LINE__,__FILE__);
            $woodinfo = $wood->fields;
            $startwood = $woodinfo[amount];
            $product = 0;
            while( $woodinfo[amount] > 0 && $act_do[actives] > 0 )
            {
                $woodinfo[amount] -= 1;
                $act_do[actives] -= 1;
                $product += 1;
            }
            $deltawood = $startwood - $woodinfo[amount];
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount - $deltawood "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'logs'");
              db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + $product "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'ladder'");
              db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("INSERT INTO $dbtables[logs] "
                        ."VALUES("
                        ."'',"
                        ."'$month[count]',"
                        ."'$year[count]',"
                        ."'$tribe[clanid]',"
                        ."'$tribe[tribeid]',"
                        ."'SEIGEEQ',"
                        ."'$stamp',"
                        ."'Heavy Weapons: $product $act_do[product] "
                        ."made using $deltawood logs.')");
             db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("DELETE FROM $dbtables[activities] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND product = '$act_do[product]'");
             db_op_result($query,__LINE__,__FILE__);
        }
        if( $act_do[product] == 'pavis' )
        {
            $wood = $db->Execute("SELECT * FROM $dbtables[products] "
                                ."WHERE tribeid = '$tribe[goods_tribe]' "
                                ."AND long_name = 'logs'");
             db_op_result($wood,__LINE__,__FILE__);
            $woodinfo = $wood->fields;
            $startwood = $woodinfo[amount];
            $product = 0;
            while( $woodinfo[amount] > 0 && $act_do[actives] > 0 )
            {
                $woodinfo[amount] -= 1;
                $act_do[actives] -= 1;
                $product += 1;
            }
            $deltawood = $startwood - $woodinfo[amount];
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount - $deltawood "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'logs'");
             db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + $product "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'pavis'");
             db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("INSERT INTO $dbtables[logs] "
                        ."VALUES("
                        ."'',"
                        ."'$month[count]',"
                        ."'$year[count]',"
                        ."'$tribe[clanid]',"
                        ."'$tribe[tribeid]',"
                        ."'SEIGEEQ',"
                        ."'$stamp',"
                        ."'Heavy Weapons: $product $act_do[product] "
                        ."made using $deltawood logs.')");
             db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("DELETE FROM $dbtables[activities] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND product = '$act_do[product]'");
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
