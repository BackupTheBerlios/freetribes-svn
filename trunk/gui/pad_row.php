<?php
//
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

function GUI_pad_row ($num_cols, $size_sub_cols, $num_rows, $cols_in_last_row)
{
		if ( $cols_in_last_row < ($num_cols/$size_sub_cols) )
		{
			$record_pad=$size_sub_cols*(($num_cols/$size_sub_cols)-$cols_in_last_row);
			if ($num_rows == 1)
			{
				echo "<TD VALIGN=TOP ";
					echo "width=\"100%\" colspan=$record_pad";
				echo ">"
					."&nbsp;</TD>";
			}
		}
}

?>