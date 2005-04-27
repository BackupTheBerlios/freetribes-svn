<?
require_once("config.php");

//include("header.php");

include("game_time.php");

global $db, $dbtables;

connectdb();
  $res = $db->Execute("SELECT * FROM $dbtables[tribes] WHERE clanid = '$_SESSION[clanid]' OR hex_id = '$_SESSION[hex_id]'");
  while(!$res->EOF)
   {
    $tribe = $res->fields;

    ///////////////First, figure out the carry capacity//////////////////////////////////
    $liv = $db->Execute("SELECT * FROM $dbtables[livestock] WHERE tribeid = '$tribe[tribeid]' AND amount > 0");
    $horse = 0;
    $elephant = 0;
    $wagon = 0;
    $maxweight = 0;
    while(!$liv->EOF)
    {
        $livinfo = $liv->fields;
        if($livinfo[type] == 'Horses')
        {
            $horse = $livinfo[amount];
        }
        elseif($livinfo[type] == 'Elephants')
        {
            $elephant = $livinfo[amount];
        }
        elseif($livinfo[type] == 'Cattle')
        {
            $cattle = $livinfo[amount];
        }
        $liv->MoveNext();
    }

    $sad = $db->Execute("SELECT * FROM $dbtables[products] "
                       ."WHERE long_name = 'saddlebags' "
                       ."AND tribeid = '$tribe[tribeid]'");
    $wag = $db->Execute("SELECT * FROM $dbtables[products] "
                       ."WHERE long_name = 'wagon' "
                       ."AND tribeid = '$tribe[tribeid]'");
    $bak = $db->Execute("SELECT * FROM $dbtables[products] "
                       ."WHERE long_name = 'backpack' "
                       ."AND tribeid = '$tribe[tribeid]'");
    $pal = $db->Execute("SELECT * FROM $dbtables[products] "
                       ."WHERE long_name = 'palanquin' "
                       ."AND tribeid = '$tribe[tribeid]'");
    $palanquin = $pal->fields;
    ////////////Figure out how many bearers needed for the palanquins////////
    $bearers_needed = $palanquin[amount] * 4;
    ///////Now, deduct the bearers from further calculations////////////////
    while( $bearers_needed > 0 && $tribe[slavepop] > 0 && $tribe[activepop] > 0 )
    {
        while( $tribe[slavepop] > 0 && $bearers_needed > 0 )
        {
            $bearers_needed -= 1;
            $tribe[slavepop] -= 1;
            $palanquins += 300;
        }
        while( $tribe[activepop] > 0 && $bearers_needed > 0 )
        {
            $bearers_needed -= 1;
            $tribe[activepop] -= 1;
            $palanquins += 300;
        }
    }
    $maxweight = ($tribe[activepop] * 30)+($tribe[slavepop] * 30) + ($tribe[inactivepop] * 15);
    $saddlebags = $sad->fields;
    $wagons = $wag->fields;
    $backpacks = $bak->fields;

    $wagonscheck = ($horse/2) + ($cattle/2) + $elephant;
    $wagons_used = 0;
    while($wagons[amount] > 0 && $cattle > 1 && $wagonscheck > 0 )
    {
        $wagons[amount] -= 1;
        $cattle -= 2;
        $wagons_used += 1;
        $wagonscheck -= 1;
    }
    while($wagons[amount] > 0 && $horse > 1 && $wagonscheck > 0 )
    {
        $wagons[amount] -= 1;
        $horse -= 2;
        $wagons_used += 1;
        $wagonscheck -= 1;
    }
    while($wagons[amount] > 0 && $elephant > 0 && $wagonscheck > 0 )
    {
        $wagons[amount] -= 1;
        $elephant -= 1;
        $wagons_used += 1;
        $wagonscheck -= 1;
    }

    $maxweight = $maxweight + ($wagons_used * 2300) + ($horse * 150) + ($elephant * 1000);
    
    $backpackwearers = $tribe[activepop] + $tribe[slavepop] + $tribe[inactivepop] - $horse;
    if($backpackwearers < 0)
    {
        $backpackwearers = 0;
    }
    if($backpacks[amount] > $backpackwearers)
    {
        $backpacks[amount] = $backpackwearers;
    }
    $maxweight = $maxweight + ($backpacks[amount] * 30);


    if($saddlebags[amount] > $horse)
    {
        $saddlebags[amount] = $horse;
    }
    $maxweight = $maxweight + ($saddlebags[amount] * 150);

    if( $tribe[tribeid] == $tribe[goods_tribe] )
    {
        $db->Execute("UPDATE $dbtables[tribes] SET maxweight = '$maxweight' WHERE tribeid = '$tribe[goods_tribe]'");
    }
    else
    {
        $db->Execute("UPDATE $dbtables[tribes] SET maxweight = maxweight + $maxweight WHERE tribeid = '$tribe[goods_tribe]'");
        $db->Execute("UPDATE $dbtables[tribes] SET maxweight = 0 WHERE tribeid = '$tribe[tribeid]'");
    } 

//////////////////////////////////////////Next, figure out how much they're carrying///////////////////////////


    $prod = $db->Execute("SELECT * FROM $dbtables[products] WHERE tribeid = '$tribe[tribeid]' AND amount > 0");
    $totalweight = 0;
    while( !$prod->EOF)
    {
        $prodinfo = $prod->fields;
        $weight = $db->Execute("SELECT * FROM $dbtables[product_table] WHERE long_name = '$prodinfo[long_name]'");
        $prodweight = $weight->fields;
        $totalweight += $prodweight[weight] * $prodinfo[amount];
        $prod->MoveNext();
    }

    $resource = $db->Execute("SELECT * FROM $dbtables[resources] WHERE tribeid = '$tribe[tribeid]' AND amount > 0");
    while( !$resource->EOF)
    {
        $resinfo = $resource->fields;
        $totalweight += $resinfo[amount];
        $resource->MoveNext();
    }

    $db->Execute("UPDATE $dbtables[tribes] SET curweight = $totalweight WHERE tribeid = '$tribe[tribeid]'");


$res->MoveNext();
}

?>
