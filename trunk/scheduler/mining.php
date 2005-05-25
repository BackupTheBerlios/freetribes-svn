<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: mining.php
$pos = (strpos($_SERVER['PHP_SELF'], "/mining.php"));
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
                       ."WHERE tribeid = '$tribe[tribeid]' "
                       ."AND skill_abbr = 'min' "
                       ."LIMIT 1");
        db_op_result($act,__LINE__,__FILE__);
    $act_do = $act->fields;
    if( $act_do['skill_abbr'] == 'min' )
    {
        $start_miners = $act_do['actives'];
        $skill = $db->Execute("SELECT * FROM $dbtables[skills] "
                             ."WHERE tribeid = '$tribe[tribeid]' "
                             ."AND abbr = 'min'");
            db_op_result($skill,__LINE__,__FILE__);
        $skillinfo = $skill->fields;
        $hex = $db->Execute("SELECT * FROM $dbtables[hexes] "
                           ."WHERE hex_id = '$tribe[hex_id]'");
          db_op_result($hex,__LINE__,__FILE__);
        $hexinfo = $hex->fields;

        if( $hexinfo['resource'] == 'Y' )
        {
            $shovinfo['amount'] = 0;
            $shovinfo['long_name'] = '';
            $pickinfo['amount'] = 0;
            $pickinfo['long_name'] = '';
            $mattockinfo['amount'] = 0;
            $mattockinfo['long_name'] = '';
            $shov = $db->Execute("SELECT amount,long_name FROM $dbtables[products] "
                                ."WHERE tribeid = '$tribe[goods_tribe]' "
                                ."AND amount > 0 "
                                ."AND long_name = 'shovel'");
             db_op_result($shov,__LINE__,__FILE__);
             if( !$shov->EOF )
            {
                $shovinfo = $shov->fields;
            }

            $pick = $db->Execute("SELECT amount FROM $dbtables[products] "
                                ."WHERE tribeid = '$tribe[goods_tribe]' "
                                ."AND amount > 0 "
                                ."AND long_name = 'picks'");
             db_op_result($pick,__LINE__,__FILE__);
             if( !$pick->EOF )
            {
                $pickinfo = $pick->fields;
            }

            $mattock = $db->Execute("SELECT amount FROM $dbtables[products] "
                                   ."WHERE tribeid = '$tribe[goods_tribe]' "
                                   ."AND amount > 0 "
                                   ."AND long_name = 'mattock'");
                 db_op_result($mattock,__LINE__,__FILE__);
            if( !$mattock->EOF )
            {
                $mattockinfo = $mattock->fields;
            }


            if( $shovinfo['amount'] > $act_do['actives'] )
            {
                $shovinfo['amount'] = $act_do['actives'];
            }
            if( !$shovinfo['long_name'] == '' && $shovinfo['amount'] > 1 )
            {
                $query = $db->Execute("INSERT INTO $dbtables[products_used] "
                            ."VALUES("
                            ."'$tribe[goods_tribe]',"
                            ."'$shovinfo[amount]',"
                            ."'$shovinfo[long_name]')");
                  db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("UPDATE $dbtables[products] "
                            ."SET amount = amount - $shovinfo[amount] "
                            ."WHERE tribeid = '$tribe[goods_tribe]' "
                            ."AND long_name = '$shovinfo[long_name]'");
                  db_op_result($query,__LINE__,__FILE__);
            }

            if( $pickinfo['amount'] > ( $act_do['actives'] - $shovinfo['amount'] ) )
            {
                $pickinfo['amount'] = $act_do['actives'] - $shovinfo['amount'];
            }
            if( !$pickinfo['long_name'] == '' && $pickinfo['amount'] > 1 )
            {
                $query = $db->Execute("INSERT INTO $dbtables[products_used] "
                            ."VALUES("
                            ."'$tribe[goods_tribe]',"
                            ."'$pickinfo[amount]',"
                            ."'$pickinfo[long_name]')");
                 db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("UPDATE $dbtables[products] "
                            ."SET amount = amount - $pickinfo[amount] "
                            ."WHERE tribeid = '$tribe[goods_tribe]' "
                            ."AND long_name = '$pickinfo[long_name]'");
                  db_op_result($query,__LINE__,__FILE__);
            }

            if( $mattockinfo['amount'] > ( $act_do['actives'] - $shovinfo['amount'] - $pickinfo['amount'] ) )
            {
                $mattockinfo['amount'] = $act_do['actives'] - $shovinfo['amount'] - $pickinfo['amount'];
            }
            if( !$mattockinfo['long_name'] == '' && $mattockinfo['amount'] > 1 )
            {
                $query = $db->Execute("INSERT INTO $dbtables[products_used] "
                            ."VALUES("
                            ."'$tribe[goods_tribe]',"
                            ."'$mattockinfo[amount]',"
                            ."'$mattockinfo[long_name]')");
                 db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("UPDATE $dbtables[products] "
                            ."SET amount = amount - $mattockinfo[amount] "
                            ."WHERE tribeid = '$tribe[goods_tribe]' "
                            ."AND long_name = '$mattockinfo[long_name]'");
                    db_op_result($query,__LINE__,__FILE__);
            }

            if( $skillinfo['level'] > 0 )
            {
                $skill_bonus_prune = $skillinfo['level'] - 1;
                $skill_bonus = ($skillinfo['level'] * 1.1);
                $skill_bonus = $skill_bonus - $skill_bonus_prune;
            }
            $shov_bonus = round($shovinfo['amount'] * 1.4);
            $pick_bonus = round($pickinfo['amount'] * 1.5);
            $mattock_bonus = round($mattockinfo['amount'] * 1.3);

            $rand_accident = rand(1,1000);
            $season = $db->Execute("SELECT count FROM $dbtables[game_date] "
                                  ."WHERE type = 'season'");
                db_op_result($season,__LINE__,__FILE__);
            $season_modifyer = $season->fields;
            $weather = $db->Execute("SELECT * FROM $dbtables[game_date] "
                                   ."WHERE type = 'weather'");
              db_op_result($weather,__LINE__,__FILE__);
            $weatherinfo = $weather->fields;
            $safe_mine = $skillinfo['level'] + $rand_accident;
            $res_limit = false;

            if( $safe_mine < ( ( $weatherinfo['count'] + $season_modifyer['count'] ) / 4 ) )
            {
                if( $skillinfo['level'] < 10 )
                {
                    $lost = ( 10 - $skillinfo['level'] ) * .01;
                    $potential_miners_lost = round( $act_do['actives'] * $lost);
                    $miners_lost = rand( 1, $potential_miners_lost );
                }
                else
                {
                    $potential_miners_lost = round( $act_do['actives']  * .015 );
                    $miners_lost = rand( 1, $potential_miners_lost );
                }
                if( $miners_lost < 1 )
                {
                    $miners_lost = 1;
                }
                $act_do['actives'] = $act_do['actives'] - $miners_lost;
                if( $miners_lost > 0 )
                {
                    $query = $db->Execute("UPDATE $dbtables[tribes] "
                                ."SET actives = actives - $miners_lost, "
                                ."totalpop = totalpop - $miners_lost, "
                                ."maxam = maxam - $miners_lost, "
                                ."curam = curam - $miners_lost, "
                                ."morale = morale - .002 "
                                ."WHERE tribeid = $tribe[tribeid]");
                     db_op_result($query,__LINE__,__FILE__);
                    $query = $db->Execute("INSERT INTO $dbtables[logs] "
                                ."VALUES("
                                ."'',"
                                ."'$month[count]',"
                                ."'$year[count]',"
                                ."'$tribe[clanid]',"
                                ."'$tribe[tribeid]',"
                                ."'MINING',"
                                ."'$stamp',"
                                ."'Mining: We lost $miners_lost actives in mining accidents this month.')");
                     db_op_result($query,__LINE__,__FILE__);
                    $query = $db->Execute("INSERT INTO $dbtables[logs] "
                                ."VALUES("
                                ."'',"
                                ."'$month[count]',"
                                ."'$year[count]',"
                                ."'0000',"
                                ."'0000.00',"
                                ."'MINING',"
                                ."'$stamp',"
                                ."'Mining: $tribe[tribeid] Skill ($skillinfo[level]) lost "
                                ."$miners_lost actives in mining accidents this month.')");
                       db_op_result($query,__LINE__,__FILE__);
                }
            }

            $mining_actives = ($act_do['actives'] + $shov_bonus + $pick_bonus + $mattock_bonus) * $skill_bonus;
            $mining_ability = round($mining_actives / $season_modifyer['count']);

            if( $skillinfo['level'] >= 1 )
            {
                $mining_ability = round( $mining_ability * $skillinfo['level'] );
            }
            else
            {
                $minint_ability = round( $mining_ability * .5 );
            }

            if( $hexinfo['res_type'] == 'coal' )
            {
                $res_type = "Coal";
            }
            elseif( $hexinfo['res_type'] == 'salt' )
            {
                $res_type = "Salt";
                $mining_ability = round( $mining_ability * .80 );
            }
            elseif( $hexinfo['res_type'] == 'tin' )
            {
                $res_type = "Tin Ore";
                $mining_ability = round( $mining_ability * .65 );
            }
            elseif( $hexinfo['res_type'] == 'copper' )
            {
                $res_type = "Copper Ore";
                $mining_ability = round( $mining_ability * .55 );
            }
            elseif( $hexinfo['res_type'] == 'gold' )
            {
                $res_type = "Gold Ore";
                $res_limit = true;
                $mining_ability = round( $mining_ability * .15 );
            }
            elseif( $hexinfo['res_type'] == 'zinc' )
            {
                $res_type = "Zinc Ore";
                $mining_ability = round( $mining_ability * .70 );
            }
            elseif( $hexinfo['res_type'] == 'iron' )
            {
                $res_type = "Iron Ore";
                $mining_ability = round( $mining_ability * .50 );
            }
            elseif( $hexinfo['res_type'] == 'silver' )
            {
                $res_type = "Silver Ore";
                $res_limit = true;
                $mining_ability = round( $mining_ability * .25 );
            }
            elseif( $hexinfo['res_type'] == 'lead' )
            {
                $res_type = "Lead Ore";
                $mining_ability = round( $mining_ability * .40 );
            }
            elseif( $hexinfo['res_type'] == 'gems' )
            {
                $res_type = "Raw Gems";
                $res_limit = true;
                $mining_ability = round( $mining_ability * .05 );
            }
            if( $res_limit )
            {
                if( $mining_ability > $hexinfo['res_amount'] )
                {
                    $mining_ability = $hexinfo['res_amount'];
                }
                if( $mining_ability < 0 )
                {
                    $mining_ability = 0;
                }
                $query = $db->Execute("UPDATE $dbtables[hexes] "
                            ."SET res_amount = res_amount - '$mining_ability' "
                            ."WHERE hex_id = '$hexinfo[hex_id]'");
                  db_op_result($query,__LINE__,__FILE__);
            }
            if( $mining_ability < 0 )
            {
                $mining_ability = 0;
            }
            $query = $db->Execute("UPDATE $dbtables[resources] "
                        ."SET amount = amount + '$mining_ability' "
                        ."WHERE long_name = '$res_type' "
                        ."AND tribeid = '$tribe[goods_tribe]'");
            db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("DELETE FROM $dbtables[activities] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND product = 'ore'");
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
                        ."'Mining: $mining_ability $res_type "
                        ."mined with $start_miners miners.')");
            db_op_result($query,__LINE__,__FILE__);
        }
        else
        {
            $query = $db->Execute("DELETE FROM $dbtables[activities] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND product = 'ore'");
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
                        ."'Mining: There are no resources to mine in this area.')");
             db_op_result($query,__LINE__,__FILE__);
        }

    }

    $res->MoveNext();
}

?>
