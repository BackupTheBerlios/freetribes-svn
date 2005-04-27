<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<TITLE> New Document </TITLE>
<META NAME="Generator" CONTENT="EditPlus">
<META NAME="Author" CONTENT="">
<META NAME="Keywords" CONTENT="">
<META NAME="Description" CONTENT="">

<link rel="stylesheet" type="text/css" href="../themes/Original/style.css">

<link rel="icon" HREF="../themes/Original/icon.ico">

</HEAD>

<BODY>
<?php
if( $_REQUEST[admin_mail] )
{
    exec("echo $game_name = \"$_REQUEST[game_name]\" >> install_config_local.php");
    exec("echo $admin_mail = \"$_REQUEST[admin_mail]\" > install_config_local.php");
    exec("echo $gamedomain = \"$_REQUEST[gamedomain]\" > install_config_local.php");
    exec("echo $gamepath = \"$_REQUEST[gamepath]\" > install_config_local.php");
    exec("echo $link_forums = \"$_REQUEST[link_forums]\" > install_config_local.php");
    exec("echo $ADOdbpath = \"$_REQUEST[ADOdbpath]\" > install_config_local.php");
    exec("echo $db_prefix = \"$_REQUEST[db_prefix]\" > install_config_local.php");
    exec("echo $dbhost = \"$_REQUEST[dbhost]\" > install_config_local.php");
    exec("echo $dbport = \"$_REQUEST[dbport]\" > install_config_local.php");
    exec("echo $dbuname = \"$_REQUEST[dbuname]\" > install_config_local.php");
    exec("echo $dbpass = \"$_REQUEST[dbpass]\" > install_config_local.php");
    exec("echo $db_type = \"$_REQUEST[db_type]\" > install_config_local.php");
    exec("echo $db_persistent = \"$_REQUEST[db_persistent]\" > install_config_local.php");
}
?>

<FORM METHOD=POST ACTION=install_cfg.php>

<TABLE BORDER=1 CELLPADDING=4>
<TR>
<TD VALIGN=TOP><B>Game Name</B><BR><INPUT TYPE=INPUT LENGTH=50 SIZE=50 NAME=game_name VALUE="Prowler"></TD>
<TD VALIGN=TOP>This is the name of your game, often the name of your server. At time of writing there are three Tribe Strive servers known to be running. Those are Agamemnon, Avalanche and Prowler (the original server).
</TD>
</TR>
<TR>
<TD VALIGN=TOP><B>Admin Email</B><BR><INPUT TYPE=INPUT LENGTH=50 SIZE=50 NAME=admin_mail VALUE="tribe_admin@my.host.com"></TD>
<TD VALIGN=TOP>This email address will be used as the address to reply to on all outgoing mail and as the contact address for incoming mail.
</TD>
</TR>
<TR>
<TD VALIGN=TOP><B>Game Domain</B><BR><INPUT TYPE=INPUT LENGTH=50 SIZE=50 NAME=gamedomain VALUE=".tribe.net"></TD>
<TD VALIGN=TOP>Domain & path of the game on your webserver (used to validate login cookie). This is the domain name part of the URL people enter to access your game. So if your game is at www.blah.com you would have:
<P>$gamedomain = ".blah.com";
<P>Do not enter slashes for $gamedomain or anything that would come after a slash if you get weird errors with cookies then make sure the game domain has TWO dots i.e. if you reside your game on http://www.tribe.net type in .tribe.net. If your game is on http://www.some.site.net put .some.site.net as your game domain. Do not put port numbers in $gamedomain.</TD>
</TR>
<TR>
<TD VALIGN=TOP><B>Game Path</B><BR><INPUT TYPE=INPUT LENGTH=50 SIZE=50 NAME=gamepath VALUE="/tribe/"></TD>
<TD VALIGN=TOP>This is the trailing part of the URL, that is not part of the domain. If you enter www.blah.com/blacknova to access the game, you would leave the line as it is. If you do not need to specify TribeStrive, just enter a single slash eg:
</TD>
</TR>
<TR>
<TD VALIGN=TOP><B>Game Root</B><BR><INPUT TYPE=INPUT LENGTH=50 SIZE=50 NAME=gamepath VALUE="/var/www/tribe/"></TD>
<TD VALIGN=TOP>This is location of Tribe Strive on your physical hard disk. eg
<P>UNIX: <b>/var/www/tribe/</b>
<BR>WINXP: <B>c:\www\tribe\</B>
</TD>
</TR>
<TR>
<TD VALIGN=TOP><B>Forums Link</B><BR><INPUT TYPE=INPUT LENGTH=50 SIZE=50 NAME=link_forums VALUE="./phpbb2/"></TD>
<TD VALIGN=TOP>This is the path to any forum that you have set up.<P>By default, this is set to link to the preconfigured <B>phpBB2</B> forum that is distributed with the Tribe Strive source code.<P>You will have the option to set up the preconfigured forum after installation of the Tribe Strive database is complete.
</TD>
</TR>
<TR>
<TD VALIGN=TOP><B>Path to ADOdb</B><br><INPUT TYPE=INPUT LENGTH=50 SIZE=50 NAME=ADOdbpath VALUE=ADOdb></TD>
<TD VALIGN=TOP>The ADOdb db module is required to run Tribe Strive. You can find it at http://php.weblogs.com/ADODB. <P>Enter the path where it is installed here. The game assumes that ADOdb is installed in a subdirectory of the main game directory.</TD>
</TR>
<TR>
<TD VALIGN=TOP><B>DB Prefix</B><BR><INPUT TYPE=INPUT LENGTH=50 SIZE=50 NAME=db_prefix VALUE="tribe_"></TD>
<TD VALIGN=TOP>Table prefix for the database. If you want to run more than one game of TN on the same database, or if the current table names conflict with tables you already have in your db, you will
need to change this.</TD>
</TR>
<TR>
<TD VALIGN=TOP><B>DB Server Name</B><BR><INPUT TYPE=INPUT LENGTH=50 SIZE=50 NAME=dbhost VALUE="localhost"></TD>
<TD VALIGN=TOP>Hostname of the database server.<P><b>localhost</b> is the default. You normally won't have to change it if you are running the game on your own server. If your game is going to run on a service provided by an ISP you will probably have to get the correct server name from them.
</TD>
</TR>
<TR>
<TD VALIGN=TOP><B>DB Service Port</B><BR><INPUT TYPE=INPUT LENGTH=50 SIZE=50 NAME=dbport VALUE=""></TD>
<TD VALIGN=TOP>The port that your database server receives requests on.<P><i>Empty</i> is the default. You normally won't have to change it if you are running the game on your own server. If your game is going to run on a service provided by an ISP you will probably have to get the correct server name from them.
</TD>
</TR>
<TR>
<TD VALIGN=TOP><B>DB User Name</B><BR><INPUT TYPE=INPUT LENGTH=50 SIZE=50 NAME=dbuname VALUE="root"></TD>
<TD VALIGN=TOP>A username with full privileges that you can connect to the database with.<P><b>root</b> is a common default, but you may have to change it.
</TD>
</TR>
<TR>
<TD VALIGN=TOP><B>DB User Password</B><BR><INPUT TYPE=INPUT LENGTH=50 SIZE=50 NAME=dbpass VALUE=""></TD>
<TD VALIGN=TOP>A username with full privileges that you can connect to the database with.<P><b>root</b> is a common default, but you may have to change it.
</TD>
</TR>
<TR>
<TD VALIGN=TOP><B>DB Type</B><BR><INPUT TYPE=INPUT LENGTH=50 SIZE=50 NAME=db_type VALUE="mysql"></TD>
<TD VALIGN=TOP>Type of the SQL database. This can be anything supported by ADOdb. Here are a few:
<P><b>access</b> for MS Access databases. You need to create an ODBC DSN.
<BR><b>ado</b> for ADO databases
<BR><b>ibase</b> for Interbase 6 or earlier
<BR><b>borland_ibase</b> for Borland Interbase 6.5 or up
<BR><B>mssql</B> for Microsoft SQL
<BR><B>mysql</B> for MySQL
<BR><B>oci8</B> for Oracle8/9
<BR><B>odbc</B> for a generic ODBC database
<BR><B>postgres</B> for PostgreSQL ver < 7
<BR><B>postgres7</B> for PostgreSQL ver 7 and up
<BR><B>sybase</B> for a SyBase database
<P>Note that, at present, only MySQL is guaranteed to work as the result of the source code still containing legacy MySQL specific commands.
</TD>
</TR>
<TR>
<TD VALIGN=TOP><B>DB Persistance</B><BR><INPUT TYPE=INPUT LENGTH=50 SIZE=50 NAME=db_persistent VALUE="0"></TD>
<TD VALIGN=TOP>Set this to 1 to use db persistent connections, 0 otherwise.<P>Note that persistent connections have no particular benefit if you are using MySQL.</TD>
</TR>
<TR ALIGN=CENTER><TD COLSPAN=2><INPUT TYPE=SUBMIT NAME=submit VALUE=SUBMIT></TD></TR>
</TABLE></FORM>


</BODY>
</HTML>
