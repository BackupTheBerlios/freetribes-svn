<?php
session_start();
header("Cache-control: private");

include("config.php");
include_once('global_funcs.php');

$_SESSION['theme'] = $theme_default;  // This must be set on this page before the header is included

$title= 'TribeStrive';

page_header("Tribe Strive");

echo "<CENTER>";
$username = "";
$password = "";

navbar_open();
navbar_link("heraldry.php", "", "Who's On?");
navbar_link("new.php", "", "Create Clan");
navbar_link("help.php", "", "Help");
navbar_link("bugtracker.php", "", "Bug Reporting");
navbar_link("$link_forums", "", "Forums");
navbar_close();
?>
<BR><BR><P>
    <form action="login2.php" method="post">
    <TABLE ALIGN=CENTER CELLPADDING="4">
    <TR ALIGN=LEFT>
        <TD align="right" valign="bottom">Username:</TD>
        <TD align="left" valign="bottom"><INPUT TYPE="TEXT" NAME="username" SIZE="20" MAXLENGTH="40" VALUE="<?php echo "$username" ?>" class=edit_area></TD>
        </TR>

    </TR>
    <TR>
        <TD align="right" valign="top">Password:</TD>
        <TD align="left" valign="top"><INPUT TYPE="PASSWORD" NAME="password" SIZE="20" MAXLENGTH="20" VALUE="<?php echo "$password" ?>" class=edit_area></TD>
    </TR>
    <TR>
        <TD COLSPAN=2 ALIGN=RIGHT><INPUT TYPE="SUBMIT" VALUE="SUBMIT"></TD>
    </TR>
    </TABLE>
    </FORM>

<P>

<TABLE BORDER=0 WIDTH=100%><TR ALIGN=CENTER>
<TR>
    <TD WIDTH="100%" ALIGN=CENTER COLSPAN=3>
<?php
 $sql = "SELECT headline,news from $dbtables[news] order by expire limit 3";
 $result = $db->Execute($sql);
 db_op_result($result,__LINE__,__FILE__);
 while(!$result->EOF)
 {
    $data = $result->fields;
    echo "<p align='center'><strong>$data[headline] </strong></p>";
    echo "<p align='left'>$data[news]</p>";
    echo "<br>";
    $result->MoveNext();
 }
  include('motd_bottom_top.html');
     ?>

     </TD>
</TR>
</TABLE>
<? page_footer(); ?>
