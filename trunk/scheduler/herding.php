<?php
$pos = (strpos($_SERVER['PHP_SELF'], "/herding.php"));
if ($pos !== false)
{
    die("You cannot access this page directly!");
}
//we must ensure that herding and provisions skills are enabled with whatever remaining actives are available at the beginning of
//scheduler.

$sql = "select a.tribeid,a.goods_tribe,a.clanid,b.actives from $dbtables[activities] as b,$dbtables[tribes] as a where b.skill_abbr='herd' and b.actives > 0 and b.tribeid=a.tribeid";
$mainloop = $db->Execute($sql);
db_op_result($mainloop,__LINE__,__FILE__);
while(!$mainloop->EOF)
{
     $data = $mainloop->fields;
     $herders = $data['actives'];
     //OK now let's get how many this tribe currently has in livestock
    $sql = $db->Prepare("SELECT type,amount from $dbtables[livestock] WHERE tribeid=?");
    db_op_result($mainloop,__LINE__,__FILE__);
    $animals = $db->Execute($sql,array($data['tribeid']));
    while(!$animals->EOF)
    {
         $info = $animals->fields;
         $name = $info['type'];
         $amt = $info['amount'];
         $$name = $amt;
         //above is a variable variable, resulting in something like $Catttle = 3724;
         //NOTE- animal names appear to be capitalized letters, this likely will be important
         $animals->MoveNext();
    }
    //OK now lets get this tribe's actual skill level
    $skills = $db->Prepare("SELECT level FROM $dbtables[skills] WHERE tribeid = ? AND abbr = 'herd'");
    $skill = $db->Execute($skills,array($data['tribeid']));
    db_op_result($skill,__LINE__,__FILE__);
    $skillinfo = $skill->fields;
    //now we calculate herders required in total  and see if we have enough actives assigned
    $cat_hors_dog_herd = 10 + $skillinfo['level'];
    $eleph_herd = 5 + $skillinfo['level'];
    $goat_pig_shp_herd = 20 + $skillinfo['level'];
    $required_herders = 0;
    $pigs_bred = 0;
    $goats_bred=0;
    $sheep_bred=0;
    $horses_bred=0;
    $dogs_bred=0;
    $cattle_bred=0;
    $elephants_bred=0;
    $pigs_lost = 0;
    $goats_lost=0;
    $sheep_lost=0;
    $horses_lost=0;
    $dogs_lost=0;
    $cattle_lost=0;
    $elephants_lost=0;
    $cattle = abs(round($Cattle/$cat_hors_dog_herd));
    $horse = abs(round($Horses/$cat_hors_dog_herd));
    $dogs = abs(round($Dogs/$cat_hors_dog_herd));
    $elephants = abs(round($Elephants/$eleph_herd));
    $goat = abs(round($Goats/$goat_pig_shp_herd));
    $sheep = abs(round($Sheep/$goat_pig_shp_herd));
    $pigs = abs(round($Pigs/$goat_pig_shp_herd));
    $required_herders = abs($cattle+$horse+$dogs+$elephants+$goat+$sheep+$pigs);
    //set bonus based on how many excess herders
    if($required_herders < $herders)
    {
        $surplus = abs(round(($herders - $required_herders)/3));
        if($surplus < 100)
        {
           $divisor = 20;
        }
        elseif($surplus >100 && $surplus < 500)
        {
            $divisor = 50;
        }
        else
        {
           $divisor = 100;
        }
        $bonus = ((0.01*($surplus/$divisor))+(0.015*$skillinfo['level']));
        $cattle_bred = abs(ceil(round($Cattle/5)*$bonus));
        $log_breed = "$data[tribeid] produced $cattle_bred additional cattle,";

        $horses_bred = abs(ceil(round($Horses/5)*$bonus));
        $log_breed .= "$horses_bred additional horses, ";

        $dogs_bred = abs(ceil(round($Dogs/3)*$bonus));
        $log_breed .= "$dogs_bred additional dogs, ";

        $elephants_bred = abs(ceil(round($Elephants/7)*$bonus));
        $log_breed .= "$elephants_bred additional elephants, ";

        $goats_bred = abs(ceil(round($Goats/5)*$bonus));
        $log_breed .= "$goats_bred additional goats, ";

        $sheep_bred = abs(ceil(round($Sheep/5)*$bonus));
        $log_breed .= "$sheep_bred additional sheep, ";

        $pigs_bred = abs(ceil(round(($Pigs/3)*$bonus)));
        $log_breed .= "and $pigs_bred additional pigs with $surplus extra herders. needed $required_herders had $herders ";
        playerlog($data['tribeid'],$data['clanid'],'BREEDING',$month['count'],$year['count'],$log_breed,$dbtables);
    }
    elseif($required_herders > $herders)
    {
         //animals run away
         $loss = abs(round(($required_herders - $herders)/3));
         $runaway = abs((0.05*(mt_rand(1,$loss)/100)));
         $cattle_lost = abs(round($Cattle*$runaway));
         $log_lost = "$data[tribeid] Lost $cattle_lost cattle, ";

         $horses_lost = abs(round($Horses*$runaway));
         $log_lost .= "$data[tribeid] Lost $horses_lost horses, ";

         $dogs_lost = abs(round(($Dogs*$runaway)/2));//dogs tend to have loyalty and stay in pack
         $log_lost .= "$data[tribeid] Lost $dogs_lost dogs, ";

         $elephants_lost = abs(round(($Elephants*$runaway)*.01));//elephants harder to handle and herd
         $log_lost .= "$data[tribeid] Lost $elephants_lost elephants, ";

         $goats_lost = abs(round($Goats*$runaway));
         $log_lost .= "$data[tribeid] Lost $goats_lost goats, ";

         $sheep_lost = abs(round($Sheep*$runaway));
         $log_lost .= "$data[tribeid] Lost $sheep_lost sheep, ";

         $pigs_lost = abs(round($Pigs*$runaway));
         $log_lost .= "$data[tribeid] Lost $pigs_lost pigs. You did not assign enough herders. needed $required_herders had $herders ";
         playerlog($data['tribeid'],$data['clanid'],'HERDLOSS',$month['count'],$year['count'],$log_lost,$dbtables);
    }
    //OK now we have gains and losses and let's see if we have to update...
    //cattle
    if($cattle_lost > 0 || $cattle_bred > 0)
    {
        $cows = (($Cattle+$cattle_bred)-$cattle_lost);
        if($cows < 0)
        {
           $cows = 0;
        }
        $sql = $db->Prepare("UPDATE $dbtables[livestock] SET amount = ? where type='Cattle' and tribeid=?");
        $query = $db->Execute($sql,array($cows,$data['tribeid']));
        db_op_result($query,__LINE__,__FILE__);
    }
    if($horses_lost > 0 || $horses_bred > 0)
    {
        $horses = (($Horses+$horses_bred)-$horses_lost);
        if($horses < 0)
        {
           $horses = 0;
        }
        $sql = $db->Prepare("UPDATE $dbtables[livestock] SET amount = ? where type='Horses' and tribeid=?");
        $query = $db->Execute($sql,array($horses,$data['tribeid']));
        db_op_result($query,__LINE__,__FILE__);
    }
    if($dogs_lost > 0 || $dogs_bred > 0)
    {
        $cows = (($Dogs+$dogs_bred)-$dogs_lost);
        if($cows < 0)
        {
           $cows = 0;
        }
        $sql = $db->Prepare("UPDATE $dbtables[livestock] SET amount = ? where type='Dogs' and tribeid=?");
        $query = $db->Execute($sql,array($cows,$data['tribeid']));
         db_op_result($query,__LINE__,__FILE__);
    }
    if($elephants_lost > 0 || $elephants_bred > 0)
    {
        $cows = (($Elephants+$elephants_bred)-$elephants_lost);
        if($cows < 0)
        {
           $cows = 0;
        }
        $sql = $db->Prepare("UPDATE $dbtables[livestock] SET amount = ? where type='Elephants' and tribeid=?");
        $query = $db->Execute($sql,array($cows,$data['tribeid']));
        db_op_result($query,__LINE__,__FILE__);
    }
    if($goats_lost > 0 || $goats_bred > 0)
    {
        $cows = (($Goats+$goats_bred)-$goats_lost);
        if($cows < 0)
        {
           $cows = 0;
        }
        $sql = $db->Prepare("UPDATE $dbtables[livestock] SET amount = ? where type='Goats' and tribeid=?");
        $query = $db->Execute($sql,array($cows,$data['tribeid']));
        db_op_result($query,__LINE__,__FILE__);
    }
    if($sheep_lost > 0 || $sheep_bred > 0)
    {
        $cows = (($Sheep+$sheep_bred)-$sheep_lost);
        if($cows < 0)
        {
           $cows = 0;
        }
        $sql = $db->Prepare("UPDATE $dbtables[livestock] SET amount = ? where type='Sheep' and tribeid=?");
        $query = $db->Execute($sql,array($cows,$data['tribeid']));
         db_op_result($query,__LINE__,__FILE__);
    }
    if($pigs_lost > 0 || $pigs_bred > 0)
    {
        $cows = (($Pigs+$pigs_bred)-$pigs_lost);
        if($cows < 0)
        {
           $cows = 0;
        }
        $sql = $db->Prepare("UPDATE $dbtables[livestock] SET amount = ? where type='Pigs' and tribeid=?");
        $query = $db->Execute($sql,array($cows,$data['tribeid']));
        db_op_result($query,__LINE__,__FILE__);
    }

  $mainloop->MoveNext();
}

$query = $db->Execute("DELETE FROM $dbtables[activities] WHERE skill_abbr = 'herd'");
db_op_result($query,__LINE__,__FILE__);


?>
