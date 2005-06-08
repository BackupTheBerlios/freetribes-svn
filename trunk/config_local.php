<?php
////////////////////////////////////////////////////////////////
//                                                            //
// IMPORTANT: You MUST set $gameroot at the top of CONFIG.PHP //
//                                                            //
////////////////////////////////////////////////////////////////

//this may be changed if you want to use an adodb instance from elsewhere
$ADOdbpath = "adodb";

// Domain & path of the game on your webserver (used to validate login cookie)
// This is the domain name part of the URL people enter to access your game.
// So if your game is at www.blah.com you would have:
// $gamedomain = "www.crblah.com";
// Do not enter slashes for $gamedomain or anything that would come after a slash
// if you get weird errors with cookies then make sure the game domain has TWO dots
// i.e. if you reside your game on http://www.blacknova.net put .blacknova.net as $gamedomain.
// If your game is on http://www.some.site.net put .some.site.net as your game domain.
// Do not put port numbers in $gamedomain.

$cookie_domain = ".domain.com";

// This should be the name or IP of the website that your game is running at
$game_url = "www.your-website.net";

// This is the trailing part of the URL, that is not part of the domain.
// If you enter www.blah.com/blacknova to access the game, you would leave the line as it is.
// If you do not need to specify TribeStrive, just enter a single slash eg:
// $gamepath = "/";
$game_url_path = "/freetribes/";
$gamepath = $game_url_path;
$gamedomain = $game_url_path;
// Hostname and port of the database server:
// These are defaults, you normally won't have to change them
//$dbhost = "localhost";
$dbhost = "localhost";

// Note : if you do not know the port, set this to "" for default. Ex, MySQL default is 3306
$dbport = "";

// Name of the SQL database:
$dbname = "db_name";

// Username and password to connect to the database:
$dbuname = "db_user";
$dbpass = "db_sql_password";

// Type of the SQL database. This can be anything supported by ADOdb. Here are a few:
//DO NOT CHANGE THIS VALUE. Only MySQL is supported at this time
$db_type = "mysql";

// Set this to 1 to use db persistent connections, 0 otherwise
$db_persistent = 0;//persisten connection scan cause server load issues on a busy site...

/* Table prefix for the database. If you want to run more than
one game of FT on the same database, or if the current table
names conflict with tables you already have in your db, you will
need to change this */
$db_prefix = "tstr__";

// Administrator's email:
// Be sure to change these. Don't leave them as is.
$admin_mail = "whoever@whatever.domain";

// Address the forum link, link's to:
$link_forums = "http://forums.crazybri.com";

// The name of the local game
$game_name = "FreeTribes Stable";


$theme_default = "Original";    // You can set this to any of the directory names in ./themes

// Themes are currently not completely implemented
// If you use Lagoon, please disable the other themes
// by moving them to a directory outwith ./themes

// You can change the basic look of the game's pages by typing in new values below
// Fairly soon the only variable here will be $theme_default

switch ($_SESSION['theme'])
{
    case "Lagoon":
        $color_bg     = "#60AEF8";        // Page background.            Dark Grey
        $color_header = "#0054E3";        // Used for most table titles. Bright Blue
        $color_table  = "#004E98";        // Main table colour           Dark Blue
        $color_line1  = "#206EB8";        // Alternating rows color      Mid Blue
        $color_line2  = "#408ED8";        // Alternating rows color      Light Blue
        break;
    default:
        $color_bg     = "#408C57";        // Page background.            Default: Dark Green
        $color_header = "#af770e";        // Used for most table titles. Default: Orange Brown
        $color_table  = "#997637";        // Main table colour           Default: Dark Brown
        $color_line1  = "#aa8748";        // Alternating rows color      Default: Mid Brown
        $color_line2  = "#Bb9859";        // Alternating rows color      Default: Light Brown
}




?>