<?php
require_once("../config.php");
$time_start = getmicrotime();
include("game_time.php");
connectdb();

$res = $db->Execute("SELECT * FROM $dbtables[tribes]");
  db_op_result($res,__LINE__,__FILE__);
while(!$res->EOF)
{
    $tribe = $res->fields;
    $cnt = $db->Execute("SELECT actives FROM $dbtables[activities] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND skill_abbr = 'for'");
        db_op_result($cnt,__LINE__,__FILE__);
    $foresters = 0;
    while(!$cnt->EOF)
    {
        $count = $cnt->fields;
        $foresters += $count[actives];
        $cnt->MoveNext();
    }
    $foresters_used = 0;

    $act = $db->Execute("SELECT * FROM $dbtables[activities] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND skill_abbr = 'for' ");
        db_op_result($act,__LINE__,__FILE__);
    while(!$act->EOF)
    {
        $act_do = $act->fields;

        if($act_do[skill_abbr] == 'for')
        {

            $startingadze = 0;
            $hex = $db->Execute("SELECT * FROM $dbtables[hexes] "
                                ."WHERE hex_id = '$tribe[hex_id]'");
              db_op_result($hex,__LINE__,__FILE__);
            $hexinfo = $hex->fields;

            $skill = $db->Execute("SELECT * FROM $dbtables[skills] "
                                ."WHERE tribeid = '$tribe[tribeid]' "
                                ."AND abbr = 'for'");
               db_op_result($skill,__LINE__,__FILE__);
            $skillinfo = $skill->fields;

            $max_forestry = 10000000;
            if($skillinfo[level] < 10)
            {
                $max_forestry = $skillinfo[level] * 10;
                if($act_do[actives] > $max_forestry)
                {
                    $act_do[actives] = $max_forestry;
                }
                if ($foresters_used > $max_forestry)
                {
                    $act_do['actives'] = 0;
                }
                else
                {
                    $act_do['actives'] = min($act_do['actives'], $max_forestry-$foresters_used);
                }
                $foresters_used += $act_do['actives'];

            }

            $scrapeinfo[amount] = 0;
            $scrape = $db->Execute("SELECT * FROM $dbtables[products] "
                                    ."WHERE tribeid = '$tribe[goods_tribe]' "
                                    ."AND long_name = 'scrapers'");
               db_op_result($scrape,__LINE__,__FILE__);
            $scrapeinfo = $scrape->fields;
            if($scrapeinfo[amount] > $act_do[actives])
            {
                $scrapeinfo[amount] = $act_do[actives];
            }
            if( !$scrapeinfo[long_name] == '' && $scrapeinfo[amount] > 1 )
            {
                $result = $db->Execute("INSERT INTO $dbtables[products_used] "
                            ."VALUES("
                            ."'$tribe[goods_tribe]',"
                            ."'$scrapeinfo[amount]',"
                            ."'$scrapeinfo[long_name]')");
                  db_op_result($result,__LINE__,__FILE__);
                $result = $db->Execute("UPDATE $dbtables[products] "
                            ."SET amount = amount - $scrapeinfo[amount] "
                            ."WHERE tribeid = '$tribe[goods_tribe]' "
                            ."AND long_name = '$scrapeinfo[long_name]'");
                  db_op_result($result,__LINE__,__FILE__);
            }


            if(       $hexinfo[terrain] == 'cf'
                || $hexinfo[terrain] == 'ch'
                || $hexinfo[terrain] == 'lcm'
                || $hexinfo[terrain] == 'df'
                || $hexinfo[terrain] == 'dh'
                || $hexinfo[terrain] == 'jg'
                || $hexinfo[terrain] == 'jh'
                || $hexinfo[terrain] == 'ljm')
            {
                if($act_do[product] == 'bark')
                {
                    $startingadze = 0;
                    $scrapersused = 0;
                    $startingforesters = $act_do[actives];
                    if($scrapeinfo[amount] > 0)
                    {
                        while($act_do[actives] > 0 & $scrapeinfo[amount] > 0)
                        {
                            $scrapersused += 1;
                            $act_do[actives] += .5;
                            $scrapeinfo[amount] -= 1;
                        }
                        $act_do[actives] = round($act_do[actives]);
                    }

                    $barks = 0;
                    while($act_do[actives] > 0)
                    {
                        $act_do[actives] -= 1;
                        $barks += 20;
                    }
                    if($scrapersused > 0)
                    {
                        $scrapelog = ' using ' . $scrapersused . ' scrapers.';
                    }
                    else
                    {
                        $scrapelog = '.';
                    }
                    $result = $db->Execute("UPDATE $dbtables[products] "
                                ."SET amount = amount + '$barks' "
                                ."WHERE tribeid = '$tribe[goods_tribe]' "
                                ."AND long_name = 'bark'");
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
                        ."'Forestry: $barks Bark stripped by $startingforesters actives$scrapelog')");
                     db_op_result($result,__LINE__,__FILE__);
                } // end BARK

                if($act_do[product] == 'logs')
                {
                    $adze = $db->Execute("SELECT * FROM $dbtables[products] "
                                        ."WHERE tribeid = '$tribe[goods_tribe]' "
                                        ."AND amount > 0 "
                                        ."AND long_name = 'adze'");
                      db_op_result($adze,__LINE__,__FILE__);
                    $startingadze = 0;
                    $startingforesters = $act_do[actives];
                    $logs = 0;
                    if($adze->EOF)
                    {
                        while($act_do[actives] > 0)
                        {
                            $act_do[actives] -= 1;
                            $logs += 4;
                        }
                    }
                    else
                    {
                        $adzeinfo = $adze->fields;
                        $startingadze = $adzeinfo[amount];
                        if($adzeinfo[amount] == '0' | empty($adzeinfo[amount]))
                        {
                            $startingadze = 0;
                        }
                        if($adzeinfo[amount] > $act_do[actives])
                        {
                            $adzeinfo[amount] = $act_do[actives];
                            if( !$adzeinfo[long_name] == '' && $adzeinfo[amount] > 1 )
                            {
                                $result = $db->Execute("INSERT INTO $dbtables[products_used] "
                                            ."VALUES("
                                            ."'$tribe[goods_tribe]',"
                                            ."'$adzeinfo[amount]',"
                                            ."'$adzeinfo[long_name]')");
                                 db_op_result($result,__LINE__,__FILE__);
                                $result = $db->Execute("UPDATE $dbtables[products] "
                                            ."SET amount = amount - $adzeinfo[amount] "
                                            ."WHERE tribeid = '$tribe[goods_tribe]' "
                                            ."AND long_name = '$adzeinfo[long_name]'");
                                  db_op_result($result,__LINE__,__FILE__);
                            }
                        }
                        while($act_do[actives] > 0 & $adzeinfo[amount] > 0)
                        {
                            $act_do[actives] -= 1;
                            $adzeinfo[amount] -= 1;
                            $logs += 8;
                        }
                        while($act_do[actives] > 0)
                        {
                            $act_do[actives] -= 1;
                            $logs += 4;
                        }
                    }

                    if($startingadze > 0)
                    {
                        $adzelog = ' using ' . $startingadze . ' adze.';
                    }
                    else
                    {
                        $adzelog = '.';
                    }
                    $result = $db->Execute("UPDATE $dbtables[products] "
                                ."SET amount = amount + '$logs' "
                                ."WHERE tribeid = '$tribe[goods_tribe]' "
                                ."AND long_name = 'logs'");
                      db_op_result($result,__LINE__,__FILE__);
                    $result = $db->Execute("INSERT INTO $dbtables[logs] "
                                ."VALUES("
                                ."'',"
                                ."'$month[count]',"
                                ."'$year[count]',"
                                ."'$tribe[clanid]',"
                                ."'$tribe[tribeid]',"
                                ."'UPDATE','$stamp','Forestry: $logs Logs collected with $startingforesters actives$adzelog')");
                      db_op_result($result,__LINE__,__FILE__);
                } //end LOGS

            } // end TERRAIN
            else
            {
                $result = $db->Execute("INSERT INTO $dbtables[logs] "
                            ."VALUES("
                            ."'',"
                            ."'$month[count]',"
                            ."'$year[count]',"
                            ."'$tribe[clanid]',"
                            ."'$tribe[tribeid]',"
                            ."'UPDATE',"
                            ."'$stamp',"
                            ."'Forestry: We must be in a forested location (deciduous, coniferous, deciduous hills, coniferous hills, jungles, jungle hills, low coniferous mountains, low jungle mountains).')");
              db_op_result($result,__LINE__,__FILE__);
            }

        } //end FORESTRY

        $act->MoveNext();

    } //end ACT

    $result = $db->Execute("DELETE FROM $dbtables[activities] "
                ."WHERE tribeid = '$tribe[tribeid]' "
                ."AND skill_abbr = 'for'");
       db_op_result($result,__LINE__,__FILE__);
    $res->MoveNext();

} //end RES


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
