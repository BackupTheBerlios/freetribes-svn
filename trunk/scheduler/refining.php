<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: refining.php

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
                       ."WHERE tribeid = '$tribe[tribeid]'");
     db_op_result($act,__LINE__,__FILE__);

    while( !$act->EOF )
    {
        $act_do = $act->fields;


        if( $act_do[skill_abbr] == 'ref' )
        {
            $refiner = $db->Execute("SELECT * FROM $dbtables[structures] "
                                   ."WHERE long_name = 'refinery' "
                                   ."AND clanid = '$tribe[clanid]' "
                                   ."AND used = 'N' "
                                   ."AND hex_id = '$tribe[hex_id]'");
             db_op_result($refiner,__LINE__,__FILE__);
            $refinerinfo = $refiner->fields;


            $ref = $db->Execute("SELECT * FROM $dbtables[skills] "
                               ."WHERE abbr = 'ref' "
                               ."AND tribeid = '$tribe[tribeid]'");
              db_op_result($ref,__LINE__,__FILE__);
            $refskill = $ref->fields;

            $max_skill = 1000000;

            if( $refskill[level] < 10 )
            {
                $act_do[actives] = $refskill[level] * 10;
            }

            $max_refiners = $refinerinfo[number] * 10;

            if( $act_do[actives] > $max_refiners )
            {
                $act_do[actives] = $max_refiners;
            }

            if( !$refiner->EOF )
            {

                $coal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                    ."WHERE long_name = 'Coal' "
                                    ."AND tribeid = '$tribe[goods_tribe]' "
                                    ."AND amount > 0");
                  db_op_result($coal,__LINE__,__FILE__);
                $coalinfo = $coal->fields;
                $coalstart = $coalinfo[amount];
                $resource = 0;
                $normal = true;

                if( $act_do[product] == 'iron' )
                {

                    $ore = $db->Execute("SELECT * FROM $dbtables[resources] "
                                       ."WHERE long_name = 'Iron Ore' "
                                       ."AND tribeid = '$tribe[goods_tribe]' "
                                       ."AND amount > 0");
                      db_op_result($ore,__LINE__,__FILE__);
                    $oreinfo = $ore->fields;
                    $orestart = $oreinfo[amount];
                    while( $oreinfo[amount] > 19 && $coalinfo[amount] > 9 && $act_do[actives] > 0 )
                    {
                        $oreinfo[amount] -= 20;
                        $coalinfo[amount] -= 10;
                        $act_do[actives] -= 1;
                        $resource += 15;
                    }
                    $deltaore = $orestart - $oreinfo[amount];
                    $deltacoal = $coalstart - $coalinfo[amount];
                    $oreused = "using $deltaore iron ore and $deltacoal coal.";
                }
                elseif( $act_do[product] == 'copper' )
                {

                    $ore = $db->Execute("SELECT * FROM $dbtables[resources] "
                                       ."WHERE long_name = 'Copper Ore' "
                                       ."AND tribeid = '$tribe[goods_tribe]' "
                                       ."AND amount > 0");
                     db_op_result($ore,__LINE__,__FILE__);
                    $oreinfo = $ore->fields;
                    $orestart = $oreinfo[amount];
                    while( $oreinfo[amount] > 19 && $coalinfo[amount] > 3 && $act_do[actives] > 0 )
                    {
                        $oreinfo[amount] -= 20;
                        $coalinfo[amount] -= 4;
                        $act_do[actives] -= 1;
                        $resource += 15;
                    }
                    $deltaore = $orestart - $oreinfo[amount];
                    $deltacoal = $coalstart - $coalinfo[amount];
                    $oreused = "using $deltaore copper ore and $deltacoal coal.";
                }
                elseif( $act_do[product] == 'tin' )
                {
                    $ore = $db->Execute("SELECT * FROM $dbtables[resources] "
                                       ."WHERE long_name = 'Tin Ore' "
                                       ."AND tribeid = '$tribe[goods_tribe]' "
                                       ."AND amount > 0");
                       db_op_result($ore,__LINE__,__FILE__);
                    $oreinfo = $ore->fields;
                    $orestart = $oreinfo[amount];
                    while( $oreinfo[amount] > 19 && $coalinfo[amount] > 5 && $act_do[actives] > 0 )
                    {
                        $oreinfo[amount] -= 20;
                        $coalinfo[amount] -= 6;
                        $act_do[actives] -= 1;
                        $resource += 15;
                    }
                    $deltaore = $orestart - $oreinfo[amount];
                    $deltacoal = $coalstart - $coalinfo[amount];
                    $oreused = "using $deltaore tin ore and $deltacoal coal.";
                }
                elseif( $act_do[product] == 'zinc' )
                {
                    $ore = $db->Execute("SELECT * FROM $dbtables[resources] "
                                       ."WHERE long_name = 'Zinc Ore' "
                                       ."AND tribeid = '$tribe[goods_tribe]' "
                                       ."AND amount > 0");
                    db_op_result($ore,__LINE__,__FILE__);
                    $oreinfo = $ore->fields;
                    $orestart = $oreinfo[amount];
                    while( $oreinfo[amount] > 19 && $coalinfo[amount] > 7 && $act_do[actives] > 0 )
                    {
                        $oreinfo[amount] -= 20;
                        $coalinfo[amount] -= 8;
                        $act_do[actives] -= 1;
                        $resource += 15;
                    }
                    $deltaore = $orestart - $oreinfo[amount];
                    $deltacoal = $coalstart - $coalinfo[amount];
                    $oreused = "using $deltaore $oreinfo[long_name] and $deltacoal coal.";
                }
                elseif( $act_do[product] == 'lead' )
                {
                    $ore = $db->Execute("SELECT * FROM $dbtables[resources] "
                                       ."WHERE long_name = 'Lead Ore' "
                                       ."AND tribeid = '$tribe[goods_tribe]' "
                                       ."AND amount > 0");
                    db_op_result($ore,__LINE__,__FILE__);
                    $oreinfo = $ore->fields;
                    $orestart = $oreinfo[amount];
                    while( $oreinfo[amount] > 19 && $coalinfo[amount] > 5 && $act_do[actives] > 0 )
                    {
                        $oreinfo[amount] -= 20;
                        $coalinfo[amount] -= 6;
                        $act_do[actives] -= 1;
                        $resource += 15;
                    }
                    $deltaore = $orestart - $oreinfo[amount];
                    $deltacoal = $coalstart - $coalinfo[amount];
                    $oreused = "using $deltaore $oreinfo[long_name] and $deltacoal coal.";
                }
                elseif( $act_do[product] == 'gold' )
                {
                    $ore = $db->Execute("SELECT * FROM $dbtables[resources] "
                                       ."WHERE long_name = 'Gold Ore' "
                                       ."AND tribeid = '$tribe[goods_tribe]' "
                                       ."AND amount > 0");
                    db_op_result($ore,__LINE__,__FILE__);
                    $oreinfo = $ore->fields;
                    $orestart = $oreinfo[amount];
                    while( $oreinfo[amount] > 19 && $coalinfo[amount] > 5 && $act_do[actives] > 0 )
                    {
                        $oreinfo[amount] -= 20;
                        $coalinfo[amount] -= 6;
                        $act_do[actives] -= 1;
                        $resource += 15;
                    }
                    $deltaore = $orestart - $oreinfo[amount];
                    $deltacoal = $coalstart - $coalinfo[amount];
                    $oreused = "using $deltaore $oreinfo[long_name] and $deltacoal coal.";
                }
                elseif( $act_do[product] == 'silver' )
                {
                    $ore = $db->Execute("SELECT * FROM $dbtables[resources] "
                                       ."WHERE long_name = 'Silver Ore' "
                                       ."AND tribeid = '$tribe[goods_tribe]' "
                                       ."AND amount > 0");
                      db_op_result($ore,__LINE__,__FILE__);
                    $oreinfo = $ore->fields;
                    $orestart = $oreinfo[amount];
                    while( $oreinfo[amount] > 14 && $coalinfo[amount] > 5 && $act_do[actives] > 0 )
                    {
                        $oreinfo[amount] -= 15;
                        $coalinfo[amount] -= 6;
                        $act_do[actives] -= 1;
                        $resource += 15;
                    }
                    $deltaore = $orestart - $oreinfo[amount];
                    $deltacoal = $coalstart - $coalinfo[amount];
                    $oreused = "using $deltaore $oreinfo[long_name] and $deltacoal coal.";
                }
                elseif( $act_do[product] == 'bronze' )
                {
                    $copper = $db->Execute("SELECT * FROM $dbtables[resources] "
                                          ."WHERE long_name = 'Copper' "
                                          ."AND tribeid = '$tribe[goods_tribe]'");
                       db_op_result($copper,__LINE__,__FILE__);
                    $tin = $db->Execute("SELECT * FROM $dbtables[resources] "
                                       ."WHERE long_name = 'Tin' "
                                       ."AND tribeid = '$tribe[goods_tribe]'");
                       db_op_result($tin,__LINE__,__FILE__);
                    $copperinfo = $copper->fields;
                    $tininfo = $tin->fields;
                    $copperstart = $copperinfo[amount];
                    $tinstart = $tininfo[amount];

                    while( $copperinfo[amount] > 24 && $tininfo[amount] > 4 &&  $coalinfo[amount] > 9 && $act_do[actives] > 0 )
                    {
                        $copperinfo[amount] -= 25;
                        $tininfo[amount] -= 5;
                        $coalinfo[amount] -= 10;
                        $act_do[actives] -= 1;
                        $resource += 30;
                    }
                    $deltacopper = $copperstart - $copperinfo[amount];
                    $deltatin = $tinstart - $tininfo[amount];
                    $deltacoal = $coalstart - $coalinfo[amount];
                    $oreused = "using $deltacopper copper ore, $deltatin tin ore, and $deltacoal coal.";
                    $query = $db->Execute("UPDATE $dbtables[resources] "
                                ."SET amount = '$copperinfo[amount]' "
                                ."WHERE tribeid = '$tribe[goods_tribe]' "
                                ."AND long_name = 'Copper'");
                     db_op_result($query,__LINE__,__FILE__);
                    $query = $db->Execute("UPDATE $dbtables[resources] "
                                ."SET amount = '$tininfo[amount]' "
                                ."WHERE tribeid = '$tribe[goods_tribe]' "
                                ."AND long_name = 'Tin'");
                  db_op_result($query,__LINE__,__FILE__);
                }
                elseif( $act_do[product] == 'brass' )
                {
                    $copper = $db->Execute("SELECT * FROM $dbtables[resources] "
                                          ."WHERE long_name = 'Copper' "
                                          ."AND tribeid = '$tribe[goods_tribe]'");
                         db_op_result($copper,__LINE__,__FILE__);
                    $zinc = $db->Execute("SELECT * FROM $dbtables[resources] "
                                        ."WHERE long_name = 'Zinc' "
                                        ."AND tribeid = '$tribe[goods_tribe]'");
                          db_op_result($zinc,__LINE__,__FILE__);
                    $copperinfo = $copper->fields;
                    $zincinfo = $zinc->fields;
                    $copperstart = $copperinfo[amount];
                    $zincstart = $zincinfo[amount];

                    while( $copperinfo[amount] > 15 && $zincinfo[amount] > 3 && $coalinfo[amount] > 9 && $act_do[actives] > 0 )
                    {
                        $copperinfo[amount] -= 16;
                        $zincinfo[amount] -= 4;
                        $coalinfo[amount] -= 10;
                        $act_do[actives] -= 1;
                        $resource += 20;
                    }
                    $deltacopper = $copperstart - $copperinfo[amount];
                    $deltazinc = $zincstart - $zincinfo[amount];
                    $deltacoal = $coalstart - $coalinfo[amount];
                    $oreused = "using $deltacopper copper ore, $deltazinc zinc ore, and $deltacoal coal.";
                    $query = $db->Execute("UPDATE $dbtables[resources] "
                                ."SET amount = '$copperinfo[amount]' "
                                ."WHERE tribeid = '$tribe[goods_tribe]' "
                                ."AND long_name = 'Copper'");
                        db_op_result($query,__LINE__,__FILE__);
                    $query = $db->Execute("UPDATE $dbtables[resources] "
                                ."SET amount = '$zincinfo[amount]' "
                                ."WHERE tribeid = '$tribe[goods_tribe]' "
                                ."AND long_name = 'Zinc'");
                   db_op_result($query,__LINE__,__FILE__);
                }
                elseif( $act_do[product] == 'steel' )
                {
                    $normal = false;
                    $iron = $db->Execute("SELECT * FROM $dbtables[resources] "
                                        ."WHERE long_name = 'Iron' "
                                        ."AND tribeid = '$tribe[goods_tribe]'");
                       db_op_result($iron,__LINE__,__FILE__);
                    $coke = $db->Execute("SELECT * FROM $dbtables[resources] "
                                        ."WHERE long_name = 'Coke' "
                                        ."AND tribeid = '$tribe[goods_tribe]'");
                       db_op_result($coke,__LINE__,__FILE__);
                    $ironinfo = $iron->fields;
                    $cokeinfo = $coke->fields;
                    $ironstart = $ironinfo[amount];
                    $cokestart = $cokeinfo[amount];
                    while( $ironinfo[amount] > 19 && $cokeinfo[amount] > 4 && $act_do[actives] > 0 )
                    {
                        $cokeinfo[amount] -= 5;
                        $ironinfo[amount] -= 20;
                        $act_do[actives] -= 1;
                        $resource += 15;
                    }
                    $irondelta = $ironstart - $ironinfo[amount];
                    $cokedelta = $cokestart - $cokeinfo[amount];
                    $oreused = "using $cokedelta coke and $irondelta iron.";
                    $query = $db->Execute("UPDATE $dbtables[resources] "
                                ."SET amount = '$ironinfo[amount]' "
                                ."WHERE tribeid = '$tribe[goods_tribe]' "
                                ."AND long_name = 'Iron'");
                          db_op_result($query,__LINE__,__FILE__);
                    $query = $db->Execute("UPDATE $dbtables[resources] "
                                ."SET amount = '$cokeinfo[amount]' "
                                ."WHERE tribeid = '$tribe[goods_tribe]' "
                                ."AND long_name = 'Coke'");
                          db_op_result($query,__LINE__,__FILE__);
                    $query = $db->Execute("UPDATE $dbtables[resources] "
                                ."SET amount = amount + '$resource' "
                                ."WHERE tribeid = '$tribe[goods_tribe]' "
                                ."AND long_name = '$act_do[product]'");
                           db_op_result($query,__LINE__,__FILE__);

                }
                elseif( $act_do[product] == 'steel1' )
                {
                    $normal = false;
                    $iron = $db->Execute("SELECT * FROM $dbtables[resources] "
                                        ."WHERE long_name = 'Iron' "
                                        ."AND tribeid = '$tribe[goods_tribe]'");
                       db_op_result($iron,__LINE__,__FILE__);
                    $coke = $db->Execute("SELECT * FROM $dbtables[resources] "
                                        ."WHERE long_name = 'Coke' "
                                        ."AND tribeid = '$tribe[goods_tribe]'");
                        db_op_result($coke,__LINE__,__FILE__);
                    $ironinfo = $iron->fields;
                    $cokeinfo = $coke->fields;
                    $ironstart = $ironinfo[amount];
                    $cokestart = $cokeinfo[amount];

                    while( $ironinfo[amount] > 24 && $cokeinfo[amount] > 9 && $act_do[actives] > 0 )
                    {
                        $cokeinfo[amount] -= 10;
                        $ironinfo[amount] -= 25;
                        $act_do[actives] -= 1;
                        $resource += 15;
                    }
                    $irondelta = $ironstart - $ironinfo[amount];
                    $cokedelta = $cokestart - $cokeinfo[amount];
                    $oreused = "using $cokedelta coke and $irondelta iron.";
                    $query = $db->Execute("UPDATE $dbtables[resources] "
                                ."SET amount = '$ironinfo[amount]' "
                                ."WHERE tribeid = '$tribe[goods_tribe]' "
                                ."AND long_name = 'Iron'");
                      db_op_result($query,__LINE__,__FILE__);
                    $query = $db->Execute("UPDATE $dbtables[resources] "
                                ."SET amount = '$cokeinfo[amount]' "
                                ."WHERE tribeid = '$tribe[goods_tribe]' "
                                ."AND long_name = 'Coke'");
                         db_op_result($query,__LINE__,__FILE__);
                    $query = $db->Execute("UPDATE $dbtables[resources] "
                                ."SET amount = amount + '$resource' "
                                ."WHERE tribeid = '$tribe[goods_tribe]' "
                                ."AND long_name = '$act_do[product]'");
                      db_op_result($query,__LINE__,__FILE__);

                }
                elseif( $act_do[product] == 'steel2' )
                {
                    $normal = false;
                    $iron = $db->Execute("SELECT * FROM $dbtables[resources] "
                                        ."WHERE long_name = 'Iron' "
                                        ."AND tribeid = '$tribe[goods_tribe]'");
                      db_op_result($iron,__LINE__,__FILE__);
                    $coke = $db->Execute("SELECT * FROM $dbtables[resources] "
                                        ."WHERE long_name = 'Coke' "
                                        ."AND tribeid = '$tribe[goods_tribe]'");
                       db_op_result($coke,__LINE__,__FILE__);

                    $ironinfo = $iron->fields;
                    $cokeinfo = $coke->fields;
                    $ironstart = $ironinfo[amount];
                    $cokestart = $cokeinfo[amount];
                    while( $ironinfo[amount] > 29 && $cokeinfo[amount] > 9 && $act_do[actives] > 0 )
                    {
                        $cokeinfo[amount] -= 10;
                        $ironinfo[amount] -= 30;
                        $act_do[actives] -= 1;
                        $resource += 15;
                    }
                    $irondelta = $ironstart - $ironinfo[amount];
                    $cokedelta = $cokestart - $cokeinfo[amount];
                    $oreused = "using $cokedelta coke and $irondelta iron.";
                    $query = $db->Execute("UPDATE $dbtables[resources] "
                                ."SET amount = '$ironinfo[amount]' "
                                ."WHERE tribeid = '$tribe[goods_tribe]' "
                                ."AND long_name = 'Iron'");
                      db_op_result($query,__LINE__,__FILE__);
                    $query = $db->Execute("UPDATE $dbtables[resources] "
                                ."SET amount = '$cokeinfo[amount]' "
                                ."WHERE tribeid = '$tribe[goods_tribe]' "
                                ."AND long_name = 'Coke'");
                       db_op_result($query,__LINE__,__FILE__);
                    $query = $db->Execute("UPDATE $dbtables[resources] "
                                ."SET amount = amount + '$resource' "
                                ."WHERE tribeid = '$tribe[goods_tribe]' "
                                ."AND long_name = '$act_do[product]'");

                    db_op_result($query,__LINE__,__FILE__);
                }
                elseif( $act_do[product] == 'coke' )
                {

                    while( $coalinfo[amount] > 19 && $act_do[actives] > 0 )
                    {
                        $coalinfo[amount] -= 20;
                        $act_do[actives] -= 1;
                        $resource += 15;
                    }
                    $deltacoal = $coalstart - $coalinfo[amount];
                    $oreused = "using $deltacoal coal.";
                }
                elseif( $act_do[product] == 'sulphur' )
                {
                    $normal = false;
                    $st = $db->Execute("SELECT * FROM $dbtables[products] "
                                      ."WHERE long_name = 'stones' "
                                      ."AND tribeid = '$tribe[goods_tribe]' "
                                      ."AND amount > 0");
                     db_op_result($st,__LINE__,__FILE__);
                    $stones = $st->fields;
                    $stonestart = $stones[amount];
                    while( $coalinfo[amount] > 19 && $act_do[actives] > 0 && $stones[amount] > 9 )
                    {
                        $coalinfo[amount] -= 20;
                        $stones[amount] -= 10;
                        $act_do[actives] -= 1;
                        $resource += 10;
                    }
                    $deltastone = $stonestart - $stones[amount];
                    $deltacoal = $coalstart - $coalinfo[amount];
                    $oreused = "using $deltastone stones and $deltacoal coal.";
                    $query = $db->Execute("UPDATE $dbtables[products] "
                                ."SET amount = '$stones[amount]' "
                                ."WHERE long_name = 'stones' "
                                ."AND tribeid = '$tribe[goods_tribe]'");
                   db_op_result($query,__LINE__,__FILE__);
                    $query = $db->Execute("UPDATE $dbtables[resources] "
                                ."SET amount = '$coalinfo[amount]' "
                                ."WHERE long_name = 'Coal' "
                                ."AND tribeid = '$tribe[goods_tribe]'");
                    db_op_result($query,__LINE__,__FILE__);
                    $query = $db->Execute("UPDATE $dbtables[products] "
                                ."SET amount = amount + $resource "
                                ."WHERE long_name = 'sulphur' "
                                ."AND tribeid = '$tribe[goods_tribe]'");
                 db_op_result($query,__LINE__,__FILE__);
                }

                if( $normal )
                {
                $query = $db->Execute("UPDATE $dbtables[resources] "
                            ."SET amount = '$coalinfo[amount]' "
                            ."WHERE tribeid = '$tribe[goods_tribe]' "
                            ."AND long_name = 'Coal'");
                 db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("UPDATE $dbtables[resources] "
                            ."SET amount = amount + '$resource' "
                            ."WHERE tribeid = '$tribe[goods_tribe]' "
                            ."AND long_name = '$act_do[product]'");
                 db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("UPDATE $dbtables[resources] "
                            ."SET amount = '$oreinfo[amount]' "
                            ."WHERE tribeid = '$tribe[goods_tribe]' "
                            ."AND long_name = '$oreinfo[long_name]'");
                    db_op_result($query,__LINE__,__FILE__);
                }

                $query = $db->Execute("DELETE FROM $dbtables[activities] "
                            ."WHERE product = '$act_do[product]' "
                            ."AND tribeid = '$tribe[tribeid]' "
                            ."AND skill_abbr = '$act_do[skill_abbr]'");
                  db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("INSERT INTO $dbtables[logs] "
                            ."VALUES("
                            ."'',"
                            ."'$month[count]',"
                            ."'$year[count]',"
                            ."'$tribe[clanid]',"
                            ."'$tribe[tribeid]',"
                            ."'UPDATE','$stamp',"
                            ."'Activity: $resource $act_do[product] refined $oreused'"
                            .")");
               db_op_result($query,__LINE__,__FILE__);
            }

            $query = $db->Execute("DELETE FROM $dbtables[activities] "
                        ."WHERE product = '$act_do[product]' "
                        ."AND tribeid = '$tribe[tribeid]' "
                        ."AND skill_abbr = '$act_do[skill_abbr]'");
          db_op_result($query,__LINE__,__FILE__);

        }
        $query = $db->Execute("UPDATE $dbtables[structures] "
                    ."SET used = 'Y' "
                    ."WHERE struct_id = '$refinerinfo[struct_id]'");
         db_op_result($query,__LINE__,__FILE__);
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
