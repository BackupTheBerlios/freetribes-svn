<?
session_start();
header("Cache-control: private");

include("config.php");

$title="Multi-player Detector";
include("header.php");
include("lookups.php");

connectdb();

$username = $_SESSION['username'];
$admin = $db->Execute("SELECT * FROM $dbtables[chiefs] "
                     ."WHERE username = '$username'");
$admininfo = $admin->fields;


$module = $_REQUEST[menu];

if( !$admininfo[admin] >= '2' )
{
    echo 'You must be an administrator to use this tool.<BR>';
    TEXT_GOTOMAIN();
    die();
}

echo 'Click <A HREF=admin.php>here</A> to return to the admin menu.<BR>';
echo '<P>';
$chief = $db->Execute("SELECT * FROM $dbtables[chiefs]");
while( !$chief->EOF )
{
    $chiefinfo = $chief->fields;
    $checkpass1 = md5($chiefinfo[chiefname]);
    $checkpass2 = md5($chiefinfo[username]);
    $hourchecktop = $chiefinfo[hour] + 900;
    $hourcheckbottom = $chiefinfo[hour] - 900;
    $check = $db->Execute("SELECT * FROM $dbtables[chiefs] "
                         ."WHERE password = '$chiefinfo[password]' "
                         ."AND clanid <> '$chiefinfo[clanid]' "
                         ."OR password = '$checkpass1' "
                         ."AND clanid <> '$chiefinfo[clanid]' "
                         ."OR password = '$checkpass2' "
                         ."AND clanid <> '$chiefinfo[clanid]'");
    $hour = $db->Execute("SELECT * FROM $dbtables[chiefs] "
                        ."WHERE hour < $hourchecktop "
                        ."AND hour > $hourcheckbottom "
                        ."AND clanid <> $chiefinfo[clanid] "
                        ."AND hour > 0");
    while( !$check->EOF )
    {
        $checkinfo = $check->fields;
        echo "$chiefinfo[clanid] $chiefinfo[chiefname] $chiefinfo[username] ";
        echo "is a possible dual player with $checkinfo[clanid] $checkinfo[chiefname] $checkinfo[username]<BR>";
        $check->MoveNext();
    }
    while ( !$hour->EOF )
    {
        $hourinfo = $hour->fields;
        if( $hourinfo[ipaddr] == $chiefinfo[ipaddr] )
        {
            echo "$chiefinfo[clanid] possible multiplayer with $hourinfo[clanid].<BR>";
            google_user_email( $chiefinfo[email] );
        }
        $hour->MoveNext();
    }
    $chief->MoveNext();
}
echo "<P>";
TEXT_GOTOMAIN();
include("footer.php");
?>
