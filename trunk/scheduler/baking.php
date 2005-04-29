<?php
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
                       ."WHERE skill_abbr = 'bak' "
                       ."AND tribeid = '$tribe[tribeid]'");
      db_op_result($act,__LINE__,__FILE__);
    while( !$act->EOF )
    {
        $act_do = $act->fields;

        $skl = $db->Execute("SELECT * FROM $dbtables[skills] "
                           ."WHERE tribeid = '$tribe[tribeid]' "
                           ."AND abbr = 'bak'");
         db_op_result($skl,__LINE__,__FILE__);
        $skill = $skl->fields;
        $max_actives = $act_do[actives];
        if( $skill[level] < 10 )
        {
            $max_actives = $skill[level] * 10;
        }
        $act_do[actives] = $max_actives;
        $ov = $db->Execute("SELECT * FROM $dbtables[structures] "
                          ."WHERE used = 'N' "
                          ."AND clanid = '$tribe[clanid]' "
                          ."AND hex_id = '$tribe[hex_id]' "
                          ."AND complete = 'Y' "
                          ."AND number > 0 "
                          ."AND long_name = 'bakery'");
           db_op_result($ov,__LINE__,__FILE__);
        if( $act_do[product] == 'bread' && !$ov->EOF )
        {
            $ovens = $ov->fields;
            $max_poss_acts = $ovens[number] * 10;
            if( $act_do[actives] > $max_poss_acts )
            {
                $act_do[actives] = $max_poss_acts;
            }
            $grist = $db->Execute("SELECT * FROM $dbtables[products] "
                                 ."WHERE tribeid = '$tribe[goods_tribe]' "
                                 ."AND long_name = 'grain' "
                                 ."AND amount > 0");
               db_op_result($grist,__LINE__,__FILE__);
            $min_number = 20;
            $prov_number = 5;
            if( $grist->EOF )
            {
                $grist = $db->Execute("SELECT * FROM $dbtables[products] "
                                     ."WHERE tribeid = '$tribe[goods_tribe]' "
                                     ."AND long_name = 'flour'");
                 db_op_result($grist,__LINE__,__FILE__);
                $min_number = 40;
                $prov_number = 10;
            }
            $ingredient = $grist->fields;
            $startgrain = $ingredient[amount];
            if( $act_do[actives] > ( $ovens[number] * 10 ) )
            {
                $act_do[actives] = ( $ovens[number] * 10 );
            }
            $provs = 0;
            while( $act_do[actives] > 0 && $ingredient[amount] > ($min_number - 1) )
            {
                $act_do[actives] -= 1;
                $ingredient[amount] -= $min_number;
                $provs += $prov_number;
            }
            $deltagrain = $startgrain - $ingredient[amount];
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount - $deltagrain "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = '$ingredient[long_name]'");
               db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + $provs "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'provs'");
              db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("INSERT INTO $dbtables[logs] "
                        ."VALUES("
                        ."'',"
                        ."'$month[count]',"
                        ."'$year[count]',"
                        ."'$tribe[clanid]',"
                        ."'$tribe[tribeid]',"
                        ."'BAKING',"
                        ."'$stamp',"
                        ."'Baking: $provs bread made (provs) using $deltagrain $ingredient[long_name].')");
             db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[structures] "
                        ."SET used = 'Y' "
                        ."WHERE struct_id = '$ovens[struct_id]'");
              db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("DELETE FROM $dbtables[activities] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND skill_abbr = 'bak'");
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
