<?php
$pos = (strpos($_SERVER['PHP_SELF'], "/leatherworking.php"));
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
                        ."WHERE tribeid = '$tribe[tribeid]'");
      db_op_result($act,__LINE__,__FILE__);
    while( !$act->EOF )
    {
        $act_do = $act->fields;

        if( $act_do['skill_abbr'] == 'ltr' )
        {
            if( $act_do['product'] == 'hood' )
            {
                $leather = $db->Execute("SELECT * FROM $dbtables[products] "
                                        ."WHERE long_name = 'leather' "
                                        ."AND tribeid = '$tribe[goods_tribe]'");
                  db_op_result($leather,__LINE__,__FILE__);
                $leatherinfo = $leather->fields;
                $hoods = 0;
                $startltr = $leatherinfo['amount'];
                while( $leatherinfo['amount'] > 1 && $act_do['actives'] > 0 )
                {
                    $hoods += 2;
                    $leatherinfo['amount'] -= 1;
                    $act_do['actives'] -= 1;
                }
                $deltaltr = $startltr - $leatherinfo['amount'];
                $query = $db->Execute("UPDATE $dbtables[products] "
                             ."SET amount = amount + '$hoods' "
                             ."WHERE tribeid = '$tribe[goods_tribe]' "
                             ."AND long_name = 'hood'");
                 db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("DELETE FROM $dbtables[activities] "
                             ."WHERE tribeid = '$tribe[tribeid]' "
                             ."AND skill_abbr = 'ltr' "
                             ."AND product = 'hood'");
                db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("UPDATE $dbtables[products] "
                             ."SET amount = '$leatherinfo[amount]' "
                             ."WHERE tribeid = '$tribe[goods_tribe]' "
                             ."AND long_name = 'leather'");
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
                             ."'Leatherworking: $hoods Hoods made using $deltaltr leather.')");
                       db_op_result($query,__LINE__,__FILE__);
            }
            if( $act_do['product'] == 'leatherbarding' )
            {
                $leather = $db->Execute("SELECT * FROM $dbtables[products] "
                                        ."WHERE long_name = 'leather' "
                                        ."AND tribeid = '$tribe[goods_tribe]'");
                 db_op_result($leather,__LINE__,__FILE__);
                $leatherinfo = $leather->fields;
                $product = 0;
                $startltr = $leatherinfo['amount'];
                while( $leatherinfo['amount'] > 6 && $act_do['actives'] > 1 )
                {
                    $product += 2;
                    $leatherinfo['amount'] -= 6;
                    $act_do['actives'] -= 2;
                }
                $deltaltr = $startltr - $leatherinfo['amount'];
                $query = $db->Execute("UPDATE $dbtables[products] "
                             ."SET amount = amount + '$product' "
                             ."WHERE tribeid = '$tribe[goods_tribe]' "
                             ."AND long_name = '$act_do[product]'");
                 db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("DELETE FROM $dbtables[activities] "
                             ."WHERE tribeid = '$tribe[tribeid]' "
                             ."AND skill_abbr = 'ltr' "
                             ."AND product = '$act_do[product]'");
                db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("UPDATE $dbtables[products] "
                             ."SET amount = '$leatherinfo[amount]' "
                             ."WHERE tribeid = '$tribe[goods_tribe]' "
                             ."AND long_name = 'leather'");
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
                             ."'Leatherworking: $product barding made using $deltaltr leather.')");
                db_op_result($query,__LINE__,__FILE__);
            }

            if($act_do['product'] == 'sling')
            {

                $leather = $db->Execute("SELECT * FROM $dbtables[products] WHERE long_name = 'leather' AND tribeid = '$tribe[goods_tribe]'");
                db_op_result($leather,__LINE__,__FILE__);
                $leatherinfo = $leather->fields;
                $slings = 0;
                while($leatherinfo['amount'] > 0 & $act_do['actives'] > 0)
                {

                    $slings += 1;
                    $leatherinfo['amount'] -= 1;
                    $act_do['actives'] -= 1;

                }
                $query = $db->Execute("UPDATE $dbtables[products] set amount = amount + '$slings' WHERE tribeid = '$tribe[goods_tribe]' and long_name = '$act_do[product]'");
                 db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("DELETE FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' AND skill_abbr = 'ltr' AND product = 'sling'");
                db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("UPDATE $dbtables[products] set amount = '$leatherinfo[amount]' where tribeid = '$tribe[goods_tribe]' and long_name = 'leather'");
                db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Leatherworking: $slings Slings made.')");
                db_op_result($query,__LINE__,__FILE__);
            }
            if($act_do['product'] == 'heaters')
            {

                $leather = $db->Execute("SELECT * FROM $dbtables[products] WHERE long_name = 'leather' AND tribeid = '$tribe[goods_tribe]'");
                db_op_result($leather,__LINE__,__FILE__);
                $leatherinfo = $leather->fields;
                $startltr = $leatherinfo['amount'];
                $frame = $db->Execute("SELECT * FROM $dbtables[products] WHERE long_name = 'frame' AND tribeid = '$tribe[goods_tribe]' AND amount > 0 OR long_name = 'boneframe' AND tribeid = '$tribe[goods_tribe]' AND amount > 0");
                 db_op_result($frame,__LINE__,__FILE__);
                $frameinfo = $frame->fields;
                if($frameinfo['long_name'] == 'boneframe')
                {
                    $frametype = 'boneframe';
                }
                else
                {
                    $frametype = 'frame';
                }
                $startframe = $frameinfo['amount'];
                $heaters = 0;
                while($leatherinfo['amount'] > 2 & $act_do['actives'] > 0 & $frameinfo['amount'] > 0)
                {
                    $heaters += 1;
                    $leatherinfo['amount'] -= 1;
                    $act_do['actives'] -= 1;
                    $frameinfo['amount'] -= 1;
                }
                $deltaframe = $startframe - $frameinfo['amount'];
                $deltaltr = $startltr - $leatherinfo['amount'];

                $query = $db->Execute("UPDATE $dbtables[products] set amount = amount + '$heaters' WHERE tribeid = '$tribe[goods_tribe]' and long_name = 'heaters'");
                db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("DELETE FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' AND skill_abbr = 'ltr' AND product = 'heaters'");
               db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("UPDATE $dbtables[products] set amount = amount - $deltaltr where tribeid = '$tribe[goods_tribe]' and long_name = 'leather'");
               db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("UPDATE $dbtables[products] set amount = amount - $deltaframe where tribeid = '$tribe[goods_tribe]' and long_name = '$frametype'");
               db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Leatherworking: $heaters Heaters made using $deltaltr leather and $deltaframe $frametype.')");
               db_op_result($query,__LINE__,__FILE__);
            }
            if($act_do['product'] == 'jerkin')
            {

                $leather = $db->Execute("SELECT * FROM $dbtables[products] WHERE long_name = 'leather' AND tribeid = '$tribe[goods_tribe]'");
                db_op_result($leather,__LINE__,__FILE__);
                $leatherinfo = $leather->fields;
                $jerkins = 0;

                while($leatherinfo['amount'] > 3 & $act_do['actives'] > 1)
                {

                    $jerkins += 1;
                    $leatherinfo['amount'] -= 4;
                    $act_do['actives'] -= 2;

                }
                $query = $db->Execute("UPDATE $dbtables[products] set amount = amount + '$jerkins' WHERE tribeid = '$tribe[goods_tribe]' and long_name = 'jerkin'");
                db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("DELETE FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' AND skill_abbr = 'ltr' AND product = 'jerkin'");
                db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("UPDATE $dbtables[products] set amount = '$leatherinfo[amount]' where tribeid = '$tribe[goods_tribe]' and long_name = 'leather'");
                db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Leatherworking: $jerkins Jerkins made.')");
                db_op_result($query,__LINE__,__FILE__);
            }

            if($act_do['product'] == 'trews')
            {

                $leather = $db->Execute("SELECT * FROM $dbtables[products] WHERE long_name = 'leather' AND tribeid = '$tribe[goods_tribe]'");
                db_op_result($leather,__LINE__,__FILE__);
                $leatherinfo = $leather->fields;
                $trews = 0;
                while($leatherinfo['amount'] > 1 & $act_do['actives'] > 0)
                {

                    $trews += 1;
                    $leatherinfo['amount'] -= 2;
                    $act_do['actives'] -= 1;

                }
                $query = $db->Execute("UPDATE $dbtables[products] set amount = amount + '$trews' WHERE tribeid = '$tribe[goods_tribe]' and long_name = 'trews'");
                 db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("DELETE FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' AND skill_abbr = 'ltr' AND product = 'trews'");
                 db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("UPDATE $dbtables[products] set amount = '$leatherinfo[amount]' where tribeid = '$tribe[goods_tribe]' and long_name = 'leather'");
                db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Leatherworking: $trews Trews made.')");
                db_op_result($query,__LINE__,__FILE__);
            }

            if($act_do['product'] == 'leathergreaves')
            {

                $leather = $db->Execute("SELECT * FROM $dbtables[products] WHERE long_name = 'leather' AND tribeid = '$tribe[goods_tribe]'");
                db_op_result($leather,__LINE__,__FILE__);
                $leatherinfo = $leather->fields;

                $greaves = 0;
                while($leatherinfo['amount'] > 0 & $act_do['actives'] > 0)
                {
                    $greaves += 1;
                    $leatherinfo['amount'] -= 2;
                    $act_do['actives'] -= 1;

                }
                $query = $db->Execute("UPDATE $dbtables[products] set amount = amount + '$greaves' WHERE tribeid = '$tribe[goods_tribe]' and long_name = 'leathergreaves'");
                 db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("DELETE FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' AND skill_abbr = 'ltr' AND product = 'leathergreaves'");
                db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("UPDATE $dbtables[products] set amount = '$leatherinfo[amount]' where tribeid = '$tribe[goods_tribe]' and long_name = 'leather'");
                db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Leatherworking: $greaves Leather Greaves made.')");
              db_op_result($query,__LINE__,__FILE__);
            }


            if($act_do['product'] == 'rope')
            {

                $leather = $db->Execute("SELECT * FROM $dbtables[products] WHERE long_name = 'leather' AND tribeid = '$tribe[goods_tribe]'");
                db_op_result($leather,__LINE__,__FILE__);
                $leatherinfo = $leather->fields;

                $ropes = 0;
                while($leatherinfo['amount'] > 4 & $act_do['actives'] > 1)
                {

                    $ropes += 1;
                    $leatherinfo['amount'] -= 5;
                    $act_do['actives'] -= 2;

                }
                $query = $db->Execute("UPDATE $dbtables[products] set amount = amount + '$ropes' WHERE tribeid = '$tribe[goods_tribe]' and long_name = 'rope'");
              db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("DELETE FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' AND skill_abbr = 'ltr' AND product = 'rope'");
               db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("UPDATE $dbtables[products] set amount = '$leatherinfo[amount]' where tribeid = '$tribe[goods_tribe]' and long_name = 'leather'");
                db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Leatherworking: $ropes Ropes made.')");
                  db_op_result($query,__LINE__,__FILE__);
            }

            if($act_do['product'] == 'backpack')
            {

                $leather = $db->Execute("SELECT * FROM $dbtables[products] WHERE long_name = 'leather' AND tribeid = '$tribe[goods_tribe]'");
                db_op_result($leather,__LINE__,__FILE__);
                $leatherinfo = $leather->fields;

                $backpacks = 0;
                while($leatherinfo['amount'] > 1 & $act_do['actives'] > 1)
                {

                    $backpacks += 1;
                    $leatherinfo['amount'] -= 2;
                    $act_do['actives'] -= 2;

                }
                $query = $db->Execute("UPDATE $dbtables[products] set amount = amount + '$backpacks' WHERE tribeid = '$tribe[goods_tribe]' and long_name = 'backpack'");
                 db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("DELETE FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' AND skill_abbr = 'ltr' AND product = 'backpack'");
                   db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("UPDATE $dbtables[products] set amount = '$leatherinfo[amount]' where tribeid = '$tribe[goods_tribe]' and long_name = 'leather'");
                  db_op_result($query,__LINE__,__FILE__);
               $query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Leatherworking: $backpacks Backpacks made.')");
               db_op_result($query,__LINE__,__FILE__);
            }

            if($act_do['product'] == 'whip')
            {
                $leather = $db->Execute("SELECT * FROM $dbtables[products] WHERE long_name = 'leather' AND tribeid = '$tribe[goods_tribe]'");
                 db_op_result($leather,__LINE__,__FILE__);
                $leatherinfo = $leather->fields;
                $whips = 0;
                while($leatherinfo['amount'] > 0 & $act_do['actives'] > 0)
                {

                    $whips += 1;
                    $leatherinfo['amount'] -= 1;
                    $act_do['actives'] -= 1;

                }
                $query = $db->Execute("UPDATE $dbtables[products] set amount = amount + '$whips' WHERE tribeid = '$tribe[goods_tribe]' and long_name = 'whip'");
                  db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("DELETE FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' AND skill_abbr = 'ltr' AND product = 'whip'");
                   db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("UPDATE $dbtables[products] set amount = '$leatherinfo[amount]' where tribeid = '$tribe[goods_tribe]' and long_name = 'leather'");
                   db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Leatherworking: $whips Whips made.')");
            }

            if($act_do['product'] == 'saddlebags')
            {
                $leather = $db->Execute("SELECT * FROM $dbtables[products] WHERE long_name = 'leather' AND tribeid = '$tribe[goods_tribe]'");
                  db_op_result($leather,__LINE__,__FILE__);
                $leatherinfo = $leather->fields;
                $saddlebags = 0;
                while($leatherinfo['amount'] > 3 & $act_do['actives'] > 1)
                {

                    $saddlebags += 1;
                    $leatherinfo['amount'] -= 4;
                    $act_do['actives'] -= 2;

                }
                $query = $db->Execute("UPDATE $dbtables[products] set amount = amount + '$saddlebags' WHERE tribeid = '$tribe[goods_tribe]' and long_name = 'saddlebags'");
                  db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("DELETE FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' AND skill_abbr = 'ltr' AND product = 'saddlebags'");
                   db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("UPDATE $dbtables[products] set amount = '$leatherinfo[amount]' where tribeid = '$tribe[goods_tribe]' and long_name = 'leather'");
                  db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Leatherworking: $saddlebags Saddlebags made.')");
                  db_op_result($query,__LINE__,__FILE__);
            }

            if($act_do['product'] == 'saddle')
            {

                $leather = $db->Execute("SELECT * FROM $dbtables[products] WHERE long_name = 'leather' AND tribeid = '$tribe[goods_tribe]'");
                  db_op_result($leather,__LINE__,__FILE__);
                $leatherinfo = $leather->fields;
                $saddles = 0;
                while($leatherinfo['amount'] > 3 & $act_do['actives'] > 2)
                {

                    $saddles += 1;
                    $leatherinfo['amount'] -= 4;
                    $act_do['actives'] -= 3;

                }
                $query = $db->Execute("UPDATE $dbtables[products] set amount = amount + '$saddles' WHERE tribeid = '$tribe[goods_tribe]' and long_name = 'saddle'");
                   db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("DELETE FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' AND skill_abbr = 'ltr' AND product = 'saddle'");
                  db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("UPDATE $dbtables[products] set amount = '$leatherinfo[amount]' where tribeid = '$tribe[goods_tribe]' and long_name = 'leather'");
                  db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Leatherworking: $saddles Saddles made.')");
                db_op_result($query,__LINE__,__FILE__);
            }

            if($act_do['product'] == 'kayak')
            {

                $leather = $db->Execute("SELECT * FROM $dbtables[products] WHERE long_name = 'leather' AND tribeid = '$tribe[goods_tribe]'");
                  db_op_result($leather,__LINE__,__FILE__);
                $leatherinfo = $leather->fields;
                $struct = $db->Execute("SELECT * FROM $dbtables[products] WHERE long_name = 'structure' AND tribeid = '$tribe[goods_tribe]'");
                 db_op_result($struct,__LINE__,__FILE__);
                $structinfo = $struct->fields;
                $kayaks = 0;
                while($leatherinfo['amount'] > 9 & $act_do['actives'] > 5 & $structinfo['amount'] > 0)
                {
                    $kayaks += 1;
                    $leatherinfo['amount'] -= 10;
                    $act_do['actives'] -= 6;
                    $structinfo['amount'] -= 1;

                }
                $query = $db->Execute("UPDATE $dbtables[products] set amount = amount + '$kayaks' WHERE tribeid = '$tribe[goods_tribe]' and long_name = 'kayak'");
                   db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("DELETE FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' AND skill_abbr = 'ltr' AND product = 'kayak'");
                   db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("UPDATE $dbtables[products] set amount = '$leatherinfo[amount]' where tribeid = '$tribe[goods_tribe]' and long_name = 'leather'");
                  db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("UPDATE $dbtables[products] set amount = '$structinfo[amount]' where tribeid = '$tribe[goods_tribe]' and long_name = 'structure'");
                  db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Leatherworking: $kayaks Kayaks made.')");
                 db_op_result($query,__LINE__,__FILE__);
            }
        }





        $act->MoveNext();
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
