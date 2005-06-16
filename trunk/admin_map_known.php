<?php
session_start();
header("Cache-control: private");
include("config.php");
$admincheck = $_SESSION['username'];
//here we should query the db to get admin's username or id or some such to auth
if($admincheck !== 'admin')
{
    die("You Do Not    Have permissions to view this page!");
}
page_header("Admin Mapping - Known World");


    echo "<BR><FONT SIZE=+1>Click <a href=main.php>here</a> to return to the main menu.</FONT><br>";
    //echo "<P>This map will be changed, to be more representative of a map, instead of being so random.";

    connectdb();

    if(!$_SESSION['clanid'])
    {
      echo "You must <a href=index.php>login</a> before viewing this page.\n";
      page_footer();
    }


    $result = $db->Execute("SELECT hex_id, res_type, terrain FROM $dbtables[hexes] ORDER BY hex_id ASC");
    db_op_result($result,__LINE__,__FILE__);
    $row = $result->fields;

        bigtitle();

        echo "<TABLE border=0 cellpadding=0 bgcolor=black>\n";

    while(!$result->EOF)
    {
        $i = 0;
        while($i < 64)
        {
            $here = $db->Execute("SELECT * FROM $dbtables[mapping] WHERE hex_id = '$row[hex_id]' AND `admin_0000` > 0");
            db_op_result($here,__LINE__,__FILE__);
            if(!$here->EOF)
            {
                $port=$row['terrain'] . $row['res_type'];
                $alt=$row['hex_id'];
                $tile = "<TD><img src=images/" . $port . ".png title=$alt border=0></A></TD>";
            }
            else
            {
                $tile = "<TD><IMG SRC=images/unknown.png></TD>";
            }
            echo $tile;
            $result->Movenext();
            $row = $result->fields;
            $i++;
        }
           echo "</TR>";
    }

    echo "</TABLE>";

    echo "<BR><BR><P>";

    include("gui/table_map_key.php");

page_footer();
?>
