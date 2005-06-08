<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: credits.php

session_start();
header("Cache-control: private");

include("config.php");

page_header("Credits and Acknowledgements");

connectdb();

echo "<CENTER>";

navbar_help();
echo "<BR><BR>";
echo "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=\"70%\"><TR><TD>";

echo "<DT><FONT COLOR=WHITE>Acknowledgements</FONT>:</DT>";
echo "<DD> FreeTribes  is an MMORPG coded by Joel Gridley (A.K.A Jarmaug) <a href='http://tribestrive.net'> TribeStrive Original Server</a><BR></DD>";
echo "<BR> The Current FreeTribes code is a stable release version and a fork from the original TribeStrive, with several enhancements and optimizations by <a href='http://www.crazybri.net/'>Brian Gustin</a> (AKA Trukfixer)<br>";
echo "<BR> Acknowledgements go out to Tribes PBEM game from which the concept of this game was derived.";
echo "<BR> Several portions and snippets of this code are borrowed from <a href='http://kabal-invasion.com'>TKI- The Kabal Invasion</a> and <a href='http://sourceforge.net/projects/jompt/>JOMPT</a> Projects.";
echo "<BR> Acknowledgements to <a href='blacknova.net'>BlackNova Traders</a> which is the base code that this game started from...";
echo "<BR> Special thanks to <a href=\"http://developer.berlios.de\" title=\"BerliOS Developer\"> <img src=\"http://developer.berlios.de/bslogo.php?group_id=3607\" width=\"124px\" height=\"32px\" border=\"0\" alt=\"BerliOS Developer Logo\"></a> for providing Project space.";



echo "</TD></TR></TABLE><BR>";

navbar_help();

if( $_SESSION[clanid] )
{
    TEXT_GOTOMAIN();
}
echo "</CENTER>";

page_footer();

?>
