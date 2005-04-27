<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: population.php

require_once("../config.php");
$time_start = getmicrotime();
include("game_time.php");
connectdb();
$loginfogoat = 0;
$loginfohorses = 0;
$loginfocattle = 0;
$loginfoelephant = 0;

$res = $db->Execute("SELECT * FROM $dbtables[tribes]");
 db_op_result($res,__LINE__,__FILE__);
while( !$res->EOF )
{
    $tribe = array();
    $tribe = $res->fields;
    $war = $db->Execute("SELECT * FROM $dbtables[garrisons] "
                       ."WHERE tribeid = '$tribe[tribeid]'");
      db_op_result($war,__LINE__,__FILE__);
    $totalwar = 0;
    while( !$war->EOF )
    {
        $warinfo = $war->fields;
        $totalwar += $warinfo[force];
        $war->MoveNext();
    }

    $totalpop = $tribe[activepop] + $tribe[inactivepop] + $totalwar + $tribe[slavepop];

    if( $game_pop_debug )
    {
        $result = $db->Execute("INSERT INTO $dbtables[logs] "
                    ."VALUES("
                    ."'',"
                    ."'$month[count]',"
                    ."'$year[count]',"
                    ."'0000',"
                    ."'0000.00',"
                    ."'POPCHECK',"
                    ."'$stamp',"
                    ."'Population: $tribe[tribeid] starts "
                    ."the process with $totalpop total, "
                    ."$tribe[activepop] actives, $tribe[inactivepop] inactives, $tribe[slavepop] slaves.')");
          db_op_result($result,__LINE__,__FILE__);
    }



    $liv2 = $db->Execute("SELECT * FROM $dbtables[livestock] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND type = 'Horses'");
      db_op_result($liv2,__LINE__,__FILE__);

    $liv3 = $db->Execute("SELECT * FROM $dbtables[livestock] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND type = 'Elephants'");
       db_op_result($liv3,__LINE__,__FILE__);

    $slsk = $db->Execute("SELECT * FROM $dbtables[skills] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND abbr = 'slv'");
      db_op_result($slsk,__LINE__,__FILE__);
    $slaver = $slsk->fields;

    $whip = $db->Execute("SELECT * FROM $dbtables[products] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND long_name = 'whip'");
      db_op_result($whip,__LINE__,__FILE__);
    $whips = $whip->fields;

    if( $whips[amount] > $tribe[slavepop]/10 )
    {
        $whips[amount] = $tribe[slavepop]/10;
    }
    $slaversneeded = 10 + $slaver[level];

    $shack = $db->Execute("SELECT * FROM $dbtables[products] "
                         ."WHERE tribeid = '$tribe[tribeid]' "
                         ."AND long_name = 'shackles'");
          db_op_result($shack,__LINE__,__FILE__);
    $shackles = $shack->fields;
    $slaves = $tribe[slavepop];

    if( $tribe[slavepop] < $shackles[amount] )
    {
        $slaves = $slaves/2;
    }
    $slavers = ceil( $slaves / ( $slaversneeded + $whips[amount] ) );

    $liv2 = $db->Execute("SELECT * FROM $dbtables[livestock] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND type = 'Horses'");
        db_op_result($liv2,__LINE__,__FILE__);
    $mounts2 = $liv2->fields;
    $liv3 = $db->Execute("SELECT * FROM $dbtables[livestock] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND type = 'Elephants'");
        db_op_result($liv3,__LINE__,__FILE__);
    $mounts3 = $liv3->fields;
    $available_mounts = $mounts2[amount] + ($mounts3[amount] * 4);


    if( $totalpop <= $available_mounts )
    {
        $result = $db->Execute("UPDATE $dbtables[tribes] "
                    ."SET move_pts = '27' "
                    ."WHERE tribeid = '$tribe[tribeid]'");
         db_op_result($result,__LINE__,__FILE__);
    }
    else
    {
        $result = $db->Execute("UPDATE $dbtables[tribes] "
                    ."SET move_pts = '18' "
                    ."WHERE tribeid = '$tribe[tribeid]'");
           db_op_result($result,__LINE__,__FILE__);
    }

    $vil = $db->Execute("SELECT * FROM $dbtables[structures] "
                       ."WHERE long_name = 'meetinghouse' "
                       ."AND tribeid = '$tribe[tribeid]' "  /////Tribes with goods tribes should not get the benefit of being a village.
                       ."AND hex_id = '$tribe[hex_id]'");
          db_op_result($vil,__LINE__,__FILE__);

    if( !$vil->EOF )
    {
        $popgrowth = .015 * $tribe[morale];
    }
    else
    {
        $popgrowth = .01 * $tribe[morale];
    }

    if( $tribe[activepop] < $tribe[inactivepop] )
    {
        $tribeactivebred = round($tribe[activepop] * $popgrowth);
        $tribeinactivebred = round($tribe[activepop] * $popgrowth);
        $tribe[activepop] += $tribeactivebred;
        $tribe[inactivepop] += $tribeinactivebred;
    }
    else
    {
        $tribeactivebred = round($tribe[inactivepop] * $popgrowth);
        $tribeinactivebred = round($tribe[inactivepop] * $popgrowth);
        $tribe[activepop] += $tribeactivebred;
        $tribe[inactivepop] += $tribeinactivebred;
    }

    $tribeslavesbred = ceil($tribe[slavepop] * $popgrowth);
    $tribe[slavepop] += ceil($tribe[slavepop] * $popgrowth);
    $tribe[totalpop] = $tribe[activepop] + $tribe[inactivepop] + $tribe[slavepop] + $totalwar;
    $maxam = $tribe[slavepop] + $tribe[activepop] - $slavers;

    $goat_provs = 4;
    $cow_provs = 20;
    $horse_provs = 30;
    $elephant_provs = 60;
    $sheep_provs = 4;
    $pig_prov = 4;
    $food_eaten = round($tribe[warpop] * 2) + round($tribe[activepop] * 1) + round($tribe[inactivepop] * .8) + $tribe[slavepop];

    $food = $db->Execute("SELECT * FROM $dbtables[products] "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'provs'");
       db_op_result($food,__LINE__,__FILE__);
    $foodinfo = $food->fields;

    if( $foodinfo[amount] >= $food_eaten )
    {
        $result = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount - '$food_eaten' "
                    ."WHERE tribeid = '$tribe[goods_tribe]' "
                    ."AND long_name = 'provs'");
         db_op_result($result,__LINE__,__FILE__);
    }
    else
    {
        $goat = $db->Execute("SELECT * FROM $dbtables[livestock] "
                            ."WHERE type = 'Goats' "
                            ."AND tribeid = '$tribe[goods_tribe]'");
          db_op_result($goat,__LINE__,__FILE__);
        $mount = $goat->fields;
        $goat = $mount[amount];
        $foodneeded = $food_eaten;
        $foodneeded -= $foodinfo[amount];
        $foodinfo[amount] = 0;
        $loginfogoat = 0;
        while( $foodneeded > 0 && $goat > 0 )
        {
            $loginfogoat += 1;
            $goat -= 1;
            $foodneeded -= $goat_provs;
        }
        $result = $db->Execute("UPDATE $dbtables[livestock] "
                    ."SET amount = amount - $loginfogoat "
                    ."WHERE type = 'Goats' "
                    ."AND tribeid = '$tribe[goods_tribe]'");
          db_op_result($result,__LINE__,__FILE__);
        $cattle = $db->Execute("SELECT * FROM $dbtables[livestock] "
                              ."WHERE type = 'Cattle' "
                              ."AND tribeid = '$tribe[goods_tribe]'");
          db_op_result($cattle,__LINE__,__FILE__);
        $mount = $cattle->fields;
        $cattle = $mount[amount];
        $loginfocattle = 0;
        while( $foodneeded > 0 & $cattle > 0 )
        {
            $loginfocattle += 1;
            $cattle -= 1;
            $foodneeded -= $cow_provs;
        }
        $result = $db->Execute("UPDATE $dbtables[livestock] "
                    ."SET amount = amount - $loginfocattle "
                    ."WHERE type = 'Cattle' "
                    ."AND tribeid = '$tribe[goods_tribe]'");
         db_op_result($result,__LINE__,__FILE__);
        $sheep = $db->Execute("SELECT * FROM $dbtables[livestock] "
                             ."WHERE type = 'Sheep' "
                             ."AND tribeid = '$tribe[goods_tribe]'");
          db_op_result($sheep,__LINE__,__FILE__);
        $mount = $sheep->fields;
        $sheep = $mount[amount];
        $loginfosheep = 0;
        while( $foodneeded > 0 & $sheep > 0 )
        {
            $loginfosheep += 1;
            $sheep -= 1;
            $foodneeded -= 4;
        }
        $result = $db->Execute("UPDATE $dbtables[livestock] "
                    ."SET amount = amount - $loginfosheep "
                    ."WHERE type = 'Sheep' "
                    ."AND tribeid = '$tribe[goods_tribe]'");
         db_op_result($result,__LINE__,__FILE__);
        $pig = $db->Execute("SELECT * FROM $dbtables[livestock] "
                           ."WHERE type = 'Pigs' "
                           ."AND tribeid = '$tribe[goods_tribe]'");
             db_op_result($pig,__LINE__,__FILE__);
        $mount = $pig->fields;
        $pig = $mount[amount];
        $loginfopig = 0;
        while( $foodneeded > 0 & $pig > 0 )
        {
            $loginfopig += 1;
            $pig -= 1;
            $foodneeded -= 4;
        }
        $result = $db->Execute("UPDATE $dbtables[livestock] "
                    ."SET amount = amount - $loginfopig "
                    ."WHERE type = 'Pigs' "
                    ."AND tribeid = '$tribe[goods_tribe]'");
         db_op_result($result,__LINE__,__FILE__);

        $horses = $db->Execute("SELECT * FROM $dbtables[livestock] "
                              ."WHERE type = 'Horses' "
                              ."AND tribeid = '$tribe[goods_tribe]'");
           db_op_result($horses,__LINE__,__FILE__);
        $mount = $horses->fields;
        $horses = $mount[amount];
        $loginfohorses = 0;
        while( $foodneeded > 0 && $horses > 0 )
        {
            $loginfohorses += 1;
            $horses -= 1;
            $foodneeded -= $horse_provs;
        }
        $result = $db->Execute("UPDATE $dbtables[livestock] "
                    ."SET amount = amount - $loginfohorses "
                    ."WHERE type = 'Horses' "
                    ."AND tribeid = '$tribe[goods_tribe]'");
         db_op_result($result,__LINE__,__FILE__);

        $elephant = $db->Execute("SELECT * FROM $dbtables[livestock] "
                                ."WHERE type = 'Elephants' "
                                ."AND tribeid = '$tribe[goods_tribe]'");
            db_op_result($elephant,__LINE__,__FILE__);
        $mount = $elephant->fields;
        $elephant = $mount[amount];
        $loginfoelephant = 0;
        while( $foodneeded > 0 & $elephant > 0 )
        {
            $loginfoelephant += 1;
            $elephant = $elephant - 1;
            $foodneeded -= $elephant_provs;
        }
        $result = $db->Execute("UPDATE $dbtables[livestock] "
                    ."SET amount = amount - $loginfoelephant "
                    ."WHERE type = 'Elephants' "
                    ."AND tribeid = '$tribe[goods_tribe]'");
           db_op_result($result,__LINE__,__FILE__);

        $foodinfo[amount] = 0;

        if( $foodinfo[amount] < 1 && $foodneeded > 0 && $goat < 1 && $cattle < 1 && $horse < 1 && $elephant < 1 && $pig < 1 && $sheep < 1 )
        {
            $loginfoactives = round( $tribe[activepop] * .05 );
            $loginfoinactives = round( $tribe[inactivepop] * .05 );
            $loginfoslavepop = round( $tribe[slavepop] * .20 );
            $tribe[activepop] -= $loginfoactives;
            $tribe[slavepop] -= $loginfoslavepop;
            $tribe[inactivepop] -= $loginfoinactives;
            $starving = true;
        }
        $totalpop = $tribe[activepop] + $tribe[inactivepop] + $totalwar + $tribe[slavepop];
        $maxam = $tribe[activepop] + $tribe[slavepop] - $slavers;

        $result = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = '0' "
                    ."WHERE tribeid = '$tribe[goods_tribe]' "
                    ."AND long_name = 'provs'");
           db_op_result($result,__LINE__,__FILE__);

        $logtext = "Food shortage: $loginfogoat Goats, "
                  ."$loginfocattle Cattle, $loginfohorses Horses, "
                  ."$loginfoelephant Elephants, "
                  ."$loginfosheep Sheep, $loginfopig Pigs eaten.";

        $logtext2 = "Starvation: $loginfoactives Actives, $loginfoinactives Inactives, "
                   ."$loginfoslavepop Slaves either starved or ran away.";

        if( $loginfogoat > 0 || $loginfocattle > 0 || $loginfohorses > 0 || $loginfoelephant > 0 || $loginfosheep > 0 || $loginfopig > 0 )
        {
            $result = $db->Execute("INSERT INTO $dbtables[logs] "
                        ."VALUES("
                        ."'',"
                        ."'$month[count]',"
                        ."'$year[count]',"
                        ."'$tribe[clanid]',"
                        ."'$tribe[tribeid]',"
                        ."'STARVE',"
                        ."'$stamp',"
                        ."'$logtext')");
              db_op_result($result,__LINE__,__FILE__);
        }
        if( $loginfoactives > 0 || $loginfoinactives > 0 || $loginfoslavepop > 0 )
        {
            $result = $db->Execute("INSERT INTO $dbtables[logs] "
                        ."VALUES("
                        ."'',"
                        ."'$month[count]',"
                        ."'$year[count]',"
                        ."'$tribe[clanid]',"
                        ."'$tribe[tribeid]',"
                        ."'STARVE',"
                        ."'$stamp',"
                        ."'$logtext2')");
             db_op_result($result,__LINE__,__FILE__);
        }
    }
    $totalpop = $tribe[activepop] + $tribe[inactivepop] + $totalwar + $tribe[slavepop];

    if( !ISSET( $starving ) )
    {
        $result = $db->Execute("UPDATE $dbtables[tribes] "
                    ."SET totalpop = '$totalpop', "
                    ."warpop = '$totalwar', "
                    ."activepop = '$tribe[activepop]', "
                    ."inactivepop = '$tribe[inactivepop]', "
                    ."maxam = '$maxam', "
                    ."curam = '$maxam', "
                    ."slavepop = '$tribe[slavepop]' "
                    ."WHERE tribeid = '$tribe[tribeid]'");
          db_op_result($result,__LINE__,__FILE__);
    }
    else
    {
        $actives = $tribe[activepop] - $loginfoactives;
        $inactives = $tribe[inactivepop] - $loginfoinactives;
        $slavepop = $tribe[slavepop] - $loginfoslavepop;
        $totalpop = $totalwar + $actives + $inactives + $slavepop;
        $maxam = $actives + $slavepop;
        $result = $db->Execute("UPDATE $dbtables[tribes] "
                    ."SET totalpop = '$totalpop', "
                    ."warpop = '$totalwar', "
                    ."activepop = '$actives', "
                    ."inactivepop = '$inactives', "
                    ."maxam = '$maxam', "
                    ."curam = '$maxam', "
                    ."slavepop = '$slavepop' "
                    ."morale = morale - .01 "
                    ."WHERE tribeid = '$tribe[tribeid]'");
          db_op_result($result,__LINE__,__FILE__);
    }
    $logtext = 'Pop gains: ';

    if( $tribeactivebred >= 1 )
    {
        $logtext .= "$tribeactivebred Actives";
    }
    if( $tribeinactivebred >= 1 )
    {
        $logtext .= ", $tribeinactivebred Inactives";
    }
    if( $tribeslavesbred >= 1 )
    {
        $logtext .= ", $tribeslavesbred Slaves";
    }
    if( $tribeactivebred < 1 && $tribeinactivebred < 1 && $tribeslavesbred < 1)
    {
        $logtext .= 'none';
    }
    $logtext .= '.';
    $result = $db->Execute("INSERT INTO $dbtables[logs] "
                ."VALUES("
                ."'',"
                ."'$month[count]',"
                ."'$year[count]',"
                ."'$tribe[clanid]',"
                ."'$tribe[tribeid]',"
                ."'UPDATE',"
                ."'$stamp',"
                ."'$logtext')");
      db_op_result($result,__LINE__,__FILE__);
    if( $starving )
    {
        $starvlog = ' after starvation.';
    }
    else
    {
        $starvlog = '.';
    }
    if( $game_pop_debug )
    {
        $result = $db->Execute("INSERT INTO $dbtables[logs] "
                    ."VALUES("
                    ."'',"
                    ."'$month[count]',"
                    ."'$year[count]',"
                    ."'0000',"
                    ."'0000.00',"
                    ."'POPCHECK',"
                    ."'$stamp',"
                    ."'Population: $tribe[tribeid] ends "
                    ."the process with $tribe[totalpop] total, "
                    ."$tribe[activepop] actives, $tribe[inactivepop] inactives$starvlog')");
         db_op_result($result,__LINE__,__FILE__);

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
