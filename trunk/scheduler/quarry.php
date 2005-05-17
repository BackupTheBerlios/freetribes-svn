<?php
$pos = (strpos($_SERVER['PHP_SELF'], "/mysqlt-common.php"));
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
                       ."AND skill_abbr = 'qry'");
    db_op_result($act,__LINE__,__FILE__);
    while( !$act->EOF )
    {
        $act_do = $act->fields;

        if( $act_do['skill_abbr'] == 'qry' )
        {
            $hex = $db->Execute("SELECT * FROM $dbtables[hexes] "
                               ."WHERE hex_id = '$tribe[hex_id]'");
             db_op_result($hex,__LINE__,__FILE__);
            $hexinfo = $hex->fields;
            if( $hexinfo['terrain'] == 'gh' | $hexinfo['terrain'] == 'dh' | $hexinfo['terrain'] == 'ch' | $hexinfo['terrain'] == 'jh' | $hexinfo['terrain'] == 'lcm' | $hexinfo['terrain'] == 'ljm' )
            {
                $qryskill = $db->Execute("SELECT * FROM $dbtables[skills] "
                                        ."WHERE tribeid = '$tribe[tribeid]' "
                                        ."AND abbr = 'qry'");
                 db_op_result($qryskill,__LINE__,__FILE__);
                $qryinfo = $qryskill->fields;
                $max_quarry = $qryinfo['level'] * 10;
                if( $act_do['actives'] > $max_quarry )
                {
                    $act_do['actives'] = $max_quarry;
                }
                $shov = $db->Execute("SELECT * FROM $dbtables[products] "
                                    ."WHERE tribeid = '$tribe[goods_tribe]' "
                                    ."AND amount > 0 "
                                    ."AND long_name = 'shovel'");
                db_op_result($shov,__LINE__,__FILE__);
                $pick = $db->Execute("SELECT * FROM $dbtables[products] "
                                    ."WHERE tribeid = '$tribe[goods_tribe]' "
                                    ."AND amount > 0 "
                                    ."AND long_name = 'picks'");
                db_op_result($pick,__LINE__,__FILE__);
                $mattock = $db->Execute("SELECT * FROM $dbtables[products] "
                                       ."WHERE tribeid = '$tribe[goods_tribe]' "
                                       ."AND amount > 0 "
                                       ."AND long_name = 'mattock'");
                  db_op_result($mattock,__LINE__,__FILE__);

                if( !$shov->EOF )
                {
                    $shovinfo = $shov->fields;
                }
                if( !$pick->EOF )
                {
                    $pickinfo = $pick->fields;
                }
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

                if( $pickinfo['amount'] > ($act_do['actives'] - $shovinfo['amount']) )
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

                if( $mattockinfo['amount'] > ($act_do['actives'] - $shovinfo['amount'] - $pickinfo['amount']) )
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
                $startactives = $act_do['actives'];
                $shov_bonus = round($shovinfo['amount'] * .5);
                $pick_bonus = round($pickinfo['amount'] * .5);
                $mattock_bonus = $mattockinfo['amount'];
                $act_do['actives'] += $shov_bonus;
                $act_do['actives'] += $pick_bonus;
                $act_do['actives'] += $mattock_bonus;

                $stones = 0;
                while( $act_do['actives'] > 0 )
                {
                    $stones += 5;
                    $act_do['actives'] -= 1;
                }
                $query = $db->Execute("UPDATE $dbtables[products] "
                            ."SET amount = amount + '$stones' "
                            ."WHERE long_name = 'stones' "
                            ."AND tribeid = '$tribe[goods_tribe]'");
                 db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("DELETE FROM $dbtables[activities] "
                            ."WHERE tribeid = '$tribe[tribeid]' "
                            ."AND product = 'stones'");
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
                            ."'Quarrying: $stones Stones quarried with $startactives actives.')");
                 db_op_result($query,__LINE__,__FILE__);
            }
            else
            {
                $query = $db->Execute("INSERT INTO $dbtables[logs] "
                            ."VALUES("
                            ."'',"
                            ."'$month[count]',"
                            ."'$year[count]',"
                            ."'$tribe[clanid]',"
                            ."'$tribe[tribeid]',"
                            ."'UPDATE',"
                            ."'$stamp',"
                            ."'Quarrying: We must be in a hilly area for quarrying stones.')");
                db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("DELETE FROM $dbtables[activities] "
                            ."WHERE tribeid = '$tribe[tribeid]' "
                            ."AND product = 'stones'");
                 db_op_result($query,__LINE__,__FILE__);
            }
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
