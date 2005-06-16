<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: adminreimburse.php
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

$title="Administrator Tribe Reimbursement";
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
    echo '<CENTER><FONT SIZE=+2 COLOR=WHITE>Reimburse which tribe?</FONT>';
    echo '<CENTER><TABLE BORDER=0 CELLPADDING=0 WIDTH=60%><TR ALIGN=CENTER><TD>';
    echo '<BR>Select a Tribe:</TD><TD>';
    echo '<FORM ACTION=adminreimburse.php METHOD=POST>';
    echo '<SELECT NAME=tribe>';
    while( !$res->EOF)
    {
        $tribe = $res->fields;
        $chf = $db->Execute("SELECT * FROM $dbtables[chiefs] "
                           ."WHERE clanid = '$tribe[clanid]'");
        db_op_result($chf,__LINE__,__FILE___);
        $chief = $chf->fields;
        echo "<OPTION VALUE=$tribe[tribeid]>$tribe[tribeid] ($chief[username] / $chief[chiefname])</OPTION>";

        $res->MoveNext();
    }
    echo '</SELECT></TD></TR><TR ALIGN=CENTER><TD>';
    echo "Please select what type of reimbursement.</TD>";
    echo "<TD>";
    echo "<SELECT NAME=type>";
    echo "<OPTION VALUE=resource>Resource</OPTION>";
    echo "<OPTION VALUE=products>Products</OPTION>";
    echo "<OPTION VALUE=livestock>Livestock</OPTION>";
    echo "</SELECT>";
    echo "</TD></TR>";
    echo "<TR ALIGN=CENTER><TD>";
    echo "Please select the action to take.</TD><TD>";
    echo "<SELECT NAME=action>";
    echo "<OPTION VALUE=add>+</OPTION>";
    echo "<OPTION VALUE=set>=</OPTION>";
    echo "<OPTION VALUE=subtract>-</OPTION>";
    echo "</SELECT>";
    echo "</TD></TR>";
    echo "<TR><TD COLSPAN=2 ALIGN=CENTER><INPUT TYPE=SUBMIT VALUE=Continue></FORM></TD></TR></TABLE>";
}

if( !$_POST['type'] == '' && !$_POST['tribe'] == '' && !$_POST['action'] == '')
{
    echo "<CENTER><TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0><TR><TD>";
    echo '<FORM ACTION=adminreimburse.php METHOD=POST>';
    echo '<SELECT NAME=item>';
    if( $_POST[type] == 'resource' )
    {
        $reim = $db->Execute("SELECT DISTINCT long_name, dbname FROM $dbtables[resources] ORDER BY long_name");
        while( !$reim->EOF )
        {
            $reimburse = $reim->fields;
            echo "<OPTION VALUE=\"$reimburse[dbname]\">$reimburse[long_name]</OPTION>";
            $reim->MoveNext();
        }
    }
    elseif( $_POST['type'] == 'livestock' )
    {
        $reim = $db->Execute("SELECT DISTINCT type FROM $dbtables[livestock] ORDER BY type");
        while( !$reim->EOF )
        {
            $reimburse = $reim->fields;
            echo "<OPTION VALUE=$reimburse[type]>$reimburse[type]</OPTION>";
            $reim->MoveNext();
        }
    }
    elseif( $_POST['type'] == 'products' )
    {
        $reim = $db->Execute("SELECT DISTINCT long_name, proper FROM $dbtables[products] ORDER BY proper");
        while( !$reim->EOF )
        {
            $reimburse = $reim->fields;
            echo "<OPTION VALUE=$reimburse[long_name]>$reimburse[proper]</OPTION>";
            $reim->MoveNext();
        }
    }
    echo "</SELECT></TD><TD>";
    if( $_POST['action'] == 'add' )
    {
        echo " + ";
    }
    elseif( $_POST['action'] == 'set' )
    {
        echo " = ";
    }
    elseif( $_POST['action'] == 'subtract' )
    {
        echo " - ";
    }
    echo "<INPUT CLASS=edit_area TYPE=TEXT NAME=amount VALUE=''>";
    echo "</TD></TR>";
    echo "<INPUT TYPE=HIDDEN NAME=tribe VALUE=$_POST[tribe]>";
    echo "<INPUT TYPE=HIDDEN NAME=action VALUE=$_POST[action]>";
    echo "<INPUT TYPE=HIDDEN NAME=type VALUE=$_POST[type]>";
    echo "<TR ALIGN=CENTER><TD COLSPAN=2>";
    echo "<INPUT TYPE=SUBMIT VALUE='Reimburse'>";
    echo "</TD></FORM></TABLE>";
}

if( !$_POST['amount'] == '' && !$_POST['item'] == '' )
{

    if( $_POST['type'] == 'resource' )
    {
        if( $_POST['action'] == 'add' )
        {
            $qry = $db->Execute("UPDATE $dbtables[resources] "
                        ."SET amount = amount + $_POST[amount] "
                        ."WHERE tribeid = '$_POST[tribe]' "
                        ."AND dbname = '$_POST[item]'");
            db_op_result($qry,__LINE__,__FILE__);
            $verb = 'added to';
        }
        elseif( $_POST['action'] == 'set' )
        {
           $qry = $db->Execute("UPDATE $dbtables[resources] "
                        ."SET amount = $_POST[amount] "
                        ."WHERE tribeid = '$_POST[tribe]' "
                        ."AND dbname = '$_POST[item]'");
            db_op_result($qry,__LINE__,__FILE__);
            $verb = 'set to';
        }
        elseif( $_POST['action'] == 'subtract' )
        {
            $qry = $db->Execute("UPDATE $dbtables[resources] "
                        ."SET amount = amount - '$_POST[amount] "
                        ."WHERE tribeid = '$_POST[tribe]' "
                        ."AND dbname = '$_POST[item]'");
            db_op_result($qry,__LINE__,__FILE__);
            $verb = 'subtracted from';
        }
    }
    elseif( $_POST['type'] == 'livestock' )
        {
        if( $_POST['action'] == 'add' )
        {
            $qry = $db->Execute("UPDATE $dbtables[livestock] "
                        ."SET amount = amount + $_POST[amount] "
                        ."WHERE tribeid = '$_POST[tribe]' "
                        ."AND type = '$_POST[item]'");
            db_op_result($qry,__LINE__,__FILE__);
            $verb = 'added to';
        }
        elseif( $_POST['action'] == 'set' )
        {
            $qry = $db->Execute("UPDATE $dbtables[livestock] "
                        ."SET amount = $_POST[amount] "
                        ."WHERE tribeid = '$_POST[tribe]' "
                        ."AND type = '$_POST[item]'");
            db_op_result($qry,__LINE__,__FILE__);
            $verb = 'set to';
        }
        elseif( $_POST['action'] == 'subtract' )
        {
            $qry = $db->Execute("UPDATE $dbtables[livestock] "
                        ."SET amount = amount - '$_POST[amount] "
                        ."WHERE tribeid = '$_POST[tribe]' "
                        ."AND type = '$_POST[item]'");
             db_op_result($qry,__LINE__,__FILE__);
            $verb = 'subtracted from';
        }
    }
    elseif( $_POST['type'] == 'products' )
        {
        if( $_POST['action'] == 'add' )
        {
            $qry = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + $_POST[amount] "
                        ."WHERE tribeid = '$_POST[tribe]' "
                        ."AND long_name = '$_POST[item]'");
             db_op_result($qry,__LINE__,__FILE__);
            $verb = 'added to';
        }
        elseif( $_POST['action'] == 'set' )
        {
            $qry = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = $_POST[amount] "
                        ."WHERE tribeid = '$_POST[tribe]' "
                        ."AND long_name = '$_POST[item]'");
            db_op_result($qry,__LINE__,__FILE__);
            $verb = 'set to';
        }
        elseif( $_POST['action'] == 'subtract' )
        {
            $qry = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount - '$_POST[amount] "
                        ."WHERE tribeid = '$_POST[tribe]' "
                        ."AND long_name = '$_POST[item]'");
            db_op_result($qry,__LINE__,__FILE__);
            $verb = 'subtracted from';
        }
    }

   $qry =  $db->Execute("INSERT INTO $dbtables[logs] "
                ."VALUES("
                ."'',"
                ."'$month[count]',"
                ."'$year[count]',"
                ."'0000',"
                ."'0000.00',"
                ."'REIMB',"
                ."'$stamp',"
                ."'Admin: $_POST[amount] $_POST[item] $verb $_POST[tribe] by $_SESSION[clanid].')");
   db_op_result($qry,__LINE__,__FILE__);
    echo "<CENTER>$_POST[tribe] $_POST[item] $verb $_POST[amount]</CENTER><BR>";

}

echo '<BR><P>';
TEXT_GOTOMAIN();
include("footer.php");
?>
