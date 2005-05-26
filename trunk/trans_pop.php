<?php
if( !ISSET($_SESSION['username']) )
{
    echo 'You must <a href=index.php>log in</a> to view this page.<br>';
    TEXT_GOTOLOGIN();
    die();
}
//HOLEEE Crap! this code badly needs a total overhaul!
//not gonna right now . just fix the remaining ones and
$from_tribe = $_POST['from_tribe'];
$to_tribe = $_POST['to_tribe'];
$unit = $_POST['unit'];
$clanid = $_SESSION['clanid'];
$tribeid = $_SESSION['current_unit'];

$tribe = $db->Execute("SELECT * FROM $dbtables[tribes] WHERE tribeid = '$from_tribe'");
$tribeinfo = $tribe->fields;

$xfer = $db->Execute("SELECT * FROM $dbtables[poptrans] WHERE tribeid = '$from_tribe'");
$xpop = $xfer->fields;

$skill = $db->Execute("SELECT * FROM $dbtables[skills] WHERE tribeid = '$from_tribe' AND abbr = 'adm'");
$skillinfo = $skill->fields;
$maxtrans = (5 + $skillinfo['level']) * .01;
$maxpoptrans = round($tribeinfo['totalpop'] * $maxtrans);
$maxpoptrans2 = $maxpoptrans;

if($maxpoptrans < 100 && $tribeinfo['totalpop'] >= 100 )
{
    $maxpoptrans = 100;
}


$activepop   = $_POST['activepop'];
$inactivepop = $_POST['inactivepop'];
$slavepop    = $_POST['slavepop'];
$specialpop  = $_POST['specialpop'];

$number      = $activepop + $inactivepop;

$feedback = "";

if( $activepop > 0  | $inactivepop > 0  | $slavepop  > 0 | $specialpop  > 0 )
{

    if( $_POST['activepop'] < 0 )
    {
        $_POST['activepop'] = 0;
    }
    if( $_POST['inactivepop'] < 0 )
    {
        $_POST['inactivepop'] = 0;
    }
    if( $_POST['slavepop'] < 0 )
    {
        $_POST['slavepop'] = 0;
    }
    if( $_POST['specialpop'] < 0 )
    {
        $_POST['specialpop'] = 0;
    }
    $allow = $db->Execute("SELECT * FROM $dbtables[poptrans] WHERE tribeid = '$from_tribe' AND actives = '$to_tribe'");
    $allowinfo = $allow->fields;
    $number2 = $allowinfo['number'] + $_POST['activepop'] + $_POST['inactivepop'];
    $slv = $db->Execute("SELECT * FROM $dbtables[tribes] WHERE tribeid = '$from_tribe'");
    $slaveinfo = $slv->fields;
    if( $_POST['activepop'] > $slaveinfo['activepop'] )
    {
        $_POST['activepop'] = $slaveinfo['activepop'];
    }
    if( $_POST['inactivepop'] > $slaveinfo['inactivepop'] )
    {
        $_POST['inactivepop'] = $slaveinfo['inactivepop'];
    }
    if( $_POST['slavepop'] > $slaveinfo['slavepop'] )
    {
        $_POST['slavepop'] = $slaveinfo['slavepop'];
    }
    if( $_POST['specialpop'] > $slaveinfo['specialpop'] )
    {
        $_POST['specialpop'] = $slaveinfo['specialpop'];
    }

    if( $allow->EOF )
    {
        if( $number <= $maxpoptrans && $slaveinfo['slavepop'] >= $_POST['slavepop'] )
        {
            $db->Execute("UPDATE $dbtables[tribes] "
                        ."SET activepop = activepop + '$_POST[activepop]', "
                        ."inactivepop = inactivepop + '$_POST[inactivepop]', "
                        ."slavepop = slavepop + '$_POST[slavepop]', "
                        ."specialpop = specialpop + '$_POST[specialpop]' "
                        ."WHERE tribeid = '$to_tribe'");
            $db->Execute("UPDATE $dbtables[tribes] "
                        ."SET activepop = activepop - '$_POST[activepop]', "
                        ."inactivepop = inactivepop - '$_POST[inactivepop]', "
                        ."slavepop = slavepop - '$_POST[slavepop]', "
                        ."specialpop = specialpop - '$_POST[specialpop]' "
                        ."WHERE tribeid = '$from_tribe'");
            $db->Execute("INSERT INTO $dbtables[poptrans] "
                        ."VALUES("
                        ."'$from_tribe',"
                        ."'$number',"
                        ."'$to_tribe',"
                        ."'$to_tribe')");
            echo "$_POST[activepop] actives, $_POST[inactivepop] inactives, ";
            echo "$_POST[slavepop] transferred to $to_tribe.<BR>";
            $db->Execute("INSERT INTO $dbtables[subtribe_id] "
                        ."(`unique_id`) "
                        ."values("
                        ."'$_POST[unique]')");
            include("weight.php");
        }
    }
    elseif( $maxpoptrans2 >= $number2 && $slaveinfo['slavepop'] >= $_POST['slavepop'] )
    {
        $db->Execute("UPDATE $dbtables[tribes] "
                    ."SET activepop = activepop + '$_POST[activepop]', "
                    ."inactivepop = inactivepop + '$_POST[inactivepop]', "
                    ."slavepop = slavepop + '$_POST[slavepop]', "
                    ."specialpop = specialpop + '$_POST[specialpop]' "
                    ."WHERE tribeid = '$to_tribe'");
        $db->Execute("UPDATE $dbtables[tribes] "
                    ."SET activepop = activepop - '$_POST[activepop]', "
                    ."inactivepop = inactivepop - '$_POST[inactivepop]', "
                    ."slavepop = slavepop - '$_POST[slavepop]', "
                    ."specialpop = specialpop - '$_POST[specialpop]' "
                    ."WHERE tribeid = '$from_tribe'");
        $db->Execute("UPDATE $dbtables[poptrans] "
                    ."set number = number + $number "
                    ."WHERE tribeid = '$from_tribe'");
        $db->Execute("INSERT INTO $dbtables[subtribe_id] "
                    ."(`unique_id`) "
                    ."values("
                    ."'$_POST[unique]')");

        $feedback = "$_POST[activepop] actives, $_POST[inactivepop] inactives, $_POST[slavepop] transferred to $to_tribe.";

        include("weight.php");

    }
    elseif( $number2 > $maxpoptrans  | ($number2 + $number) > $maxpoptrans )
    {
        $feedback = "You cannot transfer that many this at this time.";
    }

}


if(empty($feedback))
{
$unique = uniqid(microtime(),1);
echo "<INPUT TYPE=HIDDEN NAME=unique VALUE='$unique'>"
    ."<INPUT TYPE=HIDDEN NAME=ALREADY VALUE=1>";

echo "<TABLE BGCOLOR=\"$color_header\" BORDER=0 CELLSPACING=0 CELLPADDING=4 WIDTH=\"100%\">";
echo "<TR BGCOLOR=\"$color_header\" WIDTH=\"100%\">"
    ."<TD COLSPAN=4>"
    ."<FONT CLASS=page_subtitle>Population Available ($from_tribe)</FONT>"
    ."</TD>"
    ."<TD COLSPAN=2 VALIGN=TOP ALIGN=RIGHT>"
    ."Max. Transfer=$maxpoptrans"
    ."</TD>"
    ."</TR>";
echo "<TR BGCOLOR=\"$color_header\" WIDTH=\"100%\">"
    ."<TD COLSPAN=6>&nbsp;</TD>"
    ."</TR>";
echo "<TR BGCOLOR=$color_header ALIGN=CENTER>"
    ."<TD COLSPAN=2 ALIGN=LEFT>&nbsp;</TD>"
    ."<TD>"
    ."<INPUT CLASS=edit_area TYPE=TEXT SIZE=6 WIDTH=6 MAXLENGTH=6 NAME=activepop VALUE=0>"
    ."</TD>"
    ."<TD>"
    ."<INPUT CLASS=edit_area TYPE=TEXT SIZE=6 WIDTH=6 MAXLENGTH=6 NAME=inactivepop VALUE=0>"
    ."</TD>"
    ."<TD>"
    ."<INPUT CLASS=edit_area TYPE=TEXT SIZE=6 WIDTH=6 MAXLENGTH=6 NAME=slavepop VALUE=0>"
    ."</TD>"
    ."<TD>"
    ."<INPUT CLASS=edit_area TYPE=TEXT SIZE=6 WIDTH=6 MAXLENGTH=6 NAME=specialpop VALUE=0>"
    ."</TD>"
    ."</TR>";
echo "<TR BGCOLOR=$color_header ALIGN=CENTER>"
    ."<TD>Tribe (Max):</TD>"
    ."<TD>Clan</TD>"
    ."<TD>Actives</TD>"
    ."<TD>Inactives</TD>"
    ."<TD>Slaves</TD>"
    ."<TD>Special Pop</TD>";
echo "<TR BGCOLOR=$color_table ALIGN=CENTER>"
    ."<TD>$tribeinfo[tribeid]</TD>"
    ."<TD>$tribeinfo[clanid]</TD>"
    ."<TD>$tribeinfo[activepop]</TD>"
    ."<TD>$tribeinfo[inactivepop]</TD>"
    ."<TD>$tribeinfo[slavepop]</TD>"
    ."<TD>$tribeinfo[specialpop]</TD>";

$clan = $db->Execute("SELECT * FROM $dbtables[tribes] "
                    ."WHERE tribeid <> '$from_tribe' "
                    ."AND hex_id = '$tribeinfo[hex_id]' "
                    ."ORDER BY tribeid ASC");
$n=0;
while( !$clan->EOF )
{
    $rc=$n % 2;
    $claninfo = $clan->fields;
    $allow = $db->Execute("SELECT * FROM $dbtables[poptrans] "
                         ."WHERE tribeid = '$from_tribe' "
                         ."AND actives = '$claninfo[tribeid]'");
    $allowinfo = $allow->fields;
    if( $allowinfo['number'] )
    {
        $maxpoptrans = $maxpoptrans - $allowinfo['number'];
    }

    if( $claninfo['clanid'] == $_SESSION['clanid'] )
    {
        echo "<TR CLASS=row_color$rc ALIGN=CENTER>"
            ."<TD>$claninfo[tribeid] ($maxpoptrans)</TD>"
            ."<TD>$claninfo[clanid]</TD>"
            ."<TD>$claninfo[activepop]</TD>"
            ."<TD>$claninfo[inactivepop]</TD>"
            ."<TD>$claninfo[slavepop]</TD>"
            ."<TD>$claninfo[specialpop]</TD>"
            ."</TR>";
    }
    else
    {
        $ally = $db->Execute("SELECT * FROM $dbtables[alliances] "
                            ."WHERE offerer_id = '$claninfo[clanid]' "
                            ."AND accept = 'Y' "
                            ."OR receipt_id = '$claninfo[clanid]' "
                            ."AND accept = 'Y'");
        echo "<TR CLASS=row_color$rc ALIGN=CENTER>"
            ."<TD>$claninfo[tribeid] ($maxpoptrans)</TD>"
            ."<TD>$claninfo[clanid]</TD>";
        if( $ally )
        {
            echo "<TD>$claninfo[activepop]</TD>"
                ."<TD>$claninfo[inactivepop]</TD>"
                ."<TD>$claninfo[slavepop]</TD>"
                ."<TD>$claninfo[specialpop]</TD>"
                ."</TR>";
        }
        else
        {
            echo "<TD>&nbsp;</TD>"
                ."<TD>&nbsp;</TD>"
                ."<TD>&nbsp;</TD>"
                ."<TD>&nbsp;</TD>"
                ."</TR>";
        }
    }
    $clan->MoveNext();
    $n++;
}
}
echo "<TR BGCOLOR=\"$color_header\">"
    ."<TD COLSPAN=6>"
    ."<FONT COLOR=WHITE>&nbsp;$feedback</font>"
    ."</TD>"
    ."</TR>"
    ."</TABLE>";

?>
