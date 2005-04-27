<?
session_start();
header("Cache-control: private");
include("config.php");
include ("gui/navbar.php");
$time_start = getmicrotime();

page_header("Chiefs of the World");
navbar_open();
if( !$_SESSION[clanid] )
{
    navbar_link("index.php", "", "Login");
}
else
{
    navbar_link("main.php", "", "Overview");
}
navbar_link("new.php", "", "Create Clan");
navbar_link("help_map_editor.php", "", "Map Edit Help");
navbar_link("help.php", "", "Help");
navbar_link("help_faq.php", "", "FAQ");
navbar_link("help_maps.php", "", "Map Info");
navbar_link($link_forums, "", "Forums");
navbar_close();


connectdb();

//-------------------------------------------------------------------------------------------------

$res = $db->Execute("SELECT COUNT(*) AS num_players FROM $dbtables[chiefs]");
$row = $res->fields;
$num_players = $row['num_players'];

$adminres = $db->Execute("SELECT COUNT(*) as num_admins FROM $dbtables[chiefs] WHERE admin > 1 AND clanid > 0000");
$admins = $adminres->fields;
$num_admins = $admins[num_admins];
$adminres = $db->Execute("SELECT * FROM $dbtables[chiefs] WHERE admin > 1 ORDER BY admin DESC");

if($_REQUEST[sort] == ''){
$res = $db->Execute("SELECT * from $dbtables[chiefs] WHERE admin < 2 ORDER BY score DESC");
}
else{
    if( $_REQUEST[sort] == 'lastseen' )
    {
        $res = $db->Execute("SELECT * FROM $dbtables[chiefs] WHERE admin < 2 ORDER BY lastseen_year DESC, lastseen_month DESC");
    }
    else
    {
        $res = $db->Execute("SELECT * FROM $dbtables[chiefs] WHERE admin < 2 ORDER BY $_REQUEST[sort] DESC");
    }
}

//-------------------------------------------------------------------------------------------------


if(!$res)
{
  echo "None<BR>";
}
else
{
  if( $num_admins > 1 )
  {
     $num_admin_text = "Admins";
  }
  else
  {
     $num_admin_text = "Admin";
  }
  echo "<BR><FONT SIZE=+1>" . NUMBER($num_players) . " Chiefs known in the realm, $num_admins $num_admin_text.</FONT> <BR>";
  if(ISSET($_SESSION[clanid])){
  echo "<A HREF=mailto.php>Send a Message</A>";
  }
  echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=2 WIDTH=\"100%\">";
  $color = $color_line1;
    if($color == $color_line1){
     $color = $color_line2;
     }
     else{
     $color = $color_line1;
     }
  echo "<TR BGCOLOR=$color_header><TD COLSPAN=10 ALIGN=CENTER>Administrators</TD></TR>";
    if($color == $color_line1){
     $color = $color_line2;
     }
     else{
     $color = $color_line1;
     }
  if($adminres->EOF){
  echo "<TR BGCOLOR=$color><TD COLSPAN=9 ALIGN=CENTER> None</TD></TR>";
   }
  else{
  while (!$adminres->EOF){
  $adminrow = $adminres->fields;
   $a++;
    $clans = $db->Execute("SELECT * FROM $dbtables[clans] WHERE clanid = '$adminrow[clanid]'");
    $claninfo = $clans->fields;
    $time = "$adminrow[lastseen_month] / $adminrow[lastseen_year]";
    $cur = $db->Execute("SELECT count FROM $dbtables[game_date] WHERE type = 'day'");
    $curtime = $cur->fields;
    $lasttime = $adminrow[active];
    $gm = $db->Execute("SELECT * FROM $dbtables[game_date] WHERE type = 'month'");
    $gy = $db->Execute("SELECT * FROM $dbtables[game_date] WHERE type = 'year'");
    $month = $gm->fields;
    $year = $gy->fields;
    $curdate = "$month[count] / $year[count]";
    $online = " ";
    $now = time();
    $onlinenow = $now - $adminrow[hour];
    $onlineminutes = floor($onlinenow/60);
    if($adminrow[admin] > 1 && $adminrow[admin] < 10 ){ $admintype = "Builder"; }
    if($adminrow[admin] > 9 && $adminrow[admin] < 50 ){ $admintype = "JrAdmin"; }
    if($adminrow[admin] > 49 && $adminrow[admin] < 99 ){ $admintype = "Admin"; }
    if($adminrow[admin] > 98){$admintype = "Developer";}
    if($onlineminutes < 10 ){
    $online = "Y";
    }
    echo "<TR BGCOLOR=$color ALIGN=CENTER><TD>" . NUMBER($a) . "</TD><TD>$claninfo[clanid]</TD><TD>";
    echo "&nbsp;";
    echo "&nbsp;";
    echo "<b>$adminrow[chiefname]</B></TD><TD>&nbsp;</TD><TD>$claninfo[clanname]</TD><TD>$time</TD><TD>&nbsp;&nbsp;</TD><TD>$claninfo[religion]</TD><TD>$online</TD><TD>$admintype</TD></TR>";
    if($color == $color_line1){
     $color = $color_line2;
     }
     else{
     $color = $color_line1;
     }
     $adminres->MoveNext();
     }
     }
  echo "<TR><TD COLSPAN=9 ALIGN=CENTER>&nbsp</TD></TR>";

  echo "<TR BGCOLOR=\"$color_header\" ALIGN=CENTER><TD><FONT COLOR=BLACK><B>Listing:</B></FONT></TD><TD><B><A HREF=heraldry.php?sort=clanid>Clan ID</A>:</B></TD><TD><B><A HREF=heraldry.php?sort=chiefname>Chief</A>:</B></TD><TD><FONT COLOR=BLACK><B>Battles</B></FONT></TD><TD><FONT COLOR=BLACK><B>Clan Name:</B></FONT></TD><TD><B><A HREF=heraldry.php?sort=lastseen>Last Seen</A>:</B></TD><TD>&nbsp;</TD><TD><FONT COLOR=BLACK><B>Religion</B></FONT></TD><TD><FONT COLOR=BLACK><B>Online</B></FONT></TD><TD><B><A HREF=heraldry.php?sort=score>Prestige</A>:</B></TD></TR>\n";

     
  while(!$res->EOF)
  {

    $row = $res->fields;
    $i++;
    $clans = $db->Execute("SELECT * FROM $dbtables[clans] WHERE clanid = '$row[clanid]'");
    $claninfo = $clans->fields;
    $bat = $db->Execute("SELECT DISTINCT(combat_id), tribeid FROM $dbtables[combats] "
                       ."WHERE tribeid LIKE '$row[clanid]%'");
    $battles = $bat->_numOfRows;
    $time = "$row[lastseen_month] / $row[lastseen_year]";
    $cur = $db->Execute("SELECT count FROM $dbtables[game_date] WHERE type = 'day'");
    $curtime = $cur->fields;
    $lasttime = $row[active];
    $gm = $db->Execute("SELECT * FROM $dbtables[game_date] WHERE type = 'month'");
    $gy = $db->Execute("SELECT * FROM $dbtables[game_date] WHERE type = 'year'");
    $month = $gm->fields;
    $year = $gy->fields;
    $curdate = "$month[count] / $year[count]";
    $online = " ";
    $now = time();
    $onlinenow = $now - $row[hour];
    $onlineminutes = floor($onlinenow/60);
    if($onlineminutes < 10){  
    $online = "Y";
    }
    if($i == 11){
    echo "<TR><TD COLSPAN=9>&nbsp;</TD></TR>";
    }
    echo "<TR BGCOLOR=\"$color\" ALIGN=CENTER><TD>" . NUMBER($i) . "</TD><TD>$claninfo[clanid]</TD><TD>";
    echo "&nbsp;";
    echo "&nbsp;";
    echo "<b>$row[chiefname]</b></TD>";
    echo "<TD>($battles)</TD>";
    echo "<TD>$claninfo[clanname]</TD><TD>$time</TD><TD>&nbsp;&nbsp;</TD><TD>$claninfo[religion]</TD><TD>$online</TD><TD>$row[score]</TD></TR>\n";
    if($color == $color_line1)
    {
      $color = $color_line2;
    }
    else
    {
      $color = $color_line1;
    }
    $res->MoveNext();
  }
  echo "</TABLE>";
}

echo "<FONT SIZE=+1>Current Date is $curdate</FONT><P>";

page_footer();
?>
