<?php
$pos = (strpos($_SERVER['PHP_SELF'], "/herding.php"));
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
                       ."AND skill_abbr = 'herd'");
      db_op_result($act,__LINE__,__FILE__);
    $act_do = $act->fields;
    if($act_do['actives'] > 0){
    }

    $liv1 = $db->Execute("SELECT * FROM $dbtables[livestock] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND type = 'Cattle'");
             db_op_result($liv1,__LINE__,__FILE__);
    $liv2 = $db->Execute("SELECT * FROM $dbtables[livestock] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND type = 'Horses'");
              db_op_result($liv2,__LINE__,__FILE__);
    $liv3 = $db->Execute("SELECT * FROM $dbtables[livestock] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND type = 'Elephants'");
             db_op_result($liv3,__LINE__,__FILE__);
    $liv4 = $db->Execute("SELECT * FROM $dbtables[livestock] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND type = 'Goats'");
         db_op_result($liv4,__LINE__,__FILE__);
    $liv5 = $db->Execute("SELECT * FROM $dbtables[livestock] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND type = 'Dogs'");
      db_op_result($liv5,__LINE__,__FILE__);
    $liv6 = $db->Execute("SELECT * FROM $dbtables[livestock] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND type = 'Pigs'");
       db_op_result($liv6,__LINE__,__FILE__);
    $liv7 = $db->Execute("SELECT * FROM $dbtables[livestock] "
                        ."WHERE tribeid = '$tribe[tribeid]' "
                        ."AND type = 'Sheep'");
     db_op_result($liv7,__LINE__,__FILE__);

    $mounts1 = $liv1->fields;
    $mounts2 = $liv2->fields;
    $mounts3 = $liv3->fields;
    $mounts4 = $liv4->fields;
    $mounts5 = $liv5->fields;
    $mounts6 = $liv6->fields;
    $mounts7 = $liv7->fields;

    $skill = $db->Execute("SELECT * FROM $dbtables[skills] "
                         ."WHERE tribeid = '$tribe[tribeid]' "
                         ."AND abbr = 'herd'");
       db_op_result($skill,__LINE__,__FILE__);
    $skillinfo = $skill->fields;

    $denominator = 10 + $skillinfo['level'];
    $denominator2 = 5 + $skillinfo['level'];
    $denominator3 = 20 + $skillinfo['level'];
    $required_herders = 0;
    $required_herders = round($mounts1['amount']/$denominator);
    $required_herders = $required_herders + round($mounts2['amount']/$denominator);
    $required_herders = $required_herders + round($mounts3['amount']/$denominator2);
    $required_herders = $required_herders + round($mounts4['amount']/$denominator3);
    $required_herders = $required_herders + round($mounts5['amount']/$denominator);
    $required_herders = $required_herders + round($mounts6['amount']/$denominator3);
    $required_herders = $required_herders + round($mounts7['amount']/$denominator3);


if($required_herders <= $act_do['actives'] && $required_herders > 0 ){
        if( $skillinfo['level'] < 1 )
        {
            $skillinfo['level'] = .5;
        }
        $popbonus = (.015 * $skillinfo['level']);
        $horses = $mounts2['amount'];
        $pig    = $mounts6['amount'];
        $goat   = $mounts4['amount'];
        $sheep  = $mounts7['amount'];
        $dog    = $mounts5['amount'];
        $cattle = $mounts1['amount'];
        $elephant = $mounts3['amount'];

        $horsesbred = round(($horses * $popbonus)/5);
        $pigbred = round(($pig * $popbonus)/3);
        $goatbred = round(($goat * $popbonus)/4);
        $sheepbred = round(($sheep * $popbonus)/3);
        $dogbred = round(($dog * $popbonus)/6);
        $cattlebred = round(($cattle * $popbonus)/5);
        $elephantbred = round(($elephant * $popbonus)/8);

//        $query = $db->Execute("INSERT INTO $dbtables[logs] "
//                    ."VALUES("
//                    ."'',"
//                    ."'$month[count]',"
//                    ."'$year[count]',"
//                    ."'0000',"
//                    ."'0000.00',"
//                    ."'BREEDING',"
//                    ."'$stamp',"
//                    ."'BREEDING: $tribe[tribeid] $horses/$horsesbred horses, "
//                    ."$pig/$pigbred pigs, "
//                    ."$goat/$goatbred goats, "
//                    ."$sheep/$sheepbred sheep, "
//                    ."$dog/$dogbred dogs, "
//                    ."$cattle/$cattlebred cattle, "
//                    ."$elephant/$elephantbred elephants.')");
//           db_op_result($query,__LINE__,__FILE__);



        $logtext = 'Herding: ';
        $logcount = 0;

        if( $elephantbred > 0 )
        {
            $query = $db->Execute("UPDATE $dbtables[livestock] "
                        ."SET amount = amount + '$elephantbred' "
                        ."WHERE type = 'Elephants' "
                        ."AND tribeid = '$tribe[goods_tribe]'");
                 db_op_result($query,__LINE__,__FILE__);
            $logtext = "$logtext$elephantbred Elephants";
            $logcount++;
        }

        if( $horsesbred > 0 )
        {
            if( $logcount > 0 )
            {
                $logtext .= ',';
            }
            $query = $db->Execute("UPDATE $dbtables[livestock] "
                        ."SET amount = amount + '$horsesbred' "
                        ."WHERE type = 'Horses' "
                        ."AND tribeid = '$tribe[goods_tribe]'");
               db_op_result($query,__LINE__,__FILE__);
            $logtext .= "$horsesbred Horses";
            $logcount++;
        }

        if( $cattlebred > 0 )
        {
            if( $logcount > 0 )
            {
                $logtext .= ',';
            }
            $query = $db->Execute("UPDATE $dbtables[livestock] "
                        ."SET amount = amount + '$cattlebred' "
                        ."WHERE type = 'Cattle' "
                        ."AND tribeid = '$tribe[goods_tribe]'");
               db_op_result($query,__LINE__,__FILE__);
            $logtext .= " $cattlebred Cattle";
            $logcount++;
        }

        if( $goatbred > 0 )
        {
            if( $logcount > 0 )
            {
                $logtext .= ',';
            }
            $query = $db->Execute("UPDATE $dbtables[livestock] "
                        ."SET amount = amount + '$goatbred' "
                        ."WHERE type = 'Goats' "
                        ."AND tribeid = '$tribe[goods_tribe]'");
            db_op_result($query,__LINE__,__FILE__);
            $logtext .= " $goatbred Goats";
            $logcount++;
        }

        if( $sheepbred > 0 )
        {
            if( $logcount > 0 )
            {
                $logtext .= ',';
            }
            $query = $db->Execute("UPDATE $dbtables[livestock] "
                        ."SET amount = amount + '$sheepbred' "
                        ."WHERE type = 'Sheep' "
                        ."AND tribeid = '$tribe[goods_tribe]'");
              db_op_result($query,__LINE__,__FILE__);
            $logtext .= " $sheepbred Sheep";
            $logcount++;
        }

        if( $pigbred > 0 )
        {
            if( $logcount > 0 )
            {
                $logtext .= ',';
            }
        $query = $db->Execute("UPDATE $dbtables[livestock] "
                    ."SET amount = amount + '$pigbred' "
                    ."WHERE type = 'Pigs' "
                    ."AND tribeid = '$tribe[goods_tribe]'");
           db_op_result($query,__LINE__,__FILE__);
            $logtext .= " $pigbred Pigs";
            $logcount++;
        }

        if( $dogbred > 0 )
        {
            if( $logcount > 0 )
            {
                $logtext .= ',';
            }
        $query = $db->Execute("UPDATE $dbtables[livestock] "
                    ."SET amount = amount + '$dogbred' "
                    ."WHERE type = 'Dogs' "
                    ."AND tribeid = '$tribe[goods_tribe]'");
           db_op_result($query,__LINE__,__FILE__);
            $logtext .= " $dogbred Dogs";
            $logcount++;
        }

        if( $logcount > 0 )
        {
            $logtext .= " gained.";
        }
        else
        {
            $logtext .= " Nothing gained.";
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
                    ."'$logtext')");

        db_op_result($query,__LINE__,__FILE__);


}
else
{
    if( $required_herders > 0 )
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
                    ."'Herding: $act_do[actives] assigned, $required_herders required. No breeding occurred.')");
          db_op_result($query,__LINE__,__FILE__);
    }
}
$query = $db->Execute("DELETE FROM $dbtables[activities] WHERE tribeid = '$tribe[tribeid]' AND skill_abbr = 'herd'");
db_op_result($query,__LINE__,__FILE__);
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
