<?php
session_start();
header("Cache-control: private");

include("config.php");

page_header("Tribe Transfers");

connectdb();

$username = $_SESSION['username'];
$to_tribe = "";
//OK if a tribe we're transferring to is in the same hex as we are, we're fine, otherwise we should
//have a transfer time penalty that you cannot transfer any more than what can be carried to that tribe
//therefore, you have to transfer using whatever counts as carry capacity
//so we need to know weight/capacity on submit

//It's a far more complex algorithm to do so, so at this point, the goal is just to get this shit working *correctly!!*


$goods_tribe = $db->Execute("SELECT goods_tribe from $dbtables[tribes] WHERE tribeid = '$from_tribe'");
db_op_result($goods_tribe,__LINE__,__FILE__);
$goods_tribe = $goods_tribe->fields['goods_tribe'];
$opnames = array('tribe'=>'Goods Tribe','pop'=>'Population','res'=>'Resources','prod'=>'Stores','liv'=>'LiveStock');//,'bldg'=>'Buildings')
//Oh whatever - do we really need to know this?
echo "<strong> Currently in Debugging stages- Please do not attempt to use this script if this message is displayed. Make transfers at your own risk, Developer will *not* reimburse you for any losses that occur, and your attempts will show in the debug log :)</strong><br><br>";
echo "<FORM ACTION=transfer.php METHOD=POST>";
echo "<TABLE BORDER=0 CELLPADDING=4>";
// TRANSFER OPTIONS SELECTOR
if(empty($_POST['op']))
{
echo "<TR><TD><b>Transfer</b></TD><TD VALIGN=TOP>\n"
    ."<SELECT NAME=op>\n"
    ."<OPTION VALUE=tribe>$opnames[tribe]</OPTION>\n"
    ."<OPTION VALUE=pop >$opnames[pop]</OPTION>\n"
    ."<OPTION VALUE=res>$opnames[res]</OPTION>\n"
    ."<OPTION VALUE=prod SELECTED>$opnames[prod]</OPTION>\n"
    ."<OPTION VALUE=liv >$opnames[liv]</OPTION>\n"
    //."<OPTION VALUE=bldg>$opnames[bldg]</OPTION>\n"
    ."</SELECT></TD>";
 //put a goddamn new line in this shit! It's friggin' impossible to read the viewsource when debugging.. geez!

echo "<TD><b>of</b></TD><TD>";
}
else
{
   $operation = $_POST['op'];
    echo "<tr><td><input type='hidden' name='op' value='$_POST[op]'><strong>$opnames[$operation]</strong></td><td> OF </td><td>\n";
}

if(empty($_POST['from_tribe']))
{
    echo "<SELECT NAME=from_tribe>\n";

    $clanid = $_SESSION['clanid'];
    $tribes = $db->Execute("SELECT tribeid FROM $dbtables[tribes] WHERE clanid = '$clanid' ORDER BY tribeid ASC");
    while(!$tribes->EOF)
    {
        $tribe = $tribes->fields;
        echo "<OPTION VALUE=$tribe[tribeid]";
        if ($tribe['tribeid'] == $from_tribe)
        {
            echo " SELECTED";
        }
        echo ">$tribe[tribeid]</OPTION>";
        $tribes->MoveNext();
    }
    echo "</SELECT></TD>";
}
else
{
    echo "<input type='hidden' name='from_tribe' value='$_POST[from_tribe]'><strong>$_POST[from_tribe]</strong></td>";
}

// Force the 'to' selector to show the GT if we want a GT transfer and it has not been set
if(empty($_POST['to_tribe']) && $_POST['op'] == "tribe")
{
    $to_tribe = $goods_tribe;
}
elseif(!empty($_POST['to_tribe']))
{
   $to_tribe = $_POST['to_tribe'];
}

if(empty($_POST['to_tribe']) && empty($to_tribe))
{
// Get own tribes that can be transferred to
echo "<TD><b>to</b></TD><TD><SELECT NAME=to_tribe>";
$from_hex = $db->Execute("SELECT hex_id FROM $dbtables[tribes] WHERE clanid = '$clanid' ORDER BY tribeid ");
db_op_result($from_hex,__LINE__,__FILE__);
//Seems like we could set $_SESSION['current_hex'] somewhere and update it in move.php ?
//that would remove a lot of shit like this .. it could be a join but it's a non-elegant solution
$from_hex = $from_hex->fields;
$from_hex = $from_hex['hex_id'];
$hex_tribes = $db->Execute("SELECT tribeid FROM $dbtables[tribes] WHERE clanid = '$clanid' AND hex_id='$from_hex' ORDER BY tribeid");
db_op_result($hex_tribes,__LINE__,__FILE__);
while(!$hex_tribes->EOF)
{
    $tribe = $hex_tribes->fields;
    $in_same_hex[] = $tribe[tribeid];
    echo "<OPTION VALUE=$tribe[tribeid]>";
    echo "&nbsp;&nbsp;&nbsp;$tribe[tribeid]</OPTION>\n";
    $hex_tribes->MoveNext();
}
// Get allies that can be transferred to  must be in the same hex or its unfair with the above limitation
if ($op<>"tribe")
{
    $allies = $db->Execute("SELECT offerer_id, receipt_id FROM $dbtables[alliances] WHERE accept = 'Y' AND (offerer_id = '$clanid' OR receipt_id = '$clanid')");
    db_op_result($allies,__LINE__,__FILE__);
   $i = 0;
    $allies_selectable = array();
    while(!$allies->EOF)
    {
        if ($allies->fields['offerer_id']<>$clanid)
        {
            $ally = $allies->fields['offerer_id'];
        }
        else
        {
            $ally = $allies->fields['receipt_id'];
        }

        $local_allies = $db->Execute("SELECT tribeid FROM $dbtables[tribes] WHERE clanid = '$ally' AND hex_id='$from_hex' ORDER BY tribeid ");
        db_op_result($local_allies,__LINE__,__FILE__);
        while(!$local_allies->EOF)
        {
            $allies_selectable[$i] = $local_allies->fields['tribeid'];
            $local_allies->MoveNext();
            $i++;
        }

        $allies->MoveNext();
    }
    if ($i<>0)
    {
        echo "<OPTION VALUE=Allies>--- Allies ---</OPTION>\n";
        sort($allies_selectable);
        foreach ($allies_selectable as $ally)
        {
            echo "<OPTION VALUE=$ally";
            if ($ally==$to_tribe)
            {
                echo " SELECTED";
            }
            echo ">&nbsp;&nbsp;&nbsp;$ally</OPTION>\n";
        }
    }

 }

echo "</SELECT>\n";
}
else
{
    echo "<TD><input type='hidden' name='to_tribe' value='$to_tribe'><b>to</b></TD><TD><strong>$to_tribe</strong>";
}

echo "</TD>\n";

if(empty($_POST))
{
    $doit="No";
    $submit="Set Transfer Settings";
}
else
{
    $doit = "Yes";
    $submit = "Submit Transfer";
}


echo "<TD>&nbsp;<INPUT TYPE=SUBMIT NAME=submit VALUE=\"$submit\">\n"
    ."</TD></TR><TR><TD COLSPAN=7>\n";

if ($op=="tribe")
{
    echo "<I>To remove $from_tribe from a goods tribe relationship, set $from_tribe as the tribe you want to transfer to.</I>\n";
}

echo "&nbsp;<P></TD></TR></TABLE>\n";

$button_main = true;
//OK the above shit seems to work perfectly, now to fix the trans_* scripts to reflect new design of this code..

if(!empty($_POST['op']))
{
    $_POST['unit']=$_SESSION['current_unit'];
    $_POST['unit']=$_SESSION['current_unit'];
    if($_POST['op'] == "res")
    {
        include_once('trans_res.php');
    }
    elseif($_POST['op'] == "liv")
    {
        include_once('trans_liv.php');
    }
    elseif($_POST['op'] == "tribe")
    {
        include_once('trans_tribe.php');
    }
    elseif($_POST['op'] == "prod")
    {
        include_once('trans_prod.php');
    }
    elseif($_POST['op'] == "pop")
    {
        include_once('trans_pop.php');
    }
    elseif($_POST['op'] == "bldg")
    {
        include_once('trans_bldg.php');
    }
    else
    {
         //someone been trying to hack!
         $message = "Someone from $ip_addr has attempted to exploit transfer.php in a transfer attempt.";
         //admin_log('HACKATTEMPT',$message);
         die("Possible Hack Attempt has been logged in admin records. Transfer Operation not recognized!");
    }
}
echo "</FORM>";

page_footer();

?>
