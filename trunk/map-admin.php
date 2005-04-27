<?
session_start();
header("Cache-control: private");
	include("config.php");

	include("header.php");
$title = "Map Editor";
	connectdb();
        bigtitle();
$username = $_SESSION['username'];
$admin = $db->Execute("SELECT * FROM $dbtables[chiefs] WHERE username = '$username'");
$admininfo = $admin->fields;

if(!$admininfo[admin] > '1'){
echo "You must be an administrator to use this tool.<BR>\n";
die();
}

if(!ISSET($_REQUEST[hex_id])){

echo "<FORM ACTION=map-admin.php METHOD=POST>";
echo "Which hex do you wish to center on?<INPUT TYPE=TEXT NAME=hex_id SIZE=7 WIDTH=7 VALUE=''><INPUT TYPE=SUBMIT VALUE=SUBMIT>";
}
else {
$tribehex = array();
$tribehex[hex_id] = $_REQUEST[hex_id];  
	$startrow0 = $tribehex[hex_id] - 2511;
	$endrow0 = $startrow0 + 22;
	$res0 = $db->Execute("SELECT hex_id, res_type, terrain FROM $dbtables[hexes] WHERE hex_id > '$startrow0' AND hex_id < '$endrow0'");
	
	$startrow1 = $startrow0 + 250;
	$endrow1 = $startrow1 + 22;
	$res1 = $db->Execute("SELECT hex_id, res_type, terrain FROM $dbtables[hexes] WHERE hex_id > '$startrow1' AND hex_id < '$endrow1'");

	$startrow2 = $startrow1 + 250;
	$endrow2 = $startrow2 + 22;
	$res2 = $db->Execute("SELECT hex_id, res_type, terrain FROM $dbtables[hexes] WHERE hex_id > '$startrow2' AND hex_id < '$endrow2'");

	$startrow3 = $startrow2 + 250;
	$endrow3 = $startrow3 + 22;
	$res3 = $db->Execute("SELECT hex_id, res_type, terrain FROM $dbtables[hexes] WHERE hex_id > '$startrow3' AND hex_id < '$endrow3'");

	$startrow4 = $startrow3 + 250;
	$endrow4 = $startrow4 + 22;
	$res4 = $db->Execute("SELECT hex_id, res_type, terrain FROM $dbtables[hexes] WHERE hex_id > '$startrow4' AND hex_id < '$endrow4'");

	$startrow5 = $startrow4 + 250;
	$endrow5 = $startrow5 + 22;
	$res5 = $db->Execute("SELECT hex_id, res_type, terrain FROM $dbtables[hexes] WHERE hex_id > '$startrow5' AND hex_id < '$endrow5'");
	
	$startrow6 = $startrow5 + 250;
	$endrow6 = $startrow6 + 22;
	$res6 = $db->Execute("SELECT hex_id, res_type, terrain FROM $dbtables[hexes] WHERE hex_id > '$startrow6' AND hex_id < '$endrow6'");

	$startrow7 = $startrow6 + 250;
	$endrow7 = $startrow7 + 22;
	$res7 = $db->Execute("SELECT hex_id, res_type, terrain FROM $dbtables[hexes] WHERE hex_id > '$startrow7' AND hex_id < '$endrow7'");

	$startrow8 = $startrow7 + 250;
	$endrow8 = $startrow8 + 22;
	$res8 = $db->Execute("SELECT hex_id, res_type, terrain FROM $dbtables[hexes] WHERE hex_id > '$startrow8' AND hex_id < '$endrow8'");

	$startrow9 = $startrow8 + 250;
	$endrow9 = $startrow9 + 22;
	$res9 = $db->Execute("SELECT hex_id, res_type, terrain FROM $dbtables[hexes] WHERE hex_id > '$startrow9' AND hex_id < '$endrow9'");

	$startrow10 = $startrow9 + 250;
	$endrow10 = $startrow10 + 22;
	$res10 = $db->Execute("SELECT hex_id, res_type, terrain FROM $dbtables[hexes] WHERE hex_id > '$startrow10' AND hex_id < '$endrow10'");
	
	$startrow11 = $startrow10 + 250;
	$endrow11 = $startrow11 + 22;
	$res11 = $db->Execute("SELECT hex_id, res_type, terrain FROM $dbtables[hexes] WHERE hex_id > '$startrow11' AND hex_id < '$endrow11'");

	$startrow12 = $startrow11 + 250;
	$endrow12 = $startrow12 + 22;
	$res12 = $db->Execute("SELECT hex_id, res_type, terrain FROM $dbtables[hexes] WHERE hex_id > '$startrow12' AND hex_id < '$endrow12'");

	$startrow13 = $startrow12 + 250;
	$endrow13 = $startrow13 + 22;
	$res13 = $db->Execute("SELECT hex_id, res_type, terrain FROM $dbtables[hexes] WHERE hex_id > '$startrow13' AND hex_id < '$endrow13'");

	$startrow14 = $startrow13 + 250;
	$endrow14 = $startrow14 + 22;
	$res14 = $db->Execute("SELECT hex_id, res_type, terrain FROM $dbtables[hexes] WHERE hex_id > '$startrow14' AND hex_id < '$endrow14'");

	$startrow15 = $startrow14 + 250;
	$endrow15 = $startrow15 + 22;
	$res15 = $db->Execute("SELECT hex_id, res_type, terrain FROM $dbtables[hexes] WHERE hex_id > '$startrow15' AND hex_id < '$endrow15'");

	$startrow16 = $startrow15 + 250;
	$endrow16 = $startrow16 + 22;
	$res16 = $db->Execute("SELECT hex_id, res_type, terrain FROM $dbtables[hexes] WHERE hex_id > '$startrow16' AND hex_id < '$endrow16'");

	$startrow17 = $startrow16 + 250;
	$endrow17 = $startrow17 + 22;
	$res17 = $db->Execute("SELECT hex_id, res_type, terrain FROM $dbtables[hexes] WHERE hex_id > '$startrow17' AND hex_id < '$endrow17'");

	$startrow18 = $startrow17 + 250;
	$endrow18 = $startrow18 + 22;
	$res18 = $db->Execute("SELECT hex_id, res_type, terrain FROM $dbtables[hexes] WHERE hex_id > '$startrow18' AND hex_id < '$endrow18'");

	$startrow19 = $startrow18 + 250;
	$endrow19 = $startrow19 + 22;
	$res19 = $db->Execute("SELECT hex_id, res_type, terrain FROM $dbtables[hexes] WHERE hex_id > '$startrow19' AND hex_id < '$endrow19'");

	$startrow20 = $startrow19 + 250;
	$endrow20 = $startrow20 + 22;
	$res20 = $db->Execute("SELECT hex_id, res_type, terrain FROM $dbtables[hexes] WHERE hex_id > '$startrow20' AND hex_id < '$endrow20'");



        $west_targ = $tribehex[hex_id] - 5;
        $east_targ = $tribehex[hex_id] + 5;
        $north_targ = $tribehex[hex_id] - 1250;
        $south_targ = $tribehex[hex_id] + 1250;
        echo "<TABLE BORDER=0 CELLPADDING=0><TR><TD COLSPAN=3 ALIGN=CENTER>Navigation</TD></TR>\n";
        echo "<TR><TD ROWSPAN=3 ALIGN=CENTER VALIGN=CENTER><A HREF=map-admin.php?hex_id=$west_targ>\n";
        echo "<IMG SRC=images/arrowwest.gif WIDTH=25 BORDER=0></A></TD><TD ALIGN=CENTER><TABLE BORDER=0 CELLPADDING=0>\n";
        echo "<TR><TD ALIGN=CENTER><A HREF=map-admin.php?hex_id=$north_targ>\n";
        echo "<IMG SRC=images/arrownorth.gif HEIGHT=25 BORDER=0></A></TD></TR><TR><TD ALIGN=CENTER>\n";
        echo "<A HREF=map-admin.php?hex_id=$tribe_position>Back</A></TD></TR>\n";
        echo "<TR><TD ALIGN=CENTER><A HREF=map-admin.php?hex_id=$south_targ>\n";
        echo "<IMG SRC=images/arrowsouth.gif HEIGHT=25 BORDER=0></A></TD></TR></TABLE></TD>\n";
        echo "<TD ROWSPAN=3 ALIGN=CENTER VALIGN=CENTER><A HREF=map-admin.php?hex_id=$east_targ>\n";
        echo "<IMG SRC=images/arroweast.gif WIDTH=25 BORDER=0></A></TD></TR></TABLE>\n";



echo "<CENTER><TABLE BORDER=0 WIDTH=\"100%\"><TR><TD BGCOLOR=$color_header ALIGN=CENTER><FONT SIZE=+2>Current Terrain Type/Resource</FONT></TD></TR>";
if($_SESSION[res_type] == ''){
$resourcetype = 'None';
}
else{
$resourcetype = $_SESSION[res_type];
}
echo "<TR><TD BGCOLOR=$color_line1 ALIGN=CENTER><FONT SIZE=+2 COLOR=white>$_SESSION[terrain] / $resourcetype</FONT></TD></TR>";
echo "<TR><TD BGCOLOR=$color_line2 ALIGN=CENTER><FONE SIZE=+1>Do not assign a non-hill terrain to a resource hex.</TD></TR></TABLE></CENTER>";
echo "<P>";

if(ISSET($_REQUEST[hex_id])){
$_SESSION[hex_id] = $_REQUEST[hex_id];
}
else {
$_REQUEST[hex_id] = $_SESSION[hex_id];
}

$_SESSION[terrain] = $_REQUEST[terrain];
if( $_REQUEST[terrain] == 'lcm' | $_REQUEST[terrain] == 'ljm' | $_REQUEST[terrain] == 'jh' | $_REQUEST[terrain] == 'dh' | $_REQUEST[terrain] == 'ch' | $_REQUEST[terrain] == 'gh'){
$_SESSION[res_type] = $_REQUEST[res_type];
}
elseif( $_REQUEST[terrain] == 'pr'  &&  !$_REQUEST[res_type] == '' )
{
$_SESSION[res_type] = 'salt';
}
else{
$_SESSION[res_type] = '';
}

if($_REQUEST[res_type] == ' '){
$_SESSION[res_type] = '';
}

if($_REQUEST[terrain] == ''){
$curterr = $db->Execute("SELECT terrain FROM $dbtables[hexes] WHERE hex_id = '$_REQUEST[hex_id]'");
$terrain = $curterr->fields;
$_REQUEST[terrain] = $terrain[terrain];
}

if($_SESSION[terrain] == 'o'){
$move = '30';
$safe = 'N';
}
elseif($_SESSION[terrain] == 'pr'){
$move = '3';
$safe = 'Y';
}
elseif($_SESSION[terrain] == 'cf'){
$move = '5';
$safe = 'Y';
}
elseif($_SESSION[terrain] == 'de'){
$move = '5';
$safe = 'N';
}
elseif($_SESSION[terrain] == 'ch'){
$move = '6';
$safe = 'Y';
}
elseif($_SESSION[terrain] == 'dh'){
$move = '6';
$safe = 'Y';
}
elseif($_SESSION[terrain] == 'gh'){
$move = '5';
$safe = 'Y';
}
elseif($_SESSION[terrain] == 'ljm'){
$move = '10';
$safe = 'N';
}
elseif($_SESSION[terrain] == 'lcm'){
$move = '10';
$safe = 'N';
}
elseif($_SESSION[terrain] == 'jg'){
$move = '5';
$safe = 'Y';
}
elseif($_SESSION[terrain] == 'hsm'){
$move = '25';
$safe = 'N';
}
elseif($_SESSION[terrain] == 'l'){
$move = '30';
$safe = 'N';
}
elseif($_SESSION[terrain] == 'df'){
$move = '5';
$safe = 'Y';
}
elseif($_SESSION[terrain] == 'sw'){
$move = '8';
$safe = 'N';
}
elseif($_SESSION[terrain] == 'jh'){
$move = '6';
$safe = 'Y';
}
else {
$move = '4';
$safe = 'Y';
}




echo "<CENTER><FORM ACTION=map-admin.php METHOD=POST>";
echo "Which hex do you wish to center on?<INPUT CLASS=edit_area TYPE=TEXT NAME=hex_id SIZE=7 WIDTH=7 VALUE=''><INPUT TYPE=SUBMIT VALUE=SUBMIT></FORM>";


echo "<TABLE border=0 cellpadding=0 bgcolor=black>\n";
 echo "<FORM ACTION=map-admin.php METHOD=POST>";
echo "<INPUT TYPE=HIDDEN NAME=change_terrain VALUE=$new_terrain><INPUT TYPE=HIDDEN NAME=change_res_type VALUE=$new_res_type>";
echo "<TR><TD>Terrain:<SELECT NAME=terrain><OPTION>pr</OPTION><OPTION>gh</OPTION><OPTION>l</OPTION><OPTION>df</OPTION><OPTION>cf</OPTION><OPTION>jg</OPTION><OPTION>dh</OPTION><OPTION>ch</OPTION><OPTION>jh</OPTION><OPTION>lcm</OPTION><OPTION>ljm</OPTION><OPTION>tu</OPTION><OPTION>sw</OPTION><OPTION>de</OPTION><OPTION>hsm</OPTION><OPTION>o</OPTION></SELECT></TD>";
 echo "<TD><SELECT NAME=res_type><OPTION SELECTED></OPTION><OPTION>coal</OPTION><OPTION>lead</OPTION><OPTION>copper</OPTION><OPTION>iron</OPTION><OPTION>zinc</OPTION><OPTION>tin</OPTION><OPTION>salt</OPTION></SELECT></TD>";
echo "</TD><TD><INPUT TYPE=HIDDEN NAME=hex_id VALUE=$_REQUEST[hex_id]><INPUT TYPE=SUBMIT VALUE=SET></TD></FORM></TR>";
echo "</TABLE></CENTER>";



	echo "<TABLE border=0 cellpadding=0><TR><TD>\n";

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
	echo "</TD><TD>";

	echo "<TABLE border = 0 cellpadding = 0 bgcolor=black>";

		////////////////////////////do the first row////////////////////////////////////
	echo "<TR>";
	while(!$res0->EOF){
		$row = $res0->fields;
		$port=$row[terrain] . $row[res_type];
		$alt = $row[hex_id];
		$tile = "<TD><A HREF=\"map-admin.php?hex_id=$alt&terrain=$_SESSION[terrain]&res_type=$_SESSION[res_type]&safe=$safe&move=$move\"><INPUT TYPE=image src=images/" . $port . ".png title=$alt></A></TD>";
                echo $tile;
              $res0->Movenext();
	      $row = $res0->fields;
	      }
      	    echo "</TR>";
	//////////////////////////second row now////////////////////////////////////////
	echo "<TR>";
        while(!$res1->EOF){
                $row = $res1->fields;
                $port=$row[terrain] . $row[res_type];
		$alt = $row[hex_id];
                $tile = "<TD><A HREF=\"map-admin.php?hex_id=$alt&terrain=$_SESSION[terrain]&res_type=$_SESSION[res_type]&safe=$safe&move=$move\"><INPUT TYPE=image src=images/" . $port . ".png title=$alt></A></TD>";
                echo $tile;
              $res1->Movenext();
              $row = $res1->fields;
              }
            echo "</TR>";
	///////////////////////////Three in a row//////////////////////////////////////
        echo"<TR>";
        while(!$res2->EOF){
                $row = $res2->fields;
                $port=$row[terrain] . $row[res_type];
		$alt=$row[hex_id];
                $tile = "<TD><A HREF=map-admin.php?hex_id=$alt&terrain=$_SESSION[terrain]&res_type=$_SESSION[res_type]&safe=$safe&move=$move><INPUT TYPE=image src=images/" . $port . ".png title=$alt></A></TD>";
                echo $tile;
              $res2->Movenext();
              $row = $res2->fields;
              }
            echo "</TR>";
	/////////////////////////////Four in a row/////////////////////////////////////
        echo"<TR>";
        while(!$res3->EOF){
                $row = $res3->fields;
                $port=$row[terrain] . $row[res_type];
		$alt=$row[hex_id];
		$tile = "<TD><A HREF=map-admin.php?hex_id=$alt&terrain=$_SESSION[terrain]&res_type=$_SESSION[res_type]&safe=$safe&move=$move><INPUT TYPE=image src=images/" . $port . ".png title=$alt></A></TD>";
                echo $tile;
              $res3->Movenext();
              $row = $res3->fields;
              }
            echo "</TR>";
	////////////////////////////////Five/////////////////////////////////////////
        echo"<TR>";
        while(!$res4->EOF){
                $row = $res4->fields;
                $port=$row[terrain] . $row[res_type];
		$alt=$row[hex_id];
		$tile = "<TD><A HREF=map-admin.php?hex_id=$alt&terrain=$_SESSION[terrain]&res_type=$_SESSION[res_type]&safe=$safe&move=$move><INPUT TYPE=image src=images/" . $port . ".png title=$alt></A></TD>";
                echo $tile;
              $res4->Movenext();
              $row = $res4->fields;
              }
            echo "</TR>";
	/////////////////////////////////Six////////////////////////////////////////
        echo"<TR>";
        while(!$res5->EOF){
                $row = $res5->fields;
                $port=$row[terrain] . $row[res_type];
		$alt=$row[hex_id];
		$tile = "<TD><A HREF=map-admin.php?hex_id=$alt&terrain=$_SESSION[terrain]&res_type=$_SESSION[res_type]&safe=$safe&move=$move><INPUT TYPE=image src=images/" . $port . ".png title=$alt></A></TD>";
                echo $tile;
              $res5->Movenext();
              $row = $res5->fields;
              }
            echo "</TR>";
	////////////////////////////////Seven//////////////////////////////////////
        echo"<TR>";
        while(!$res6->EOF){
                $row = $res6->fields;
                $port=$row[terrain] . $row[res_type];
		$alt=$row[hex_id];
		$tile = "<TD><A HREF=map-admin.php?hex_id=$alt&terrain=$_SESSION[terrain]&res_type=$_SESSION[res_type]&safe=$safe&move=$move><INPUT TYPE=image src=images/" . $port . ".png title=$alt></A></TD>";
                echo $tile;
              $res6->Movenext();
              $row = $res6->fields;
              }
            echo "</TR>";
	////////////////////////////////Eight//////////////////////////////////////
        echo"<TR>";
        while(!$res7->EOF){
                $row = $res7->fields;
                $port=$row[terrain] . $row[res_type];
		$alt=$row[hex_id];
		$tile = "<TD><A HREF=map-admin.php?hex_id=$alt&terrain=$_SESSION[terrain]&res_type=$_SESSION[res_type]&safe=$safe&move=$move><INPUT TYPE=image src=images/" . $port . ".png title=$alt></A></TD>";
                echo $tile;
              $res7->Movenext();
              $row = $res7->fields;
              }
            echo "</TR>";
	/////////////////////////////////Nine/////////////////////////////////////
        echo"<TR>";
        while(!$res8->EOF){
                $row = $res8->fields;
                $port=$row[terrain] . $row[res_type];
		$alt=$row[hex_id];
		$tile = "<TD><A HREF=map-admin.php?hex_id=$alt&terrain=$_SESSION[terrain]&res_type=$_SESSION[res_type]&safe=$safe&move=$move><INPUT TYPE=image src=images/" . $port . ".png title=$alt></A></TD>";
                echo $tile;
              $res8->Movenext();
              $row = $res8->fields;
              }
            echo "</TR>";
	////////////////////////////////Ten/////////////////////////////////////////
        echo"<TR>";
        while(!$res9->EOF){
		$row = $res9->fields;
                $port=$row[terrain] . $row[res_type];
		$alt=$row[hex_id];
		$tile = "<TD><A HREF=map-admin.php?hex_id=$alt&terrain=$_SESSION[terrain]&res_type=$_SESSION[res_type]&safe=$safe&move=$move><INPUT TYPE=image src=images/" . $port . ".png title=$alt></A></TD>";
                echo $tile;
              $res9->Movenext();
              $row = $res9->fields;
              }
            echo "</TR>";
	////////////////////////////////Eleven////////////////////////////////////////
        echo"<TR>";
        while(!$res10->EOF){
                $row = $res10->fields;
                $port=$row[terrain] . $row[res_type];
		$alt=$row[hex_id];
		$tile = "<TD><A HREF=map-admin.php?hex_id=$alt&terrain=$_SESSION[terrain]&res_type=$_SESSION[res_type]&safe=$safe&move=$move><INPUT TYPE=image src=images/" . $port . ".png VALUE=SUBMIT title=$alt></A></TD>";
                echo $tile;
              $res10->Movenext();
              $row = $res10->fields;
              }
            echo "</TR>";
	//////////////////////////////////////See how she shows///////////////////////////
        echo"<TR>";
        while(!$res11->EOF){
                $row = $res11->fields;
                $port=$row[terrain] . $row[res_type];
		$alt=$row[hex_id];
		$tile = "<TD><A HREF=map-admin.php?hex_id=$alt&terrain=$_SESSION[terrain]&res_type=$_SESSION[res_type]&safe=$safe&move=$move><INPUT TYPE=image src=images/" . $port . ".png title=$alt></A></TD>";
                echo $tile;
              $res11->Movenext();
              $row = $res11->fields;
              }
            echo "</TR>";
	///////////////////////////////////////////////////////////////////////////////////
        echo"<TR>";
        while(!$res12->EOF){
                $row = $res12->fields;
                $port=$row[terrain] . $row[res_type];
		$alt=$row[hex_id];
		$tile = "<TD><A HREF=map-admin.php?hex_id=$alt&terrain=$_SESSION[terrain]&res_type=$_SESSION[res_type]&safe=$safe&move=$move><INPUT TYPE=image src=images/" . $port . ".png title=$alt></A></TD>";
                echo $tile;
              $res12->Movenext();
              $row = $res12->fields;
              }
            echo "</TR>";
	//////////////////////////////////////////////////////////////////////////////////
        echo"<TR>";
        while(!$res13->EOF){
                $row = $res13->fields;
                $port=$row[terrain] . $row[res_type];
		$alt=$row[hex_id];
		$tile = "<TD><A HREF=map-admin.php?hex_id=$alt&terrain=$_SESSION[terrain]&res_type=$_SESSION[res_type]&safe=$safe&move=$move><INPUT TYPE=image src=images/" . $port . ".png title=$alt></A></TD>";
                echo $tile;
              $res13->Movenext();
              $row = $res13->fields;
              }
            echo "</TR>";
	////////////////////////////////////////////////////////////////////////////////
        echo"<TR>";
        while(!$res14->EOF){
                $row = $res14->fields;
                $port=$row[terrain] . $row[res_type];
		$alt=$row[hex_id];
		$tile = "<TD><A HREF=map-admin.php?hex_id=$alt&terrain=$_SESSION[terrain]&res_type=$_SESSION[res_type]&safe=$safe&move=$move><INPUT TYPE=image src=images/" . $port . ".png title=$alt></A></TD>";
                echo $tile;
              $res14->Movenext();
              $row = $res14->fields;
              }
            echo "</TR>";
	////////////////////////////////////////////////////////////////////////////////////
        echo"<TR>";
        while(!$res15->EOF){
                $row = $res15->fields;
                $port=$row[terrain] . $row[res_type];
		$alt=$row[hex_id];
		$tile = "<TD><A HREF=map-admin.php?hex_id=$alt&terrain=$_SESSION[terrain]&res_type=$_SESSION[res_type]&safe=$safe&move=$move><INPUT TYPE=image src=images/" . $port . ".png title=$alt></A></TD>";
                echo $tile;
              $res15->Movenext();
              $row = $res15->fields;
              }
            echo "</TR>";
	/////////////////////////////////////////////////////////////////////////////////
        echo"<TR>";
        while(!$res16->EOF){
                $row = $res16->fields;
                $port=$row[terrain] . $row[res_type];
		$alt=$row[hex_id];
		$tile = "<TD><A HREF=map-admin.php?hex_id=$alt&terrain=$_SESSION[terrain]&res_type=$_SESSION[res_type]&safe=$safe&move=$move><INPUT TYPE=image src=images/" . $port . ".png title=$alt></A></TD>";
                echo $tile;
              $res16->Movenext();
              $row = $res16->fields;
              }
            echo "</TR>";
	///////////////////////////////////////////////////////////////////////////////////
        echo"<TR>";
        while(!$res17->EOF){
                $row = $res17->fields;
                $port=$row[terrain] . $row[res_type];
		$alt=$row[hex_id];
		$tile = "<TD><A HREF=map-admin.php?hex_id=$alt&terrain=$_SESSION[terrain]&res_type=$_SESSION[res_type]&safe=$safe&move=$move><INPUT TYPE=image src=images/" . $port . ".png title=$alt></A></TD>";
                echo $tile;
              $res17->Movenext();
              $row = $res17->fields;
              }
            echo "</TR>";
	///////////////////////////////////////////////////////////////////////////////////
        echo"<TR>";
        while(!$res18->EOF){
                $row = $res18->fields;
                $port=$row[terrain] . $row[res_type];
		$alt=$row[hex_id];
		$tile = "<TD><A HREF=map-admin.php?hex_id=$alt&terrain=$_SESSION[terrain]&res_type=$_SESSION[res_type]&safe=$safe&move=$move><INPUT TYPE=image src=images/" . $port . ".png title=$alt></A></TD>";
                echo $tile;
              $res18->Movenext();
              $row = $res18->fields;
              }
            echo "</TR>";
	//////////////////////////////////////////////////////////////////////////////////
        echo"<TR>";
        while(!$res19->EOF){
                $row = $res19->fields;
                $port=$row[terrain] . $row[res_type];
		$alt=$row[hex_id];
		$tile = "<TD><A HREF=map-admin.php?hex_id=$alt&terrain=$_SESSION[terrain]&res_type=$_SESSION[res_type]&safe=$safe&move=$move><INPUT TYPE=image src=images/" . $port . ".png title=$alt></A></TD>";
                echo $tile;
              $res19->Movenext();
              $row = $res19->fields;
              }
            echo "</TR>";
	////////////////////////////////////////////////////////////////////////////////////
        echo"<TR>";
        while(!$res20->EOF){
                $row = $res20->fields;
                $port=$row[terrain] . $row[res_type];
		$alt=$row[hex_id];
		$tile = "<TD><A HREF=map-admin.php?hex_id=$alt&terrain=$_SESSION[terrain]&res_type=$_SESSION[res_type]&safe=$safe&move=$move><INPUT TYPE=image src=images/" . $port . ".png title=$alt></A></TD>";
                echo $tile;
              $res20->Movenext();
              $row = $res20->fields;
              }
            echo "</TR></TABLE></FORM>";






if(ISSET($_REQUEST[hex_id]) & ISSET($_REQUEST[terrain]) & ISSET($_REQUEST[move]) & ISSET($_REQUEST[safe])){
if(!ISSET($_REQUEST[res_type])){
$db->Execute("UPDATE $dbtables[hexes] SET terrain = '$_REQUEST[terrain]', move = '$_REQUEST[move]', safe = '$_REQUEST[safe]' WHERE hex_id = '$_REQUEST[hex_id]'");
}
else{
if( $_REQUEST[terrain] == 'pr' | $_REQUEST[terrain] == 'jh' | $_REQUEST[terrain] == 'gh' | $_REQUEST[terrain] == 'dh' | $_REQUEST[terrain] == 'ch' | $_REQUEST[terrain] == 'lcm' | $_REQUEST[terrain] == 'ljm' ){
if(!$_REQUEST[res_type] == '')
{
$db->Execute("UPDATE $dbtables[hexes] SET resource = 'Y', terrain = '$_REQUEST[terrain]', move = '$_REQUEST[move]', safe = '$_REQUEST[safe]', res_type = '$_REQUEST[res_type]' WHERE hex_id = '$_REQUEST[hex_id]'");
}
else
{
$db->Execute("UPDATE $dbtables[hexes] SET resource = 'N', terrain = '$_REQUEST[terrain]', move = '$_REQUEST[move]', safe = '$_REQUEST[safe]', res_type = '$_REQUEST[res_pe]' WHERE hex_id = '$_REQUEST[hex_id]'");
}
}
else{
$db->Execute("UPDATE $dbtables[hexes] SET terrain = '$_REQUEST[terrain]', move = '$_REQUEST[move]', safe = '$_REQUEST[safe]' WHERE hex_id ='$_REQUEST[hex_id]'");
}

}

}









	echo "</TABLE>";
	echo "</TD></TR>";
	echo "</TABLE>\n";

}
TEXT_GOTOMAIN();
	include("footer.php");
?> 
