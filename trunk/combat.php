<?
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: combat.php

session_start();
header("Cache-control: private");
include("config.php");
include("game_time.php");

page_header("Tribal Combat");

connectdb();

    if( $_REQUEST[uniqueid] == '' )
    {
        $uniqueid = uniqid(microtime(),1);
    }
    else
    {
        $uniqueid = $_REQUEST[uniqueid];
    }
    $uniqueid2 = uniqid(microtime(),1);

if( !ISSET( $_REQUEST[orders] ) )
{
    if( $_REQUEST[orders] == 'cancel' | $_REQUEST[target] == 'cancel' )
    {
        $db->Execute("DELETE FROM $dbtables[combats] "
                    ."WHERE combat_id = '$uniqueid'");
        header("location:main.php");
    }

    $move = $db->Execute("SELECT * FROM $dbtables[tribes] "
                        ."WHERE tribeid = '$_SESSION[current_unit]'");
    $moves = $move->fields;

    $nwbe_clan = $db->Execute("SELECT * FROM $dbtables[tribes] "
                             ."WHERE tribeid = '$_REQUEST[target]'");
    $newbie_clan = $nwbe_clan->fields;

    $nwbe = $db->Execute("SELECT * FROM $dbtables[chiefs] "
                        ."WHERE clanid = '$newbie_clan[clanid]'");
    $newbie = $nwbe->fields;

    if( $newbie[clanid] == $_SESSION[clanid] )
    {
        echo 'You cannot attack yourself.<BR>';
        page_footer();
    }

    if( $newbie[active] < 25 )
    {
        echo 'This tribe is under Newbie Protection.<BR>';
        page_footer();
    }
//    $check = $db->Execute("SELECT * FROM $dbtables[garrisons] "
//                         ."WHERE tribeid = '$_SESSION[tribeid]' "
//                         ."AND hex_id = '$_SESSION[hex_id]'");
//    if( $check->EOF )
//    {
//        header("location:main.php");
//    }

    if( $moves[move_pts] < 1)
    { 
        header("location:main.php"); 
    }

    $target = $_REQUEST[target];
    $tabletarget = explode('.', $target);
    $tableattacker = explode('.', $_SESSION[current_unit]);
    $tribe = $db->Execute("SELECT * FROM $dbtables[tribes] "
                         ."WHERE tribeid = '$target'");
    $tribeinfo = $tribe->fields;

////////////////////////////Remind ourselves who we have for soldiers////////////////////
    $gar = $db->Execute("SELECT * FROM $dbtables[garrisons] "
                       ."WHERE tribeid = '$_SESSION[current_unit]'");
    $afor = 0;

    while( !$gar->EOF )
    {
        $garrison = $gar->fields;
        $sectorforce1 = round($garrison[force]/4);
        $sectorforce3 = round($garrison[force]/4);
        $sectorforce2 = $garrison[force] - ($sectorforce1 + $sectorforce3);

        $db->Execute("INSERT INTO $dbtables[combats] "
                    ."VALUES("
                    ."'$uniqueid',"
                    ."'A',"
                    ."'$garrison[garid]',"
                    ."'$garrison[tribeid]',"
                    ."'$garrison[force]',"
                    ."'$garrison[force]',"
                    ."'$garrison[experience]',"
                    ."'$garrison[terrainsp]',"
                    ."'$garrison[exp]',"
                    ."'$garrison[horses]',"
                    ."'$garrison[weapon1]',"
                    ."'$garrison[weapon2]',"
                    ."'$garrison[head_armor]',"
                    ."'$garrison[torso_armor]',"
                    ."'$garrison[otorso_armor]',"
                    ."'$garrison[legs_armor]',"
                    ."'$garrison[shield]',"
                    ."'$garrison[horse_armor]',"
                    ."'$garrison[trooptype]',"
                    ."'$sectorforce1',"
                    ."'$sectorforce1',"
                    ."'$sectorforce2',"
                    ."'$sectorforce2',"
                    ."'$sectorforce3',"
                    ."'$sectorforce3',"
                    ."'$tribeinfo[hex_id]'"
                    .")");

        $afor += $garrison[force];
        $gar->MoveNext();
    }


//////////////////////////////////////Gather info about the target////////////////////////
    $target = $_REQUEST[target];
    $tar = $db->Execute("SELECT * FROM $dbtables[tribes] "
                       ."WHERE tribeid = '$target'");
    $targetinfo = $tar->fields;

    $gar = array();
    $gar = $db->Execute("SELECT * FROM $dbtables[garrisons] "
                       ."WHERE tribeid = '$target'");
    $tfor = 0;

    while( !$gar->EOF )
    {
        $garrison = $gar->fields;
        $sectorforce1 = round($garrison[force]/4);
        $sectorforce3 = round($garrison[force]/4);
        $sectorforce2 = $garrison[force] - ($sectorforce1 + $sectorforce3);

        $db->Execute("INSERT INTO $dbtables[combats] "
                    ."VALUES("
                    ."'$uniqueid',"
                    ."'D',"
                    ."'$garrison[garid]',"
                    ."'$garrison[tribeid]',"
                    ."'$garrison[force]',"
                    ."'$garrison[force]',"
                    ."'$garrison[experience]',"
                    ."'$garrison[terrainsp]',"
                    ."'$garrison[exp]',"
                    ."'$garrison[horses]',"
                    ."'$garrison[weapon1]',"
                    ."'$garrison[weapon2]',"
                    ."'$garrison[head_armor]',"
                    ."'$garrison[torso_armor]',"
                    ."'$garrison[otorso_armor]',"
                    ."'$garrison[legs_armor]',"
                    ."'$garrison[shield]',"
                    ."'$garrison[horse_armor]',"
                    ."'$garrison[trooptype]',"
                    ."'$sectorforce1',"
                    ."'$sectorforce1',"
                    ."'$sectorforce2',"
                    ."'$sectorforce2',"
                    ."'$sectorforce3',"
                    ."'$sectorforce3',"
                    ."'$garrison[hex_id]'"
                    .")");

        $tfor += $garrison[force];
        $gar->MoveNext();
    }

/////////////////////////////////////Gather info about possible clanmates////////////////////////
    $rei = $db->Execute("SELECT * FROM $dbtables[garrisons] "
                       ."WHERE tribeid <> '$target' "
                       ."AND hex_id = '$targetinfo[hex_id]' "
                       ."AND clanid = '$targetinfo[clanid]'");

    while( !$rei->EOF )
    {
        $garrison = $rei->fields;
        $clanrein = round($garrison[force]/2);
        $sectorforce1 = round($clanrein/4);
        $sectorforce3 = round($clanrein/4);
        $sectorforce2 = $clanrein - ($sectorforce1 + $sectorforce3);
        $tfor += $clanrein;

        $db->Execute("INSERT INTO $dbtables[combats] "
                    ."VALUES("
                    ."'$uniqueid',"
                    ."'D',"
                    ."'$garrison[garid]',"
                    ."'$garrison[tribeid]',"
                    ."'$clanrein','$clanrein',"
                    ."'$garrison[experience]',"
                    ."'$garrison[terrainsp]',"
                    ."'$garrison[exp]',"
                    ."'$garrison[horses]',"
                    ."'$garrison[weapon1]',"
                    ."'$garrison[weapon2]',"
                    ."'$garrison[head_armor]',"
                    ."'$garrison[torso_armor]',"
                    ."'$garrison[otorso_armor]',"
                    ."'$garrison[legs_armor]',"
                    ."'$garrison[shield]',"
                    ."'$garrison[horse_armor]',"
                    ."'$garrison[trooptype]',"
                    ."'$sectorforce1',"
                    ."'$sectorforce1',"
                    ."'$sectorforce2',"
                    ."'$sectorforce2',"
                    ."'$sectorforce3',"
                    ."'$sectorforce3',"
                    ."'$garrison[hex_id]'"
                    .")");

        $rei->MoveNext();
    }

    $rei = array();
////////////////////////////////////Gather info about possible allies/////////////////////////////
    $garrison = array();

    $all = $db->Execute("SELECT * FROM $dbtables[garrisons] "
                       ."WHERE hex_id = '$targetinfo[hex_id]' "
                       ."AND clanid <> '$targetinfo[clanid]'");

    while( !$all->EOF )
    {
        $garrison = $all->fields;
        $ally = $db->Execute("SELECT * FROM $dbtables[alliances] "
                            ."WHERE offerer_id = '$targetinfo[clanid]' "
                            ."AND receipt_id = '$garrison[clanid]' "
                            ."AND accept = 'Y'");
        $ally1info = $ally->fields;

        if( $ally1info[offerer_id] == '' )
        {
            $ally2 = $db->Execute("SELECT * FROM $dbtables[alliances] "
                                 ."WHERE offerer_id = '$garrison[clanid]' "
                                 ."AND receipt_id = '$targetinfo[clanid]' "
                                 ."AND accept = 'Y'");
            $ally2info = $ally2->fields;
        }

        if( $ally1info[receipt_id] )
        { 
            $allied_reinforcement = round($garrison[force]/3);
            $sectorforce1 = round($allied_reinforcement/4);
            $sectorforce3 = round($allied_reinforcement/4);
            $sectorforce2 = $allied_reinforcement - ($sectorforce1 + $sectorforce3);

            $db->Execute("INSERT INTO $dbtables[combats] "
                        ."VALUES("
                        ."'$uniqueid',"
                        ."'D',"
                        ."'$garrison[garid]',"
                        ."'$garrison[tribeid]',"
                        ."'$allied_reinforcement',"
                        ."'$allied_reinforcement',"
                        ."'$garrison[experience]',"
                        ."'$garrison[terrainsp]',"
                        ."'$garrison[exp]',"
                        ."'$garrison[horses]',"
                        ."'$garrison[weapon1]',"
                        ."'$garrison[weapon2]',"
                        ."'$garrison[head_armor]',"
                        ."'$garrison[torso_armor]',"
                        ."'$garrison[otorso_armor]',"
                        ."'$garrison[legs_armor]',"
                        ."'$garrison[shield]',"
                        ."'$garrison[horse_armor]',"
                        ."'$garrison[trooptype]',"
                        ."'$sectorforce1',"
                        ."'$sectorforce1',"
                        ."'$sectorforce2',"
                        ."'$sectorforce2',"
                        ."'$sectorforce3',"
                        ."'$sectorforce3',"
                        ."'$garrison[hex_id]'"
                        .")");

            $tfor += $allied_reinforcement;
        }
        elseif( $ally2info[receipt_id] )
        {
            $allied_reinforcement = round($garrison[force]/3);
            $sectorforce1 = round($allied_reinforcement/4);
            $sectorforce3 = round($allied_reinforcement/4);
            $sectorforce2 = $allied_reinforcement - ($sectorforce1 + $sectorforce3);

            $db->Execute("INSERT INTO $dbtables[combats] "
                        ."VALUES("
                        ."'$uniqueid',"
                        ."'D',"
                        ."'$garrison[garid]',"
                        ."'$garrison[tribeid]',"
                        ."'$allied_reinforcement',"
                        ."'$allied_reinforcement',"
                        ."'$garrison[experience]',"
                        ."'$garrison[terrainsp]',"
                        ."'$garrison[exp]',"
                        ."'$garrison[horses]',"
                        ."'$garrison[weapon1]',"
                        ."'$garrison[weapon2]',"
                        ."'$garrison[head_armor]',"
                        ."'$garrison[torso_armor]',"
                        ."'$garrison[otorso_armor]',"
                        ."'$garrison[legs_armor]',"
                        ."'$garrison[shield]',"
                        ."'$garrison[horse_armor]',"
                        ."'$garrison[trooptype]',"
                        ."'$sectorforce1',"
                        ."'$sectorforce1',"
                        ."'$sectorforce2',"
                        ."'$sectorforce2',"
                        ."'$sectorforce3',"
                        ."'$sectorforce3',"
                        ."'$garrison[hex_id]'"
                        .")");

            $tfor += $allied_reinforcement;
        }
        $all->MoveNext();
    }
   

    $attacker = $db->Execute("SELECT * FROM $dbtables[combats] "
                            ."WHERE side = 'A' "
                            ."AND combat_id = '$uniqueid'");

    
    echo '<CENTER><TABLE BORDER=0><TR BGCOLOR=';
    echo "$color_header";
    echo '><TD ALIGN=CENTER COLSPAN=12><FONT SIZE=+2>You Have Run Across Unallied Tribe ';
    echo "$target";
    echo '</FONT></TD></TR>';
    echo '<TR BGCOLOR=';
    echo "$color_header";
    echo '><TD ALIGN=CENTER COLSPAN=9>Your Units Await Your Orders</TD>';
    echo '<TD COLSPAN=3 BGCOLOR=RED ALIGN=CENTER><FORM ACTION=combat.php METHOD=POST>';
    echo '<SELECT NAME=orders><OPTION VALUE=cancel>Cancel</OPTION><OPTION VALUE=attack>Attack</OPTION>';

    if( $afor > $tfor )
    {
        echo '<OPTION VALUE=DeVA>DeVA</OPTION></SELECT>';
    }
    else
    {
        echo '</SELECT>';
    }
    echo "<INPUT TYPE=HIDDEN NAME=uniqueid VALUE=\"$uniqueid\">";
    echo '<INPUT TYPE=HIDDEN NAME=target VALUE=';
    echo "$target";
    echo '><INPUT TYPE=SUBMIT VALUE=COMMIT></FORM>';
    echo '</TD></TR>';
    echo '<TR BGCOLOR=';
    echo "$color_header";
    echo '><TD>ID</TD><TD>Force Size</TD><TD>Horses</TD><TD>Experience</TD>';
    echo '<TD>Primary Weapon</TD><TD>Secondary Weapon</TD>';
    echo '<TD>Head Armor</TD><TD>Over Body Armor</TD><TD>Body Armor</TD>';
    echo '<TD>Leg Armor</TD><TD>Shield</TD><TD>Horse Armor</TD></TR>';

    $linecolor = $color_line2;

    if( $attacker->EOF )
    {
        echo '<TR bgcolor=';
        echo "$linecolor";
        echo '><TD COLSPAN=12><CENTER>None</CENTER></TD></TR></TABLE>';
    }

    while( !$attacker->EOF )
    {
        if( $linecolor == $color_line1 )
        {
            $linecolor = $color_line2;
        }
        else
        {
            $linecolor = $color_line1;
        }

        $agarinfo = $attacker->fields;
        echo '<TR bgcolor = ';
        echo "$linecolor";
        echo '><TD>';
        echo "$agarinfo[garid]";
        echo '</TD><TD>';
        echo "$agarinfo[curforce]";
        echo '</TD><TD>';
        echo "$agarinfo[horses]";
        echo '</TD><TD>';

        if( $agarinfo[experience] < 6 )
        {
            echo 'Recruits';
        }
        elseif( $agarinfo[experience] < 12 )
        {
            echo 'Green';
        }
        elseif( $agarinfo[experience] < 24 )
        {
            echo 'Seasoned';
        }
        elseif( $agarinfo[experience] < 48 )
        {
            echo 'Veteran';
        }
        elseif( $agarinfo[experience] <78 )
        {
            echo 'Elite';
        }
        elseif( $agarinfo[experience] < 100 )
        {
            echo 'Crack';
        }
        else
        {
            echo 'Commando';
        }

        echo '</TD><TD>';
        echo "$agarinfo[weapon1]";
        echo '</TD><TD>';

        if( $agarinfo[weapon2] == '' )
        {
            echo 'None';
        }
        else
        {
            echo "$agarinfo[weapon2]";
        }
        echo '</TD><TD>';

        if( $agarinfo[head_armor] == '' )
        {
            echo 'None';
        }
        else
        {
            echo "$agarinfo[head_armor]";
        }
        echo '</TD><TD>';

        if( $agarinfo[otorso_armor] == '' )
        {
            echo 'None';
        }
        else
        {
            echo "$agarinfo[otorso_armor]";
        }
        echo '</TD><TD>';

        if( $agarinfo[torso_armor] == '' )
        {
            echo 'None';
        }
        else
        {
            echo "$agarinfo[torso_armor]";
        }
        echo '</TD><TD>';

        if( $agarinfo[legs_armor] == '' )
        {
            echo 'None';
        }
        else
        {
            echo "$agarinfo[legs_armor]";
        }
        echo '</TD><TD>';

        if( $agarinfo[shield] == '' )
        {
            echo 'None';
        }
        else
        {
            echo "$agarinfo[shield]";
        }
        echo '</TD><TD>';

        if( $agarinfo[horse_armor] == '' )
        {
            echo 'None';
        }
        else
        {
            echo "$agarinfo[horse_armor]";
        }
        echo '</TD></TR>';

        $attacker->MoveNext();
    }

    echo '</TABLE><P>';


}

if( $_REQUEST[orders] == 'cancel' | $_REQUEST[target] == 'cancel' )
{
    $db->Execute("DELETE FROM $dbtables[combats] "
                ."WHERE combat_id = '$uniqueid'");
    header("location:main.php");
    $target = explode('.', $_REQUEST[target]);
}
elseif( $_REQUEST[orders] == 'DeVA' )
{
    $target = $_REQUEST[target];

    $hex = $db->Execute("SELECT * FROM $dbtables[tribes] "
                       ."WHERE tribeid = '$target'");
    $hexinfo = $hex->fields;

    $db->Execute("UPDATE $dbtables[tribes] "
                ."SET move_pts = '0', "
                ."hex_id = '$hexinfo[hex_id]' "
                ."WHERE goods_tribe = '$_SESSION[current_unit]'");

    $db->Execute("UPDATE $dbtables[mapping] "
                ."SET `$_SESSION[clanid]` = 'Y' "
                ."WHERE hex_id = '$hexinfo[hex_id]'");

    $db->Execute("UPDATE $dbtables[garrisons] "
                ."SET hex_id = '$hexinfo[hex_id]' "
                ."WHERE tribeid = '$_SESSION[current_unit]'");

    $db->Execute("UPDATE $dbtables[tribes] "
                ."SET DeVA = '$_SESSION[current_unit]' "
                ."WHERE tribeid = '$target'");

    $db->Execute("INSERT INTO $dbtables[logs] "
                ."VALUES("
                ."'',"
                ."'$month[count]',"
                ."'$year[count]',"
                ."'$_SESSION[clanid]',"
                ."'$_SESSION[current_unit]',"
                ."'WAR',"
                ."'$stamp',"
                ."'War Activity: $_SESSION[current_unit] has "
                ."established seige to $target and will begin to "
                ."deny extra village activities (DeVA).')");

    $db->Execute("INSERT INTO $dbtables[logs] "
                ."VALUES("
                ."'',"
                ."'$month[count]',"
                ."'$year[count]',"
                ."'0000',"
                ."'0000.00',"
                ."'WAR',"
                ."'$stamp',"
                ."'War Activity: $_SESSION[current_unit] has "
                ."established seige to $target and will begin to "
                ."deny extra village activities (DeVA).')");

    $db->Execute("INSERT INTO $dbtables[logs] "
                ."VALUES("
                ."'',"
                ."'$month[count]',"
                ."'$year[count]',"
                ."'$hexinfo[clanid]',"
                ."'$hexinfo[tribeid]',"
                ."'WAR',"
                ."'$stamp',"
                ."'War Activity: $_SESSION[current_unit] has "
                ."established seige to $target and will begin "
                ."to deny extra village activities (DeVA).')");

    $db->Execute("DELETE $dbtables[activities] "
                ."WHERE tribeid = '$target'");
    echo "You are now laying seige to $target.<BR>";
    TEXT_GOTOMAIN();
}
elseif( $_REQUEST[orders] == 'attack' )
{

    $checkdone = $db->Execute("SELECT * FROM $dbtables[subtribe_id] "
                             ."WHERE unique_id = '$uniqueid'");
    if( !$checkdone->EOF )
    {
        echo "<P>You must have hit backspace or refresh by \"accident\".<BR>";
        page_footer();
    }
    $db->Execute("INSERT INTO $dbtables[subtribe_id] "
                ."VALUES("
                ."'$uniqueid')");
    $targetclan = explode( '.', $_REQUEST[target] );
    $currentunit = explode('.', $_SESSION[current_unit]);
    $chief = $db->Execute("SELECT * FROM $dbtables[chiefs] "
                         ."WHERE clanid = '$_SESSION[clanid]'");
    $chiefinfo = $chief->fields;
    if( $chiefinfo[active] < 24 )
    {
        ///
        ///Make New Chiefs non-protected if they choose to attack someone.
        ///
        $db->Execute("UPDATE $dbtables[chiefs] "
                    ."SET active = 24 "
                    ."WHERE clanid = '$chiefinfo[clanid]'");
    }

    echo "<CENTER><TABLE WIDTH=\"80%\" BORDER=1><TR><TD><TABLE BORDER=0 WIDTH=\"100%\">";
    ///////////////////Get the starting stats////////////////////////////////////////////////////////
    $totalatt = 0;
    $totaldef = 0;
    $totalhorseatt = 0;
    $totalhorsedef = 0;
    $attfor = $db->Execute("SELECT * FROM $dbtables[combats] "
                          ."WHERE side = 'A' "
                          ."AND combat_id = '$uniqueid'");
    while( !$attfor->EOF )
    {
        $attforce = $attfor->fields;
        $totalatt += $attforce[startforce];
        $totalhorseatt += $attforce[horses];
        $attfor->MoveNext();
    }
    $deffor = $db->Execute("SELECT * FROM $dbtables[combats] "
                          ."WHERE side = 'D' "
                          ."AND combat_id = '$uniqueid'");
    while( !$deffor->EOF )
    {
        $defforce = $deffor->fields;
        $totaldef += $defforce[startforce];
        $totalhorsedef += $defforce[horses];
        $deffor->MoveNext();
    }
    echo "<TR BGCOLOR=$color_header ALIGN=CENTER><TD><FONT SIZE=+1>";
    echo "Total Attacking Forces:</FONT></TD><TD><FONT SIZE=+1>";
    echo "Total Defending Forces:</FONT></TD></TR>";
    echo "<TR BGCOLOR=$color_line1 ALIGN=CENTER><TD>";
    echo "$totalatt (warriors)</TD><TD>$totaldef (warriors)</TD></TR>";
    echo "<TR BGCOLOR=$color_line2 ALIGN=CENTER><TD>";
    echo "$totalhorseatt (horses)</TD><TD>$totalhorsedef (horses)</TD></TR>";

    echo "</TABLE></TD></TR></TABLE></CENTER><CENTER>";
    echo "<TABLE WIDTH=\"80%\" BORDER=1><TR><TD><TABLE BORDER=0 WIDTH=\"100%\">";

    ////////Missile Phase, defender first/////////////////////////////////////////////////////////////////
    $def = $db->Execute("SELECT * FROM $dbtables[combats] "
                       ."WHERE trooptype = 'A' "
                       ."AND side = 'D' "
                       ."AND combat_id = '$uniqueid' "
                       ."OR trooptype = 'Q' "
                       ."AND side = 'D' "
                       ."AND combat_id = '$uniqueid' "
                       ."OR trooptype = 'B' "
                       ."AND SIDE = 'D' "
                       ."AND combat_id = '$uniqueid' "
                       ."OR weapon1 = 'Horsebow' "
                       ."AND side = 'D' "
                       ."AND combat_id = '$uniqueid' "
                       ."AND trooptype = 'C'");

    $titlecount = 0;
    if( $def->EOF )
    {
        echo "<TR ALIGN=CENTER><TD COLSPAN=3><FONT SIZE=+2>";
        echo "Archery Phase (Defenders)</FONT></TD></TR>";
        echo "<TR BGCOLOR=$color_header ALIGN=CENTER>";
        echo "<TD COLSPAN=3>No Archer Units</TD></TR>";
    }
    while( !$def->EOF )
    {
        $definfo = $def->fields;

        if( $titlecount < 1 )
        {
            echo "<CENTER><FONT SIZE=+2>Archery Phase (Defenders)</FONT></CENTER>";
            echo "<TR BGCOLOR=$color_header><TD><FONT SIZE=+1>Sector One</FONT></TD>";
            echo "<TD><FONT SIZE=+1>Sector Two</FONT></TD>";
            echo "<TD><FONT SIZE=+1>Sector Three</FONT></TD></TR>";
            $titlecount++;
        }


        ///////////Get the right variables//////////////////////////////////////////////////////////////////////
        echo "<TR><TD COLSPAN=3>&nbsp;</TD></TR>";
        $weap = $db->Execute("SELECT * FROM $dbtables[missile_types] "
                            ."WHERE type = '$definfo[weapon1]'");
        $weapon1 = $weap->fields;

        $skarc = $db->Execute("SELECT * FROM $dbtables[skills] "
                             ."WHERE tribeid = '$definfo[tribeid]' "
                             ."AND abbr = 'arc'");
        $archery = $skarc->fields;

        if( $definfo[trooptype] == 'A' | $definfo[trooptype] == 'C' )
        {
            $arr = $db->Execute("SELECT * FROM $dbtables[products] "
                               ."WHERE tribeid = '$definfo[tribeid]' "
                               ."AND long_name = 'arrows'");
            $arrows = $arr->fields;
            $ammotype = 'arrows';
        }
        elseif( $definfo[trooptype] == 'B' )
        {
            $arr = $db->Execute("SELECT * FROM $dbtables[products] "
                               ."WHERE tribeid = '$definfo[tribeid]' "
                               ."AND long_name = 'pellets'");
            $arrows = $arr->fields;
            $ammotype = 'pellets';
        }
        elseif( $definfo[trooptype] == 'Q' )
        {
            $arr = $db->Execute("SELECT * FROM $dbtables[products] "
                               ."WHERE tribeid = '$definfo[tribeid]' "
                               ."AND long_name = 'quarrels'");
            $arrows = $arr->fields;
            $ammotype = 'quarrels';
        }
        else
        {
            $arrows[amount] = 0;
            $ammotype = 'none';
        }



        $skldr = $db->Execute("SELECT * FROM $dbtables[skills] "
                             ."WHERE tribeid = '$definfo[tribeid]' "
                             ."AND abbr = 'ldr'");
        $leadership = $skldr->fields;

        $mor = $db->Execute("SELECT * FROM $dbtables[tribes] "
                           ."WHERE tribeid = '$definfo[tribeid]'");
        $morale = $mor->fields;

        $eff = $db->Execute("SELECT * FROM $dbtables[combat_terrain_effect] "
                           ."WHERE type = 'archery'");
        $terrain_effect = $eff->fields;

        $ter = $db->Execute("SELECT * FROM $dbtables[hexes] "
                           ."WHERE hex_id = '$definfo[hex_id]'");
        $terrain = $ter->fields;

        $ter_mods = $db->Execute("SELECT * from $dbtables[combat_terrain_mods]");
        $terrainmods = $ter_mods->fields;

        $cw = $db->Execute("SELECT * FROM $dbtables[weather] "
                          ."WHERE current_type = 'Y'");
        $cur_weath = $cw->fields;

        $weath = $db->Execute("SELECT * FROM $dbtables[combat_weather] "
                             ."WHERE type = '$weapon1[long_name]'");
        $weather = $weath->fields;

        $modify = $terrain[terrain];
        $hownow = $cur_weath[weather_id];
        //////////////////////////Select a target and get the armor info//////////////////////////////////////////
        $attackers1 = $db->Execute("SELECT COUNT(*) AS total FROM $dbtables[combats] "
                                  ."WHERE side = 'A' "
                                  ."AND combat_id = '$uniqueid' "
                                  ."AND sector1 > 0");
        $numattackers1 = $attackers1->fields;
        $numattackers1[total] -= 1;
        $selection1 = rand(0,$numattackers1[total]);

        $select1 = $db->Execute("SELECT * FROM $dbtables[combats] "
                               ."WHERE side = 'A' "
                               ."AND combat_id = '$uniqueid' "
                               ."AND sector1 > 0 "
                               ."LIMIT $selection1, 1");
        $selected1 = $select1->fields;

        $attackers2 = $db->Execute("SELECT COUNT(*) AS total FROM $dbtables[combats] "
                                  ."WHERE side = 'A' "
                                  ."AND combat_id = '$uniqueid' "
                                  ."AND sector2 > 0");
        $numattackers2 = $attackers2->fields;
        $numattackers2[total] -= 1;
        $selection2 = rand(0,$numattackers2[total]);

        $select2 = $db->Execute("SELECT * FROM $dbtables[combats] "
                               ."WHERE side = 'A' "
                               ."AND combat_id = '$uniqueid' "
                               ."AND sector2 > 0 "
                               ."LIMIT $selection2, 1");
        $selected2 = $select2->fields;

        $attackers3 = $db->Execute("SELECT COUNT(*) AS total FROM $dbtables[combats] "
                                  ."WHERE side = 'A' "
                                  ."AND combat_id = '$uniqueid' "
                                  ."AND sector3 > 0");
        $numattackers3 = $attackers3->fields;
        $numattackers3[total] -= 1;
        $selection3 = rand(0,$numattackers3[total]);

        $select3 = $db->Execute("SELECT * FROM $dbtables[combats] "
                               ."WHERE side = 'A' "
                               ."AND combat_id = '$uniqueid' "
                               ."AND sector3 > 0 "
                               ."LIMIT $selection3, 1");
        $selected3 = $select3->fields;


        $head = $db->Execute("SELECT * FROM $dbtables[armor] "
                            ."WHERE proper = '$selected1[head_armor]'");
        $head_armor1 = $head->fields;

        $torso = $db->Execute("SELECT * FROM $dbtables[armor] "
                             ."WHERE proper = '$selected1[torso_armor]'");
        $torso_armor1 = $torso->fields;

        $otorso = $db->Execute("SELECT * FROM $dbtables[armor] "
                              ."WHERE proper = '$selected1[otorso_armor]'");
        $otorso_armor1 = $otorso->fields;

        $legs = $db->Execute("SELECT * FROM $dbtables[armor] "
                            ."WHERE proper = '$selected1[legs_armor]'");
        $legs_armor1 = $legs->fields;

        $shield = $db->Execute("SELECT * FROM $dbtables[armor] "
                              ."WHERE proper = '$selected1[shield]'");
        $shield_armor1 = $shield->fields;

        $horse = $db->Execute("SELECT * FROM $dbtables[armor] "
                             ."WHERE proper = '$selected1[horse_armor]'");
        $horse_armor1 = $horse->fields;


        $head = $db->Execute("SELECT * FROM $dbtables[armor] "
                            ."WHERE proper = '$selected2[head_armor]'");
        $head_armor2 = $head->fields;

        $torso = $db->Execute("SELECT * FROM $dbtables[armor] "
                             ."WHERE proper = '$selected2[torso_armor]'");
        $torso_armor2 = $torso->fields;

        $otorso = $db->Execute("SELECT * FROM $dbtables[armor] "
                              ."WHERE proper = '$selected2[otorso_armor]'");
        $otorso_armor2 = $otorso->fields;

        $legs = $db->Execute("SELECT * FROM $dbtables[armor] "
                            ."WHERE proper = '$selected2[legs_armor]'");
        $legs_armor2 = $legs->fields;

        $shield = $db->Execute("SELECT * FROM $dbtables[armor] "
                              ."WHERE proper = '$selected2[shield]'");
        $shield_armor2 = $shield->fields;

        $horse = $db->Execute("SELECT * FROM $dbtables[armor] "
                             ."WHERE proper = '$selected2[horse_armor]'");
        $horse_armor2 = $horse->fields;

        $head = $db->Execute("SELECT * FROM $dbtables[armor] "
                            ."WHERE proper = '$selected3[head_armor]'");
        $head_armor3 = $head->fields;

        $torso = $db->Execute("SELECT * FROM $dbtables[armor] "
                             ."WHERE proper = '$selected3[torso_armor]'");
        $torso_armor3 = $torso->fields;

        $otorso = $db->Execute("SELECT * FROM $dbtables[armor] "
                              ."WHERE proper = '$selected3[otorso_armor]'");
        $otorso_armor3 = $otorso->fields;

        $legs = $db->Execute("SELECT * FROM $dbtables[armor] "
                            ."WHERE proper = '$selected3[legs_armor]'");
        $legs_armor3 = $legs->fields;

        $shield = $db->Execute("SELECT * FROM $dbtables[armor] "
                              ."WHERE proper = '$selected3[shield]'");
        $shield_armor3 = $shield->fields;

        $horse = $db->Execute("SELECT * FROM $dbtables[armor] "
                             ."WHERE proper = '$selected3[horse_armor]'");
        $horse_armor3 = $horse->fields;


        if( $definfo[trooptype] == 'A' )
        {
            $armortype = 'arrow';
        }
        elseif( $definfo[trooptype] == 'Q' )
        {
            $armortype = 'quarrel';
        }
        elseif( $definfo[trooptype] == 'B' )
        {
            $armortype = 'pellet';
        }
        elseif( $definfo[trooptype] == 'C' )
        {
            $armortype = 'arrow';
        }
        elseif( $definfo[weapon1] == 'Horsebow' )
        {
            $armortype = 'arrow';
        }
        $armormod1 = $head_armor1[$armortype] + $torso_armor1[$armortype] + $otorso_armor1[$armortype] + $legs_armor1[$armortype] + $shield_armor1[$armortype] + $horse_armor1[$armortype];
        $armormod2 = $head_armor2[$armortype] + $torso_armor2[$armortype] + $otorso_armor2[$armortype] + $legs_armor2[$armortype] + $shield_armor2[$armortype] + $horse_armor2[$armortype];
        $armormod3 = $head_armor3[$armortype] + $torso_armor3[$armortype] + $otorso_armor3[$armortype] + $legs_armor3[$armortype] + $shield_armor3[$armortype] + $horse_armor3[$armortype];

        $sector1archers = 0;
        $sector2archers = 0;
        $sector3archers = 0;
        if( $definfo[sector1] > 0 )
        {
            $random1 = rand( 1,12 );
            $arrowbonus = 0;
            while( $arrows[amount] > 4 && $arrowbonus < $definfo[startsector1] )
            {
                $arrows[amount] -= 5;
                $arrowbonus += 1;
            }
            $definfo[startsector1] += $arrowbonus;
            $sector1archers = round(($weapon1[value] + ($archery[level] * $weapon1[skill_mult])) * $definfo[startsector1] * $terrainmods[$modify] * $weather[$hownow] * ($leadership[level] + $definfo[exp] + 10)/10 * $morale[morale] * (3 + $archery[level] + $leadership[level] + $random1/2)/10);
        }
        if( $definfo[sector2] > 0 )
        {
            $random2 = rand( 1,12 );
            $arrowbonus = 0;
            while( $arrows[amount] > 4 && $arrowbonus < $definfo[startsector2] )
            {
                $arrows[amount] -= 5;
                $arrowbonus += 1;
            }
            $definfo[startsector2] += $arrowbonus;
            $sector2archers = round(($weapon1[value] + ($archery[level] * $weapon1[skill_mult])) * $definfo[startsector2] * $terrainmods[$modify] * $weather[$hownow] * ($leadership[level] + $definfo[exp] + 10)/10 * $morale[morale] * (3 + $archery[level] + $leadership[level] + $random2/2)/10);
        }
        if( $definfo[sector3] > 0 )
        {
            $random3 = rand( 1,12 );
            $arrowbonus = 0;
            while( $arrows[amount] > 4 && $arrowbonus < $definfo[startsector3] )
            {
                $arrows[amount] -= 5;
                $arrowbonus += 1;
            }
            $definfo[startsector3] += $arrowbonus;
            $sector3archers = round(($weapon1[value] + ($archery[level] * $weapon1[skill_mult])) * $definfo[startsector3] * $terrainmods[$modify] * $weather[$hownow] * ($leadership[level] + $definfo[exp] + 10)/10 * $morale[morale] * (3 + $archery[level] + $leadership[level] + $random3/2)/10);
        }

$db->Execute("UPDATE $dbtables[products] "
            ."SET amount = $arrows[amount] "
            ."WHERE tribeid = $definfo[tribeid] "
            ."AND long_name = '$ammotype'");

$sector1archers = round(($sector1archers - round($armormod1 * ($sector1archers/10)))/10);
if($sector1archers < 10 && $definfo[sector1] > 0){
$sector1archers = round(($leadership[level] + $archery[level] + $morale[morale]) * 10);
}
if($sector1archers < 0){
$sector1archers = 0;
}
$work1 = $sector1archers;
while($work1 > 9){
$rand10 = rand(1,10);
$sector1defendingarchercasualties += $rand10;
$work1 -= 10;
}

if($sector1defendingarchercasualties > $selected1[sector1]){
$sector1defendingarchercasualties = $selected1[sector1];
}
if($sector1defendingarchercasualties > 0 && $selected1[sector1] < 1){
$sector1defendingarchercasualties = 0;
}
if($sector1defendingarchercasualties < 0){
$sector1defendingarchercasualties = 0;
}
$total1sectoratt += $sector1defendingarchercasualties;

if($total1sectoratt < 0){
$total1sectoratt = 0;
}
if(!$total1sectoratt){ $total1sectoratt = 0; }



$sector2archers = round(($sector2archers - round($armormod2 * ($sector2archers/10)))/10);
if($sector2archers < 10 && $definfo[sector2] > 0){
$sector2archers = round(($leadership[level] + $archery[level] + $morale[morale]) * 10);
}
if($sector2archers < 0){
$sector2archers = 0;
}
$work2 = $sector2archers;
while($work2 > 9){
$rand10 = rand(1,10);
$sector2defendingarchercasualties += $rand10;
$work2 -= 10;
}

if($sector2defendingarchercasualties > $selected2[sector2]){
$sector2defendingarchercasualties = $selected2[sector2];
}
if($sector2defendingarchercasualties > 0 && $selected2[sector2] < 1){
$sector2defendingarchercasualties = 0;
}
if($sector2defendingarchercasualties < 0){
$sector2defendingarchercasualties = 0;
}
$total2sectoratt += $sector2defendingarchercasualties;

if($total1sectordef < 0){
$total1sectordef = 0;
}
if(!$total2sectoratt){ $total2sectoratt = 0; }



$sector3archers = round(($sector3archers - round($armormod3 * ($sector3archers/10)))/10);
if($sector3archers < 10 && $definfo[sector3] > 0){
$sector3archers = round(($leadership[level] + $archery[level] + $morale[morale]) * 10);
}
if($sector3archers < 0){
$sector3archers = 0;
}
$work3 = $sector3archers;
while($work3 > 9){
$rand10 = rand(1,10);
$sector3defendingarchercasualties += $rand10;
$work3 -= 10;
}

if($sector3defendingarchercasualties > $selected3[sector3]){
$sector3defendingarchercasualties = $selected3[sector3];
}
if($sector3defendingarchercasualties > 0 && $selected3[sector3] < 1){
$sector3defendingarchercasualties = 0;
}
if($sector3defendingarchercasualties < 0){
$sector3defendingarchercasualties = 0;
}
$total3sectoratt += $sector3defendingarchercasualties;

if($total3sectoratt < 0){
$total3sectoratt = 0;
}
if(!$total3sectoratt){ $total3sectoratt = 0; }


echo "<TR>";
if($definfo[sector1] > 0){
echo "<TD>Force: $definfo[sector1] from Garrison $definfo[garid]</TD>";
}
else{
echo "<TD>$definfo[garid] Routed</TD>";
}
if($definfo[sector2] > 0){
echo "<TD>Force: $definfo[sector2] from Garrison $definfo[garid]</TD>";
}
else{
echo "<TD>$definfo[garid] Routed</TD>";
}
if($definfo[sector3] > 0){
echo "<TD>Force: $definfo[sector3] from Garrison $definfo[garid]</TD>";
}
else{
echo "<TD>$definfo[garid] Routed</TD>";
}
echo "</TR><TR>";
if($selected1[sector1] > 0){
echo "<TD>Attacking $selected1[sector1] from Garrison $selected1[garid]</TD>";
}
else{
echo "<TD>Looting</TD>";
}
if($selected2[sector2] > 0){
echo "<TD>Attacking $selected2[sector2] from Garrison $selected2[garid]</TD>";
}
else{
echo "<TD>Looting</TD>";
}
if($selected3[sector3] > 0){
echo "<TD>Attacking $selected3[sector3] from Garrison $selected3[garid]</TD>";
}
else{
echo "<TD>Looting</TD>";
}
echo "</TR>";
echo "<TR><TD><FONT COLOR=RED>Casualties: $sector1defendingarchercasualties</FONT></TD><TD><FONT COLOR=RED>Casualties: $sector2defendingarchercasualties</FONT></TD><TD><FONT COLOR=RED>Casualties: $sector3defendingarchercasualties</FONT></TD></TR>";

$db->Execute("UPDATE $dbtables[combats] SET sector1 = sector1 - '$sector1defendingarchercasualties', curforce = curforce - '$sector1defendingarchercasualties' where garid = '$selected1[garid]' AND combat_id = '$uniqueid'");
$db->Execute("UPDATE $dbtables[combats] SET sector2 = sector2 - '$sector2defendingarchercasualties', curforce = curforce - '$sector2defendingarchercasualties' where garid = '$selected2[garid]' AND combat_id = '$uniqueid'");
$db->Execute("UPDATE $dbtables[combats] SET sector3 = sector3 - '$sector3defendingarchercasualties', curforce = curforce - '$sector3defendingarchercasualties' where garid = '$selected3[garid]' AND combat_id = '$uniqueid'");
$sector1defendingarchercasualties = 0;
$sector2defendingarchercasualties = 0;
$sector3defendingarchercasualties = 0;

$def->MoveNext();
}
echo "<TR><TD COLSPAN=3>&nbsp</TD></TR>";
echo "<TR><TD>Total Sector Kills: $total1sectoratt</TD><TD>Total Sector Kills: $total2sectoratt</TD><TD>Total Sector Kills: $total3sectoratt</TD></TR>";
echo "</TD></TR></TABLE></TD></TR></TABLE></CENTER>";

//////////////////////////////////////////////////Attacker's Archery Turn///////////////////////////////////////////////////////
echo "<CENTER><TABLE WIDTH=\"80%\" BORDER=1><TR><TD><TABLE BORDER=0 WIDTH=\"100%\">";
$att = $db->Execute("SELECT * FROM $dbtables[combats] "
                   ."WHERE trooptype = 'A' "
                   ."AND side = 'A' "
                   ."AND combat_id = '$uniqueid' "
                   ."OR trooptype = 'Q' "
                   ."AND side = 'A' "
                   ."AND combat_id = '$uniqueid' "
                   ."OR trooptype = 'B' "
                   ."AND SIDE = 'A' "
                   ."AND combat_id = '$uniqueid' "
                   ."OR weapon1 = 'Horsebow' "
                   ."AND side = 'A' "
                   ."AND trooptype = 'C' "
                   ."AND combat_id = '$uniqueid'");

echo "<TR BGCOLOR=$color_header><TD><FONT SIZE=+1>Sector One</FONT></TD><TD><FONT SIZE=+1>Sector Two</FONT></TD><TD><FONT SIZE=+1>Sector Three</FONT></TD></TR>";
$titlecount = 0;
if($att->EOF){
echo "<TR ALIGN=CENTER><TD COLSPAN=3><FONT SIZE=+2>Archery Phase (Attackers)</FONT></TD></TR>";
echo "<TR BGCOLOR=$color_header ALIGN=CENTER><TD COLSPAN=3>No Archer Units</TD></TR>";
}
while(!$att->EOF){
$attinfo = $att->fields;
if($titlecount < 1){
echo "<CENTER><FONT SIZE=+2>Archery Phase (Attackers)</FONT></CENTER>";
$titlecount++;
}
///////////Get the right variables//////////////////////////////////////////////////////////////////////
echo "<TR><TD COLSPAN=3>&nbsp;</TD></TR>";
$weap = $db->Execute("SELECT * FROM $dbtables[missile_types] WHERE type = '$attinfo[weapon1]'");
$weapon1 = $weap->fields;
$skarc = $db->Execute("SELECT * FROM $dbtables[skills] WHERE tribeid = '$attinfo[tribeid]' AND abbr = 'arc'");
$archery = $skarc->fields;
if( $attinfo[trooptype] == 'A' | $attinfo[trooptype] == 'C' )
{
$arr = $db->Execute("SELECT * FROM $dbtables[products] "
                   ."WHERE tribeid = '$attinfo[tribeid]' "
                   ."AND long_name = 'arrows'");
$arrows = $arr->fields;
$ammotype = 'arrows';
}
        elseif( $definfo[trooptype] == 'B' )
        {
            $arr = $db->Execute("SELECT * FROM $dbtables[products] "
                               ."WHERE tribeid = '$definfo[tribeid]' "
                               ."AND long_name = 'pellets'");
            $arrows = $arr->fields;
            $ammotype = 'pellets';
        }
        elseif( $definfo[trooptype] == 'Q' )
        {
            $arr = $db->Execute("SELECT * FROM $dbtables[products] "
                               ."WHERE tribeid = '$definfo[tribeid]' "
                               ."AND long_name = 'quarrels'");
            $arrows = $arr->fields;
            $ammotype = 'quarrels';
        }
        else
        {
            $arrows[amount] = 0;
            $ammotype = 'none';
        } 


$skldr = $db->Execute("SELECT * FROM $dbtables[skills] WHERE tribeid = '$attinfo[tribeid]' AND abbr = 'ldr'");
$leadership = $skldr->fields;
$mor = $db->Execute("SELECT * FROM $dbtables[tribes] WHERE tribeid = '$attinfo[tribeid]'");
$morale = $mor->fields;
$eff = $db->Execute("SELECT * FROM $dbtables[combat_terrain_effect] WHERE type = 'archery'");
$terrain_effect = $eff->fields;
$ter = $db->Execute("SELECT * FROM $dbtables[hexes] WHERE hex_id = '$definfo[hex_id]'");
$terrain = $ter->fields;
$ter_mods = $db->Execute("SELECT * from $dbtables[combat_terrain_mods]");
$terrainmods = $ter_mods->fields;
$cw = $db->Execute("SELECT * FROM $dbtables[weather] WHERE current_type = 'Y'");
$cur_weath = $cw->fields;
$weath = $db->Execute("SELECT * FROM $dbtables[combat_weather] WHERE type = '$weapon1[long_name]'");
$weather = $weath->fields;
$modify = $terrain[terrain];
$hownow = $cur_weath[weather_id];
//////////////////////////Select a target and get the armor info//////////////////////////////////////////

$defenders1 = $db->Execute("SELECT COUNT(*) AS total FROM $dbtables[combats] "
                          ."WHERE side = 'D' "
                          ."AND combat_id = '$uniqueid' "
                          ."AND sector1 > 0");
$numdefenders1 = $defenders1->fields;
$numdefenders1[total] -= 1;
$selection1 = rand(0,$numdefenders1[total]);
$select1 = $db->Execute("SELECT * FROM $dbtables[combats] "
                       ."WHERE side = 'D' "
                       ."AND combat_id = '$uniqueid' "
                       ."AND sector1 > 0 "
                       ."LIMIT $selection1, 1");
$selected1 = $select1->fields;

$defenders2 = $db->Execute("SELECT COUNT(*) AS total FROM $dbtables[combats] "
                          ."WHERE side = 'D' "
                          ."AND combat_id = '$uniqueid' "
                          ."AND sector2 > 0");
$numdefenders2 = $defenders2->fields;
$numdefenders2[total] -= 1;
$selection2 = rand(0,$numdefenders2[total]);
$select2 = $db->Execute("SELECT * FROM $dbtables[combats] "
                       ."WHERE side = 'D' "
                       ."AND combat_id = '$uniqueid' "
                       ."AND sector2 > 0 "
                       ."LIMIT $selection2, 1");
$selected2 = $select2->fields;

$defenders3 = $db->Execute("SELECT COUNT(*) AS total FROM $dbtables[combats] "
                          ."WHERE side = 'D' "
                          ."AND combat_id = '$uniqueid' "
                          ."AND sector3 > 0");
$numdefenders3 = $defenders3->fields;
$numdefenders3[total] -= 1;
$selection3 = rand(0,$numdefenders3[total]);
$select3 = $db->Execute("SELECT * FROM $dbtables[combats] "
                       ."WHERE side = 'D' "
                       ."AND combat_id = '$uniqueid' "
                       ."AND sector3 > 0 "
                       ."LIMIT $selection3, 1");
$selected3 = $select3->fields;


$head = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$selected1[head_armor]'");
$head_armor1 = $head->fields;
$torso = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$selected1[torso_armor]'");
$torso_armor1 = $torso->fields;
$otorso = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$selected1[otorso_armor]'");
$otorso_armor1 = $otorso->fields;
$legs = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$selected1[legs_armor]'");
$legs_armor1 = $legs->fields;
$shield = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$selected1[shield]'");
$shield_armor1 = $shield->fields;
$horse = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$selected1[horse_armor]'");
$horse_armor1 = $horse->fields;


$head = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$selected2[head_armor]'");
$head_armor2 = $head->fields;
$torso = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$selected2[torso_armor]'");
$torso_armor2 = $torso->fields;
$otorso = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$selected2[otorso_armor]'");
$otorso_armor2 = $otorso->fields;
$legs = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$selected2[legs_armor]'");
$legs_armor2 = $legs->fields;
$shield = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$selected2[shield]'");
$shield_armor2 = $shield->fields;
$horse = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$selected2[horse_armor]'");
$horse_armor2 = $horse->fields;

$head = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$selected3[head_armor]'");
$head_armor3 = $head->fields;
$torso = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$selected3[torso_armor]'");
$torso_armor3 = $torso->fields;
$otorso = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$selected3[otorso_armor]'");
$otorso_armor3 = $otorso->fields;
$legs = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$selected3[legs_armor]'");
$legs_armor3 = $legs->fields;
$shield = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$selected3[shield]'");
$shield_armor3 = $shield->fields;
$horse = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$selected3[horse_armor]'");
$horse_armor3 = $horse->fields;


if($attinfo[trooptype] == 'A'){
$armortype = 'arrow';
}
elseif($attinfo[trooptype] == 'Q'){
$armortype = 'quarrel';
}
elseif($attinfo[trooptype] == 'B'){
$armortype = 'pellet';
}
elseif($attinfo[trooptype] == 'C'){
$armortype = 'arrow';
}
elseif($attinfo[weapon1] == 'Horsebow'){
$armortype = 'arrow';
}

$armormod1 = $head_armor1[$armortype] + $torso_armor1[$armortype] + $otorso_armor1[$armortype] + $legs_armor1[$armortype] + $shield_armor1[$armortype] + $horse_armor1[$armortype];
$armormod2 = $head_armor2[$armortype] + $torso_armor2[$armortype] + $otorso_armor2[$armortype] + $legs_armor2[$armortype] + $shield_armor2[$armortype] + $horse_armor2[$armortype];
$armormod3 = $head_armor3[$armortype] + $torso_armor3[$armortype] + $otorso_armor3[$armortype] + $legs_armor3[$armortype] + $shield_armor3[$armortype] + $horse_armor3[$armortype];

$sector1archers = 0;
$sector2archers = 0;
$sector3archers = 0;

if($attinfo[sector1] > 0){
$random1 = rand(1,12);
            $arrowbonus = 0;
            while( $arrows[amount] > 4 && $arrowbonus < $attinfo[startsector1] )
            {
                $arrows[amount] -= 5;
                $arrowbonus += 1;
            }
            $attinfo[startsector1] += $arrowbonus;
$sector1archers = round(($weapon1[value] + ($archery[level] * $weapon1[skill_mult])) * $attinfo[startsector1] * $terrainmods[$modify] * $weather[$hownow] * ($leadership[level] + $attinfo[exp] + 10)/10 * $morale[morale] * (3 + $archery[level] + $leadership[level] + $random1/2)/10);
}
if($attinfo[sector2] > 0){
$random2 = rand(1,12);
            $arrowbonus = 0;
            while( $arrows[amount] > 4 && $arrowbonus < $attinfo[startsector2] )
            {
                $arrows[amount] -= 5;
                $arrowbonus += 1;
            }
            $attinfo[startsector2] += $arrowbonus;
$sector2archers = round(($weapon1[value] + ($archery[level] * $weapon1[skill_mult])) * $attinfo[startsector2] * $terrainmods[$modify] * $weather[$hownow] * ($leadership[level] + $attinfo[exp] + 10)/10 * $morale[morale] * (3 + $archery[level] + $leadership[level] + $random2/2)/10);
}
if($attinfo[sector3] > 0){
$random3 = rand(1,12);
            $arrowbonus = 0;
            while( $arrows[amount] > 4 && $arrowbonus < $attinfo[startsector3] )
            {
                $arrows[amount] -= 5;
                $arrowbonus += 1;
            }
            $attinfo[startsector3] += $arrowbonus;
$sector3archers = round(($weapon1[value] + ($archery[level] * $weapon1[skill_mult])) * $attinfo[startsector3] * $terrainmods[$modify] * $weather[$hownow] * ($leadership[level] + $attinfo[exp] + 10)/10 * $morale[morale] * (3 + $archery[level] + $leadership[level] + $random3/2)/10);
}

$db->Execute("UPDATE $dbtables[products] "
            ."SET amount = $arrows[amount] "
            ."WHERE tribeid = '$attinfo[tribeid]' "
            ."AND long_name = '$ammotype'");
$sector1archers = round(($sector1archers - round($armormod1 * ($sector1archers/10)))/10);
if($sector1archers < 10 && $attinfo[sector1] > 0){
$sector1archers = round(($leadership[level] + $archery[level] + $morale[morale]) * 10);
}
if($sector1archers < 0){
$sector1archers = 0;
}
$work1 = $sector1archers;
while($work1 > 9){
$rand10 = rand(1,10);
$sector1archercasualties += $rand10;
$work1 -= 10;
}

if($sector1archercasualties > $selected1[sector1]){
$sector1archercasualties = $selected1[sector1];
}
if($sector1archercasualties < 0){
$sector1archercasualties = 0;
}
if($sector1archercasualties > 0 && $selected1[sector1] < 1){
$sector1archercasualties = 0;
}

$total1sectordef += $sector1archercasualties;

if($total1sectordef < 0){
$total1sectordef = 0;
}


$sector2archers = round(($sector2archers - round($armormod2 * ($sector2archers/10)))/10);
if($sector2archers < 10 && $attinfo[sector2] > 0){
$sector2archers = round(($leadership[level] + $archery[level] + $morale[morale]) * 10);
}
if($sector2archers < 0){
$sector2archers = 0;
}
$work2 = $sector2archers;
while($work2 > 9){
$rand10 = rand(1,10);
$sector2archercasualties += $rand10;
$work2 -= 10;
}

if($sector2archercasualties > $selected2[sector2]){
$sector1archercasualties = $selected2[sector2];
}
if($sector2archercasualties < 0){
$sector2archercasualties = 0;
}
if($sector2archercasualties > 0 && $selected2[sector2] < 1){
$sector2archercasualties = 0;
}

$total2sectordef += $sector2archercasualties;

if($total2sectordef < 0){
$total2sectordef = 0;
}


$sector3archers = round(($sector3archers - round($armormod3 * ($sector3archers/10)))/10);
if($sector3archers < 10 && $attinfo[sector3] > 0){
$sector3archers = round(($leadership[level] + $archery[level] + $morale[morale]) * 10);
}
if($sector3archers < 0){
$sector3archers = 0;
}
$work3 = $sector3archers;
while($work3 > 9){
$rand10 = rand(1,10);
$sector3archercasualties += $rand10;
$work3 -= 10;
}

if($sector3archercasualties > $selected3[sector3]){
$sector3archercasualties = $selected3[sector3];
}
if($sector3archercasualties > 0 && $selected3[sector3] < 1){
$sector3archercasualties = 0;
}
if($sector3archercasualties < 0){
$sector3archercasualties = 0;
}
if(!$sector3archercasualties){
$sector3archercasualties = 0;
}
$total3sectordef += $sector3archercasualties;

if($total3sectordef < 0){
$total3sectordef = 0;
}

echo "<TR>";
if($attinfo[sector1] > 0){
echo "<TD>Force: $attinfo[startsector1] from Garrison $attinfo[garid]</TD>";
}
else{
echo "<TD>$attinfo[garid] Routed</TD>";
}
if($attinfo[sector2] > 0){
echo "<TD>Force: $attinfo[startsector2] from Garrison $attinfo[garid]</TD>";
}
else{
echo "<TD>$attinfo[garid] Routed</TD>";
}
if($attinfo[sector3] > 0){
echo "<TD>Force: $attinfo[startsector3] from Garrison $attinfo[garid]</TD>";
}
else {
echo "<TD>$attinfo[garid] Routed</TD>";
}
echo "</TR><TR>";
if($selected1[sector1] > 0){
echo "<TD>Attacking $selected1[sector1] from Garrison $selected1[garid]</TD>";
}
else {
echo "<TD>Looting</TD>";
}
if($selected2[sector2] > 0){
echo "<TD>Attacking $selected2[sector2] from Garrison $selected2[garid]</TD>";
}
else{
echo "<TD>Looting</TD>";
}
if($selected3[sector3] > 0){
echo "<TD>Attacking $selected3[sector3] from Garrison $selected3[garid]</TD>";
}
else{
echo "<TD>Looting</TD>";
}
echo "</TR>";
echo "<TR><TD><FONT COLOR=RED>Casualties: $sector1archercasualties</FONT></TD><TD><FONT COLOR=RED>Casualties: $sector2archercasualties</FONT></TD><TD><FONT COLOR=RED>Casualties: $sector3archercasualties</FONT></TD></TR>";
echo "<TR><TD>";
if($attinfo[weapon1]){
echo "$attinfo[weapon1]<BR>";
}
if($attinfo[weapon2]){
echo "$attinfo[weapon2]<BR>";
}
if($attinfo[head_armor]){
echo "$attinfo[head_armor]<BR>";
}
if($attinfo[torso_armor]){
echo "$attinfo[torso_armor]<BR>";
}
if($attinfo[otorso_armor]){
echo "$attinfo[otorso_armor]<BR>";
}
if($attinfo[legs_armor]){
echo "$attinfo[legs_armor]<BR>";
}
if($attinfo[shield]){
echo "$attinfo[shield]<BR>";
}
if($attinfo[horse_armor]){
echo "$attinfo[horse_armor]<BR>";
}
echo "</TD><TD>";
if($attinfo[weapon1]){
echo "$attinfo[weapon1]<BR>";
}
if($attinfo[weapon2]){
echo "$attinfo[weapon2]<BR>";
}
if($attinfo[head_armor]){
echo "$attinfo[head_armor]<BR>";
}
if($attinfo[torso_armor]){
echo "$attinfo[torso_armor]<BR>";
}
if($attinfo[otorso_armor]){
echo "$attinfo[otorso_armor]<BR>";
}
if($attinfo[legs_armor]){
echo "$attinfo[legs_armor]<BR>";
}
if($attinfo[shield]){
echo "$attinfo[shield]<BR>";
}
if($attinfo[horse_armor]){
echo "$attinfo[horse_armor]<BR>";
}
echo "</TD><TD>";
if($attinfo[weapon1]){
echo "$attinfo[weapon1]<BR>";
}
if($attinfo[weapon2]){
echo "$attinfo[weapon2]<BR>";
}
if($attinfo[head_armor]){
echo "$attinfo[head_armor]<BR>";
}
if($attinfo[torso_armor]){
echo "$attinfo[torso_armor]<BR>";
}
if($attinfo[otorso_armor]){
echo "$attinfo[otorso_armor]<BR>";
}
if($attinfo[legs_armor]){
echo "$attinfo[legs_armor]<BR>";
}
if($attinfo[shield]){
echo "$attinfo[shield]<BR>";
}
if($attinfo[horse_armor]){
echo "$attinfo[horse_armor]<BR>";
}
echo "</TD></TR>";

$db->Execute("UPDATE $dbtables[combats] set sector1 = sector1 - '$sector1archercasualties', curforce = curforce - '$sector1archercasulaties' where garid = '$selected1[garid]' AND combat_id = '$uniqueid'");
$db->Execute("UPDATE $dbtables[combats] set sector2 = sector2 - '$sector2archercasualties', curforce = curforce - '$sector2archercasualties' where garid = '$selected2[garid]' AND combat_id = '$uniqueid'");
$db->Execute("UPDATE $dbtables[combats] set sector3 = sector3 - '$sector3archercasualties', curforce = curforce - '$sector3archercasualties' where garid = '$selected3[garid]' AND combat_id = '$uniqueid'");

$sector1archercasualties = 0;
$sector2archercasualties = 0;
$sector3archercasualties = 0;


$att->MoveNext();
}
echo "<TR><TD COLSPAN=3>&nbsp</TD></TR>";
echo "<TR><TD>Total Sector Kills: $total1sectordef</TD><TD>Total Sector Kills: $total2sectordef</TD><TD>Total Sector Kills: $total3sectordef</TD></TR>";
echo "</TD></TR></TABLE></TD></TR></TABLE></CENTER>";


//////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////Afterphase Cleanup//////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////

$morale = $db->Execute("SELECT * FROM $dbtables[combats] "
                      ."WHERE combat_id = '$uniqueid'");
$cleanuptitle = 0;
$line_color = $color_line1;
while(!$morale->EOF){
$moraleinfo = $morale->fields;
$skldr = $db->Execute("SELECT * FROM $dbtables[skills] WHERE tribeid = '$moraleinfo[tribeid]' AND abbr = 'ldr'");
$leadership = $skldr->fields;
$tribe = $db->Execute("SELECT * FROM $dbtables[tribes] WHERE tribeid = '$moraleinfo[tribeid]'");
$tribeinfo = $tribe->fields; 
if($moraleinfo[sector1] < 0){
$db->Execute("UPDATE $dbtables[combats] "
            ."set sector1 = 0 "
            ."WHERE tribeid = '$moraleinfo[tribeid]' "
            ."AND combat_id = '$uniqueid' "
            ."AND garid = '$moraleinfo[garid]'");
$moraleinfo[sector1] = 0;
}
if($moraleinfo[sector2] < 0){
$db->Execute("UPDATE $dbtables[combats] "
            ."set sector2 = 0 "
            ."WHERE tribeid = '$moraleinfo[tribeid]' "
            ."AND combat_id = '$uniqueid' "
            ."AND garid = '$moraleinfo[garid]'");
$moraleinfo[sector2] = 0;
}
if($moraleinfo[sector3] < 0){
$db->Execute("UPDATE $dbtables[combats] "
            ."set sector3 = 0 "
            ."WHERE tribeid = '$moraleinfo[tribeid]' "
            ."AND combat_id = '$uniqueid' "
            ."AND garid = '$moraleinfo[garid]'");
$moraleinfo[sector3] = 0;
}
//////////////////////////////sector1 morale checks///////////////////////////////////////////////////////
$percentwounded = round(1 - ($moraleinfo[sector1]/$moraleinfo[startsector1]), 2);
$survivors = round((1 - $percentwounded), 2);
$percentwounded = explode('.', $percentwounded);
$percentwounded = $percentwounded[1];
$survivors = explode('.', $survivors);
$survivors = $survivors[1];
if($survivors < 0 | !$survivors){
$survivors = 0;
}
if($percentwounded < 0 | !$percentwounded){
$percentwounded = 0;
}
$routechance = 100 - ($survivors + $leadership[level] + $tribeinfo[morale]);
if($routechance < 0){
$routechance = 0;
}
if($percentwounded == 0){
$routechance = 0;
}
if($routechance > 0){
$routerand = rand(1, 100);
if($routerand < $routechance){
if($cleanuptitle < 1){
echo "<CENTER><TABLE WIDTH=\"80%\"><TR><TD><TABLE WIDTH=\"100%\">";
echo "<TR BGCOLOR=$color_header><TD COLSPAN=4 ALIGN=CENTER><FONT SIZE=+2>Morale Checks</FONT></TD></TR>";
if($line_color == $color_line2){
$line_color = $color_line1;
}
elseif($line_color == $color_line1){
$line_color = $color_line2;
}
echo "<TR BGCOLOR=$line_color ALIGN=CENTER><TD>Garrison ID</TD><TD>Remaining Force</TD><TD>Sector</TD><TD>Status</TD></TR>";
$cleanuptitle++;
}
if($line_color == $color_line2){
$line_color = $color_line1;
}
elseif($line_color == $color_line1){
$line_color = $color_line2;
}
echo "<TR BGCOLOR=$line_color ALIGN=CENTER><TD>$moraleinfo[garid]</TD><TD>$moraleinfo[sector1]</TD><TD>sector 1</TD><TD>routed!</TD></TR>";
$db->Execute("UPDATE $dbtables[combats] "
            ."set curforce = curforce - sector1, "
            ."sector1 = 0 "
            ."where garid = '$moraleinfo[garid]' "
            ."AND combat_id = '$uniqueid'");
}
else{
if($cleanuptitle < 1){
echo "<CENTER><TABLE WIDTH=\"80%\"><TR><TD><TABLE WIDTH=\"100%\">";
echo "<TR BGCOLOR=$color_header><TD COLSPAN=4 ALIGN=CENTER><FONT SIZE=+2>Morale Checks</FONT></TD></TR>";
if($line_color == $color_line2){
$line_color = $color_line1;
}
elseif($line_color == $color_line1){
$line_color = $color_line2;
}
echo "<TR BGCOLOR=$line_color ALIGN=CENTER><TD>Garrison ID</TD><TD>Remaining Force</TD><TD>Sector</TD><TD>Status</TD></TR>";
$cleanuptitle++;
}
if($line_color == $color_line2){
$line_color = $color_line1;
}
elseif($line_color == $color_line1){
$line_color = $color_line2;
}
echo "<TR BGCOLOR=$line_color ALIGN=CENTER><TD>$moraleinfo[garid]</TD><TD>$moraleinfo[sector1]</TD><TD>sector 1</TD><TD>firm</TD></TR>";
}
}
///////////////////////////////sector2 morale checks/////////////////////////////////////////////////////
$percentwounded = round(1 - ($moraleinfo[sector2]/$moraleinfo[startsector2]), 2);
$survivors = round((1 - $percentwounded), 2);
$percentwounded = explode('.', $percentwounded);
$percentwounded = $percentwounded[1];
$survivors = explode('.', $survivors);
$survivors = $survivors[1];
if($survivors < 0 | !$survivors){ $survivors = 0; }
if($percentwounded < 0 | !$percentwounded){ $percentwounded = 0; }
$routechance = 100 - ($survivors + $leadership[level] + $tribeinfo[morale]);
if($routechance < 0){ $routechance = 0; }
if($percentwounded == 0){ $routechance = 0; }
if($routechance > 0){
$routerand = rand(1, 100);
if($routerand < $routechance){
if($cleanuptitle < 1){
echo "<CENTER><TABLE WIDTH=\"80%\"><TR><TD><TABLE WIDTH=\"100%\">";
echo "<TR BGCOLOR=$color_header ALIGN=CENTER><TD COLSPAN=4><FONT SIZE=+2>Morale Checks</FONT></TD></TR>";
if($line_color == $color_line2){ $line_color = $color_line1; }
elseif($line_color == $color_line1){ $line_color = $color_line2; }
echo "<TR BGCOLOR=$line_color ALIGN=CENTER><TD>Garrison ID</TD><TD>Remaining Force</TD><TD>Sector</TD><TD>Status</TD></TR>";
$cleanuptitle++;
}
if($line_color == $color_line2){ $line_color = $color_line1; }
elseif($line_color == $color_line1){ $line_color = $color_line2; }
echo "<TR BGCOLOR=$line_color ALIGN=CENTER><TD>$moraleinfo[garid]</TD><TD>$moraleinfo[sector2]</TD><TD>sector 2</TD><TD>routed!</TD></TR>";
$db->Execute("UPDATE $dbtables[combats] "
            ."set curforce = curforce - sector2, "
            ."sector2 = 0 "
            ."where garid = '$moraleinfo[garid]' "
            ."AND combat_id = '$uniqueid'");
}
else{
if($cleanuptitle < 1){
echo "<CENTER><TABLE WIDTH=\"80%\"><TR><TD><TABLE WIDTH=\"100%\">";
echo "<TR BGCOLOR=$color_header><TD COLSPAN=4 ALIGN=CENTER><FONT SIZE=+2>Morale Checks</FONT></TD></TR>";
if($line_color == $color_line2){ $line_color = $color_line1; }
elseif($line_color == $color_line1){ $line_color = $color_line2; }
echo "<TR BGCOLOR=$line_color ALIGN=CENTER><TD>Garrison ID</TD><TD>Remaining Force</TD><TD>Sector</TD><TD>Status</TD></TR>";
$cleanuptitle++;
}
if($line_color == $color_line2){ $line_color = $color_line1; }
elseif($line_color == $color_line1){ $line_color = $color_line2; }
echo "<TR BGCOLOR=$line_color ALIGN=CENTER><TD>$moraleinfo[garid]</TD><TD>$moraleinfo[sector2]</TD><TD>sector 2</TD><TD>firm</TD></TR>";
}
}
////////////////////////////////sector3 morale checks/////////////////////////////////////////////////////
$percentwounded = round(1 - ($moraleinfo[sector3]/$moraleinfo[startsector3]), 2);
$survivors = round((1 - $percentwounded), 2);
$percentwounded = explode('.', $percentwounded);
$percentwounded = $percentwounded[1];
$survivors = explode('.', $survivors);
$survivors = $survivors[1];
if($survivors < 0 | !$survivors){ $survivors = 0; }
if($percentwounded < 0 | !$percentwounded){ $percentwounded = 0; }
$routechance = 100 - ($survivors + $leadership[level] + $tribeinfo[morale]);
if($routechance < 0){ $routechance = 0; }
if($percentwounded == 0){ $routechance = 0; }
if($routechance > 0){
$routerand = rand(1, 100);
if($routerand < $routechance){
if($cleanuptitle < 1){
echo "<CENTER><TABLE WIDTH=\"80%\"><TR><TD><TABLE WIDTH=\"100%\">";
echo "<TR BGCOLOR=$color_header ALIGN=CENTER><TD COLSPAN=4><FONT SIZE=+2>Morale Checks</FONT></TD></TR>";
if($line_color == $color_line2){ $line_color = $color_line1; }
elseif($line_color == $color_line1){ $line_color = $color_line2; }
echo "<TR BGCOLOR=$line_color ALIGN=CENTER><TD>Garrison ID</TD><TD>Remaining Force</TD><TD>Sector</TD><TD>Status</TD></TR>";
$cleanuptitle++;
}
if($line_color == $color_line2){ $line_color = $color_line1; }
elseif($line_color == $color_line1){ $line_color = $color_line2; }
echo "<TR BGCOLOR=$line_color ALIGN=CENTER><TD>$moraleinfo[garid]</TD><TD>$moraleinfo[sector3]</TD><TD>sector 3</TD><TD>routed!</TD></TR>";
$db->Execute("UPDATE $dbtables[combats] "
            ."set curforce = curforce - sector3, "
            ."sector3 = 0 "
            ."where garid = '$moraleinfo[garid]' "
            ."AND combat_id = '$uniqueid'");
}
else{
if($cleanuptitle < 1){
echo "<CENTER><TABLE WIDTH=\"80%\"><TR><TD><TABLE WIDTH=\"100%\">";
echo "<TR BGCOLOR=$color_header><TD COLSPAN=4 ALIGN=CENTER><FONT SIZE=+2>Morale Checks</FONT></TD></TR>";
if($line_color == $color_line2){ $line_color = $color_line1; }
elseif($line_color == $color_line1){ $line_color = $color_line2; }
echo "<TR BGCOLOR=$line_color ALIGN=CENTER><TD>Garrison ID</TD><TD>Remaining Force</TD><TD>Sector</TD><TD>Status</TD></TR>";
$cleanuptitle++;
}
if($line_color == $color_line2){ $line_color = $color_line1; }
elseif($line_color == $color_line1){ $line_color = $color_line2; }
echo "<TR BGCOLOR=$line_color ALIGN=CENTER><TD>$moraleinfo[garid]</TD><TD>$moraleinfo[sector3]</TD><TD>sector 3</TD><TD>firm</TD></TR>";
}
}
$morale->MoveNext();
}
echo "</TABLE></TD></TR></TABLE>";
//////////////////////////////////Defender Calvalry Phase////////////////////////////////////////////////////
if(!$attacker->EOF && !$defender->EOF){
echo "<CENTER><TABLE WIDTH=\"80%\" BORDER=1><TR><TD><TABLE BORDER=0 WIDTH=\"100%\">";

$def = $db->Execute("SELECT * FROM $dbtables[combats] "
                   ."WHERE side = 'D' "
                   ."AND combat_id = '$uniqueid' "
                   ."AND trooptype = 'C'");

if($def->EOF){
echo "<TR><TD COLSPAN=3><CENTER><FONT SIZE=+2>Calvalry Phase (Defenders)</FONT></CENTER></TD></TR>";
echo "<TR ALIGN=CENTER BGCOLOR=$color_header><TD COLSPAN=3>No Calvalry Units</TD></TR>";
}

$titlecount = 0;
while(!$def->EOF){
$definfo = $def->fields;

if($titlecount < 1){
echo "<TR><TD COLSPAN=3><CENTER><FONT SIZE=+2>Calvalry Phase (Defenders)</FONT></CENTER></TD></TR>";
$titlecount++;
echo "<TR BGCOLOR=$color_header><TD ALIGN=CENTER><FONT SIZE=+1>Sector One</FONT></TD><TD ALIGN=CENTER><FONT SIZE=+1>Sector Two</FONT></TD><TD ALIGN=CENTER><FONT SIZE=+1>Sector Three</FONT></TD></TR>";
}



///////////Get the right variables//////////////////////////////////////////////////////////////////////
echo "<TR><TD COLSPAN=3>&nbsp;</TD></TR>";
$weap = $db->Execute("SELECT * FROM $dbtables[weapons] WHERE proper = '$definfo[weapon1]'");

if($definfo[weapon1] == 'Horsebow'){
$weap = $db->Execute("SELECT * FROM $dbtables[weapons] WHERE proper = '$definfo[weapon2]'");
}

$weapon = $weap->fields;


$skcom = $db->Execute("SELECT * FROM $dbtables[skills] WHERE tribeid = '$definfo[tribeid]' AND abbr = 'com'");
$combat = $skcom->fields;
$skhor = $db->Execute("SELECT * FROM $dbtables[skills] WHERE tribeid = '$definfo[tribeid]' AND abbr = 'hor'");
$horsemanship = $skhor->fields;
$skldr = $db->Execute("SELECT * FROM $dbtables[skills] WHERE tribeid = '$definfo[tribeid]' AND abbr = 'ldr'");
$leadership = $skldr->fields;
$mor = $db->Execute("SELECT * FROM $dbtables[tribes] WHERE tribeid = '$definfo[tribeid]'");
$morale = $mor->fields;
$eff = $db->Execute("SELECT * FROM $dbtables[combat_terrain_effect] WHERE type = 'defense'");
$terrain_effect = $eff->fields;
$ter = $db->Execute("SELECT * FROM $dbtables[hexes] WHERE hex_id = '$definfo[hex_id]'");
$terrain = $ter->fields;
$ter_mods = $db->Execute("SELECT * from $dbtables[combat_terrain_mods]");
$terrainmods = $ter_mods->fields;
$cw = $db->Execute("SELECT * FROM $dbtables[weather] WHERE current_type = 'Y'");
$cur_weath = $cw->fields;
$modify = $terrain[terrain];
$hownow = $cur_weath[weather_id];

if($definfo[sector1] < 0){
$definfo[sector1] = 0;
}
if($definfo[sector2] < 0){
$definfo[sector2] = 0;
}
if($definfo[sector3] < 0){
$definfo[sector3] = 0;
}



/////////////////Now, pick a target////////////////////////////////////////////////////////////////////

$tar1 = '';
$type1 = 0;
$tar1 = $db->Execute("SELECT count(*) as count FROM $dbtables[combats] "
                    ."WHERE side = 'A' "
                    ."AND trooptype = 'I' "
                    ."AND combat_id = '$uniqueid' "
                    ."AND sector1 > 0");
$target1 = $tar1->fields;
if($target1[count] < 1){
$type1 = 1;
$tar1 = $db->Execute("SELECT count(*) as count FROM $dbtables[combats] "
                    ."WHERE side = 'A' "
                    ."AND trooptype = 'C' "
                    ."AND combat_id = '$uniqueid' "
                    ."AND sector1 > 0");
$target1 = $tar1->fields;
}
if($target1[count] < 1){
$type1 = 2;
$tar1 = $db->Execute("SELECT count(*) as count FROM $dbtables[combats] "
                    ."WHERE side = 'A' "
                    ."AND combat_id = '$uniqueid' "
                    ."AND sector1 > 0");
$target1 = $tar1->fields;
}
$maxtarget1 = ($target1[count] - 1);
$target1 = rand(0, $maxtarget1);
if($type1 == '2'){
$tar1 = $db->Execute("SELECT * FROM $dbtables[combats] "
                    ."WHERE side = 'A' "
                    ."AND combat_id = '$uniqueid' "
                    ."AND sector1 > 0 "
                    ."LIMIT $target1, 1");
}
elseif($type1 == '1'){
$tar1 = $db->Execute("SELECT * FROM $dbtables[combats] "
                    ."WHERE side = 'A' "
                    ."AND trooptype = 'C' "
                    ."AND sector1 > 0 "
                    ."AND combat_id = '$uniqueid' "
                    ."LIMIT $target1, 1");
}
elseif($type1 == '0'){
$tar1 = $db->Execute("SELECT * FROM $dbtables[combats] "
                    ."WHERE side = 'A' "
                    ."AND trooptype = 'I' "
                    ."AND combat_id = '$uniqueid' "
                    ."AND curforce > 0 "
                    ."LIMIT $target1, 1");
}
$target1 = $tar1->fields;
////////////////////////////Sector2//////////////////////////////////////////////////////////
$tar2 = '';
$type2 = 0;
$tar2 = $db->Execute("SELECT count(*) as count FROM $dbtables[combats] "
                    ."WHERE side = 'A' "
                    ."AND combat_id = '$uniqueid' "
                    ."AND trooptype = 'I' "
                    ."AND sector2 > 0");
$target2 = $tar2->fields;
if($target2[count] < 1){
$type2 = 1;
$tar2 = $db->Execute("SELECT count(*) as count FROM $dbtables[combats] "
                    ."WHERE side = 'A' "
                    ."AND combat_id = '$uniqueid' "
                    ."AND trooptype = 'C' "
                    ."AND sector2 > 0");
$target2 = $tar2->fields;
}
if($target2[count] < 1){
$type2 = 2;
$tar2 = $db->Execute("SELECT count(*) as count FROM $dbtables[combats] "
                    ."WHERE side = 'A' "
                    ."AND combat_id = '$uniqueid' "
                    ."AND sector2 > 0");
$target2 = $tar2->fields;
}
$maxtarget2 = ($target2[count] - 1);
$target2 = rand(0, $maxtarget2);
if($type2 == '2'){
$tar2 = $db->Execute("SELECT * FROM $dbtables[combats] "
                    ."WHERE side = 'A' "
                    ."AND combat_id = '$uniqueid' "
                    ."AND sector2 > 0 " 
                    ."LIMIT $target2, 1");
}
elseif($type2 == '1'){
$tar2 = $db->Execute("SELECT * FROM $dbtables[combats] "
                    ."WHERE side = 'A' "
                    ."AND trooptype = 'C' "
                    ."AND combat_id = '$uniqueid' "
                    ."AND sector2 > 0 "
                    ."LIMIT $target2, 1");
}
elseif($type2 == '0'){
$tar2 = $db->Execute("SELECT * FROM $dbtables[combats] "
                    ."WHERE side = 'A' "
                    ."AND trooptype = 'I' "
                    ."AND combat_id = '$uniqueid' "
                    ."AND sector2 > 0 "
                    ."LIMIT $target2, 1");
}
$target2 = $tar2->fields;
////////////////////////////Sector3/////////////////////////////////////////////////////////
$tar3 = '';
$type3 = 0;
$tar3 = $db->Execute("SELECT count(*) as count FROM $dbtables[combats] "
                    ."WHERE side = 'A' "
                    ."AND combat_id = '$uniqueid' "
                    ."AND trooptype = 'I' "
                    ."AND sector3 > 0");
$target3 = $tar3->fields;
if($target3[count] < 1){
$type3 = 1;
$tar3 = $db->Execute("SELECT count(*) as count FROM $dbtables[combats] "
                    ."WHERE side = 'A' "
                    ."AND combat_id = '$uniqueid' "
                    ."AND trooptype = 'C' "
                    ."AND sector3 > 0");
$target3 = $tar3->fields;
}
if($target3[count] < 1){
$type3 = 2;
$tar3 = $db->Execute("SELECT count(*) as count FROM $dbtables[combats] "
                    ."WHERE side = 'A' "
                    ."AND combat_id = '$uniqueid' "
                    ."AND sector3 > 0");
$target3 = $tar3->fields;
}
$maxtarget3 = ($target3[count] - 1);
$target3 = rand(0, $maxtarget3);
if($type3 == '2'){
$tar3 = $db->Execute("SELECT * FROM $dbtables[combats] "
                    ."WHERE side = 'A' "
                    ."AND combat_id = '$uniqueid' "
                    ."AND sector3 > 0 "
                    ."LIMIT $target3, 1");
}
elseif($type3 == '1'){
$tar3 = $db->Execute("SELECT * FROM $dbtables[combats] "
                    ."WHERE side = 'A' "
                    ."AND combat_id = '$uniqueid' "
                    ."AND trooptype = 'C' "
                    ."AND sector3 > 0 "
                    ."LIMIT $target3, 1");
}
elseif($type3 == '0'){
$tar3 = $db->Execute("SELECT * FROM $dbtables[combats] "
                    ."WHERE side = 'A' "
                    ."AND combat_id = '$uniqueid' "
                    ."AND trooptype = 'I' "
                    ."AND sector3 > 0 "
                    ."LIMIT $target3, 1");
}
$target3 = $tar3->fields;


//////////////////////////////////////Get the target's armor information//////////////////////////////////
//////////////////////////////////Sector1////////////////////////////////////////////////////////////////
$head = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target1[head_armor]'");
$head_armor1 = $head->fields;
$torso = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target1[torso_armor]'");
$torso_armor1 = $torso->fields;
$otorso = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target1[otorso_armor]'");
$otorso_armor1 = $otorso->fields;
$legs = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target1[legs_armor]'");
$legs_armor1 = $legs->fields;
$shield = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target1[shield]'");
$shield_armor1 = $shield->fields;
$horse = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target1[horse_armor]'");
$horse_armor1 = $horse->fields;

$weapon_type = explode(' ', $definfo[weapon1]);
$weapontype = $weapon_type[0];
if($weapontype == ''){
$weapontype = 'stone';
}
$armormod1 = $head_armor1[$weapontype] + $torso_armor1[$weapontype] + $otorso_armor1[$weapontype] + $legs_armor1[$weapontype] + $shield_armor1[$weapontype] + $horse_armor1[$weapontype];
///////////////////////////////////Sector2//////////////////////////////////////////////////////////////////
$head = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target2[head_armor]'");
$head_armor2 = $head->fields;
$torso = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target2[torso_armor]'");
$torso_armor2 = $torso->fields;
$otorso = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target2[otorso_armor]'");
$otorso_armor2 = $otorso->fields;
$legs = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target2[legs_armor]'");
$legs_armor2 = $legs->fields;
$shield = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target2[shield]'");
$shield_armor2 = $shield->fields;
$horse = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target2[horse_armor]'");
$horse_armor2 = $horse->fields;

$weapon_type = explode(' ', $definfo[weapon1]);
$weapontype = $weapon_type[0];
if($weapontype == ''){
$weapontype = 'stone';
}
$armormod2 = $head_armor2[$weapontype] + $torso_armor2[$weapontype] + $otorso_armor2[$weapontype] + $legs_armor2[$weapontype] + $shield_armor2[$weapontype] + $horse_armor2[$weapontype];
/////////////////////////////////////Sector3////////////////////////////////////////////////////////////////
$head = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target3[head_armor]'");
$head_armor3 = $head->fields;
$torso = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target3[torso_armor]'");
$torso_armor3 = $torso->fields;
$otorso = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target3[otorso_armor]'");
$otorso_armor3 = $otorso->fields;
$legs = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target3[legs_armor]'");
$legs_armor3 = $legs->fields;
$shield = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target3[shield]'");
$shield_armor3 = $shield->fields;
$horse = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target3[horse_armor]'");
$horse_armor3 = $horse->fields;

$weapon_type = explode(' ', $definfo[weapon1]);
$weapontype = $weapon_type[0];
if($weapontype == ''){
$weapontype = 'stone';
}
$armormod3 = $head_armor3[$weapontype] + $torso_armor3[$weapontype] + $otorso_armor3[$weapontype] + $legs_armor3[$weapontype] + $shield_armor3[$weapontype] + $horse_armor3[$weapontype];
//////////////////////////////////////////////////////////////////////////////////////////////////////////

if($target1[trooptype] == 'I'){
$weaponeffect1 = $weapon[cav_inf];
}
elseif($target1[trooptype] == 'C'){
$weaponeffect1 = $weapon[cav_cav];
}
else {
$weaponeffect1 = $weapon[cav_arc];
}
if($target2[trooptype] == 'I'){
$weaponeffect2 = $weapon[cav_inf];
}
elseif($target2[trooptype] == 'C'){
$weaponeffect2 = $weapon[cav_cav];
}
else {
$weaponeffect2 = $weapon[cav_arc];
}
if($target3[trooptype] == 'I'){
$weaponeffect3 = $weapon[cav_inf];
}
elseif($target3[trooptype] == 'C'){
$weaponeffect3 = $weapon[cav_cav];
}
else {
$weaponeffect3 = $weapon[cav_arc];
}

if($target1[sector1] > 0 && $definfo[sector1] > 0){
$random6 = rand(1,8);
$chargeattack1 = round(($weaponeffect1 * $weather[$hownow] * $terrain_effect[$modify] * $morale[morale] * ($leadership[level] + ($horsemanship[level] + $combat[level])/2) + 6 + $random6)/7 * $definfo[sector1]);
}
if($target2[sector2] > 0 && $definfo[sector2] > 0){
$random6 = rand(1,8);
$chargeattack2 = round(($weaponeffect2 * $weather[$hownow] * $terrain_effect[$modify] * $morale[morale] * ($leadership[level] + ($horsemanship[level] + $combat[level])/2) + 6 + $random6)/7 * $definfo[sector2]);
}
if($target3[sector3] > 0 && $definfo[sector3] > 0){
$random6 = rand(1,8);
$chargeattack3 = round(($weaponeffect3 * $weather[$hownow] * $terrain_effect[$modify] * $morale[morale] * ($leadership[level] + ($horsemanship[level] + $combat[level])/2) + 6 + $random6)/7 * $definfo[sector3]);
}
echo "<TR><TD>Force: $definfo[sector1] from Garrison $definfo[garid]</TD><TD>Force: $definfo[sector2] from Garrison $definfo[garid]</TD><TD>Force: $definfo[sector3] from Garrison $definfo[garid]</TD></TR>";
if($target1[sector1] > 0){
echo "<TR><TD>Attacking: $target1[sector1] from Garrison $target1[garid]</TD>";
}
else{
echo "<TR><TD>Looting</TD>";
}
if($target2[sector2] > 0){
echo "<TD>Attacking: $target2[sector2] from Garrison $target2[garid]</TD>";
}
else{
echo "<TD>Looting</TD>";
}
if($target3[sector3] > 0){
echo "<TD>Attacking: $target3[sector3] from Garrison $target3[garid]</TD></TR>";
}
else{
echo "<TD>Looting</TD></TR>";
}
if($target1[sector1] > 0){
$work1 = $chargeattack1 - round($armormod1 * ($chargeattack1/10));
while($work1 > 10){
$random10 = rand(1,10);
$actual1 += $random10;
$work1 -= 10;
}
$actual1 = round($actual1/10);
if($target1[sector1] <= 0 | $definfo[sector1] <= 0){
$actual1 = 0;
}
$total1sectoratt += $actual1;
}
if($target2[sector2] > 0){
$work2 = $chargeattack2 - round($armormod2 * ($chargeattack2/10));
while($work2 > 10){
$random10 = rand(1,10);
$actual2 += $random10;
$work2 -= 10;
}
$actual2 = round($actual2/10);
if($target2[sector2] <= 0 | $definfo[sector2] <= 0 ){
$actual2 = 0;
}
$total2sectoratt += $actual2;
}
if($target3[sector3] > 0){
$work3 = $chargeattack3 - round($armormod3 * ($chargeattack3/10));
while($work3 > 10){
$random10 = rand(1,10);
$actual3 += $random10;
$work3 -= 10;
}
$actual3 = round($actual3/10);
if($target3[sector3] <= 0 | $definfo[sector3] <= 0){
$actual3 = 0;
}
$total3sectoratt += $actual3;
}
if(!$actual1 | $definfo[sector1] < 1){
$actual1 = 0;
}
if(!$actual2 | $definfo[sector2] < 1){
$actual2 = 0;
}
if(!$actual3 | $definfo[sector3] < 1){
$actual3 = 0;
}
echo "<TR><TD><FONT COLOR=RED>Casualties: $actual1</FONT></TD><TD><FONT COLOR=RED>Casualties: $actual2</FONT></TD><TD><FONT COLOR=RED>Casualties: $actual3</FONT></TD></TR>";

$db->Execute("UPDATE $dbtables[combats] "
            ."set sector1 = sector1 - '$actual1', "
            ."curforce = curforce - '$actual1' "
            ."WHERE garid = '$target1[garid]' "
            ."AND combat_id = '$uniqueid'");
$db->Execute("UPDATE $dbtables[combats] "
            ."set sector2 = sector2 - '$actual2', "
            ."curforce = curforce - '$actual2' "
            ."WHERE garid = '$target2[garid]' "
            ."AND combat_id = '$uniqueid'");
$db->Execute("UPDATE $dbtables[combats] "
            ."set sector3 = sector3 - '$actual3', "
            ."curforce = curforce - '$actual3' "
            ."WHERE garid = '$target3[garid]' " 
            ."AND combat_id = '$uniqueid'");


$def->MoveNext();
}
echo "<TR><TD>Total Sector Kills: $total1sectoratt</TD><TD>Total Sector Kills: $total2sectoratt</TD><TD>Total Sector Kills: $total3sectoratt</TD></TR>";
echo "</TABLE></TD></TR></TABLE></CENTER>";




///////////////////////////////////////////Attacker's Calvalry Phase/////////////////////////////////////////////



echo "<CENTER><TABLE WIDTH=\"80%\" BORDER=1><TR><TD><TABLE BORDER=0 WIDTH=\"100%\">";

$att = $db->Execute("SELECT * FROM $dbtables[combats] "
                   ."WHERE side = 'A' "
                   ."AND trooptype = 'C' "
                   ."AND combat_id = '$uniqueid'");
$titlecount = 0;
if($att->EOF){
echo "<TR><TD COLSPAN=3><CENTER><FONT SIZE=+2>Calvalry Phase (Attackers)</FONT></CENTER></TD></TR>";
echo "<TR BGCOLOR=$color_header><TD COLSPAN=3><CENTER>No Calvalry Units</CENTER></TD></TR>";
}

while(!$att->EOF){
$attinfo = $att->fields;

if($titlecount < 1){
echo "<TR><TD COLSPAN=3><CENTER><FONT SIZE=+2>Calvalry Phase (Attackers)</FONT></CENTER></TD></TR>";
$titlecount++;
echo "<TR BGCOLOR=$color_header><TD ALIGN=CENTER><FONT SIZE=+1>Sector One</FONT></TD><TD ALIGN=CENTER><FONT SIZE=+1>Sector Two</FONT></TD><TD ALIGN=CENTER><FONT SIZE=+1>Sector Three</FONT></TD></TR>";
}


if($attinfo[sector1] < 0){
$attinfo[sector1] = 0;
}
if($attinfo[sector2] < 0){
$attinfo[sector2] = 0;
}
if($attinfo[sector3] < 0){
$attinfo[sector3] = 0;
}




///////////Get the right variables//////////////////////////////////////////////////////////////////////
echo "<TR><TD COLSPAN=3>&nbsp;</TD></TR>";
$weap = $db->Execute("SELECT * FROM $dbtables[weapons] WHERE proper = '$attinfo[weapon1]'");

if($attinfo[weapon1] == 'Horsebow'){
$weap = $db->Execute("SELECT * FROM $dbtables[weapons] WHERE proper = '$attinfo[weapon2]'");
}

$weapon = $weap->fields;


$skcom = $db->Execute("SELECT * FROM $dbtables[skills] WHERE tribeid = '$attinfo[tribeid]' AND abbr = 'com'");
$combat = $skcom->fields;
$skhor = $db->Execute("SELECT * FROM $dbtables[skills] WHERE tribeid = '$attinfo[tribeid]' AND abbr = 'hor'");
$horsemanship = $skhor->fields;
$skldr = $db->Execute("SELECT * FROM $dbtables[skills] WHERE tribeid = '$attinfo[tribeid]' AND abbr = 'ldr'");
$leadership = $skldr->fields;
$mor = $db->Execute("SELECT * FROM $dbtables[tribes] WHERE tribeid = '$attinfo[tribeid]'");
$morale = $mor->fields;
$eff = $db->Execute("SELECT * FROM $dbtables[combat_terrain_effect] WHERE type = 'attack'");
$terrain_effect = $eff->fields;
$ter = $db->Execute("SELECT * FROM $dbtables[hexes] WHERE hex_id = '$attinfo[hex_id]'");
$terrain = $ter->fields;
$ter_mods = $db->Execute("SELECT * from $dbtables[combat_terrain_mods]");
$terrainmods = $ter_mods->fields;
$cw = $db->Execute("SELECT * FROM $dbtables[weather] WHERE current_type = 'Y'");
$cur_weath = $cw->fields;
$modify = $terrain[terrain];
$hownow = $cur_weath[weather_id];

/////////////////Now, pick a target////////////////////////////////////////////////////////////////////
///////////////////////////////////Sector1////////////////////////////////////////////////////////////
$tar1 = '';
$type1 = 0;
$tar1 = $db->Execute("SELECT count(*) as count FROM $dbtables[combats] "
                    ."WHERE side = 'D' "
                    ."AND combat_id = '$uniqueid' "
                    ."AND trooptype = 'I' "
                    ."AND sector1 > 0");
$target1 = $tar1->fields;
if($target1[count] < 1){
$type1 = 1;
$tar1 = $db->Execute("SELECT count(*) as count FROM $dbtables[combats] "
                    ."WHERE side = 'D' "
                    ."AND combat_id = '$uniqueid' "
                    ."AND trooptype = 'C' "
                    ."AND sector1 > 0");
$target1 = $tar1->fields;
}
if($target1[count] < 1){
$type1 = 2;
$tar1 = $db->Execute("SELECT count(*) as count FROM $dbtables[combats] "
                    ."WHERE side = 'D' "
                    ."AND combat_id = '$uniqueid' "
                    ."AND sector1 > 0");
$target1 = $tar1->fields;
}
$maxtarget1 = ($target1[count] - 1);
$target1 = rand(0, $maxtarget1);
if($type1 == '2'){
$tar1 = $db->Execute("SELECT * FROM $dbtables[combats] "
                    ."WHERE side = 'D' "
                    ."AND combat_id = '$uniqueid' "
                    ."AND sector1 > 0 "
                    ."LIMIT $target1, 1");
}
elseif($type1 == '1'){
$tar1 = $db->Execute("SELECT * FROM $dbtables[combats] "
                    ."WHERE side = 'D' "
                    ."AND trooptype = 'C' "
                    ."AND combat_id = '$uniqueid' "
                    ."AND sector1 > 0 "
                    ."LIMIT $target1, 1");
}
elseif($type1 == '0'){
$tar1 = $db->Execute("SELECT * FROM $dbtables[combats] "
                    ."WHERE side = 'D' "
                    ."AND combat_id = '$uniqueid' "
                    ."AND trooptype = 'I' "
                    ."AND sector1 > 0 "
                    ."LIMIT $target1, 1");
}
$target1 = $tar1->fields;
////////////////////////////////////Sector2/////////////////////////////////////////////////////////////
$tar2 = '';
$type2 = 0;
$tar2 = $db->Execute("SELECT count(*) as count FROM $dbtables[combats] "
                    ."WHERE side = 'D' "
                    ."AND combat_id = '$uniqueid' "
                    ."AND trooptype = 'I' "
                    ."AND sector2 > 0");
$target2 = $tar2->fields;
if($target2[count] < 1){
$type2 = 1;
$tar2 = $db->Execute("SELECT count(*) as count FROM $dbtables[combats] "
                    ."WHERE side = 'D' "
                    ."AND combat_id = '$uniqueid' "
                    ."AND trooptype = 'C' "
                    ."AND sector2 > 0");
$target2 = $tar2->fields;
}
if($target2[count] < 1){
$type2 = 2;
$tar2 = $db->Execute("SELECT count(*) as count FROM $dbtables[combats] "
                    ."WHERE side = 'D' "
                    ."AND combat_id = '$uniqueid' "
                    ."AND sector2 > 0");
$target2 = $tar2->fields;
}
$maxtarget2 = ($target2[count] - 1);
$target2 = rand(0, $maxtarget2);
if($type2 == '2'){
$tar2 = $db->Execute("SELECT * FROM $dbtables[combats] "
                    ."WHERE side = 'D' "
                    ."AND combat_id = '$uniqueid' "
                    ."AND sector2 > 0 "
                    ."LIMIT $target2, 1");
}
elseif($type2 == '1'){
$tar2 = $db->Execute("SELECT * FROM $dbtables[combats] "
                    ."WHERE side = 'D' "
                    ."AND trooptype = 'C' "
                    ."AND combat_id = '$uniqueid' "
                    ."AND sector2 > 0 "
                    ."LIMIT $target2, 1");
}
elseif($type2 == '0'){
$tar2 = $db->Execute("SELECT * FROM $dbtables[combats] "
                    ."WHERE side = 'D' "
                    ."AND combat_id = '$uniqueid' "
                    ."AND trooptype = 'I' "
                    ."AND sector2 > 0 "
                    ."LIMIT $target2, 1");
}
$target2 = $tar2->fields;


////////////////////////////////////Sector3/////////////////////////////////////////////////////////////

$tar3 = '';
$type3 = 0;
$tar3 = $db->Execute("SELECT count(*) as count FROM $dbtables[combats] "
                    ."WHERE side = 'D' "
                    ."AND combat_id = '$uniqueid' "
                    ."AND trooptype = 'I' "
                    ."AND sector3 > 0");
$target3 = $tar3->fields;
if($target3[count] < 1){
$type3 = 1;
$tar3 = $db->Execute("SELECT count(*) as count FROM $dbtables[combats] "
                    ."WHERE side = 'D' "
                    ."AND combat_id = '$uniqueid' "
                    ."AND trooptype = 'C' "
                    ."AND sector3 > 0");
$target3 = $tar3->fields;
}
if($target3[count] < 1){
$type3 = 2;
$tar3 = $db->Execute("SELECT count(*) as count FROM $dbtables[combats] "
                    ."WHERE side = 'D' "
                    ."AND combat_id = '$uniqueid' "
                    ."AND sector3 > 0");
$target3 = $tar3->fields;
}
$maxtarget3 = ($target3[count] - 1);
$target3 = rand(0, $maxtarget3);
if($type3 == '2'){
$tar3 = $db->Execute("SELECT * FROM $dbtables[combats] "
                    ."WHERE side = 'D' "
                    ."AND sector3 > 0 "
                    ."AND combat_id = '$uniqueid' "
                    ."LIMIT $target3, 1");
}
elseif($type3 == '1'){
$tar3 = $db->Execute("SELECT * FROM $dbtables[combats] "
                    ."WHERE side = 'D' "
                    ."AND combat_id = '$uniqueid' "
                    ."AND trooptype = 'C' "
                    ."AND sector3 > 0 "
                    ."LIMIT $target3, 1");
}
elseif($type3 == '0'){
$tar3 = $db->Execute("SELECT * FROM $dbtables[combats] "
                    ."WHERE side = 'D' "
                    ."AND combat_id = '$uniqueid' "
                    ."AND trooptype = 'I' "
                    ."AND sector3 > 0 "
                    ."LIMIT $target3, 1");
}
$target3 = $tar3->fields;

//////////////////////////////////////Get the target's armor information//////////////////////////////////
////////////////////////////////////////Sector1///////////////////////////////////////////////////////////
$head = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target1[head_armor]'");
$head_armor = $head->fields;
$torso = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target1[torso_armor]'");
$torso_armor = $torso->fields;
$otorso = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target1[otorso_armor]'");
$otorso_armor = $otorso->fields;
$legs = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target1[legs_armor]'");
$legs_armor = $legs->fields;
$shield = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target1[shield]'");
$shield_armor = $shield->fields;
$horse = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target1[horse_armor]'");
$horse_armor = $horse->fields;
$weapon_type = explode(' ', $attinfo[weapon1]);
$weapontype = $weapon_type[0];
if($weapontype == ''){
$weapontype = 'stone';
}
$armormod1 = $head_armor[$weapontype] + $torso_armor[$weapontype] + $otorso_armor[$weapontype] + $legs_armor[$weapontype] + $shield_armor[$weapontype] + $horse_armor[$weapontype];
/////////////////////////////////////////Sector2////////////////////////////////////////////////////////////
$head = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target2[head_armor]'");
$head_armor = $head->fields;
$torso = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target2[torso_armor]'");
$torso_armor = $torso->fields;
$otorso = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target2[otorso_armor]'");
$otorso_armor = $otorso->fields;
$legs = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target2[legs_armor]'");
$legs_armor = $legs->fields;
$shield = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target2[shield]'");
$shield_armor = $shield->fields;
$horse = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target2[horse_armor]'");
$horse_armor = $horse->fields;
$weapon_type = explode(' ', $attinfo[weapon1]);
$weapontype = $weapon_type[0];
if($weapontype == ''){
$weapontype = 'stone';
}
$armormod2 = $head_armor[$weapontype] + $torso_armor[$weapontype] + $otorso_armor[$weapontype] + $legs_armor[$weapontype] + $shield_armor[$weapontype] + $horse_armor[$weapontype];
/////////////////////////////////////////Sector3////////////////////////////////////////////////////////////
$head = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target3[head_armor]'");
$head_armor = $head->fields;
$torso = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target3[torso_armor]'");
$torso_armor = $torso->fields;
$otorso = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target3[otorso_armor]'");
$otorso_armor = $otorso->fields;
$legs = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target3[legs_armor]'");
$legs_armor = $legs->fields;
$shield = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target3[shield]'");
$shield_armor = $shield->fields;
$horse = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target3[horse_armor]'");
$horse_armor = $horse->fields;
$weapon_type = explode(' ', $attinfo[weapon1]);
$weapontype = $weapon_type[0];
if($weapontype == ''){
$weapontype = 'stone';
}
$armormod3 = $head_armor[$weapontype] + $torso_armor[$weapontype] + $otorso_armor[$weapontype] + $legs_armor[$weapontype] + $shield_armor[$weapontype] + $horse_armor[$weapontype];
//////////////////////////////////////////////////////////////////////////////////////////////////////////
if($target1[trooptype] == 'I'){
$weaponeffect1 = $weapon[cav_inf];
}
elseif($target1[trooptype] == 'C'){
$weaponeffect1 = $weapon[cav_cav];
}
else {
$weaponeffect1 = $weapon[cav_arc];
}
if($target2[trooptype] == 'I'){
$weaponeffect2 = $weapon[cav_inf];
}
elseif($target2[trooptype] == 'C'){
$weaponeffect2 = $weapon[cav_cav];
}
else {
$weaponeffect2 = $weapon[cav_arc];
}
if($target3[trooptype] == 'I'){
$weaponeffect3 = $weapon[cav_inf];
}
elseif($target3[trooptype] == 'C'){
$weaponeffect3 = $weapon[cav_cav];
}
else {
$weaponeffect3 = $weapon[cav_arc];
}

if($target1[sector1] > 0 && $attinfo[sector1] > 0){
$random6 = rand(1,8);
$chargeattack1 = round(($weaponeffect1 * $weather[$hownow] * $terrain_effect[$modify] * $morale[morale] * ($leadership[level] + ($horsemanship[level] + $combat[level])/2) + 6 + $random6)/7 * $attinfo[sector1]);
}
if($target2[sector2] > 0 && $attinfo[sector2] > 0){
$random6 = rand(1,8);
$chargeattack2 = round(($weaponeffect2 * $weather[$hownow] * $terrain_effect[$modify] * $morale[morale] * ($leadership[level] + ($horsemanship[level] + $combat[level])/2) + 6 + $random6)/7 * $attinfo[sector2]);
}
if($target3[sector3] > 0 && $attinfo[sector3] > 0){
$random6 = rand(1,8);
$chargeattack3 = round(($weaponeffect3 * $weather[$hownow] * $terrain_effect[$modify] * $morale[morale] * ($leadership[level] + ($horsemanship[level] + $combat[level])/2) + 6 + $random6)/7 * $attinfo[sector3]);
}
echo "<TR><TD>Force: $attinfo[sector1] from Garrison $attinfo[garid]</TD><TD>Force: $attinfo[sector2] from Garrison $attinfo[garid]</TD><TD>Force: $attinfo[sector3] from Garrison $attinfo[garid]</TD></TR>";
if($target1[sector1] > 0){
echo "<TR><TD>Attacking: $target1[sector1] from Garrison $target1[garid]</TD>";
}
else{
echo "<TR><TD>Looting</TD>";
}
if($target2[sector2] > 0){
echo "<TD>Attacking: $target2[sector2] from Garrison $target2[garid]</TD>";
}
else{
echo "<TD>Looting</TD>";
}
if($target3[sector3] > 0){
echo "<TD>Attacking: $target3[sector3] from Garrison $target3[garid]</TD></TR>";
}
else{
echo "<TD>Looting</TD></TR>";
}
if($target1[sector1] > 0){
$work1 = $chargeattack1 - round($armormod1 * ($chargeattack1/10));
while($work1 > 9){
$random10 = rand(1,10);
$actual1 += $random10;
$work1 -= 10;
}
$actual1 = round($actual1/10);
if($attinfo[sector1] <= 0 | $target1[sector1] <= 0){
$actual1 = 0;
}
$total1sectordef += $actual1;
}
if($target2[sector2] > 0){
$work2 = $chargeattack2 - round($armormod2 * ($chargeattack2/10));
while($work2 > 9){
$random10 = rand(1,10);
$actual2 += $random10;
$work2 -= 10;
}
$actual2 = round($actual2/10);
if($attinfo[sector2] <= 0 | $target2[sector2] <= 0){
$actual2 = 0;
}
$total2sectordef += $actual2;
}
if($target3[sector3] > 0){
$work3 = $chargeattack3 - round($armormod3 * ($chargeattack3/10));
while($work3 > 9){

$random10 = rand(1,10);
$actual3 += $random10;
$work3 -= 10;
}
$actual3 = round($actual3/10);
if($attinfo[sector3] <= 0 | $target3[sector3] <= 0){
$actual3 = 0;
}
$total3sectordef += $actual3;
}
if(!$actual1 | $attinfo[sector1] < 1 ){
$actual1 = 0;
}
if(!$actual2 | $attinfo[sector2] < 1){
$actual2 = 0;
}
if(!$actual3 | $attinfo[sector3] < 1){
$actual3 = 0;
}
echo "<TR><TD><FONT COLOR=RED>Casualties: $actual1</FONT></TD><TD><FONT COLOR=RED>Casualties: $actual2</FONT></TD><TD><FONT COLOR=RED>Casualties: $actual3</FONT></TD></TR>";


$db->Execute("UPDATE $dbtables[combats] "
            ."set sector1 = sector1 - '$actual1', "
            ."curforce = curforce - '$actual1' "
            ."WHERE garid = '$target1[garid]' "
            ."AND combat_id = '$uniqueid'");
$db->Execute("UPDATE $dbtables[combats] "
            ."set sector2 = sector2 - '$actual2', "
            ."curforce = curforce - '$actual2' "
            ."WHERE garid = '$target2[garid]' "
            ."AND combat_id = '$uniqueid'");
$db->Execute("UPDATE $dbtables[combats] "
            ."set sector3 = sector3 - '$actual3', "
            ."curforce = curforce - '$actual3' "
            ."WHERE garid = '$target3[garid]' "
            ."AND combat_id = '$uniqueid'");



$att->MoveNext();
}
echo "<TR><TD>Total Sector Kills: $total1sectordef</TD><TD>Total Sector Kills: $total2sectordef</TD><TD>Total Sector Kills: $total3sectordef</TD></TR>";
echo "</TABLE></TD></TR></TABLE></CENTER>";



////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////Calvalry Cleanup///////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////


$morale = $db->Execute("SELECT * FROM $dbtables[combats] "
                      ."WHERE combat_id = '$uniqueid'");
$cleanuptitle = 0;
$line_color = $color_line1;
while(!$morale->EOF){
$moraleinfo = $morale->fields;
$skldr = $db->Execute("SELECT * FROM $dbtables[skills] WHERE tribeid = '$moraleinfo[tribeid]' AND abbr = 'ldr'");
$leadership = $skldr->fields;
$tribe = $db->Execute("SELECT * FROM $dbtables[tribes] WHERE tribeid = '$moraleinfo[tribeid]'");
$tribeinfo = $tribe->fields;
if($moraleinfo[sector1] < 0){
$db->Execute("UPDATE $dbtables[combats] "
            ."set sector1 = 0 "
            ."WHERE tribeid = '$moraleinfo[tribeid]' "
            ."AND garid = '$moraleinfo[garid]' "
            ."AND combat_id = '$uniqueid'");
$moraleinfo[sector1] = 0;
}
if($moraleinfo[sector2] < 0){
$db->Execute("UPDATE $dbtables[combats] "
            ."set sector2 = 0 "
            ."WHERE tribeid = '$moraleinfo[tribeid]' "
            ."AND combat_id = '$uniqueid' "
            ."AND garid = '$moraleinfo[garid]'");
$moraleinfo[sector2] = 0;
}
if($moraleinfo[sector3] < 0){
$db->Execute("UPDATE $dbtables[combats] "
            ."set sector3 = 0 "
            ."WHERE tribeid = '$moraleinfo[tribeid]' "
            ."AND combat_id = '$uniqueid' "
            ."AND garid = '$moraleinfo[garid]'");
$moraleinfo[sector3] = 0;
}
//////////////////////////////sector1 cleanup///////////////////////////////////////////////////////
$percentwounded = round(1 - ($moraleinfo[sector1]/$moraleinfo[startsector1]), 2);
$survivors = round((1 - $percentwounded), 2);
$percentwounded = explode('.', $percentwounded);
$percentwounded = $percentwounded[1];
$survivors = explode('.', $survivors);
$survivors = $survivors[1];
if($survivors < 0 | !$survivors){
$survivors = 0;
}
if($percentwounded < 0 | !$percentwounded){
$percentwounded = 0;
}
$routechance = 100 - ($survivors + $leadership[level] + $tribeinfo[morale]);
if($percentwounded == 0){
$routechance = 0;
}
if($routechance > 0){
$routerand = rand(1, 100);
if($routerand < $routechance){
if($cleanuptitle < 1){
echo "<CENTER><TABLE WIDTH=\"80%\"><TR><TD><TABLE WIDTH=\"100%\">";
echo "<TR BGCOLOR=$color_header><TD COLSPAN=4 ALIGN=CENTER><FONT SIZE=+2>Morale Checks</FONT></TD></TR>";
if($line_color == $color_line2){
$line_color = $color_line1;
}
elseif($line_color == $color_line1){
$line_color = $color_line2;
}
echo "<TR BGCOLOR=$line_color ALIGN=CENTER><TD>Garrison ID</TD><TD>Remaining Force</TD><TD>Sector</TD><TD>Status</TD></TR>";
$cleanuptitle++;
}
if($line_color == $color_line2){
$line_color = $color_line1;
}
elseif($line_color == $color_line1){
$line_color = $color_line2;
}
echo "<TR BGCOLOR=$line_color ALIGN=CENTER><TD>$moraleinfo[garid]</TD><TD>$moraleinfo[sector1]</TD><TD>sector 1</TD><TD>routed!</TD></TR>";
$db->Execute("UPDATE $dbtables[combats] "
            ."set curforce = curforce - sector1, "
            ."sector1 = 0 "
            ."where garid = '$moraleinfo[garid]' "
            ."AND combat_id = '$uniqueid'");
}
else{
if($cleanuptitle < 1){
echo "<CENTER><TABLE WIDTH=\"80%\"><TR><TD><TABLE WIDTH=\"100%\">";
echo "<TR BGCOLOR=$color_header><TD COLSPAN=4 ALIGN=CENTER><FONT SIZE=+2>Morale Checks</FONT></TD></TR>";
if($line_color == $color_line2){
$line_color = $color_line1;
}
elseif($line_color == $color_line1){
$line_color = $color_line2;
}
echo "<TR BGCOLOR=$line_color ALIGN=CENTER><TD>Garrison ID</TD><TD>Remaining Force</TD><TD>Sector</TD><TD>Status</TD></TR>";
$cleanuptitle++;
}
if($line_color == $color_line2){
$line_color = $color_line1;
}
elseif($line_color == $color_line1){
$line_color = $color_line2;
}
echo "<TR BGCOLOR=$line_color ALIGN=CENTER><TD>$moraleinfo[garid]</TD><TD>$moraleinfo[sector1]</TD><TD>sector 1</TD><TD>firm</TD></TR>";
}
}



/////////////////////////////////////Sector 2 Cleanup////////////////////////////////////////////////////


$percentwounded = round(1 - ($moraleinfo[sector2]/$moraleinfo[startsector2]), 2);
$survivors = round((1 - $percentwounded), 2);
$percentwounded = explode('.', $percentwounded);
$percentwounded = $percentwounded[1];
$survivors = explode('.', $survivors);
$survivors = $survivors[1];
if($survivors < 0 | !$survivors){ $survivors = 0; }
if($percentwounded < 0 | !$percentwounded){ $percentwounded = 0; }
$routechance = 100 - ($survivors + $leadership[level] + $tribeinfo[morale]);
if($routechance < 0){ $routechance = 0; }
if($percentwounded == 0){ $routechance = 0; }
if($routechance > 0){
$routerand = rand(1, 100);
if($routerand < $routechance){
if($cleanuptitle < 1){
echo "<CENTER><TABLE WIDTH=\"80%\"><TR><TD><TABLE WIDTH=\"100%\">";
echo "<TR BGCOLOR=$color_header ALIGN=CENTER><TD COLSPAN=4><FONT SIZE=+2>Morale Checks</FONT></TD></TR>";
if($line_color == $color_line2){ $line_color = $color_line1; }
elseif($line_color == $color_line1){ $line_color = $color_line2; }
echo "<TR BGCOLOR=$line_color ALIGN=CENTER><TD>Garrison ID</TD><TD>Remaining Force</TD><TD>Sector</TD><TD>Status</TD></TR>";
$cleanuptitle++;
}
if($line_color == $color_line2){ $line_color = $color_line1; }
elseif($line_color == $color_line1){ $line_color = $color_line2; }
echo "<TR BGCOLOR=$line_color ALIGN=CENTER><TD>$moraleinfo[garid]</TD><TD>$moraleinfo[sector2]</TD><TD>sector 2</TD><TD>routed!</TD></TR>";
$db->Execute("UPDATE $dbtables[combats] "
            ."set curforce = curforce - sector2, "
            ."sector2 = 0 "
            ."where garid = '$moraleinfo[garid]' "
            ."AND combat_id = '$uniqueid'");
}
else{
if($cleanuptitle < 1){
echo "<CENTER><TABLE WIDTH=\"80%\"><TR><TD><TABLE WIDTH=\"100%\">";
echo "<TR BGCOLOR=$color_header><TD COLSPAN=4 ALIGN=CENTER><FONT SIZE=+2>Morale Checks</FONT></TD></TR>";
if($line_color == $color_line2){ $line_color = $color_line1; }
elseif($line_color == $color_line1){ $line_color = $color_line2; }
echo "<TR BGCOLOR=$line_color ALIGN=CENTER><TD>Garrison ID</TD><TD>Remaining Force</TD><TD>Sector</TD><TD>Status</TD></TR>";
$cleanuptitle++;
}
if($line_color == $color_line2){ $line_color = $color_line1; }
elseif($line_color == $color_line1){ $line_color = $color_line2; }
echo "<TR BGCOLOR=$line_color ALIGN=CENTER><TD>$moraleinfo[garid]</TD><TD>$moraleinfo[sector2]</TD><TD>sector 2</TD><TD>firm</TD></TR>";


}
}



///////////////////////////////////Sector3 Cleanup//////////////////////////////////////////////////////////////////////////

$percentwounded = round(1 - ($moraleinfo[sector3]/$moraleinfo[startsector3]), 2);
$survivors = round((1 - $percentwounded), 2);
$percentwounded = explode('.', $percentwounded);
$percentwounded = $percentwounded[1];
$survivors = explode('.', $survivors);
$survivors = $survivors[1];
if($survivors < 0 | !$survivors){ $survivors = 0; }
if($percentwounded < 0 | !$percentwounded){ $percentwounded = 0; }
$routechance = 100 - ($survivors + $leadership[level] + $tribeinfo[morale]);
if($routechance < 0){ $routechance = 0; }
if($percentwounded == 0){ $routechance = 0; }
if($routechance > 0){
$routerand = rand(1, 100);
if($routerand < $routechance){
if($cleanuptitle < 1){
echo "<CENTER><TABLE WIDTH=\"80%\"><TR><TD><TABLE WIDTH=\"100%\">";
echo "<TR BGCOLOR=$color_header ALIGN=CENTER><TD COLSPAN=4><FONT SIZE=+2>Morale Checks</FONT></TD></TR>";
if($line_color == $color_line2){ $line_color = $color_line1; }
elseif($line_color == $color_line1){ $line_color = $color_line2; }
echo "<TR BGCOLOR=$line_color ALIGN=CENTER><TD>Garrison ID</TD><TD>Remaining Force</TD><TD>Sector</TD><TD>Status</TD></TR>";
$cleanuptitle++;
}
if($line_color == $color_line2){ $line_color = $color_line1; }
elseif($line_color == $color_line1){ $line_color = $color_line2; }
echo "<TR BGCOLOR=$line_color ALIGN=CENTER><TD>$moraleinfo[garid]</TD><TD>$moraleinfo[sector3]</TD><TD>sector 3</TD><TD>routed!</TD></TR>";
$db->Execute("UPDATE $dbtables[combats] "
            ."set curforce = curforce - sector3, "
            ."sector3 = 0 "
            ."where garid = '$moraleinfo[garid]' "
            ."AND combat_id = '$uniqueid'");
}
else{
if($cleanuptitle < 1){
echo "<CENTER><TABLE WIDTH=\"80%\"><TR><TD><TABLE WIDTH=\"100%\">";
echo "<TR BGCOLOR=$color_header><TD COLSPAN=4 ALIGN=CENTER><FONT SIZE=+2>Morale Checks</FONT></TD></TR>";
if($line_color == $color_line2){ $line_color = $color_line1; }
elseif($line_color == $color_line1){ $line_color = $color_line2; }
echo "<TR BGCOLOR=$line_color ALIGN=CENTER><TD>Garrison ID</TD><TD>Remaining Force</TD><TD>Sector</TD><TD>Status</TD></TR>";
$cleanuptitle++;
}
if($line_color == $color_line2){ $line_color = $color_line1; }
elseif($line_color == $color_line1){ $line_color = $color_line2; }
echo "<TR BGCOLOR=$line_color ALIGN=CENTER><TD>$moraleinfo[garid]</TD><TD>$moraleinfo[sector3]</TD><TD>sector 3</TD><TD>firm</TD></TR>";
}
}
$morale->MoveNext();
}
echo "</TABLE></TD></TR></TABLE>";


/////////////////////////////////////////////Melee Phase (Defender)////////////////////////

echo "<CENTER><TABLE WIDTH=\"80%\" BORDER=1><TR><TD><TABLE BORDER=0 WIDTH=\"100%\">";

$def = $db->Execute("SELECT * FROM $dbtables[combats] "
                   ."WHERE side = 'D' "
                   ."AND combat_id = '$uniqueid' "
                   ."AND trooptype = 'I'");
if($def->EOF){
echo "<TR><TD COLSPAN=3><CENTER><FONT SIZE=+2>Infantry Phase (Defenders)</FONT></CENTER></TD></TR>";
echo "<TR BGCOLOR=$color_header><TD COLSPAN=3><CENTER>No Infantry Units</CENTER></TD></TR>";
}


$titlecount = 0;
while(!$def->EOF){
$definfo = $def->fields;

if($titlecount < 1){
echo "<TR><TD COLSPAN=3><CENTER><FONT SIZE=+2>Infantry Phase (Defenders)</FONT></CENTER></TD></TR>";
$titlecount++;
echo "<TR BGCOLOR=$color_header><TD ALIGN=CENTER><FONT SIZE=+1>Sector One</FONT></TD><TD ALIGN=CENTER><FONT SIZE=+1>Sector Two</FONT></TD><TD ALIGN=CENTER><FONT SIZE=+1>Sector Three</FONT></TD></TR>";
}



///////////Get the right variables//////////////////////////////////////////////////////////////////////
echo "<TR><TD COLSPAN=3>&nbsp;</TD></TR>";
$weap = $db->Execute("SELECT * FROM $dbtables[weapons] WHERE proper = '$definfo[weapon1]'");
$weapon = $weap->fields;


$skcom = $db->Execute("SELECT * FROM $dbtables[skills] WHERE tribeid = '$definfo[tribeid]' AND abbr = 'com'");
$combat = $skcom->fields;
$skhor = $db->Execute("SELECT * FROM $dbtables[skills] WHERE tribeid = '$definfo[tribeid]' AND abbr = 'hor'");
$horsemanship = $skhor->fields;
$skldr = $db->Execute("SELECT * FROM $dbtables[skills] WHERE tribeid = '$definfo[tribeid]' AND abbr = 'ldr'");
$leadership = $skldr->fields;
$mor = $db->Execute("SELECT * FROM $dbtables[tribes] WHERE tribeid = '$definfo[tribeid]'");
$morale = $mor->fields;
$eff = $db->Execute("SELECT * FROM $dbtables[combat_terrain_effect] WHERE type = 'defense'");
$terrain_effect = $eff->fields;
$ter = $db->Execute("SELECT * FROM $dbtables[hexes] WHERE hex_id = '$definfo[hex_id]'");
$terrain = $ter->fields;
$ter_mods = $db->Execute("SELECT * from $dbtables[combat_terrain_mods]");
$terrainmods = $ter_mods->fields;
$cw = $db->Execute("SELECT * FROM $dbtables[weather] WHERE current_type = 'Y'");
$cur_weath = $cw->fields;
$modify = $terrain[terrain];
$hownow = $cur_weath[weather_id];

/////////////////Now, pick a target////////////////////////////////////////////////////////////////////
//////////////////////////////////////Sector1/////////////////////////////////////////////////////////
$tar = '';
$type = 0;
$tar = $db->Execute("SELECT count(*) as count FROM $dbtables[combats] "
                   ."WHERE side = 'A' "
                   ."AND combat_id = '$uniqueid' "
                   ."AND trooptype = 'I' "
                   ."AND sector1 > 0");
$target1 = $tar->fields;
if($target1[count] < 1){
$type = 1;
$tar = $db->Execute("SELECT count(*) as count FROM $dbtables[combats] "
                   ."WHERE side = 'A' "
                   ."AND combat_id = '$uniqueid' "
                   ."AND trooptype = 'C' "
                   ."AND sector1 > 0");
$target1 = $tar->fields;
}
if($target1[count] < 1){
$type = 2;
$tar = $db->Execute("SELECT count(*) as count FROM $dbtables[combats] "
                   ."WHERE side = 'A' "
                   ."AND combat_id = '$uniqueid' "
                   ."AND sector1 > 0");
$target1 = $tar->fields;
}
$maxtarget1 = ($target1[count] - 1);
$target1 = rand(0, $maxtarget1);
if($type == '2'){
$tar = $db->Execute("SELECT * FROM $dbtables[combats] "
                   ."WHERE side = 'A' "
                   ."AND combat_id = '$uniqueid' "
                   ."AND sector1 > 0 "
                   ."LIMIT $target1, 1");
}
elseif($type == '1'){
$tar = $db->Execute("SELECT * FROM $dbtables[combats] "
                   ."WHERE side = 'A' "
                   ."AND combat_id = '$uniqueid' "
                   ."AND trooptype = 'C' "
                   ."AND sector1 > 0 "
                   ."LIMIT $target1, 1");
}
elseif($type == '0'){
$tar = $db->Execute("SELECT * FROM $dbtables[combats] "
                   ."WHERE side = 'A' "
                   ."AND combat_id = '$uniqueid' "
                   ."AND trooptype = 'I' "
                   ."AND sector1 > 0 "
                   ."LIMIT $target1, 1");
}
$target1 = $tar->fields;
///////////////////////////////////////////Sector2/////////////////////////////////////////////////////////
$tar = '';
$type = 0;
$tar = $db->Execute("SELECT count(*) as count FROM $dbtables[combats] "
                   ."WHERE side = 'A' "
                   ."AND trooptype = 'I' "
                   ."AND combat_id = '$uniqueid' "
                   ."AND sector2 > 0");
$target2 = $tar->fields;
if($target2[count] < 1){
$type = 1;
$tar = $db->Execute("SELECT count(*) as count FROM $dbtables[combats] "
                   ."WHERE side = 'A' "
                   ."AND combat_id = '$uniqueid' "
                   ."AND trooptype = 'C' "
                   ."AND sector2 > 0");
$target2 = $tar->fields;
}
if($target2[count] < 1){
$type = 2;
$tar = $db->Execute("SELECT count(*) as count FROM $dbtables[combats] "
                   ."WHERE side = 'A' "
                   ."AND combat_id = '$uniqueid' "
                   ."AND sector2 > 0");
$target2 = $tar->fields;
}
$maxtarget2 = ($target2[count] - 1);
$target2 = rand(0, $maxtarget2);
if($type == '2'){
$tar = $db->Execute("SELECT * FROM $dbtables[combats] "
                   ."WHERE side = 'A' "
                   ."AND combat_id = '$uniqueid' "
                   ."AND sector2 > 0 "
                   ."LIMIT $target2, 1");
}
elseif($type == '1'){
$tar = $db->Execute("SELECT * FROM $dbtables[combats] "
                   ."WHERE side = 'A' "
                   ."AND combat_id = '$uniqueid' "
                   ."AND trooptype = 'C' "
                   ."AND sector2 > 0 "
                   ."LIMIT $target2, 1");
}
elseif($type == '0'){
$tar = $db->Execute("SELECT * FROM $dbtables[combats] "
                   ."WHERE side = 'A' "
                   ."AND combat_id = '$uniqueid' "
                   ."AND trooptype = 'I' "
                   ."AND sector2 > 0 "
                   ."LIMIT $target2, 1");
}
$target2 = $tar->fields;
////////////////////////////////////////////Sector3///////////////////////////////////////////////////////
$tar = '';
$type = 0;
$tar = $db->Execute("SELECT count(*) as count FROM $dbtables[combats] "
                   ."WHERE side = 'A' "
                   ."AND combat_id = '$uniqueid' "
                   ."AND trooptype = 'I' "
                   ."AND sector3 > 0");
$target3 = $tar->fields;
if($target3[count] < 1){
$type = 1;
$tar = $db->Execute("SELECT count(*) as count FROM $dbtables[combats] "
                   ."WHERE side = 'A' "
                   ."AND combat_id = '$uniqueid' "
                   ."AND trooptype = 'C' "
                   ."AND sector3 > 0");
$target3 = $tar->fields;
}
if($target3[count] < 1){
$type = 2;
$tar = $db->Execute("SELECT count(*) as count FROM $dbtables[combats] "
                   ."WHERE side = 'A' "
                   ."AND combat_id = '$uniqueid' "
                   ."AND sector3 > 0");
$target3 = $tar->fields;
}
$maxtarget3 = ($target3[count] - 1);
$target3 = rand(0, $maxtarget3);
if($type == '2'){
$tar = $db->Execute("SELECT * FROM $dbtables[combats] "
                   ."WHERE side = 'A' "
                   ."AND combat_id = '$uniqueid' "
                   ."AND sector3 > 0 "
                   ."LIMIT $target3, 1");
}
elseif($type == '1'){
$tar = $db->Execute("SELECT * FROM $dbtables[combats] "
                   ."WHERE side = 'A' "
                   ."AND combat_id = '$uniqueid' "
                   ."AND trooptype = 'C' "
                   ."AND sector3 > 0 "
                   ."LIMIT $target3, 1");
}
elseif($type == '0'){
$tar = $db->Execute("SELECT * FROM $dbtables[combats] "
                   ."WHERE side = 'A' "
                   ."AND combat_id = '$uniqueid' "
                   ."AND trooptype = 'I' "
                   ."AND sector3 > 0 "
                   ."LIMIT $target3, 1");
}
$target3 = $tar->fields;
//////////////////////////////////////Get the target's armor information//////////////////////////////////
///////////////////////////////////////Sector1////////////////////////////////////////////////////////////
$head = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target1[head_armor]'");
$head_armor = $head->fields;
$torso = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target1[torso_armor]'");
$torso_armor = $torso->fields;
$otorso = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target1[otorso_armor]'");
$otorso_armor = $otorso->fields;
$legs = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target1[legs_armor]'");
$legs_armor = $legs->fields;
$shield = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target1[shield]'");
$shield_armor = $shield->fields;
$weapon_type = explode(' ', $definfo[weapon1]);
$weapontype = $weapon_type[0];
if($weapontype == ''){
$weapontype = 'stone';
}
$armormod1 = $head_armor[$weapontype] + $torso_armor[$weapontype] + $otorso_armor[$weapontype] + $legs_armor[$weapontype] + $shield_armor[$weapontype];
////////////////////////////////////////Sector2/////////////////////////////////////////////////////////////
$head = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target2[head_armor]'");
$head_armor = $head->fields;
$torso = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target2[torso_armor]'");
$torso_armor = $torso->fields;
$otorso = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target2[otorso_armor]'");
$otorso_armor = $otorso->fields;
$legs = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target2[legs_armor]'");
$legs_armor = $legs->fields;
$shield = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target2[shield]'");
$shield_armor = $shield->fields;
$weapon_type = explode(' ', $definfo[weapon1]);
$weapontype = $weapon_type[0];
if($weapontype == ''){
$weapontype = 'stone';
}
$armormod2 = $head_armor[$weapontype] + $torso_armor[$weapontype] + $otorso_armor[$weapontype] + $legs_armor[$weapontype] + $shield_armor[$weapontype];
////////////////////////////////////////Sector3////////////////////////////////////////////////////////////
$head = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target3[head_armor]'");
$head_armor = $head->fields;
$torso = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target3[torso_armor]'");
$torso_armor = $torso->fields;
$otorso = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target3[otorso_armor]'");
$otorso_armor = $otorso->fields;
$legs = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target3[legs_armor]'");
$legs_armor = $legs->fields;
$shield = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target3[shield]'");
$shield_armor = $shield->fields;
$weapon_type = explode(' ', $definfo[weapon1]);
$weapontype = $weapon_type[0];
if($weapontype == ''){
$weapontype = 'stone';
}
$armormod3 = $head_armor[$weapontype] + $torso_armor[$weapontype] + $otorso_armor[$weapontype] + $legs_armor[$weapontype] + $shield_armor[$weapontype];
//////////////////////////////////////////////////////////////////////////////////////////////////////////

if($target1[trooptype] == 'I'){
$weaponeffect1 = $weapon[inf_inf];
}
elseif($target1[trooptype] == 'C'){
$weaponeffect1 = $weapon[inf_cav];
}
else {
$weaponeffect1 = $weapon[inf_arc];
}
if($target2[trooptype] == 'I'){
$weaponeffect2 = $weapon[inf_inf];
}
elseif($target2[trooptype] == 'C'){
$weaponeffect2 = $weapon[inf_cav];
}
else {
$weaponeffect2 = $weapon[inf_arc];
}
if($target3[trooptype] == 'I'){
$weaponeffect3 = $weapon[inf_inf];
}
elseif($target3[trooptype] == 'C'){
$weaponeffect3 = $weapon[inf_cav];
}
else {
$weaponeffect3 = $weapon[inf_arc];
}

if($target1[sector1] > 0 && $definfo[sector1] > 0){
$random6 = rand(1,8);
$meleeattack1 = round(($weaponeffect1 * $weather[$hownow] * $terrain_effect[$modify] * $morale[morale] * $leadership[level] + $definfo[exp] + 6 + $random6)/7 * $definfo[sector1]);
}
if($target2[sector2] > 0 && $definfo[sector2] > 0){
$random6 = rand(1,8);
$meleeattack2 = round(($weaponeffect2 * $weather[$hownow] * $terrain_effect[$modify] * $morale[morale] * $leadership[level] + $definfo[exp] + 6 + $random6)/7 * $definfo[sector2]);
}
if($target3[sector3] > 0 && $definfo[sector3] > 0){
$random6 = rand(1,8);
$meleeattack3 = round(($weaponeffect3 * $weather[$hownow] * $terrain_effect[$modify] * $morale[morale] * $leadership[level] + $definfo[exp] + 6 + $random6)/7 * $definfo[sector3]);
}
if($definfo[sector1] < 0){
$definfo[sector1] = 0;
}
if($definfo[sector2] < 0){
$definfo[sector2] = 0;
}
if($definfo[sector3] < 0){
$definfo[sector3] = 0;
}
echo "<TR><TD>Force: $definfo[sector1] from Garrison $definfo[garid]</TD><TD>Force: $definfo[sector2] from Garrison $definfo[garid]</TD><TD>Force: $definfo[sector3] from Garrison $definfo[garid]</TD></TR>";
if($target1[sector1] > 0){
echo "<TR><TD>Attacking: $target1[sector1] from Garrison $target1[garid]</TD>";
}
else{
echo "<TR><TD>Looting</TD>";
}
if($target2[sector2] > 0){
echo "<TD>Attacking: $target2[sector2] from Garrison $target2[garid]</TD>";
}
else{
echo "<TD>Looting</TD>";
}
if($target3[sector3] > 0){
echo "<TD>Attacking: $target3[sector3] from Garrison $target3[garid]</TD></TR>";
}
else{
echo "<TD>Looting</TD></TR>";
}
if($target1[sector1] > 0 | $definfo[sector1] > 0){
$work1 = $meleeattack1 - round($armormod1 * ($meleeattack1/10));
while($work1 > 9){
$random10 = rand(1,10);
$actual1 += $random10;
$work1 -= 10;
}
$actual1 = round($actual1/10);
if($definfo[sector1] <= 0 | $target1[sector1] <= 0){
$actual1 = 0;
}
$total1sectoratt += $actual1;
}
if($target2[sector2] > 0 | $definfo[sector2] > 0){
$work2 = $meleeattack2 - round($armormod2 * ($meleeattack2/10));
while($work2 > 9){
$random10 = rand(1,10);
$actual2 += $random10;
$work2 -= 10;
}
$actual2 = round($actual2/10);
if($definfo[sector2] <= 0 | $target2[sector2] <= 0){
$actual2 = 0;
}
$total2sectoratt += $actual2;
}
if($target3[sector3] > 0 | $definfo[sector3] > 0){
$work3 = $meleeattack3 - round($armormod3 * ($meleeattack3/10));
while($work3 > 9){

$random10 = rand(1,10);
$actual3 += $random10;
$work3 -= 10;
}
$actual3 = round($actual3/10);
if($definfo[sector3] <= 0 | $target3[sector3] <= 0){
$actual3 = 0;
}
$total3sectoratt += $actual3;
}
if(!$actual1){
$actual1 = 0;
}
if(!$actual2){
$actual2 = 0;
}
if(!$actual3){
$actual3 = 0;
}
echo "<TR><TD><FONT COLOR=RED>Casualties: $actual1</FONT></TD><TD><FONT COLOR=RED>Casualties: $actual2</FONT></TD><TD><FONT COLOR=RED>Casualties: $actual3</FONT></TD></TR>";

$db->Execute("UPDATE $dbtables[combats] "
            ."set sector1 = sector1 - '$actual1', "
            ."curforce = curforce - '$actual1' "
            ."WHERE garid = '$target1[garid]' "
            ."AND combat_id = '$uniqueid'");
$db->Execute("UPDATE $dbtables[combats] "
            ."set sector2 = sector2 - '$actual2', "
            ."curforce = curforce - '$actual2' "
            ."WHERE garid = '$target2[garid]' "
            ."AND combat_id = '$uniqueid'");
$db->Execute("UPDATE $dbtables[combats] "
            ."set sector3 = sector3 - '$actual3', "
            ."curforce = curforce - '$actual3' "
            ."WHERE garid = '$target3[garid]' "
            ."AND combat_id = '$uniqueid'");

$def->MoveNext();
}
echo "<TR><TD>Total Sector Kills: $total1sectoratt</TD><TD>Total Sector Kills: $total2sectoratt</TD><TD>Total Sector Kills: $total3sectoratt</TD></TR>";
echo "</TABLE></TD></TR></TABLE></CENTER>";



////////////////////////////////////////////////////Attacker's Infantry Phase//////////////////////////////////////////////


echo "<CENTER><TABLE WIDTH=\"80%\" BORDER=1><TR><TD><TABLE BORDER=0 WIDTH=\"100%\">";

$att = $db->Execute("SELECT * FROM $dbtables[combats] "
                   ."WHERE side = 'A' "
                   ."AND combat_id = '$uniqueid' "
                   ."AND trooptype = 'I' "
                   ."AND curforce > 0");

if($att->EOF){
echo "<TR><TD COLSPAN=3><CENTER><FONT SIZE=+2>Infantry Phase (Attackers)</FONT></CENTER></TD></TR>";
echo "<TR BGCOLOR=$color_header><TD COLSPAN=3><CENTER>No Infantry Units</CENTER></TD></TR>";
}

$titlecount = 0;
while(!$att->EOF){
$attinfo = $att->fields;

if($titlecount < 1){
echo "<TR><TD COLSPAN=3><CENTER><FONT SIZE=+2>Infantry Phase (Attacker)</FONT></CENTER></TD></TR>";
$titlecount++;
echo "<TR BGCOLOR=$color_header><TD ALIGN=CENTER><FONT SIZE=+1>Sector One</FONT></TD><TD ALIGN=CENTER><FONT SIZE=+1>Sector Two</FONT></TD><TD ALIGN=CENTER><FONT SIZE=+1>Sector Three</FONT></TD></TR>";
}



///////////Get the right variables//////////////////////////////////////////////////////////////////////
echo "<TR><TD COLSPAN=3>&nbsp;</TD></TR>";
$weap = $db->Execute("SELECT * FROM $dbtables[weapons] WHERE proper = '$attinfo[weapon1]'");
$weapon = $weap->fields;


$skcom = $db->Execute("SELECT * FROM $dbtables[skills] WHERE tribeid = '$attinfo[tribeid]' AND abbr = 'com'");
$combat = $skcom->fields;
$skhor = $db->Execute("SELECT * FROM $dbtables[skills] WHERE tribeid = '$attinfo[tribeid]' AND abbr = 'hor'");
$horsemanship = $skhor->fields;
$skldr = $db->Execute("SELECT * FROM $dbtables[skills] WHERE tribeid = '$attinfo[tribeid]' AND abbr = 'ldr'");
$leadership = $skldr->fields;
$mor = $db->Execute("SELECT * FROM $dbtables[tribes] WHERE tribeid = '$attinfo[tribeid]'");
$morale = $mor->fields;
$eff = $db->Execute("SELECT * FROM $dbtables[combat_terrain_effect] WHERE type = 'attack'");
$terrain_effect = $eff->fields;
$ter = $db->Execute("SELECT * FROM $dbtables[hexes] WHERE hex_id = '$attinfo[hex_id]'");
$terrain = $ter->fields;
$ter_mods = $db->Execute("SELECT * from $dbtables[combat_terrain_mods]");
$terrainmods = $ter_mods->fields;
$cw = $db->Execute("SELECT * FROM $dbtables[weather] WHERE current_type = 'Y'");
$cur_weath = $cw->fields;
$modify = $terrain[terrain];
$hownow = $cur_weath[weather_id];

/////////////////Now, pick a target////////////////////////////////////////////////////////////////////
/////////////////////////////////////Sector1////////////////////////////////////////////////////////////
$tar = '';
$type = 0;
$tar = $db->Execute("SELECT count(*) as count FROM $dbtables[combats] "
                   ."WHERE side = 'D' "
                   ."AND combat_id = '$uniqueid' "
                   ."AND trooptype = 'I' "
                   ."AND sector1 > 0");
$target1 = $tar->fields;
if($target1[count] < 1){
$type = 1;
$tar = $db->Execute("SELECT count(*) as count FROM $dbtables[combats] "
                   ."WHERE side = 'D' "
                   ."AND combat_id = '$uniqueid' "
                   ."AND trooptype = 'C' "
                   ."AND sector1 > 0");
$target1 = $tar->fields;
}
if($target1[count] < 1){
$type = 2;
$tar = $db->Execute("SELECT count(*) as count FROM $dbtables[combats] "
                   ."WHERE side = 'D' "
                   ."AND combat_id = '$uniqueid' "
                   ."AND sector1 > 0");
$target1 = $tar->fields;
}
$maxtarget1 = ($target1[count] - 1);
$target1 = rand(0, $maxtarget1);
if($type == '2'){
$tar = $db->Execute("SELECT * FROM $dbtables[combats] "
                   ."WHERE side = 'D' "
                   ."AND combat_id = '$uniqueid' "
                   ."AND sector1 > 0 "
                   ."LIMIT $target1, 1");
}
elseif($type == '1'){
$tar = $db->Execute("SELECT * FROM $dbtables[combats] "
                   ."WHERE side = 'D' "
                   ."AND combat_id = '$uniqueid' "
                   ."AND trooptype = 'C' "
                   ."AND sector1 > 0 "
                   ."LIMIT $target1, 1");
}
elseif($type == '0'){
$tar = $db->Execute("SELECT * FROM $dbtables[combats] "
                   ."WHERE side = 'D' "
                   ."AND combat_id = '$uniqueid' "
                   ."AND trooptype = 'I' "
                   ."AND sector1 > 0 "
                   ."LIMIT $target1, 1");
}
$target1 = $tar->fields;
////////////////////////////////////Sector2////////////////////////////////////////////////////////////
$tar = '';
$type = 0;
$tar = $db->Execute("SELECT count(*) as count FROM $dbtables[combats] "
                   ."WHERE side = 'D' "
                   ."AND trooptype = 'I' "
                   ."AND combat_id = '$uniqueid' "
                   ."AND sector2 > 0");
$target2 = $tar->fields;
if($target2[count] < 1){
$type = 1;
$tar = $db->Execute("SELECT count(*) as count FROM $dbtables[combats] "
                   ."WHERE side = 'D' "
                   ."AND combat_id = '$uniqueid' "
                   ."AND trooptype = 'C' "
                   ."AND sector2 > 0");
$target2 = $tar->fields;
}
if($target2[count] < 1){
$type = 2;
$tar = $db->Execute("SELECT count(*) as count FROM $dbtables[combats] "
                   ."WHERE side = 'D' "
                   ."AND combat_id = '$uniqueid' "
                   ."AND sector2 > 0");
$target2 = $tar->fields;
}
$maxtarget2 = ($target2[count] - 1);
$target2 = rand(0, $maxtarget2);
if($type == '2'){
$tar = $db->Execute("SELECT * FROM $dbtables[combats] "
                   ."WHERE side = 'D' "
                   ."AND combat_id = '$uniqueid' "
                   ."AND sector2 > 0 "
                   ."LIMIT $target2, 1");
}
elseif($type == '1'){
$tar = $db->Execute("SELECT * FROM $dbtables[combats] "
                   ."WHERE side = 'D' "
                   ."AND combat_id = '$uniqueid' "
                   ."AND trooptype = 'C' "
                   ."AND sector2 > 0 "
                   ."LIMIT $target2, 1");
}
elseif($type == '0'){
$tar = $db->Execute("SELECT * FROM $dbtables[combats] "
                   ."WHERE side = 'D' "
                   ."AND combat_id = '$uniqueid' "
                   ."AND trooptype = 'I' "
                   ."AND sector2 > 0 "
                   ."LIMIT $target2, 1");
}
$target2 = $tar->fields;
////////////////////////////////////Sector3////////////////////////////////////////////////////////////
$tar = '';
$type = 0;
$tar = $db->Execute("SELECT count(*) as count FROM $dbtables[combats] "
                   ."WHERE side = 'D' "
                   ."AND combat_id = '$uniqueid' "
                   ."AND trooptype = 'I' "
                   ."AND sector3 > 0");
$target3 = $tar->fields;
if($target3[count] < 1){
$type = 1;
$tar = $db->Execute("SELECT count(*) as count FROM $dbtables[combats] "
                   ."WHERE side = 'D' "
                   ."AND trooptype = 'C' "
                   ."AND combat_id = '$uniqueid' "
                   ."AND sector3 > 0");
$target3 = $tar->fields;
}
if($target3[count] < 1){
$type = 2;
$tar = $db->Execute("SELECT count(*) as count FROM $dbtables[combats] "
                   ."WHERE side = 'D' "
                   ."AND combat_id = '$uniqueid' "
                   ."AND sector3 > 0");
$target3 = $tar->fields;
}
$maxtarget3 = ($target3[count] - 1);
$target3 = rand(0, $maxtarget3);
if($type == '2'){
$tar = $db->Execute("SELECT * FROM $dbtables[combats] "
                   ."WHERE side = 'D' "
                   ."AND combat_id = '$uniqueid' "
                   ."AND sector3 > 0 "
                   ."LIMIT $target3, 1");
}
elseif($type == '1'){
$tar = $db->Execute("SELECT * FROM $dbtables[combats] "
                   ."WHERE side = 'D' "
                   ."AND combat_id = '$uniqueid' "
                   ."AND trooptype = 'C' "
                   ."AND sector3 > 0 "
                   ."LIMIT $target3, 1");
}
elseif($type == '0'){
$tar = $db->Execute("SELECT * FROM $dbtables[combats] "
                   ."WHERE side = 'D' "
                   ."AND combat_id = '$uniqueid' "
                   ."AND trooptype = 'I' "
                   ."AND sector3 > 0 "
                   ."LIMIT $target3, 1");
}
$target3 = $tar->fields;
//////////////////////////////////////Get the target's armor information//////////////////////////////////
/////////////////////////////////////////////Sector1////////////////////////////////////////////////////
$head = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target1[head_armor]'");
$head_armor = $head->fields;
$torso = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target1[torso_armor]'");
$torso_armor = $torso->fields;
$otorso = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target1[otorso_armor]'");
$otorso_armor = $otorso->fields;
$legs = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target1[legs_armor]'");
$legs_armor = $legs->fields;
$shield = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target1[shield]'");
$shield_armor = $shield->fields;
$weapon_type = explode(' ', $attinfo[weapon1]);
$weapontype = $weapon_type[0];
if($weapontype == ''){
$weapontype = 'stone';
}
$armormod1 = $head_armor[$weapontype] + $torso_armor[$weapontype] + $otorso_armor[$weapontype] + $legs_armor[$weapontype] + $shield_armor[$weapontype];
///////////////////////////////////////////////Sector2///////////////////////////////////////////////////
$head = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target2[head_armor]'");
$head_armor = $head->fields;
$torso = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target2[torso_armor]'");
$torso_armor = $torso->fields;
$otorso = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target2[otorso_armor]'");
$otorso_armor = $otorso->fields;
$legs = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target2[legs_armor]'");
$legs_armor = $legs->fields;
$shield = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target2[shield]'");
$shield_armor = $shield->fields;
$weapon_type = explode(' ', $attinfo[weapon1]);
$weapontype = $weapon_type[0];
if($weapontype == ''){
$weapontype = 'stone';
}
$armormod2 = $head_armor[$weapontype] + $torso_armor[$weapontype] + $otorso_armor[$weapontype] + $legs_armor[$weapontype] + $shield_armor[$weapontype];
///////////////////////////////////////////////Sector3////////////////////////////////////////////////////
$head = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target3[head_armor]'");
$head_armor = $head->fields;
$torso = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target3[torso_armor]'");
$torso_armor = $torso->fields;
$otorso = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target3[otorso_armor]'");
$otorso_armor = $otorso->fields;
$legs = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target3[legs_armor]'");
$legs_armor = $legs->fields;
$shield = $db->Execute("SELECT * FROM $dbtables[armor] WHERE proper = '$target3[shield]'");
$shield_armor = $shield->fields;
$weapon_type = explode(' ', $attinfo[weapon1]);
$weapontype = $weapon_type[0];
if($weapontype == ''){
$weapontype = 'stone';
}
$armormod3 = $head_armor[$weapontype] + $torso_armor[$weapontype] + $otorso_armor[$weapontype] + $legs_armor[$weapontype] + $shield_armor[$weapontype];
//////////////////////////////////////////////////////////////////////////////////////////////////////////

if($target1[trooptype] == 'I'){
$weaponeffect1 = $weapon[inf_inf];
}
elseif($target1[trooptype] == 'C'){
$weaponeffect1 = $weapon[inf_cav];
}
else {
$weaponeffect1 = $weapon[inf_arc];
}
if($target2[trooptype] == 'I'){
$weaponeffect2 = $weapon[inf_inf];
}
elseif($target2[trooptype] == 'C'){
$weaponeffect2 = $weapon[inf_cav];
}
else {
$weaponeffect2 = $weapon[inf_arc];
}
if($target3[trooptype] == 'I'){
$weaponeffect3 = $weapon[inf_inf];
}
elseif($target3[trooptype] == 'C'){
$weaponeffect3 = $weapon[inf_cav];
}
else {
$weaponeffect3 = $weapon[inf_arc];
}

if($target1[sector1] > 0){
$random6 = rand(1,8);
$meleeattack1 = round(($weaponeffect1 * $weather[$hownow] * $terrain_effect[$modify] * $morale[morale] * $leadership[level] + $attinfo[exp] + 6 + $random6)/7 * $attinfo[sector1]);
}
if($target2[sector2] > 0){
$random6 = rand(1,8);
$meleeattack2 = round(($weaponeffect2 * $weather[$hownow] * $terrain_effect[$modify] * $morale[morale] * $leadership[level] + $attinfo[exp] + 6 + $random6)/7 * $attinfo[sector2]);
}
if($target3[sector3] > 0){
$random6 = rand(1,8);
$meleeattack3 = round(($weaponeffect3 * $weather[$hownow] * $terrain_effect[$modify] * $morale[morale] * $leadership[level] + $attinfo[exp] + 6 + $random6)/7 * $attinfo[sector3]);
}
echo "<TR><TD>Force: $attinfo[sector1] from Garrison $attinfo[garid]</TD><TD>Force: $attinfo[sector2] from Garrison $attinfo[garid]</TD><TD>Force: $attinfo[sector3] from Garrison $attinfo[garid]</TD></TR>";
if($target1[sector1] > 0){
echo "<TR><TD>Attacking: $target1[sector1] from Garrison $target1[garid]</TD>";
}
else{
echo "<TR><TD>Looting</TD>";
}
if($target2[sector2] > 0){
echo "<TD>Attacking: $target2[sector2] from Garrison $target2[garid]</TD>";
}
else{
echo "<TD>Looting</TD>";
}
if($target3[sector3] > 0){
echo "<TD>Attacking: $target3[sector3] from Garrison $target3[garid]</TD></TR>";
}
else{
echo "<TD>Looting</TD></TR>";
}
if($target1[sector1] > 0 && $attinfo[sector1] > 0){
$work1 = $meleeattack1 - round($armormod1 * ($meleeattack1/10));
while($work1 > 9){
$random10 = rand(1,10);
$actual1 += $random10;
$work1 -= 10;
}
$actual1 = round($actual1/10);
if($attinfo[sector1] <= 0 | $target1[sector1] <= 0){
$actual1 = 0;
}
$total1sectordef += $actual1;
}
if($target2[sector2] > 0 && $attinfo[sector2] > 0){
$work2 = $meleeattack2 - round($armormod2 * ($meleeattack2/10));
while($work2 > 9){
$random10 = rand(1,10);
$actual2 += $random10;
$work2 -= 10;
}
$actual2 = round($actual2/10);
if($attinfo[sector2] <= 0 | $target2[sector2] <= 0){
$actual2 = 0;
}
$total2sectordef += $actual2;
}
if($target3[sector3] > 0 && $attinfo[sector3] > 0){
$work3 = $meleeattack3 - round($armormod3 * ($meleeattack3/10));
while($work3 > 9){

$random10 = rand(1,10);
$actual3 += $random10;
$work3 -= 10;
}
$actual3 = round($actual3/10);
if($attinfo[sector3] <= 0  | $target3[sector3] <= 0){
$actual3 = 0;
}
$total3sectordef += $actual3;
}
if(!$actual1){
$actual1 = 0;
}
if(!$actual2){
$actual2 = 0;
}
if(!$actual3){
$actual3 = 0;
}
echo "<TR><TD><FONT COLOR=RED>Casualties: $actual1</FONT></TD><TD><FONT COLOR=RED>Casualties: $actual2</FONT></TD><TD><FONT COLOR=RED>Casualties: $actual3</FONT></TD></TR>";


$db->Execute("UPDATE $dbtables[combats] "
            ."set sector1 = sector1 - '$actual1', "
            ."curforce = curforce - '$actual1' "
            ."WHERE garid = '$target1[garid]' "
            ."AND combat_id = '$uniqueid'");
$db->Execute("UPDATE $dbtables[combats] "
            ."set sector2 = sector2 - '$actual2', "
            ."curforce = curforce - '$actual2' "
            ."WHERE garid = '$target2[garid]' "
            ."AND combat_id = '$uniqueid'");
$db->Execute("UPDATE $dbtables[combats] "
            ."set sector3 = sector3 - '$actual3', "
            ."curforce = curforce - '$actual3' "
            ."WHERE garid = '$target3[garid]' "
            ."AND combat_id = '$uniqueid'");


$att->MoveNext();
}
echo "<TR><TD>Total Sector Kills: $total1sectordef</TD><TD>Total Sector Kills: $total2sectordef</TD><TD>Total Sector Kills: $total3sectordef</TD></TR>";
echo "</TABLE></TD></TR></TABLE></CENTER>";



//////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////After Infantry Cleanup///////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////

$morale = $db->Execute("SELECT * FROM $dbtables[combats] "
                      ."WHERE combat_id = '$uniqueid'");
$cleanuptitle = 0;
$line_color = $color_line1;
while(!$morale->EOF){
$moraleinfo = $morale->fields;
$skldr = $db->Execute("SELECT * FROM $dbtables[skills] WHERE tribeid = '$moraleinfo[tribeid]' AND abbr = 'ldr'");
$leadership = $skldr->fields;
$tribe = $db->Execute("SELECT * FROM $dbtables[tribes] WHERE tribeid = '$moraleinfo[tribeid]'");
$tribeinfo = $tribe->fields;
if($moraleinfo[sector1] < 0){
$db->Execute("UPDATE $dbtables[combats] "
            ."set sector1 = 0 "
            ."WHERE tribeid = '$moraleinfo[tribeid]' "
            ."AND combat_id = '$uniqueid' "
            ."AND garid = '$moraleinfo[garid]'");
$moraleinfo[sector1] = 0;
}
if($moraleinfo[sector2] < 0){
$db->Execute("UPDATE $dbtables[combats] "
            ."set sector2 = 0 "
            ."WHERE tribeid = '$moraleinfo[tribeid]' "
            ."AND combat_id = '$uniqueid' "
            ."AND garid = '$moraleinfo[garid]'");
$moraleinfo[sector2] = 0;
}
if($moraleinfo[sector3] < 0){
$db->Execute("UPDATE $dbtables[combats] "
            ."set sector3 = 0 "
            ."WHERE tribeid = '$moraleinfo[tribeid]' "
            ."AND combat_id = '$uniqueid' "
            ."AND garid = '$moraleinfo[garid]'");
$moraleinfo[sector3] = 0;
}
//////////////////////////////sector1 cleanup///////////////////////////////////////////////////////
$percentwounded = round(1 - ($moraleinfo[sector1]/$moraleinfo[startsector1]), 2);
$survivors = round((1 - $percentwounded), 2);
$percentwounded = explode('.', $percentwounded);
$percentwounded = $percentwounded[1];
$survivors = explode('.', $survivors);
$survivors = $survivors[1];
if($survivors < 0 | !$survivors){
$survivors = 0;
}
if($percentwounded < 0 | !$percentwounded){
$percentwounded = 0;
}
$routechance = 100 - ($survivors + $leadership[level] + $tribeinfo[morale]);
if($routechance < 0){
$routechance = 0;
}
if($percentwounded == 0){
$routechance = 0;
}

if($routechance > 0){
$routerand = rand(1, 100);
if($routerand < $routechance){
if($cleanuptitle < 1){
echo "<CENTER><TABLE WIDTH=\"80%\"><TR><TD><TABLE WIDTH=\"100%\">";
echo "<TR BGCOLOR=$color_header><TD COLSPAN=4 ALIGN=CENTER><FONT SIZE=+2>Morale Checks</FONT></TD></TR>";
if($line_color == $color_line2){
$line_color = $color_line1;
}
elseif($line_color == $color_line1){
$line_color = $color_line2;
}
echo "<TR BGCOLOR=$line_color ALIGN=CENTER><TD>Garrison ID</TD><TD>Remaining Force</TD><TD>Sector</TD><TD>Status</TD></TR>";
$cleanuptitle++;
}
if($line_color == $color_line2){
$line_color = $color_line1;
}
elseif($line_color == $color_line1){
$line_color = $color_line2;
}
echo "<TR BGCOLOR=$line_color ALIGN=CENTER><TD>$moraleinfo[garid]</TD><TD>$moraleinfo[sector1]</TD><TD>sector 1</TD><TD>routed!</TD></TR>";
$db->Execute("UPDATE $dbtables[combats] "
            ."set curforce = curforce - sector1, "
            ."sector1 = 0 "
            ."where garid = '$moraleinfo[garid]' "
            ."AND combat_id = '$uniqueid'");
}
else{
if($cleanuptitle < 1){
echo "<CENTER><TABLE WIDTH=\"80%\"><TR><TD><TABLE WIDTH=\"100%\">";
echo "<TR BGCOLOR=$color_header><TD COLSPAN=4 ALIGN=CENTER><FONT SIZE=+2>Morale Checks</FONT></TD></TR>";
if($line_color == $color_line2){
$line_color = $color_line1;
}
elseif($line_color == $color_line1){
$line_color = $color_line2;
}
echo "<TR BGCOLOR=$line_color ALIGN=CENTER><TD>Garrison ID</TD><TD>Remaining Force</TD><TD>Sector</TD><TD>Status</TD></TR>";
$cleanuptitle++;
}
if($line_color == $color_line2){
$line_color = $color_line1;
}
elseif($line_color == $color_line1){
$line_color = $color_line2;
}
echo "<TR BGCOLOR=$line_color ALIGN=CENTER><TD>$moraleinfo[garid]</TD><TD>$moraleinfo[sector1]</TD><TD>sector 1</TD><TD>firm</TD></TR>";
}
}



/////////////////////////////////////Sector 2 Cleanup////////////////////////////////////////////////////


$percentwounded = round(1 - ($moraleinfo[sector2]/$moraleinfo[startsector2]), 2);
$survivors = round((1 - $percentwounded), 2);
$percentwounded = explode('.', $percentwounded);
$percentwounded = $percentwounded[1];
$survivors = explode('.', $survivors);
$survivors = $survivors[1];
if($survivors < 0 | !$survivors){ $survivors = 0; }
if($percentwounded < 0 | !$percentwounded){ $percentwounded = 0; }
$routechance = 100 - ($survivors + $leadership[level] + $tribeinfo[morale]);
if($routechance < 0){ $routechance = 0; }
if($percentwounded == 0){ $routechance = 0; }
if($routechance > 0){
$routerand = rand(1, 100);
if($routerand < $routechance){
if($cleanuptitle < 1){
echo "<CENTER><TABLE WIDTH=\"80%\"><TR><TD><TABLE WIDTH=\"100%\">";
echo "<TR BGCOLOR=$color_header ALIGN=CENTER><TD COLSPAN=4><FONT SIZE=+2>Morale Checks</FONT></TD></TR>";
if($line_color == $color_line2){ $line_color = $color_line1; }
elseif($line_color == $color_line1){ $line_color = $color_line2; }
echo "<TR BGCOLOR=$line_color ALIGN=CENTER><TD>Garrison ID</TD><TD>Remaining Force</TD><TD>Sector</TD><TD>Status</TD></TR>";
$cleanuptitle++;
}
if($line_color == $color_line2){ $line_color = $color_line1; }
elseif($line_color == $color_line1){ $line_color = $color_line2; }
echo "<TR BGCOLOR=$line_color ALIGN=CENTER><TD>$moraleinfo[garid]</TD><TD>$moraleinfo[sector2]</TD><TD>sector 2</TD><TD>routed!</TD></TR>";
$db->Execute("UPDATE $dbtables[combats] "
            ."set curforce = curforce - sector2, "
            ."sector2 = 0 "
            ."where garid = '$moraleinfo[garid]' "
            ."AND combat_id = '$uniqueid'");
}
else{
if($cleanuptitle < 1){
echo "<CENTER><TABLE WIDTH=\"80%\"><TR><TD><TABLE WIDTH=\"100%\">";
echo "<TR BGCOLOR=$color_header><TD COLSPAN=4 ALIGN=CENTER><FONT SIZE=+2>Morale Checks</FONT></TD></TR>";
if($line_color == $color_line2){ $line_color = $color_line1; }
elseif($line_color == $color_line1){ $line_color = $color_line2; }
echo "<TR BGCOLOR=$line_color ALIGN=CENTER><TD>Garrison ID</TD><TD>Remaining Force</TD><TD>Sector</TD><TD>Status</TD></TR>";
$cleanuptitle++;
}
if($line_color == $color_line2){ $line_color = $color_line1; }
elseif($line_color == $color_line1){ $line_color = $color_line2; }
echo "<TR BGCOLOR=$line_color ALIGN=CENTER><TD>$moraleinfo[garid]</TD><TD>$moraleinfo[sector2]</TD><TD>sector 2</TD><TD>firm</TD></TR>";


}
}



///////////////////////////////////Sector3 Cleanup//////////////////////////////////////////////////////////////////////////

$percentwounded = round(1 - ($moraleinfo[sector3]/$moraleinfo[startsector3]), 2);
$survivors = round((1 - $percentwounded), 2);
$percentwounded = explode('.', $percentwounded);
$percentwounded = $percentwounded[1];
$survivors = explode('.', $survivors);
$survivors = $survivors[1];
if($survivors < 0 | !$survivors){ $survivors = 0; }
if($percentwounded < 0 | !$percentwounded){ $percentwounded = 0; }
$routechance = 100 - ($survivors + $leadership[level] + $tribeinfo[morale]);
if($routechance < 0){ $routechance = 0; }
if($percentwounded == 0){ $routechance = 0; }
if($routechance > 0){
$routerand = rand(1, 100);
if($routerand < $routechance){
if($cleanuptitle < 1){
echo "<CENTER><TABLE WIDTH=\"80%\"><TR><TD><TABLE WIDTH=\"100%\">";
echo "<TR BGCOLOR=$color_header ALIGN=CENTER><TD COLSPAN=4><FONT SIZE=+2>Morale Checks</FONT></TD></TR>";
if($line_color == $color_line2){ $line_color = $color_line1; }
elseif($line_color == $color_line1){ $line_color = $color_line2; }
echo "<TR BGCOLOR=$line_color ALIGN=CENTER><TD>Garrison ID</TD><TD>Remaining Force</TD><TD>Sector</TD><TD>Status</TD></TR>";
$cleanuptitle++;
}
if($line_color == $color_line2){ $line_color = $color_line1; }
elseif($line_color == $color_line1){ $line_color = $color_line2; }
echo "<TR BGCOLOR=$line_color ALIGN=CENTER><TD>$moraleinfo[garid]</TD><TD>$moraleinfo[sector3]</TD><TD>sector 3</TD><TD>routed!</TD></TR>";
$db->Execute("UPDATE $dbtables[combats] "
            ."set curforce = curforce - sector3, "
            ."sector3 = 0 "
            ."where garid = '$moraleinfo[garid]' "
            ."AND combat_id = '$uniqueid'");
}
else{
if($cleanuptitle < 1){
echo "<CENTER><TABLE WIDTH=\"80%\"><TR><TD><TABLE WIDTH=\"100%\">";
echo "<TR BGCOLOR=$color_header><TD COLSPAN=4 ALIGN=CENTER><FONT SIZE=+2>Morale Checks</FONT></TD></TR>";
if($line_color == $color_line2){ $line_color = $color_line1; }
elseif($line_color == $color_line1){ $line_color = $color_line2; }
echo "<TR BGCOLOR=$line_color ALIGN=CENTER><TD>Garrison ID</TD><TD>Remaining Force</TD><TD>Sector</TD><TD>Status</TD></TR>";
$cleanuptitle++;
}
if($line_color == $color_line2){ $line_color = $color_line1; }
elseif($line_color == $color_line1){ $line_color = $color_line2; }
echo "<TR BGCOLOR=$line_color ALIGN=CENTER><TD>$moraleinfo[garid]</TD><TD>$moraleinfo[sector3]</TD><TD>sector 3</TD><TD>firm</TD></TR>";
}
}
$morale->MoveNext();
}
echo "</TABLE></TD></TR></TABLE>";


//////////////////////////////////////Now, let's kill us some horses///////////////////////////////
$horse = $db->Execute("SELECT * FROM $dbtables[combats] "
                     ."WHERE horses > 0 "
                     ."AND combat_id = '$uniqueid' "
                     ."AND side = 'A'");
while(!$horse->EOF){
$horseinfo = $horse->fields;

$posshorseloss = $horseinfo[startforce] - $horseinfo[curforce];
while($posshorseloss > 0){
$roll = 0;
$roll = rand(1,10);
if($roll > 2){
$horselossatt += 1;
}
$posshorseloss -= 1;
}
$horsesatt = 0;
if($horselossatt > $horseinfo[horses]){
$horselossatt = $horseinfo[horses];
}
$horsesatt = $horseinfo[horses] - $horselossatt;
$db->Execute("UPDATE $dbtables[combats] "
            ."set horses = '$horsesatt' "
            ."WHERE garid = '$horseinfo[garid]' "
            ."AND combat_id = '$uniqueid' "
            ."and side = 'A'");
$db->Execute("UPDATE $dbtables[garrisons] set horses = '$horsesatt' WHERE garid = '$horseinfo[garid]' and tribeid = '$horseinfo[tribeid]'");
$horse->MoveNext();
}


$horse = $db->Execute("SELECT * FROM $dbtables[combats] "
                     ."WHERE horses > 0 "
                     ."AND combat_id = '$uniqueid' "
                     ."AND side = 'D'");
$horseloss = 0;
while(!$horse->EOF){
$horseinfo = $horse->fields;

$posshorseloss = $horseinfo[startforce] - $horseinfo[curforce];
while($posshorseloss > 0){
$roll = 0;
$roll = rand(1,10);
if($roll > 2){
$horselossdef += 1;
}
$posshorseloss -= 1;
}
$horsesdef = 0;
if($horselossdef > $horseinfo[horses]){
$horselossdef = $horseinfo[horses];
}
$horsesdef = $horseinfo[horses] - $horselossdef;
$db->Execute("UPDATE $dbtables[combats] "
            ."set horses = '$horsesdef' "
            ."WHERE garid = '$horseinfo[garid]' "
            ."AND combat_id = '$uniqueid' "
            ."AND side = 'D'");
$db->Execute("UPDATE $dbtables[garrisons] set horses = '$horsesdef' WHERE garid = '$horseinfo[garid]' AND tribeid = '$horseinfo[tribeid]'");
$horse->MoveNext();
}


echo "<CENTER><TABLE WIDTH=\"80%\" BORDER=1><TR><TD><TABLE BORDER=0 WIDTH=\"100%\">";
//}
///////////////////Get the ending stats////////////////////////////////////////////////////////
$endtotalatt = 0;
$endtotaldef = 0;
$totalhorseatt = 0;
$totalhorsedef = 0;
$attfor = $db->Execute("SELECT * FROM $dbtables[combats] "
                      ."WHERE side = 'A' "
                      ."AND combat_id = '$uniqueid'");
while(!$attfor->EOF){
$attforce = $attfor->fields;
$heal = $db->Execute("SELECT * FROM $dbtables[skills] WHERE tribeid = '$attforce[tribeid]' AND abbr = 'heal'");
$healinfo = $heal->fields;
$healers = $healinfo[level] * 10;
$atthurt = 0;
$atthurt = $attforce[startforce] - $attforce[curforce];
$check = $db->Execute("SELECT * FROM $dbtables[garrisons] WHERE garid = '$attforce[garid]'");
$checkinfo = $check->fields;
if($checkinfo[force] < $atthurt){
$atthurt = $checkinfo[force];
}
$db->Execute("UPDATE $dbtables[garrisons] SET force = force - $atthurt where garid = '$attforce[garid]' and tribeid = '$attforce[tribeid]'");
if($healers > $atthurt){ $healers = $atthurt; }
while($healers > 0){
$randomheal = rand(1,10);
if($randomheal < 3){
$patchedatt += 1;
}
$healers -= 1;
}
$db->Execute("UPDATE $dbtables[tribes] SET actives = actives + $patchedatt where tribeid = '$attforce[tribeid]'");
$endtotalatt1 += $attforce[sector1];
$endtotalatt2 += $attforce[sector2];
$endtotalatt3 += $attforce[sector3];
$totalhorseatt += $attforce[horses];
$attfor->MoveNext();
}
$deffor = $db->Execute("SELECT * FROM $dbtables[combats] "
                      ."WHERE side = 'D' "
                      ."AND combat_id = '$uniqueid'");
while(!$deffor->EOF){
$defforce = $deffor->fields;
$heal = $db->Execute("SELECT * FROM $dbtables[skills] WHERE tribeid = '$defforce[tribeid]' AND abbr = 'heal'");
$healinfo = $heal->fields;
$healers = $healinfo[level] * 10;
$defhurt = 0;
$defhurt = $defforce[startforce] - $defforce[curforce];
$check = $db->Execute("SELECT * FROM $dbtables[garrisons] WHERE garid = '$defforce[garid]'");
$checkinfo = $check->fields;
if($checkinfo[force] < $defhurt){
$defhurt = $checkinfo[force];
}
$db->Execute("UPDATE $dbtables[garrisons] SET force = force - $defhurt where garid = '$defforce[garid]' and tribeid = '$defforce[tribeid]'");
if($healers > $defhurt){ $healers = $defhurt; }
while($healers > 0){
$randomheal = rand(1,10);
if($randomheal < 3){
$patcheddef += 1;
}
$healers -= 1;
}
$db->Execute("UPDATE $dbtables[tribes] SET actives = actives + $patcheddef where tribeid = '$defforce[tribeid]'");
$endtotaldef1 += $defforce[sector1];
$endtotaldef2 += $defforce[sector2];
$endtotaldef3 += $defforce[sector3];
$totalhorsedef += $defforce[horses];
$deffor->MoveNext();
}
if(!$patchedatt){ $patchedatt = 0; }
if(!$patcheddef){ $patcheddef = 0; }
if(!$endtotaldef1){ $endtotaldef1 = 0; }
if(!$endtotaldef2){ $endtotaldef2 = 0; }
if(!$endtotaldef3){ $endtotaldef3 = 0; }
if(!$endtotalatt1){ $endtotalatt1 = 0; }
if(!$endtotalatt2){ $endtotalatt2 = 0; }
if(!$endtotalatt3){ $endtotalatt3 = 0; }
$db->Execute("UPDATE $dbtables[tribes] SET move_pts = 0 WHERE tribeid = '$_SESSION[current_unit]'");
$db->Execute("INSERT INTO $dbtables[map_table] VALUES('','$_SESSION[current_unit]','$_SESSION[clanid]','$hexinfo[hex_id]','$stamp','$_SESSION[clanid]')");
$db->Execute("INSERT INTO $dbtables[movement_log] VALUES('','$_SESSION[clanid]','$_SESSION[clanid]','$hexinfo[hex_id]','$stamp')");
echo "<TR BGCOLOR=$color_header ALIGN=CENTER><TD COLSPAN=6><FONT SIZE=+2>After Action Report</FONT></TD></TR>";
echo "<TR BGCOLOR=$color_header ALIGN=CENTER><TD COLSPAN=3><FONT SIZE=+1>Total Attacking Forces:</FONT></TD><TD COLSPAN=3><FONT SIZE=+1>Total Defending Forces:</FONT></TD></TR>";
echo "<TR BGCOLOR=$color_header ALIGN=CENTER><TD><FONT SIZE=+1>Sector1</FONT></TD><TD><FONT SIZE=+1>Sector2</FONT></TD><TD><FONT SIZE=+1>Sector3</TD><TD><FONT SIZE=+1>Sector1</FONT></TD><TD><FONT SIZE=+1>Sector2</FONT></TD><TD><FONT SIZE=+1>Sector3</FONT></TD></TR>";
echo "<TR BGCOLOR=$color_line1 ALIGN=CENTER><TD>$endtotalatt1</TD><TD>$endtotalatt2</TD><TD>$endtotalatt3</TD><TD>$endtotaldef1</TD><TD>$endtotaldef2</TD><TD>$endtotaldef3</TD></TR>";
echo "<TR BGCOLOR=$color_line2 ALIGN=CENTER><TD COLSPAN=3>$totalhorseatt (horses)</TD><TD COLSPAN=3>$totalhorsedef (horses)</TD></TR>";
echo "<TR BGCOLOR=$color_line1 ALIGN=CENTER><TD COLSPAN=3>$patchedatt (healed)</TD><TD COLSPAN=3>$patcheddef (healed)</TD></TR>";
$attwin = 0;
$defwin = 0;
if($endtotaldef1 < 1){ $attwin++; }
if($endtotaldef2 < 1){ $attwin++; }
if($endtotaldef3 < 1){ $attwin++; }
if($endtotalatt1 < 1){ $defwin++; }
if($endtotalatt2 < 1){ $defwin++; }
if($endtotalatt3 < 1){ $defwin++; }

$gy = $db->Execute("SELECT * FROM $dbtables[game_date] WHERE type = 'year'");
$year = $gy->fields;
$gm = $db->Execute("SELECT * FROM $dbtables[game_date] WHERE type = 'month'");
$month = $gm->fields;
$tribe = $db->Execute("SELECT * FROM $dbtables[tribes] WHERE tribeid = '$_REQUEST[target]'");
$targetinfo = $tribe->fields;
$stamp = date("Y-m-d H:i:s");
$totalattkilled = $totalatt - ($endtotalatt1 + $endtotalatt2 + $endtotalatt3);
$totaldefkilled = $totaldef - ($endtotaldef1 + $endtotaldef2 + $endtotaldef3);

if($defwin > ($attwin + 1)){
echo "<TR BGCOLOR=$color_header ALIGN=CENTER><TD COLSPAN=6><FONT SIZE=+2>Defender Wins!</FONT></TD></TR>";
$deflog = "Combat Report: We were attacked by $totalatt warriors from $_SESSION[current_unit], but we were victorious, killing or wounding $totalattkilled of them. Our losses were $totaldefkilled.";
$db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$targetinfo[clanid]','$targetinfo[tribeid]','COMBAT','$stamp','$deflog')");
$db->Execute("INSERT INTO $dbtables[logs] "
            ."VALUES("
            ."'',"
            ."'$month[count]',"
            ."'$year[count]',"
            ."'0000',"
            ."'0000.00',"
            ."'WAR',"
            ."'$stamp',"
            ."'Combat Report: $_SESSION[current_unit] attacked $targetinfo[tribeid] ($totalattkilled/$totaldefkilled)')");


////////////////////////////////////////Now, the looting begins!///////////////////////////////////////////////////////

$bty1 = $db->Execute("SELECT * FROM $dbtables[combats] "
                    ."WHERE side = 'A' "
                    ."AND combat_id = '$uniqueid' "
                    ."AND sector1 < 1");
while(!$bty1->EOF){
$booty = $bty1->fields;
$randomweapon1 = rand(0, $booty[startsector1]);
$db->Execute("UPDATE $dbtables[products] set amount = amount + $randomweapon1 where proper = '$booty[weapon1]' AND tribeid = '$targetinfo[tribeid]'");
$randomweapon2 = rand(0, $booty[startsector1]);
$db->Execute("UPDATE $dbtables[products] set amount = amount + $randomweapon2 where proper = '$booty[weapon2]' AND tribeid = '$targetinfo[tribeid]'");
$randomhead = rand(0, $booty[startsector1]);
$db->Execute("UPDATE $dbtables[products] set amount = amount + $randomhead where proper = '$booty[head_armor]' AND tribeid = '$targetinfo[tribeid]'");
$randomtorso = rand(0, $booty[startsector1]);
$db->Execute("UPDATE $dbtables[products] set amount = amount + $randomtorso  where proper = '$booty[torso_armor]' AND tribeid = '$targetinfo[tribeid]'");
$randomotorso = rand(0, $booty[startsector1]);
$db->Execute("UPDATE $dbtables[products] set amount = amount + $randomotorso where proper = '$booty[otorso_armor]' AND tribeid = '$targetinfo[tribeid]'");
$randomlegs = rand(0, $booty[startsector1]);
$db->Execute("UPDATE $dbtables[products] set amount = amount + $randomlegs where proper = '$booty[legs_armor]' AND tribeid = '$targetinfo[tribeid]'");
$randomshield = rand(0, $booty[startsector1]);
$db->Execute("UPDATE $dbtables[products] set amount = amount + $randomshield where proper = '$booty[shield]' AND tribeid = '$targetinfo[tribeid]'");
$randomhorsea = rand(0, $booty[startsector1]);
$db->Execute("UPDATE $dbtables[products] set amount = amount + $randomhorsea where proper = '$booty[horse_armor]' AND tribeid = '$targetinfo[tribeid]'");
if($booty[horses] > 0){
$posshorsegrab = round($booty[startsector1]/5);
$randomhorse = rand(0, $posshorsegrab);
$db->Execute("UPDATE $dbtables[livestock] set amount = amount + $randomhorse where type = 'Horses' AND tribeid = '$targetinfo[tribeid]'");
}
$potentialpow = $booty[startsector1] - $booty[sector1];
$totalpow = rand(0, $potentialpow);
$db->Execute("UPDATE $dbtables[tribes] set slavepop = slavepop + $totalpow where tribeid = '$targetinfo[tribeid]'");
$bty1->MoveNext();
}
$bty2 = $db->Execute("SELECT * FROM $dbtables[combats] "
                    ."WHERE side = 'A' "
                    ."AND combat_id = '$uniqueid' "
                    ."AND sector2 < 1");
while(!$bty2->EOF){
$booty = $bty2->fields;
$randomweapon1 = rand(0, $booty[startsector2]);
$db->Execute("UPDATE $dbtables[products] set amount = amount + $randomweapon1 where proper = '$booty[weapon1]' AND tribeid = '$targetinfo[tribeid]'");
$randomweapon2 = rand(0, $booty[startsector2]);
$db->Execute("UPDATE $dbtables[products] set amount = amount + $randomweapon2 where proper = '$booty[weapon2]' AND tribeid = '$targetinfo[tribeid]'");
$randomhead = rand(0, $booty[startsector2]);
$db->Execute("UPDATE $dbtables[products] set amount = amount + $randomhead where proper = '$booty[head_armor]' AND tribeid = '$targetinfo[tribeid]'");
$randomtorso = rand(0, $booty[startsector2]);
$db->Execute("UPDATE $dbtables[products] set amount = amount + $randomtorso  where proper = '$booty[torso_armor]' AND tribeid = '$targetinfo[tribeid]'");
$randomotorso = rand(0, $booty[startsector2]);
$db->Execute("UPDATE $dbtables[products] set amount = amount + $randomotorso where proper = '$booty[otorso_armor]' AND tribeid = '$targetinfo[tribeid]'");
$randomlegs = rand(0, $booty[startsector2]);
$db->Execute("UPDATE $dbtables[products] set amount = amount + $randomlegs where proper = '$booty[legs_armor]' AND tribeid = '$targetinfo[tribeid]'");
$randomshield = rand(0, $booty[startsector2]);
$db->Execute("UPDATE $dbtables[products] set amount = amount + $randomshield where proper = '$booty[shield]' AND tribeid = '$targetinfo[tribeid]'");
$randomhorsea = rand(0, $booty[startsector2]);
$db->Execute("UPDATE $dbtables[products] set amount = amount + $randomhorsea where proper = '$booty[horse_armor]' AND tribeid = '$targetinfo[tribeid]'");
if($booty[horses] > 0){
$posshorsegrab = round($booty[startsector2]/5);
$randomhorse = rand(0, $posshorsegrab);
$db->Execute("UPDATE $dbtables[livestock] set amount = amount + $randomhorse where type = 'Horses' AND tribeid = '$targetinfo[tribeid]'");
}
$potentialpow = $booty[startsector2] - $booty[sector2];
$totalpow = rand(0, $potentialpow);
$db->Execute("UPDATE $dbtables[tribes] set slavepop = slavepop + $totalpow where tribeid = '$targetinfo[tribeid]'");
$bty2->MoveNext();
}
$bty3 = $db->Execute("SELECT * FROM $dbtables[combats] "
                    ."WHERE side = 'A' "
                    ."AND combat_id = '$uniqueid' "
                    ."AND sector3 < 1");
while(!$bty3->EOF){
$booty = $bty3->fields;
$randomweapon1 = rand(0, $booty[startsector3]);
$db->Execute("UPDATE $dbtables[products] set amount = amount + $randomweapon1 where proper = '$booty[weapon1]' AND tribeid = '$targetinfo[tribeid]'");
$randomweapon2 = rand(0, $booty[startsector3]);
$db->Execute("UPDATE $dbtables[products] set amount = amount + $randomweapon2 where proper = '$booty[weapon2]' AND tribeid = '$targetinfo[tribeid]'");
$randomhead = rand(0, $booty[startsector3]);
$db->Execute("UPDATE $dbtables[products] set amount = amount + $randomhead where proper = '$booty[head_armor]' AND tribeid = '$targetinfo[tribeid]'");
$randomtorso = rand(0, $booty[startsector3]);
$db->Execute("UPDATE $dbtables[products] set amount = amount + $randomtorso  where proper = '$booty[torso_armor]' AND tribeid = '$targetinfo[tribeid]'");
$randomotorso = rand(0, $booty[startsector3]);
$db->Execute("UPDATE $dbtables[products] set amount = amount + $randomotorso where proper = '$booty[otorso_armor]' AND tribeid = '$targetinfo[tribeid]'");
$randomlegs = rand(0, $booty[startsector3]);
$db->Execute("UPDATE $dbtables[products] set amount = amount + $randomlegs where proper = '$booty[legs_armor]' AND tribeid = '$targetinfo[tribeid]'");
$randomshield = rand(0, $booty[startsector3]);
$db->Execute("UPDATE $dbtables[products] set amount = amount + $randomshield where proper = '$booty[shield]' AND tribeid = '$targetinfo[tribeid]'");
$randomhorsea = rand(0, $booty[startsector3]);
$db->Execute("UPDATE $dbtables[products] set amount = amount + $randomhorsea where proper = '$booty[horse_armor]' AND tribeid = '$targetinfo[tribeid]'");
if($booty[horses] > 0){
$posshorsegrab = round($booty[startsector3]/5);
$randomhorse = rand(0, $posshorsegrab);
$db->Execute("UPDATE $dbtables[livestock] set amount = amount + $randomhorse where type = 'Horses' AND tribeid = '$targetinfo[tribeid]'");
}
$potentialpow = $booty[startsector3] - $booty[sector3];
$totalpow = rand(0, $potentialpow);
$db->Execute("UPDATE $dbtables[tribes] set slavepop = slavepop + $totalpow where tribeid = '$targetinfo[tribeid]'");
$bty3->MoveNext();
}
$db->Execute("UPDATE $dbtables[garrisons] SET exp = exp + '.04', experience = experience + 6 WHERE tribeid = '$targetinfo[tribeid]'");
$db->Execute("UPDATE $dbtables[tribes] SET morale = morale + .04 WHERE tribeid = '$targetinfo[tribeid]'");
$db->Execute("UPDATE $dbtables[garrisons] SET exp = exp + '.02', experience = experience + 2 WHERE tribeid = '$_SESSION[current_unit]'");
$db->Execute("UPDATE $dbtables[tribes] SET morale = morale - .04 WHERE tribeid = '$_SESSION[current_unit]'");
$db->Execute("UPDATE $dbtables[tribes] SET move_pts = 0 WHERE tribeid = '$_SESSION[current_unit]'");
$db->Execute("INSERT INTO $dbtables[map_table] VALUES('','$_SESSION[current_unit]','$_SESSION[clanid]','$hexinfo[hex_id]','$stamp','$_SESSION[clanid]')");
$db->Execute("INSERT INTO $dbtables[movement_log] VALUES('','$_SESSION[clanid]','$_SESSION[clanid]','$hexinfo[hex_id]','$stamp')");
}



elseif($attwin > ($defwin + 1)){
echo "<TR BGCOLOR=$color_header ALIGN=CENTER><TD COLSPAN=6><FONT SIZE=+2>Attacker Wins!</FONT></TD></TR>";
$deflog = "Combat Report: We were attacked by $totalatt warriors from $_SESSION[current_unit], and they overran us. We did manage to kill or wound $totalattkilled of them, but they also killed or wounded $totaldefkilled of our warriors.";
$db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$targetinfo[clanid]','$targetinfo[tribeid]','COMBAT','$stamp','$deflog')");
$db->Execute("UPDATE $dbtables[tribes] set hex_id = '$targetinfo[hex_id]' where tribeid = '$_SESSION[current_unit]'");
$db->Execute("UPDATE $dbtables[garrisons] set hex_id = '$targetinfo[hex_id]' where tribeid = '$_SESSION[current_unit]'");
////////////////////////////////////////Now, the looting begins!///////////////////////////////////////////////////////

$bty1 = $db->Execute("SELECT * FROM $dbtables[combats] "
                    ."WHERE side = 'D' "
                    ."AND combat_id = '$uniqueid' "
                    ."AND sector1 < 1");
while(!$bty1->EOF){
$booty = $bty1->fields;
$randomweapon1 = rand(0, $booty[startsector1]);
$db->Execute("UPDATE $dbtables[products] set amount = amount + $randomweapon1 where proper = '$booty[weapon1]' AND tribeid = '$_SESSION[current_unit]'");
$randomweapon2 = rand(0, $booty[startsector1]);
$db->Execute("UPDATE $dbtables[products] set amount = amount + $randomweapon2 where proper = '$booty[weapon2]' AND tribeid = '$_SESSION[current_unit]'");
$randomhead = rand(0, $booty[startsector1]);
$db->Execute("UPDATE $dbtables[products] set amount = amount + $randomhead where proper = '$booty[head_armor]' AND tribeid = '$_SESSION[current_unit]'");
$randomtorso = rand(0, $booty[startsector1]);
$db->Execute("UPDATE $dbtables[products] set amount = amount + $randomtorso  where proper = '$booty[torso_armor]' AND tribeid = '$_SESSION[current_unit]'");
$randomotorso = rand(0, $booty[startsector1]);
$db->Execute("UPDATE $dbtables[products] set amount = amount + $randomotorso where proper = '$booty[otorso_armor]' AND tribeid = '$_SESSION[current_unit]'");
$randomlegs = rand(0, $booty[startsector1]);
$db->Execute("UPDATE $dbtables[products] set amount = amount + $randomlegs where proper = '$booty[legs_armor]' AND tribeid = '$_SESSION[current_unit]'");
$randomshield = rand(0, $booty[startsector1]);
$db->Execute("UPDATE $dbtables[products] set amount = amount + $randomshield where proper = '$booty[shield]' AND tribeid = '$_SESSION[current_unit]'");
$randomhorsea = rand(0, $booty[startsector1]);
$db->Execute("UPDATE $dbtables[products] set amount = amount + $randomhorsea where proper = '$booty[horse_armor]' AND tribeid = '$_SESSION[current_unit]'");
if($booty[horses] > 0){
$posshorsegrab = round($booty[startsector1]/5);
$randomhorse = rand(0, $posshorsegrab);
$db->Execute("UPDATE $dbtables[livestock] set amount = amount + $randomhorse where type = 'Horses' AND tribeid = '$_SESSION[current_unit]'");
}
$potentialpow = $booty[startsector1] - $booty[sector1];
$totalpow = rand(0, $potentialpow);
$db->Execute("UPDATE $dbtables[tribes] set slavepop = slavepop + $totalpow where tribeid = '$_SESSION[current_unit]'");
$bty1->MoveNext();
}
$bty2 = $db->Execute("SELECT * FROM $dbtables[combats] "
                    ."WHERE side = 'D' "
                    ."AND combat_id = '$uniqueid' "
                    ."AND sector2 < 1");
while(!$bty2->EOF){
$booty = $bty2->fields;
$randomweapon1 = rand(0, $booty[startsector2]);
$db->Execute("UPDATE $dbtables[products] set amount = amount + $randomweapon1 where proper = '$booty[weapon1]' AND tribeid = '$_SESSION[current_unit]'");
$randomweapon2 = rand(0, $booty[startsector2]);
$db->Execute("UPDATE $dbtables[products] set amount = amount + $randomweapon2 where proper = '$booty[weapon2]' AND tribeid = '$_SESSION[current_unit]'");
$randomhead = rand(0, $booty[startsector2]);
$db->Execute("UPDATE $dbtables[products] set amount = amount + $randomhead where proper = '$booty[head_armor]' AND tribeid = '$_SESSION[current_unit]'");
$randomtorso = rand(0, $booty[startsector2]);
$db->Execute("UPDATE $dbtables[products] set amount = amount + $randomtorso  where proper = '$booty[torso_armor]' AND tribeid = '$_SESSION[current_unit]'");
$randomotorso = rand(0, $booty[startsector2]);
$db->Execute("UPDATE $dbtables[products] set amount = amount + $randomotorso where proper = '$booty[otorso_armor]' AND tribeid = '$_SESSION[current_unit]'");
$randomlegs = rand(0, $booty[startsector2]);
$db->Execute("UPDATE $dbtables[products] set amount = amount + $randomlegs where proper = '$booty[legs_armor]' AND tribeid = '$_SESSION[current_unit]'");
$randomshield = rand(0, $booty[startsector2]);
$db->Execute("UPDATE $dbtables[products] set amount = amount + $randomshield where proper = '$booty[shield]' AND tribeid = '$_SESSION[current_unit]'");
$randomhorsea = rand(0, $booty[startsector2]);
$db->Execute("UPDATE $dbtables[products] set amount = amount + $randomhorsea where proper = '$booty[horse_armor]' AND tribeid = '$_SESSION[current_unit]'");
if($booty[horses] > 0){
$posshorsegrab = round($booty[startsector2]/5);
$randomhorse = rand(0, $posshorsegrab);
$db->Execute("UPDATE $dbtables[livestock] set amount = amount + $randomhorse where type = 'Horses' AND tribeid = '$_SESSION[current_unit]'");
}
$potentialpow = $booty[startsector2] - $booty[sector2];
$totalpow = rand(0, $potentialpow);
$db->Execute("UPDATE $dbtables[tribes] set slavepop = slavepop + $totalpow where tribeid = '$_SESSION[current_unit]'");
$bty2->MoveNext();
}
$bty3 = $db->Execute("SELECT * FROM $dbtables[combats] "
                    ."WHERE side = 'D' "
                    ."AND combat_id = '$uniqueid' "
                    ."AND sector3 < 1");
while(!$bty3->EOF){
$booty = $bty3->fields;
$randomweapon1 = rand(0, $booty[startsector3]);
$db->Execute("UPDATE $dbtables[products] set amount = amount + $randomweapon1 where proper = '$booty[weapon1]' AND tribeid = '$_SESSION[current_unit]'");
$randomweapon2 = rand(0, $booty[startsector3]);
$db->Execute("UPDATE $dbtables[products] set amount = amount + $randomweapon2 where proper = '$booty[weapon2]' AND tribeid = '$_SESSION[current_unit]'");
$randomhead = rand(0, $booty[startsector3]);
$db->Execute("UPDATE $dbtables[products] set amount = amount + $randomhead where proper = '$booty[head_armor]' AND tribeid = '$_SESSION[current_unit]'");
$randomtorso = rand(0, $booty[startsector3]);
$db->Execute("UPDATE $dbtables[products] set amount = amount + $randomtorso  where proper = '$booty[torso_armor]' AND tribeid = '$_SESSION[current_unit]'");
$randomotorso = rand(0, $booty[startsector3]);
$db->Execute("UPDATE $dbtables[products] set amount = amount + $randomotorso where proper = '$booty[otorso_armor]' AND tribeid = '$_SESSION[current_unit]'");
$randomlegs = rand(0, $booty[startsector3]);
$db->Execute("UPDATE $dbtables[products] set amount = amount + $randomlegs where proper = '$booty[legs_armor]' AND tribeid = '$_SESSION[current_unit]'");
$randomshield = rand(0, $booty[startsector3]);
$db->Execute("UPDATE $dbtables[products] set amount = amount + $randomshield where proper = '$booty[shield]' AND tribeid = '$_SESSION[current_unit]'");
$randomhorsea = rand(0, $booty[startsector3]);
$db->Execute("UPDATE $dbtables[products] set amount = amount + $randomhorsea where proper = '$booty[horse_armor]' AND tribeid = '$_SESSION[current_unit]'");
if($booty[horses] > 0){
$posshorsegrab = round($booty[startsector3]/5);
$randomhorse = rand(0, $posshorsegrab);
$db->Execute("UPDATE $dbtables[livestock] set amount = amount + $randomhorse where type = 'Horses' AND tribeid = '$_SESSION[current_unit]'");
}
$potentialpow = $booty[startsector3] - $booty[sector3];
$totalpow = rand(0, $potentialpow);
$db->Execute("UPDATE $dbtables[tribes] set slavepop = slavepop + $totalpow where tribeid = '$_SESSION[current_unit]'");
$bty3->MoveNext();
}
$db->Execute("UPDATE $dbtables[garrisons] SET exp = exp + '.04', experience = experience + 6 WHERE tribeid = '$_SESSION[current_unit]'");
$db->Execute("UPDATE $dbtables[tribes] SET morale = morale + .04 WHERE tribeid = '$_SESSION[current_unit]'");
$db->Execute("UPDATE $dbtables[garrisons] SET exp = exp + '.02', experience = experience + 2 WHERE tribeid = '$_SESSION[current_unit]'");
$db->Execute("UPDATE $dbtables[tribes] SET morale = morale - .04 WHERE tribeid = '$targetinfo[tribeid]'");


//////////////////////////////////////////////Now, Loot the village!!!///////////////////////////////////////////////

if($attwin > 2){
$slskl = $db->Execute("SELECT * FROM $dbtables[skills] WHERE tribeid = '$_SESSION[current_unit]' AND abbr = 'slv'");
$slavery = $slskl->fields;
$shack = $db->Execute("SELECT * FROM $dbtables[products] WHERE proper = 'Shackles' and tribeid = '$_SESSION[current_unit]'");
$shackles = $shack->fields;
$endtotalatt = $endtotalatt1 + $endtotalatt2 + $endtotalatt3;
if($shackles[amount] > $endtotalatt){
$shackles[amount] = $endtotalatt;
}
while($targetinfo[slavepop] > 0 && $slavetakers > 0){
$targetinfo[slavepop] -= 1;
$totalslavestaken += 1;
$slavetakers -= 1;
}
$slavetakers = $endtotalatt + $shackles[amount];
while($targetinfo[inactivepop] > 0 && $slavetakers > 0){
$targetinfo[inactivepop] -= 1;
$totalslavestaken += 1;
$slavetakers -= 1;
}
while($targetinfo[activepop] > 0 && $slavetakers > 0 ){
$targetinfo[activepop] -= 1;
$totalslavestaken += 1;
$slavetakers -= 1;
}
$db->Execute("UPDATE $dbtables[tribes] set inactivepop = '$targetinfo[inactivepop]', slavepop = '$targetinfo[slavepop]', activepop = '$targetinfo[activepop]' WHERE tribeid = '$targetinfo[tribeid]'");
$db->Execute("UPDATE $dbtables[tribes] set slavepop = slavepop + '$totalslavestaken' where tribeid = '$_SESSION[current_unit]'");
if($totalslavestaken > 0){
echo "<TR BGCOLOR=$color_header ALIGN=CENTER><TD COLSPAN=6>We captured $totalslavestaken villagers as slaves!</TD></TR>";
}
else {
echo "<TR BGCOLOR=$color_header ALIGN=CENTER><TD COLSPAN=6>We took no villagers during the looting.</TD></TR>";
}

while($endtotalatt > 0){
$randloot = rand(1,3);
if($randloot == 1){
$prodchance = $db->Execute("SELECT COUNT(*) as count FROM $dbtables[products] WHERE tribeid = '$targetinfo[tribeid]' AND amount > 0");
$chance = $prodchance->fields;
$chance[count] -= 1;
$randprod = rand(0, $chance[count]);
$prod = $db->Execute("SELECT * FROM $dbtables[products] WHERE tribeid = '$targetinfo[tribeid]' AND amount > 0 LIMIT $randprod, 1");
$product = $prod->fields;
$db->Execute("UPDATE $dbtables[products] SET amount = amount - 1 WHERE tribeid = '$targetinfo[tribeid]' AND proper = '$product[proper]'");
$db->Execute("UPDATE $dbtables[products] SET amount = amount + 1 WHERE tribeid = '$_SESSION[current_unit]' AND proper = '$product[proper]'"); 
}
elseif($randloot == 2){
$livchance = $db->Execute("SELECT COUNT(*) as count FROM $dbtables[livestock] WHERE tribeid = '$targetinfo[tribeid]' AND amount > 0");
$chance = $livchance->fields;
$chance[count] -= 1;
$randliv = rand(0, $chance[count]);
$liv = $db->Execute("SELECT * FROM $dbtables[livestock] WHERE tribeid = '$targetinfo[tribeid]' AND amount > 0 LIMIT $randliv, 1");
$livestock = $liv->fields;
$db->Execute("UPDATE $dbtables[livestock] SET amount = amount - 1 WHERE tribeid = '$targetinfo[tribeid]' AND type = '$livestock[type]'");
$db->Execute("UPDATE $dbtables[livestock] SET amount = amount + 1 WHERE tribeid = '$_SESSION[current_unit]' AND type = '$livestock[type]'");
}
else{
$reschance = $db->Execute("SELECT COUNT(*) as count FROM $dbtables[resources] WHERE tribeid = '$targetinfo[tribeid]' AND amount > 0");
$chance = $reschance->fields;
$chance[count] -= 1;
$randres = rand(0, $chance[count]);
$res = $db->Execute("SELECT * FROM $dbtables[resources] WHERE tribeid = '$targetinfo[tribeid]' AND amount > 0 LIMIT $randres, 1");
$resources = $res->fields;
$db->Execute("UPDATE $dbtables[resources] SET amount = amount - 1 WHERE tribeid = '$targetinfo[tribeid]' AND long_name = '$resources[long_name]'");
$db->Execute("UPDATE $dbtables[resources] SET amount = amount + 1 WHERE tribeid = '$_SESSION[current_unit]' AND long_name = '$resources[long_name]'");
}
$endtotalatt -= 1;
}
}
$db->Execute("UPDATE $dbtables[tribes] SET move_pts = 0 WHERE tribeid = '$_SESSION[current_unit]'");
$db->Execute("INSERT INTO $dbtables[map_table] VALUES('','$_SESSION[current_unit]','$_SESSION[clanid]','$hexinfo[hex_id]','$stamp','$_SESSION[clanid]')");
$db->Execute("INSERT INTO $dbtables[movement_log] VALUES('','$_SESSION[clanid]','$_SESSION[clanid]','$hexinfo[hex_id]','$stamp')");
}
else{
echo "<TR BGCOLOR=$color_header ALIGN=CENTER><TD COLSPAN=6><FONT SIZE=+2>No Winner. Both sides withdraw.</FONT></TD></TR>";
$db->Execute("UPDATE $dbtables[tribes] SET move_pts = 0 WHERE tribeid = '$_SESSION[current_unit]'");
$db->Execute("INSERT INTO $dbtables[map_table] VALUES('','$_SESSION[current_unit]','$_SESSION[clanid]','$hexinfo[hex_id]','$stamp','$_SESSION[clanid]')");
$db->Execute("INSERT INTO $dbtables[movement_log] VALUES('','$_SESSION[clanid]','$_SESSION[clanid]','$hexinfo[hex_id]','$stamp')");
}


echo "</TABLE></TD></TR></TABLE></CENTER>";

TEXT_GOTOMAIN();





}
}
page_footer();

?> 
