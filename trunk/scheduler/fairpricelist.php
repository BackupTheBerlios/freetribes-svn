<?php
$pos = (strpos($_SERVER['PHP_SELF'], "/fairpricelist.php"));
if ($pos !== false)
{
    die("You cannot access this page directly!");
}
if( $month['count'] == '6' || $month['count'] == '12' )
{


$sk = $db->Execute("SELECT distinct tribeid FROM $dbtables[skills] WHERE abbr = 'eco' AND level > 5");
      db_op_result($sk,__LINE__,__FILE__);
if( !$sk->EOF )
{
    $pricelist = "<TABLE BORDER=0 CELLSPACING=0><TR><TD>";
    $pricelist .= "<FONT SIZE=+1 COLOR=BLACK>&nbsp;Item&nbsp;";
    $pricelist .= "</FONT></TD><TD><FONT SIZE=+1 COLOR=BLACK>";
    $pricelist .= "&nbsp;Sell Price&nbsp;</FONT></TD><TD><FONT ";
    $pricelist .= "SIZE=+1 COLOR=BLACK>&nbsp;Buy Price&nbsp;</FONT></TD></TR>";

    $fp = $db->Execute("SELECT * FROM $dbtables[fair] ORDER BY proper_name ASC");
        db_op_result($fp,__LINE__,__FILE__);
    while( !$fp->EOF )
    {
        $price = $fp->fields;
        $pricelist .= "<TR><TD><FONT COLOR=BLACK>$price[proper_name]</FONT></TD>"
                   ."<TD><FONT COLOR=BLACK>$price[price_sell]</FONT></TD>"
                   ."<TD><FONT COLOR=BLACK>$price[price_buy]</FONT></TD></TR>";
        $fp->MoveNext();
    }
    $pricelist .= "</TABLE>";
    $subject = "$month[count]/$year[count] Fair Price List";

    while( !$sk->EOF )
    {
        $skill = $sk->fields;
        $res = $db->Execute("SELECT * FROM $dbtables[tribes] WHERE tribeid = '$skill[tribeid]'");
        db_op_result($res,__LINE__,__FILE__);
        $tribe = $res->fields;
       $res = $db->Execute("INSERT INTO $dbtables[messages] "
                    ."VALUES("
                    ."'',"
                    ."'0000',"
                    ."'$tribe[clanid]',"
                    ."'$subject',"
                    ."'$stamp',"
                    ."'$pricelist',"
                    ."'N')");
            db_op_result($res,__LINE__,__FILE__);
        $sk->MoveNext();
    }
}
}

?>
