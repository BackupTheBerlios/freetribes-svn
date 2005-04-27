<?
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: translate.php
session_start();
header("Cache-control: private");


$module = $_REQUEST[menu];


if( !$_REQUEST[translate] )
{
    echo "<CENTER><TABLE BORDER=0><TR><FORM ACTION=translate.php METHOD=POST>";
    echo "<TD><INPUT TYPE=TEXT NAME=email VALUE=\"\"></INPUT></TD><TD><INPUT TYPE=SUBMIT NAME=translate VALUE=Translate></TD></TR>";
    echo "</FORM></TABLE></CENTER>";
}
else
{
    $translated = strtolower($_REQUEST[email]);
    $translated1 = '';
    for( $i = 0, $j = strlen($translated); $i < $j; $i++ )
    {
        if( $translated[$i] == '@' )
        {
            $translated1 .= '%40';
        }
        elseif( $translated[$i] == '.' )
        {
            $translated1 .= '%2E';
        }
        elseif( $translated[$i] == 'a' )
        {
            $translated1 .= '%61';
        }
        elseif( $translated[$i] == 'b' )
        {
            $translated1 .= '%62';
        }
        elseif( $translated[$i] == 'c' )
        {
             $translated1 .= '%63';
        }
        elseif( $translated[$i] == 'd' )
        {
             $translated1 .= '%64';
        }
        elseif( $translated[$i] == 'e' )
        {
             $translated1 .= '%65';
        }
        elseif( $translated[$i] == 'f' )
        {
             $translated1 .= '%66';
        }
        elseif( $translated[$i] == 'g' )
        {
             $translated1 .= '%67';
        }
        elseif( $translated[$i] == 'h' )
        {
             $translated1 .= '%68';
        }
        elseif( $translated[$i] == 'i' )
        {
             $translated1 .= '%69';
        }
        elseif( $translated[$i] == 'j' )
        {
             $translated1 .= '%6A';
        }
        elseif( $translated[$i] == 'k' )
        {
            $translated1 .= '%6B';
        }
        elseif( $translated[$i] == 'l' )
        {
            $translated1 .= '%6C';
        }
        elseif( $translated[$i] == 'm' )
        {
            $translated1 .= '%6D';
        }
        elseif( $translated[$i] == 'n' )
        {
            $translated1 .= '%6E';
        }
        elseif( $translated[$i] == 'o' )
        {
            $translated1 .= '%6F';
        }
        elseif( $translated[$i] == 'p' )
        {
            $translated1 .= '%70';
        }
        elseif( $translated[$i] == 'q' )
        {
            $translated1 .= '%71'; 
        }
        elseif( $translated[$i] == 'r' )
        {
            $translated1 .= '%72';
        }
        elseif( $translated[$i] == 's' )
        {
            $translated1 .= '%73';
        }
        elseif( $translated[$i] == 't' )
        {
            $translated1 .= '%74';
        }
        elseif( $translated[$i] == 'u' )
        {
            $translated1 .= '%75';
        }
        elseif( $translated[$i] == 'v' )
        {
            $translated1 .= '%76';
        }
        elseif( $translated[$i] == 'w' )
        {
            $translated1 .= '%77';
        }
        elseif( $translated[$i] == 'x' )
        {
            $translated1 .= '%78';
        }
        elseif( $translated[$i] == 'y' )
        {
            $translated1 .= '%79';
        }
        elseif( $translated[$i] == 'z' )
        {
            $translated1 .= '%7A';
        }
        elseif( $translated[$i] == '1' )
        {
            $translated1 .= '%31';
        }
        elseif( $translated[$i] == '2' )
        {
            $translated1 .= '%32';
        }
        elseif( $translated[$i] == '3' )
        {
            $translated1 .= '%33';
        }
        elseif( $translated[$i] == '4' )
        {
            $translated1 .= '%34';
        }
        elseif( $translated[$i] == '5' )
        {
            $translated1 .= '%35';
        }
        elseif( $translated[$i] == '6' )
        {
            $translated1 .= '%36';
        }
        elseif( $translated[$i] == '7' )
        {
            $translated1 .= '%37';
        }
        elseif( $translated[$i] == '8' )
        {
            $translated1 .= '%38';
        }
        elseif( $translated[$i] == '9' )
        {
            $translated1 .= '%39';
        }
        elseif( $translated[$i] == '0' )
        {
            $translated1 .= '%30';
        }
        elseif( $translated[$i] == '-' )
        {
            $translated1 .= '%2D';
        }
        elseif( $translated[$i] == '_' )
        {
            $translated1 .= '%5F';
        } 
    }
    echo "<A href=mailto:$translated1>Test to see if it works!</A><BR>";
    echo "$translated1<BR>";
}

echo '<BR><P>';
?>
