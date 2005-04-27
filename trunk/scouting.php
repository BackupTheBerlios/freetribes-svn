<?
session_start();
header("Cache-control: private");
include("config.php");
include("game_time.php");

page_header("Tribal Scouts");

connectdb();

$username = $_SESSION['username'];

echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0>"
	."<TR>"
	."<FORM ACTION=scouting.php METHOD=POST>"
	."<TD>"
	."<INPUT TYPE=HIDDEN NAME=orders VALUE=P>"
	."<INPUT TYPE=HIDDEN NAME=action VALUE=recruit>"
	."<INPUT TYPE=SUBMIT VALUE=\"Send Out Mappers\">"
	."&nbsp;</TD>"
	."</FORM>"
	."<!-- <FORM ACTION=scouting.php METHOD=POST> -->"
	."<TD>"
	."<!-- <INPUT TYPE=HIDDEN NAME=orders VALUE=L> -->"
	."<!-- <INPUT TYPE=HIDDEN NAME=action VALUE=recruit> -->"
	."<!-- <INPUT TYPE=SUBMIT VALUE=\"Send Out Locators\"> -->"
	."&nbsp;</TD>"
	."<!-- </FORM> -->"
        ."<!-- <FORM ACTION=scouting.php METHOD=POST> -->"
        ."<TD>"
        ."<!-- <INPUT TYPE=HIDDEN NAME=orders VALUE=M> -->"
        ."<!-- <INPUT TYPE=HIDDEN NAME=action VALUE=recruit> -->"
        ."<!-- <INPUT TYPE=SUBMIT VALUE=\"Send Out Prospectors\"> -->"
        ."</TD>"
        ."<!-- </FORM> -->"
	."</TR>"
	."</TABLE>";

echo "<CENTER><BR>"
	."<TABLE BORDER=0 WIDTH=80%>"
	."<TR CLASS=color_header ALIGN=CENTER>"
	."<TD COLSPAN=2>"
	."<A HREF=activities.php>Activities</A> | "
	."<A HREF=garrisons.php>Garrisons</A> | "
	."<A HREF=goodstribe.php>Change Goods Tribe</A>"
	."</TD>"
	."</TR>"
	."</TABLE>"
	."<BR>";


if( $_REQUEST[force] > 0 && $_REQUEST[direction] != 'all' )
{
    $mounted = 'N';
    $tribe = $db->Execute("SELECT * FROM $dbtables[tribes] "
                         ."WHERE tribeid = '$_SESSION[current_unit]'");
    $tribeinfo = $tribe->fields;
    if( $tribeinfo[curam] > 0 )
    {
        $available = $tribeinfo[curam] - $tribeinfo[slavepop];
        if( $_REQUEST[force] > $available )
        {
            $_REQUEST[force] = $available;
        }
        if( $_REQUEST[horses] )
        {
            $horse = $db->Execute("SELECT * FROM $dbtables[livestock] "
                                 ."WHERE type = 'Horses' "
                                 ."AND tribeid = '$_SESSION[current_unit]'");
            $horseinfo = $horse->fields;
            if( $horseinfo[amount] < $_REQUEST[force] )
            {
                $_REQUEST[force] = $horseinfo[amount];
            }
            $db->Execute("UPDATE $dbtables[livestock] "
                        ."SET amount = amount - '$_REQUEST[force]' "
                        ."WHERE type = 'Horses' "
                        ."AND tribeid = '$_SESSION[current_unit]'");
            $mounted = 'Y';

			$move_method = "riding";
        }
		else
		{
			$move_method = "sent";
		}
        $db->Execute("INSERT INTO $dbtables[scouts] "
                    ."VALUES("
                    ."'',"
                    ."'$tribeinfo[tribeid]',"
                    ."'$_REQUEST[force]',"
                    ."'$_REQUEST[direction]',"
                    ."'$mounted',"
                    ."'$_REQUEST[orders]')");
		if ($_REQUEST[orders]=='P')
		{
			$journey = "on patrol";
		}
                elseif( $_REQUEST[orders] == 'M' )
                {
                    $journey = "prospecting";
                }
		else
		{
			$journey = "to hunt";
		}

		echo "<CENTER>$_REQUEST[force] scouts $move_method out $journey.</CENTER><BR>";

        $db->Execute("UPDATE $dbtables[tribes] "
                    ."SET activepop = activepop - $_REQUEST[force], "
                    ."curam = curam - $_REQUEST[force] "
                    ."WHERE tribeid = '$_SESSION[current_unit]'");
        include("weight.php");
    }
}

if( $_REQUEST[force] > 0 && $_REQUEST[direction] == 'all' )
{
    $type = array('n','ne','e','se','s','sw','w','nw');
	$num_scouts = 0;
    foreach( $type as $dir )
    {
        $_REQUEST[direction] = $dir; 
        $mounted = 'N';
        $tribe = $db->Execute("SELECT * FROM $dbtables[tribes] "
                             ."WHERE tribeid = '$_SESSION[current_unit]'");
        $tribeinfo = $tribe->fields;
        if( $tribeinfo[curam] > 0 )
        {
            $available = $tribeinfo[curam] - $tribeinfo[slavepop];
            if( $_REQUEST[force] > $available )
            {
                $_REQUEST[force] = $available;
            }

			$num_scouts += $_REQUEST['force'];

            if( $_REQUEST[horses] )
            {
                $horse = $db->Execute("SELECT * FROM $dbtables[livestock] "
                                     ."WHERE type = 'Horses' "
                                     ."AND tribeid = '$_SESSION[current_unit]'");
                $horseinfo = $horse->fields;
                if( $horseinfo[amount] < $_REQUEST[force] )
                {
                    $_REQUEST[force] = $horseinfo[amount];
                }
                $db->Execute("UPDATE $dbtables[livestock] "
                            ."SET amount = amount - '$_REQUEST[force]' "
                            ."WHERE type = 'Horses' "
                            ."AND tribeid = '$_SESSION[current_unit]'");
                $mounted = 'Y';

				$move_method = "riding";
			}
			else
			{
				$move_method = "sent";
			}
            $db->Execute("INSERT INTO $dbtables[scouts] "
                        ."VALUES("
                        ."'',"
                        ."'$tribeinfo[tribeid]',"
                        ."'$_REQUEST[force]',"
                        ."'$_REQUEST[direction]',"
                        ."'$mounted',"
                        ."'$_REQUEST[orders]')");

            $db->Execute("UPDATE $dbtables[tribes] "
                        ."SET activepop = activepop - $_REQUEST[force], "
                        ."curam = curam - $_REQUEST[force] "
                        ."WHERE tribeid = '$_SESSION[current_unit]'");
            include("weight.php");
        }
    }

	if ($_REQUEST[orders]=='P')
	{
		$journey = "on patrol";
	}
        elseif( $_REQEUST[orders] == 'M' )
        {
            $journey = "prospecting";
        }
	else
	{
		$journey = "to hunt";
	}


	echo "$num_scouts scouts $move_method out $journey,<BR>$_REQUEST[force] in each direction.<P>";
}


if( ISSET( $_REQUEST[disband] ) )
{
    $dis = $db->Execute("SELECT * FROM $dbtables[scouts] "
                       ."WHERE scoutid = '$_REQUEST[disband]' "
                       ."AND tribeid = '$_SESSION[current_unit]'");
    $disband = $dis->fields;

	echo "<CENTER>$disband[actives] scouts from unit $_REQUEST[disband] recalled &amp; returned to the tribe";

	$db->Execute("UPDATE $dbtables[tribes] "
                ."SET activepop = activepop + '$disband[actives]', "
                ."maxam = maxam + '$disband[actives]' "
                ."WHERE tribeid = '$disband[tribeid]'");
    if( $disband[mounted] == 'Y' )
    {
        $db->Execute("UPDATE $dbtables[livestock] "
                    ."SET amount = amount + '$disband[actives]' "
                    ."WHERE type = 'Horses' "
                    ."AND tribeid = '$disband[tribeid]'");

		echo "<CENTER>and $disband[actives] horses now happily eating hay";

	}
	echo ".</CENTER><BR>";
    include("weight.php");
    $db->Execute("DELETE FROM $dbtables[scouts] "
                ."WHERE scoutid = '$_REQUEST[disband]' "
                ."AND tribeid = '$_SESSION[current_unit]'");
}
        

if( $_REQUEST[action] == 'recruit' )
{
    $linecolor = $color_line1;
    $hs = $db->Execute("SELECT * FROM $dbtables[livestock] "
                      ."WHERE tribeid = '$_SESSION[current_unit]' "
                      ."AND type = 'Horses'");
    $horseinfo = $hs->fields;
    $war = $db->Execute("SELECT * FROM $dbtables[tribes] "
                       ."WHERE tribeid = '$_SESSION[current_unit]'");
    $warinfo = $war->fields;

    echo "<TABLE BORDER=0>"
		."<FORM ACTION=scouting.php METHOD=POST>";

	$avail = $warinfo[curam] - $warinfo[slavepop];
    if( $avail < 0 )
    {
        $avail = 0;
    }

	
	echo "<TR CLASS=color_header>"
		."<TD COLSPAN=3>"
		."<CENTER>Recruiting Scouts"
		."</CENTER>"
		."</TD>"
		."</TR>"
		."<TR CLASS=color_row0>"
		."<TD>How many scouts?</TD>"
		."<TD><INPUT CLASS=edit_area TYPE=TEXT NAME=force SIZE=6 MAXSIZE=6></TD>"
		."<TD>$avail Actives available</TD>"
		."</TR>"
		."<TR CLASS=color_row1>"
		."<TD>Mounted:</TD>"
		."<TD>"
		."<INPUT TYPE=checkbox NAME=horses VALUE=1>&nbsp;Yes</TD>"
		."<TD>$horseinfo[amount] Horses available</TD>"
		."</TR>"
		."<TR CLASS=color_row0>"
		."<TD>Direction:</TD>"
		."<TD COLSPAN=2 ALIGN=CENTER>"
		."<SELECT NAME=direction>"
		."<OPTION VALUE=n>North</OPTION>"
		."<OPTION VALUE=ne>Northeast</OPTION>"
		."<OPTION VALUE=e>East</OPTION>"
		."<OPTION VALUE=se>Southeast</OPTION>"
		."<OPTION VALUE=s>South</OPTION>"
		."<OPTION VALUE=sw>Southwest</OPTION>"
		."<OPTION VALUE=w>West</OPTION>"
		."<OPTION VALUE=nw>Northwest</OPTION>"
		."<OPTION VALUE=all>All</OPTION>"
		."</SELECT>"
		."<INPUT TYPE=HIDDEN NAME=orders VALUE=$_REQUEST[orders]>"
		."</TD>"
		."</TR>"
		."<TR CLASS=color_header>"
		."<TD COLSPAN=3>"
		."<CENTER><INPUT TYPE=SUBMIT VALUE=RECRUIT>"
		."</CENTER>"
		."</TD>"
		."</TR>"
		."</FORM>"
		."</TABLE>"
		."<BR><CENTER>Duplicate Scouts will be ignored, please consolidate.</CENTER><BR>";
}



echo "<TABLE BORDER=0 ALIGN=CENTER WIDTH=80%>"
	."<TR CLASS=color_header>"
	."<TD colspan=6>"
	."<CENTER>Current scouting assignments</CENTER>"
	."</TD>"
	."</TR>";
$tgar = $db->Execute("SELECT * FROM $dbtables[scouts] "
                    ."WHERE tribeid = '$_SESSION[current_unit]'");
echo "<TR CLASS=color_header ALIGN=CENTER>"
	."<TD>ID</TD>"
	."<TD>Force Size</TD>"
	."<TD>Horses</TD>"
	."<TD>Direction</TD>"
	."<TD>Orders</TD>"
	."<TD></TD>";

if( $tgar->EOF )
{
    echo "<TR CLASS=color_row1 ALIGN=CENTER>"
	."<TD COLSPAN=6>"
	."<CENTER>None</CENTER>"
	."</TD>"
	."</TR>"
	."</TABLE>";
}


$r = 0;
while( !$tgar->EOF )
{
	$rc = $r % 2;
	$r++;

    $tgarinfo = $tgar->fields;
    if( $tgarinfo[direction] == 'n' )
    { 
        $direction = 'North'; 
    }
    if( $tgarinfo[direction] == 'ne' )
    { 
        $direction = 'Northeast'; 
    }
    if( $tgarinfo[direction] == 'e' )
    { 
        $direction = 'East'; 
    }
    if( $tgarinfo[direction] == 'se' )
    { 
        $direction = 'Southeast'; 
    }
    if( $tgarinfo[direction] == 's' )
    { 
        $direction = 'South'; 
    }
    if( $tgarinfo[direction] == 'sw' )
    {
        $direction = 'Southwest'; 
    }
    if( $tgarinfo[direction] == 'w' )
    { 
        $direction = 'West'; 
    }
    if( $tgarinfo[direction] == 'nw' )
    { 
        $direction = 'Northwest'; 
    }
    if( $tgarinfo[orders] == 'P' )
    { 
        $orders = 'Patrol'; 
    }
    if( $tgarinfo[orders] == 'M' )
    {
        $orders = 'Prospect';
    }
    if( $tgarinfo[orders] == 'L' )
    { 
        $orders = 'Locate'; 
    }
    echo "<TR CLASS=color_row$rc ALIGN=CENTER>";
    echo "<TD>$tgarinfo[scoutid]</TD>";
    echo "<TD>$tgarinfo[actives]</TD>";
    echo "<TD>$tgarinfo[mounted]</TD>";
    echo "<TD>$direction</TD>";
    echo "<TD>$orders</TD>"
		."<FORM METHOD=POST ACTION=scouting.php>"
		."<TD>"
		."<INPUT TYPE=HIDDEN NAME=disband VALUE=\"$tgarinfo[scoutid]\">"
		."<INPUT TYPE=SUBMIT VALUE=Disband>"
		."</TD>"
		."</FORM>"
		."</TR>";
    $tgar->MoveNext();
}
echo "</TABLE>";


page_footer();
?> 
