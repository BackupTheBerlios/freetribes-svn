<?
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: adminmove.php
session_start();
header("Cache-control: private");
$admincheck = $_SESSION['username'];
//here we should query the db to get admin's username or id or some such to auth
if($admincheck !== 'admin')
{
die("You Do Not	Have permissions to view this page!");
}
include("config.php");
include("game_time.php");

page_header("Account Admin - Move Tribe");

connectdb();

$username = $_SESSION['username'];
$admin = $db->Execute("SELECT * FROM $dbtables[chiefs] WHERE username = '$username'");
$admininfo = $admin->fields;


$module = $_REQUEST[menu];

if( !$admininfo[admin] >= $privilege['adm_mapping'] )
{
    echo "You must mapping privilege to use this tool.<BR>\n";
    page_footer();
}

////////////////////////////////////First, display a list of tribes to move.//////////////////
if( $_REQUEST[newhex] == '' && $_REQUEST[tribe] == '' )
{
    $res = $db->Execute("SELECT * FROM $dbtables[tribes] "
                       ."WHERE tribeid = goods_tribe "
                       ."ORDER BY tribeid");
    echo '<CENTER><FONT SIZE=+2 COLOR=WHITE>Move which tribe?</FONT>';
    echo '<CENTER><TABLE BORDER=0 CELLPADDING=0 WIDTH=60%><TR ALIGN=CENTER><TD>';
    echo '<BR>Select a Tribe:</TD><TD>';
    echo '<FORM ACTION=admin_tribe_move.php METHOD=POST>';
    echo '<SELECT NAME=tribe>';
    while( !$res->EOF)
    {
        $tribe = $res->fields;
        $hx = $db->Execute("SELECT * FROM $dbtables[hexes] "
                          ."WHERE hex_id = '$tribe[hex_id]'");
        $hex = $hx->fields;
        echo "<OPTION VALUE=$tribe[tribeid]>$tribe[tribeid] ($hex[terrain])</OPTION>";

        $res->MoveNext();
    }
    echo '</SELECT></TD></TR><TR ALIGN=CENTER><TD>';
    echo "Please select a new hex to place them.</TD>";
    echo "<TD><INPUT CLASS=edit_area TYPE=TEXT NAME=newhex VALUE=''></TD></TR>";
    echo "<TR><TD COLSPAN=2 ALIGN=CENTER><INPUT TYPE=SUBMIT VALUE=Move></FORM></TD></TR></TABLE>";
}

if( !$_REQUEST[newhex] == '' && !$_REQUEST[tribe] == '' )
{
    if( $_REQUEST[newhex] > 1 && $_REQUEST[newhex] < 37501 )
    {
        $gt = $db->Execute("SELECT * FROM $dbtables[tribes] "
                          ."WHERE goods_tribe = '$_REQUEST[tribe]'");
        while( !$gt->EOF)
        {
            $subtribe = $gt->fields;
            $db->Execute("UPDATE $dbtables[garrisons] "
                        ."SET hex_id = '$_REQUEST[newhex]' "
                        ."WHERE tribeid = '$subtribe[tribeid]'");
            $db->Execute("UPDATE $dbtables[structures] "
                        ."SET hex_id = '$_REQUEST[newhex]' "
                        ."WHERE tribeid = '$subtribe[tribeid]' "
                        ."AND hex_id = '$subtribe[hex_id]'");
            echo "<CENTER>Tribe $subtribe[tribeid] moved to $_REQUEST[newhex].<BR></CENTER>";
            $gt->MoveNext();
        }
        $db->Execute("UPDATE $dbtables[tribes] "
                    ."SET hex_id = '$_REQUEST[newhex]' "
                    ."WHERE goods_tribe = '$_REQUEST[tribe]'");
    $db->Execute("INSERT INTO $dbtables[logs] "
                ."VALUES("
                ."'',"
                ."'$month[count]',"
                ."'$year[count]',"
                ."'0000',"
                ."'0000.00',"
                ."'ADMINMOVED',"
                ."'$stamp',"
                ."'Admin: $_REQUEST[tribe] moved to $_REQUEST[newhex] by $_SESSION[clanid].')");

    }
}

echo '<BR><P>';
page_footer();
?>
