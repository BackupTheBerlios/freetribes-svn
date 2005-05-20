<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: waxwork.php
$pos = (strpos($_SERVER['PHP_SELF'], "/waxwork.php"));
if ($pos !== false)
{
    die("You cannot access this page directly!");
}
require_once("config.php");
$time_start = getmicrotime();
include("scheduler/game_time.php");
connectdb();

    $act = $db->Execute("SELECT * FROM $dbtables[activities] WHERE skill_abbr LIKE '%wax%'");
    db_op_result($act,__LINE__,__FILE__);
    while( !$act->EOF )
    {
        $act_do = $act->fields;
        $res = $db->Execute("SELECT * FROM $dbtables[tribes] WHERE tribeid = '$act_do[tribeid]'");
         db_op_result($res,__LINE__,__FILE__);
        $tribe = $res->fields;

        if( $act_do['product'] == 'parchment' )
        {
            $wax = $db->Execute("SELECT * FROM $dbtables[products] "
                               ."WHERE tribeid = '$tribe[goods_tribe]' "
                               ."AND long_name = 'wax'");
              db_op_result($wax,__LINE__,__FILE__);
            $waxinfo = $wax->fields;

            $skin = $db->Execute("SELECT * FROM $dbtables[products] "
                                ."WHERE tribeid = '$tribe[goods_tribe]' "
                                ."AND long_name = 'skins'");
              db_op_result($skin,__LINE__,__FILE__);
            $skininfo = $skin->fields;

            $waxstart = $waxinfo['amount'];
            $skinstart = $skininfo['amount'];
            $parchment = 0;

            while( $act_do['actives'] > 0 & $skininfo['amount'] > 4 & $waxinfo['amount'] > 1 )
            {
                $parchment += 5;
                $act_do['actives'] -= 1;
                $skininfo['amount'] -= 5;
                $waxinfo['amount'] -= 1;
            }

            $waxdelta = $waxstart - $waxinfo['amount'];
            $skindelta = $skinstart - $skininfo['amount'];

            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount - $skindelta "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'skins'");
              db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount - $waxdelta "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'wax'");
               db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + $parchment "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'parchment'");
              db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("DELETE FROM $dbtables[activities] "
                        ."WHERE skill_abbr = 'wax' "
                        ."AND tribeid = '$tribe[tribeid]' "
                        ."AND product = '$act_do[product]'");
             db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("INSERT INTO $dbtables[logs] "
                        ."VALUES('','$month[count]',"
                        ."'$year[count]','$tribe[clanid]',"
                        ."'$tribe[tribeid]','UPDATE','$stamp',"
                        ."'Waxworking: $parchment Parchment "
                        ."made using $skindelta skins and $waxdelta wax.')");
             db_op_result($query,__LINE__,__FILE__);
        }

        if( $act_do['product'] == 'strings' )
        {
            $wax = $db->Execute("SELECT * FROM $dbtables[products] "
                               ."WHERE tribeid = '$tribe[goods_tribe]' "
                               ."AND long_name = 'wax'");
                db_op_result($wax,__LINE__,__FILE__);
            $waxinfo = $wax->fields;
            $sec = $db->Execute("SELECT * FROM $dbtables[products] "
                               ."WHERE tribeid = '$tribe[goods_tribe]' "
                               ."AND amount > 0 "
                               ."AND long_name = 'gut'");
               db_op_result($sec,__LINE__,__FILE__);
            if( $sec->EOF )
            {
                $sec = $db->Execute("SELECT * FROM $dbtables[products] "
                                   ."WHERE tribeid = '$tribe[goods_tribe]' "
                                   ."AND long_name = 'cotton'");
                  db_op_result($sec,__LINE__,__FILE__);
            }

            $secinfo = $sec->fields;
            $waxstart = $waxinfo['amount'];
            $secstart = $secinfo['amount'];
            $string = 0;

            while( $act_do['actives'] > 0 & $secinfo['amount'] > 0 & $waxinfo['amount'] > 0 )
            {
                $string += 5;
                $act_do['actives'] -= 1;
                $secinfo['amount'] -= 1;
                $waxinfo['amount'] -= 1;
            }

            $waxdelta = $waxstart - $waxinfo['amount'];
            $secdelta = $secstart - $secinfo['amount'];

            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount - $secdelta "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = '$secinfo[long_name]'");
                db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount - $waxdelta "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'wax'");
                db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + $string "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'strings'");
                db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("DELETE FROM $dbtables[activities] "
                        ."WHERE skill_abbr = 'wax' "
                        ."AND tribeid = '$tribe[tribeid]' "
                        ."AND product = '$act_do[product]'");
               db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("INSERT INTO $dbtables[logs] "
                        ."VALUES('','$month[count]','$year[count]',"
                        ."'$tribe[clanid]','$tribe[tribeid]','UPDATE',"
                        ."'$stamp','Waxworking: $string Strings made "
                        ."using $secdelta $secinfo[proper] and $waxdelta Wax.')");
              db_op_result($query,__LINE__,__FILE__);
        }


        if( $act_do['product'] == 'candles' )
        {
            $cal = $db->Execute("SELECT * FROM $dbtables[products] "
                               ."WHERE tribeid = '$tribe[goods_tribe]' "
                               ."AND long_name = 'cauldron'");
               db_op_result($query,__LINE__,__FILE__);
            $caldron = $cal->fields;

            $wax = $db->Execute("SELECT * FROM $dbtables[products] "
                               ."WHERE tribeid = '$tribe[goods_tribe]' "
                               ."AND long_name = 'wax'");
               db_op_result($query,__LINE__,__FILE__);
            $waxinfo = $wax->fields;

            $cot = $db->Execute("SELECT * FROM $dbtables[products] "
                               ."WHERE tribeid = '$tribe[goods_tribe]' "
                               ."AND long_name = 'cotton'");
               db_op_result($query,__LINE__,__FILE__);
            $cotton = $cot->fields;

            $coal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                ."WHERE tribeid = '$tribe[goods_tribe]' "
                                ."AND long_name = 'Coal'");
              db_op_result($query,__LINE__,__FILE__);
            $coalinfo = $coal->fields;

            $waxstart = $waxinfo['amount'];
            $startcot = $cotton['amount'];
            $startcol = $coalinfo['amount'];
            $candle = 0;

            while( $caldron['amount'] > 0 & $act_do['actives'] > 3 & $waxinfo['amount'] > 19 & $cotton['amount'] > 0 & $coalinfo['amount'] > 4 )
            {
                $candle += 1;
                $act_do['actives'] -= 4;
                $waxinfo['amount'] -= 20;
                $cotton['amount'] -= 1;
                $coalinfo['amount'] -= 5;
            }

            $waxdelta = $waxstart - $waxinfo['amount'];
            $cotdelta = $startcot - $cotton['amount'];
            $coaldelta = $startcol - $coalinfo['amount'];

            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount - $waxdelta "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'wax'");
                db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount - $cotdelta "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'cotton'");
               db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[resources] "
                        ."SET amount = amount - $coaldelta "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'Coal'");
                db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + $candle "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'candles'");
                  db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("DELETE FROM $dbtables[activities] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND skill_abbr = 'wax' "
                        ."AND product = 'candles'");
               db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("INSERT INTO $dbtables[logs] "
                        ."VALUES('','$month[count]','$year[count]',"
                        ."'$tribe[clanid]','$tribe[tribeid]','UPDATE',"
                        ."'$stamp','Waxworking: $candle made using $waxdelta "
                        ."Wax, $cotdelta Cotton, and $coaldelta Coal.')");
              db_op_result($query,__LINE__,__FILE__);
        }

        if( $act_do['product'] == 'cuirboilli' )
        {
            $cal = $db->Execute("SELECT * FROM $dbtables[products] "
                               ."WHERE tribeid = '$tribe[goods_tribe]' "
                               ."AND long_name = 'cauldron'");
               db_op_result($cal,__LINE__,__FILE__);
            $caldron = $cal->fields;

            $wax = $db->Execute("SELECT * FROM $dbtables[products] "
                               ."WHERE tribeid = '$tribe[goods_tribe]' "
                               ."AND long_name = 'wax'");
               db_op_result($wax,__LINE__,__FILE__);
            $waxinfo = $wax->fields;

            $coal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                ."WHERE tribeid = '$tribe[goods_tribe]' "
                                ."AND long_name = 'Coal'");
              db_op_result($coal,__LINE__,__FILE__);
            $coalinfo = $coal->fields;

            $leat = $db->Execute("SELECT * FROM $dbtables[products] "
                                ."WHERE tribeid = '$tribe[goods_tribe]' "
                                ."AND long_name = 'leather'");
               db_op_result($leat,__LINE__,__FILE__);
            $leather = $leat->fields;

            $waxstart = $waxinfo['amount'];
            $startcol = $coalinfo['amount'];
            $startleather = $leather['amount'];

            if( $waxinfo['amount'] > 11 & $coalinfo['amount'] > 1 & $leather['amount'] > 1 & $cauldron['amount'] > 0 )
            {
                $waxinfo['amount'] -= 10;
            }

            $armor = 0;

            while( $act_do['actives'] > 1 && $waxinfo['amount'] > 1 && $coalinfo['amount'] > 1 && $leather['amount'] > 1 && $cauldron['amount'] > 0 )
            {
                $armor += 1;
                $act_do['actives'] -= 2;
                $waxinfo['amount'] -= 2;
                $coalinfo['amount'] -= 2;
                $leather['amount'] -= 2;
            }

            $waxdelta = $waxstart - $waxinfo['amount'];
            $coaldelta = $startcol - $coalinfo['amount'];
            $leatherdelta = $startleather - $leather['amount'];

            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount - $waxdelta "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'wax'");
               db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[resources] "
                        ."SET amount = amount - $coaldelta "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'Coal'");
                  db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount - $leatherdelta "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'leather'");
                   db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("DELETE FROM $dbtables[activities] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND skill_abbr = 'wax' "
                        ."AND product = 'cuirboilli'");
                  db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("INSERT INTO $dbtables[logs] "
                        ."VALUES('','$month[count]','$year[count]',"
                        ."'$tribe[clanid]','$tribe[tribeid]','UPDATE',"
                        ."'$stamp','Waxworking: $armor Cuirboilli made "
                        ."using $waxdelta wax, $coaldelta coal, and $leatherdelta leather.')");
             db_op_result($query,__LINE__,__FILE__);
        }

        $act->MoveNext();
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
