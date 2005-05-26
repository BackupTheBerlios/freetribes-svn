<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: mailto.php

session_start();
header("Cache-control: private");
include("config.php");
include("game_time.php");

page_header("Diplomacy");
connectdb();

$res = $db->Execute("SELECT * FROM $dbtables[chiefs] WHERE clanid = '$_SESSION[clanid]'");
db_op_result($res,__LINE__,__FILE__);
$playerinfo = $res->fields;

echo "<P>";

if( !get_magic_quotes_gpc() )
{
    $_POST['subject'] = addslashes($_POST['subject']);
    $_POST['content'] = addslashes($_POST['content']);
    $_POST['message'] = addslashes($_POST['message']);
}

if ( !ISSET($_POST['view']) )
{
    $_POST['view'] = "Inbox";
}


if ( ISSET($_POST['last_view']) && $_POST['last_view']<>$_POST['view'])
{
    unset ( $_POST['ID'] );
}

/*
echo "<PRE>";
print_r ($_POST);
echo "</PRE>";
*/

echo "<CENTER>";

///////////////////////////////Process Deletes/////////////////////////////////////////
if( !ISSET($_POST['view']) && !ISSET($_SESSION['view']) )
{
    $_SESSION['view'] = 'Inbox';
}
if( $_POST['delete'] == 'Delete' && $_POST['view'] == 'Inbox' )
{
    $res = $db->Execute("DELETE FROM $dbtables[messages] WHERE ID = '$_POST[ID]'");
    db_op_result($res,__LINE__,__FILE__);
}
elseif( $_POST['delete'] == 'Delete' && $_POST['view'] == 'Outbox' )
{
    $res = $db->Execute("DELETE FROM $dbtables[outbox] WHERE ID = '$_POST[ID]'");
    db_op_result($res,__LINE__,__FILE__);
}

////////////////////////////////Process Alliance Proposals/////////////////////////////

if( $_POST['alliance'] == 1 )
{
    $present = $db->Execute("SELECT * FROM $dbtables[alliances] WHERE offerer_id = '$_SESSION[clanid]' AND receipt_id = '$_POST[recmsg]' OR receipt_id = '$_SESSION[clanid]' AND offerer_id = '$_POST[recmsg]'");
    db_op_result($present,__LINE__,__FILE__);
    $presented = $present->fields;
    if( $present->EOF )
    {
        $res = $db->Execute("INSERT INTO $dbtables[alliances] VALUES ( '', '$_SESSION[clanid]', '$_POST[recmsg]', 'N')");
        db_op_result($res,__LINE__,__FILE__);
        $corps = $db->Execute("SELECT * FROM $dbtables[skills] WHERE level > 11 AND abbr = 'spy' AND tribeid <> $_SESSION[clanid].00 AND tribeid <> '$_POST[recmsg].00'");
        db_op_result($corps,__LINE__,__FILE__);
        while( !$corps->EOF )
        {
            $corpsdedip = $corps->fields;
            $clan = $db->Execute("SELECT * FROM $dbtables[tribes] WHERE tribeid = '$corpsdedip[tribeid]'");
            db_op_result($clan,__LINE__,__FILE__);
            $claninfo = $clan->fields;
            $res = $db->Execute("INSERT INTO $dbtables[logs] VALUES ('','$month[count]','$year[count]','$claninfo[clanid]','$corpsdedip[tribeid]','DIPSPY','$stamp','Spy Report: $_SESSION[clanid] has offered alliance with $_POST[recmsg]!')");
            db_op_result($res,__LINE__,__FILE__);
            $corps->MoveNext();
            $clan = array();
        }
    }
    else
    {
        echo "<script language=\"javascript\" type=\"text/javascript\">";
        echo "alert(\"Alliance has already been proposed!\")</SCRIPT>";
    }
}

//////////////////////////////////Process Map Transfers////////////////////////////////
if( $_POST['maptrans'] == 1 )
{
    $present = $db->Execute("SELECT * FROM $dbtables[alliances] WHERE offerer_id = '$_SESSION[clanid]' AND receipt_id = '$_POST[recmsg]' AND accept = 'Y' OR receipt_id = '$_SESSION[clanid]' AND offerer_id = '$_POST[recmsg]' AND accept = 'Y'");
    db_op_result($present,__LINE__,__FILE__);
    if( !$present->EOF )
    {
        $transcost = 250;
    }
    else
    {
        $transcost = 500;
    }

    $cur = $db->Execute("SELECT * FROM $dbtables[tribes] "
                       ."WHERE tribeid = '$_SESSION[clanid]'");
    $curam = $cur->fields;
    if( $curam['curam'] >= $transcost )
    {
        $mapto = $db->Execute("SELECT hex_id, `$_SESSION[clanid]`  FROM $dbtables[mapping] WHERE `clanid_$_SESSION[clanid]` > '0'");
         db_op_result($mapto,__LINE__,__FILE__);
        $bef = $db->Execute("SELECT COUNT(hex_id) AS before FROM $dbtables[mapping] WHERE `clanid_$_POST[recmsg]` > '0'");
        db_op_result($bef,__LINE__,__FILE__);
        $total = $bef->fields;
        $i = 0;
        while( !$mapto->EOF )
        {
            $mappingtransfer = $mapto->fields;
            $query = $db->Execute("UPDATE $dbtables[mapping] SET `clanid_$_POST[recmsg]` = 'clanid_$_SESSION[clanid]' WHERE hex_id = '$mappingtransfer[hex_id]' AND `clanid_$_POST[recmsg]` = '0'");
            db_op_result($query,__LINE__,__FILE__);
            $i++;
            $query = $db->Execute("UPDATE $dbtables[mapping] SET `admin_0000` = 'clanid_$_SESSION[clanid]' WHERE hex_id = '$mappingtransfer[hex_id]' AND `admin_0000` = '0'");
            db_op_result($query,__LINE__,__FILE__);
            $mapto->MoveNext();
        }
        $aft = $db->Execute("SELECT COUNT(hex_id) AS after FROM $dbtables[mapping] WHERE `clanid_$_POST[recmsg]` > '0'");
        db_op_result($aft,__LINE__,__FILE__);
        $after = $aft->fields;
        $tiles = $after['after'] - $total['before'];
        echo "We have transferred $tiles map tiles of mapping information to $_POST[recmsg]<BR>";
        $data = "We have transferred $tiles map tiles of mapping information to $_POST[recmsg]";
        playerlog("$_SESSION[current_unit]","$_SESSION[clanid]",'MAPSHARES',$month['count'],$year['count'],$data,$dbtables);
        $data = "Diplomacy: We have received $tiles tiles of mapping information from $_SESSION[clanid], located at $_SESSION[hex_id]";
        playerlog("$_POST[recmsg].00","$_POST[recmsg]",'MAPSHARES',$month['count'],$year['count'],$data,$dbtables);

        $query = $db->Execute("UPDATE $dbtables[tribes] SET curam = curam - $transcost WHERE tribeid = '$_SESSION[clanid]'");
        db_op_result($query,__LINE__,__FILE__);
    }
    else
    {
        echo "We do not have the required $transcost actives to send the mapping information.<BR>";
    }

}

///////////////////////////////////View Inbox///////////////////////////////////////////
if( !$_POST['compose'] == 'Compose' && !$_POST['reply'] == 'Reply' )
{
    echo "<FORM ACTION=mailto.php METHOD=POST>"
        ."<INPUT TYPE=HIDDEN NAME=last_view VALUE=$_POST[view]>";
    echo "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0><TR><TD>";
    if( $_POST['view'] == 'Outbox' )
    {
        echo "<INPUT TYPE=SUBMIT NAME=view VALUE=Inbox>";
        echo "<INPUT TYPE=SUBMIT NAME=compose VALUE=Compose>&nbsp;";
        echo "<B>Viewing OUTBOX</B>";
    }
    else
    {
        echo "<INPUT TYPE=SUBMIT NAME=view VALUE=Outbox>";
        echo "<INPUT TYPE=SUBMIT NAME=compose VALUE=Compose>&nbsp;";
        echo "<B>Viewing INBOX</B>";
    }
    echo "</TD></TR>"
        ."</FORM>"
        ."</TABLE>"
        ."<P>";

    echo "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=\"80%\">";
    echo "<TR BGCOLOR=$color_header>";
    if( $_POST['view'] == 'Outbox' )
    {
        echo "<TD>&nbsp;</TD><TD>To:</TD><TD>Subject</TD><TD></TD></TR>";
    }
    else
    {
        echo "<TD>&nbsp;</TD><TD>From:</TD><TD>Subject</TD><TD></TD></TR>";
    }

    if( $_POST['view'] == 'Outbox' )
    {
        $mes = $db->Execute("SELECT * FROM $dbtables[outbox] WHERE sender_id='$_SESSION[clanid]' ORDER BY sent DESC");
        db_op_result($mes,__LINE__,__FILE__);
    }
    else
    {
        $mes = $db->Execute("SELECT * FROM $dbtables[messages] WHERE recp_id='$_SESSION[clanid]' ORDER BY sent DESC");
        db_op_result($mes,__LINE__,__FILE__);
    }
    if( $mes->EOF )
    {
        echo "<TR CLASS=color_row0 ALIGN=CENTER><TD COLSPAN=4>No Messages</TD></TR>";
    }

    $line_counter = true;
    $r = 0;
    while( !$mes->EOF )
    {
        $rc = $r % 2;
        $r++;
        $message = $mes->fields;
        if( $_SESSION['view'] == 'Outbox' )
        {
            $result = $db->Execute("SELECT * FROM $dbtables[chiefs] WHERE clanid = '$message[recp_id]'");
            db_op_result($result,__LINE__,__FILE__);
        }
        else
        {
            $result = $db->Execute("SELECT * FROM $dbtables[chiefs] WHERE clanid = '$message[sender_id]'");
             db_op_result($result,__LINE__,__FILE__);
        }
        if( $result->EOF && !$message['sender_id'] == '0000' )
        {
            $chiefinfo['chiefname'] = 'Deleted';
        }
        else
        {
            if( $message['sender_id'] == '0000' )
            {
                $chiefinfo['chiefname'] = 'Fair Merchant';
            }
            else
            {
                $chiefinfo = $result->fields;
                if( empty( $chiefinfo['chiefname'] ) )
                {
                    $chiefinfo['chiefname'] = 'Deleted';
                }
            }
        }
        if( $line_counter )
        {
            $linecolor = $color_line2;
            $line_counter = false;
        }
        else
        {
            $linecolor = $color_line1;
            $line_counter = true;
        }
        if( empty( $message['subject'] ) )
        {
            $message['subject'] = '(no subject)';
        }
        if( $_POST['view'] == 'Outbox' )
        {
            echo "<TR CLASS=color_row$rc>";
            echo "<FORM ACTION=mailto.php METHOD=POST>"
                ."<INPUT TYPE=HIDDEN NAME=view VALUE=Outbox>"
                ."<INPUT TYPE=HIDDEN NAME=ID VALUE=$message[ID]>";
            if( $_POST['ID'] == $message['ID'] )
            {
                echo "<TD>"
                    ."<INPUT TYPE=HIDDEN NAME=ID VALUE=$message[ID]>"
                    ."<BR>&nbsp;"
                    ."</TD>";
                echo "<TD><B>$chiefinfo[chiefname]</B></TD>";
                echo "<TD><B>$message[subject]</B></TD>";
                echo "<TD><INPUT TYPE=SUBMIT NAME=delete VALUE=Delete><BR>&nbsp;</TD>";
            }
            else
            {
                echo "<TD>"
                    ."&nbsp;&nbsp;<INPUT TYPE=SUBMIT VALUE=View>"
                    ."<BR>&nbsp;"
                    ."</TD>";
                echo "<TD>$chiefinfo[chiefname]</TD>";
                echo "<TD>$message[subject]</TD>";
                echo "<TD><INPUT TYPE=SUBMIT NAME=delete VALUE=Delete><BR>&nbsp;</TD>";
            }
            echo "</FORM>";
            echo "</TR>";
        }
        else
        {
            echo "<FORM ACTION=mailto.php METHOD=POST>"
                ."<INPUT TYPE=HIDDEN NAME=view VALUE=Inbox>"
                ."<INPUT TYPE=HIDDEN NAME=ID VALUE=$message[ID]>";
            echo "<TR CLASS=color_row$rc>";
            if( $_POST['ID'] == $message['ID'] )
            {
                echo "<TD>"
                    ."&nbsp;&nbsp;<INPUT TYPE=SUBMIT NAME=reply VALUE=Reply>"
                    ."<BR>&nbsp;"
                    ."</TD>";
                echo "<TD><B>$chiefinfo[chiefname]</B></TD>";
                echo "<TD><B>$message[subject]</B></TD>";
                echo "<TD><INPUT TYPE=SUBMIT NAME=delete VALUE=Delete><BR>&nbsp;</TD>";
            }
            else
            {
                echo "<TD>"
                    ."&nbsp;&nbsp;<INPUT TYPE=SUBMIT VALUE=View>"
                    ."<BR>&nbsp;"
                    ."</TD>";
                echo "<TD>$chiefinfo[chiefname]</TD>";
                echo "<TD>$message[subject]</TD>";
                echo "<TD><INPUT TYPE=SUBMIT NAME=delete VALUE=Delete><BR>&nbsp;</TD>";
            }
            echo "</TR>";
            echo "</FORM>";
        }

        if( $_POST['ID'] == $message['ID'] )
        {
            echo "<TR><TD COLSPAN=4><TABLE WIDTH=\"100%\" BORDER=0 CELLPADDING=0 CELLSPACING=0>";
            echo "<TR CLASS=table1_td_cc>";
            echo "<TD>&nbsp;</TD>";
            echo "<TD><FONT COLOR=black><BR>$message[message]<P></FONT></TD>";
            echo "<TD>&nbsp;</TD></TR>";
            echo "</TABLE></TD></TR>";
        }
        $mes->MoveNext();
    }
    echo "</TABLE>";
}

/////////////////////////////////////Compose/////////////////////////////////////////////

if( $_POST['compose'] == 'Compose' || $_POST['reply'] == 'Reply' )
{
    if( !empty($_POST['ID']) && $_POST['reply'] == 'Reply' )
    {
        $mes = $db->Execute("SELECT * FROM $dbtables[messages] WHERE ID = '$_POST[ID]'");
        db_op_result($mes,__LINE__,__FILE__);
        $messid = $mes->fields;
        $res = $db->Execute("SELECT * FROM $dbtables[chiefs] WHERE clanid = '$messid[sender_id]'");
        db_op_result($res,__LINE__,__FILE__);
        if( $res->EOF )
        {
            $res = $db->Execute("SELECT * FROM $dbtables[chiefs] ORDER BY chiefname ASC");
            db_op_result($res,__LINE__,__FILE__);
        }
    }
    else
    {
        $res = $db->Execute("SELECT * FROM $dbtables[chiefs] ORDER BY chiefname ASC");
        db_op_result($res,__LINE__,__FILE__);
    }
    echo "<FORM ACTION=mailto.php METHOD=POST>";
    echo "<TABLE>"
        ."<TR><TD>"
        ."<INPUT TYPE=SUBMIT NAME=view VALUE=Inbox>"
        ."</TD>"
        ."<TD ALIGN=RIGHT></TD></TR>";
    echo "<TR CLASS=color_row1><TD>To</TD><TD><SELECT NAME=recmsg>";
    while( !$res->EOF )
    {
        $row=$res->fields;
        if( $row['clanid'] == $recmsg )
        {
            echo "\n<OPTION VALUE=$row[clanid] SELECTED>$row[chiefname]</OPTION>";
        }
        else
        {
            echo "\n<OPTION VALUE=$row[clanid]>$row[chiefname]</OPTION>";
        }
        $res->MoveNext();
    }
    echo "</SELECT></TD></TR>";
    $res = $db->Execute("SELECT * FROM $dbtables[chiefs] WHERE clanid = '$_SESSION[clanid]'");
     db_op_result($res,__LINE__,__FILE__);
    $playerinfo = $res->fields;

    echo "<TR CLASS=color_row1><TD>From</TD>";
    echo "<TD><INPUT CLASS=edit_area DISABLED TYPE=TEXT NAME=from SIZE=40 MAXLENGTH=40 VALUE=\"$playerinfo[chiefname]\"></TD></TR>";
    if( $messid['subject'] )
    {
        echo "<TR CLASS=color_row1><TD>Subject</TD>";
        echo "<TD><INPUT CLASS=edit_area TYPE=TEXT NAME=subject SIZE=40 MAXLENGTH=40 VALUE=\"RE: $messid[subject]\"></TD></TR>";
    }
    else
    {
        echo "<TR CLASS=color_row1><TD>Subject</TD>";
        echo "<TD><INPUT CLASS=edit_area TYPE=TEXT NAME=subject SIZE=40 MAXLENGTH=40></TD></TR>";
    }
    echo "<TR CLASS=color_row0><TD>&nbsp;</TD>";
    echo "<TD><INPUT TYPE=checkbox NAME=maptrans VALUE='1'>Send Mapping Info ";
    echo "<INPUT TYPE=checkbox NAME=alliance VALUE='1'>Offer Alliance</TD></TR>";
    echo "<TR CLASS=color_row1><TD VALIGN=TOP CLASS=color_row1>Message</TD>";
    echo "<TD><TEXTAREA CLASS=edit_area NAME=content ROWS=8 COLS=80></TEXTAREA></TD></TR>";
    echo "<TR CLASS=color_row0><TD></TD>";
    echo "<TD ALIGN=RIGHT>"
        ."<INPUT TYPE=RESET VALUE=\"Clear Message\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"
        ."<INPUT TYPE=SUBMIT NAME=Message VALUE=Send>";
    echo "</TD>"
        ."</TR>";
    echo "</TABLE>";
    echo "</FORM>";
}
else
{
    $recmsg = $_POST['recmsg'];
    $content = htmlspecialchars($_POST['content']);
    $subject = htmlspecialchars($_POST['subject']);
    $res = $db->Execute("SELECT * FROM $dbtables[chiefs] WHERE clanid = '$recmsg'");
    db_op_result($res,__LINE__,__FILE__);
    $target_info = $res->fields;
    if( $_POST['Message'] )
    {
        $res = $db->Execute("INSERT INTO $dbtables[messages] VALUES ('','$playerinfo[clanid]','$target_info[clanid]','$subject','$stamp','$content','N')");
        db_op_result($res,__LINE__,__FILE__);
        $res = $db->Execute("INSERT INTO $dbtables[outbox] VALUES ('','$playerinfo[clanid]','$target_info[clanid]','$subject','$stamp','$content','Y')");
       db_op_result($res,__LINE__,__FILE__);
    }
}

////////////////////////////////////Alliances////////////////////////////////////////////

if( !empty( $_POST['broken'] ) )
{
    $res = $db->Execute("DELETE FROM $dbtables[alliances] WHERE alliance_id = '$_POST[broken]' AND offerer_id = '$_SESSION[clanid]' OR alliance_id = '$_POST[broken]' AND receipt_id = '$_SESSION[clanid]'");
     db_op_result($res,__LINE__,__FILE__);
}
if( $_POST['alliance'] )
{
    $res = $db->Execute("UPDATE $dbtables[alliances] SET accept = 'Y' WHERE alliance_id = '$_POST[alliance]'");
    db_op_result($res,__LINE__,__FILE__);
}


echo "<P><TABLE BORDER=0 WIDTH=80%><TR bgcolor=$color_header>";
echo "<TD COLSPAN=5>Current Alliances</TD></TR>";
echo "<TR bgcolor=$color_header><TD>With:</TD>";
echo "<TD>Status</TD><TD ALIGN=CENTER>Retract/Break</TD>";
echo "<TD ALIGN=CENTER>Accept</TD><TD>&nbsp;</TD></TR>";

$alliances = $db->Execute("SELECT * FROM $dbtables[alliances] WHERE offerer_id = '$_SESSION[clanid]' OR receipt_id = '$_SESSION[clanid]'");
db_op_result($alliances,__LINE__,__FILE__);
if( $alliances->EOF )
{
    echo "<TR CLASS=color_row0 ALIGN=CENTER><TD COLSPAN=5>None</TD></TR>";
}
$linecolor = $color_line1;
while( !$alliances->EOF )
{
    $allinfo = $alliances->fields;
    if( $linecolor = $color_line2 )
    {
        $linecolor = $color_line1;
    }
    if( $_SESSION['clanid'] == $allinfo['receipt_id'] )
    {
        $chief = $db->Execute("SELECT * FROM $dbtables[chiefs] WHERE clanid = '$allinfo[offerer_id]'");
        db_op_result($chief,__LINE__,__FILE__);
        $clanid = $allinfo['offerer_id'];
    }
    elseif( $_SESSION['clanid'] == $allinfo['offerer_id'] )
    {
        $chief = $db->Execute("SELECT * FROM $dbtables[chiefs] WHERE clanid = '$allinfo[receipt_id]'");
        db_op_result($chief,__LINE__,__FILE__);
        $clanid = $allinfo['receipt_id'];
    }
    $chiefinfo = $chief->fields;
    echo "<TR bgcolor=$linecolor><TD>$chiefinfo[chiefname] ($clanid)</TD>";
    if( $allinfo['accept'] == 'Y' )
    {
        echo "<TD>Established/Accepted</TD>";
        echo "<TD><FORM ACTION=mailto.php METHOD=POST>";
        echo "<INPUT TYPE=CHECKBOX NAME=broken VALUE=$allinfo[alliance_id]>&nbsp;Break</TD>";
        echo "<TD>&nbsp;</TD><TD><INPUT TYPE=SUBMIT VALUE=Commit></TD></FORM></TR>";
    }
    elseif( $allinfo['accept'] == 'N' && $allinfo['offerer_id'] == $_SESSION['clanid'] )
    {
        echo "<TD>Pending acceptance</TD>";
        echo "<TD><FORM ACTION=mailto.php METHOD=POST>";
        echo "<INPUT TYPE=CHECKBOX NAME=broken VALUE=$allinfo[alliance_id]>&nbsp;Decline</TD>";
        echo "<TD>&nbsp;</TD><TD><INPUT TYPE=SUBMIT VALUE=Decline></FORM</TD></TR>";
    }
    elseif( $allinfo['accept'] == 'N' && $allinfo['receipt_id'] == $_SESSION['clanid'] )
    {
        echo "<TD>Pending acceptance</TD>";
        echo "<TD><FORM ACTION=mailto.php METHOD=POST>";
        echo "<INPUT TYPE=CHECKBOX NAME=broken VALUE=$allinfo[alliance_id]>&nbsp;Break</TD>";
        echo "<TD><INPUT TYPE=CHECKBOX NAME=alliance VALUE=$allinfo[alliance_id]>Accept</TD>";
        echo "<TD><INPUT TYPE=SUBMIT VALUE=COMMIT></FORM></TD></TR>";
    }
    $alliances->MoveNext();
}
echo "</TABLE>";
echo "<P><BR><BR>";

page_footer();
?>
