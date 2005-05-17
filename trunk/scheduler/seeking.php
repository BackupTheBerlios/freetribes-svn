<?php
$pos = (strpos($_SERVER['PHP_SELF'], "/seeking.php"));
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
    $act = $db->Execute("SELECT * FROM $dbtables[seeking] "
                       ."WHERE tribeid = '$tribe[tribeid]'");
        db_op_result($act,__LINE__,__FILE__);
/*
    if( !$act->EOF )
    {
        $query = $db->Execute("DELETE FROM $dbtables[seeking] "
                    ."WHERE tribeid = '$tribe[tribeid]'");
        db_op_result($query,__LINE__,__FILE__);
    }
*/
    $scout = array();
    $seek = array();
    $hex = $db->Execute("SELECT * FROM $dbtables[hexes] "
                       ."WHERE hex_id = '$tribe[hex_id]'");
       db_op_result($hex,__LINE__,__FILE__);
    $hexinfo = $hex->fields;
    $skill = $db->Execute("SELECT * FROM $dbtables[skills] "
                         ."WHERE tribeid = '$tribe[tribeid]' "
                         ."AND abbr = 'sct'");
     db_op_result($scout,__LINE__,__FILE__);
    $scout = $skill->fields;
    $skill = $db->Execute("SELECT * FROM $dbtables[skills] "
                         ."WHERE tribeid = '$tribe[tribeid]' "
                         ."AND abbr = 'seek'");
     db_op_result($skill,__LINE__,__FILE__);
    $seek = $skill->fields;
    $ter = $db->Execute("SELECT $hexinfo[terrain] FROM $dbtables[combat_terrain_mods]");
    $terrain = $ter->fields;
    $terrain_type = $hexinfo['terrain'];
    $terrain_mod = $terrain[$terrain_type];
    $log_text = 'Seeking: We found';
    $log_tracker = 0;

    while( !$act->EOF )
    {
        $act_do = $act->fields;

        if( $act_do['target'] == 'wax' )
        {
            if( $act_do['horses'] > 100 )
            {
                $act_do['horses'] = 100;
            }
            $horse_bonus = $act_do['horses'] * 1.3;
            $backpacks = $act_do['backpacks'] * 1.0;
            $saddlebags = $act_do['saddlebags'] * 1.3;
            $wagons = $act_do['wagons'] * 3;
            $seek_carry = $act_do['actives'] + $horse_bonus + $backpacks + $saddlebags + $wagons;
            $level_bonus = ( 1 + ( $scout['level'] / 3 ) + ( $seek['level'] / 2 )) * 2;
            $items = round(($seek_carry * $level_bonus) / 7 );
            $wax_terrain = round( $items * $terrain_mod);
            $poss_wax = round( $wax_terrain / 10 );
            $log_poss = $poss_wax;
            $wax_found = 0;
            while( $poss_wax > 0 )
            {
                $random = rand( 1, 50);
                if( $random < $level_bonus )
                {
                    $wax_found += rand(1,10);
                    $random = 0;
                }
                $poss_wax -= 1;
            }
            if( $wax_found > 0 )
            {

                $log_text .= " $wax_found wax,";
                $log_tracker++;

                $query = $db->Execute("UPDATE $dbtables[products] "
                            ."SET amount = amount + $wax_found "
                            ."WHERE tribeid = '$tribe[goods_tribe]' "
                            ."AND long_name = '$act_do[target]'");
                 db_op_result($query,__LINE__,__FILE__);
                if( $game_debug )
                {
                    $query = $db->Execute("INSERT INTO $dbtables[logs] "
                                ."VALUES("
                                ."'',"
                                ."'$month[count]',"
                                ."'$year[count]',"
                                ."'0000',"
                                ."'0000.00',"
                                ."'DEBUG',"
                                ."'$stamp',"
                                ."'Seeking (Wax) $tribe[tribeid] found $wax_found wax out of a possible $log_poss.')");
                  db_op_result($query,__LINE__,__FILE__);
                }
            }
        }

        if( $act_do['target'] == 'hives' )
        {
            $horse_bonus = $act_do['horses'] * 1.3;
            $backpacks = $act_do['backpacks'] * 1.0;
            $saddlebags = $act_do['saddlebags'] * 1.3;
            $wagons = $act_do['wagons'] * 3;
            $seek_carry = $act_do['actives'] + $horse_bonus + $backpacks + $saddlebags + $wagons;
            $level_bonus = ( 1 + ( $scout['level'] / 3 ) + ( $seek['level'] / 2 )) * 2;
            $items = round(( $seek_carry * $level_bonus) / 35 );
            $hive_terrain = round( $items * $terrain_mod);
            $poss_hive = round( $hive_terrain / 10 );
            $log_poss = $poss_hive;
            $hive_found = 0;
            while( $poss_hive > 0 )
            {
                $random = rand( 1, 50);
                if( $random < $level_bonus )
                {
                    $hive_found += rand(1,10);
                    $random = 0;
                }
                $poss_hive -= 1;
            }
            if( $hive_found > 0 )
            {

                $log_text .= " $hive_found hives,";
                $log_tracker++;
                $query = $db->Execute("UPDATE $dbtables[products] "
                            ."SET amount = amount + $hive_found "
                            ."WHERE tribeid = '$tribe[goods_tribe]' "
                            ."AND long_name = '$act_do[target]'");
                      db_op_result($query,__LINE__,__FILE__);
                if( $game_debug )
                {
                    $query = $db->Execute("INSERT INTO $dbtables[logs] "
                                ."VALUES("
                                ."'',"
                                ."'$month[count]',"
                                ."'$year[count]',"
                                ."'0000',"
                                ."'0000.00',"
                                ."'DEBUG',"
                                ."'$stamp',"
                                ."'Seeking (Hives) $tribe[tribeid] found $hive_found hives out of a possible $log_poss.')");
                    db_op_result($query,__LINE__,__FILE__);
                }

            }

        }

        if( $act_do['target'] == 'spice' )
        {
            $horse_bonus = $act_do['horses'] * 1.3;
            $backpacks = $act_do['backpacks'] * 1.0;
            $saddlebags = $act_do['saddlebags'] * 1.3;
            $wagons = $act_do['wagons'] * 3;
            $seek_carry = $act_do['actives'] + $horse_bonus + $backpacks + $saddlebags + $wagons;
            $level_bonus = ( 1 + ( $scout['level'] / 3 ) + ( $seek['level'] / 2 )) * 2;
            $items = round(($seek_carry * $level_bonus) / 30 );
            $spice_terrain = round( $items * $terrain_mod);
            $poss_spice = round( $spice_terrain / 10 );
            $log_poss = $poss_spice;
            $spice_found = 0;
            while( $poss_spice > 0 )
            {
                $random = rand( 1, 50);
                if( $random < $level_bonus )
                {
                    $spice_found += rand(1,10);
                    $random = 0;
                }
                $poss_spice -= 1;
            }
            if( $spice_found > 0 )
            {

                $log_text .= " $spice_found spice,";
                $log_tracker++;
                $query = $db->Execute("UPDATE $dbtables[products] "
                            ."SET amount = amount + $spice_found "
                            ."WHERE tribeid = '$tribe[goods_tribe]' "
                            ."AND long_name = '$act_do[target]'");
                 db_op_result($query,__LINE__,__FILE__);
                if( $game_debug )
                {
                    $query = $db->Execute("INSERT INTO $dbtables[logs] "
                                ."VALUES("
                                ."'',"
                                ."'$month[count]',"
                                ."'$year[count]',"
                                ."'0000',"
                                ."'0000.00',"
                                ."'DEBUG',"
                                ."'$stamp',"
                                ."'Seeking (Spice) $tribe[tribeid] found $spice_found spice out of a possible $log_poss.')");
                   db_op_result($query,__LINE__,__FILE__);
                }

            }

        }

        if( $act_do['target'] == 'recruit' )
        {
            $horse_bonus = $act_do['horses'] * 1.3;
            $seek_carry = $act_do['actives'] + $horse_bonus;
            $level_bonus = ( 1 + ( $scout['level'] / 3 ) + ( $seek['level'] / 2 )) * 2;
            $items = round(($seek_carry * $level_bonus) / 22 );
            $recruit_terrain = round( $items * $terrain_mod);
            $poss_recruit = round( $recruit_terrain / 10 );
            $log_poss = $poss_recruit;
            $recruit_found = 0;
            while( $poss_recruit > 0 )
            {
                $random = rand( 1, 50);
                if( $random < $level_bonus )
                {
                    $recruit_found += rand(1,10);
                    $random = 0;
                }
                $poss_recruit -= 1;
            }
            if( $recruit_found > 0 )
            {

                $log_text .= " $recruit_found recruits,";
                $log_tracker++;
                $query = $db->Execute("UPDATE $dbtables[tribes] "
                            ."SET activepop = activepop + $recruit_found "
                            ."WHERE tribeid = '$tribe[tribeid]'");
                 db_op_result($query,__LINE__,__FILE__);
                if( $game_debug )
                {
                    $query = $db->Execute("INSERT INTO $dbtables[logs] "
                                ."VALUES("
                                ."'',"
                                ."'$month[count]',"
                                ."'$year[count]',"
                                ."'0000',"
                                ."'0000.00',"
                                ."'DEBUG',"
                                ."'$stamp',"
                                ."'Seeking (Recruit) $tribe[tribeid] found $recruit_found recruits out of a possible $log_poss.')");
                  db_op_result($query,__LINE__,__FILE__);
                }

            }

        }

        if( $act_do['target'] == 'honey' )
        {
            $horse_bonus = $act_do['horses'] * 1.3;
            $backpacks = $act_do['backpacks'] * 1.0;
            $saddlebags = $act_do['saddlebags'] * 1.3;
            $wagons = $act_do['wagons'] * 3;
            $seek_carry = $act_do['actives'] + $horse_bonus + $backpacks + $saddlebags + $wagons;
            $level_bonus = ( 1 + ( $scout['level'] / 3 ) + ( $seek['level'] / 2 )) * 2;
            $items = round(($seek_carry * $level_bonus) / 3 );
            $honey_terrain = round( $items * $terrain_mod);
            $poss_honey = round( $honey_terrain / 10 );
            $log_poss = $poss_honey;
            $honey_found = 0;
            while( $poss_honey > 0 )
            {
                $random = rand( 1, 50);
                if( $random < $level_bonus )
                {
                    $honey_found += rand(1,10);
                    $random = 0;
                }
                $poss_honey -= 1;
            }
            if( $honey_found > 0 )
            {

                $log_text .= " $honey_found honey,";
                $log_tracker++;
                $query = $db->Execute("UPDATE $dbtables[products] "
                            ."SET amount = amount + $honey_found "
                            ."WHERE tribeid = '$tribe[goods_tribe]' "
                            ."AND long_name = '$act_do[target]'");
                 db_op_result($query,__LINE__,__FILE__);
                if( $game_debug )
                {
                    $query = $db->Execute("INSERT INTO $dbtables[logs] "
                                ."VALUES("
                                ."'',"
                                ."'$month[count]',"
                                ."'$year[count]',"
                                ."'0000',"
                                ."'0000.00',"
                                ."'DEBUG',"
                                ."'$stamp',"
                                ."'Seeking (Honey) $tribe[tribeid] found $honey_found honey out of a possible $log_poss.')");
                   db_op_result($query,__LINE__,__FILE__);
                }

            }

        }

        if( $act_do['target'] == 'herbs' )
        {
            $horse_bonus = $act_do['horses'] * 1.3;
            $backpacks = $act_do['backpacks'] * 1.0;
            $saddlebags = $act_do['saddlebags'] * 1.3;
            $wagons = $act_do['wagons'] * 3;
            $seek_carry = $act_do['actives'] + $horse_bonus + $backpacks + $saddlebags + $wagons;
            $level_bonus = ( 1 + ( $scout['level'] / 3 ) + ( $seek['level'] / 2 )) * 2;
            $items = round(($seek_carry * $level_bonus) / 1.5 );
            $herbs_terrain = round( $items * $terrain_mod);
            $poss_herbs = round( $herbs_terrain / 10 );
            $log_poss = $poss_herbs;
            $herbs_found = 0;
            while( $poss_herbs > 0 )
            {
                $random = rand( 1, 50);
                if( $random < $level_bonus )
                {
                    $herbs_found += rand(1,10);
                    $random = 0;
                }
                $poss_herbs -= 1;
            }
            if( $herbs_found > 0 )
            {

                $log_text .= " $herbs_found herbs,";
                $log_tracker++;
                $query = $db->Execute("UPDATE $dbtables[products] "
                            ."SET amount = amount + $herbs_found "
                            ."WHERE tribeid = '$tribe[goods_tribe]' "
                            ."AND long_name = '$act_do[target]'");
                    db_op_result($query,__LINE__,__FILE__);
                if( $game_debug )
                {
                    $query = $db->Execute("INSERT INTO $dbtables[logs] "
                                ."VALUES("
                                ."'',"
                                ."'$month[count]',"
                                ."'$year[count]',"
                                ."'0000',"
                                ."'0000.00',"
                                ."'DEBUG',"
                                ."'$stamp',"
                                ."'Seeking (Herbs) $tribe[tribeid] found $herbs_found herbs out of a possible $log_poss.')");
                   db_op_result($query,__LINE__,__FILE__);
                }

            }

        }

        if( $act_do['target'] == 'Goats' )
        {
            $horse_bonus = $act_do['horses'] * 1.3;
            $seek_carry = $act_do['actives'] + $horse_bonus;
            $level_bonus = ( 1 + ( $scout['level'] / 3 ) + ( $seek['level'] / 2 )) * 2;
            $items = round(($seek_carry * $level_bonus) / 2.5 );
            $goats_terrain = round( $items * $terrain_mod);
            $poss_goats = round( $goats_terrain / 10 );
            $log_poss = $poss_goats;
            $goats_found = 0;
            while( $poss_goats > 0 )
            {
                $random = rand( 1, 50);
                if( $random < $level_bonus )
                {
                    $goats_found += rand(1,10);
                    $random = 0;
                }
                $poss_goats -= 1;
            }
            if( $goats_found > 0 )
            {

                $log_text .= " $goats_found goats,";
                $log_tracker++;
                $query = $db->Execute("UPDATE $dbtables[livestock] "
                            ."SET amount = amount + $goats_found "
                            ."WHERE tribeid = '$tribe[goods_tribe]' "
                            ."AND type = '$act_do[target]'");
                   db_op_result($query,__LINE__,__FILE__);
                if( $game_debug )
                {
                    $query = $db->Execute("INSERT INTO $dbtables[logs] "
                                ."VALUES("
                                ."'',"
                                ."'$month[count]',"
                                ."'$year[count]',"
                                ."'0000',"
                                ."'0000.00',"
                                ."'DEBUG',"
                                ."'$stamp',"
                                ."'Seeking (Goats) $tribe[tribeid] found $goats_found goats out of a possible $log_poss.')");
                      db_op_result($query,__LINE__,__FILE__);
                }

            }

        }

        if( $act_do['target'] == 'Cattle' )
        {
            $horse_bonus = $act_do['horses'] * 1.3;
            $seek_carry = $act_do['actives'] + $horse_bonus;
            $level_bonus = ( 1 + ( $scout['level'] / 3 ) + ( $seek['level'] / 2 )) * 2;
            $items = round(($seek_carry * $level_bonus) / 15 );
            $cattle_terrain = round( $items * $terrain_mod);
            $poss_cattle = round( $cattle_terrain / 10 );
            $log_poss = $poss_cattle;
            $cattle_found = 0;
            while( $poss_cattle > 0 )
            {
                $random = rand( 1, 50);
                if( $random < $level_bonus )
                {
                    $cattle_found += rand(1,10);
                    $random = 0;
                }
                $poss_cattle -= 1;
            }
            if( $cattle_found > 0 )
            {

                $log_text .= " $cattle_found cattle,";
                $log_tracker++;
                $query = $db->Execute("UPDATE $dbtables[livestock] "
                            ."SET amount = amount + $cattle_found "
                            ."WHERE tribeid = '$tribe[goods_tribe]' "
                            ."AND type = '$act_do[target]'");
                  db_op_result($query,__LINE__,__FILE__);
                if( $game_debug )
                {
                    $query = $db->Execute("INSERT INTO $dbtables[logs] "
                                ."VALUES("
                                ."'',"
                                ."'$month[count]',"
                                ."'$year[count]',"
                                ."'0000',"
                                ."'0000.00',"
                                ."'DEBUG',"
                                ."'$stamp',"
                                ."'Seeking (Cattle) $tribe[tribeid] found $cattle_found cattle out of a possible $log_poss.')");
                  db_op_result($query,__LINE__,__FILE__);
                }

            }


        }

        if( $act_do['target'] == 'Elephants' )
        {
            $horse_bonus = $act_do['horses'] * 1.3;
            $seek_carry = $act_do['actives'] + $horse_bonus;
            $level_bonus = ( 1 + ( $scout['level'] / 3 ) + ( $seek['level'] / 2 )) * 2;
            $items = round(($seek_carry * $level_bonus) / 37 );
            $elephant_terrain = round( $items * $terrain_mod);
            $poss_elephant = round( $elephant_terrain / 10 );
            $log_poss = $poss_elephant;
            $elephant_found = 0;
            while( $poss_elephant > 0 )
            {
                $random = rand( 1, 50);
                if( $random < $level_bonus )
                {
                    $elephant_found += rand(1,10);
                    $random = 0;
                }
                $poss_elephant -= 1;
            }
            if( $elephant_found > 0 )
            {

                $log_text .= " $elephant_found elephants,";
                $log_tracker++;
                $query = $db->Execute("UPDATE $dbtables[livestock] "
                            ."SET amount = amount + $elephant_found "
                            ."WHERE tribeid = '$tribe[goods_tribe]' "
                            ."AND type = '$act_do[target]'");
                  db_op_result($query,__LINE__,__FILE__);
                if( $game_debug )
                {
                    $query = $db->Execute("INSERT INTO $dbtables[logs] "
                                ."VALUES("
                                ."'',"
                                ."'$month[count]',"
                                ."'$year[count]',"
                                ."'0000',"
                                ."'0000.00',"
                                ."'DEBUG',"
                                ."'$stamp',"
                                ."'Seeking (Elephants) $tribe[tribeid] found $elephant_found elephants out of a possible $log_poss.')");
                 db_op_result($query,__LINE__,__FILE__);
                }

            }


        }

        if( $act_do['target'] == 'Horses' )
        {
            $horse_bonus = $act_do['horses'] * 1.3;
            $seek_carry = $act_do['actives'] + $horse_bonus;
            $level_bonus = ( 1 + ( $scout['level'] / 3 ) + ( $seek['level'] / 2 )) * 2;
            $items = round(($seek_carry * $level_bonus) / 30 );
            $horse_terrain = round( $items * $terrain_mod);
            $poss_horse = round( $horse_terrain / 10 );
            $log_poss = $poss_horse;
            $horse_found = 0;
            while( $poss_horse > 0 )
            {
                $random = rand( 1, 50);
                if( $random < $level_bonus )
                {
                    $horse_found += rand(1,10);
                    $random = 0;
                }
                $poss_horse -= 1;
            }
            if( $horse_found > 0 )
            {

                $log_text .= " $horse_found horses,";
                $log_tracker++;
                $query = $db->Execute("UPDATE $dbtables[livestock] "
                            ."SET amount = amount + $horse_found "
                            ."WHERE tribeid = '$tribe[goods_tribe]' "
                            ."AND type = '$act_do[target]'");
                db_op_result($query,__LINE__,__FILE__);
                if( $game_debug )
                {
                    $query = $db->Execute("INSERT INTO $dbtables[logs] "
                                ."VALUES("
                                ."'',"
                                ."'$month[count]',"
                                ."'$year[count]',"
                                ."'0000',"
                                ."'0000.00',"
                                ."'DEBUG',"
                                ."'$stamp',"
                                ."'Seeking (Horses) $tribe[tribeid] found $horse_found horses out of a possible $log_poss.')");
                     db_op_result($query,__LINE__,__FILE__);
                }

            }


        }

        if( $act_do['target'] == 'Dogs' )
        {
            $horse_bonus = $act_do['horses'] * 1.3;
            $seek_carry = $act_do['actives'] + $horse_bonus;
            $level_bonus = ( 1 + ( $scout['level'] / 3 ) + ( $seek['level'] / 2 )) * 2;
            $items = round(($seek_carry * $level_bonus) / 47 );
            $dog_terrain = round( $items * $terrain_mod);
            $poss_dog = round( $dog_terrain / 10 );
            $log_poss = $poss_dog;
            $dog_found = 0;
            while( $poss_dog > 0 )
            {
                $random = rand( 1, 50);
                if( $random < $level_bonus )
                {
                    $dog_found += rand(1,10);
                    $random = 0;
                }
                $poss_dog -= 1;
            }
            if( $dog_found > 0 )
            {

                $log_text .= " $dog_found dogs,";
                $log_tracker++;

                $query = $db->Execute("UPDATE $dbtables[livestock] "
                            ."SET amount = amount + $dog_found "
                            ."WHERE tribeid = '$tribe[goods_tribe]' "
                            ."AND type = '$act_do[target]'");
                db_op_result($query,__LINE__,__FILE__);
                if( $game_debug )
                {
                    $query = $db->Execute("INSERT INTO $dbtables[logs] "
                                ."VALUES("
                                ."'',"
                                ."'$month[count]',"
                                ."'$year[count]',"
                                ."'0000',"
                                ."'0000.00',"
                                ."'DEBUG',"
                                ."'$stamp',"
                                ."'Seeking (Dogs) $tribe[tribeid] found $dog_found dogs out of a possible $log_poss.')");
                db_op_result($query,__LINE__,__FILE__);
                }

            }


        }
        $query = $db->Execute("UPDATE $dbtables[livestock] "
                    ."SET amount = amount + '$act_do[horses]' "
                    ."WHERE tribeid = '$tribe[goods_tribe]' "
                    ."AND type = 'Horses'");
        db_op_result($query,__LINE__,__FILE__);
        $query = $db->Execute("UPDATE $dbtables[livestock] "
                    ."SET amount = amount + '$act_do[burden_beasts]' "
                    ."WHERE tribeid = '$tribe[goods_tribe]' "
                    ."AND type = 'Cattle'");
        db_op_result($query,__LINE__,__FILE__);
        $query = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount + '$act_do[backpacks]' "
                    ."WHERE tribeid = '$tribe[goods_tribe]' "
                    ."AND long_name = 'backpack'");
       db_op_result($query,__LINE__,__FILE__);
        $query = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount + '$act_do[saddlebags]' "
                    ."WHERE tribeid = '$tribe[goods_tribe]' "
                    ."AND long_name = 'saddlebags'");
       db_op_result($query,__LINE__,__FILE__);
        $query = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount + '$act_do[wagons]' "
                    ."WHERE tribeid = '$tribe[goods_tribe]' "
                    ."AND long_name = 'wagon'");
           db_op_result($query,__LINE__,__FILE__);
        $act->MoveNext();
        if( $act->EOF && $log_tracker > 0 )
        {
            $query = $db->Execute("INSERT INTO $dbtables[logs] "
                        ."VALUES("
                        ."'',"
                        ."'$month[count]',"
                        ."'$year[count]',"
                        ."'$tribe[clanid]',"
                        ."'$tribe[tribeid]',"
                        ."'SEEK',"
                        ."'$stamp',"
                        ."'$log_text')");
             db_op_result($query,__LINE__,__FILE__);
        }
        elseif( $act->EOF && $log_tracker == 0 )
        {
            $query = $db->Execute("INSERT INTO $dbtables[logs] "
                        ."VALUES("
                        ."'',"
                        ."'$month[count]',"
                        ."'$year[count]',"
                        ."'$tribe[clanid]',"
                        ."'$tribe[tribeid]',"
                        ."'SEEK',"
                        ."'$stamp',"
                        ."'Seeking: Nothing Found.')");
             db_op_result($query,__LINE__,__FILE__);
        }

    }
    $query = $db->Execute("DELETE FROM $dbtables[seeking] "
                ."WHERE tribeid = '$tribe[tribeid]'");
       db_op_result($query,__LINE__,__FILE__);

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
