<?php
require_once("../config.php");
$time_start = getmicrotime();
include("game_time.php");
connectdb();
$res = $db->Execute("SELECT * FROM $dbtables[tribes]");
while( !$res->EOF )
{
    $tribe = $res->fields;
    $act = $db->Execute("SELECT * FROM $dbtables[activities] "
                       ."WHERE tribeid = '$tribe[tribeid]' "
                       ."AND skill_abbr = 'skn' LIMIT 1");
      db_op_result($act,__LINE__,__FILE__);
    while( !$act->EOF )
    {
        $act_do = $act->fields;
        if( $act_do[skill_abbr] == 'skn' )
        {
            $gskill = $db->Execute("SELECT * FROM $dbtables[skills] "
                                  ."WHERE level > 0 "
                                  ."AND tribeid = '$tribe[tribeid]' "
                                  ."AND abbr = 'gut'");
             db_op_result($gskill,__LINE__,__FILE__);
            $bskill = $db->Execute("SELECT * FROM $dbtables[skills] "
                                  ."WHERE level > 0 "
                                  ."AND tribeid = '$tribe[tribeid]' "
                                  ."AND abbr = 'bon'");
             db_op_result($bskill,__LINE__,__FILE__);
            $sskill = $db->Execute("SELECT * FROM $dbtables[skills] "
                                  ."WHERE level > 0 "
                                  ."AND tribeid = '$tribe[tribeid]' "
                                  ."AND abbr = 'skn'");
              db_op_result($sskill,__LINE__,__FILE__);
            $gskill = $gskill->fields;
            $bskill = $bskill->fields;
            $sskill = $sskill->fields;
            if( $gskill->EOF )
            {
                $gskill[level] = 0;
            }
            if( $bskill->EOF )
            {
                $bskill[level] = 0;
            }
            if( $sskill->EOF )
            {
                $sskill[level] = 0;
            }
            $gutters = $act_do[actives];
            $boners = $act_do[actives];
            $skinners = $act_do[actives];
            $maxgutters = $act_do[actives];
            $maxboners = $act_do[actives];
            $maxskinners = $act_do[actives];
            if( $gskill[level] < 10 )
            {
                $maxgutters = $gskill[level] * 10;
            }
            if( $maxgutters < $act_do[actives] )
            {
                $gutters = $maxgutters;
            }
            else
            {
                $gutters = $act_do[actives];
            }
            if( $bskill[level] < 10 )
            {
                $maxboners = $bskill[level] * 10;
            }
            if( $maxboners < $act_do[actives] )
            {
                $boners = $maxboners;
            }
            else
            {
                $boners = $act_do[actives];
            }
            if( $sskill[level] < 10 )
            {
                $maxskinners = $sskill[level] * 10;
            }
            if( $maxskinners < $act_do[actives] )
            {
                $skinners = $maxskinners;
            }
            else
            {
                $skinners = $act_do[actives];
            }
            $numanim = $db->Execute("SELECT * FROM $dbtables[livestock] "
                                   ."WHERE amount > 0 "
                                   ."AND tribeid = '$tribe[goods_tribe]' "
                                   ."AND type = 'Goats'");
               db_op_result($numanim,__LINE__,__FILE__);
            $speed = 3;
            $posskins = 1;
            $possboned = 6;
            $possgut = 6;
            $posprovs = 4;
            if( $numanim->EOF )
            {
                $numanim = $db->Execute("SELECT * FROM $dbtables[livestock] "
                                       ."WHERE amount > 0 "
                                       ."AND tribeid = '$tribe[goods_tribe]' "
                                       ."AND type = 'Cattle'");
                 db_op_result($numanim,__LINE__,__FILE__);
                $speed = 1;
                $posskins = 2;
                $possboned = 3;
                $possgut = 3;
                $posprovs = 20;
            }
            if( $numanim->EOF )
            {
                $numanim = $db->Execute("SELECT * FROM $dbtables[livestock] "
                                       ."WHERE amount > 0 "
                                       ."AND tribeid = '$tribe[goods_tribe]' "
                                       ."AND type = 'Sheep'");
                 db_op_result($numanim,__LINE__,__FILE__);
                $speed = 3;
                $posskins = 1;
                $possboned = 6;
                $possgut = 6;
                $posprovs = 4;
            }
            if( $numanim->EOF )
            {
                $numanim = $db->Execute("SELECT * FROM $dbtables[livestock] "
                                       ."WHERE amount > 0 AND "
                                       ."tribeid = '$tribe[goods_tribe]' "
                                       ."AND type = 'Pigs'");
                  db_op_result($numanim,__LINE__,__FILE__);
                $speed = 3;
                $posskins = 1;
                $possboned = 6;
                $possgut = 6;
                $posprovs = 3;
            }
            if( $numanim->EOF )
            {
                $numanim = $db->Execute("SELECT * FROM $dbtables[livestock] "
                                       ."WHERE amount > 0 "
                                       ."AND tribeid = '$tribe[goods_tribe]' "
                                       ."AND type = 'Horses'");
                 db_op_result($numanim,__LINE__,__FILE__);
                $speed = 1;
                $posskins = 3;
                $possboned = 2;
                $possgut = 2;
                $posprovs = 30;
            }
            if( $numanim->EOF )
            {
                $numanim = $db->Execute("SELECT * FROM $dbtables[livestock] "
                                       ."WHERE amount > 0 "
                                       ."AND tribeid = '$tribe[goods_tribe]' "
                                       ."AND type = 'Elephants'");
                  db_op_result($numanim,__LINE__,__FILE__);
                $speed = .5;
                $posskins = 6;
                $possboned = 1;
                $possgut = 1;
                $posprovs += 60;
            }
            $animals = $numanim->fields;
            $skins = 0;
            $numskinned = 0;
            $provs = 0;
            $lognum = 0;
            $checkspeed = $speed - 1;
            $totalanimals = 0;
            $totalanimals = $animals[amount];
            while( $totalanimals > $checkspeed && $skinners > 0 )
            {
                $skins += ($posskins * $speed);
                $skinners -= 1;
                $totalanimals -= $speed;
                $provs += $posprovs * $speed;
                $numskinned += $speed;
                $lognum += $speed;
            }
            $totalanimals -= $numskinned;
            $skinnedprovs = $provs;
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + $skins "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND proper = 'Skins'");
                 db_op_result($query,__LINE__,__FILE__);
            $bones = 0;
            $reclaim = 0;
            if( $sskill[level] == 0 )
            {
                $reclaim = 1;
                $numboned = $animals[amount];
            }
            else
            {
                $numboned = $numskinned;
                $reclaim = 0;
            }
            $bones = 0;
            $checkposs = $possboned - 1;
            while( $numboned > $checkposs && $boners > 0 )
            {
                $bones += 12;
                $boners -= 1;
                $numboned -= $possboned;
                if( $sskill[level] < 1 )
                {
                    $lognum += $possboned;
                }
                if( $reclaim == 1 )
                {
                    $provs += $posprovs * $possboned;
                }
            }
            if( $sskill[level] < 1 )
            {
                $totalanimals -= $numboned;
            }
            $skinnedprovs = $provs;
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + $bones "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND proper = 'Bones'");
             db_op_result($query,__LINE__,__FILE__);
            $gut = 0;
            $reclaim = 0;
            if( $sskill[level] < 1 && $bskill[level] < 1 )
            {
                $reclaim = 1;
                $numgutted = $animals[amount];
            }
            else
            {
                $numgutted = $numskinned;
                $reclaim = 0;
            }
            $gut = 0;
            $checkposs = $possgut - 1;
            while( $numgutted > $checkposs && $gutters > 0 )
            {
                $gut += 12;
                $gutters -= 1;
                $numgutted -= $possgut;
                if( $sskill[level] < 1 && $bskill[level] < 1 )
                {
                    $lognum += $possboned;
                }
                if( $reclaim == 1 )
                {
                    $provs += $posprovs * $possgut;
                }
            }
            if( $sskill[level] < 1 && $bskill[level] < 1 )
            {
                $totalanimals = $numgutted;
            }
            $query = $db->Execute("UPDATE $dbtables[livestock] "
                        ."SET amount = '$totalanimals' "
                        ."WHERE type = '$animals[type]' "
                        ."AND tribeid = '$tribe[goods_tribe]'");
                 db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + $gut "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND proper = 'Gut'");
            db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + $provs "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND proper = 'Provisions'");
              db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("DELETE FROM $dbtables[activities] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND skill_abbr = 'skn'");
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
                        ."'Skin/Gut/Bone: $skins skins, $bones bones, "
                        ."$gut guts and $provs provs gained using $lognum $animals[type]')");
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
