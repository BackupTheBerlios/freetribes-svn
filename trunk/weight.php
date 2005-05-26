<?php
error_reporting  (E_ALL);
//include('config.php');
//connectdb();
$res = $db->Execute("SELECT tribeid,slavepop,activepop,inactivepop,goods_tribe FROM $dbtables[tribes]");
  db_op_result($res,__LINE__,__FILE__);
while( !$res->EOF )
{
    $tribe = $res->fields;

    ///////////////First, figure out the carry capacity//////////////////////////////////
    $liv = $db->Execute("SELECT * FROM $dbtables[livestock] WHERE tribeid = '$tribe[tribeid]' AND amount > 0");
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
    //echo "Horse = $horse <br> Elephant = $elephant<br> Cattle = $cattle <br>";
    $sad = $db->Execute("SELECT * FROM $dbtables[products] WHERE long_name = 'saddlebags' AND tribeid = '$tribe[tribeid]'");
      db_op_result($sad,__LINE__,__FILE__);
    $wag = $db->Execute("SELECT * FROM $dbtables[products] WHERE long_name = 'wagon' AND tribeid = '$tribe[tribeid]'");
     db_op_result($wag,__LINE__,__FILE__);
    $bak = $db->Execute("SELECT * FROM $dbtables[products] WHERE long_name = 'backpack' AND tribeid = '$tribe[tribeid]'");
    db_op_result($bak,__LINE__,__FILE__);
    $pal = $db->Execute("SELECT * FROM $dbtables[products] WHERE long_name = 'palanquin' AND tribeid = '$tribe[tribeid]'");
        db_op_result($pal,__LINE__,__FILE__);
    $palanquin = $pal->fields;
    ////////////Figure out how many bearers needed for the palanquins////////
    $bearers_needed = $palanquin['amount'] * 4;
    ///////Now, deduct the bearers from further calculations////////////////
    //echo "bearers = $bearers_needed<br> Slaves = $tribe[slavepop]<br> Actives = $tribe[activepop]<br>";
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
    // echo "Palanquins= $palanquins<br>";
    $maxweight = ( $tribe['activepop'] * 30 )+( $tribe['slavepop'] * 30 ) + ( $tribe['inactivepop'] * 15 ) + $palanquins;
    //echo "MaxWeight: $maxweight<br>";
    $saddlebags = $sad->fields;
    $wagons = $wag->fields;
    $backpacks = $bak->fields;
    $wagonscheck = ( $horse / 2 ) + ( $cattle / 2 ) + $elephant;
    $wagons_used = 0;
    //echo "wagonscheck : $wagonscheck<br>";
    while( $wagons['amount'] > 0 && $cattle > 1 && $wagonscheck > 0 )
    {
        $wagons['amount'] -= 1;
        $cattle -= 2;
        $wagons_used += 1;
        $wagonscheck -= 1;
        //echo "wagons:$wagons_used Cattle = $cattle :: ";
    }
    while( $wagons['amount'] > 0 && $horse > 1 && $wagonscheck > 0 )
    {
        $wagons['amount'] -= 1;
        $horse -= 2;
        $wagons_used += 1;
        $wagonscheck -= 1;
        //echo "wagons:$wagons_used Horses = $horse :: ";
    }
    while( $wagons['amount'] > 0 && $elephant > 0 && $wagonscheck > 0 )
    {
        $wagons['amount'] -= 1;
        $elephant -= 1;
        $wagons_used += 1;
        $wagonscheck -= 1;
       // echo "wagons:$wagons_used Eleph = $elephant :: ";
    }

    $maxweight = $maxweight + ( $wagons_used * 2300 ) + ( $horse * 150 ) + ( $elephant * 1000 );
    //echo "<br> MAXWEIGHT after animals = $maxweight <br>";
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
    //echo "Max weight after backpacks: $maxweight<br>";
    if( $saddlebags['amount'] > $horse )
    {
        $saddlebags['amount'] = $horse;
    }
    $maxweight = $maxweight + ( $saddlebags['amount'] * 150 );
    //echo "Max weight after saddlebags: $maxweight<br>";
    if( $tribe['tribeid'] == $tribe['goods_tribe'] )
    {
        $query = $db->Execute("UPDATE $dbtables[tribes] SET maxweight = '$maxweight' WHERE tribeid = '$tribe[goods_tribe]'");
        db_op_result($query,__LINE__,__FILE__);
        //echo "Goods Tribe max weight set ".$db->ErrorMsg()."<br>";
    }
    else
    {
        //$query = $db->Execute("UPDATE $dbtables[tribes] SET maxweight = maxweight + $maxweight WHERE tribeid = '$tribe[tribeid]'");
        //db_op_result($query,__LINE__,__FILE__);
        $query = $db->Execute("UPDATE $dbtables[tribes] SET maxweight = '$maxweight' WHERE tribeid = '$tribe[tribeid]'");
        db_op_result($query,__LINE__,__FILE__);
        //echo "Other Tribe max weight set ".$db->ErrorMsg()."<br>";
    }
   //echo "Carry Cap $tribe[tribeid] : $maxweight<br>";
//////////////////////////////////////////Next, figure out how much they're carrying///////////////////////////


    $prod = $db->Execute("SELECT * FROM $dbtables[products] WHERE tribeid = '$tribe[tribeid]' AND amount > 0");
         db_op_result($prod,__LINE__,__FILE__);
    $totalweight = 0;
    while( !$prod->EOF )
    {
        $prodinfo = $prod->fields;
        $weight = $db->Execute("SELECT * FROM $dbtables[product_table] WHERE long_name = '$prodinfo[long_name]'");
          db_op_result($weight,__LINE__,__FILE__);
        $prodweight = $weight->fields;
        $totalweight += $prodweight['weight'] * $prodinfo['amount'];
        //echo "Weight for $prodweight[long_name] = $totalweight (WT: $prodweight[weight] X AMT: $prodinfo[amount]) <br>";
        $prod->MoveNext();
    }

    $resource = $db->Execute("SELECT * FROM $dbtables[resources] WHERE tribeid = '$tribe[tribeid]' AND amount > 0");
       db_op_result($resource,__LINE__,__FILE__);
    while( !$resource->EOF )
    {
        $resinfo = $resource->fields;
        $totalweight += $resinfo['amount'];
         //echo"Weight for $resinfo[long_name] = $totalweight (WT: $resinfo[amount]) <br>";
        $resource->MoveNext();
    }
    //echo "Total Weight $tribe[tribeid] = $totalweight<br><br>";
    $query = $db->Execute("UPDATE $dbtables[tribes] SET curweight = $totalweight WHERE tribeid = '$tribe[tribeid]'");
         db_op_result($query,__LINE__,__FILE__);
    $res->MoveNext();
}

?>