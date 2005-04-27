<?
session_start();
header("Cache-control: private");
include("config.php");

$admincheck = $_SESSION['username'];
//here we should query the db to get admin's username or id or some such to auth
if($admincheck !== 'admin')
{
die("You Do Not Have permissions to view this page!");
}


page_header("Admin Mapping - Entire Map");
echo "<P>";

connectdb();

	$result = $db->Execute("SELECT hex_id, res_type, terrain FROM $dbtables[hexes] ORDER BY hex_id ASC");
	$row = $result->fields;
	
	echo "<TABLE border=0 cellpadding=0 bgcolor=black>\n";

	while(!$result->EOF)
	{
		$i = 0;
		echo "<TR>";
		while($i < 64 )
		{
			$port=$row[terrain] . $row[res_type];
			$alt=$row[hex_id];
			$tile = "<TD><img src=images/" . $port . ".png title=$alt border=0></TD>";
			echo $tile;
			$result->Movenext();
			$row = $result->fields;
			$i++;
		}
		echo "</TR>";
	}

	echo "</TABLE>";
	echo "<BR><BR><P>";

include("gui/table_map_key.php");
var_dump($_SESSION);

page_footer();

?> 
