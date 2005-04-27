<?
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
die("You Do Not	Have permissions to view this page!");
}
include("config.php");
include("game_time.php");

page_header("Account Admin - Tribe Reimbursement");

connectdb();

$username = $_SESSION['username'];
$admin = $db->Execute("SELECT * FROM $dbtables[chiefs] WHERE username = '$username'");
$admininfo = $admin->fields;


$module = $_REQUEST[menu];

if( !$admininfo[admin] >= $privilege['adm_accounts'] )
{
    echo "You must have account admin privilege to use this page.<BR>\n";
    page_footer();
}


////////////////////////////////////First, display a list of tribes to move.//////////////////
if( $_REQUEST[newhex] == '' && $_REQUEST[tribe] == '' )
{
    $res = $db->Execute("SELECT * FROM $dbtables[tribes] "
                       ."WHERE tribeid = goods_tribe "
                       ."ORDER BY tribeid");
    echo '<CENTER><FONT SIZE=+2 COLOR=WHITE>Reimburse which tribe?</FONT>';
    echo '<CENTER><TABLE BORDER=0 CELLPADDING=0 WIDTH=60%><TR ALIGN=CENTER><TD>';
    echo '<BR>Select a Tribe:</TD><TD>';
    echo '<FORM ACTION=admin_tribe_reimburse.php METHOD=POST>';
    echo '<SELECT NAME=tribe>';
    while( !$res->EOF)
    {
        $tribe = $res->fields;
        $chf = $db->Execute("SELECT * FROM $dbtables[chiefs] "
                           ."WHERE clanid = '$tribe[clanid]'");
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

if( !$_REQUEST[type] == '' && !$_REQUEST[tribe] == '' && !$_REQUEST[action] == '')
{
    echo "<CENTER><TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0><TR><TD>";
    echo '<FORM ACTION=admin_tribe_reimburse.php METHOD=POST>';
    echo '<SELECT NAME=item>';
    if( $_REQUEST[type] == 'resource' )
    {
        $reim = $db->Execute("SELECT DISTINCT long_name, dbname FROM $dbtables[resources] ORDER BY long_name");
        while( !$reim->EOF )
        {
            $reimburse = $reim->fields;
            echo "<OPTION VALUE=\"$reimburse[dbname]\">$reimburse[long_name]</OPTION>";
            $reim->MoveNext();
        }
    }
    elseif( $_REQUEST[type] == 'livestock' )
    {
        $reim = $db->Execute("SELECT DISTINCT type FROM $dbtables[livestock] ORDER BY type");
        while( !$reim->EOF )
        {
            $reimburse = $reim->fields;
            echo "<OPTION VALUE=$reimburse[type]>$reimburse[type]</OPTION>";
            $reim->MoveNext();
        }
    }
    elseif( $_REQUEST[type] == 'products' )
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
    if( $_REQUEST[action] == 'add' )
    {
        echo " + ";
    }
    elseif( $_REQUEST[action] == 'set' )
    {
        echo " = ";
    }
    elseif( $_REQUEST[action] == 'subtract' )
    {
        echo " - ";
    }
    echo "<INPUT CLASS=edit_area TYPE=TEXT NAME=amount VALUE=''>";
    echo "</TD></TR>";
    echo "<INPUT TYPE=HIDDEN NAME=tribe VALUE=$_REQUEST[tribe]>";
    echo "<INPUT TYPE=HIDDEN NAME=action VALUE=$_REQUEST[action]>";
    echo "<INPUT TYPE=HIDDEN NAME=type VALUE=$_REQUEST[type]>";
    echo "<TR ALIGN=CENTER><TD COLSPAN=2>";
    echo "<INPUT TYPE=SUBMIT VALUE='Reimburse'>";
    echo "</TD></FORM></TABLE>";
}

if( !$_REQUEST[amount] == '' && !$_REQUEST[item] == '' )
{

    if( $_REQUEST[type] == 'resource' )
    {
        if( $_REQUEST[action] == 'add' )
        {
            $db->Execute("UPDATE $dbtables[resources] "
                        ."SET amount = amount + $_REQUEST[amount] "
                        ."WHERE tribeid = '$_REQUEST[tribe]' "
                        ."AND dbname = '$_REQUEST[item]'");
            $verb = 'added to';
        }
        elseif( $_REQUEST[action] == 'set' )
        {
            $db->Execute("UPDATE $dbtables[resources] "
                        ."SET amount = $_REQUEST[amount] "
                        ."WHERE tribeid = '$_REQUEST[tribe]' "
                        ."AND dbname = '$_REQUEST[item]'");
            $verb = 'set to';
        }
        elseif( $_REQUEST[action] == 'subtract' )
        {
            $db->Execute("UPDATE $dbtables[resources] "
                        ."SET amount = amount - '$_REQUEST[amount] "
                        ."WHERE tribeid = '$_REQUEST[tribe]' "
                        ."AND dbname = '$_REQUEST[item]'");
            $verb = 'subtracted from';
        }
    }
    elseif( $_REQUEST[type] == 'livestock' )
        {
        if( $_REQUEST[action] == 'add' )
        {
            $db->Execute("UPDATE $dbtables[livestock] "
                        ."SET amount = amount + $_REQUEST[amount] "
                        ."WHERE tribeid = '$_REQUEST[tribe]' "
                        ."AND type = '$_REQUEST[item]'");
            $verb = 'added to';
        }
        elseif( $_REQUEST[action] == 'set' )
        {
            $db->Execute("UPDATE $dbtables[livestock] "
                        ."SET amount = $_REQUEST[amount] "
                        ."WHERE tribeid = '$_REQUEST[tribe]' "
                        ."AND type = '$_REQUEST[item]'");
            $verb = 'set to';
        }
        elseif( $_REQUEST[action] == 'subtract' )
        {
            $db->Execute("UPDATE $dbtables[livestock] "
                        ."SET amount = amount - '$_REQUEST[amount] "
                        ."WHERE tribeid = '$_REQUEST[tribe]' "
                        ."AND type = '$_REQUEST[item]'");
            $verb = 'subtracted from';
        }
    }
    elseif( $_REQUEST[type] == 'products' )
        {
        if( $_REQUEST[action] == 'add' )
        {
            $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + $_REQUEST[amount] "
                        ."WHERE tribeid = '$_REQUEST[tribe]' "
                        ."AND long_name = '$_REQUEST[item]'");
            $verb = 'added to';
        }
        elseif( $_REQUEST[action] == 'set' )
        {
            $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = $_REQUEST[amount] "
                        ."WHERE tribeid = '$_REQUEST[tribe]' "
                        ."AND long_name = '$_REQUEST[item]'");
            $verb = 'set to';
        }
        elseif( $_REQUEST[action] == 'subtract' )
        {
            $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount - '$_REQUEST[amount] "
                        ."WHERE tribeid = '$_REQUEST[tribe]' "
                        ."AND long_name = '$_REQUEST[item]'");
            $verb = 'subtracted from';
        }
    }

    $db->Execute("INSERT INTO $dbtables[logs] "
                ."VALUES("
                ."'',"
                ."'$month[count]',"
                ."'$year[count]',"
                ."'0000',"
                ."'0000.00',"
                ."'REIMB',"
                ."'$stamp',"
                ."'Admin: $_REQUEST[amount] $_REQUEST[item] $verb $_REQUEST[tribe] by $_SESSION[clanid].')");

    echo "<CENTER>$_REQUEST[tribe] $_REQUEST[item] $verb $_REQUEST[amount]</CENTER><BR>";

}

echo '<BR><P>';
page_footer();
?>
