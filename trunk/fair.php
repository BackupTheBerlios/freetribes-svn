<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: fair.php

session_start();
header("Cache-control: private");

include("config.php");
include("game_time.php");

page_header("Welcome to the Fair!");


echo "<TABLE BORDER=0 CELLSPACING=4>"
    ."<TR>"
    ."<FORM METHOD=POST ACTION=fair.php>"
    ."<TD>"
    ."<INPUT TYPE=HIDDEN NAME=culture VALUE=1>"
    ."<INPUT TYPE=HIDDEN NAME=transactions VALUE=B>"
    ."<INPUT TYPE=SUBMIT NAME=op VALUE=Buy>"
    ."</TD>"
    ."</FORM>"
    ."<FORM METHOD=POST ACTION=fair.php>"
    ."<TD>"
    ."<INPUT TYPE=HIDDEN NAME=culture VALUE=1>"
    ."<INPUT TYPE=HIDDEN NAME=transactions VALUE=S>"
    ."<INPUT TYPE=SUBMIT NAME=op VALUE=Sell>"
    ."</TD>"
    ."</FORM>"
    ."<FORM METHOD=POST ACTION=fair.php>"
    ."<TD>"
    ."<INPUT TYPE=HIDDEN NAME=culture VALUE=1>"
    ."<INPUT TYPE=HIDDEN NAME=fair1 VALUE=YC>"
    ."<INPUT TYPE=SUBMIT NAME=op VALUE=Cultural>"
    ."</TD>"
    ."</FORM>"
    ."<TD VALIGN=TOP>";

switch ($_REQUEST['op'])
{
    case "Buy":
        echo "<FONT CLASS=page_subtitle>Buying ...</FONT>";
        break;
    case "Sell":
        echo "<FONT CLASS=page_subtitle>Selling ...</FONT>";
        break;
    case "Cultural":
        echo "<FONT CLASS=page_subtitle>Cultural ...</FONT>";
        break;
    default:
}


echo "</TD>"
    ."</TR>"
    ."</TABLE>";



function display_transactions()
{
    global $db, $dbtables;


    $res5 = $db->Execute("SELECT * FROM $dbtables[fair_tribe] "
                        ."WHERE clan_id = '$_SESSION[clanid]' "
                        ."ORDER BY trans_id");

    echo "<P>"
        ."<CENTER>"
        ."<TABLE CLASS=color_table BORDER=1 CELLPADDING=4 CELLSPACING=0>"
        ."<TR CLASS=color_header>"
        ."<TD>Buy/Sell</TD>"
        ."<TD>Product</TD>"
        ."<TD>Quantity</TD>"
        ."<TD>Price</TD>"
        ."<TD>Total</TD>"
        ."<TD>Cancel</TD>"
        ."</TR>";

    $r = 0;
    while( !$res5->EOF )
    {
        $rc = $r % 2;
        $r++;
        $output = $res5->fields;
        echo "<TR CLASS=color_row$rc>";

        if( $output[buy_sell] == 'B' )
        {
            echo "<TD>Buy</TD>";
        }
        elseif( $output[buy_sell] == 'S' )
        {
            echo "<TD>Sell</TD>";
        }
        elseif( $output[buy_sell] == 'C' )
        {
            echo "<TD>Culture</TD>";
        }
        $total = $output[price] * $output[quantity];

        if( $output[buy_sell] == 'C' )
        {
            $total = $output[price];
        }

        echo "<TD>$output[product]</TD>"
            ."<TD>$output[quantity]</TD>"
            ."<TD>$output[price]</TD>"
            ."<TD>";
        $total = NUMBER($total);
        echo "$total</TD>"
            ."<TD>"
            ."<FORM ACTION=fair.php METHOD=POST><INPUT TYPE=HIDDEN NAME=transaction VALUE=\"$output[buy_sell]\">"
            ."<INPUT TYPE=HIDDEN NAME=quantity VALUE=\"$output[quantity]\">"
            ."<INPUT TYPE=HIDDEN NAME=cancel_trans_id VALUE=\"$output[trans_id]\">"
            ."<INPUT TYPE=HIDDEN NAME=cancel_product VALUE=\"$output[product]\">"
            ."<INPUT TYPE=SUBMIT VALUE=CANCEL>"
            ."</FORM>"
            ."</TD>"
            ."</TR>";
        $res5->MoveNext();
    }

    echo "</TABLE>"
        ."</CENTER>";
}

connectdb();

$clanid = $_SESSION['clanid'];

$res = $db->Execute("SELECT * FROM $dbtables[tribes] "
                   ."WHERE tribeid = '$_SESSION[current_unit]' "
                   ."AND hex_id = '$_SESSION[hex_id]'");
$tribeinfo = $res->fields;

$res2 = $db->Execute("SELECT level FROM $dbtables[skills] "
                    ."WHERE tribeid = '$tribeinfo[tribeid]' "
                    ."AND abbr = 'eco'");
$skillinfo = $res2->fields;

$once = $db->Execute("SELECT * FROM $dbtables[fair_tribe] "
                    ."WHERE clan_id = '$tribeinfo[clanid]' "
                    ."AND tribeid != '$tribeinfo[tribeid]'");

$limit = $db->Execute("SELECT * FROM $dbtables[fair_tribe] "
                     ."WHERE clan_id = '$tribeinfo[clanid]' "
                     ."AND product = '$_REQUEST[product]'");

if( !$limit->EOF )
{
    $_REQUEST['product'] = '';
    $_REQUEST['buy_sell'] = '';
    $_REQUEST['quantity'] = '';
}

if( !$once->EOF )
{
    $oncetribe = $once->fields;
    echo "<CENTER>";
    echo "Sorry, but you have started conducting fair transactions with<BR> ";
    echo "$oncetribe[tribeid] ";
    echo "and are allowed only one tribe per fair to participate.<BR>";
    echo "</CENTER>";
    page_footer();
}

if( !$month['count'] == 4 && !$month['count'] == 10 )
{
    echo "<CENTER>";
    echo "Fairs are conducted on the 4th and 10th month of each year ONLY.<br>";
    echo "</CENTER>";
    page_footer();
}

if( !$tribeinfo['tribeid'] == $_SESSION['clanid'] )
{
    echo "<CENTER>";
    echo "Only your main tribe may participate in a fair.";
    echo "</CENTER>";
    page_footer();
}

if( $_REQUEST['cancel_trans_id'] )
{
    $db->Execute("DELETE FROM $dbtables[fair_tribe] "
                ."WHERE clan_id = '$tribeinfo[clanid]' "
                ."AND trans_id = '$_REQUEST[cancel_trans_id]'");

    if( $_REQUEST['transaction'] == 'B' )
    {
        $db->Execute("UPDATE $dbtables[fair] "
                    ."SET amount = amount + '$_REQUEST[quantity]' "
                    ."WHERE proper_name = '$_REQUEST[product]'");
    }
    elseif( $_REQUEST['transaction'] == 'S' )
    {
        $db->Execute("UPDATE $dbtables[fair] "
                    ."SET amount = amount - '$_REQUEST[quantity]' "
                    ."WHERE proper_name = '$_REQUEST[product]'");
    }
    echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=fair.php\">";
}

$res3 = $db->Execute("SELECT count(trans_id) AS transactions FROM $dbtables[fair_tribe] "
                    ."WHERE clan_id = '$tribeinfo[clanid]'");
$transinfo = $res3->fields;

$struct = $db->Execute("SELECT * FROM $dbtables[structures] "
                      ."WHERE clanid = '$tribeinfo[clanid]' "
                      ."AND complete = 'Y' "
                      ."AND long_name = 'tradepost' "
                      ."AND hex_id = '$tribeinfo[hex_id]'");

if( $skillinfo['level'] <= '4' && $struct->EOF )
{
    echo "<CENTER>";
    echo "Sorry, but you have not gained enough skill in economics.<BR>";
    echo "You can conduct one transaction per level of economics after level 5.<BR>";
    echo "Fairs occur on the fourth and tenth month of each year.<BR><BR>";
    echo "Your economics level is ";
    echo "$skillinfo[level]";
    echo ".<BR>";
    echo "</CENTER>";

    $res5 = $db->Execute("SELECT * FROM $dbtables[fair_tribe] "
                        ."WHERE clan_id = '$_SESSION[clanid]' "
                        ."ORDER BY trans_id");
    display_transactions();
    page_footer();
}

if( $transinfo['transactions'] >= $skillinfo['level'] )
{
    echo "<CENTER>";
    echo "Sorry, but you have reached the limit of your fair activities.<BR>";
    echo "You can conduct one transaction per level of economics.<BR>";
    echo "Fairs occur on the fourth and tenth month of each year.<BR>";
    echo "Your economics level is ";
    echo "$skillinfo[level]";
    echo ".<BR>";
    echo "You have already completed ";
    echo "$transinfo[transactions]";
    echo " transactions.<br>";
    echo "</CENTER>";

    display_transactions();
    page_footer();
}

    if( $res3->EOF && $skillinfo['level'] > 4 )
    {
        $available_trans = $skillinfo['level'];
    }

    if( !$res3->EOF && $skillinfo['level'] > $transinfo['transactions'] )
    {
        $available_trans = $skillinfo['level'] - $transinfo['transactions'];
    }



    if( !ISSET($_REQUEST['culture']) )
    {
        echo "<CENTER>"
            ."<TABLE BORDER=0 CELLPADDING=4 CELLSPACING=0 ALIGN=CENTER>"
            ."<FORM ACTION=fair.php METHOD=POST>"
            ."<TR>"
            ."<TD VALIGN=MIDDLE>&nbsp;</TD>"
            ."<TD VALIGN=MIDDLE>&nbsp;</TD>"
            ."</TR>"
            ."</FORM>"
            ."</TABLE>"
            ."</CENTER>";

        display_transactions();
        page_footer();
    }

    if( ISSET($_REQUEST['culture']) && $_REQUEST['fair1'] == 'YC' && !ISSET($_REQUEST['type']) )
    {

        echo "<CENTER>"
            ."<TABLE CLASS=color_table BORDER=0 CELLPADDING=4 CELLSPACING=0>"
            ."<FORM ACTION=fair.php METHOD=POST>"
            ."<TR>"
            ."<TD>"
            ."<SELECT NAME=type>"
            ."<OPTION VALUE=tri>Triball</OPTION>"
            ."<OPTION VALUE=art>Art</OPTION>"
            ."<OPTION VALUE=dan>Dance</OPTION>"
            ."<OPTION VALUE=mus>Music</OPTION>"
            ."<OPTION VALUE=cook>Cooking</OPTION>"
            ."</SELECT>"
            ."</TD>"
            ."<TD>"
            ."<INPUT TYPE=HIDDEN NAME=culture VALUE=1>"
            ."<INPUT TYPE=HIDDEN NAME=fair1 VALUE=YC>"
            ."<INPUT TYPE=SUBMIT VALUE=SUBMIT>"
            ."</TD>"
            ."</TR>"
            ."</FORM>"
            ."</TABLE>"
            ."<P>";

        display_transactions();
        page_footer();
    }

    if(ISSET( $_REQUEST[culture]) && $_REQUEST[fair1] == 'YC' && ISSET($_REQUEST[type]) && !ISSET($_REQUEST[participants]) )
    {
        $cult = $db->Execute("SELECT * FROM $dbtables[skills] "
                            ."WHERE abbr = '$_REQUEST[type]' "
                            ."AND tribeid = '$tribeinfo[tribeid]'");
        $cultinfo = $cult->fields;

        echo "<CENTER>"
            ."<TABLE CLASS=color_table BORDER=0 CELLPADDING=4 CELLSPACING=0>"
            ."<TR>"
            ."<TD>"
            ."<FORM ACTION=fair.php METHOD=POST>"
            ."<SELECT NAME=participants>"
            ."<OPTION VALUE=50> 50</OPTION>"
            ."<OPTION VALUE=100>100</OPTION>"
            ."<OPTION VALUE=200>200</OPTION>"
            ."<OPTION VALUE=300>300</OPTION>"
            ."<OPTION VALUE=400>400</OPTION>"
            ."<OPTION VALUE=500>500</OPTION>"
            ."</SELECT>"
            ."<INPUT TYPE=HIDDEN NAME=culture VALUE=1>"
            ."<INPUT TYPE=HIDDEN NAME=type VALUE=$_REQUEST[type]>"
            ."<INPUT TYPE=HIDDEN NAME=fair1 VALUE=YC>"
            ."</TD>"
            ."<TD>"
            ."<INPUT TYPE=SUBMIT VALUE=SUBMIT>"
            ."</TD>"
            ."</TR>"
            ."</FORM>"
            ."</TABLE>"
            ."</CENTER>"
            ."<P>";
        display_transactions();
        page_footer();
    }

    if( ISSET($_REQUEST[culture]) && $_REQUEST[fair1] == 'YC' && ISSET($_REQUEST[type]) && ISSET($_REQUEST[participants]) )
    {
        if( $tribeinfo[curam] < $_REQUEST[participants] )
        {
            echo "<CENTER>Sorry, you do not have that many actives to participate.</CENTER>";
            echo "<CENTER>Please try again.</CENTER>";
        page_footer();
        }

        $cult = $db->Execute("SELECT * FROM $dbtables[skills] "
                            ."WHERE abbr = '$_REQUEST[type]' "
                            ."AND tribeid = '$tribeinfo[tribeid]'");
        $cultinfo = $cult->fields;

        $cult2 = $db->Execute("SELECT * FROM $dbtables[skills] "
                             ."WHERE abbr = 'eco' "
                             ."AND tribeid = '$tribeinfo[tribeid]'");
        $ecoinfo = $cult2->fields;

        if( $_REQUEST[participants] < 0 )
        {
            $_REQUEST[participants] = 0;
        }

        if( !$_REQUEST[type] == 'tri' )
        {
            $reward = $_REQUEST[participants] * (2 + $cultinfo[level]/4 + $ecoinfo[level]/4);
        }
        else
        {
            $reward = $_REQUEST[participants] * (2 + $cultinfo[level]/2 + $ecoinfo[level]/4);
        }

        echo "<CENTER>Your ";
        echo "$cultinfo[long_name]";
        echo " skills earn you ";
        echo "$reward";
        echo " silver at the fair.</CENTER>";
        echo "<CENTER>Click <a href=fair.php>here</a> to enter another fair order.</CENTER></P>";

        $count = $db->Execute("SELECT count(*) AS count FROM $dbtables[fair_tribe] "
                             ."WHERE clan_id = '$tribeinfo[tribeid]'");
        $countinfo = $count->fields;

        $count = $countinfo[count] + 1;

        $db->Execute("INSERT INTO $dbtables[fair_tribe] "
                    ."VALUES("
                    ."'$tribeinfo[clanid]',"
                    ."'$tribeinfo[tribeid]',"
                    ."'',"
                    ."'$cultinfo[level]',"
                    ."'C',"
                    ."'$cultinfo[long_name]',"
                    ."'$_REQUEST[participants]',"
                    ."'$reward'"
                    .")");

        $db->Execute("UPDATE $dbtables[tribes] "
                    ."SET curam = curam - $_REQUEST[participants] "
                    ."WHERE tribeid = '$tribeinfo[tribeid]'");

        display_transactions();
        page_footer();
    }

    if( ISSET($_REQUEST[culture]) && !ISSET($_REQUEST[trans_id]) && $_REQUEST[fair1] == 'NC' )
    {
        echo "<CENTER>"
            ."<TABLE CLASS=color_table BORDER=0 CELLPADDING=4 CELLSPACING=0>"
            ."<FORM ACTION=fair.php METHOD=POST>"
            ."<TR>"
            ."<TD>"
            ."<SELECT NAME=transactions>"
            ."<OPTION VALUE=B>Buy</OPTION>"
            ."<OPTION VALUE=S>Sell</OPTION>"
            ."</SELECT>"
            ."<INPUT TYPE=HIDDEN NAME=culture VALUE=1>"
            ."</TD>"
            ."<TD>"
            ."<INPUT TYPE=SUBMIT VALUE=SUBMIT>"
            ."</TD>"
            ."</TR>"
            ."</FORM>"
            ."</TABLE>"
            ."</CENTER>";
        display_transactions();
    }



if( ISSET($_REQUEST[culture]) && ISSET($_REQUEST[transactions]) && !ISSET($_REQUEST[product]) )
{
    echo "<CENTER><TABLE CLASS=color_table BORDER=0 CELLPADDING=4 CELLSPACING=0>"
        ."<FORM ACTION=fair.php METHOD=POST>"
        ."<TR>"
        ."<TD>"
        ."<SELECT NAME=item>";

    if( $_REQUEST[transactions] == 'B' )
    {
        $res4 = $db->Execute("SELECT * FROM $dbtables[fair] "
                            ."WHERE price_buy > 0 "
                            ."AND cultural = 'N' "
                            ."AND amount > 0 "
                            ."ORDER BY proper_name");
    }
    else
    {
        $res4 = $db->Execute("SELECT * FROM $dbtables[products] "
                            ."WHERE tribeid = '$_SESSION[current_unit]' "
                            ."AND amount > 0");

        $res5 = $db->Execute("SELECT * FROM $dbtables[livestock] "
                            ."WHERE tribeid = '$_SESSION[current_unit]' "
                            ."AND amount > 0");

        $res6 = $db->Execute("SELECT * FROM $dbtables[resources] "
                            ."WHERE tribeid = '$_SESSION[current_unit]' "
                            ."AND amount > 0");

        $res7 = $db->Execute("SELECT * FROM $dbtables[tribes] "
                            ."WHERE tribeid = '$_SESSION[current_unit]' "
                            ."AND slavepop > 0");

        $slaves = $res7->fields;
    }

    while( !$res4->EOF )
    {
        $fairinfo = $res4->fields;

        if( $_REQUEST[transactions] == 'B' )
        {
            $proper_name = $fairinfo[proper_name];
            echo "<OPTION VALUE=\"$fairinfo[proper_name]\">$fairinfo[proper_name]</OPTION>";
        }
        else
        {
            echo "<OPTION VALUE=\"$fairinfo[proper]\">$fairinfo[proper]</OPTION>";
        }
        $res4->MoveNext();
    }

    if( $_REQUEST[transactions] == 'S' )
    {

        while( !$res5->EOF )
        {
            $livestock = $res5->fields;
            echo "<OPTION VALUE=\"$livestock[type]\">$livestock[type]</OPTION>";
            $res5->MoveNext();
        }

        while( !$res6->EOF )
        {
            $resources = $res6->fields;
            echo "<OPTION VALUE=\"$resources[long_name]\">$resources[long_name]</OPTION>";
            $res6->MoveNext();
        }

        if( $slaves[slavepop] > 0 )
        {
            $slaves = $res7->fields;
            echo "<OPTION VALUE=Slaves>Slaves</OPTION>";
        }
    }
    echo "</SELECT>"
        ."<INPUT TYPE=HIDDEN NAME=transactions VALUE=$_REQUEST[transactions]>"
        ."<INPUT TYPE=HIDDEN NAME=culture VALUE=1>"
        ."<INPUT TYPE=HIDDEN NAME=product VALUE=1>"
        ."</TD>"
        ."<TD>"
        ."<INPUT TYPE=SUBMIT VALUE=SUBMIT>"
        ."</TD>"
        ."</TR>"
        ."</FORM>"
        ."</TABLE>"
        ."</CENTER>";
    display_transactions();
}

if( ISSET($_REQUEST[culture]) && ISSET($_REQUEST[transactions]) && ISSET($_REQUEST[product]) && !ISSET($_REQUEST[quantity]) )
{
    $item = $db->Execute("SELECT * FROM $dbtables[fair] "
                        ."WHERE proper_name = '$_REQUEST[item]'");
    $iteminfo = $item->fields;

    echo "<CENTER>"
        ."<TABLE CLASS=color_table BORDER=0 CELLPADDING=4 CELLSPACING=0>"
        ."<TR>"
        ."<TD COLSPAN=2>";

    if( $_REQUEST[transactions] == 'B' )
    {
        echo "Buying ";
        $price = $iteminfo[price_buy];
    }
    elseif( $_REQUEST[transactions] == 'S' )
    {
        echo "Selling ";
        $price = $iteminfo[price_sell];
    }

    echo "$iteminfo[proper_name](s) at $price each with a limit of $iteminfo[limit]"
        ."</TD>"
        ."</TR>";

    echo "<FORM ACTION=fair.php METHOD=POST>"
        ."<TR>"
        ."<TD>"
        ."<INPUT CLASS=edit_area TYPE=TEXT NAME=quantity VALUE=$iteminfo[limit]>"
        ."</TD>"
        ."<TD>"
        ."<INPUT TYPE=SUBMIT VALUE=SUBMIT>"
        ."<INPUT TYPE=HIDDEN NAME=cost VALUE=$price>"
        ."<INPUT TYPE=HIDDEN NAME=product VALUE=\"$iteminfo[proper_name]\">"
        ."<INPUT TYPE=HIDDEN NAME=culture VALUE=$_REQUEST[culture]>"
        ."<INPUT TYPE=HIDDEN NAME=transactions VALUE=$_REQUEST[transactions]>"
        ."</TD>"
        ."</TR>"
        ."</FORM>"
        ."</TABLE>"
        ."</CENTER>"
        ."<P>";
    display_transactions();
}

if( ISSET($_REQUEST[quantity]) )
{
    $count = $db->Execute("SELECT count(*) AS count FROM $dbtables[fair_tribe] "
                         ."WHERE clan_id = '$tribeinfo[clanid]'");
    $countinfo = $count->fields;

    $count = $countinfo[count] + 1;

    $item = $db->Execute("SELECT * FROM $dbtables[fair] "
                        ."WHERE proper_name = '$_REQUEST[product]'");
    $iteminfo = $item->fields;


    $check = $db->Execute("SELECT * FROM $dbtables[fair_tribe] "
                         ."WHERE clan_id = '$_SESSION[clanid]' "
                         ."AND product = '$_REQUEST[product]'");

    if( $_REQUEST[quantity] < 0 )
    {
        echo "<CENTER>Invalid quantity. Try again.</CENTER>";
        page_footer();
    }

    if( $_REQUEST[quantity] > $iteminfo[limit] )
    {
        $_REQUEST[quantity] = $iteminfo[limit];
    }
    if( $check->EOF && $_REQUEST[product] )
    {
    $db->Execute("INSERT INTO $dbtables[fair_tribe] "
                ."VALUES("
                ."'$tribeinfo[clanid]',"
                ."'$tribeinfo[tribeid]',"
                ."'',"
                ."'$skillinfo[level]',"
                ."'$_REQUEST[transactions]',"
                ."'$_REQUEST[product]',"
                ."'$_REQUEST[quantity]',"
                ."'$_REQUEST[cost]'"
                .")");

    $total = $_REQUEST[cost] * $_REQUEST[quantity];
    }
    else
    {
        echo "<CENTER>You have already transacted this item. Please cancel your first order before doing this again.</CENTER>";
        page_footer();
    }

    if( $_REQUEST[transactions] == 'B' )
    {
        echo "<CENTER><FONT SIZE=+1>You have made an order to purchase ";
    }
    else
    {
        echo "<CENTER><FONT SIZE=+1>You have made an order to sell ";
    }
    echo "$_REQUEST[quantity]";
    echo " ";
    echo "$_REQUEST[product]";
    echo " for a total of ";
    echo "$total";
    echo ".</FONT>";
    display_transactions();

}

page_footer();

?>
