<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: config.php
error_reporting  (E_ERROR | E_WARNING | E_PARSE | !E_NOTICE);

// This may have to be forceably set if we want to use the header() function
// ini_set("output_buffering", "On");

// The ini configurations that affect the development environment should be set explicitly

// Setting register_globals off may mean that we need to explicitly define global variables
// as global inside any included files.
ini_set("register_globals", "Off");


// Path on the filesystem where the Tribe Strive files will reside
// You MUST set this variable correctly to reflect your local game path

$game_root = "/var/www/freetribes/";
$gameroot = $game_root;
include($game_root."config_local.php");


include_once($game_root."global_funcs.php");

include($game_root."$ADOdbpath" . "/adodb.inc.php");

/* GUI settings */
$color_header = "#af770e";
$color_table  = "#997637";
$color_line1  = "#ad8b4c";
$color_line2  = "#BD9B5C";
$color_bg     = "#408C57";

$theme_default = "Original";

/* Localization (regional) settings */
$local_number_dec_point = ".";
$local_number_thousands_sep = ",";
$language = "english";
$title = "FreeTribes";
$version = "v 0.9.4-Alpha";

/* game variables */
$ip = getenv("REMOTE_ADDR");

// The last numeral in the version string is the gui code base level.
// It is liable to be independent of the first four numerals that J sets as the main release level.
   $game_version            = "v.00.09.4";
   $maxlen_password         = 16;
   $server_closed           = false;       // true = block logins but not new account creation
   $account_creation_closed = false;       // true = block new account creation
   $game_debug              = false;        // true = turns on debug logger
   $game_debug_move         = false;       // true = turns on the movement debugger
   $game_debug_xfer         = false;        // true = turns on the transfer debugger
   $game_skill_debug        = false;       // true = turns on the skill attempts debugger
   $game_pop_debug          = false;       // true = turns on the population debugger

   $default_lang            = 'english';

   $display_password        = false;       // If true, will display password on signup screen.
   $sched_type              = 0;           // 0 = Cron based, 1 = player triggered.


///* map setting */
$map_width = 64;


?>