<?php
////////////////////////////////////////////////////////////////
//                                                            //
// IMPORTANT: You MUST set $gameroot at the top of CONFIG.PHP //
//                                                            //
////////////////////////////////////////////////////////////////



// The ADOdb db module is now required to run TN. You
// can find it at http://php.weblogs.com/ADODB. Enter the
// path where it is installed here. I suggest simply putting
// every ADOdb file in a subdir of TN.
$ADOdbpath = "ADOdb";

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
$game_url_path = "/tribestrive/";
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
// "access" for MS Access databases. You need to create an ODBC DSN.
// "ado" for ADO databases
// "ibase" for Interbase 6 or earlier
// "borland_ibase" for Borland Interbase 6.5 or up
// "mssql" for Microsoft SQL
// "mysql" for MySQL
// "oci8" for Oracle8/9
// "odbc" for a generic ODBC database
// "postgres" for PostgreSQL ver < 7
// "postgres7" for PostgreSQL ver 7 and up
// "sybase" for a SyBase database
// NOTE: only mysql work as of right now, due to SQL compat code
$db_type = "mysql";

// Set this to 1 to use db persistent connections, 0 otherwise
$db_persistent = 0;//persisten connection scan cause server load issues on a busy site... 

/* Table prefix for the database. If you want to run more than
one game of TN on the same database, or if the current table
names conflict with tables you already have in your db, you will
need to change this */
$db_prefix = "tstr__";

// Administrator's email:
// Be sure to change these. Don't leave them as is.
$admin_mail = "whoever@whatever.domain";

// Address the forum link, link's to:
$link_forums = "http://forums.crazybri.com";

// The name of the local game
$game_name = "TribeStrive Stable";


$theme_default = "Original";	// You can set this to any of the directory names in ./themes

// Themes are currently not completely implemented
// If you use Lagoon, please disable the other themes
// by moving them to a directory outwith ./themes

// You can change the basic look of the game's pages by typing in new values below
// Fairly soon the only variable here will be $theme_default

switch ($_SESSION['theme'])
{
	case "Lagoon":
		$color_bg     = "#60AEF8";		// Page background.            Dark Grey
		$color_header = "#0054E3";		// Used for most table titles. Bright Blue
		$color_table  = "#004E98";		// Main table colour           Dark Blue
		$color_line1  = "#206EB8";		// Alternating rows color      Mid Blue
		$color_line2  = "#408ED8";		// Alternating rows color      Light Blue
		break;
	default:
		$color_bg     = "#408C57";		// Page background.            Default: Dark Green
		$color_header = "#af770e";		// Used for most table titles. Default: Orange Brown
		$color_table  = "#997637";		// Main table colour           Default: Dark Brown
		$color_line1  = "#aa8748";		// Alternating rows color      Default: Mid Brown
		$color_line2  = "#Bb9859";		// Alternating rows color      Default: Light Brown
}


// The following variables let you define how the game runs and
// what type of debug tracking you want, if any

	$maxlen_password         = 16;
	$server_closed           = false;       // true = block logins but not new account creation
	$account_creation_closed = false;       // true = block new account creation
	$game_debug              = true;        // true = turns on debug logger
	$game_debug_move         = true;       // true = turns on the movement debugger
	$game_debug_xfer         = true;        // true = turns on the transfer debugger
	$game_skill_debug        = true;       // true = turns on the skill attempts debugger
	$game_pop_debug          = true;       // true = turns on the population debugger

	$default_lang            = 'english';

	$display_password        = false;       // If true, will display password on signup screen.
	$sched_type              = 0;           // 0 = Cron based, 1 = player triggered.


?>
