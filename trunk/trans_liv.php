<?php
if( !ISSET( $_SESSION['username'] ) )
{
    echo "You must <a href=index.php>log in</a> to view this page.<br>\n";
    TEXT_GOTOLOGIN();
    die();
}
$dump = "No";
if (ISSET($_POST['dump']))
{
    $dump="Yes";
}
//O-Tay.. so far so good it seems to be werkin' well :)
$clanid = $_SESSION['clanid'];
$tribeid = $_SESSION['current_unit'];
$from_tribe = $_POST['from_tribe'];
$to_tribe = $_POST['to_tribe'];
$unit = $_POST['unit']; //current unit

if( empty( $_POST['livestock'] ) )
{
    echo "<TABLE BGCOLOR=\"$color_table\" BORDER=0 CELLPADDING=1 CELLSPACING=0 ALIGN=CENTER WIDTH=\"80%\">\n";
    echo "<TR BGCOLOR=\"$color_header\" WIDTH=\"100%\">\n"
        ."<TD COLSPAN=8>"
        ."<FONT CLASS=page_subtitle>Livestock Available ($from_tribe)</FONT>\n"
        ."</TD>\n"
        ."</TR>\n";
    echo "<TR WIDTH=\"100%\" BGCOLOR=\"$color_header\"><TD COLSPAN=12>&nbsp;</TD></TR>\n";
    echo "<TR WIDTH=\"100%\"><TD>&nbsp;</TD>\n";
    $livestock = $db->Execute("SELECT * FROM $dbtables[livestock] WHERE tribeid = '$from_tribe' AND amount > 0");
    db_op_result($livestock,__LINE__,__FILE__);
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
        echo "</TR><TR CLASS=row_color$rc WIDTH=\"100%\">\n";
        $unit_liv = $livestock->fields;
            if( $unit_liv['amount'] > 0 )
            {
                echo "<TD CLASS=row_sep></TD>\n"
                    ."<TD>"
                        ."<TABLE BORDER=0 CELLSPADDING=0>\n"
                        ."<TR>"
                        ."<TD>$unit_liv[type] (".$unit_liv['amount'].")</TD>\n"
                        ."</TR>\n"
                        ."<TR>"
                        ."<TD>"
                        ."<INPUT CLASS=edit_area TYPE=TEXT NAME=\"livestock[".$unit_liv['type']."]\" VALUE=''>\n"
                        ."</TD>"
                        ."</TR>"
                        ."</TABLE>\n"
                    ."</TD>";

            $livestock->MoveNext();
        }
        $n++;
    }
    while ($i < 4)
    {
        echo "<TD CLASS=row_sep WIDTH=0></TD>\n"
            ."<TD>&nbsp;</TD>";
        $i++;
    }
    echo "</TR>\n";
    if ($n==0)
    {
        echo "<TR WIDTH=\"100%\"><TD COLSPAN=8><FONT COLOR=WHITE><CENTER>You have no livestock to transfer</FONT></CENTER></TD></TR>\n";
        echo "<TR WIDTH=\"100%\"><TD COLSPAN=8>&nbsp;</TD></TR>\n";
        echo "<TR WIDTH=\"100%\" BGCOLOR=\"$color_header\"><TD COLSPAN=8>&nbsp;</TD></TR>\n";
    }
    else
    {
        echo "<TR WIDTH=\"100%\"><TD COLSPAN=8>&nbsp;</TD></TR>\n";
        echo "<TR WIDTH=\"100%\"><TD COLSPAN=8>&nbsp;</TD></TR>\n";
        echo "<TR WIDTH=\"100%\"><TD COLSPAN=8>Click the Submit Transfer Button at the <strong>TOP</strong> of this page to transfer the specified amounts. Click the button below *ONLY* if you wish to get rid of these Critters</TD></TR>\n";
        echo "<TR WIDTH=\"100%\" BGCOLOR=\"$color_header\"><TD COLSPAN=8><INPUT TYPE=SUBMIT NAME=dump Value='Release Livestock'>&nbsp; WARNING! The specified amounts will be *LOST* forever if you click this button!</TD></TR>\n";
    }

    echo "</TABLE>\n";
}
else
{
    echo "<TABLE BGCOLOR=\"$color_table\" BORDER=0 CELLPADDING=1 CELLSPACING=0 ALIGN=CENTER WIDTH=\"80%\">\n";
    echo "<TR BGCOLOR=\"$color_header\" WIDTH=\"100%\">"
        ."<TD COLSPAN=8>"
        ."<FONT CLASS=page_subtitle>Transferring Livestock from $from_tribe to $to_tribe</FONT>\n"
        ."</TD>"
        ."</TR>";
    echo "<TR WIDTH=\"100%\" BGCOLOR=\"$color_header\"><TD COLSPAN=12>&nbsp;</TD></TR>\n";
    echo "<TR WIDTH=\"100%\"><TD>&nbsp;</TD>\n";

    if ($dump=="Yes")
    {
        echo "<TR><TD>The following beasties freed to, once more, happily roam the countryside never more to be enslaved by the bonds of man!</TD></TR><BR>&nbsp;\n";
        echo "<TR><TD>Freed</TD><TD>Amount</TD></TR>\n";
    }
    else
    {
        echo "<TR><TD>Transferring</TD><TD>Amount</TD><TD>From</TD><TD>To</TD></TR>\n";
    }


    $n = 0;
    foreach( $_POST['livestock'] as $key => $liv )
    {
        $rc = $n % 2;
        $giv = $db->Execute("SELECT * FROM $dbtables[livestock] WHERE tribeid = '$from_tribe' AND type = '$key'");
       db_op_result($giv,__LINE__,__FILE__);
        $giv_liv = $giv->fields;

        $rec = $db->Execute("SELECT * FROM $dbtables[livestock] WHERE tribeid = '$to_tribe' AND type = '$key'");
        db_op_result($rec,__LINE__,__FILE__);
        $rec_liv = $rec->fields;

        if( $dump=="Yes" && $liv > 0 )
        {
            $n++;
            $query = $db->Execute("UPDATE $dbtables[livestock] SET amount = amount - $liv WHERE tribeid = '$from_tribe' AND type = '$key'");
            db_op_result($query,__LINE__,__FILE__);
            if( $liv && $game_debug_xfer )
            {
                $query = $db->Execute("INSERT INTO $dbtables[logs] "
                            ."VALUES("
                            ."'',"
                            ."'$month[count]',"
                            ."'$year[count]',"
                            ."'0000',"
                            ."'0000.00',"
                            ."'DEBUG',"
                            ."'$stamp',"
                            ."'LIV TRANS: $from_tribe dumped $liv (liv) $key (key).')");
                 db_op_result($query,__LINE__,__FILE__);
            }

            echo "<TR CLASS=row_color$rc WIDTH=\"100%\">\n"
                ."<TD>$key</TD>"
                ."<TD>$liv</TD>"
                ."</TR>\n";

        include("weight.php");
        }

        if( $dump=="No" && ISSET($rec_liv['amount']) && $liv > 0 )
        {
            $n++;
            if ($from_tribe==$to_tribe && $dump=="No")
            {
                echo "<TR><TD COLSPAN=4><FONT SIZE=+1 COLOR=white><CENTER>You can't transfer livestock from a tribe to itself.</CENTER><BR></TD></TR>\n";
                echo "<TR WIDTH=\"100%\"><TD COLSPAN=8>&nbsp;</TD></TR>\n";
                echo "<TR WIDTH=\"100%\" BGCOLOR=\"$color_header\"><TD COLSPAN=8>&nbsp;</FONT></TD></TR>\n";
                echo "</TABLE>\n";
                page_footer();
            }

            if( $giv_liv['amount'] >= $liv )
            {
                $rec_liv['amount'] += $liv;
                $giv_liv['amount'] -= $liv;
                $upgrade = $liv;
                if( !$upgrade )
                {
                    $upgrade = 0;
                }

                $query = $db->Execute("UPDATE $dbtables[livestock] "
                                ."SET amount = amount + $liv "
                                ."WHERE type = '$key' "
                                ."AND tribeid = '$to_tribe'");
                 db_op_result($query,__LINE__,__FILE__);
                $query = $db->Execute("UPDATE $dbtables[livestock] "
                                ."SET amount = amount - $liv "
                                ."WHERE type = '$key' "
                                ."AND tribeid = '$from_tribe'");
                 db_op_result($query,__LINE__,__FILE__);
                echo "<TR CLASS=row_color$rc>"
                    ."<TD>$give_liv[type]</TD>"
                    ."<TD ALIGN=LEFT>$upgrade</TD>"
                    ."<TD>$from_tribe</TD>"
                    ."<TD>$to_tribe</TD>"
                    ."</TR>";

                if( $game_debug_xfer )
                {
                $query = $db->Execute("INSERT INTO $dbtables[logs] "
                            ."VALUES("
                            ."'',"
                            ."'$month[count]',"
                            ."'$year[count]',"
                            ."'0000',"
                            ."'0000.00',"
                            ."'DEBUG',"
                            ."'$stamp',"
                            ."'LIV TRANS: $from_tribe transfers $upgrade $giv_liv[type] to $to_tribe.')");
                  db_op_result($query,__LINE__,__FILE__);
                }

                include("weight.php");
            }
            else
            {
                $n++;
                echo "<TR CLASS=row_color$rc>"
                    ."<TD>$giv_liv[type]</TD>"
                    ."<TD>$upgrade</TD>\n"
                    ."<TD COLSPAN=2><FONT SIZE=+1 COLOR=WHITE>You do not have that many.</FONT></TD>\n"
                    ."</TR>\n";
            }
        }
    }
    echo "<TR WIDTH=\"100%\"><TD COLSPAN=8>&nbsp;</TD></TR>\n";
    echo "<TR WIDTH=\"100%\"><TD COLSPAN=8>&nbsp;</TD></TR>\n";
    echo "<TR WIDTH=\"100%\" BGCOLOR=\"$color_header\"><TD COLSPAN=8>&nbsp;</FONT></TD></TR>\n";
    echo "</TABLE>\n";
}

?>
