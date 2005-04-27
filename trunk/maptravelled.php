<?
session_start();
header("Cache-control: private");
	include("config.php");

	$title="Known World";
	//include("header.php");
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

	echo "<BR><FONT SIZE=+1>Click <a href=main.php>here</a> to return to the main menu.</FONT><br>";
	echo "<P>This map will be changed, to be more representative of a map, instead of being so random.";

	connectdb();

	if(!$_SESSION['clanid'])
	{
	  echo "You must <a href=index.php>login</a> before viewing this page.\n";
	  die();
	}

  
	$result = $db->Execute("SELECT hex_id, res_type, terrain FROM $dbtables[hexes] ORDER BY hex_id ASC");
	$row = $result->fields;
	
        bigtitle();
	
        echo "<TABLE border=0 cellpadding=0 bgcolor=black>\n";

          while(!$result->EOF){
	    $i = 0;
	    while($i < 250){
                $here = $db->Execute("SELECT * FROM $dbtables[mapping] WHERE hex_id = $row[hex_id] AND `0000` = 'Y'");
                if(!$here->EOF){
		$port=$row[terrain] . $row[res_type];
		$alt=$row[hex_id];
		$tile = "<TD><img src=images/" . $port . ".png title=$alt border=0></A></TD>";
                }
                else{
                $tile = "<TD><IMG SRC=images/unknown.png></TD>";
                }
                echo $tile;
              $result->Movenext();
	      $row = $result->fields;
	      $i++;
	      }
      	    echo "</TR>";
	    }




	echo "</TABLE>";
        echo "<BR><BR><P>";
	echo "<TABLE border=1 cellpadding=0>\n";
	echo "<TR><TD colspan=2 align=center> Map KEY </TD></TR>\n";
        echo "<TR><TD><img src=images/gh.png></TD><TD>Grassy Hills</TD></TR>\n";
        echo "<TR><TD><img src=images/df.png></TD><TD>Deciduous Forest</TD></TR>\n";
	echo "<TR><TD><img src=images/dh.png></TD><TD>Deciduous Hills</TD></TR>\n";
	echo "<TR><TD><img src=images/cf.png></TD><TD>Coniferous Forest</TD></TR>\n";
        echo "<TR><TD><img src=images/ch.png></TD><TD>Coniferous Hills</TD></TR>\n";
	echo "<TR><TD><img src=images/lcm.png></TD><TD>Low Coniferous Mountains</TD></TR>\n";
        echo "<TR><TD><img src=images/jg.png></TD><TD>Jungle</TD></TR>\n";
        echo "<TR><TD><img src=images/jh.png></TD><TD>Jungle Hills</TD></TR>\n";
	echo "<TR><TD><img src=images/ljm.png></TD><TD>Low Jungle Mountains</TD></TR>\n";
	echo "<TR><TD><img src=images/sw.png></TD><TD>Swamps</TD></TR>\n";
	echo "<TR><TD><img src=images/hsm.png></TD><TD>High Snowy Mountains</TD></TR>\n";
	echo "<TR><TD><img src=images/tu.png></TD><TD>Tundra</TD></TR>\n";
	echo "<TR><TD><img src=images/de.png></TD><TD>Desert</TD></TR>\n";
	echo "<TR><TD>&nbsp;</TD><TD>&nbsp;</TD></TR>\n";
	echo "<TR><TD><img src=images/l.png></TD><TD>Lake</TD></TR>\n";
	echo "<TR><TD><img src=images/o.png></TD><TD>Ocean</TD></TR>\n";
	echo "<TR><TD>&nbsp;</TD><TD>&nbsp;</TD></TR>\n";
        echo "<TR><TD><img src=images/unknown.png></TD><TD>Unexplored</TD></TR>\n";
	echo "</TABLE>\n";

	include("footer.php");
?> 
