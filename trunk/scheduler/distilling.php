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
                       ."WHERE skill_abbr = 'dis' "
                       ."AND tribeid = '$tribe[tribeid]'");
      db_op_result($act,__LINE__,__FILE__);
    while( !$act->EOF )
    {
        $act_do = $act->fields;
        $still = $db->Execute("SELECT * FROM $dbtables[structures] "
                             ."WHERE long_name = 'distillery' "
                             ."AND hex_id = '$tribe[hex_id]' "
                             ."AND complete = 'Y' "
                             ."AND clanid = '$tribe[clanid]' "
                             ."AND used = 'N' "
                             ."AND number > 0");
            db_op_result($still,__LINE__,__FILE__);

        if( $act_do[product] == 'ale' && !$still->EOF )
        {
            $distillery = $still->fields;
            $gr = $db->Execute("SELECT * FROM $dbtables[products] "
                              ."WHERE tribeid = '$tribe[goods_tribe]' "
                              ."AND long_name = 'grain'");
              db_op_result($gr,__LINE__,__FILE__);
            $grain = $gr->fields;
            $startgrain = $grain[amount];
            $bar = $db->Execute("SELECT * FROM $dbtables[products] "
                               ."WHERE tribeid = '$tribe[goods_tribe]' "
                               ."AND long_name = 'barrels'");
                db_op_result($bar,__LINE__,__FILE__);
            $barrel = $bar->fields;
            $startbar = $barrel[amount];
            if( $act_do[actives] > ( $distillery[number] * 10 ) )
            {
                $act_do[actives] = ( $distillery[number] * 10 );
            }
            $grog = 0;
            while( $act_do[actives] > 4 && $grain[amount] > 99 && $barrel[amount] > 0 )
            {
                $act_do[actives] -= 5;
                $grain[amount] -= 100;
                $barrel[amount] -= 1;
                $grog += 100;
            }
            $deltagrain = $startgrain - $grain[amount];
            $deltabarrel = $startbar - $barrel[amount];
            $res = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + $grog "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'ale'");
               db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount - $deltagrain "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'grain'");
              db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount - $deltabarrel "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'barrels'");
                db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("DELETE FROM $dbtables[activities] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND skill_abbr = '$act_do[skill_abbr]' "
                        ."AND product = '$act_do[product]'");
               db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("UPDATE $dbtables[structures] "
                        ."SET used = 'Y' "
                        ."WHERE struct_id = '$distillery[struct_id]'");
               db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("INSERT INTO $dbtables[logs] "
                        ."VALUES("
                        ."'',"
                        ."'$month[count]',"
                        ."'$year[count]',"
                        ."'$tribe[clanid]',"
                        ."'$tribe[tribeid]',"
                        ."'DISTILL',"
                        ."'$stamp',"
                        ."'Distilling: $grog $act_do[product] distilled using $deltagrain grain, $deltabarrel barrels.')");
             db_op_result($res,__LINE__,__FILE__);
        }


        if( $act_do[product] == 'mead' && !$still->EOF )
        {
            $distillery = $still->fields;
            $hon = $db->Execute("SELECT * FROM $dbtables[products] "
                              ."WHERE tribeid = '$tribe[goods_tribe]' "
                              ."AND long_name = 'honey'");
               db_op_result($hon,__LINE__,__FILE__);
            $honey = $ho->fields;
            $starthoney = $honey[amount];
            $bar = $db->Execute("SELECT * FROM $dbtables[products] "
                               ."WHERE tribeid = '$tribe[goods_tribe]' "
                               ."AND long_name = 'barrels'");
               db_op_result($bar,__LINE__,__FILE__);
            $barrel = $bar->fields;
            $startbar = $barrel[amount];
            if( $act_do[actives] > ( $distillery[number] * 10 ) )
            {
                $act_do[actives] = ( $distillery[number] * 10 );
            }
            $grog = 0;
            while( $act_do[actives] > 4 && $honey[amount] > 19 && $barrel[amount] > 0 )
            {
                $act_do[actives] -= 5;
                $honey[amount] -= 20;
                $barrel[amount] -= 1;
                $grog += 100;
            }
            $deltahoney = $starthoney - $honey[amount];
            $deltabarrel = $startbar - $barrel[amount];
            $res = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + $grog "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'mead'");
                db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount - $deltahoney "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'honey'");
                db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount - $deltabarrel "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'barrels'");
                db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("UPDATE $dbtables[structures] "
                        ."SET used = 'Y' "
                        ."WHERE struct_id = '$distillery[struct_id]'");
               db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("DELETE FROM $dbtables[activities] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND skill_abbr = '$act_do[skill_abbr]' "
                        ."AND product = '$act_do[product]'");
               db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("INSERT INTO $dbtables[logs] "
                        ."VALUES("
                        ."'',"
                        ."'$month[count]',"
                        ."'$year[count]',"
                        ."'$tribe[clanid]',"
                        ."'$tribe[tribeid]',"
                        ."'DISTILL',"
                        ."'$stamp',"
                        ."'Distilling: $grog $act_do[product] distilled using $deltahoney honey, $deltabarrel barrels.')");
              db_op_result($res,__LINE__,__FILE__);
        }


        if( $act_do[product] == 'wine' && !$still->EOF )
        {
            $distillery = $still->fields;
            $gr = $db->Execute("SELECT * FROM $dbtables[products] "
                              ."WHERE tribeid = '$tribe[goods_tribe]' "
                              ."AND long_name = 'grapes'");
              db_op_result($gr,__LINE__,__FILE__);
            $grapes = $gr->fields;
            $startgrapes = $grapes[amount];
            $bar = $db->Execute("SELECT * FROM $dbtables[products] "
                               ."WHERE tribeid = '$tribe[goods_tribe]' "
                               ."AND long_name = 'barrels'");
               db_op_result($bar,__LINE__,__FILE__);
            $barrel = $bar->fields;
            $startbar = $barrel[amount];
            if( $act_do[actives] > ( $distillery[number] * 10 ) )
            {
                $act_do[actives] = ( $distillery[number] * 10 );
            }
            $grog = 0;
            while( $act_do[actives] > 4 && $grapes[amount] > 99 && $barrel[amount] > 0 )
            {
                $act_do[actives] -= 5;
                $grapes[amount] -= 100;
                $barrel[amount] -= 1;
                $grog += 100;
            }
            $deltagrapes = $startgrapes - $grapes[amount];
            $deltabarrel = $startbar - $barrel[amount];
            $res = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + $grog "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'wine'");
              db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount - $deltagrapes "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'grapes'");
              db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount - $deltabarrel "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'barrels'");
               db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("UPDATE $dbtables[structures] "
                        ."SET used = 'Y' "
                        ."WHERE struct_id = '$distillery[struct_id]'");
             db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("DELETE FROM $dbtables[activities] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND skill_abbr = '$act_do[skill_abbr]' "
                        ."AND product = '$act_do[product]'");
              db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("INSERT INTO $dbtables[logs] "
                        ."VALUES("
                        ."'',"
                        ."'$month[count]',"
                        ."'$year[count]',"
                        ."'$tribe[clanid]',"
                        ."'$tribe[tribeid]',"
                        ."'DISTILL',"
                        ."'$stamp',"
                        ."'Distilling: $grog $act_do[product] distilled using $deltagrapes grapes, $deltabarrel barrels.')");
                  db_op_result($res,__LINE__,__FILE__);
        }

        if( $act_do[product] == 'rum' && !$still->EOF )
        {
            $distillery = $still->fields;
            $su = $db->Execute("SELECT * FROM $dbtables[products] "
                              ."WHERE tribeid = '$tribe[goods_tribe]' "
                              ."AND long_name = 'sugar'");
              db_op_result($su,__LINE__,__FILE__);
            $sugar = $su->fields;
            $startsugar = $sugar[amount];
            $bar = $db->Execute("SELECT * FROM $dbtables[products] "
                               ."WHERE tribeid = '$tribe[goods_tribe]' "
                               ."AND long_name = 'barrels'");
              db_op_result($bar,__LINE__,__FILE__);
            $barrel = $bar->fields;
            $startbar = $barrel[amount];
            if( $act_do[actives] > ( $distillery[number] * 10 ) )
            {
                $act_do[actives] = ( $distillery[number] * 10 );
            }
            $grog = 0;
            while( $act_do[actives] > 4 && $sugar[amount] > 99 && $barrel[amount] > 0 )
            {
                $act_do[actives] -= 5;
                $sugar[amount] -= 100;
                $barrel[amount] -= 1;
                $grog += 100;
            }
            $deltasugar = $startsugar - $sugar[amount];
            $deltabarrel = $startbar - $barrel[amount];
            $res = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + $grog "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'rum'");
                db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount - $deltasugar "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'sugar'");
               db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount - $deltabarrel "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'barrels'");
              db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("UPDATE $dbtables[structures] "
                        ."SET used = 'Y' "
                        ."WHERE struct_id = '$distillery[struct_id]'");
              db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("DELETE FROM $dbtables[activities] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND skill_abbr = '$act_do[skill_abbr]' "
                        ."AND product = '$act_do[product]'");
              db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("INSERT INTO $dbtables[logs] "
                        ."VALUES("
                        ."'',"
                        ."'$month[count]',"
                        ."'$year[count]',"
                        ."'$tribe[clanid]',"
                        ."'$tribe[tribeid]',"
                        ."'DISTILL',"
                        ."'$stamp',"
                        ."'Distilling: $grog $act_do[product] distilled using $deltasugar sugar, $deltabarrel barrels.')");
           db_op_result($res,__LINE__,__FILE__);
        }


        if( $act_do[product] == 'brandy' && !$still->EOF )
        {
            $distillery = $still->fields;
            $su = $db->Execute("SELECT * FROM $dbtables[products] "
                              ."WHERE tribeid = '$tribe[goods_tribe]' "
                              ."AND long_name = 'sugar'");
            db_op_result($su,__LINE__,__FILE__);
            $sugar = $su->fields;
            $startsugar = $sugar[amount];
            $gr = $db->Execute("SELECT * FROM $dbtables[products] "
                              ."WHERE tribeid = '$tribe[goods_tribe]' "
                              ."AND long_name = 'grapes'");
             db_op_result($gr,__LINE__,__FILE__);
            $grapes = $gr->fields;
            $startgrapes = $grapes[amount];
            $bar = $db->Execute("SELECT * FROM $dbtables[products] "
                               ."WHERE tribeid = '$tribe[goods_tribe]' "
                               ."AND long_name = 'barrels'");
             db_op_result($bar,__LINE__,__FILE__);
            $barrel = $bar->fields;
            $startbar = $barrel[amount];
            if( $act_do[actives] > ( $distillery[number] * 10 ) )
            {
                $act_do[actives] = ( $distillery[number] * 10 );
            }
            $grog = 0;
            while( $act_do[actives] > 4 && $sugar[amount] > 49 && $grapes[amount] > 49 && $barrel[amount] > 0 )
            {
                $act_do[actives] -= 5;
                $sugar[amount] -= 50;
                $grapes[amount] -= 50;
                $barrel[amount] -= 1;
                $grog += 100;
            }
            $deltasugar = $startsugar - $sugar[amount];
            $deltabarrel = $startbar - $barrel[amount];
            $deltagrapes = $startgrapes - $grapes[amount];
            $res = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + $grog "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'brandy'");
               db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount - $deltasugar "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'sugar'");
               db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount - $deltagrapes "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'grapes'");
               db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount - $deltabarrel "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'barrels'");
              db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("UPDATE $dbtables[structures] "
                        ."SET used = 'Y' "
                        ."WHERE struct_id = '$distillery[struct_id]'");
              db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("DELETE FROM $dbtables[activities] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND skill_abbr = '$act_do[skill_abbr]' "
                        ."AND product = '$act_do[product]'");
              db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("INSERT INTO $dbtables[logs] "
                        ."VALUES("
                        ."'',"
                        ."'$month[count]',"
                        ."'$year[count]',"
                        ."'$tribe[clanid]',"
                        ."'$tribe[tribeid]',"
                        ."'DISTILL',"
                        ."'$stamp',"
                        ."'Distilling: $grog $act_do[product] distilled "
                        ."using $deltasugar sugar, $deltagrapes grapes, $deltabarrel barrels.')");
            db_op_result($res,__LINE__,__FILE__);
        }


        $act->MoveNext();
    }

    $res = $db->Execute("DELETE * FROM $dbtables[activities] "
                ."WHERE tribeid = '$tribe[tribeid]' "
                ."AND skill_abbr = 'dis'");
     db_op_result($res,__LINE__,__FILE__);


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
