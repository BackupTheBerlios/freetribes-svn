<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: secondaryskill.php
$pos = (strpos($_SERVER['PHP_SELF'], "/secondaryskill.php"));
if ($pos !== false)
{
    die("You cannot access this page directly!");
}

$res = $db->Execute("SELECT * FROM $dbtables[tribes] "
                   ."WHERE sec_skill_att != ''");
  db_op_result($res,__LINE__,__FILE__);
while( !$res->EOF )
{
    $tribe = $res->fields;
    if( !$tribe['sec_skill_att'] == "" )
    {
        ////////////////////////////////Get the info needed//////////////////////////////////
        $cur_sec = $db->Execute("SELECT level,abbr FROM $dbtables[skills] "
                               ."WHERE abbr = '$tribe[sec_skill_att]' "
                               ."AND tribeid = '$tribe[tribeid]'");
         db_op_result($cur_sec,__LINE__,__FILE__);
        $current_sec = $cur_sec->fields;
        $pinfo = $db->Execute("SELECT * FROM $dbtables[skill_table] "
                             ."WHERE abbr = '$tribe[pri_skill_att]'");
           db_op_result($pinfo,__LINE__,__FILE__);
        $pkinfo = $pinfo->fields;
        $sinfo = $db->Execute("SELECT * FROM $dbtables[skill_table] "
                             ."WHERE abbr = '$tribe[sec_skill_att]'");
            db_op_result($sinfo,__LINE__,__FILE__);
        $skinfo = $sinfo->fields;
        $lit = $db->Execute("SELECT * FROM $dbtables[skills] "
                           ."WHERE abbr = 'lit' "
                           ."AND tribeid = '$tribe[tribeid]'");
          db_op_result($lit,__LINE__,__FILE__);
        $literacy = $lit->fields;
        if( $current_sec['level'] > 10 )
        {
            $resk = $db->Execute("SELECT * FROM $dbtables[skills] "
                                ."WHERE abbr = 'res' "
                                ."and tribeid = '$tribe[tribeid]'");
            db_op_result($resk,__LINE__,__FILE__);
            $research = $resk->fields;
        }

    // Queries added to get religious skill bonuses and penalties

    $religion = $db->Execute("SELECT * FROM $dbtables[religions] "
                ."WHERE clanid='$tribe[clanid]'");
       db_op_result($religion,__LINE__,__FILE__);

    $rel_bonus = 0;
    if (!$religion->EOF)
    {
        $rel = $religion->fields;

        // Get skill bonus/penalty for attempting a religious skill

        if ($current_sec['abbr'] == $rel['arch_skill1'])
        {
            $rel_bonus = $rel['arch_skill1_amount'];
        }
        if ($current_sec['abbr'] == $rel['arch_skill2'])
        {
            $rel_bonus = $rel['arch_skill2_amount'];
        }

        if ($current_sec['abbr'] == $rel['pros_skill'])
        {
            $rel_bonus += $rel['pros_skill_amount'];
        }

        if ($current_sec['abbr'] == $rel['arch_pen1'])
        {
            $rel_bonus -= $rel['arch_pen1_amount'];
        }
        if ($current_sec['abbr'] == $rel['arch_pen2'])
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

            $rel_bonus = $rel_bonus * $rel_level * 2.5;

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
                            ."attempted to raise ir/religious skill ".$current_sec['abbr']
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
        $secondary = 0;
        $totalchance = 0;
        ///////////////////////////////Set some variables//////////////////////////////////////
        $skillroll = rand(1,100);                             ///bluesman addition
        $basechance = 55;                                     ///bluesman addition
        if( !$cur_sec )
        {
            $current_sec['level'] = 0;
        }
        $secondary = $current_sec['level'];
        $secondary++;
        $chancebonus = 0;
        if( $secondary > 10 )
        {
            $totaldeduct = round( $basechance + $secondary/2 - .5);                      ///bluesman addition
            $chancebonus += round(($research['level']/2) + ($literacy['level']/2));
        }
        elseif( $secondary <= 10 )
        {
            $totaldeduct = $secondary * 5;                      ///bluesman addition
            $chancebonus += round( $literacy['level']/2 );        ///bluesman addition
        if( $chancebonus < 0 )
        {
            $chancebonus = 0;
        }
    }

    $totalchance += $basechance + $chancebonus - $totaldeduct;

    if( $tribe['pri_skill_att'] == $tribe['sec_skill_att'] )
    {
        $totalchance = 0;
    }
    elseif( $pkinfo['group'] == $skinfo['group'] )
    {
        $totalchance = round( $totalchance / 2 );
    }

    $totalchance = $totalchance + $rel_bonus;

    $totalchance -= $skillroll;

    if( $totalchance >= 0 && $current_sec['level'] < 20 )                 ///bluesman addition
    {
        $current_sec['level'] += 1;
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
                        ."'DEBUG: Secondary SUCCESS $tribe[tribeid]<BR>"
                        ."rolled $skillroll and had total chance "
                        ."$totalchance = "
                        ."$basechance - $skillroll + $chancebonus - $totaldeduct + $rel_bonus')");
             db_op_result($query,__LINE__,__FILE__);
        }
        $query = $db->Execute("UPDATE $dbtables[skills] "
                    ."SET level = '$current_sec[level]' "
                    ."WHERE tribeid = '$tribe[tribeid]' "
                    ."AND abbr = '$skinfo[abbr]'");
           db_op_result($query,__LINE__,__FILE__);
        $query = $db->Execute("UPDATE $dbtables[tribes] "
                    ."SET sec_skill_att = '' "
                    ."WHERE tribeid = '$tribe[tribeid]'");
           db_op_result($query,__LINE__,__FILE__);
        if( $skinfo['morale'] == 'Y'  || $rel_bonus<>0)
        {
            if ($rel_bonus<>0)
            {
                $morale_bonus = 0.002;
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
        if( $current_sec['level'] > 10 )
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
                    ."'SSKILL',"
                    ."'$stamp',"
                    ."'Secondary:$skinfo[long_name] now $current_sec[level].$rel_type')");
              db_op_result($query,__LINE__,__FILE__);
        }
        else
        {
            $query = $db->Execute("UPDATE $dbtables[tribes] "
                        ."SET sec_skill_att = '' "
                        ."WHERE tribeid = '$tribe[tribeid]'");
            db_op_result($query,__LINE__,__FILE__);

        $query = $db->Execute("INSERT INTO $dbtables[logs] "
                    ."VALUES("
                    ."'',"
                    ."'$month[count]',"
                    ."'$year[count]',"
                    ."'$tribe[clanid]',"
                    ."'$tribe[tribeid]',"
                    ."'SSKILL',"
                    ."'$stamp',"
                    ."'Secondary:$skinfo[long_name] failed.$rel_type')");
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
                        ."'DEBUG: Secondary FAILED $tribe[tribeid]<BR>"
                        ."rolled $skillroll and had total chance "
                        ."$totalchance = "
                        ."$basechance - $skillroll + $chancebonus - $totaldeduct + $rel_bonus')");
              db_op_result($query,__LINE__,__FILE__);
        }
        }
        $query = $db->Execute("UPDATE $dbtables[tribes] "
                    ."SET pri_skill_att = '' "
                    ."WHERE tribeid = '$tribe[tribeid]'");
           db_op_result($query,__LINE__,__FILE__);

        $query = $db->Execute("UPDATE $dbtables[tribes] "
                    ."SET sec_skill_att = '' "
                    ."WHERE tribeid = '$tribe[tribeid]'");
          db_op_result($query,__LINE__,__FILE__);
    }
    $res->MoveNext();
}

?>
