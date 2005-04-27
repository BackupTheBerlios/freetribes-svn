<?
session_start();
header("Cache-control: private");
$admincheck = $_SESSION['username'];
//here we should query the db to get admin's username or id or some such to auth
if($admincheck !== 'admin')
{
die("You Do Not	Have permissions to view this page!");
}
include("config.php");

page_header("Admin Tracking - Player IP");

connectdb();
bigtitle();

$username = $_SESSION['username'];
$admin = $db->Execute("SELECT * FROM $dbtables[chiefs] "
                     ."WHERE username = '$username'");
$admininfo = $admin->fields;

$module = $_REQUEST[menu];

if( !$admininfo[admin] >= $privilege['adm_tracking'] )
{
    echo 'You must have player tracking privilege to use this tool.<BR>';
    page_footer();
}
if( !ISSET($_REQUEST[sort]) )
{
    $_REQUEST[sort] = 'clanid';
}

echo "<BR>Click <A HREF=admin.php>here</A> to return to the admin page.<BR><P>";
$res = $db->Execute("SELECT * FROM $dbtables[chiefs] "
                   ."ORDER BY $_REQUEST[sort]");
echo '<CENTER><TABLE BORDER=1 CELLPADDING=0 CELLSPACING=0>';
echo "<TR BGCOLOR=$color_header ALIGN=CENTER>";
echo "<TD><A HREF=listing.php?sort=clanid>Clan ID</A></TD>";
echo "<TD><A HREF=listing.php?sort=username>Username</A></TD>";
echo "<TD><A HREF=listing.php?sort=chiefname>Chief Name</A></TD>";
echo "<TD><A HREF=listing.php?sort=ipaddr>IP Address</A></TD>";
echo "<TD><A HREF=listing.php?sort=active>Turns Played</A></TD>";
echo "<TD><A HREF=listing.php?sort=hour>Last Login</A></TD></TR>";
while( !$res->EOF )
{
    $chief = $res->fields;
    echo "<TR><TD>$chief[clanid]</TD><TD>$chief[username]</TD><TD>$chief[chiefname]</TD>";
    echo "<TD><A HREF=http://network-tools.com/default.asp?prog=express&Netnic=whois.arin.net&host=$chief[ipaddr] target=_blank>$chief[ipaddr]</A></TD>";
    echo "<TD>$chief[active]</TD><TD>$chief[hour]</TD></TR>";
    $res->MoveNext();
}
echo '</TABLE></CENTER>';
echo "<P>";

page_footer();
?>
