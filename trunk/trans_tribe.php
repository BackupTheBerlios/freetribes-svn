<?
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: goodstribe.php

if(!ISSET($_SESSION['username']))
{
	echo "You must <a href=index.php>log in</a> to view this page.<br>\n";
	TEXT_GOTOMAIN();
	die();
}


if(ISSET($to_tribe))
{
    echo "<TABLE BGCOLOR=\"$color_table\" BORDER=0 CELLPADDING=0 CELLSPACING=0 ALIGN=CENTER WIDTH=\"80%\">";
	echo "<TR WIDTH=\"100%\" BGCOLOR=\"$color_header\"><TD><FONT CLASS=page_subtitle>Goods Tribe for $from_tribe</FONT></TD></TR>";
	echo "<TR WIDTH=\"100%\" BGCOLOR=\"$color_header\"><TD>&nbsp;</TD></TR>";
	echo "<TR WIDTH=\"100%\"><TD><FONT COLOR=WHITE><CENTER><BR>";
	if ($goods_tribe==$to_tribe)
	{
	    echo "is currently";
	}
	else
	{
	    echo "has been";
	}
	echo " set to tribe $to_tribe</CENTER><br></TD></TR>";
	echo "<TR WIDTH=\"100%\" BGCOLOR=\"$color_header\"><TD>&nbsp;</TD></TR>";
	echo "</TABLE>";

	if ($goods_tribe==$to_tribe)
	{
		include("footer.php");
		die();
	}


	$db->Execute("UPDATE $dbtables[tribes] SET goods_tribe = '$to_tribe' WHERE tribeid = '$from_tribe'");
	$result = $db->Execute("SELECT * FROM $dbtables[tribes] "
							."WHERE tribeid = '$from_tribe'");

	while( !$result->EOF )
	{
		$tribe = $result->fields;

		// Structures
		$struct = $db->Execute("SELECT * FROM $dbtables[structures] "
						   ."WHERE tribeid = '$tribe[tribeid]'");
		while ( !$struct->EOF )
		{
			$structinfo = $res->fields;
			$db->Execute("UPDATE $dbtables[structures] "
						."SET tribeid = '$tribe[goods_tribe]' "
						."WHERE tribeid = '$tribe[tribeid]' "
						."AND hex_id = '$tribe[hex_id]'");
			$struct->MoveNext();
		}

		// Resources
		$res = $db->Execute("SELECT * FROM $dbtables[resources] "
						   ."WHERE tribeid = '$tribe[tribeid]' "
						   ."AND amount > 0");
		while( !$res->EOF )
		{
			$resinfo = $res->fields;
			$db->Execute("UPDATE $dbtables[resources] "
						."SET amount = amount + $resinfo[amount] "
						."WHERE tribeid = '$tribe[goods_tribe]' "
						."AND long_name = '$resinfo[long_name]'");

			$db->Execute("UPDATE $dbtables[resources] "
						."SET amount = amount - $resinfo[amount] "
						."WHERE tribeid = '$tribe[tribeid]' "
						."AND long_name = '$resinfo[long_name]'");
			$res->MoveNext();
		}

		// Products
		$prod = $db->Execute("SELECT * FROM $dbtables[products] "
							."WHERE tribeid = '$tribe[tribeid]' "
							."AND amount > 0 "
							."AND long_name != 'totem'");
		while( !$prod->EOF )
		{
			$prodinfo = $prod->fields;
			$db->Execute("UPDATE $dbtables[products] "
						."SET amount = amount + $prodinfo[amount] "
						."WHERE tribeid = '$tribe[goods_tribe]' "
						."AND long_name = '$prodinfo[long_name]' "
						."AND long_name != 'totem'");

			$db->Execute("UPDATE $dbtables[products] "
						."SET amount = amount - $prodinfo[amount] "
						."WHERE tribeid = '$tribe[tribeid]' "
						."AND long_name = '$prodinfo[long_name]' "
						."AND long_name != 'totem'");
			$prod->MoveNext();
		}

		// Livestock
		$liv = $db->Execute("SELECT * FROM $dbtables[livestock] "
						   ."WHERE tribeid = '$tribe[tribeid]' "
						   ."AND amount > 0");
		while( !$liv->EOF )
		{
			$livinfo = $liv->fields;
			$db->Execute("UPDATE $dbtables[livestock] "
						."SET amount = amount + $livinfo[amount] "
						."WHERE tribeid = '$tribe[goods_tribe]' "
						."AND type = '$livinfo[type]'");
			$db->Execute("UPDATE $dbtables[livestock] "
						."SET amount = amount - '$livinfo[amount]' "
						."WHERE tribeid = '$tribe[tribeid]' "
						."AND type = '$livinfo[type]'");
			$liv->MoveNext();
		}

		$result->MoveNext();
	} //End BIG WHILE

	include("weight.php");
}


?> 
