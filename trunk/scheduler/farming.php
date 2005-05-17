<?php
$pos = (strpos($_SERVER['PHP_SELF'], "/farming.php"));
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
while( !$res->EOF )
{
    $tribe = $res->fields;

    $act = $db->Execute("SELECT * FROM $dbtables[farm_activities] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND hex_id = '$tribe[hex_id]' "
                        ."AND action = 'plow'");
        db_op_result($act,__LINE__,__FILE__);
    $hex = $db->Execute("SELECT terrain FROM $dbtables[hexes] "
                       ."WHERE hex_id = '$tribe[hex_id]'");
       db_op_result($hex,__LINE__,__FILE__);
    $hexinfo = $hex->fields;

    if( $hexinfo['terrain'] == 'pr' )
    {
        $bonus_acres = 1;
    }
    elseif( $hexinfo['terrain'] == 'gh' )
    {
        $bonus_acres = .5;
    }
    elseif( $hexinfo['terrain'] == 'df' | $hexinfo['terrain'] == 'cf' | $hexinfo['terrain'] == 'jg' )
    {
        $bonus_acres = 0;
    }
    elseif( $hexinfo['terrain'] == 'dh' | $hexinfo['terrain'] == 'ch' | $hexinfo['terrain'] == 'jh' )
    {
        $bonus_acres = -.5;
    }
    elseif( $hexinfo['terrain'] == 'sw' | $hexinfo['terrain'] == 'ljm' | $hexinfo['terrain'] == 'lcm' )
    {
        $bonus_acres = -.75;
    }
    else
    {
        $bonus_acres = -.95;
    }

    while( !$act->EOF )
    {
        $act_do = $act->fields;
        if( $game_debug )
        {
            $result = $db->Execute("INSERT INTO $dbtables[logs] "
                        ."VALUES("
                        ."'',"
                        ."'$month[count]',"
                        ."'$year[count]',"
                        ."'$tribe[clanid]',"
                        ."'$tribe[tribeid]',"
                        ."'DEBUG',"
                        ."'$stamp',"
                        ."'Farming debug: $tribe[tribeid] tried $act_do[action] $act_do[crop] with $act_do[actives] actives.')");
          db_op_result($result,__LINE__,__FILE__);
        }

        if( $act_do['action'] == 'plow' )
        {
            echo "Found plowing activity<BR>";
            if( $month['count'] == '3' | $month['count'] == '4' | $month['count'] == '5' | $month['count'] == '6' || $_REQUEST['farming'] == 1)
            {
                $plow = $db->Execute("SELECT * FROM $dbtables[products] "
                                    ."WHERE tribeid = '$tribe[goods_tribe]' "
                                    ."AND long_name = 'plow'");
                      db_op_result($plow,__LINE__,__FILE__);
                $plowinfo = $plow->fields;
                $rake = $db->Execute("SELECT * FROM $dbtables[products] "
                                    ."WHERE tribeid = '$tribe[goods_tribe]' "
                                    ."AND long_name = 'rake'");
                     db_op_result($rake,__LINE__,__FILE__);
                $rakeinfo = $rake->fields;
                $hoe = $db->Execute("SELECT * FROM $dbtables[products] "
                                   ."WHERE tribeid = '$tribe[goods_tribe]' "
                                   ."AND long_name = 'hoe'");
                    db_op_result($hoe,__LINE__,__FILE__);
                $hoeinfo = $hoe->fields;

                $totalacres = 0;
                $plowsused = 0;
                $rakesused = 0;
                $hoesused = 0;

                $acres = 8 + $bonus_acres;
                while( $plowinfo[amount] > 0 && $act_do['actives'] > 0 )
                {
                    $plowinfo[amount] -= 1;
                    $act_do['actives'] -= 1;
                    $totalacres += $acres;
                    $plowsused += 1;
                }
                $acres = 1 + $bonus_acres;
                while( $rakeinfo[amount] > 0 && $act_do['actives'] > 0 )
                {
                    $rakeinfo[amount] -= 1;
                    $act_do['actives'] -= 1;
                    $totalacres += $acres;
                    $rakesused += 1;
                }
                $acres = 2 + $bonus_acres;
                while( $hoeinfo[amount] > 0 && $act_do['actives'] > 0 )
                {
                    $hoeinfo[amount] -= 1;
                    $act_do['actives'] -= 1;
                    $totalacres += $acres;
                    $hoesused += 1;
                }

                if( $plowsused > 0 )
                {
                    $result = $db->Execute("INSERT INTO $dbtables[products_used] "
                                ."VALUES("
                                ."'$tribe[goods_tribe]',"
                                ."'$plowsused',"
                                ."'plow')");
                      db_op_result($result,__LINE__,__FILE__);
                    $result = $db->Execute("UPDATE $dbtables[products] "
                                ."SET amount = amount - $plowsused "
                                ."WHERE tribeid = '$tribe[goods_tribe]' "
                                ."AND long_name = 'plow'");
                      db_op_result($result,__LINE__,__FILE__);
                }
                if( $hoesused > 0 )
                {
                    $result = $db->Execute("INSERT INTO $dbtables[products_used] "
                                ."VALUES("
                                ."'$tribe[goods_tribe]',"
                                ."'$hoesused',"
                                ."'hoe')");
                     db_op_result($result,__LINE__,__FILE__);
                    $result = $db->Execute("UPDATE $dbtables[products] "
                                ."SET amount = amount - $hoesused "
                                ."WHERE tribeid = '$tribe[goods_tribe]' "
                                ."AND long_name = 'hoe'");
                     db_op_result($result,__LINE__,__FILE__);
                }
                if( $rakesused > 0 )
                {
                    $result = $db->Execute("INSERT INTO $dbtables[products_used] "
                                ."VALUES("
                                ."'$tribe[goods_tribe]',"
                                ."'$rakesused',"
                                ."'rake')");
                     db_op_result($result,__LINE__,__FILE__);
                    $result = $db->Execute("UPDATE $dbtables[products] "
                                ."SET amount = amount - $rakesused "
                                ."WHERE tribeid = '$tribe[goods_tribe]' "
                                ."AND long_name = 'rake'");
                      db_op_result($result,__LINE__,__FILE__);
                }

                $result = $db->Execute("INSERT INTO $dbtables[logs] "
                            ."VALUES("
                            ."'',"
                            ."'$month[count]',"
                            ."'$year[count]',"
                            ."'$tribe[clanid]',"
                            ."'$tribe[tribeid]',"
                            ."'FARMING',"
                            ."'$stamp',"
                            ."'Farming: We have plowed $totalacres acres for farming using $plowsused plows, $rakesused rakes, $hoesused hoes.')");

                   db_op_result($result,__LINE__,__FILE__);
                $skill = $db->Execute("SELECT * FROM $dbtables[skills] "
                                     ."WHERE tribeid = '$tribe[tribeid]' "
                                     ."AND abbr = 'farm'");
                 db_op_result($skill,__LINE__,__FILE__);
                $skillinfo = $skill->fields;

                $here = $db->Execute("SELECT * FROM $dbtables[farming] "
                                    ."WHERE clanid = '$tribe[clanid]' "
                                    ."AND hex_id = '$tribe[hex_id]' "
                                    ."AND crop = 'NONE'");
                      db_op_result($here,__LINE__,__FILE__);
                if( $here->EOF )
                {
                    echo "No empty fields found<BR>";
                    $result = $db->Execute("INSERT INTO $dbtables[farming] "
                                ."VALUES("
                                ."'',"
                                ."'$tribe[clanid]',"
                                ."'$tribe[hex_id]',"
                                ."'NONE',"
                                ."'Plowed',"
                                ."'$totalacres',"
                                ."'$skillinfo[level]',"
                                ."'0',"
                                ."'0')");
                      db_op_result($result,__LINE__,__FILE__);
                }
                else
                {
                    echo "Empty field found<BR>";
                    $result = $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres + '$totalacres' "
                                ."WHERE clanid = '$tribe[clanid]' "
                                ."AND hex_id = '$tribe[hex_id]' "
                                ."AND crop = 'NONE'");
                     db_op_result($result,__LINE__,__FILE__);
                }

                $result = $db->Execute("DELETE FROM $dbtables[farm_activities] "
                            ."WHERE tribeid = '$tribe[tribeid]' "
                            ."AND action = 'plow'");
                  db_op_result($result,__LINE__,__FILE__);

            }
            else
            {
                $result = $db->Execute("INSERT INTO $dbtables[logs] "
                            ."VALUES("
                            ."'',"
                            ."'$month[count]',"
                            ."'$year[count]',"
                            ."'$tribe[clanid]',"
                            ."'$tribe[tribeid]',"
                            ."'FARMING',"
                            ."'$stamp',"
                            ."'Farming: We can only plow during spring months.')");
                   db_op_result($result,__LINE__,__FILE__);

                $result = $db->Execute("DELETE FROM $dbtables[farm_activities] "
                            ."WHERE tribeid = '$tribe[tribeid]' "
                            ."AND action = 'plow'");
                 db_op_result($result,__LINE__,__FILE__);
            }
        }
        $act->MoveNext();
    }
    $act = $db->Execute("SELECT * FROM $dbtables[farm_activities] "
                       ."WHERE tribeid = '$tribe[tribeid]' "
                       ."AND hex_id = '$tribe[hex_id]' "
                       ."AND action = 'plant'");
        db_op_result($act,__LINE__,__FILE__);

    while( !$act->EOF )
    {
        $act_do = $act->fields;
        $hex = $db->Execute("SELECT terrain FROM $dbtables[hexes] "
                           ."WHERE hex_id = '$tribe[hex_id]'");
           db_op_result($hex,__LINE__,__FILE__);
        $hexinfo = $hex->fields;

        if( $hexinfo['terrain'] == 'pr' )
        {
            $bonus_acres = 1;
        }
        elseif( $hexinfo['terrain'] == 'gh' )
        {
            $bonus_acres = .5;
        }
        elseif( $hexinfo['terrain'] == 'df' | $hexinfo['terrain'] == 'cf' | $hexinfo['terrain'] == 'jg' )
        {
            $bonus_acres = 0;
        }
        elseif( $hexinfo['terrain'] == 'dh' | $hexinfo['terrain'] == 'ch' | $hexinfo['terrain'] == 'jh' )
        {
            $bonus_acres = -.5;
        }
        elseif( $hexinfo['terrain'] == 'sw' | $hexinfo['terrain'] == 'ljm' | $hexinfo['terrain'] == 'lcm' )
        {
            $bonus_acres = -.75;
        }
        else
        {
            $bonus_acres = -.95;
        }

        if( $act_do['action'] == 'plant' )
        {
            $acres = 0;
            $plow = $db->Execute("SELECT * FROM $dbtables[farming] "
                                ."WHERE clanid = '$tribe[clanid]' "
                                ."AND hex_id = '$tribe[hex_id]' "
                                ."AND crop = 'NONE'");
                  db_op_result($plow,__LINE__,__FILE__);
            if( !$plow->EOF )
            {
            $plowinfo = $plow->fields;
            $acres_planted = 0;

            if( $act_do['crop'] == 'cotton' )
            {
                $acres = 3 + $bonus_acres;
                while( $plowinfo[acres] > $acres && $act_do['actives'] > 0 )
                {
                    $plowinfo[acres] -= $acres;
                    $act_do['actives'] -= 1;
                    $acres_planted += $acres;
                }
                $acres_planted = round($acres_planted);
                if( $plowinfo[acres] < 1 )
                {
                    $result = $db->Execute("DELETE FROM $dbtables[farming] "
                                ."WHERE cropid = '$plowinfo[cropid]' "
                                ."AND hex_id = '$tribe[hex_id]' "
                                ."AND crop = 'NONE' "
                                ."AND clanid = '$tribe[clanid]'");
                       db_op_result($result,__LINE__,__FILE__);
                }
                else
                {
                    $result = $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres - $acres_planted "
                                ."WHERE clanid = '$tribe[clanid]' "
                                ."AND crop = 'NONE' "
                                ."AND cropid = '$plowinfo[cropid]' "
                                ."AND hex_id = '$tribe[hex_id]'");
                      db_op_result($result,__LINE__,__FILE__);
                }

                $there = $db->Execute("SELECT * FROM $dbtables[farming] "
                                     ."WHERE clanid = '$tribe[clanid]' "
                                     ."AND hex_id = '$tribe[hex_id]' "
                                     ."AND crop = 'cotton'");
                     db_op_result($there,__LINE__,__FILE__);
                if( !$there->EOF )
                {
                    $therecrop = $there->fields;
                    $result = $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres + $acres_planted "
                                ."WHERE cropid = '$therecrop[cropid]' "
                                ."AND crop = '$act_do[crop]' "
                                ."AND hex_id = '$tribe[hex_id]' "
                                ."AND clanid = '$tribe[clanid]'");
                     db_op_result($result,__LINE__,__FILE__);

                }
                else
                {
                    $skill = $db->Execute("SELECT * FROM $dbtables[skills] "
                                         ."WHERE tribeid = '$tribe[tribeid]' "
                                         ."AND abbr = 'farm'");
                     db_op_result($skill,__LINE__,__FILE__);
                    $skillinfo = $skill->fields;

                    $result = $db->Execute("INSERT INTO $dbtables[farming] "
                                ."VALUES("
                                ."'',"
                                ."'$tribe[clanid]', "
                                ."'$tribe[hex_id]', "
                                ."'$act_do[crop]', "
                                ."'Planted', "
                                ."'$acres_planted', "
                                ."'$skillinfo[level]', "
                                ."'0', "
                                ."'0')");
                      db_op_result($result,__LINE__,__FILE__);
                }
                $result = $db->Execute("DELETE FROM $dbtables[farm_activities] "
                            ."WHERE action = 'plant' "
                            ."AND crop = '$act_do[crop]' "
                            ."AND tribeid = '$tribe[tribeid]'");
                   db_op_result($result,__LINE__,__FILE__);
                $result = $db->Execute("INSERT INTO $dbtables[logs] "
                            ."VALUES("
                            ."'',"
                            ."'$month[count]',"
                            ."'$year[count]',"
                            ."'$tribe[clanid]',"
                            ."'$tribe[tribeid]',"
                            ."'FARMING',"
                            ."'$stamp',"
                            ."'Farming: We planted $acres_planted acres of $act_do[crop].')");
                  db_op_result($result,__LINE__,__FILE__);

            }

            if( $act_do['crop'] == 'grain' )
            {
                $acres = 5 + $bonus_acres;
                while( $plowinfo[acres] >= $acres && $act_do['actives'] > 0 )
                {
                    $plowinfo[acres] -= $acres;
                    $act_do['actives'] -= 1;
                    $acres_planted += $acres;
                }
                $acres_planted = round($acres_planted);
                if( $plowinfo[acres] < 1 )
                {
                    $result = $db->Execute("DELETE FROM $dbtables[farming] "
                                ."WHERE cropid = '$plowinfo[cropid]' "
                                ."AND hex_id = '$tribe[hex_id]' "
                                ."AND crop = 'NONE' "
                                ."AND clanid = '$tribe[clanid]'");
                     db_op_result($result,__LINE__,__FILE__);
                }
                else
                {
                    $result = $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres - $acres_planted "
                                ."WHERE clanid = '$tribe[clanid]' "
                                ."AND crop = 'NONE' "
                                ."AND cropid = '$plowinfo[cropid]' "
                                ."AND hex_id = '$tribe[hex_id]'");
                    db_op_result($result,__LINE__,__FILE__);
                }

                $there = $db->Execute("SELECT * FROM $dbtables[farming] "
                                     ."WHERE clanid = '$tribe[clanid]' "
                                     ."AND hex_id = '$tribe[hex_id]' "
                                     ."AND crop = 'grain'");
                   db_op_result($there,__LINE__,__FILE__);
                if( !$there->EOF )
                {
                    $therecrop = $there->fields;
                    $result = $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres + $acres_planted "
                                ."WHERE cropid = '$therecrop[cropid]' "
                                ."AND crop = '$act_do[crop]' "
                                ."AND hex_id = '$tribe[hex_id]' "
                                ."AND clanid = '$tribe[clanid]'");
                     db_op_result($result,__LINE__,__FILE__);
                }
                else
                {
                    $skill = $db->Execute("SELECT * FROM $dbtables[skills] "
                                         ."WHERE tribeid = '$tribe[tribeid]' "
                                         ."AND abbr = 'farm'");
                       db_op_result($skill,__LINE__,__FILE__);
                    $skillinfo = $skill->fields;

                    $result = $db->Execute("INSERT INTO $dbtables[farming] "
                                ."VALUES("
                                ."'',"
                                ."'$tribe[clanid]', "
                                ."'$tribe[hex_id]', "
                                ."'$act_do[crop]', "
                                ."'Planted', "
                                ."'$acres_planted', "
                                ."'$skillinfo[level]', "
                                ."'0', "
                                ."'0')");
                   db_op_result($result,__LINE__,__FILE__);
                }
                $result = $db->Execute("DELETE FROM $dbtables[farm_activities] "
                            ."WHERE action = 'plant' "
                            ."AND crop = '$act_do[crop]' "
                            ."AND tribeid = '$tribe[tribeid]'");
                  db_op_result($result,__LINE__,__FILE__);
                $result = $db->Execute("INSERT INTO $dbtables[logs] "
                            ."VALUES("
                            ."'',"
                            ."'$month[count]',"
                            ."'$year[count]',"
                            ."'$tribe[clanid]',"
                            ."'$tribe[tribeid]',"
                            ."'FARMING',"
                            ."'$stamp',"
                            ."'Farming: We planted $acres_planted acres of $act_do[crop].')");
                    db_op_result($result,__LINE__,__FILE__);
            }


            if( $act_do['crop'] == 'grapes' )
            {
                $acres = 2 + $bonus_acres;
                while( $plowinfo[acres] >= $acres && $act_do['actives'] > 0 )
                {
                    $plowinfo[acres] -= $acres;
                    $act_do['actives'] -= 1;
                    $acres_planted += $acres;
                }
                $acres_planted = round($acres_planted);

                if( $acres_planted > 0 )
                {
                    $skill = $db->Execute("SELECT * FROM $dbtables[skills] "
                                         ."WHERE tribeid = '$tribe[tribeid]' "
                                         ."AND abbr = 'farm'");
                      db_op_result($skill,__LINE__,__FILE__);
                    $skillinfo = $skill->fields;

                    $result = $db->Execute("INSERT INTO $dbtables[farming] "
                                ."VALUES ("
                                        ."'',"
                                        ."'".$tribe['clanid']."',"
                                        ."'".$tribe['hex_id']."',"
                                        ."'".$act_do['crop']."',"
                                        ."'Planted',"
                                        ."'".$acres_planted."',"
                                        ."'".$skillinfo['level']."',"
                                        ."'0',"
                                        ."'0'"
                                        .")");
                      db_op_result($result,__LINE__,__FILE__);
/* Delete the bit above and uncomment this to get one field per crop

                        $there = $db->Execute("SELECT * FROM $dbtables[farming] "
                                             ."WHERE clanid = '$tribe[clanid]' "
                                             ."AND hex_id = '$tribe[hex_id]' "
                                             ."AND crop = 'grapes'");
                           db_op_result($there,__LINE__,__FILE__);
                        if( !$there->EOF )
                        {
                            $therecrop = $there->fields;
                            $result = $db->Execute("UPDATE $dbtables[farming] "
                                        ."SET acres = acres + $acres_planted "
                                        ."WHERE cropid = '$therecrop[cropid]' "
                                        ."AND crop = '$act_do[crop]' "
                                        ."AND hex_id = '$tribe[hex_id]' "
                                        ."AND clanid = '$tribe[clanid]'");
                             db_op_result($result,__LINE__,__FILE__);
                        }
                        else
                        {
                            $skill = $db->Execute("SELECT * FROM $dbtables[skills] "
                                                 ."WHERE tribeid = '$tribe[tribeid]' "
                                                 ."AND abbr = 'farm'");
                               db_op_result($skill,__LINE__,__FILE__);
                            $skillinfo = $skill->fields;

                            $result = $db->Execute("INSERT INTO $dbtables[farming] "
                                        ."VALUES("
                                        ."'',"
                                        ."'$tribe[clanid]', "
                                        ."'$tribe[hex_id]', "
                                        ."'$act_do[crop]', "
                                        ."'Planted', "
                                        ."'$acres_planted', "
                                        ."'$skillinfo[level]', "
                                        ."'0', "
                                        ."'0')");
                             db_op_result($result,__LINE__,__FILE__);
                        }
*/
                }

                if( $plowinfo[acres] < 1 )  // WAS $plowinfo[acres] < 1
                {
                    $result = $db->Execute("DELETE FROM $dbtables[farming] "
                                ."WHERE cropid = '$plowinfo[cropid]' "
                                ."AND hex_id = '$tribe[hex_id]' "
                                ."AND crop = 'NONE' "
                                ."AND clanid = '$tribe[clanid]'");
                      db_op_result($result,__LINE__,__FILE__);
                }
                else
                {
                    $result = $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres - $acres_planted "
                                ."WHERE clanid = '$tribe[clanid]' "
                                ."AND crop = 'NONE' "
                                ."AND cropid = '$plowinfo[cropid]' "
                                ."AND hex_id = '$tribe[hex_id]'");
                     db_op_result($result,__LINE__,__FILE__);
                }

                $result = $db->Execute("DELETE FROM $dbtables[farm_activities] "
                            ."WHERE action = 'plant' "
                            ."AND crop = '$act_do[crop]' "
                            ."AND tribeid = '$tribe[tribeid]'");
                 db_op_result($result,__LINE__,__FILE__);

                $result = $db->Execute("INSERT INTO $dbtables[logs] "
                            ."VALUES("
                            ."'',"
                            ."'$month[count]',"
                            ."'$year[count]',"
                            ."'$tribe[clanid]',"
                            ."'$tribe[tribeid]',"
                            ."'FARMING',"
                            ."'$stamp',"
                            ."'Farming: We planted $acres_planted acres of $act_do[crop].')");
                   db_op_result($result,__LINE__,__FILE__);
            }

            if( $act_do['crop'] == 'sugar' )
            {
                $acres = 3 + $bonus_acres;
                while( $plowinfo[acres] >= $acres && $act_do['actives'] > 0 )
                {
                    $plowinfo[acres] -= $acres;
                    $act_do['actives'] -= 1;
                    $acres_planted += $acres;
                }
                $acres_planted = round($acres_planted);
                if( $plowinfo[acres] < 1 )
                {
                    $result = $db->Execute("DELETE FROM $dbtables[farming] "
                                ."WHERE cropid = '$plowinfo[cropid]' "
                                ."AND hex_id = '$tribe[hex_id]' "
                                ."AND crop = 'NONE' "
                                ."AND clanid = '$tribe[clanid]'");
                     db_op_result($result,__LINE__,__FILE__);
                }
                else
                {
                    $result = $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres - $acres_planted "
                                ."WHERE clanid = '$tribe[clanid]' "
                                ."AND crop = 'NONE' "
                                ."AND cropid = '$plowinfo[cropid]' "
                                ."AND hex_id = '$tribe[hex_id]'");
                    db_op_result($result,__LINE__,__FILE__);
                }

                $there = $db->Execute("SELECT * FROM $dbtables[farming] "
                                     ."WHERE clanid = '$tribe[clanid]' "
                                     ."AND hex_id = '$tribe[hex_id]' "
                                     ."AND crop = 'sugar'");
                    db_op_result($there,__LINE__,__FILE__);
                if( !$there->EOF )
                {
                    $therecrop = $there->fields;
                    $result = $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres + $acres_planted "
                                ."WHERE cropid = '$therecrop[cropid]' "
                                ."AND crop = '$act_do[crop]' "
                                ."AND hex_id = '$tribe[hex_id]' "
                                ."AND clanid = '$tribe[clanid]'");
                      db_op_result($result,__LINE__,__FILE__);
                }
                else
                {
                    $skill = $db->Execute("SELECT * FROM $dbtables[skills] "
                                         ."WHERE tribeid = '$tribe[tribeid]' "
                                         ."AND abbr = 'farm'");
                       db_op_result($skill,__LINE__,__FILE__);
                    $skillinfo = $skill->fields;

                    $result = $db->Execute("INSERT INTO $dbtables[farming] "
                                ."VALUES("
                                ."'',"
                                ."'$tribe[clanid]', "
                                ."'$tribe[hex_id]', "
                                ."'$act_do[crop]', "
                                ."'Planted', "
                                ."'$acres_planted', "
                                ."'$skillinfo[level]', "
                                ."'0', "
                                ."'0')");
                     db_op_result($result,__LINE__,__FILE__);
                }
                $result = $db->Execute("DELETE FROM $dbtables[farm_activities] "
                            ."WHERE action = 'plant' "
                            ."AND crop = '$act_do[crop]' "
                            ."AND tribeid = '$tribe[tribeid]'");
                   db_op_result($result,__LINE__,__FILE__);
                $result = $db->Execute("INSERT INTO $dbtables[logs] "
                            ."VALUES("
                            ."'',"
                            ."'$month[count]',"
                            ."'$year[count]',"
                            ."'$tribe[clanid]',"
                            ."'$tribe[tribeid]',"
                            ."'FARMING',"
                            ."'$stamp',"
                            ."'Farming: We planted $acres_planted acres of $act_do[crop].')");
                    db_op_result($result,__LINE__,__FILE__);
            }


            if( $act_do['crop'] == 'tobacco' )
            {
                $acres = 2 + $bonus_acres;
                while( $plowinfo[acres] >= $acres && $act_do['actives'] > 0 )
                {
                    $plowinfo[acres] -= $acres;
                    $act_do['actives'] -= 1;
                    $acres_planted += $acres;
                }
                $acres_planted = round($acres_planted);
                if( $plowinfo[acres] < 1 )
                {
                    $result = $db->Execute("DELETE FROM $dbtables[farming] "
                                ."WHERE cropid = '$plowinfo[cropid]' "
                                ."AND hex_id = '$tribe[hex_id]' "
                                ."AND crop = 'NONE' "
                                ."AND clanid = '$tribe[clanid]'");
                       db_op_result($result,__LINE__,__FILE__);
                }
                else
                {
                    $result = $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres - $acres_planted "
                                ."WHERE clanid = '$tribe[clanid]' "
                                ."AND crop = 'NONE' "
                                ."AND cropid = '$plowinfo[cropid]' "
                                ."AND hex_id = '$tribe[hex_id]'");
                       db_op_result($result,__LINE__,__FILE__);
                }

                $there = $db->Execute("SELECT * FROM $dbtables[farming] "
                                     ."WHERE clanid = '$tribe[clanid]' "
                                     ."AND hex_id = '$tribe[hex_id]' "
                                     ."AND crop = 'tobacco'");
                      db_op_result($there,__LINE__,__FILE__);
                if( !$there->EOF )
                {
                    $therecrop = $there->fields;
                    $result = $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres + $acres_planted "
                                ."WHERE cropid = '$therecrop[cropid]' "
                                ."AND crop = '$act_do[crop]' "
                                ."AND hex_id = '$tribe[hex_id]' "
                                ."AND clanid = '$tribe[clanid]'");
                     db_op_result($result,__LINE__,__FILE__);
                }
                else
                {
                    $skill = $db->Execute("SELECT * FROM $dbtables[skills] "
                                         ."WHERE tribeid = '$tribe[tribeid]' "
                                         ."AND abbr = 'farm'");
                         db_op_result($skill,__LINE__,__FILE__);
                    $skillinfo = $skill->fields;

                    $result = $db->Execute("INSERT INTO $dbtables[farming] "
                                ."VALUES("
                                ."'',"
                                ."'$tribe[clanid]', "
                                ."'$tribe[hex_id]', "
                                ."'$act_do[crop]', "
                                ."'Planted', "
                                ."'$acres_planted', "
                                ."'$skillinfo[level]', "
                                ."'0', "
                                ."'0')");
                     db_op_result($result,__LINE__,__FILE__);
                }
                $result = $db->Execute("DELETE FROM $dbtables[farm_activities] "
                            ."WHERE action = 'plant' "
                            ."AND crop = '$act_do[crop]' "
                            ."AND tribeid = '$tribe[tribeid]'");
                   db_op_result($result,__LINE__,__FILE__);
                $result = $db->Execute("INSERT INTO $dbtables[logs] "
                            ."VALUES("
                            ."'',"
                            ."'$month[count]',"
                            ."'$year[count]',"
                            ."'$tribe[clanid]',"
                            ."'$tribe[tribeid]',"
                            ."'FARMING',"
                            ."'$stamp',"
                            ."'Farming: We planted $acres_planted acres of $act_do[crop].')");
                    db_op_result($result,__LINE__,__FILE__);
            }

            if( $act_do['crop'] == 'flax' )
            {
                $acres = 3 + $bonus_acres;
                while( $plowinfo[acres] >= $acres && $act_do['actives'] > 0 )
                {
                    $plowinfo[acres] -= $acres;
                    $act_do['actives'] -= 1;
                    $acres_planted += $acres;
                }
                $acres_planted = round($acres_planted);
                if( $plowinfo[acres] < 1 )
                {
                    $result = $db->Execute("DELETE FROM $dbtables[farming] "
                                ."WHERE cropid = '$plowinfo[cropid]' "
                                ."AND hex_id = '$tribe[hex_id]' "
                                ."AND crop = 'NONE' "
                                ."AND clanid = '$tribe[clanid]'");
                     db_op_result($result,__LINE__,__FILE__);
                }
                else
                {
                    $result = $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres - $acres_planted "
                                ."WHERE clanid = '$tribe[clanid]' "
                                ."AND crop = 'NONE' "
                                ."AND cropid = '$plowinfo[cropid]' "
                                ."AND hex_id = '$tribe[hex_id]'");
                      db_op_result($result,__LINE__,__FILE__);
                }

                $there = $db->Execute("SELECT * FROM $dbtables[farming] "
                                     ."WHERE clanid = '$tribe[clanid]' "
                                     ."AND hex_id = '$tribe[hex_id]' "
                                     ."AND crop = 'flax'");
                  db_op_result($there,__LINE__,__FILE__);
                if( !$there->EOF )
                {
                    $therecrop = $there->fields;
                    $result = $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres + $acres_planted "
                                ."WHERE cropid = '$therecrop[cropid]' "
                                ."AND crop = '$act_do[crop]' "
                                ."AND hex_id = '$tribe[hex_id]' "
                                ."AND clanid = '$tribe[clanid]'");
                  db_op_result($result,__LINE__,__FILE__);
                }
                else
                {
                    $skill = $db->Execute("SELECT * FROM $dbtables[skills] "
                                         ."WHERE tribeid = '$tribe[tribeid]' "
                                         ."AND abbr = 'farm'");
                        db_op_result($skill,__LINE__,__FILE__);
                    $skillinfo = $skill->fields;

                    $result = $db->Execute("INSERT INTO $dbtables[farming] "
                                ."VALUES("
                                ."'',"
                                ."'$tribe[clanid]', "
                                ."'$tribe[hex_id]', "
                                ."'$act_do[crop]', "
                                ."'Planted', "
                                ."'$acres_planted', "
                                ."'$skillinfo[level]', "
                                ."'0', "
                                ."'0')");
                      db_op_result($result,__LINE__,__FILE__);
                }
                $result = $db->Execute("DELETE FROM $dbtables[farm_activities] "
                            ."WHERE action = 'plant' "
                            ."AND crop = '$act_do[crop]' "
                            ."AND tribeid = '$tribe[tribeid]'");
                  db_op_result($result,__LINE__,__FILE__);
                $result = $db->Execute("INSERT INTO $dbtables[logs] "
                            ."VALUES("
                            ."'',"
                            ."'$month[count]',"
                            ."'$year[count]',"
                            ."'$tribe[clanid]',"
                            ."'$tribe[tribeid]',"
                            ."'FARMING',"
                            ."'$stamp',"
                            ."'Farming: We planted $acres_planted acres of $act_do[crop].')");
                  db_op_result($result,__LINE__,__FILE__);
            }

            if( $act_do['crop'] == 'hemp' )
            {
                $acres = 2 + $bonus_acres;
                while( $plowinfo[acres] >= $acres && $act_do['actives'] > 0 )
                {
                    $plowinfo[acres] -= $acres;
                    $act_do['actives'] -= 1;
                    $acres_planted += $acres;
                }
                $acres_planted = round($acres_planted);
                if( $plowinfo[acres] < 1 )
                {
                    $result = $db->Execute("DELETE FROM $dbtables[farming] "
                                ."WHERE cropid = '$plowinfo[cropid]' "
                                ."AND hex_id = '$tribe[hex_id]' "
                                ."AND crop = 'NONE' "
                                ."AND clanid = '$tribe[clanid]'");
                     db_op_result($result,__LINE__,__FILE__);
                }
                else
                {
                    $result = $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres - $acres_planted "
                                ."WHERE clanid = '$tribe[clanid]' "
                                ."AND crop = 'NONE' "
                                ."AND cropid = '$plowinfo[cropid]' "
                                ."AND hex_id = '$tribe[hex_id]'");
                      db_op_result($result,__LINE__,__FILE__);
                }

                $there = $db->Execute("SELECT * FROM $dbtables[farming] "
                                     ."WHERE clanid = '$tribe[clanid]' "
                                     ."AND hex_id = '$tribe[hex_id]' "
                                     ."AND crop = 'hemp'");
                    db_op_result($there,__LINE__,__FILE__);
                if( !$there->EOF )
                {
                    $therecrop = $there->fields;
                    $result = $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres + $acres_planted "
                                ."WHERE cropid = '$therecrop[cropid]' "
                                ."AND crop = '$act_do[crop]' "
                                ."AND hex_id = '$tribe[hex_id]' "
                                ."AND clanid = '$tribe[clanid]'");
                     db_op_result($result,__LINE__,__FILE__);
                }
                else
                {
                    $skill = $db->Execute("SELECT * FROM $dbtables[skills] "
                                         ."WHERE tribeid = '$tribe[tribeid]' "
                                         ."AND abbr = 'farm'");
                         db_op_result($skill,__LINE__,__FILE__);
                    $skillinfo = $skill->fields;

                    $result = $db->Execute("INSERT INTO $dbtables[farming] "
                                ."VALUES("
                                ."'',"
                                ."'$tribe[clanid]', "
                                ."'$tribe[hex_id]', "
                                ."'$act_do[crop]', "
                                ."'Planted', "
                                ."'$acres_planted', "
                                ."'$skillinfo[level]', "
                                ."'0', "
                                ."'0')");
                     db_op_result($result,__LINE__,__FILE__);
                }
                $result = $db->Execute("DELETE FROM $dbtables[farm_activities] "
                            ."WHERE action = 'plant' "
                            ."AND crop = '$act_do[crop]' "
                            ."AND tribeid = '$tribe[tribeid]'");
                    db_op_result($result,__LINE__,__FILE__);
                $result = $db->Execute("INSERT INTO $dbtables[logs] "
                            ."VALUES("
                            ."'',"
                            ."'$month[count]',"
                            ."'$year[count]',"
                            ."'$tribe[clanid]',"
                            ."'$tribe[tribeid]',"
                            ."'FARMING',"
                            ."'$stamp',"
                            ."'Farming: We planted $acres_planted acres of $act_do[crop].')");
                    db_op_result($result,__LINE__,__FILE__);
            }

            if( $act_do['crop'] == 'potatoes' )
            {
                $acres = 3 + $bonus_acres;
                while( $plowinfo[acres] >= $acres && $act_do['actives'] > 0 )
                {
                    $plowinfo[acres] -= $acres;
                    $act_do['actives'] -= 1;
                    $acres_planted += $acres;
                }
                $acres_planted = round($acres_planted);
                if( $plowinfo[acres] < 1 )
                {
                    $result = $db->Execute("DELETE FROM $dbtables[farming] "
                                ."WHERE cropid = '$plowinfo[cropid]' "
                                ."AND hex_id = '$tribe[hex_id]' "
                                ."AND crop = 'NONE' "
                                ."AND clanid = '$tribe[clanid]'");
                    db_op_result($result,__LINE__,__FILE__);
                }
                else
                {
                    $result = $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres - $acres_planted "
                                ."WHERE clanid = '$tribe[clanid]' "
                                ."AND crop = 'NONE' "
                                ."AND cropid = '$plowinfo[cropid]' "
                                ."AND hex_id = '$tribe[hex_id]'");
                      db_op_result($result,__LINE__,__FILE__);
                }

                $there = $db->Execute("SELECT * FROM $dbtables[farming] "
                                     ."WHERE clanid = '$tribe[clanid]' "
                                     ."AND hex_id = '$tribe[hex_id]' "
                                     ."AND crop = 'potatoes'");
                     db_op_result($skill,__LINE__,__FILE__);
                if( !$there->EOF )
                {
                    $therecrop = $there->fields;
                    $result = $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres + $acres_planted "
                                ."WHERE cropid = '$therecrop[cropid]' "
                                ."AND crop = '$act_do[crop]' "
                                ."AND hex_id = '$tribe[hex_id]' "
                                ."AND clanid = '$tribe[clanid]'");
                      db_op_result($result,__LINE__,__FILE__);
                }
                else
                {
                    $skill = $db->Execute("SELECT * FROM $dbtables[skills] "
                                         ."WHERE tribeid = '$tribe[tribeid]' "
                                         ."AND abbr = 'farm'");
                       db_op_result($skill,__LINE__,__FILE__);
                    $skillinfo = $skill->fields;

                    $result = $db->Execute("INSERT INTO $dbtables[farming] "
                                ."VALUES("
                                ."'',"
                                ."'$tribe[clanid]', "
                                ."'$tribe[hex_id]', "
                                ."'$act_do[crop]', "
                                ."'Planted', "
                                ."'$acres_planted', "
                                ."'$skillinfo[level]', "
                                ."'0', "
                                ."'0')");
                    db_op_result($result,__LINE__,__FILE__);
                }
                $result = $db->Execute("DELETE FROM $dbtables[farm_activities] "
                            ."WHERE action = 'plant' "
                            ."AND crop = '$act_do[crop]' "
                            ."AND tribeid = '$tribe[tribeid]'");
                   db_op_result($result,__LINE__,__FILE__);
                $result = $db->Execute("INSERT INTO $dbtables[logs] "
                            ."VALUES("
                            ."'',"
                            ."'$month[count]',"
                            ."'$year[count]',"
                            ."'$tribe[clanid]',"
                            ."'$tribe[tribeid]',"
                            ."'FARMING',"
                            ."'$stamp',"
                            ."'Farming: We planted $acres_planted acres of $act_do[crop].')");
                     db_op_result($result,__LINE__,__FILE__);
            }

            if( $act_do['crop'] == 'corn' )
            {
                $acres = 5 + $bonus_acres;
                while( $plowinfo[acres] >= $acres && $act_do['actives'] > 0 )
                {
                    $plowinfo[acres] -= $acres;
                    $act_do['actives'] -= 1;
                    $acres_planted += $acres;
                }
                $acres_planted = round($acres_planted);
                if( $plowinfo[acres] < 1 )
                {
                    $result = $db->Execute("DELETE FROM $dbtables[farming] "
                                ."WHERE cropid = '$plowinfo[cropid]' "
                                ."AND hex_id = '$tribe[hex_id]' "
                                ."AND crop = 'NONE' "
                                ."AND clanid = '$tribe[clanid]'");
                     db_op_result($result,__LINE__,__FILE__);
                }
                else
                {
                    $result = $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres - $acres_planted "
                                ."WHERE clanid = '$tribe[clanid]' "
                                ."AND crop = 'NONE' "
                                ."AND cropid = '$plowinfo[cropid]' "
                                ."AND hex_id = '$tribe[hex_id]'");
                       db_op_result($result,__LINE__,__FILE__);
                }

                $there = $db->Execute("SELECT * FROM $dbtables[farming] "
                                     ."WHERE clanid = '$tribe[clanid]' "
                                     ."AND hex_id = '$tribe[hex_id]' "
                                     ."AND crop = 'corn'");
                        db_op_result($there,__LINE__,__FILE__);
                if( !$there->EOF )
                {
                    $therecrop = $there->fields;
                    $result = $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres + $acres_planted "
                                ."WHERE cropid = '$therecrop[cropid]' "
                                ."AND crop = '$act_do[crop]' "
                                ."AND hex_id = '$tribe[hex_id]' "
                                ."AND clanid = '$tribe[clanid]'");
                       db_op_result($result,__LINE__,__FILE__);
                }
                else
                {
                    $skill = $db->Execute("SELECT * FROM $dbtables[skills] "
                                         ."WHERE tribeid = '$tribe[tribeid]' "
                                         ."AND abbr = 'farm'");
                    $skillinfo = $skill->fields;

                    $result = $db->Execute("INSERT INTO $dbtables[farming] "
                                ."VALUES("
                                ."'',"
                                ."'$tribe[clanid]', "
                                ."'$tribe[hex_id]', "
                                ."'$act_do[crop]', "
                                ."'Planted', "
                                ."'$acres_planted', "
                                ."'$skillinfo[level]', "
                                ."'0', "
                                ."'0')");
                      db_op_result($result,__LINE__,__FILE__);
                }
                $result = $db->Execute("DELETE FROM $dbtables[farm_activities] "
                            ."WHERE action = 'plant' "
                            ."AND crop = '$act_do[crop]' "
                            ."AND tribeid = '$tribe[tribeid]'");
                       db_op_result($result,__LINE__,__FILE__);
                $result = $db->Execute("INSERT INTO $dbtables[logs] "
                            ."VALUES("
                            ."'',"
                            ."'$month[count]',"
                            ."'$year[count]',"
                            ."'$tribe[clanid]',"
                            ."'$tribe[tribeid]',"
                            ."'FARMING',"
                            ."'$stamp',"
                            ."'Farming: We planted $acres_planted acres of $act_do[crop].')");
                     db_op_result($result,__LINE__,__FILE__);
            }

            if( $act_do['crop'] == 'herbs' )
            {
                $acres = 1 + $bonus_acres;
                while( $plowinfo[acres] >= $acres && $act_do['actives'] > 0 )
                {
                    $plowinfo[acres] -= $acres;
                    $act_do['actives'] -= 1;
                    $acres_planted += $acres;
                }
                $acres_planted = round($acres_planted);
                if( $plowinfo[acres] < 1 )
                {
                    $result = $db->Execute("DELETE FROM $dbtables[farming] "
                                ."WHERE cropid = '$plowinfo[cropid]' "
                                ."AND hex_id = '$tribe[hex_id]' "
                                ."AND crop = 'NONE' "
                                ."AND clanid = '$tribe[clanid]'");
                       db_op_result($result,__LINE__,__FILE__);
                }
                else
                {
                    $result = $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres - $acres_planted "
                                ."WHERE clanid = '$tribe[clanid]' "
                                ."AND crop = 'NONE' "
                                ."AND cropid = '$plowinfo[cropid]' "
                                ."AND hex_id = '$tribe[hex_id]'");
                       db_op_result($result,__LINE__,__FILE__);
                }

                $there = $db->Execute("SELECT * FROM $dbtables[farming] "
                                     ."WHERE clanid = '$tribe[clanid]' "
                                     ."AND hex_id = '$tribe[hex_id]' "
                                     ."AND crop = 'herbs'");
                      db_op_result($there,__LINE__,__FILE__);
                if( !$there->EOF )
                {
                    $therecrop = $there->fields;
                    $result = $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres + $acres_planted "
                                ."WHERE cropid = '$therecrop[cropid]' "
                                ."AND crop = '$act_do[crop]' "
                                ."AND hex_id = '$tribe[hex_id]' "
                                ."AND clanid = '$tribe[clanid]'");
                    db_op_result($result,__LINE__,__FILE__);
                }
                else
                {
                    $skill = $db->Execute("SELECT * FROM $dbtables[skills] "
                                         ."WHERE tribeid = '$tribe[tribeid]' "
                                         ."AND abbr = 'farm'");
                       db_op_result($skill,__LINE__,__FILE__);
                    $skillinfo = $skill->fields;

                    $result = $db->Execute("INSERT INTO $dbtables[farming] "
                                ."VALUES("
                                ."'',"
                                ."'$tribe[clanid]', "
                                ."'$tribe[hex_id]', "
                                ."'$act_do[crop]', "
                                ."'Planted', "
                                ."'$acres_planted', "
                                ."'$skillinfo[level]', "
                                ."'0', "
                                ."'0')");
                       db_op_result($result,__LINE__,__FILE__);
                }
                $result = $db->Execute("DELETE FROM $dbtables[farm_activities] "
                            ."WHERE action = 'plant' "
                            ."AND crop = '$act_do[crop]' "
                            ."AND tribeid = '$tribe[tribeid]'");
                     db_op_result($result,__LINE__,__FILE__);
                $result = $db->Execute("INSERT INTO $dbtables[logs] "
                            ."VALUES("
                            ."'',"
                            ."'$month[count]',"
                            ."'$year[count]',"
                            ."'$tribe[clanid]',"
                            ."'$tribe[tribeid]',"
                            ."'FARMING',"
                            ."'$stamp',"
                            ."'Farming: We planted $acres_planted acres of $act_do[crop].')");
                  db_op_result($result,__LINE__,__FILE__);
            }

            if( $act_do['crop'] == 'spice' )
            {
                $acres = 1 + $bonus_acres;
                while( $plowinfo[acres] >= $acres && $act_do['actives'] > 0 )
                {
                    $plowinfo[acres] -= $acres;
                    $act_do['actives'] -= 1;
                    $acres_planted += $acres;
                }
                $acres_planted = round($acres_planted);
                if( $plowinfo[acres] < 1 )
                {
                    $result = $db->Execute("DELETE FROM $dbtables[farming] "
                                ."WHERE cropid = '$plowinfo[cropid]' "
                                ."AND hex_id = '$tribe[hex_id]' "
                                ."AND crop = 'NONE' "
                                ."AND clanid = '$tribe[clanid]'");
                        db_op_result($result,__LINE__,__FILE__);
                }
                else
                {
                    $result = $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres - $acres_planted "
                                ."WHERE clanid = '$tribe[clanid]' "
                                ."AND crop = 'NONE' "
                                ."AND cropid = '$plowinfo[cropid]' "
                                ."AND hex_id = '$tribe[hex_id]'");
                      db_op_result($result,__LINE__,__FILE__);
                }

                $there = $db->Execute("SELECT * FROM $dbtables[farming] "
                                     ."WHERE clanid = '$tribe[clanid]' "
                                     ."AND hex_id = '$tribe[hex_id]' "
                                     ."AND crop = 'spice'");
                     db_op_result($there,__LINE__,__FILE__);
                if( !$there->EOF )
                {
                    $therecrop = $there->fields;
                    $result = $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres + $acres_planted "
                                ."WHERE cropid = '$therecrop[cropid]' "
                                ."AND crop = '$act_do[crop]' "
                                ."AND hex_id = '$tribe[hex_id]' "
                                ."AND clanid = '$tribe[clanid]'");
                     db_op_result($result,__LINE__,__FILE__);
                }
                else
                {
                    $skill = $db->Execute("SELECT * FROM $dbtables[skills] "
                                         ."WHERE tribeid = '$tribe[tribeid]' "
                                         ."AND abbr = 'farm'");
                         db_op_result($skill,__LINE__,__FILE__);
                    $skillinfo = $skill->fields;

                    $result = $db->Execute("INSERT INTO $dbtables[farming] "
                                ."VALUES("
                                ."'',"
                                ."'$tribe[clanid]', "
                                ."'$tribe[hex_id]', "
                                ."'$act_do[crop]', "
                                ."'Planted', "
                                ."'$acres_planted', "
                                ."'$skillinfo[level]', "
                                ."'0', "
                                ."'0')");
                     db_op_result($result,__LINE__,__FILE__);
                }
                $result = $db->Execute("DELETE FROM $dbtables[farm_activities] "
                            ."WHERE action = 'plant' "
                            ."AND crop = '$act_do[crop]' "
                            ."AND tribeid = '$tribe[tribeid]'");
                    db_op_result($result,__LINE__,__FILE__);
                $result = $db->Execute("INSERT INTO $dbtables[logs] "
                            ."VALUES("
                            ."'',"
                            ."'$month[count]',"
                            ."'$year[count]',"
                            ."'$tribe[clanid]',"
                            ."'$tribe[tribeid]',"
                            ."'FARMING',"
                            ."'$stamp',"
                            ."'Farming: We planted $acres_planted acres of $act_do[crop].')");
                      db_op_result($result,__LINE__,__FILE__);
            }
                        }
            else
            {
                $result = $db->Execute("INSERT INTO $dbtables[logs] "
                            ."VALUES("
                            ."'',"
                            ."'$month[count]',"
                            ."'$year[count]',"
                            ."'$tribe[clanid]',"
                            ."'$tribe[tribeid]',"
                            ."'FARMING',"
                            ."'$stamp',"
                            ."'Farming: We tried to plant, but the plowing has not been completed yet.')");
                  db_op_result($result,__LINE__,__FILE__);
                $result = $db->Execute("DELETE FROM $dbtables[farm_activities] "
                            ."WHERE action = 'plant' "
                            ."AND crop = '$act_do[crop]' "
                            ."AND tribeid = '$tribe[tribeid]'");
                 db_op_result($result,__LINE__,__FILE__);
            }

        }
        $act->MoveNext();
    }

    $act = $db->Execute("SELECT * FROM $dbtables[farm_activities] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND hex_id = '$tribe[hex_id]' "
                        ."AND action = 'harvest'");
              db_op_result($act,__LINE__,__FILE__);

    while( !$act->EOF )
    {
        $act_do = $act->fields;
        if( $act_do['action'] == 'harvest' )
        {
            $crop = $db->Execute("SELECT * FROM $dbtables[farming] "
                                ."WHERE hex_id = '$tribe[hex_id]' "
                                ."AND clanid = '$tribe[clanid]' "
                                ."AND crop = '$act_do[crop]'");
                 db_op_result($crop,__LINE__,__FILE__);
            if( !$crop->EOF )
            {
                $cropinfo = $crop->fields;
                $acres_harvested = 0;
                $cotton_harvest = 0;
                if( $cropinfo['crop'] == 'cotton' )
                {
                    $harvest = ($cropinfo[harvest] / $cropinfo['acres']) * 2;

                    $acres_harvested = 0;
                    $cotton_harvest = 0;

                    while( $cropinfo['acres'] > 1 && $act_do['actives'] > 1 )
                    {
                        $cropinfo['acres'] -= 2;
                        $acres_harvested += 2;
                        $act_do['actives'] -= 1;
                        $cotton_harvest += $harvest;
                    }
                    $cotton_harvest = round($cotton_harvest);

                    $result = $db->Execute("UPDATE $dbtables[products] "
                                ."SET amount = amount + $cotton_harvest "
                                ."WHERE tribeid = '$tribe[goods_tribe]' "
                                ."AND long_name = 'cotton'");
                     db_op_result($result,__LINE__,__FILE__);
                    $result = $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres - $acres_harvested "
                                ."WHERE cropid = '$cropinfo[cropid]' "
                                ."AND clanid = '$tribe[clanid]' "
                                ."AND hex_id = '$tribe[hex_id]'");
                     db_op_result($result,__LINE__,__FILE__);
                    $result = $db->Execute("INSERT INTO $dbtables[logs] "
                                ."VALUES("
                                ."'',"
                                ."'$month[count]',"
                                ."'$year[count]',"
                                ."'$tribe[clanid]',"
                                ."'$tribe[tribeid]',"
                                ."'FARMING',"
                                ."'$stamp',"
                                ."'Farming: We harvested $cotton_harvest cotton from $acres_harvested acres of farmland.')");
                   db_op_result($result,__LINE__,__FILE__);
                   $result = $db->Execute("DELETE FROM $dbtables[farm_activities] "
                               ."WHERE tribeid = '$tribe[tribeid]' "
                               ."AND action = '$act_do[action]' "
                               ."AND crop = '$act_do[crop]'");
                     db_op_result($result,__LINE__,__FILE__);
                }

                if( $cropinfo[crop] == 'grain' )
                {
                    $scy = $db->Execute("SELECT * FROM $dbtables[products] "
                                       ."WHERE tribeid = '$tribe[goods_tribe]' "
                                       ."AND long_name = 'scythe'");
                      db_op_result($scy,__LINE__,__FILE__);
                    if( !$scy->EOF )
                    {
                        $scythe = $scy->fields;
                        $actives = $act_do['actives'];
                        $scythe_used = 0;
                        while( $scythe[amount] > 0 && $actives > 0 )
                        {
                            $scythe[amount] -= 1;
                            $actives -= 1;
                            $act_do['actives'] += 1;
                            $scythe_used += 1;
                        }
                        if( $scythe_used > 0 )
                        {
                            $result = $db->Execute("INSERT INTO $dbtables[products_used] "
                                        ."VALUES("
                                        ."'$tribe[goods_tribe]',"
                                        ."'$scythe_used',"
                                        ."'scythe')");
                              db_op_result($result,__LINE__,__FILE__);
                            $result = $db->Execute("UPDATE $dbtables[products] "
                                        ."SET amount = amount - $scythe_used "
                                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                                        ."AND long_name = 'scythe'");
                               db_op_result($result,__LINE__,__FILE__);
                        }
                    }
                    $harvest = ($cropinfo[harvest] / $cropinfo['acres']) * 3;
                    $acres_harvested = 0;
                    $cotton_harvest = 0;
                    while( $cropinfo['acres'] > 2 && $act_do['actives'] > 1 )
                    {
                        $cropinfo['acres'] -= 3;
                        $acres_harvested += 3;
                        $act_do['actives'] -= 1;
                        $cotton_harvest += $harvest;
                    }
                    $cotton_harvest = round($cotton_harvest);
                    $result = $db->Execute("UPDATE $dbtables[products] "
                                ."SET amount = amount + $cotton_harvest "
                                ."WHERE tribeid = '$tribe[goods_tribe]' "
                                ."AND long_name = '$act_do[crop]'");
                     db_op_result($result,__LINE__,__FILE__);
                    $result = $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres - $acres_harvested "
                                ."WHERE cropid = '$cropinfo[cropid]' "
                                ."AND clanid = '$tribe[clanid]' "
                                ."AND hex_id = '$tribe[hex_id]'");
                      db_op_result($result,__LINE__,__FILE__);
                    $result = $db->Execute("INSERT INTO $dbtables[logs] "
                                ."VALUES("
                                ."'',"
                                ."'$month[count]',"
                                ."'$year[count]',"
                                ."'$tribe[clanid]',"
                                ."'$tribe[tribeid]',"
                                ."'FARMING',"
                                ."'$stamp',"
                                ."'Farming: We harvested $cotton_harvest $act_do[crop] from $acres_harvested acres of farmland.')");
                     db_op_result($result,__LINE__,__FILE__);
                   $result = $db->Execute("DELETE FROM $dbtables[farm_activities] "
                               ."WHERE tribeid = '$tribe[tribeid]' "
                               ."AND action = '$act_do[action]' "
                               ."AND crop = '$act_do[crop]'");
                    db_op_result($result,__LINE__,__FILE__);
                }

                if( $cropinfo['crop'] == 'grapes' )
                {
                    $harvest = ($cropinfo[harvest] / $cropinfo['acres']);
                    $acres_harvested = 0;
                    $cotton_harvest = 0;

                    while( $cropinfo['acres'] > 0 && $act_do['actives'] > 1 )
                    {
                        $cropinfo['acres'] -= 1;
                        $acres_harvested += 1;
                        $act_do['actives'] -= 1;
                        $cotton_harvest += $harvest;
                    }
                    $cotton_harvest = round($cotton_harvest);

                    $result = $db->Execute("UPDATE $dbtables[products] "
                                ."SET amount = amount + $cotton_harvest "
                                ."WHERE tribeid = '$tribe[goods_tribe]' "
                                ."AND long_name = '$act_do[crop]'");
                     db_op_result($result,__LINE__,__FILE__);
                    $result = $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres - $acres_harvested "
                                ."WHERE cropid = '$cropinfo[cropid]' "
                                ."AND clanid = '$tribe[clanid]' "
                                ."AND hex_id = '$tribe[hex_id]'");
                      db_op_result($result,__LINE__,__FILE__);
                    $result = $db->Execute("INSERT INTO $dbtables[logs] "
                                ."VALUES("
                                ."'',"
                                ."'$month[count]',"
                                ."'$year[count]',"
                                ."'$tribe[clanid]',"
                                ."'$tribe[tribeid]',"
                                ."'FARMING',"
                                ."'$stamp',"
                                ."'Farming: We harvested $cotton_harvest $act_do[crop] from $acres_harvested acres of farmland.')");
                   db_op_result($result,__LINE__,__FILE__);
                   $result = $db->Execute("DELETE FROM $dbtables[farm_activities] "
                               ."WHERE tribeid = '$tribe[tribeid]' "
                               ."AND action = '$act_do[action]' "
                               ."AND crop = '$act_do[crop]'");
                     db_op_result($result,__LINE__,__FILE__);
                }

                if( $cropinfo['crop'] == 'sugar' )
                {
                    $scy = $db->Execute("SELECT * FROM $dbtables[products] "
                                       ."WHERE tribeid = '$tribe[goods_tribe]' "
                                       ."AND long_name = 'scythe'");
                        db_op_result($scy,__LINE__,__FILE__);
                    if( !$scy->EOF )
                    {
                        $scythe = $scy->fields;
                        $actives = $act_do['actives'];
                        $scythe_used = 0;
                        while( $scythe[amount] > 0 && $actives > 0 )
                        {
                            $scythe[amount] -= 1;
                            $actives -= 1;
                            $act_do['actives'] += 1;
                            $scythe_used += 1;
                        }
                        if( $scythe_used > 0 )
                        {
                            $result = $db->Execute("INSERT INTO $dbtables[products_used] "
                                        ."VALUES("
                                        ."'$tribe[goods_tribe]',"
                                        ."'$scythe_used',"
                                        ."'scythe')");
                              db_op_result($result,__LINE__,__FILE__);
                            $result = $db->Execute("UPDATE $dbtables[products] "
                                        ."SET amount = amount - $scythe_used "
                                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                                        ."AND long_name = 'scythe'");
                             db_op_result($result,__LINE__,__FILE__);
                        }
                    }
                    $harvest = ($cropinfo[harvest] / $cropinfo['acres']) * 2;
                    $acres_harvested = 0;
                    $cotton_harvest = 0;

                    while( $cropinfo['acres'] > 1 && $act_do['actives'] > 1 )
                    {
                        $cropinfo['acres'] -= 2;
                        $acres_harvested += 2;
                        $act_do['actives'] -= 1;
                        $cotton_harvest += $harvest;
                    }
                    $cotton_harvest = round($cotton_harvest);

                    $result = $db->Execute("UPDATE $dbtables[products] "
                                ."SET amount = amount + $cotton_harvest "
                                ."WHERE tribeid = '$tribe[goods_tribe]' "
                                ."AND long_name = '$act_do[crop]'");
                    db_op_result($result,__LINE__,__FILE__);
                    $result = $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres - $acres_harvested "
                                ."WHERE cropid = '$cropinfo[cropid]' "
                                ."AND clanid = '$tribe[clanid]' "
                                ."AND hex_id = '$tribe[hex_id]'");
                      db_op_result($result,__LINE__,__FILE__);
                    $result = $db->Execute("INSERT INTO $dbtables[logs] "
                                ."VALUES("
                                ."'',"
                                ."'$month[count]',"
                                ."'$year[count]',"
                                ."'$tribe[clanid]',"
                                ."'$tribe[tribeid]',"
                                ."'FARMING',"
                                ."'$stamp',"
                                ."'Farming: We harvested $cotton_harvest $act_do[crop] from $acres_harvested acres of farmland.')");
                     db_op_result($result,__LINE__,__FILE__);
                   $result = $db->Execute("DELETE FROM $dbtables[farm_activities] "
                               ."WHERE tribeid = '$tribe[tribeid]' "
                               ."AND action = '$act_do[action]' "
                               ."AND crop = '$act_do[crop]'");
                    db_op_result($result,__LINE__,__FILE__);
                }

                if( $cropinfo['crop'] == 'tobacco' )
                {
                    $harvest = ($cropinfo[harvest] / $cropinfo['acres']);
                    $acres_harvested = 0;
                    $cotton_harvest = 0;

                    while( $cropinfo['acres'] > 0 && $act_do['actives'] > 1 )
                    {
                        $cropinfo['acres'] -= 1;
                        $acres_harvested += 1;
                        $act_do['actives'] -= 1;
                        $cotton_harvest += $harvest;
                    }
                    $cotton_harvest = round($cotton_harvest);

                    $result = $db->Execute("UPDATE $dbtables[products] "
                                ."SET amount = amount + $cotton_harvest "
                                ."WHERE tribeid = '$tribe[goods_tribe]' "
                                ."AND long_name = '$act_do[crop]'");
                     db_op_result($result,__LINE__,__FILE__);
                    $result = $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres - $acres_harvested "
                                ."WHERE cropid = '$cropinfo[cropid]' "
                                ."AND clanid = '$tribe[clanid]' "
                                ."AND hex_id = '$tribe[hex_id]'");
                     db_op_result($result,__LINE__,__FILE__);
                    $result = $db->Execute("INSERT INTO $dbtables[logs] "
                                ."VALUES("
                                ."'',"
                                ."'$month[count]',"
                                ."'$year[count]',"
                                ."'$tribe[clanid]',"
                                ."'$tribe[tribeid]',"
                                ."'FARMING',"
                                ."'$stamp',"
                                ."'Farming: We harvested $cotton_harvest $act_do[crop] from $acres_harvested acres of farmland.')");
                    db_op_result($result,__LINE__,__FILE__);
                   $result = $db->Execute("DELETE FROM $dbtables[farm_activities] "
                               ."WHERE tribeid = '$tribe[tribeid]' "
                               ."AND action = '$act_do[action]' "
                               ."AND crop = '$act_do[crop]'");
                      db_op_result($result,__LINE__,__FILE__);
                }
                if( $cropinfo['crop'] == 'flax' )
                {
                    $harvest = ($cropinfo[harvest] / $cropinfo['acres']) * 3;
                    $acres_harvested = 0;
                    $cotton_harvest = 0;

                    while( $cropinfo['acres'] > 2 && $act_do['actives'] > 1 )
                    {
                        $cropinfo['acres'] -= 3;
                        $acres_harvested += 3;
                        $act_do['actives'] -= 1;
                        $cotton_harvest += $harvest;
                    }
                    $cotton_harvest = round($cotton_harvest);

                    $result = $db->Execute("UPDATE $dbtables[products] "
                                ."SET amount = amount + $cotton_harvest "
                                ."WHERE tribeid = '$tribe[goods_tribe]' "
                                ."AND long_name = 'cotton'");
                     db_op_result($result,__LINE__,__FILE__);
                    $result = $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres - $acres_harvested "
                                ."WHERE cropid = '$cropinfo[cropid]' "
                                ."AND clanid = '$tribe[clanid]' "
                                ."AND hex_id = '$tribe[hex_id]'");
                      db_op_result($result,__LINE__,__FILE__);
                    $result = $db->Execute("INSERT INTO $dbtables[logs] "
                                ."VALUES("
                                ."'',"
                                ."'$month[count]',"
                                ."'$year[count]',"
                                ."'$tribe[clanid]',"
                                ."'$tribe[tribeid]',"
                                ."'FARMING',"
                                ."'$stamp',"
                                ."'Farming: We harvested $cotton_harvest $act_do[crop] from $acres_harvested acres of farmland.')");
                     db_op_result($result,__LINE__,__FILE__);
                   $result = $db->Execute("DELETE FROM $dbtables[farm_activities] "
                               ."WHERE tribeid = '$tribe[tribeid]' "
                               ."AND action = '$act_do[action]' "
                               ."AND crop = '$act_do[crop]'");
                     db_op_result($result,__LINE__,__FILE__);
                }

                if( $cropinfo['crop'] == 'hemp' )
                {
                    $harvest = ($cropinfo[harvest] / $cropinfo['acres']);
                    $acres_harvested = 0;
                    $cotton_harvest = 0;

                    while( $cropinfo['acres'] > 0 && $act_do['actives'] > 1 )
                    {
                        $cropinfo['acres'] -= 1;
                        $acres_harvested += 1;
                        $act_do['actives'] -= 1;
                        $cotton_harvest += $harvest;
                    }
                    $cotton_harvest = round($cotton_harvest);

                    $result = $db->Execute("UPDATE $dbtables[products] "
                                ."SET amount = amount + $cotton_harvest "
                                ."WHERE tribeid = '$tribe[goods_tribe]' "
                                ."AND long_name = 'cotton'");
                     db_op_result($result,__LINE__,__FILE__);
                    $result = $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres - $acres_harvested "
                                ."WHERE cropid = '$cropinfo[cropid]' "
                                ."AND clanid = '$tribe[clanid]' "
                                ."AND hex_id = '$tribe[hex_id]'");
                      db_op_result($result,__LINE__,__FILE__);
                    $result = $db->Execute("INSERT INTO $dbtables[logs] "
                                ."VALUES("
                                ."'',"
                                ."'$month[count]',"
                                ."'$year[count]',"
                                ."'$tribe[clanid]',"
                                ."'$tribe[tribeid]',"
                                ."'FARMING',"
                                ."'$stamp',"
                                ."'Farming: We harvested $cotton_harvest $act_do[crop] from $acres_harvested acres of farmland.')");
                   db_op_result($result,__LINE__,__FILE__);
                   $result = $db->Execute("DELETE FROM $dbtables[farm_activities] "
                               ."WHERE tribeid = '$tribe[tribeid]' "
                               ."AND action = '$act_do[action]' "
                               ."AND crop = '$act_do[crop]'");
                   db_op_result($result,__LINE__,__FILE__);
                }

                if( $cropinfo['crop'] == 'potatoes' )
                {
                    $harvest = ($cropinfo[harvest] / $cropinfo['acres']);
                    $acres_harvested = 0;
                    $cotton_harvest = 0;

                    while( $cropinfo['acres'] > 0 && $act_do['actives'] > 1 )
                    {
                        $cropinfo['acres'] -= 1;
                        $acres_harvested += 1;
                        $act_do['actives'] -= 1;
                        $cotton_harvest += $harvest;
                    }
                    $cotton_harvest = round($cotton_harvest);

                    $result = $db->Execute("UPDATE $dbtables[products] "
                                ."SET amount = amount + $cotton_harvest "
                                ."WHERE tribeid = '$tribe[goods_tribe]' "
                                ."AND long_name = 'provs'");
                      db_op_result($result,__LINE__,__FILE__);
                    $result = $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres - $acres_harvested "
                                ."WHERE cropid = '$cropinfo[cropid]' "
                                ."AND clanid = '$tribe[clanid]' "
                                ."AND hex_id = '$tribe[hex_id]'");
                     db_op_result($result,__LINE__,__FILE__);
                    $result = $db->Execute("INSERT INTO $dbtables[logs] "
                                ."VALUES("
                                ."'',"
                                ."'$month[count]',"
                                ."'$year[count]',"
                                ."'$tribe[clanid]',"
                                ."'$tribe[tribeid]',"
                                ."'FARMING',"
                                ."'$stamp',"
                                ."'Farming: We harvested $cotton_harvest $act_do[crop] from $acres_harvested acres of farmland.')");
                    db_op_result($result,__LINE__,__FILE__);
                   $result = $db->Execute("DELETE FROM $dbtables[farm_activities] "
                               ."WHERE tribeid = '$tribe[tribeid]' "
                               ."AND action = '$act_do[action]' "
                               ."AND crop = '$act_do[crop]'");
                     db_op_result($result,__LINE__,__FILE__);
                }

                if( $cropinfo['crop'] == 'corn' )
                {
                    $harvest = ($cropinfo[harvest] / $cropinfo['acres']) * 3;
                    $acres_harvested = 0;
                    $cotton_harvest = 0;

                    while( $cropinfo['acres'] > 2 && $act_do['actives'] > 1 )
                    {
                        $cropinfo['acres'] -= 3;
                        $acres_harvested += 3;
                        $act_do['actives'] -= 1;
                        $cotton_harvest += $harvest;
                    }
                    $cotton_harvest = round($cotton_harvest);

                    $result = $db->Execute("UPDATE $dbtables[products] "
                                ."SET amount = amount + $cotton_harvest "
                                ."WHERE tribeid = '$tribe[goods_tribe]' "
                                ."AND long_name = 'provs'");
                     db_op_result($result,__LINE__,__FILE__);
                    $result = $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres - $acres_harvested "
                                ."WHERE cropid = '$cropinfo[cropid]' "
                                ."AND clanid = '$tribe[clanid]' "
                                ."AND hex_id = '$tribe[hex_id]'");
                      db_op_result($result,__LINE__,__FILE__);
                    $result = $db->Execute("INSERT INTO $dbtables[logs] "
                                ."VALUES("
                                ."'',"
                                ."'$month[count]',"
                                ."'$year[count]',"
                                ."'$tribe[clanid]',"
                                ."'$tribe[tribeid]',"
                                ."'FARMING',"
                                ."'$stamp',"
                                ."'Farming: We harvested $cotton_harvest $act_do[crop] from $acres_harvested acres of farmland.')");
                    db_op_result($result,__LINE__,__FILE__);
                   $result = $db->Execute("DELETE FROM $dbtables[farm_activities] "
                               ."WHERE tribeid = '$tribe[tribeid]' "
                               ."AND action = '$act_do[action]' "
                               ."AND crop = '$act_do[crop]'");
                    db_op_result($result,__LINE__,__FILE__);
                }

                if( $cropinfo['crop'] == 'herbs' )
                {
                    $harvest = ($cropinfo[harvest] / $cropinfo['acres']);
                    $acres_harvested = 0;
                    $cotton_harvest = 0;

                    while( $cropinfo['acres'] > 0 && $act_do['actives'] > 1 )
                    {
                        $cropinfo['acres'] -= 1;
                        $acres_harvested += 1;
                        $act_do['actives'] -= 1;
                        $cotton_harvest += $harvest;
                    }
                    $cotton_harvest = round($cotton_harvest);

                    $result = $db->Execute("UPDATE $dbtables[products] "
                                ."SET amount = amount + $cotton_harvest "
                                ."WHERE tribeid = '$tribe[goods_tribe]' "
                                ."AND long_name = 'herbs'");
                     db_op_result($result,__LINE__,__FILE__);
                    $result = $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres - $acres_harvested "
                                ."WHERE cropid = '$cropinfo[cropid]' "
                                ."AND clanid = '$tribe[clanid]' "
                                ."AND hex_id = '$tribe[hex_id]'");
                    db_op_result($result,__LINE__,__FILE__);
                    $result = $db->Execute("INSERT INTO $dbtables[logs] "
                                ."VALUES("
                                ."'',"
                                ."'$month[count]',"
                                ."'$year[count]',"
                                ."'$tribe[clanid]',"
                                ."'$tribe[tribeid]',"
                                ."'FARMING',"
                                ."'$stamp',"
                                ."'Farming: We harvested $cotton_harvest $act_do[crop] from $acres_harvested acres of farmland.')");
                      db_op_result($result,__LINE__,__FILE__);
                   $result = $db->Execute("DELETE FROM $dbtables[farm_activities] "
                               ."WHERE tribeid = '$tribe[tribeid]' "
                               ."AND action = '$act_do[action]' "
                               ."AND crop = '$act_do[crop]'");
                     db_op_result($result,__LINE__,__FILE__);
                }
                if( $cropinfo['crop'] == 'spice' )
                {
                    $harvest = ($cropinfo[harvest] / $cropinfo['acres']);
                    $acres_harvested = 0;
                    $cotton_harvest = 0;

                    while( $cropinfo['acres'] > 0 && $act_do['actives'] > 1 )
                    {
                        $cropinfo['acres'] -= 1;
                        $acres_harvested += 1;
                        $act_do['actives'] -= 1;
                        $cotton_harvest += $harvest;
                    }
                    $cotton_harvest = round($cotton_harvest);

                    $result = $db->Execute("UPDATE $dbtables[products] "
                                ."SET amount = amount + $cotton_harvest "
                                ."WHERE tribeid = '$tribe[goods_tribe]' "
                                ."AND long_name = 'spice'");
                      db_op_result($result,__LINE__,__FILE__);
                    $result = $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres - $acres_harvested "
                                ."WHERE cropid = '$cropinfo[cropid]' "
                                ."AND clanid = '$tribe[clanid]' "
                                ."AND hex_id = '$tribe[hex_id]'");
                      db_op_result($result,__LINE__,__FILE__);
                    $result = $db->Execute("INSERT INTO $dbtables[logs] "
                                ."VALUES("
                                ."'',"
                                ."'$month[count]',"
                                ."'$year[count]',"
                                ."'$tribe[clanid]',"
                                ."'$tribe[tribeid]',"
                                ."'FARMING',"
                                ."'$stamp',"
                                ."'Farming: We harvested $cotton_harvest $act_do[crop] from $acres_harvested acres of farmland.')");
                      db_op_result($result,__LINE__,__FILE__);
                   $result = $db->Execute("DELETE FROM $dbtables[farm_activities] "
                               ."WHERE tribeid = '$tribe[tribeid]' "
                               ."AND action = '$act_do[action]' "
                               ."AND crop = '$act_do[crop]'");
                      db_op_result($result,__LINE__,__FILE__);
                }
            }
            else
            {
                $result = $db->Execute("INSERT INTO $dbtables[logs] "
                            ."VALUES("
                            ."'',"
                            ."'$month[count]',"
                            ."'$year[count]',"
                            ."'$tribe[clanid]',"
                            ."'$tribe[tribeid]',"
                            ."'FARMING',"
                            ."'$stamp',"
                            ."'Farming: We could not find any $act_do[crop] to harvest.')");
                  db_op_result($result,__LINE__,__FILE__);
            }
            $result = $db->Execute("DELETE FROM $dbtables[farm_activities] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND action = '$act_do[action]'");
               db_op_result($result,__LINE__,__FILE__);
        }

    $act->MoveNext();
    }
    $result = $db->Execute("DELETE FROM $dbtables[farm_activities] "
                ."WHERE tribeid = '$tribe[tribeid]'");
      db_op_result($result,__LINE__,__FILE__);
    $res->MoveNext();
}
$result = $db->Execute("DELETE FROM $dbtables[farming] "
            ."WHERE acres < 1");
 db_op_result($result,__LINE__,__FILE__);

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
