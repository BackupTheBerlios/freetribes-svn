<?php
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

$mode = $_POST['action'];

if( !ISSET($_POST['recruit_garrison']) )
{
    echo "<FORM ACTION=garrisons.php METHOD=POST>"
        ."<INPUT TYPE=SUBMIT NAME=recruit_garrison VALUE=\"Recruit a New Garrison Unit\"></FORM></CENTER>";
}

echo "<CENTER>";


    // DISPLAY RECRUITING PANEL


if( ISSET($_POST['recruit_garrison']) )
{
    $war = $db->Execute("SELECT * FROM $dbtables[tribes] "
                           ."WHERE tribeid = '$_SESSION[current_unit]'");
     db_op_result($war,__LINE__,__FILE__);
    $warinfo = $war->fields;
        $hs = $db->Execute("SELECT * FROM $dbtables[livestock] "
                          ."WHERE tribeid = '$warinfo[goods_tribe]' "
                          ."AND type = 'Horses'");
        db_op_result($hs,__LINE__,__FILE__);
        $horseinfo = $hs->fields;
        $sad = $db->Execute("SELECT * FROM $dbtables[products] "
                           ."WHERE long_name = 'saddle' "
                           ."AND tribeid = '$warinfo[goods_tribe]'");
        db_op_result($sad,__LINE__,__FILE__);
        $saddle = $sad->fields;
    echo "<FONT CLASS=page_subtitle>Recruiting</font><P>";
    echo "<TABLE BORDER=0><FORM ACTION=garrisons.php METHOD=POST>";
    $avail = $warinfo['curam'] - $warinfo['slavepop'];
        if($avail < 0)
        {
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
    db_op_result($weap,__LINE__,__FILE__);
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
     db_op_result($weap,__LINE__,__FILE__);
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
    db_op_result($arm,__LINE__,__FILE__);
    $option = 0;
    while(!$arm->EOF)
    {
    $arminfo = $arm->fields;
    $head = $db->Execute("SELECT * FROM $dbtables[products] "
                            ."WHERE long_name = '$arminfo[long_name]' "
                            ."AND tribeid = '$warinfo[goods_tribe]' "
                            ."AND amount > 0");
    db_op_result($head,__LINE__,__FILE__);
    while(!$head->EOF)
    {
    $headinfo = $head->fields;
    if($option < 1)
    {
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
    db_op_result($armor,__LINE__,__FILE__);
    $option = 0;
    while(!$armor->EOF)
    {
    $arminfo = $armor->fields;
    $otorso = $db->Execute("SELECT * FROM $dbtables[products] "
                              ."WHERE long_name = '$arminfo[long_name]' "
                              ."AND tribeid = '$warinfo[goods_tribe]' "
                              ."AND amount > 0");
    db_op_result($otorso,__LINE__,__FILE__);
    while(!$otorso->EOF)
    {
    $otorsoinfo = $otorso->fields;
    if($option <1)
    {
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
    db_op_result($armor,__LINE__,__FILE__);
    $option = 0;
    while(!$armor->EOF)
    {
    $arminfo = $armor->fields;
    $torso = $db->Execute("SELECT * FROM $dbtables[products] "
                             ."WHERE long_name = '$arminfo[long_name]' "
                             ."AND tribeid = '$warinfo[goods_tribe]' "
                             ."AND amount > 0");
     db_op_result($torso,__LINE__,__FILE__);
    while(!$torso->EOF)
    {
    $torsoinfo = $torso->fields;
    if($option <1)
    {
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
    db_op_result($arm,__LINE__,__FILE__);
    $option = 0;
    while(!$arm->EOF)
    {
    $arminfo = $arm->fields;
    $leg = $db->Execute("SELECT * FROM $dbtables[products] "
                           ."WHERE long_name = '$arminfo[long_name]' "
                           ."AND tribeid = '$warinfo[goods_tribe]' "
                           ."AND amount > 0");
    db_op_result($leg,__LINE__,__FILE__);
    while(!$leg->EOF)
    {
    $leginfo = $leg->fields;
    if($option <1)
    {
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
    db_op_result($arm,__LINE__,__FILE__);
    $option = 0;
    while(!$arm->EOF)
    {
    $arminfo = $arm->fields;
    $shield = $db->Execute("SELECT * FROM $dbtables[products] "
                              ."WHERE long_name = '$arminfo[long_name]' "
                              ."AND tribeid = '$warinfo[goods_tribe]' "
                              ."AND amount > 0");
    db_op_result($shield,__LINE__,__FILE__);
    while(!$shield->EOF)
    {
    $shieldinfo = $shield->fields;
    if($option <1)
    {
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
     db_op_result($arm,__LINE__,__FILE__);
    $option = 0;
    while(!$arm->EOF)
    {
    $arminfo = $arm->fields;
    $barding = $db->Execute("SELECT * FROM $dbtables[products] "
                               ."WHERE long_name = '$arminfo[long_name]' "
                               ."AND tribeid = '$warinfo[goods_tribe]' "
                               ."AND amount > 0");
    db_op_result($barding,__LINE__,__FILE__);
    while(!$barding->EOF)
    {
    $bardinfo = $barding->fields;
    if($option <1)
    {
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


if(ISSET($_POST['force']))
{
    $trooptype = "I";
    $tribe = $db->Execute("SELECT * FROM $dbtables[tribes] WHERE tribeid = '$_SESSION[current_unit]'");
    db_op_result($tribe,__LINE__,__FILE__);
    $tribeinfo = $tribe->fields;
    $available = $tribeinfo['curam'] - $tribeinfo['slavepop'];
    if($_POST['force'] > $available)
    {
    $_POST['force'] = $available;
    }
    if($_POST['force'] < 1)
    {
    $_POST['force'] = 0;
    }
    if($_POST['horses'] == '1')
    {
    $horse = $db->Execute("SELECT * FROM $dbtables[livestock] "
                         ."WHERE type = 'Horses' "
                         ."AND tribeid = '$tribeinfo[goods_tribe]'");
    db_op_result($horse,__LINE__,__FILE__);
    $horseinfo = $horse->fields;
    $saddle = $db->Execute("SELECT * FROM $dbtables[products] "
                          ."WHERE long_name = 'saddle' "
                          ."AND tribeid = '$tribeinfo[goods_tribe]'");
    db_op_result($saddle,__LINE__,__FILE__);
    $sadinfo = $saddle->fields;


        if( $horseinfo['amount'] > $sadinfo['amount'] )
        {
            $horseinfo['amount'] = $sadinfo['amount'];
        }
        if($horseinfo['amount'] < $_POST['force'])
        {
            $_POST['force'] = $horseinfo['amount'];
        }
        $horses = $_POST['force'];
        if($horses > 1)
        {
            $trooptype = 'C';
        }
    }
    if($_POST['weapon1'] == '' && !$_POST['weapon2'] == '')
    {
    $_POST['weapon1'] = $_POST['weapon2'];
    $_POST['weapon2'] = '';
    }
    if ($_POST['weapon1']==$_POST['weapon2'])
    {
        $_POST['weapon2'] = "";
    }

    if($_POST['weapon1'] == 'arbalest')
    {
    $trooptype = 'Q';
    }
    if($_POST['weapon1'] == 'crossbow')
    {
    $trooptype = 'Q';
    }
    if($_POST['weapon1'] == 'repeatingarbalest')
    {
    $trooptype = 'Q';
    }
     if($_POST['weapon1'] == 'bow')
     {
    $trooptype = 'A';
    }
    if($_POST['weapon1'] == 'longbow')
    {
    $trooptype = 'A';
    }
    if($_POST['weapon1'] == 'sling')
    {
    $trooptype = 'B';
    }
    $weap = $db->Execute("SELECT * FROM $dbtables[products] "
                            ."WHERE long_name = '$_POST[weapon1]' "
                            ."AND tribeid = '$tribeinfo[goods_tribe]'");
     db_op_result($weap,__LINE__,__FILE__);
    $weapinfo = $weap->fields;
    if($_POST['force'] > $weapinfo['amount'])
    {
    $_POST['force'] = $weapinfo['amount'];
    }
    if(!$_POST['weapon2'] == '')
    {
    $weap2 = $db->Execute("SELECT * FROM $dbtables[products] "
                             ."WHERE long_name = '$_POST[weapon2]' "
                             ."AND tribeid = '$tribeinfo[goods_tribe]'");
    db_op_result($weap2,__LINE__,__FILE__);
    $weapinfo2 = $weap2->fields;
        if($_POST['force'] > $weapinfo2['amount'])
        {
        $_POST['force'] = $weapinfo2['amount'];
        }
    }
    if(!$_POST['head'] == '')
    {
    $head = $db->Execute("SELECT * FROM $dbtables[products] "
                            ."WHERE long_name = '$_POST[head]' "
                            ."AND tribeid = '$tribeinfo[goods_tribe]'");
    db_op_result($head,__LINE__,__FILE__);
    $headinfo = $head->fields;
        if($_POST['force'] > $headinfo['amount'])
        {
        $_POST['force'] = $headinfo['amount'];
        }
    }
    if(!$_POST['torso'] == '')
    {
    $torso = $db->Execute("SELECT * FROM $dbtables[products] "
                             ."WHERE long_name = '$_POST[torso]' "
                             ."AND tribeid = '$tribeinfo[goods_tribe]'");
    db_op_result($torso,__LINE__,__FILE__);
    $torsoinfo = $torso->fields;
        if($_POST['force'] > $torsoinfo['amount'])
        {
        $_POST['force'] = $torsoinfo['amount'];
        }
    }
    if(!$_POST['otorso'] == '')
    {
    $arm = $db->Execute("SELECT * FROM $dbtables[products] "
                           ."WHERE long_name = '$_POST[otorso]' "
                           ."AND tribeid = '$tribeinfo[goods_tribe]'");
    db_op_result($arm,__LINE__,__FILE__);
    $otorsoinfo = $arm->fields;
        if($_POST['force'] > $otorsoinfo['amount'])
        {
        $_POST['force'] = $otorsoinfo['amount'];
        }
    }
    if(!$_POST['leg'] == '')
    {
    $leg = $db->Execute("SELECT * FROM $dbtables[products] "
                           ."WHERE long_name = '$_POST[leg]' "
                           ."AND tribeid = '$tribeinfo[goods_tribe]'");
    db_op_result($leg,__LINE__,__FILE__);
    $leginfo = $leg->fields;
        if($_POST['force'] > $leginfo['amount'])
        {
        $_POST['force'] = $leginfo['amount'];
        }
    }
    if(!$_POST['shield'] == '')
    {
    $shield = $db->Execute("SELECT * FROM $dbtables[products] "
                              ."WHERE long_name = '$_POST[shield]' "
                              ."AND tribeid = '$tribeinfo[goods_tribe]'");
    db_op_result($shield,__LINE__,__FILE__);
    $shieldinfo = $shield->fields;
        if($_POST['force'] > $shieldinfo['amount'])
        {
        $_POST['force'] = $shieldinfo['amount'];
        }
    }
    if(!$_POST['barding'] == '')
    {
    $bard = $db->Execute("SELECT * FROM $dbtables[products] "
                            ."WHERE long_name = '$_POST[barding]' "
                            ."AND tribeid = '$tribeinfo[goods_tribe]'");
    db_op_result($bard,__LINE__,__FILE__);
    $bardinfo = $bard->fields;
        if($_POST['force'] > $bardinfo['amount'])
        {
        $_POST['force'] = $bardinfo['amount'];
        }
    }
    $hex = $db->Execute("SELECT * FROM $dbtables[hexes] WHERE hex_id = '$tribeinfo[hex_id]'");
    db_op_result($hex,__LINE__,__FILE__);
    $hexinfo = $hex->fields;

    if($horses > $_POST['force'])
    {
    $horses = $_POST['force'];
    }

    if (!ISSET($_POST['force']) || $_POST['force']=="" || $_POST['force']==0)
    {
        $_POST['force'] = 0;
    }

    if ($_POST['force'] > 0)
    {
        $insert = $db->Execute("INSERT INTO $dbtables[garrisons] "
                        ."VALUES("
                        ."'',"
                        ."'$tribeinfo[hex_id]',"
                        ."'$tribeinfo[clanid]',"
                        ."'$tribeinfo[tribeid]',"
                        ."'$_POST[force]',"
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
       db_op_result($insert,__LINE__,__FILE__);
    }

    echo "<CENTER>$_POST[force] warriors added.</CENTER>";
    if ($_POST['force']==0)
    {
        echo "<CENTER>You probably forgot to allocate some of the equipment that they need to be able to fight.</CENTER>";
    }
    $upd1 = $db->Execute("UPDATE $dbtables[tribes] "
                    ."SET activepop = activepop - $_POST[force], "
                    ."curam = curam - $_POST[force], "
                    ."warpop = warpop + $_POST[force] "
                    ."WHERE tribeid = '$_SESSION[current_unit]'");
     db_op_result($upd1,__LINE__,__FILE__);
    if($_POST['horses'] == '1')
    {
        $hoss = $db->Execute("UPDATE $dbtables[livestock] "
                    ."SET amount = amount - $horses "
                    ."WHERE tribeid = '$tribeinfo[goods_tribe]' "
                    ."AND type = 'Horses'");
        db_op_result($hoss,__LINE__,__FILE__);
        $prod = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount - $horses "
                    ."WHERE tribeid = '$tribeinfo[goods_tribe]' "
                    ."AND long_name = 'saddle'");
        db_op_result($prod,__LINE__,__FILE__);
    }
    if(!$_POST['weapon1'] == '')
    {
         $weap = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount - $_POST[force] "
                    ."WHERE tribeid = '$tribeinfo[goods_tribe]' "
                    ."AND long_name = '$_POST[weapon1]'");
         db_op_result($weap,__LINE__,__FILE__);
    }
    if(!$_POST['weapon2'] == '')
    {
        $weap = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount - $_POST[force] "
                    ."WHERE tribeid = '$tribeinfo[goods_tribe]' "
                    ."AND long_name = '$_POST[weapon2]'");
        db_op_result($weap,__LINE__,__FILE__);
    }
    if(!$_POST['head'] == '')
    {
        $weap = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount - $_POST[force] "
                    ."WHERE tribeid = '$tribeinfo[goods_tribe]' "
                    ."AND long_name = '$_POST[head]'");
         db_op_result($weap,__LINE__,__FILE__);
    }
    if(!$_POST['torso'] == '')
    {
        $weap = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount - $_POST[force] "
                    ."WHERE tribeid = '$tribeinfo[goods_tribe]' "
                    ."AND long_name = '$_POST[torso]'");
        db_op_result($weap,__LINE__,__FILE__);
    }
    if(!$_POST['otorso'] == '')
    {
        $weap = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount - $_POST[force] "
                    ."WHERE tribeid = '$tribeinfo[goods_tribe]' "
                    ."AND long_name = '$_POST[otorso]'");
        db_op_result($weap,__LINE__,__FILE__);
    }
    if(!$_POST['leg'] == '')
    {
        $weap = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount - $_POST[force] "
                    ."WHERE tribeid = '$tribeinfo[goods_tribe]' "
                    ."AND long_name = '$_POST[leg]'");
         db_op_result($weap,__LINE__,__FILE__);
    }
    if(!$_POST['shield'] == '')
    {
        $weap = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount - $_POST[force] "
                    ."WHERE tribeid = '$tribeinfo[goods_tribe]' "
                    ."AND long_name = '$_POST[shield]'");
          db_op_result($weap,__LINE__,__FILE__);
    }
    if(!$_POST['barding'] == '')
    {
        $weap = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount - $_POST[force] "
                    ."WHERE tribeid = '$tribeinfo[goods_tribe]' "
                    ."AND long_name = '$_POST[barding]'");
         db_op_result($weap,__LINE__,__FILE__);
    }
        include("weight.php");
}


    // DISBAND GARRISONS


if(ISSET($_POST['disband']))
{
    echo "<CENTER>Unit $_POST[disband] disbanded.</CENTER>";
    $dis = $db->Execute("SELECT * FROM $dbtables[garrisons] WHERE garid = $_POST[disband]");
    db_op_result($dis,__LINE__,__FILE__);
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
    db_op_result($res,__LINE__,__FILE__);
    $tribe = $res->fields;

    $qiry = $db->Execute("UPDATE $dbtables[tribes] "
                    ."SET warpop = warpop - $disband[force], "
                    ."activepop = activepop + $disband[force], "
                    ."maxam = maxam + $disband[force] "
                    ."WHERE tribeid = $disband[tribeid] "
                    ."AND clanid = $disband[clanid] "
                    ."AND hex_id = $disband[hex_id]");
     db_op_result($qiry,__LINE__,__FILE__);
    $qiry = $db->Execute("UPDATE $dbtables[livestock] "
                    ."SET amount = amount + $disband[horses] "
                    ."WHERE type = 'Horses' "
                    ."AND tribeid = '$tribe[goods_tribe]'");
     db_op_result($qiry,__LINE__,__FILE__);
    $qiry = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount + $disband[horses] "
                    ."WHERE long_name = 'saddle' "
                    ."AND tribeid = '$tribe[goods_tribe]'");
    db_op_result($qiry,__LINE__,__FILE__);
    $qiry = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount + $disband[force] "
                    ."WHERE proper = '$disband[weapon1]' "
                    ."AND tribeid = '$tribe[goods_tribe]'");
     db_op_result($qiry,__LINE__,__FILE__);
    $qiry = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount + $disband[force] "
                    ."WHERE proper = '$disband[weapon2]' "
                    ."AND tribeid = '$tribe[goods_tribe]'");
     db_op_result($qiry,__LINE__,__FILE__);
    $qiry = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount + $disband[force] "
                    ."WHERE proper = '$disband[head_armor]' "
                    ."AND tribeid = '$tribe[goods_tribe]'");
    db_op_result($qiry,__LINE__,__FILE__);
    $qiry = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount + $disband[force] "
                    ."WHERE proper = '$disband[torso_armor]' "
                    ."AND tribeid = '$tribe[goods_tribe]'");
     db_op_result($qiry,__LINE__,__FILE__);
    $qiry = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount + $disband[force] "
                    ."WHERE proper = '$disband[otorso_armor]' "
                    ."AND tribeid = '$tribe[goods_tribe]'");
     db_op_result($qiry,__LINE__,__FILE__);
    $qiry = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount + $disband[force] "
                    ."WHERE proper = '$disband[legs_armor]' "
                    ."AND tribeid = '$tribe[goods_tribe]'");
      db_op_result($qiry,__LINE__,__FILE__);
    $qiry = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount + $disband[force] "
                    ."WHERE proper = '$disband[shield]' "
                    ."AND tribeid = '$tribe[goods_tribe]'");
     db_op_result($qiry,__LINE__,__FILE__);
    $qiry = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount + $disband[force] "
                    ."WHERE proper = '$disband[horse_armor]' "
                    ."AND tribeid = '$tribe[goods_tribe]'");
      db_op_result($qiry,__LINE__,__FILE__);
    $qiry = $db->Execute("DELETE FROM $dbtables[garrisons] "
                    ."WHERE garid = $_POST[disband]");
      db_op_result($qiry,__LINE__,__FILE__);
    echo "<CENTER>$disband[force] $disband[weapon1]";
    if(!$disband['weapon2'] == '')
    {
    echo ", $disband[weapon2]";
    }
    if(!$disband['head_armor'] == '')
    {
    echo ", $disband[head_armor]";
    }
    if(!$disband['torso_armor'] == '')
    {
    echo ", $disband[torso_armor]";
    }
    if(!$disband['otorso_armor'] == '')
    {
    echo ", $disband[otorso_armor]";
    }
    if(!$disband['legs_armor'] == '')
    {
    echo ", $disband[legs_armor]";
    }
    if(!$disband['shield'] == '')
    {
    echo ", $disband[shield]";
    }
    if(!$disband['horses'] > 0)
    {
    echo ", horses &amp; saddles";
    }
    if(!$disband['horse_armor'] == '')
    {
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
     db_op_result($tgar,__LINE__,__FILE__);
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

if($tgar->EOF)
{
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
if($tgarinfo['experience'] < 6)
{
    echo "Recruits";
        }
elseif($tgarinfo['experience'] < 12)
{
    echo "Green";
        }
elseif($tgarinfo['experience'] < 24)
{
    echo "Seasoned";
        }
elseif($tgarinfo['experience'] < 48)
{
    echo "Veteran";
        }
elseif($tgarinfo['experience'] <78)
{
    echo "Elite";
        }
elseif($tgarinfo['experience'] < 100)
{
    echo "Crack";
        }
else
{
    echo "Commando";
        }

    echo "</TD><TD>$tgarinfo[weapon1]</TD><TD>";
if($tgarinfo['weapon2'] == '')
{
    echo "None";
}
else
{
    echo "$tgarinfo[weapon2]";
}
    echo "</TD><TD>";
if($tgarinfo['head_armor'] == '')
{
    echo "None";
}
else
{
    echo "$tgarinfo[head_armor]";
}
    echo "</TD><TD>";
if($tgarinfo['otorso_armor'] == '')
{
    echo "None";
}
else
{
    echo "$tgarinfo[otorso_armor]";
}
    echo "</TD><TD>";
if($tgarinfo['torso_armor'] == '')
{
    echo "None";
}
else
{
    echo "$tgarinfo[torso_armor]";
}
    echo "</TD><TD>";
if($tgarinfo['legs_armor'] == '')
{
    echo "None";
}
else
{
    echo "$tgarinfo[legs_armor]";
}
    echo "</TD><TD>";
if($tgarinfo['shield'] == '')
{
    echo "None";
}
else
{
    echo "$tgarinfo[shield]";
}
    echo "</TD><TD>";
if($tgarinfo['horse_armor'] == '')
{
    echo "None";
}
else
{
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
                        ."WHERE tribeid <> '".$_SESSION['current_unit']."' AND "
                        ."clanid = '".$_SESSION['clanid']."' "
                        ."ORDER BY `force`");
    db_op_result($cgar,__LINE__,__FILE__);
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
    if($cgarinfo['experience'] < 6)
    {
        echo "Recruits";
    }
    elseif($cgarinfo['experience'] < 12)
    {
        echo "Green";
    }
    elseif($cgarinfo['experience'] < 24)
    {
        echo "Seasoned";
    }
    elseif($cgarinfo['experience'] < 48)
    {
        echo "Veteran";
    }
    elseif($cgarinfo['experience'] <78)
    {
        echo "Elite";
    }
    elseif($cgarinfo['experience'] < 100)
    {
        echo "Crack";
    }
    else
    {
        echo "Commando";
    }

    echo "</TD><TD>$cgarinfo[weapon1]</TD><TD>";
    if($cgarinfo['weapon2'] == '')
    {
        echo "None";
    }
    else
    {
        echo "$cgarinfo[weapon2]";
    }

    echo "</TD><TD>";
    if($cgarinfo['head_armor'] == '')
    {
        echo "None";
    }
    else
    {
        echo "$cgarinfo[head_armor]";
    }
    echo "</TD><TD>";

    if($cgarinfo['torso_armor'] == '')
    {
        echo "None";
    }
    else
    {
        echo "$cgarinfo[torso_armor]";
    }
    echo "</TD><TD>";

    if($cgarinfo['legs_armor'] == '')
    {
        echo "None";
    }
    else
    {
        echo "$cgarinfo[legs_armor]";
    }
    echo "</TD><TD>";

    if($cgarinfo['shield'] == '')
    {
        echo "None";
    }
    else
    {
        echo "$cgarinfo[shield]";
    }
    echo "</TD><TD>";

    if($cgarinfo['horse_armor'] == '')
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
