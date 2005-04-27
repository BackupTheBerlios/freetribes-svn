<?
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: convert.php

session_start();
header("Cache-control: private");

include("config.php");

page_header("Admin DB - Database Conversion");

connectdb();

$username = $_SESSION['username'];
$admin = $db->Execute("SELECT * FROM $dbtables[chiefs] WHERE username = '$username'");
$admininfo = $admin->fields;


$module = $_REQUEST[menu];

if(!$admininfo[admin] >= $privilege['adm_tables']){
echo "You must be an administrator to use this tool.<BR>\n";
page_footer();
}

$menu = 27;

    if( $map_width )
    {
        echo "$map_width<BR>";
        echo "Creating a test mapping table with " . ($map_width * $map_width) . " hexes.<BR>";
        $sql = "CREATE TABLE $dbtables[mapping_test] (";
        $sql .= "`clanid` int(4) unsigned zerofill NOT NULL default '0000',";
        $i = 1;
        $j = 1;
        echo "Generating SQL query.";
        while( $i <= ($map_width * $map_width))
        {
            $sql .= "`hex_id_$i` set('0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F') NOT NULL default '0',";
            if( $i == $j )
            {
                echo ".";
                $j += 500;
            }
            $i++;
        }
        $sql .= "UNIQUE KEY `clanid` (`clanid`)) TYPE=MyISAM";
        echo "complete!";
        echo "<BR>";
        echo "<PRE>";
        echo $sql;
        echo "</PRE>";
        $db->Execute("$sql");
        echo $db->ErrorMsg() . "<BR>";
        echo "<A HREF=admin.php>Click Here</A> to return to the admin page.<BR>";
    }    


    if( $menu == 26 )
    {
        $old = 'dump.txt';
        $new = 'schema.sql';
        copy($old, $new);
        $res = $db->Execute("SHOW TABLES");
        while( !$res->EOF )
        {
            $table = $res->fields;
            if( $table[Tables_in_tribe] == 'logs' | $table[Tables_in_tribe] == 'tribes' | $table[Tables_in_tribe] == 'products' )
            {
                $sql =  "mysqldump -d ";
                $sql .= $dbname;
                $sql .= " -u ";
                $sql .= $dbuname;
                $sql .= " ";
                $sql .= $table[Tables_in_tribe];
                $sql .= " >> ";
                $sql .= $gameroot;
                $sql .= "schema.sql";
                exec("$sql");
                echo $sql . "<BR>";
            }
            elseif( $table[Tables_in_tribe] == 'activities' )
            {
                $sql =  "mysqldump -d ";
                $sql .= $dbname;
                $sql .= " -u ";
                $sql .= $dbuname;
                $sql .= " ";
                $sql .= $table[Tables_in_tribe];
                $sql .= " >> ";
                $sql .= $gameroot;
                $sql .= "schema.sql";
                exec("$sql");
                echo $sql . "<BR>";
            }
            elseif( $table[Tables_in_tribe] == 'alliances' )
            {
                $sql =  "mysqldump -d ";
                $sql .= $dbname;
                $sql .= " -u ";
                $sql .= $dbuname;
                $sql .= " ";
                $sql .= $table[Tables_in_tribe];
                $sql .= " >> ";
                $sql .= $gameroot;
                $sql .= "schema.sql";
                exec("$sql");
                echo $sql . "<BR>";
            }
            elseif( $table[Tables_in_tribe] == 'chiefs' )
            {
                $sql =  "mysqldump -d ";
                $sql .= $dbname;
                $sql .= " -u ";
                $sql .= $dbuname;
                $sql .= " ";
                $sql .= $table[Tables_in_tribe];
                $sql .= " >> ";
                $sql .= $gameroot;
                $sql .= "schema.sql";
                exec("$sql");
                echo $sql . "<BR>";
            }
            elseif( $table[Tables_in_tribe] == 'clans' )
            {
                $sql =  "mysqldump -d ";
                $sql .= $dbname;
                $sql .= " -u ";
                $sql .= $dbuname;
                $sql .= " ";
                $sql .= $table[Tables_in_tribe];
                $sql .= " >> ";
                $sql .= $gameroot;
                $sql .= "schema.sql";
                exec("$sql");
                echo $sql . "<BR>";
            }
            elseif( $table[Tables_in_tribe] == 'combats' )
            {
                $sql =  "mysqldump -d ";
                $sql .= $dbname;
                $sql .= " -u ";
                $sql .= $dbuname;
                $sql .= " ";
                $sql .= $table[Tables_in_tribe];
                $sql .= " >> ";
                $sql .= $gameroot;
                $sql .= "schema.sql";
                exec("$sql");
                echo $sql . "<BR>";
            }
            elseif( $table[Tables_in_tribe] == 'fair_tribe' )
            {
                $sql =  "mysqldump -d ";
                $sql .= $dbname;
                $sql .= " -u ";
                $sql .= $dbuname;
                $sql .= " ";
                $sql .= $table[Tables_in_tribe];
                $sql .= " >> ";
                $sql .= $gameroot;
                $sql .= "schema.sql";
                exec("$sql");
                echo $sql . "<BR>";
            }
            elseif( $table[Tables_in_tribe] == 'farm_activities' )
            {
                $sql =  "mysqldump -d ";
                $sql .= $dbname;
                $sql .= " -u ";
                $sql .= $dbuname;
                $sql .= " ";
                $sql .= $table[Tables_in_tribe];
                $sql .= " >> ";
                $sql .= $gameroot;
                $sql .= "schema.sql";
                exec("$sql");
                echo $sql . "<BR>";
            }
            elseif( $table[Tables_in_tribe] == 'farming' )
            {
                $sql =  "mysqldump -d ";
                $sql .= $dbname;
                $sql .= " -u ";
                $sql .= $dbuname;
                $sql .= " ";
                $sql .= $table[Tables_in_tribe];
                $sql .= " >> ";
                $sql .= $gameroot;
                $sql .= "schema.sql";
                exec("$sql");
                echo $sql . "<BR>";
            }
            elseif( $table[Tables_in_tribe] == 'garrisons' )
            {
                $sql =  "mysqldump -d ";
                $sql .= $dbname;
                $sql .= " -u ";
                $sql .= $dbuname;
                $sql .= " ";
                $sql .= $table[Tables_in_tribe];
                $sql .= " >> ";
                $sql .= $gameroot;
                $sql .= "schema.sql";
                exec("$sql");
                echo $sql . "<BR>";
            }
            elseif( $table[Tables_in_tribe] == 'hexes' )
            {
                $sql =  "mysqldump -d ";
                $sql .= $dbname;
                $sql .= " -u ";
                $sql .= $dbuname;
                $sql .= " ";
                $sql .= $table[Tables_in_tribe];
                $sql .= " >> ";
                $sql .= $gameroot;
                $sql .= "schema.sql";
                exec("$sql");
                echo $sql . "<BR>";
            }
            elseif( $table[Tables_in_tribe] == 'last_turn' )
            {
                $sql =  "mysqldump -d ";
                $sql .= $dbname;
                $sql .= " -u ";
                $sql .= $dbuname;
                $sql .= " ";
                $sql .= $table[Tables_in_tribe];
                $sql .= " >> ";
                $sql .= $gameroot;
                $sql .= "schema.sql";
                exec("$sql");
                echo $sql . "<BR>";
            }
            elseif( $table[Tables_in_tribe] == 'livestock' )
            {
                $sql =  "mysqldump -d ";
                $sql .= $dbname;
                $sql .= " -u ";
                $sql .= $dbuname;
                $sql .= " ";
                $sql .= $table[Tables_in_tribe];
                $sql .= " >> ";
                $sql .= $gameroot;
                $sql .= "schema.sql";
                exec("$sql");
                echo $sql . "<BR>";
            }
            elseif( $table[Tables_in_tribe] == 'map_view' )
            {
                $sql =  "mysqldump -d ";
                $sql .= $dbname;
                $sql .= " -u ";
                $sql .= $dbuname;
                $sql .= " ";
                $sql .= $table[Tables_in_tribe];
                $sql .= " >> ";
                $sql .= $gameroot;
                $sql .= "schema.sql";
                exec("$sql");
                echo $sql . "<BR>";
            }
            elseif( $table[Tables_in_tribe] == 'mapping' )
            {
                $sql =  "mysqldump -d ";
                $sql .= $dbname;
                $sql .= " -u ";
                $sql .= $dbuname;
                $sql .= " ";
                $sql .= $table[Tables_in_tribe];
                $sql .= " >> ";
                $sql .= $gameroot;
                $sql .= "schema.sql";
                exec("$sql");
                echo $sql . "<BR>";
            }
            elseif( $table[Tables_in_tribe] == 'messages' )
            {
                $sql =  "mysqldump -d ";
                $sql .= $dbname;
                $sql .= " -u ";
                $sql .= $dbuname;
                $sql .= " ";
                $sql .= $table[Tables_in_tribe];
                $sql .= " >> ";
                $sql .= $gameroot;
                $sql .= "schema.sql";
                exec("$sql");
                echo $sql . "<BR>";
            }
            elseif( $table[Tables_in_tribe] == 'outbox' )
            {
                $sql =  "mysqldump -d ";
                $sql .= $dbname;
                $sql .= " -u ";
                $sql .= $dbuname;
                $sql .= " ";
                $sql .= $table[Tables_in_tribe];
                $sql .= " >> ";
                $sql .= $gameroot;
                $sql .= "schema.sql";
                exec("$sql");
                echo $sql . "<BR>";
            }
            elseif( $table[Tables_in_tribe] == 'poptrans' )
            {
                $sql =  "mysqldump -d ";
                $sql .= $dbname;
                $sql .= " -u ";
                $sql .= $dbuname;
                $sql .= " ";
                $sql .= $table[Tables_in_tribe];
                $sql .= " >> ";
                $sql .= $gameroot;
                $sql .= "schema.sql";
                exec("$sql");
                echo $sql . "<BR>";
            }
            elseif( $table[Tables_in_tribe] == 'religions' )
            {
                $sql =  "mysqldump -d ";
                $sql .= $dbname;
                $sql .= " -u ";
                $sql .= $dbuname;
                $sql .= " ";
                $sql .= $table[Tables_in_tribe];
                $sql .= " >> ";
                $sql .= $gameroot;
                $sql .= "schema.sql";
                exec("$sql");
                echo $sql . "<BR>";
            }
            elseif( $table[Tables_in_tribe] == 'resources' )
            {
                $sql =  "mysqldump -d ";
                $sql .= $dbname;
                $sql .= " -u ";
                $sql .= $dbuname;
                $sql .= " ";
                $sql .= $table[Tables_in_tribe];
                $sql .= " >> ";
                $sql .= $gameroot;
                $sql .= "schema.sql";
                exec("$sql");
                echo $sql . "<BR>";
            }
            elseif( $table[Tables_in_tribe] == 'scouts' )
            {
                $sql =  "mysqldump -d ";
                $sql .= $dbname;
                $sql .= " -u ";
                $sql .= $dbuname;
                $sql .= " ";
                $sql .= $table[Tables_in_tribe];
                $sql .= " >> ";
                $sql .= $gameroot;
                $sql .= "schema.sql";
                exec("$sql");
                echo $sql . "<BR>";
            }
            elseif( $table[Tables_in_tribe] == 'seeking' )
            {
                $sql =  "mysqldump -d ";
                $sql .= $dbname;
                $sql .= " -u ";
                $sql .= $dbuname;
                $sql .= " ";
                $sql .= $table[Tables_in_tribe];
                $sql .= " >> ";
                $sql .= $gameroot;
                $sql .= "schema.sql";
                exec("$sql");
                echo $sql . "<BR>";
            }
            elseif( $table[Tables_in_tribe] == 'skills' )
            {
                $sql =  "mysqldump -d ";
                $sql .= $dbname;
                $sql .= " -u ";
                $sql .= $dbuname;
                $sql .= " ";
                $sql .= $table[Tables_in_tribe];
                $sql .= " >> ";
                $sql .= $gameroot;
                $sql .= "schema.sql";
                exec("$sql");
                echo $sql . "<BR>";
            }
            elseif( $table[Tables_in_tribe] == 'structures' )
            {
                $sql =  "mysqldump -d ";
                $sql .= $dbname;
                $sql .= " -u ";
                $sql .= $dbuname;
                $sql .= " ";
                $sql .= $table[Tables_in_tribe];
                $sql .= " >> ";
                $sql .= $gameroot;
                $sql .= "schema.sql";
                exec("$sql");
                echo $sql . "<BR>";
            }
            elseif( $table[Tables_in_tribe] == 'subtribe_id' )
            {
                $sql =  "mysqldump -d ";
                $sql .= $dbname;
                $sql .= " -u ";
                $sql .= $dbuname;
                $sql .= " ";
                $sql .= $table[Tables_in_tribe];
                $sql .= " >> ";
                $sql .= $gameroot;
                $sql .= "schema.sql";
                exec("$sql");
                echo $sql . "<BR>";
            }
            else
            {
                $sql = "mysqldump -c ";
                $sql .= $dbname;
                $sql .= " -u ";
                $sql .= $dbuname;
                $sql .= " ";
                $sql .= $table[Tables_in_tribe];
                $sql .= " >> ";
                $sql .= $gameroot;
                $sql .= "schema.sql";
                exec("$sql");
                echo $sql . "<BR>";
            }
            $res->MoveNext();
            if( $res->EOF )
            {
                echo "DONE!<BR>";
                echo "<A HREF=admin.php>Click Here</A> to return to the admin page.<BR>";
            }
        }
    }
    if( $menu == 25 )
    {
        $res = $db->Execute("SELECT distinct(combat_id), tribeid FROM $dbtables[combats] "
                           ."WHERE tribeid like '$_SESSION[clanid]%'");
        if( $res )
        {
            $echo = $res->_numOfRows;
            echo "$echo <BR>";
            
        }
        if( !$res )
        {
            echo "<PRE>";
            print_r($db);
            print_r($res);
            echo "</PRE>";
        }

    }
    if( $menu == 24)
    {
        $rel = $db->Execute("SELECT distinct type, amount, archetype FROM $dbtables[religion_archetype] "
                           ."WHERE archetype = 'Animism' "
                           ."ORDER BY amount DESC "
                           ."LIMIT 23");
        while( !$rel->EOF )
        {
            $religion = $rel->fields;
            $newamount = $religion[amount] + .01;
            $db->Execute("INSERT INTO $dbtables[religion_archetype] "
                        ."VALUES("
                        ."'$religion[type]',"
                        ."'$newamount',"
                        ."'$religion[archetype]')");
            echo "Inserted $newamount bonus for $religion[type].<BR>"; 
            $rel->MoveNext();
            if( $rel->EOF )
            {
                echo "Done!<BR>";
                TEXT_GOTOMAIN();
            }
        }
    }
    if( $menu == 23)
    {
        $chck = $db->Execute("SELECT COUNT(DISTINCT long_name) AS count FROM $dbtables[products]");
        $check = $chck->fields;
        $res = $db->Execute("SELECT * FROM $dbtables[tribes]");
        while(!$res->EOF)
        {
            $tribe = $res->fields;
            $prod = $db->Execute("SELECT COUNT(long_name) AS count FROM $dbtables[products] "
                                ."WHERE tribeid = '$tribe[tribeid]'");
            $prodcount = $prod->fields;
            if( $check[count] > $prodcount[count] | $check[count] < $prodcount[count] )
            {
                echo "$tribe[tribeid] has a descrepancy.<BR>";
            }
            $res->MoveNext();
            if( $res->EOF )
            {
                echo "Done!<BR>";
                TEXT_GOTOMAIN();
            }
        }
    }

    if( $menu == 22)
    {
        $chck = $db->Execute("SELECT COUNT(DISTINCT long_name) AS count FROM $dbtables[products]");
        $check = $chck->fields;
        $res = $db->Execute("SELECT * FROM $dbtables[tribes]");
        while( !$res->EOF )
        {
            $tribe = $res->fields;
            $chck = $db->Execute("SELECT * FROM $dbtables[product_table] "
                                ."WHERE include = 'N' "
                                ."AND skill_abbr = 'eng'");
            $check = $chck->fields;
            while( !$chck->EOF )
            {
                $check = $chck->fields;
                $prod = $db->Execute("SELECT COUNT[long_name] AS count FROM $dbtables[products] "
                                    ."WHERE long_name = '$check[long_name]' "
                                    ."AND tribeid = '$tribe[tribeid]'");
                if( !$prod->EOF )
                {
                    echo "$tribe[tribeid] has a $check[long_name].<BR>";
                    $db->Execute("SELECT * FROM $dbtables[products] "
                                ."WHERE long_name = '$check[long_name]' "
                                ."AND tribeid = '$tribe[tribeid]'");
                    echo "$check[long_name] removed from $tribe[tribeid].<BR>";
                }
                $chck->MoveNext();
            }
            $res->MoveNext();
            if( $res->EOF )
            {
                echo "Done!<BR>";
                TEXT_GOTOMAIN();
            }
        }
    }

    if( $menu == 21)
    {
        $res = $db->Execute("SELECT * FROM $dbtables[tribes]");
        while( !$res->EOF )
        {
            $tribe = $res->fields;
            $prod = $db->Execute("SELECT * FROM $dbtables[product_table] "
                                ."WHERE include = 'Y'");
            while( !$prod->EOF )
            {
                $product = $prod->fields;
                $trpd = $db->Execute("SELECT * FROM $dbtables[products] "
                                    ."WHERE long_name = '$product[long_name]' "
                                    ."AND tribeid = '$tribe[tribeid]'");
                if( $trpd->EOF )
                {
                    $db->Execute("INSERT INTO $dbtables[products] "
                                ."VALUES("
                                ."'$tribe[tribeid]',"
                                ."'$product[proper]',"
                                ."'$product[long_name]',"
                                ."'0',"
                                ."'$product[weapon]',"
                                ."'$product[armor]')");
                    echo "$product[proper] added to $tribe[tribeid] inventory.<BR>";
                }
                $prod->MoveNext();
            }
            $res->MoveNext();
            if( $res->EOF )
            {
                echo "Done.<BR>";
                TEXT_GOTOMAIN();
            }
        }
    }
               




    if($menu == 20)
    {
       $res = $db->Execute("SELECT * FROM $dbtables[tribes]");
       while(!$res->EOF)
       {
           $tribe = $res->fields;
           $prodct = $db->Execute("SELECT COUNT(*) AS count FROM $dbtables[products] "
                                ."WHERE tribeid = '$tribe[tribeid]'");
           $products = $prodct->fields;
           $prodct = $db->Execute("SELECT COUNT(DISTINCT long_name) AS count FROM $dbtables[products] "
                                ."WHERE tribeid = '$tribe[tribeid]'");
           $realcheck = $prodct->fields;
           if($realcheck[count] <> $products[count])
           {
               echo '<FONT COLOR=RED>';
               $full = $db->Execute("SELECT * FROM $dbtables[products] "
                                   ."WHERE tribeid = '$tribe[tribeid]'");
               while(!$full->EOF)
               {
                   $fullinfo = $full->fields;
                   $db->Execute("DELETE FROM $dbtables[products] "
                               ."WHERE tribeid = '$tribe[tribeid]' "
                               ."AND long_name = '$fullinfo[long_name]' "
                               ."LIMIT 1");
                   $full->MoveNext();
               }
           }
           else
           {
           echo '<FONT COLOR=WHITE>';
           }
           echo "$tribe[tribeid] has $products[count] entries, and $realcheck[count] distinct entries.<BR></FONT>";
           if( $products[count] > $realcheck[count] )
           {
               $prodct = $db->Execute("SELECT COUNT(*) AS count FROM $dbtables[products] "
                                     ."WHERE tribeid = '$tribe[tribeid]'");
               $products2 = $prodct->fields;
               $prodct = $db->Execute("SELECT COUNT(DISTINCT long_name) AS count FROM $dbtables[products] "
                                     ."WHERE tribeid = '$tribe[tribeid]'");
               $realcheck2 = $prodct->fields;
               echo "$tribe[tribeid] now has $products2[count] entries, and $realcheck2[count] distinct entries.<BR>";
           }
           $res->MoveNext();
           if($res->EOF)
           {
           TEXT_GOTOMAIN();
           }
       }
    } 

    if($menu == 19)
    {
       $res = $db->Execute("SELECT * FROM $dbtables[smelters]");
       while(!$res->EOF){
       $row = $res->fields;
       $db->Execute("UPDATE $dbtables[structures] "
                   ."SET number = '$row[number]', "
                   ."subunit = 'smelters' "
                   ."WHERE struct_id = '$row[refinery_id]'");
       echo "Added $row[number] smelters to refinery $row[refinery_id] located at $row[hex_id] for $row[tribeid].<BR>";
       $res->MoveNext();
       }
       echo "Done!<BR>";
       TEXT_GOTOMAIN();
     }


    if( $menu == 18 )
    {
      $i = 1;
       while($i < 37501){
       $db->Execute("INSERT INTO $dbtables[mapping] VALUES('','N')");
       $i++;
       }
    page_footer();
    }
       






    if( $menu == 17 )
    {
        $res = $db->Execute("SELECT * FROM $dbtables[tribes]");

        while( !$res->EOF )
        {
            $tribe = $res->fields;
            $here = $db->Execute("SELECT * FROM $dbtables[resources] "
                                ."WHERE long_name = 'Copper' "
                                ."AND tribeid = '$tribe[tribeid]'");

            if( $here->EOF )
            {
                $db->Execute("INSERT INTO $dbtables[resources] "
                            ."VALUES("
                            ."'$tribe[tribeid]',"
                            ."'Copper',"
                            ."'0',"
                            ."'copper',"
                            .")");
                echo "Tribe $tribe[tribeid] needed Copper inserted.<BR>";
                $res->MoveNext();
            }
            else
            {
                echo "Tribe $tribe[tribeid] was okay.<BR>";
                $res->MoveNext();
            }
        }
    }        


   if($menu == 16){
     $tr = $db->Execute("SELECT * FROM $dbtables[chiefs]");
     while(!$tr->EOF){
     $tribe = $tr->fields;
     echo "Populating the hexes mapped by $tribe[clanid].<BR>";
     $res = $db->Execute("SELECT distinct hex_id, clanid FROM $dbtables[movement_log] where clanid = '$tribe[clanid]'");
     $bef = $db->Execute("SELECT COUNT(*) AS count FROM $dbtables[mapping] WHERE `$tribe[clanid]` = 'Y'");
     $before = $bef->fields;
     while(!$res->EOF){
     $row = $res->fields;
     $db->Execute("UPDATE $dbtables[mapping] SET `$tribe[clanid]` = 'Y' WHERE hex_id = '$row[hex_id]'");
     $res->MoveNext();
     }
     $movect = $db->Execute("SELECT COUNT(distinct hex_id) as count FROM $dbtables[movement_log] WHERE clanid = '$tribe[clanid]'");
     $move = $movect->fields;
     echo "$move[count] hexes mapped in movement_log from $tribe[clanid].<BR>";
     $tr->MoveNext();
     }
     $mapct = $db->Execute("SELECT COUNT(*) as count FROM $dbtables[mapping] WHERE `$tribe[clanid]` = 'Y'");
     $map = $mapct->fields;
     $added = $map[count] - $before[count];
     echo "Added $added hexes as mapped for $tribe[clanid].<BR>";
     }
     
     

   if($menu == 15){
     $res = $db->Execute("SELECT * FROM $dbtables[tribes]");
     while(!$res->EOF){
     $tribe = $res->fields;
     $activepop = $tribe[activepop] + $tribe[inactivepop];
     $inactivepop = $activepop * .35;
     $activepop = $activepop - $inactivepop;
     $db->Execute("UPDATE $dbtables[tribes] SET activepop = '$activepop', inactivepop = '$inactivepop' WHERE tribeid = '$tribe[tribeid]' AND clanid = '$tribe[clanid]'");
     echo "$tribe[tribeid] went from $tribe[activepop] to $activepop actives, and $tribe[inactivepop] to $inactivepop inactives.<BR>";
     $res->MoveNext();
     }
     }

   if($menu == 14){
     $res = $db->Execute("SELECT * FROM $dbtables[tribes]");
     while(!$res->EOF){
     $tribe = $res->fields;
     $i = 0;
     $map = $db->Execute("SELECT distinct hex_id, tribeid, clanid, time FROM $dbtables[map_table] WHERE clanid = '$tribe[clanid]'");
     while(!$map->EOF){
     $mapinfo = $map->fields;
     $here = $db->Execute("SELECT * FROM $dbtables[movement_log] WHERE hex_id = $mapinfo[hex_id] AND clanid = '$mapinfo[clanid]'");
     if($here->EOF){
     $db->Execute("INSERT INTO $dbtables[movement_log] VALUES('','$mapinfo[tribeid]','$mapinfo[clanid]','$mapinfo[hex_id]','$mapinfo[time]')");
     $i++;
     }
     $map->MoveNext();
     }
     echo "$i map tiles transferred for $tribe[clanid].<BR>";
     $res->MoveNext();
     }
     echo "Done!<BR>";
     TEXT_GOTOMAIN();
     }

  if($menu == 13){
     $chief = $db->Execute("SELECT * FROM $dbtables[chiefs]");
     $stamp = date("Y-m-d H:i:s");
     while(!$chief->EOF){
     $chiefinfo = $chief->fields;
     $move = $db->Execute("SELECT distinct hex_id, clanid FROM $dbtables[movement_log] WHERE clanid = $chiefinfo[clanid]");
     $db->Execute("DELETE FROM $dbtables[movement_log] WHERE clanid = '$chiefinfo[clanid]'");
     $db->Execute("INSERT INTO $dbtables[movement_log] VALUES('','$chiefinfo[clanid].00','$claninfo[clanid]','$moveinfo[hex_id]','$stamp')");
     $map = $db->Execute("SELECT distinct hex_id, clanid FROM $dbtables[map_table] WHERE clanid = $chiefinfo[clanid]");
     $db->Execute("DELETE FROM $dbtables[map_table] WHERE clanid = '$chiefinfo[clanid]");
     while(!$map->EOF){
     $mapinfo = $map->fields;
     $db->Execute("INSERT INTO $dbtables[map_table] VALUES('','$chiefinfo[clanid].00','$chiefinfo[clanid]','$mapinfo[hex_id]','$stamp','$chiefinfo[clanid].00')");
     $db->Execute("INSERT INTO $dbtables[movement_log] VALUES('','$chiefinfo[clanid].00','$chiefinfo[clanid]','$mapinfo[hex_id]','$stamp')");

     $map->MoveNext();
     }
     echo "$chiefinfo[clanid] map information has been pruned.<BR>";
     $chief->MoveNext();
     }
     echo "Done!<BR>";
     }
     

  if($menu == 12){
     $chief = $db->Execute("SELECT * FROM $dbtables[chiefs]");
     while(!$chief->EOF){
     $chiefinfo = $chief->fields;
     $checkpass1 = md5($chiefinfo[chiefname]);
     $checkpass2 = md5($chiefinfo[username]);
     $check = $db->Execute("SELECT * FROM $dbtables[chiefs] WHERE password = '$chiefinfo[password]' AND clanid <> '$chiefinfo[clanid]'");
     while(!$check->EOF){
     $checkinfo = $check->fields;
     echo "$chiefinfo[clanid] $chiefinfo[chiefname] $chiefinfo[username] is a possible dual player with $checkinfo[clanid] $checkinfo[chiefname] $checkinfo[username]<BR>";
     $check->MoveNext();
     }
     $check2 = $db->Execute("SELECT * FROM $dbtables[chiefs] WHERE password = '$checkpass1'");
     while(!$check2->EOF){
     $checkinfo2 = $check2->fields;
     echo "$chiefinfo[clanid] $chiefinfo[username] seems to be using $chiefinfo[chiefname]($checkinfo2[password]) as the password.<BR>";
     $check2->MoveNext();
     }
     $check3 = $db->Execute("SELECT * FROM $dbtables[chiefs] WHERE password = '$checkpass2'");
     while(!$check3->EOF){
     $checkinfo3 = $check3->fields;
     echo "$chiefinfo[clanid] $chiefinfo[username] seems to be using $chiefinfo[username] ($checkinfo3[password]) as the password.<BR>";
     $check3->MoveNext();
     }
     $chief->MoveNext();
     }
     }


  if($menu == 11){
     $tribe = $db->Execute("SELECT * FROM $dbtables[tribes]");
    while(!$tribe->EOF){
      $tribeinfo = $tribe->fields;
      $isthere = $db->Execute("SELECT * FROM $dbtables[products] where proper = 'Provisions' AND tribeid = '$tribeinfo[tribeid]'");
      if($isthere->EOF){
      $rand = rand(8000, ($tribeinfo[totalpop] * 3));
      $db->Execute("INSERT INTO $dbtables[products] VALUES('$tribeinfo[tribeid]','Provisions','provs','$rand','N','N')");
      }
     $tribe->MoveNext();
     }
     }
     
  if($menu == 10){
     $chief = $db->Execute("SELECT * FROM $dbtables[chiefs]");
     while(!$chief->EOF){
     $chiefinfo = $chief->fields;
     $oldscore = $chiefinfo[score];
     $chiefinfo[score] = round($chiefinfo[score]/1000);
     $db->Execute("UPDATE $dbtables[chiefs] SET score = '$chiefinfo[score]' WHERE clanid = '$chiefinfo[clanid]'");
     echo "Updated $chiefinfo[clanid] ($chiefinfo[chiefname]) from $oldscore to $chiefinfo[score].<BR>";
     $chief->MoveNext();
     }
     echo "DONE!<BR>";
     }
    

  if($menu == 1)
    {
    $armor = $db->Execute("SELECT * FROM $dbtables[armor]");
    $count = 0;
    while(!$armor->EOF){
    $armorinfo = $armor->fields;
    $prod = $db->Execute("SELECT * FROM $dbtables[product_table] WHERE proper = '$armorinfo[proper]' AND armor = 'Y' AND long_name = '$armorinfo[long_name]'");
    $prodinfo = $prod->fields;
    if(!ISSET($prodinfo[proper])){
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES('','$armorinfo[long_name]','$armorinfo[proper]','N','Y','aaa','99')");
	echo "Entered $armorinfo[long_name] into Database.<BR>";
        $count++;
    }
    $prod = array();
    $armor->MoveNext();
    }
    echo "<CENTER> DONE! ($count items)</CENTER>";
    }    

  if($menu == 2)
   {
   $prod = $db->Execute("SELECT * FROM $dbtables[product_table]");
   while(!$prod->EOF){
   $prodinfo = $prod->fields;
   $fair = $db->Execute("SELECT * FROM $dbtables[fair] WHERE proper_name = '$prodinfo[proper]' AND abbr = '$prodinfo[long_name]'");
   $fairinfo = $fair->fields;
   if(!ISSET($fairinfo[proper_name])){
   $db->Execute("INSERT INTO $dbtables[fair] VALUES('','$prodinfo[proper]','$prodinfo[long_name]','0','0','0','0','N','0')");
      echo "Entered $prodinfo[proper] into Fair Database.<BR>";
   }
   $fair = array();
   $prod->MoveNext();
   }
   echo "<CENTER> DONE! </CENTER>";
   }

  if($menu == 3){
   $prod = $db->Execute("SELECT * FROM $dbtables[product_table] ORDER BY proper");
   while(!$prod->EOF){
   $correct = 0;
   $prodinfo = $prod->fields;
   $liv = $db->Execute("SELECT * FROM $dbtables[livestock] WHERE type = '$prodinfo[proper]'");
   $livinfo = $liv->fields;
   if(ISSET($livinfo[type])){
   echo "<CENTER>$livinfo[type] is correct.<BR></CENTER>";
   $correct++;
   }
   $res = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = '$prodinfo[proper]'");
   $resinfo = $res->fields;
   if(ISSET($resinfo[long_name])){
   echo "<CENTER>$resinfo[long_name] is correct.<BR></CENTER>";
   $correct++;
   }
   $prdt = $db->Execute("SELECT * FROM $dbtables[products] WHERE proper = '$prodinfo[proper]'");
   $prdtinfo = $prdt->fields;
   if(ISSET($prdtinfo[proper])){
   echo "<CENTER>$prdtinfo[proper] is correct.<BR></CENTER>";
   $correct++;
   }
   if($correct == '0'){
   echo "<CENTER>$prodinfo[proper] needs to be CHANGED!</CENTER>";
    }
   $prod->MoveNext();
   }
   echo "<CENTER>DONE!!!</CENTER>";
   }
 
  if($menu == 4){
   $fair = $db->Execute("SELECT * FROM $dbtables[fair] ORDER BY proper_name");
   while(!$fair->EOF){
   $prod = array();
   $liv = array();
   $res = array();
   $fairinfo = array();
   $livinfo = array();
   $prodinfo = array();
   $resinfo = array();
   $fairinfo = $fair->fields;
   $prod = $db->Execute("SELECT * FROM $dbtables[product_table] WHERE proper = '$fairinfo[proper_name]'");
   $prodinfo = $prod->fields;
   $liv = $db->Execute("SELECT * FROM $dbtables[livestock] WHERE type = '$fairinfo[proper_name]'");
   $livinfo = $liv->fields;
   $res = $db->Execute("SELECT * FROM $dbtables[resources] WHERE long_name = '$fairinfo[proper_name]'");
   $resinfo = $res->fields;
   if($prodinfo[proper] == '' & $livinfo[type] == '' & $resinfo[long_name] == ''){
   $db->Execute("INSERT INTO $dbtables[product_table] VALUES('','$fairinfo[abbr]','$fairinfo[proper_name]','N','N','FAIR','99','')");
   echo "<CENTER> Iserting $fairinfo[abbr].</CENTER><BR>";
   }
   $fair->MoveNext();
   }
   echo "<CENTER>Done!!</CENTER>";
   }


  if($menu == 5){
   $targ = $db->Execute("SELECT * FROM $dbtables[tribes]");
   while(!$targ->EOF){
   $targinfo = $targ->fields;
   $prod = $db->Execute("SELECT distinct proper, long_name, weapon, armor FROM $dbtables[product_table]"); 
   while(!$prod->EOF){
   $prodinfo = $prod->fields;
   $db->Execute("INSERT INTO $dbtables[products] VALUES('$targinfo[tribeid]','$prodinfo[proper]','$prodinfo[long_name]','0','$prodinfo[weapon]','$prodinfo[armor]')");
   $prod->MoveNext();
   }
   $targ->MoveNext();
   }
   }


   if($menu == 6){
   $weap = $db->Execute("SELECT * FROM $dbtables[weapons] ORDER BY proper");
   echo "<CENTER><TABLE BORDER=0 WIDTH=\"100%\"><TR BGCOLOR=$color_header><TD>Matched</TD><TD>Unmatched</TD></TR>";
   $line_col = $color_line1;
   while(!$weap->EOF){
   $weapinfo = $weap->fields;
   $check = $db->Execute("SELECT * FROM $dbtables[product_table] WHERE proper = '$weapinfo[proper]'");
   $checkinfo = $check->fields;
   if(!$checkinfo[proper] == ''){
   echo "<TR BGCOLOR=$line_col><TD>$weapinfo[proper]</TD><TD>&nbsp;</TD></TR>";
   }
   else{
   echo "<TR BGCOLOR=$line_col><TD>&nbsp;</TD><TD>$weapinfo[proper]</TD></TR>";
   $db->Execute("INSERT INTO $dbtables[product_table] VALUES('','$weapinfo[dbname]','$weapinfo[proper]','Y','N','XXX','99','')");
   }
   $weap->MoveNext();
   if($line_col == $color_line1){
   $line_col = $color_line2;
   }
   else{
   $line_col = $color_line1;
   }
   }
   echo "</TABLE>";
   echo "DONE!!</CENTER>";
   }


  if($menu == 7){
   $prod = array();
   $prod = $db->Execute("SELECT distinct long_name FROM $dbtables[product_table]");
   while(!$prod->EOF){
     $prodinfo = $prod->fields;
     $db->Execute("UPDATE $dbtables[product_table] set include = 'Y' WHERE long_name = '$prodinfo[long_name]' limit 1");   
     echo "Updated $prodinfo[long_name]<BR>";
     $prod->MoveNext();
     }
     echo "DONE!!";
   }

  if($menu == 8){
   $prod = array();
   $prod = $db->Execute("SELECT * from $dbtables[product_table]");
   while(!$prod->EOF){
    $prodinfo = $prod->fields;
    $db->Execute("INSERT INTO $dbtables[inventory] VALUES('','$prodinfo[proper]','$prodinfo[long_name]','$prodinfo[weapon]','$prodinfo[armor]','N','$prodinfo[skill_abbr]','','','','$prodinfo[skill_level]','','','','$prodinfo[material]','','','','Y','Y','','','','','','N')");
    echo "Added $prodinfo[proper] to inventory database.<BR>";
    $prod->MoveNext();
   }
    echo "Done!<BR>";
  }


  if($menu == 9){
   $prod = array();
   $prod = $db->Execute("SELECT * FROM $dbtables[fair]");
   while(!$prod->EOF){
   $prodinfo = $prod->fields;
   $db->Execute("UPDATE $dbtables[inventory] SET fairinclude = 'Y', pricesell = '$prodinfo[price_sell]', pricebuy = '$prodinfo[price_buy]', fairamount = '$prodinfo[amount]', fairlimit = '$prodinfo[limit]', permamount = '$prodinfo[p_amount]' WHERE longname = '$prodinfo[proper_name]' AND dbname = '$prodinfo[abbr]'");
   $res = $db->Execute("SELECT * FROM $dbtables[inventory] where longname = '$prodinfo[proper_name]'");
   $result = $res->fields;
   if($result[longname] == ''){
   echo "<FONT COLOR=RED>$prodinfo[proper_name] is not in the list!</FONT><BR>";
   }
   else {
   echo "Updated $prodinfo[proper_name] in inventory.<BR>";
   }
   $prod->MoveNext();
   }
   echo "Done!<BR>";
   }



page_footer();

?> 
