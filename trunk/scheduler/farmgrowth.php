<?php
require_once("../config.php");
$time_start = getmicrotime();
include("game_time.php");
connectdb();
$res = $db->Execute("SELECT * FROM $dbtables[farming] WHERE crop != 'NONE' AND month < 4");
 db_op_result($res,__LINE__,__FILE__);
while(!$res->EOF)
{
    $farm = $res->fields;

    if( $farm[crop] == 'sugar' )
    {
        $growthrate = 1.2 * $farm[skill];
    }
    elseif( $farm[crop] == 'cotton' )
    {
        $growthrate = 1.3 * $farm[skill];
    }
    elseif( $farm[crop] == 'grapes' )
    {
        $growthrate = 1.3 * $farm[skill];
    }
    elseif( $farm[crop] == 'tobacco' )
    {
        $growthrate = 1.1 * $farm[skill];
    }
    elseif( $farm[crop] == 'grain' )
    {
        $growthrate = 1.4 * $farm[skill];
    }
    elseif( $farm[crop] == 'flax' )
    {
        $growthrate = 1.6 * $farm[skill];
    }
    elseif( $farm[crop] == 'hemp' )
    {
        $growthrate = 1.8 * $farm[skill];
    }
    elseif( $farm[crop] == 'potatoes' )
    {
        $growthrate = 1.6 * $farm[skill];
    }
    elseif( $farm[crop] == 'corn' )
    {
        $growthrate = 1.7 * $farm[skill];
    }
    elseif( $farm[crop] == 'herbs' )
    {
        $growthrate = 1.1 * $farm[skill];
    }
    elseif( $farm[crop] == 'spice' )
    {
        $growthrate = 1.05 * $farm[skill];
    }

    $hex = $db->Execute("SELECT * FROM $dbtables[hexes] "
                       ."WHERE hex_id = '$farm[hex_id]'");
       db_op_result($hex,__LINE__,__FILE__);
    $hexinfo = $hex->fields;

    if( $hexinfo[terrain] == 'pr' )
    {
        $growthrate = $growthrate * .5;
    }
    elseif( $hexinfo[terrain] == 'gh' )
    {
        $growthrate = $growthrate * 1.2;
    }
    elseif( $hexinfo[terrain] == 'df' | $hexinfo[terrain] == 'cf' )
    {
        $growthrate = $growthrate * 1.15;
    }
    elseif( $hexinfo[terrain] == 'jg' )
    {
        $growthrate = $growthrate * 1.8;
    }
    elseif( $hexinfo[terrain] == 'jh' )
    {
        $growthrate = $growthrate * 1.6;
    }
    elseif( $hexinfo[terrain] == 'dh' | $hexinfo[terrain] == 'ch' )
    {
        $growthrate = $growthrate * 1.4;
    }
    elseif( $hexinfo[terrain] == 'ljm' )
    {
        $growthrate = $growthrate * 1.2;
    }
    elseif( $hexinfo[terrain] == 'lcm' )
    {
        $growthrate = $growthrate * 1.1;
    }
    else
    {
        $growthrate = $growthrate * .95;
    }

    $totalgrowth = $growthrate * $farm[acres];
    $res1 = $db->Execute("UPDATE $dbtables[farming] "
                ."SET harvest = harvest + $totalgrowth, "
                ."month = month + 1 "
                ."WHERE cropid = '$farm[cropid]' "
                ."AND crop = '$farm[crop]'");
       db_op_result($res1,__LINE__,__FILE__);




$res->MoveNext();
}
$res = $db->Execute("SELECT * FROM $dbtables[farming] WHERE crop != 'NONE' AND month > 3");
  db_op_result($res,__LINE__,__FILE__);
while( !$res->EOF )
{
    $farm = $res->fields;
    $res1 = $db->Execute("UPDATE $dbtables[farming] "
                ."SET harvest = harvest * .25 "
                ."WHERE cropid = '$farm[cropid]'");
         db_op_result($res1,__LINE__,__FILE__);
    if( $farm[harvest] < $farm[acres] )
    {
        $res1 = $db->Execute("DELETE FROM $dbtables[farming] "
                    ."WHERE cropid = '$farm[cropid]'");
         db_op_result($res1,__LINE__,__FILE__);
    }
    $res->MoveNext();
}

$res = $db->Execute("SELECT * FROM $dbtables[farming]");
  db_op_result($res,__LINE__,__FILE__);
while( !$res->EOF )
{
    $farm = $res->fields;
    if( $farm[status] == 'Planted' )
    {
        $res1 = $db->Execute("UPDATE $dbtables[farming] "
                    ."SET status = 'Growing' "
                    ."WHERE cropid = '$farm[cropid]'");
          db_op_result($res1,__LINE__,__FILE__);
    }
    elseif( $farm[status] == 'Growing' && $farm[month] > 2 && $farm[month] < 4 )
    {
        $res1 = $db->Execute("UPDATE $dbtables[farming] "
                    ."SET status = 'Ready' "
                    ."WHERE cropid = '$farm[cropid]'");
          db_op_result($res1,__LINE__,__FILE__);
    }
    elseif( $farm[status] == 'Ready' && $farm[month] > 3 )
    {
        $res1 = $db->Execute("UPDATE $dbtables[farming] "
                    ."SET status = 'Seed' "
                    ."WHERE cropid = '$farm[cropid]'");
          db_op_result($res1,__LINE__,__FILE__);
    }
    $res->MoveNext();
}

$res = $db->Execute("SELECT * FROM $dbtables[farming] WHERE crop = 'NONE'");
 db_op_result($res,__LINE__,__FILE__);
while( !$res->EOF )
{
    $farm = $res->fields;
    if( $month[count] == '11' | $month[count] == '12' | $month[count] == '1' )
    {
        $res1 = $db->Execute("DELETE FROM $dbtables[farming] "
                    ."WHERE crop = 'NONE' "
                    ."AND cropid = '$farm[cropid]'");
            db_op_result($res1,__LINE__,__FILE__);
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
