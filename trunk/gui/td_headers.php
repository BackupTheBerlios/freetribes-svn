<?php

function td_rounded_header ($caption)
{
echo "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=\"100%\">"
	."<TR>"
//	."<TD><IMG SRC=themes/$_SESSION[theme]/images/title_side_5_l.gif BORDER=0></TD>"
	."<TD HEIGHT=33 style=\" border: thin outset; background : url(themes/$_SESSION[theme]/images/title_centre_1.gif);\" ALIGN=LEFT VALIGN=MIDDLE>"
	."&nbsp;<B>$caption</B>&nbsp;</TD>"
//	."<TD><IMG SRC=themes/$_SESSION[theme]/images/title_side_5_r.gif BORDER=0></TD>"
	."</TR>"
	."</TABLE>";
}


function td_square_header ($caption)
{
echo "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=\"100%\">"
	."<TR>"
	."<TD WIDTH=5><IMG SRC=themes/$_SESSION[theme]/images/title_side_5_l.gif BORDER=0></TD>"
	."<TD style=\"background : url(themes/$_SESSION[theme]/images/title_centre_1.gif);\" ALIGN=LEFT VALIGN=MIDDLE>"
	."&nbsp;<B>$caption</B>&nbsp;</TD>"
	."<TD WIDTH=5><IMG SRC=themes/$_SESSION[theme]/images/title_side_5_r.gif BORDER=0></TD>"
	."</TR>"
	."</TABLE>";
}


?>