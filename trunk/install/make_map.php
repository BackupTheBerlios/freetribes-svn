<?php
error_reporting  (E_ERROR | E_WARNING | E_PARSE | !E_NOTICE);
require_once("../config.php");
connectdb();

$WIDTH = 1;
$XA = 0;
$XB = $map_width;                   //width of map
$YA = 0;
$YB = $map_width;                   //height of map
$Z1 = 0;
$Z2 = 0;
$Z3 = 0;
$Z4 = 0;

$sea_shift = 0;
//$XADD = 64;                   // move x axis left on screen
$XADD = 128;                    // move x axis left on screen
$YADD = 0;                  // lower y axis view
$LEVEL_UNDEFINED = 99999;
$LEVEL_WATER = -6;          //sea level
$NUM_TERRAIN_TYPE = 18;      //number of terrain types
$display = 0;

$map_width_sel[$map_width] = "SELECTED";

$picType = array();
$pic = array();

$steep;
$sealevel;
$buf = "";
$buf2 = "";
$pic0;
$height_map = array();

function make_seed()
{
    list($usec, $sec) = explode(' ', microtime());
    return (float) $sec + ((float) $usec * 100000);
}

$db->Execute("TRUNCATE TABLE $dbtables[hexes]");
if( !$seed )
{
    $seed = rand();
    //$seed = 916158095;
}
srand($seed);

$half = (int)(getrandmax() / 2);
$full = getrandmax();


function my_rand()
{
    global $full, $seed;

    return rand() / $full;
}
function ZColor ($z0)
{
    global $LEVEL_WATER, $LEVEL_UNDEFINED, $NUM_TERRAIN_TYPE, $sea_shift, $file, $height_map;
    global $db, $dbtables, $display, $map_width;

    $show_val=false;
        /////some map zone variables/////
        $display++;
        $zone_params = ( $map_width * $map_width ) / 3;
        $zone_two = $zone_params;
        $zone_three = $zone_params * 2;
        $zone_total = $zone_params * 3;
        $curr_zone = 1;
        if( $display >= $zone_two && $display < $zone_three)
        {
            $curr_zone = 2;
        }
        elseif( $display >= $zone_three )
        {
            $curr_zone = 3;
        }


    $t1 = $z0;

        $res_chance = round( rand( 1, 10000 ) + ( ( $display / 30 ) / $curr_zone ) );
        $gem_chance = 15;
        $gold_chance = 30;
        $silver_chance = 50;
        $iron_chance = 165;
        $tin_chance = 320;
        $zinc_chance = 475;
        $copper_chance = 630;
        $lead_chance = 725;
        $salt_chance = 865;
        $coal_chance = 1200;
        $res_pres = "N";
        if( $res_chance <= $gem_chance )
        {
            $resource = "gems";
            $res_pres = "Y";
        }
        elseif( $res_chance < $gold_chance )
        {
            $resource = "gold";
            $res_pres = "Y";
        }
        elseif( $res_chance < $silver_chance )
        {
            $resource = "silver";
            $res_pres = "Y";
        }
        elseif( $res_chance < $iron_chance )
        {
            $resource = "iron";
            $res_pres = "Y";
        }
        elseif( $res_chance < $tin_chance )
        {
            $resource = "tin";
            $res_pres = "Y";
        }
        elseif( $res_chance < $zinc_chance )
        {
            $resource = "zinc";
            $res_pres = "Y";
        }
        elseif( $res_chance < $copper_chance )
        {
            $resource = "copper";
            $res_pres = "Y";
        }
        elseif( $res_chance < $lead_chance )
        {
            $resource = "lead";
            $res_pres = "Y";
        }
        elseif( $res_chance < $salt_chance )
        {
            $resource = "salt";
            $res_pres = "Y";
        }
        elseif( $res_chance < $coal_chance )
        {
            $resource = "coal";
            $res_pres = "Y";
        }
        else
        {
            $resource = "";
            $res_pres = "N";
        }

    $val = "";
//TODO: This insertmap shit needs to be re-worked- logically , it is illogical, causing database errors
//and likely some resources or events end up running incorrectly
//I believe a better method for this is a switch/case series instead of if/else
    if ($t1<=$LEVEL_WATER)
    {
                $terrain = "o";
                $safe = "N";
                $move = 30;
                insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                $return;
    }
        if ( $t1 == -5 )
        {
                if( $curr_zone == 2 )
                {
                    if( $res_chance < ( $salt_chance - 75 ) && $resource == 'salt' )
                    {
                        $terrain = "pr";
                        $safe = "Y";
                        $move = 3;
                        insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                        return;
                    }
                    else
                    {
                        $terrain = "pr";
                        $safe = "Y";
                        $move = 3;
                        insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                        return;
                    }
                }
                elseif( $curr_zone == 3 )
                {
                    $terrain = "de";
                    $safe = "N";
                    $move = 3;
                    insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                    return;
                }
                else
                {
                    $terrain = "tu";
                    $safe = "N";
                    $move = 3;
                    insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                    return;
                }
        }
        if ( $t1 == -4 )
        {
                if( $curr_zone == 2 )
                {
                    if( $res_chance < ( $salt_chance - 75 ) && $resource == 'salt' )
                    {
                        $terrain = "pr";
                        $safe = "Y";
                        $move = 3;
                        insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                        return;
                    }
                    else
                    {
                        $terrain = "pr";
                        $safe = "Y";
                        $move = 3;
                        insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                        return;
                    }
                }
                elseif( $curr_zone == 3 )
                {
                    $terrain = "de";
                    $safe = "N";
                    $move = 3;
                    insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                    return;
                }
                else
                {
                    $terrain = "tu";
                    $safe = "N";
                    $move = 3;
                    insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                    return;
                }
        }
        if ( $tl == -3 )
        {
                if( $curr_zone > 1 )
                {
                    $terrain = "de";
                    $safe = "N";
                    $move = 3;
                    insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                    return;
                }
                else
                {
                    $terrain = "tu";
                    $safe = "N";
                    $move = 4;
                    insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                    return;
                }
        }
    if ( $t1 == -2 )
    {
                if( $curr_zone == 2 )
                {
                    if( $res_chance < ( $salt_chance - 75 ) && $resource == 'salt' )
                    {
                        $terrain = "pr";
                        $safe = "Y";
                        $move = 3;
                        insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                        return;
                    }
                    else
                    {
                        $terrain = "pr";
                        $safe = "Y";
                        $move = 3;
                        insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                        return;
                    }
                }
                elseif( $curr_zone == 3 )
                {
                    $terrain = "de";
                    $safe = "N";
                    $move = 3;
                    insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                    return;
                }
                else
                {
                    $terrain = "tu";
                    $safe = "N";
                    $move = 4;
                    insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                    return;
                }
    }
    if ( $t1 == -1 )
    {
                if( $curr_zone < 3 )
                {
                    if( $res_chance < ( $salt_chance - 75 ) && $resource == 'salt' )
                    {
                        $terrain = "pr";
                        $safe = "Y";
                        $move = 3;
                        insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                        return;
                    }
                    else
                    {
                        $terrain = "pr";
                        $safe = "Y";
                        $move = 3;
                        insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                        return;
                    }
                }
                else
                {
                    $swamp = rand( 1, 100);
                    if( $swamp >= 95 )
                    {
                        $terrain = "sw";
                        $safe = "N";
                        $move = 5;
                        insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                        return;
                    }
                    else
                    {
                        $terrain = "pr";
                        $safe = "Y";
                        $move = 3;
                        insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                        return;
                    }
                }
    }
    if (  $t1 == 0 )                //Lake
    {
                    if( $res_chance < ( $salt_chance - 75 ) && $resource == 'salt' )
                    {
                        $terrain = "pr";
                        $safe = "Y";
                        $move = 3;
                        insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                        return;
                    }
                    else
                    {
                        $terrain = "pr";
                        $safe = "Y";
                        $move = 3;
                        insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                        return;
                    }
    }
    if (  $t1 == '1' )              //Beach level
    {
                if( $res_chance < ( $salt_chance - 75 ) && $resource == 'salt' )
                {
                    $terrain = "pr";
                    $safe = "Y";
                    $move = 3;
                    insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                    return;
                }
                else
                {
                    $terrain = "pr";
                    $safe = "Y";
                    $move = 3;
                    insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                    return;
                }
    }
    if (  $t1 == '2' )              //Prairie
    {
                if( $curr_zone < 2 )
                {
                    $terrain = "cf";
                    $safe = "Y";
                    $move = 4;
                    insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                    return;
                }
                elseif( $curr_zone < 2 )
                {
                    $terrain = "jg";
                    $safe = "Y";
                    $move = 5;
                    insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                    return;
                }
                else
                {
                    $terrain = "df";
                    $safe = "Y";
                    $move = 4;
                    insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                    return;
                }
    }

    if (  $t1 == '3' )
    {
                $terrain = "pr";
                $safe = "Y";
                $move = 3;
                insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                return;
    }
    if (  $t1 == '4' )
    {
                if( $curr_zone < 2 )
                {
                    $terrain = "cf";
                    $safe = "Y";
                    $move = 4;
                    insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                    return;
                }
                elseif( $curr_zone > 2 )
                {
                    $terrain = "jg";
                    $safe = "Y";
                    $move = 5;
                    insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                    return;
                }
                else
                {
                    $terrain = "df";
                    $safe = "Y";
                    $move = 4;
                    insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                    return;
                }
    }
    if (  $t1 == '5' )
    {
                $terrain = "pr";
                $safe = "Y";
                $move = 3;
                insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                return;
    }

    if (  $t1 == '6')               //Low Hills
    {
                if( $curr_zone < 2 )
                {
                    $terrain = "gh";
                    $safe = "Y";
                    $move = 4;
                    insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                    return;
                }
                elseif( $curr_zone > 2 )
                {
                    $terrain = "gh";
                    $safe = "Y";
                    $move = 4;
                    insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                    return;
                }
                else
                {
                    $terrain = "gh";
                    $safe = "Y";
                    $move = 4;
                    insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                    return;
                }
    }

    if (  $t1 == '7' )
    {
                $terrain = "gh";
                $safe = "Y";
                $move = 4;
                insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                return;
    }
    if (  $t1 == '8' )
    {
                if( $curr_zone < 2 )
                {
                    $terrain = "gh";
                    $safe = "Y";
                    $move = 5;
                    insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                    return;
                }
                elseif( $curr_zone > 2 )
                {
                    $terrain = "gh";
                    $safe = "Y";
                    $move = 5;
                    insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                    return;
                }
                else
                {
                    $terrain = "gh";
                    $safe = "Y";
                    $move = 5;
                    insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                    return;
                }
    }

    if (  $t1 == '9' )              //High Hills
    {
                $terrain = "gh";
                $safe = "Y";
                $move = 5;
                insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                return;
    }

    if (  $t1 == '10' )
    {
                if( $curr_zone < 2 )
                {
                    $terrain = "ch";
                    $safe = "Y";
                    $move = 6;
                    insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                    return;
                }
                elseif( $curr_zone > 2 )
                {
                    $terrain = "jh";
                    $safe = "Y";
                    $move = 7;
                    insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                    return;
                }
                else
                {
                    $terrain = "dh";
                    $safe = "Y";
                    $move = 6;
                    insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                    return;
                }
    }
    if (  $t1 == '11' )
    {
                if( $curr_zone < 3 )
                {
                    $terrain = "ch";
                    $safe = "Y";
                    $move = 6;
                    insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                    return;
                }
                else
                {
                    $terrain = "jh";
                    $safe = "Y";
                    $move = 7;
                    insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                    return;
                }
    }

    if (  $t1 == '12' )             //Mountain
    {
                if( $curr_zone < 3 )
                {
                    $terrain = "lcm";
                    $safe = "N";
                    $move = 8;
                    insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                    return;
                }
                else
                {
                    $terrain = "ljm";
                    $safe = "N";
                    $move = 9;
                    insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                    return;
                }
    }

    if (  $t1 == '13' )
    {
                if( $curr_zone < 3 )
                {
                    $terrain = "lcm";
                    $safe = "N";
                    $move = 8;
                    insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                    return;
                }
                else
                {
                    $terrain = "ljm";
                    $safe = "N";
                    $move = 9;
                    insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                    return;
                }
    }

    if (  $t1 == '14' )             //High mountain
    {
                if( $curr_zone > 2 )
                {
                    $volc = rand( 1, 100 );
                    if( $volc > 95 )
                    {
                        $terrain = "hvm";
                        $safe = "N";
                        $move = "30";
                        insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                        return;
                    }
                    else
                    {
                        $terrain = "hsm";
                        $safe = "N";
                        $move = "30";
                        insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                        return;
                    }
                }
                else
                {
                    $terrain = "hsm";
                    $safe = "N";
                    $move = "30";
                    insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                    return;
                }
    }
        if (  $t1 == '15' )
        {
                if( $curr_zone < 3 )
                {
                    $terrain = "lcm";
                    $safe = "N";
                    $move = "8";
                    insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                    return;
                }
                else
                {
                    $terrain = "ljm";
                    $safe = "N";
                    $move = "9";
                    insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                    return;
                }
        }
        if (  $t1 == '16' )
        {
                if( $curr_zone < 2 )
                {
                    $terrain = "ch";
                    $safe = "Y";
                    $move = 6;
                    insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                    return;
                }
                elseif( $curr_zone > 2 )
                {
                    $terrain = "jh";
                    $safe = "Y";
                    $move = 7;
                    insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                    return;
                }
                else
                {
                    $terrain = "dh";
                    $safe = "Y";
                    $move = 6;
                    insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                    return;
                }
        }
        if (  $t1 == '17' )                              //High Hills
        {
                $terrain = "gh";
                $safe = "Y";
                $move = 4;
                insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                return;
        }

        if( $curr_zone == 2 )
        {
            if( $res_chance < ( $salt_chance - 75 ) && $resource == 'salt' )
            {
                $terrain = "pr";
                $safe = "Y";
                $move = 3;
                insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                return;
            }
            else
            {
                $terrain = "pr";
                $safe = "Y";
                $move = 3;
                insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                return;
            }
        }
        elseif( $curr_zone == 3 )
        {
            $desert = rand( $zone_three, $zone_total );
            if( $desert < $display )
            {
                $terrain = "de";
                $safe = "N";
                $move = 3;
                insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                return;
            }
            else
            {
                if( $res_chance < ( $salt_chance - 75 ) && $resource == 'salt' )
                {
                    $terrain = "pr";
                    $safe = "Y";
                    $move = 3;
                    insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                    return;
                }
                else
                {
                    $terrain = "pr";
                    $safe = "Y";
                    $move = 3;
                    insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                    return;
                }
            }
        }
        else
        {
            $tundra = rand( 1, $zone_two );
            if( $tundra > $display )
            {
                $terrain = "tu";
                $safe = "N";
                $move = 3;
                insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                return;
            }
            else
            {
                if( $res_chance < ( $salt_chance - 75 ) && $resource == 'salt' )
                {
                    $terrain = "pr";
                    $safe = "Y";
                    $move = 3;
                    insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                    return;
                }
                else
                {
                    $terrain = "pr";
                    $safe = "Y";
                    $move = 3;
                    insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game);
                    return;
                }
            }
        }
}

function insertmap($display, $terrain, $res_pres, $resource, $move, $safe, $game)
{
    global $db, $dbtables, $map_width;
    if( $terrain == 'pr' )
    {
        $game = 30000;
    }
    elseif( $terrain == 'gh' )
    {
        $game = 35000;
    }
    elseif( $terrain == 'df' )
    {
        $game = 40000;
    }
    elseif( $terrain == 'dh' )
    {
        $game = 45000;
    }
    elseif( $terrain == 'cf' )
    {
        $game = 35000;
    }
    elseif( $terrain == 'ch' )
    {
        $game = 40000;
    }
    elseif( $terrain == 'lcm' )
    {
        $game = 25000;
    }
    elseif( $terrain == 'jg' )
    {
        $game = 55000;
    }
    elseif( $terrain == 'jh' )
    {
        $game = 60000;
    }
    elseif( $terrain == 'ljm' )
    {
        $game = 50000;
    }
    elseif( $terrain == 'sw' )
    {
        $game = 15000;
    }
    elseif( $terrain == 'hsm' || $terrain == 'hvm' )
    {
        $game = 2000;
    }
    elseif( $terrain == 'tu' )
    {
        $game = 10000;
    }
    elseif( $terrain == 'de' )
    {
        $game = 1000;
    }
    else
    {
        $game = 0;
    }

    $north = ( $display - $map_width );
    if( $north < 1 )
    {
        $north = 0;
    }
    $northeast = ($display - $map_width ) + 1;
    if( $northeast < 1 )
    {
        $northeast = 0;
    }
    $east = $display + 1;
    if( $east < 1 || $east > ( $map_width * $map_width ) )
    {
        $east = 0;
    }
    $southeast = ( $display + $map_width ) + 1;
    if( $southeast < 1 || $southeast > ( $map_width * $map_width ) )
    {
        $southeast = 0;
    }
    $south = ( $display + $map_width );
    if( $south > ( $map_width * $map_width ) )
    {
        $south = 0;
    }
    $southwest = ( $display + $map_width ) - 1;
    if( $southwest > ( $map_width * $map_width ) )
    {
        $southwest = 0;
    }
    $west = $display - 1;
    if( $west < 1 )
    {
        $west = 0;
    }
    $northwest = ( $display - $map_width ) - 1;
    if( $northwest < 1 )
    {
        $northwest = 0;
    }

    if( $resource == 'gold' )
    {
        $res_amount = rand(1000,50000);
    }
    elseif( $res_type == 'gems' )
    {
        $res_amount = rand(1,30000);
    }
    elseif( $res_type == 'silver' )
    {
        $res_amount = rand(5000,75000);
    }
    else
    {
        $res_amount = -1;
    }

    if( $terrain == 'pr' )
    {
        if( $resource != 'salt' )
        {
            $resource = '';
            $res_amount = -1;
            $res_pres = 'N';
        }
    }
    if( $terrain == 'hvm'
      || $terrain == 'hsm'
      || $terrain == 'o'
      || $terrain == 'l'
      || $terrain == 'de'
      || $terrain == 'sw'
      || $terrain == 'tu'
      || $terrain == 'df'
      || $terrain == 'cf'
      || $terrain == 'jg' )
    {
        $resource = '';
        $res_amount = -1;
        $res_pres = 'N';
    }
    if( $display <= 4096 && $display <> $display2 )
    {
        if( $res_pres == 'Y' )
        {
            $prospect = rand( 0, 15 );
        }
        else
        {
            $prospect = 0;
        }
        $hexseed = rand();
        srand($hexseed);
        ////east cleanup
        $i = 1 + ($map_width * $map_width);
        while( $i > 0 )
        {
            if( $display == $i )
            {
              $east = 0;
            }
            $i -= $map_width;
        }
        //OK- TODO - Up above, the if/else shit is making this do exactly what you tell it to
        //however it causes duplicate key errors , so temporarily its an insert ignore.
        //the if/else shit needs to be made a switch/case instead.... added to TODO for V 1.0
        $result2 = $db->Execute("INSERT IGNORE INTO $dbtables[hexes] VALUES("
                               ."'$display',"
                               ."'$terrain',"
                               ."'$north',"
                               ."'$east',"
                               ."'$south',"
                               ."'$west',"
                               ."'$northeast',"
                               ."'$southeast',"
                               ."'$southwest',"
                               ."'$northwest',"
                               ."'$res_pres',"
                               ."'$resource',"
                               ."'$res_amount',"
                               ."'$move',"
                               ."'$safe',"
                               ."'$game',"
                               ."'$prospect',"
                               ."'$hexseed')");
        $display2 = $display;
        //if( !$result2 )
        //{
        //    echo $db->ErrorMsg() . "($display)<BR>";
        //}
    }
}


function frac( $x0, $y0, $x2, $y2, $z0, $z1, $z2, $z3 )
{
    global $half, $YB, $WIDTH, $LEVEL_WATER, $pic, $picType, $steep, $sealevel;

    //  50% chance rise or descend

    $newz = round( ($z0+$z1+$z2+$z3) / 4);

    if ( rand() < $half )
    {
        $newz += round( my_rand() * ($y2-$y0) * $steep );
    }
    else
    {
        $newz -= round( my_rand() * ($y2-$y0) * $steep );
    }

    $xmid = ( $x0 + $x2) >> 1;
    $ymid = ( $y0 + $y2) >> 1;
    $z12 =  ( $z1 + $z2) >> 1;
    $z30 =  ( $z3 + $z0) >> 1;
    $z01 =  ( $z0 + $z1) >> 1;
    $z23 =  ( $z2 + $z3) >> 1;

    if ( (($x2-$x0)>$WIDTH) && (($y2-$y0)>$WIDTH) ) //effectively: if they are greater than 1
    {
        frac( $x0, $y0, $xmid, $ymid, $z0, $z01, $newz, $z30);
        frac( $xmid, $y0, $x2, $ymid, $z01, $z1, $z12, $newz);
        frac( $x0, $ymid, $xmid, $y2, $z30, $newz, $z23, $z3);
        frac( $xmid, $ymid, $x2, $y2, $newz, $z12, $z2, $z23);
    }
    else
    {
        if ( $newz <= $sealevel )                           // above sea level
        {
            $picType[$ymid*$YB+$xmid] = "l";
            $pic[$ymid*$YB+$xmid] = $newz;
        }
        else                                                //  below "sea level"
        {
            $picType[$ymid*$YB+$xmid] = "s";
            $pic[$ymid*$YB+$xmid] = $LEVEL_WATER;
        }
    }
}


function landscape()
{
    global $picType, $steep, $sealevel, $pic, $pic0, $buf2;
    global $LEVEL_UNDEFINED, $LEVEL_WATER, $NUM_TERRAIN_TYPE;
    global $Z1, $Z2, $Z3, $Z4;
    global $XA, $XB, $YA, $YB;
    global $XADD, $YADD;

    $steep = ( my_rand() / 2 ) + 0.75;
    $sealevel = round( (($NUM_TERRAIN_TYPE*3)/2+2) * my_rand() - (($NUM_TERRAIN_TYPE/2)+1) );

    $Z1 = round( ($NUM_TERRAIN_TYPE-1) * my_rand() - ($NUM_TERRAIN_TYPE/2));
    $Z2 = round( ($NUM_TERRAIN_TYPE-1) * my_rand() - ($NUM_TERRAIN_TYPE/2));
    $Z3 = round( ($NUM_TERRAIN_TYPE-1) * my_rand() - ($NUM_TERRAIN_TYPE/2));
    $Z4 = round( ($NUM_TERRAIN_TYPE-1) * my_rand() - ($NUM_TERRAIN_TYPE/2));

    frac( $XA, $YA, $XB, $YB, $Z1, $Z2, $Z3, $Z4);

    ksort($pic);
    $min_height=min($pic);
    $max_height=max($pic);
    $height_diff=abs($max_height-$min_height);
    $mod = 0 - $min_height;
    $new_min = $min_height+($height_diff - $max_height);
    $new_max = $max_height+($height_diff - $max_height);
    $new_diff = abs($new_max-$newmin);
    $num_els = count($pic);

    foreach ($pic as $key => $value)
    {
        $pic[$key] = (int)((($pic[$key]-$min_height) * ($NUM_TERRAIN_TYPE-1) ) / $height_diff)+1;
    }
    $min_height=min($pic);
    $max_height=max($pic);
    $height_diff=abs($max_height-$min_height);
    for ($i = 0; $i < $XB; $i++)
    {
        for ($j = 0; $j < $YB; $j++)
        {

            $pic0 = $LEVEL_UNDEFINED;
            $loc = $j*$YB+$i;
            if ( $picType[$j*$YB+$i] == "l" )
            {
                $pic0 = abs($pic[$j*$YB+$i] - $sealevel);
            }

            if ($picType[$j*$YB+$i] == "s" )
            {
                $pic0 = $LEVEL_WATER;
            }

            $buf2 .= ZColor($pic0);
        }

    }

    return $buf2;
}


if (ISSET($_REQUEST['load_map']))
{

    echo "<TR><TD COLSPAN=$XB>Displaying map from file</TD></TR>";

    $map_file = array();

    $map_file = file ($game_root."map.txt");

    foreach ($map_file AS $key => $value)
    {
        $map_file[$key] = trim($value);
    }

    $map_width = $map_file[0];
    $XB = $map_width;
    $YB = $map_width;
    $sea_shift=0;

    echo "<TR><TD COLSPAN=$XB>".count($map_file).", $map_width, $XB, $YB</TD></TR>";

    unset ($_REQUEST[invert_height]);

    $out2 = "";

    for ($i = 0; $i < $XB; $i++)
    {
        $out2 .= "<TR>";
        for ($j = 0; $j < $XB; $j++)
        {
            $out3 = ZColor($map_file[$i*$YB+$j+1]);
            $out2 .= $out3;
        }

        $out2 .= "</TR>";
    }
    echo $out2;

}
else
{

    if (ISSET($_REQUEST['save_map']))
    {
        if ( !$file = fopen ($game_root."map.txt", "w") )
        {
            echo "Unable to create file ".$game_root."map.txt!";
            page_footer();
        }
        elseif ( !fwrite ($file, "$map_width\n") )
        {
            echo "Unable to write to ".$game_root."map.txt!";
            page_footer();
        }
    }

    $out = landscape();

    if ( ISSET($_REQUEST['save_map']) )
    {
        /*
                foreach ($height_map AS $key => $value)
                {
                    $db->Execute("INSERT INTO `".$dbtables['height_map']."` "
                                ."(`height`)"
                                ."VALUES ('$value')");
                }
        */
        fclose($file);

    }

    echo " Done! seed used ($seed)<BR>";
        flush();
}


/*

This next bit is for calculating the climatic zones.

At the poles we will have an ice cap or, at least, the coldest zone. As we move toward the centre of the map, the temperature will get warmer.

But, in the TS context, we are not that likely to have map tiles for more than a few temperacy types, eg polar, tundra, temperate, tropics and equatorial.

So as we move toward the centre of the map, we should see zonal changes that reflect that idea. We may simply want to make those zones reflect a temperature rating and since temperature is more flexible, having greater range than 5 zone types. The zones will be calculated to reflect temperature on a scale or zero, polar, to one ten thousand, equatorial.

But our map is not going to look right if we do that based directly on how far from the edge of the map we are and how close to the centre we are, because the poles will be further away from the sun but as we move closer to the equator we will be moving over the surface of a ball, so that the temperature will increase rapidly as we move from the poles, ie we are curing out toward the sun and then down toward the equator and not moving straight from the pole to the equator.

Like this
    _
   /
  |
*

Not like this

    /
   /
  /
*

So we will move quickly toward the sun at the pole and slower as we near the equator. But that means that we will reach warmer temperaures faster.

So the method below does not simply base the temperature on the distance we have moved inward from the pole, but on the square of the distance we have moved inward from the pole.

We start out having moved 0% from the pole and finish up having moved 100% toward the equator, so our temperature goes from 0*0=0 to 100*100=10000.

But then we want to translate those temperatures to the tiles that we will show on our map and we will have different tiles for different temperate zones.

The way temperature will map to those zones will be something like a quarter sine curve


  ^         -
  |        /     Imagine this is a quarter curve
Temp      -

Degrees  0 > 90


At the start, in the first 20% from the left, the curve will not move upward much, ie it will not get much warmer. This reflects how much above zero temperature that we go before we move into the tundral climate zone. IE we wont move very far in temperature terms before we move from polar to tundral.

So we would move into tundra when $temperature/10000 = sin(20/100 * 90) = sin(18) = 0.31
ie when $temperature=3100

But the temperature is just the square of the proportion of the distance that we have moved from the pole, so the tundra line is SQRT(2000)% from the top of the map, ie

function zones()
{
    global $XB, $YB;

    Organise the gd_terrain table so that the plain, low hill, high hill, mountain, high mountain
    data is striated by temperacy. IE have 10% tundra in north and south, have 25% temperate inward, then 10% tropical zones inward, then 10% desert in the middle.

    Bias calculation of terrain type according to how far we are from the vertical centre line so that at polar caps, we get a chance of tundra on the outer 90% on each side, starting at 1% chance at the centre and increasing as we move outward. Do likewise with the other temperacy zones.

    Bit of a poxxy algorithm in the TS context since it will generate a map that looks, wrt temperacy, like a spherical globe on a map that is purely cartesian - lol



    $zone_free =    the amount of the zone that is guaranteedly of that zone
                    ie has no possibility of the terrain migrating to the next zone type

    $mid_line = $XB >> 1;  // half the line width
    $mid_world = $YB >> 1; // halfway through world

    for ($i=0; $i<=$mid_world; $i++)
    {
        //cumulative center of zone as we move from poles
        $zone_free_y = //total percent of land calculated so far

        // potential for terrain to migrate as we move away from top/bottom
        $chance_line_migrate =  ((($YB-$i)/$YB) - $zone_free_y);

        for ($j=0; $j<=$mid_line; $j++)
        {
            //we are j points from edge of map
            //
            $chance_migrate = ((($XB-$j)/$XB) - $zone_free) * $chance_line_migrate;

            // Do the hex in the top left

            $zone = $this_zone; //set the zone to the current temperacy
            if (my_rand() < $chance_migrate)
            {
                $zone = $zone + 1; //set the zone to the next one up
            }
            allocate_hex($zone, $i*$XB+$j);

            // Do the hex in the top right

            $zone = $this_zone; //set the zone to the current temperacy
            if (my_rand() < $chance_migrate)
            {
                $zone = $zone + 1; //set the zone to the next one up
            }
            allocate_hex( $zone, $i*$XB+($XB-$j) );

            // do the hex in the bottom left
            $zone = $this_zone; //set the zone to the current temperacy
            if (my_rand() < $chance_migrate)
            {
                $zone = $zone + 1; //set the zone to the next one up
            }
            allocate_hex($zone, ($YB*$XB)-($XB*$i)+$j);

            // do the hex in the bottom right
            $zone = $this_zone; //set the zone to the current temperacy
            if (my_rand() < $chance_migrate)
            {
                $zone = $zone + 1; //set the zone to the next one up
            }
            allocate_hex($zone, ($YB*$XB)-($XB*$i)-$j);
        }
    }
}
*/


?>


