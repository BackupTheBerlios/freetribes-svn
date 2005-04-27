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

$resource = $db->Execute("SELECT * FROM $dbtables[resources] "
                        ."WHERE tribeid = '$from_tribe' "
                        ."AND amount > 0");
$resinfo = $resource->fields;


$module = $_POST['receiver'];


if( !ISSET($_REQUEST['resource']) )
{
	echo "<TABLE BGCOLOR=\"$color_table\" BORDER=0 CELLPADDING=1 CELLSPACING=0 ALIGN=CENTER WIDTH=\"80%\">";
	echo "<TR BGCOLOR=\"$color_header\" WIDTH=\"100%\">"
		."<TD COLSPAN=8>"
		."<FONT CLASS=page_subtitle>Resources Available ($from_tribe)</FONT>"
		."</TD>"
		."</TR>";
	echo "<TR WIDTH=\"100%\" BGCOLOR=\"$color_header\"><TD COLSPAN=12>&nbsp;</TD></TR>";
	echo "<TR WIDTH=\"100%\"><TD>&nbsp;</TD>";

	if( $resource->EOF )
	{
		echo '</TR><TR><TD COLSPAN=8><FONT SIZE=+1 COLOR=white><CENTER>You have no resources to transfer.</CENTER><BR></TD></TR>';
		echo "<TR WIDTH=\"100%\" BGCOLOR=\"$color_header\"><TD COLSPAN=8>&nbsp;</TD></TR>";
		echo "</TABLE>";
		include("footer.php");
		die();
	}

	$n=0;
	while( !$resource->EOF )
	{
		$rc = $n % 2;
		echo "</TR><TR CLASS=row_color$rc WIDTH=\"100%\">";
		$unit_res = $resource->fields;
		$i = 0;
		while( $i < 4 && !$resource->EOF)
		{
			$unit_res = $resource->fields;
			if( $unit_res[amount] > 0 )
			{
				echo "<TD CLASS=row_sep></TD>"
					."<TD>"
						."<TABLE BORDER=0 CELLSPADDING=0>"
						."<TR>"
						."<TD>$unit_res[long_name] (".$unit_res[amount].")</TD>"
						."</TR>"
						."<TR>"
						."<TD>"
						."<INPUT CLASS=edit_area TYPE=TEXT NAME=\"resource[".$unit_res[dbname]."]\" VALUE=''>"
						."</TD>"
						."</TR>"
						."</TABLE>"
					."</TD>";
				$i++;
			}
			$resource->MoveNext();
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
		echo "<TR WIDTH=\"100%\" BGCOLOR=\"$color_header\"><TD COLSPAN=8><INPUT TYPE=SUBMIT NAME=dump Value=Destroy>&nbsp;<FONT COLOR=WHITE> This really will DESTROY your resources!</FONT></TD></TR>";
	}

	echo "</TABLE>";
}
else
{
	echo "<TABLE BGCOLOR=\"$color_table\" BORDER=0 CELLPADDING=1 CELLSPACING=0 ALIGN=CENTER WIDTH=\"80%\">";
	echo "<TR BGCOLOR=\"$color_header\" WIDTH=\"100%\">"
		."<TD COLSPAN=8>"
		."<FONT CLASS=page_subtitle>Transferring Resources from $from_tribe to $to_tribe</FONT>"
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
	foreach( $_REQUEST['resource'] as $key => $ore )
	{
		$rc = $n % 2;
		$giv = $db->Execute("SELECT * FROM $dbtables[resources] "
							   ."WHERE tribeid = '$from_tribe' "
							   ."AND dbname = '$key'");
		$giv_res = $giv->fields;

		$rec = $db->Execute("SELECT * FROM $dbtables[resources] "
						   ."WHERE tribeid = '$to_tribe' "
						   ."AND dbname = '$key'");
		$rec_res = $rec->fields;

		if( $dump == "Yes" && $ore > 0 )
		{
			$n++;
			$db->Execute("UPDATE $dbtables[resources] "
						."SET amount = amount - $ore "
						."WHERE tribeid = '$from_tribe' "
						."AND dbname = '$key'");
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
							."'RESOURCE TRANS: $from_tribe dumped $ore $giv_res[long_name]')");
			}

			echo "<TR CLASS=row_color$rc>"
				."<TD><FONT SIZE=+1 COLOR=WHITE>$giv_res[long_name]</FONT></TD>"
				."<TD><FONT SIZE=+1 COLOR=WHITE>$ore</FONT></TD>"
				."</TR>";

			include("weight.php");

		}
		if( $dump=="No" && ISSET($rec_res[amount]) && $ore > 0 )
		{
			$n++;
			if ($from_tribe==$to_tribe && $dump=="No")
			{
				echo "<TR><TD COLSPAN=4><FONT SIZE=+1 COLOR=white><CENTER>You can't transfer resources from a tribe to itself.</CENTER><BR></TD></TR>";
				echo "<TR WIDTH=\"100%\"><TD COLSPAN=8>&nbsp;</TD></TR>";
				echo "<TR WIDTH=\"100%\" BGCOLOR=\"$color_header\"><TD COLSPAN=8>&nbsp;</FONT></TD></TR>";
				echo "</TABLE>";
				page_footer();
			}

			if( $giv_res[amount] >= $ore )
			{
				$db->Execute("UPDATE $dbtables[resources] "
							."SET amount = amount + '$ore' "
							."WHERE dbname = '$rec_res[dbname]' "
							."AND tribeid = '$to_tribe'");
				$db->Execute("UPDATE $dbtables[resources] "
							."SET amount = amount - '$ore' "
							."WHERE dbname = '$giv_res[dbname]' "
							."AND tribeid = '$from_tribe'");

				echo "<TR CLASS=row_color$rc>"
					."<TD>$rec_res[long_name]</TD>"
					."<TD>$ore</TD>"
					."<TD>$from_tribe</TD>"
					."<TD>$to_tribe</TD>"
					."</TR>";

				$trib = $db->Execute("SELECT * FROM $dbtables[tribes] "
									."WHERE tribeid = '$to_tribe'");
				$recinfo = $trib->fields;

				if( $recinfo[clanid] <> $_SESSION[clanid] )
				{
					$db->Execute("INSERT INTO $dbtables[logs] "
								."VALUES("
								."'',"
								."'$month[count]',"
								."'$year[count]',"
								."'$recinfo[clanid]',"
								."'$recinfo[tribeid]',"
								."'TRANS',"
								."'$stamp',"
								."'Transfer: $recinfo[tribeid] has received $ore $giv_res[long_name] from $from_tribe.')");
				} 
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
								."'RESOURCE TRANS: $from_tribe transferred $ore $giv_res[long_name] to $to_tribe')");
				}
				include("weight.php");
			}
			else 
			{
				$n++;
				echo "<TR CLASS=row_color$rc>"
					."<TD>$giv_res[long_name]</TD>"
					."<TD>$ore</TD>"
					."<TD COLSPAN=2><FONT SIZE=+1 COLOR=WHITE>You do not have that much.</FONT></TD>"
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
