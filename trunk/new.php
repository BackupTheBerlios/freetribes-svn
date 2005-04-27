<?
include("config.php");
page_header("Clan Creation");
navbar_open();
navbar_link("heraldry.php", "", "Who's On?");
navbar_link("index.php", "", "Login");
navbar_link("help.php", "", "Help");
navbar_link("webcal", "", "Web Calendar");
navbar_link("tickets", "", "Bug Reporting");
navbar_link($link_forums, "", "Forums");
navbar_close();

?>
<P>
<form action="new2.php" method="post" name="newClanForm" onSubmit="doSubmit();">
 <center>
  <table  width="" border="0" cellspacing="0" cellpadding="4">
   <tr onmouseover="return overlib('Select what username you will use to log in with. This may not be changed later.');" onmouseout="nd();">
    <TD>Username</td>
    <TD><input class=edit_area type="text" name="username" size="35" maxlength="35" value=""></td>
    <TD>&nbsp;</TD>
   </tr>
   <tr onmouseover="return overlib('Select what your people will be known as to others. This may be changed later.');" onmouseout="nd();">
    <TD>Clan Name</td>
    <TD><input class=edit_area type="text" name="clanname" size="35" maxlength="35" value=""></td>
    <TD>&nbsp;</TD>
   </tr>
   <tr onmouseover="return overlib('Select what you will be known as to others. This may not be changed later.');" onmouseout="nd();">
    <td>Chief Name</td>
    <td>
    <input class=edit_area type="text" name="character" size="35" maxlength="35" value=""></td>
    <TD>&nbsp;</TD>
   </tr>
   <tr onmouseover="return overlib('This needs to be a valid email address so that we may send your password and any password changes to you for your records. This may not be changed later.');" onmouseout="nd();">
   <td>Email Address</td>
   <td>
   <input class=edit_area type="text" name="email" size="35" maxlength="35" value=""></td>
   <TD>&nbsp;</TD>
   </tr>
   <TR onmouseover="return overlib('This needs to be a valid email address so that we may send your password and any password changes to you for your records. This may not be changed later.');" onmouseout="nd();">
   <TD>Confirm Email</TD>
   <TD>
   <INPUT class=edit_area TYPE="text" name="email2" size="35" maxlength="35" value=""></TD>
   <TD>&nbsp;</TD>
   </TR>
   </TABLE>
   <TABLE BORDER="0" CELLSPACING="0" CELLPADDING="4">

   <tr align=center>
    <td>
     <table border="0" cellspacing="0" cellpadding="4" bgcolor=#0f6e3f onmouseover="return overlib('This is a choice between a small amount of iron \(which can be used to make weapons and armor as well as tools\) and a smaller amount of bronze \(which can be used to make mostly tools only\).');" onmouseout="nd();">
      <tr>
       <td>1200 Iron or,</td>
       <td><input type="radio" name="startore" value="1"></td>
      </tr>
      <tr>
       <td>1800 Bronze</td>
       <td><input type="radio" name="startore" value="2" checked></td>
      </tr>
     </table>
    </td>
    <td>
     <table border="0" cellspacing="0" cellpadding="4" bgcolor=#0f6e3f onmouseover="return overlib('This is a choice between relatively peaceful traps for gathering food, or a small number of swords for those of you planning to be aggressive right away.');" onmouseout="nd();">
      <tr>
       <td>
       <FONT>1000 Traps, or</td>
       <td>
      <input type="radio" name="startitem1" value="1" checked></td>
      </tr>
      <tr>
       <td>100 Swords</td>
       <td><input type="radio" name="startitem1" value="2"></td></tr></table> </td>
       <td>
	    <table border ="0" cellspacing="0" cellpadding="4" bgcolor=#0f6e3f onmouseover="return overlib('Check this box if you wish to have a chance at starting out with a random number of slaves. Leave it unchecked if you wish to run a clan which does not utilize slaves \(including prisoners of war\) for production.');" onmouseout="nd();">
         <tr>
          <td COLSPAN=2>
          Slaver</td>
          <td>
          <input type="checkbox" name="slaver" value="1"></td>
         </tr>
         <tr>
		  <td ALIGN=CENTER COLSPAN=2>&nbsp;</td>
		 </tr>
        </table>
	   </td>
      <tr>
	   <!-- GR - Added <td colspan="3"> below.  It was missing and probably throwing errors in some browsers.  -->
	   <td colspan="3">
	   <!-- GR End -->
        <table border="0" cellspacing="0" cellpadding="4" bgcolor=#0f6e3f onmouseover="return overlib('You have up to 50 \'points\' to spend on skills. How you spend these points depends greatly on your style of play and your desired goals. Skills are broken into three \'groups\', of which you are able to choose any of the group \'A\' skills for a single point for each level of skill. Group \'B\' and \'C\' skills are only selectively available, and at an increased price of 3 points for each level of skill. Any unspent points will be allocated towards your tribe\'s starting inventory so do not be anxious if you cannot figure out how to spend the last few points.');"
                  onmouseout="nd();">
         <tr>
		  <td colspan=8 align=center><font color=brown>1 Point per level</font></td>
		 </tr>
		 <tr>
		  <!-- GR - Added onChange="calcPoints();" to the <select> tags below.  -->
          <td>Armor</td>
		  <td><select NAME=armor onChange="calcPoints();"><option VALUE="0" SELECTED>0</option><option VALUE="1">1</option><option VALUE="2">2</option><option VALUE="3">3</option><option VALUE="4">4</option><option VALUE="5">5</option><option VALUE="6">6</option><option VALUE="7">7</option></select></td>
          <td>Bonework</td>
		  <td><select NAME=bonework onChange="calcPoints();"><option VALUE="0" SELECTED>0</option><option VALUE="1">1</option><option VALUE="2">2</option><option VALUE="3">3</OPTION><option VALUE="4">4</option><option VALUE="5">5</option><option VALUE="6">6</option><option VALUE="7">7</option></select></td>
          <td>Boning</td>
		  <td><select NAME=boning onChange="calcPoints();"><option VALUE="0" SELECTED>0</option><option VALUE="1">1</option><option VALUE="2">2</option><option VALUE="3">3</OPTION><option VALUE="4">4</option><option VALUE="5">5</option><option VALUE="6">6</option><option VALUE="7">7</option></select></td>
          <td>Curing</td>
		  <td><select NAME=curing onChange="calcPoints();"><option VALUE="0" SELECTED>0</option><option VALUE="1">1</option><option VALUE="2">2</option><option VALUE="3">3</OPTION><option VALUE="4">4</option><option VALUE="5">5</option><option VALUE="6">6</option><option VALUE="7">7</option></select></td>
         </tr>
         <tr>
   		  <td>Dressing</td>
		  <td><select NAME=dressing onChange="calcPoints();"><option VALUE="0" SELECTED>0</option><option VALUE="1">1</option><option VALUE="2">2</option><option VALUE="3">3</OPTION><option VALUE="4">4</option><option VALUE="5">5</option><option VALUE="6">6</option><option VALUE="7">7</option></select></td>
          <td>Fishing</td>
		  <td><select NAME=fishing onChange="calcPoints();"><option VALUE="0" SELECTED>0</option><option VALUE="1">1</option><option VALUE="2">2</option><option VALUE="3">3</OPTION><option VALUE="4">4</option><option VALUE="5">5</option><option VALUE="6">6</option><option VALUE="7">7</option></select></td>
          <td>Fletching</td>
		  <td><select NAME=fletching onChange="calcPoints();"><option VALUE="0" SELECTED>0</option><option VALUE="1">1</option><option VALUE="2">2</option><option VALUE="3">3</OPTION><option VALUE="4">4</option><option VALUE="5">5</option><option VALUE="6">6</option><option VALUE="7">7</option></select></td>
          <td>Forestry</td>
		  <td><select NAME=forestry onChange="calcPoints();"><option VALUE="0" SELECTED>0</option><option VALUE="1">1</option><option VALUE="2">2</option><option VALUE="3">3</OPTION><option VALUE="4">4</option><option VALUE="5">5</option><option VALUE="6">6</option><option VALUE="7">7</option></select></td>
         </tr>
         <tr>
          <td>Gutting</td>
		  <td><select NAME=gutting onChange="calcPoints();"><option VALUE="0" SELECTED>0</option><option VALUE="1">1</option><option VALUE="2">2</option><option VALUE="3">3</OPTION><option VALUE="4">4</option><option VALUE="5">5</option><option VALUE="6">6</option><option VALUE="7">7</option></select></td>
          <td>Herd</td>
		  <td><select NAME=herding onChange="calcPoints();"><option VALUE="0" SELECTED>0</option><option VALUE="1">1</option><option VALUE="2">2</option><option VALUE="3">3</OPTION><option VALUE="4">4</option><option VALUE="5">5</option><option VALUE="6">6</option><option VALUE="7">7</option></select></td>
          <td>Hunt</td>
		  <td><select NAME=hunting onChange="calcPoints();"><option VALUE="0" SELECTED>0</option><option VALUE="1">1</option><option VALUE="2">2</option><option VALUE="3">3</OPTION><option VALUE="4">4</option><option VALUE="5">5</option><option VALUE="6">6</option><option VALUE="7">7</option></select></td>
          <td>Jewelry</td>
		  <td><select NAME=jewelery onChange="calcPoints();"><option VALUE="0" SELECTED>0</option><option VALUE="1">1</option><option VALUE="2">2</option><option VALUE="3">3</OPTION><option VALUE="4">4</option><option VALUE="5">5</option><option VALUE="6">6</option><option VALUE="7">7</option></select></td>
         </tr>
         <tr>
          <td>Leatherwork</td>
		  <td><select NAME=leather onChange="calcPoints();"><option VALUE="0" SELECTED>0</option><option VALUE="1">1</option><option VALUE="2">2</option><option VALUE="3">3</OPTION><option VALUE="4">4</option><option VALUE="5">5</option><option VALUE="6">6</option><option VALUE="7">7</option></select></td>
          <td>Metalwork</td>
		  <td><select NAME=metalwork onChange="calcPoints();"><option VALUE="0" SELECTED>0</option><option VALUE="1">1</option><option VALUE="2">2</option><option VALUE="3">3</OPTION><option VALUE="4">4</option><option VALUE="5">5</option><option VALUE="6">6</option><option VALUE="7">7</option></select></td>
          <td>Mining</td>
		  <td><select NAME=mining onChange="calcPoints();"><option VALUE="0" SELECTED>0</option><option VALUE="1">1</option><option VALUE="2">2</option><option VALUE="3">3</OPTION><option VALUE="4">4</option><option VALUE="5">5</option><option VALUE="6">6</option><option VALUE="7">7</option></select></td>
          <td>Pottery</td>
		  <td><select NAME=pottery onChange="calcPoints();"><option VALUE="0" SELECTED>0</option><option VALUE="1">1</option><option VALUE="2">2</option><option VALUE="3">3</OPTION><option VALUE="4">4</option><option VALUE="5">5</option><option VALUE="6">6</option><option VALUE="7">7</option></select></td>
         </tr>
         <tr>
          <td>Quarrying</td>
		  <td><select NAME=quarry onChange="calcPoints();"><option VALUE="0" SELECTED>0</option><option VALUE="1">1</option><option VALUE="2">2</option><option VALUE="3">3</OPTION><option VALUE="4">4</option><option VALUE="5">5</option><option VALUE="6">6</option><option VALUE="7">7</option></select></td>
          <td>Salting</td>
		  <td><select NAME=salting onChange="calcPoints();"><option VALUE="0" SELECTED>0</option><option VALUE="1">1</option><option VALUE="2">2</option><option VALUE="3">3</OPTION><option VALUE="4">4</option><option VALUE="5">5</option><option VALUE="6">6</option><option VALUE="7">7</option></select></td>
          <td>Sewing</td>
		  <td><select NAME=sewing onChange="calcPoints();"><option VALUE="0" SELECTED>0</option><option VALUE="1">1</option><option VALUE="2">2</option><option VALUE="3">3</OPTION><option VALUE="4">4</option><option VALUE="5">5</option><option VALUE="6">6</option><option VALUE="7">7</option></select></td>
          <td>Siege Equipment</td>
		  <td><select NAME=siege onChange="calcPoints();"><option VALUE="0" SELECTED>0</option><option VALUE="1">1</option><option VALUE="2">2</option><option VALUE="3">3</OPTION><option VALUE="4">4</option><option VALUE="5">5</option><option VALUE="6">6</option><option VALUE="7">7</option></select></td>
		 </tr>
         <tr>
          <td>Skinning</td>
		  <td><select NAME=skinning onChange="calcPoints();"><option VALUE="0" SELECTED>0</option><option VALUE="1">1</option><option VALUE="2">2</option><option VALUE="3">3</OPTION><option VALUE="4">4</option><option VALUE="5">5</option><option VALUE="6">6</option><option VALUE="7">7</option></select></td>
          <td>Tanning</td>
		  <td><select NAME=tanning onChange="calcPoints();"><option VALUE="0" SELECTED>0</option><option VALUE="1">1</option><option VALUE="2">2</option><option VALUE="3">3</OPTION><option VALUE="4">4</option><option VALUE="5">5</option><option VALUE="6">6</option><option VALUE="7">7</option></select></td>
          <td>Waxwork</td>
		  <td><select NAME=waxworking onChange="calcPoints();"><option VALUE="0" SELECTED>0</option><option VALUE="1">1</option><option VALUE="2">2</option><option VALUE="3">3</OPTION><option VALUE="4">4</option><option VALUE="5">5</option><option VALUE="6">6</option><option VALUE="7">7</option></select></td>
          <td>Weapons</td>
		  <td><select NAME=weapons onChange="calcPoints();"><option VALUE="0" SELECTED>0</option><option VALUE="1">1</option><option VALUE="2">2</option><option VALUE="3">3</OPTION><option VALUE="4">4</option><option VALUE="5">5</option><option VALUE="6">6</option><option VALUE="7">7</option></select></td>
         </tr>
         <tr>
          <td>Weaving</td>
		  <td><select NAME=weaving onChange="calcPoints();"><option VALUE="0" SELECTED>0</option><option VALUE="1">1</option><option VALUE="2">2</option><option VALUE="3">3</OPTION><option VALUE="4">4</option><option VALUE="5">5</option><option VALUE="6">6</option><option VALUE="7">7</option></select></td>
          <td>Whaling</td>
		  <td><select NAME=whaling onChange="calcPoints();"><option VALUE="0" SELECTED>0</option><option VALUE="1">1</option><option VALUE="2">2</option><option VALUE="3">3</OPTION><option VALUE="4">4</option><option VALUE="5">5</option><option VALUE="6">6</option><option VALUE="7">7</option></select></td>
          <td>Woodwork</td>
		  <td><select NAME=woodwork onChange="calcPoints();"><option VALUE="0" SELECTED>0</option><option VALUE="1">1</option><option VALUE="2">2</option><option VALUE="3">3</OPTION><option VALUE="4">4</option><option VALUE="5">5</option><option VALUE="6">6</option><option VALUE="7">7</option></select></td>
          <td>Furrier</td>
		  <td><select NAME=furrier onChange="calcPoints();"><option VALUE="0" SELECTED>0</option><option VALUE="1">1</option><option VALUE="2">2</option><option VALUE="3">3</option><option VALUE="4">4</option><option VALUE="5">5</option><option VALUE="6">6</option><option VALUE="7">7</option></select></td>
         </tr>
		 <tr>
		 </tr>
		 <tr>
		  <td colspan=8 align=center><font color=brown>3 Points per level</font></td>
		 </tr>
		 <tr>
          <td>Leadership</td>
		  <td><select NAME=leadership onChange="calcPoints();"><option VALUE="0" SELECTED>0</option><option VALUE="1">1</option><option VALUE="2">2</option><option VALUE="3">3</OPTION><option VALUE="4">4</option><option VALUE="5">5</option></select></td>
          <td>Scouting</td>
		  <td><select NAME=scouting onChange="calcPoints();"><option VALUE="0" SELECTED>0</option><option VALUE="1">1</option><option VALUE="2">2</option><option VALUE="3">3</OPTION><option VALUE="4">4</option><option VALUE="5">5</option></select></td>
          <td>Administration</td>
		  <td><select NAME=administration onChange="calcPoints();"><option VALUE="0" SELECTED>0</option><option VALUE="1">1</option><option VALUE="2">2</option><option VALUE="3">3</OPTION><option VALUE="4">4</option><option VALUE="5">5</option></select></td>
          <td>Economics</td>
		  <td><select NAME=economics onChange="calcPoints();"><option VALUE="0" SELECTED>0</option><option VALUE="1">1</option><option VALUE="2">2</option><option VALUE="3">3</option><option VALUE="4">4</option><option VALUE="5">5</option></select></td>
         </tr>
		 <tr>
          <td colspan=8 align=center>You have 50 points to spend.</td> 
         </tr>
		 <!-- GR - Added table row to show points used -->
		 <tr>
		  <td colspan="8" align="center">
                  Points you have used:
                  <input type="text" class=form_input  name="points" disabled size="2"  value="0"> 
                  </td>
		 </tr>
		 <!-- GR END -->
        </table>
       </td>
      </tr>
     </table>
	 
  <br>
<?
$unique = uniqid(microtime(),1);
echo "<INPUT TYPE=HIDDEN NAME=UNIQUE VALUE=\"$unique\">";
?>
  <input type="submit" value="Submit">
  <INPUT type="reset" value="Reset">

  <br><br>We don't use your e-mail address except to send you your server generated password.<br>
 </center>
</form>
<?
include("footer.php");
?>
