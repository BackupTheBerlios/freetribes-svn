<?php
$pos = (strpos($_SERVER['PHP_SELF'], "/fletching.php"));
if ($pos !== false)
{
    die("You cannot access this page directly!");
}

$res = $db->Execute("SELECT * FROM $dbtables[tribes]");
db_op_result($res,__LINE__,__FILE__);
while( !$res->EOF )
{
    $tribe = $res->fields;
    $act = $db->Execute("SELECT * FROM $dbtables[activities] "
                       ."WHERE tribeid = '$tribe[tribeid]' "
                       ."AND skill_abbr = 'flet' "
                       ."LIMIT 1");
      db_op_result($act,__LINE__,__FILE__);
    while( !$act->EOF )
    {
        $act_do = $act->fields;
        $flet = $db->Execute("SELECT * FROM $dbtables[skills] "
                            ."WHERE tribeid = '$tribe[tribeid]' "
                            ."AND abbr = 'flet'");
          db_op_result($flet,__LINE__,__FILE__);
        $skill = $flet->fields;
        $skillcheck = $skill['level'] * 10;
        if( $skill['level'] < 10 && $act_do['actives'] > $skillcheck )
        {
            $act_do['actives'] = $skillcheck;
        }
        $coal = $db->Execute("SELECT * FROM $dbtables[resources] "
                            ."WHERE tribeid = '$tribe[goods_tribe]' "
                            ."AND long_name = 'Coal'");
          db_op_result($coal,__LINE__,__FILE__);
        $coalinfo = $coal->fields;
        $startcoal = $coalinfo['amount'];
        $mtl = $db->Execute("SELECT * FROM $dbtables[resources] "
                           ."WHERE tribeid = '$tribe[goods_tribe]' "
                           ."AND long_name = 'Iron'");
            db_op_result($mtl,__LINE__,__FILE__);
        $mtlinfo = $mtl->fields;
        $startmtl = $mtlinfo['amount'];
        $product = 0;
        while( $act_do['actives'] > 0 && $mtlinfo['amount'] > 0 && $coalinfo['amount'] > 9 )
        {
            $act_do['actives'] -= 1;
            $mtlinfo['amount'] -= 1;
            $coalinfo['amount'] -= 10;
            $product += 10;
        }
        $deltacoal = $startcoal - $coalinfo['amount'];
        $deltamtl = $startmtl - $mtlinfo['amount'];
        $result = $db->Execute("UPDATE $dbtables[resources] "
                    ."SET amount = amount - $deltamtl "
                    ."WHERE tribeid = '$tribe[goods_tribe]' "
                    ."AND long_name = '$mtlinfo[long_name]'");
          db_op_result($result,__LINE__,__FILE__);
        $result = $db->Execute("UPDATE $dbtables[resources] "
                    ."SET amount = amount - $deltacoal "
                    ."WHERE tribeid = '$tribe[goods_tribe]' "
                    ."AND long_name = '$coalinfo[long_name]'");
        db_op_result($result,__LINE__,__FILE__);
        $result = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount + $product "
                    ."WHERE tribeid = '$tribe[goods_tribe]' "
                    ."AND long_name = '$act_do[product]'");
        db_op_result($result,__LINE__,__FILE__);
       $result = $db->Execute("DELETE FROM $dbtables[activities] "
                    ."WHERE tribeid = '$tribe[tribeid]' "
                    ."AND skill_abbr = 'flet'");
          db_op_result($result,__LINE__,__FILE__);
        $result = $db->Execute("INSERT INTO $dbtables[logs] "
                    ."VALUES("
                    ."'',"
                    ."'$month[count]',"
                    ."'$year[count]',"
                    ."'$tribe[clanid]',"
                    ."'$tribe[tribeid]',"
                    ."'UPDATE',"
                    ."'$stamp',"
                    ."'Fletching: $product arrows made using $deltamtl $mtlinfo[long_name] and $deltacoal $coalinfo[long_name].')");
         db_op_result($result,__LINE__,__FILE__);

        $act->MoveNext();
    }
    $res->MoveNext();
}


?>
