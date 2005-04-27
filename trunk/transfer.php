<?
session_start();
header("Cache-control: private");

include("config.php");

page_header("Tribe Transfers");

connectdb();

$username = $_SESSION['username'];

$op_selected = array(
			"none"  =>"",
			"tribe" =>"",
			"pop"   =>"",
			"res"   =>"",
			"prod"  =>"",
			"liv"   =>"",
			"bldg"  =>""
			);


// Set default action to Stores transfer
if(!ISSET($_REQUEST['op']))
{
	$op = "prod";
	$op_selected['prod'] = "SELECTED";
}
else
{
	$op = $_REQUEST['op'];
	$op_selected[$op] = "SELECTED";
}

// Reset data if either tribe has changed or we have selected a void target
if ($_REQUEST['from_tribe']<>$_REQUEST['last_from_tribe']
	|| $_REQUEST['to_tribe']<>$_REQUEST['last_to_tribe']
	|| $_REQUEST['to_tribe']=="Allies"
	|| $_REQUEST['to_tribe']=="Clan") 
{
	unset($_REQUEST['resource']);
	unset($_REQUEST['product']);
	unset($_REQUEST['livestock']);
	unset($_REQUEST['structure']);

	unset($_REQUEST['activepop']);
	unset($_REQUEST['inactivepop']);
	unset($_REQUEST['slavepop']);
	unset($_REQUEST['specialpop']);
	unset($_REQUEST['ALREADY']);
}

// Make sure that nothing happens if the target is void
$valid_target = true;
if ($_REQUEST['to_tribe']=="Allies"
	|| $_REQUEST['to_tribe']=="Clan")
{
	$valid_target = false;
}

// Set the tribe we are transferring from
if (ISSET($_REQUEST['from_tribe']))
{
	$from_tribe = $_REQUEST['from_tribe'];
}
else
{
	$from_tribe = $_SESSION['current_unit'];
}

/*
	The next bit lets us change the tribe we are transferring from If it is in the same hex
	If the tribe is not in the same hex, we would update the 'to' selector to show the tribes
	that we can transfer to
*/
$last_from_tribe="";
if (ISSET($_REQUEST['last_from_tribe']))
{
	$last_from_tribe = $_REQUEST['last_from_tribe'];
	if (ISSET($_REQUEST['in_same_hex']))
	{
		foreach ($_REQUEST['in_same_hex'] as $tribeid)
		{
			if ($tribeid==$from_tribe)
			{
				$last_from_tribe=$from_tribe;
			}
		}
	}
}

// Set the target to the same tribe and avoid recalculating valid target for hex change
if (!ISSET($_REQUEST['to_tribe']))
{
	$to_tribe = $from_tribe;
	$last_from_tribe = $from_tribe;
}
else
{
	$to_tribe = $_REQUEST['to_tribe'];
}

$goods_tribe = $db->Execute("SELECT goods_tribe from $dbtables[tribes] WHERE tribeid = '$from_tribe'");
$goods_tribe = $goods_tribe->fields['goods_tribe'];


echo "<FORM ACTION=transfer.php METHOD=POST>";
echo "<TABLE BORDER=0 CELLPADDING=4>";


// TRANSFER OPTIONS SELECTOR


echo "<TR><TD><b>Transfer</b></TD>"
	."<TD VALIGN=TOP>"
	."<SELECT NAME=op>"
	."<OPTION VALUE=tribe ".$op_selected['tribe'].">Goods Tribe</OPTION>"
	."<OPTION VALUE=pop ".$op_selected['pop'].">Population</OPTION>"
	."<OPTION VALUE=res ".$op_selected['res'].">Resources</OPTION>"
	."<OPTION VALUE=prod ".$op_selected['prod'].">Stores</OPTION>"
	."<OPTION VALUE=liv ".$op_selected['liv'].">Livestock</OPTION>"
	."<OPTION VALUE=bldg ".$op_selected['bldg'].">Buildings</OPTION>"
	."</SELECT>"
	."</TD>";


// TRANSFER FROM TRIBE SELECTOR


echo "<TD><b>of</b></TD>"
	."<TD>";

// Hide selector if we have selected a new source tribe in a different hex
if ($from_tribe<>$last_from_tribe)
{
	echo "<b>$from_tribe</b>"
		."<INPUT TYPE=HIDDEN NAME=from_tribe VALUE=$from_tribe>";
}
else
{
	echo "<SELECT NAME=from_tribe>";

	$clanid = $_SESSION['clanid'];
	$tribes = $db->Execute("SELECT tribeid FROM $dbtables[tribes] "
				   ."WHERE clanid = '$clanid' "
				   ."ORDER BY tribeid ASC");
	while(!$tribes->EOF)
	{
		$tribe = $tribes->fields;
		echo "<OPTION VALUE=$tribe[tribeid]";
		if ($tribe['tribeid']==$from_tribe)
		{
			echo " SELECTED";
		}
		echo ">$tribe[tribeid]</OPTION>";
		$tribes->MoveNext();
	}
}

echo "</SELECT>"
	."</TD>";


// TRANSFER TO TRIBE SELECTOR

$in_same_hex = array();


// Force the 'to' selector to show the GT if we aon a GT transfer and it has not been set
if ( !ISSET($_REQUEST['to_tribe']) && $op=="tribe")
{
	$to_tribe = $goods_tribe;
}


// Get own tribes that can be transferred to
echo "<TD><b>to</b></TD>"
	."<TD>"
	."<SELECT NAME=to_tribe>";

$from_hex = $db->Execute("SELECT hex_id FROM $dbtables[tribes] "
			   ."WHERE tribeid = '$from_tribe' ");
$from_hex = $from_hex->fields;
$from_hex = $from_hex['hex_id'];

$hex_tribes = $db->Execute("SELECT tribeid FROM $dbtables[tribes] "
						   ."WHERE clanid = '$clanid' "
						   ."AND hex_id='$from_hex'"
						   ."ORDER BY tribeid ASC");
if (!$hex_tribes->EOF)
{
	echo "<OPTION VALUE=Clan>--- Clan ---";
}

while(!$hex_tribes->EOF)
{
	$tribe = $hex_tribes->fields;
	$in_same_hex[] = $tribe[tribeid];
	echo "<OPTION VALUE=$tribe[tribeid]";
/*
	if ($op=="tribe" && $tribe['tribeid']==$goods_tribe)
	{
		$to_tribe = $goods_tribe;
	}
*/
	if ($tribe['tribeid']==$to_tribe)
	{
		echo " SELECTED";
	}
	echo ">&nbsp;&nbsp;&nbsp;$tribe[tribeid]</OPTION>";
	$hex_tribes->MoveNext();
}

// Get allies that can be transferred to
if ($op<>"tribe")
{
	$allies = $db->Execute("SELECT offerer_id, receipt_id FROM $dbtables[alliances] "
						."WHERE accept = 'Y' "
						."AND (offerer_id = '$clanid' OR receipt_id = '$clanid')");
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

		$local_allies = $db->Execute("SELECT tribeid FROM $dbtables[tribes] "
								   ."WHERE clanid = '$ally' "
								   ."AND hex_id='$from_hex'"
								   ."ORDER BY tribeid ASC");
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
		echo "<OPTION VALUE=Allies>--- Allies ---</OPTION>";
		sort($allies_selectable);
		foreach ($allies_selectable as $ally)
		{
			echo "<OPTION VALUE=$ally";
			if ($ally==$to_tribe)
			{
				echo " SELECTED";
			}
			echo ">&nbsp;&nbsp;&nbsp;$ally</OPTION>";
		}
	}
//	echo "<OPTION VALUE=Dump>&gt; Dump &lt;</OPTION>";
}

echo "</SELECT>";

foreach ($in_same_hex as $ish)
{
	echo "<INPUT TYPE=HIDDEN name='in_same_hex[]' VALUE=$ish>";
}

echo "</TD>";


// SUBMIT BUTTON


if ($from_tribe<>$last_from_tribe)
{
	$doit="No";
	$submit="Set Target Tribe";
}
else
{
	$doit = "Yes";
	$submit = "Submit";
}


echo "<TD>"
	."<INPUT TYPE=HIDDEN NAME=last_from_tribe VALUE=".$from_tribe.">"
	."<INPUT TYPE=HIDDEN NAME=last_to_tribe VALUE=".$to_tribe.">"
	."&nbsp;<INPUT TYPE=SUBMIT NAME=submit VALUE=\"$submit\">"
	."</TD>"
	."</TR>"
	."<TR>"
	."<TD COLSPAN=7>";

if ($op=="tribe")
{
	echo "<I>To remove $from_tribe from a goods tribe relationship, set $from_tribe as the tribe you want to transfer to.</I>";
}

echo "&nbsp;<P></TD>"
	."</TR>"
	."</TABLE>";

$button_main = true;

	if (ISSET($_REQUEST['op']) && $doit=="Yes" && $valid_target)
	{
		$_REQUEST['unit']=$_SESSION['current_unit'];
		$_POST['unit']=$_SESSION['current_unit'];
		include("trans_".$op.".php");
	}
echo "</FORM>";

page_footer();

?> 
