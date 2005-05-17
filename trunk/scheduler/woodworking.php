<?php
$pos = (strpos($_SERVER['PHP_SELF'], "/woodworking.php"));
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

    $act = $db->Execute("SELECT * FROM $dbtables[activities] "
                       ."WHERE tribeid = '$tribe[tribeid]' "
                       ."AND skill_abbr = 'wd'");
      db_op_result($act,__LINE__,__FILE__);

    while( !$act->EOF )
    {
        $act_do = $act->fields;

        if( $act_do['product'] == 'club' )
        {
            $log = $db->Execute("SELECT * FROM $dbtables[products] "
                               ."WHERE tribeid = '$tribe[goods_tribe]' "
                               ."AND long_name = 'logs'");
                db_op_result($log,__LINE__,__FILE__);
            $loginfo = $log->fields;
            $startlog = $loginfo['amount'];

            $club = 0;
            while( $act_do['actives'] > 0 && $loginfo['amount'] > 0 )
            {
                $club += 4;
                $act_do['actives'] -= 1;
                $loginfo['amount'] -= 1;
            }
            $logdelta = $startlog - $loginfo['amount'];
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount - $logdelta "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'logs'");
             db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + $club "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'club'");
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
                        ."'Woodworking: $club Clubs made using $logdelta logs.')");
              db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("DELETE FROM $dbtables[activities] "
                        ."WHERE skill_abbr = 'wd' "
                        ."AND product = '$act_do[product]' "
                        ."AND tribeid = '$tribe[tribeid]'");
              db_op_result($query,__LINE__,__FILE__);
        }

        if( $act_do['product'] == 'frame' )
        {
            $log = $db->Execute("SELECT * FROM $dbtables[products] "
                               ."WHERE tribeid = '$tribe[goods_tribe]' "
                               ."AND long_name = 'logs'");
               db_op_result($log,__LINE__,__FILE__);
            $loginfo = $log->fields;
            $startlog = $loginfo['amount'];

            $frame = 0;
            while( $act_do['actives'] > 0 && $loginfo['amount'] > 0 )
            {
                $frame += 2;
                $loginfo['amount'] -= 1;
                $act_do['actives'] -= 1;
            }
            $logdelta = $startlog - $loginfo['amount'];
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount - $logdelta "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'logs'");
             db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + $frame "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'frame'");
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
                        ."'Woodworking: $frame Frames made using $logdelta logs.')");
              db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("DELETE FROM $dbtables[activities] "
                        ."WHERE skill_abbr = 'wd' "
                        ."AND product = '$act_do[product]' "
                        ."AND tribeid = '$tribe[tribeid]'");
              db_op_result($query,__LINE__,__FILE__);
        }

        if( $act_do['product'] == 'rake' )
        {
            $log = $db->Execute("SELECT * FROM $dbtables[products] "
                               ."WHERE tribeid = '$tribe[goods_tribe]' "
                               ."AND long_name = 'logs'");
                db_op_result($log,__LINE__,__FILE__);
            $loginfo = $log->fields;
            $startlog = $loginfo['amount'];

            $rake = 0;
            while( $act_do['actives'] > 0 && $loginfo['amount'] > 0 )
            {
                $rake += 1;
                $loginfo['amount'] -= 1;
                $act_do['actives'] -= 1;
            }
            $logdelta = $startlog - $loginfo['amount'];
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount - $logdelta "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'logs'");
             db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + $rake "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'rake'");
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
                        ."'Woodworking: $rake Rakes made using $logdelta logs.')");
             db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("DELETE FROM $dbtables[activities] "
                        ."WHERE skill_abbr = 'wd' "
                        ."AND product = '$act_do[product]' "
                        ."AND tribeid = '$tribe[tribeid]'");
               db_op_result($query,__LINE__,__FILE__);
        }

        if( $act_do['product'] == 'wagon' )
        {
            $log = $db->Execute("SELECT * FROM $dbtables[products] "
                               ."WHERE tribeid = '$tribe[goods_tribe]' "
                               ."AND long_name = 'logs'");
                 db_op_result($log,__LINE__,__FILE__);
            $loginfo = $log->fields;
            $startlog = $loginfo['amount'];
            $wagon = 0;
            while( $act_do['actives'] > 9 && $loginfo['amount'] > 5 )
            {
                $wagon += 1;
                $loginfo['amount'] -= 6;
                $act_do['actives'] -= 10;
            }
            $logdelta = $startlog - $loginfo['amount'];
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount - $logdelta "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'logs'");
              db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + $wagon "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'wagon'");
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
                        ."'Woodworking: $wagon Wagons made using $logdelta logs.')");
             db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("DELETE FROM $dbtables[activities] "
                        ."WHERE skill_abbr = 'wd' "
                        ."AND product = '$act_do[product]' "
                        ."AND tribeid = '$tribe[tribeid]'");
              db_op_result($query,__LINE__,__FILE__);
        }

        if( $act_do['product'] == 'canoe' )
        {
            $log = $db->Execute("SELECT * FROM $dbtables[products] "
                               ."WHERE tribeid = '$tribe[goods_tribe]' "
                               ."AND long_name = 'logs'");
               db_op_result($log,__LINE__,__FILE__);
            $loginfo = $log->fields;
            $startlog = $loginfo['amount'];
            $canoe = 0;
            while( $act_do['actives'] > 9 && $loginfo['amount'] > 1 )
            {
                $canoe += 1;
                $loginfo['amount'] -= 2;
                $act_do['actives'] -= 10;
            }
            $logdelta = $startlog - $loginfo['amount'];
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount - $logdelta "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'logs'");
              db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + $canoe "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'canoe'");
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
                        ."'Woodworking: $canoe Canoes made using $logdelta logs.')");
             db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("DELETE FROM $dbtables[activities] "
                        ."WHERE skill_abbr = 'wd' "
                        ."AND product = '$act_do[product]' "
                        ."AND tribeid = '$tribe[tribeid]'");
              db_op_result($query,__LINE__,__FILE__);
        }

        if( $act_do['product'] == 'structure' )
        {
            $log = $db->Execute("SELECT * FROM $dbtables[products] "
                               ."WHERE tribeid = '$tribe[goods_tribe]' "
                               ."AND long_name = 'logs'");
                 db_op_result($log,__LINE__,__FILE__);
            $loginfo = $log->fields;
            $startlog = $loginfo['amount'];
            $structure = 0;
            while( $act_do['actives'] > 4 && $loginfo['amount'] > 1 )
            {
                $structure += 1;
                $act_do['actives'] -= 5;
                $loginfo['amount'] -= 2;
            }
            $logdelta = $startlog - $loginfo['amount'];
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount - $logdelta "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'logs'");
             db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + $structure "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'structure'");
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
                        ."'Woodworking: $structure Structures made using $logdelta logs.')");
              db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("DELETE FROM $dbtables[activities] "
                        ."WHERE skill_abbr = 'wd' "
                        ."AND product = '$act_do[product]' "
                        ."AND tribeid = '$tribe[tribeid]'");
               db_op_result($query,__LINE__,__FILE__);
        }

        if( $act_do['product'] == 'totem' )
        {
            $log = $db->Execute("SELECT * FROM $dbtables[products] "
                               ."WHERE tribeid = '$tribe[goods_tribe]' "
                               ."AND long_name = 'logs'");
               db_op_result($log,__LINE__,__FILE__);
            $loginfo = $log->fields;
            $startlog = $loginfo['amount'];
            $totem = 0;
            while( $act_do['actives'] > 9 && $loginfo['amount'] > 11 )
            {
                $totem += 1;
                $act_do['actives'] -= 10;
                $loginfo['amount'] -= 12;
            }
            $logdelta = $startlog - $loginfo['amount'];
            $here = $db->Execute("SELECT * FROM $dbtables[products] "
                                ."WHERE long_name = 'totem' "
                                ."AND tribeid = '$tribe[tribeid]'");
                  db_op_result($here,__LINE__,__FILE__);
            $hereinfo = $here->fields;
            if( $hereinfo['amount'] < 1 && $totem > 0 )
            {
                $query = $db->Execute("UPDATE $dbtables[tribes] "
                            ."SET morale = morale + .04 "
                            ."WHERE tribeid = '$tribe[tribeid]'");
                  db_op_result($query,__LINE__,__FILE__);
            }
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount - $logdelta "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'logs'");
                db_op_result($query,__LINE__,__FILE__);
            ////////does not go into the goods tribe!//////////////
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + $totem "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND long_name = 'totem'");
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
                        ."'Woodworking: $totem Totem made using $logdelta logs.')");
                db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("DELETE FROM $dbtables[activities] "
                        ."WHERE skill_abbr = 'wd' "
                        ."AND product = '$act_do[product]' "
                        ."AND tribeid = '$tribe[tribeid]'");
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
