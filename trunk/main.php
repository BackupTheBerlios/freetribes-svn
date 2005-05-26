<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: main.php
error_reporting  (E_ERROR | E_WARNING | E_PARSE | !E_NOTICE);

session_start();
header("Cache-control: private");
include("config.php");
include("gui/pad_row.php");
include("gui/menu_option.php");
include ("gui/table1.php");
include ("gui/table2.php");
include ("gui/td_headers.php");


include("game_time.php");


page_header("");

connectdb();


//-------------------------------------------------------------------------------------------------

$clanid = $_SESSION['clanid'];
$curr_unit = $_SESSION['current_unit'];

$ch = $db->Execute("SELECT * FROM $dbtables[chiefs] WHERE clanid = '$clanid'");
$chiefinfo = $ch->fields;

$res = $db->Execute("SELECT clanname FROM $dbtables[clans] WHERE clanid = '$clanid'");
$resclan = $res->fields;

$_SESSION['clanname'] = $resclan['clanname'];
if(!empty($_GET['id']))
{
    $tribe_id=$_GET['id'];
    $restrib = $db->Execute("SELECT * FROM $dbtables[tribes] WHERE clanid = '$clanid' AND tribeid = '$tribe_id'");
    $tribeinfo = $restrib->fields;
    if($tribeinfo['clanid'] == $clanid)
    {
        $_SESSION['current_unit'] = $tribeinfo['tribeid'];
    }
    else
    {
        $res2 = $db->Execute("SELECT * FROM $dbtables[tribes] WHERE clanid = '$clanid'");
        $tribeinfo = $res2->fields;
        $_SESSION['current_unit'] = $tribeinfo['tribeid'];

    }
}
if(!empty($_SESSION['current_unit']))
{
    $res2 = $db->Execute("SELECT * FROM $dbtables[tribes] WHERE tribeid = '$_SESSION[current_unit]'");
    $tribeinfo = $res2->fields;
    $_SESSION['current_unit'] = $tribeinfo['tribeid'];
}
else
{
    $res2 = $db->Execute("SELECT * FROM $dbtables[tribes] WHERE clanid = '$clanid'");
    $tribeinfo = $res2->fields;
    $_SESSION['current_unit'] = $tribeinfo['tribeid'];
}
$skillsunavail = false;


$_SESSION['hex_id'] = $tribeinfo['hex_id'];
$_SESSION['clanid'] = $tribeinfo['clanid'];


$res4 = $db->Execute("SELECT * FROM $dbtables[hexes] "
                    ."WHERE hex_id = '$tribeinfo[hex_id]'");
$hexinfo = $res4->fields;

$db->Execute("UPDATE $dbtables[mapping] "
            ."SET `clanid_$_SESSION[clanid]` = '1'"
            ."WHERE hex_id = '$tribeinfo[hex_id]'"
            ."AND `clanid_$_SESSION[clanid]` = '0'");

$neighbors = array($hexinfo['nw'], $hexinfo['n'], $hexinfo['ne'], $hexinfo['w'], $hexinfo['e'], $hexinfo['sw'], $hexinfo['s'], $hexinfo['se']);
$mapped = array();
$hexes = array();

foreach( $neighbors as $map )
{
    $result3 = $db->Execute("SELECT hex_id FROM $dbtables[mapping] "
                           ."WHERE hex_id = '$map' "
                           ."AND `clanid_$_SESSION[clanid]` > '0'");
    $row3 = $result3->fields;

    $result4 = $db->Execute("SELECT * FROM $dbtables[hexes] "
                           ."WHERE hex_id = '$map'");
    $row4 = $result4->fields;


// ^^^ Multiple IF statements replaced with SWITCH

    switch ($row4['terrain'])
        {
        case 'lcm':
        case 'ljm':
        case 'o':
        case 'l':
        case 'hsm':
            $mapped[] = array($map);
            $db->Execute("UPDATE $dbtables[mapping] "
                                    ."SET `clanid_$_SESSION[clanid]` = '1' "
                                    ."WHERE hex_id = '$row4[hex_id]'"
                                    ."AND `clanid_$_SESSION[clanid]` = '0'");
            break;
        }

    if( $row3['hex_id'] == '' )
    {
        $mapped[] = array($row3);
    }
    elseif( !$row3['hex_id'] == '' )
    {
    $mapped[] = array_merge($row3);
    }
}

$messres = $db->Execute("SELECT count(*) as count FROM $dbtables[messages] "
                       ."WHERE recp_id ='$_SESSION[clanid]' AND notified='N'");
$messages = $messres->fields;

if( $messages['count'] > 0 )
{
        echo "<script language=\"javascript\" type=\"text/javascript\">";
        echo "alert(\"You have $messages[count] messages waiting for you.\")</SCRIPT>";
}

$db->Execute("UPDATE $dbtables[messages] "
            ."SET notified='Y' "
            ."WHERE recp_id = '$_SESSION[clanid]'");

echo "<TABLE BORDER=0 CELLPADDING=0 align=center width=\"40%\">";
echo "<TR ALIGN=CENTER><TD>";
if( $_SESSION['tooltip'] == '1' )
{
echo "<TABLE class=table1_td_cc BORDER=0 CELLPADDING=0 WIDTH=100% "
     ."onmouseover=\"return overlib('This section contains information regarding the terrain of the "
     ."map hex your unit is currently in, what resources are currently available here, as well as the "
     ."date, weather, and season. The mini map is also how you move your unit around, clicking on the "
     ."map tiles that you wish to travel to. Your unit is always located in the center map tile.');\" "
     ."onmouseout=\"nd();\">";
}
else
{
echo "<TABLE class=table1_td_cc BORDER=0 CELLPADDING=0 WIDTH=100%>";
}
echo "<TR CLASS=color_header>"
        ."<TD CLASS=heading_rounded COLSPAN=3>&nbsp;"
        ."MINI MAP"
        ."&nbsp;"
        ."</TD></TR>";
echo "<TR><TD WIDTH=\"33%\">";
echo "<TABLE BORDER=0 CELLPADDING=0 ALIGN=CENTER><TR><TD>";
$hextype = $db->Execute("SELECT name FROM $dbtables[gd_terrain] "
                       ."WHERE abbr ='".$hexinfo['terrain']."'");
$hextype = $hextype->fields['name'];

echo "<FONT COLOR=BLACK SIZE=-2>$hextype</FONT>";
echo "</TD></TR>";
echo "<TR><TD>";

$reshex = $db->Execute("SELECT produce, name FROM $dbtables[gd_resources] "
                       ."WHERE name ='".$hexinfo['res_type']."'");
$reshex = $reshex->fields['produce'];
$clanmap = "clanid_" . $_SESSION['clanid'];
$resf = $db->Execute("SELECT * FROM $dbtables[mapping] "
                       ."WHERE `$clanmap` <= '1' "
                       ."AND hex_id = '$hexinfo[hex_id]'");
$resfind = $resf->fields[$clanmap];

if( $reshex == '' || !$resf->EOF )
{
    $reshex = 'No Minerals';
}


echo "<FONT COLOR=BLACK SIZE=-2><CENTER>$reshex</CENTER></FONT>";
echo "</TD></TR>";
echo "</TABLE>";
echo "</TD><TD WIDTH=\"33%\">";

table2_fancy_open (0, 0, "");

echo "<TABLE BORDER=1 CELLPADDING=0 CELLSPACING=0 ALIGN=CENTER><TR>";

$direction = array('nw', 'n', 'ne', 'w', 'e', 'sw', 's', 'se');

$map_chief = array();

foreach ($direction as $direct)
{
    foreach( $mapped as $hexed )
    {
        foreach( $hexed as $hex )
        {
            if( $hex == $hexinfo[$direct] )
            {
                $map_chief[$direct] = true;
            }
        }
    }
}


// MAKE MINIMAP

$top_dir = array('nw', 'n', 'ne', 'w');

$hex_count = 0; // ^^^ count how many hexes we have done

foreach ($top_dir as $direct)
{
    echo "<TD>";
        $clanmap = "clanid_" . $clanid;
    if( ISSET($map_chief[$direct]) )
    {
        $res = $db->Execute("SELECT hex_id, res_type, terrain FROM $dbtables[hexes] "
                           ."WHERE hex_id = '".$hexinfo[$direct]."'");
        $this_hex = $res->fields;

        $res = $db->Execute("SELECT name FROM $dbtables[gd_terrain] "
                            ."WHERE abbr='".$this_hex['terrain']."'");
        $terrain = $res->fields['name'];
                $prosp = $db->Execute("SELECT $clanmap FROM $dbtables[mapping] "
                                     ."WHERE hex_id = '$this_hex[hex_id]'");
                $prospct = $prosp->fields[$clanmap];
        if ($this_hex['res_type'] <> "" && $prospct > 1 )
        {
            $res = $db->Execute("SELECT produce FROM $dbtables[gd_resources] "
                                ."WHERE name='".$this_hex['res_type']."'");
            $resource = $res->fields['produce'];
                        $resource2 = $this_hex['res_type'];
            $resource = " \n ".$resource;
        }
        else
        {
            $resource = "";
                        $resource2 = "";
        }

        if( $tribeinfo['goods_tribe'] == $tribeinfo['tribeid'] )
        {
                    if( $this_hex['hex_id'] )
                    {
                echo "<A HREF=move.php?dest=" . $this_hex['hex_id'] . ">";
                        echo "<IMG SRC=images/" . $this_hex['terrain'] . $resource2;
            echo ".png border=0 title=\" $terrain$resource \"></A>";
                    }
                    else
                    {
                        echo "<IMG SRC=images/blank.png>";
                    }
        }
        else
        {
                    if( $this_hex['hex_id'] )
                    {
                        echo "<IMG SRC=images/" . $this_hex['terrain'] . $resource2;
            echo ".png border=0 title=\" $terrain$resource \">";
                    }
                    else
                    {
                        echo "<IMG SRC=images/blank.png>";
                    }
        }
    }
    else
    {
        $res = $db->Execute("SELECT hex_id FROM $dbtables[hexes] "
                           ."WHERE hex_id = '".$hexinfo[$direct]."'");
        $this_hex = $res->fields;
            if( $this_hex['hex_id'] )
            {
        if( $tribeinfo['goods_tribe'] == $tribeinfo['tribeid'] )
        {
            echo "<A HREF=move.php?dest=" . $this_hex['hex_id'] . ">";
            echo "<IMG SRC=images/unknown.png border=0 title=\" Terra Incognita \"></A>";
        }
        else
        {
            echo "<IMG SRC=images/unknown.png border=0 title=\" Terra Incognita \">";
        }
            }
            else
            {
                echo "<IMG SRC=images/blank.png>";
            }
    }
    echo "</TD>";
    $hex_count += 1;
    if ($hex_count == 3)  // If 3 hexes done then move to next row
    {
        echo "</TR><TR>";
    }

}


/////////////////////////////////////////// Hex the tribe is in (center)

$clanmap = "clanid_" . $clanid;

$res = $db->Execute("SELECT name FROM $dbtables[gd_terrain] "
                    ."WHERE abbr='".$hexinfo['terrain']."'");
$terrain = $res->fields['name'];
                $prosp = $db->Execute("SELECT $clanmap, hex_id FROM $dbtables[mapping] "
                                     ."WHERE hex_id = '$hexinfo[hex_id]' "
                                     ."AND $clanmap > '1'");
                $prospct = $prosp->fields[$clanmap];

if ($hexinfo['res_type'] <> "" && $prospct )
{
    $res = $db->Execute("SELECT produce, name FROM $dbtables[gd_resources] "
                        ."WHERE name='".$hexinfo['res_type']."'");
    $resource = $res->fields['produce'];
        $resource2 = $hexinfo['res_type'];
    $resource = " \n ".$resource;
}
else
{
    $resource = "";
        $resource2 = "";
}

echo "<TD><img src=images/";
echo $hexinfo['terrain'].$resource2;
echo ".png border=0 title=\" $terrain$resource \"></TD>";
echo "</A>";
/////////////////////////////////////////////// End of center

$top_dir = array('e', 'sw', 's', 'se');

$hex_count = 0; // count how many hexes we have done

foreach ($top_dir as $direct)
{
    echo "<TD>";
    if( ISSET($map_chief[$direct]) )
    {
                $clanmap = "clanid_" . $clanid;
        $res = $db->Execute("SELECT hex_id, res_type, terrain FROM $dbtables[hexes] "
                           ."WHERE hex_id = '".$hexinfo[$direct]."'");
        $this_hex = $res->fields;

        $res = $db->Execute("SELECT name FROM $dbtables[gd_terrain] "
                            ."WHERE abbr='".$this_hex[terrain]."'");
        $terrain = $res->fields[name];
                $prosp = $db->Execute("SELECT $clanmap FROM $dbtables[mapping] "
                                     ."WHERE hex_id = '$this_hex[hex_id]'");
                $prospct = $prosp->fields;
        if ($this_hex[res_type] <> "" && $prospct[$clanmap] > 1 )
        {
            $res = $db->Execute("SELECT produce FROM $dbtables[gd_resources] "
                                ."WHERE name='".$this_hex[res_type]."'");
            $resource = $res->fields[produce];
                        $resource2 = $this_hex[res_type];
            $resource = " \n ".$resource;
        }
        else
        {
            $resource = "";
                        $resource2 = "";
        }
            if( $this_hex['hex_id'] )
            {
                if( $tribeinfo['goods_tribe'] == $tribeinfo['tribeid'] )
        {
            echo "<A HREF=move.php?dest=" . $this_hex['hex_id'] . ">";
            //echo "<IMG SRC=images/" . $this_hex[terrain] . $this_hex[res_type];
                        echo "<IMG SRC=images/" . $this_hex['terrain'] . $resource2;
            echo ".png border=0 title=\" $terrain$resource \"></A>";
        }
        else
        {
            //echo "<IMG SRC=images/" . $this_hex[terrain] . $this_hex[res_type];
                        echo "<IMG SRC=images/" . $this_hex['terrain'] . $resource2;
            echo ".png border=0 title=\" $terrain$resource \">";
        }
            }
            else
            {
                echo "<IMG SRC=images/blank.png>";
            }
    }
    else
    {
        $res = $db->Execute("SELECT hex_id FROM $dbtables[hexes] "
                           ."WHERE hex_id = '".$hexinfo[$direct]."'");
        $this_hex = $res->fields;
                if( $this_hex['hex_id'] )
                {
            if( $tribeinfo['goods_tribe'] == $tribeinfo['tribeid'] )
            {
            echo "<A HREF=move.php?dest=" . $this_hex['hex_id'] . ">";
            echo "<IMG SRC=images/unknown.png border=0 title=\" Terra Incognita \"></A>";
            }
            else
            {
            echo "<IMG SRC=images/unknown.png border=0 title=\" Terra Incognita \">";
            }
                }
                else
                {
                    echo "<IMG SRC=images/blank.png>";
                }
    }
    echo "</TD>";
    $hex_count += 1;
    if ($hex_count == 1)  // If 1 hexes done then move to next row
    {
        echo "</TR><TR>";
    }
}

echo "</TABLE>";

table2_fancy_close (0, 0);

echo "</TD><TD>";
echo "<TABLE BORDER=0 CELLPADDING=0 WIDTH=\"100%\"><TR><TD>";

$time = time();
$db->Execute("UPDATE $dbtables[chiefs] "
            ."SET lastseen_month = '$month[count]',"
            ." lastseen_year = '$year[count]',"
            ." hour = '$time'"
            ." WHERE clanid = '$_SESSION[clanid]'");

if( $gameseason[count] == '1' )
{
    $season = 'Spring';
}
elseif( $gameseason[count] == '2' )
{
    $season = 'Summer';
}
elseif( $gameseason[count] == '3' )
{
    $season = 'Autumn';
}
else
{
    $season = 'Winter';
}
echo "<CENTER>";

help_link($month[count]." / ".$year[count], "", "topic", "Game Time", "Game Time");

echo "</CENTER></TD></TR><TR><TD>";
echo "<CENTER>";

help_link($season, "", "topic", "Game Seasons", "Game Seasons");
echo " ";

help_link("(".$weather[long_name].")", "", "topic", "Game Weather", "Game Weather");
echo "</TR></FONT></CENTER></TABLE>";
echo "</TD></TR></TD></TR></TABLE>";


echo "<P><TABLE BORDER=1 CELLPADDING=0 CELLSPACING=0 VALIGN=TOP><TR>";

echo "<TD CLASS=table1_td_cc VALIGN=TOP >";


/////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////Navigation Bar////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////

echo "<TABLE CLASS=table1_td_cc BORDER=1 CELLPADDING=0 CELLSPACING=0 WIDTH=\"25%\">";

echo "<TR CLASS=color_header>"
    ."<TD CLASS=heading_rounded>&nbsp;Navigation&nbsp;"
    ."</TD></TR>";



echo "<TR><TD ALIGN=LEFT onmouseover=\"return overlib('The links in this section are to game pages that allow you to view your world, assign actives, train warriors, and talk to other chiefs.');\" onmouseout=\"nd();\">";
GUI_menu_option("activities.php", "", "Activities");
GUI_menu_option("mailto.php", "", "Diplomacy");
GUI_menu_option("garrisons.php", "", "Garrisons");
GUI_menu_option("heraldry.php", "", "Heraldry");
GUI_menu_option("mapping.php", "", "Maps");
GUI_menu_option("religion.php", "", "Religion");
GUI_menu_option("report.php", "", "Reports");
GUI_menu_option("scouting.php", "", "Scouts");
GUI_menu_option("newtribe.php", "", "Subtribes");
GUI_menu_option("transfer.php", "", "Transfers");
echo "</TD></TR>";
echo "<TR><TD ALIGN=LEFT onmouseover=\"return overlib('The links in this section are to non-game specific pages');\" onmouseout=\"nd();\">";
GUI_menu_option($link_forums, "ts_forum", "Forums");
GUI_menu_option("help.php", "ts_help",  "Help");
GUI_menu_option("helper.php", "ts_helper", "Helper");
GUI_menu_option("bugtracker.php", "",  "Report Bug");
GUI_menu_option("options.php", "", "Options");
echo "</TD></TR>";

if( $tribeinfo[tribeid] == $tribeinfo[goods_tribe] && $month[count] == '4' | $month[count] == '10' )
{
echo "<TR onmouseover=\"return overlib('The fair is a special event that occurs only twice a year where you can buy and sell items to and from other chiefs.');\" onmouseout=\"nd();\">"
        ."<TD ALIGN=LEFT>"
    ."<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=8><TR><TD>";
    GUI_menu_option("fair.php", "", "Fair!!");
echo "</TD></TR></TABLE>"
    ."</TD></TR>";
}

echo "<TR onmouseover=\"return overlib('This logs you out of the account.');\" onmouseout=\"nd();\">"
        ."<TD ALIGN=LEFT>"
    ."<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=8><TR><TD>";
GUI_menu_option("logout.php", "", "Logout");
echo "</TD></TR></TABLE>"
    ."</TD></TR>";

if( $chiefinfo[admin] >= $privilege['adm_access'] )
{
echo "<TR><TD ALIGN=LEFT>"
    ."<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=8><TR><TD>";
    GUI_menu_option("admin.php", "", "Admin");
echo "</TD></TR></TABLE>"
    ."</TD></TR>";
}
echo "</TABLE>";
echo "</TD><TD CLASS=table2_td_cc>";



///////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////Center Tables///////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////
echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=\"100%\">";

echo "<TR CLASS=color_header>"
    ."<TD CLASS=heading_rounded>&nbsp;CLAN OVERVIEW&nbsp;"
    ."</TD></TR>";

echo "<TR><TD>";
table_tribe_info($tribeinfo);
echo "</TD></TR>";
echo "<TR><TD>&nbsp;</TD></TR>";

echo "<TR><TD>";
table_pop_info($tribeinfo);
echo "</TD></TR>";
echo "<TR><TD>&nbsp;</TD></TR>";

echo "<TR><TD>";
table_struct_info($tribeinfo);
echo "</TD></TR>";
echo "<TR><TD>&nbsp;</TD></TR>";

echo "<TR><TD>";
table_rsrc_info($tribeinfo);
echo "</TD></TR>";
echo "<TR><TD>&nbsp;</TD></TR>";

echo "<TR><TD>";
table_stores_info($tribeinfo);
echo "</TD></TR>";
echo "<TR><TD>&nbsp;</TD></TR>";

echo "<TR><TD>";
table_stock_info($tribeinfo);
echo "</TD></TR>";
echo "<TR><TD>&nbsp;</TD></TR>";

////////////////////////////////////third table in the center done/////////////////////////////
echo "</TD></TR><TR><TD>";
//////////////////////////////////////////////////////////////////////////////////////////////
include("gui/option_skills.php");

// SKILLS

echo "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=4 WIDTH=\"100%\">";
echo "<TR>"
    ."<TD colspan=3 valign=top><FONT CLASS=rsrc_title>Skills</FONT></TD>";

// SKILL SELECTORS

echo "<TD COLSPAN=9 valign=top align=right>";

echo "<TABLE BORDER=0 CELLPADDING=4 valign=top align=right>";

echo "<TR><TD valign=top>";
$totalpop = $tribeinfo[activepop] + $tribeinfo[inactivepop];
if( $totalpop < 200 )
{
    $skillsunavail = true;
    $db->Execute("UPDATE $dbtables[tribes] "
                ."SET pri_skill_att = '',"
                ."sec_skill_att = '' "
                ."WHERE tribeid = '$tribeinfo[tribeid]'");
}


// CLEAR VARIABLES IF THE CLEAR BUTTON HAS BEEN PRESSED


if( ISSET($_REQUEST['resetp']) )
{
    $db->Execute("UPDATE $dbtables[tribes] "
                ."SET pri_skill_att = '',"
                ."sec_skill_att = '' "
                ."WHERE tribeid = '$tribeinfo[tribeid]'");

    unset ($_REQUEST['resetp']);
    unset ($_REQUEST['attempta']);
    unset ($_REQUEST['attemptb']);
    unset ($_REQUEST['pri']);
    unset ($_REQUEST['sec']);
    $tribeinfo[pri_skill_att] = "";
    $tribeinfo[sec_skill_att] = "";
}


// DISPLAY PRIMARY SKILL ATTEMPT


if( $tribeinfo[pri_skill_att] == '' && !$skillsunavail )
{
    if( !ISSET($_REQUEST[attempta]) )
    {
        echo "<FORM ACTION=main.php METHOD=POST valign=top>";
        echo "<SELECT NAME=pri valign=top>";
        option_skills("a");
        option_skills("b");
        option_skills("c");
        echo "</SELECT></A>";
        echo "<INPUT TYPE=HIDDEN NAME=attempta VALUE=commit>";
        if( $_SESSION[tooltip] == '1' )
        {
            echo "<INPUT TYPE=SUBMIT VALUE=Primary onmouseover=\"return overlib('Select your primary skill attempt for this turn.');\" onmouseout=\"nd();\">";
        }
        else
        {
            echo "<INPUT TYPE=SUBMIT VALUE=Primary>";
        }
        echo "</FORM>";

        echo "</TD><TD valign=top>";
    }
    elseif( $_REQUEST[attempta] == 'commit' )
    {
        $db->Execute("UPDATE $dbtables[tribes] "
                    ."SET pri_skill_att = '$_REQUEST[pri]' "
                    ."WHERE tribeid = '$tribeinfo[tribeid]'");

        $longn = $db->Execute("SELECT long_name FROM $dbtables[skill_table] "
                             ."WHERE abbr = '$_REQUEST[pri]'");
        $longname = $longn->fields;

        if( !$skillsunavail )
        {
            echo "<FONT CLASS=rsrc_name>Primary</FONT></TD><TD valign=top align=right>";
            echo "<FONT CLASS=stat_resource>$longname[long_name]";

            echo "</TD><TD valign=top>";
        }
        else
        {
            echo "&nbsp;</TD><TD valign=top>";
        }
    }
}
else
{
    $longn = $db->Execute("SELECT long_name,abbr FROM $dbtables[skill_table] "
                         ."WHERE abbr = '$tribeinfo[pri_skill_att]'");
    $longname = $longn->fields;

    if( !$skillsunavail )
    {
        echo "<FONT CLASS=rsrc_name>Primary</FONT></TD><TD valign=top align=right>";
        echo "<FONT CLASS=stat_resource>$longname[long_name]";

        echo "</TD><TD valign=top>";
    }
    else
    {
        echo "&nbsp;</TD><TD valign=top>";
    }
}


// DISPLAY SECONDARY SKILL ATTEMPT


if( $tribeinfo[sec_skill_att] == '' && !$skillsunavail | $tribeinfo[sec_skill_att] == $tribeinfo[pri_skill_att] && !$skillsunavail )
{
    $tribeinfo[sec_skill_att] = '';
    if( !ISSET($_REQUEST[attemptb]) )
    {
        echo "<FORM ACTION=main.php METHOD=POST>";
        echo "<SELECT NAME=sec>";
        option_skills("a");
        option_skills("b");
        option_skills("c");
        echo "</SELECT>";
        echo "<INPUT TYPE=HIDDEN NAME=attemptb VALUE=commit>";
        if( $_SESSION[tooltip] == '1' )
        {
            echo "<INPUT TYPE=SUBMIT VALUE=Secondary onmouseover=\"return overlib('Select your secondary skill attempt for this turn.');\" onmouseout=\"nd();\">";
        }
        else
        {
            echo "<INPUT TYPE=SUBMIT VALUE=Secondary>";
        }
        echo "</FORM>";

        echo "</TD><TD valign=top>";
    }
    elseif( $_REQUEST[attemptb] == 'commit' )
    {
        $db->Execute("UPDATE $dbtables[tribes] "
                    ."SET sec_skill_att = '$_REQUEST[sec]' "
                    ."WHERE tribeid = '$tribeinfo[tribeid]'");

        $longn = $db->Execute("SELECT long_name FROM $dbtables[skill_table] "
                             ."WHERE abbr = '$_REQUEST[sec]'");
        $longname = $longn->fields;

        if( !$skillsunavail )
        {
            echo "<FONT CLASS=rsrc_name>Secondary</FONT></TD><TD valign=top align=right>";
            echo "<FONT CLASS=stat_resource>$longname[long_name]";

            echo "</TD><TD valign=top>";
        }
        else
        {
            echo "&nbsp;</TD><TD valign=top>";
        }
    }
}
else
{
    $longn = $db->Execute("SELECT long_name,abbr FROM $dbtables[skill_table] "
                         ."WHERE abbr = '$tribeinfo[sec_skill_att]'");
    $longname = $longn->fields;

    if( !$skillsunavail )
    {
        echo "<FONT CLASS=rsrc_name>Secondary</FONT></TD><TD valign=top>";
        echo "<FONT CLASS=stat_resource>$longname[long_name]";

        echo "</TD><TD valign=top>";
    }
    else
    {
        echo "&nbsp;</TD><TD valign=top>";
    }
}

if( $tribeinfo['pri_skill_att']<>'' | $tribeinfo['sec_skill_att']<>'' )
{
    echo "<FORM ACTION=main.php METHOD=POST align=right>";
    echo "<INPUT TYPE=HIDDEN NAME=resetp VALUE=resetp>";
    if( $_SESSION[tooltip] == '1' )
    {
        echo "<INPUT TYPE=SUBMIT VALUE=CLEAR onmouseover=\"return overlib('Clears out both your skill selections so that you may select new ones.');\" onmouseout=\"nd();\">";
    }
    else
    {
        echo "<INPUT TYPE=SUBMIT VALUE=CLEAR>";
    }
    echo "</FORM>";
    echo "</TD>";
}
else
{
    echo "&nbsp;</TD>";
}


echo "</TD></TR></TABLE>";


echo "</TD></TR></TABLE></TD></TR>";

///////////////////////////////////////////Skills display//////////////////////////////////////////////


echo "<TR><TD>";
list_skills($tribeinfo, "a");
echo "</TD></TR>";
echo "<TR><TD>&nbsp;</TD></TR>";

echo "<TR><TD>";
list_skills($tribeinfo, "b");
echo "</TD></TR>";
echo "<TR><TD>&nbsp;</TD></TR>";

echo "<TR><TD>";
list_skills($tribeinfo, "c");
echo "</TD></TR>";


////////////////////////////////////////////////table in the center done///////////////////////////////
echo "</TABLE>";
/////////////////////////////////////////////////////////////////////////////////////////////////////

echo "<TD CLASS=table1_td_cc VALIGN=TOP HEIGHT=\"100%\">";
echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=\"100%\" HEIGHT=\"100%\" ";
echo "VALIGN=TOP><TR VALIGN=top><TD align=top><TABLE BORDER=1 CELLSPACING=0 ";
echo "CELLPADDING=0 ALIGN=TOP>";

if( $_SESSION['tooltip'] == '1' )
{
    echo "<TR CLASS=color_header>"
           ."<TD CLASS=heading_rounded onmouseover=\"return overlib('This is a list of your available tribe and subtribes. Any tribes that are in the same hex as the unit you have focus on will be yellow, select which tribe to view by clicking the unit number below.');\" onmouseout=\"nd();\">"
        ."&nbsp;Units&nbsp;"
    ."</TD></TR>";
}
else
{
    echo "<TR CLASS=color_header>"
        ."<TD CLASS=heading_rounded>"
        ."&nbsp;Units&nbsp;"
        ."</TD></TR>";
}

$clanid = $_SESSION['clanid'];
$res = $db->Execute("SELECT tribeid, hex_id FROM $dbtables[tribes] "
                   ."WHERE clanid = '$clanid' "
                   ."ORDER BY tribeid ASC");

if( !$res )
{
    echo "<TR CLASS=color_highlight><TD>&nbsp;None</TD></TR>";
}
else
{
    echo "<TR><TD>";
    while( !$res->EOF )
    {
        $row = $res->fields;
        if( $row[hex_id] == $tribeinfo[hex_id] )
        {
            if ($row['tribeid']==$_SESSION['current_unit'])
            {
                $hl1 = "<FONT CLASS=color_highlight>";
                $hl2 = "</FONT>";
            }
            else
            {
                $hl1 = "";
                $hl2 = "";
            }
            echo "&nbsp;<B><a href=main.php?id=$row[tribeid]>".$hl1.$row['tribeid'].$hl2."</A></B><BR>";
        }
        else
        {
            echo "&nbsp;<a href=main.php?id=$row[tribeid]>$row[tribeid]</A><BR>";
        }
        $res->MoveNext();
    }
    echo "</TD></TR>";
}


// GET NEARBY UNITS

$scout = $db->Execute("SELECT * FROM $dbtables[skills] "
                     ."WHERE tribeid = '$tribeinfo[tribeid]' "
                     ."AND abbr = 'sct'");
$scoutskill = $scout->fields;

$res = $db->Execute("SELECT tribeid, hex_id FROM $dbtables[tribes] "
                   ."WHERE hex_id = '$tribeinfo[hex_id]' "
                   ."AND clanid <> '$tribeinfo[clanid]'");
$row = $res->fields;

$ss = $db->Execute("SELECT level FROM $dbtables[skills] "
                  ."WHERE tribeid = '$tribeinfo[tribeid]' "
                  ."AND abbr = 'spy'");
$spyskill = $ss->fields;

$nearby = 0;
if( $scoutskill['level'] > 7 )
{
    $far = $db->Execute("SELECT * FROM $dbtables[hexes] "
                       ."WHERE hex_id = '$tribeinfo[hex_id]'");
    $farinfo = $far->fields;

    $neighbor = array(
        "NW" => $farinfo[nw],
        "N" => $farinfo[n],
        "NE" => $farinfo[ne],
        "W" => $farinfo[w],
        "X" => $tribeinfo[hex_id],
        "E" => $farinfo[e],
        "SW" => $farinfo[sw],
        "S" => $farinfo[s],
        "SE" => $farinfo[se]);

    $neigh_info = array();
    foreach ($neighbor AS $key => $value)
    {
        $neigh = $db->Execute("SELECT tribeid FROM $dbtables[tribes] "
                            ."WHERE hex_id = '$value' "
                            ."AND clanid <> '$tribeinfo[clanid]'");
        $neigh_info[$key] = array();
        $neigh_info[$key] = $neigh->fields;

        if ($key=="X ")
        {
            $hl1 = "<FONT CLASS=color_danger>";
            $hl2 = "</FONT>";
        }
        else
        {
            $hl1 = "";
            $hl2 = "";
        }
        if (!$neigh->EOF)
        {
            echo "<TR CLASS=color_header>"
                ."<TD CLASS=heading_rounded>&nbsp;Scouted&nbsp;"
                ."</TD></TR>";

            echo "<TR><TD>";
            while (!$neigh->EOF)
            {
                $neigh_tribe = $neigh->fields;
                if( !$neigh_tribe['tribeid'] == '' )
                {
                    echo "&nbsp;<A HREF=combat.php?target=".$neigh_tribe['tribeid'].">"
                        .$hl1.$neigh_tribe['tribeid'].$hl2
                        ."</A> ($key)<BR>";
                    $nearby++;
                }
                $neigh->MoveNext();
            }
            echo "</TD></TR>";
        }
    }
}

if( !$row[tribeid] && $nearby < 1 )
{
    if( $_SESSION['tooltip'] == '1' )
    {
    echo "<TR CLASS=color_header>"
            ."<TD CLASS=heading_rounded onmouseover=\"return overlib('This is a list of foreign tribes that are in proximity to the unit you are currently viewing. How far away you can detect them depends upon how high your unit\'s scouting skill is. Spy or attack these units by clicking on them if a link is provided.');\"onmouseout=\"nd();\">";
    }
    else
    {
        echo "<TR CLASS=color_header>"
            ."<TD CLASS=heading_rounded>";
    }
    echo "&nbsp;Nearby&nbsp;</A>"
        ."</TD></TR>";

    echo "<TR><TD CLASS=color_highlight>&nbsp;None ";
    echo $neigh_info['N ']['tribeid']
        ." "
        .$neigh_info['E ']['tribeid']
        ." "
        .$neigh_info['S ']['tribeid']
        ." "
        .$neigh_info['W ']['tribeid'];
    echo "</TD></TR>";
}

elseif( $row[tribeid] && $spyskill[level] > 0 )
{
    echo "<TR CLASS=color_header>"
        ."<TD CLASS=heading_rounded>&nbsp;Spy On&nbsp;"
        ."</TD></TR>";

    echo "<TR><TD>";
    while( !$res->EOF )
    {
        $row = $res->fields;
        echo "&nbsp;<a href=spy.php?id=$row[tribeid]>$row[tribeid]</A><BR>";
        $res->MoveNext();
    }
    echo "</TD></TR>";
}
elseif( $row[hex_id] == $tribeinfo[hex_id] && $spyskill[level] < 1 )
{
    echo "<TR CLASS=color_header>"
        ."<TD CLASS=heading_rounded>&nbsp;Attack&nbsp;"
        ."</TD></TR>";

    echo "<TR><TD>";
    while( !$res->EOF )
    {
        $row = $res->fields;
        echo "&nbsp;<a href=combat.php?target=$row[tribeid]>$row[tribeid]</A><BR>";
        $res->MoveNext();
    }
    echo "</TD></TR>";
}
elseif ( !$res->EOF )
{
    echo "<TR CLASS=color_header>"
        ."<TD CLASS=heading_rounded>&nbsp;Nearby&nbsp;"
        ."</TD></TR>";

    echo "<TR><TD>";
    while( !$res->EOF )
    {
        $row = $res->fields;
        echo "$row[tribeid]";
        echo "<BR>";
        $res->MoveNext();
    }
    echo "</TD></TR>";
}
echo "</TD></TR></TABLE>";
echo "</TD></TR></TABLE>";

echo "</TD></TR></TABLE>";

echo "</TD></TR></TABLE>";

page_footer();


// FUNCTION DEFINITIONS

function table_rows ($title, $cols, $data)
{
    $num_cols=$cols * 3;

    echo "<p>";
    echo "<TABLE COLS=$num_cols BORDER=0 CELLPADDING=2 CELLSPACING=0 WIDTH=\"100%\">";
    if ($title <> "")
    {
        echo "<TR width=\"100%\">"
            ."<TD width=\"100%\" colspan=$num_cols>"
            ."<FONT CLASS=rsrc_title>&nbsp;$title</FONT>"
            ."</TD>"
            ."</TR>";
    }

    $r=0;
    while( !$data->EOF )
    {
        $rc = $r % 2;
        $r++;
        echo "<TR CLASS=color_row$rc>";
        $i = 0;
        while( $i < $cols && !$data->EOF)
        {
            $data_info = $data->fields;
            if( $data_info[value] > 0 )
            {
                echo "<TD NOWRAP><FONT CLASS=rsrc_name2>&nbsp;$data_info[name]&nbsp;</TD>";
                echo "<TD>"
                    ."<TABLE ALIGN=RIGHT BORDER=0 CELLPADDING=0 CELLSPACING=0><TR>"
                    ."<TD ALIGN=RIGHT><FONT CLASS=stat_resource>&nbsp;$data_info[value]&nbsp;</TD>"
                    ."</TR></TABLE>"
                    ."</TD>";
                if ($i < $cols - 1)
                {
                    echo "<TD CLASS=row_sep></TD>";
                }
            $i++;
            }
            $data->MoveNext();
        }
        GUI_pad_row ($num_cols, 3, $r, $i);
        echo "</TR>";
    }

    echo "</TABLE>";
}


function table_tribe_info($tribeinfo)
{
    echo "<TABLE BORDER=0 CELLSPACING=4 CELLPADDING=0 WIDTH=\"100%\">";

        echo "<TR>";
            echo "<TD><FONT CLASS=rsrc_title>Morale</TD>";
            echo "<TD align=right><FONT CLASS=stat_primary>$tribeinfo[morale]</TD>";
            echo "<TD><FONT CLASS=rsrc_title>&nbsp;Carry Capacity</TD>";
            $maxweight = ceil($tribeinfo['maxweight']);
            echo "<TD align=right><FONT CLASS=stat_primary>$maxweight</TD>";
            echo "<TD></TD>";
            echo "<TD></TD>";
        echo "</TR>";

        echo "<TR>";
            echo "<TD><FONT CLASS=rsrc_title>Move Points</TD>";
            echo "<TD align=right><FONT CLASS=stat_primary>$tribeinfo[move_pts]</TD>";
                $curweight = ceil($tribeinfo['curweight']);
                if($curweight > $maxweight)
                {
                $weight_class = 'weight_excess';
                }
                elseif(($curweight * 1.75) > $maxweight)
                {
                $weight_class = 'weight_limit';
                }
                else
                {
                $weight_class = 'weight_normal';
                }
            echo "<TD><FONT CLASS=rsrc_title>&nbsp;Current Weight</TD>";
            echo "<TD align=right><FONT CLASS=$weight_class>$curweight</TD>";
            echo "<TD ALIGN=RIGHT><FONT CLASS=rsrc_title>Goods Tribe</TD>";
            if( $tribeinfo['goods_tribe'] <> $tribeinfo['tribeid'] )
            {
                echo "<TD align=right><FONT CLASS=stat_primary><a href=transfer.php?op=tribe>$tribeinfo[goods_tribe]</a></TD>";
            } else {
                echo "<TD align=right><FONT CLASS=stat_primary><a href=transfer.php?op=tribe>Self</a></TD>";
            }
        echo "</TR>";

    echo "</TABLE>";
}


function table_struct_info ($tribeinfo)
{
    global $db, $dbtables;

    $struct = $db->Execute("SELECT * FROM $dbtables[structures] "
                          ."WHERE clanid = '$tribeinfo[clanid]' "
                          ."AND hex_id = '$tribeinfo[hex_id]'");

    if( !$struct->EOF )
    {
        echo "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=4 WIDTH=\"100%\">";
        echo "<TR>";
        echo "<TD colspan=10><FONT CLASS=rsrc_title>Buildings</FONT></TD>";
        echo "</TR>";

        while( !$struct->EOF )
        {
            $i = 0;
            echo "<TR>";
            while( $i < 5 )
            {
                $structinfo = $struct->fields;
                if( !$structinfo[proper] == '' )
                {
                    $trid = "";
                    echo "<TD VALIGN=TOP><FONT CLASS=rsrc_name2>";
                    if( $structinfo[struct_pts] < $structinfo[max_struct_pts] )
                    {
                        if ( $structinfo[tribeid] <>  $tribeinfo[tribeid])
                        {
                            // Does the Building belong to this tribe?
                            ereg ("[^.]*.(.*)", $structinfo[tribeid], $tid);
                            $trid = "[$tid[1]]";
                        }
                        echo "<i>$structinfo[proper]$trid ";
                        if( $structinfo[subunit] <> '' )
                        {
                            echo "<BR>$structinfo[number] $structinfo[subunit]</i>";
                        }
                        else
                        {}
                    }
                    else
                    {
                        if ( $structinfo[tribeid] <>  $tribeinfo[tribeid])
                        {
                            // Does the Building belong to this tribe?
                            ereg ("[^.]*.(.*)", $structinfo[tribeid], $tid);
                            $trid = "[$tid[1]]";
                        }
                        echo "$structinfo[proper]$trid ";
                        if( $structinfo[subunit] <> '' )
                        {
                            echo "<BR>$structinfo[number] $structinfo[subunit]";
                        }
                        else {}
                    }
                    echo "&nbsp;&nbsp;</TD>";
                }
                $struct->MoveNext();
                $i++;
            }
            echo "</TR>";
        }
        echo "</TABLE>";
    }
}


function table_pop_info($tribeinfo)
{
    global $db, $dbtables;

    echo "<P>";
    echo "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=4 WIDTH=\"100%\">";

    echo "<TR>";
        echo "<TD colspan=8><FONT CLASS=rsrc_title>Population</FONT></TD>";
    echo "</TR>";

    echo "<TR>";
        $totalpop = NUMBER(($tribeinfo[activepop] + $tribeinfo[warpop] + $tribeinfo[inactivepop]),0);
            echo "<TD><FONT CLASS=rsrc_name>Total</FONT></TD>";
            echo "<TD align=right><FONT CLASS=stat_resource>$totalpop</TD>";

        if( $tribeinfo[activepop] > 0 )
        {
            $activepop = NUMBER($tribeinfo[activepop],0);
            $unassigned = NUMBER($tribeinfo[curam],0);
            echo "<TD><FONT CLASS=rsrc_name>Actives</FONT></TD>";
            echo "<TD align=right><FONT CLASS=stat_resource>$activepop ($unassigned)</TD>";
        }

        if( $tribeinfo[inactivepop] > 0 )
        {
            $inactivepop = NUMBER($tribeinfo[inactivepop],0);
            echo "<TD><FONT CLASS=rsrc_name>Inactives</FONT></TD>";
            echo "<TD align=right><FONT CLASS=stat_resource>$inactivepop</TD>";
        }

        if( $tribeinfo[warpop] > 0 )
        {
            $gar = $db->Execute("SELECT count(*) as garrisons FROM $dbtables[garrisons] "
                               ."WHERE tribeid = '$_SESSION[current_unit]'");
            $garinfo = $gar->fields;
            $warpop = NUMBER($tribeinfo[warpop]);
            echo "<TD><FONT CLASS=rsrc_name>Warriors</FONT></TD>";
            echo "<TD align=right><FONT CLASS=stat_resource>$warpop ($garinfo[garrisons])</TD>";
        }
    echo "</TR>";

    if ( $tribeinfo[slavepop] > 0  ||  $tribeinfo[specialpop] > 0 )
    {
        echo "<TR>";
            $record_pad=8;
            if( $tribeinfo[slavepop] > 0 )
            {
                $record_pad=$record_pad-2;
                echo "<TD><FONT CLASS=rsrc_name>Slaves</FONT></TD>";
                echo "<TD align=right><FONT CLASS=stat_resource>$tribeinfo[slavepop]</TD>";
            }

            if( $tribeinfo[specialpop] > 0 )
            {
                $record_pad=$record_pad-2;
                echo "<TD><FONT CLASS=rsrc_name>Special</FONT></TD>";
                echo "<TD align=right><FONT CLASS=stat_resource>$tribeinfo[specialpop]</TD>";
            }
            echo "<TD colspan=$record_pad>&nbsp;</TD>";
        echo "</TR>";
    }

    echo "</TABLE>";
}


function table_rsrc_info($tribeinfo)
{
    global $db, $dbtables;
    // RESOURCES LIST

    $resource = $db->Execute("SELECT long_name AS name, amount AS value FROM $dbtables[resources] "
                            ."WHERE tribeid = '$tribeinfo[tribeid]'"
                            ."ORDER BY long_name");
    table_rows("Resources", 5, $resource);
}


function table_stores_info($tribeinfo)
{
    global $db, $dbtables;
    // PRODUCTS LIST

    $production = $db->Execute("SELECT distinct proper AS name, amount AS value FROM $dbtables[products] "
                              ."WHERE tribeid = '$tribeinfo[tribeid]' "
                              ."AND amount > '0' "
                              ."ORDER BY proper");
    table_rows("Stores", 5, $production);
}


function table_stock_info ($tribeinfo)
{
    global $db, $dbtables;
    // LIVESTOCK LIST

    $animals = $db->Execute("SELECT type AS name, amount AS value FROM $dbtables[livestock] "
                           ."WHERE tribeid = '$tribeinfo[tribeid]' "
                           ."AND amount > '0'"
                           ."ORDER BY type");
        if( !$animals )
        {
            echo $db->ErrorMsg() . "<BR>";
        }

    table_rows("Livestock", 5, $animals);
}


function list_skills($tribeinfo, $skillgroup)
{
    global $db, $dbtables;

    $skill = $db->Execute("SELECT long_name AS name, level AS value FROM $dbtables[skills] "
                     ."WHERE tribeid = '$tribeinfo[tribeid]' "
                     ."AND level > '0' "
                     ."AND `group` = '".$skillgroup."' "
                     ."ORDER BY `long_name` ASC");
    table_rows("", 5, $skill);
}


?>
