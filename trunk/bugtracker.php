<?
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: bugtracker.php

session_start();
header("Cache-control: private");
include("config.php");
include("game_time.php");

page_header("Bug Tracker");

connectdb();

$username = $_SESSION['username'];

echo "<BR>Click <A HREF=bugtracker.php>Here</A> to return to the bug tracker main screen.<br><p>";
$module = $_POST['action'];

if( $_POST['submit'] )
{
    if( !$_POST['summary'] )
    {
        echo "<CENTER><FONT COLOR=WHITE>Please use your browser's back button ";
        echo "and fill in a summary for your problem.<BR></FONT></CENTER>";
        include("footer.php");
        die();
    }
    else
    {
        $_POST['summary'] = addslashes($_POST['summary']);
        $_POST['detail'] = addslashes($_POST['detail']);
        $db->Execute("INSERT INTO $dbtables[bug_tracker] "
                    ."VALUES("
                    ."'',"
                    ."'',"
                    ."'$_POST[clanid]',"
                    ."'$_POST[tribeid]',"
                    ."'$_POST[username]',"
                    ."'$_POST[skillname]',"
                    ."'$_POST[product]',"
                    ."'$_POST[summary]',"
                    ."'$_POST[detail]',"
                    ."'NEW',"
                    ."'Nobody',"
                    ."'$month[count]',"
                    ."'$year[count]')");
        $tik = $db->Execute("SELECT * FROM $dbtables[bug_tracker] "
                           ."WHERE summary = '$_POST[summary]' "
                           ."AND clanid = '$_POST[clanid]' "
                           ."AND tribeid = '$_POST[tribeid]' "
                           ."AND product = '$_POST[product]'");
        $ticket = $tik->fields;
        $db->Execute("UPDATE $dbtables[bug_tracker] "
                    ."SET ticketid = entryid "
                    ."WHERE entryid = $ticket[entryid]");
    }
    echo "<CENTER><FONT COLOR=WHITE>Your bug report has been submitted. Thank you.<BR></FONT></CENTER>";
    $title = $_POST['summary'];
    $description = $_POST['detail'];
    $type = $_POST['task_type'];
    $category = $_POST['product_category'];
    $severity = $_POST['task_severity'];
    $priority = 1;
    $server = $game_url.$game_url_path;
    $skill = $_POST['skillname'];
    $res = file_bug_report($title,$description,$type,$category,$skill,$severity,$priority,$server,$version);
}
if( $_POST['search'] )
{
    echo "<FORM ACTION=bugtracker.php METHOD=POST>";
    echo "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=\"100%\">";
    echo "<TR BGCOLOR=$color_header ALIGN=CENTER><TD COLSPAN=3><FONT COLOR=WHITE SIZE=+2>";
    echo "Ticket Search</FONT></TD></TR>";
    echo "<TR BGCOLOR=$color_header ALIGN=CENTER>";
    echo "<TD><SELECT NAME=field>";
    $fld = $db->Execute("DESCRIBE $dbtables[bug_tracker]");
    while( !$fld->EOF )
    {
        $table = $fld->fields;
        echo "<OPTION VALUE=$table[Field]>$table[Field]</OPTION>";
        $fld->MoveNext();
    }
    echo "</SELECT>&nbsp;<SELECT NAME=compare>";
    echo "<OPTION VALUE=\"=\">IS</OPTION>";
    echo "<OPTION VALUE=\"!=\">IS NOT</OPTION>";
    echo "<OPTION VALUE=\"LIKE\">LIKE</OPTION>";
    echo "<OPTION VALUE=\"NOT LIKE\">NOT LIKE</OPTION>";
    echo "</SELECT>";
    echo "<INPUT CLASS=edit_area TYPE=TEXT SIZE=25 MAXLENGTH=25 NAME=criteria VALUE=\"\"></TD>";
    echo "<TD><INPUT TYPE=SUBMIT NAME=search1 VALUE=\"Submit\"></TD></TR></TABLE></FORM>";
}

function make_tickets()
{
    global $db, $dbtables, $link_forums, $color_line1, $color_line2, $color_header;
    include("game_time.php");
    $chf = $db->Execute("SELECT * FROM $dbtables[chiefs] "
                       ."WHERE clanid = '$_SESSION[clanid]'");
    $chief = $chf->fields;
    echo "<CENTER>This is <B>*NOT*</B> for asking for help or hints. There is a ";
    echo "<A HREF=$link_forums>forum board</A> for that.</CENTER><BR>";
    echo "<FORM ACTION=bugtracker.php METHOD=POST>";
    echo "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=\"100%\">";
    echo "<TR BGCOLOR=$color_header ALIGN=CENTER><TD COLSPAN=3><FONT COLOR=WHITE SIZE=+2>";
    echo "Ticket Creation</FONT></TD></TR>";
    echo "<TR BGCOLOR=$color_header ALIGN=CENTER>";
    echo "<TD>Clan ID:&nbsp;<INPUT TYPE=HIDDEN NAME=clanid VALUE=\"$_SESSION[clanid]\">$_SESSION[clanid]</TD>";
    echo "<TD>Tribe ID:&nbsp;<INPUT TYPE=HIDDEN NAME=tribeid VALUE=\"$_SESSION[current_unit]\">$_SESSION[current_unit]</TD>";
    echo "<TD>Username:&nbsp;<INPUT TYPE=HIDDEN NAME=username VALUE=\"$chief[username]\">$chief[username]</TD>";
    echo "</TR><TR BGCOLOR=$color_line1 ALIGN=CENTER>";
    echo "<td>Severity: <select name=\"task_severity\"><option value=\"5\">Critical</option><option value=\"4\">High</option><option value=\"3\">Medium</option><option value=\"2\" SELECTED>Low</option><option value=\"1\">Very Low</option></select></td>";
    echo "<td>Type:<select name='task_type'><option value='1'>Bug Report</option><option value='2'>Feature Request</option><option value='3'>Support Request</option></select></td>";
    echo "<td>Category: <select name='product_category'><option value='1'>Backend / Core</option><option value='2'>User Interface</option></select></td>";
    echo "</TR><TR BGCOLOR=$color_line1 ALIGN=CENTER>";
    echo "<TD>Skill affected:&nbsp; <SELECT NAME=skillname>";
    echo "<OPTION VALUE=\"NA\">N/A</OPTION>";
    $skl = $db->Execute("SELECT * FROM $dbtables[skill_table] ORDER BY long_name");
    while( !$skl->EOF )
    {
        $skill = $skl->fields;
        echo "<OPTION VALUE=\"$skill[abbr]\">$skill[long_name]</OPTION>";
        $skl->MoveNext();
    }
    echo "</SELECT></TD>";
    echo "<TD>Product affected:&nbsp; <SELECT NAME=product>";
    echo "<OPTION VALUE=\"NA\">N/A</OPTION>";
    $prod = $db->Execute("SELECT * FROM $dbtables[product_table] ORDER BY proper");
    while( !$prod->EOF )
    {
        $product = $prod->fields;
        echo "<OPTION VALUE=\"$product[long_name]\">$product[proper]</OPTION>";
        $prod->MoveNext();
    }
    $liv = $db->Execute("SELECT DISTINCT type FROM $dbtables[livestock]");
    while( !$liv->EOF )
    {
        $livestock = $liv->fields;
        echo "<OPTION VALUE=\"$livestock[type]\">$livestock[type]</OPTION>";
        $liv->MoveNext();
    }
    $ore = $db->Execute("SELECT DISTINCT long_name FROM $dbtables[resources]");
    while( !$ore->EOF )
    {
        $resource = $ore->fields;
        echo "<OPTION VALUE=\"$resource[long_name]\">$resource[long_name]</OPTION>";
        $ore->MoveNext();
    }
    echo "</SELECT></TD>";
    echo "<TD>&nbsp;</TD></TR>";
    echo "<TR ALIGN=CENTER BGCOLOR=$color_line2><TD COLSPAN=3>Please provide a brief summary:&nbsp;";
    echo "<INPUT CLASS=edit_area TYPE=TEXT NAME=summary SIZE=50 MAXLENGTH=50 VALUE=\"\"></TD></TR>";
    echo "<TR BGCOLOR=$color_header ALIGN=CENTER>";
    echo "<TD COLSPAN=3>Please describe the suspected bug:&nbsp;<BR>";
    echo "<TEXTAREA NAME=detail ROWS=8 COLS=80>";
    echo "\r\n";
    echo "</TEXTAREA></TD></TR>";
    echo "<TR ALIGN=CENTER><TD COLSPAN=3><INPUT TYPE=SUBMIT NAME=submit VALUE=\"Submit\" onclick=\"Disable1()\"></TD></TR>";
    echo "</TABLE></FORM>";
}

function show_tickets( $field, $compare, $criteria )
{
    global $db, $dbtables, $color_line1, $color_line2, $color_header;
    if( !$field && !$compare && !$criteria )
    {
        $res = $db->Execute("SELECT DISTINCT ticketid FROM $dbtables[bug_tracker] "
                           ."ORDER BY ticketid DESC");
    }
    else
    {
        $res = $db->Execute("SELECT * FROM $dbtables[bug_tracker] "
                           ."WHERE $field $compare '%$criteria%'");
    }
    $adm = $db->Execute("SELECT * FROM $dbtables[chiefs] "
                       ."WHERE clanid = '$_SESSION[clanid]'");
    $admin = $adm->fields;
    echo "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=\"100%\">";
    echo "<TR BGCOLOR=$color_header ALIGN=CENTER><TD COLSPAN=";
    if( $admin[admin] > 2 )
    {
        echo "6";
    }
    else
    {
        echo "5";
    }
    echo "><FONT COLOR=WHITE SIZE=+2>";
    echo "Tracked Bugs</FONT></TD></TR>";
    echo "<TR BGCOLOR=$color_header ALIGN=CENTER>";
    if( $admin[admin] > 2 )
    {
        echo "<TD>&nbsp;</TD>";
    }
    echo "<TD><FONT COLOR=WHITE SIZE=+1>TicketID</FONT></TD>";
    echo "<TD><FONT COLOR=WHITE SIZE=+1>Summary</FONT></TD>";
    echo "<TD><FONT COLOR=WHITE SIZE=+1>Status</FONT></TD>";
    echo "<TD><FONT COLOR=WHITE SIZE=+1>Game Month</FONT></TD>";
    echo "<TD><FONT COLOR=WHITE SIZE=+1>Game Year</FONT></TD></TR>";
    if( $res->EOF )
    {
        echo "<TR BGCOLOR=$color_line1 ALIGN=CENTER><TD COLSPAN=";
        if( $admin[admin] > 2 )
        {
            echo "6";
        }
        else
        {
            echo "5";
        }
        echo ">None</TD></TR>";
    }
    else
    {
        $line_color = $color_line2;
        while( !$res->EOF )
        {
            $row = $res->fields;
            $tics = $db->Execute("SELECT * FROM $dbtables[bug_tracker] "
                                ."WHERE ticketid = '$row[ticketid]'");
            $ticket = $tics->fields;
            if( $line_color == $color_line2 )
            {
                $line_color = $color_line1;
            }
            else
            {
                $line_color = $color_line2;
            }
            if( $_REQUEST['ticketid'] != $ticket[ticketid] )
            {
                echo "<TR BGCOLOR=$line_color ALIGN=CENTER>";
                if( $admin[admin] > 2 )
                {
                    echo "<TD><INPUT TYPE=RADIO NAME=ticketid VALUE=$ticket[ticketid]></TD>";
                }
                echo "<TD><A HREF=\"bugtracker.php?ticketid=$ticket[ticketid]\">$ticket[ticketid]</A></TD>";
                echo "<TD><A HREF=\"bugtracker.php?ticketid=$ticket[ticketid]\">$ticket[summary]</A></TD>";
                echo "<TD>$ticket[status]</TD>";
                echo "<TD>$ticket[month]</TD>";
                echo "<TD>$ticket[year]</TD></TR>";
            }
            else
            {
                echo "<TR><TD COLSPAN=";
                if( $admin[admin] > 2 )
                {
                    echo "6";
                }
                else
                {
                    echo "5";
                }
                echo ">&nbsp;</TD></TR>";
                echo "<TR BGCOLOR=$line_color ALIGN=CENTER>";
                if( $admin[admin] > 2 )
                {
                    echo "<INPUT TYPE=RADIO NAME=ticketid VALUE=$ticket[ticketid]></TD>";
                }
                echo "<TD><A HREF=\"bugtracker.php\">$ticket[ticketid]</A></TD>";
                echo "<TD><A HREF=\"bugtracker.php\">$ticket[summary]</A></TD>";
                echo "<TD>$ticket[status]</TD>";
                echo "<TD>$ticket[month]</TD>";
                echo "<TD>$ticket[year]</TD></TR>";
                if( $line_color == $color_line2 )
                {
                    $line_color = $color_line1;
                }
                else
                {
                    $line_color = $color_line2;
                }
                echo "<TR ALIGN=CENTER BGCOLOR=$line_color>";
                echo "<TD>Clanid: $ticket[clanid]</TD>";
                echo "<TD>Tribe: $ticket[tribeid]</TD>";
                echo "<TD>&nbsp;</TD>";
                echo "<TD>Skill: $ticket[skillname]</TD>";
                echo "<TD>Product: $ticket[product]</TD>";
                echo "</TR>";
                echo "<TR ALIGN=CENTER style=\"background-image: url(images/parchment_bg.png);\">";
                echo "<TD COLSPAN=5><FONT COLOR=BLACK>$ticket[detail]</FONT></TD></TR>";
                echo "<TR><TD COLSPAN=";
                if( $admin[admin] > 2 )
                {
                    echo "6";
                }
                else
                {
                    echo "5";
                }
                echo ">&nbsp;</TD></TR>";
            }
            $res->MoveNext();
        }
    }
    echo "</TABLE>";
}
if( $_POST[search1] )
{
    $_POST[criteria] = addslashes( $_POST[criteria] );
    show_tickets($_POST[field], $_POST[compare], $_POST[criteria]);
    include("footer.php");
    die();
}

if( !$_POST['new'] && !$_POST['submit'] )
{
    $adm = $db->Execute("SELECT * FROM $dbtables[chiefs] "
                       ."WHERE clanid = $_SESSION[clanid]");
    $admin = $adm->fields;
    echo "<FORM ACTION=bugtracker.php METHOD=POST>";
    echo "<TABLE CELLPADDING=0 CELLSPACING=0 BORDER=0><TR>";
    echo "<TD>&nbsp;<INPUT TYPE=SUBMIT NAME=new VALUE=\"Submit Bug\">&nbsp;</TD>";
    echo "<TD>&nbsp;<INPUT TYPE=SUBMIT NAME=search VALUE=\"Search\">&nbsp;</TD>";
    if( $admin[admin] > 2 )
    {
        echo "<TD>&nbsp;<INPUT TYPE=SUBMIT NAME=update VALUE=\"Update\">&nbsp;</TD>";
    }
    echo "</TR></TABLE></FORM>";
    show_tickets($_POST[field], $_POST[compare], $_POST[criteria]);
}
elseif( $_POST['new'] )
{
    make_tickets();
}


page_footer();

function file_bug_report($title,$description,$type,$category,$skill,$severity,$priority,$server,$version)
{

  /*<form name="form1" action="http://phplogix.com/bugtrax/index.php" method="post">

      <input type="hidden" name="do" value="modify" />
      <input type="hidden" name="action" value="newtask" />
      <input type="hidden" name="project_id" value="1" />
      <input id="itemsummary" type="text" name="item_summary" size="50" maxlength="100" />

<select name="task_type" id="tasktype">
<option value="1">Bug Report</option>
<option value="2">Feature Request</option>
<option value="3">Support Request</option>
</select>
<select class="adminlist" name="product_category" id="productcategory">
<option value="1">Backend / Core</option>
<option value="2">User Interface</option>
</select>
<select id="itemstatus" name="item_status">
<option value="2" SELECTED>New</option>
</select>
<input type="hidden" name="item_status" value="1">
<input type="hidden" name="task_priority" value="2">
<input type="hidden" name="assigned_to" value="0">
<input type="hidden" name="operating_system" value="1">
<select id="taskseverity" class="adminlist" name="task_severity">
<option value="5">Critical</option>
<option value="4">High</option>
<option value="3">Medium</option>
<option value="2" SELECTED>Low</option>
<option value="1">Very Low</option>
</select>
<select id="taskpriority" name="task_priority">
<option value="5">Immediate</option>
<option value="4">Urgent</option>
<option value="3">High</option>
<option value="2" SELECTED>Normal</option>
<option value="1">Low</option>
</select>
<input type="hidden" value="<?php echo $verval; ?>" name="product_version">
<textarea id="detaileddesc" class="admintext" name="detailed_desc" cols="60" rows="10"></textarea>
<input class="adminbutton" type="submit" name="buSubmit" value="Add this task" onclick="Disable1()" accesskey="s"/>
</form>*/

}
?>