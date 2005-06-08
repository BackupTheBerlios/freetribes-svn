<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: footer.php
echo "<BR>";
echo "<TABLE WIDTH=\"100%\" border=0 cellspacing=0 cellpadding=0>\n";

global $page_name;
echo "<TR>"
    ."<TD ALIGN=CENTER>"
    ."<FONT CLASS=text_small>";
echo "<br>";
global $time_start, $game_version, $game_name, $game_url_path, $theme_default;
$time_end = getmicrotime();
$time = getmicrotime() - $time_start;
if (!ISSET($_SESSION['theme']))
{
    $_SESSION['theme'] = $theme_default;
}
echo "<TD ALIGN=CENTER>"
    ."<FONT CLASS=text_small><B>$game_name</B><BR>Tribe Strive $game_version</FONT>"
    ."<br><FONT CLASS=text_small>Served in $time seconds.</FONT>"
?>
</td></tr>
<tr>

<?php

echo "<TD ALIGN=CENTER colspan=\"2\">"
    ."<A HREF=\"credits.php\">"
    ."<FONT CLASS=text_small>Miscellaneous Credits</A>"

        ."</TD>"
    ."</TR>";

echo "</TABLE>";

echo "</BODY>";
echo "</HTML>";
die();
?>
