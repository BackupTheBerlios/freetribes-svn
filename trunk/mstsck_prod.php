<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: main.php
error_reporting  (E_ERROR | E_WARNING | E_PARSE | !E_NOTICE);

session_start();
header("Cache-control: private");
include("config.php");

page_header("Admin - MSTSCK Products");

connectdb();

if (ISSET($_REQUEST['show_usage']))
{
	$show_usage = $_REQUEST['show_usage'];
}
else
{
	$show_usage = '-1';
}
$show_usage++;
$show_usage = $show_usage % 2;


// Set get type of current product being defined
if (ISSET($_REQUEST['res_type']))
{
	$res_type = $_REQUEST['res_type'];
}
else
{
	$res_type = '';
}


echo "<TABLE BORDER=0>"
."<TR>"
."<TD>"
."<FORM METHOD=post action=mstsck_prod.php>"
."<INPUT TYPE=HIDDEN NAME=res_type VALUE=\"$res_type\">"
."<INPUT TYPE=HIDDEN NAME=show_usage VALUE=$show_usage> "
."<INPUT TYPE=SUBMIT VALUE=Usage>"
."</FORM>"
."</TD>"
."<TD>"
."<FORM METHOD=post action=mstsck_prod.php>"
."<INPUT TYPE=HIDDEN NAME=res_type VALUE=\"$res_type\">"
."<INPUT TYPE=SUBMIT VALUE=\"Different $res_type\">"
."</FORM>"
."</TD>"
."</TR>"
."</TABLE>";

if ($show_usage=='1')
{
	echo "To edit an existing product, select the product and then click Edit"
	."<BR>To add a new method for making a product, slect the product and click New Method"
	."<P>Before you add a new methods of making something, please make sure that that method is not already defined. You do that by selecting the product, clicking Edit and then carefully examining each product method in turn.";
}

// MAIN CODE STARTS NOW

// Set get id of current product being defined

// I don't think this is actually used now
// If it s, it should really be replaced by using res_key
if (ISSET($_REQUEST['res_id']))
{
	$res_id = $_REQUEST['res_id'];
}
else
{
	$res_id = '0';
}

// Set get id of current requirement being defined
if (ISSET($_REQUEST['id']))
{
	$id = $_REQUEST['id'];
}
else
{
	$id = '0';
}

// Set get key of current product being defined
if (ISSET($_REQUEST['res_key']))
{
	$res_key = $_REQUEST['res_key'];
}
else
{
	$res_key = '';
}

// Set get method of current product being defined
if (ISSET($_REQUEST['method']))
{
	$method = $_REQUEST['method'];
}
else
{
	$method = '0';
}


/*
echo "<PRE>";
print_r ($_REQUEST);
echo "</PRE>";
*/


// Cludge to allow rest of code to need less alteration in other files
// which will deal with different rq_types
//$res_type = "Product";

// Get the default table defs from gd_rq_tables for the current res_type
$default_res = $db->Execute("SELECT * FROM ".$dbtables['gd_rq_tables']." "
                            ."WHERE entry_type='res' "
                            ."AND r_type='$res_type'");
$def_res = array();
$def_res[$res_type] = $default_res->fields;

//The next lot are the default tables for each type of requirement
$def_rqcl = array();
$rqcl = $db->Execute("SELECT * FROM ".$dbtables['gd_rq_tables']." "
                     ."WHERE entry_type='rq_cl' ");
while (!$rqcl->EOF)
{
	$def_rqcl[$rqcl->fields['r_type']] = $rqcl->fields;
	$rqcl->MoveNext();
}


if ($res_key<>"")
{
	if ($_REQUEST['op']=="New Requirement")
	{
		$results = $db->Execute("SELECT * FROM ".$dbtables[$def_res[$res_type]['r_table']]." "
		                        ."WHERE ".$def_res[$res_type]['r_keyf']."='$res_key' "
		                        ."LIMIT 1");
		/*
				$key_data = $db->Execute("SELECT "
										."`".$def_rqcl[$_REQUEST['Skill']]['r_keyf']."` "
										."FROM "
										."`".$dbtables[$def_rqcl[$_REQUEST['Skill']]['r_table']]."` "
										."LIMIT 1");
				$a_key = $key_data->fields[$def_rqcl[$_REQUEST['Skill']]['r_keyf']];
		*/
		$key_data = $db->Execute("SELECT "
		                         ."`".$def_rqcl['Skill']['r_keyf']."` "
		                         ."FROM "
		                         ."`".$dbtables[$def_rqcl['Skill']['r_table']]."` "
		                         ."LIMIT 1");
		$a_key = $key_data->fields[$def_rqcl['Skill']['r_keyf']];

		$db->Execute ("INSERT INTO $dbtables[gd_rq] "
		              ."VALUES ("
		              ."'',"
		              ."'".$res_type."',"
		              ."'".$def_res[$res_type]['r_table']."',"				// eg Products
		              ."'".$def_res[$res_type]['r_idf']."',"					// eg prod_id
		              ."'".$results->fields[$def_res[$res_type]['r_idf']]."',"
		              ."'".$def_res[$res_type]['r_keyf']."',"					// eg long_name
		              ."'".$results->fields[$def_res[$res_type]['r_keyf']]."',"
		              ."'Skill',"
		              ."'".$def_rqcl['Skill']['r_table']."',"
		              ."'".$def_rqcl['Skill']['r_idf']."',"
		              ."'0',"
		              ."'".$def_rqcl['Skill']['r_keyf']."',"
		              ."'$a_key',"
		              ."'-1',"
		              ."'".$def_rqcl['Skill']['r_goods_tribe']."',"
		              ."'".$def_rqcl['Skill']['r_hex_id']."',"
		              ."'$method',"
		              ."'".$def_rqcl['Skill']['cl_table']."',"
		              ."'".$def_rqcl['Skill']['cl_clanidf']."',"
		              ."'".$def_rqcl['Skill']['cl_tribeidf']."',"
		              ."'".$def_rqcl['Skill']['cl_keyf']."',"
		              ."'".$def_rqcl['Skill']['cl_valf']."'"
		              .")");

		$_REQUEST['op']="Edit";
	}


	if ($_REQUEST['op']=='Delete')
	{
		$last_method = $db->Execute("SELECT method FROM $dbtables[gd_rq] "
		                            ."WHERE id='$id' ");
		$last_method = $last_method->fields['method'];


		$db->Execute ("DELETE FROM $dbtables[gd_rq] WHERE id='$id'");

		$dead = $db->Execute("SELECT method FROM $dbtables[gd_rq] "
		                     ."WHERE method='$last_method' "
		                     ."AND res_key='$res_key' "
		                     ."AND res_type='$res_type'");
		if ($dead->EOF)
		{
			$method='0';
		}

		$_REQUEST['op']="Edit";
	}


	if ($method=='0')  // ASK USER FOR METHOD TO EDIT
	{
		// Get number of methods for getting this res
		$method = $db->Execute("SELECT res_type, res_key, MAX(method) AS method "
		                       ."FROM $dbtables[gd_rq] "
		                       ."GROUP BY res_key "
		                       ."HAVING res_key='$res_key' "
		                       ."AND res_type='$res_type' ");
		$method = $method->fields['method'];


		if ($_REQUEST['op']=="New Method")
		{
			$method++;

			$results = $db->Execute("SELECT * FROM ".$dbtables[$def_res[$res_type]['r_table']]." "
			                        ."WHERE ".$def_res[$res_type]['r_keyf']."='$res_key' "
			                        ."LIMIT 1");

			$db->Execute ("INSERT INTO $dbtables[gd_rq] "
			              ."VALUES ("
			              ."'',"
			              ."'".$res_type."',"
			              ."'".$def_res[$res_type]['r_table']."',"  // eg Products
			              ."'".$def_res[$res_type]['r_idf']."',"    // eg prod_id
			              ."'".$results->fields[$def_res[$res_type]['r_idf']]."',"
			              ."'".$def_res[$res_type]['r_keyf']."',"   // eg long_name
			              ."'".$results->fields[$def_res[$res_type]['r_keyf']]."',"
			              ."'Skill',"
			              ."'".$def_rqcl['Skill']['r_table']."',"
			              ."'".$def_rqcl['Skill']['r_idf']."',"
			              ."'0',"
			              ."'".$def_rqcl['Skill']['r_keyf']."',"
			              ."'".$results->fields['skill_abbr']."',"
			              ."'".$results->fields['skill_level']."',"
						  ."'".$def_rqcl['Skill']['r_goods_tribe']."',"
						  ."'".$def_rqcl['Skill']['r_hex_id']."',"
						  ."'$method',"
			              ."'".$def_rqcl['Skill']['cl_table']."',"
			              ."'".$def_rqcl['Skill']['cl_clanidf']."',"
			              ."'".$def_rqcl['Skill']['cl_tribeidf']."',"
			              ."'".$def_rqcl['Skill']['cl_keyf']."',"
			              ."'".$def_rqcl['Skill']['cl_valf']."'"
			              .")");
		}



		if ($method > 1)
		{
			echo "<P ALIGN=CENTER>"
			."<TABLE>"
			."<TR CLASS=color_header>"
			."<TD COLSPAN=5>&nbsp;<BR><CENTER>Select method to produce <B>$res_key</b><BR>&nbsp;</TD>"
			."</TR>"
			."<TR CLASS=color_header>"
			."<TD>&nbsp;</TD>"
			."<TD><B>&nbsp;Method&nbsp;</B></TD>"
			."<TD><B>&nbsp;Req type&nbsp;</B>"
			."<TD><B>&nbsp;Requirement&nbsp;</B>"
			."<TD><B>&nbsp;Value&nbsp;</B>"
			."</TR>";
			for ($i=1; $i<=$method; $i++) // LIST SUMMARY OF METHODS
			{
				$rc = ($i-1) % 2;
				//Get the requirements for one method
				$reqs = $db->Execute("SELECT * FROM $dbtables[gd_rq] "
				                     ."WHERE res_key='$res_key' "
				                     ."AND method='$i' "
				                     ."AND res_type='$res_type' ");
				$num_reqs = $reqs->rowcount();

				$n=1;
				while (!$reqs->EOF)
				{
					$this_req = $reqs->fields;
					echo "<FORM METHOD=POST ACTION=mstsck_prod.php>";
					echo "<TR CLASS=row_color$rc>";
					if ($n==1)
					{
						echo "<TD VALIGN=TOP ROWSPAN=$num_reqs>"
						//							."<INPUT TYPE=HIDDEN NAME=res_id VALUE=$res_id>"
						."<INPUT TYPE=HIDDEN NAME=res_type VALUE=\"$res_type\">"
						."<INPUT TYPE=HIDDEN NAME=res_key VALUE=\"$res_key\">"
						."<INPUT TYPE=HIDDEN NAME=method VALUE=".$this_req['method'].">"
						."<INPUT TYPE=SUBMIT NAME=op VALUE=Edit>"
						."&nbsp;</TD>"
						."<TD VALIGN=TOP ROWSPAN=$num_reqs>&nbsp;".$this_req['method']."</TD>";
					}

					echo "<TD VALIGN=TOP>&nbsp;".$this_req['rq_type']."</TD>"
					."<TD VALIGN=TOP>&nbsp;".$this_req['rq_key']."</TD>"
					."<TD VALIGN=TOP>&nbsp;".$this_req['rq_val']."</TD>"
					."</TR>"
					."</FORM>";
					$n++;
					$reqs->MoveNext();
				}
			}
			echo "</TABLE>";
			page_footer();
		}
		else
		{
			$method=1;
		}

	}


	$rq = $db->Execute("SELECT * FROM ".$dbtables['gd_rq']." "
	                   ."WHERE res_key='$res_key' "
	                   ."AND res_type='$res_type' "
	                   ."AND method='$method' ");

	if (!$rq->EOF)
	{
		echo "Currently defining $res_type <b>"
		.$rq->fields['res_key']
		."</b>.";
		$res_id = $rq->fields['res_id'];
	}
	else
	{
		echo "This $res_type does not yet exist in gd_rq.<BR>"
		."Adding a default entry now!<BR>";

		$results = $db->Execute("SELECT * FROM ".$dbtables[$def_res[$res_type]['r_table']]." "
		                        ."WHERE ".$def_res[$res_type]['r_keyf']."='$res_key' "
		                        ."LIMIT 1");
		$db->Execute ("INSERT INTO $dbtables[gd_rq] "
		              ."VALUES ("
		              ."'',"
		              ."'".$res_type."',"
		              ."'".$def_res[$res_type]['r_table']."',"  // eg Products
		              ."'".$def_res[$res_type]['r_idf']."',"    // eg prod_id
		              ."'".$results->fields[$def_res[$res_type]['r_idf']]."',"
		              ."'".$def_res[$res_type]['r_keyf']."',"   // eg long_name
		              ."'".$results->fields[$def_res[$res_type]['r_keyf']]."',"
		              ."'Skill',"
		              ."'".$def_rqcl['Skill']['r_table']."',"
		              ."'".$def_rqcl['Skill']['r_idf']."',"
		              ."'0',"
		              ."'".$def_rqcl['Skill']['r_keyf']."',"
		              ."'".$results->fields['skill_abbr']."',"
		              ."'".$results->fields['skill_level']."',"
		              ."'".$def_rqcl['Skill']['r_goods_tribe']."',"
		              ."'".$def_rqcl['Skill']['r_hex_id']."',"
		              ."'$method',"
		              ."'".$def_rqcl['Skill']['cl_table']."',"
		              ."'".$def_rqcl['Skill']['cl_clanidf']."',"
		              ."'".$def_rqcl['Skill']['cl_tribeidf']."',"
		              ."'".$def_rqcl['Skill']['cl_keyf']."',"
		              ."'".$def_rqcl['Skill']['cl_valf']."'"
		              .")");

		echo "<FORM METHOD=post action=mstsck_prod.php>"
		."<INPUT TYPE=SUBMIT VALUE=Continue>"
		."<INPUT TYPE=HIDDEN NAME=res_id VALUE=".$results->fields[$def_res[$res_type]['r_idf']].">"
		."<INPUT TYPE=HIDDEN NAME=res_type VALUE=\"$res_type\">"
		."<INPUT TYPE=HIDDEN NAME=res_key VALUE=\"$res_key\">"
		."<INPUT TYPE=HIDDEN NAME=method VALUE=1>"
		."</FORM>";
	}
}
else
{
	//Get the available results from the appropriate table
	$results = $db->Execute("SELECT "
	                        .$def_res[$res_type]['r_idf'].", "
	                        .$def_res[$res_type]['r_keyf']." "
	                        ."FROM ".$dbtables[$def_res[$res_type]['r_table']]." "
	                        ."GROUP BY ".$def_res[$res_type]['r_keyf']." "
	                        ."ORDER BY ".$def_res[$res_type]['r_keyf']);

	echo "<FORM METHOD=POST ACTION=mstsck_prod.php>"
	."<TABLE BORDER=0>"
	."<TR>"
	."<TD>"
	."<SELECT NAME=res_key>";

	while (!$results->EOF)
	{
		$res_key = $results->fields[$def_res[$res_type]['r_keyf']];
		echo "<OPTION VALUE=\"$res_key\">$res_key";
		$results->MoveNext();
	}

	echo "</SELECT>"
	."</TD>"
	."<TD>"
	."<INPUT TYPE=HIDDEN NAME=res_type VALUE=\"$res_type\">"
	."<INPUT TYPE=HIDDEN NAME=method VALUE=0>"
	."<INPUT TYPE=SUBMIT NAME=op VALUE=\"Edit\">"
	."</TD>"
	."<TD>"
	."<INPUT TYPE=SUBMIT NAME=op VALUE=\"New Method\">"
	."</TD>"
	."</TR>"
	."</TABLE>"
	."</FORM>";

}

if ($_REQUEST['op']=="Edit" || $_REQUEST['op']=="Update" || $_REQUEST['op']=="Continue")
{

	echo "<FORM METHOD=POST ACTION=mstsck_prod.php>"
	."<INPUT TYPE=HIDDEN NAME=method VALUE=$method>"
	."<INPUT TYPE=HIDDEN NAME=res_type VALUE=\"$res_type\">"
	."<INPUT TYPE=HIDDEN NAME=method VALUE=$res_key>"
	."<TABLE BORDER=0>"
	."<TR CLASS=color_header>"
	."<TD COLSPAN=6><B>&nbsp;Result Table Definition</B></TD>"
	."</TR>"
	."<TR CLASS=color_header>"
	."<TD COLSPAN=6>&nbsp;These selectors allow you to define where to find the basic information about what you are trying to do.<BR>&nbsp;Generally, you should <b>not</b> need to change these.</TD>"
	."</TR>"
	."<TR CLASS=color_row1>"
	."<TD>"
	."Type"
	."</TD>"
	."<TD>"
	."Table"
	."</TD>"
	."<TD>"
	."Key Field"
	."</TD>"
	."<TD>"
	."ID Field"
	."</TD>"
	."<TD>"
	."KEY"
	."</TD>"
	."<TD>"
	."ID"
	."</TD>"		."</TR>";

	echo "<TR CLASS=color_row1>";


	// If we have just changed the res_table, then update the value in gd_rq


	if (ISSET($_REQUEST['res_type']))
	{
		if ($_REQUEST['res_type']<>$rq->
		        fields['res_type'])
		{
			$result = $db->Execute("UPDATE $dbtables[gd_rq] "
			                       ."SET"
			                       ."  `res_type`='".$_REQUEST['res_type']."' "
			                       ."WHERE method='$method' "
			                       ."AND res_key='$res_key' "
			                       ."AND res_type='$res_type' ");
		}
		$res_type = $_REQUEST['res_type'];
	}
	else
	{
		$res_type = $rq->fields['res_type'];
	}


	// Display res_type selector

	if ($change_constants=='1')
	{
		$sel_res_type = array (
		                    "Intrinsic" => "",
		                    "Livestock" => "",
		                    "Product"   => "",
		                    "Skill"     => "",
		                    "Structure" => ""
		                );
		$sel_res_type[$res_type] = "SELECTED";

		echo "<TD>"
		."<SELECT NAME=res_type>";
		foreach ($sel_res_type AS $key => $value)
		{
			echo "<OPTION $value>$key";
		}
		echo "</TD>";
	}
	else
	{
		echo "<TD>$res_type</TD>";
	}


	// If we have just changed the res_table, then update the value in gd_rq


	if (ISSET($_REQUEST['res_table']))
	{
		if ($_REQUEST['res_table']<>$rq->
		        fields['res_table'])
		{
			$result = $db->Execute("UPDATE $dbtables[gd_rq] "
			                       ."SET"
			                       ."  `res_table`='".$_REQUEST['res_table']."' "
			                       ."WHERE method='$method' "
			                       ."AND res_key='$res_key' "
			                       ."AND res_type='$res_type' ");
		}
		$res_table = $_REQUEST['res_table'];
	}
	else
	{
		$res_table = $rq->fields['res_table'];
	}


	// Display res_table selector


	if ($change_constants=='1')
	{
		$tables = $db->Execute("SHOW TABLES FROM $dbname");
		$tkeys = array_keys($tables->fields); // Get the name of the column that is returned
		$tkey = $tkeys[0];

		echo "<TD>"
		."<SELECT NAME=res_table>";
		$i=0;
		while (!$tables->EOF)
		{
			$table = $tables->fields[$tkey];
			$table_x = str_replace ($db_prefix, "", $table);
			echo "<OPTION VALUE=$table_x";
			if ($table_x==$res_table)
			{
				echo " SELECTED";
			}
			echo ">$table_x";

			$i++;
			$tables->MoveNext();
		}
		echo "</SELECT>"
		."</TD>";
	}
	else
	{
		echo "<TD>$res_table</TD>";
	}


	// If we have just changed the res_keyf, then update the value in gd_rq


	if (ISSET($_REQUEST['res_keyf']))
	{
		if ($_REQUEST['res_keyf']<>$rq->
		        fields['res_keyf'])
		{
			$result = $db->Execute("UPDATE $dbtables[gd_rq] "
			                       ."SET"
			                       ."  `res_keyf`='".$_REQUEST['res_keyf']."' "
			                       ."WHERE method='$method' "
			                       ."AND res_key='$res_key' "
			                       ."AND res_type='$res_type' ");
		}
		$res_keyf = $_REQUEST['res_keyf'];
	}
	else
	{
		$res_keyf = $rq->fields['res_keyf'];
	}

	// Display res_keyf selector



	if ($change_constants=='1')
	{
		echo "<TD>"
		."<SELECT NAME=res_keyf>";

		option_fields ($res_table, $res_keyf);

		echo "</SELECT>"
		."</TD>";
	}
	else
	{
		echo "<TD>$res_keyf</TD>";
	}


	// If we have just changed the res_idf, then update the value in gd_rq


	if (ISSET($_REQUEST['res_idf']))
	{
		if ($_REQUEST['res_idf']<>$rq->
		        fields['res_idf'])
		{
			$result = $db->Execute("UPDATE $dbtables[gd_rq] "
			                       ."SET"
			                       ."  `res_idf`='".$_REQUEST['res_idf']."' "
			                       ."WHERE method='$method' "
			                       ."AND res_key='$res_key' "
			                       ."AND res_type='$res_type' ");
		}
		$res_idf = $_REQUEST['res_idf'];
	}
	else
	{
		$res_idf = $rq->fields['res_idf'];
	}

	// Display res_idf selector


	if ($change_constants=='1')
	{
		echo "<TD>"
		."<SELECT NAME=res_idf>";

		option_fields ($res_table, $res_idf);

		echo "</SELECT>"
		."</TD>";
	}
	else
	{
		echo "<TD>$res_idf</TD>";
	}


	if ($change_constants=='1')
	{
		echo "<TD>"
		."<SELECT NAME=res_key>";


		// If we have just changed the res_keyf, then update the value in gd_rq


		if (ISSET($_REQUEST['res_key']))
		{
			if ($_REQUEST['res_key']<>$rq->
			        fields['res_key'])
			{
				$new_id = $db->Execute("SELECT `$res_keyf` FROM $dbtables[$res_table] "
				                       ."WHERE `$res_keyf`='".$_REQUEST['res_key']."' ");
				$new_id = $new_id->fields[$res_idf];
				$result = $db->Execute("UPDATE $dbtables[gd_rq] "
				                       ."SET"
				                       ."  `res_key`='".$_REQUEST['res_key']."', "
				                       ."  `res_id`='$new_id', "
				                       ."WHERE method='$method' "
				                       ."AND res_key='$res_key' "
				                       ."AND res_type='$res_type' ");
			}
			$res_key = $_REQUEST['res_key'];
		}
		else
		{
			$res_key = $rq->fields['res_key'];
		}

		option_rows ($res_table, $res_keyf, $rq->fields['res_key']);

		echo "</SELECT>"
		."</TD>";
	}
	else
	{
		echo "<TD><B>$res_key</B></TD>";
	}


	if ($change_constants=='1')
	{
		echo "<TD>"
		."<INPUT CLASS=edit_area TYPE=TEXT MAXLENGTH=10 SIZE=10 VALUE=$res_id>"
		."</TD>";
	}
	else
	{
		echo "<TD><B>$res_id</B></TD>";
	}

	echo "</TR>"
	."</TABLE>"
	."</FORM>";


	// NOW SHOW THE REQUIREMENTS ------------------------------------


	echo "<P>"
	."<TABLE BORDER=0>"
	."<TR CLASS=color_header>"
	."<TD COLSPAN=10><B>&nbsp;Requirement Table Definition</B></TD>"
	."</TR>"
	."<TR CLASS=color_header>"
	."<TD COLSPAN=10>&nbsp;These selectors allow you to define where to find the basic information about the requirements that are needed to achieve the result that you want to get. These define what is need to be able to do things so you may have to change them from time to time and will probably need to add new requirements.</TD>"
	."</TR>"
	."<TR CLASS=color_header>"
	."<TD>&nbsp;</TD>"
	."<TD><B>Type</B></TD>"
	."<TD><B>Table</B></TD>"
	."<TD><B>Key Field</B></TD>"
	."<TD><B>ID Field</B></TD>"
	."<TD><B>KEY</B></TD>"
	."<TD><B>ID</B></TD>"
	."<TD><B>Use GT?</B></TD>"
	."<TD><B>Value</B></TD>"
	."<TD>&nbsp;</TD>"
	."</TR>";

	$reqs = $db->Execute("SELECT * FROM $dbtables[gd_rq] "
	                     ."WHERE res_key='$res_key' "
	                     ."AND method='$method' "
	                     ."AND res_type='$res_type' "
	                     ."ORDER BY id ");
	$num_reqs = $reqs->rowcount();

	// The values in $_REQUEST get changed inside the next while loop,
	// so we make a copy of the array now so that we can reset $_REQUEST
	// to its original values every time we start the loop
	$r_bak = array();
	$r_bak = $_REQUEST;

	$r=0;
	while (!$reqs->EOF)
	{
		$_REQUEST = $r_bak;
		$rq = $reqs->fields;
		$id = $rq['id'];
		$rc = $r % 2;
		echo "\n\n<FORM METHOD=POST ACTION=mstsck_prod.php>"
		."<INPUT TYPE=HIDDEN NAME=id VALUE=$id>"
		."<INPUT TYPE=HIDDEN NAME=method VALUE=$method>"
		."<INPUT TYPE=HIDDEN NAME=res_key VALUE=\"$res_key\">"
		."<INPUT TYPE=HIDDEN NAME=res_type VALUE=\"$res_type\">"
		."<TR CLASS=row_color$rc>"
		."<TD><INPUT TYPE=SUBMIT NAME=op VALUE=Update></TD>";

		// If we have just changed the rq_type, then update the values in gd_rq

		$rq_type = $rq['rq_type'];

		if (ISSET($_REQUEST['rq_type']))
		{

			if ($_REQUEST['rq_type']<>$rq['rq_type'] && $_REQUEST['id']==$id)
			{
				$rq_type = $_REQUEST['rq_type'];

				$key_data = $db->Execute("SELECT "
				                         ."`".$def_rqcl[$rq_type]['r_keyf']."` "
				                         ."FROM "
				                         ."`".$dbtables[$def_rqcl[$rq_type]['r_table']]."` "
				                         ."LIMIT 1");
				$a_key = $key_data->fields[$def_rqcl[$rq_type]['r_keyf']];

				$result = $db->Execute("UPDATE $dbtables[gd_rq] "
				                       ."SET"
				                       ."  `rq_type`='".$rq_type."', "
				                       ."  `cl_table`='".$def_rqcl[$rq_type]['cl_table']."', "
				                       ."  `cl_clanidf`='".$def_rqcl[$rq_type]['cl_clanidf']."', "
				                       ."  `cl_tribeidf`='".$def_rqcl[$rq_type]['cl_tribeidf']."', "
				                       ."  `cl_keyf`='".$def_rqcl[$rq_type]['cl_keyf']."', "
				                       ."  `cl_valf`='".$def_rqcl[$rq_type]['cl_valf']."' "
				                       ."WHERE id=$id ");

				// Change these so that the rest of the selectors get set properly
				$_REQUEST['rq_table']       = $def_rqcl[$_REQUEST['rq_type']]['r_table'];
				$_REQUEST['rq_idf']         = $def_rqcl[$_REQUEST['rq_type']]['r_idf'];
				$rq['rq_id']                = -1;
				$_REQUEST['rq_keyf']        = $def_rqcl[$_REQUEST['rq_type']]['r_keyf'];
				$_REQUEST['rq_key']         = $a_key;
				$_REQUEST['rq_val']         = "";
				$_REQUEST['rq_goods_tribe'] = $def_rqcl[$_REQUEST['rq_type']]['r_goods_tribe'];
			}
		}


		// Display rq_type selector


		$sel_rq_type = array (
		                   "Actives"   => "",
		                   "Hex ID"    => "",
		                   "Intrinsic" => "",
		                   "Output"    => "",
		                   "Livestock" => "",
		                   "Product"   => "",
		                   "Skill"     => "",
		                   "Structure" => "",
		                   "Prod Unit" => "",
		                   "Resource"  => ""
		               );
		$sel_rq_type[$rq_type] = "SELECTED";

		echo "<TD>"
		."<SELECT NAME=rq_type>";
		foreach ($sel_rq_type AS $key => $value)
		{
			echo "<OPTION $value>$key";
		}
		echo "</TD>";


		// If we have just changed the rq_table, then update the value in gd_rq

		$rq_table = $rq['rq_table'];

		if (ISSET($_REQUEST['rq_table']))
		{
			if ($_REQUEST['rq_table']<>$rq['rq_table'] && $_REQUEST['id']==$id)
			{
				$result = $db->Execute("UPDATE $dbtables[gd_rq] "
				                       ."SET "
				                       ."  rq_table='".$_REQUEST['rq_table']."' "
				                       ."WHERE id='$id' ");

				$rq_table = $_REQUEST['rq_table'];
			}
		}


		// Display rq_table selector


		$tables = $db->Execute("SHOW TABLES FROM $dbname");
		$tkeys = array_keys($tables->fields); // Get the name of the column that is returned
		$tkey = $tkeys[0];

		echo "<TD>"
		."<SELECT NAME=rq_table>";
		$i=0;
		while (!$tables->EOF)
		{
			$table = $tables->fields[$tkey];
			$table_x = str_replace ($db_prefix, "", $table);
			echo "<OPTION VALUE=$table_x";
			if ($table_x==$rq_table)
			{
				echo " SELECTED";
			}
			echo ">$table_x";

			$i++;
			$tables->MoveNext();
		}
		echo "</SELECT>"
		."</TD>";


		// If we have just changed the rq_keyf, then update the value in gd_rq

		$rq_keyf = $rq['rq_keyf'];

		if (ISSET($_REQUEST['rq_keyf']))
		{
			if ($_REQUEST['rq_keyf']<>$rq['rq_keyf'] && $_REQUEST['id']==$id)
			{
				$key_data = $db->Execute("SELECT "
				                         ."`".$def_rqcl[$rq_type]['r_keyf']."` "
				                         ."FROM "
				                         ."`".$dbtables[$def_rqcl[$rq_type]['r_table']]."` "
				                         ."LIMIT 1");
				$a_key = $key_data->fields[$def_rqcl[$rq_type]['r_keyf']];

				$result = $db->Execute("UPDATE $dbtables[gd_rq] "
				                       ."SET "
				                       ."  `rq_keyf`='".$_REQUEST['rq_keyf']."', "
				                       ."  `rq_key`='$a_key' "
				                       ."WHERE id='$id' ");

				$_REQUEST['rq_key'] = $a_key;
				$rq['rd_id']  = -1;
				$rq_keyf = $_REQUEST['rq_keyf'];
			}
		}


		// Display rq_keyf selector

		echo "<TD>"
		."<SELECT NAME=rq_keyf>";

		option_fields ($rq_table, $rq_keyf);

		echo "</SELECT>"
		."</TD>";


		// If we have just changed the rq_idf, then update the value in gd_rq

		$rq_idf = $rq['rq_idf'];

		if (ISSET($_REQUEST['rq_idf']))
		{
			if ($_REQUEST['rq_idf']<>$rq['rq_idf'] && $_REQUEST['id']==$id)
			{
				$result = $db->Execute("UPDATE $dbtables[gd_rq] "
				                       ."SET"
				                       ."  `rq_idf`='".$_REQUEST['rq_idf']."' "
				                       ."WHERE id='$id' ");

				$rq['rq_id'] = -1;
				$rq_idf = $_REQUEST['rq_idf'];
			}
		}


		// Display res_idf selector

		echo "<TD>"
		."<SELECT NAME=rq_idf>";

		option_fields ($rq_table, $rq_idf);

		echo "</SELECT>"
		."</TD>";


		// If we have just changed the rq_key, then update the value in gd_rq

		$rq_key = $rq['rq_key'];

		if (ISSET($_REQUEST['rq_key']))
		{
			if ($_REQUEST['rq_key']<>$rq['rq_key'] && $_REQUEST['id']==$id)
			{
				$new_id = $db->Execute("SELECT `$rq[rq_idf]` "
				                       ."FROM `".$dbtables[$rq_table]."` "
				                       ."WHERE $rq_keyf='".$_REQUEST['rq_key']."' ");
				$rq['rq_id'] = $new_id->fields[$rq['rq_idf']];

				$result = $db->Execute("UPDATE $dbtables[gd_rq] "
				                       ."SET"
				                       ."  rq_key='".$_REQUEST['rq_key']."', "
				                       ."  rq_id='".$rq['rq_id']."' "
				                       ."WHERE id='$id' ");

				$rq_key = $_REQUEST['rq_key'];
			}
		}


		echo "<TD>"
		."<SELECT NAME=rq_key>";

		option_rows ($rq_table, $rq_keyf, $rq_key);

		echo "</SELECT>"
		."</TD>";


		// Display the id


		echo "<TD>"
		."<INPUT TYPE=HIDDEN NAME=id VALUE=$id>"
		."<B>$rq[rq_id]</B>"
		."</TD>";


		// Update the value of rq_goods_tribe in gd_rq


		echo "<TD>";

		$rq_gt = $rq['rq_goods_tribe'];

		if ($_REQUEST['rq_goods_tribe']<>$rq['rq_goods_tribe'] && $_REQUEST['id']==$id)
		{
			if ($_REQUEST['rq_goods_tribe']<>'Y')
			{
				$_REQUEST['rq_goods_tribe'] = 'N';
			}

			$db->Execute("UPDATE $dbtables[gd_rq] "
			             ."SET "
			             ."  `rq_goods_tribe`='".$_REQUEST['rq_goods_tribe']."' "
			             ."WHERE id='$id' ");

			$rq_gt = $_REQUEST['rq_goods_tribe'];

		}


		// Display rq_val edit box


		if ($rq_gt=='Y')
		{
			$rq_gt="\"Y\" CHECKED";
		}
		else
		{
			$rq_gt = "\"Y\"";
		}


		echo "<INPUT TYPE=CHECKBOX NAME=rq_goods_tribe VALUE=$rq_gt>"
		."</TD>";


		// If we have just changed the rq_val, then update the value in gd_rq


		echo "<TD>";

		$rq_val = $rq['rq_val'];

		if (ISSET($_REQUEST['rq_val']))
		{
			if ($_REQUEST['rq_val']<>$rq['rq_val'] && $_REQUEST['id']==$id)
			{
				$result = $db->Execute("UPDATE $dbtables[gd_rq] "
				                       ."SET "
				                       ."  `rq_val`='".$_REQUEST['rq_val']."' "
				                       ."WHERE id='$id' ");

				$rq_val = $_REQUEST['rq_val'];
			}
		}

		// Display rq_val edit box

		echo "<INPUT CLASS=edit_area NAME=rq_val VALUE=\"$rq_val\" SIZE=10 MAXLENGTH=10>"
		."</TD>";

		// Close row
		echo "<TD><INPUT TYPE=SUBMIT NAME=op VALUE=Delete></TD>"
		."</TR>"
		."</FORM>";

		$r++;
		$reqs->MoveNext();
	}

	echo "<FORM METHOD=POST ACTION=mstsck_prod.php>"
	."<TR CLASS=color_header>"
	."<TD COLSPAN=10>"
	."<BR>"
	."<INPUT TYPE=HIDDEN NAME=id VALUE=$id>"
	."<INPUT TYPE=HIDDEN NAME=method VALUE=$method>"
	."<INPUT TYPE=HIDDEN NAME=res_key VALUE=\"$res_key\">"
	."<INPUT TYPE=HIDDEN NAME=res_type VALUE=\"$res_type\">"
	."<INPUT TYPE=SUBMIT NAME=op VALUE=\"New Requirement\">"
	."</TD>"
	."</TR>"
	."</FORM>"
	."</TABLE>";
}



echo "<P>"
."<TABLE BORDER=0>"
."<TR>"
."<TD>"
."<FORM METHOD=post action=mstsck_prod.php>"
."<INPUT TYPE=HIDDEN NAME=res_type VALUE=\"$res_type\">"
."<INPUT TYPE=HIDDEN NAME=show_usage VALUE=$show_usage> "
."<INPUT TYPE=SUBMIT VALUE=Usage>"
."</FORM>"
."</TD>"
."<TD>"
."<FORM METHOD=post action=mstsck_prod.php>"
."<INPUT TYPE=HIDDEN NAME=res_type VALUE=\"$res_type\">"
."<INPUT TYPE=SUBMIT VALUE=\"Different $res_type\">"
."</FORM>"
."</TD>"
."</TR>"
."</TABLE>";




function option_fields ($table, $selected)
{
	global $db, $dbtables;

	$fields = $db->Execute("SHOW FIELDS FROM $dbtables[$table]");
	$fkeys = array_keys($fields->fields); // Get the name of the first column that is returned
	$fkey = $fkeys[0];

	while (!$fields->EOF)
	{
		$field = $fields->fields[$fkey];
		echo "<OPTION VALUE=\"$field\"";
		if ($field==$selected)
		{
			echo " SELECTED";
		}
		echo ">$field";
		$fields->MoveNext();
	}
}

function option_rows ($table, $field_name, $selected)
{
	global $db, $dbtables;

	$fields = $db->Execute("SELECT DISTINCT $field_name FROM $dbtables[$table] "
	                       ."ORDER BY $field_name ");

	while (!$fields->EOF)
	{
		$field = $fields->fields[$field_name];
		echo "<OPTION VALUE=\"$field\"";
		if ($field==$selected)
		{
			echo " SELECTED";
		}
		echo ">$field";
		$fields->MoveNext();
	}
}

?>
