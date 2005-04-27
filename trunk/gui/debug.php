<?php
// basic debug function
// this takes a string and prints it with HTML and normal text ends of line
// if the variable $debug_on == 1

function debug_msg ($s)
{
	global $debug_on;

	if ($debug_on == 1)
	{
		echo "<BR> $s<BR>\n";
	}
}

?>