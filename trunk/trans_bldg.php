<?
if( !ISSET($_SESSION['username']) )
{
    TEXT_GOTOLOGIN();
    die();
}

$clanid = $_SESSION['clanid'];
$tribeid = $_SESSION['current_unit'];
$module = $_REQUEST['to_tribe'];

$dump="No";
if (ISSET($_REQUEST['dump']))
{
	$dump="Yes";
}

$structure = $db->Execute("SELECT * FROM $dbtables[structures] "
                         ."WHERE tribeid = '$from_tribe' "
                         ."AND hex_id = '$from_hex'");
$structinfo = $structure->fields;

if( !ISSET($_REQUEST['structure']) )
{
    echo "<TABLE BGCOLOR=\"$color_table\" BORDER=0 CELLPADDING=0 CELLSPACING=0 ALIGN=CENTER WIDTH=\"80%\">";
	echo "<TR BGCOLOR=\"$color_header\" WIDTH=\"100%\"><TD COLSPAN=12><FONT CLASS=page_subtitle>Buildings Available ($from_tribe)</FONT></TD></TR>";
	echo "<TR WIDTH=\"100%\" BGCOLOR=\"$color_header\"><TD COLSPAN=12>&nbsp;</TD></TR>";
	echo "<TR WIDTH=\"100%\"><TD>&nbsp;</TD></TR>";

    if( $structure->EOF )
    {
        echo '</TR><TR><TD COLSPAN=12><FONT SIZE=+1 COLOR=white><CENTER>You have no buildings to transfer.</CENTER><BR></TD></TR>';
		echo "<TR WIDTH=\"100%\" BGCOLOR=\"$color_header\"><TD>&nbsp;</TD></TR>";
		echo "</TABLE>";
        include("footer.php");
        die();
    }

	$n = 0;
    while( !$structure->EOF )
    {
		$rc = $n % 2;
        echo "<TR CLASS=row_color$rc WIDTH=\"100%\">";
        $unit_bldg = $structure->fields;
        $i = 0;
        while( $i < 4 )
        {
            $unit_bldg = $structure->fields;
            if( $unit_bldg[struct_pts] > 0 )
            {
                echo "<TD CLASS=row_sep>"
					."</TD>"
					."<TD>"
					."<INPUT TYPE=CHECKBOX NAME='structure[]' VALUE='$unit_bldg[struct_id]'>"
					."</TD>"
					."<TD>"
					."$unit_bldg[proper]"
					."</TD>";
            }
            $i++;
            $structure->MoveNext();
        }
        echo '</TR>';
		$n++;
    }
	echo "<TR><TD COLSPAN=12>&nbsp;</TD></TR>";
	echo "<TR><TD COLSPAN=12>&nbsp;</TD></TR>";
    echo "<TR WIDTH=\"100%\" BGCOLOR=\"$color_header\"><TD COLSPAN=12><INPUT TYPE=SUBMIT NAME=dump Value=Destroy>&nbsp;<FONT COLOR=WHITE>This really will DESTROY your buildings!</FONT></TD></TR></TABLE>"; 
}
else
{
	echo "<TABLE BGCOLOR=\"$color_table\" BORDER=0 CELLPADDING=1 CELLSPACING=0 ALIGN=CENTER WIDTH=\"80%\">";
	echo "<TR BGCOLOR=\"$color_header\" WIDTH=\"100%\">"
		."<TD COLSPAN=8>"
		."<FONT CLASS=page_subtitle>Transferring Buildings from $from_tribe to $to_tribe</FONT>"
		."</TD>"
		."</TR>";
	echo "<TR WIDTH=\"100%\" BGCOLOR=\"$color_header\"><TD COLSPAN=12>&nbsp;</TD></TR>";
	echo "<TR WIDTH=\"100%\"><TD>&nbsp;</TD>";

	if ($dump=="Yes")
	{
		echo "<TR><TD>Dismantled ...</TD></TR>";
	}
	else
	{
		echo "<TR><TD>Transferring</TD><TD>From</TD><TD>To</TD></TR>";
	}


	$n = 0;
    foreach( $_REQUEST['structure'] as $key => $bldg )
    {
		$rc = $n % 2;
        $giv = $db->Execute("SELECT * FROM $dbtables[structures] "
                           ."WHERE struct_id = '$bldg' "
                           ."AND tribeid = '$from_tribe'");
        $giv_info = $giv->fields;
        if( !empty($to_tribe) )
        {
            if( $dump == "Yes" && !empty($giv_info[struct_id]) )
            {
				$n++;
                $db->Execute("DELETE FROM $dbtables[structures] "
                            ."WHERE tribeid = '$from_tribe' "
                            ."AND struct_id = '$bldg'");

			echo "<TR CLASS=row_color$rc WIDTH=\"100%\">"
				."<TD>$giv_info[proper]</TD>"
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
							."'BLDG TRANS: $from_tribe destroys $giv_info[proper].')");
			}
		}

			if($dump == "No" && ISSET($giv_info[struct_id]) && ISSET($to_tribe) )
            {
				$n++;
				if ($from_tribe==$to_tribe && $dump=="No")
				{
					echo "<TR><TD><FONT SIZE=+1 COLOR=white><CENTER>You can't transfer buildings from a tribe to itself.</CENTER><BR></TD></TR>";
					echo "<TR WIDTH=\"100%\"><TD COLSPAN=8>&nbsp;</TD></TR>";
					echo "<TR WIDTH=\"100%\" BGCOLOR=\"$color_header\"><TD COLSPAN=8>&nbsp;</FONT></TD></TR>";
					echo "</TABLE>";
					page_footer();
				}

				echo "<TR CLASS=row_color$rc>"
					."<TD>$giv_info[proper]</TD>"
					."<TD>$from_tribe</TD>"
					."<TD>$to_tribe</TD>"
					."</TR>";

				$to_clan = $db->Execute("SELECT clanid FROM $dbtables[tribes] "
							."WHERE tribeid='$to_tribe'");
				$to_clan = $to_clan->fields['clanid'];

                $db->Execute("UPDATE $dbtables[structures] "
                            ."SET tribeid = '$to_tribe', "
							."clanid='$to_clan' "
                            ."WHERE struct_id = '$giv_info[struct_id]' "
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
                                ."'BLDG TRANS: $from_tribe transfers $giv_info[proper] to $to_tribe.')");
                }
            }
        }
        else
        {
            echo "<TR CLASS=row_color$rc><TD>Please select a tribe to transfer to.</TD></TR>";
			echo "<TR WIDTH=\"100%\"><TD COLSPAN=8>&nbsp;</TD></TR>";
			echo "<TR WIDTH=\"100%\" BGCOLOR=\"$color_header\"><TD COLSPAN=8>&nbsp;</FONT></TD></TR>";
			echo "</TABLE>";
            page_footer();
        }
    }
	echo "<TR WIDTH=\"100%\"><TD COLSPAN=8>&nbsp;</TD></TR>";
	echo "<TR WIDTH=\"100%\" BGCOLOR=\"$color_header\"><TD COLSPAN=8>&nbsp;</FONT></TD></TR>";
	echo "</TABLE>";
}


?> 
