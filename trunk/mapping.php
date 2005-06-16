<?php
session_start();
header("Cache-control: private");
include("config.php");
$time_start = getmicrotime();

page_header("Known World");

connectdb();

echo "Click <A HREF=bigmap.php>here</A> to view the large map. (5 views per turn)<BR>";


$tribeid = $_SESSION['current_unit'];
$orighex = $db->Execute("SELECT * FROM $dbtables[tribes] "
                        ."WHERE tribeid = '$tribeid'");
 db_op_result($orighex,__LINE__,__FILE__);
$orig = $orighex->fields;
$tribe_position = $orig['hex_id'];

if( !ISSET( $_REQUEST['target'] ) )
{
    $tribe_hex = $db->Execute("SELECT hex_id FROM $dbtables[tribes] "
                              ."WHERE tribeid = '$tribeid'");
    db_op_result($tribe_hex,__LINE__,__FILE__);
    $tribehex = $tribe_hex->fields;
}
else
{
    $tribehex['hex_id'] = $_REQUEST['target'];
}

$startrow = $tribehex['hex_id'] - (($map_width * 10) + 10);
$endrow = $startrow + 22;

for ($i=0; $i<21; $i++)
{
    ${"res$i"} = $db->Execute("SELECT hex_id, res_type, terrain "
                              ."FROM $dbtables[hexes] "
                              ."WHERE hex_id > '$startrow' "
                              ."AND hex_id < '$endrow'");
    db_op_result(${"res$i"},__LINE__,__FILE__);
    $startrow += $map_width;
    $endrow = $startrow + 22;
}


function get_direction($currenthex, $tribehex)
{
    //Ported from Java code contributed by Eric Hartmann (ekhartmann@sbcglobal.net)
    $tile = $currenthex;
    $tile2 = $tribehex;
    $MapWidth = 64;
    $MapHeight = 64;
    $yDirection = 'North';
    $xDirection = 'East';
    $y2Direction = 'North';
    $x2Direction = 'East';
    $ycoord = 0;
    $xcoord = 0;
    $y2coord = 0;
    $x2coord = 0;
    $ycoord = ceil( ( $MapHeight / 2 ) - ( ( $tile - 1 ) / $MapWidth ) );
    $xcoord = ( ( $tile - 1 ) % $MapWidth ) - ( $MapWidth / 2 );
    $y2coord = ceil( ( $MapHeight / 2 ) - ( ( $tile2 - 1 ) / $MapWidth ) );
    $x2coord = ceil( ( $tile2 - 1 ) % $MapWidth ) - ( $MapWidth / 2 );

    if( $ycoord >= 0 )
    {
        $yDirection = 'North';
    }
    else
    {
        $yDirection = 'South';
    }
    if( $xcoord >= 0 )
    {
        $xDirection = 'East';
    }
    else
    {
        $xDirection = 'West';
    }

    if( $y2coord >= 0 )
    {
        $y2Direction = 'North';
    }
    else
    {
        $y2Direction = 'South';
    }
    if( $x2coord >= 0 )
    {
        $x2Direction = 'East';
    }
    else
    {
        $x2Direction = 'West';
    }

    $xAbs = $xcoord >= 0 ? $xcoord : -$xcoord;
    $yAbs = $ycoord >= 0 ? $ycoord : -$ycoord;
    $x2Abs = $x2coord >= 0 ? $x2coord : -$x2coord;
    $y2Abs = $y2coord >= 0 ? $y2coord : -$y2coord;

    $xdelta = 0;
    $ydelta = 0;
    $xdelta = $xcoord - $x2coord;
    $ydelta = $ycoord - $y2coord;
    $ydeltaDirection = 'North';
    $xdeltaDirection = 'East';
    if( $ydelta >= 0 )
    {
        $ydeltaDirection = 'North';
    }
    else
    {
        $ydeltaDirection = 'South';
    }
    if( $xdelta >= 0 )
    {
        $xdeltaDirection = 'East';
    }
    else
    {
        $xdeltaDirection = 'West';
    }
    $xdeltaAbs = $xdelta >= 0 ? $xdelta : -$xdelta;
    $ydeltaAbs = $ydelta >= 0 ? $ydelta : -$ydelta;

    echo 'Your tribe is ';
    if( $ydeltaAbs <> 0 )
    {
        echo "$ydeltaAbs $ydeltaDirection";
    }
    if( $ydeltaAbs <> 0 && $xdeltaAbs <> 0 )
    {
        echo ' and ';
    }
    if( $xdeltaAbs <> 0 )
    {
        echo "$xdeltaAbs $xdeltaDirection ";
    }
    if( $ydeltaAbs <> 0 | $xdeltaAbs <> 0 )
    {
        echo " of $tile2.";
    }
    else
    {
        echo " at $tile2.";
    }

}


echo '<TABLE border=0 cellpadding=0><TR ALIGN=CENTER><TD>&nbsp;</TD><TD><FONT COLOR=WHITE>';
get_direction( $orig[hex_id], $tribehex[hex_id] );
echo '</FONT></TD><TD>&nbsp;</TD></TR>';
echo '<TR ALIGN=CENTER><TD>&nbsp;</TD><TD>';
echo '<FORM ACTION=mapping.php METHOD=POST>';
echo "Which hex do you wish to center on?<BR>";
echo "<INPUT CLASS=edit_area TYPE=TEXT NAME=target SIZE=7 WIDTH=7 VALUE='".$_REQUEST['hex_id']."'>";
echo "<INPUT TYPE=SUBMIT VALUE=SUBMIT>\n";
echo '</TD><TD>&nbsp;</TR>';
echo "<TR><TD>&nbsp;</TD><TD ALIGN=CENTER>Centered on: $tribehex[hex_id]</TD><TD>&nbsp;</TD></TR>\n";

echo '<TR><TD VALIGN=TOP>';

include("gui/table_map_key.php");

;
echo "</TD><TD>";

echo "<TABLE BORDER=0 CELLPADDING=1 CELLSPACING=0 BGCOLOR=BLACK>\n";

$clanid = $_SESSION['clanid'];
for ($i=0; $i<21; $i++)
{
    echo"<TR>";
    $r = 0;
    while(!${"res$i"}->EOF && $r < 22 )
    {
        $row = ${"res$i"}->fields;
        $terrain = $row['terrain'];
        $res_type = $row['res_type'];
        $alt=$row['hex_id'];
                $firstcompare = ( $row['hex_id'] / $map_width );
                $secondcompare = round( $row['hex_id'] / $map_width );
                $clanmap = "clanid_" . $clanid;
        $map = $db->Execute("SELECT hex_id, $clanmap FROM $dbtables[mapping] "
                            ."WHERE hex_id = '$alt' "
                                    ."AND $clanmap > '0'");
        db_op_result($map,__LINE__,__FILE__);
        $mapping = $map->fields;
                if( $mapping[$clanmap] != '1' )
                {
                    $port = $terrain . $res_type;
                }
                elseif( $mapping[$clanmap] == '1' )
                {
                    $port = $terrain;
                }
                if( $mapping['hex_id'] == $_SESSION['hex_id'] )
        {
                        $highlight = " BGCOLOR=WHITE";
        }
        else
        {
            $highlight = "";
        }
        if(ISSET($mapping['hex_id']))
        {
            $tile = "<TD$highlight><A HREF=mapping.php?target=$alt><IMG SRC=\"images/" . $port . ".png\" TITLE=$alt BORDER=0></A></TD>";
        }
        else
        {
            $port="unknown";
            $tile = "<TD><img src=images/" . $port . ".png border=0></TD>\n";
        }
                if( $firstcompare == $secondcompare )
                {
                        while( $r < 22 )
                        {
                            $tile .= "<TD$highlight><IMG SRC=\"images/blank.png\" TITLE=\"Border\" BORDER=0></TD>";
                            $r++;
                        }
                }
        echo $tile;
        $r++;
        ${"res$i"}->Movenext();
    }
        if( $r == '0' || $r == '22' )
        {
            $image = "<IMG SRC=\"images/blank.png\" TITLE=\"Border\" BORDER=0>";
        }
    echo "<TD>$image</TD></TR>";
}

echo "</TABLE>";
echo "</TD><TD VALIGN=TOP ALIGN=CENTER>";

$pan = ( $map_width * 5 );

$west_targ = $tribehex['hex_id'] - 5;
$east_targ = $tribehex['hex_id'] + 5;
$north_targ = $tribehex['hex_id'] - $pan;
$south_targ = $tribehex['hex_id'] + $pan;

include("gui/table_map_nav.php");

include("gui/table_map_ore_key.php");


echo "</TD></TR>\n";


echo "</TD></TR>";
echo "</TABLE>\n";
$time_end = getmicrotime();
$time = $time_end - $time_start;

page_footer();
?>
