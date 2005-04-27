<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: armormaking.php

require_once("../config.php");
$time_start = getmicrotime();
connectdb();
include("game_time.php");
$act = $db->Execute("SELECT * FROM $dbtables[activities] "
                   ."WHERE skill_abbr = 'arm'");
  db_op_result($act,__LINE__,__FILE__);
while( !$act->EOF )
{
    $act_do = $act->fields;
    $res = $db->Execute("SELECT * FROM $dbtables[tribes] "
                       ."WHERE tribeid = '$act_do[tribeid]'");
        db_op_result($res,__LINE__,__FILE__);
    $tribe = $res->fields;
    if( $act_do[product] == 'woodenshield' )
    {
        $logs = $db->Execute("SELECT * FROM $dbtables[products] "
                            ."WHERE long_name = 'logs' "
                            ."AND tribeid = '$tribe[goods_tribe]'");
          db_op_result($logs,__LINE__,__FILE__);
        $loginfo = $logs->fields;
        $logsused = 0;
        $shields = 0;
        $startlog = $loginfo[amount];
        while( $loginfo[amount] > 0 && $act_do[actives] > 1 )
        {
            $shields += 1;
            $loginfo[amount] -= 1;
            $logsused += 1;
            $act_do[actives] -= 2;
        }
        $logdelta = $startlog - $loginfo[amount];
        $res = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount + '$shields' "
                    ."WHERE tribeid = '$tribe[goods_tribe]' "
                    ."AND long_name = 'woodenshield'");
           db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("DELETE FROM $dbtables[activities] "
                    ."WHERE tribeid = '$tribe[tribeid]' "
                    ."AND skill_abbr = 'arm' "
                    ."AND product = '$act_do[product]'");
          db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount + '$logsused' "
                    ."WHERE tribeid = '$tribe[goods_tribe]' "
                    ."AND long_name = 'logs'");
         db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("INSERT INTO $dbtables[logs] "
                    ."VALUES("
                    ."'',"
                    ."'$month[count]',"
                    ."'$year[count]',"
                    ."'$tribe[clanid]',"
                    ."'$tribe[tribeid]',"
                    ."'UPDATE',"
                    ."'$stamp',"
                    ."'Armormaking: $shields Wooden Shields made using $logdelta logs.')");
          db_op_result($res,__LINE__,__FILE__);
    }

    if( $act_do[product] == 'haube' )
    {
        $brnz = $db->Execute("SELECT * FROM $dbtables[resources] "
                            ."WHERE long_name = 'Bronze' "
                            ."AND tribeid = '$tribe[goods_tribe]'");
          db_op_result($brnz,__LINE__,__FILE__);
        $coal = $db->Execute("SELECT * FROM $dbtables[resources] "
                            ."WHERE long_name = 'Coal' "
                            ."AND tribeid = '$tribe[goods_tribe]'");
          db_op_result($coal,__LINE__,__FILE__);
        $bronzeinfo = $brnz->fields;
        $coalinfo = $coal->fields;
        $haubemade = 0;
        $coal = $coalinfo[amount];
        $bronze = $bronzeinfo[amount];
        while( $bronze > 2 && $coal > 9 && $act_do[actives] > 1 )
        {
            $haubemade++;
            $bronze -= 3;
            $coal -= 10;
            $act_do[actives] -= 2;
        }
        $deltabronze = $bronzeinfo[amount] - $bronze;
        $deltacoal = $coalinfo[amount] - $coal;
        $res = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount + '$haubemade' "
                    ."WHERE tribeid = '$tribe[goods_tribe]' "
                    ."AND long_name = 'haube'");
          db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("UPDATE $dbtables[resources] "
                    ."SET amount = '$coal' "
                    ."WHERE tribeid = '$tribe[goods_tribe]' "
                    ."AND long_name = 'Coal'");
          db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("UPDATE $dbtables[resources] "
                    ."SET amount = '$bronze' "
                    ."WHERE tribeid = '$tribe[goods_tribe]' "
                    ."AND long_name = 'Bronze'");
         db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("DELETE FROM $dbtables[activities] "
                    ."WHERE tribeid = '$tribe[tribeid]' "
                    ."AND skill_abbr = 'arm' "
                    ."AND product = '$act_do[product]'");
         db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("INSERT INTO $dbtables[logs] "
                    ."VALUES("
                    ."'',"
                    ."'$month[count]',"
                    ."'$year[count]',"
                    ."'$tribe[clanid]',"
                    ."'$tribe[tribeid]',"
                    ."'UPDATE',"
                    ."'$stamp',"
                    ."'Armormaking: $haubemade Haubes made using $deltabronze "
                    ."bronze and $deltacoal coal.')");
         db_op_result($res,__LINE__,__FILE__);
    }

    if( $act_do[product] == 'scutum' )
    {
        $brnz = $db->Execute("SELECT * FROM $dbtables[resources] "
                            ."WHERE long_name = 'Bronze' "
                            ."AND tribeid = '$tribe[goods_tribe]'");
         db_op_result($brnz,__LINE__,__FILE__);
        $coal = $db->Execute("SELECT * FROM $dbtables[resources] "
                            ."WHERE long_name = 'Coal' "
                            ."AND tribeid = '$tribe[goods_tribe]'");
          db_op_result($coal,__LINE__,__FILE__);
        $bronzeinfo = $brnz->fields;
        $coalinfo = $coal->fields;
        $scutummade = 0;
        $coal = $coalinfo[amount];
        $bronze = $bronzeinfo[amount];
        while( $bronze > 4 && $coal > 14 && $act_do[actives] > 1 )
        {
            $scutummade++;
            $bronze -= 5;
            $coal -= 15;
            $act_do[actives] -= 2;
        }
        $deltabronze = $bronzeinfo[amount] - $bronze;
        $deltacoal = $coalinfo[amount] - $coal;
        $res = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount + '$scutummade' "
                    ."WHERE tribeid = '$tribe[goods_tribe]' "
                    ."AND long_name = 'scutum'");
          db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("UPDATE $dbtables[resources] "
                    ."SET amount = '$coal' "
                    ."WHERE tribeid = '$tribe[goods_tribe]' "
                    ."AND long_name = 'Coal'");
          db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("UPDATE $dbtables[resources] "
                    ."SET amount = '$bronze' "
                    ."WHERE tribeid = '$tribe[goods_tribe]' "
                    ."AND long_name = 'Bronze'");
          db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("DELETE FROM $dbtables[activities] "
                    ."WHERE tribeid = '$tribe[tribeid]' "
                    ."AND skill_abbr = 'arm' "
                    ."AND product = '$act_do[product]'");
         db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("INSERT INTO $dbtables[logs] "
                    ."VALUES("
                    ."'',"
                    ."'$month[count]',"
                    ."'$year[count]',"
                    ."'$tribe[clanid]',"
                    ."'$tribe[tribeid]',"
                    ."'UPDATE',"
                    ."'$stamp',"
                    ."'Armormaking: $scutummade Scutum made using $deltabronze bronze "
                    ."and $deltacoal coal.')");
        db_op_result($res,__LINE__,__FILE__);
    }

    if( $act_do[product] == 'ironshield' )
    {
        $ironore = $db->Execute("SELECT * FROM $dbtables[resources] "
                               ."WHERE long_name = 'Iron' "
                               ."AND tribeid = '$tribe[goods_tribe]'");
          db_op_result($ironore,__LINE__,__FILE__);
        $coal = $db->Execute("SELECT * FROM $dbtables[resources] "
                            ."WHERE long_name = 'Coal' "
                            ."AND tribeid = '$tribe[goods_tribe]'");
        db_op_result($coal,__LINE__,__FILE__);
        $ironinfo = $ironore->fields;
        $coalinfo = $coal->fields;
        $shield = 0;
        $coal = $coalinfo[amount];
        $iron = $ironinfo[amount];
        while( $iron > 4 && $coal > 29 && $act_do[actives] > 1 )
        {
            $shield++;
            $iron -= 5;
            $coal -= 30;
            $act_do[actives] -= 2;
        }
        $deltairon = $ironinfo[amount] - $iron;
        $deltacoal = $coalinfo[amount] - $coal;
        $res = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount + '$shield' "
                    ."WHERE tribeid = '$tribe[goods_tribe]' "
                    ."AND long_name = 'ironshield'");
         db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("UPDATE $dbtables[resources] "
                    ."SET amount = '$coal' "
                    ."WHERE tribeid = '$tribe[goods_tribe]' "
                    ."AND long_name = 'Coal'");
         db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("UPDATE $dbtables[resources] "
                    ."SET amount = '$iron' "
                    ."WHERE tribeid = '$tribe[goods_tribe]' "
                    ."AND long_name = 'Iron'");
         db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("DELETE FROM $dbtables[activities] "
                    ."WHERE tribeid = '$tribe[tribeid]' "
                    ."AND skill_abbr = 'arm' "
                    ."AND product = '$act_do[product]'");
          db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("INSERT INTO $dbtables[logs] "
                    ."VALUES("
                    ."'',"
                    ."'$month[count]',"
                    ."'$year[count]',"
                    ."'$tribe[clanid]',"
                    ."'$tribe[tribeid]',"
                    ."'UPDATE',"
                    ."'$stamp',"
                    ."'Armormaking: $shield Iron Shields made "
                    ."using $deltairon iron and $deltacoal coal.')");
        db_op_result($res,__LINE__,__FILE__);
    }

    if( $act_do[product] == 'steelshield' )
    {
            $stl = $db->Execute("SELECT * FROM $dbtables[resources] "
                               ."WHERE long_name = 'Steel' "
                               ."AND tribeid = '$tribe[goods_tribe]'");
              db_op_result($stl,__LINE__,__FILE__);
            $coke = $db->Execute("SELECT * FROM $dbtables[resources] "
                                ."WHERE long_name = 'Coke' "
                                ."AND tribeid = '$tribe[goods_tribe]'");
              db_op_result($coke,__LINE__,__FILE__);
            $steelinfo = $stl->fields;
            $cokeinfo = $coke->fields;
            $startsteel = $steelinfo[amount];
            $startcoke = $cokeinfo[amount];
            $shield = 0;
            $coke = $cokeinfo[amount];
            $steel = $steelinfo[amount];
            while( $steel > 4 && $coke > 19 && $act_do[actives] > 1 )
            {
                $shield++;
                $steel -= 5;
                $coke -= 20;
                $act_do[actives] -= 2;
            }
            $deltacoke = $startcoke - $coke;
            $deltasteel = $startsteel - $steel;
            $res = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + '$shield' "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'steelshield'");
               db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("UPDATE $dbtables[resources] "
                        ."SET amount = '$coke' "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'Coke'");
              db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("UPDATE $dbtables[resources] "
                        ."SET amount = '$steel' "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'Steel'");
              db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("DELETE FROM $dbtables[activities] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND skill_abbr = 'arm' "
                        ."AND product = '$act_do[product]'");
              db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("INSERT INTO $dbtables[logs] "
                        ."VALUES("
                        ."'',"
                        ."'$month[count]',"
                        ."'$year[count]',"
                        ."'$tribe[clanid]',"
                        ."'$tribe[tribeid]',"
                        ."'UPDATE',"
                        ."'$stamp',"
                        ."'Armormaking: $shield Steel shields made using $deltacoke coke and $deltasteel steel.')");
             db_op_result($res,__LINE__,__FILE__);
        }

        if( $act_do[product] == 'ironhelm' )
        {
            $iron = $db->Execute("SELECT * FROM $dbtables[resources] "
                                ."WHERE long_name = 'Iron' "
                                ."AND tribeid = '$tribe[goods_tribe]'");
             db_op_result($iron,__LINE__,__FILE__);
            $coal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                ."WHERE long_name = 'Coal' "
                                ."AND tribeid = '$tribe[goods_tribe]'");
             db_op_result($coal,__LINE__,__FILE__);
            $ironinfo = $iron->fields;
            $coalinfo = $coal->fields;
            $helm = 0;
            $startmtl = $ironinfo[amount];
            $startcoal = $coalinfo[amount];
            while( $ironinfo[amount] > 2 && $coalinfo[amount] > 19 && $act_do[actives] > 1 )
            {
                $helm += 1;
                $ironinfo[amount] -= 3;
                $coalinfo[amount] -= 20;
                $act_do[actives] -= 2;
            }
            $coaldelta = $startcoal - $coalinfo[amount];
            $mtldelta = $startmtl - $ironinfo[amount];
            $res = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + '$helm' "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'ironhelm'");
             db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("UPDATE $dbtables[resources] "
                        ."SET amount = amount - $mtldelta "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'Iron'");
             db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("UPDATE $dbtables[resources] "
                        ."SET amount = amount - '$coaldelta' "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'Coal'");
             db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("DELETE FROM $dbtables[activities] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND skill_abbr = 'arm' "
                        ."AND product = '$act_do[product]'");
             db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("INSERT INTO $dbtables[logs] "
                        ."VALUES("
                        ."'',"
                        ."'$month[count]',"
                        ."'$year[count]',"
                        ."'$tribe[clanid]',"
                        ."'$tribe[tribeid]',"
                        ."'UPDATE',"
                        ."'$stamp',"
                        ."'Armormaking: $helm Iron helms made using $mtldelta iron and $coaldelta coal.')");
              db_op_result($res,__LINE__,__FILE__);
        }

        if( $act_do[product] == 'ironchain' )
        {
            $ironore = $db->Execute("SELECT * FROM $dbtables[resources] "
                                   ."WHERE long_name = 'Iron' "
                                   ."AND tribeid = '$tribe[goods_tribe]'");
              db_op_result($ironore,__LINE__,__FILE__);
            $coal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                ."WHERE long_name = 'Coal' "
                                ."AND tribeid = '$tribe[goods_tribe]'");
             db_op_result($coal,__LINE__,__FILE__);
            $ironinfo = $ironore->fields;
            $coalinfo = $coal->fields;
            $product = 0;
            $startcoal = $coalinfo[amount];
            $startiron = $ironinfo[amount];
            while( $ironinfo[amount] > 17 && $coalinfo[amount] > 39 && $act_do[actives] > 3 )
            {
                $product++;
                $ironinfo[amount] -= 18;
                $coalinfo[amount] -= 40;
                $act_do[actives] -= 4;
            }
            $coaldelta = $startcoal - $coalinfo[amount];
            $irondelta = $startiron - $ironinfo[amount];
            $res = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + '$product' "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = '$act_do[product]'");
              db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("UPDATE $dbtables[resources] "
                        ."SET amount = amount - '$coaldelta' "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'Coal'");
             db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("UPDATE $dbtables[resources] "
                        ."SET amount = amount - '$irondelta' "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'Iron'");
             db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("DELETE FROM $dbtables[activities] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND skill_abbr = 'arm' "
                        ."AND product = '$act_do[product]'");
              db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("INSERT INTO $dbtables[logs] "
                        ."VALUES("
                        ."'',"
                        ."'$month[count]',"
                        ."'$year[count]',"
                        ."'$tribe[clanid]',"
                        ."'$tribe[tribeid]',"
                        ."'UPDATE',"
                        ."'$stamp',"
                        ."'Armormaking: $product Iron Chainmail made using $coaldelta coal and $irondelta iron.')");
            db_op_result($res,__LINE__,__FILE__);
        }

        if( $act_do[product] == 'cuirass' )
        {
            $mtl = $db->Execute("SELECT * FROM $dbtables[resources] "
                               ."WHERE long_name = 'Bronze' "
                               ."AND tribeid = '$tribe[goods_tribe]'");
              db_op_result($mtl,__LINE__,__FILE__);
            $coal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                ."WHERE long_name = 'Coal' "
                                ."AND tribeid = '$tribe[goods_tribe]'");
             db_op_result($coal,__LINE__,__FILE__);
            $mtlinfo = $mtl->fields;
            $coalinfo = $coal->fields;
            $product = 0;
            $coal = $coalinfo[amount];
            $startcoal = $coal;
            $mtl = $mtlinfo[amount];
            $startmtl = $mtl;
            while( $mtl > 29 && $coal > 19 && $act_do[actives] > 2 )
            {
                $product++;
                $mtl -= 30;
                $coal -= 20;
                $act_do[actives] -= 3;
            }
            $coaldelta = $startcoal - $coal;
            $mtldelta = $startmtl - $mtl;
            $res = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + '$product' "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = '$act_do[product]'");
              db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("UPDATE $dbtables[resources] "
                        ."SET amount = amount - '$coaldelta' "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'Coal'");
             db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("UPDATE $dbtables[resources] "
                        ."SET amount = amount - '$mtldelta' "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'Bronze'");
             db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("DELETE FROM $dbtables[activities] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND skill_abbr = 'arm' "
                        ."AND product = '$act_do[product]'");
              db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("INSERT INTO $dbtables[logs] "
                        ."VALUES("
                        ."'',"
                        ."'$month[count]',"
                        ."'$year[count]',"
                        ."'$tribe[clanid]',"
                        ."'$tribe[tribeid]',"
                        ."'UPDATE',"
                        ."'$stamp',"
                        ."'Armormaking: $product Cuirass made using $coaldelta coal and $mtldelta bronze.')");
             db_op_result($res,__LINE__,__FILE__);
        }

        if( $act_do[product] == 'ironbreastplate' )
        {
            $mtl = $db->Execute("SELECT * FROM $dbtables[resources] "
                               ."WHERE long_name = 'Iron' "
                               ."AND tribeid = '$tribe[goods_tribe]'");
             db_op_result($mtl,__LINE__,__FILE__);
            $coal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                ."WHERE long_name = 'Coal' "
                                ."AND tribeid = '$tribe[goods_tribe]'");
             db_op_result($coal,__LINE__,__FILE__);
            $mtlinfo = $mtl->fields;
            $coalinfo = $coal->fields;
            $product = 0;
            $coal = $coalinfo[amount];
            $startcoal = $coal;
            $mtl = $mtlinfo[amount];
            $startmtl = $mtl;
            while( $mtl > 19 && $coal > 39 && $act_do[actives] > 3 )
            {
                $product++;
                $mtl -= 20;
                $coal -= 40;
                $act_do[actives] -= 4;
            }
            $coaldelta = $startcoal - $coal;
            $mtldelta = $startmtl - $mtl;
            $res = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + '$product' "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = '$act_do[product]'");
             db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("UPDATE $dbtables[resources] "
                        ."SET amount = amount - '$coaldelta' "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'Coal'");
            db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("UPDATE $dbtables[resources] "
                        ."SET amount = amount - '$mtldelta' "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'Iron'");
            db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("DELETE FROM $dbtables[activities] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND skill_abbr = 'arm' "
                        ."AND product = '$act_do[product]'");
             db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("INSERT INTO $dbtables[logs] "
                        ."VALUES("
                        ."'',"
                        ."'$month[count]',"
                        ."'$year[count]',"
                        ."'$tribe[clanid]',"
                        ."'$tribe[tribeid]',"
                        ."'UPDATE',"
                        ."'$stamp',"
                        ."'Armormaking: $product Iron breastplates made using $coaldelta coal and $mtldelta iron.')");
           db_op_result($res,__LINE__,__FILE__);
        }
        if( $act_do[product] == 'ironplatebarding' )
        {
            $mtl = $db->Execute("SELECT * FROM $dbtables[resources] "
                               ."WHERE long_name = 'Iron' "
                               ."AND tribeid = '$tribe[goods_tribe]'");
             db_op_result($mtl,__LINE__,__FILE__);
            $coal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                ."WHERE long_name = 'Coal' "
                                ."AND tribeid = '$tribe[goods_tribe]'");
              db_op_result($coal,__LINE__,__FILE__);
            $mtlinfo = $mtl->fields;
            $coalinfo = $coal->fields;
            $product = 0;
            $coal = $coalinfo[amount];
            $startcoal = $coal;
            $mtl = $mtlinfo[amount];
            $startmtl = $mtl;
            while( $mtl > 19 && $coal > 39 && $act_do[actives] > 3 )
            {
                $product++;
                $mtl -= 20;
                $coal -= 40;
                $act_do[actives] -= 4;
            }
            $coaldelta = $startcoal - $coal;
            $mtldelta = $startmtl - $mtl;
            $res = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + '$product' "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = '$act_do[product]'");
             db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("UPDATE $dbtables[resources] "
                        ."SET amount = amount - '$coaldelta' "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'Coal'");
            db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("UPDATE $dbtables[resources] "
                        ."SET amount = amount - '$mtldelta' "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'Iron'");
             db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("DELETE FROM $dbtables[activities] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND skill_abbr = 'arm' "
                        ."AND product = '$act_do[product]'");
            db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("INSERT INTO $dbtables[logs] "
                        ."VALUES("
                        ."'',"
                        ."'$month[count]',"
                        ."'$year[count]',"
                        ."'$tribe[clanid]',"
                        ."'$tribe[tribeid]',"
                        ."'UPDATE',"
                        ."'$stamp',"
                        ."'Armormaking: $product Iron platemail barding made using $coaldelta coal and $mtldelta iron.')");
             db_op_result($res,__LINE__,__FILE__);
        }
        if( $act_do[product] == 'ironchainbarding' )
        {
            $mtl = $db->Execute("SELECT * FROM $dbtables[resources] "
                               ."WHERE long_name = 'Iron' "
                               ."AND tribeid = '$tribe[goods_tribe]'");
            db_op_result($mtl,__LINE__,__FILE__);
            $coal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                ."WHERE long_name = 'Coal' "
                                ."AND tribeid = '$tribe[goods_tribe]'");
            db_op_result($coal,__LINE__,__FILE__);
            $mtlinfo = $mtl->fields;
            $coalinfo = $coal->fields;
            $product = 0;
            $coal = $coalinfo[amount];
            $startcoal = $coal;
            $mtl = $mtlinfo[amount];
            $startmtl = $mtl;
            while( $mtl > 17 && $coal > 29 && $act_do[actives] > 2 )
            {
                $product++;
                $mtl -= 18;
                $coal -= 30;
                $act_do[actives] -= 3;
            }
            $coaldelta = $startcoal - $coal;
            $mtldelta = $startmtl - $mtl;
            $res = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + '$product' "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = '$act_do[product]'");
            db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("UPDATE $dbtables[resources] "
                        ."SET amount = amount - '$coaldelta' "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'Coal'");
             db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("UPDATE $dbtables[resources] "
                        ."SET amount = amount - '$mtldelta' "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'Iron'");
             db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("DELETE FROM $dbtables[activities] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND skill_abbr = 'arm' "
                        ."AND product = '$act_do[product]'");
            db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("INSERT INTO $dbtables[logs] "
                        ."VALUES("
                        ."'',"
                        ."'$month[count]',"
                        ."'$year[count]',"
                        ."'$tribe[clanid]',"
                        ."'$tribe[tribeid]',"
                        ."'UPDATE',"
                        ."'$stamp',"
                        ."'Armormaking: $product Iron chain mail barding made using $coaldelta coal and $mtldelta iron.')");
             db_op_result($res,__LINE__,__FILE__);
    }


        if( $act_do[product] == 'steelchainbarding' )
        {
            $mtl = $db->Execute("SELECT * FROM $dbtables[resources] "
                               ."WHERE long_name = 'Steel' "
                               ."AND tribeid = '$tribe[goods_tribe]'");
            db_op_result($mtl,__LINE__,__FILE__);
            $coal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                ."WHERE long_name = 'Coke' "
                                ."AND tribeid = '$tribe[goods_tribe]'");
            db_op_result($coal,__LINE__,__FILE__);
            $mtlinfo = $mtl->fields;
            $coalinfo = $coal->fields;
            $product = 0;
            $coal = $coalinfo[amount];
            $startcoal = $coal;
            $mtl = $mtlinfo[amount];
            $startmtl = $mtl;
            while( $mtl > 17 && $coal > 29 && $act_do[actives] > 2 )
            {
                $product++;
                $mtl -= 18;
                $coal -= 30;
                $act_do[actives] -= 3;
            }
            $coaldelta = $startcoal - $coal;
            $mtldelta = $startmtl - $mtl;
            $res = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + '$product' "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = '$act_do[product]'");
             db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("UPDATE $dbtables[resources] "
                        ."SET amount = amount - '$coaldelta' "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'Coke'");
            db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("UPDATE $dbtables[resources] "
                        ."SET amount = amount - '$mtldelta' "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'Steel'");
            db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("DELETE FROM $dbtables[activities] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND skill_abbr = 'arm' "
                        ."AND product = '$act_do[product]'");
             db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("INSERT INTO $dbtables[logs] "
                        ."VALUES("
                        ."'',"
                        ."'$month[count]',"
                        ."'$year[count]',"
                        ."'$tribe[clanid]',"
                        ."'$tribe[tribeid]',"
                        ."'UPDATE',"
                        ."'$stamp',"
                        ."'Armormaking: $product Steel chain mail barding made using $coaldelta coke and $mtldelta iron.')");
            db_op_result($res,__LINE__,__FILE__);
    }


        if( $act_do[product] == 'steelplatebarding' )
        {
            $mtl = $db->Execute("SELECT * FROM $dbtables[resources] "
                               ."WHERE long_name = 'Steel' "
                               ."AND tribeid = '$tribe[goods_tribe]'");
             db_op_result($mtl,__LINE__,__FILE__);
            $coal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                ."WHERE long_name = 'Coke' "
                                ."AND tribeid = '$tribe[goods_tribe]'");
             db_op_result($coal,__LINE__,__FILE__);
            $mtlinfo = $mtl->fields;
            $coalinfo = $coal->fields;
            $product = 0;
            $coal = $coalinfo[amount];
            $startcoal = $coal;
            $mtl = $mtlinfo[amount];
            $startmtl = $mtl;
            while( $mtl > 19 && $coal > 39 && $act_do[actives] > 3 )
            {
                $product++;
                $mtl -= 20;
                $coal -= 40;
                $act_do[actives] -= 4;
            }
            $coaldelta = $startcoal - $coal;
            $mtldelta = $startmtl - $mtl;
            $res = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + '$product' "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = '$act_do[product]'");
             db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("UPDATE $dbtables[resources] "
                        ."SET amount = amount - '$coaldelta' "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'Coke'");
              db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("UPDATE $dbtables[resources] "
                        ."SET amount = amount - '$mtldelta' "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'Steel'");
              db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("DELETE FROM $dbtables[activities] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND skill_abbr = 'arm' "
                        ."AND product = '$act_do[product]'");
                        db_op_result($res,__LINE__,__FILE__);

            $res = $db->Execute("INSERT INTO $dbtables[logs] "
                        ."VALUES("
                        ."'',"
                        ."'$month[count]',"
                        ."'$year[count]',"
                        ."'$tribe[clanid]',"
                        ."'$tribe[tribeid]',"
                        ."'UPDATE',"
                        ."'$stamp',"
                        ."'Armormaking: $product Steel platemail barding made using $coaldelta coke and $mtldelta iron.')");
                        db_op_result($res,__LINE__,__FILE__);
        }





    $act->MoveNext();
}
$time_end = getmicrotime();
$time = $time_end - $time_start;
$page_name =   str_replace($game_root."scheduler/",'',__FILE__);//ereg("([^/]*).php", $_SERVER['PHP_SELF'], $page_name); // get the name of the file being viewed
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
