<?
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: garrisons.php

session_start();
header("Cache-control: private");

include("config.php");

page_header("Tribe Garrisons");

connectdb();

$username = $_SESSION['username'];

$mode = $_REQUEST[action];

if( !ISSET($_REQUEST['recruit_garrison']) )
{
    echo "<FORM ACTION=garrisons.php METHOD=POST>"
		."<INPUT TYPE=SUBMIT NAME=recruit_garrison VALUE=\"Recruit a New Garrison Unit\"></FORM></CENTER>";
}

echo "<CENTER>";


	// DISPLAY RECRUITING PANEL


if( ISSET($_REQUEST['recruit_garrison']) )
	{
	$war = $db->Execute("SELECT * FROM $dbtables[tribes] "
                           ."WHERE tribeid = '$_SESSION[current_unit]'");
	$warinfo = $war->fields;
        $hs = $db->Execute("SELECT * FROM $dbtables[livestock] "
                          ."WHERE tribeid = '$warinfo[goods_tribe]' "
                          ."AND type = 'Horses'");
        $horseinfo = $hs->fields;
        $sad = $db->Execute("SELECT * FROM $dbtables[products] "
                           ."WHERE long_name = 'saddle' "
                           ."AND tribeid = '$warinfo[goods_tribe]'");
        $saddle = $sad->fields;
	echo "<FONT CLASS=page_subtitle>Recruiting</font><P>";
	echo "<TABLE BORDER=0><FORM ACTION=garrisons.php METHOD=POST>";
	$avail = $warinfo[curam] - $warinfo[slavepop];
        if($avail < 0){
        $avail = 0;
        }

	// Allocate men
	
	echo "<TR CLASS=color_row0><TD>How many warriors?</TD><TD><INPUT CLASS=edit_area TYPE=TEXT NAME=force SIZE=6 MAXSIZE=6></TD><TD>$avail Actives available</TD></TR>";

	// Allocate horses
	
	echo "<TR bgcolor=$color_line2><TD>Calvalry?</TD><TD><INPUT TYPE=checkbox NAME=horses VALUE=1>&nbsp;Yes</TD><TD>$horseinfo[amount] Horses available ($saddle[amount] saddles)</TD></TR>";

	// Allocate primary weapons
	
	$weap = $db->Execute("SELECT * FROM $dbtables[products] "
                            ."WHERE weapon = 'Y' "
                            ."AND long_name != 'catapult' "
                            ."AND long_name != 'ballista' "
                            ."AND tribeid = '$warinfo[goods_tribe]' "
                            ."AND amount > 0");

	if (!$weap->EOF)
	{
		echo "<TR CLASS=color_row0>"
			."<TD>Primary Weapon</TD>"
			."<TD COLSPAN=2>"
			."<SELECT NAME=weapon1>";

		while(!$weap->EOF)
		{
			$weapinfo = $weap->fields;
			echo "<OPTION VALUE=$weapinfo[long_name]>$weapinfo[proper] ($weapinfo[amount])</OPTION>";
			$weap->MoveNext();
		}

		echo "</SELECT></TD></TR>";
	}
	else
	{
		echo "<TR CLASS=color_row0>"
			."<TD>Primary Weapon</TD>"
			."<TD COLSPAN=2><INPUT TYPE=HIDDEN NAME=weapon1 VALUE=\"\">None available</TD>"
			."</TR>";
	}

	// Allocate secondary weapons
	
	$weap = $db->Execute("SELECT * FROM $dbtables[products] "
                            ."WHERE weapon = 'Y' "
                            ."AND long_name != 'catapult' "
                            ."AND long_name != 'ballista' "
                            ."AND tribeid = '$warinfo[goods_tribe]' "
                            ."AND amount > 0");
	if (!$weap->EOF)
	{
		echo "<TR CLASS=color_row0>"
			."<TD>Secondary Weapon</TD>"
			."<TD COLSPAN=2>"
			."<SELECT NAME=weapon2>"
			."<OPTION VALUE=\"\"></OPTION>";

		while(!$weap->EOF)
		{
			$weapinfo = $weap->fields;
			echo "<OPTION VALUE=$weapinfo[long_name]>$weapinfo[proper] ($weapinfo[amount])</OPTION>";
			$weap->MoveNext();
		}

		echo "</SELECT>"
			."</TD>"
			."</TR>";
	}
	else
	{
		echo "<TR CLASS=color_row0>"
			."<TD>Primary Weapon</TD>"
			."<TD COLSPAN=2>"
			."<INPUT TYPE=HIDDEN NAME=weapon2 VALUE=\"\">None available"
			."</TD>"
			."</TR>";
	}

	// Allocate head armour

	$arm = $db->Execute("SELECT * FROM $dbtables[armor] WHERE type = 'head'");
	$option = 0;
	while(!$arm->EOF){
	$arminfo = $arm->fields;
	$head = $db->Execute("SELECT * FROM $dbtables[products] "
                            ."WHERE long_name = '$arminfo[long_name]' "
                            ."AND tribeid = '$warinfo[goods_tribe]' "
                            ."AND amount > 0");
	while(!$head->EOF){
	$headinfo = $head->fields;
	if($option < 1){
	echo "<TR CLASS=color_row0><TD>Head Armor</TD><TD COLSPAN=2><SELECT NAME=head>";

	echo "<OPTION VALUE=\"\"></OPTION>";
	$option++;
	}
	echo "<OPTION VALUE=$headinfo[long_name]>$headinfo[proper] ($headinfo[amount])</OPTION>";
	$head->MoveNext();
	}
	$arm->MoveNext();
	}
	echo "</SELECT></TD></TR>";

	// Allocate overtorso armour

	$armor = $db->Execute("SELECT * FROM $dbtables[armor] WHERE type = 'overtorso'");
	$option = 0;
	while(!$armor->EOF){
	$arminfo = $armor->fields;
	$otorso = $db->Execute("SELECT * FROM $dbtables[products] "
                              ."WHERE long_name = '$arminfo[long_name]' "
                              ."AND tribeid = '$warinfo[goods_tribe]' "
                              ."AND amount > 0");
	while(!$otorso->EOF){
	$otorsoinfo = $otorso->fields;
	if($option <1){
	echo "<TR bgcolor=$color_line2><TD>Over Torso Armor</TD><TD COLSPAN=2><SELECT NAME=otorso>";

	echo "<OPTION VALUE=\"\"></OPTION>";
	$option++;
	}
	echo "<OPTION VALUE=$otorsoinfo[long_name]>$otorsoinfo[proper] ($otorsoinfo[amount])</OPTION>";
	$otorso->MoveNext();
	}
	$armor->MoveNext();
	}
	echo "</SELECT></TD></TR>";
	$armor = array();

	// Allocate torsoarmour

	$armor = $db->Execute("SELECT * FROM $dbtables[armor] WHERE type = 'torso'");
	$option = 0;
	while(!$armor->EOF){
	$arminfo = $armor->fields;
	$torso = $db->Execute("SELECT * FROM $dbtables[products] "
                             ."WHERE long_name = '$arminfo[long_name]' "
                             ."AND tribeid = '$warinfo[goods_tribe]' "
                             ."AND amount > 0");
	while(!$torso->EOF){
	$torsoinfo = $torso->fields;
	if($option <1){
	echo "<TR CLASS=color_row0><TD>Body Armor</TD><TD COLSPAN=2><SELECT NAME=torso>";

	echo "<OPTION VALUE=\"\"></OPTION>";
	$option++;
	}
	echo "<OPTION VALUE=$torsoinfo[long_name]>$torsoinfo[proper] ($torsoinfo[amount])</OPTION>";
	$torso->MoveNext();
	}
	$armor->MoveNext();
	}
	echo "</SELECT></TD></TR>";

	// Allocate leg armour

	$arm = $db->Execute("SELECT * FROM $dbtables[armor] WHERE type = 'leg'");
	$option = 0;
	while(!$arm->EOF){
	$arminfo = $arm->fields;
	$leg = $db->Execute("SELECT * FROM $dbtables[products] "
                           ."WHERE long_name = '$arminfo[long_name]' "
                           ."AND tribeid = '$warinfo[goods_tribe]' "
                           ."AND amount > 0");
	while(!$leg->EOF){
	$leginfo = $leg->fields;
	if($option <1){
	echo "<TR bgcolor=$color_line2><TD>Leg Armor</TD><TD COLSPAN=2><SELECT NAME=leg>";

	echo "<OPTION VALUE=\"\"></OPTION>";
	$option++;
	}
	echo "<OPTION VALUE=$leginfo[long_name]>$leginfo[proper] ($leginfo[amount])</OPTION>";
	$leg->MoveNext();
	}
	$arm->MoveNext();
	}
	echo "</SELECT></TD></TR>";

	// Allocate shield

	$arm = $db->Execute("SELECT * FROM $dbtables[armor] WHERE type = 'shield'");
	$option = 0;
	while(!$arm->EOF){
	$arminfo = $arm->fields;
	$shield = $db->Execute("SELECT * FROM $dbtables[products] "
                              ."WHERE long_name = '$arminfo[long_name]' "
                              ."AND tribeid = '$warinfo[goods_tribe]' "
                              ."AND amount > 0");
	while(!$shield->EOF){
	$shieldinfo = $shield->fields;
	if($option <1){
	echo "<TR CLASS=color_row0><TD>Shield</TD><TD COLSPAN=2><SELECT NAME=shield>";

	echo "<OPTION VALUE=\"\"></OPTION>";
	$option++;
	}
	echo "<OPTION VALUE=$shieldinfo[long_name]>$shieldinfo[proper] ($shieldinfo[amount])</OPTION>";
	$shield->MoveNext();
	}
	$arm->MoveNext();
	}
	echo "</SELECT></TD></TR>";

	// Allocate horse barding

	$arm = $db->Execute("SELECT * FROM $dbtables[armor] WHERE type = 'horse'");
	$option = 0;
	while(!$arm->EOF){
	$arminfo = $arm->fields;
	$barding = $db->Execute("SELECT * FROM $dbtables[products] "
                               ."WHERE long_name = '$arminfo[long_name]' "
                               ."AND tribeid = '$warinfo[goods_tribe]' "
                               ."AND amount > 0");
	while(!$barding->EOF){
	$bardinfo = $barding->fields;
	if($option <1){
	echo "<TR bgcolor=$color_line2><TD>Horse Armor</TD><TD COLSPAN=2><SELECT NAME=barding>";

	echo "<OPTION VALUE=\"\"></OPTION>";
	$option++;
        }
	echo "<OPTION VALUE=$bardinfo[long_name]>$bardinfo[proper] ($bardinfo[amount])</OPTION>";
	$barding->MoveNext();
	}
	$arm->MoveNext();
	}
	echo "</SELECT></TD></TR>";
	echo "<TR bgcolor=$linecolor><TD COLSPAN=3><CENTER><INPUT TYPE=SUBMIT VALUE=RECRUIT></FORM></CENTER></TD></TR></TABLE>";
}


	// ALLOCATE ASSIGNED RESOURCES TO A NEW UNIT


if(ISSET($_REQUEST[force])){
	$trooptype = "I";
	$tribe = $db->Execute("SELECT * FROM $dbtables[tribes] WHERE tribeid = '$_SESSION[current_unit]'");
	$tribeinfo = $tribe->fields;
	$available = $tribeinfo[curam] - $tribeinfo[slavepop];
	if($_REQUEST[force] > $available){
	$_REQUEST[force] = $available;
	}
	if($_REQUEST[force] < 1){
	$_REQUEST[force] = 0;
	}
	if($_REQUEST[horses] == '1'){
	$horse = $db->Execute("SELECT * FROM $dbtables[livestock] "
						 ."WHERE type = 'Horses' "
						 ."AND tribeid = '$tribeinfo[goods_tribe]'");
	$horseinfo = $horse->fields;
	$saddle = $db->Execute("SELECT * FROM $dbtables[products] "
						  ."WHERE long_name = 'saddle' "
						  ."AND tribeid = '$tribeinfo[goods_tribe]'");
	$sadinfo = $saddle->fields;


		if( $horseinfo[amount] > $sadinfo[amount] )
		{
			$horseinfo[amount] = $sadinfo[amount];
		}
		if($horseinfo[amount] < $_REQUEST[force])
		{
			$_REQUEST[force] = $horseinfo[amount];
		}
		$horses = $_REQUEST[force];
        if($horses > 1)
		{
			$trooptype = 'C';
        }
	}
	if($_REQUEST[weapon1] == '' && !$_REQUEST[weapon2] == '')
	{
	$_REQUEST[weapon1] = $_REQUEST[weapon2];
	$_REQUEST[weapon2] = '';
	}
	if ($_REQUEST[weapon1]==$_REQUEST[weapon2])
	{
		$_REQUEST[weapon2] = "";
	}

        if($_REQUEST[weapon1] == 'arbalest'){
	$trooptype = 'Q';
	}
	if($_REQUEST[weapon1] == 'crossbow'){
	$trooptype = 'Q';
	}
	if($_REQUEST[weapon1] == 'repeatingarbalest'){
	$trooptype = 'Q';
	}
 	if($_REQUEST[weapon1] == 'bow'){
	$trooptype = 'A';
	}
	if($_REQUEST[weapon1] == 'longbow'){
	$trooptype = 'A';
	}
	if($_REQUEST[weapon1] == 'sling'){
	$trooptype = 'B';
	}
	$weap = $db->Execute("SELECT * FROM $dbtables[products] "
                            ."WHERE long_name = '$_REQUEST[weapon1]' "
                            ."AND tribeid = '$tribeinfo[goods_tribe]'");
	$weapinfo = $weap->fields;
	if($_REQUEST[force] > $weapinfo[amount]){
	$_REQUEST[force] = $weapinfo[amount];
	}
	if(!$_REQUEST[weapon2] == ''){
	$weap2 = $db->Execute("SELECT * FROM $dbtables[products] "
                             ."WHERE long_name = '$_REQUEST[weapon2]' "
                             ."AND tribeid = '$tribeinfo[goods_tribe]'");
	$weapinfo2 = $weap2->fields;
		if($_REQUEST[force] > $weapinfo2[amount]){
		$_REQUEST[force] = $weapinfo2[amount];
		}
	}
	if(!$_REQUEST[head] == ''){
	$head = $db->Execute("SELECT * FROM $dbtables[products] "
                            ."WHERE long_name = '$_REQUEST[head]' "
                            ."AND tribeid = '$tribeinfo[goods_tribe]'");
	$headinfo = $head->fields;
		if($_REQUEST[force] > $headinfo[amount]){
		$_REQUEST[force] = $headinfo[amount];
		}
	}
	if(!$_REQUEST[torso] == ''){
	$torso = $db->Execute("SELECT * FROM $dbtables[products] "
                             ."WHERE long_name = '$_REQUEST[torso]' "
                             ."AND tribeid = '$tribeinfo[goods_tribe]'");
	$torsoinfo = $torso->fields;
		if($_REQUEST[force] > $torsoinfo[amount]){
		$_REQUEST[force] = $torsoinfo[amount];
		}
	}
	if(!$_REQUEST[otorso] == ''){
	$arm = $db->Execute("SELECT * FROM $dbtables[products] "
                           ."WHERE long_name = '$_REQUEST[otorso]' "
                           ."AND tribeid = '$tribeinfo[goods_tribe]'");
	$otorsoinfo = $arm->fields;
		if($_REQUEST[force] > $otorsoinfo[amount]){
		$_REQUEST[force] = $otorsoinfo[amount];
		}
	}
	if(!$_REQUEST[leg] == ''){
	$leg = $db->Execute("SELECT * FROM $dbtables[products] "
                           ."WHERE long_name = '$_REQUEST[leg]' "
                           ."AND tribeid = '$tribeinfo[goods_tribe]'");
	$leginfo = $leg->fields;
		if($_REQUEST[force] > $leginfo[amount]){
		$_REQUEST[force] = $leginfo[amount];
		}
	}
	if(!$_REQUEST[shield] == ''){
	$shield = $db->Execute("SELECT * FROM $dbtables[products] "
                              ."WHERE long_name = '$_REQUEST[shield]' "
                              ."AND tribeid = '$tribeinfo[goods_tribe]'");
	$shieldinfo = $shield->fields;
		if($_REQUEST[force] > $shieldinfo[amount]){
		$_REQUEST[force] = $shieldinfo[amount];
		}
	}
	if(!$_REQUEST[barding] == ''){
	$bard = $db->Execute("SELECT * FROM $dbtables[products] "
                            ."WHERE long_name = '$_REQUEST[barding]' "
                            ."AND tribeid = '$tribeinfo[goods_tribe]'");
	$bardinfo = $bard->fields;
		if($_REQUEST[force] > $bardinfo[amount]){
		$_REQUEST[force] = $bardinfo[amount];
		}
	}
	$hex = $db->Execute("SELECT * FROM $dbtables[hexes] WHERE hex_id = '$tribeinfo[hex_id]'");
	$hexinfo = $hex->fields;

	if($horses > $_REQUEST[force]){
	$horses = $_REQUEST[force];
	}

	if (!ISSET($_REQUEST['force']) || $_REQUEST['force']=="" || $_REQUEST['force']==0)
	{
		$_REQUEST['force'] = 0;
	}

	if ($_REQUEST['force'] > 0)
	{
		$db->Execute("INSERT INTO $dbtables[garrisons] "
						."VALUES("
						."'',"
						."'$tribeinfo[hex_id]',"
						."'$tribeinfo[clanid]',"
						."'$tribeinfo[tribeid]',"
						."'$_REQUEST[force]',"
						."'1.0',"
						."'$hexinfo[terrain]',"
						."'1.0',"
						."'$horses',"
						."'$weapinfo[proper]',"
						."'$weapinfo2[proper]',"
						."'$headinfo[proper]',"
						."'$torsoinfo[proper]',"
						."'$otorsoinfo[proper]',"
						."'$leginfo[proper]',"
						."'$shieldinfo[proper]',"
						."'$bardinfo[proper]',"
						."'$trooptype')");
	}

	echo "<CENTER>$_REQUEST[force] warriors added.</CENTER>";
	if ($_REQUEST['force']==0)
	{
		echo "<CENTER>You probably forgot to allocate some of the equipment that they need to be able to fight.</CENTER>";
	}
	$db->Execute("UPDATE $dbtables[tribes] "
                    ."SET activepop = activepop - $_REQUEST[force], "
                    ."curam = curam - $_REQUEST[force], "
                    ."warpop = warpop + $_REQUEST[force] "
                    ."WHERE tribeid = '$_SESSION[current_unit]'");
	if($_REQUEST[horses] == '1'){
	$db->Execute("UPDATE $dbtables[livestock] "
                    ."SET amount = amount - $horses "
                    ."WHERE tribeid = '$tribeinfo[goods_tribe]' "
                    ."AND type = 'Horses'");
        $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount - $horses "
                    ."WHERE tribeid = '$tribeinfo[goods_tribe]' "
                    ."AND long_name = 'saddle'");
	}
	if(!$_REQUEST[weapon1] == ''){
	$db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount - $_REQUEST[force] "
                    ."WHERE tribeid = '$tribeinfo[goods_tribe]' "
                    ."AND long_name = '$_REQUEST[weapon1]'");
	}
	if(!$_REQUEST[weapon2] == ''){
	$db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount - $_REQUEST[force] "
                    ."WHERE tribeid = '$tribeinfo[goods_tribe]' "
                    ."AND long_name = '$_REQUEST[weapon2]'");
	}
	if(!$_REQUEST[head] == ''){
	$db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount - $_REQUEST[force] "
                    ."WHERE tribeid = '$tribeinfo[goods_tribe]' "
                    ."AND long_name = '$_REQUEST[head]'");
	}
	if(!$_REQUEST[torso] == ''){
	$db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount - $_REQUEST[force] "
                    ."WHERE tribeid = '$tribeinfo[goods_tribe]' "
                    ."AND long_name = '$_REQUEST[torso]'");
	}
	if(!$_REQUEST[otorso] == ''){
	$db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount - $_REQUEST[force] "
                    ."WHERE tribeid = '$tribeinfo[goods_tribe]' "
                    ."AND long_name = '$_REQUEST[otorso]'");
	}
	if(!$_REQUEST[leg] == ''){
	$db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount - $_REQUEST[force] "
                    ."WHERE tribeid = '$tribeinfo[goods_tribe]' "
                    ."AND long_name = '$_REQUEST[leg]'");
	}
	if(!$_REQUEST[shield] == ''){
	$db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount - $_REQUEST[force] "
                    ."WHERE tribeid = '$tribeinfo[goods_tribe]' "
                    ."AND long_name = '$_REQUEST[shield]'");
	}
	if(!$_REQUEST[barding] == ''){
	$db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount - $_REQUEST[force] "
                    ."WHERE tribeid = '$tribeinfo[goods_tribe]' "
                    ."AND long_name = '$_REQUEST[barding]'");
	}
        include("weight.php");
}


	// DISBAND GARRISONS


if(ISSET($_REQUEST[disband]))
{
	echo "<CENTER>Unit $_REQUEST[disband] disbanded.</CENTER>";
	$dis = $db->Execute("SELECT * FROM $dbtables[garrisons] WHERE garid = $_REQUEST[disband]");
	$disband = $dis->fields;

	// WARNING: The following line is a quick fix for the fact that `force` is a reserved word
	//          in later versions of MySQL and as a result the field name is sometimes returned
	//          as FORCE instead of force.
	if (ISSET($disband['FORCE']))
	{
		$disband['force'] = $disband['FORCE'];
	}

	$res = $db->Execute("SELECT * FROM $dbtables[tribes] "
					   ."WHERE tribeid = '$disband[tribeid]'");
	$tribe = $res->fields;

	$db->Execute("UPDATE $dbtables[tribes] "
                    ."SET warpop = warpop - $disband[force], "
                    ."activepop = activepop + $disband[force], "
                    ."maxam = maxam + $disband[force] "
                    ."WHERE tribeid = $disband[tribeid] "
                    ."AND clanid = $disband[clanid] "
                    ."AND hex_id = $disband[hex_id]");
	$db->Execute("UPDATE $dbtables[livestock] "
                    ."SET amount = amount + $disband[horses] "
                    ."WHERE type = 'Horses' "
                    ."AND tribeid = '$tribe[goods_tribe]'");
    $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount + $disband[horses] "
                    ."WHERE long_name = 'saddle' "
                    ."AND tribeid = '$tribe[goods_tribe]'");
	$db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount + $disband[force] "
                    ."WHERE proper = '$disband[weapon1]' "
                    ."AND tribeid = '$tribe[goods_tribe]'");
	$db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount + $disband[force] "
                    ."WHERE proper = '$disband[weapon2]' "
                    ."AND tribeid = '$tribe[goods_tribe]'");
	$db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount + $disband[force] "
                    ."WHERE proper = '$disband[head_armor]' "
                    ."AND tribeid = '$tribe[goods_tribe]'");
	$db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount + $disband[force] "
                    ."WHERE proper = '$disband[torso_armor]' "
                    ."AND tribeid = '$tribe[goods_tribe]'");
	$db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount + $disband[force] "
                    ."WHERE proper = '$disband[otorso_armor]' "
                    ."AND tribeid = '$tribe[goods_tribe]'");
	$db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount + $disband[force] "
                    ."WHERE proper = '$disband[legs_armor]' "
                    ."AND tribeid = '$tribe[goods_tribe]'");
	$db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount + $disband[force] "
                    ."WHERE proper = '$disband[shield]' "
                    ."AND tribeid = '$tribe[goods_tribe]'");
	$db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount + $disband[force] "
                    ."WHERE proper = '$disband[horse_armor]' "
                    ."AND tribeid = '$tribe[goods_tribe]'");
	$db->Execute("DELETE FROM $dbtables[garrisons] "
                    ."WHERE garid = $_REQUEST[disband]");
	echo "<CENTER>$disband[force] $disband[weapon1]";
	if(!$disband[weapon2] == ''){
	echo ", $disband[weapon2]";
	}
	if(!$disband[head_armor] == ''){
	echo ", $disband[head_armor]";
	}
	if(!$disband[torso_armor] == ''){
	echo ", $disband[torso_armor]";
	}
	if(!$disband[otorso_armor] == ''){
	echo ", $disband[otorso_armor]";
	}
	if(!$disband[legs_armor] == ''){
	echo ", $disband[legs_armor]";
	}
	if(!$disband[shield] == ''){
	echo ", $disband[shield]";
	}
	if(!$disband[horses] > 0){
	echo ", horses &amp; saddles";
	}
	if(!$disband[horse_armor] == ''){
	echo ", $disband[horse_armor]";
	}
	echo " replaced into the good tribe's inventory.</CENTER>";
       include("weight.php");
}
        
	// DISPLAY LIST OF GARRISONS IN TRIBE


    echo "<TABLE BORDER=0 ALIGN=CENTER WIDTH=80%><TR bgcolor=$color_header><TD colspan=13>";
    echo "<CENTER>Currently assembled garrisons defending your tribe.</CENTER></TD></TR>";

	$tgar = $db->Execute("SELECT * FROM $dbtables[garrisons] "
						."WHERE tribeid = '$_SESSION[current_unit]'");

    echo "<TR bgcolor=$color_header>"
		."<TD>ID</TD>"
		."<TD>Force Size</TD>"
		."<TD>Horses</TD>"
		."<TD>Experience</TD>"
		."<TD>Primary Weapon</TD>"
		."<TD>Secondary Weapon</TD>"
		."<TD>Head Armor</TD>"
		."<TD>Over Body Armor</TD>"
		."<TD>Body Armor</TD>"
		."<TD>Leg Armor</TD>"
		."<TD>Shield</TD>"
		."<TD>Horse Armor</TD>"
		."<TD></TD>"
		."</TR>";

if($tgar->EOF){
    echo "<TR bgcolor=$color_line2><TD COLSPAN=12><CENTER>None</CENTER></TD></TR></TABLE>";
}


$r = 0;
while(!$tgar->EOF)
{
	$rc = $r % 2;
	$r++;

	$tgarinfo = $tgar->fields;

	// WARNING: The following line is a quick fix for the fact that `force` is a reserved word
	//          in later versions of MySQL and as a result the field name is sometimes returned
	//          as FORCE instead of force.
	if (ISSET($tgarinfo['FORCE']))
	{
		$tgarinfo['force'] = $tgarinfo['FORCE'];
	}

    echo "<TR CLASS=color_row$rc>"
		."<TD>$tgarinfo[garid]</TD><TD>$tgarinfo[force]</TD><TD>$tgarinfo[horses]</TD><TD>";
if($tgarinfo[experience] < 6){
    echo "Recruits";
        }
elseif($tgarinfo[experience] < 12){
    echo "Green";
        }
elseif($tgarinfo[experience] < 24){
    echo "Seasoned";
        }
elseif($tgarinfo[experience] < 48){
    echo "Veteran";
        }
elseif($tgarinfo[experience] <78){
    echo "Elite";
        }
elseif($tgarinfo[experience] < 100){
    echo "Crack";
        }
else{
    echo "Commando";
        }

    echo "</TD><TD>$tgarinfo[weapon1]</TD><TD>";
if($tgarinfo[weapon2] == ''){
    echo "None";
}
else{
    echo "$tgarinfo[weapon2]";
}
    echo "</TD><TD>";
if($tgarinfo[head_armor] == ''){
    echo "None";
}
else{
    echo "$tgarinfo[head_armor]";
}
    echo "</TD><TD>";
if($tgarinfo[otorso_armor] == ''){
    echo "None";
}
else{
    echo "$tgarinfo[otorso_armor]";
}
    echo "</TD><TD>";
if($tgarinfo[torso_armor] == ''){
    echo "None";
}
else{
    echo "$tgarinfo[torso_armor]";
}
    echo "</TD><TD>";
if($tgarinfo[legs_armor] == ''){
    echo "None";
}
else{
    echo "$tgarinfo[legs_armor]";
}
    echo "</TD><TD>";
if($tgarinfo[shield] == ''){
    echo "None";
}
else{
    echo "$tgarinfo[shield]";
}
    echo "</TD><TD>";
if($tgarinfo[horse_armor] == ''){
    echo "None";
}
else{
    echo "$tgarinfo[horse_armor]";
}
    echo "</TD>";

	echo "<TD>"
		."<FORM METHOD=POST ACTION=garrisons.php>"
		."<INPUT TYPE=HIDDEN NAME=disband VALUE=\"$tgarinfo[garid]\">"
		."<INPUT TYPE=SUBMIT VALUE=\"Disband\">"
		."</FORM>"
		."</TD>"
		."</TR>";
$tgar->MoveNext();
}
echo "</TABLE><P>";

	if (ISSET($tgarinfo['FORCE']))
	{
		echo "<P><B>ADMIN WARNING</B>"
			."<BR>Your game's database tables are incorrectly installed!"
			."<BR>In the table <I>garrisons</I>, please rename the field <I>FORCE</I> to <I>force</i>."
			."<BR>If you fail to do so, combat will not work in your game.";
	}


	// DISPLAY LIST OF GARRISONS IN OTHER TRIBES OF CLAN


    echo "<TABLE BODER=0 ALIGN=CENTER WIDTH=80%><TR bgcolor=$color_header><TD colspan=11>";
    echo "<CENTER>Other assembled garrisons defending your clan.</CENTER></TD></TR>";

	$cgar = $db->Execute("SELECT * FROM $dbtables[garrisons] "
						."WHERE tribeid <> '".$_SESSION[current_unit]."' AND "
						."clanid = '".$_SESSION[clanid]."' "
						."ORDER BY `force`");

    echo "<TR bgcolor=$color_header>"
		."<TD>Tribe</TD>"
		."<TD>Force Size</TD>"
		."<TD>Horses</TD>"
		."<TD>Experience</TD>"
		."<TD>Primary Weapon</TD>"
		."<TD>Secondary Weapon</TD>"
		."<TD>Head Armor</TD>"
		."<TD>Body Armor</TD>"
		."<TD>Leg Armor</TD>"
		."<TD>Shield</TD>"
		."<TD>Horse Armor</TD>"
		."</TR>";

$linecolor = $color_line2;

if($cgar->EOF)
{
    echo "<TR bgcolor=$linecolor><TD COLSPAN=12><CENTER>None</CENTER></TD></TR></TABLE>";
}

while(!$cgar->EOF)
{
	if($linecolor == $color_line1)
	{
		$linecolor = $color_line2;
	}
	else
	{
		$linecolor = $color_line1;
	}

	$cgarinfo = $cgar->fields;

	// WARNING: The following line is a quick fix for the fact that `force` is a reserved word
	//          in later versions of MySQL and as a result the field name is sometimes returned
	//          as FORCE instead of force.
	if (ISSET($cgarinfo['FORCE']))
	{
		$cgarinfo['force'] = $cgarinfo['FORCE'];
	}

	echo "<TR bgcolor = $linecolor>"
		."<TD>$cgarinfo[tribeid]</TD>"
		."<TD>$cgarinfo[force]</TD>"
		."<TD>$cgarinfo[horses]</TD><TD>";
	if($cgarinfo[experience] < 6)
	{
		echo "Recruits";
	}
	elseif($cgarinfo[experience] < 12)
	{
		echo "Green";
	}
	elseif($cgarinfo[experience] < 24)
	{
		echo "Seasoned";
	}
	elseif($cgarinfo[experience] < 48)
	{
		echo "Veteran";
	}
	elseif($cgarinfo[experience] <78)
	{
		echo "Elite";
	}
	elseif($cgarinfo[experience] < 100)
	{
		echo "Crack";
	}
	else
	{
		echo "Commando";
	}

	echo "</TD><TD>$cgarinfo[weapon1]</TD><TD>";
	if($cgarinfo[weapon2] == '')
	{
		echo "None";
	}
	else
	{
		echo "$cgarinfo[weapon2]";
	}

	echo "</TD><TD>";
	if($cgarinfo[head_armor] == '')
	{
		echo "None";
	}
	else
	{
		echo "$cgarinfo[head_armor]";
	}
	echo "</TD><TD>";

	if($cgarinfo[torso_armor] == '')
	{
		echo "None";
	}
	else
	{
		echo "$cgarinfo[torso_armor]";
	}
	echo "</TD><TD>";

	if($cgarinfo[legs_armor] == '')
	{
		echo "None";
	}
	else
	{
		echo "$cgarinfo[legs_armor]";
	}
	echo "</TD><TD>";

	if($cgarinfo[shield] == '')
	{
		echo "None";
	}
	else
	{
		echo "$cgarinfo[shield]";
	}
	echo "</TD><TD>";

	if($cgarinfo[horse_armor] == '')
	{
		echo "None";
	}
	else
	{
		echo "$cgarinfo[horse_armor]";
	}
	echo "</TD></TR>";

	$cgar->MoveNext();
}
echo "</TABLE><P>";


page_footer();
?> 
