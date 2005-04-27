<?
session_start();
header("Cache-control: private");
include("config.php");

page_header("Moving the tribe");

//Connect to the database
connectdb();


$dest = '';
$dest = $_GET[dest];
if(!isset($_SESSION[hex_id])){
	echo "You cannot access this page directly.\n<BR>";
	page_footer();
	}

if(!isset($dest))
{
	echo "There seems to be something wrong, here... you're going nowhere?.<BR>";
	page_footer();
}

$username = $_SESSION['username'];
$result = $db->Execute ("SELECT * FROM $dbtables[chiefs] WHERE username='$username'");
$playerinfo=$result->fields;
$clanid = $_SESSION['clanid'];
$clanresult = $db->Execute("SELECT * FROM $dbtables[clans] WHERE clanid = '$clanid'");
$claninfo = $clanresult->fields;
$currentunit = $_SESSION['current_unit'];
$triberesult = $db->Execute("SELECT * FROM $dbtables[tribes] WHERE tribeid = '$currentunit'");
$tribeinfo = $triberesult->fields;
$newhexid = $dest;
$hex = $db->Execute("SELECT * FROM $dbtables[hexes] WHERE hex_id = '$tribeinfo[hex_id]'");
$hexinfo = $hex->fields;

$neighbors = array($hexinfo[n],$hexinfo[ne],$hexinfo[e],$hexinfo[se],$hexinfo[s],$hexinfo[sw],$hexinfo[w],$hexinfo[nw]);

if($newhexid == $tribeinfo[hex_id]){
echo "You are already there.<BR>\n";
echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"2;URL=main.php\">";
}

//Check to see if the player has less than one move_pt available
//and if so return to the main menu
if ($tribeinfo[move_pts]<1)
{
	echo "You do not have any more movement points to go further. <BR><BR>";
	echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"2;URL=main.php\">";
	page_footer();
}

//Retrieve all the hex information about the current hex
$result2 = $db->Execute ("SELECT * FROM $dbtables[hexes] WHERE hex_id='$newhexid'");
//Put the hex information into the array "hexinfo"
$hexinfo=$result2->fields;

if($hexinfo[move] > $tribeinfo[move_pts])
{
  echo "You do not have enough move points to move further.<BR><BR>";
  echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"2;URL=main.php\">";
}
elseif($hexinfo[terrain] == 'o' | $hexinfo[terrain] == 'l') 
{
  echo "You cannot move onto water.<BR><BR>";
  $stamp = date("Y-m-d H:i:s");
  $tribeid = $tribeinfo[tribeid];
  $db->Execute("UPDATE $dbtables[mapping] SET `clanid_$tribeinfo[clanid]` = '1', `admin_0000` = '1' WHERE hex_id = '$dest'");
  echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"2;URL=main.php\">";;
}
elseif($tribeinfo[curweight] > $tribeinfo[maxweight])
{
  echo 'You are too encumbered to move!<BR><BR>';
  echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"2;URL=main.php\">";
}  
else
{
    if($hexinfo[terrain] == 'jh' | $hexinfo[terrain] == 'sw')
    {
        $wag = $db->Execute("SELECT * FROM $dbtables[products] "
                           ."WHERE long_name = 'wagon' "
                           ."AND tribeid = '$tribeinfo[tribeid]'");
        $wagon = $wag->fields;
        $ele = $db->Execute("SELECT * FROM $dbtables[livestock] "
                           ."WHERE type = 'Elephants' "
                           ."AND tribeid = '$tribeinfo[tribeid]'");
        $elephant = $ele->fields;
        if( $elephant[amount] < $wagon[amount] )
        {
            echo 'You cannot bring wagons through this terrain without being pulled by an elephant.<BR><BR>';
            echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"2;URL=main.php\">";
            page_footer();
        }
    }

$def = $db->Execute("SELECT * FROM $dbtables[tribes] WHERE hex_id = '$hexinfo[hex_id]' AND clanid <> '$_SESSION[clanid]'");
$table = 0;
$safe = 0;
while(!$def->EOF){
$defender = $def->fields;
$eld = $db->Execute("SELECT * FROM $dbtables[chiefs] WHERE active > 23 AND clanid = '$defender[clanid]'");
while(!$eld->EOF){
$eldinfo = $eld->fields;
$ally1 = $db->Execute("SELECT * FROM $dbtables[alliances] WHERE offerer_id = '$eldinfo[clanid]' AND receipt_id = '$_SESSION[clanid]' AND accept = 'Y' OR receipt_id = '$eldinfo[clanid]' AND offerer_id = '$_SESSION[clanid]' AND accept = 'Y'");
$ally1info = $ally1->fields;
if($ally1info[alliance_id]){
$safe = 1;
}
else{
if($table == 0){
echo "<CENTER>$ally1info[alliance_id]</CENTER><BR>";

	echo "<CENTER><TABLE BORDER=0 WIDTH=\"80%\"><TR BGCOLOR=$color_header><TD ALIGN=CENTER COLSPAN=2><FONT SIZE=+2>You have stumbled upon some unfriendlies!</FONT></TD></TR>";
	echo "<TR BGCOLOR=$color_line1><TD ALIGN=CENTER>Please select a target.</TD>";
	echo "<TD ALIGN=CENTER><FORM ACTION=combat.php METHOD=POST><SELECT NAME=target>";
	echo "<OPTION VALUE=cancel>Cancel</OPTION>";
	echo "<OPTION VALUE=$defender[tribeid]>$defender[tribeid]</OPTION>";
	$table++;
}
else{
	echo "<OPTION VALUE=$defender[tribeid]>$defender[tribeid]</OPTION>";
}
}
$eld->MoveNext();
}
$def->MoveNext();
}
if($table > 0){
	echo "</SELECT><INPUT TYPE=SUBMIT VALUE=Submit></FORM></TD></TR></TABLE></CENTER>";
        page_footer();
}



       echo "You move your tribe.<BR><BR>";
       $tribeid = $tribeinfo[tribeid];
       $newpts = $tribeinfo[move_pts] - $hexinfo[move];
       $db->Execute("UPDATE $dbtables[tribes] SET hex_id = '$hexinfo[hex_id]', move_pts = '$newpts' WHERE goods_tribe = '$tribeid' AND maxweight >= curweight");
       $db->Execute("UPDATE $dbtables[garrisons] SET hex_id = '$hexinfo[hex_id]' WHERE tribeid = '$tribeid'");
       $_SESSION['hex_id'] = $hexinfo[hex_id];
       $stamp = date("Y-m-d H:i:s");
       $minsk = $db->Execute("SELECT * FROM $dbtables[skills] "
                            ."WHERE tribeid = '$tribeid' "
                            ."AND abbr = 'min'");
       $min_skill = $minsk->fields;
       $cur_map = $db->Execute("SELECT * FROM $dbtables[mapping] "
                              ."WHERE hex_id = '$dest'");
       $curmap = $cur_map->fields;
       $prospect = rand( 0, 5 );
       $min_skill[level] += $prospect;
       $clanid = $tribeinfo[clanid];
       $restype = $db->Execute("SELECT * FROM $dbtables[gd_resources] "
                              ."WHERE name = '$hexinfo[res_type]'");
       $res = $restype->fields;
       if( $min_skill[level] < $hexinfo[prospect] || $hexinfo[res_type] == '' )
       { 
           if( $curmap[$clanid] < $res[res_code] )
           {
               $db->Execute("UPDATE $dbtables[mapping] SET `clanid_$tribeinfo[clanid]` = '1', `admin_0000` = '1' WHERE hex_id = '$dest'");
           }
       }
       else
       {
          $restype = $db->Execute("SELECT * FROM $dbtables[gd_resources] "
                                 ."WHERE name = '$hexinfo[res_type]'");
          $res = $restype->fields;
          if( $curmap[$clanid] <> $res[res_code] )
          {
              $db->Execute("UPDATE $dbtables[mapping] SET `clanid_$tribeinfo[clanid]` = '$res[res_code]', `admin_0000` = '$res[res_code]' WHERE hex_id = '$dest'");
          }
       }
       echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=main.php\">";;
      }

page_footer();
?>
