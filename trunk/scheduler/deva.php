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

    if( $tribe[DeVA] > 0 )
    {
        $seige = $db->Execute("SELECT * FROM $dbtables[garrisons] "
                             ."WHERE tribeid = '$tribe[DeVA]' "
                             ."AND hex_id = '$tribe[hex_id]'");
         db_op_result($seige,__LINE__,__FILE__);
        $defense = $db->Execute("SELECT * FROM $dbtables[garrisons] "
                               ."WHERE clanid = '$tribe[clanid]' "
                               ."AND hex_id = '$tribe[hex_id]'");
          db_op_result($defense,__LINE__,__FILE__);
        $defenders = 0;
        $seigers = 0;
        while( !$seige->EOF )
        {
            $seigeinfo = $seige->fields;
            $seigers += $seigeinfo[force];
            $seige->MoveNext();
        }
        while( !$defense->EOF )
        {
            $definfo = $defense->fields;
            $defenders += $definfo[force];
            $defense->MoveNext();
        }
        if( $seigers > $defenders )
        {
            $sg = $db->Execute("SELECT * FROM $dbtables[tribes] "
                              ."WHERE tribeid = '$tribe[DeVA]'");
              db_op_result($sg,__LINE__,__FILE__);
            $sginfo = $sg->fields;
            $res = $db->Execute("DELETE FROM $dbtables[activities] "
                        ."WHERE tribeid = '$tribe[tribeid]'");
              db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("DELETE FROM $dbtables[farm_activities] "
                        ."WHERE tribeid = '$tribe[tribeid]'");
             db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("UPDATE $dbtables[tribes] "
                        ."SET morale = morale - .001 "
                        ."WHERE tribeid = '$tribe[tribeid]'");
              db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("INSERT INTO $dbtables[logs] "
                        ."VALUES("
                        ."'',"
                        ."'$month[count]',"
                        ."'$year[count]',"
                        ."'$tribe[clanid]',"
                        ."'$tribe[tribeid]',"
                        ."'WAR',"
                        ."'$stamp',"
                        ."'War Activity: We are still "
                        ."under seige by $tribe[DeVA]! "
                        ."We were unable to complete any activities!')");
             db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("INSERT INTO $dbtables[logs] "
                        ."VALUES("
                        ."'',"
                        ."'$month[count]',"
                        ."'$year[count]',"
                        ."'$sginfo[clanid]',"
                        ."'$sginfo[tribeid]',"
                        ."'WAR',"
                        ."'$stamp',"
                        ."'War Activity: We are still "
                        ."maintaining a seige of $tribe[tribeid]. "
                        ."We have denied them the ability to conduct extra activities!')");
             db_op_result($res,__LINE__,__FILE__);
        }
        elseif( $defenders > $seigers )
        {
            $sg = $db->Execute("SELECT * FROM $dbtables[tribes] "
                              ."WHERE tribeid = '$tribe[DeVA]'");
              db_op_result($sg,__LINE__,__FILE__);
            $sginfo = $sg->fields;
            $res = $db->Execute("UPDATE $dbtables[tribes] "
                        ."SET DeVA = '0000.00', "
                        ."morale = morale + .002 "
                        ."WHERE tribeid = '$tribe[tribeid]'");
               db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("INSERT INTO $dbtables[logs] "
                        ."VALUES("
                        ."'',"
                        ."'$month[count]',"
                        ."'$year[count]',"
                        ."'$tribe[clanid]',"
                        ."'$tribe[tribeid]',"
                        ."'WAR','$stamp','War Activity: We have broken "
                        ."the seige layed by $tribe[DeVA]! "
                        ."We may now begin to conduct village activities once again!')");
               db_op_result($res,__LINE__,__FILE__);
            $res = $db->Execute("INSERT INTO $dbtables[logs] "
                        ."VALUES("
                        ."'',"
                        ."'$month[count]',"
                        ."'$year[count]',"
                        ."'$sginfo[clanid]',"
                        ."'$sginfo[tribeid]',"
                        ."'WAR',"
                        ."'$stamp',"
                        ."'War Activity: We were unable "
                        ."to continue the seige on $tribe[tribeid]. "
                        ."They are now conducting village activities again.')");
             db_op_result($res,__LINE__,__FILE__);
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
