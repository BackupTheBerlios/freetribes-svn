<?php
//Routines to create bordered tables in style 2

function table2_fancy_open ($width, $height, $heading)
{
	echo "<TABLE width=\"45%\" height=\"$height\" BORDER=0 CELLSPACING=0 CELLPADDING=0 ALIGN=CENTER>";

	if ($heading == "")
	{
		echo "<TR>"
			."<TD class=table2_td_tl></TD>"
			."<TD width=\"$width\" class=table2_td_tc></TD>"
			."<TD class=table2_td_tr></TD>"
			."</TR>";
	} else {
		echo "<TR>"
			."<TD class=table2_th_l></TD>"
			."<TD width=\"$width\" align=middle valign=center class=table2_th_c>$heading</TD>"
			."<TD class=table2_th_r></TD>"
			."</TR>";
	}

	echo "<TR>"
		."<TD class=table2_td_cl></TD>"
		."<TD width=\"$width\" height=\"$height\" valign=top class=table2_td_cc>";
}


function table2_fancy_record ($width)
{
	echo "</TD><TD class=table2_td_cr></TD>";
	echo "</TR>";

	echo "<TR>"
		."<TD class=table2_td_cl></TD>"
		."<TD width=\"$width\" valign=top class=table2_td_cc>";
}


function table2_fancy_close ($width, $height)
{
	echo "</TD><TD height=\"$height\" class=table2_td_cr></TD>";
	echo "</TR>";

	echo "<TR>"
		."<TD class=table2_td_bl></TD>"
		."<TD width=\"$width\" class=table2_td_bc></TD>"
		."<TD class=table2_td_br></TD></TR>"
		."</TABLE>";
}

?>
