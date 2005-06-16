<?php
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
die("You Do Not Have permissions to view this page!");
}
include("config.php");
include("game_time.php");

$title="Administrator Tribe Mover";
include("header.php");

connectdb();
bigtitle();

$username = $_SESSION['username'];
$admin = $db->Execute("SELECT * FROM $dbtables[chiefs] WHERE username = '$username'");
db_op_result($admin,__LINE__,__FILE__);
$admininfo = $admin->fields;


$module = $_POST['menu'];

if( !$admininfo['admin'] >= '2' )
{
    echo "You must be an administrator to use this tool.<BR>\n";
    TEXT_GOTOMAIN();
    die();
}

////////////////////////////////////First, display a list of tribes to move.//////////////////
if( $_POST['newhex'] == '' && $_POST['tribe'] == '' )
{
    $res = $db->Execute("SELECT * FROM $dbtables[tribes] "
                       ."WHERE tribeid = goods_tribe "
                       ."ORDER BY tribeid");
                       db_op_result($res,__LINE__,__FILE__);
    echo '<CENTER><FONT SIZE=+2 COLOR=WHITE>Move which tribe?</FONT>';
    echo '<CENTER><TABLE BORDER=0 CELLPADDING=0 WIDTH=60%><TR ALIGN=CENTER><TD>';
    echo '<BR>Select a Tribe:</TD><TD>';
    echo '<FORM ACTION=adminmove.php METHOD=POST>';
    echo '<SELECT NAME=tribe>';
    while( !$res->EOF)
    {
        $tribe = $res->fields;
        $hx = $db->Execute("SELECT * FROM $dbtables[hexes] "
                          ."WHERE hex_id = '$tribe[hex_id]'");
        db_op_result($hx,__LINE__,__FILE__);
        $hex = $hx->fields;
        echo "<OPTION VALUE=$tribe[tribeid]>$tribe[tribeid] ($hex[terrain])</OPTION>";

        $res->MoveNext();
    }
    echo '</SELECT></TD></TR><TR ALIGN=CENTER><TD>';
    echo "Please select a new hex to place them.</TD>";
    echo "<TD><INPUT CLASS=edit_area TYPE=TEXT NAME=newhex VALUE=''></TD></TR>";
    echo "<TR><TD COLSPAN=2 ALIGN=CENTER><INPUT TYPE=SUBMIT VALUE=Move></FORM></TD></TR></TABLE>";
}

if( !$_POST['newhex'] == '' && !$_POST['tribe'] == '' )
{
    if( $_POST['newhex'] > 1 && $_POST['newhex'] < 37501 )
    {
        $gt = $db->Execute("SELECT * FROM $dbtables[tribes] "
                          ."WHERE goods_tribe = '$_POST[tribe]'");
         db_op_result($gt,__LINE__,__FILE__);
        while( !$gt->EOF)
        {
            $subtribe = $gt->fields;
            $qry = $db->Execute("UPDATE $dbtables[garrisons] "
                        ."SET hex_id = '$_POST[newhex]' "
                        ."WHERE tribeid = '$subtribe[tribeid]'");
            db_op_result($qry,__LINE__,__FILE__);
            $qry = $db->Execute("UPDATE $dbtables[structures] "
                        ."SET hex_id = '$_POST[newhex]' "
                        ."WHERE tribeid = '$subtribe[tribeid]' "
                        ."AND hex_id = '$subtribe[hex_id]'");
            db_op_result($qry,__LINE__,__FILE__);
            echo "<CENTER>Tribe $subtribe[tribeid] moved to $_POST[newhex].<BR></CENTER>";
            $gt->MoveNext();
        }
        $qry = $db->Execute("UPDATE $dbtables[tribes] "
                    ."SET hex_id = '$_POST[newhex]' "
                    ."WHERE goods_tribe = '$_POST[tribe]'");
        db_op_result($qry,__LINE__,__FILE__);
    $qry = $db->Execute("INSERT INTO $dbtables[logs] "
                ."VALUES("
                ."'',"
                ."'$month[count]',"
                ."'$year[count]',"
                ."'0000',"
                ."'0000.00',"
                ."'ADMINMOVED',"
                ."'$stamp',"
                ."'Admin: $_POST[tribe] moved to $_POST[newhex] by $_SESSION[clanid].')");
     db_op_result($qry,__LINE__,__FILE__);
    }
}

echo '<BR><P>';
TEXT_GOTOMAIN();
include("footer.php");
?>
