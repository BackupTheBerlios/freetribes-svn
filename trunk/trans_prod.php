<?
if(!ISSET($_SESSION['username']))
{
  echo "You must <a href=index.php>log in</a> to view this page.<br>\n";
  TEXT_GOTOLOGIN();
  die();
}



$dump="No";
if (ISSET($_REQUEST['dump']) || $to_tribe=="dump")
{
	$dump="Yes";
}

$clanid = $_SESSION['clanid'];
$tribeid = $_SESSION['current_unit'];

$products = $db->Execute("SELECT distinct proper, amount, long_name FROM $dbtables[products] "
                        ."WHERE tribeid = '$from_tribe' "
                        ."AND amount > 0 "
                        ."AND long_name != 'totem'"
                        ."ORDER BY long_name");
$prodinfo = $products->fields;

if(!ISSET($_REQUEST['product']))
	{

	echo "<TABLE BGCOLOR=\"$color_table\" BORDER=0 CELLPADDING=1 CELLSPACING=0 ALIGN=CENTER WIDTH=\"80%\">";
	echo "<TR BGCOLOR=\"$color_header\" WIDTH=\"100%\">"
		."<TD COLSPAN=8>"
		."<FONT CLASS=page_subtitle>Stores Available ($from_tribe)</FONT>"
		."</TD>"
		."</TR>";
	echo "<TR WIDTH=\"100%\" BGCOLOR=\"$color_header\"><TD COLSPAN=12>&nbsp;</TD></TR>";
	echo "<TR WIDTH=\"100%\"><TD>&nbsp;</TD>";

	if( $products->EOF )
	{
		echo '</TR><TR><TD COLSPAN=8><FONT SIZE=+1 COLOR=white><CENTER>You have no stores to transfer.</CENTER><BR></TD></TR>';
		echo "<TR WIDTH=\"100%\" BGCOLOR=\"$color_header\"><TD COLSPAN=8>&nbsp;</TD></TR>";
		echo "</TABLE>";
		include("footer.php");
		die();
	}


	$n=0;
	while( !$products->EOF )
	{
		$rc = $n % 2;
		echo "</TR><TR CLASS=row_color$rc WIDTH=\"100%\">";
		$unit_res = $products->fields;
		$i = 0;
		while( $i < 4 && !$products->EOF)
		{
			$unit_prod = $products->fields;
			if( $unit_prod[amount] > 0 )
			{
				echo "<TD CLASS=row_sep></TD>"
					."<TD>"
						."<TABLE BORDER=0 CELLSPADDING=0>"
						."<TR>"
						."<TD>$unit_prod[proper] (".$unit_prod[amount].")</TD>"
						."</TR>"
						."<TR>"
						."<TD>"
						."<INPUT CLASS=edit_area TYPE=TEXT NAME=\"product[".$unit_prod[long_name]."]\" VALUE=''>"
						."</TD>"
						."</TR>"
						."</TABLE>"
					."</TD>";
				$i++;
			}
			$products->MoveNext();
		}
		$n++;
	}
	while ($i < 4)
	{
		echo "<TD CLASS=row_sep WIDTH=0></TD>"
			."<TD>&nbsp;</TD>";
		$i++;
	}
	echo "</TR>";
	if ($n==0)
	{
		echo "<TR WIDTH=\"100%\"><TD COLSPAN=8><FONT COLOR=WHITE><CENTER>You have no resources to transfer</FONT></CENTER></TD></TR>";
		echo "<TR WIDTH=\"100%\"><TD COLSPAN=8>&nbsp;</TD></TR>";
		echo "<TR WIDTH=\"100%\" BGCOLOR=\"$color_header\"><TD COLSPAN=8>&nbsp;</TD></TR>";
	}
	else
	{
		echo "<TR WIDTH=\"100%\"><TD COLSPAN=8>&nbsp;</TD></TR>";
		echo "<TR WIDTH=\"100%\"><TD COLSPAN=8>&nbsp;</TD></TR>";
		echo "<TR WIDTH=\"100%\" BGCOLOR=\"$color_header\"><TD COLSPAN=8><INPUT TYPE=SUBMIT NAME=dump Value=Destroy>&nbsp;<FONT COLOR=WHITE> This really will DESTROY your stores!</FONT></TD></TR>";
	}

	echo "</TABLE>";
}
else
{
	echo "<TABLE BGCOLOR=\"$color_table\" BORDER=0 CELLPADDING=1 CELLSPACING=0 ALIGN=CENTER WIDTH=\"80%\">";
	echo "<TR BGCOLOR=\"$color_header\" WIDTH=\"100%\">"
		."<TD COLSPAN=8>"
		."<FONT CLASS=page_subtitle>Transferring Stores from $from_tribe to $to_tribe</FONT>"
		."</TD>"
		."</TR>";
	echo "<TR WIDTH=\"100%\" BGCOLOR=\"$color_header\"><TD COLSPAN=12>&nbsp;</TD></TR>";
	echo "<TR WIDTH=\"100%\"><TD>&nbsp;</TD>";

	if ($dump=="Yes")
	{
		echo "<TR><TD>Dumped</TD><TD>Amount</TD></TR>";
	}
	else
	{
		echo "<TR><TD>Transferring</TD><TD>Amount</TD><TD>From</TD><TD>To</TD></TR>";
	}


	$n = 0;
	foreach($_REQUEST['product'] as $key => $prod)
	{
		$rc = $n % 2;
		$giv = $db->Execute("SELECT * FROM $dbtables[products] "
							."WHERE tribeid = '$from_tribe' AND long_name = '$key'");
		$giv_prod = $giv->fields;

		$rec = $db->Execute("SELECT * FROM $dbtables[products] "
							."WHERE tribeid = '$to_tribe' AND long_name = '$key'");
		$rec_prod = $rec->fields;

		if($dump == "Yes" && $prod > 0)
		{
			$n++;
			$feedback = $prod;
			$db->Execute("UPDATE $dbtables[products] "
						."SET amount = amount - $prod "
						."WHERE long_name = '$giv_prod[long_name]' "
						."AND tribeid = '$from_tribe'");

			if( $game_debug_xfer)
			{
				$db->Execute("INSERT INTO $dbtables[logs] "
							."VALUES("
							."'',"
							."'$month[count]',"
							."'$year[count]',"
							."'0000',"
							."'0000.00',"
							."'DEBUG',"
							."'$stamp',"
							."'PROD TRANS: $from_tribe dumped $prod $giv_prod[long_name]')");
			}

			echo "<TR CLASS=row_color$rc WIDTH=\"100%\">"
				."<TD>$key</TD>"
				."<TD>$prod</TD>"
				."</TR>";

            include("weight.php");
	    }

		if($dump == "No" && ISSET($rec_prod[amount]) && $prod > 0)
		{
			$n++;
			if ($from_tribe==$to_tribe && $dump=="No")
			{
				echo "<TR><TD COLSPAN=4><FONT SIZE=+1 COLOR=white><CENTER>You can't transfer stores from a tribe to itself.</CENTER><BR></TD></TR>";
				echo "<TR WIDTH=\"100%\"><TD COLSPAN=8>&nbsp;</TD></TR>";
				echo "<TR WIDTH=\"100%\" BGCOLOR=\"$color_header\"><TD COLSPAN=8>&nbsp;</FONT></TD></TR>";
				echo "</TABLE>";
				page_footer();
			}

	        if($giv_prod[amount] >= $prod)
			{
				echo "<TR CLASS=row_color$rc>"
					."<TD>$rec_prod[proper]</TD>"
					."<TD ALIGN=LEFT>$prod</TD>"
					."<TD>$from_tribe</TD>"
					."<TD>$to_tribe</TD>"
					."</TR>";

				$db->Execute("UPDATE $dbtables[products] "
							."SET amount = amount + '$prod' "
							."WHERE long_name = '$rec_prod[long_name]' "
							."AND tribeid = '$to_tribe'");
				$db->Execute("UPDATE $dbtables[products] "
							."SET amount = amount - '$prod' "
							."WHERE long_name = '$giv_prod[long_name]' "
							."AND tribeid = '$from_tribe'");
				if( $game_debug_xfer )
				{
				$db->Execute("INSERT INTO $dbtables[logs] "
							."VALUES("
							."'',"
							."'$month[count]',"
							."'$year[count]',"
							."'0000',"
							."'0000.00',"
							."'DEBUG',"
							."'$stamp',"
							."'PROD TRANS: $from_tribe transferred $prod $giv_prod[proper] to $to_tribe.')");
				}
				include("weight.php");
			}
			else
			{
				echo "<TR CLASS=row_color$rc>"
					."<TD>$giv_prod[proper]</TD>"
					."<TD>$prod</TD>"
					."<TD COLSPAN=2><FONT SIZE=+1 COLOR=WHITE>You do not have that many.</FONT></TD>"
					."</TR>";
			}
		}
	}
	echo "<TR WIDTH=\"100%\"><TD COLSPAN=8>&nbsp;</TD></TR>";
	echo "<TR WIDTH=\"100%\"><TD COLSPAN=8>&nbsp;</TD></TR>";
	echo "<TR WIDTH=\"100%\" BGCOLOR=\"$color_header\"><TD COLSPAN=8>&nbsp;</FONT></TD></TR>";
	echo "</TABLE>";
}


?> 
