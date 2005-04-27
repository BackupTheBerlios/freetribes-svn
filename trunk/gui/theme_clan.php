<?php
// Get the theme that a player has selected and set it in $_SESSION

function theme_clan($clanid)
{
	global $db, $dbtables, $theme_default;

	$_SESSION['theme'] = $theme_default;

	$res = $db-Execute ("SELECT theme FROM $dbtables[chiefs] WHERE clanid='$clanid'");
	if (!$res->EOF)
	{
		if ($res->fields[theme] <> "")
		{
			$_SESSION['theme'] = $res->fields[theme];
		}
	}
}




?>