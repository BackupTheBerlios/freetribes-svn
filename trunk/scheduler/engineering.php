<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: engineering.php
$pos = (strpos($_SERVER['PHP_SELF'], "/engineering.php"));
if ($pos !== false)
{
    die("You cannot access this page directly!");
}


    $act = $db->Execute("SELECT * FROM $dbtables[activities] WHERE skill_abbr = 'eng'");
    db_op_result($act,__LINE__,__FILE__);
    //select activities for this tribe
    while( !$act->EOF )
    {

        $act_do = $act->fields;
         $tribeid = $act_do['tribeid'];
         $res = $db->Execute("SELECT clanid,hex_id,goods_tribe FROM $dbtables[tribes] where tribeid = '$tribeid'");
        db_op_result($res,__LINE__,__FILE__);
        $tribe = $res->fields;
        $clanid = $tribe['clanid'];
        $hex_id = $tribe['hex_id'];
        $goods_tribe = $tribe['goods_tribe'];

            $mhp = $db->Execute("SELECT count(*) as count FROM $dbtables[structures] "
                                ."WHERE complete = 'Y' "
                                ."AND long_name = 'meetinghouse' "
                                ."AND clanid = '$clanid' "
                                ."AND hex_id = '$hex_id'");
               db_op_result($mhp,__LINE__,__FILE__);
            $has_meethouse = $mhp->fields['count'];
            $tpp = $db->Execute("SELECT count(*) as count FROM $dbtables[structures] "
                                ."WHERE complete = 'Y' "
                                ."AND long_name = 'tradepost' "
                                ."AND clanid = '$clanid' "
                                ."AND hex_id = '$hex_id'");
                 db_op_result($tpp,__LINE__,__FILE__);
            $has_tradepost = $tpp->fields['count'];

            if( $act_do['product'] == 'tradepost' )
            {

                if( $has_meethouse > 0 && $has_tradepost < 1 )
                {
                    $log = $db->Execute("SELECT * FROM $dbtables[products] "
                                        ."WHERE long_name = 'logs' "
                                        ."AND tribeid = '$goods_tribe'");
                          db_op_result($log,__LINE__,__FILE__);
                    $loginfo = $log->fields;
                    $logs_installed = 0;
                    while( $loginfo['amount'] > 0 && $act_do['actives'] > 0 && $logs_installed < 100 )
                    {
                        $logs_installed += 2;
                        $act_do['actives'] -= 1;
                        $loginfo['amount'] -= 2;
                    }

                    $tradepost = $db->Execute("SELECT * FROM $dbtables[structures] "
                                              ."WHERE long_name = 'tradepost' "
                                              ."AND clanid = '$clanid' "
                                              ."AND tribeid = '$goods_tribe' "
                                              ."AND hex_id = '$hex_id'");
                          db_op_result($tradepost,__LINE__,__FILE__);
                    if( !$tradepost->EOF )
                    {
                        $tradepostinfo = $tradepost->fields;
                        if( $logs_installed + $tradepostinfo['struct_pts'] >= 100 )
                        {
                            $query = $db->Execute("UPDATE $dbtables[structures] "
                                         ."SET complete = 'Y',"
                                         ."struct_pts = '100' "
                                         ."WHERE clanid = '$clanid' "
                                         ."AND hex_id = '$hex_id' "
                                         ."AND long_name = 'tradepost' "
                                         ."AND struct_id = '$tradepostinfo[struct_id]'");
                                db_op_result($query,__LINE__,__FILE__);
                        }
                        elseif( $logs_installed + $tradepostinfo['struct_pts'] < 100 )
                        {
                            $query = $db->Execute("UPDATE $dbtables[structures] "
                                         ."SET "
                                         ."  struct_pts = struct_pts + '$logs_installed', "
                                         ."  used='Y' "
                                         ."WHERE clanid = '$clanid' "
                                         ."AND hex_id = '$hex_id' "
                                         ."AND long_name = 'tradepost' "
                                         ."AND struct_id = '$tradepostinfo[struct_id]'");
                               db_op_result($query,__LINE__,__FILE__);
                        }
                    }
                    else
                    {
                        if( $logs_installed > 99 )
                        {
                            $query = $db->Execute("INSERT INTO $dbtables[structures] "
                                         ."VALUES("
                                         ."'',"
                                         ."'tradepost',"
                                         ."'Trade Post',"
                                         ."'$hex_id',"
                                         ."'$goods_tribe',"
                                         ."'$clanid',"
                                         ."'Y',"
                                         ."'100',"
                                         ."'100',"
                                         ."'',"
                                         ."'',"
                                         ."'N')");
                           db_op_result($query,__LINE__,__FILE__);
                           $log_type = "ENG::TRADE";
                            $logtext = "Engineering: Tradepost Construction completed using $logs_installed logs.";

                        }
                        elseif( $logs_installed < 100 && $logs_installed > 1 )
                        {
                            $query = $db->Execute("INSERT INTO $dbtables[structures] "
                                         ."VALUES("
                                         ."'',"
                                         ."'tradepost',"
                                         ."'Trade Post',"
                                         ."'$hex_id',"
                                         ."'$goods_tribe',"
                                         ."'$clanid',"
                                         ."'N',"
                                         ."'$logs_installed',"
                                         ."'100',"
                                         ."'',"
                                         ."'',"
                                         ."'Y')");
                           db_op_result($query,__LINE__,__FILE__);
                            $log_type = "ENG::TRADE";
                            $logtext = "Engineering: Tradepost Construction in progress using $logs_installed logs.";

                        }
                        else
                        {
                          $log_type = "ENG::TRADE";
                       $logtext = "Engineering: Could not build trading post- Insufficient logs ";

                        }
                    }

                }
                else
                {
                    $log_type = "ENG::TRADE";
                    $logtext = "Engineering: Could not build trading post- either you have no meeting house or you already ahve a trading post here";
                }
           playerlog($tribeid,$clanid,$log_type,$month['count'],$year['count'],$logtext,$dbtables);

                $query = $db->Execute("DELETE FROM $dbtables[activities] "
                             ."WHERE tribeid = '$tribeid' "
                             ."AND product = 'tradepost'");
                    db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("UPDATE $dbtables[products] "
                             ."SET amount = amount - '$logs_installed' "
                             ."WHERE tribeid = '$goods_tribe' "
                             ."AND long_name = 'logs'");
                     db_op_result($query,__LINE__,__FILE__);

            }

            if( $act_do['product'] == 'meetinghouse' )
            {
                if( $has_meethouse == 0 )
                {
                      $log = $db->Execute("SELECT * FROM $dbtables[products] WHERE long_name = 'logs' AND tribeid = '$goods_tribe'");
                            db_op_result($log,__LINE__,__FILE__);
                    $loginfo = $log->fields;
                    $logs_installed = 0;
                   //ok we know how many logs we have

                     $meetinghouse = $db->Execute("SELECT struct_id,struct_pts FROM $dbtables[structures] "
                                                 ."WHERE long_name = 'meetinghouse' "
                                                 ."AND tribeid = '$goods_tribe' "
                                                 ."AND hex_id = '$hex_id'");
                           db_op_result($meetinghouse,__LINE__,__FILE__);
                    if( !$meetinghouse->EOF )
                    {
                      //we have an incomplete structure, let's finish it
                        $logs_installed = $meetinghouse->fields['struct_pts'];
                        $struct_id = $meetinghouse->fields['struct_id'];
                        while( $loginfo['amount'] > 0 && $act_do['actives'] > 0 && $logs_installed < 100 )
                        {
                            $logs_installed += 2;
                            $act_do['actives'] -= 1;
                            $loginfo['amount'] -= 2;
                        }

                        if( $logs_installed + $meetinghouseinfo['struct_pts'] > 99 )
                        {
                            $query = $db->Execute("UPDATE $dbtables[structures] "
                                         ."SET complete = 'Y', "
                                         ."struct_pts = '100' "
                                         ."WHERE tribeid = '$goods_tribe' "
                                         ."AND hex_id = '$hex_id' "
                                         ."AND long_name = 'meetinghouse'");
                              db_op_result($query,__LINE__,__FILE__);
                              $log_type = "UPDATE";
                            $logtext =  "Engineering: Meetinghouse construction finished, used $logs_installed logs.";
                            playerlog($tribeid,$clanid,$log_type,$month['count'],$year['count'],$logtext,$dbtables);

                        }
                        elseif( $logs_installed + $meetinghouseinfo['struct_pts'] < 100 )
                        {
                            $query = $db->Execute("UPDATE $dbtables[structures] "
                                         ."SET struct_pts = struct_pts + '$logs_installed' "
                                         ."WHERE tribeid = '$goods_tribe' "
                                         ."AND hex_id = '$hex_id' "
                                         ."AND long_name = 'meetinghouse'");
                               db_op_result($query,__LINE__,__FILE__);
                           $log_type = "UPDATE";
                            $logtext =  "Engineering: Meetinghouse construction started, not complete, used $logs_installed logs.";
                            playerlog($tribeid,$clanid,$log_type,$month['count'],$year['count'],$logtext,$dbtables);
                        }

                    }
                    else
                    {
                        //we start or build a new structure.
                        while( $loginfo['amount'] > 0 && $act_do['actives'] > 0 && $logs_installed < 100 )
                        {
                            $logs_installed += 2;
                            $act_do['actives'] -= 1;
                            $loginfo['amount'] -= 2;
                        }
                        if( $logs_installed >= 100 )
                        {
                            $query = $db->Execute("INSERT INTO $dbtables[structures] "
                                         ."VALUES("
                                         ."'',"
                                         ."'meetinghouse',"
                                         ."'Meeting House',"
                                         ."'$hex_id',"
                                         ."'$goods_tribe',"
                                         ."'$clanid',"
                                         ."'Y',"
                                         ."'100',"
                                         ."'100',"
                                         ."'',"
                                         ."'',"
                                         ."'N')");
                             db_op_result($query,__LINE__,__FILE__);
                            $log_type = "UPDATE";
                            $logtext =  "Engineering: Meetinghouse constructed using $logs_installed logs.";
                            playerlog($tribeid,$clanid,$log_type,$month['count'],$year['count'],$logtext,$dbtables);
                        }
                        else
                        {
                            $query = $db->Execute("INSERT INTO $dbtables[structures] "
                                         ."VALUES("
                                         ."'',"
                                         ."'meetinghouse',"
                                         ."'Meeting House',"
                                         ."'$hex_id',"
                                         ."'$goods_tribe',"
                                         ."'$clanid',"
                                         ."'N',"
                                         ."'$logs_installed',"
                                         ."'100',"
                                         ."'',"
                                         ."'',"
                                         ."'N')");
                            db_op_result($query,__LINE__,__FILE__);
                            $log_type = "UPDATE";
                            $logtext =  "Engineering: Meetinghouse construction started, not complete, used $logs_installed logs.";
                            playerlog($tribeid,$clanid,$log_type,$month['count'],$year['count'],$logtext,$dbtables);

                        }
                    }
                }
                else
                {
                    $log_type = "UPDATE";
                    $logtext =  "Engineering: You already have a meeting house, so it was not built.";
                    playerlog($tribeid,$clanid,$log_type,$month['count'],$year['count'],$logtext,$dbtables);


                }
                $query = $db->Execute("DELETE FROM $dbtables[activities] "
                             ."WHERE tribeid = '$tribeid' "
                             ."AND product = 'meetinghouse'");
                    db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("UPDATE $dbtables[products] "
                             ."SET amount = amount - '$logs_installed' "
                             ."WHERE tribeid = '$goods_tribe' "
                             ."AND long_name = 'logs'");
                    db_op_result($query,__LINE__,__FILE__);

            }

            if( $act_do['product'] == 'sq. yards moat' )
            {
                if( $has_meethouse > 0 )
                {
                    $moat = $db->Execute("SELECT * FROM $dbtables[structures] "
                                         ."WHERE long_name = 'sq. yards moat' "
                                         ."AND clanid = '$clanid' "
                                         ."AND hex_id = '$hex_id'");
                         db_op_result($moat,__LINE__,__FILE__);
                    $moat_length = 0;
                    while( $act_do['actives'] > 1 )
                    {
                        $act_do['actives'] -= 2;
                        $moat_length += 1;
                    }
                    if( !$moat->EOF )
                    {
                        $query = $db->Execute("UPDATE $dbtables[structures] "
                                     ."SET number = number + '$moat_length', "
                                     ."struct_pts = struct_pts + '$moat_length', "
                                     ."max_struct_pts = max_struct_pts + '$moat_length' "
                                     ."WHERE tribeid = '$tribeid' "
                                     ."AND long_name = 'sq. yards moat'");
                             db_op_result($query,__LINE__,__FILE__);
            $log_type="UPDATE";
            $logtext = "Engineering: $moat_length sq yards added to Moat.";
 playerlog($tribeid,$clanid,$log_type,$month['count'],$year['count'],$logtext,$dbtables);


                    }
                    else
                    {
                        $query = $db->Execute("INSERT INTO $dbtables[structures] "
                                     ."VALUES("
                                     ."'',"
                                     ."'sq. yards moat',"
                                     ."'Moat',"
                                     ."'$hex_id',"
                                     ."'$tribeid',"
                                     ."'$clanid',"
                                     ."'Y',"
                                     ."'$moat_length',"
                                     ."'$moat_length',"
                                     ."'sq. yds',"
                                     ."'$moat_length',"
                                     ."'N')");
                        db_op_result($query,__LINE__,__FILE__);
             $log_type="UPDATE";
            $logtext = "Engineering: $moat_length sq yards added to Moat.";
 playerlog($tribeid,$clanid,$log_type,$month['count'],$year['count'],$logtext,$dbtables);

                    }
                }
                $query = $db->Execute("DELETE FROM $dbtables[activities] "
                             ."WHERE tribeid = '$tribeid' "
                             ."AND product = 'sq. yards moat'");
                     db_op_result($query,__LINE__,__FILE__);
            }

            if( $act_do['product'] == 'gate' )
            {
                if( $has_meethouse > 0 )
                {
                    $wall = $db->Execute("SELECT * FROM $dbtables[structures] "
                                         ."WHERE tribeid = '$tribeid' "
                                         ."AND hex_id = '$hex_id' "
                                         ."AND long_name = 'stonewalls10' "
                                         ."OR long_name = 'stonewalls15' "
                                         ."AND tribeid = '$tribeid' "
                                         ."AND hex_id = '$hex_id' "
                                         ."OR long_name = 'stonewalls20' "
                                         ."AND tribeid = '$tribeid' "
                                         ."AND hex_id = '$hex_id' "
                                         ."OR long_name = 'palisade' "
                                         ."AND tribeid = '$tribeid' "
                                         ."AND hex_id = '$hex_id'");
                                   db_op_result($wall,__LINE__,__FILE__);
                    if( !$wall->EOF )
                    {
                        $log = $db->Execute("SELECT * FROM $dbtables[products] "
                                            ."WHERE long_name = 'logs' "
                                            ."AND tribeid = '$goods_tribe'");
                                db_op_result($log,__LINE__,__FILE__);
                        $loginfo = $log->fields;
                        if( $loginfo['amount'] >= 10 && $act_do['actives'] >= 5 )
                        {
                            $query = $db->Execute("INSERT INTO $dbtables[structures] "
                                         ."VALUES("
                                         ."'',"
                                         ."'gate',"
                                         ."'Gate',"
                                         ."'$hex_id',"
                                         ."'$tribeid',"
                                         ."'$clanid',"
                                         ."'Y',"
                                         ."'10',"
                                         ."'10',"
                                         ."'',"
                                         ."'',"
                                         ."'N')");
                              db_op_result($query,__LINE__,__FILE__);
                            $query = $db->Execute("UPDATE $dbtables[products] "
                                         ."SET amount = amount - 10 "
                                         ."WHERE tribeid = '$goods_tribe' "
                                         ."AND long_name = 'logs'");
                              db_op_result($query,__LINE__,__FILE__);
                        }
                    }
                }
                $query = $db->Execute("DELETE FROM $dbtables[activities] "
                             ."WHERE tribeid = '$tribeid' "
                             ."AND product = 'gate'");
                    db_op_result($query,__LINE__,__FILE__);
            }

            if( $act_do['product'] == 'refinery' )
            {

                if( $has_meethouse > 0 )
                {
                    $log = $db->Execute("SELECT * FROM $dbtables[products] WHERE long_name = 'logs' AND tribeid = '$goods_tribe'");
                    db_op_result($log,__LINE__,__FILE__);
                    $loginfo = $log->fields;
                    $logs_installed = 0;
                    while( $loginfo['amount'] > 0 && $act_do['actives'] > 0 && $logs_installed < 100 )
                    {
                        $logs_installed += 2;
                        $act_do['actives'] -= 1;
                        $loginfo['amount'] -= 2;
                    }
                    $refine = $db->Execute("SELECT * FROM $dbtables[structures] WHERE long_name = 'refinery' AND tribeid = '$goods_tribe' AND complete = 'N' AND hex_id = '$hex_id'");
                    db_op_result($refine,__LINE__,__FILE__);
                    if( !$refine->EOF )
                    {
                        $refineinfo = $refine->fields;
                        if( $logs_installed + $refineinfo['struct_pts'] >= 100 )
                        {
                            $query = $db->Execute("UPDATE $dbtables[structures] SET complete = 'Y', struct_pts = '100', subunit = 'smelter' WHERE tribeid = '$goods_tribe' AND hex_id = '$hex_id' AND long_name = 'refinery'");
                            db_op_result($query,__LINE__,__FILE__);
                        }
                        elseif( $logs_installed + $refineinfo['struct_pts'] < 100 && $logs_installed > 1 )
                        {
                            $query = $db->Execute("UPDATE $dbtables[structures] SET struct_pts = struct_pts + '$logs_installed' WHERE tribeid = '$goods_tribe' AND hex_id = '$hex_id' AND long_name = 'refinery'");
                                db_op_result($query,__LINE__,__FILE__);
                        }
                    }
                    else
                    {
                        if( $logs_installed >= 100 )
                        {
                            $query = $db->Execute("INSERT INTO $dbtables[structures]
                                VALUES( '', 'refinery', 'Refinery', '$hex_id', '$goods_tribe', '$clanid', 'Y', '$logs_installed', '100', 'smelter', '','N')");
                            db_op_result($query,__LINE__,__FILE__);
                        }
                        elseif( $logs_installed < 100 && $logs_installed > 1 )
                        {
                            $query = $db->Execute("INSERT INTO $dbtables[structures] "
                                         ."VALUES("
                                         ."'',"
                                         ."'refinery',"
                                         ."'Refinery',"
                                         ."'$hex_id',"
                                         ."'$goods_tribe',"
                                         ."'$clanid',"
                                         ."'N',"
                                         ."'$logs_installed',"
                                         ."'100',"
                                         ."'smelter',"
                                         ."'',"
                                         ."'N')");
                             db_op_result($query,__LINE__,__FILE__);
                        }

                    }

                    $query = $db->Execute("DELETE FROM $dbtables[activities] WHERE tribeid = '$tribeid' AND product = 'refinery'");
                    db_op_result($query,__LINE__,__FILE__);
                    $query = $db->Execute("UPDATE $dbtables[products] SET amount = amount - '$logs_installed' WHERE tribeid = '$goods_tribe' AND long_name = 'logs'");
                    db_op_result($query,__LINE__,__FILE__);
                    $log_type='UPDATE';
                    $logtext = "Engineering: Refinery Construction using $logs_installed logs.";
                    playerlog($tribeid,$clanid,$log_type,$month['count'],$year['count'],$logtext,$dbtables);

                  }
                  else
                  {
                      $log_type='UPDATE';
                      $logtext = "Refinery construction failed - no meeting house (Hex: $hex_id Tribe: $tribeid Clan: $clanid Logs: $loginfo[amount] Debug Number: $has_meethouse)";
                      playerlog($tribeid,$clanid,$log_type,$month['count'],$year['count'],$logtext,$dbtables);
                  }
            }

            if( $act_do['product'] == 'smelter' )
            {
                $refine = $db->Execute("SELECT * FROM $dbtables[structures] "
                                       ."WHERE clanid = '$clanid' "
                                       ."AND long_name = 'refinery' "
                                       ."AND hex_id = '$hex_id' "
                                       ."AND complete = 'Y' "
                                       ."AND number < 100");
                               db_op_result($refine,__LINE__,__FILE__);
                $refineinfo = $refine->fields;

                if( !$refine->EOF )
                {
                    $maxsmelt = 100 - $refineinfo['number'];

                    if( $has_meethouse > 0 && !$refine->EOF )
                    {
                        $coal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                             ."WHERE tribeid = '$goods_tribe' "
                                             ."AND long_name = 'Coal'");
                             db_op_result($coal,__LINE__,__FILE__);
                        $metal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                              ."WHERE tribeid = '$goods_tribe' "
                                              ."AND long_name = 'Iron' "
                                              ."AND amount > 49");
                               db_op_result($metal,__LINE__,__FILE__);
                        if( $metal->EOF )
                        {
                            $metal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                                  ."WHERE tribeid = '$goods_tribe' "
                                                  ."AND long_name = 'Bronze'");
                             db_op_result($metal,__LINE__,__FILE__);
                        }
                        $coalinfo = $coal->fields;
                        $metalinfo = $metal->fields;
                        $startcoal = $coalinfo['amount'];
                        $startmetal = $metalinfo['amount'];
                        $smelters = 0;

                        while( $smelters < $maxsmelt && $metalinfo['amount'] > 49 && $coalinfo['amount'] > 199 && $act_do['actives'] > 4 )
                        {
                            $metalinfo['amount'] -= 50;
                            $coalinfo['amount'] -= 200;
                            $act_do['actives'] -= 5;
                            $smelters += 1;
                        }
                        $deltacoal = 0;
                        $deltametal = 0;
                        $deltacoal = $startcoal - $coalinfo['amount'];
                        $deltametal = $startmetal - $metalinfo['amount'];

                        if( $smelters < 0 )
                        {
                            $smelters = 0;
                        }
                        $query = $db->Execute("UPDATE $dbtables[structures] "
                                     ."SET number = number + '$smelters', "
                                     ."subunit = 'smelter' "
                                     ."WHERE struct_id = '$refineinfo[struct_id]' "
                                     ."AND clanid = '$clanid'");
                           db_op_result($query,__LINE__,__FILE__);
                        $query = $db->Execute("UPDATE $dbtables[resources] "
                                     ."SET amount = amount - '$deltametal' "
                                     ."WHERE tribeid = '$goods_tribe' "
                                     ."AND long_name = '$metalinfo[long_name]'");
                            db_op_result($query,__LINE__,__FILE__);
                        $query = $db->Execute("UPDATE $dbtables[resources] "
                                     ."SET amount = amount - '$deltacoal' "
                                     ."WHERE tribeid = '$goods_tribe' "
                                     ."AND long_name = 'Coal'");
                          db_op_result($query,__LINE__,__FILE__);
                        $query = $db->Execute("DELETE FROM $dbtables[activities] "
                                     ."WHERE tribeid = '$tribeid' "
                                     ."AND product = 'smelter'");
                          db_op_result($query,__LINE__,__FILE__);
                       $log_info = "Smelter construction completed Built $smelters smelters using $deltacoal coal and $deltametal $metalinfo[long_name].";

                    }
                    else
                    {
                        $log_info = "Smelter construction failed- You have a refinery but no meetinghouse!";
                    }
                }
                else
                {
                       $log_info = "Smelter construction failed- scheduler found no empty refineries.";
                }
          playerlog($tribe['tribeid'],$tribe['clanid'],'Smelter',$month['count'],$year['count'],$log_info,$dbtables);

                $query = $db->Execute("DELETE FROM $dbtables[activities] "
                             ."WHERE tribeid = '$tribeid' "
                             ."AND product = 'smelter'");
                   db_op_result($query,__LINE__,__FILE__);
            }

            if( $act_do['product'] == 'ovens' )
            {
                $bake = $db->Execute("SELECT * FROM $dbtables[structures] "
                                     ."WHERE clanid = '$clanid' "
                                     ."AND long_name = 'bakery' "
                                     ."AND hex_id = '$hex_id' "
                                     ."AND complete = 'Y' "
                                     ."AND number < 101");
                     db_op_result($bake,__LINE__,__FILE__);
                $bakeinfo = $bake->fields;

                if( !$bake->EOF )
                {
                    $maxoven = 100 - $bakeinfo['number'];

                    if( $has_meethouse > 0 && !$bake->EOF )
                    {
                        $coal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                             ."WHERE tribeid = '$goods_tribe' "
                                             ."AND long_name = 'Coal'");
                               db_op_result($coal,__LINE__,__FILE__);
                        $metal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                              ."WHERE tribeid = '$goods_tribe' "
                                              ."AND long_name = 'Iron' "
                                              ."AND amount > 99");
                              db_op_result($metal,__LINE__,__FILE__);
                        if( $metal->EOF )
                        {
                            $metal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                                  ."WHERE tribeid = '$goods_tribe' "
                                                  ."AND long_name = 'Bronze'");
                              db_op_result($metal,__LINE__,__FILE__);
                        }
                        $coalinfo = $coal->fields;
                        $metalinfo = $metal->fields;
                        $startcoal = $coalinfo['amount'];
                        $startmetal = $metalinfo['amount'];
                        $ovens = 0;
                        while( $ovens < $maxoven && $metalinfo['amount'] > 99 && $coalinfo['amount'] > 199 && $act_do['actives'] > 9 )
                        {
                            $metalinfo['amount'] -= 100;
                            $coalinfo['amount'] -= 200;
                            $act_do['actives'] -= 10;
                            $ovens += 1;
                        }
                        $deltacoal = 0;
                        $deltametal = 0;
                        $deltacoal = $startcoal - $coalinfo['amount'];
                        $deltametal = $startmetal - $metalinfo['amount'];

                        if( $ovens < 0 )
                        {
                            $ovens = 0;
                        }
                        $query = $db->Execute("UPDATE $dbtables[structures] "
                                     ."SET number = number + '$ovens', "
                                     ."subunit = 'ovens' "
                                     ."WHERE struct_id = '$bakeinfo[struct_id]' "
                                     ."AND clanid = '$clanid'");
                            db_op_result($query,__LINE__,__FILE__);
                        $query = $db->Execute("UPDATE $dbtables[resources] "
                                     ."SET amount = amount - '$deltametal' "
                                     ."WHERE tribeid = '$goods_tribe' "
                                     ."AND long_name = '$metalinfo[long_name]'");
                             db_op_result($query,__LINE__,__FILE__);
                        $query = $db->Execute("UPDATE $dbtables[resources] "
                                     ."SET amount = amount - '$deltacoal' "
                                     ."WHERE tribeid = '$goods_tribe' "
                                     ."AND long_name = 'Coal'");
                            db_op_result($query,__LINE__,__FILE__);
                        $query = $db->Execute("DELETE FROM $dbtables[activities] "
                                     ."WHERE tribeid = '$tribeid' "
                                     ."AND product = 'ovens'");
                            db_op_result($query,__LINE__,__FILE__);
   $log_type='UPDATE';
  $logtext = "Engineering: $ovens ovens constructed using $deltacoal coal and $deltametal $metalinfo[long_name].";
  playerlog($tribeid,$clanid,$log_type,$month['count'],$year['count'],$logtext,$dbtables);

                    }
                }
                $query = $db->Execute("DELETE FROM $dbtables[activities] "
                             ."WHERE tribeid = '$tribeid' "
                             ."AND product = 'ovens'");
                   db_op_result($query,__LINE__,__FILE__);
            }

            if( $act_do['product'] == 'stills' )
            {
                $distill = $db->Execute("SELECT * FROM $dbtables[structures] "
                                        ."WHERE clanid = '$clanid' "
                                        ."AND long_name = 'distillery' "
                                        ."AND hex_id = '$hex_id' "
                                        ."AND complete = 'Y' "
                                        ."AND number < 100");
                         db_op_result($distill,__LINE__,__FILE__);
                $distillinfo = $distill->fields;

                if( !$distill->EOF )
                {
                    $maxstill = 100 - $distillinfo['number'];

                    if( $has_meethouse > 0 && !$distill->EOF )
                    {
                        $coal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                             ."WHERE tribeid = '$goods_tribe' "
                                             ."AND long_name = 'Coal'");
                             db_op_result($coal,__LINE__,__FILE__);
                        $metal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                              ."WHERE tribeid = '$goods_tribe' "
                                              ."AND long_name = 'Copper' "
                                              ."AND amount > 99");
                            db_op_result($metal,__LINE__,__FILE__);
                        $coalinfo = $coal->fields;
                        $metalinfo = $metal->fields;
                        $startcoal = $coalinfo['amount'];
                        $startmetal = $metalinfo['amount'];
                        $stills = 0;
                        while( $stills < $maxstill && $metalinfo['amount'] > 99 && $coalinfo['amount'] > 499 && $act_do['actives'] > 9 )
                        {
                            $metalinfo['amount'] -= 100;
                            $coalinfo['amount'] -= 500;
                            $act_do['actives'] -= 10;
                            $stills += 1;
                        }
                        $deltacoal = 0;
                        $deltametal = 0;
                        $deltacoal = $startcoal - $coalinfo['amount'];
                        $deltametal = $startmetal - $metalinfo['amount'];
                        if( $stills < 0 )
                        {
                            $stills = 0;
                        }
                        $query = $db->Execute("UPDATE $dbtables[structures] "
                                     ."SET number = number + '$stills', "
                                     ."subunit = 'stills' "
                                     ."WHERE struct_id = '$distillinfo[struct_id]' "
                                     ."AND clanid = '$clanid'");
                            db_op_result($query,__LINE__,__FILE__);
                        $query = $db->Execute("UPDATE $dbtables[resources] "
                                     ."SET amount = amount - '$deltametal' "
                                     ."WHERE tribeid = '$goods_tribe' "
                                     ."AND long_name = '$metalinfo[long_name]'");
                              db_op_result($query,__LINE__,__FILE__);
                        $query = $db->Execute("UPDATE $dbtables[resources] "
                                     ."SET amount = amount - '$deltacoal' "
                                     ."WHERE tribeid = '$goods_tribe' "
                                     ."AND long_name = 'Coal'");
                           db_op_result($query,__LINE__,__FILE__);
                        $query = $db->Execute("DELETE FROM $dbtables[activities] "
                                     ."WHERE tribeid = '$tribeid' "
                                     ."AND product = 'stills'");
                           db_op_result($query,__LINE__,__FILE__);
     $log_type='UPDATE';
  $logtext = "Engineering: $stills stills constructed using $deltacoal coal and $deltametal $metalinfo[long_name].";
  playerlog($tribeid,$clanid,$log_type,$month['count'],$year['count'],$logtext,$dbtables);

                    }
                }
                $query = $db->Execute("DELETE FROM $dbtables[activities] "
                             ."WHERE tribeid = '$tribeid' "
                             ."AND product = 'stills'");
                    db_op_result($query,__LINE__,__FILE__);
            }


            if( $act_do['product'] == 'apiary' )
            {
                $sec = $db->Execute("SELECT * FROM $dbtables[skills] "
                                    ."WHERE tribeid = '$tribeid' "
                                    ."AND abbr = 'mtl' "
                                    ."AND level > 2");
                    db_op_result($sec,__LINE__,__FILE__);
                $coal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                     ."WHERE tribeid = '$goods_tribe' "
                                     ."AND long_name = 'Coal' "
                                     ."AND amount > 99");
                     db_op_result($coal,__LINE__,__FILE__);
                $mtl = $db->Execute("SELECT * FROM $dbtables[resources] "
                                    ."WHERE tribeid = '$goods_tribe' "
                                    ."AND long_name = 'Iron' "
                                    ."AND amount > 19");
                      db_op_result($mtl,__LINE__,__FILE__);
                if( $mtl->EOF )
                {
                    $mtl = $db->Execute("SELECT * FROM $dbtables[resources] "
                                        ."WHERE tribeid = '$goods_tribe' "
                                        ."AND long_name = 'Bronze' "
                                        ."AND amount > 19");
                       db_op_result($mtl,__LINE__,__FILE__);
                    if( $mtl->EOF )
                    {
                        $mtlbail = true;
                    }
                }
                $soft = $db->Execute("SELECT * FROM $dbtables[products] "
                                     ."WHERE tribeid = '$goods_tribe' "
                                     ."AND long_name = 'cloth' "
                                     ."AND amount > 1");
                    db_op_result($soft,__LINE__,__FILE__);
                $softamount = 2;
                if( $soft->EOF )
                {
                    $soft = $db->Execute("SELECT * FROM $dbtables[products] "
                                         ."WHERE tribeid = '$goods_tribe' "
                                         ."AND long_name = 'leather'"
                                         ."AND amount > 19");
                       db_op_result($soft,__LINE__,__FILE__);
                    if( $soft->EOF )
                    {
                        $softbail = true;
                    }
                    $softamount = 20;
                }

                if( $sec->EOF )
                {
                    $sec = $db->Execute("SELECT * FROM $dbtables[skills] "
                                        ."WHERE tribeid = '$tribeid' "
                                        ."AND abbr = 'wd' "
                                        ."AND level > 3");
                      db_op_result($sec,__LINE__,__FILE__);
                }
                $secinfo = $sec->fields;
                if( $secinfo['abbr'] == 'mtl' )
                {
                    if( !$coal->EOF && !$mtl->EOF && !$soft->EOF )
                    {
                        $metal = true;
                    }
                }
                elseif( $secinfo['abbr'] == 'wd' )
                {
                    if( !$soft->EOF )
                    {
                        $wood = true;
                    }
                }
                if( $wood || $metal )
                {
                    $good = true;
                }
                if( $has_meethouse > 0 && $good && !ISSET($softbail) && !ISSET($mtlbail) )
                {
                    $log = $db->Execute("SELECT * FROM $dbtables[products] "
                                        ."WHERE long_name = 'logs' "
                                        ."AND tribeid = '$goods_tribe'");
                       db_op_result($log,__LINE__,__FILE__);
                    $loginfo = $log->fields;
                    $logs_installed = 0;
                    if( $metal )
                    {
                        while( $loginfo['amount'] > 0 && $act_do['actives'] > 0 && $logs_installed < 100 )
                        {
                            $logs_installed += 2;
                            $act_do['actives'] -= 1;
                            $loginfo['amount'] -= 2;
                        }
                        $struct_points = $logs_installed;
                        $softinfo = $soft->fields;
                        $query = $db->Execute("UPDATE $dbtables[products] "
                                     ."SET amount = amount - $softamount "
                                     ."WHERE tribeid = '$goods_tribe' "
                                     ."AND long_name = '$softinfo[long_name]'");
                             db_op_result($query,__LINE__,__FILE__);
                        $mtlinfo = $mtl->fields;
                        $mtlinfo['amount'] -= 20;
                        $mtlamount = 20;
                        $query = $db->Execute("UPDATE $dbtables[resources] "
                                     ."SET amount = amount - $mtlamount "
                                     ."WHERE tribeid = '$goods_tribe' "
                                     ."AND long_name = '$mtlinfo[long_name]'");
                            db_op_result($query,__LINE__,__FILE__);
                        $coalinfo = $coal->fields;
                        $coalamount = 100;
                        $query = $db->Execute("UPDATE $dbtables[resources] "
                                     ."SET amount = amount - $coalamount "
                                     ."WHERE tribeid = '$goods_tribe' "
                                     ."AND long_name = '$coalinfo[long_name]'");
                            db_op_result($query,__LINE__,__FILE__);
                    }
                    else
                    {
                        while( $loginfo['amount'] > 0 && $act_do['actives'] > 0 && $logs_installed < 160 )
                        {
                            $logs_installed += 2;
                            $act_do['actives'] -= 1;
                            $loginfo['amount'] -= 2;
                        }
                        $struct_points = round($logs_installed * .625);
                        $softinfo = $soft->fields;
                        $query = $db->Execute("UPDATE $dbtables[products] "
                                     ."SET amount = amount - $softamount "
                                     ."WHERE tribeid = '$goods_tribe' "
                                     ."AND long_name = '$softinfo[long_name]'");
                          db_op_result($query,__LINE__,__FILE__);
                    }
                    $apiary = $db->Execute("SELECT * FROM $dbtables[structures] "
                                           ."WHERE long_name = 'apiary' "
                                           ."AND tribeid = '$goods_tribe' "
                                           ."AND complete = 'N' "
                                           ."AND hex_id = '$hex_id'");
                          db_op_result($apiary,__LINE__,__FILE__);
                    if( !$apiary->EOF )
                    {
                        $apiaryinfo = $apiary->fields;
                        if( $struct_points + $apiaryinfo['struct_pts'] >= 100 )
                        {
                            $query = $db->Execute("UPDATE $dbtables[structures] "
                                         ."SET complete = 'Y',"
                                         ."struct_pts = '100',"
                                         ."subunit = 'hives' "
                                         ."WHERE tribeid = '$goods_tribe' "
                                         ."AND hex_id = '$hex_id' "
                                         ."AND long_name = 'apiary'");
                               db_op_result($query,__LINE__,__FILE__);
                        }
                        elseif( $struct_points + $apiaryinfo['struct_pts'] < 100 && $logs_installed > 1 )
                        {
                            $query = $db->Execute("UPDATE $dbtables[structures] "
                                         ."SET struct_pts = struct_pts + '$struct_points' "
                                         ."WHERE tribeid = '$goods_tribe' "
                                         ."AND hex_id = '$hex_id' "
                                         ."AND long_name = 'apiary'");
                              db_op_result($query,__LINE__,__FILE__);
                        }
                    }
                    else
                    {
                        if( $struct_points >= 100 )
                        {
                            $query = $db->Execute("INSERT INTO $dbtables[structures] "
                                         ."VALUES("
                                         ."'',"
                                         ."'apiary',"
                                         ."'Apiary',"
                                         ."'$hex_id',"
                                         ."'$goods_tribe',"
                                         ."'$clanid',"
                                         ."'Y',"
                                         ."'$struct_points',"
                                         ."'100',"
                                         ."'hives',"
                                         ."'',"
                                         ."'N')");
                                 db_op_result($query,__LINE__,__FILE__);
                            $hiv = $db->Execute("SELECT * FROM $dbtables[products] "
                                                ."WHERE long_name = 'hives' "
                                                ."AND tribeid = '$goods_tribe'");
                                 db_op_result($hiv,__LINE__,__FILE__);
                            if( !$hiv->EOF )
                            {
                                $hive = $hiv->fields;
                                if( $hive['amount'] > 19 )
                                {
                                    $query = $db->Execute("UPDATE $dbtables[structures] "
                                                 ."SET number = 20 "
                                                 ."WHERE long_name = 'apiary' "
                                                 ."AND hex_id = '$hex_id' "
                                                 ."AND clanid = '$clanid' "
                                                 ."AND tribeid = '$goods_tribe' "
                                                 ."AND complete = 'Y' "
                                                 ."AND subunit = 'hives'");
                                       db_op_result($query,__LINE__,__FILE__);
                                    $query = $db->Execute("UPDATE $dbtables[products] "
                                                 ."SET amount = amount - 20 "
                                                 ."WHERE tribeid = '$goods_tribe' "
                                                 ."AND long_name = 'hives'");
                                        db_op_result($query,__LINE__,__FILE__);
                                }
                                elseif( $hive['amount'] > 1 && $hive['amount'] < 20 )
                                {
                                    $query = $db->Execute("UPDATE $dbtables[structures] "
                                                 ."SET number = $hive[amount] "
                                                 ."WHERE long_name = 'apiary' "
                                                 ."AND hex_id = '$hex_id' "
                                                 ."AND clanid = '$clanid' "
                                                 ."AND tribeid = '$goods_tribe' "
                                                 ."AND complete = 'Y' "
                                                 ."AND subunit = 'hives'");
                                      db_op_result($query,__LINE__,__FILE__);
                                    $query = $db->Execute("UPDATE $dbtables[products] "
                                                 ."SET amount = 0 "
                                                 ."WHERE tribeid = '$goods_tribe' "
                                                 ."AND long_name = 'hives'");
                                      db_op_result($query,__LINE__,__FILE__);
                                }
                            }
                        }
                        elseif( $struct_points < 100 && $struct_points > 1 )
                        {
                            $query = $db->Execute("INSERT INTO $dbtables[structures] "
                                         ."VALUES("
                                         ."'',"
                                         ."'apiary',"
                                         ."'Apiary',"
                                         ."'$hex_id',"
                                         ."'$goods_tribe',"
                                         ."'$clanid',"
                                         ."'N',"
                                         ."'$struct_points',"
                                         ."'100',"
                                         ."'hives',"
                                         ."'',"
                                         ."'N')");
                            db_op_result($query,__LINE__,__FILE__);
                        }
                    }
                }
                $query = $db->Execute("DELETE FROM $dbtables[activities] "
                             ."WHERE tribeid = '$tribeid' "
                             ."AND product = 'apiary'");
                    db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("UPDATE $dbtables[products] "
                             ."SET amount = amount - '$logs_installed' "
                             ."WHERE tribeid = '$goods_tribe' "
                             ."AND long_name = 'logs'");
                    db_op_result($query,__LINE__,__FILE__);
   $log_type='UPDATE';
  $logtext = "Engineering: Apiary Construction using $logs_installed logs $softamount $softinfo[proper] $mtlamount $mtlinfo[long_name] $coalamount $coalinfo[long_name] installed.";
  playerlog($tribeid,$clanid,$log_type,$month['count'],$year['count'],$logtext,$dbtables);

            }

            if( $act_do['product'] == 'bakery' )
            {
                if( $has_meethouse > 0 )
                {
                    $log = $db->Execute("SELECT * FROM $dbtables[products] "
                                        ."WHERE long_name = 'logs' "
                                        ."AND tribeid = '$goods_tribe'");
                       db_op_result($log,__LINE__,__FILE__);
                    $loginfo = $log->fields;
                    $logs_installed = 0;
                    while( $loginfo['amount'] > 1 && $act_do['actives'] > 0 && $logs_installed < 40)
                    {
                        $logs_installed += 2;
                        $act_do['actives'] -= 1;
                        $loginfo['amount'] -= 2;
                    }
                    $bakery = $db->Execute("SELECT * FROM $dbtables[structures] "
                                           ."WHERE long_name = 'bakery' "
                                           ."AND tribeid = '$goods_tribe' "
                                           ."AND complete = 'N' "
                                           ."AND hex_id = '$hex_id'");
                          db_op_result($bakery,__LINE__,__FILE__);
                    if( !$bakery->EOF )
                    {
                        $bakeryinfo = $bakery->fields;
                        if( $logs_installed + $bakeryinfo['struct_pts'] >= 40 )
                        {
                            $query = $db->Execute("UPDATE $dbtables[structures] "
                                         ."SET complete = 'Y',"
                                         ."struct_pts = '40',"
                                         ."subunit = 'ovens' "
                                         ."WHERE tribeid = '$goods_tribe' "
                                         ."AND hex_id = '$hex_id' "
                                         ."AND long_name = 'bakery'");
                               db_op_result($query,__LINE__,__FILE__);
                        }
                        elseif( $logs_installed + $bakeryinfo['struct_pts'] < 40 && $logs_installed > 1 )
                        {
                            $query = $db->Execute("UPDATE $dbtables[structures] "
                                         ."SET struct_pts = struct_pts + '$logs_installed' "
                                         ."WHERE tribeid = '$goods_tribe' "
                                         ."AND hex_id = '$hex_id' "
                                         ."AND long_name = 'bakery'");
                               db_op_result($query,__LINE__,__FILE__);
                        }
                    }
                    else
                    {
                        if( $logs_installed >= 40 )
                        {
                            $query = $db->Execute("INSERT INTO $dbtables[structures] "
                                         ."VALUES("
                                         ."'',"
                                         ."'bakery',"
                                         ."'Bakery',"
                                         ."'$hex_id',"
                                         ."'$goods_tribe',"
                                         ."'$clanid',"
                                         ."'Y',"
                                         ."'$logs_installed',"
                                         ."'40',"
                                         ."'ovens',"
                                         ."'',"
                                         ."'N')");
                              db_op_result($query,__LINE__,__FILE__);
                        }
                        elseif( $logs_installed < 40 && $logs_installed > 1 )
                        {
                            $query = $db->Execute("INSERT INTO $dbtables[structures] "
                                         ."VALUES("
                                         ."'',"
                                         ."'bakery',"
                                         ."'Bakery',"
                                         ."'$hex_id',"
                                         ."'$goods_tribe',"
                                         ."'$clanid',"
                                         ."'N',"
                                         ."'$logs_installed',"
                                         ."'40',"
                                         ."'ovens',"
                                         ."'',"
                                         ."'N')");
                             db_op_result($query,__LINE__,__FILE__);
                        }
                    }
                }
                $query = $db->Execute("DELETE FROM $dbtables[activities] "
                             ."WHERE tribeid = '$tribeid' "
                             ."AND product = 'bakery'");
                    db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("UPDATE $dbtables[products] "
                             ."SET amount = amount - '$logs_installed' "
                             ."WHERE tribeid = '$goods_tribe' "
                             ."AND long_name = 'logs'");
                   db_op_result($query,__LINE__,__FILE__);
 $log_type='UPDATE';
  $logtext = "Engineering: Bakery Construction using $logs_installed logs.";
  playerlog($tribeid,$clanid,$log_type,$month['count'],$year['count'],$logtext,$dbtables);

            }

            if( $act_do['product'] == 'distillery' )
            {
                if( $has_meethouse > 0 )
                {
                    $log = $db->Execute("SELECT * FROM $dbtables[products] "
                                        ."WHERE long_name = 'logs' "
                                        ."AND tribeid = '$goods_tribe'");
                        db_op_result($log,__LINE__,__FILE__);
                    $loginfo = $log->fields;
                    $logs_installed = 0;
                    while( $loginfo['amount'] > 1 && $act_do['actives'] > 0 && $logs_installed < 80)
                    {
                        $logs_installed += 2;
                        $act_do['actives'] -= 1;
                        $loginfo['amount'] -= 2;
                    }
                    $distillery = $db->Execute("SELECT * FROM $dbtables[structures] "
                                               ."WHERE long_name = 'distillery' "
                                               ."AND tribeid = '$goods_tribe' "
                                               ."AND complete = 'N' "
                                               ."AND hex_id = '$hex_id'");
                           db_op_result($distillery,__LINE__,__FILE__);
                    if( !$distillery->EOF )
                    {
                        $distilleryinfo = $distillery->fields;
                        if( $logs_installed + $distilleryinfo['struct_pts'] >= 80 )
                        {
                            $query = $db->Execute("UPDATE $dbtables[structures] "
                                         ."SET complete = 'Y',"
                                         ."struct_pts = '80',"
                                         ."subunit = 'stills' "
                                         ."WHERE tribeid = '$goods_tribe' "
                                         ."AND hex_id = '$hex_id' "
                                         ."AND long_name = 'distillery'");
                               db_op_result($query,__LINE__,__FILE__);
                        }
                        elseif( $logs_installed + $distilleryinfo['struct_pts'] < 80 && $logs_installed > 1 )
                        {
                            $query = $db->Execute("UPDATE $dbtables[structures] "
                                         ."SET struct_pts = struct_pts + '$logs_installed' "
                                         ."WHERE tribeid = '$goods_tribe' "
                                         ."AND hex_id = '$hex_id' "
                                         ."AND long_name = 'distillery'");
                              db_op_result($query,__LINE__,__FILE__);
                        }
                    }
                    else
                    {
                        if( $logs_installed >= 80 )
                        {
                            $query = $db->Execute("INSERT INTO $dbtables[structures] "
                                         ."VALUES("
                                         ."'',"
                                         ."'distillery',"
                                         ."'Distillery',"
                                         ."'$hex_id',"
                                         ."'$goods_tribe',"
                                         ."'$clanid',"
                                         ."'Y',"
                                         ."'$logs_installed',"
                                         ."'80',"
                                         ."'stills',"
                                         ."'',"
                                         ."'N')");
                              db_op_result($query,__LINE__,__FILE__);
                        }
                        elseif( $logs_installed < 80 && $logs_installed > 1 )
                        {
                            $query = $db->Execute("INSERT INTO $dbtables[structures] "
                                         ."VALUES("
                                         ."'',"
                                         ."'distillery',"
                                         ."'Distillery',"
                                         ."'$hex_id',"
                                         ."'$goods_tribe',"
                                         ."'$clanid',"
                                         ."'N',"
                                         ."'$logs_installed',"
                                         ."'80',"
                                         ."'stills',"
                                         ."'',"
                                         ."'N')");
                            db_op_result($query,__LINE__,__FILE__);
                        }
                    }
                }
                $query = $db->Execute("DELETE FROM $dbtables[activities] "
                             ."WHERE tribeid = '$tribeid' "
                             ."AND product = 'distillery'");
                     db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("UPDATE $dbtables[products] "
                             ."SET amount = amount - '$logs_installed' "
                             ."WHERE tribeid = '$goods_tribe' "
                             ."AND long_name = 'logs'");
                   db_op_result($query,__LINE__,__FILE__);
  $log_type='UPDATE';
  $logtext = "Engineering: Distillery Construction using $logs_installed logs.";
  playerlog($tribeid,$clanid,$log_type,$month['count'],$year['count'],$logtext,$dbtables);

            }
            if( $act_do['product'] == 'brickworks' )
            {
                if( $has_meethouse > 0 )
                {
                    $log = $db->Execute("SELECT * FROM $dbtables[products] "
                                        ."WHERE long_name = 'logs' "
                                        ."AND tribeid = '$goods_tribe'");
                         db_op_result($log,__LINE__,__FILE__);
                    $loginfo = $log->fields;
                    $logs_installed = 0;
                    while( $loginfo['amount'] > 0 && $act_do['actives'] > 0 && $logs_installed < 100 )
                    {
                        $logs_installed += 2;
                        $act_do['actives'] -= 1;
                        $loginfo['amount'] -= 2;
                    }
                    $bricked = $db->Execute("SELECT * FROM $dbtables[structures] "
                                            ."WHERE long_name = 'brickworks' "
                                            ."AND tribeid = '$goods_tribe' "
                                            ."AND complete = 'N' "
                                            ."AND hex_id = '$hex_id'");
                        db_op_result($bricked,__LINE__,__FILE__);
                    if( !$bricked->EOF )
                    {
                        $brickinfo = $bricked->fields;
                        if( $logs_installed + $brickinfo['struct_pts'] >= 100 )
                        {
                            $query = $db->Execute("UPDATE $dbtables[structures] "
                                         ."SET complete = 'Y',"
                                         ."struct_pts = '100',"
                                         ."subunit = 'ovens' "
                                         ."WHERE tribeid = '$goods_tribe' "
                                         ."AND hex_id = '$hex_id' "
                                         ."AND long_name = 'brickworks'");
                               db_op_result($query,__LINE__,__FILE__);
                        }
                        elseif( $logs_installed + $brickinfo['struct_pts'] < 100 && $logs_installed > 1 )
                        {
                            $query = $db->Execute("UPDATE $dbtables[structures] "
                                         ."SET struct_pts = struct_pts + '$logs_installed' "
                                         ."WHERE tribeid = '$goods_tribe' "
                                         ."AND hex_id = '$hex_id' "
                                         ."AND long_name = 'brickworks'");
                             db_op_result($query,__LINE__,__FILE__);
                        }
                    }
                    else
                    {
                        if( $logs_installed >= 100 )
                        {
                            $query = $db->Execute("INSERT INTO $dbtables[structures] "
                                         ."VALUES("
                                         ."'',"
                                         ."'brickworks',"
                                         ."'Brickworks',"
                                         ."'$hex_id',"
                                         ."'$goods_tribe',"
                                         ."'$clanid',"
                                         ."'Y',"
                                         ."'$logs_installed',"
                                         ."'100',"
                                         ."'ovens',"
                                         ."'',"
                                         ."'N')");
                              db_op_result($query,__LINE__,__FILE__);
                        }
                        elseif( $logs_installed < 100 && $logs_installed > 1 )
                        {
                            $query = $db->Execute("INSERT INTO $dbtables[structures] "
                                         ."VALUES("
                                         ."'',"
                                         ."'brickworks',"
                                         ."'Brickworks',"
                                         ."'$hex_id',"
                                         ."'$goods_tribe',"
                                         ."'$clanid',"
                                         ."'N',"
                                         ."'$logs_installed',"
                                         ."'100',"
                                         ."'ovens',"
                                         ."'',"
                                         ."'N')");
                              db_op_result($query,__LINE__,__FILE__);
                        }

                    }
                }
                $query = $db->Execute("DELETE FROM $dbtables[activities] "
                             ."WHERE tribeid = '$tribeid' "
                             ."AND product = '$act_do[product]'");
                   db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("UPDATE $dbtables[products] "
                             ."SET amount = amount - '$logs_installed' "
                             ."WHERE tribeid = '$goods_tribe' "
                             ."AND long_name = 'logs'");
                   db_op_result($query,__LINE__,__FILE__);
     $log_type='UPDATE';
  $logtext = "Engineering: Brickworks Construction using $logs_installed logs.";
  playerlog($tribeid,$clanid,$log_type,$month['count'],$year['count'],$logtext,$dbtables);

            }

            if( $act_do['product'] == 'brickworkoven' )
            {
                $bricked = $db->Execute("SELECT * FROM $dbtables[structures] "
                                        ."WHERE clanid = '$clanid' "
                                        ."AND long_name = 'brickworks' "
                                        ."AND hex_id = '$hex_id' "
                                        ."AND complete = 'Y' "
                                        ."AND number < 100");
                    db_op_result($bricked,__LINE__,__FILE__);
                $brickinfo = $bricked->fields;

                if( !$bricked->EOF )
                {
                    $maxoven = 100 - $brickinfo['number'];

                    if( $has_meethouse > 0 && !$bricked->EOF )
                    {
                        $coal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                             ."WHERE tribeid = '$goods_tribe' "
                                             ."AND long_name = 'Coal'");
                         db_op_result($coal,__LINE__,__FILE__);
                        $metal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                              ."WHERE tribeid = '$goods_tribe' "
                                              ."AND long_name = 'Iron' "
                                              ."AND amount > 3");
                           db_op_result($metal,__LINE__,__FILE__);
                        if( $metal->EOF )
                        {
                            $metal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                                  ."WHERE tribeid = '$goods_tribe' "
                                                  ."AND long_name = 'Bronze'");
                              db_op_result($metal,__LINE__,__FILE__);
                        }
                        $coalinfo = $coal->fields;
                        $metalinfo = $metal->fields;
                        $startcoal = $coalinfo['amount'];
                        $startmetal = $metalinfo['amount'];
                        $ovens = 0;

                        while( $ovens < $maxoven && $metalinfo['amount'] > 3 && $coalinfo['amount'] > 19 && $act_do['actives'] > 0 )
                        {
                            $metalinfo['amount'] -= 4;
                            $coalinfo['amount'] -= 20;
                            $act_do['actives'] -= 1;
                            $ovens += 1;
                        }
                        $deltacoal = 0;
                        $deltametal = 0;
                        $deltacoal = $startcoal - $coalinfo['amount'];
                        $deltametal = $startmetal - $metalinfo['amount'];

                        if( $ovens < 0 )
                        {
                            $ovens = 0;
                        }
                        $query = $db->Execute("UPDATE $dbtables[structures] "
                                     ."SET number = number + '$ovens', "
                                     ."subunit = 'ovens' "
                                     ."WHERE struct_id = '$brickinfo[struct_id]' "
                                     ."AND clanid = '$clanid'");
                            db_op_result($query,__LINE__,__FILE__);
                        $query = $db->Execute("UPDATE $dbtables[resources] "
                                     ."SET amount = amount - '$deltametal' "
                                     ."WHERE tribeid = '$goods_tribe' "
                                     ."AND long_name = '$metalinfo[long_name]'");
                             db_op_result($query,__LINE__,__FILE__);
                        $query = $db->Execute("UPDATE $dbtables[resources] "
                                     ."SET amount = amount - '$deltacoal' "
                                     ."WHERE tribeid = '$goods_tribe' "
                                     ."AND long_name = 'Coal'");
                           db_op_result($query,__LINE__,__FILE__);
                        $query = $db->Execute("DELETE FROM $dbtables[activities] "
                                     ."WHERE tribeid = '$tribeid' "
                                     ."AND product = '$act_do[product]'");
                           db_op_result($query,__LINE__,__FILE__);
  $log_type='UPDATE';
  $logtext = "Engineering: $ovens Brickworking Ovens constructed using $deltacoal coal and $deltametal $metalinfo[long_name].";
  playerlog($tribeid,$clanid,$log_type,$month['count'],$year['count'],$logtext,$dbtables);

                    }
                }
                $query = $db->Execute("DELETE FROM $dbtables[activities] "
                             ."WHERE tribeid = '$tribeid' "
                             ."AND product = '$act_do[product]'");
                  db_op_result($query,__LINE__,__FILE__);
            }

            if( $act_do['product'] == 'charhouse' )
            {
                if( $has_meethouse > 0 )
                {
                    $log = $db->Execute("SELECT * FROM $dbtables[products] "
                                        ."WHERE long_name = 'logs' "
                                        ."AND tribeid = '$goods_tribe'");
                       db_op_result($log,__LINE__,__FILE__);
                    $loginfo = $log->fields;
                    $logs_installed = 0;
                    while( $loginfo['amount'] > 0 && $act_do['actives'] > 0 && $logs_installed < 100 )
                    {
                        $logs_installed += 2;
                        $act_do['actives'] -= 1;
                        $loginfo['amount'] -= 2;
                    }
                    $charred = $db->Execute("SELECT * FROM $dbtables[structures] "
                                            ."WHERE long_name = 'charhouse' "
                                            ."AND tribeid = '$goods_tribe' "
                                            ."AND complete = 'N' "
                                            ."AND hex_id = '$hex_id'");
                         db_op_result($charred,__LINE__,__FILE__);
                    if( !$charred->EOF )
                    {
                        $charinfo = $charred->fields;
                        if( $logs_installed + $charinfo['struct_pts'] >= 100 )
                        {
                            $query = $db->Execute("UPDATE $dbtables[structures] "
                                         ."SET complete = 'Y',"
                                         ."struct_pts = '100',"
                                         ."subunit = 'burners' "
                                         ."WHERE tribeid = '$goods_tribe' "
                                         ."AND hex_id = '$hex_id' "
                                         ."AND long_name = 'charhouse'");
                              db_op_result($query,__LINE__,__FILE__);
                        }
                        elseif( $logs_installed + $charinfo['struct_pts'] < 100 && $logs_installed > 1 )
                        {
                            $query = $db->Execute("UPDATE $dbtables[structures] "
                                         ."SET struct_pts = struct_pts + '$logs_installed' "
                                         ."WHERE tribeid = '$goods_tribe' "
                                         ."AND hex_id = '$hex_id' "
                                         ."AND long_name = 'charhouse'");
                             db_op_result($query,__LINE__,__FILE__);
                        }
                    }
                    else
                    {
                        if( $logs_installed >= 100 )
                        {
                            $query = $db->Execute("INSERT INTO $dbtables[structures] "
                                         ."VALUES("
                                         ."'',"
                                         ."'charhouse',"
                                         ."'Char House',"
                                         ."'$hex_id',"
                                         ."'$goods_tribe',"
                                         ."'$clanid',"
                                         ."'Y',"
                                         ."'$logs_installed',"
                                         ."'100',"
                                         ."'burners',"
                                         ."'',"
                                         ."'N')");
                            db_op_result($query,__LINE__,__FILE__);
                        }
                        elseif( $logs_installed < 100 && $logs_installed > 1 )
                        {
                            $query = $db->Execute("INSERT INTO $dbtables[structures] "
                                         ."VALUES("
                                         ."'',"
                                         ."'charhouse',"
                                         ."'Char House',"
                                         ."'$hex_id',"
                                         ."'$goods_tribe',"
                                         ."'$clanid',"
                                         ."'N',"
                                         ."'$logs_installed',"
                                         ."'100',"
                                         ."'burners',"
                                         ."'',"
                                         ."'N')");
                              db_op_result($query,__LINE__,__FILE__);
                        }

                    }
                }
                $query = $db->Execute("DELETE FROM $dbtables[activities] "
                             ."WHERE tribeid = '$tribeid' "
                             ."AND product = '$act_do[product]'");
                   db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("UPDATE $dbtables[products] "
                             ."SET amount = amount - '$logs_installed' "
                             ."WHERE tribeid = '$goods_tribe' "
                             ."AND long_name = 'logs'");
                  db_op_result($query,__LINE__,__FILE__);
  $log_type='UPDATE';
  $logtext = "Engineering: Char House Constructed using $logs_installed logs.";
  playerlog($tribeid,$clanid,$log_type,$month['count'],$year['count'],$logtext,$dbtables);

            }

            if( $act_do['product'] == 'burner' )
            {
                $charred = $db->Execute("SELECT * FROM $dbtables[structures] "
                                        ."WHERE clanid = '$clanid' "
                                        ."AND long_name = 'charhouse' "
                                        ."AND hex_id = '$hex_id' "
                                        ."AND complete = 'Y' "
                                        ."AND number < 100");
                  db_op_result($charred,__LINE__,__FILE__);
                $charinfo = $charred->fields;

                if( !$charred->EOF )
                {
                    $maxburn = 100 - $charinfo['number'];

                    if( $has_meethouse > 0 && !$charred->EOF )
                    {
                        $coal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                             ."WHERE tribeid = '$goods_tribe' "
                                             ."AND long_name = 'Coal'");
                          db_op_result($coal,__LINE__,__FILE__);
                        $metal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                              ."WHERE tribeid = '$goods_tribe' "
                                              ."AND long_name = 'Iron' "
                                              ."AND amount > 3");
                           db_op_result($metal,__LINE__,__FILE__);
                        if( $metal->EOF )
                        {
                            $metal = $db->Execute("SELECT * FROM $dbtables[resources] "
                                                  ."WHERE tribeid = '$goods_tribe' "
                                                  ."AND long_name = 'Bronze'");
                              db_op_result($metal,__LINE__,__FILE__);
                        }
                        $coalinfo = $coal->fields;
                        $metalinfo = $metal->fields;
                        $startcoal = $coalinfo['amount'];
                        $startmetal = $metalinfo['amount'];
                        $burners = 0;

                        while( $burners < $maxburn && $metalinfo['amount'] > 3 && $coalinfo['amount'] > 19 && $act_do['actives'] > 0 )
                        {
                            $metalinfo['amount'] -= 4;
                            $coalinfo['amount'] -= 20;
                            $act_do['actives'] -= 1;
                            $burners += 1;
                        }
                        $deltacoal = 0;
                        $deltametal = 0;
                        $deltacoal = $startcoal - $coalinfo['amount'];
                        $deltametal = $startmetal - $metalinfo['amount'];

                        if( $burners < 0 )
                        {
                            $burners = 0;
                        }
                        $query = $db->Execute("UPDATE $dbtables[structures] "
                                     ."SET number = number + '$burners', "
                                     ."subunit = 'burners' "
                                     ."WHERE struct_id = '$charinfo[struct_id]' "
                                     ."AND clanid = '$clanid'");
                           db_op_result($query,__LINE__,__FILE__);
                        $query = $db->Execute("UPDATE $dbtables[resources] "
                                     ."SET amount = amount - '$deltametal' "
                                     ."WHERE tribeid = '$goods_tribe' "
                                     ."AND long_name = '$metalinfo[long_name]'");
                            db_op_result($query,__LINE__,__FILE__);
                        $query = $db->Execute("UPDATE $dbtables[resources] "
                                     ."SET amount = amount - '$deltacoal' "
                                     ."WHERE tribeid = '$goods_tribe' "
                                     ."AND long_name = 'Coal'");
                           db_op_result($query,__LINE__,__FILE__);
                        $query = $db->Execute("DELETE FROM $dbtables[activities] "
                                     ."WHERE tribeid = '$tribeid' "
                                     ."AND product = '$act_do[product]'");
                          db_op_result($query,__LINE__,__FILE__);
    $log_type='UPDATE';
  $logtext = "Engineering: $burners Burners constructed using $deltacoal coal and $deltametal $metalinfo[long_name].";
  playerlog($tribeid,$clanid,$log_type,$month['count'],$year['count'],$logtext,$dbtables);

                    }
                }
                $query = $db->Execute("DELETE FROM $dbtables[activities] "
                             ."WHERE tribeid = '$tribeid' "
                             ."AND product = '$act_do[product]'");
                  db_op_result($query,__LINE__,__FILE__);
            }


        $act->MoveNext();
    }


?>