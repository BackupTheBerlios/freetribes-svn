<?php
/*
  Eliminate the possibility of a full wildcard search (or one that has a single space as the $search value) ???
  Check add functionality
  Write a decent preparser to remove quotes and replace them at edit/output time
*/
error_reporting  (E_ERROR | E_WARNING | E_PARSE | !E_NOTICE);

session_start();
header("Cache-control: private");

include("config.php");
include("game_time.php");
include("gui/debug.php");
// include("gui/globals.php");

connectdb();

$version='1.0';
$debug_on = 0;
$title = "TribeStrive Helper";

if (!ISSET($_SESSION[theme]))
{
	$_SESSION[theme] = $theme_default;
}
/*
	Recommended admin levels
	1	Search
	2	Help ed             Edit/Add/Preview/Submit/Modify their own entries)
	3	Help ed exp.        also Delete their own entries)
	3	Super Help Ed       Edit/Add/Preview/Submit/Modify any entry)
	4   Super Help Ed exp.  also Delete any entry
	5	Help Sadmin         all the above and delete help records
	6+  Other game admins

	To affect any existing entry that you have not made yourself,
	you must have +1 privilege in the necessary operation.
*/
/*
$privilege = array	(                      //Privilege levels for the helper ops
					"Search"     => 0,
					"Edit"       => 1,     //For safety, should really be 2
					"Add"        => 1,     //For safety, should really be 2
					"Preview"    => 1,     //For safety, should really be 2
					"Submit"     => 1,     //For safety, should really be 2
					"Modify"     => 1,     //For safety, should really be 2
					"Delete"     => 1,     //For safety, should really be 3
					"Delete All" => 2,     //For safety, should really be 5
					"Confirm"    => 1      //For safety, should really be 3
					);
*/

// Create help subject info
$display_title = $title;
if (ISSET($_POST['type']) || ISSET($_POST['subject']) || ISSET($_POST['htitle']))
{
	$display_title .= ": ";
}

// This should only be set if the helper is invoked by one
// of the external support functions, eg help_link();
// When it is not set the user search box is not displayed
if ( ISSET($_REQUEST['query']) ) 
{
	$query = $_REQUEST['query'];
}

if ( ISSET($_REQUEST['id']) )
{
	$id = $_REQUEST['id'];
}

if ( ISSET($_REQUEST['search']) )
{
	$search = strtolower($_REQUEST['search']);
	$search = "%".$search."%";
}

if (ISSET($_REQUEST['value']))
{
	$value = strtolower($_REQUEST['value']);
	$topic = $value;
}
if (ISSET($_REQUEST['htitle']))
{
	$htitle = $_REQUEST['htitle'];
	$topic = $htitle;
}
$display_title .= " / ".$topic;

if (ISSET($_REQUEST['type']))
{
	$type = $_REQUEST['type'];
	$display_title .= " ($type)";
}


$title = $display_title."</FONT>";
page_header($title);


if ( !ISSET($_SESSION['admin']) )    // THIS SHOULD REALLy BE GETTING SET IN main.php
{
	$ch = $db->Execute("SELECT admin FROM $dbtables[chiefs] "
		              ."WHERE clanid = '".$_SESSION['clanid']."'");
	$_SESSION['admin'] = $ch->fields['admin'];
}
if ($_SESSION['admin'] > 0 && $debug_on == 1)
{
	debug_msg("ID: $id");
	debug_msg("QUERY: $query");
	debug_msg("VALUE: $value");
	debug_msg("TYPE: $type");
	debug_msg("TITLE: $htitle");
	debug_msg("SEARCH: $search");
	echo "SESSION:";
	print_r($_SESSION);
}

if (ISSET($_REQUEST[op]))
{
	$op = $_REQUEST[op];
}
else
{
	$op = "Search";
}


if ($op == "Search")
{

	echo "<CENTER>";

	display_search_form();

	echo "<A NAME=result_list></A>";

	$skill=$_REQUEST[skill];
	if (ISSET($skill) && $skill <> "ignore")
	{
		$type="skill";
		$value = $skill;
		$search = $skill;
	}



	if ( ISSET($search) )
	{
		debug_msg ("Search is set<br>Search value is $search<br>");

		$topics = "";
		$pages = "";
		$skills = "";
		$resources = "";


		if ( !ISSET($type) || $type=="all" || $type=="topic")
		{
			$topics = query_help_type ("topic", $search);
		}

		if ( !ISSET($type) || $type=="all" || $type=="page" )
		{
			$pages = query_help_type ("page", $search);
		}

		if ( !ISSET($type) || $type=="all" || $type=="skill")
		{
			$skills = query_help_type ("skill", $search);
		}

		if ( !ISSET($type) || $type=="all" || $type=="resource")
		{
			$resources = query_help_type ("resource", $search);
		}

	}
	elseif (ISSET($type))
	{

		debug_msg ("Type is set to $type<br>Value is $value<br>");

		$conditions="";
		if (ISSET($htitle))
		{
			$conditions=" AND title='$htitle'";
		}
		if (ISSET($value))
		{
			$conditions .= " AND value='$value'";
		}

		debug_msg ("SELECT * FROM $dbtables[gd_help] "
							."WHERE type='$type' $conditions "
							."ORDER BY title");

		$res = $db->Execute("SELECT * FROM $dbtables[gd_help] "
							."WHERE type='$type' $conditions "
							."ORDER BY title");
		if ($res->EOF)
		{
			$res = "";
		}

		switch ($type)
		{
			case "topic":
				$topics = $res;
				break;
			case "page":
				$pages = $res;
				break;
			case "skill":
				$skills = $res;
				break;
			case "resource":
				$resources = $res;
				break;
		}
	}

	echo "<A NAME=result_list></A>";

	if ($topics <> "" || $type=="topic")
	{
		if ($topics<>"")
		{
			display_help($topics, "Topics", $t_exclusions);
		}
		elseif (ISSET($query) && $type=="topic")
		{
			echo "<FORM METHOD=POST ACTION=helper.php>";
			echo "<TABLE BORDER=1 WIDTH=\"100%\" BGCOLOR=\"$color_line2\">"
				."<TR>"
				."<TD>";
			display_button ("Edit", $id, $type, $value, $htitle);
			echo "&nbsp;<B>$htitle</b> ($type)"
				."<TR>"
				."<TD>"
				."<BR>There is no help available on this subject<P>"
				."</TD>"
				."</TR>"
				."</TABLE>"
				."</FORM>";
		}
	}
	
	if ($pages <> "" || $type=="page")
	{
		if ($pages<>"")
		{
			display_help($pages, "Pages", $p_exclusions);
		}
		elseif (ISSET($query) && $type=="page")
		{
			echo "<FORM METHOD=POST ACTION=helper.php>";
			echo "<TABLE BORDER=1 WIDTH=\"100%\" BGCOLOR=\"$color_line2\">"
				."<TR>"
				."<TD>";
			display_button ("Edit", $id, $type, $value, $htitle);
			echo "&nbsp;<B>$htitle</b> ($type)"
				."<TR>"
				."<TD>"
				."<BR>There is no help available on this subject<P>"
				."</TD>"
				."</TR>"
				."</TABLE>"
				."</FORM>";
		}
	}

	if ($skills <> "" || $type == "skill")
	{
		$s_inclusions = "";
		if ( $skills<>"" )
		{
			display_help($skills, "Skills", $s_inclusions);
		}


		if ( $type=="skill" )
		{
			if ($s_inclusions <> "")
			{
				$s_inclusions = " abbr NOT IN (''".$s_inclusions.")";
				if ( ISSET ($search) )
				{
					$s_inclusions = $s_inclusions." AND abbr LIKE '$search'";
				}
			}
			else
			{
				$s_inclusions = "abbr = '$value'";
			}

			debug_msg ("Exclusions: $s_inclusions");
			debug_msg ("SELECT abbr, long_name FROM $dbtables[skill_table] "
										."WHERE $s_inclusions "
										."ORDER BY long_name");

			$skill_list = $db->Execute("SELECT abbr, long_name FROM $dbtables[skill_table] "
										."WHERE $s_inclusions "
										."ORDER BY long_name");

			if (!$skill_list->EOF)
			{
				echo "<TABLE BORDER=1 CELLPADDING=0 WIDTH=\"100%\" BGCOLOR=\"$color_table\">"
					."<TR BGCOLOR=\"$color_header\">"
					."<TD>"
					."&nbsp;Help is <b>not</b> available for the following skill(s)<BR>&nbsp;"
					."</TD>"
					."</TR>"
					."<TR>"
					."<TD>";

				echo "<TABLE BORDER=0 CELLPADDING=0 WIDTH=\"100%\">";
			}

			$i = 0;
			while ( $skill_list_info=$skill_list->FetchRow() )
			{
				$rc = $i % 2;
				$i++;
				echo ""
					."<FORM METHOD=POST ACTION=helper.php>"
					."<TR CLASS=\"row_color$rc\">"
					."<TD WIDTH=50></TD>"
					."<TD VALIGN=TOP WIDTH=80>";
				display_button ("Edit", "", "skill", $skill_list_info['abbr'], $skill_list_info['long_name']);
				echo "</TD>"
					."<TD VALIGN=TOP>".$skill_list_info['long_name']."&nbsp;</TD>"
					."</TR>"
					."</FORM>";
			}
			$skill_list->MoveFirst();
			if (!$skill_list->EOF)
			{
				echo "</TABLE></TD></TR></TABLE><BR>";
			}
		}
	}

	if ($resources <> "" || $type=="resource")
	{
		if ($resources<>"")
		{
			display_help($resources, "Resources", $r_exclusions);
		}
		elseif (ISSET($query) && $type=="resource")
		{
			echo "<FORM METHOD=POST ACTION=helper.php>";
			echo "<TABLE BORDER=1 WIDTH=\"100%\" BGCOLOR=\"$color_line2\">"
				."<TR>"
				."<TD>";
			display_button ("Edit", $id, $type, $value, $htitle);
			echo "&nbsp;<B>$htitle</b> ($type)"
				."<TR>"
				."<TD>"
				."<BR>There is no help available on this subject<P>"
				."</TD>"
				."</TR>"
				."</TABLE>"
				."</FORM>";
		}
	}
	
/*
	if ($resources <> "" || $type=="resource")
	{
		if ($resources<>"")
		{
			display_help($resources, "Resources", $r_exclusions);
		}

		if ($type == "resource")
		{
			$extend = "OR proper LIKE '$search' OR LOWER(long_name) LIKE '$search' ";
		}
		$product_list = $db->Execute("SELECT proper, long_name FROM $dbtables[product_table] "
									."WHERE proper='$subject' ".$extend
									."ORDER BY long_name");

		if (!$product_list->EOF)
		{
			echo "<TABLE BORDER=1 CELLPADDING=4 WIDTH=\"100%\">"
				."<TR>"
				."<TD>"
				."Help is <b>not</b> available for the following resource(s)"
				."</TD>"
				."</TR>"
				."<TR>"
				."<TD>";

			echo "<TABLE BORDER=0 CELLPADDING=0>"
				."<TR>"
				."<TD WIDTH=50></TD>"
				."<TD></TD>"
				."<TD><B>Resource</B></TD>"
				."</TR>";
		}

		while ($product_list_info = $product_list->FetchRow())
		{
			echo ""
				."<FORM METHOD=POST ACTION=helper.php>"
				."<TR>"
				."<TD></TD>"
				."<TD VALIGN=TOP>";
			display_button ("Edit", "", "resource", $product_list_info['proper'], $product_list_info['long_name']);
			echo "</TD>"
				."<TD VALIGN=TOP>".$product_list_info['long_name']."&nbsp;</TD>"
				."</TR>"
				."</FORM>"
				."";
		}
		$product_list->MoveFirst();
		if (!$product_list->EOF)
		{
			echo "</TABLE></TD></TR></TABLE>";
		}
	}
*/
}
elseif ($op == "Modify")
{

	if ( !ISSET($_REQUEST['clanid']) )
	{
		$clanid = $_SESSION['clanid'];
	}
	$qhelp = $db->Execute("SELECT * FROM $dbtables[gd_help] "
						."WHERE id='$id'");
	$help_record = $qhelp->fields;
	$type = $help_record[type];
	$value = $help_record[value];
	$title = $help_record[title];
	$help = $help_record[help];
	$fragments = explode("<ENTRY-", $help);

	if ($fragments[0]=="<ENTRIES-0>" || $fragments[0]=="")
	{
		echo "<TABLE BGCOLOR=\"$color_table\" CELLPADDING=4 BORDER=1 ALIGN=CENTER WIDTH=\"100%\">"
			."<TR>"
			."<TD VALIGN=TOP><B>$title($value): $type, $id</B></TD>"
			."</TR>"
			."<TR>"
			."<TD VALIGN=TOP>&nbsp;<BR>"
			."There are no entries for this. You can add one using the Edit button from the results of a Search."
			."<BR>&nbsp;</TD>"
			."</TR>"
			."<TR>"
			."<FORM METHOD=POST ACTION=helper.php>"
			."<TD BGCOLOR=\"$color_header\" VALIGN=TOP ALIGN=RIGHT>";
		display_button("Search", "", "", "", "");
		echo "</TD>"
			."</FORM>"
			."</TR>"
			."</TABLE>";
	}
	else
	{
		echo  "<TABLE BGCOLOR=\"$color_table\" CELLPADDING=4 BORDER=1 ALIGN=CENTER WIDTH=\"100%\">"
			."<TR>"
			."<TD VALIGN=TOP colspan=2><B>$title($value): $type, $id</B></TD>"
			."</TR>";
		if ($_SESSION['admin'] >= $privilege['hlp_Delete'])
		{
			echo "<TR BGCOLOR=\"$color_line1\">"
				."<FORM METHOD=POST ACTION=helper.php>"
				."<TD VALIGN=MIDDLE>";
			display_button("Delete", $id, $type, $value, $title);
			echo "</TD>"
				."<TD BGCOLOR=\"$color_line1\" VALIGN=TOP WIDTH=\"100%\">"
				."<B>Warning!</B><BR>Clicking the button to the left will start a process allowing you to delete <B>every</B> entry ";
			if ($_SESSION['admin'] > $privilege['hlp_Delete'])
			{
				echo "for this help topic!";
			}
			else
			{
				echo "that <B>you</B> have made for this help topic."
					."<INPUT TYPE=HIDDEN NAME=clanid VALUE=\"$clanid\">";
			}
			echo "</TD>"
				."</FORM>"
				."</TR>";
		}
		echo "<TR>"
			."<TD VALIGN=TOP ALIGN=CENTER COLSPAN=2>"
			."Below are listed any entries for the help section described above that you have sufficient privilege to modify."
			."</TD>"
			."</TR>"
			."</TABLE>"
			."<P>";

		unset($fragments[0]);
		foreach ($fragments as $frag)
		{
			ereg("([^-]*)(-)([^>]*)(>)(.*)", $frag, $parsed);

			$can_mod = 0;
			if ($_SESSION['admin'] >= $privilege['hlp_Modify'] && $parsed[1]==$clanid)
			{
				$can_mod = 1;
			}
			if ($_SESSION['admin'] > $privilege['hlp_Modify'])
			{
				$can_mod = 1;
			}


			if ($can_mod==1)
			{
				$can_del = 0;
				if ($_SESSION['admin'] >= $privilege['hlp_Delete'] && $parsed[1]==$clanid)
				{
					$can_del = 1;
				}
				if ($_SESSION['admin'] > $privilege['hlp_Delete'])
				{
					$can_del = 1;
				}

				$can_ed = 0;
				if ($_SESSION['admin'] >= $privilege['hlp_Edit'] && $parsed[1]==$clanid)
				{
					$can_ed = 1;
				}
				if ($_SESSION['admin'] > $privilege['hlp_Edit'])
				{
					$can_ed = 1;
				}

				$help = str_replace ("</ENTRY>", "", $parsed[5]);
				$help = form_text_view ($help);
				$addtext = ereg_replace("\"","&quot;",$help);
				echo "<TABLE BGCOLOR=\"$color_table\" WIDTH=\"100%\" BORDER=1 CELLPADDING=4>"
					."<TR BGCOLOR=\"$color_line1\">"
					."<FORM METHOD=POST ACTION=helper.php>"
					."<TD VALIGN=TOP>";
				if ($can_del==1)
				{
					echo "<INPUT TYPE=SUBMIT NAME=op VALUE=Delete>"
						."<INPUT TYPE=HIDDEN NAME=entry VALUE=\"".$parsed[3]."\">"
						."<INPUT TYPE=HIDDEN NAME=id VALUE=\"$id\">";
				}
				else
				{
					echo "Cannot Delete";
				}
					echo "</TD>"
						."</FORM>"
						."<FORM METHOD=POST ACTION=helper.php>"
						."<TD BGCOLOR=\"$color_line2\" VALIGN=TOP>";
				if ($can_ed==1)
				{
					echo "<INPUT TYPE=SUBMIT NAME=op VALUE=Edit>"
						."<INPUT TYPE=HIDDEN NAME=entry VALUE=\"".$parsed[3]."\">"
						."<INPUT TYPE=HIDDEN NAME=clanid VALUE=\"$clanid\">"
						."<INPUT TYPE=HIDDEN NAME=addtext VALUE=\"$addtext\">"
						."<INPUT TYPE=HIDDEN NAME=id VALUE=\"$id\">";
				}
				else
				{
					echo "Cannot Edit";
				}
				echo "</TD>"
					."</FORM>"
					."<TD BGCOLOR=\"$color_line1\" VALIGN=TOP WIDTH=\"100%\">"
					.$help
					."</TD>"
					."</TR>"
					."<TR BGCOLOR=\"$color_bg\"><TD COLSPAN=3>&nbsp;</TD></TR>"
					."</TABLE>";
			}
		}
	}
}
elseif ($op == "Edit")
{
	if (ISSET($_REQUEST['addtext']))
	{
		$addtext = form_text_edit($_REQUEST['addtext']);
	}
	else
	{
		$addtext = "";
	}

	if (ISSET($_REQUEST['id']))									//we know the help record exists
	{
		$id = $_REQUEST['id'];
		$qhelp = $db->Execute("SELECT * FROM $dbtables[gd_help] "
					."WHERE id='$id'");
		$help_record = $qhelp->fields;

		$type = $help_record[type];
		$value = $help_record[value];
		$title = $help_record[title];
		$help = $help_record[help];
	}
	else														//We are not sure if the record exists
	{
		$value = $_REQUEST['value'];

		$qhelp = $db->Execute("SELECT * FROM $dbtables[gd_help] "
							."WHERE value='$value' "
							."AND type='$type' "
							."AND title='$htitle'");

		$help_record = $qhelp->fields;

		if ($qhelp->EOF)										//it does NOT exist
		{
			unset($qhelp);
			unset($help_record);
			$res = $db->Execute("INSERT INTO $dbtables[gd_help] "
						."(title, type, value) "
						."VALUES ('$htitle', '$type', '$value')");

			$qhelp = $db->Execute("SELECT * FROM $dbtables[gd_help] "
								."WHERE value='$value' "
								."AND type='$type' "
								."AND title='$htitle'");
			$help_record = $qhelp->fields;

			$id = $help_record[id];
			$type = $help_record[type];
			$value = $help_record[value];
			$title = $help_record[title];
			$help = $help_record[help];
		}
		else													// it does exist
		{
			$id = $help_record->fields[id];
			$type = $help_record[type];
			$value = $help_record[value];
			$title = $help_record[title];
			$help = $help_record[help];
		}
	}
	if ($addtext=="" && $op == "Modify")
	{
		$addtext = form_text_edit($help);
	}
	$help = form_text_view($help);


	echo "<FORM METHOD=POST ACTION=\"helper.php\">";

	echo "<TABLE BORDER=1 CELLPADDING=4 ALIGN=CENTER WIDTH=\"100%\" BGCOLOR=\"$color_table\">"
		."<TR>"
		."<TD VALIGN=TOP><B>$title($value): $type, $id</B></TD>"
		."</TR>"
		."<TR BGCOLOR=\"$color_line1\">"
		."<TD><P ALIGN=JUSTIFY>".$help."</TD>"
		."</TR>"
		."<TR>"
		."<TD><P ALIGN=JUSTIFY></TD>"
		."<TR BGCOLOR=\"$color_line2\">"
		."<TD VALIGN=TOP>Please type the help you want to add into the box below.<p align=center>"
		."<TEXTAREA class=edit_area maxlength=16384 cols=\"80\" rows=\"20\" name=\"addtext\">"
		.$addtext
		."</TEXTAREA>"
		."</TD>"
		."</TR>"
		."<TR BGCOLOR=\"$color_header\">"
		."<TD ALIGN=RIGHT>";
	display_button ("Preview", $id, $type, $value, $title);
	if ( ISSET($_REQUEST[clanid]) )
	{
		echo "<INPUT TYPE=HIDDEN NAME=clanid VALUE=".$_REQUEST[clanid].">";
	}
	if ( ISSET($_REQUEST[entry]) )
	{
		echo "<INPUT TYPE=HIDDEN NAME=entry VALUE=".$_REQUEST[entry].">";
	}
	echo "</TD>"
		."</TR>"
		."</TABLE>";

	echo "</FORM>";

}
elseif ($op == "Preview")
{
	if (ISSET($_REQUEST['addtext']))
	{
		$addtext = form_text_clean($_REQUEST['addtext']);
		$viewtext= form_text_view($_REQUEST['addtext']);
	}
	else
	{
		$addtext = "";
		$viewtext = "";
	}

	if (ISSET($_REQUEST['id']))									//we know the help record exists
	{
		$id = $_REQUEST['id'];
		$qhelp = $db->Execute("SELECT * FROM $dbtables[gd_help] "
					."WHERE id='$id'");
		$help_record = $qhelp->fields;

		$type = $help_record[type];
		$value = $help_record[value];
		$title = $help_record[title];
	}
	else														//Bad error if this happens
	{
		die ("<P>You should not be seeing this!<BR>"
			."Something has gone wrong!!\n");
	}

	echo "<FORM METHOD=POST ACTION=\"helper.php\">";
	echo "<TABLE BORDER=1 CELLPADDING=4 ALIGN=CENTER WIDTH=\"100%\" BGCOLOR=\"$color_table\">"
		."<TR>"
		."<TD VALIGN=TOP><B>PREVIEWING $title: $type($value), $id</B></TD>"
		."</TR>"
		."<TR BGCOLOR=\"$color_line1\">"
		."<TD><P ALIGN=JUSTIFY>".$viewtext."</TD>"
		."</TR>"
		."<TR>"
		."<TD ALIGN=RIGHT>"
		."<INPUT TYPE=HIDDEN NAME=id VALUE=$id>"
		."<INPUT TYPE=HIDDEN NAME=addtext VALUE=\"$addtext\">"
		."<INPUT TYPE=SUBMIT NAME=op VALUE=Edit>";
	if ( ISSET($_REQUEST[clanid]) )
	{
		echo "<INPUT TYPE=HIDDEN NAME=clanid VALUE=".$_REQUEST[clanid].">";
	}
	if ( ISSET($_REQUEST[entry]) )
	{
		echo "<INPUT TYPE=HIDDEN NAME=entry VALUE=".$_REQUEST[entry].">";
	}
	echo "</TD>"
		."</TR>"
		."</TABLE>"
		."</FORM>";

	echo "<FORM METHOD=POST ACTION=\"helper.php\">";
	echo "<TABLE BORDER=1 CELLPADDING=4 ALIGN=CENTER WIDTH=\"100%\" BGCOLOR=\"$color_header\">"
		."<TR>"
		."<TD VALIGN=TOP>"
		."<B>SUBMIT $title: $type($value), $id</B>"
		."</TD>"
		."<TD ALIGN=RIGHT>"
		."<INPUT TYPE=HIDDEN NAME=id VALUE=$id>"
		."<INPUT TYPE=HIDDEN NAME=type VALUE=$type>"
		."<INPUT TYPE=HIDDEN NAME=value VALUE=\"$value\">"
		."<INPUT TYPE=HIDDEN NAME=htitle VALUE=\"$title\">"
		."<INPUT TYPE=HIDDEN NAME=addtext VALUE=\"$addtext\">"
		."<INPUT TYPE=SUBMIT NAME=op VALUE=Submit>";
	if ( ISSET($_REQUEST[clanid]) )
	{
		echo "<INPUT TYPE=HIDDEN NAME=clanid VALUE=".$_REQUEST[clanid].">";
	}
	if ( ISSET($_REQUEST[entry]) )
	{
		echo "<INPUT TYPE=HIDDEN NAME=entry VALUE=".$_REQUEST[entry].">";
	}
	echo "</TD>"
		."</TR>"
		."</TABLE>"
		."</FORM>";

}
elseif ($op == "Submit")
{
	if (ISSET($_REQUEST['addtext']))
	{
		$sqltext= form_text_sql($_REQUEST['addtext']);
	}
	else
	{
		$addtext = "";
		$sqltext = "";
	}

	if ( ISSET($_REQUEST[entry]) )
	{
		$entry=$_REQUEST[entry];
	}
	if ( ISSET($_REQUEST[clanid]) )
	{
		$clanid=$_REQUEST[clanid];
	}

	if (ISSET($_REQUEST['id']))									//we know the help record exists
	{
		$id = $_REQUEST['id'];

		if (!ISSET($_REQUEST['entry']))
		{
			$qhelp = $db->Execute("SELECT * FROM $dbtables[gd_help] "
						."WHERE id='$id'");
			$help_record = $qhelp->fields;

			$type = $help_record[type];
			$value = $help_record[value];
			$title = $help_record[title];
			$help = $help_record[help];

			ereg("(<ENTRIES-)([^>]*)(>)(.*)", $help, $parsed);

			if ($parsed[2] == "")
			{
				$entries = 1;
			}
			else
			{
				$entries = $parsed[2] + 1;
			}

			$help = "<ENTRIES-$entries>".$parsed[4]
					."<ENTRY-$clanid-$entries>"
					.$sqltext
					."</ENTRY>";

			$db->Execute("UPDATE $dbtables[gd_help] "
						."SET `help`='$help' "
						."WHERE id='$id'");

			$qhelp = $db->Execute("SELECT * FROM $dbtables[gd_help] "
						."WHERE id='$id'");
			$help_record = $qhelp->fields;
			$help = $help_record[help];
		}
		else
		{
			db_replace_entry ($id, $clan, $entry, $sqltext);
			$help = $addtext;
		}
	}
	else														//Bad error if this happens
	{
		die ("<P>You should not be seeing this!<BR>"
			."Something has gone wrong!!\n");
	}

	$help=form_text_view($help);

	echo "<FORM METHOD=POST ACTION=\"helper.php\">";

	echo "<TABLE BORDER=1 CELLPADDING=4 ALIGN=CENTER WIDTH=\"100%\" BGCOLOR=\"$color_table\">"
		."<TR>"
		."<TD VALIGN=TOP><B>UPDATED $htitle: $type($value), $id</B></TD>"
		."</TR>"
		."<TR BGCOLOR=\"$color_line1\">"
		."<TD><P ALIGN=JUSTIFY>".$help."</TD>"
		."</TR>"
		."<TR BGCOLOR=\"$color_header\">"
		."<TD ALIGN=RIGHT>"
		."<INPUT TYPE=SUBMIT NAME=op VALUE=Search>"
		."</TD>"
		."</TR>"
		."</TABLE>";

	echo "</FORM>";

}
elseif ($op == "Add")
{
	echo "<FORM METHOD=POST ACTION=\"helper.php\">";

	echo "<TABLE BORDER=1 CELLPADDING=4 ALIGN=CENTER BGCOLOR=\"$color_table\">"
		."<TR>"
		."<TD VALIGN=TOP>"
		."<SELECT NAME=type SIZE=1>"
		."<OPTION VALUE=topic>Topic"
		."<OPTION VALUE=page>Page"
		."</SELECT>"
		."<P>Type of entry to add"
		."</TD>"
		."<TD VALIGN=TOP>"
		."<INPUT class=edit_area TYPE=\"TEXT\" NAME=\"value\" SIZE=\"20\" MAXLENGTH=\"40\" VALUE=\"\">"
		."<P>Enter key value of search field<ul><li>this must be all one word<li>a Page entry should have something like <i>main.php</i> in here<li>A Topic entry should have a single word (no spaces) value for quick searching</ul>"
		."</TD>"
		."<TD VALIGN=TOP>"
		."<INPUT class=edit_area TYPE=\"TEXT\" NAME=\"htitle\" SIZE=\"20\" MAXLENGTH=\"50\" VALUE=\"\">"
		."<P>Enter a human readable title for the help entry"
		."<TD VALIGN=TOP ALIGN=RIGHT>"
		."<RIGHT><INPUT TYPE=SUBMIT NAME=op VALUE=Edit>"
		."<P>Ready, steady ..."
		."</TD>"
		."</TR>";

	echo "</TABLE>";

	echo "</FORM>";
}
elseif ($op=="Delete")
{
	
	if (ISSET($_REQUEST['id']))									//we know the help record exists
	{
		$id = $_REQUEST['id'];

		$qhelp = $db->Execute("SELECT * FROM $dbtables[gd_help] "
							."WHERE id='$id'");
		$help_record = $qhelp->fields;

		$type = $help_record[type];
		$value = $help_record[value];
		$title = $help_record[title];
		$help = $help_record[help];
	}
	else
	{
		die ("<b>You should never see this.<p>Something is wrong!</b>");
	}


	echo "<TABLE BORDER=1 CELLPADDING=4 ALIGN=CENTER BGCOLOR=\"$color_table\">"
		."<TR BGCOLOR=\"$color_header\">"
		."<TD VALIGN=TOP><B>DELETING $title($value): $type, $id</B></TD>"
		."</TR>"
		."<TR>"
		."<TD VALIGN=TOP>";

	if (ISSET($_REQUEST['clanid']))
	{
		echo "You are about to delete <b>all the entries that you have made</b> for the help described above.<P>Are you sure you want to do that?";
	} 
	elseif (ISSET($_REQUEST['entry']))
	{
		$text = db_get_entry ($id, $_REQUEST['entry']);
		echo "You are about to delete the entry shown below. Are you sure you want to do this?"
			."</TD>"
			."</TR>"
			."<TR>"
			."<TD BGCOLOR=\"$color_line1\" VALIGN=TOP>"
			.$text;
	}
	elseif (ISSET($_REQUEST['delete_all']))
	{
		echo "You are about to delete <b>all</b> the entries that have been made for the help described above.<P>Are you sure you want to do that?";
	}
	else
	{
		echo "You are about to delete the <b>entire</b> record for the help described above.<P>Are you sure you want to do that?";
	}

	echo "</TD>"
		."</TR>"
		."<TR>"
		."<FORM METHOD=POST ACTION=helper.php>"
		."<TD VALIGN=TOP ALIGN=CENTER BGCOLOR=\"$color_header\">";

	display_button("Confirm", "$id", "$type", "$value", "$title");
	if (ISSET($_REQUEST['clanid']))
	{
		echo "<INPUT TYPE=HIDDEN NAME=clanid VALUE=".$_REQUEST['clanid'].">";
	} 
	if (ISSET($_REQUEST['entry']))
	{
		echo "<INPUT TYPE=HIDDEN NAME=entry VALUE=".$_REQUEST['entry'].">";
	} 
	if (ISSET($_REQUEST['delete_all']))
	{
		echo "<INPUT TYPE=HIDDEN NAME=delete_all VALUE=1>";
	} 
	echo "</TD>"
		."</FORM>"
		."<FORM METHOD=POST ACTION=helper.php>"
		."<TD VALIGN=TOP ALIGN=CENTER BGCOLOR=\"$color_line2\">";
	display_button("Search", "", "", "", "");
	echo "</TD>"
		."</FORM>"
		."</TR>";

	echo "</TABLE>";

}

elseif ($op=="Confirm")
{
	echo "<TABLE BORDER=1 CELLPADDING=4 ALIGN=CENTER BGCOLOR=\"$color_table\">"
		."<TR BGCOLOR=\"$color_header\">"
		."<TD VALIGN=TOP><B>DELETING $htitle($value): $type, $id</B></TD>"
		."</TR>"
		."<TR>"
		."<TD VALIGN=TOP>&nbsp;<BR>";
	if (ISSET($_REQUEST['clanid']))
	{
		db_delete_entry ($id, $_REQUEST['clanid'], "");
		echo "All entries made by you for this help have been deleted.";
	} 
	elseif (ISSET($_REQUEST['entry']))
	{
		db_delete_entry ($id, "", $_REQUEST['entry']);
		echo "Entry number ".$_REQUEST['entry']." for this help has been deleted.";
	}
	elseif (ISSET($_REQUEST['delete_all']))
	{
		db_delete_entry ($id, "", "");
		echo "All entries for this help have been deleted.";
	}
	else
	{
		if (ISSET($_REQUEST['id']))									//we know the help record exists
		{
			$id = $_REQUEST['id'];

			$db->Execute("DELETE FROM $dbtables[gd_help] "
						."WHERE id='$id'");
			echo "The help for this has been completely deleted.";
		}
		else
		{
			die ("<b>You should never see this.<p>Something is wrong!</b>");
		}
	}
	echo "<BR>&nbsp;</TD>"
		."</TR>"
		."<FORM METHOD=POST ACTION=helper.php>"
		."<TR BGCOLOR=\"$color_header\">"
		."<TD VALIGN=TOP ALIGN=CENTER>";
	display_button("Search", "", "", "", "");
	echo "</TD>"
		."</TR>"
		."</FORM>";

	echo "</TABLE>";
}


// SUPPORT FUNCTIONS


function query_help_type ($type, $search)
{
	global $db, $dbtables;

	$result = $db->Execute("SELECT * FROM $dbtables[gd_help] "
							."WHERE (LOWER(title) LIKE '$search' "
							."OR LOWER(value) LIKE '$search') "
							."AND type='$type' "
							."ORDER BY title");
	if ($result->EOF)
	{
		$result = "";
	}

	return $result;
}


function display_search_form()
{
	global $color_line1, $color_line2, $color_bg, $color_table, $version, $privilege;

	if ( !ISSET($_REQUEST['query']) )
	{
//		GUI_open_table1 ("", "", "Search Helper");

		echo "<FORM METHOD=POST ACTION=\"helper.php#result_list\">";

		echo "<TABLE BORDER=1 CELLPADDING=4 ALIGN=CENTER BGCOLOR=\"$color_table\">"
			."<TD VALIGN=TOP>"
			."Search for help in<P>"
			."<SELECT NAME=type SIZE=1>"
			."<OPTION VALUE=all>All"
			."<OPTION VALUE=topic>Topics"
			."<OPTION VALUE=page>Pages"
			."<OPTION VALUE=skill>Skills"
			."<OPTION VALUE=resource>Resources"
			."</SELECT>"
			."</TD>"
			."<TD VALIGN=TOP>Enter search phrase<P>"
			."<INPUT class=edit_area TYPE=\"TEXT\" NAME=\"search\" SIZE=\"20\" MAXLENGTH=\"50\" VALUE=\"\">"
			."<P><B>OR</B> Select Skill<P>"
			."<SELECT NAME=skill>"
			."<OPTION VALUE=ignore>Ignore";

		include("gui/option_skills.php");
		option_skills("a");
		option_skills("b");
		option_skills("c");

		echo "</SELECT>"
			."</TD>"
			."<TD VALIGN=TOP ALIGN=RIGHT>Select action<P>"
			."<RIGHT><INPUT TYPE=SUBMIT NAME=op VALUE=Search ALIGN=RIGHT><P>"
			."</TD>"
			."</TR>";
		if ($_SESSION['admin'] >= $privilege['hlp_Add'])
		{
			echo "<TR>"
				."<TD COLSPAN=2>"
				."Add <b>New Topic</b> or <b>New Page</b> "
				."</TD>"
				."<TD ALIGN=RIGHT>"
				."<INPUT TYPE=SUBMIT NAME=op VALUE=\"Add\">"
				."</TD>"
				."</TR>";
			}
		echo "<TR>"
			."<TD VALIGN=TOP ALIGN=CENTER COLSPAN=3>";
		help_link ("Show me the help for the helper!", "", "topic", "helper", "");
		echo "<FONT SIZE=-1> (v$version)</FONT>"
			."</TD>"
			."</TR>"
			."</TABLE>";

		echo "</FORM>";

//		GUI_close_table1("", "");
	}
}


function form_text_clean ($s)
{
	$t = $s;

	$from =  array ("\\\'", '\\\"', "\"", "<\?", "\?>", "&lt;", "&gt;");
	$to = array ("'", "&quot;", "&quot;", "", "", "&amp;lt;", "&amp;gt;");

	for ($i = 0; $i < count($from); $i++)
	{
		$t = ereg_replace($from[$i], $to[$i], $t);
	}

	return $t;
}


function form_text_edit ($s)
{
	$t = $s;

	$from =  array ("\r\r", "\r\n", "\n\n", "\n\n", "<BR>", "<P ALIGN=JUSTIFY>", '\\\"', "\"", "&squot;", "\\\'", "&lt;", "&gt;");
	$to = array ("\n", "\n", "\n", "\n", "\n", "\n\n", "&quot;", "&quot;", "'", "'", "&amp;lt;", "&amp;gt;");

	for ($i = 0; $i < count($from); $i++)
	{
		$t = ereg_replace($from[$i], $to[$i], $t);
	}

	return $t;
}


function form_text_view ($s)
{
	$t = $s;

	$from = array ("\r\n", "\n\n", "\n", "&squot;", "\\\'", '\\\"', "&amp;lt;", "&amp;gt;");
	$to = array ("\n", "<P ALIGN=JUSTIFY>", "<BR>", "'", "'", "&quot;", "&lt;", "&gt;");

	for ($i = 0; $i < count($from); $i++)
	{
		$t = ereg_replace($from[$i], $to[$i], $t);
	}

	$t = parse_help($t);

	return $t;
}


function form_text_sql ($s)
	{
		$t = $s;

		$from =  array ("\r\n", "\n\n", "\n", "'", "\"");
		$to = array ("\n", "<P ALIGN=JUSTIFY>", "<BR>", "&squot;", "&quot;");

		for ($i = 0; $i < count($from); $i++)
		{
			$t = ereg_replace($from[$i], $to[$i], $t);
		}

		return $t;
	}


function parse_help ($s)
{
	$t = $s;

	$from =  array ("<ENTRY[^>]*>",
					"</ENTRY>",
					"(<topic>)([^<]*)(</topic>)",
					"(<page>)([^<]*)(</page>)",
					"(<skill>)([^<]*)(</skill>)",
					"(<resource>)([^<]*)(</resource>)");
	$to = array ("<TABLE BORDER=1 BGCOLOR=\"$color_line2\" WIDTH=\"100%\" ALIGN=CENTER class=help_entry><TR><TD><P ALIGN=JUSTIFY><FONT class=help_text>",
				 "</FONT></TD></TR></TABLE>",
				 "<a href=\"helper.php?query=1&htitle=\\2&type=topic\" target=ts_helper>\\2</a>",
				 "<a href=\"helper.php?query=1&htitle=\\2&type=page\" target=ts_helper>\\2</a>",
				 "<a href=\"helper.php?query=1&htitle=\\2&type=skill\" target=ts_helper>\\2</a>",
				 "<a href=\"helper.php?query=1&htitle=\\2&type=resource\" target=ts_helper>\\2</a>");

	for ($i = 0; $i < count($from); $i++)
	{
		$t = eregi_replace($from[$i], $to[$i], $t);
	}

	return $t;
}


function display_button ($op, $id, $type, $value, $title)
{
	global $privilege;

	if ($_SESSION['admin'] >= $privilege["hlp_".$op])
	{
		echo "<INPUT TYPE=SUBMIT name=op VALUE=\"$op\"> ";
		if ($id<>"")
		{
			echo "<INPUT TYPE=HIDDEN NAME=id VALUE=\"".$id."\">";
		}
		if ($type<>"")
		{
			echo "<INPUT TYPE=HIDDEN NAME=type VALUE=\"".$type."\">";
		}
		if ($value<>"")
		{
			echo "<INPUT TYPE=HIDDEN NAME=value VALUE=\"".$value."\">";
		}
		if ($title<>"")
		{
			echo "<INPUT TYPE=HIDDEN NAME=htitle VALUE=\"".$title."\">";
		}
	}
}


function display_help ($info, $section, &$inclusions)
{
	global $db, $dbtables, $query, $color_line1, $color_line2, $color_bg, $color_table, $privilege;

	if (!ISSET($_REQUEST['query']))
	{
		echo "<A NAME=$section></A>";

		if ($section == "Topics")
		{
			echo "<B>$section</B> |";
		} else {
			echo "<A HREF=\"#Topics\">Topics</A> | ";
		}
		if ($section == "Pages")
		{
			echo "<B>$section</B> |";
		} else {
			echo "<A HREF=\"#Pages\">Pages</A> | ";
		}
		if ($section == "Skills")
		{
			echo "<B>$section</B> |";
		} else {
			echo "<A HREF=\"#Skills\">Skills</A> | ";
		}
		if ($section == "Resources")
		{
			echo "<B>$section</B>";
		} else {
			echo "<A HREF=\"#Resources\">Resources</A>";
		}
	}

	echo "<TABLE BORDER=1 CELLPADDING=0 BGCOLOR=\"$color_table\" WIDTH=\"100%\">";

	while ( !$info->EOF )
	{
		$infofields=$info->fields;
		$help_text = form_text_view ($infofields['help']);
		$inclusions=$inclusions.", '".$infofields['value']."'";
		echo "<TR>"
			."<TD>"
			."<TABLE BORDER=0 CELLPADDING=0 WIDTH=\"100%\">";
		echo "<TR BGCOLOR=\"$color_table\">";
		echo "<TD VALIGN=TOP WIDTH=\"100%\">";
		echo "&nbsp;<B>".$infofields['title']."</B>&nbsp;";
		echo "</TD>";
		echo "<TD VALIGN=TOP ALIGN=CENTER>";
		echo "<FORM METHOD=POST ACTION=helper.php>";
		display_button ("Edit", $infofields['id'], $infofields['type'], $infofields['value'], $infofields['title']);
		echo "</FORM>";
		echo "</TD>";

		echo "<TD VALIGN=TOP ALIGN=CENTER>";
		echo "<FORM METHOD=POST ACTION=helper.php>";
		display_button ("Modify", $infofields['id'], $infofields['type'], $infofields['value'], $infofields['title']);
		echo "</FORM>";
		echo "</TD>";

		echo "<TD VALIGN=TOP ALIGN=CENTER>";
		echo "<FORM METHOD=POST ACTION=helper.php>";
		if ($_SESSION['admin'] > $privilege["hlp_Delete All"])
		{
			display_button ("Delete", $infofields['id'], $infofields['type'], $infofields['value'], $infofields['title']);
			echo "<INPUT TYPE=HIDDEN NAME=delete_all VALUE=1>";
		}
		echo "</FORM>";
		echo "</TD>";

		echo "</TR>";
		echo "<TR BGCOLOR=\"$color_line1\">"
			."<TD VALIGN=TOP COLSPAN=4>".$help_text."</TD>"
			."</TR>"
			."</TABLE>"
			."</TD>"
			."</TR>";

		if ($infofields['type'] == "skill" && $infofields['value'] <> "")  //get resources related to skill
		{
			$prod_list = $db->Execute("SELECT * FROM $dbtables[product_table] "
										."WHERE skill_abbr='".$infofields['value']."' "
										."ORDER BY skill_level");

			if (!$prod_list->EOF)
			{
				echo "<TR BGCOLOR=\"$color_line2\">"
					."<TD VALIGN=TOP>"
					."<TABLE BORDER=0 CELLPADDING=0>"
					."<TR>"
					."<TD COLSPAN=6>"
					."<B>You can use ".$infofields['title']." to get the following products</B><P>"
					."</TD>"
					."</TR>"
					."<TR>"
					."<TD WIDTH=50></TD>"
					."<TD></TD>"
					."<TD>&nbsp;<B>Skill Level</B>&nbsp;</TD>"
					."<TD>&nbsp;<B>Product</B>&nbsp;</TD>"
					."<TD>&nbsp;<B>Weight&nbsp;</B></TD>"
					."<TD>&nbsp;<B>Material Reqd&nbsp;</TD>"
					."</TR>";
			}

			while ( $prod_list_info=$prod_list->FetchRow() )
			{
				echo ""
					."<FORM METHOD=POST ACTION=helper.php>"
					."<TR>"
					."<TD></TD>"
					."<TD VALIGN=TOP>";
				display_button ("Edit", "", "Resource", $prod_list_info['long_name'], $prod_list_info['proper']);
				echo "</TD>"
					."<TD VALIGN=TOP ALIGN=RIGHT> ".$prod_list_info['skill_level']."&nbsp;</TD>"
					."<TD VALIGN=TOP>&nbsp;"
					."<A HREF=\"helper.php?query=1&htitle=".$prod_list_info['proper']."&type=resource\">"
					.$prod_list_info['proper']."</A>&nbsp;"
					."</TD>"
					."<TD VALIGN=TOP ALIGN=RIGHT>&nbsp;".$prod_list_info['weight']."&nbsp;</TD>"
					."<TD VALIGN=TOP>&nbsp;".$prod_list_info['material']."&nbsp;</TD>"
					."</TR>"
					."</FORM>";
			}
			$prod_list->MoveFirst();
			if (!$prod_list->EOF)
			{
				echo "</TABLE></TD></TR>";
			}
			

		}
		$info->MoveNext();
		echo "<TR BGCOLOR=\"$color_bg\"><TD>&nbsp;</TD></TR>";
	}

	echo "</TABLE>";
}




function db_get_entry ($id, $entry)
{
	global $db, $dbtables;

	$qhelp = $db->Execute("SELECT * FROM $dbtables[gd_help] "
						."WHERE id='$id'");
	$help_record = $qhelp->fields;
	$type = $help_record[type];
	$value = $help_record[value];
	$title = $help_record[title];
	$help = $help_record[help];

	$fragments = explode("<ENTRY-", $help);
	unset ($fragments[0]);

	foreach ($fragments as $frag)
	{
		ereg("([^-]*)(-)([^>]*)(>)(.*)", $frag, $parsed);

		if ($parsed[3]==$entry)
		{
			return "<ENTRY-".$frag;
		}
	}

	return "";
}


function db_delete_entry ($id, $clan, $entry)
{
	global $db, $dbtables;

	if ($clan=="" && $entry=="")
	{
		$i=0;
		$new_help="<ENTRIES-O>";
		$db->Execute("UPDATE $dbtables[gd_help] "
					."SET `help`='$new_help' "
					."WHERE id='$id'");
		return;
	}

	$qhelp = $db->Execute("SELECT * FROM $dbtables[gd_help] "
						."WHERE id='$id'");
	$help_record = $qhelp->fields;
	$type = $help_record[type];
	$value = $help_record[value];
	$title = $help_record[title];
	$help = $help_record[help];

	$fragments = explode("<ENTRY-", $help);
	unset ($fragments[0]);
	$new_help = "";

	$i = 0;
	foreach ($fragments as $frag)
	{
		ereg("([^-]*)(-)([^>]*)(>)(.*)", $frag, $parsed);

		if ($clan=="" && $entry<>$parsed[3])
			{
				$i++;
				$new_help = $new_help."<ENTRY-".$parsed[1]."-".$i.">".$parsed[5];
			}
		elseif ($entry=="" && $clan<>$parsed[1])
			{
				$i++;
				$new_help = $new_help."<ENTRY-".$parsed[1]."-".$i.">".$parsed[5];
			}
	}
	$new_help = "<ENTRIES-$i>".$new_help;
	
	$db->Execute("UPDATE $dbtables[gd_help] "
				."SET `help`='$new_help' "
				."WHERE id='$id'");
}


function db_replace_entry ($id, $clan, $entry, $new_text)
{
	global $db, $dbtables;

	$qhelp = $db->Execute("SELECT * FROM $dbtables[gd_help] "
						."WHERE id='$id'");
	$help_record = $qhelp->fields;
	$type = $help_record[type];
	$value = $help_record[value];
	$title = $help_record[title];
	$help = $help_record[help];

	
	$fragments = explode("<ENTRY-", $help);

	$new_help = $fragments[0];
	unset($fragments[0]);

	foreach ($fragments as $frag)
	{
		ereg("([^-]*)(-)([^>]*)(>)(.*)", $frag, $parsed);
		$new_help = $new_help."<ENTRY-".$parsed[1]."-".$parsed[3].">";

		if ($parsed[3]==$entry)
		{
			$sqltext = form_text_sql($new_text);
			$new_help = $new_help.$sqltext."</ENTRY>";
		}
		else
		{
			$new_help = $new_help.$parsed[5];
		}
	}

	$db->Execute("UPDATE $dbtables[gd_help] "
				."SET `help`='$new_help' "
				."WHERE id='$id'");
}


page_footer();

?>