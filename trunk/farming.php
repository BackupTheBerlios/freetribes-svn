<?php
require_once("../config.php");
$time_start = getmicrotime();
include("game_time.php");
connectdb();
$res = $db->Execute("SELECT * FROM $dbtables[tribes]");
while( !$res->EOF )
{
    $tribe = $res->fields;

    $act = $db->Execute("SELECT * FROM $dbtables[farm_activities] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND hex_id = '$tribe[hex_id]' "
                        ."AND action = 'plow'");
    $hex = $db->Execute("SELECT terrain FROM $dbtables[hexes] "
                       ."WHERE hex_id = '$tribe[hex_id]'");
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
            $db->Execute("INSERT INTO $dbtables[logs] "
                        ."VALUES("
                        ."'',"
                        ."'$month[count]',"
                        ."'$year[count]',"
                        ."'0000',"
                        ."'0000.00',"
                        ."'DEBUG',"
                        ."'$stamp',"
                        ."'Farming debug: $tribe[tribeid] tried $act_do[action] $act_do[crop] with $act_do[actives] actives.')");
        }

        if( $act_do['action'] == 'plow' )
        {
            if( $month['count'] == '3' | $month['count'] == '4' | $month['count'] == '5' | $month['count'] == '6' || $_REQUEST['farming'] == 1)
            {
                $plow = $db->Execute("SELECT * FROM $dbtables[products] "
                                    ."WHERE tribeid = '$tribe[goods_tribe]' "
                                    ."AND long_name = 'plow'");
                $plowinfo = $plow->fields;
                $rake = $db->Execute("SELECT * FROM $dbtables[products] "
                                    ."WHERE tribeid = '$tribe[goods_tribe]' "
                                    ."AND long_name = 'rake'");
                $rakeinfo = $rake->fields;
                $hoe = $db->Execute("SELECT * FROM $dbtables[products] "
                                   ."WHERE tribeid = '$tribe[goods_tribe]' "
                                   ."AND long_name = 'hoe'");
                $hoeinfo = $hoe->fields;
                $totalacres = 0;
                $plowsused = 0;
                $rakesused = 0;
                $hoesused = 0;
                $acres = 8 + $bonus_acres;
                while( $plowinfo['amount'] > 0 && $act_do['actives'] > 0 )
                {
                    $plowinfo['amount'] -= 1;
                    $act_do['actives'] -= 1;
                    $totalacres += $acres;
                    $plowsused += 1;
                }
                $acres = 1 + $bonus_acres;
                while( $rakeinfo['amount'] > 0 && $act_do['actives'] > 0 )
                {
                    $rakeinfo['amount'] -= 1;
                    $act_do['actives'] -= 1;
                    $totalacres += $acres;
                    $rakesused += 1;
                }
                $acres = 2 + $bonus_acres;
                while( $hoeinfo['amount'] > 0 && $act_do['actives'] > 0 )
                {
                    $hoeinfo['amount'] -= 1;
                    $act_do['actives'] -= 1;
                    $totalacres += $acres;
                    $hoesused += 1;
                }
                if( $plowsused > 0 )
                {
                $db->Execute("INSERT INTO $dbtables[products_used] "
                            ."VALUES("
                            ."'$tribe[goods_tribe]',"
                            ."'$plowsused',"
                            ."'plow')");
                $db->Execute("UPDATE $dbtables[products] "
                            ."SET amount = amount - $plowsused "
                            ."WHERE tribeid = '$tribe[goods_tribe]' "
                            ."AND long_name = 'plow'");
                }
                if( $hoesused > 0 )
                {
                $db->Execute("INSERT INTO $dbtables[products_used] "
                            ."VALUES("
                            ."'$tribe[goods_tribe]',"
                            ."'$hoesused',"
                            ."'hoe')");
                $db->Execute("UPDATE $dbtables[products] "
                            ."SET amount = amount - $hoesused "
                            ."WHERE tribeid = '$tribe[goods_tribe]' "
                            ."AND long_name = 'hoe'");
                }
                if( $rakesused > 0 )
                {
                $db->Execute("INSERT INTO $dbtables[products_used] "
                            ."VALUES("
                            ."'$tribe[goods_tribe]',"
                            ."'$rakesused',"
                            ."'rake')");
                $db->Execute("UPDATE $dbtables[products] "
                            ."SET amount = amount - $rakesused "
                            ."WHERE tribeid = '$tribe[goods_tribe]' "
                            ."AND long_name = 'rake'");
                }
                $db->Execute("INSERT INTO $dbtables[logs] "
                            ."VALUES("
                            ."'',"
                            ."'$month[count]',"
                            ."'$year[count]',"
                            ."'$tribe[clanid]',"
                            ."'$tribe[tribeid]',"
                            ."'FARMING',"
                            ."'$stamp',"
                            ."'Farming: We have plowed $totalacres acres for farming using $plowsused plows, $rakesused rakes, $hoesused hoes.')");
                $skill = $db->Execute("SELECT * FROM $dbtables[skills] "
                                     ."WHERE tribeid = '$tribe[tribeid]' "
                                     ."AND abbr = 'farm'");
                $skillinfo = $skill->fields;
                $here = $db->Execute("SELECT * FROM $dbtables[farming] "
                                    ."WHERE clanid = '$tribe[clanid]' "
                                    ."AND hex_id = '$tribe[hex_id]' "
                                    ."AND crop = 'NONE'");
                if( $here->EOF )
                {
                $db->Execute("INSERT INTO $dbtables[farming] "
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
                }
                else
                {
                $db->Execute("UPDATE $dbtables[farming] "
                            ."SET acres = acres + $totalacres "
                            ."WHERE clanid = '$tribe[clanid]' "
                            ."AND hex_id = '$tribe[hex_id] "
                            ."AND crop = 'NONE'");
                }
                $db->Execute("DELETE FROM $dbtables[farm_activities] "
                            ."WHERE tribeid = '$tribe[tribeid]' "
                            ."AND action = 'plow'");
            }
            else
            {
                $db->Execute("INSERT INTO $dbtables[logs] "
                            ."VALUES("
                            ."'',"
                            ."'$month[count]',"
                            ."'$year[count]',"
                            ."'$tribe[clanid]',"
                            ."'$tribe[tribeid]',"
                            ."'FARMING',"
                            ."'$stamp',"
                            ."'Farming: We can only plow during spring months.')");
                $db->Execute("DELETE FROM $dbtables[farm_activities] "
                            ."WHERE tribeid = '$tribe[tribeid]' "
                            ."AND action = 'plow'");
            }
        }
        $act->MoveNext();
    }
    $act = $db->Execute("SELECT * FROM $dbtables[farm_activities] "
                       ."WHERE tribeid = '$tribe[tribeid]' "
                       ."AND hex_id = '$tribe[hex_id]' "
                       ."AND action = 'plant'");

    while( !$act->EOF )
    {
        $act_do = $act->fields;
        $hex = $db->Execute("SELECT terrain FROM $dbtables[hexes] "
                           ."WHERE hex_id = '$tribe[hex_id]'");
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
            if( !$plow->EOF )
            {
            $plowinfo = $plow->fields;
            $acres_planted = 0;

            if( $act_do['crop'] == 'cotton' )
            {
                $acres = 3 + $bonus_acres;
                while( $plowinfo['acres'] > $acres && $act_do['actives'] > 0 )
                {
                    $plowinfo['acres'] -= $acres;
                    $act_do['actives'] -= 1;
                    $acres_planted += $acres;
                }
                $acres_planted = round($acres_planted);
                if( $plowinfo['acres'] < 1 )
                {
                    $db->Execute("DELETE FROM $dbtables[farming] "
                                ."WHERE cropid = '$plowinfo[cropid]' "
                                ."AND hex_id = '$tribe[hex_id]' "
                                ."AND crop = 'NONE' "
                                ."AND clanid = '$tribe[clanid]'");
                }
                else
                {
                    $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres - $acres_planted "
                                ."WHERE clanid = '$tribe[clanid]' "
                                ."AND crop = 'NONE' "
                                ."AND cropid = '$plowinfo[cropid]' "
                                ."AND hex_id = '$tribe[hex_id]'");
                }

                $there = $db->Execute("SELECT * FROM $dbtables[farming] "
                                     ."WHERE clanid = '$tribe[clanid]' "
                                     ."AND hex_id = '$tribe[hex_id]' "
                                     ."AND crop = 'cotton'");
                if( !$there->EOF )
                {
                    $therecrop = $there->fields;
                    $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres + $acres_planted "
                                ."WHERE cropid = '$therecrop[cropid]' "
                                ."AND crop = '$act_do[crop]' "
                                ."AND hex_id = '$tribe[hex_id]' "
                                ."AND clanid = '$tribe[clanid]'");

                }
                else
                {
                    $skill = $db->Execute("SELECT * FROM $dbtables[skills] "
                                         ."WHERE tribeid = '$tribe[tribeid]' "
                                         ."AND abbr = 'farm'");
                    $skillinfo = $skill->fields;

                    $db->Execute("INSERT INTO $dbtables[farming] "
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
                }
                $db->Execute("DELETE FROM $dbtables[farm_activities] "
                            ."WHERE action = 'plant' "
                            ."AND crop = '$act_do[crop]' "
                            ."AND tribeid = '$tribe[tribeid]'");
                $db->Execute("INSERT INTO $dbtables[logs] "
                            ."VALUES("
                            ."'',"
                            ."'$month[count]',"
                            ."'$year[count]',"
                            ."'$tribe[clanid]',"
                            ."'$tribe[tribeid]',"
                            ."'FARMING',"
                            ."'$stamp',"
                            ."'Farming: We planted $acres_planted acres of $act_do[crop].')");

            }

            if( $act_do['crop'] == 'grain' )
            {
                $acres = 5 + $bonus_acres;
                while( $plowinfo['acres'] >= $acres && $act_do['actives'] > 0 )
                {
                    $plowinfo['acres'] -= $acres;
                    $act_do['actives'] -= 1;
                    $acres_planted += $acres;
                }
                $acres_planted = round($acres_planted);
                if( $plowinfo['acres'] < 1 )
                {
                    $db->Execute("DELETE FROM $dbtables[farming] "
                                ."WHERE cropid = '$plowinfo[cropid]' "
                                ."AND hex_id = '$tribe[hex_id]' "
                                ."AND crop = 'NONE' "
                                ."AND clanid = '$tribe[clanid]'");
                }
                else
                {
                    $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres - $acres_planted "
                                ."WHERE clanid = '$tribe[clanid]' "
                                ."AND crop = 'NONE' "
                                ."AND cropid = '$plowinfo[cropid]' "
                                ."AND hex_id = '$tribe[hex_id]'");
                }

                $there = $db->Execute("SELECT * FROM $dbtables[farming] "
                                     ."WHERE clanid = '$tribe[clanid]' "
                                     ."AND hex_id = '$tribe[hex_id]' "
                                     ."AND crop = 'grain'");
                if( !$there->EOF )
                {
                    $therecrop = $there->fields;
                    $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres + $acres_planted "
                                ."WHERE cropid = '$therecrop[cropid]' "
                                ."AND crop = '$act_do[crop]' "
                                ."AND hex_id = '$tribe[hex_id]' "
                                ."AND clanid = '$tribe[clanid]'");
                }
                else
                {
                    $skill = $db->Execute("SELECT * FROM $dbtables[skills] "
                                         ."WHERE tribeid = '$tribe[tribeid]' "
                                         ."AND abbr = 'farm'");
                    $skillinfo = $skill->fields;

                    $db->Execute("INSERT INTO $dbtables[farming] "
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
                }
                $db->Execute("DELETE FROM $dbtables[farm_activities] "
                            ."WHERE action = 'plant' "
                            ."AND crop = '$act_do[crop]' "
                            ."AND tribeid = '$tribe[tribeid]'");
                $db->Execute("INSERT INTO $dbtables[logs] "
                            ."VALUES("
                            ."'',"
                            ."'$month[count]',"
                            ."'$year[count]',"
                            ."'$tribe[clanid]',"
                            ."'$tribe[tribeid]',"
                            ."'FARMING',"
                            ."'$stamp',"
                            ."'Farming: We planted $acres_planted acres of $act_do[crop].')");
            }


            if( $act_do['crop'] == 'grapes' )
            {
                $acres = 2 + $bonus_acres;
                while( $plowinfo['acres'] >= $acres && $act_do['actives'] > 0 )
                {
                    $plowinfo['acres'] -= $acres;
                    $act_do['actives'] -= 1;
                    $acres_planted += $acres;
                }
                $acres_planted = round($acres_planted);
                if( $plowinfo['acres'] < 1 )
                {
                    $db->Execute("DELETE FROM $dbtables[farming] "
                                ."WHERE cropid = '$plowinfo[cropid]' "
                                ."AND hex_id = '$tribe[hex_id]' "
                                ."AND crop = 'NONE' "
                                ."AND clanid = '$tribe[clanid]'");
                }
                else
                {
                    $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres - $acres_planted "
                                ."WHERE clanid = '$tribe[clanid]' "
                                ."AND crop = 'NONE' "
                                ."AND cropid = '$plowinfo[cropid]' "
                                ."AND hex_id = '$tribe[hex_id]'");
                }

                $there = $db->Execute("SELECT * FROM $dbtables[farming] "
                                     ."WHERE clanid = '$tribe[clanid]' "
                                     ."AND hex_id = '$tribe[hex_id]' "
                                     ."AND crop = 'grapes'");
                if( !$there->EOF )
                {
                    $therecrop = $there->fields;
                    $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres + $acres_planted "
                                ."WHERE cropid = '$therecrop[cropid]' "
                                ."AND crop = '$act_do[crop]' "
                                ."AND hex_id = '$tribe[hex_id]' "
                                ."AND clanid = '$tribe[clanid]'");
                }
                else
                {
                    $skill = $db->Execute("SELECT * FROM $dbtables[skills] "
                                         ."WHERE tribeid = '$tribe[tribeid]' "
                                         ."AND abbr = 'farm'");
                    $skillinfo = $skill->fields;

                    $db->Execute("INSERT INTO $dbtables[farming] "
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
                }
                $db->Execute("DELETE FROM $dbtables[farm_activities] "
                            ."WHERE action = 'plant' "
                            ."AND crop = '$act_do[crop]' "
                            ."AND tribeid = '$tribe[tribeid]'");
                $db->Execute("INSERT INTO $dbtables[logs] "
                            ."VALUES("
                            ."'',"
                            ."'$month[count]',"
                            ."'$year[count]',"
                            ."'$tribe[clanid]',"
                            ."'$tribe[tribeid]',"
                            ."'FARMING',"
                            ."'$stamp',"
                            ."'Farming: We planted $acres_planted acres of $act_do[crop].')");
            }

            if( $act_do['crop'] == 'sugar' )
            {
                $acres = 3 + $bonus_acres;
                while( $plowinfo['acres'] >= $acres && $act_do['actives'] > 0 )
                {
                    $plowinfo['acres'] -= $acres;
                    $act_do['actives'] -= 1;
                    $acres_planted += $acres;
                }
                $acres_planted = round($acres_planted);
                if( $plowinfo['acres'] < 1 )
                {
                    $db->Execute("DELETE FROM $dbtables[farming] "
                                ."WHERE cropid = '$plowinfo[cropid]' "
                                ."AND hex_id = '$tribe[hex_id]' "
                                ."AND crop = 'NONE' "
                                ."AND clanid = '$tribe[clanid]'");
                }
                else
                {
                    $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres - $acres_planted "
                                ."WHERE clanid = '$tribe[clanid]' "
                                ."AND crop = 'NONE' "
                                ."AND cropid = '$plowinfo[cropid]' "
                                ."AND hex_id = '$tribe[hex_id]'");
                }

                $there = $db->Execute("SELECT * FROM $dbtables[farming] "
                                     ."WHERE clanid = '$tribe[clanid]' "
                                     ."AND hex_id = '$tribe[hex_id]' "
                                     ."AND crop = 'sugar'");
                if( !$there->EOF )
                {
                    $therecrop = $there->fields;
                    $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres + $acres_planted "
                                ."WHERE cropid = '$therecrop[cropid]' "
                                ."AND crop = '$act_do[crop]' "
                                ."AND hex_id = '$tribe[hex_id]' "
                                ."AND clanid = '$tribe[clanid]'");
                }
                else
                {
                    $skill = $db->Execute("SELECT * FROM $dbtables[skills] "
                                         ."WHERE tribeid = '$tribe[tribeid]' "
                                         ."AND abbr = 'farm'");
                    $skillinfo = $skill->fields;

                    $db->Execute("INSERT INTO $dbtables[farming] "
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
                }
                $db->Execute("DELETE FROM $dbtables[farm_activities] "
                            ."WHERE action = 'plant' "
                            ."AND crop = '$act_do[crop]' "
                            ."AND tribeid = '$tribe[tribeid]'");
                $db->Execute("INSERT INTO $dbtables[logs] "
                            ."VALUES("
                            ."'',"
                            ."'$month[count]',"
                            ."'$year[count]',"
                            ."'$tribe[clanid]',"
                            ."'$tribe[tribeid]',"
                            ."'FARMING',"
                            ."'$stamp',"
                            ."'Farming: We planted $acres_planted acres of $act_do[crop].')");
            }


            if( $act_do['crop'] == 'tobacco' )
            {
                $acres = 2 + $bonus_acres;
                while( $plowinfo['acres'] >= $acres && $act_do['actives'] > 0 )
                {
                    $plowinfo['acres'] -= $acres;
                    $act_do['actives'] -= 1;
                    $acres_planted += $acres;
                }
                $acres_planted = round($acres_planted);
                if( $plowinfo['acres'] < 1 )
                {
                    $db->Execute("DELETE FROM $dbtables[farming] "
                                ."WHERE cropid = '$plowinfo[cropid]' "
                                ."AND hex_id = '$tribe[hex_id]' "
                                ."AND crop = 'NONE' "
                                ."AND clanid = '$tribe[clanid]'");
                }
                else
                {
                    $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres - $acres_planted "
                                ."WHERE clanid = '$tribe[clanid]' "
                                ."AND crop = 'NONE' "
                                ."AND cropid = '$plowinfo[cropid]' "
                                ."AND hex_id = '$tribe[hex_id]'");
                }

                $there = $db->Execute("SELECT * FROM $dbtables[farming] "
                                     ."WHERE clanid = '$tribe[clanid]' "
                                     ."AND hex_id = '$tribe[hex_id]' "
                                     ."AND crop = 'tobacco'");
                if( !$there->EOF )
                {
                    $therecrop = $there->fields;
                    $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres + $acres_planted "
                                ."WHERE cropid = '$therecrop[cropid]' "
                                ."AND crop = '$act_do[crop]' "
                                ."AND hex_id = '$tribe[hex_id]' "
                                ."AND clanid = '$tribe[clanid]'");
                }
                else
                {
                    $skill = $db->Execute("SELECT * FROM $dbtables[skills] "
                                         ."WHERE tribeid = '$tribe[tribeid]' "
                                         ."AND abbr = 'farm'");
                    $skillinfo = $skill->fields;

                    $db->Execute("INSERT INTO $dbtables[farming] "
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
                }
                $db->Execute("DELETE FROM $dbtables[farm_activities] "
                            ."WHERE action = 'plant' "
                            ."AND crop = '$act_do[crop]' "
                            ."AND tribeid = '$tribe[tribeid]'");
                $db->Execute("INSERT INTO $dbtables[logs] "
                            ."VALUES("
                            ."'',"
                            ."'$month[count]',"
                            ."'$year[count]',"
                            ."'$tribe[clanid]',"
                            ."'$tribe[tribeid]',"
                            ."'FARMING',"
                            ."'$stamp',"
                            ."'Farming: We planted $acres_planted acres of $act_do[crop].')");
            }

            if( $act_do['crop'] == 'flax' )
            {
                $acres = 3 + $bonus_acres;
                while( $plowinfo['acres'] >= $acres && $act_do['actives'] > 0 )
                {
                    $plowinfo['acres'] -= $acres;
                    $act_do['actives'] -= 1;
                    $acres_planted += $acres;
                }
                $acres_planted = round($acres_planted);
                if( $plowinfo['acres'] < 1 )
                {
                    $db->Execute("DELETE FROM $dbtables[farming] "
                                ."WHERE cropid = '$plowinfo[cropid]' "
                                ."AND hex_id = '$tribe[hex_id]' "
                                ."AND crop = 'NONE' "
                                ."AND clanid = '$tribe[clanid]'");
                }
                else
                {
                    $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres - $acres_planted "
                                ."WHERE clanid = '$tribe[clanid]' "
                                ."AND crop = 'NONE' "
                                ."AND cropid = '$plowinfo[cropid]' "
                                ."AND hex_id = '$tribe[hex_id]'");
                }

                $there = $db->Execute("SELECT * FROM $dbtables[farming] "
                                     ."WHERE clanid = '$tribe[clanid]' "
                                     ."AND hex_id = '$tribe[hex_id]' "
                                     ."AND crop = 'flax'");
                if( !$there->EOF )
                {
                    $therecrop = $there->fields;
                    $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres + $acres_planted "
                                ."WHERE cropid = '$therecrop[cropid]' "
                                ."AND crop = '$act_do[crop]' "
                                ."AND hex_id = '$tribe[hex_id]' "
                                ."AND clanid = '$tribe[clanid]'");
                }
                else
                {
                    $skill = $db->Execute("SELECT * FROM $dbtables[skills] "
                                         ."WHERE tribeid = '$tribe[tribeid]' "
                                         ."AND abbr = 'farm'");
                    $skillinfo = $skill->fields;

                    $db->Execute("INSERT INTO $dbtables[farming] "
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
                }
                $db->Execute("DELETE FROM $dbtables[farm_activities] "
                            ."WHERE action = 'plant' "
                            ."AND crop = '$act_do[crop]' "
                            ."AND tribeid = '$tribe[tribeid]'");
                $db->Execute("INSERT INTO $dbtables[logs] "
                            ."VALUES("
                            ."'',"
                            ."'$month[count]',"
                            ."'$year[count]',"
                            ."'$tribe[clanid]',"
                            ."'$tribe[tribeid]',"
                            ."'FARMING',"
                            ."'$stamp',"
                            ."'Farming: We planted $acres_planted acres of $act_do[crop].')");
            }

            if( $act_do['crop'] == 'hemp' )
            {
                $acres = 2 + $bonus_acres;
                while( $plowinfo['acres'] >= $acres && $act_do['actives'] > 0 )
                {
                    $plowinfo['acres'] -= $acres;
                    $act_do['actives'] -= 1;
                    $acres_planted += $acres;
                }
                $acres_planted = round($acres_planted);
                if( $plowinfo['acres'] < 1 )
                {
                    $db->Execute("DELETE FROM $dbtables[farming] "
                                ."WHERE cropid = '$plowinfo[cropid]' "
                                ."AND hex_id = '$tribe[hex_id]' "
                                ."AND crop = 'NONE' "
                                ."AND clanid = '$tribe[clanid]'");
                }
                else
                {
                    $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres - $acres_planted "
                                ."WHERE clanid = '$tribe[clanid]' "
                                ."AND crop = 'NONE' "
                                ."AND cropid = '$plowinfo[cropid]' "
                                ."AND hex_id = '$tribe[hex_id]'");
                }

                $there = $db->Execute("SELECT * FROM $dbtables[farming] "
                                     ."WHERE clanid = '$tribe[clanid]' "
                                     ."AND hex_id = '$tribe[hex_id]' "
                                     ."AND crop = 'hemp'");
                if( !$there->EOF )
                {
                    $therecrop = $there->fields;
                    $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres + $acres_planted "
                                ."WHERE cropid = '$therecrop[cropid]' "
                                ."AND crop = '$act_do[crop]' "
                                ."AND hex_id = '$tribe[hex_id]' "
                                ."AND clanid = '$tribe[clanid]'");
                }
                else
                {
                    $skill = $db->Execute("SELECT * FROM $dbtables[skills] "
                                         ."WHERE tribeid = '$tribe[tribeid]' "
                                         ."AND abbr = 'farm'");
                    $skillinfo = $skill->fields;

                    $db->Execute("INSERT INTO $dbtables[farming] "
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
                }
                $db->Execute("DELETE FROM $dbtables[farm_activities] "
                            ."WHERE action = 'plant' "
                            ."AND crop = '$act_do[crop]' "
                            ."AND tribeid = '$tribe[tribeid]'");
                $db->Execute("INSERT INTO $dbtables[logs] "
                            ."VALUES("
                            ."'',"
                            ."'$month[count]',"
                            ."'$year[count]',"
                            ."'$tribe[clanid]',"
                            ."'$tribe[tribeid]',"
                            ."'FARMING',"
                            ."'$stamp',"
                            ."'Farming: We planted $acres_planted acres of $act_do[crop].')");
            }

            if( $act_do['crop'] == 'potatoes' )
            {
                $acres = 3 + $bonus_acres;
                while( $plowinfo['acres'] >= $acres && $act_do['actives'] > 0 )
                {
                    $plowinfo['acres'] -= $acres;
                    $act_do['actives'] -= 1;
                    $acres_planted += $acres;
                }
                $acres_planted = round($acres_planted);
                if( $plowinfo['acres'] < 1 )
                {
                    $db->Execute("DELETE FROM $dbtables[farming] "
                                ."WHERE cropid = '$plowinfo[cropid]' "
                                ."AND hex_id = '$tribe[hex_id]' "
                                ."AND crop = 'NONE' "
                                ."AND clanid = '$tribe[clanid]'");
                }
                else
                {
                    $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres - $acres_planted "
                                ."WHERE clanid = '$tribe[clanid]' "
                                ."AND crop = 'NONE' "
                                ."AND cropid = '$plowinfo[cropid]' "
                                ."AND hex_id = '$tribe[hex_id]'");
                }

                $there = $db->Execute("SELECT * FROM $dbtables[farming] "
                                     ."WHERE clanid = '$tribe[clanid]' "
                                     ."AND hex_id = '$tribe[hex_id]' "
                                     ."AND crop = 'potatoes'");
                if( !$there->EOF )
                {
                    $therecrop = $there->fields;
                    $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres + $acres_planted "
                                ."WHERE cropid = '$therecrop[cropid]' "
                                ."AND crop = '$act_do[crop]' "
                                ."AND hex_id = '$tribe[hex_id]' "
                                ."AND clanid = '$tribe[clanid]'");
                }
                else
                {
                    $skill = $db->Execute("SELECT * FROM $dbtables[skills] "
                                         ."WHERE tribeid = '$tribe[tribeid]' "
                                         ."AND abbr = 'farm'");
                    $skillinfo = $skill->fields;

                    $db->Execute("INSERT INTO $dbtables[farming] "
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
                }
                $db->Execute("DELETE FROM $dbtables[farm_activities] "
                            ."WHERE action = 'plant' "
                            ."AND crop = '$act_do[crop]' "
                            ."AND tribeid = '$tribe[tribeid]'");
                $db->Execute("INSERT INTO $dbtables[logs] "
                            ."VALUES("
                            ."'',"
                            ."'$month[count]',"
                            ."'$year[count]',"
                            ."'$tribe[clanid]',"
                            ."'$tribe[tribeid]',"
                            ."'FARMING',"
                            ."'$stamp',"
                            ."'Farming: We planted $acres_planted acres of $act_do[crop].')");
            }

            if( $act_do['crop'] == 'corn' )
            {
                $acres = 5 + $bonus_acres;
                while( $plowinfo['acres'] >= $acres && $act_do['actives'] > 0 )
                {
                    $plowinfo['acres'] -= $acres;
                    $act_do['actives'] -= 1;
                    $acres_planted += $acres;
                }
                $acres_planted = round($acres_planted);
                if( $plowinfo['acres'] < 1 )
                {
                    $db->Execute("DELETE FROM $dbtables[farming] "
                                ."WHERE cropid = '$plowinfo[cropid]' "
                                ."AND hex_id = '$tribe[hex_id]' "
                                ."AND crop = 'NONE' "
                                ."AND clanid = '$tribe[clanid]'");
                }
                else
                {
                    $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres - $acres_planted "
                                ."WHERE clanid = '$tribe[clanid]' "
                                ."AND crop = 'NONE' "
                                ."AND cropid = '$plowinfo[cropid]' "
                                ."AND hex_id = '$tribe[hex_id]'");
                }

                $there = $db->Execute("SELECT * FROM $dbtables[farming] "
                                     ."WHERE clanid = '$tribe[clanid]' "
                                     ."AND hex_id = '$tribe[hex_id]' "
                                     ."AND crop = 'corn'");
                if( !$there->EOF )
                {
                    $therecrop = $there->fields;
                    $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres + $acres_planted "
                                ."WHERE cropid = '$therecrop[cropid]' "
                                ."AND crop = '$act_do[crop]' "
                                ."AND hex_id = '$tribe[hex_id]' "
                                ."AND clanid = '$tribe[clanid]'");
                }
                else
                {
                    $skill = $db->Execute("SELECT * FROM $dbtables[skills] "
                                         ."WHERE tribeid = '$tribe[tribeid]' "
                                         ."AND abbr = 'farm'");
                    $skillinfo = $skill->fields;

                    $db->Execute("INSERT INTO $dbtables[farming] "
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
                }
                $db->Execute("DELETE FROM $dbtables[farm_activities] "
                            ."WHERE action = 'plant' "
                            ."AND crop = '$act_do[crop]' "
                            ."AND tribeid = '$tribe[tribeid]'");
                $db->Execute("INSERT INTO $dbtables[logs] "
                            ."VALUES("
                            ."'',"
                            ."'$month[count]',"
                            ."'$year[count]',"
                            ."'$tribe[clanid]',"
                            ."'$tribe[tribeid]',"
                            ."'FARMING',"
                            ."'$stamp',"
                            ."'Farming: We planted $acres_planted acres of $act_do[crop].')");
            }

            if( $act_do['crop'] == 'herbs' )
            {
                $acres = 1 + $bonus_acres;
                while( $plowinfo['acres'] >= $acres && $act_do['actives'] > 0 )
                {
                    $plowinfo['acres'] -= $acres;
                    $act_do['actives'] -= 1;
                    $acres_planted += $acres;
                }
                $acres_planted = round($acres_planted);
                if( $plowinfo['acres'] < 1 )
                {
                    $db->Execute("DELETE FROM $dbtables[farming] "
                                ."WHERE cropid = '$plowinfo[cropid]' "
                                ."AND hex_id = '$tribe[hex_id]' "
                                ."AND crop = 'NONE' "
                                ."AND clanid = '$tribe[clanid]'");
                }
                else
                {
                    $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres - $acres_planted "
                                ."WHERE clanid = '$tribe[clanid]' "
                                ."AND crop = 'NONE' "
                                ."AND cropid = '$plowinfo[cropid]' "
                                ."AND hex_id = '$tribe[hex_id]'");
                }

                $there = $db->Execute("SELECT * FROM $dbtables[farming] "
                                     ."WHERE clanid = '$tribe[clanid]' "
                                     ."AND hex_id = '$tribe[hex_id]' "
                                     ."AND crop = 'herbs'");
                if( !$there->EOF )
                {
                    $therecrop = $there->fields;
                    $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres + $acres_planted "
                                ."WHERE cropid = '$therecrop[cropid]' "
                                ."AND crop = '$act_do[crop]' "
                                ."AND hex_id = '$tribe[hex_id]' "
                                ."AND clanid = '$tribe[clanid]'");
                }
                else
                {
                    $skill = $db->Execute("SELECT * FROM $dbtables[skills] "
                                         ."WHERE tribeid = '$tribe[tribeid]' "
                                         ."AND abbr = 'farm'");
                    $skillinfo = $skill->fields;

                    $db->Execute("INSERT INTO $dbtables[farming] "
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
                }
                $db->Execute("DELETE FROM $dbtables[farm_activities] "
                            ."WHERE action = 'plant' "
                            ."AND crop = '$act_do[crop]' "
                            ."AND tribeid = '$tribe[tribeid]'");
                $db->Execute("INSERT INTO $dbtables[logs] "
                            ."VALUES("
                            ."'',"
                            ."'$month[count]',"
                            ."'$year[count]',"
                            ."'$tribe[clanid]',"
                            ."'$tribe[tribeid]',"
                            ."'FARMING',"
                            ."'$stamp',"
                            ."'Farming: We planted $acres_planted acres of $act_do[crop].')");
            }

            if( $act_do['crop'] == 'spice' )
            {
                $acres = 1 + $bonus_acres;
                while( $plowinfo['acres'] >= $acres && $act_do['actives'] > 0 )
                {
                    $plowinfo['acres'] -= $acres;
                    $act_do['actives'] -= 1;
                    $acres_planted += $acres;
                }
                $acres_planted = round($acres_planted);
                if( $plowinfo['acres'] < 1 )
                {
                    $db->Execute("DELETE FROM $dbtables[farming] "
                                ."WHERE cropid = '$plowinfo[cropid]' "
                                ."AND hex_id = '$tribe[hex_id]' "
                                ."AND crop = 'NONE' "
                                ."AND clanid = '$tribe[clanid]'");
                }
                else
                {
                    $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres - $acres_planted "
                                ."WHERE clanid = '$tribe[clanid]' "
                                ."AND crop = 'NONE' "
                                ."AND cropid = '$plowinfo[cropid]' "
                                ."AND hex_id = '$tribe[hex_id]'");
                }

                $there = $db->Execute("SELECT * FROM $dbtables[farming] "
                                     ."WHERE clanid = '$tribe[clanid]' "
                                     ."AND hex_id = '$tribe[hex_id]' "
                                     ."AND crop = 'spice'");
                if( !$there->EOF )
                {
                    $therecrop = $there->fields;
                    $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres + $acres_planted "
                                ."WHERE cropid = '$therecrop[cropid]' "
                                ."AND crop = '$act_do[crop]' "
                                ."AND hex_id = '$tribe[hex_id]' "
                                ."AND clanid = '$tribe[clanid]'");
                }
                else
                {
                    $skill = $db->Execute("SELECT * FROM $dbtables[skills] "
                                         ."WHERE tribeid = '$tribe[tribeid]' "
                                         ."AND abbr = 'farm'");
                    $skillinfo = $skill->fields;

                    $db->Execute("INSERT INTO $dbtables[farming] "
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
                }
                $db->Execute("DELETE FROM $dbtables[farm_activities] "
                            ."WHERE action = 'plant' "
                            ."AND crop = '$act_do[crop]' "
                            ."AND tribeid = '$tribe[tribeid]'");
                $db->Execute("INSERT INTO $dbtables[logs] "
                            ."VALUES("
                            ."'',"
                            ."'$month[count]',"
                            ."'$year[count]',"
                            ."'$tribe[clanid]',"
                            ."'$tribe[tribeid]',"
                            ."'FARMING',"
                            ."'$stamp',"
                            ."'Farming: We planted $acres_planted acres of $act_do[crop].')");
            }
                        }
            else
            {
                $db->Execute("INSERT INTO $dbtables[logs] "
                            ."VALUES("
                            ."'',"
                            ."'$month[count]',"
                            ."'$year[count]',"
                            ."'$tribe[clanid]',"
                            ."'$tribe[tribeid]',"
                            ."'FARMING',"
                            ."'$stamp',"
                            ."'Farming: We tried to plant, but the plowing has not been completed yet.')");
                $db->Execute("DELETE FROM $dbtables[farm_activities] "
                            ."WHERE action = 'plant' "
                            ."AND crop = '$act_do[crop]' "
                            ."AND tribeid = '$tribe[tribeid]'");
            }

        }
        $act->MoveNext();
    }

    $act = $db->Execute("SELECT * FROM $dbtables[farm_activities] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND hex_id = '$tribe[hex_id]' "
                        ."AND action = 'harvest'");

    while( !$act->EOF )
    {
        $act_do = $act->fields;
        if( $act_do['action'] == 'harvest' )
        {
            $crop = $db->Execute("SELECT * FROM $dbtables[farming] "
                                ."WHERE hex_id = '$tribe[hex_id]' "
                                ."AND clanid = '$tribe[clanid]' "
                                ."AND crop = '$act_do[crop]'");
            if( !$crop->EOF )
            {
                $cropinfo = $crop->fields;
                $acres_harvested = 0;
                $cotton_harvest = 0;
                if( $cropinfo['crop'] == 'cotton' )
                {
                    $harvest = ($cropinfo['harvest'] / $cropinfo['acres']) * 2;

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

                    $db->Execute("UPDATE $dbtables[products] "
                                ."SET amount = amount + $cotton_harvest "
                                ."WHERE tribeid = '$tribe[goods_tribe]' "
                                ."AND long_name = 'cotton'");
                    $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres - $acres_harvested "
                                ."WHERE cropid = '$cropinfo[cropid]' "
                                ."AND clanid = '$tribe[clanid]' "
                                ."AND hex_id = '$tribe[hex_id]'");
                    $db->Execute("INSERT INTO $dbtables[logs] "
                                ."VALUES("
                                ."'',"
                                ."'$month[count]',"
                                ."'$year[count]',"
                                ."'$tribe[clanid]',"
                                ."'$tribe[tribeid]',"
                                ."'FARMING',"
                                ."'$stamp',"
                                ."'Farming: We harvested $cotton_harvest cotton from $acres_harvested acres of farmland.')");
                   $db->Execute("DELETE FROM $dbtables[farm_activities] "
                               ."WHERE tribeid = '$tribe[tribeid]' "
                               ."AND action = '$act_do[action]' "
                               ."AND crop = '$act_do[crop]'");
                }

                if( $cropinfo['crop'] == 'grain' )
                {
                    $scy = $db->Execute("SELECT * FROM $dbtables[products] "
                                       ."WHERE tribeid = '$tribe[goods_tribe]' "
                                       ."AND long_name = 'scythe'");
                    if( !$scy->EOF )
                    {
                        $scythe = $scy->fields;
                        $actives = $act_do['actives'];
                        $scythe_used = 0;
                        while( $scythe['amount'] > 0 && $actives > 0 )
                        {
                            $scythe['amount'] -= 1;
                            $actives -= 1;
                            $act_do['actives'] += 1;
                            $scythe_used += 1;
                        }
                        if( $scythe_used > 0 )
                        {
                            $db->Execute("INSERT INTO $dbtables[products_used] "
                                        ."VALUES("
                                        ."'$tribe[goods_tribe]',"
                                        ."'$scythe_used',"
                                        ."'scythe')");
                            $db->Execute("UPDATE $dbtables[products] "
                                        ."SET amount = amount - $scythe_used "
                                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                                        ."AND long_name = 'scythe'");
                        }
                    }
                    $harvest = ($cropinfo['harvest'] / $cropinfo['acres']) * 3;
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
                    $db->Execute("UPDATE $dbtables[products] "
                                ."SET amount = amount + $cotton_harvest "
                                ."WHERE tribeid = '$tribe[goods_tribe]' "
                                ."AND long_name = '$act_do[crop]'");
                    $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres - $acres_harvested "
                                ."WHERE cropid = '$cropinfo[cropid]' "
                                ."AND clanid = '$tribe[clanid]' "
                                ."AND hex_id = '$tribe[hex_id]'");
                    $db->Execute("INSERT INTO $dbtables[logs] "
                                ."VALUES("
                                ."'',"
                                ."'$month[count]',"
                                ."'$year[count]',"
                                ."'$tribe[clanid]',"
                                ."'$tribe[tribeid]',"
                                ."'FARMING',"
                                ."'$stamp',"
                                ."'Farming: We harvested $cotton_harvest $act_do[crop] from $acres_harvested acres of farmland.')");
                   $db->Execute("DELETE FROM $dbtables[farm_activities] "
                               ."WHERE tribeid = '$tribe[tribeid]' "
                               ."AND action = '$act_do[action]' "
                               ."AND crop = '$act_do[crop]'");
                }

                if( $cropinfo['crop'] == 'grapes' )
                {
                    $harvest = ($cropinfo['harvest'] / $cropinfo['acres']);
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

                    $db->Execute("UPDATE $dbtables[products] "
                                ."SET amount = amount + $cotton_harvest "
                                ."WHERE tribeid = '$tribe[goods_tribe]' "
                                ."AND long_name = '$act_do[crop]'");
                    $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres - $acres_harvested "
                                ."WHERE cropid = '$cropinfo[cropid]' "
                                ."AND clanid = '$tribe[clanid]' "
                                ."AND hex_id = '$tribe[hex_id]'");
                    $db->Execute("INSERT INTO $dbtables[logs] "
                                ."VALUES("
                                ."'',"
                                ."'$month[count]',"
                                ."'$year[count]',"
                                ."'$tribe[clanid]',"
                                ."'$tribe[tribeid]',"
                                ."'FARMING',"
                                ."'$stamp',"
                                ."'Farming: We harvested $cotton_harvest $act_do[crop] from $acres_harvested acres of farmland.')");
                   $db->Execute("DELETE FROM $dbtables[farm_activities] "
                               ."WHERE tribeid = '$tribe[tribeid]' "
                               ."AND action = '$act_do[action]' "
                               ."AND crop = '$act_do[crop]'");
                }

                if( $cropinfo['crop'] == 'sugar' )
                {
                    $scy = $db->Execute("SELECT * FROM $dbtables[products] "
                                       ."WHERE tribeid = '$tribe[goods_tribe]' "
                                       ."AND long_name = 'scythe'");
                    if( !$scy->EOF )
                    {
                        $scythe = $scy->fields;
                        $actives = $act_do['actives'];
                        $scythe_used = 0;
                        while( $scythe['amount'] > 0 && $actives > 0 )
                        {
                            $scythe['amount'] -= 1;
                            $actives -= 1;
                            $act_do['actives'] += 1;
                            $scythe_used += 1;
                        }
                        if( $scythe_used > 0 )
                        {
                            $db->Execute("INSERT INTO $dbtables[products_used] "
                                        ."VALUES("
                                        ."'$tribe[goods_tribe]',"
                                        ."'$scythe_used',"
                                        ."'scythe')");
                            $db->Execute("UPDATE $dbtables[products] "
                                        ."SET amount = amount - $scythe_used "
                                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                                        ."AND long_name = 'scythe'");
                        }
                    }
                    $harvest = ($cropinfo['harvest'] / $cropinfo['acres']) * 2;
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

                    $db->Execute("UPDATE $dbtables[products] "
                                ."SET amount = amount + $cotton_harvest "
                                ."WHERE tribeid = '$tribe[goods_tribe]' "
                                ."AND long_name = '$act_do[crop]'");
                    $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres - $acres_harvested "
                                ."WHERE cropid = '$cropinfo[cropid]' "
                                ."AND clanid = '$tribe[clanid]' "
                                ."AND hex_id = '$tribe[hex_id]'");
                    $db->Execute("INSERT INTO $dbtables[logs] "
                                ."VALUES("
                                ."'',"
                                ."'$month[count]',"
                                ."'$year[count]',"
                                ."'$tribe[clanid]',"
                                ."'$tribe[tribeid]',"
                                ."'FARMING',"
                                ."'$stamp',"
                                ."'Farming: We harvested $cotton_harvest $act_do[crop] from $acres_harvested acres of farmland.')");
                   $db->Execute("DELETE FROM $dbtables[farm_activities] "
                               ."WHERE tribeid = '$tribe[tribeid]' "
                               ."AND action = '$act_do[action]' "
                               ."AND crop = '$act_do[crop]'");
                }

                if( $cropinfo['crop'] == 'tobacco' )
                {
                    $harvest = ($cropinfo['harvest'] / $cropinfo['acres']);
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

                    $db->Execute("UPDATE $dbtables[products] "
                                ."SET amount = amount + $cotton_harvest "
                                ."WHERE tribeid = '$tribe[goods_tribe]' "
                                ."AND long_name = '$act_do[crop]'");
                    $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres - $acres_harvested "
                                ."WHERE cropid = '$cropinfo[cropid]' "
                                ."AND clanid = '$tribe[clanid]' "
                                ."AND hex_id = '$tribe[hex_id]'");
                    $db->Execute("INSERT INTO $dbtables[logs] "
                                ."VALUES("
                                ."'',"
                                ."'$month[count]',"
                                ."'$year[count]',"
                                ."'$tribe[clanid]',"
                                ."'$tribe[tribeid]',"
                                ."'FARMING',"
                                ."'$stamp',"
                                ."'Farming: We harvested $cotton_harvest $act_do[crop] from $acres_harvested acres of farmland.')");
                   $db->Execute("DELETE FROM $dbtables[farm_activities] "
                               ."WHERE tribeid = '$tribe[tribeid]' "
                               ."AND action = '$act_do[action]' "
                               ."AND crop = '$act_do[crop]'");
                }
                if( $cropinfo['crop'] == 'flax' )
                {
                    $harvest = ($cropinfo['harvest'] / $cropinfo['acres']) * 3;
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

                    $db->Execute("UPDATE $dbtables[products] "
                                ."SET amount = amount + $cotton_harvest "
                                ."WHERE tribeid = '$tribe[goods_tribe]' "
                                ."AND long_name = 'cotton'");
                    $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres - $acres_harvested "
                                ."WHERE cropid = '$cropinfo[cropid]' "
                                ."AND clanid = '$tribe[clanid]' "
                                ."AND hex_id = '$tribe[hex_id]'");
                    $db->Execute("INSERT INTO $dbtables[logs] "
                                ."VALUES("
                                ."'',"
                                ."'$month[count]',"
                                ."'$year[count]',"
                                ."'$tribe[clanid]',"
                                ."'$tribe[tribeid]',"
                                ."'FARMING',"
                                ."'$stamp',"
                                ."'Farming: We harvested $cotton_harvest $act_do[crop] from $acres_harvested acres of farmland.')");
                   $db->Execute("DELETE FROM $dbtables[farm_activities] "
                               ."WHERE tribeid = '$tribe[tribeid]' "
                               ."AND action = '$act_do[action]' "
                               ."AND crop = '$act_do[crop]'");
                }

                if( $cropinfo['crop'] == 'hemp' )
                {
                    $harvest = ($cropinfo['harvest'] / $cropinfo['acres']);
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

                    $db->Execute("UPDATE $dbtables[products] "
                                ."SET amount = amount + $cotton_harvest "
                                ."WHERE tribeid = '$tribe[goods_tribe]' "
                                ."AND long_name = 'cotton'");
                    $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres - $acres_harvested "
                                ."WHERE cropid = '$cropinfo[cropid]' "
                                ."AND clanid = '$tribe[clanid]' "
                                ."AND hex_id = '$tribe[hex_id]'");
                    $db->Execute("INSERT INTO $dbtables[logs] "
                                ."VALUES("
                                ."'',"
                                ."'$month[count]',"
                                ."'$year[count]',"
                                ."'$tribe[clanid]',"
                                ."'$tribe[tribeid]',"
                                ."'FARMING',"
                                ."'$stamp',"
                                ."'Farming: We harvested $cotton_harvest $act_do[crop] from $acres_harvested acres of farmland.')");
                   $db->Execute("DELETE FROM $dbtables[farm_activities] "
                               ."WHERE tribeid = '$tribe[tribeid]' "
                               ."AND action = '$act_do[action]' "
                               ."AND crop = '$act_do[crop]'");
                }

                if( $cropinfo['crop'] == 'potatoes' )
                {
                    $harvest = ($cropinfo['harvest'] / $cropinfo['acres']);
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

                    $db->Execute("UPDATE $dbtables[products] "
                                ."SET amount = amount + $cotton_harvest "
                                ."WHERE tribeid = '$tribe[goods_tribe]' "
                                ."AND long_name = 'provs'");
                    $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres - $acres_harvested "
                                ."WHERE cropid = '$cropinfo[cropid]' "
                                ."AND clanid = '$tribe[clanid]' "
                                ."AND hex_id = '$tribe[hex_id]'");
                    $db->Execute("INSERT INTO $dbtables[logs] "
                                ."VALUES("
                                ."'',"
                                ."'$month[count]',"
                                ."'$year[count]',"
                                ."'$tribe[clanid]',"
                                ."'$tribe[tribeid]',"
                                ."'FARMING',"
                                ."'$stamp',"
                                ."'Farming: We harvested $cotton_harvest $act_do[crop] from $acres_harvested acres of farmland.')");
                   $db->Execute("DELETE FROM $dbtables[farm_activities] "
                               ."WHERE tribeid = '$tribe[tribeid]' "
                               ."AND action = '$act_do[action]' "
                               ."AND crop = '$act_do[crop]'");
                }

                if( $cropinfo['crop'] == 'corn' )
                {
                    $harvest = ($cropinfo['harvest'] / $cropinfo['acres']) * 3;
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

                    $db->Execute("UPDATE $dbtables[products] "
                                ."SET amount = amount + $cotton_harvest "
                                ."WHERE tribeid = '$tribe[goods_tribe]' "
                                ."AND long_name = 'provs'");
                    $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres - $acres_harvested "
                                ."WHERE cropid = '$cropinfo[cropid]' "
                                ."AND clanid = '$tribe[clanid]' "
                                ."AND hex_id = '$tribe[hex_id]'");
                    $db->Execute("INSERT INTO $dbtables[logs] "
                                ."VALUES("
                                ."'',"
                                ."'$month[count]',"
                                ."'$year[count]',"
                                ."'$tribe[clanid]',"
                                ."'$tribe[tribeid]',"
                                ."'FARMING',"
                                ."'$stamp',"
                                ."'Farming: We harvested $cotton_harvest $act_do[crop] from $acres_harvested acres of farmland.')");
                   $db->Execute("DELETE FROM $dbtables[farm_activities] "
                               ."WHERE tribeid = '$tribe[tribeid]' "
                               ."AND action = '$act_do[action]' "
                               ."AND crop = '$act_do[crop]'");
                }

                if( $cropinfo['crop'] == 'herbs' )
                {
                    $harvest = ($cropinfo['harvest'] / $cropinfo['acres']);
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

                    $db->Execute("UPDATE $dbtables[products] "
                                ."SET amount = amount + $cotton_harvest "
                                ."WHERE tribeid = '$tribe[goods_tribe]' "
                                ."AND long_name = 'herbs'");
                    $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres - $acres_harvested "
                                ."WHERE cropid = '$cropinfo[cropid]' "
                                ."AND clanid = '$tribe[clanid]' "
                                ."AND hex_id = '$tribe[hex_id]'");
                    $db->Execute("INSERT INTO $dbtables[logs] "
                                ."VALUES("
                                ."'',"
                                ."'$month[count]',"
                                ."'$year[count]',"
                                ."'$tribe[clanid]',"
                                ."'$tribe[tribeid]',"
                                ."'FARMING',"
                                ."'$stamp',"
                                ."'Farming: We harvested $cotton_harvest $act_do[crop] from $acres_harvested acres of farmland.')");
                   $db->Execute("DELETE FROM $dbtables[farm_activities] "
                               ."WHERE tribeid = '$tribe[tribeid]' "
                               ."AND action = '$act_do[action]' "
                               ."AND crop = '$act_do[crop]'");
                }
                if( $cropinfo['crop'] == 'spice' )
                {
                    $harvest = ($cropinfo['harvest'] / $cropinfo['acres']);
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

                    $db->Execute("UPDATE $dbtables[products] "
                                ."SET amount = amount + $cotton_harvest "
                                ."WHERE tribeid = '$tribe[goods_tribe]' "
                                ."AND long_name = 'spice'");
                    $db->Execute("UPDATE $dbtables[farming] "
                                ."SET acres = acres - $acres_harvested "
                                ."WHERE cropid = '$cropinfo[cropid]' "
                                ."AND clanid = '$tribe[clanid]' "
                                ."AND hex_id = '$tribe[hex_id]'");
                    $db->Execute("INSERT INTO $dbtables[logs] "
                                ."VALUES("
                                ."'',"
                                ."'$month[count]',"
                                ."'$year[count]',"
                                ."'$tribe[clanid]',"
                                ."'$tribe[tribeid]',"
                                ."'FARMING',"
                                ."'$stamp',"
                                ."'Farming: We harvested $cotton_harvest $act_do[crop] from $acres_harvested acres of farmland.')");
                   $db->Execute("DELETE FROM $dbtables[farm_activities] "
                               ."WHERE tribeid = '$tribe[tribeid]' "
                               ."AND action = '$act_do[action]' "
                               ."AND crop = '$act_do[crop]'");
                }
            }
            else
            {
                $db->Execute("INSERT INTO $dbtables[logs] "
                            ."VALUES("
                            ."'',"
                            ."'$month[count]',"
                            ."'$year[count]',"
                            ."'$tribe[clanid]',"
                            ."'$tribe[tribeid]',"
                            ."'FARMING',"
                            ."'$stamp',"
                            ."'Farming: We could not find any $act_do[crop] to harvest.')");
            }
            $db->Execute("DELETE FROM $dbtables[farm_activities] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND action = '$act_do[action]'");
        }

    $act->MoveNext();
    }
    $db->Execute("DELETE FROM $dbtables[farm_activities] "
                ."WHERE tribeid = '$tribe[tribeid]'");
    $res->MoveNext();
}
$db->Execute("DELETE FROM $dbtables[farming] "
            ."WHERE acres < 1");
$time_end = getmicrotime();
$time = $time_end - $time_start;
$file = __FILE__;
$file = explode('/', $file);
$file = $file[6];
$db->Execute("INSERT INTO $dbtables[logs] "
            ."VALUES("
            ."'',"
            ."'$month[count]',"
            ."'$year[count]',"
            ."'0000',"
            ."'0000.00',"
            ."'BENCHMARK',"
            ."'$stamp',"
            ."'$file completed in $time seconds.')");

?>
