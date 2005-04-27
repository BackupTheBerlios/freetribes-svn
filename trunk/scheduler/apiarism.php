<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: apiarism.php

require_once("../config.php");
$time_start = getmicrotime();
include("game_time.php");
connectdb();
$res = $db->Execute("SELECT * FROM $dbtables[tribes]");
db_op_result($res,__LINE__,__FILE__);
while( !$res->EOF )
{
    $tribe = $res->fields;


////////////////////////////Fill up the apiaries////////////////
    $act = $db->Execute("SELECT * FROM $dbtables[structures] "
                       ."WHERE long_name = 'apiary' "
                       ."AND tribeid = '$tribe[goods_tribe]' "
                       ."AND complete = 'Y' "
                       ."AND number < 20 "
                       ."ORDER BY struct_id DESC");
    db_op_result($act,__LINE__,__FILE__);
    $hiv = $db->Execute("SELECT * FROM $dbtables[products] "
                       ."WHERE long_name = 'hives' "
                       ."AND tribeid = '$tribe[goods_tribe]' "
                       ."AND amount > 0");
    db_op_result($hiv,__LINE__,__FILE__);
    if( !$hiv->EOF && !$act->EOF )
    {
        $act_do = $act->fields;
        $hives = $hiv->fields;
        $installed = 0;
        while( $act_do[number] < 20 && $hives[amount] > 0 )
        {
            $act_do[number] += 1;
            $hives[amount] -= 1;
            $installed += 1;
        }
        $res = $db->Execute("UPDATE $dbtables[structures] "
                    ."SET number = '$act_do[number]' "
                    ."WHERE struct_id = '$act_do[struct_id]' "
                    ."AND tribeid = '$act_do[tribeid]' "
                    ."AND hex_id = '$act_do[hex_id]' "
                    ."AND hex_id = '$tribe[hex_id]' "
                    ."LIMIT 1");
        db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount - $hives[amount] "
                    ."WHERE tribeid = '$tribe[goods_tribe]' "
                    ."AND long_name = '$hives[long_name]'");
        db_op_result($res,__LINE__,__FILE__);
    }
///////////////////////////////////////////////////////////////

    $act = $db->Execute("SELECT * FROM $dbtables[activities] "
                       ."WHERE tribeid = '$tribe[tribeid]' "
                       ."AND skill_abbr = 'api'");
    db_op_result($act,__LINE__,__FILE__);
    while( !$act->EOF )
    {
        $act_do = $act->fields;
        $sk = $db->Execute("SELECT * FROM $dbtables[skills] "
                          ."WHERE tribeid = '$tribe[tribeid]' "
                          ."AND abbr = 'api'");
        db_op_result($sk,__LINE__,__FILE__);
        $skill = $sk->fields;
        $api = $db->Execute("SELECT * FROM $dbtables[structures] "
                           ."WHERE hex_id = '$tribe[hex_id]' "
                           ."AND clanid = '$tribe[clanid]' "
                           ."AND long_name = 'apiary' "
                           ."AND used = 'N' "
                           ."AND number > 0");
        db_op_result($api,__LINE__,__FILE__);
        $beekeepers = 0;
        while( !$api->EOF )
        {
            $apiary = $api->fields;
            $beekeepers += round($apiary[number] / 5 );
            $res = $db->Execute("UPDATE $dbtables[structures] "
                        ."SET used = 'Y' "
                        ."WHERE struct_id = '$apiary[struct_id]'");
            db_op_result($res,__LINE__,__FILE__);
            $api->MoveNext();
        }
        $wax = 0;
        $honey = 0;
        while( $beekeepers > 0 )
        {
            $beekeepers -= 1;
            $wax += rand(0, $skill[level]);
            $honey += rand(0, $skill[level]);
        }
        $res = $db->Execute("DELETE FROM $dbtables[activities] "
                    ."WHERE tribeid = '$tribe[tribeid]' "
                    ."AND skill_abbr = 'api'");
        db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount + $wax "
                    ."WHERE tribeid = '$tribe[goods_tribe]' "
                    ."AND long_name = 'wax'");
        db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount + $honey "
                    ."WHERE long_name = 'honey' "
                    ."AND tribeid = '$tribe[goods_tribe]'");
        db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("INSERT INTO $dbtables[logs] "
                    ."VALUES("
                    ."'',"
                    ."'$month[count]',"
                    ."'$year[count]',"
                    ."'$tribe[clanid]',"
                    ."'$tribe[tribeid]',"
                    ."'APIARY',"
                    ."'$stamp',"
                    ."'Apiarism: $wax wax, $honey honey collected from hives.')");
        db_op_result($res,__LINE__,__FILE__);
        $act->MoveNext();
    }
    $res->MoveNext();
}
$time_end = getmicrotime();
$time = $time_end - $time_start;
//eg("([^/]*).php", $_SERVER['PHP_SELF'], $page_name); // get the name of the file being viewed
$page_name =   str_replace($game_root."scheduler/",'',__FILE__);
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
