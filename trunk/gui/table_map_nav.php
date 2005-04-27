<?php
echo "<TABLE BORDER=0 CELLPADDING=0 WIDTH=\"100%\">"
	."<TR><TD BGCOLOR=\"$color_header\" COLSPAN=3 ALIGN=CENTER>Navigation</TD></TR>"
	."<TR>"
	."<TD ROWSPAN=3 ALIGN=CENTER VALIGN=CENTER><A HREF=mapping.php?target=$west_targ><IMG SRC=images/arrowwest.gif WIDTH=25 BORDER=0></A></TD>"
	."<TD ALIGN=CENTER>"
		."<TABLE BORDER=0 CELLPADDING=0>"
		."<TR><TD ALIGN=CENTER><A HREF=mapping.php?target=$north_targ><IMG SRC=images/arrownorth.gif HEIGHT=25 BORDER=0></A></TD></TR>"
		."<TR><TD ALIGN=CENTER><A HREF=mapping.php?target=$tribe_position>Back</A></TD></TR>"
		."<TR><TD ALIGN=CENTER><A HREF=mapping.php?target=$south_targ><IMG SRC=images/arrowsouth.gif HEIGHT=25 BORDER=0></A></TD>"
		."</TR>"
		."</TABLE>"
	."</TD>"
	."<TD ROWSPAN=3 ALIGN=CENTER VALIGN=CENTER><A HREF=mapping.php?target=$east_targ><IMG SRC=images/arroweast.gif WIDTH=25 BORDER=0></A></TD>"
	."</TR>"
	."</TABLE>";
?>