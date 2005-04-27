<?php
// Globally useful GUI routines
// Author List: Aerig
// Last Modified: 13th Aug 04
// Change List:


//Starts a table with a fancy border as defined by the current theme 
function GUI_open_table1 ($width, $height, $heading)
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
function GUI_table1_record ($width)
{
	echo "</TD><TD class=table1_td_cr></TD>"
		."</TR>";
	echo "<TR>"
		."<TD class=table1_td_cl></TD>"
		."<TD width=\"$width\" valign=top class=table1_td_cc>";
}


function GUI_close_table1 ($width, $height)
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


function GUI_open_table2 ($width, $height, $heading)
{
	echo '<TABLE width=\"100%\" height=\"$height\" BORDER=0 CELLSPACING=0 CELLPADDING=0>';

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


function GUI_table2_record ($width)
{
	echo "</TD><TD class=table2_td_cr></TD>";
	echo "</TR>";

	echo "<TR>"
		."<TD class=table2_td_cl></TD>"
		."<TD width=\"$width\" valign=top class=table2_td_cc>";
}


function GUI_close_table2 ($width, $height)
{
	echo "</TD><TD height=\"$height\" class=table2_td_cr></TD>";
	echo "</TR>";

	echo "<TR>"
		."<TD class=table2_td_bl></TD>"
		."<TD width=\"$width\" class=table2_td_bc></TD>"
		."<TD class=table2_td_br></TD></TR>"
		."</TABLE>";
}

function GUI_menu_option ($href, $target, $text)
{
	echo "<font class=\"menu_opt\"><a href=\"$href\" target=\"$target\">&nbsp;$text</a></font><br>";
}


// GUI_pad_row
// Assuming the previous display of a table with indeterminate number of rows and
// $actual_cols wide, where the data is laid out regularly in
// multiples of $sub_cols this function will pad the last row out with empty cells
// $this_row should be the current row number beginning at 1 and
// $cols_in_last_row should be the number of sub columns displayed on the last line displayed

// eg
// if
//   you have a table that actually has 12 columns
//   and you are printing 3 columns at a time
//   and you have just printed 2 subcolumns (each of those 3 actual columns wide)
//   and you are on row 5
// then
//   GUI_pad_row (12, 3, 5, 2);
// would pad the line you have just printed to occupy the full width of the table
// Implemented this slightly weird way because the counters for printing a table
// this way are more likely to be directly available in this format

function GUI_pad_row ($actual_cols, $sub_cols, $this_row, $cols_in_last_row)
{
		if ( $cols_in_last_row < ($actual_cols/$sub_cols) )
		{
			$record_pad=$sub_cols*(($actual_cols/$sub_cols)-$cols_in_last_row);
			echo "<TD ";
			if ($this_row == 1)
			{
				echo "width=\"100%\" ";
			}
			echo "colspan=$record_pad>"
				."</TD>";
		}
}


?>