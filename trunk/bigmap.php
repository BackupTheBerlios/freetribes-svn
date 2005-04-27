<?
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: bigmap.php

session_start();
header("Cache-control: private");
include("config.php");
$time_start = getmicrotime();

page_header("Known World");


if( !$_SESSION['clanid'] )
{
    echo 'You must <a href=index.php>login</a> before viewing this page.';
    die();
}

connectdb();

$view = $db->Execute("SELECT * FROM $dbtables[map_view] "
                    ."WHERE clanid = '$_SESSION[clanid]'");
$viewinfo = $view->fields;

if( !$view->EOF )
{
    if( $viewinfo[times] > 4 )
    {
        echo 'You are allowed only 5 views of the big map per turn.';
	die();
    }
    else
    {
        $db->Execute("UPDATE $dbtables[map_view] "
                    ."SET times = times + 1 "
                    ."WHERE clanid = '$_SESSION[clanid]'");
    }
}
else
{
    $db->Execute("INSERT INTO $dbtables[map_view] "
                ."VALUES('$_SESSION[clanid]','1')");
}
          
$result = $db->Execute("SELECT hex_id, res_type, terrain FROM $dbtables[hexes] "
                      ."ORDER BY hex_id ASC");
$row = $result->fields;

echo '<TABLE border=0 cellpadding=0 bgcolor=black>';

while( !$result->EOF )
{
    $i = 0;
    while( $i < 64 )
    {
        $clanmap = "clanid_" . $_SESSION[clanid];
        $here = $db->Execute("SELECT * FROM $dbtables[mapping] "
                            ."WHERE hex_id = $row[hex_id] "
                            ."AND `$clanmap` > '0'");
        $hereres = $here->fields;
        if( !$here->EOF )
        {
            if( $hereres[$clanmap] != '1' )
            {
                $port = $row[terrain] . $row[res_type];
            }
            else
            {
                $port = $row[terrain];
            }
            $alt = $row[hex_id];
            $tile = "<TD><img src=images/$port.png title=$alt border=0></TD>";
        }
        else
        { 
            $tile = '<TD><IMG SRC=images/unknown.png></TD>';
        }
        echo $tile;
        $result->Movenext();
        $row = $result->fields;
        $i++;
    }
    echo '</TR>';
}




echo '</TABLE>';
echo '<BR><BR><P>';
echo '<TABLE border=1 cellpadding=0>';
echo '<TR><TD colspan=2 align=center> Map KEY </TD></TR>';
echo '<TR><TD><img src=images/gh.png></TD><TD>Grassy Hills</TD></TR>';
echo '<TR><TD><img src=images/df.png></TD><TD>Deciduous Forest</TD></TR>';
echo '<TR><TD><img src=images/dh.png></TD><TD>Deciduous Hills</TD></TR>';
echo '<TR><TD><img src=images/cf.png></TD><TD>Coniferous Forest</TD></TR>';
echo '<TR><TD><img src=images/ch.png></TD><TD>Coniferous Hills</TD></TR>';
echo '<TR><TD><img src=images/lcm.png></TD><TD>Low Coniferous Mountains</TD></TR>';
echo '<TR><TD><img src=images/jg.png></TD><TD>Jungle</TD></TR>';
echo '<TR><TD><img src=images/jh.png></TD><TD>Jungle Hills</TD></TR>';
echo '<TR><TD><img src=images/ljm.png></TD><TD>Low Jungle Mountains</TD></TR>';
echo '<TR><TD><img src=images/sw.png></TD><TD>Swamps</TD></TR>';
echo '<TR><TD><img src=images/hsm.png></TD><TD>High Snowy Mountains</TD></TR>';
echo '<TR><TD><img src=images/tu.png></TD><TD>Tundra</TD></TR>';
echo '<TR><TD><img src=images/de.png></TD><TD>Desert</TD></TR>';
echo '<TR><TD>&nbsp;</TD><TD>&nbsp;</TD></TR>';
echo '<TR><TD><img src=images/l.png></TD><TD>Lake</TD></TR>';
echo '<TR><TD><img src=images/o.png></TD><TD>Ocean</TD></TR>';
echo '<TR><TD>&nbsp;</TD><TD>&nbsp;</TD></TR>';
echo '<TR><TD><img src=images/unknown.png></TD><TD>Unexplored</TD></TR>';
echo '</TABLE>';

include('footer.php');
?> 
