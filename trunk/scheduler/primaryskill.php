<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: primaryskill.php
 $pos = (strpos($_SERVER['PHP_SELF'], "/mysqlt-common.php"));
if ($pos !== false)
{
    die("You cannot access this page directly!");
}
require_once("config.php");
$time_start = getmicrotime();
include("scheduler/game_time.php");
connectdb();
$res = $db->Execute("SELECT * FROM $dbtables[tribes] "
                   ."WHERE pri_skill_att != ''");
     db_op_result($res,__LINE__,__FILE__);
while( !$res->EOF )
{
    $tribe = $res->fields;
    ////////////////////////////////Get the info needed//////////////////////////////////
    $cur_pri = $db->Execute("SELECT * FROM $dbtables[skills] "
                           ."WHERE abbr = '$tribe[pri_skill_att]' "
                           ."AND tribeid = '$tribe[tribeid]'");
     db_op_result($cur_pri,__LINE__,__FILE__);
    $current_pri = $cur_pri->fields;

    $sinfo = $db->Execute("SELECT * FROM $dbtables[skill_table] "
                         ."WHERE abbr = '$tribe[pri_skill_att]'");
      db_op_result($sinfo,__LINE__,__FILE__);
    $skinfo = $sinfo->fields;

    $lit = $db->Execute("SELECT * FROM $dbtables[skills] "
                       ."WHERE abbr = 'lit' "
                       ."AND tribeid = '$tribe[tribeid]'");
     db_op_result($lit,__LINE__,__FILE__);
    $literacy = $lit->fields;

    $resk = $db->Execute("SELECT * FROM $dbtables[skills] "
                        ."WHERE abbr = 'res' "
                        ."AND tribeid = '$tribe[tribeid]'");
       db_op_result($resk,__LINE__,__FILE__);
    $research = $resk->fields;

    // Queries added to get religious skill bonuses and penalties

    $religion = $db->Execute("SELECT * FROM $dbtables[religions] "
                ."WHERE clanid='$tribe[clanid]'");
       db_op_result($religion,__LINE__,__FILE__);

    $rel_bonus = 0;
    if (!$religion->EOF)
    {
        $rel = $religion->fields;

        // Get skill bonus/penalty for attempting a religious skill

        if ($current_pri['abbr'] == $rel['arch_skill1'])
        {
            $rel_bonus = $rel['arch_skill1_amount'];
        }
        if ($current_pri['abbr'] == $rel['arch_skill2'])
        {
            $rel_bonus = $rel['arch_skill2_amount'];
        }

        if ($current_pri['abbr'] == $rel['pros_skill'])
        {
            $rel_bonus += $rel['pros_skill_amount'];
        }

        if ($current_pri['abbr'] == $rel['arch_pen1'])
        {
            $rel_bonus -= $rel['arch_pen1_amount'];
        }
        if ($current_pri['abbr'] == $rel['arch_pen2'])
        {
            $rel_bonus -= $rel['arch_pen2_amount'];
        }

        if ($rel_bonus <> 0)
        {
            $rel_level = $db->Execute("SELECT * FROM $dbtables[skills] "
                        ."WHERE abbr='rel' "
                        ."AND tribeid='$tribe[tribeid]'");
                db_op_result($rel_level,__LINE__,__FILE__);
            $rel_level = $rel_level->fields['level'];

            $rel_bonus = $rel_bonus * $rel_level * 7.5;

            if( $game_skill_debug )
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
                            ."'DEBUG: Primary $tribe[tribeid] "
                            ."attempted to raise ir/religious skill ".$current_pri['abbr']
                            ." with a modifier of $rel_bonus.')");
               db_op_result($query,__LINE__,__FILE__);
            }
        }

    }

    if ($rel_bonus > 0)
    {
        $rel_type = " (Religious)";
    }
    elseif ($rel_bonus < 0)
    {
        $rel_type = " (Irreligious)";
    }
    else
    {
        $rel_type = "";
    }

    /////////////////////////////Zero out any used variables////////////////////////////////
    $primary = 0;
    $totalchance = 0;
    ///////////////////////////////Set some variables//////////////////////////////////////
    $skillroll = rand(1,100);    ///bluesman addition
    $basechance = 110;           ///bluesman addition
    if( !$cur_pri )
    {
        $current_pri[level] = 0;
    }
    $primary = $current_pri['level'];
    $primary++;
    $chancebonus = 0;
    if( $primary > 10 )
    {
        $totaldeduct = $basechance + $primary - 1;   ///bluesman addition
        $chancebonus += $research['level'] + $literacy['level'];
    }
    elseif( $primary <= 10 )
    {
        $totaldeduct = $primary * 10;            ///bluesman addition
        $chancebonus += $literacy['level'];
        if( $chancebonus < 0 )
        {
            $chancebonus = 0;
        }
    }
    $totalchance = $basechance - $skillroll + $chancebonus - $totaldeduct;   ///bluesman addition

    $totalchance = $totalchance + $rel_bonus;

    if( $totalchance >= 0 && $current_pri['level'] < 20 )                      ///bluesman addition
    {
        $current_pri['level'] += 1;
        if( $game_skill_debug )
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
                        ."'DEBUG: Primary SUCCESS $tribe[tribeid]<BR>"
                        ."rolled $skillroll and had total chance "
                        ."$totalchance = "
                        ."$basechance - $skillroll + $chancebonus - $totaldeduct + $rel_bonus')");
                db_op_result($query,__LINE__,__FILE__);
        }
        $query = $db->Execute("UPDATE $dbtables[skills] "
                    ."SET level = '$current_pri[level]' "
                    ."WHERE tribeid = '$tribe[tribeid]' "
                    ."AND abbr = '$skinfo[abbr]'");
            db_op_result($query,__LINE__,__FILE__);
        $query = $db->Execute("UPDATE $dbtables[tribes] "
                    ."SET pri_skill_att = '' "
                    ."WHERE tribeid = '$tribe[tribeid]'");
             db_op_result($query,__LINE__,__FILE__);
        // NOTE: $rel_bonus<>0 will also result in a morale gain
        // if the tribe succeeded with a skill penalised by the religion

        if( $skinfo['morale'] == 'Y' || $rel_bonus<>0)
        {
            if ($rel_bonus<>0)
            {
                $morale_bonus = 0.005;
            }
            else
            {
                $morale_bonus = 0.01;
            }
                        $query = $db->Execute("UPDATE $dbtables[tribes] "
                        ."SET morale = morale + '$morale_bonus' "
                        ."WHERE tribeid = '$tribe[tribeid]'");
                    db_op_result($query,__LINE__,__FILE__);
        }
        if( $current_pri['level'] > 10 )
        {
            $query = $db->Execute("UPDATE $dbtables[skills] "
                        ."SET level = '0' "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND abbr = 'res'");
              db_op_result($query,__LINE__,__FILE__);
        }
        $query = $db->Execute("INSERT INTO $dbtables[logs] "
                    ."VALUES("
                    ."'',"
                    ."'$month[count]',"
                    ."'$year[count]',"
                    ."'$tribe[clanid]',"
                    ."'$tribe[tribeid]',"
                    ."'PSKILL',"
                    ."'$stamp',"
                    ."'Primary:$skinfo[long_name] now $current_pri[level].$rel_type')");
         db_op_result($query,__LINE__,__FILE__);
    }
    else
    {
        $query = $db->Execute("UPDATE $dbtables[tribes] "
                    ."SET pri_skill_att = '' "
                    ."WHERE tribeid = '$tribe[tribeid]'");
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
                    ."'Primary:$skinfo[long_name] failed.$rel_type')");
           db_op_result($query,__LINE__,__FILE__);
        if( $game_skill_debug )
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
                        ."'DEBUG: Primary FAILED $tribe[tribeid]<BR>"
                        ."rolled $skillroll and had total chance "
                        ."$totalchance = "
                        ."$basechance - $skillroll + $chancebonus - $totaldeduct + $rel_bonus')");
                db_op_result($query,__LINE__,__FILE__);
        }
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
