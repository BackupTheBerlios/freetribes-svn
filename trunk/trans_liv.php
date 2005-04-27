<?
if( !ISSET( $_SESSION['username'] ) )
{
    echo "You must <a href=index.php>log in</a> to view this page.<br>\n";
    TEXT_GOTOLOGIN();
    die();
}

$dump="No";
if (ISSET($_REQUEST['dump']))
{
	$dump="Yes";
}

$clanid = $_SESSION['clanid'];
$tribeid = $_SESSION['current_unit'];

$livestock = $db->Execute("SELECT * FROM $dbtables[livestock] "
						 ."WHERE tribeid = '$from_tribe' "
						 ."AND amount > 0");
$livinfo = $livestock->fields;

if( !ISSET( $_REQUEST['livestock'] ) )
{
	echo "<TABLE BGCOLOR=\"$color_table\" BORDER=0 CELLPADDING=1 CELLSPACING=0 ALIGN=CENTER WIDTH=\"80%\">";
	echo "<TR BGCOLOR=\"$color_header\" WIDTH=\"100%\">"
		."<TD COLSPAN=8>"
		."<FONT CLASS=page_subtitle>Livestock Available ($from_tribe)</FONT>"
		."</TD>"
		."</TR>";
	echo "<TR WIDTH=\"100%\" BGCOLOR=\"$color_header\"><TD COLSPAN=12>&nbsp;</TD></TR>";
	echo "<TR WIDTH=\"100%\"><TD>&nbsp;</TD>";

	if( $livestock->EOF )
	{
		echo '</TR><TR><TD COLSPAN=8><FONT SIZE=+1 COLOR=white><CENTER>You have no livestock to transfer.</CENTER><BR></TD></TR>';
		echo "<TR WIDTH=\"100%\" BGCOLOR=\"$color_header\"><TD COLSPAN=8>&nbsp;</TD></TR>";
		echo "</TABLE>";
		include("footer.php");
		die();
	}

	$n=0;
	while( !$livestock->EOF )
	{
		$rc = $n % 2;
		echo "</TR><TR CLASS=row_color$rc WIDTH=\"100%\">";
		$unit_liv = $livestock->fields;
		$i = 0;
		while( $i < 4 && !$livestock->EOF)
		{
			$unit_liv = $livestock->fields;
			if( $unit_liv[amount] > 0 )
			{
				echo "<TD CLASS=row_sep></TD>"
					."<TD>"
						."<TABLE BORDER=0 CELLSPADDING=0>"
						."<TR>"
						."<TD>$unit_liv[type] (".$unit_liv[amount].")</TD>"
						."</TR>"
						."<TR>"
						."<TD>"
						."<INPUT CLASS=edit_area TYPE=TEXT NAME=\"livestock[".$unit_liv[type]."]\" VALUE=''>"
						."</TD>"
						."</TR>"
						."</TABLE>"
					."</TD>";
				$i++;
			}
			$livestock->MoveNext();
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
		echo "<TR WIDTH=\"100%\"><TD COLSPAN=8><FONT COLOR=WHITE><CENTER>You have no livestock to transfer</FONT></CENTER></TD></TR>";
		echo "<TR WIDTH=\"100%\"><TD COLSPAN=8>&nbsp;</TD></TR>";
		echo "<TR WIDTH=\"100%\" BGCOLOR=\"$color_header\"><TD COLSPAN=8>&nbsp;</TD></TR>";
	}
	else
	{
		echo "<TR WIDTH=\"100%\"><TD COLSPAN=8>&nbsp;</TD></TR>";
		echo "<TR WIDTH=\"100%\"><TD COLSPAN=8>&nbsp;</TD></TR>";
		echo "<TR WIDTH=\"100%\" BGCOLOR=\"$color_header\"><TD COLSPAN=8><INPUT TYPE=SUBMIT NAME=dump Value=Destroy>&nbsp;<FONT COLOR=WHITE> This really will DESTROY your livestock!</FONT></TD></TR>";
	}

	echo "</TABLE>";
}
else
{
	echo "<TABLE BGCOLOR=\"$color_table\" BORDER=0 CELLPADDING=1 CELLSPACING=0 ALIGN=CENTER WIDTH=\"80%\">";
	echo "<TR BGCOLOR=\"$color_header\" WIDTH=\"100%\">"
		."<TD COLSPAN=8>"
		."<FONT CLASS=page_subtitle>Transferring Livestock from $from_tribe to $to_tribe</FONT>"
		."</TD>"
		."</TR>";
	echo "<TR WIDTH=\"100%\" BGCOLOR=\"$color_header\"><TD COLSPAN=12>&nbsp;</TD></TR>";
	echo "<TR WIDTH=\"100%\"><TD>&nbsp;</TD>";

	if ($dump=="Yes")
	{
		echo "<TR><TD>The following beasties freed to, once more, happily roam the countryside never more to be enslaved by the bonds of man!</TD></TR><BR>&nbsp;";
		echo "<TR><TD>Freed</TD><TD>Amount</TD></TR>";
	}
	else
	{
		echo "<TR><TD>Transferring</TD><TD>Amount</TD><TD>From</TD><TD>To</TD></TR>";
	}


	$n = 0;
	foreach( $_REQUEST['livestock'] as $key => $liv )
	{
		$rc = $n % 2;
		$giv = $db->Execute("SELECT * FROM $dbtables[livestock] "
							   ."WHERE tribeid = '$from_tribe' "
							   ."AND type = '$key'");
		$giv_liv = $giv->fields;

		$rec = $db->Execute("SELECT * FROM $dbtables[livestock] "
						   ."WHERE tribeid = '$to_tribe' "
						   ."AND type = '$key'");
		$rec_liv = $rec->fields;

		if( $dump=="Yes" && $liv > 0 )
		{
			$n++;
			$db->Execute("UPDATE $dbtables[livestock] "
						."SET amount = amount - $liv "
						."WHERE tribeid = '$from_tribe' "
						."AND type = '$key'");
			if( $liv && $game_debug_xfer )
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
							."'LIV TRANS: $from_tribe dumped $liv (liv) $key (key).')");
			}

			echo "<TR CLASS=row_color$rc WIDTH=\"100%\">"
				."<TD>$key</TD>"
				."<TD>$liv</TD>"
				."</TR>";

		include("weight.php");
		}

		if( $dump=="No" && ISSET($rec_liv[amount]) && $liv > 0 )
		{
			$n++;
			if ($from_tribe==$to_tribe && $dump=="No")
			{
				echo "<TR><TD COLSPAN=4><FONT SIZE=+1 COLOR=white><CENTER>You can't transfer livestock from a tribe to itself.</CENTER><BR></TD></TR>";
				echo "<TR WIDTH=\"100%\"><TD COLSPAN=8>&nbsp;</TD></TR>";
				echo "<TR WIDTH=\"100%\" BGCOLOR=\"$color_header\"><TD COLSPAN=8>&nbsp;</FONT></TD></TR>";
				echo "</TABLE>";
				page_footer();
			}

			if( $giv_liv[amount] >= $liv )
			{
				$rec_liv[amount] += $liv;
				$giv_liv[amount] -= $liv;
				$upgrade = $liv;
				if( !$upgrade )
				{
					$upgrade = 0;
				}

				$db->Execute("UPDATE $dbtables[livestock] "
								."SET amount = amount + $liv "
								."WHERE type = '$key' "
								."AND tribeid = '$to_tribe'");
				$db->Execute("UPDATE $dbtables[livestock] "
								."SET amount = amount - $liv "
								."WHERE type = '$key' "
								."AND tribeid = '$from_tribe'");

				echo "<TR CLASS=row_color$rc>"
					."<TD>$give_liv[type]</TD>"
					."<TD ALIGN=LEFT>$upgrade</TD>"
					."<TD>$from_tribe</TD>"
					."<TD>$to_tribe</TD>"
					."</TR>";

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
							."'LIV TRANS: $from_tribe transfers $upgrade $giv_liv[type] to $to_tribe.')");
				}

				include("weight.php");
			}
			else 
			{
				$n++;
				echo "<TR CLASS=row_color$rc>"
					."<TD>$giv_liv[type]</TD>"
					."<TD>$upgrade</TD>"
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
