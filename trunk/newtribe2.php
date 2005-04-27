<?php
session_start();
header("Cache-control: private");
include("config.php");

page_header("Tribe Creation");

include("game_time.php");
connectdb();

///////////////////////////////////////////////////////////////////////////////

$username = $_SESSION[username];

$chief = $db->Execute("SELECT * FROM $dbtables[chiefs] "
                     ."WHERE username = '$username'");
$chiefinfo = $chief->fields;

$clan = $db->Execute("SELECT * FROM $dbtables[clans] "
                    ."WHERE clanid = '$chiefinfo[clanid]'");
$claninfo = $clan->fields;

$current_unit = $_SESSION[current_unit];

$tribe = $db->Execute("SELECT * FROM $dbtables[tribes] "
                     ."WHERE tribeid = '$current_unit'");
$tribeinfo = $tribe->fields;

$numb = $db->Execute("SELECT * FROM $dbtables[tribes] "
                    ."WHERE clanid = '$_SESSION[clanid]'");
$tribenum = $numb->RecordCount();

$skill = $db->Execute("SELECT * FROM $dbtables[skills] "
                     ."WHERE tribeid = '$current_unit' "
                     ."ORDER BY long_name");
$skillinfo = $skill->fields;

$diplo = $db->Execute("SELECT * FROM $dbtables[skills] "
                     ."WHERE tribeid = '$current_unit' "
                     ."AND abbr = 'dip'");
$diploinfo = $diplo->fields;

$req = $db->Execute("SELECT * FROM $dbtables[subtribe_id] "
                   ."WHERE unique_id = '$_REQUEST[unique]'");

if( !$req->EOF )
{
    $request = $req->fields;
    echo "You have already submitted this form.<BR>";
    page_footer();
}

if( $diplo->EOF | $diploinfo[level] <= $tribenum && !ISSET( $_REQUEST[skills] ) )
{
    echo "You do not have the required amount of Diplomacy skill.<BR><BR>";
    echo "Diplomacy skill: $diploinfo[level] <BR>Current Tribes: " . $tribenum . "<BR><BR>";
    page_footer();
}
else
{
    if( !ISSET( $_REQUEST[skills] ) )
    {
        echo "<CENTER><FORM ACTION=newtribe2.php METHOD=POST>\n";
        echo "<TABLE WIDTH=\"100%\" BORDER=0 CELLPADDING=0 CELLSPACING=0>"
			."<TR BGCOLOR=\"$color_header\">"
			."<TD ALIGN=LEFT COLSPAN=12>"
			."<FONT class=page_subtitle>Skills Transfer"
			."</TD>"
			."</TR>";

		echo "<TR CLASS=row_color0>";
        $n = 0;
		$m = 0;
        while( !$skill->EOF )
        {
            $skillinfo = $skill->fields;
            if( ISSET($skillinfo[long_name]))
            {
				$n++;
				$i = 0;
				echo "<TD>&nbsp;$skillinfo[long_name]</TD><TD><SELECT NAME=$skillinfo[abbr]>";
				while( $i <= $skillinfo[level] )
				{
					echo "<OPTION>$i</OPTION>";
					$i++;
				}
				$skill->MoveNext();
				echo "</SELECT></TD>";
				if( $n >= 6 )
				{
					$n=0;
					$m++;
					$rc = $m % 2;
					echo "</TR><TR CLASS=row_color$rc>\n";
				}
            }
        }
		$cols = 12 - $n*2;
        echo "<TD COLSPAN=$cols>&nbsp;<INPUT TYPE=HIDDEN NAME=newtribe VALUE=$newtribe>";
        echo "<INPUT TYPE=HIDDEN NAME=skills VALUE=1></TD></TR><TR>\n";
        echo "<TD ALIGN=CENTER COLSPAN=12><INPUT TYPE=HIDDEN NAME=unique VALUE='$_REQUEST[unique]'>";
        echo "<INPUT TYPE=SUBMIT VALUE=NEXT></TD></TR>";
        echo "</TABLE></FORM>";
	}
}

if( ISSET( $_REQUEST[skills] ) )
{
	$temp = $db->Execute("SELECT tribeid FROM $dbtables[tribes] "
						."WHERE clanid = '$_SESSION[clanid]' "
						."ORDER BY tribeid "
						."DESC "
						."LIMIT 1");
	$temptribe = $temp->fields;
	$newtribe = $temptribe[tribeid] + .01;
	$_SESSION[newtribe] = $newtribe;
	$adm = $db->Execute("SELECT level FROM $dbtables[skills] "
					   ."WHERE abbr = 'adm' "
					   ."AND tribeid = '$_SESSION[current_unit]'");
	$adm_percent = $adm->fields;
	$start_total_trans_percent = ($adm_percent[level] + 5) * .01;
	$newtribe_totalpop = $tribeinfo[totalpop] * $start_total_trans_percent;
	$newtribe_warpop = 0;
	$newtribe_actives = $tribeinfo[activepop] * $start_total_trans_percent;
	$newtribe_inactives = $tribeinfo[inactivepop] * $start_total_trans_percent;
	$tribeinfo[totalpop] -= $tribeinfo[totalpop] * $start_total_trans_percent;
	$tribeinfo[activepop] -= $tribeinfo[activepop] * $start_total_trans_percent;
	$tribeinfo[inactivepop] -= $tribeinfo[inactivepop] * $start_total_trans_percent;
	$db->Execute("INSERT INTO $dbtables[tribes] "
				."VALUES("
				."'$_SESSION[clanid]',"
				."'$newtribe',"
				."'',"
				."'Y',"
				."'$newtribe_totalpop',"
				."'$newtribe_warpop',"
				."'$newtribe_actives',"
				."'$newtribe_inactives',"
				."'0',"
				."'0',"
				."'0',"
				."'0',"
				."'1.0',"
				."'0',"
				."'0',"
				."'$tribeinfo[hex_id]',"
				."'',"
				."'',"
				."'',"
				."'18',"
				."'$_SESSION[current_unit]')");
	$db->Execute("UPDATE $dbtables[tribes] "
				."SET totalpop = '$tribeinfo[totalpop]', "
				."warpop = '$tribeinfo[warpop]', "
				."activepop = '$tribeinfo[activepop]', "
				."inactivepop = '$tribeinfo[inactivepop]' "
				."WHERE tribeid = '$_SESSION[current_unit]'");
	$subres = $db->Execute("SELECT * FROM $dbtables[resources] "
						  ."WHERE tribeid = '$newtribe'");
	if( $subres->EOF )
	{
		$db->Execute("INSERT INTO $dbtables[resources] VALUES('$newtribe','Bronze','0','bronze')");
		$db->Execute("INSERT INTO $dbtables[resources] VALUES('$newtribe','Iron','0','iron')");
		$db->Execute("INSERT INTO $dbtables[resources] VALUES('$newtribe','Coal','0','coal')");
		$db->Execute("INSERT INTO $dbtables[resources] VALUES('$newtribe','Zinc','0','zinc')");
		$db->Execute("INSERT INTO $dbtables[resources] VALUES('$newtribe','Tin','0','tin')");
		$db->Execute("INSERT INTO $dbtables[resources] VALUES('$newtribe','Silver','0','silver')");
		$db->Execute("INSERT INTO $dbtables[resources] VALUES('$newtribe','Brass','0','brass')");
		$db->Execute("INSERT INTO $dbtables[resources] VALUES('$newtribe','Lead','0','lead')");
		$db->Execute("INSERT INTO $dbtables[resources] VALUES('$newtribe','Salt','0','salt')");
		$db->Execute("INSERT INTO $dbtables[resources] VALUES('$newtribe','Stones','0','stones')");
		$db->Execute("INSERT INTO $dbtables[resources] VALUES('$newtribe','Gold','0','gold')");
		$db->Execute("INSERT INTO $dbtables[resources] VALUES('$newtribe','Copper','0','copper')");
		$db->Execute("INSERT INTO $dbtables[resources] VALUES('$newtribe','Steel','0','steel')");
		$db->Execute("INSERT INTO $dbtables[resources] VALUES('$newtribe','Steel_1','0','steel_1')");
		$db->Execute("INSERT INTO $dbtables[resources] VALUES('$newtribe','Steel_2','0','steel_2')");
		$db->Execute("INSERT INTO $dbtables[resources] VALUES('$newtribe','Coke','0','coke')");
		$db->Execute("INSERT INTO $dbtables[resources] VALUES('$newtribe','Gems','0','gems')");
		$db->Execute("INSERT INTO $dbtables[resources] VALUES('$newtribe','Iron Ore','0','iron.ore')");
		$db->Execute("INSERT INTO $dbtables[resources] VALUES('$newtribe','Copper Ore','0','copper.ore')");
		$db->Execute("INSERT INTO $dbtables[resources] VALUES('$newtribe','Zinc Ore','0','zinc.ore')");
		$db->Execute("INSERT INTO $dbtables[resources] VALUES('$newtribe','Tin Ore','0','tin.ore')");
		$db->Execute("INSERT INTO $dbtables[resources] VALUES('$newtribe','Silver Ore','0','silver.ore')");
		$db->Execute("INSERT INTO $dbtables[resources] VALUES('$newtribe','Lead Ore','0','lead.ore')");
		$db->Execute("INSERT INTO $dbtables[resources] VALUES('$newtribe','Gold Ore','0','gold.ore')");
		$db->Execute("INSERT INTO $dbtables[resources] VALUES('$newtribe','Raw Gems','0','raw.gems')");
		$db->Execute("INSERT INTO $dbtables[livestock] VALUES('','$newtribe','Cattle','0')");
		$db->Execute("INSERT INTO $dbtables[livestock] VALUES('','$newtribe','Horses','0')");
		$db->Execute("INSERT INTO $dbtables[livestock] VALUES('','$newtribe','Elephants','0')");
		$db->Execute("INSERT INTO $dbtables[livestock] VALUES('','$newtribe','Goats','0')");
		$db->Execute("INSERT INTO $dbtables[livestock] VALUES('','$newtribe','Sheep','0')");
		$db->Execute("INSERT INTO $dbtables[livestock] VALUES('','$newtribe','Pigs','0')");
		$db->Execute("INSERT INTO $dbtables[livestock] VALUES('','$newtribe','Dogs','0')");
	}
	$db->Execute("INSERT INTO $dbtables[subtribe_id] "
				."(`unique_id`) "
				."values("
				."'$_REQUEST[unique]')");
	$table = $db->Execute("SELECT * from $dbtables[skill_table]");
	$subskill = $db->Execute("SELECT * FROM $dbtables[skills] "
							."WHERE tribeid = '$newtribe'");
	if( $subskill->EOF )
	{
		while( !$table->EOF )
		{
			$tableinfo = $table->fields;
			$db->Execute("INSERT INTO $dbtables[skills] "
						."VALUES("
						."'',"
						."'$tableinfo[abbr]',"
						."'$tableinfo[long_name]',"
						."'$tableinfo[group]',"
						."'$newtribe',"
						."'0',"
						."'')");
			$table->MoveNext();
		}
	}

	$prod = $db->Execute("SELECT DISTINCT proper, long_name, weapon, armor "
						."FROM $dbtables[product_table] "
						."WHERE include = 'Y'");
	$subprod = $db->Execute("SELECT * FROM $dbtables[products] WHERE tribeid = '$newtribe'");
	if($subprod->EOF)
	{
		while(!$prod->EOF)
		{
			$prodinfo = $prod->fields;
			$db->Execute("INSERT INTO $dbtables[products] "
						."VALUES("
						."'$newtribe',"
						."'$prodinfo[proper]',"
						."'$prodinfo[long_name]',"
						."'0',"
						."'$prodinfo[weapon]',"
						."'$prodinfo[armor]')");
			$prod->MoveNext();
		}
	}
	$db->Execute("UPDATE $dbtables[products] "
				."SET amount = 0 "
				."WHERE amount < 0 "
				."AND tribeid = '$newtribe'");

	echo "<CENTER><BR><BR><FONT SIZE=+1>New Subtribe $newtribe Created</CENTER></FONT><BR><BR>";

	include("weight.php");

	foreach($_REQUEST as $key => $value)
	{
		$new = $db->Execute("SELECT * FROM $dbtables[skill_table] WHERE abbr = '$key'");
		$newskill = $new->fields;
		if(ISSET($newskill[long_name]))
		{
			$db->Execute("UPDATE $dbtables[skills] "
						."SET level = level + '$value' "
						."WHERE abbr = '$newskill[abbr]' "
						."AND tribeid = '$newtribe'");
			$old = $db->Execute("SELECT * FROM $dbtables[skills] "
								."WHERE tribeid = '$_SESSION[current_unit]' "
								."AND abbr = '$newskill[abbr]'");
			$oldskill = $old->fields;

			$db->Execute("UPDATE $dbtables[skills] "
						."SET level = level - '$value' "
						."WHERE abbr = '$oldskill[abbr]' "
						."AND tribeid = '$tribeinfo[tribeid]'");
		 }
	}
}


page_footer();

?>
