<?
session_start();
header("Cache-control: private");
include("config.php");

$admincheck = $_SESSION['username'];
//here we should query the db to get admin's username or id or some such to auth
if($admincheck !== 'admin')
{
die("You Do Not	Have permissions to view this page!");
}
page_header("Admin Mapping - Edit World");
connectdb();


$username = $_SESSION['username'];
$admin = $db->Execute("SELECT * FROM $dbtables[chiefs] WHERE username = '$username'");
$admininfo = $admin->fields;

if(!$admininfo[admin] >= $privilege['adm_mapping'])
{
	echo "You must have admin mapping privilege to use this tool.<BR>\n";
	page_footer();
}


// Set new tile properties if the person editing them has asked them to be set
if ($_REQUEST['op']=="SET" && ISSET($_REQUEST['hex_id']))
{
	$terrain  = $_REQUEST['terrain'];
	$res_type = $_REQUEST['res_type'];
	$move     = $_REQUEST['move'];

	set_move_safe ($terrain, $move, $safe);
	val_res_type  ($terrain, $res_type);

	$resource = "Y";
	if ($res_type=="")
	{
		$resource = "N";
	}
	
	$db->Execute("UPDATE $dbtables[hexes] "
				."SET "
				."resource = '$resource', "
				."terrain  = '$terrain', "
				."move     = '$move', "
				."safe     = '$safe', "
				."res_type = '$res_type' "
				."WHERE hex_id = '$_REQUEST[hex_id]'");
}

// Get the terrain and resource current for the current hex
if (ISSET($_REQUEST['hex_id']))
{
	$hex = $db->Execute("SELECT terrain, res_type "
						."FROM $dbtables[hexes] "
						."WHERE hex_id = '$_REQUEST[hex_id]'");
	$terrain  = $hex->fields['terrain'];
	$res_type = $hex->fields['res_type'];
}

$terr_opt = array (
					"pr"	=> "",
					"gh"	=> "",
					"df"	=> "",
					"dh"	=> "",
					"cf"	=> "",
					"ch"	=> "",
					"lcm"	=> "",
					"jg"	=> "",
					"jh"	=> "",
					"ljm"	=> "",
					"hsm"	=> "",
					"tu"	=> "",
					"sw"	=> "",
					"de"	=> "",
					"l"		=> "",
					"o"		=> "",
					);
$terr_opt[$terrain] = "SELECTED";


if($res_type=="") // Set this for the page display
{
	$res_type = "None";
}

$res_opt = array (
					"None"		=> "",
					"coal"		=> "",
					"copper"	=> "",
					"iron"		=> "",
					"lead"		=> "",
					"salt"		=> "",
					"tin"		=> "",
					"zinc"		=> ""
					);
$res_opt[$res_type] = "SELECTED";


if(!ISSET($_REQUEST[hex_id]))
{

	echo "<FORM ACTION=admin_map_edit.php METHOD=POST>";
	echo "Which hex do you wish to center on?"
		."<INPUT TYPE=TEXT NAME=hex_id SIZE=7 WIDTH=7 VALUE=''>"
		."<INPUT TYPE=SUBMIT VALUE=SUBMIT>";
}
else
{
	$tribehex = array();
	$tribehex[hex_id] = $_REQUEST[hex_id];


	$startrow = $tribehex[hex_id] - 2511;
	$endrow = $startrow + 22;

	for ($i=0; $i<21; $i++)
	{
		${"res$i"} = $db->Execute("SELECT hex_id, res_type, terrain "
		                          ."FROM $dbtables[hexes] "
		                          ."WHERE hex_id > '$startrow' "
		                          ."AND hex_id < '$endrow'");
		$startrow += 250;
		$endrow = $startrow + 22;
	}


	$west_targ = $tribehex[hex_id] - 5;
	$east_targ = $tribehex[hex_id] + 5;
	$north_targ = $tribehex[hex_id] - 1250;
	$south_targ = $tribehex[hex_id] + 1250;
/*
	include("gui/table_map_nav.php");
*/

	echo "<TABLE BORDER=0 CELLPADDING=0>"
		."<TR>"
		."<TD COLSPAN=3 ALIGN=CENTER>Navigation</TD>"
		."</TR>"
		."<TR>"
		."<TD ROWSPAN=3 ALIGN=CENTER VALIGN=CENTER>"
		."<A HREF=admin_map_edit.php?hex_id=$west_targ><IMG SRC=images/arrowwest.gif WIDTH=25 BORDER=0></A>"
		."</TD>"
		."<TD ALIGN=CENTER>"
		."<TABLE BORDER=0 CELLPADDING=0>"
		."<TR>"
		."<TD ALIGN=CENTER>"
		."<A HREF=admin_map_edit.php?hex_id=$north_targ><IMG SRC=images/arrownorth.gif HEIGHT=25 BORDER=0></A>"
		."</TD>"
		."</TR>"
		."<TR>"
		."<TD ALIGN=CENTER>"
		."<A HREF=admin_map_edit.php?hex_id=$tribe_position>Back</A>"
		."</TD>"
		."</TR>"
		."<TR>"
		."<TD ALIGN=CENTER>"
		."<A HREF=admin_map_edit.php?hex_id=$south_targ><IMG SRC=images/arrowsouth.gif HEIGHT=25 BORDER=0></A>"
		."</TD>"
		."</TR>"
		."</TABLE>"
		."</TD>"
		."<TD ROWSPAN=3 ALIGN=CENTER VALIGN=CENTER>"
		."<A HREF=admin_map_edit.php?hex_id=$east_targ><IMG SRC=images/arroweast.gif WIDTH=25 BORDER=0></A>"
		."</TD>"
		."</TR>"
		."</TABLE>";

	// Display terrain / resource in current tile
	echo "<CENTER><TABLE BORDER=0 WIDTH=\"100%\"><TR><TD BGCOLOR=$color_header ALIGN=CENTER><FONT SIZE=+2>Current Terrain Type/Resource</FONT></TD></TR>";

	echo "<TR><TD BGCOLOR=$color_line1 ALIGN=CENTER><FONT SIZE=+2 COLOR=white>$terrain / $res_type</FONT></TD></TR>";
	echo "<TR><TD BGCOLOR=$color_line2 ALIGN=CENTER><FONT SIZE=+1>Do not assign a non-hill terrain to a resource hex.</TD></TR></TABLE></CENTER>";
	echo "<P>";

	echo "<CENTER><FORM ACTION=admin_map_edit.php METHOD=POST>";
	echo "Which hex do you wish to center on?<INPUT CLASS=edit_area TYPE=TEXT NAME=hex_id SIZE=7 WIDTH=7 VALUE='".$_REQUEST['hex_id']."'><INPUT TYPE=SUBMIT NAME=op VALUE=SUBMIT></FORM>";


	echo "<TABLE border=0 cellpadding=0 bgcolor=black>"
		."<FORM ACTION=admin_map_edit.php METHOD=POST>"
		."<TR>"
		."<TD>Terrain:"
		."<SELECT NAME=terrain>";
	foreach ($terr_opt AS $key => $value)
	{
		echo "<OPTION ".$terr_opt[$key].">$key</OPTION>";
	}
	echo "</SELECT>"
		."</TD>"
		."<TD>"
		."<SELECT NAME=res_type>";
	foreach ($res_opt AS $key => $value)
	{
		echo "<OPTION ".$res_opt[$key].">$key</OPTION>";
	}
	echo "</SELECT>"
		."</TD>"
		."</TD>"
		."<TD>"
		."<INPUT TYPE=HIDDEN NAME=hex_id VALUE=$_REQUEST[hex_id]>"
		."<INPUT TYPE=SUBMIT NAME=op VALUE=SET>"
		."</TD>"
		."</FORM>"
		."</TR>"
		."</TABLE>"
		."</CENTER>";



	echo "<TABLE border=0 cellpadding=0><TR><TD VALIGN=TOP>\n";

	include("gui/table_map_key.php");

	echo "</TD><TD>";

	echo "<TABLE BORDER=1 CELLPADDING=0 CELLSPACING=0 BGCOLOR=BLACK>";

	for ($i=0; $i<21; $i++)
	{
		echo"<TR>";
		$r=0;
		while(!${"res$i"}->EOF)
		{
			$row = ${"res$i"}->fields;
			$terrain = $row[terrain];
			$res_type = $row[res_type];
			$port=$terrain . $res_type;
			$alt=$row[hex_id];
			if ($r==10 && $i==10)
			{
				$highlight = " BORDERCOLOR=RED";
			}
			else
			{
				$highlight = "";
			}
			$tile = "<TD$highlight>"
					."<A HREF=admin_map_edit.php?hex_id=$alt&terrain=$terrain&res_type=$res_type&safe=$safe&move=$move>"
					."<IMG src=images/" . $port . ".png title=$alt>"
					."</A>"
					."</TD>";
			echo $tile;
			$r++;
			${"res$i"}->Movenext();
		}
		echo "</TR>";
	}

	echo "</TABLE></FORM>";




	echo "</TABLE>";
	echo "</TD></TR>";
	echo "</TABLE>\n";

}


function set_move_safe($terrain, &$move, &$safe)
{
	if($terrain == 'o')
	{
		$move = '30';
		$safe = 'N';
	}
	elseif($terrain == 'pr')
	{
		$move = '3';
		$safe = 'Y';
	}
	elseif($terrain == 'cf')
	{
		$move = '5';
		$safe = 'Y';
	}
	elseif($terrain == 'de')
	{
		$move = '5';
		$safe = 'N';
	}
	elseif($terrain == 'ch')
	{
		$move = '6';
		$safe = 'Y';
	}
	elseif($terrain == 'dh')
	{
		$move = '6';
		$safe = 'Y';
	}
	elseif($terrain == 'gh')
	{
		$move = '5';
		$safe = 'Y';
	}
	elseif($terrain == 'ljm')
	{
		$move = '10';
		$safe = 'N';
	}
	elseif($terrain == 'lcm')
	{
		$move = '10';
		$safe = 'N';
	}
	elseif($terrain == 'jg')
	{
		$move = '5';
		$safe = 'Y';
	}
	elseif($terrain == 'hsm')
	{
		$move = '25';
		$safe = 'N';
	}
	elseif($terrain == 'l')
	{
		$move = '30';
		$safe = 'N';
	}
	elseif($terrain == 'df')
	{
		$move = '5';
		$safe = 'Y';
	}
	elseif($terrain == 'sw')
	{
		$move = '8';
		$safe = 'N';
	}
	elseif($terrain == 'jh')
	{
		$move = '6';
		$safe = 'Y';
	}
	else
	{
		$move = '4';
		$safe = 'Y';
	}
}

function val_res_type ($terrain, &$res_type)
{
	if($res_type=="None") // Set this for the page display
	{
		$res_type = "";
	}
	if( $terrain == 'pr'  &&  $res_type == 'salt' )
	{
		$res_type = 'salt';
	}
	if( $terrain == 'lcm' ||
		$terrain == 'ljm' ||
		$terrain == 'jh'  ||
		$terrain == 'dh'  ||
		$terrain == 'ch'  ||
		$terrain == 'gh')
	{
		$res_type = $res_type;
	}
	else
	{
		$res_type = '';
	}
}

page_footer();
?>
