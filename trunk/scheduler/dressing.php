<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: dressing.php
require_once("../config.php");
$time_start = getmicrotime();
include("game_time.php");
connectdb();
$res = $db->Execute("SELECT * FROM $dbtables[tribes]");
 db_op_result($res,__LINE__,__FILE__);
while( !$res->EOF )
{
    $tribe = $res->fields;
    $act = $db->Execute("SELECT * FROM $dbtables[activities] "
                       ."WHERE tribeid = '$tribe[tribeid]' "
                       ."AND skill_abbr = 'dre'");
          db_op_result($act,__LINE__,__FILE__);
    while( !$act->EOF )
    {
        $act_do = $act->fields;
        if( $act_do[skill_abbr] == 'dre' )
        {
            $salt = $db->Execute("SELECT * FROM $dbtables[resources] "
                                ."WHERE long_name = 'Salt' "
                                ."AND tribeid = '$tribe[goods_tribe]'");
              db_op_result($salt,__LINE__,__FILE__);
            $saltinfo = $salt->fields;

            $fur = $db->Execute("SELECT * FROM $dbtables[products] "
                               ."WHERE long_name = 'furs' "
                               ."AND tribeid = '$tribe[goods_tribe]'");
              db_op_result($fur,__LINE__,__FILE__);
            $furinfo = $fur->fields;

            $skin = $db->Execute("SELECT * FROM $dbtables[products] "
                                ."WHERE long_name = 'skins' "
                                ."AND tribeid = '$tribe[goods_tribe]'");
              db_op_result($skin,__LINE__,__FILE__);
            $skininfo = $skin->fields;

            $dress = $db->Execute("SELECT * FROM $dbtables[skills] "
                                 ."WHERE tribeid = '$tribe[tribeid]' "
                                 ."AND abbr = 'dre'");
              db_op_result($dress,__LINE__,__FILE__);
            $dressinfo = $dress->fields;
            $max_dressers = 10000000;
            if( $dressinfo[level] < 10 )
            {
                $max_dressers = $dressinfo[level] * 10;
                if( $act_do[actives] > $max_dressers )
                {
                    $act_do[actives] = $max_dressers;
                }
            }

            $leathermake = 0;
            while( $skininfo[amount] > 3 && $saltinfo[amount] > 0 && $act_do[actives] > 0 )
            {
                $saltinfo[amount] -= 1;
                $act_do[actives] -= 1;
                $skininfo[amount] -= 4;
                $leathermake += 4;
            }
            while( $furinfo[amount] > 3 && $act_do[actives] > 0 && $saltinfo[amount] > 0 )
            {
                $saltinfo[amount] -= 1;
                $act_do[actives] -= 1;
                $furinfo[amount] -= 4;
                $leathermake += 4;
            }
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + '$leathermake' "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'leather'");
               db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("DELETE FROM $dbtables[activities] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND product = 'leather'");
               db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[resources] "
                        ."SET amount = '$saltinfo[amount]' "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'Salt'");
               db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = '$skininfo[amount]' "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'skins'");
              db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = '$furinfo[amount]' "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'furs'");
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
                        ."'Dressing: $leathermake Leather dressed.')");
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
