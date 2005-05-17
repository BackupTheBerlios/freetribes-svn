<?php
error_reporting  (E_ALL);
$pos = (strpos($_SERVER['PHP_SELF'], "/mysqlt-common.php"));
if ($pos !== false)
{
    die("You cannot access this page directly!");
}
require_once("config.php"); //we dont need THESE do we? this stuff is already included in the calling file.. but oh well
$time_start = getmicrotime();
include("game_time.php");
connectdb();
$res = $db->Execute("SELECT * FROM $dbtables[tribes]");
  db_op_result($res,__LINE__,__FILE__);
while( !$res->EOF )
{
    $tribe = $res->fields;

    ///////////////First, figure out the carry capacity//////////////////////////////////
    $liv = $db->Execute("SELECT * FROM $dbtables[livestock] "
                       ."WHERE tribeid = '$tribe[tribeid]' "
                       ."AND amount > 0");
        db_op_result($liv,__LINE__,__FILE__);
    $horse = 0;
    $elephant = 0;
    $wagon = 0;
    $cattle = 0;
    $palanquin = 0;
    $palanquins = 0;
    $maxweight = 0;
    while( !$liv->EOF )
    {
        $livinfo = $liv->fields;
        if( $livinfo['type'] == 'Horses' )
        {
            $horse = $livinfo['amount'];
        }
        elseif( $livinfo['type'] == 'Elephants' )
        {
            $elephant = $livinfo['amount'];
        }
        elseif( $livinfo['type'] == 'Cattle' )
        {
            $cattle = $livinfo['amount'];
        }
        $liv->MoveNext();
    }
    $bak = array();

    $sad = $db->Execute("SELECT * FROM $dbtables[products] "
                       ."WHERE long_name = 'saddlebags' "
                       ."AND tribeid = '$tribe[tribeid]'");
      db_op_result($sad,__LINE__,__FILE__);
    $wag = $db->Execute("SELECT * FROM $dbtables[products] "
                       ."WHERE long_name = 'wagon' "
                       ."AND tribeid = '$tribe[tribeid]'");
     db_op_result($wag,__LINE__,__FILE__);
    $bak = $db->Execute("SELECT * FROM $dbtables[products] "
                       ."WHERE long_name = 'backpack' "
                       ."AND tribeid = '$tribe[tribeid]'");
    db_op_result($bak,__LINE__,__FILE__);
    $pal = $db->Execute("SELECT * FROM $dbtables[products] "
                       ."WHERE long_name = 'palanquin' "
                       ."AND tribeid = '$tribe[tribeid]'");
        db_op_result($pal,__LINE__,__FILE__);
    $palanquin = $pal->fields;
    ////////////Figure out how many bearers needed for the palanquins////////
    $bearers_needed = $palanquin['amount'] * 4;
    ///////Now, deduct the bearers from further calculations////////////////
    while( $bearers_needed > 0 && $tribe['slavepop'] > 0 && $tribe['activepop'] > 0 )
    {
        while( $tribe['slavepop'] > 0 && $bearers_needed > 0 )
        {
            $bearers_needed -= 1;
            $tribe['slavepop'] -= 1;
            $palanquins += 300;
        }
        while( $tribe['activepop'] > 0 && $bearers_needed > 0 )
        {
            $bearers_needed -= 1;
            $tribe['activepop'] -= 1;
            $palanquins += 300;
        }
    }
    $maxweight = ( $tribe['activepop'] * 30 )+( $tribe['slavepop'] * 30 ) + ( $tribe['inactivepop'] * 15 ) + $palanquins;
    $saddlebags = $sad->fields;
    $wagons = $wag->fields;
    $backpacks = $bak->fields;
    $wagonscheck = ( $horse / 2 ) + ( $cattle / 2 ) + $elephant;
    $wagons_used = 0;
    while( $wagons['amount'] > 0 && $cattle > 1 && $wagonscheck > 0 )
    {
        $wagons['amount'] -= 1;
        $cattle -= 2;
        $wagons_used += 1;
        $wagonscheck -= 1;
    }
    while( $wagons['amount'] > 0 && $horse > 1 && $wagonscheck > 0 )
    {
        $wagons['amount'] -= 1;
        $horse -= 2;
        $wagons_used += 1;
        $wagonscheck -= 1;
    }
    while( $wagons['amount'] > 0 && $elephant > 0 && $wagonscheck > 0 )
    {
        $wagons['amount'] -= 1;
        $elephant -= 1;
        $wagons_used += 1;
        $wagonscheck -= 1;
    }

    $maxweight = $maxweight + ( $wagons_used * 2300 ) + ( $horse * 150 ) + ( $elephant * 1000 );
    $backpackwearers = $tribe['activepop'] + $tribe['slavepop'] + $tribe['inactivepop'] - $horse;
    if( $backpackwearers < 0 )
    {
        $backpackwearers = 0;
    }
    if( $backpacks['amount'] > $backpackwearers )
    {
        $backpack['amount'] = $backpackwearers;
    }
    $maxweight = $maxweight + ( $backpacks['amount'] * 30 );

    if( $saddlebags['amount'] > $horse )
    {
        $saddlebags['amount'] = $horse;
    }
    $maxweight = $maxweight + ( $saddlebags['amount'] * 150 );

    if( $tribe['tribeid'] == $tribe['goods_tribe'] )
    {
        $query = $db->Execute("UPDATE $dbtables[tribes] "
                    ."SET maxweight = '$maxweight' "
                    ."WHERE tribeid = '$tribe[goods_tribe]'");
            db_op_result($query,__LINE__,__FILE__);
    }
    else
    {
        $query = $db->Execute("UPDATE $dbtables[tribes] "
                    ."SET maxweight = maxweight + $maxweight "
                    ."WHERE tribeid = '$tribe[goods_tribe]'");
        db_op_result($query,__LINE__,__FILE__);
        $query = $db->Execute("UPDATE $dbtables[tribes] "
                    ."SET maxweight = 0 "
                    ."WHERE tribeid = '$tribe[tribeid]'");
          db_op_result($query,__LINE__,__FILE__);
    }

//////////////////////////////////////////Next, figure out how much they're carrying///////////////////////////


    $prod = $db->Execute("SELECT * FROM $dbtables[products] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND amount > 0");
         db_op_result($prod,__LINE__,__FILE__);
    $totalweight = 0;
    while( !$prod->EOF )
    {
        $prodinfo = $prod->fields;
        $weight = $db->Execute("SELECT * FROM $dbtables[product_table] "
                              ."WHERE long_name = '$prodinfo[long_name]'");
          db_op_result($weight,__LINE__,__FILE__);
        $prodweight = $weight->fields;
        $totalweight += $prodweight['weight'] * $prodinfo['amount'];
        $prod->MoveNext();
    }

    $resource = $db->Execute("SELECT * FROM $dbtables[resources] "
                            ."WHERE tribeid = '$tribe[tribeid]' "
                            ."AND amount > 0");
       db_op_result($resource,__LINE__,__FILE__);
    while( !$resource->EOF )
    {
        $resinfo = $resource->fields;
        $totalweight += $resinfo['amount'];
        $resource->MoveNext();
    }

    $query = $db->Execute("UPDATE $dbtables[tribes] "
                ."SET curweight = $totalweight "
                ."WHERE tribeid = '$tribe[tribeid]'");
         db_op_result($query,__LINE__,__FILE__);
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