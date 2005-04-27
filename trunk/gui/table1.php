<?php
//Routines to create bordered tables in style 1

function table1_open ($width, $height, $heading)
{
	echo '<TABLE width=$width height=\"$height\" BORDER=0 CELLSPACING=0 CELLPADDING=0>';

	if ($heading == "")
	{
		echo "<TR>"
			."<TD class=table1_td_tl></TD>"
			."<TD width=\"$width\" class=table1_td_tc></TD>"
			."<TD class=table1_td_tr></TD>"
			."</TR>";
	} else {
		echo "<TR>"
			."<TD class=table1_th_l></TD>"
			."<TD width=\"$width\" align=middle valign=center class=table1_th_c>$heading</TD>"
			."<TD class=table1_th_r></TD>"
			."</TR>";
	}

	echo "<TR>"
		."<TD class=table1_td_cl></TD>"
		."<TD width=\"$width\" height=\"$height\" valign=top class=table1_td_cc>";
}


//Inserts a record separator
function table1_record ($width)
{
	echo "</TD><TD class=table1_td_cr></TD>"
		."</TR>";
	echo "<TR>"
		."<TD class=table1_td_cl></TD>"
		."<TD width=\"$width\" valign=top class=table1_td_cc>";
}


function table1_close ($width, $height)
{
	echo "</TD><TD height=\"$height\" class=table1_td_cr></TD>"
		."</TR>";

	echo "<TR>"
		."<TD class=table1_td_bl></TD>"
		."<TD width=\"$width\" class=table1_td_bc></TD>"
		."<TD class=table1_td_br></TD>"
		."</TR>"
		."</TABLE>";
}

?>