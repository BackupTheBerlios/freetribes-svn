<?
session_start();
header("Cache-control: private");
include("config.php");
include("game_time.php");

page_header("Tribal Seekers");

connectdb();

/*
echo "<PRE>";
print_r($_REQUEST);
echo "</PRE>";
*/

echo "<CENTER>"
	."<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0>"
	."<TR>"

	."<FORM ACTION=seeking.php METHOD=POST>"
	."<TD>"
	."<INPUT TYPE=HIDDEN NAME=target VALUE=herbs>"
	."<INPUT TYPE=HIDDEN NAME=action VALUE=assign>"
	."<INPUT TYPE=SUBMIT VALUE=\"Herbs\">"
	."&nbsp;</TD>"
	."</FORM>"

	."<FORM ACTION=seeking.php METHOD=POST>"
	."<TD>"
	."<INPUT TYPE=HIDDEN NAME=target VALUE=hives>"
	."<INPUT TYPE=HIDDEN NAME=action VALUE=assign>"
	."<INPUT TYPE=SUBMIT VALUE=\"Hives\">"
	."&nbsp;</TD>"
	."</FORM>"

	."<FORM ACTION=seeking.php METHOD=POST>"
	."<TD>"
	."<INPUT TYPE=HIDDEN NAME=target VALUE=spice>"
	."<INPUT TYPE=HIDDEN NAME=action VALUE=assign>"
	."<INPUT TYPE=SUBMIT VALUE=\"Spices\">"
	."&nbsp;</TD>"
	."</FORM>"

	."<FORM ACTION=seeking.php METHOD=POST>"
	."<TD>"
	."<INPUT TYPE=HIDDEN NAME=target VALUE=wax>"
	."<INPUT TYPE=HIDDEN NAME=action VALUE=assign>"
	."<INPUT TYPE=SUBMIT VALUE=\"Wax\">"
	."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</TD>"
	."</FORM>"

	."<FORM ACTION=seeking.php METHOD=POST>"
	."<TD>"
	."<INPUT TYPE=HIDDEN NAME=target VALUE=Dogs>"
	."<INPUT TYPE=HIDDEN NAME=action VALUE=assign>"
	."<INPUT TYPE=SUBMIT VALUE=\"Dogs\">"
	."&nbsp;</TD>"
	."</FORM>"

	."<FORM ACTION=seeking.php METHOD=POST>"
	."<TD>"
	."<INPUT TYPE=HIDDEN NAME=target VALUE=Elephants>"
	."<INPUT TYPE=HIDDEN NAME=action VALUE=assign>"
	."<INPUT TYPE=SUBMIT VALUE=\"Elephants\">"
	."&nbsp;</TD>"
	."</FORM>"

	."<FORM ACTION=seeking.php METHOD=POST>"
	."<TD>"
	."<INPUT TYPE=HIDDEN NAME=target VALUE=Goats>"
	."<INPUT TYPE=HIDDEN NAME=action VALUE=assign>"
	."<INPUT TYPE=SUBMIT VALUE=\"Goats\">"
	."&nbsp;</TD>"
	."</FORM>"

	."<FORM ACTION=seeking.php METHOD=POST>"
	."<TD>"
	."<INPUT TYPE=HIDDEN NAME=target VALUE=Horses>"
	."<INPUT TYPE=HIDDEN NAME=action VALUE=assign>"
	."<INPUT TYPE=SUBMIT VALUE=\"Horses\">"
	."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</TD>"
	."</FORM>"

	."<FORM ACTION=seeking.php METHOD=POST>"
	."<TD>"
	."<INPUT TYPE=HIDDEN NAME=target VALUE=recruit>"
	."<INPUT TYPE=HIDDEN NAME=action VALUE=assign>"
	."<INPUT TYPE=SUBMIT VALUE=\"Recruits\">"
	."&nbsp;</TD>"
	."</FORM>"

	."</TR>"
	."</TABLE>";

echo "<BR><CENTER>";
echo "<TABLE BORDER=0 WIDTH=80%>";
echo "<TR CLASS=color_header ALIGN=CENTER><TD COLSPAN=2>";
echo "<A HREF=activities.php>Activities</A> | <A HREF=garrisons.php>Garrisons</A> | <A HREF=goodstribe.php>Change Goods Tribe</A></TD></TR>";
echo "</TABLE>"
	."<BR>";
$mode = $_REQUEST[action];

/*
if( !ISSET( $mode ) )
{
    echo "<CENTER>Select an action to perform:<BR>";
    echo "<FORM ACTION=seeking.php METHOD=POST><SELECT NAME=action>";
    echo "<OPTION VALUE=assign>Assign</OPTION>";
    echo "<OPTION VALUE=disband>Cancel</OPTION>";
    echo "</SELECT>&nbsp;<INPUT TYPE=SUBMIT VALUE=SUBMIT></FORM></CENTER>";
}
*/

if( $_REQUEST[action] == 'assign' )
{
    $linecolor = $color_line1;
    $war = $db->Execute("SELECT * FROM $dbtables[tribes] "
                       ."WHERE tribeid = '$_SESSION[current_unit]'");
    $warinfo = $war->fields;
    $goodstribe = $war->fields;
    $hs = $db->Execute("SELECT * FROM $dbtables[livestock] "
                      ."WHERE tribeid = '$goodstribe[goods_tribe]' "
                      ."AND type = 'Horses'");
    $horseinfo = $hs->fields;
    echo "<TABLE BORDER=0><FORM ACTION=seeking.php METHOD=POST>";
    $avail = $warinfo[curam] - $warinfo[slavepop];
    if( $avail < 0 )
    {
        $avail = 0;
    }

	$seek_sel = array	(
						"Herbs"     => "herbs",
						"Hives"     => "hives",
						"Spice"     => "spice",
						"Wax"       => "wax",
						"Dogs"      => "Dogs",
						"Elephants" => "Elephants",
						"Goats"     => "Goats",
						"Horses"    => "Horses",
						"Recruits"  => "recruit"
						);

	echo "<TR CLASS=color_row1><TD>Objective</TD>";
    echo "<TD COLSPAN=2 ALIGN=CENTER><SELECT NAME=target>";
	foreach ($seek_sel AS $key => $value)
	{
		if ($value==$_REQUEST['target'])
		{
			$selected = " SELECTED";
		}
		else
		{
			$selected = "";
		}
	    echo "<OPTION VALUE=$value$selected>$key</OPTION>";
	}
    echo "</SELECT></TD></TR>";

	echo "<TR CLASS=color_row0><TD>How many seekers?</TD>";
    echo "<TD><INPUT CLASS=edit_area TYPE=TEXT NAME=force SIZE=3 MAXLENGTH=3>";
    echo "</TD><TD>$avail Actives available</TD></TR>";
    echo "<TR CLASS=color_row1><TD>How many mounts?</TD>";
    echo "<TD><INPUT CLASS=edit_area TYPE=TEXT NAME=horses SIZE=3 MAXLENGTH=3>";
    echo "</TD><TD>$horseinfo[amount] Horses available</TD></TR>";
    $wag = $db->Execute("SELECT * FROM $dbtables[products] "
                       ."WHERE tribeid = '$goodstribe[goods_tribe]' "
                       ."AND long_name = 'wagon'");
    $wagoninfo = $wag->fields;
    $cat = $db->Execute("SELECT * FROM $dbtables[livestock] "
                       ."WHERE tribeid = '$goodstribe[goods_tribe]' "
                       ."AND type = 'Cattle'");
    $cattleinfo = $cat->fields;
    $back = $db->Execute("SELECT * FROM $dbtables[products] "
                        ."WHERE tribeid = '$goodstribe[goods_tribe]' "
                        ."AND long_name = 'backpack'");
    $backinfo = $back->fields;
    $sad = $db->Execute("SELECT * FROM $dbtables[products] "
                       ."WHERE tribeid = '$goodstribe[goods_tribe]' "
                       ."AND long_name = 'saddlebags'");
    $sadbag = $sad->fields;
    echo "<TR CLASS=color_row0><TD>How many wagons?</TD>";
    echo "<TD><INPUT CLASS=edit_area TYPE=TEXT NAME=wagons SIZE=3 MAXLENGTH=2></TD>";
    echo "<TD>$wagoninfo[amount] Wagons Available ($cattleinfo[amount] Cattle)</TD></TR>";
    echo "<TR CLASS=color_row1><TD>How many backpacks?</TD>";
    echo "<TD><INPUT CLASS=edit_area TYPE=TEXT NAME=backpacks SIZE=3 MAXLENGTH=3></TD>";
    echo "<TD>$backinfo[amount] Backpacks Available</TD></TR>";
    echo "<TR CLASS=color_row0><TD>How many saddlebags?</TD>";
    echo "<TD><INPUT CLASS=edit_area TYPE=TEXT NAME=saddlebags SIZE=3 MAXLENGTH=3></TD>";
    echo "<TD>$sadbag[amount] Saddlebags Available</TD></TR>";

    echo "<TR CLASS=color_row0><TD COLSPAN=3><CENTER>";
    echo "<INPUT TYPE=SUBMIT VALUE=SEEK></FORM></CENTER></TD></TR></TABLE>";
    echo "<CENTER>Duplicate seekers will be ignored, please consolidate.</CENTER><BR>";
}

if( $_REQUEST[force] > 0 )
{
    $tribe = $db->Execute("SELECT * FROM $dbtables[tribes] WHERE tribeid = '$_SESSION[current_unit]'");
    $tribeinfo = $tribe->fields;
    if( $tribeinfo[curam] > 0 )
    {
        $available = $tribeinfo[curam] - $tribeinfo[slavepop];
        if( $_REQUEST[force] > $available )
        {
            $_REQUEST[force] = $available;
        }
        elseif( $_REQUEST[force] > 100 )
        {
            $_REQUEST[force] = 100;
        }
        if( $_REQUEST[horses] > 0 )
        {
            $horse = $db->Execute("SELECT * FROM $dbtables[livestock] "
                                 ."WHERE type = 'Horses' "
                                 ."AND tribeid = '$_SESSION[current_unit]'");
            $horseinfo = $horse->fields;
            if( $horseinfo[amount] < $_REQUEST[horses] )
            {
                $_REQUEST[horses] = $horseinfo[amount];
            }
            if( $_REQUEST[horses] > 100 )
            {
                $_REQUEST[horses] = 100;
            }
            if( $_REQUEST[horses] > $_REQUEST[force] )
            {
                $_REQUEST[horses] = $_REQUEST[force];
            }
            $db->Execute("UPDATE $dbtables[livestock] "
                        ."SET amount = amount - '$_REQUEST[horses]' "
                        ."WHERE type = 'Horses' "
                        ."AND tribeid = '$_SESSION[current_unit]'");
            $mounted = $_REQUEST[horses];
        }
        
        if( $_REQUEST[wagons] > 0 )
        {
            $wag = $db->Execute("SELECT * FROM $dbtables[products] "
                               ."WHERE tribeid = '$tribeinfo[goods_tribe]' "
                               ."AND long_name = 'wagon'");
            $waginfo = $wag->fields;
            $cat = $db->Execute("SELECT * FROM $dbtables[livestock] "
                               ."WHERE tribeid = '$tribeinfo[goods_tribe]' "
                               ."AND type = 'Cattle'");
            $catinfo = $cat->fields;
            $avail_livstock = 100 - $_REQUEST[horses];
            $needed_burdens = $_REQUEST[wagons] * 2;
            if( $needed_burdens > $avail_livstock )
            {
                $_REQUEST[wagons] = round( $avail_livstock / 2 );
            }
            $burdened = round( $catinfo[amount] / 2 );
            $avail_activ = $_REQUEST[force] - $_REQUEST[horses];
            if( $_REQUEST[wagons] > $avail_activ )
            {
                $_REQUEST[wagons] = $avail_activ;
            } 
            if( $_REQUEST[wagons] > $waginfo[amount] )
            {
                $_REQUEST[wagons] = $waginfo[amount];
            }
            if( $_REQUEST[wagons] > $burdened )
            {
                while( $_REQUEST[wagons] > $burdened )
                {
                    $_REQUEST[wagons] -= 1;
                }
            }
            $used_burden = $_REQUEST[wagons] * 2;
            $db->Execute("UPDATE $dbtables[livestock] "
                        ."SET amount = amount - $used_burden "
                        ."WHERE tribeid = '$tribeinfo[goods_tribe]' "
                        ."AND type = 'Cattle'");
            $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount - $_REQUEST[wagons] "
                        ."WHERE tribeid = '$tribeinfo[goods_tribe]' "
                        ."AND long_name = 'wagon'");
        }
        if( $_REQUEST[backpacks] > 0 )
        {
            $backs = $db->Execute("SELECT * FROM $dbtables[products] "
                                 ."WHERE tribeid = '$tribeinfo[goods_tribe]' "
                                 ."AND long_name = 'backpack'");
            $backpack = $backs->fields;
            if( $_REQUEST[backpacks] > $backpack[amount] )
            {
                $_REQUEST[backpacks] = $backpack[amount];
            }
            $avail_backs = $_REQUEST[force] - $_REQUEST[horses];
            if( $_REQUEST[backpacks] > $avail_backs )
            {
                $_REQUEST[backpacks] = $avail_backs;
            }
            $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount - '$_REQUEST[backpacks]' "
                        ."WHERE tribeid = '$tribeinfo[goods_tribe]' "
                        ."AND long_name = 'backpack'");
        }  
        if( $_REQUEST[saddlebags] > 0 )
        {
            $sad = $db->Execute("SELECT * FROM $dbtables[products] "
                               ."WHERE tribeid = '$tribeinfo[goods_tribe]' "
                               ."AND long_name = 'saddlebags'");
            $sadbag = $sad->fields;
            if( $_REQUEST[saddlebags] > $sadbag[amount] )
            {
                $_REQUEST[saddlebags] = $sadbag[amount];
            }
            $avail_mounts = 100 - ($_REQUEST[wagon] * 2);
            if( $_REQUEST[saddlebags] > $avail_mounts )
            {
                $_REQUEST[saddlebags] = $avail_mounts;
            }
            $db->Execute("UPDATE $dbtables[products] "
                        ."SET amount = amount - '$_REQUEST[saddlebags]' "
                        ."WHERE tribeid = '$tribeinfo[goods_tribe]' "
                        ."AND long_name = 'saddlebags'");
        }
        $already = $db->Execute("SELECT * FROM $dbtables[seeking] "
                               ."WHERE clanid = '$tribeinfo[clanid]' "
                               ."AND target = '$_REQUEST[target]'");
        if( $already->EOF )
        {
	    $db->Execute("INSERT INTO $dbtables[seeking] "
                        ."VALUES("
                        ."'',"
                        ."'$tribeinfo[clanid]',"
                        ."'$tribeinfo[tribeid]',"
                        ."'$_REQUEST[force]',"
                        ."'$mounted',"
                        ."'$_REQUEST[wagons]',"
                        ."'$used_burden',"
                        ."'$_REQUEST[backpacks]',"
                        ."'$_REQUEST[saddlebags]',"
                        ."'$_REQUEST[target]')");

		echo "<CENTER>$_REQUEST[force] seeker sent to find $_REQUEST[target].</CENTER><BR>";

		$db->Execute("UPDATE $dbtables[tribes] "
                        ."SET curam = curam - $_REQUEST[force] "
                        ."WHERE tribeid = '$_SESSION[current_unit]'");
        }
        else
        {
            $alreadythere = $already->fields;
            echo "<CENTER>$alreadythere[tribeid] is already seeking $_REQUEST[target].</CENTER><BR>";
        }
    }
}



if( ISSET( $_REQUEST[disband] ) )
{
    $dis = $db->Execute("SELECT * FROM $dbtables[seeking] "
                       ."WHERE id = '$_REQUEST[disband]' "
                       ."AND tribeid = '$_SESSION[current_unit]'");
    $disband = $dis->fields;
    $tr = $db->Execute("SELECT * FROM $dbtables[tribes] "
                      ."WHERE tribeid = '$disband[tribeid]'");
    $tribe = $tr->fields;

	echo "<CENTER>$disband[actives] actives returned to tribe $tribe[goods_tribe].";

	$db->Execute("UPDATE $dbtables[tribes] "
                ."SET curam = curam + '$disband[actives]' "
                ."WHERE tribeid = '$disband[tribeid]'");
    $db->Execute("UPDATE $dbtables[livestock] "
                ."SET amount = amount + '$disband[horses]' "
                ."WHERE type = 'Horses' "
                ."AND tribeid = '$tribe[goods_tribe]'");

	echo "<BR>$disband[horses] horses returned to its stables.";

	$cattle = $disband[wagons] * 2;
    $db->Execute("UPDATE $dbtables[livestock] "
                ."SET amount = amount + $cattle "
                ."WHERE type = 'Cattle' "
                ."AND tribeid = '$tribe[goods_tribe]'");

	echo "<BR>$cattle cattle returned to nearby pasture.";

	$db->Execute("UPDATE $dbtables[products] "
                ."SET amount = amount + $disband[wagons] "
                ."WHERE long_name = 'wagon' "
                ."AND tribeid = '$tribe[goods_tribe]'");

	echo "<BR>$disband[wagons] wagons returned to its teamsters house.";

	$db->Execute("UPDATE $dbtables[products] "
                ."SET amount = amount + $disband[backpacks] "
                ."WHERE long_name = 'backpack' "
                ."AND tribeid = '$tribe[goods_tribe]'");
    $db->Execute("UPDATE $dbtables[products] "
                ."SET amount = amount + $disband[saddlebags] "
                ."WHERE long_name = 'saddlebags' "
                ."AND tribeid = '$tribe[goods_tribe]'");

	echo "<BR>$disband[backpacks] backpacks and $disband[saddlebags] saddlebags returned to $tribe[goods_tribe] stores.</CENTER>";

	$db->Execute("DELETE FROM $dbtables[seeking] "
                ."WHERE id = '$_REQUEST[disband]' "
                ."AND tribeid = '$_SESSION[current_unit]'");
}

echo "<TABLE BORDER=0 ALIGN=CENTER WIDTH=80%><TR CLASS=color_header><TD colspan=8>";
echo "<CENTER>Current seeking assignments</CENTER></TD></TR>";

$tgar = $db->Execute("SELECT * FROM $dbtables[seeking] "
					."WHERE tribeid = '$_SESSION[current_unit]'");
echo "<TR CLASS=color_header ALIGN=CENTER><TD>ID</TD>";
echo "<TD>Seekers</TD><TD>Horses</TD><TD>Wagons (Cattle)</TD>";
echo "<TD>Backpacks</TD><TD>Saddlebags</TD><TD>Objective</TD><TD></TD>";

$linecolor = $color_line2;
if( $tgar->EOF )
{
	echo "<TR CLASS=color_row1 ALIGN=CENTER><TD COLSPAN=7><CENTER>None</CENTER></TD></TR></TABLE>";
}
$r = 0;
while( !$tgar->EOF )
{
	$rc = $r % 2;
	$r++;
	$tgarinfo = $tgar->fields;
	echo "<TR CLASS=color_row$rc ALIGN=CENTER><TD>$tgarinfo[id]</TD>";
	echo "<TD>$tgarinfo[actives]</TD><TD>$tgarinfo[horses]</TD>";
	echo "<TD>$tgarinfo[wagons] ($tgarinfo[burden_beasts])</TD>";
	echo "<TD>$tgarinfo[backpacks]</TD><TD>$tgarinfo[saddlebags]</TD>";
	echo "<TD>$tgarinfo[target]</TD>"
		."<FORM METHOD=POST ACTION=seeking.php>"
		."<TD>"
		."<INPUT TYPE=HIDDEN NAME=disband VALUE=\"$tgarinfo[id]\">"
		."<INPUT TYPE=SUBMIT VALUE=Disband>"
		."</TD>"
		."</FORM>"
		."</TR>";
	$tgar->MoveNext();
}
echo "</TABLE>";

page_footer();
?> 
