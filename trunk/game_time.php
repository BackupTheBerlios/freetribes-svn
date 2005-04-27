<?
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: game_time.php

require_once("config.php");
connectdb();
/////////////////////////////////Establish Game Time//////////////////////////////////////////
global $db, $dbtables;

$gy = $db->Execute("SELECT * FROM $dbtables[game_date] "
                  ."WHERE type = 'year'");
$year = $gy->fields;

$gtseason = $db->Execute("SELECT * FROM $dbtables[game_date] "
                        ."WHERE type = 'season'");
$gameseason = $gtseason->fields;

$gm = $db->execute("SELECT * FROM $dbtables[game_date] "
                  ."WHERE type = 'month'");
$month = $gm->fields;

$gd = $db->Execute("SELECT * FROM $dbtables[game_date] "
                  ."WHERE type = 'day'");
$day = $gd->fields;

$wtr = $db->Execute("SELECT * FROM $dbtables[weather] "
                   ."WHERE current_type = 'Y'");
$weather = $wtr->fields;

$stamp = date("Y-m-d H:i:s");

?>
