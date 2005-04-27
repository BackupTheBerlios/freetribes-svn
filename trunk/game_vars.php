<?php
// Local settings for the game variables

	$game_version            = "v.00.09.3.001";
	$maxlen_password         = 16;
	$server_closed           = false;       // true = block logins but not new account creation
	$account_creation_closed = false;       // true = block new account creation
	$game_debug              = true;        // true = turns on debug logger
	$game_debug_move         = false;       // true = turns on the movement debugger
	$game_debug_xfer         = true;        // true = turns on the transfer debugger
	$game_skill_debug        = false;       // true = turns on the skill attempts debugger
	$game_pop_debug          = false;       // true = turns on the population debugger

	$default_lang            = 'english';

	$display_password        = false;       // If true, will display password on signup screen.
	$sched_type              = 0;           // 0 = Cron based, 1 = player triggered.

?>