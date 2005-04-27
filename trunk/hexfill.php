<?php

include("config.php");
//ini_set("max_execution_time",3000000);


// CREATE DATABASE AND TABLE IF THEY ARE NOT THERE

IF($_REQUEST['STARTED']<>"Yes")
{
	echo "Creating database <b>$dbname</b><br>";

	$dbi=mysql_connect($dbhost, $dbuname, $dbpass);

	$sql = "CREATE DATABASE IF NOT EXISTS $dbname";

	$res=mysql_query($sql)
		or die (mysql_error());

	echo "Database $dbname created<p>";

	mysql_select_db($dbname);

	echo "Creating table <b>$dbname.".$db_prefix."hexes</b><br>";

	$sql = "CREATE TABLE IF NOT EXISTS `".$db_prefix."hexes` "
		."( `hex_id` int( 11 ) NOT NULL AUTO_INCREMENT ,"
		. " `terrain` text NOT NULL ,"
		. " `n` int( 11 ) NOT NULL default '0',"
		. " `e` int( 11 ) NOT NULL default '0',"
		. " `s` int( 11 ) NOT NULL default '0',"
		. " `w` int( 11 ) NOT NULL default '0',"
		. " `ne` int( 11 ) NOT NULL default '0',"
		. " `se` int( 11 ) NOT NULL default '0',"
		. " `sw` int( 11 ) NOT NULL default '0',"
		. " `nw` int( 11 ) NOT NULL default '0',"
		. " `resource` enum( 'Y', 'N' ) NOT NULL default 'N',"
		. " `res_type` text NOT NULL ,"
		. " `res_amount` int( 11 ) NOT NULL default '-1',"
		. " `move` int( 11 ) NOT NULL default '0',"
		. " `safe` set( 'Y', 'N' ) NOT NULL default 'Y',"
		. " `game` int( 11 ) NOT NULL default '0',"
		. " UNIQUE KEY `hex_id` ( `hex_id` ) ) TYPE = MYISAM PACK_KEYS = 0 AUTO_INCREMENT = 1";
	$res=mysql_query($sql)
		or die (mysql_error());

	echo "Table $dbname.".$db_prefix."hexes created<p>";

	echo "Creating table <b>$dbname.".$db_prefix."mapping</b><br>";

	$sql = "CREATE TABLE IF NOT EXISTS `".$db_prefix."mapping` "
		."( `hex_id` int( 11 ) NOT NULL default '0',"
        . " `0000` set( 'Y', 'N' ) NOT NULL default 'N',"
        . " UNIQUE KEY `hex_id` ( `hex_id` ) ) TYPE = MYISAM "; 
	$res=mysql_query($sql)
		or die (mysql_error());

	echo "Table $dbname.".$db_prefix."hexes created<p>";
	
	
	mysql_close($dbi);

	echo "<p>Database existence verified! (I Hope ;)<p>";
	echo "<FORM METHOD=POST ACTION=\"hexfill.php\">"
		."<INPUT TYPE=HIDDEN NAME=STARTED VALUE=Yes>"
		."<INPUT TYPE=SUBMIT NAME=make_map VALUE=\"Make Map!\">"
		."</FORM>";
}

$max_hex_id = 37501;
$chunk_size=10000;

// Game constants

	$sql = "SELECT COUNT(*) FROM `".$db_prefix."hexes`";                // Get the number of hexes created so far
	$dbi=mysql_connect($dbhost, $dbuname, $dbpass);
	mysql_select_db($dbname);

	$res=mysql_query($sql) or die (mysql_error());
	$row = mysql_fetch_row($res); 
	$cur_hex_id=$row[0] + 1;
	mysql_free_result($res); 
	mysql_close($dbi);

$next_chunk = $cur_hex_id + $chunk_size;
if ($next_chunk > $max_hex_id)
{
	$next_chunk = $max_hex_id;
}


$error_advice = "<p>If you just got a timeout from PHP then you should change the value of\n"
				."\$chunk_size in the file <b>hexfill.php</b> to be slightly lower.<p> \n"
				."If you just got told that you cannot connect to the database then\n"
				."wait a few moments and then refresh the page.<p> \n";

$hex_terrain = "";
$hex_resource = "N";
$res_type = "";
$res_amount = -1;
$hex_move = "";


// prairie numbers
$pr_move = 3;
$pr_chance = 300;

// grassy hills numbers
$gh_move = 5;
$gh_chance = 500; //out of 1000

// deciduous forest numbers
$df_move = 5;
$df_chance = 650;  //out of 1000
$dh_move = 6;
$dh_chance = 700;  //out of 1000

// coniferous forest numbers
$cf_move = 5;
$cf_chance = 800;  //out of 1000
$ch_move = 6;
$ch_chance = 900;  //out of 1000
$lcm_move = 10;
$lcm_chance = 965;  //out of 1000

// snowy mountains
$hsm_move = 25;
$hsm_chance = 975; //out of 1000

// swamps
$sw_move = 8;
$sw_chance = 980;

// jungle
$jg_move = 5;
$jg_chance = 985;
$jh_move = 6;
$jh_chance = 990;
$ljm_move = 10;
$ljm_chance = 995;

// waterbodies
$l_move = 30;
$l_chance = 1000;
$o_move = 30;

// desert
$de_move = 5;
$de_chance = 985;

// tundra
$tu_move = 4;
$tu_chance = 815;


$gem_chance = 3; //out of 10000
$gold_chance = 5; //out of 10000
$silver_chance = 17; //out of 10000
$iron_chance = 165; //out of 10000
$tin_chance = 320; //out of 10000
$zinc_chance = 470; //out of 10000
$copper_chance = 730; //out of 10000
$lead_chance = 825; //out of 10000
$salt_chance = 965; //out of 10000
$coal_chance = 1300;



function get_resource ($res_chance)
{
	global $hex_resource, $res_type, $res_amount;

$max_hex_id = 37501;
$cur_hex_id = 1;
$hex_terrain = "";
$hex_resource = "N";
$res_type = "";
$res_amount = -1;
$hex_move = "";


// prairie numbers
$pr_move = 3;
$pr_chance = 300;

// grassy hills numbers
$gh_move = 5;
$gh_chance = 500; //out of 1000

// deciduous forest numbers
$df_move = 5;
$df_chance = 650;  //out of 1000
$dh_move = 6;
$dh_chance = 700;  //out of 1000

// coniferous forest numbers
$cf_move = 5;
$cf_chance = 800;  //out of 1000
$ch_move = 6;
$ch_chance = 900;  //out of 1000
$lcm_move = 10;
$lcm_chance = 965;  //out of 1000

// snowy mountains
$hsm_move = 25;
$hsm_chance = 975; //out of 1000

// swamps
$sw_move = 8;
$sw_chance = 980;

// jungle
$jg_move = 5;
$jg_chance = 985;
$jh_move = 6;
$jh_chance = 990;
$ljm_move = 10;
$ljm_chance = 995;

// waterbodies
$l_move = 30;
$l_chance = 1000;
$o_move = 30;

// desert
$de_move = 5;
$de_chance = 985;

// tundra
$tu_move = 4;
$tu_chance = 815;


$gem_chance = 3; //out of 10000
$gold_chance = 5; //out of 10000
$silver_chance = 17; //out of 10000
$iron_chance = 165; //out of 10000
$tin_chance = 320; //out of 10000
$zinc_chance = 470; //out of 10000
$copper_chance = 730; //out of 10000
$lead_chance = 825; //out of 10000
$salt_chance = 965; //out of 10000
$coal_chance = 1300;


        if($res_chance < $gem_chance)
        {
        $hex_resource = "Y";
        $res_type = "gems";
        $res_amount = mt_rand(1,30000);
        }
        elseif($res_chance < $gold_chance)
        {
        $hex_resource = "Y";
        $res_type = "gold";
        $res_amount = mt_rand(1000,50000);
        }
        elseif($res_chance < $silver_chance)
        {
        $hex_resource = "Y";
        $res_type = "silver";
        $res_amount = mt_rand(5000,75000);
        }
        elseif($res_chance < $iron_chance)
        {
        $hex_resource = "Y";
        $res_type = "iron";
        $res_amount = -1;
        }
        elseif($res_chance < $tin_chance)
        {
        $hex_resource = "Y";
        $res_type = "tin";
        $res_amount = -1;
        }
        elseif($res_chance < $zinc_chance)
        {
        $hex_resource = "Y";
        $res_type = "zinc";
        $res_amount = -1;
        }
        elseif($res_chance < $copper_chance)
        {
        $hex_resource = "Y";
        $res_type = "copper";
        $res_amount = -1;
        }
        elseif($res_chance < $lead_chance)
        {
        $hex_resource = "Y";
        $res_type = "lead";
        $res_amount = -1;
        }
        elseif($res_chance < $salt_chance)
        {
        $hex_resource = "Y";
        $res_type = "salt";
        $res_amount = -1;
        }
        elseif($res_chance < $coal_chance)
        {
        $hex_resource = "Y";
        $res_type = "coal";
        $res_amount = -1;
        }
        else
        {
        $hex_resource = "N";
        $res_type = "";
        $res_amount = -1;
        }
}


if (ISSET($_REQUEST['make_map']))
{

$dbi=mysql_connect($dbhost, $dbuname, $dbpass);
mysql_select_db($dbname);

	while($cur_hex_id < $next_chunk)
	{
		$type_chance = mt_rand(1,1000);
		$res_chance = mt_rand(1,10000);

		if($type_chance < $pr_chance)
		{
			$hex_terrain = "pr";
			$safe = "Y";
			$hex_move = $pr_move;
		} 
		elseif($type_chance < $gh_chance)
		{
			$hex_terrain = "gh";
			get_resource($res_chance);
			$safe = "Y";
			$hex_move = $gh_move;
		}
		elseif($type_chance < $df_chance)
		{
			$hex_terrain = "df";
			$safe = "Y";
			$hex_move = $df_move;
		}
		elseif($type_chance < $dh_chance)
		{
			$hex_terrain = "dh";
			get_resource($res_chance);
			$safe= "Y";
			$hex_move = $dh_move;
		}
		elseif($type_chance < $cf_chance)
		{
			$hex_terrain = "cf";
			$hex_move = $cf_move;
			$safe = "Y";
		}
		elseif($type_chance < $ch_chance)
		{
			$hex_terrain = "ch";
			get_resource($res_chance);
			$safe = "Y";
			$hex_move = $ch_move;
		}
		elseif($type_chance < $lcm_chance)
		{
			$hex_terrain = "lcm";
			get_resource($res_chance);
			$safe = "N";
			$hex_move = $lcm_move;
		}
		elseif($type_chance < $hsm_chance)
		{
			$hex_terrain = "hsm";
			$hex_move = $hsm_move;
			$safe = "N";
		}
		elseif($type_chance < $sw_chance)
		{
			$hex_terrain = "sw";
			$safe = "N";
			$hex_move = $sw_move;
		}
		elseif($type_chance < $tu_chance)
		{
			$hex_terrain = "tu";
			$safe = "N";
			$hex_move = $tu_move;
		}
		elseif($type_chance < $jg_chance)
		{
			$hex_terrain = "jg";
			$safe = "Y";
			$hex_move = $jg_move;
		}
		elseif($type_chance < $jh_chance)
		{
			$hex_terrain = "jh";
			$safe = "Y";
			get_resource($res_chance);
			$hex_move = $jh_move;
		}
		elseif($type_chance < $ljm_chance)
		{
			$hex_terrain = "ljm";
			get_resource($res_chance);
			$safe = "N";
			$hex_move = $ljm_move;
		}
		elseif($type_chance < $de_chance)
		{
			$hex_terrain = "de";
			$safe = "N";
			$hex_move = $de_move;
		}
		elseif($type_chance < $l_chance)
		{
			$hex_terrain = "l";
			$hex_move = $l_move;
			$safe = "N";
		}
		else
		{
			$hex_terrain = "pr";
			$hex_move = $pr_move;
			$safe = "Y";
		}

		$cont_start = 2511;
		$cont_stop = 34989;

		if($cur_hex_id < $cont_start)
		{
			$hex_terrain = "o";
			$safe = "N";
			$hex_move = $o_move;
			$res_type = "";
			$hex_resource = "N";
			$res_amount = -1;
		}
		if($cur_hex_id > $cont_stop)
		{
			$hex_terrain = "o";
			$hex_move = $o_move;
			$res_type = "";
			$safe = "N";
			$hex_resource = "N";
			$res_amount = -1;
		}

		$n = $cur_hex_id - 250;
		$s = $cur_hex_id + 250;
		$e = $cur_hex_id + 1;
		$w = $cur_hex_id - 1 ;
		$ne = $cur_hex_id - 249;
		$nw = $cur_hex_id - 251;
		$se = $cur_hex_id + 251;
		$sw = $cur_hex_id + 249;

		if($cur_hex_id < 250)
		{
			$n = "0";
			$nw = "0";
			$ne = "0";
		}
		if($cur_hex_id > 37250)
		{
			$s = "0";
			$sw = "0";
			$se = "0";
		}

		// Some sanity checks, to make sure resources are only in hill hexes

		$safe = "Y";

		if($hex_terrain == "pr")
		{
			$res_type = "";
			$hex_resource = "N";
			$res_amount = -1;
		}
		elseif($hex_terrain == "df")
		{
			$res_type = "";
			$hex_resource = "N";
			$res_amount = -1;
		}
		elseif($hex_terrain == "cf")
		{
			$res_type = "";
			$hex_resource = "N";
			$res_amount = -1;
		}
		elseif($hex_terrain == "jg")
		{
			$res_type = "";
			$hex_resource = "N";
			$res_amount = -1;
		}
		elseif($hex_terrain == "tu")
		{
			$res_type = "";
			$hex_resource = "N";
			$res_amount = -1;
			$safe = "N";
		}
		elseif($hex_terrain == "sw")
		{
			$res_type = "";
			$hex_resource = "N";
			$res_amount = -1;
			$safe = "N";
		}
		elseif($hex_terrain == "de")
		{
			$res_type = "";
			$hex_resource = "N";
			$res_amount = -1;
			$safe = "N";
		}
		elseif($hex_terrain == "o")
		{
			$res_type = "";
			$hex_resource = "N";
			$res_amount = -1;
			$safe = "N";
		}
		elseif($hex_terrain == "l")
		{
			$res_type = "";
			$hex_resource = "N";
			$res_amount = -1;
			$safe = "N";
		}
		elseif($hex_terrain == "lcm")
		{
			$res_type = "";
			$hex_resource = "N";
			$res_amount = -1;
			$safe = "N";
		}
		elseif($hex_terrain == "ljm")
		{
			$res_type = "";
			$hex_resource = "N";
			$res_amount = -1;
			$safe = "N";
		}
		elseif($hex_terrain == "hsm")
		{
			$res_type = "";
			$hex_resource = "N";
			$res_amount = -1;
			$safe = "N";
		}


		$sql = "INSERT INTO `".$db_prefix."hexes` "
				."VALUES("
				."'',"
				."'$hex_terrain',"
				."'$n',"
				."'$e',"
				."'$s',"
				."'$w',"
				."'$ne',"
				."'$se',"
				."'$sw',"
				."'$nw',"
				."'$hex_resource',"
				."'$res_type',"
				."'$res_amount',"
				."'$hex_move',"
				."'$safe',"
				."'90000'"
				.")";

		$res=mysql_query($sql)
			or die ("<p>".mysql_error().$error_advice);

		$cur_hex_id++;

		if ($cur_hex_id % ($chunk_size/10) == 0)
		{
			print ".";
		}

	} // END WHILE

	mysql_close($dbi); 

	echo "<p>$cur_hex_id / $max_hex_id<p>"
		."<FORM METHOD=POST ACTION=\"hexfill.php\">";
	if ($cur_hex_id >= $max_hex_id)
	{
		echo "<b>All hexes created!</b><p>";
		echo "<INPUT TYPE=HIDDEN NAME=STARTED VALUE=Yes>"
			."<INPUT TYPE=HIDDEN NAME=start_ocean VALUE=\"2489\">"
			."<INPUT TYPE=HIDDEN NAME=stop_ocean VALUE=\"2511\">"
			."<INPUT TYPE=HIDDEN NAME=ii VALUE=\"2489\">"
			."<INPUT TYPE=SUBMIT NAME=clean_map VALUE=\"Clean up sides\"";
	}
	else
	{
		echo "<INPUT TYPE=HIDDEN NAME=STARTED VALUE=Yes>"
			."<INPUT TYPE=SUBMIT NAME=make_map VALUE=\"Create next $chunk_size hexes\">";
	}
	echo "</FORM>";

}



if (ISSET($_REQUEST['clean_map']))
{

	$start_ocean = $_REQUEST['start_ocean'];
	$stop_ocean = $_REQUEST['stop_ocean'];

	$nnext_chunk = $start_ocean + $chunk_size;
	if ($nnext_chunk > $max_hex_id)
	{
		$nnext_chunk = $max_hex_id+1;
	}

	$dbi=mysql_connect($dbhost, $dbuname, $dbpass);

	mysql_select_db($dbname);

	while($start_ocean < $nnext_chunk)
	{
		$sql = "UPDATE `".$db_prefix."hexes` "
		."SET "
		."terrain = 'o', "
		."move = '30', "
		."resource = 'N', "
		."res_type = '', "
		."safe = '$safe' "
		."WHERE hex_id > '$start_ocean' AND "
		."hex_id < '$stop_ocean'";
		$res=mysql_query($sql)
			or die ("<p>Died at $ii with<p>Start ocean: $start_ocean, Stop ocean: $stop_ocean<p>" .mysql_error());

	//	$db->Execute("UPDATE tribes.hexes2 SET terrain = 'o', move = '30', resource = 'N', res_type = '', safe = '$safe' WHERE hex_id > '$start_ocean' AND hex_id < '$stop_ocean'");
		$start_ocean = $start_ocean + 250;
		$stop_ocean = $stop_ocean + 250;
	}

	mysql_close($dbi); 

	if ($start_ocean < $max_hex_id)
	{
		echo "<p>$start_ocean / $max_hex_id hexes have been cleaned<p>";
		echo "<FORM METHOD=POST ACTION=\"hexfill.php\">"
			."<INPUT TYPE=HIDDEN NAME=STARTED VALUE=Yes>"
			."<INPUT TYPE=HIDDEN NAME=start_ocean VALUE=\"$start_ocean\">"
			."<INPUT TYPE=HIDDEN NAME=stop_ocean VALUE=\"$stop_ocean\">"
			."<INPUT TYPE=SUBMIT NAME=clean_map VALUE=\"Clean next $chunk_size hexes\">"
			."</FORM>";
	}
	else
	{
		echo "<b>The map has been created</b>";
		echo "<FORM METHOD=POST ACTION=\"hexfill.php\">"
			."<INPUT TYPE=HIDDEN NAME=STARTED VALUE=Yes>"
			."<INPUT TYPE=SUBMIT NAME=mapping VALUE=\"Create Mapping Table\">"
			."</FORM>";
	}
}


if (ISSET($_REQUEST['mapping']))
{

	echo "Creating the mapping table<BR>"
		."Please be patient while this<BR>"
		."process happens and do not stop<BR>"
		."your browser.<P>";

	$dbi=mysql_connect($dbhost, $dbuname, $dbpass);
	mysql_select_db($dbname);

	$i=1;
	if (ISSET($_REQUEST['start_hex']))
	{
		$i = $_REQUEST['start_hex'];
	}

	$end_hex = $i + $chunk_size;

	if ($end_hex >= $max_hex_id)
	{
		$end_hex = $max_hex_id;
	}

	echo "Mapping from $i to $end_hex.<P>";

	while ($i<$end_hex)
	{
		$sql = "INSERT INTO `".$db_prefix."mapping` SET hex_id='$i'";
		$res=mysql_query($sql)
			or die ("<p>Died at $i.<p>".mysql_error());
		$i++;
	}
	mysql_close($dbi);

	if ($i<$max_hex_id)
	{
		echo "Mapped $i hexes.";
		echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=hexfill.php?STARTED=Yes&mapping=yes&start_hex=$i\">";
/*
		echo "<FORM METHOD=POST ACTION=\"hexfill.php\">"
			."<INPUT TYPE=HIDDEN NAME=STARTED VALUE=yes>"
			."<INPUT TYPE=HIDDEN NAME=start_hex VALUE=\"$i\">"
			."<INPUT TYPE=SUBMIT NAME=mapping VALUE=\"Mapping next $chunk_size hexes\">"
			."</FORM>";
*/
	}
	else
	{
		echo "All map tables complete!<P>";
		echo "<FORM METHOD=POST ACTION=\"index.php\">"
			."<INPUT TYPE=SUBMIT VALUE=\"Create Admin Account\">"
			."</FORM>";
	}
		

}



