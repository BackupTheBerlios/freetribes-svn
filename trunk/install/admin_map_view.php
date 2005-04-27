<?
include("../config.php");
$time_start = getmicrotime();
$title="TribeStrive Installer";
echo "<!doctype html public \"-//w3c//dtd html 3.2//en\">";
echo "<html>";
echo "<head>";
echo "<title>";
echo $title;
echo "</title>";
echo "<STYLE TYPE=\"text/css\">";
echo "<!--";
echo "A:link{text-decoration:none}";
echo "A:visited{text-decoration:none}";
echo "A:hover{text-decoration:underline}";
echo "-->";
echo "</STYLE>";
echo "</head>";
echo "<body background=\"\" bgcolor=\"#408c57\" text=\"#f4d7a4\" link=\"black\" vlink=\"black\" alink=\"#e5e3e0\">";
echo "<FONT FACE=\"Luxi Serif,Tahoma,Trebuchet MS\" POINT-SIZE=10pt>";
echo "\n";
//page_header("Admin Mapping - Entire Map");
echo "<P>";
$seed = $_REQUEST[seed];
echo "<A HREF=install_script.php?admin_name=$_REQUEST[admin_name]&password=$_REQUEST[password]&seed=$seed>Recreate</A> the world.<BR>";
echo "<A HREF=../help_maps.php>View</A> the map info.<BR>";
echo "<A HREF=../index.php>Log in</A> to the game.<BR>";
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
			$tile = "<TD><img src=../images/" . $port . ".png title=$alt border=0></TD>";
			echo $tile;
			$result->Movenext();
			$row = $result->fields;
			$i++;
		}
		echo "</TR>";
	}

	echo "</TABLE>";
	echo "<BR><BR><P>";


page_footer();
?> 
