<?php
$pos = (strpos($_SERVER['PHP_SELF'], "/fairfigures2.php"));
if ($pos !== false)
{
    die("You cannot access this page directly!");
}

if( $month['count'] == '5' | $month['count'] == '11' )
{

    $level = $db->Execute("SELECT * FROM $dbtables[fair] "
                         ."WHERE price_buy > 0");
       db_op_result($level,__LINE__,__FILE__);
    while( !$level->EOF )
    {
        $levelinfo = $level->fields;
        $delta = $levelinfo['p_amount']/$levelinfo['amount'];
        $newbuy = $levelinfo['price_buy'] * $delta;
        if( $newbuy < 1 )
        {
            $newbuy = 1;
        }
        if( $newbuy > ( $levelinfo['price_buy'] * 2 ))
        {
            $newbuy = $levelinfo['price_buy'] * 2;
        }
        $res = $db->Execute("UPDATE $dbtables[fair] "
                    ."SET price_buy = '$newbuy' "
                    ."WHERE proper_name = '$levelinfo[proper_name]'");
           db_op_result($res,__LINE__,__FILE__);
        $level->MoveNext();
    }
    $level = $db->Execute("SELECT * FROM $dbtables[fair] "
                         ."WHERE price_sell > 0");
       db_op_result($level,__LINE__,__FILE__);
    while( !$level->EOF )
    {
        $levelinfo = $level->fields;
        $delta = $levelinfo['amount']/$levelinfo['p_amount'];
        $newsell = $levelinfo['price_sell'] * $delta;
        if( $newsell < 1 )
        {
            $newsell = 1;
        }
        if( $newsell > ($levelinfo['price_sell'] * 2))
        {
            $newsell = $levelinfo['price_sell'] * 2;
        }
        $res = $db->Execute("UPDATE $dbtables[fair] "
                    ."SET price_sell = '$newsell' "
                    ."WHERE proper_name = '$levelinfo[proper_name]'");
          db_op_result($res,__LINE__,__FILE__);
        $level->MoveNext();
    }
    $res = $db->Execute("UPDATE $dbtables[fair] "
                ."SET amount = p_amount");  ///Sets the amounts back to the original levels
     db_op_result($res,__LINE__,__FILE__);
}

?>
