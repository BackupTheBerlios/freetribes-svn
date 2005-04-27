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

$res = $db->Execute("SELECT * FROM $dbtables[chiefs] "
                   ."WHERE clanid = '$_SESSION[clanid]'");
$playerinfo = $res->fields;

echo "<P>";

if( !get_magic_quotes_gpc() )
{
    $_REQUEST[subject] = addslashes($_REQUEST[subject]);
    $_REQUEST[content] = addslashes($_REQUEST[content]);
    $_REQUEST[message] = addslashes($_REQUEST[message]);
}

if ( !ISSET($_REQUEST['view']) )
{
	$_REQUEST['view'] = "Inbox";
}


if ( ISSET($_REQUEST['last_view']) && $_REQUEST['last_view']<>$_REQUEST['view'])
{
	unset ( $_REQUEST['ID'] );
}

/*
echo "<PRE>";
print_r ($_REQUEST);
echo "</PRE>";
*/

echo "<CENTER>";

///////////////////////////////Process Deletes/////////////////////////////////////////
if( !ISSET($_REQUEST[view]) && !ISSET($_SESSION[view]) )
{
    $_SESSION[view] = 'Inbox';
}
if( $_REQUEST['delete'] == 'Delete' && $_REQUEST['view'] == 'Inbox' )
{
    $db->Execute("DELETE FROM $dbtables[messages] "
                ."WHERE ID = '$_REQUEST[ID]'");
}
elseif( $_REQUEST['delete'] == 'Delete' && $_REQUEST['view'] == 'Outbox' )
{
    $db->Execute("DELETE FROM $dbtables[outbox] "
                ."WHERE ID = '$_REQUEST[ID]'");
}

////////////////////////////////Process Alliance Proposals/////////////////////////////

if( $_REQUEST[alliance] == 1 )
{
    $present = $db->Execute("SELECT * FROM $dbtables[alliances] "
                           ."WHERE offerer_id = '$_SESSION[clanid]' "
                           ."AND receipt_id = '$_REQUEST[recmsg]' "
                           ."OR receipt_id = '$_SESSION[clanid]' "
                           ."AND offerer_id = '$_REQUEST[recmsg]'");
    $presented = $present->fields;
    if( $present->EOF )
    {
        $db->Execute("INSERT INTO $dbtables[alliances] "
                    ."VALUES("
                    ."'',"
                    ."'$_SESSION[clanid]',"
                    ."'$_REQUEST[recmsg]',"
                    ."'N')");

        $corps = $db->Execute("SELECT * FROM $dbtables[skills] "
                             ."WHERE level > 11 "
                             ."AND abbr = 'spy' "
                             ."AND tribeid <> $_SESSION[clanid].00 "
                             ."AND tribeid <> '$_REQUEST[recmsg].00'");
        while( !$corps->EOF )
        {
            $corpsdedip = $corps->fields;
            $clan = $db->Execute("SELECT * FROM $dbtables[tribes] "
                                ."WHERE tribeid = '$corpsdedip[tribeid]'");
            $claninfo = $clan->fields;
            $db->Execute("INSERT INTO $dbtables[logs] "
                        ."VALUES("
                        ."'',"
                        ."'$month[count]',"
                        ."'$year[count]',"
                        ."'$claninfo[clanid]',"
                        ."'$corpsdedip[tribeid]',"
                        ."'DIPSPY',"
                        ."'$stamp',"
                        ."'Spy Report: $_SESSION[clanid] has offered alliance with $_REQUEST[recmsg]!')");
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
if( $_REQUEST[maptrans] == 1 )
{
    $present = $db->Execute("SELECT * FROM $dbtables[alliances] "
                           ."WHERE offerer_id = '$_SESSION[clanid]' "
                           ."AND receipt_id = '$_REQUEST[recmsg]' "
                           ."AND accept = 'Y' " 
                           ."OR receipt_id = '$_SESSION[clanid]' "
                           ."AND offerer_id = '$_REQUEST[recmsg]' "
                           ."AND accept = 'Y'");
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
    if( $curam[curam] >= $transcost )
    {
        $mapto = $db->Execute("SELECT hex_id, `$_SESSION[clanid]`  FROM $dbtables[mapping] "
                             ."WHERE `clanid_$_SESSION[clanid]` > '0'");
        $bef = $db->Execute("SELECT COUNT(hex_id) AS before FROM $dbtables[mapping] "
                           ."WHERE `clanid_$_REQUEST[recmsg]` > '0'");
        $total = $bef->fields; 
        $i = 0;
        while( !$mapto->EOF )
        { 
            $mappingtransfer = $mapto->fields;
            $db->Execute("UPDATE $dbtables[mapping] "
                        ."SET `clanid_$_REQUEST[recmsg]` = 'clanid_$_SESSION[clanid]' "
                        ."WHERE hex_id = '$mappingtransfer[hex_id]' "
                        ."AND `clanid_$_REQUEST[recmsg]` = '0'");
            $i++;
            $db->Execute("UPDATE $dbtables[mapping] "
                        ."SET `admin_0000` = 'clanid_$_SESSION[clanid]' "
                        ."WHERE hex_id = '$mappingtransfer[hex_id]' "
                        ."AND `admin_0000` = '0'");
            $mapto->MoveNext();
        }
        $aft = $db->Execute("SELECT COUNT(hex_id) AS after FROM $dbtables[mapping] "
                           ."WHERE `clanid_$_REQUEST[recmsg]` > '0'");
        $after = $aft->fields;
        $tiles = $after[after] - $total[before];
        echo "We have transferred $tiles map tiles of mapping information to $_REQUEST[recmsg]<BR>";
        $db->Execute("INSERT INTO $dbtables[logs] "
                    ."VALUES("
                    ."'',"
                    ."'$month[count]',"
                    ."'$year[count]',"
                    ."'$_REQUEST[recmsg]',"
                    ."'$_REQUEST[recmsg].00',"
                    ."'MAPUPDATE',"
                    ."'$stamp',"
                    ."'Diplomacy: We have received $tiles tiles of "
                    ."mapping information from $_SESSION[clanid], "
                    ."located at $_SESSION[hex_id]')");
        $db->Execute("UPDATE $dbtables[tribes] "
                    ."SET curam = curam - $transcost "
                    ."WHERE tribeid = '$_SESSION[clanid]'");
    }
    else
    {
        echo "We do not have the required $transcost actives to send the mapping information.<BR>";
    }

}

///////////////////////////////////View Inbox///////////////////////////////////////////
if( !$_REQUEST[compose] == 'Compose' && !$_REQUEST[reply] == 'Reply' )
{
    echo "<FORM ACTION=mailto.php METHOD=POST>"
		."<INPUT TYPE=HIDDEN NAME=last_view VALUE=$_REQUEST[view]>";
    echo "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0><TR><TD>";
    if( $_REQUEST[view] == 'Outbox' )
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
    if( $_REQUEST[view] == 'Outbox' )
    {
        echo "<TD>&nbsp;</TD><TD>To:</TD><TD>Subject</TD><TD></TD></TR>";
    }
    else
    {
        echo "<TD>&nbsp;</TD><TD>From:</TD><TD>Subject</TD><TD></TD></TR>";
    }

    if( $_REQUEST[view] == 'Outbox' )
    {
        $mes = $db->Execute("SELECT * FROM $dbtables[outbox] "
                           ."WHERE sender_id='$_SESSION[clanid]' "
                           ."ORDER BY sent DESC");
    }
    else
    {
        $mes = $db->Execute("SELECT * FROM $dbtables[messages] "
                           ."WHERE recp_id='$_SESSION[clanid]' "
                           ."ORDER BY sent DESC");
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
        if( $_SESSION[view] == 'Outbox' )
        {
            $result = $db->Execute("SELECT * FROM $dbtables[chiefs] "
                                  ."WHERE clanid = '$message[recp_id]'");
        }
        else
        {
            $result = $db->Execute("SELECT * FROM $dbtables[chiefs] "
                                  ."WHERE clanid = '$message[sender_id]'");
        }
        if( $result->EOF && !$message[sender_id] == '0000' ) 
        {
            $chiefinfo[chiefname] = 'Deleted';
        }
        else
        {
            if( $message[sender_id] == '0000' )
            {
                $chiefinfo[chiefname] = 'Fair Merchant';
            }
            else
            {
                $chiefinfo = $result->fields;
                if( empty( $chiefinfo[chiefname] ) )
                {
                    $chiefinfo[chiefname] = 'Deleted';
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
        if( empty( $message[subject] ) )
        {
            $message[subject] = '(no subject)';
        }
        if( $_REQUEST['view'] == 'Outbox' )
        {
            echo "<TR CLASS=color_row$rc>";
		    echo "<FORM ACTION=mailto.php METHOD=POST>"
				."<INPUT TYPE=HIDDEN NAME=view VALUE=Outbox>"
				."<INPUT TYPE=HIDDEN NAME=ID VALUE=$message[ID]>";
            if( $_REQUEST[ID] == $message[ID] )
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
            if( $_REQUEST[ID] == $message[ID] )
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

        if( $_REQUEST[ID] == $message[ID] )
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

if( $_REQUEST[compose] == 'Compose' || $_REQUEST[reply] == 'Reply' )
{
    if( !empty($_REQUEST[ID]) && $_REQUEST[reply] == 'Reply' )
    {
        $mes = $db->Execute("SELECT * FROM $dbtables[messages] "
                           ."WHERE ID = '$_REQUEST[ID]'");
        $messid = $mes->fields;
        $res = $db->Execute("SELECT * FROM $dbtables[chiefs] "
                           ."WHERE clanid = '$messid[sender_id]'");
        if( $res->EOF )
        {
            $res = $db->Execute("SELECT * FROM $dbtables[chiefs] "
//                               ."WHERE clanid != '$_SESSION[clanid]' "
                               ."ORDER BY chiefname ASC");
        }
    }
    else
    {
        $res = $db->Execute("SELECT * FROM $dbtables[chiefs] "
//                           ."WHERE clanid != '$_SESSION[clanid]' "
                           ."ORDER BY chiefname ASC");
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
        if( $row[clanid] == $recmsg )
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
    $res = $db->Execute("SELECT * FROM $dbtables[chiefs] "
                       ."WHERE clanid = '$_SESSION[clanid]'");
    $playerinfo = $res->fields;

    echo "<TR CLASS=color_row1><TD>From</TD>";
    echo "<TD><INPUT CLASS=edit_area DISABLED TYPE=TEXT NAME=from SIZE=40 MAXLENGTH=40 VALUE=\"$playerinfo[chiefname]\"></TD></TR>";
    if( $messid[subject] )
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
    $recmsg = $_REQUEST[recmsg];
    $content = htmlspecialchars($_REQUEST[content]);
    $subject = htmlspecialchars($_REQUEST[subject]);
    $res = $db->Execute("SELECT * FROM $dbtables[chiefs] "
                       ."WHERE clanid = '$recmsg'");
    $target_info = $res->fields;
    if( $_REQUEST[Message] )
    {
        $db->Execute("INSERT INTO $dbtables[messages] "
                    ."VALUES("
                    ."'',"
                    ."'$playerinfo[clanid]',"
                    ."'$target_info[clanid]',"
                    ."'$subject',"
                    ."'$stamp',"
                    ."'$content',"
                    ."'N')");
        $db->Execute("INSERT INTO $dbtables[outbox] "
                    ."VALUES("
                    ."'',"
                    ."'$playerinfo[clanid]',"
                    ."'$target_info[clanid]',"
                    ."'$subject',"
                    ."'$stamp',"
                    ."'$content',"
                    ."'Y')");
    }
}

////////////////////////////////////Alliances////////////////////////////////////////////

if( !empty( $_REQUEST[broken] ) )
{
    $db->Execute("DELETE FROM $dbtables[alliances] "
                ."WHERE alliance_id = '$_REQUEST[broken]' "
                ."AND offerer_id = '$_SESSION[clanid]' "
                ."OR alliance_id = '$_REQUEST[broken]' "
                ."AND receipt_id = '$_SESSION[clanid]'");
}
if( $_REQUEST[alliance] )
{
    $db->Execute("UPDATE $dbtables[alliances] "
                ."SET accept = 'Y' "
                ."WHERE alliance_id = '$_REQUEST[alliance]'");
}


echo "<P><TABLE BORDER=0 WIDTH=80%><TR bgcolor=$color_header>";
echo "<TD COLSPAN=5>Current Alliances</TD></TR>";
echo "<TR bgcolor=$color_header><TD>With:</TD>";
echo "<TD>Status</TD><TD ALIGN=CENTER>Retract/Break</TD>";
echo "<TD ALIGN=CENTER>Accept</TD><TD>&nbsp;</TD></TR>";

$alliances = $db->Execute("SELECT * FROM $dbtables[alliances] "
                         ."WHERE offerer_id = '$_SESSION[clanid]' "
                         ."OR receipt_id = '$_SESSION[clanid]'");
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
    if( $_SESSION[clanid] == $allinfo[receipt_id] )
    {
        $chief = $db->Execute("SELECT * FROM $dbtables[chiefs] "
                             ."WHERE clanid = '$allinfo[offerer_id]'");
        $clanid = $allinfo[offerer_id];
    }
    elseif( $_SESSION[clanid] == $allinfo[offerer_id] )
    {
        $chief = $db->Execute("SELECT * FROM $dbtables[chiefs] "
                             ."WHERE clanid = '$allinfo[receipt_id]'");
        $clanid = $allinfo[receipt_id];
    }
    $chiefinfo = $chief->fields;
    echo "<TR bgcolor=$linecolor><TD>$chiefinfo[chiefname] ($clanid)</TD>";
    if( $allinfo[accept] == 'Y' )
    {
        echo "<TD>Established/Accepted</TD>";
        echo "<TD><FORM ACTION=mailto.php METHOD=POST>";
        echo "<INPUT TYPE=CHECKBOX NAME=broken VALUE=$allinfo[alliance_id]>&nbsp;Break</TD>";
        echo "<TD>&nbsp;</TD><TD><INPUT TYPE=SUBMIT VALUE=Commit></TD></FORM></TR>";
    }
    elseif( $allinfo[accept] == 'N' && $allinfo[offerer_id] == $_SESSION[clanid] )
    {
        echo "<TD>Pending acceptance</TD>";
        echo "<TD><FORM ACTION=mailto.php METHOD=POST>";
        echo "<INPUT TYPE=CHECKBOX NAME=broken VALUE=$allinfo[alliance_id]>&nbsp;Decline</TD>";
        echo "<TD>&nbsp;</TD><TD><INPUT TYPE=SUBMIT VALUE=Decline></FORM</TD></TR>";
    }
    elseif( $allinfo[accept] == 'N' && $allinfo[receipt_id] == $_SESSION[clanid] )
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
$filename = __FILE__;
$filename = explode('/', $filename);
$extension = 'txt';
$linkname = str_replace('php', $extension, $filename[5]);
$filename = $linkname;

page_footer();
?>
