<?php
$pos = (strpos($_SERVER['PHP_SELF'], "/stonework.php"));
if ($pos !== false)
{
    die("You cannot access this page directly!");
}
require_once("config.php");
$time_start = getmicrotime();
include("scheduler/game_time.php");
connectdb();
$res = $db->Execute("SELECT * FROM $dbtables[tribes]");
 db_op_result($res,__LINE__,__FILE__);
while( !$res->EOF )
{
    $tribe = $res->fields;
    $act = $db->Execute("SELECT * FROM $dbtables[activities] "
                       ."WHERE tribeid = '$tribe[tribeid]' "
                       ."AND skill_abbr = 'stn'");
     db_op_result($act,__LINE__,__FILE__);
    $act_do = $act->fields;
    if( $act_do['product'] == 'stoneaxe' )
    {
        $clb = $db->Execute("SELECT * FROM $dbtables[products] "
                           ."WHERE long_name = 'club' "
                           ."AND tribeid = '$tribe[goods_tribe]'");
          db_op_result($clb,__LINE__,__FILE__);
        $club = $clb->fields;
        $startclub = $club['amount'];
        $ltr = $db->Execute("SELECT * FROM $dbtables[products] "
                           ."WHERE long_name = 'leather' "
                           ."AND tribeid = '$tribe[goods_tribe]'");
         db_op_result($ltr,__LINE__,__FILE__);
        $leather = $ltr->fields;
        $startleather = $leather['amount'];
        $stn = $db->Execute("SELECT * FROM $dbtables[products] "
                           ."WHERE long_name = 'stones' "
                           ."AND tribeid = '$tribe[goods_tribe]'");
         db_op_result($stn,__LINE__,__FILE__);
        $stone = $stn->fields;
        $startstone = $stone['amount'];

        while( $act_do['actives'] > 0 && $stone['amount'] > 0 && $leather['amount'] > 0 && $club['amount'] > 0 )
        {
            $act_do['actives'] -= 1;
            $stone['amount'] -= 1;
            $leather['amount'] -= 1;
            $club['amount'] -= 1;
            $product_made += 1;
        }
        $deltastone = $startstone - $stone['amount'];
        $deltaclub = $startclub - $club['amount'];
        $deltaltr = $startleather - $leather['amount'];

        $query = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount - $deltastone "
                    ."WHERE long_name = 'stones' "
                    ."AND tribeid = '$tribe[goods_tribe]'");
         db_op_result($query,__LINE__,__FILE__);
        $query = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount - $deltaltr "
                    ."WHERE long_name = 'leather' "
                    ."AND tribeid = '$tribe[goods_tribe]'");
           db_op_result($query,__LINE__,__FILE__);
        $query = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount - $deltaclub "
                    ."WHERE long_name = 'club' "
                    ."AND tribeid = '$tribe[goods_tribe]'");
           db_op_result($query,__LINE__,__FILE__);
        $query = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount + $product_made "
                    ."WHERE long_name = 'stoneaxe' "
                    ."AND tribeid = '$tribe[goods_tribe]'");
            db_op_result($query,__LINE__,__FILE__);
        $query = $db->Execute("INSERT INTO $dbtables[logs] "
                    ."VALUES("
                    ."'',"
                    ."'$month[count]',"
                    ."'$year[count]',"
                    ."'$tribe[clanid]',"
                    ."'$tribe[tribeid]',"
                    ."'UPDATE',"
                    ."'$stamp',"
                    ."'Stonework: $product_made Stone Axes made using $deltastone stones, $deltaltr leather, $deltaclub clubs.')");
        db_op_result($query,__LINE__,__FILE__);
        $query = $db->Execute("DELETE FROM $dbtables[activities] "
                    ."WHERE tribeid = '$tribe[tribeid]' "
                    ."AND skill_abbr = '$act_do[skill_abbr]' "
                    ."AND product = '$act_do[product]'");
        db_op_result($query,__LINE__,__FILE__);

    }

    if( $act_do['product'] == 'stonespear' )
    {
        $shft = $db->Execute("SELECT * FROM $dbtables[products] "
                            ."WHERE tribeid = '$tribe[goods_tribe] "
                            ."AND long_name = 'shaft'");
         db_op_result($shft,__LINE__,__FILE__);
        $shaft = $shft->fields;
        $startshaft = $shaft['amount'];
        $stn = $db->Execute("SELECT * FROM $dbtables[products] "
                           ."WHERE long_name = 'stones' "
                           ."AND tribeid = '$tribe[goods_tribe]'");
          db_op_result($stn,__LINE__,__FILE__);
        $stone = $stn->fields;
        $startstone = $stone['amount'];

        while ( $act_do['actives'] > 0 && $stone['amount'] > 0 && $shaft['amount'] > 0 )
        {
            $act_do['actives'] -= 1;
            $stone['amount'] -= 1;
            $shaft['amount'] -= 1;
            $product_made += 1;
        }
        $deltastone = $startstone - $stone['amount'];
        $deltashaft = $startshaft - $shaft['amount'];
        $query = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount - $deltastone "
                    ."WHERE long_name = 'stones' "
                    ."AND tribeid = '$tribe[goods_tribe]'");
          db_op_result($query,__LINE__,__FILE__);
        $query = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount - $deltashaft "
                    ."WHERE long_name = 'shaft' "
                    ."AND tribeid = '$tribe[goods_tribe]'");
        db_op_result($query,__LINE__,__FILE__);
        $query = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount + $product_made "
                    ."WHERE long_name = 'stonespear' "
                    ."AND tribeid = '$tribe[goods_tribe]'");
         db_op_result($query,__LINE__,__FILE__);
        $query = $db->Execute("INSERT INTO $dbtables[logs] "
                    ."VALUES("
                    ."'',"
                    ."'$month[count]',"
                    ."'$year[count]',"
                    ."'$tribe[clanid]',"
                    ."'$tribe[tribeid]',"
                    ."'UPDATE',"
                    ."'$stamp',"
                    ."'Stonework: $product_made Stone Spears made using $deltastone stones and $deltashaft shafts.')");
        db_op_result($query,__LINE__,__FILE__);
        $query = $db->Execute("DELETE FROM $dbtables[activities] "
                    ."WHERE tribeid = '$tribe[tribeid]' "
                    ."AND skill_abbr = '$act_do[skill_abbr]' "
                    ."AND product = '$act_do[product]'");
         db_op_result($query,__LINE__,__FILE__);

    }

    if( $act_do['product'] == 'millstone' )
    {
        $stn = $db->Execute("SELECT * FROM $dbtables[products] "
                           ."WHERE long_name = 'stones' "
                           ."AND tribeid = '$tribe[goods_tribe]'");
          db_op_result($stn,__LINE__,__FILE__);
        $stone = $stn->fields;
        $startstone = $stone['amount'];

        while( $act_do['actives'] > 9 && $stone['amount'] > 9 )
        {
            $act_do['actives'] -= 10;
            $stone['amount'] -= 10;
            $product_made += 1;
        }
        $deltastone = $startstone - $stone['amount'];
        $query = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount - $deltastone "
                    ."WHERE long_name = 'stones' "
                    ."AND tribeid = '$tribe[goods_tribe]'");
           db_op_result($query,__LINE__,__FILE__);
        $query = $db->Execute("INSERT INTO $dbtables[logs] "
                    ."VALUES("
                    ."'',"
                    ."'$month[count]',"
                    ."'$year[count]',"
                    ."'$tribe[clanid]',"
                    ."'$tribe[tribeid]',"
                    ."'UPDATE',"
                    ."'$stamp',"
                    ."'Stonework: $product_made Millstones made using $deltastone stones.')");
          db_op_result($query,__LINE__,__FILE__);
        $query = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount + $product_made "
                    ."WHERE tribeid = '$tribe[tribeid]' "
                    ."AND long_name = 'millstone'");
        db_op_result($query,__LINE__,__FILE__);
        $query = $db->Execute("DELETE FROM $dbtables[activities] "
                    ."WHERE tribeid = '$tribe[tribeid]' "
                    ."AND skill_abbr = '$act_do[skill_abbr]' "
                    ."AND product = '$act_do[product]'");
       db_op_result($query,__LINE__,__FILE__);

    }

    if( $act_do['product'] == 'scrapers' )
    {
        $stn = $db->Execute("SELECT * FROM $dbtables[products] "
                           ."WHERE long_name = 'stones' "
                           ."AND tribeid = '$tribe[goods_tribe]'");
         db_op_result($stn,__LINE__,__FILE__);
        $stone = $stn->fields;
        $startstone = $stone['amount'];

        while( $act_do['actives'] > 0 && $stone['amount'] > 0 )
        {
            $act_do['actives'] -= 1;
            $stone['amount'] -= 1;
            $product_made += 1;
        }
        $deltastone = $startstone - $stone['amount'];
        $query = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount - $deltastone "
                    ."WHERE long_name = 'stones' "
                    ."AND tribeid = '$tribe[goods_tribe]'");
           db_op_result($query,__LINE__,__FILE__);
        $query = $db->Execute("INSERT INTO $dbtables[logs] "
                    ."VALUES("
                    ."'',"
                    ."'$month[count]',"
                    ."'$year[count]',"
                    ."'$tribe[clanid]',"
                    ."'$tribe[tribeid]',"
                    ."'UPDATE',"
                    ."'$stamp',"
                    ."'Stonework: $product_made Stone scrapers made using $deltastone stones.')");
         db_op_result($query,__LINE__,__FILE__);
        $query = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount + $product_made "
                    ."WHERE tribeid = '$tribe[tribeid]' "
                    ."AND long_name = 'scrapers'");
         db_op_result($query,__LINE__,__FILE__);
        $query = $db->Execute("DELETE FROM $dbtables[activities] "
                    ."WHERE tribeid = '$tribe[tribeid]' "
                    ."AND skill_abbr = '$act_do[skill_abbr]' "
                    ."AND product = '$act_do[product]'");
         db_op_result($query,__LINE__,__FILE__);
    }



    if( $act_do['product'] == 'sculpture' )
    {
        $stn = $db->Execute("SELECT * FROM $dbtables[products] "
                           ."WHERE long_name = 'stones' "
                           ."AND tribeid = '$tribe[goods_tribe]'");
        db_op_result($stn,__LINE__,__FILE__);
        $stone = $stn->fields;
        $startstone = $stone['amount'];

        while( $act_do['actives'] > 3 && $stone['amount'] > 4 )
        {
            $act_do['actives'] -= 4;
            $stone['amount'] -= 5;
            $product_made += 1;
        }
        $deltastone = $startstone - $stone['amount'];
        $query = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount - $deltastone "
                    ."WHERE long_name = 'stones' "
                    ."AND tribeid = '$tribe[goods_tribe]'");
         db_op_result($query,__LINE__,__FILE__);
        $query = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount + $product_made "
                    ."WHERE tribeid = '$tribe[tribeid]' "
                    ."AND long_name = 'sculpture'");
         db_op_result($query,__LINE__,__FILE__);
        $query = $db->Execute("INSERT INTO $dbtables[logs] "
                    ."VALUES("
                    ."'',"
                    ."'$month[count]',"
                    ."'$year[count]',"
                    ."'$tribe[clanid]',"
                    ."'$tribe[tribeid]',"
                    ."'UPDATE',"
                    ."'$stamp',"
                    ."'Stonework: $product_made Sculptures made using $deltastone stones.')");
         db_op_result($query,__LINE__,__FILE__);
        $query = $db->Execute("DELETE FROM $dbtables[activities] "
                    ."WHERE tribeid = '$tribe[tribeid]' "
                    ."AND skill_abbr = '$act_do[skill_abbr]' "
                    ."AND product = '$act_do[product]'");
       db_op_result($query,__LINE__,__FILE__);

    }

    if( $act_do['product'] == 'statue' )
    {
        $stn = $db->Execute("SELECT * FROM $dbtables[products] "
                           ."WHERE long_name = 'stones' "
                           ."AND tribeid = '$tribe[goods_tribe]'");
         db_op_result($stn,__LINE__,__FILE__);
        $stone = $stn->fields;
        $startstone = $stone['amount'];

        while( $act_do['actives'] > 9 && $stone['amount'] > 9 )
        {
            $act_do['actives'] -= 10;
            $stone['amount'] -= 10;
            $product_made += 1;
        }
        $deltastone = $startstone - $stone['amount'];
        $query = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount - $deltastone "
                    ."WHERE long_name = 'stones' "
                    ."AND tribeid = '$tribe[goods_tribe]'");
            db_op_result($query,__LINE__,__FILE__);
        $query = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount + $product_made "
                    ."WHERE tribeid = '$tribe[tribeid]' "
                    ."AND long_name = 'statue'");
           db_op_result($query,__LINE__,__FILE__);
        $query = $db->Execute("INSERT INTO $dbtables[logs] "
                    ."VALUES("
                    ."'',"
                    ."'$month[count]',"
                    ."'$year[count]',"
                    ."'$tribe[clanid]',"
                    ."'$tribe[tribeid]',"
                    ."'UPDATE',"
                    ."'$stamp',"
                    ."'Stonework: $product_made Statues made using $deltastone stones.')");
         db_op_result($query,__LINE__,__FILE__);
        $query = $db->Execute("DELETE FROM $dbtables[activities] "
                    ."WHERE tribeid = '$tribe[tribeid]' "
                    ."AND skill_abbr = '$act_do[skill_abbr]' "
                    ."AND product = '$act_do[product]'");
        db_op_result($query,__LINE__,__FILE__);
    }

    if( $act_do['product'] == 'smelter' )
    {
        $ski = $db->Execute("SELECT * FROM $dbtables[skills] "
                           ."WHERE abbr = 'eng' "
                           ."AND tribeid = '$tribe[tribeid]'");
          db_op_result($ski,__LINE__,__FILE__);
        $skill = $ski->fields;
        $stn = $db->Execute("SELECT * FROM $dbtables[products] "
                           ."WHERE long_name = 'stones' "
                           ."AND tribeid = '$tribe[goods_tribe]'");
         db_op_result($stn,__LINE__,__FILE__);
        $stone = $stn->fields;
        $startstone = $stone['amount'];
        $ref = $db->Execute("SELECT * FROM $dbtables[structures] "
                           ."WHERE clanid = '$tribe[clanid]' "
                           ."AND hex_id = '$tribe[hex_id]' "
                           ."AND number < 100 "
                           ."AND long_name = 'refinery'");
           db_op_result($ref,__LINE__,__FILE__);
        if( !$ref->EOF )
        {
        $refinfo = $ref->fields;

        $product_made = 0;
        $total_smelt = $product_made + $refinfo[number];
        while( $act_do['actives'] > 0 && $stone['amount'] > 4 && $total_smelt < 100 && $skill[level] > 4 )
        {
            $act_do['actives'] -= 1;
            $stone['amount'] -= 5;
            $product_made += 1;
            $total_smelt += 1;
        }
        $deltastone = $startstone - $stone['amount'];
        $query = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount - $deltastone "
                    ."WHERE long_name = 'stones' "
                    ."AND tribeid = '$tribe[goods_tribe]'");
        db_op_result($query,__LINE__,__FILE__);
        $query = $db->Execute("UPDATE $dbtables[structures] "
                    ."SET number = number + $product_made "
                    ."WHERE hex_id = '$tribe[hex_id]' "
                    ."AND struct_id = '$refinfo[struct_id]' "
                    ."AND long_name = 'refinery' "
                    ."AND clanid = '$refinfo[clanid]'");
       db_op_result($query,__LINE__,__FILE__);
        $query = $db->Execute("INSERT INTO $dbtables[logs] "
                    ."VALUES("
                    ."'',"
                    ."'$month[count]',"
                    ."'$year[count]',"
                    ."'$tribe[clanid]',"
                    ."'$tribe[tribeid]',"
                    ."'UPDATE',"
                    ."'$stamp',"
                    ."'Stonework: $product_made smelters made using $deltastone stones.')");
       db_op_result($query,__LINE__,__FILE__);
        $query = $db->Execute("DELETE FROM $dbtables[activities] "
                    ."WHERE tribeid = '$tribe[tribeid]' "
                    ."AND skill_abbr = '$act_do[skill_abbr]' "
                    ."AND product = '$act_do[product]'");
               db_op_result($query,__LINE__,__FILE__);
        }
        else
        {
        $query = $db->Execute("INSERT INTO $dbtables[logs] "
                    ."VALUES("
                    ."'',"
                    ."'$month[count]',"
                    ."'$year[count]',"
                    ."'$tribe[clanid]',"
                    ."'$tribe[tribeid]',"
                    ."'UPDATE',"
                    ."'$stamp',"
                    ."'Stonework: Refineries in the area are already at full capacity.')");
         db_op_result($query,__LINE__,__FILE__);
        $query = $db->Execute("DELETE FROM $dbtables[activities] "
                    ."WHERE tribeid = '$tribe[tribeid]' "
                    ."AND skill_abbr = '$act_do[skill_abbr]' "
                    ."AND product = '$act_do[product]'");
          db_op_result($query,__LINE__,__FILE__);
        }

    }

    if( $act_do['product'] == 'refinery' )
    {
        $ski = $db->Execute("SELECT * FROM $dbtables[skills] "
                           ."WHERE abbr = 'eng' "
                           ."AND tribeid = '$tribe[tribeid]'");
         db_op_result($ski,__LINE__,__FILE__);
        $skill = $ski->fields;
        $stn = $db->Execute("SELECT * FROM $dbtables[products] "
                           ."WHERE long_name = 'stones' "
                           ."AND tribeid = '$tribe[goods_tribe]'");
          db_op_result($stn,__LINE__,__FILE__);
        $stone = $stn->fields;
        $startstone = $stone['amount'];
        $mh = $db->Execute("SELECT * FROM $dbtables[structures] "
                           ."WHERE clanid = '$tribe[clanid]' "
                           ."AND hex_id = '$tribe[hex_id]' "
                           ."AND complete = 'Y' "
                           ."AND long_name = 'meetinghouse'");
           db_op_result($mh,__LINE__,__FILE__);
        $ref = $db->Execute("SELECT * FROM $dbtables[structures] "
                           ."WHERE tribeid = '$tribe[goods_tribe]' "
                           ."AND hex_id = '$tribe[hex_id]' "
                           ."AND complete = 'N' "
                           ."AND long_name = 'refinery'");
               db_op_result($ref,__LINE__,__FILE__);
        if( !$mh->EOF )
        {
        $refinfo = $ref->fields;

        $product_made = 0;
        $total = $product_made + $refinfo[struct_pts];
        while( $act_do['actives'] > 0 && $stone['amount'] > 4 && $total < 100 && $skill[level] > 4 )
        {
            $act_do['actives'] -= 1;
            $stone['amount'] -= 5;
            $product_made += 5;
            $total += 1;
        }
        $deltastone = $startstone - $stone['amount'];
        if( $total == 100 )
        {
            $complete = 'Y';
        }
        else
        {
            $complete = 'N';
        }
        $query = $db->Execute("UPDATE $dbtables[products] "
                    ."SET amount = amount - $deltastone "
                    ."WHERE long_name = 'stones' "
                    ."AND tribeid = '$tribe[goods_tribe]'");
           db_op_result($query,__LINE__,__FILE__);
        if( !$ref->EOF )
        {
        $query = $db->Execute("UPDATE $dbtables[structures] "
                    ."SET struct_pts = struct_pts + $product_made, "
                    ."complete = '$complete' "
                    ."WHERE hex_id = '$tribe[hex_id]' "
                    ."AND struct_id = '$refinfo[struct_id]' "
                    ."AND long_name = 'refinery' "
                    ."AND clanid = '$refinfo[clanid]'");
                db_op_result($query,__LINE__,__FILE__);
        }
        else
        {
        $query = $db->Execute("INSERT INTO $dbtables[structures] "
                    ."VALUES("
                    ."'',"
                    ."'refinery',"
                    ."'Refinery',"
                    ."'$tribe[hex_id]',"
                    ."'$tribe[goods_tribe]',"
                    ."'$tribe[clanid]',"
                    ."'$complete',"
                    ."'$total',"
                    ."'100',"
                    ."'smelters',"
                    ."'0'");
             db_op_result($query,__LINE__,__FILE__);
        }
        $query = $db->Execute("INSERT INTO $dbtables[logs] "
                    ."VALUES("
                    ."'',"
                    ."'$month[count]',"
                    ."'$year[count]',"
                    ."'$tribe[clanid]',"
                    ."'$tribe[tribeid]',"
                    ."'UPDATE',"
                    ."'$stamp',"
                    ."'Stonework: $product_made stones laid for a refinery.')");
          db_op_result($query,__LINE__,__FILE__);
        $query = $db->Execute("DELETE FROM $dbtables[activities] "
                    ."WHERE tribeid = '$tribe[tribeid]' "
                    ."AND skill_abbr = '$act_do[skill_abbr]' "
                    ."AND product = '$act_do[product]'");
           db_op_result($query,__LINE__,__FILE__);
        }
        else
        {
        $query = $db->Execute("INSERT INTO $dbtables[logs] "
                    ."VALUES("
                    ."'',"
                    ."'$month[count]',"
                    ."'$year[count]',"
                    ."'$tribe[clanid]',"
                    ."'$tribe[tribeid]',"
                    ."'UPDATE',"
                    ."'$stamp',"
                    ."'Stonework: You must first build a Meeting House.')");
        db_op_result($query,__LINE__,__FILE__);
        $query = $db->Execute("DELETE FROM $dbtables[activities] "
                    ."WHERE tribeid = '$tribe[tribeid]' "
                    ."AND skill_abbr = '$act_do[skill_abbr]' "
                    ."AND product = '$act_do[product]'");
            db_op_result($query,__LINE__,__FILE__);
        }

    }


    $res->MoveNext();
}
$time_end = getmicrotime();
$time = $time_end - $time_start;
$page_name =   str_replace($game_root."scheduler/",'',__FILE__);// get the name of the file being viewed
$res = $db->Execute("INSERT INTO $dbtables[logs] "
            ."VALUES("
            ."'',"
            ."'$month[count]',"
            ."'$year[count]',"
            ."'0000',"
            ."'0000.00',"
            ."'BENCHMARK',"
            ."'$stamp',"
            ."'$page_name completed in $time seconds.')");
    db_op_result($res,__LINE__,__FILE__);
?>
