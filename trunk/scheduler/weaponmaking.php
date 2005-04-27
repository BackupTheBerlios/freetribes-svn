<?php
require_once("../config.php");
$time_start = getmicrotime();
include("game_time.php");
connectdb();
$res = $db->Execute("SELECT * FROM $dbtables[tribes]");
 db_op_result($res,__LINE__,__FILE__);
while( !$res->EOF )
{
    $tribe = $res->fields;
    $act = $db->Execute("SELECT * FROM $dbtables[activities] "
                       ."WHERE tribeid = '$tribe[tribeid]' "
                       ."AND skill_abbr = 'wpn'");
     db_op_result($act,__LINE__,__FILE__);
    while( !$act->EOF )
    {
        $act_do = $act->fields;

        if( $act_do[product] == 'sling' )
        {
            $cloth = $db->Execute("SELECT * FROM $dbtables[products] "
                                 ."WHERE proper = 'Cloth' "
                                 ."AND tribeid = '$tribe[goods_tribe]'");
              db_op_result($cloth,__LINE__,__FILE__);
            $clothinfo = $cloth->fields;
            $startcloth = $clothinfo[amount];
            $slings = 0;
            while( $act_do[actives] > 4 && $clothinfo[amount] > 0 )
            {
                $clothinfo[amount] -= 1;
                $act_do[actives] -= 5;
                $slings += 10;
            }
            $deltacloth = $startcloth - $clothinfo[amount];
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + $slings "
                        ."WHERE proper = 'Sling' "
                        ."AND tribeid = '$tribe[goods_tribe]'");
                 db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount - $deltacloth "
                        ."WHERE proper = 'Cloth' "
                        ."AND tribeid = '$tribe[goods_tribe]'");
              db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("DELETE FROM $dbtables[activities] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND skill_abbr = '$act_do[skill_abbr]' "
                        ."AND product = 'sling'");
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
                        ."'Weaponmaking: $slings Slings made using $deltacloth Cloth.')");
           db_op_result($query,__LINE__,__FILE__);
        }

        if( $act_do[product] == 'staves' )
        {
            $hex = $db->Execute("SELECT * FROM $dbtables[hexes] "
                               ."WHERE hex_id = '$tribe[hex_id]'");
                 db_op_result($hex,__LINE__,__FILE__);
            $hexinfo = $hex->fields;

            if( $hexinfo[terrain] == 'df' | $hexinfo[terrain] == 'jg' | $hexinfo[terrain] == 'dh' | $hexinfo[terrain] == 'jh' )
            {
                $staves = 0;
                while( $act_do[actives] > 0 )
                {
                    $staves += 1;
                    $act_do[actives] -= 1;
                }
                $query = $db->Execute("UPDATE $dbtables[products] "
                            ."SET amount = amount + $staves "
                            ."WHERE tribeid = '$tribe[goods_tribe]' "
                            ."AND proper = '$act_do[product]'");
                 db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("DELETE FROM $dbtables[activities] "
                            ."WHERE tribeid = '$tribe[tribeid]' "
                            ."AND product = '$act_do[product]'");
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
                            ."'Weaponmaking: $staves $act_do[product] made.')");
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
                            ."'Weaponmaking: Deciduous or Jungle forests needed for making staves.')");
                   db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("DELETE FROM $dbtables[activities] "
                            ."WHERE tribeid = '$tribe[tribeid]' "
                            ."AND product = '$act_do[product]'");
                   db_op_result($query,__LINE__,__FILE__);
            }
        }


        if( $act_do[product] == 'shaft' )
        {
            $hex = $db->Execute("SELECT * FROM $dbtables[hexes] "
                               ."WHERE hex_id = '$tribe[hex_id]'");
              db_op_result($hex,__LINE__,__FILE__);
            $hexinfo = $hex->fields;

            if( $hexinfo[terrain] == 'df' | $hexinfo[terrain] == 'jg' | $hexinfo[terrain] == 'dh' | $hexinfo[terrain] == 'jh' | $hexinfo[terrain] == 'cf' | $hexinfo[terrain] == 'ch' )
            {
                $staves = 0;
                while( $act_do[actives] > 0 )
                {
                    $staves += 1;
                    $act_do[actives] -= 1;
                }
                $query = $db->Execute("UPDATE $dbtables[products] "
                            ."SET amount = amount + $staves "
                            ."WHERE tribeid = '$tribe[goods_tribe]' "
                            ."AND proper = '$act_do[product]'");
                    db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("DELETE FROM $dbtables[activities] "
                            ."WHERE tribeid = '$tribe[tribeid]' "
                            ."AND product = '$act_do[product]'");
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
                            ."'Weaponmaking: $staves $act_do[product] made.')");
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
                            ."'Weaponmaking: Forests or Jungles needed for making shafts.')");
                  db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("DELETE FROM $dbtables[activities] "
                            ."WHERE tribeid = '$tribe[tribeid]' "
                            ."AND product = '$act_do[product]'");
                   db_op_result($query,__LINE__,__FILE__);
            }
        }


    if( $act_do[product] == 'bow' )
    {
        $hex = $db->Execute("SELECT * FROM $dbtables[hexes] "
                           ."WHERE hex_id = '$tribe[hex_id]'");
            db_op_result($hex,__LINE__,__FILE__);
        $hexinfo = $hex->fields;

        if( $hexinfo[terrain] == 'df' | $hexinfo[terrain] == 'jg' | $hexinfo[terrain] == 'dh' | $hexinfo[terrain] == 'jh' )
        {
            $st = $db->Execute("SELECT * FROM $dbtables[products] "
                              ."WHERE tribeid = '$tribe[goods_tribe]' "
                              ."AND proper = 'Strings'");
              db_op_result($st,__LINE__,__FILE__);
            $string = $st->fields;
            $bows = 0;
            $startstring = $string[amount];
            while( $act_do[actives] > 1 && $string[amount] > 0 )
            {
                $string[amount] -= 1;
                $act_do[actives] -= 2;
                $bows += 1;
            }
            $deltastring = $startstring - $string[amount];
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount - $deltastring "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND proper = 'Strings'");
              db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + $bows "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND proper = 'Bow'");
                db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("DELETE FROM $dbtables[activities] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND product = '$act_do[product]'");
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
                        ."'Weaponmaking: $bows Bows made using $deltastring strings in the forest.')");
                db_op_result($query,__LINE__,__FILE__);
        }
        else
        {
            $st = $db->Execute("SELECT * FROM $dbtables[products] "
                              ."WHERE tribeid = '$tribe[goods_tribe]' "
                              ."AND proper = 'Strings'");
               db_op_result($st,__LINE__,__FILE__);
            $string = $st->fields;
            $stave = $db->Execute("SELECT * FROM $dbtables[products] "
                                 ."WHERE proper = 'Staves' "
                                 ."AND tribeid = '$tribe[goods_tribe]'");
                  db_op_result($stave,__LINE__,__FILE__);
            $staveinfo = $stave->fields;
            $startstring = $string[amount];
            $startstave = $staveinfo[amount];
            $bows = 0;
            while( $act_do[actives] > 0 && $string[amount] > 0 && $staveinfo[amount] > 0 )
            {
                $string[amount] -= 1;
                $act_do[actives] -= 1;
                $bows += 1;
                $staveinfo[amount] -= 1;
            }
            $deltastring = $startstring - $string[amount];
            $deltastave = $startstave - $staveinfo[amount];
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount - $deltastring "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND proper = 'Strings'");
               db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + $bows "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND proper = 'Bow'");
             db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("DELETE FROM $dbtables[activities] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND product = 'bow'");
             db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount - $deltastave "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND proper = 'Staves'");
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
                        ."'Weaponmaking: $bows Bows made using $deltastring strings and $deltastave staves.')");
             db_op_result($query,__LINE__,__FILE__);
        }
        $query = $db->Execute("DELETE FROM $dbtables[activities] "
                    ."WHERE tribeid = '$tribe[tribeid]' "
                    ."AND product = 'bow'");
           db_op_result($query,__LINE__,__FILE__);
    }


    if( $act_do[product] == 'longbow' )
    {
        $hex = $db->Execute("SELECT * FROM $dbtables[hexes] "
                           ."WHERE hex_id = '$tribe[hex_id]'");
               db_op_result($hex,__LINE__,__FILE__);
        $hexinfo = $hex->fields;

        if( $hexinfo[terrain] == 'df' | $hexinfo[terrain] == 'jg' | $hexinfo[terrain] == 'dh' | $hexinfo[terrain] == 'jh' )
        {
            $st = $db->Execute("SELECT * FROM $dbtables[products] "
                              ."WHERE tribeid = '$tribe[goods_tribe]' "
                              ."AND proper = 'Strings'");
               db_op_result($st,__LINE__,__FILE__);
            $string = $st->fields;
            $bows = 0;
            $startstring = $string[amount];
            while( $act_do[actives] > 2 && $string[amount] > 0 )
            {
                $string[amount] -= 1;
                $act_do[actives] -= 3;
                $bows += 1;
            }
            $deltastring = $startstring - $string[amount];
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount - $deltastring "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND proper = 'Strings'");
             db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + $bows "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'longbow'");
             db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("DELETE FROM $dbtables[activities] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND product = 'longbow'");
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
                        ."'Weaponmaking: $bows Longbows made using $deltastring strings in the forest.')");
                 db_op_result($query,__LINE__,__FILE__);
        }
        else
        {
            $st = $db->Execute("SELECT * FROM $dbtables[products] "
                              ."WHERE tribeid = '$tribe[goods_tribe]' "
                              ."AND proper = 'Strings'");
                db_op_result($st,__LINE__,__FILE__);
            $string = $st->fields;
            $stave = $db->Execute("SELECT * FROM $dbtables[products] "
                                 ."WHERE proper = 'Staves' "
                                 ."AND tribeid = '$tribe[goods_tribe]'");
                db_op_result($stave,__LINE__,__FILE__);
            $staveinfo = $stave->fields;
            $startstring = $string[amount];
            $startstave = $staveinfo[amount];
            $bows = 0;
            while( $act_do[actives] > 1 && $string[amount] > 0 && $staveinfo[amount] > 0 )
            {
                $string[amount] -= 1;
                $act_do[actives] -= 2;
                $bows += 1;
                $staveinfo[amount] -= 1;
            }
            $deltastring = $startstring - $string[amount];
            $deltastave = $startstave - $staveinfo[amount];
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount - $deltastring "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND proper = 'Strings'");
              db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + $bows "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'longbow'");
             db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("DELETE FROM $dbtables[activities] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND product = 'longbow'");
             db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount - $deltastave "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND proper = 'Staves'");
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
                        ."'Weaponmaking: $bows Longbows made using $deltastring strings and $deltastave staves.')");
                db_op_result($query,__LINE__,__FILE__);
        }
        $query = $db->Execute("DELETE FROM $dbtables[activities] "
                    ."WHERE tribeid = '$tribe[tribeid]' "
                    ."AND product = 'longbow'");
        db_op_result($query,__LINE__,__FILE__);
    }



if( $act_do[product] == 'crossbow' )
{
    $mtl = $db->Execute("SELECT * FROM $dbtables[resources] "
                      ."WHERE tribeid = '$tribe[goods_tribe]' "
                      ."AND long_name = 'Iron' "
                      ."AND amount > 4");
         db_op_result($mtl,__LINE__,__FILE__);
    if( $mtl->EOF )
    {
        $mtl = $db->Execute("SELECT * FROM $dbtables[resources] "
                          ."WHERE tribeid = '$tribe[goods_tribe]' "
                          ."AND long_name = 'Bronze'");
          db_op_result($mtl,__LINE__,__FILE__);
    }
    $metalinfo = $mtl->fields;
    $startmetal = $metalinfo[amount];
    $col = $db->Execute("SELECT * FROM $dbtables[resources] "
                       ."WHERE tribeid = '$tribe[goods_tribe]' "
                       ."AND long_name = 'Coal'");
        db_op_result($col,__LINE__,__FILE__);
    $coal = $col->fields;
    $startcoal = $coal[amount];
    $st = $db->Execute("SELECT * FROM $dbtables[products] "
                      ."WHERE tribeid = '$tribe[goods_tribe]' "
                      ."AND proper = 'Strings'");
     db_op_result($st,__LINE__,__FILE__);
    $string = $st->fields;
    $bows = 0;
    $startstring = $string[amount];
    while( $act_do[actives] > 3 && $string[amount] > 0 && $metalinfo[amount] > 4 && $coal[amount] > 39)
    {
        $string[amount] -= 1;
        $act_do[actives] -= 2;
        $bows += 1;
        $metalinfo[amount] -= 5;
        $coal[amount] -= 40;
    }
    $deltalmetal = $startmetal - $metalinfo[amount];
    $deltacoal = $startcoal - $coal[amount];
    $deltastring = $startstring - $string[amount];
    $query = $db->Execute("UPDATE $dbtables[resources] "
                ."SET amount = amount - $deltacoal "
                ."WHERE tribeid = '$tribe[goods_tribe]' "
                ."AND long_name = 'Coal'");
    db_op_result($query,__LINE__,__FILE__);
    $query = $db->Execute("UPDATE $dbtables[resources] "
                ."SET amount = amount - $deltametal "
                ."WHERE tribeid = '$tribe[goods_tribe]' "
                ."AND long_name = '$metalinfo[long_name]'");
    db_op_result($query,__LINE__,__FILE__);
   $query = $db->Execute("UPDATE $dbtables[products] "
                ."SET amount = amount - $deltastring "
                ."WHERE tribeid = '$tribe[goods_tribe]' "
                ."AND proper = 'Strings'");
    db_op_result($query,__LINE__,__FILE__);
    $query = $db->Execute("UPDATE $dbtables[products] "
                ."SET amount = amount + $bows "
                ."WHERE tribeid = '$tribe[goods_tribe]' "
                ."AND long_name = 'crossbow'");
   db_op_result($query,__LINE__,__FILE__);
    $query = $db->Execute("DELETE FROM $dbtables[activities] "
                ."WHERE tribeid = '$tribe[tribeid]' "
                ."AND product = 'crossbow'");
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
                ."'Weaponmaking: $bows Crossbows made using $deltastring strings, $deltametal $metealinfo[long_name], $deltacoal coal.')");
 db_op_result($query,__LINE__,__FILE__);
}



if($act_do[product] == 'spetum'){
$shaft = $db->Execute("SELECT * FROM $dbtables[products] WHERE long_name = 'shaft' AND tribeid = '$tribe[goods_tribe]'");
     db_op_result($shaft,__LINE__,__FILE__);
$coal = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Coal' AND tribeid = '$tribe[goods_tribe]'");
      db_op_result($coal,__LINE__,__FILE__);
$brnz = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Bronze' AND tribeid = '$tribe[goods_tribe]'");
      db_op_result($brnz,__LINE__,__FILE__);
$stshft = $shaft->fields;
$stcoal = $coal->fields;
$stbrnz = $brnz->fields;
$coal = $stcoal[amount];
$brnz = $stbrnz[amount];
$shft = $stshft[amount];
$spetum = 0;
while($act_do[actives] > 0 & $stshft[amount] > 0 & $stbrnz[amount] > 1 & $stcoal[amount] > 4){
$act_do[actives] -= 1;
$stshft[amount] -= 1;
$stbrnz[amount] -= 2;
$stcoal[amount] -= 5;
$spetum += 1;
}
$coaldelta = $coal - $stcoal[amount];
$brnzdelta = $brnz - $stbrnz[amount];
$shftdelta = $shft - $stshft[amount];
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount + $spetum WHERE long_name = 'spetum' AND tribeid = '$tribe[goods_tribe]'");
   db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount - $shftdelta WHERE long_name = 'shaft' AND tribeid = '$tribe[goods_tribe]'");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = amount - $coaldelta WHERE long_name = 'Coal' AND tribeid = '$tribe[goods_tribe]'");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = amount - $brnzdelta WHERE long_name = 'Bronze' AND tribeid = '$tribe[goods_tribe]'");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Weaponmaking: $spetum Spetums made using $coaldelta Coal, $brnzdelta Bronze, and $shftdelta Shafts.')");
   db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("DELETE FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' AND product = 'spetum' AND skill_abbr = 'wpn'");
   db_op_result($query,__LINE__,__FILE__);
}

if($act_do[product] == 'bronzespear'){
$shaft = $db->Execute("SELECT * FROM $dbtables[products] WHERE long_name = 'shaft' AND tribeid = '$tribe[goods_tribe]'");
   db_op_result($shaft,__LINE__,__FILE__);
$coal = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Coal' AND tribeid = '$tribe[goods_tribe]'");
   db_op_result($coal,__LINE__,__FILE__);
$brnz = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Bronze' AND tribeid = '$tribe[goods_tribe]'");
   db_op_result($brnz,__LINE__,__FILE__);
$stshft = $shaft->fields;
$stcoal = $coal->fields;
$stbrnz = $brnz->fields;
$coal = $stcoal[amount];
$brnz = $stbrnz[amount];
$shft = $stshft[amount];
$spear = 0;
while($act_do[actives] > 0 & $stshft[amount] > 0 & $stbrnz[amount] > 1 & $stcoal[amount] > 4){
$act_do[actives] -= 1;
$stshft[amount] -= 1;
$stbrnz[amount] -= 2;
$stcoal[amount] -= 5;
$spear += 1;
}
$coaldelta = $coal - $stcoal[amount];
$brnzdelta = $brnz - $stbrnz[amount];
$shftdelta = $shft - $stshft[amount];
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount + $spear WHERE long_name = '$act_do[product]' AND tribeid = '$tribe[goods_tribe]'");
    db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount - $shftdelta WHERE long_name = 'shaft' AND tribeid = '$tribe[goods_tribe]'");
   db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = amount - $coaldelta WHERE long_name = 'Coal' AND tribeid = '$tribe[goods_tribe]'");
   db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = amount - $brnzdelta WHERE long_name = 'Bronze' AND tribeid = '$tribe[goods_tribe]'");
    db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Weaponmaking: $spear Spears made using $coaldelta Coal, $brnzdelta Bronze, and $shftdelta Shafts.')");
    db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("DELETE FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' AND product = '$act_do[product]' AND skill_abbr = 'wpn'");
     db_op_result($query,__LINE__,__FILE__);
}


if($act_do[product] == 'ironspear' | $act_do[product] == 'spears'){
$shaft = $db->Execute("SELECT * FROM $dbtables[products] WHERE long_name = 'shaft' AND tribeid = '$tribe[goods_tribe]'");
        db_op_result($shaft,__LINE__,__FILE__);
$coal = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Coal' AND tribeid = '$tribe[goods_tribe]'");
        db_op_result($coal,__LINE__,__FILE__);
$iron = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Iron' AND tribeid = '$tribe[goods_tribe]'");
        db_op_result($iron,__LINE__,__FILE__);
$stshft = $shaft->fields;
$stcoal = $coal->fields;
$stiron = $iron->fields;
$coal = $stcoal[amount];
$iron = $stiron[amount];
$shft = $stshft[amount];
$spear = 0;
while($act_do[actives] > 0 & $stshft[amount] > 0 & $stiron[amount] > 1 & $stcoal[amount] > 9){
$act_do[actives] -= 1;
$stshft[amount] -= 1;
$stiron[amount] -= 2;
$stcoal[amount] -= 10;
$spear += 1;
}
$coaldelta = $coal - $stcoal[amount];
$irondelta = $iron - $stiron[amount];
$shftdelta = $shft - $stshft[amount];
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount + $spear WHERE long_name = '$act_do[product]' AND tribeid = '$tribe[goods_tribe]'");
    db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount - $shftdelta WHERE long_name = 'shaft' AND tribeid = '$tribe[goods_tribe]'");
     db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = amount - $coaldelta WHERE long_name = 'Coal' AND tribeid = '$tribe[goods_tribe]'");
    db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = amount - $irondelta WHERE long_name = 'Iron' AND tribeid = '$tribe[goods_tribe]'");
    db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Weaponmaking: $spear Spears made using $coaldelta Coal, $irondelta Iron, and $shftdelta Shafts.')");
    db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("DELETE FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' AND product = '$act_do[product]' AND skill_abbr = 'wpn'");
     db_op_result($query,__LINE__,__FILE__);
}

if($act_do[product] == 'steelspear'){
$shaft = $db->Execute("SELECT * FROM $dbtables[products] WHERE long_name = 'shaft' AND tribeid = '$tribe[goods_tribe]'");
      db_op_result($shaft,__LINE__,__FILE__);
$coal = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Coke' AND tribeid = '$tribe[goods_tribe]'");
      db_op_result($coal,__LINE__,__FILE__);
$steel = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Steel' AND tribeid = '$tribe[goods_tribe]'");
      db_op_result($steel,__LINE__,__FILE__);
$stshft = $shaft->fields;
$stcoal = $coal->fields;
$ststeel = $steel->fields;
$coal = $stcoal[amount];
$steel = $ststeel[amount];
$shft = $stshft[amount];
$spear = 0;
while($act_do[actives] > 0 & $stshft[amount] > 0 & $ststeel[amount] > 1 & $stcoal[amount] > 9){
$act_do[actives] -= 1;
$stshft[amount] -= 1;
$ststeel[amount] -= 2;
$stcoal[amount] -= 10;
$spear += 1;
}
$coaldelta = $coal - $stcoal[amount];
$steeldelta = $steel - $ststeel[amount];
$shftdelta = $shft - $stshft[amount];
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount + $spear WHERE long_name = '$act_do[product]' AND tribeid = '$tribe[goods_tribe]'");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount - $shftdelta WHERE long_name = 'shaft' AND tribeid = '$tribe[goods_tribe]'");
   db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = amount - $coaldelta WHERE long_name = 'Coke' AND tribeid = '$tribe[goods_tribe]'");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = amount - $steeldelta WHERE long_name = 'Steel' AND tribeid = '$tribe[goods_tribe]'");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Weaponmaking: $spear Spears made using $coaldelta Coke, $steeldelta Steel, and $shftdelta Shafts.')");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("DELETE FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' AND product = '$act_do[product]' AND skill_abbr = 'wpn'");
  db_op_result($query,__LINE__,__FILE__);
}

if($act_do[product] == 'steel1spear'){
$shaft = $db->Execute("SELECT * FROM $dbtables[products] WHERE long_name = 'shaft' AND tribeid = '$tribe[goods_tribe]'");
     db_op_result($shaft,__LINE__,__FILE__);
$coal = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Coke' AND tribeid = '$tribe[goods_tribe]'");
     db_op_result($coal,__LINE__,__FILE__);
$steel = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Steel_1' AND tribeid = '$tribe[goods_tribe]'");
     db_op_result($steel,__LINE__,__FILE__);
$stshft = $shaft->fields;
$stcoal = $coal->fields;
$ststeel = $steel->fields;
$coal = $stcoal[amount];
$steel = $ststeel[amount];
$shft = $stshft[amount];
$spear = 0;
while($act_do[actives] > 0 & $stshft[amount] > 0 & $ststeel[amount] > 1 & $stcoal[amount] > 9){
$act_do[actives] -= 1;
$stshft[amount] -= 1;
$ststeel[amount] -= 2;
$stcoal[amount] -= 10;
$spear += 1;
}
$coaldelta = $coal - $stcoal[amount];
$steeldelta = $steel - $ststeel[amount];
$shftdelta = $shft - $stshft[amount];
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount + $spear WHERE long_name = '$act_do[product]' AND tribeid = '$tribe[goods_tribe]'");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount - $shftdelta WHERE long_name = 'shaft' AND tribeid = '$tribe[goods_tribe]'");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = amount - $coaldelta WHERE long_name = 'Coke' AND tribeid = '$tribe[goods_tribe]'");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = amount - $steeldelta WHERE long_name = 'Steel_1' AND tribeid = '$tribe[goods_tribe]'");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Weaponmaking: $spear Spears made using $coaldelta Coke, $steeldelta Steel_1, and $shftdelta Shafts.')");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("DELETE FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' AND product = '$act_do[product]' AND skill_abbr = 'wpn'");
 db_op_result($query,__LINE__,__FILE__);
}

if($act_do[product] == 'steel2spear'){
$shaft = $db->Execute("SELECT * FROM $dbtables[products] WHERE long_name = 'shaft' AND tribeid = '$tribe[goods_tribe]'");
      db_op_result($shaft,__LINE__,__FILE__);
$coal = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Coke' AND tribeid = '$tribe[goods_tribe]'");
      db_op_result($coal,__LINE__,__FILE__);
$steel = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Steel_2' AND tribeid = '$tribe[goods_tribe]'");
      db_op_result($steel,__LINE__,__FILE__);
$stshft = $shaft->fields;
$stcoal = $coal->fields;
$ststeel = $steel->fields;
$coal = $stcoal[amount];
$steel = $ststeel[amount];
$shft = $stshft[amount];
$spear = 0;
while($act_do[actives] > 0 & $stshft[amount] > 0 & $ststeel[amount] > 1 & $stcoal[amount] > 9){
$act_do[actives] -= 1;
$stshft[amount] -= 1;
$ststeel[amount] -= 2;
$stcoal[amount] -= 10;
$spear += 1;
}
$coaldelta = $coal - $stcoal[amount];
$steeldelta = $steel - $ststeel[amount];
$shftdelta = $shft - $stshft[amount];
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount + $spear WHERE long_name = '$act_do[product]' AND tribeid = '$tribe[goods_tribe]'");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount - $shftdelta WHERE long_name = 'shaft' AND tribeid = '$tribe[goods_tribe]'");
   db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = amount - $coaldelta WHERE long_name = 'Coke' AND tribeid = '$tribe[goods_tribe]'");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = amount - $steeldelta WHERE long_name = 'Steel_2' AND tribeid = '$tribe[goods_tribe]'");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Weaponmaking: $spear Spears made using $coaldelta Coke, $steeldelta Steel_2, and $shftdelta Shafts.')");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("DELETE FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' AND product = '$act_do[product]' AND skill_abbr = 'wpn'");
 db_op_result($query,__LINE__,__FILE__);
}

if($act_do[product] == 'ironmace'){
$coal = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Coal' AND tribeid = '$tribe[goods_tribe]'");
 db_op_result($coal,__LINE__,__FILE__);
$iron = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Iron' AND tribeid = '$tribe[goods_tribe]'");
 db_op_result($iron,__LINE__,__FILE__);
$stcoal = $coal->fields;
$stiron = $iron->fields;
$coal = $stcoal[amount];
$iron = $stiron[amount];
$mace = 0;
while($act_do[actives] > 1 & $stcoal[amount] > 29 & $stiron[amount] > 5){
$act_do[actives] -= 2;
$stcoal[amount] -= 30;
$stiron[amount] -= 6;
$mace += 1;
}
$coaldelta = $coal - $stcoal[amount];
$irondelta = $iron - $stiron[amount];
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount + $mace WHERE long_name = '$act_do[product]' AND tribeid = '$tribe[goods_tribe]'");
   db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = amount - $coaldelta WHERE long_name = 'Coal' AND tribeid = '$tribe[goods_tribe]'");
   db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = amount - $irondelta WHERE long_name = 'Iron' AND tribeid = '$tribe[goods_tribe]'");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Weaponmaking: $mace Maces made using $coaldelta Coal and $irondelta Iron.')");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("DELETE FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' AND product = '$act_do[product]' AND skill_abbr = 'wpn'");
 db_op_result($query,__LINE__,__FILE__);
}

if($act_do[product] == 'bronzeaxe'){
$coal = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Coal' AND tribeid = '$tribe[goods_tribe]'");
   db_op_result($coal,__LINE__,__FILE__);
$metal = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Bronze' AND tribeid = '$tribe[goods_tribe]'");
    db_op_result($metal,__LINE__,__FILE__);
$clb = $db->Execute("SELECT * FROM $dbtables[products] WHERE long_name = 'club' AND tribeid = '$tribe[goods_tribe]'");
    db_op_result($clb,__LINE__,__FILE__);
$stcoal = $coal->fields;
$stmetal = $metal->fields;
$stclb = $clb->fields;
$club = $stclb[amount];
$coal = $stcoal[amount];
$metal = $stmetal[amount];
$product = 0;
while($act_do[actives] > 1 && $stclb[amount] > 0 && $stcoal[amount] > 14 && $stmetal[amount] > 3){
$act_do[actives] -= 2;
$stcoal[amount] -= 15;
$stmetal[amount] -= 4;
$stclb[amount] -= 1;
$product += 1;
}
$coaldelta = $coal - $stcoal[amount];
$metaldelta = $metal - $stmetal[amount];
$clubdelta = $club - $stclb[amount];
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount - $clubdelta WHERE long_name = 'club' AND tribeid = '$tribe[goods_tribe]'");
   db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount + $product WHERE long_name = '$act_do[product]' AND tribeid = '$tribe[goods_tribe]'");
    db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = amount - $coaldelta WHERE long_name = 'Coal' AND tribeid = '$tribe[goods_tribe]'");
   db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = amount - $metaldelta WHERE long_name = 'Bronze' AND tribeid = '$tribe[goods_tribe]'");
   db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Weaponmaking: $product Bronze Axes made using $coaldelta Coal, $metaldelta Bronze, and $clubdelta clubs.')");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("DELETE FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' AND product = '$act_do[product]' AND skill_abbr = 'wpn'");
  db_op_result($query,__LINE__,__FILE__);
}

if($act_do[product] == 'ironaxe'){
$coal = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Coal' AND tribeid = '$tribe[goods_tribe]'");
      db_op_result($coal,__LINE__,__FILE__);
$metal = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Iron' AND tribeid = '$tribe[goods_tribe]'");
      db_op_result($metal,__LINE__,__FILE__);
$clb = $db->Execute("SELECT * FROM $dbtables[products] WHERE long_name = 'club' AND tribeid = '$tribe[goods_tribe]'");
     db_op_result($clb,__LINE__,__FILE__);
$stclb = $clb->fields;
$stcoal = $coal->fields;
$stmetal = $metal->fields;
$club = $stclb[amount];
$coal = $stcoal[amount];
$metal = $stmetal[amount];
$product = 0;
while( $stclb[amount] > 0 && $act_do[actives] > 1 & $stcoal[amount] > 19 & $stmetal[amount] > 3){
$act_do[actives] -= 2;
$stcoal[amount] -= 20;
$stmetal[amount] -= 4;
$stclb[amount] -= 1;
$product += 1;
}
$coaldelta = $coal - $stcoal[amount];
$metaldelta = $metal - $stmetal[amount];
$clubdelta = $club - $stclb[amount];
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount - $clubdelta WHERE long_name = 'club' AND tribeid = '$tribe[goods_tribe]'");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount + $product WHERE long_name = '$act_do[product]' AND tribeid = '$tribe[goods_tribe]'");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = amount - $coaldelta WHERE long_name = 'Coal' AND tribeid = '$tribe[goods_tribe]'");
   db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = amount - $metaldelta WHERE long_name = 'Iron' AND tribeid = '$tribe[goods_tribe]'");
   db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Weaponmaking: $product Iron Axes made using $coaldelta Coal, $metaldelta Iron, and $clubdelta clubs.')");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("DELETE FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' AND product = '$act_do[product]' AND skill_abbr = 'wpn'");
 db_op_result($query,__LINE__,__FILE__);
}

if($act_do[product] == 'steelaxe'){
$clb = $db->Execute("SELECT * FROM $dbtables[products] WHERE long_name = 'club' AND tribeid = '$tribe[goods_tribe]'");
   db_op_result($clb,__LINE__,__FILE__);
$coal = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Coke' AND tribeid = '$tribe[goods_tribe]'");
  db_op_result($coal,__LINE__,__FILE__);
$metal = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Steel' AND tribeid = '$tribe[goods_tribe]'");
  db_op_result($metal,__LINE__,__FILE__);
$stclb = $clb->fields;
$stcoal = $coal->fields;
$stmetal = $metal->fields;
$club = $stclb[amount];
$coal = $stcoal[amount];
$metal = $stmetal[amount];
$product = 0;
while( $stclb[amount] > 0 && $act_do[actives] > 1 & $stcoal[amount] > 19 & $stmetal[amount] > 3){
$act_do[actives] -= 2;
$stcoal[amount] -= 20;
$stmetal[amount] -= 4;
$stclb[amount] -= 1;
$product += 1;
}
$coaldelta = $coal - $stcoal[amount];
$metaldelta = $metal - $stmetal[amount];
$clubdelta = $club - $stclb[amount];
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount - $clubdelta WHERE long_name = 'club' AND tribeid = '$tribe[goods_tribe]'");
    db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount + $product WHERE long_name = '$act_do[product]' AND tribeid = '$tribe[goods_tribe]'");
   db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = amount - $coaldelta WHERE long_name = 'Coke' AND tribeid = '$tribe[goods_tribe]'");
   db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = amount - $metaldelta WHERE long_name = 'Steel' AND tribeid = '$tribe[goods_tribe]'");
   db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Weaponmaking: $product Steel Axes made using $coaldelta Coal, $metaldelta Steel and $clubdelta clubs.')");
    db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("DELETE FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' AND product = '$act_do[product]' AND skill_abbr = 'wpn'");
   db_op_result($query,__LINE__,__FILE__);
}

if($act_do[product] == 'steel1axe'){
$coal = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Coke' AND tribeid = '$tribe[goods_tribe]'");
   db_op_result($coal,__LINE__,__FILE__);
$metal = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Steel_1' AND tribeid = '$tribe[goods_tribe]'");
   db_op_result($metal,__LINE__,__FILE__);
$clb = $db->Execute("SELECT * FROM $dbtables[products] WHERE long_name = 'club' AND tribeid = '$tribe[goods_tribe]'");
  db_op_result($clb,__LINE__,__FILE__);
$stclb = $clb->fields;
$stcoal = $coal->fields;
$stmetal = $metal->fields;
$club = $stclb[amount];
$coal = $stcoal[amount];
$metal = $stmetal[amount];
$product = 0;
while( $stclb[amount] > 0 && $act_do[actives] > 1 & $stcoal[amount] > 19 & $stmetal[amount] > 3){
$act_do[actives] -= 2;
$stcoal[amount] -= 20;
$stmetal[amount] -= 4;
$stclb[amount] -= 1;
$product += 1;
}
$clubdelta = $club - $stclb[amount];
$coaldelta = $coal - $stcoal[amount];
$metaldelta = $metal - $stmetal[amount];
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount - $clubdelta WHERE long_name = 'club' AND tribeid = '$tribe[goods_tribe]'");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount + $product WHERE long_name = '$act_do[product]' AND tribeid = '$tribe[goods_tribe]'");
   db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = amount - $coaldelta WHERE long_name = 'Coke' AND tribeid = '$tribe[goods_tribe]'");
   db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = amount - $metaldelta WHERE long_name = 'Steel_1' AND tribeid = '$tribe[goods_tribe]'");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Weaponmaking: $product Steel_1 Axes made using $coaldelta Coke, $metaldelta Steel_1 and $clubdelta clubs.')");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("DELETE FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' AND product = '$act_do[product]' AND skill_abbr = 'wpn'");
 db_op_result($query,__LINE__,__FILE__);
}

if($act_do[product] == 'steel2axe'){
$coal = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Coke' AND tribeid = '$tribe[goods_tribe]'");
  db_op_result($coal,__LINE__,__FILE__);
$metal = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Steel_2' AND tribeid = '$tribe[goods_tribe]'");
  db_op_result($metal,__LINE__,__FILE__);
$clb = $db->Execute("SELECT * FROM $dbtables[products] WHERE long_name = 'club' AND tribeid = '$tribe[goods_tribe]'");
 db_op_result($clb,__LINE__,__FILE__);
$stclb = $clb->fields;
$stcoal = $coal->fields;
$stmetal = $metal->fields;
$club = $stclb[amount];
$coal = $stcoal[amount];
$metal = $stmetal[amount];
$product = 0;
while( $stclb[amount] > 0 && $act_do[actives] > 1 & $stcoal[amount] > 19 & $stmetal[amount] > 3){
$act_do[actives] -= 2;
$stcoal[amount] -= 20;
$stmetal[amount] -= 4;
$stclb[amount] -= 1;
$product += 1;
}
$coaldelta = $coal - $stcoal[amount];
$metaldelta = $metal - $stmetal[amount];
$clubdelta = $club - $stclb[amount];
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount - $clubdelta WHERE long_name = 'club' AND tribeid = '$tribe[goods_tribe]'");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount + $product WHERE long_name = '$act_do[product]' AND tribeid = '$tribe[goods_tribe]'");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = amount - $coaldelta WHERE long_name = 'Coke' AND tribeid = '$tribe[goods_tribe]'");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = amount - $metaldelta WHERE long_name = 'Steel_2' AND tribeid = '$tribe[goods_tribe]'");
   db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Weaponmaking: $product Steel_2 Axes made using $coaldelta Coke, $metaldelta Steel_2, and $clubdelta clubs.')");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("DELETE FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' AND product = '$act_do[product]' AND skill_abbr = 'wpn'");
 db_op_result($query,__LINE__,__FILE__);
}

if($act_do[product] == 'falchions'){
$coal = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Coal' AND tribeid = '$tribe[goods_tribe]'");
  db_op_result($coal,__LINE__,__FILE__);
$metal = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Bronze' AND tribeid = '$tribe[goods_tribe]'");
  db_op_result($metal,__LINE__,__FILE__);
$stcoal = $coal->fields;
$stmetal = $metal->fields;
$coal = $stcoal[amount];
$metal = $stmetal[amount];
$product = 0;
while($act_do[actives] > 1 & $stcoal[amount] > 14 & $stmetal[amount] > 4){
$act_do[actives] -= 2;
$stcoal[amount] -= 15;
$stmetal[amount] -= 5;
$product += 1;
}
$coaldelta = $coal - $stcoal[amount];
$metaldelta = $metal - $stmetal[amount];
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount + $product WHERE long_name = '$act_do[product]' AND tribeid = '$tribe[goods_tribe]'");
   db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = amount - $coaldelta WHERE long_name = 'Coal' AND tribeid = '$tribe[goods_tribe]'");
   db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = amount - $metaldelta WHERE long_name = 'Bronze' AND tribeid = '$tribe[goods_tribe]'");
   db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Weaponmaking: $product Falchions made using $coaldelta Coal and $metaldelta Bronze.')");
    db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("DELETE FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' AND product = '$act_do[product]' AND skill_abbr = 'wpn'");
    db_op_result($query,__LINE__,__FILE__);
}

if($act_do[product] == 'ironsword'){
$coal = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Coal' AND tribeid = '$tribe[goods_tribe]'");
 db_op_result($coal,__LINE__,__FILE__);
$metal = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Iron' AND tribeid = '$tribe[goods_tribe]'");
 db_op_result($metal,__LINE__,__FILE__);
$stcoal = $coal->fields;
$stmetal = $metal->fields;
$coal = $stcoal[amount];
$metal = $stmetal[amount];
$product = 0;
while($act_do[actives] > 2 & $stcoal[amount] > 29 & $stmetal[amount] > 4){
$act_do[actives] -= 3;
$stcoal[amount] -= 30;
$stmetal[amount] -= 5;
$product += 1;
}
$coaldelta = $coal - $stcoal[amount];
$metaldelta = $metal - $stmetal[amount];
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount + $product WHERE long_name = '$act_do[product]' AND tribeid = '$tribe[goods_tribe]'");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = amount - $coaldelta WHERE long_name = 'Coal' AND tribeid = '$tribe[goods_tribe]'");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = amount - $metaldelta WHERE long_name = 'Iron' AND tribeid = '$tribe[goods_tribe]'");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Weaponmaking: $product Iron Swords made using $coaldelta Coal and $metaldelta Iron.')");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("DELETE FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' AND product = '$act_do[product]' AND skill_abbr = 'wpn'");
 db_op_result($query,__LINE__,__FILE__);
}

if($act_do[product] == 'steelsword'){
$coal = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Coke' AND tribeid = '$tribe[goods_tribe]'");
db_op_result($coal,__LINE__,__FILE__);
$metal = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Steel' AND tribeid = '$tribe[goods_tribe]'");
db_op_result($metal,__LINE__,__FILE__);
$stcoal = $coal->fields;
$stmetal = $metal->fields;
$coal = $stcoal[amount];
$metal = $stmetal[amount];
$product = 0;
while($act_do[actives] > 2 & $stcoal[amount] > 29 & $stmetal[amount] > 4){
$act_do[actives] -= 3;
$stcoal[amount] -= 30;
$stmetal[amount] -= 5;
$product += 1;
}
$coaldelta = $coal - $stcoal[amount];
$metaldelta = $metal - $stmetal[amount];
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount + $product WHERE long_name = '$act_do[product]' AND tribeid = '$tribe[goods_tribe]'");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = amount - $coaldelta WHERE long_name = 'Coke' AND tribeid = '$tribe[goods_tribe]'");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = amount - $metaldelta WHERE long_name = 'Steel' AND tribeid = '$tribe[goods_tribe]'");
   db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Weaponmaking: $product Steel Swords made using $coaldelta Coke and $metaldelta Steel.')");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("DELETE FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' AND product = '$act_do[product]' AND skill_abbr = 'wpn'");
db_op_result($query,__LINE__,__FILE__);
}

if($act_do[product] == 'steel1sword'){
$coal = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Coke' AND tribeid = '$tribe[goods_tribe]'");
  db_op_result($coal,__LINE__,__FILE__);
$metal = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Steel_1' AND tribeid = '$tribe[goods_tribe]'");
  db_op_result($metal,__LINE__,__FILE__);
$stcoal = $coal->fields;
$stmetal = $metal->fields;
$coal = $stcoal[amount];
$metal = $stmetal[amount];
$product = 0;
while($act_do[actives] > 2 & $stcoal[amount] > 29 & $stmetal[amount] > 4){
$act_do[actives] -= 3;
$stcoal[amount] -= 30;
$stmetal[amount] -= 5;
$product += 1;
}
$coaldelta = $coal - $stcoal[amount];
$metaldelta = $metal - $stmetal[amount];
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount + $product WHERE long_name = '$act_do[product]' AND tribeid = '$tribe[goods_tribe]'");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = amount - $coaldelta WHERE long_name = 'Coke' AND tribeid = '$tribe[goods_tribe]'");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = amount - $metaldelta WHERE long_name = 'Steel_1' AND tribeid = '$tribe[goods_tribe]'");
   db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Weaponmaking: $product Steel_1 Swords made using $coaldelta Coke and $metaldelta Steel_1.')");
    db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("DELETE FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' AND product = '$act_do[product]' AND skill_abbr = 'wpn'");
  db_op_result($query,__LINE__,__FILE__);
}

if($act_do[product] == 'steel2sword'){
$coal = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Coke' AND tribeid = '$tribe[goods_tribe]'");
  db_op_result($coal,__LINE__,__FILE__);
$metal = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = 'Steel_2' AND tribeid = '$tribe[goods_tribe]'");
 db_op_result($metal,__LINE__,__FILE__);
$stcoal = $coal->fields;
$stmetal = $metal->fields;
$coal = $stcoal[amount];
$metal = $stmetal[amount];
$product = 0;
while($act_do[actives] > 2 & $stcoal[amount] > 29 & $stmetal[amount] > 4){
$act_do[actives] -= 3;
$stcoal[amount] -= 30;
$stmetal[amount] -= 5;
$product += 1;
}
$coaldelta = $coal - $stcoal[amount];
$metaldelta = $metal - $stmetal[amount];
$query = $db->Execute("UPDATE $dbtables[products] SET amount = amount + $product WHERE long_name = '$act_do[product]' AND tribeid = '$tribe[goods_tribe]'");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = amount - $coaldelta WHERE long_name = 'Coke' AND tribeid = '$tribe[goods_tribe]'");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[resources] SET amount = amount - $metaldelta WHERE long_name = 'Steel_2' AND tribeid = '$tribe[goods_tribe]'");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[logs] VALUES('','$month[count]','$year[count]','$tribe[clanid]','$tribe[tribeid]','UPDATE','$stamp','Weaponmaking: $product Steel_2 Swords made using $coaldelta Coke and $metaldelta Steel_2.')");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("DELETE FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' AND product = '$act_do[product]' AND skill_abbr = 'wpn'");
db_op_result($query,__LINE__,__FILE__);
}

if( $act_do[product] == 'arbalest' )
{
    $coal = $db->Execute("SELECT * FROM $dbtables[resources] "
                        ."WHERE long_name = 'Coal' "
                        ."AND tribeid = '$tribe[goods_tribe]'");
        db_op_result($coal,__LINE__,__FILE__);
    $metal = $db->Execute("SELECT * FROM $dbtables[resources] "
                         ."WHERE long_name = 'Bronze' "
                         ."AND tribeid = '$tribe[goods_tribe]' "
                         ."AND amount > 0");
         db_op_result($metal,__LINE__,__FILE__);
    if( $metal->EOF )
    {
        $metal = $db->Execute("SELECT * FROM $dbtables[resources] "
                             ."WHERE long_name = 'Iron' AND "
                             ."tribeid = '$tribe[goods_tribe]'");
            db_op_result($metal,__LINE__,__FILE__);
        $coalneeded = 15;
        $coalcheck = 14;
    }
    $stcoal = $coal->fields;
    $stmetal = $metal->fields;
    $coal = $stcoal[amount];
    $metal = $stmetal[amount];
    $product = 0;
    while( $act_do[actives] > 2 && $stcoal[amount] > 19 && $stmetal[amount] > 1 )
    {
        $act_do[actives] -= 3;
        $stcoal[amount] -= 20;
        $stmetal[amount] -= 2;
        $product += 1;
    }
    $coaldelta = $coal - $stcoal[amount];
    $metaldelta = $metal - $stmetal[amount];
    $query = $db->Execute("UPDATE $dbtables[products] "
                ."SET amount = amount + $product "
                ."WHERE long_name = '$act_do[product]' "
                ."AND tribeid = '$tribe[goods_tribe]'");
    db_op_result($query,__LINE__,__FILE__);
    $query = $db->Execute("UPDATE $dbtables[resources] "
                ."SET amount = amount - $coaldelta "
                ."WHERE long_name = 'Coal' "
                ."AND tribeid = '$tribe[goods_tribe]'");
    db_op_result($query,__LINE__,__FILE__);
    $query = $db->Execute("UPDATE $dbtables[resources] "
                ."SET amount = amount - $metaldelta "
                ."WHERE long_name = '$stmetal[long_name]' "
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
                ."'Weaponmaking: $product Arbalests made using $coaldelta Coal and $metaldelta $stmetal[long_name].')");
    db_op_result($query,__LINE__,__FILE__);
    $query = $db->Execute("DELETE FROM $dbtables[activities] "
                ."WHERE tribeid = '$tribe[tribeid]' "
                ."AND product = '$act_do[product]' "
                ."AND skill_abbr = 'wpn'");
       db_op_result($query,__LINE__,__FILE__);
}

if( $act_do[product] == 'repeatingarbalest' )
{
    $coal = $db->Execute("SELECT * FROM $dbtables[resources] "
                        ."WHERE long_name = 'Coal' "
                        ."AND tribeid = '$tribe[goods_tribe]'");
       db_op_result($coal,__LINE__,__FILE__);
        $metal = $db->Execute("SELECT * FROM $dbtables[resources] "
                             ."WHERE long_name = 'Iron' AND "
                             ."tribeid = '$tribe[goods_tribe]'");
        db_op_result($metal,__LINE__,__FILE__);
    $stcoal = $coal->fields;
    $stmetal = $metal->fields;
    $coal = $stcoal[amount];
    $metal = $stmetal[amount];
    $product = 0;
    while( $act_do[actives] > 3 && $stcoal[amount] > 24 && $stmetal[amount] > 1 )
    {
        $act_do[actives] -= 4;
        $stcoal[amount] -= 25;
        $stmetal[amount] -= 2;
        $product += 1;
    }
    $coaldelta = $coal - $stcoal[amount];
    $metaldelta = $metal - $stmetal[amount];
    $query = $db->Execute("UPDATE $dbtables[products] "
                ."SET amount = amount + $product "
                ."WHERE long_name = '$act_do[product]' "
                ."AND tribeid = '$tribe[goods_tribe]'");
    db_op_result($query,__LINE__,__FILE__);
    $query = $db->Execute("UPDATE $dbtables[resources] "
                ."SET amount = amount - $coaldelta "
                ."WHERE long_name = 'Coal' "
                ."AND tribeid = '$tribe[goods_tribe]'");
    db_op_result($query,__LINE__,__FILE__);
    $query = $db->Execute("UPDATE $dbtables[resources] "
                ."SET amount = amount - $metaldelta "
                ."WHERE long_name = '$stmetal[long_name]' "
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
                ."'Weaponmaking: $product Arbalests made using $coaldelta Coal and $metaldelta $stmetal[proper].')");
    db_op_result($query,__LINE__,__FILE__);
    $query = $db->Execute("DELETE FROM $dbtables[activities] "
                ."WHERE tribeid = '$tribe[tribeid]' "
                ."AND product = '$act_do[product]' "
                ."AND skill_abbr = 'wpn'");
   db_op_result($query,__LINE__,__FILE__);
}


if( $act_do[product] == 'horsebow' )
{
        $hex = $db->Execute("SELECT * FROM $dbtables[hexes] "
                           ."WHERE hex_id = '$tribe[hex_id]'");
        $hexinfo = $hex->fields;

        if( $hexinfo[terrain] == 'df' | $hexinfo[terrain] == 'jg' | $hexinfo[terrain] == 'dh' | $hexinfo[terrain] == 'jh' )
        {
            $st = $db->Execute("SELECT * FROM $dbtables[products] "
                              ."WHERE tribeid = '$tribe[goods_tribe]' "
                              ."AND proper = 'Strings'");
             db_op_result($st,__LINE__,__FILE__);
            $string = $st->fields;
            $bows = 0;
            $startstring = $string[amount];
            while( $act_do[actives] > 1 && $string[amount] > 0 )
            {
                $string[amount] -= 1;
                $act_do[actives] -= 2;
                $bows += 1;
            }
            $deltastring = $startstring - $string[amount];
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount - $deltastring "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND proper = 'Strings'");
            db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + $bows "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'horsebow'");
             db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("DELETE FROM $dbtables[activities] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND product = '$act_do[product]'");
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
                        ."'Weaponmaking: $bows Horsebows made using $deltastring strings in the forest.')");
              db_op_result($query,__LINE__,__FILE__);
        }
        else
        {
            $st = $db->Execute("SELECT * FROM $dbtables[products] "
                              ."WHERE tribeid = '$tribe[goods_tribe]' "
                              ."AND proper = 'Strings'");
               db_op_result($st,__LINE__,__FILE__);
            $string = $st->fields;
            $stave = $db->Execute("SELECT * FROM $dbtables[products] "
                                 ."WHERE proper = 'Staves' "
                                 ."AND tribeid = '$tribe[goods_tribe]'");
                db_op_result($stave,__LINE__,__FILE__);
            $staveinfo = $stave->fields;
            $startstring = $string[amount];
            $startstave = $staveinfo[amount];
            $bows = 0;
            while( $act_do[actives] > 0 && $string[amount] > 0 && $staveinfo[amount] > 0 )
            {
                $string[amount] -= 1;
                $act_do[actives] -= 1;
                $bows += 1;
                $staveinfo[amount] -= 1;
            }
            $deltastring = $startstring - $string[amount];
            $deltastave = $startstave - $staveinfo[amount];
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount - $deltastring "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND proper = 'Strings'");
            db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount + $bows "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND long_name = 'horsebow'");
            db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("DELETE FROM $dbtables[activities] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND product = '$act_do[product]'");
            db_op_result($query,__LINE__,__FILE__);
            $query = $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount - $deltastave "
                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                        ."AND proper = 'Staves'");
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
                        ."'Weaponmaking: $bows Horsebows made using $deltastring strings and $deltastave staves.')");
            db_op_result($query,__LINE__,__FILE__);
        }
        $query = $db->Execute("DELETE FROM $dbtables[activities] "
                    ."WHERE tribeid = '$tribe[tribeid]' "
                    ."AND product = '$act_do[product]'");
           db_op_result($query,__LINE__,__FILE__);
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
