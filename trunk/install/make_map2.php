<?php
error_reporting  (E_ERROR | E_WARNING | E_PARSE | !E_NOTICE);

session_start();
header("Cache-control: private");
include("../config.php");
include("../header.php");

connectdb();

$WIDTH = 1;
$XA = 0;
$XB = 64;					//width of map
$YA = 0;
$YB = 64;					//height of map
$Z1 = 0;
$Z2 = 0;
$Z3 = 0;
$Z4 = 0;

$zoom = 8;
$sea_shift = 0;
$map_width = 64;
//$XADD = 64;					// move x axis left on screen
$XADD = 128;					// move x axis left on screen
$YADD = 0;					// lower y axis view
$LEVEL_UNDEFINED = 99999;
$LEVEL_WATER = -6;			//sea level
$NUM_TERRAIN_TYPE = 18;      //number of terrain types

if (ISSET($_REQUEST['no_borders']))
{
	$border=0;
}
else
{
	$border=1;
}

if (ISSET($_REQUEST['zoom']))
{
	$zoom = $_REQUEST['zoom'];
}

$zoom_sel = array (
                "1" => "",
                "2" => "",
                "3" => "",
                "4" => "",
                "5" => "",
                "6" => "",
                "7" => "",
                "8" => ""
            );

$zoom_sel[$zoom] = "SELECTED";


if (ISSET($_REQUEST['map_width']))
{
	$map_width = $_REQUEST['map_width'];
	$XB = $map_width;
	$YB = $map_width;
}
$map_width_sel = array (
                     "8"   => "",
                     "16"  => "",
                     "32"  => "",
                     "64"  => "",
                     "128" => "",
                     "256" => ""
                 );
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

if (ISSET($_REQUEST['save_map']))
{
	$_REQUEST['keep_seed'] = "CHECKED";
}

if (ISSET($_REQUEST['load_map']))
{
	unset ($_REQUEST['sea_shift']);
}


if ( $_REQUEST['keep_seed'] == "CHECKED" )
{
	$seed = $_REQUEST['seed'];
}
else
{
	$seed = rand();
}

if (ISSET($_REQUEST['sea_shift']))
{
	$sea_shift = $_REQUEST['sea_shift'];
}

$sea_sel = array (
               "-5" => "",
               "-4" => "",
               "-3" => "",
               "-2" => "",
               "-1" => "",
               "0" => "",
               "1" => "",
               "2" => "",
               "3" => "",
               "4" => "",
               "5" => ""
           );

$sea_sel[$sea_shift] = "SELECTED";

//$seed = 1095720830.1;		//island
//$seed = 1095717738.6;		// island
//$seed = 1095702501;
//$seed = 1095716361.6;		//Nice continent
//1095651394.6
//29426
//Seed: 23522
//Seed: 5752 //inland sea, sea -3
//Seed: 14852 //Island
//$seed = 4479; //Interesting Island
//$seed = 9812; //Island
//$seed = 10674; $steep=1; //two lakes
//$seed = 13810; $steep=1; //nice bay
//$seed = 27572; $steep=2.5; //island
//$seed = 2715; $steep=2.5; //nice bay
//$seed = 3249;
//$seed = 16695;
//$seed = 26041; //interesting island
//$seed = 7155: //Krondor
//$seed = 916158095; //nice cont
//$seed = 32757; $map_width=256; $invert_height="CHECKED"; //SKULL MOUNTAIN
$seed = $_REQUEST[seed];

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
	global $LEVEL_WATER, $LEVEL_UNDEFINED, $NUM_TERRAIN_TYPE, $zoom, $sea_shift, $file, $height_map;
	global $db, $dbtables;

	$show_val=false;



	if (!ISSET($_REQUEST['load_map']))
	{

		if ( $z0 >= $LEVEL_UNDEFINED )
		{
			$t1=$NUM_TERRAIN_TYPE+1;
		}

		if (ISSET($_REQUEST['invert_height']))
		{
			$t1 = $z0 + $sea_shift;
		}
		else
		{
			$t1 = $NUM_TERRAIN_TYPE - $z0 + $sea_shift;
		}
		if ($sea_shift > 0 && $t1 > $NUM_TERRAIN_TYPE-1)
		{
			$t1 = $NUM_TERRAIN_TYPE-1;
		}
		elseif ($sea_shift < 0 && $sea_shift < $LEVEL_WATER)
		{
			$t1 = $LEVEL_WATER;
		}

		if ( $z0 <= $LEVEL_WATER )
		{
			$t1=$LEVEL_WATER;
		}


	}
	else
	{
		$t1 = $z0;
	}


	if ( $_REQUEST['save_map'] == "Save" )
	{
		fwrite ($file, "$t1\n");
	}


	//	$t1 = ($z0 % ($NUM_TERRAIN_TYPE-1))+1;
	if ( ISSET($_REQUEST['show_height']) )
	{
		$val = "$t1";
	}
	else
	{
		$val = "";
	}


	if ($t1<=$LEVEL_WATER)
	{
		return "<TD WIDTH=$zoom HEIGHT=$zoom BGCOLOR=\"#000099\"><FONT COLOR=BLACK>$val</FONT></TD>";
	}
        if ( $t1 == -5 )
        {
                return "<TD WIDTH=$zoom HEIGHT=$zoom BGCOLOR=\"#33FF63\"><FONT COLOR=BLACK>$val</FONT></TD>";
        }
        if ( $t1 == -4 )
        {
                return "<TD WIDTH=$zoom HEIGHT=$zoom BGCOLOR=\"#33FF53\"><FONT COLOR=BLACK>$val</FONT></TD>";
        }
        if ( $t1 == -3 )
        {
                return "<TD WIDTH=$zoom HEIGHT=$zoom BGCOLOR=\"#33FF43\"><FONT COLOR=BLACK>$val</FONT></TD>";
        }
	if ( $t1 == -2 )
	{
		return "<TD WIDTH=$zoom HEIGHT=$zoom BGCOLOR=\"#33FF33\"><FONT COLOR=BLACK>$val</FONT></TD>";
	}
	if ( $t1 == -1 )
	{
		return "<TD WIDTH=$zoom HEIGHT=$zoom BGCOLOR=\"#33FF33\"><FONT COLOR=BLACK>$val</FONT></TD>";
	}
	if (  $t1 == 0 )				//Lake
	{
		return "<TD WIDTH=$zoom HEIGHT=$zoom BGCOLOR=\"#33FF33\"><FONT COLOR=BLACK>$val</FONT></TD>";
	}
	if (  $t1 == '1' )				//Beach level
	{
		return "<TD WIDTH=$zoom HEIGHT=$zoom BGCOLOR=\"#33FF33\"><FONT COLOR=BLACK>$val</FONT></TD>";
	}
	if (  $t1 == '2' )				//Prairie
	{
		return "<TD WIDTH=$zoom HEIGHT=$zoom BGCOLOR=\"#33FF33\"><FONT COLOR=BLACK>$val</FONT></TD>";
	}

	if (  $t1 == '3' )
	{
		return "<TD WIDTH=$zoom HEIGHT=$zoom BGCOLOR=\"#23EF23\"><FONT COLOR=BLACK>$val</FONT></TD>";
	}
	if (  $t1 == '4' )
	{
		return "<TD WIDTH=$zoom HEIGHT=$zoom BGCOLOR=\"#13DF13\"><FONT COLOR=BLACK>$val</FONT></TD>";
	}
	if (  $t1 == '5' )
	{
		return "<TD WIDTH=$zoom HEIGHT=$zoom BGCOLOR=\"#03CF03\"><FONT COLOR=BLACK>$val</FONT></TD>";
	}

	if (  $t1 == '6')				//Low Hills
	{
		return "<TD WIDTH=$zoom HEIGHT=$zoom BGCOLOR=\"#009900\"><FONT COLOR=BLACK>$val</FONT></TD>";
	}

	if (  $t1 == '7' )
	{
		return "<TD WIDTH=$zoom HEIGHT=$zoom BGCOLOR=\"#008900\"><FONT COLOR=BLACK>$val</FONT></TD>";
	}
	if (  $t1 == '8' )
	{
		return "<TD WIDTH=$zoom HEIGHT=$zoom BGCOLOR=\"#007900\"><FONT COLOR=BLACK>$val</FONT></TD>";
	}

	if (  $t1 == '9' )				//High Hills
	{
		return "<TD WIDTH=$zoom HEIGHT=$zoom BGCOLOR=\"#FF9900\"><FONT COLOR=BLACK>$val</FONT></TD>";
	}

	if (  $t1 == '10' )
	{
		return "<TD WIDTH=$zoom HEIGHT=$zoom BGCOLOR=\"#CF8900\"><FONT COLOR=BLACK>$val</FONT></TD>";
	}
	if (  $t1 == '11' )
	{
		return "<TD WIDTH=$zoom HEIGHT=$zoom BGCOLOR=\"#AF7900\"><FONT COLOR=BLACK>$val</FONT></TD>";
	}

	if (  $t1 == '12' )				//Mountain
	{
		return "<TD WIDTH=$zoom HEIGHT=$zoom BGCOLOR=\"#BB6600\"><FONT COLOR=BLACK>$val</FONT></TD>";
	}

	if (  $t1 == '13' )
	{
		return "<TD WIDTH=$zoom HEIGHT=$zoom BGCOLOR=\"#9C4600\"><FONT COLOR=BLACK>$val</FONT></TD>";
	}

	if (  $t1 == '14' )				//High mountain
	{
		return "<TD WIDTH=$zoom HEIGHT=$zoom BGCOLOR=\"#000000\"><FONT COLOR=BLACK>$val</FONT></TD>";
	}


	return "<TD WIDTH=$zoom HEIGHT=$zoom BGCOLOR=\"#CFF900\"><FONT COLOR=BLACK>$val</FONT></TD>";

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
		if ( $newz <= $sealevel )							// above sea level
		{
			$picType[$ymid*$YB+$xmid] = "l";
			$pic[$ymid*$YB+$xmid] = $newz;
		}
		else												//  below "sea level"
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
	echo "MAX HEIGHT: $max_height, MIN HEIGHT: $min_height, DIFF: $height_diff<BR>";
	echo "NEW MAX: $new_max, NEW MIN: $new_min, NEW DIFF: $new_diff, NUM ELS: $num_els<BR>";


	foreach ($pic as $key => $value)
	{
		$pic[$key] = (int)((($pic[$key]-$min_height) * ($NUM_TERRAIN_TYPE-1) ) / $height_diff)+1;
	}

	$min_height=min($pic);
	$max_height=max($pic);
	$height_diff=abs($max_height-$min_height);
	echo "MAX HEIGHT: $max_height, MIN HEIGHT: $min_height, DIFF: $height_diff<BR>";
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

		$buf2 = "<TR WIDTH=\"100%\">".$buf2."</TR>";
	}

	return $buf2;
}



echo "<FORM METHOD=POST ACTION=make_map2.php>"
."<TABLE BORDER=1 CELLPADDING=4>"
."<TR BGCOLOR=$color_header>";

echo "<TD>Seed<BR><INPUT CLASS=edit_area NAME=seed VALUE=$_REQUEST[seed]></TD>";
echo "<TD>"
."<INPUT TYPE=CHECKBOX CLASS=edit_area NAME=keep_seed VALUE=\"CHECKED\" $_REQUEST[keep_seed]>"
."<INPUT TYPE=HIDDEN NAME=seed VALUE=$_REQUEST[seed]>"
." Keep Map<BR>"
."<INPUT TYPE=CHECKBOX CLASS=edit_area NAME=save_map VALUE=\"Save\">"
." Save Map<BR>"
."<INPUT TYPE=CHECKBOX CLASS=edit_area NAME=load_map VALUE=\"Load\">"
." Load Map"
."</TD>";

echo "<TD>"
."<INPUT TYPE=CHECKBOX CLASS=edit_area NAME=show_height VALUE=\"CHECKED\" $_REQUEST[show_height]>"
." Show Height<BR>"
."<INPUT TYPE=CHECKBOX CLASS=edit_area NAME=invert_height VALUE=\"CHECKED\" $_REQUEST[invert_height]>"
." Invert Height<BR>"
."<INPUT TYPE=CHECKBOX CLASS=edit_area NAME=no_borders VALUE=\"CHECKED\" $_REQUEST[no_borders]>"
." No Borders"
."</TD>";

echo "<TD>Width<BR><SELECT NAME=map_width>";
foreach ($map_width_sel AS $key => $value)
{
	echo "<OPTION $value>$key\n";
}
echo "</SELECT>"
."</TD>\n";

echo "<TD>Zoom<BR><SELECT NAME=zoom>";
for ($i=1; $i<=8; $i++)
{
	echo "<OPTION $zoom_sel[$i]>$i\n";
}
echo "</SELECT>"
."</TD>\n";

echo "<TD>Sea Shift<BR><SELECT NAME=sea_shift>";
foreach ($sea_sel AS $key => $value)
{
	echo "<OPTION $value>$key\n";
}
echo "</SELECT>"
."</TD>\n";

echo "<TD><INPUT TYPE=SUBMIT VALUE=Submit></TD>"
."</TR>"
."</TABLE>"
."</FORM>";

echo "To stay fixed on this map and change the zoom level on it, paste $seed into the Seed box."
."<BR>To randomly select a new map, deleted any number in the Seed box."
."<BR>To grow a five fingered plant in the Seed box paste <b>#.</b> into the seed Box ;)"
."<P>";

echo "<center>";

$table_width=$zoom*$XB;
echo "<TABLE WIDTH=$table_width BGCOLOR=$color_bg BORDER=$border BORDERCOLOR=BLACK CELLPADDING=0 CELLSPACING=0>";



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

		echo "<TR><TD COLSPAN=$XB>Map saved.".count($height_map)."</TD></TR>";
	}

	echo "<TR><TD COLSPAN=$XB>$_REQUEST[seed]</TD></TR>";
	echo $out;
}


echo "</TABLE>";

echo "</center>";

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
 
 
 
	$zone_free =	the amount of the zone that is guaranteedly of that zone
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


page_footer();
?>


