<?php
//session_start();
header("Cache-control: private");

$_SESSION['theme'] = $theme_default;  // This must be set on this page before the header is included

include("config.php");
include("game_time.php");

page_header("User Registration Submission");

connectdb();

if($account_creation_closed)
{
  echo "New accounts are disallowed right now.<BR>";
  page_footer();
}
//$character = preg_replace ("/[^\w\d\s]/","",$_POST['character']);
//$clanname = preg_replace ("/[^\w\d\s]/","",$_POST['clanname']);
//$username = preg_replace ("/[^\w\d\s]/","",$_POST['username']);
$character = str_replace(" ",'',$_POST['character']);
$clanname = str_replace(" ",'',$_POST['clanname']);
$username = str_replace(" ",'',$_POST['username']);
$startore = $_POST['startore'];
$email = str_replace(" ",'',$_POST['email']);
$email2 = $_POST['email2'];
$startitem1 = $_POST['startitem1'];

if( empty($username) || empty($character) || empty($email) || empty($email2) )
{
    echo "Email, Username, or Chiefname may not be left blank.<BR>";
    $flag=1;
}

if( !$email == $email2 )
{
    echo "Both email addresses must be the same. Please correct your email.<BR>";
    $flag=1;
}
if(trim(strtolower($character)) == 'chief')
{
   echo "Your Chiefname may not be 'Chief' , this is a reserved chief name";
}
$character=htmlspecialchars($character);
$clanname=htmlspecialchars($clanname);
$username=htmlspecialchars($username);

$sql = $db->Prepare("select email, username from $dbtables[chiefs] WHERE username=? OR email=? LIMIT 1");
$result = $db->Execute ($sql,array($username,$email));
db_op_result($result,__LINE__,__FILE__);
$flag=0;

if( $result )   //returns a boolean actually - true or false
{

    while( !$result->EOF )
    {
        $row = $result->fields;
        if( strtolower($row['email']) == strtolower($email) )
        {
            echo "E-mail address is already in use.  ";
            echo "If you have forgotten your password, please ";
            echo "contact the admins to have it reset for you.<BR>";
            $flag=1;
        }

        if( strtolower($row['chiefname']) == strtolower($character) )
        {
            echo "Chiefname is already in use!<BR>";
            $flag=1;
        }

        if( strtolower($row['clanname']) == strtolower($clanname) )
        {
            echo "Clan name is already in use!<BR>";
            $flag=1;
        }

        if( strtolower($row['username']) == strtolower($username) )
        {
            echo "Username is already in use!<br>";
            $flag=1;
        }
        $result->MoveNext();
    }
}
$pointsused = $_POST['armor'] + $_POST['bonework'] + $_POST['boning'] + $_POST['curing'] + $_POST['dressing'] + $_POST['fishing'];
$pointsused = $pointsused + $_POST['fletching'] + $_POST['forestry'] + $_POST['gutting'] + $_POST['herding'] + $_POST['hunting'];
$pointsused = $pointsused + $_POST['jewelery'] + $_POST['leather'] + $_POST['metalwork'] + $_POST['mining'] + $_POST['pottery'];
$pointsused = $pointsused + $_POST['quarry'] + $_POST['salting'] + $_POST['sewing'] + $_POST['siege'] + $_POST['skinning'] + $_POST['tanning'];
$pointsused = $pointsused + $_POST['waxworking'] + $_POST['weapons'] + $_POST['weaving'] + $_POST['whaling'] + $_POST['woodwork'];
$pointsused = $pointsused + $_POST['furrier'] + ($_POST['leadership'] * 3) + ($_POST['scouting'] * 3) + ($_POST['administration'] * 3 );
$pointsused = $pointsused + ($_POST['economics'] * 3 );
if( $pointsused > 50 )
{
    echo "You have allocated more than 50 points. ($pointsused) points used.<BR>";
    $flag=1;
}

if( $flag == 0 )
{
  /* insert code to add player to database */
    $makepass = '';
   $seed = mt_rand(0,time());
   $food = substr(md5($username.$character.$email),0,15);
   $makeup = md5($seed.$food);
   $makepass = substr($makeup,mt_rand(0,20),12);
    $hashed_pass = md5($makepass);
    $curr_hex = rand(1,4096);
    $safehex = 0;
    $safe = $db->Execute("SELECT safe,terrain FROM $dbtables[hexes] WHERE hex_id = '$curr_hex'");
    db_op_result($safe,__LINE__,__FILE__);
    while( $safehex < 1 )
    {
        $safeinfo = $safe->fields;
    if( $safeinfo['safe'] == 'N' )
        {
            $curr_hex = rand(1,4096);
            $safe = $db->Execute("SELECT safe,terrain FROM $dbtables[hexes] WHERE hex_id = '$curr_hex'");
            $safehex = 0;
        }
    else
        {
            $safehex++;
        }
    }
    $time = time();
    $insert_data = array("$username","$hashed_pass","$character","$email","$month[count]","$year[count]","$ip","$tribeid","$time");
    $sql = $db->Prepare("INSERT INTO $dbtables[chiefs] VALUES('',?,?,?,?,?,?,?,'1',?,'1','1','',?,'','1')");
     $result2 = $db->Execute($sql,$insert_data);
     db_op_result($result2,__LINE__,__FILE__);


        $sqlt = $db->Prepare("SELECT clanid FROM $dbtables[chiefs] WHERE username=?");
        $resultid = $db->Execute($sqlt,array($username));
        db_op_result($resultid,__LINE__,__FILE__);
        $clanid = $resultid->fields;
    $sqla = $db->Prepare("UPDATE $dbtables[chiefs] SET current_unit = clanid WHERE username = ?");
    $update1 = $db->Execute($sqla,array($username));
    db_op_result($update1,__LINE__,__FILE__);
    $logdata = array($month['count'],$year['count'],$clanid['clanid'],$clanid['clanid'],"NEWCHIEF",$stamp,"$clanid[clanid] has created a new clan from $ip $email");
    $logs = $db->Prepare("INSERT INTO $dbtables[logs] VALUES('',?,?,?,?,?,?,?)");
    $loginsert = $db->Execute($logs,$logdata);
    db_op_result($loginsert,__LINE__,__FILE__);

    adminlog('NEWCHIEF',"Newchief: $clanid[clanid] has created a new clan from $ip $email");


     //TODO: Port this into a mailer function using phpmailer class
        $l_new_message = "Greetings,\n\nSomeone from the IP address $ip \nrequested ";
        $l_new_message .= "that your password for TribeStrive be sent to you.\n\nYour ";
        $l_new_message .= "Username is: [user]\n\nYour password is: [pass]\n\nThank you\n\n";
        $l_new_message .= "The TribeStrive web team. \n\n\n";
        $l_new_message = str_replace("[pass]", $makepass, $l_new_message);
        $l_new_message = str_replace("[user]", $username, $l_new_message);
        $l_new_topic = "TribeStrive Password";
        $from = "From: $admin_mail\r\n";
        $replyto = "Reply-To: $admin_mail\r\n";
        $xmailer = "X-Mailer: PHP/";
       // mail("$email", "$l_new_topic", "$l_new_message\r\n\r\nhttp://$gamedomain","$from");
        mail("$email", "$l_new_topic", "$l_new_message\r\n\r\nhttp://$gamedomain","$from\r\n");
// Now, populate the rest of the tables needed for now...
        if( $startitem1 == 1 )
        {
            $traps = 1000;
            $swords = 0;
        }
        else
        {
            $swords = 100;
            $traps = 0;
        }

        if( $startore == 1 )
        {
            $bronze = 0;
            $iron = $iron + 1200;
        }
        else
        {
            $bronze = 1800;
            $iron = 0;
        }
        $coal =  round(mt_rand(900,2500));
        $bows =  round(mt_rand(50,250));
        $wagons = round(mt_rand(5,50));
        $jerkins = round(mt_rand(0,600));
        $provs = round(mt_rand(15000,22000));

        //// Give each new player a message that this is just a development server.
        $notice = $db->Execute("INSERT INTO $dbtables[messages] "
                    ."VALUES("
                    ."'',"
                    ."'0000',"
                    ."'$clanid[clanid]',"
                    ."'Welcome to TribeStrive Stable Server!',"
                    ."'$stamp',"
                    ."'Welcome to CrazyBri TribeStrive... The suggested TribeStrive Stable game server "
                    ."This game is still in monitoring- resets are expected to occur every 3 - 6 months "
                    ."Multiple Player accounts are disallowed unless cleared with an admin in the forums first."
                    .""
                    ."Thank you for Playing at <a href=http://www.crazybri.net/tribe2/>Crazybri TribeStrive</A>',"
                    ."'N')");
        db_op_result($notice,__LINE__,__FILE__);
        $insertarr = array($clanid['clanid'],$clanname);
        $sqls = $db->Prepare("INSERT INTO $dbtables[clans] VALUES(?,?,'None','1')");
        $final = $db->Execute($sqls,$insertarr);
    $tribeid = $clanid['clanid'];
        if( $slaver )
        {
            $slavehave = $slaves + rand(1,100);
            if( $slavehave > 85 )
            {
                $slavepop = $slaves + rand(1,100);
            }
        }
        else
        {
            $slavepop = 0;
        }
        $startpopmin = ($pointsleft * 2) + 7000;
        $startpopmax = ($pointsleft * 5) + 9000;
    $activepop = rand($startpopmin, $startpopmax);
        $activepop = $activepop * .65;
    $warpop = 500;
    $inactivepop = $activepop * .35;
    $totalpop = $activepop + $warpop + $inactivepop + $slavepop;
    $maxam = $activepop + $slavepop;
    $curam = $activepop + $slavepop;
    $morale = '1.0';
        $horse = round(mt_rand(100,700));
    $elephant = round(mt_rand(10,200));
    $goat = round(mt_rand(5000,7000));
    $cattle = round(mt_rand(100,400));
    $maxweight = ($active * 30)+($horse * 150)+($elephant * 250);
    $goodstribe = $tribeid;
    $newtribe_array = array($clanid['clanid'],"$tribeid","$null","Y","$totalpop","$warpop","$activepop","$inactivepop","$slavepop","0","$maxam","$curam","$morale","$maxweight","$curr_hex","$goodstribe");

    $newtribesql = $db->Prepare("INSERT INTO $dbtables[tribes] VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,'0',?,'','','','18',?)");
    $query = $db->Execute($newtribesql,$newtribe_array);
    db_op_result($query,__LINE__,__FILE__);
        $_SESSION['hex_id'] = $curr_hex;
    $gar_arr = array($curr_hex,$clanid['clanid'],$tribeid,$safeinfo['terrain']);
    $gar_sql = $db->Prepare("INSERT INTO $dbtables[garrisons] VALUES ('',?,?,?,'500','1.00',?,'1','0','Bone Spear',"
                    ."'','','Jerkin','','','','','I')");
 $query = $db->Execute($gar_sql,$gar_arr);
    db_op_result($query,__LINE__,__FILE__);
/////////////////////////////////////////////begin productions//////////////////////////////////////////

        $products = $db->Execute("SELECT * FROM $dbtables[product_table] WHERE include = 'Y'");
        db_op_result($products,__LINE__,__FILE___);
        while( !$products->EOF )
        {
            $productinfo = $products->fields;
            $ins_arr = array($tribeid,$productinfo['proper'],$productinfo['long_name'],$productinfo['weapon'],$productinfo['armor']);
            $ins_sql = $db->Prepare("INSERT INTO $dbtables[products] VALUES(?,?,?,'0',?,?,'')");
            $query = $db->Execute($ins_sql,$ins_arr);
            db_op_result($query,__LINE__,__FILE__);
            $products->MoveNext();
        }
$query = $db->Execute("UPDATE $dbtables[products] SET amount = 0 WHERE long_name = 'totem' and tribeid = '$tribeid'");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[products] SET amount = '$traps' WHERE long_name = 'traps' AND tribeid = '$tribeid'");
   db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[products] SET amount = '$swords' WHERE long_name = 'ironsword' AND tribeid = '$tribeid'");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[products] SET amount = '$bows' WHERE long_name = 'bow' AND tribeid = '$tribeid'");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[products] SET amount = '$wagons' WHERE long_name = 'wagon' AND tribeid = '$tribeid'");
   db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[products] SET amount = '$jerkins' WHERE long_name = 'jerkin' AND tribeid = '$tribeid'");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("UPDATE $dbtables[products] SET amount = '$provs' WHERE long_name = 'provs' AND tribeid = '$tribeid'");
  db_op_result($query,__LINE__,__FILE__);
////////////////////////////////////////////////end productions//////////////////////////////////////////////
//////////////////////////////////////////////begin resources////////////////////////////////////////////////

$query = $db->Execute("INSERT INTO $dbtables[resources] VALUES('$tribeid','Bronze','$bronze','bronze')");
    db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[resources] VALUES('$tribeid','Iron','$iron','iron')");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[resources] VALUES('$tribeid','Coal','$coal','coal')");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[resources] VALUES('$tribeid','Copper','$copper','copper')");
   db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[resources] VALUES('$tribeid','Zinc','$zinc','zinc')");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[resources] VALUES('$tribeid','Tin','$tin','tin')");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[resources] VALUES('$tribeid','Silver','$silver','silver')");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[resources] VALUES('$tribeid','Brass','$brass','brass')");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[resources] VALUES('$tribeid','Lead','$lead','lead')");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[resources] VALUES('$tribeid','Salt','$salt','salt')");
   db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[resources] VALUES('$tribeid','Stones','$stones','stones')");
   db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[resources] VALUES('$tribeid','Gold','$gold','gold')");
    db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[resources] VALUES('$tribeid','Steel','$steel','steel')");
     db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[resources] VALUES('$tribeid','Steel_1','$steel_1','steel_1')");
    db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[resources] VALUES('$tribeid','Steel_2','$steel_2','steel_2')");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[resources] VALUES('$tribeid','Coke','$coke','coke')");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[resources] VALUES('$tribeid','Gems','$gems','gems')");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[resources] VALUES('$tribeid','Iron Ore','$iron_ore','iron.ore')");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[resources] VALUES('$tribeid','Copper Ore','$copper_ore','copper.ore')");
   db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[resources] VALUES('$tribeid','Zinc Ore','$zinc_ore','zinc.ore')");
   db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[resources] VALUES('$tribeid','Tin Ore','$tin_ore','tin.ore')");
    db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[resources] VALUES('$tribeid','Silver Ore','$silver_ore','silver.ore')");
    db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[resources] VALUES('$tribeid','Lead Ore','$lead_ore','lead.ore')");
    db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[resources] VALUES('$tribeid','Gold Ore','$gold_ore','gold.ore')");
    db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[resources] VALUES('$tribeid','Raw Gems','$raw_gems','raw.gems')");
    db_op_result($query,__LINE__,__FILE__);
///////////////////////////////////////////////end resources////////////////////////////////////////////////
///////////////////////////////////////////////begin livestock/////////////////////////////////////////////
$query = $db->Execute("INSERT INTO $dbtables[livestock] VALUES('','$tribeid','Cattle','$cattle')");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[livestock] VALUES('','$tribeid','Horses','$horse')");
   db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[livestock] VALUES('','$tribeid','Elephants','$elephant')");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[livestock] VALUES('','$tribeid','Goats','$goat')");
   db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[livestock] VALUES('','$tribeid','Sheep','$sheep')");
   db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[livestock] VALUES('','$tribeid','Pigs','$pigs')");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[livestock] VALUES('','$tribeid','Dogs','$dogs')");
 db_op_result($query,__LINE__,__FILE__);
////////////////////////////////////////////////end livestock////////////////////////////////////////////////

/////////////////////////////////////////////////begin skills///////////////////////////////////////////////
//Problem here: if they submit raw post data from elsewhere, they can break this - even with $_GET if post is empty
//this assumes too much and requires register_globals = on
$skill_arr = array($tribeid,(int)$_POST['armor']);
$skill_insert = $db->Prepare("INSERT INTO $dbtables[skills] VALUES('','arm','Armor','a',?,?,'')");
$query = $db->Execute($skill_insert,$skill_arr);
 db_op_result($query,__LINE__,__FILE__);
$skill_arr = array($tribeid,(int)$_POST['bonework']);
$skill_insert = $db->Prepare("INSERT INTO $dbtables[skills] VALUES('','bnw','Bonework','a',?,?,'')");
$query = $db->Execute($skill_insert,$skill_arr);
 db_op_result($query,__LINE__,__FILE__);
$skill_arr = array($tribeid,(int)$_POST['boning']);
$skill_insert = $db->Prepare("INSERT INTO $dbtables[skills] VALUES('','bon','Boning','a',?,?,'')");
$query = $db->Execute($skill_insert,$skill_arr);
 db_op_result($query,__LINE__,__FILE__);
$skill_arr = array($tribeid,(int)$_POST['curing']);
$skill_insert = $db->Prepare("INSERT INTO $dbtables[skills] VALUES('','cur','Curing','a',?,?,'')");
$query = $db->Execute($skill_insert,$skill_arr);
 db_op_result($query,__LINE__,__FILE__);
$skill_arr = array($tribeid,(int)$_POST['dressing']);
$skill_insert = $db->Prepare("INSERT INTO $dbtables[skills] VALUES('','dre','Dressing','a',?,?,'')");
$query = $db->Execute($skill_insert,$skill_arr);
 db_op_result($query,__LINE__,__FILE__);
$skill_arr = array($tribeid,(int)$_POST['fishing']);
$skill_insert = $db->Prepare("INSERT INTO $dbtables[skills] VALUES('','fish','Fishing','a',?,?,'')");
$query = $db->Execute($skill_insert,$skill_arr);
 db_op_result($query,__LINE__,__FILE__);
$skill_arr = array($tribeid,(int)$_POST['fletching']);
$skill_insert = $db->Prepare("INSERT INTO $dbtables[skills] VALUES('','flet','Fletching','a',?,?,'')");
$query = $db->Execute($skill_insert,$skill_arr);
 db_op_result($query,__LINE__,__FILE__);
$skill_arr = array($tribeid,(int)$_POST['forestry']);
$skill_insert = $db->Prepare("INSERT INTO $dbtables[skills] VALUES('','for','Forestry','a',?,?,'')");
$query = $db->Execute($skill_insert,$skill_arr);
 db_op_result($query,__LINE__,__FILE__);
$skill_arr = array($tribeid,(int)$_POST['furrier']);
$skill_insert = $db->Prepare("INSERT INTO $dbtables[skills] VALUES('','fur','Furrier','a',?,?,'')");
$query = $db->Execute($skill_insert,$skill_arr);
 db_op_result($query,__LINE__,__FILE__);
$skill_arr = array($tribeid,(int)$_POST['gutting']);
$skill_insert = $db->Prepare("INSERT INTO $dbtables[skills] VALUES('','gut','Gutting','a',?,?,'')");
$query = $db->Execute($skill_insert,$skill_arr);
 db_op_result($query,__LINE__,__FILE__);
$skill_arr = array($tribeid,(int)$_POST['herding']);
$skill_insert = $db->Prepare("INSERT INTO $dbtables[skills] VALUES('','herd','Herding','a',?,?,'')");
$query = $db->Execute($skill_insert,$skill_arr);
 db_op_result($query,__LINE__,__FILE__);
$skill_arr = array($tribeid,(int)$_POST['hunting']);
$skill_insert = $db->Prepare("INSERT INTO $dbtables[skills] VALUES('','hunt','Hunting','a',?,?,'')");
$query = $db->Execute($skill_insert,$skill_arr);
 db_op_result($query,__LINE__,__FILE__);
$skill_arr = array($tribeid,(int)$_POST['jewelry']);
$skill_insert = $db->Prepare("INSERT INTO $dbtables[skills] VALUES('','jew','Jewelry','a',?,?,'')");
$query = $db->Execute($skill_insert,$skill_arr);
 db_op_result($query,__LINE__,__FILE__);
$skill_arr = array($tribeid,(int)$_POST['leather']);
$skill_insert = $db->Prepare("INSERT INTO $dbtables[skills] VALUES('','ltr','Leatherwork','a',?,?,'')");
$query = $db->Execute($skill_insert,$skill_arr);
 db_op_result($query,__LINE__,__FILE__);
$skill_arr = array($tribeid,(int)$_POST['metalwork']);
$skill_insert = $db->Prepare("INSERT INTO $dbtables[skills] VALUES('','mtl','Metalwork','a',?,?,'')");
$query = $db->Execute($skill_insert,$skill_arr);
 db_op_result($query,__LINE__,__FILE__);
$skill_arr = array($tribeid,(int)$_POST['mining']);
$skill_insert = $db->Prepare("INSERT INTO $dbtables[skills] VALUES('','min','Mining','a',?,?,'')");
$query = $db->Execute($skill_insert,$skill_arr);
 db_op_result($query,__LINE__,__FILE__);
$skill_arr = array($tribeid,(int)$_POST['pottery']);
$skill_insert = $db->Prepare("INSERT INTO $dbtables[skills] VALUES('','pot','Pottery','a',?,?,'')");
$query = $db->Execute($skill_insert,$skill_arr);
 db_op_result($query,__LINE__,__FILE__);
$skill_arr = array($tribeid,(int)$_POST['quarry']);
$skill_insert = $db->Prepare("INSERT INTO $dbtables[skills] VALUES('','qry','Quarrying','a',?,?,'')");
$query = $db->Execute($skill_insert,$skill_arr);
 db_op_result($query,__LINE__,__FILE__);
$skill_arr = array($tribeid,(int)$_POST['salting']);
$skill_insert = $db->Prepare("INSERT INTO $dbtables[skills] VALUES('','salt','Salting','a',?,?,'')");
$query = $db->Execute($skill_insert,$skill_arr);
 db_op_result($query,__LINE__,__FILE__);
$skill_arr = array($tribeid,(int)$_POST['sewing']);
$skill_insert = $db->Prepare("INSERT INTO $dbtables[skills] VALUES('','sew','Sewing','a',?,?,'')");
$query = $db->Execute($skill_insert,$skill_arr);
 db_op_result($query,__LINE__,__FILE__);
$skill_arr = array($tribeid,(int)$_POST['siege']);
$skill_insert = $db->Prepare("INSERT INTO $dbtables[skills] VALUES('','seq','Siege Equipment','a',?,?,'')");
$query = $db->Execute($skill_insert,$skill_arr);
 db_op_result($query,__LINE__,__FILE__);
$skill_arr = array($tribeid,(int)$_POST['skinning']);
$skill_insert = $db->Prepare("INSERT INTO $dbtables[skills] VALUES('','skn','Skinning','a',?,?,'')");
$query = $db->Execute($skill_insert,$skill_arr);
 db_op_result($query,__LINE__,__FILE__);
$skill_arr = array($tribeid,(int)$_POST['tanning']);
$skill_insert = $db->Prepare("INSERT INTO $dbtables[skills] VALUES('','tan','Tanning','a',?,?,'')");
$query = $db->Execute($skill_insert,$skill_arr);
 db_op_result($query,__LINE__,__FILE__);
$skill_arr = array($tribeid,(int)$_POST['waxworking']);
$skill_insert = $db->Prepare("INSERT INTO $dbtables[skills] VALUES('','wax','Waxworking','a',?,?,'')");
$query = $db->Execute($skill_insert,$skill_arr);
 db_op_result($query,__LINE__,__FILE__);
$skill_arr = array($tribeid,(int)$_POST['weapons']);
$skill_insert = $db->Prepare("INSERT INTO $dbtables[skills] VALUES('','wpn','Weapons','a',?,?,'')");
$query = $db->Execute($skill_insert,$skill_arr);
 db_op_result($query,__LINE__,__FILE__);
$skill_arr = array($tribeid,(int)$_POST['weaving']);
$skill_insert = $db->Prepare("INSERT INTO $dbtables[skills] VALUES('','wv','Weaving','a',?,?,'')");
$query = $db->Execute($skill_insert,$skill_arr);
 db_op_result($query,__LINE__,__FILE__);
$skill_arr = array($tribeid,(int)$_POST['whaling']);
$skill_insert = $db->Prepare("INSERT INTO $dbtables[skills] VALUES('','wha','Whaling','a',?,?,'')");
$query = $db->Execute($skill_insert,$skill_arr);
 db_op_result($query,__LINE__,__FILE__);
$skill_arr = array($tribeid,(int)$_POST['woodwork']);
$skill_insert = $db->Prepare("INSERT INTO $dbtables[skills] VALUES('','wd','Woodwork','a',?,?,'')");
$query = $db->Execute($skill_insert,$skill_arr);
 db_op_result($query,__LINE__,__FILE__);
$skill_arr = array($tribeid,(int)$_POST['leadership']);
$skill_insert = $db->Prepare("INSERT INTO $dbtables[skills] VALUES('','ldr','Leadership','b',?,?,'')");
$query = $db->Execute($skill_insert,$skill_arr);
 db_op_result($query,__LINE__,__FILE__);
$skill_arr = array($tribeid,(int)$_POST['scouting']);
$skill_insert = $db->Prepare("INSERT INTO $dbtables[skills] VALUES('','sct','Scouting','b',?,?,'')");
$query = $db->Execute($skill_insert,$skill_arr);
 db_op_result($query,__LINE__,__FILE__);
$skill_arr = array($tribeid,(int)$_POST['administration']);
$skill_insert = $db->Prepare("INSERT INTO $dbtables[skills] VALUES('','adm','Administration','b',?,?,'')");
$query = $db->Execute($skill_insert,$skill_arr);
 db_op_result($query,__LINE__,__FILE__);
$skill_arr = array($tribeid,(int)$_POST['economics']);
$skill_insert = $db->Prepare("INSERT INTO $dbtables[skills] VALUES('','eco','Economics','b',?,?,'')");
$query = $db->Execute($skill_insert,$skill_arr);
 db_op_result($query,__LINE__,__FILE__);
 //These are teh selected skills as posted.. the remaining values below are not even included in teh selection options
 //is it necessary to insert them?

$skill_arr = array($tribeid,0);
$skill_insert = $db->Prepare("INSERT INTO $dbtables[skills] VALUES('','arc','Archery','b',?,?,'')");
$query = $db->Execute($skill_insert,$skill_arr);
 db_op_result($query,__LINE__,__FILE__);
$skill_arr = array($tribeid,0);
$skill_insert = $db->Prepare("INSERT INTO $dbtables[skills] VALUES('','ath','Atheism','b',?,?,'')");
$query = $db->Execute($skill_insert,$skill_arr);
 db_op_result($query,__LINE__,__FILE__);
$skill_arr = array($tribeid,0);
$skill_insert = $db->Prepare("INSERT INTO $dbtables[skills] VALUES('','capt','Captaincy','b',?,?,'')");
$query = $db->Execute($skill_insert,$skill_arr);
 db_op_result($query,__LINE__,__FILE__);
$skill_arr = array($tribeid,0);
$skill_insert = $db->Prepare("INSERT INTO $dbtables[skills] VALUES('','char','Chariotry','b',?,?,'')");
$query = $db->Execute($skill_insert,$skill_arr);
 db_op_result($query,__LINE__,__FILE__);
$skill_arr = array($tribeid,0);
$skill_insert = $db->Prepare("INSERT INTO $dbtables[skills] VALUES('','com','Combat','b',?,?,'')");
$query = $db->Execute($skill_insert,$skill_arr);
 db_op_result($query,__LINE__,__FILE__);
$skill_arr = array($tribeid,0);
$skill_insert = $db->Prepare("INSERT INTO $dbtables[skills] VALUES('','dip','Diplomacy','b',?,?,'')");
$query = $db->Execute($skill_insert,$skill_arr);
 db_op_result($query,__LINE__,__FILE__);
$skill_arr = array($tribeid,0);
$skill_insert = $db->Prepare("INSERT INTO $dbtables[skills] VALUES('','capt','Captaincy','b',?,?,'')");
$query = $db->Execute($skill_insert,$skill_arr);
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[skills] VALUES('','heal','Healing','b','$tribeid',0,'')");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[skills] VALUES('','hvyw','Heavy Weapons','b','$tribeid',0,'')");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[skills] VALUES('','hor','Horsemanship','b','$tribeid',0,'')");
   db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[skills] VALUES('','mar','Mariner','b','$tribeid',0,'')");
   db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[skills] VALUES('','nav','Navigation','b','$tribeid',0,'')");
   db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[skills] VALUES('','pol','Politics','b','$tribeid',0,'')");
   db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[skills] VALUES('','rel','Religion','b','$tribeid','0','')");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[skills] VALUES('','row','Rowing','b','$tribeid','0','')");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[skills] VALUES('','sail','Sailing','b','$tribeid','0','')");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[skills] VALUES('','sea','Seamanship','b','$tribeid','0','')");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[skills] VALUES('','sec','Security','b','$tribeid','0','')");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[skills] VALUES('','shw','Shipwright','b','$tribeid','0','')");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[skills] VALUES('','slv','Slavery','b','$tribeid','0','')");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[skills] VALUES('','spy','Spying','b','$tribeid','0','')");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[skills] VALUES('','tac','Tactics','b','$tribeid','0','')");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[skills] VALUES('','tor','Torture','b','$tribeid','0','')");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[skills] VALUES('','tri','Triball','b','$tribeid','0','')");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[skills] VALUES('','alc','Alchemy','c','$tribeid','0','')");
   db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[skills] VALUES('','api','Apiarism','c','$tribeid','0','')");
   db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[skills] VALUES('','art','Art','c','$tribeid','0','')");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[skills] VALUES('','astr','Astronomy','c','$tribeid','0','')");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[skills] VALUES('','bak','Baking','c','$tribeid','0','')");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[skills] VALUES('','blub','Blubberwork','c','$tribeid','0','')");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[skills] VALUES('','brk','Brick Making','c','$tribeid','0','')");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[skills] VALUES('','cook','Cooking','c','$tribeid','0','')");
   db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[skills] VALUES('','dan','Dance','c','$tribeid','0','')");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[skills] VALUES('','dis','Distilling','c','$tribeid','0','')");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[skills] VALUES('','eng','Engineering','c','$tribeid','0','')");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[skills] VALUES('','farm','Farming','c','$tribeid','0','')");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[skills] VALUES('','flen','Flensing','c','$tribeid','0','')");
   db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[skills] VALUES('','lit','Literacy','c','$tribeid','0','')");
  db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[skills] VALUES('','mtnb','Maintain Boats','c','$tribeid','0','')");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[skills] VALUES('','mil','Milling','c','$tribeid','0','')");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[skills] VALUES('','mus','Music','c','$tribeid','0','')");
db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[skills] VALUES('','peel','Peeling','c','$tribeid','0','')");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[skills] VALUES('','ref','Refining','c','$tribeid','0','')");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[skills] VALUES('','res','Research','c','$tribeid','0','')");
db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[skills] VALUES('','san','Sanitation','c','$tribeid','0','')");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[skills] VALUES('','seek','Seeking','c','$tribeid','0','')");
db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[skills] VALUES('','shb','Shipbuilding','c','$tribeid','0','')");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[skills] VALUES('','stn','Stonework','c','$tribeid','0','')");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[skills] VALUES('','glss','Glasswork','c','$tribeid','0','')");
 db_op_result($query,__LINE__,__FILE__);
$query = $db->Execute("INSERT INTO $dbtables[skills] VALUES('','fctl','Fire Control','c','$tribeid','0','')");
 db_op_result($query,__LINE__,__FILE__);
///////////////////////////////////////////////////////end skills///////////////////////////////////////////////
///////////////////////////////////////////////////////begin mapping////////////////////////////////////////////
$query = $db->Execute("ALTER TABLE $dbtables[mapping] ADD clanid_$tribeid smallint(2) DEFAULT '0' NOT NULL");
  db_op_result($query,__LINE__,__FILE__);
$query = $query = $db->Execute("UPDATE $dbtables[mapping] SET clanid_$tribeid = '1' WHERE hex_id = '$curr_hex'");
db_op_result($query,__LINE__,__FILE__);
include("weight.php");
///////////////////////////////////////////////////////end mapping//////////////////////////////////////////////

    if($display_password)
    {
       echo "Your password is " . $makepass . "<BR><BR>";
    }
    echo "Password has been sent to $username.<BR><BR><BR>";

}

if ($flag==0 && $link_forums==$game_url.$game_url_path."forums/")
{
echo  "<FORM METHOD=POST ACTION=$link_forums/profile.php TARGET=_blank>"
    ."<INPUT TYPE=HIDDEN NAME=mode VALUE=register>"
    ."<INPUT TYPE=HIDDEN NAME=agreed VALUE=true>"
    ."<INPUT TYPE=HIDDEN NAME=coppa VALUE=0>"
    ."<INPUT TYPE=HIDDEN NAME=username VALUE=$username>"
    ."<INPUT TYPE=HIDDEN NAME=new_password VALUE=$makepass>"
    ."<INPUT TYPE=HIDDEN NAME=password_confirm VALUE=$makepass>"
    ."<INPUT TYPE=HIDDEN NAME=email VALUE=$email>"
    ."<INPUT TYPE=HIDDEN NAME=submit VALUE=Submit>"
    ."<INPUT TYPE=SUBMIT VALUE=\"Register Automatically on Forum\">"
    ."</FORM>"
    ."<P><B>Note:</B> The forum registration page will pop up and close again."
    ."<BR>It may do that so quickly that you do not see its content."
    ."<BR>However, your forum login should be created automatically"
    ."<BR>with the same <i>Name</i> and <i>Password</i> as your Tribe login."
    ."<P>You will be sent emails confirming the creation of both your Tribe"
    ."<BR>Strive account and, if your click the button above, your Tribe"
    ."<BR>Strive Forum account."
    ."<P>Trying to get automatic forum registration is a new feature."
    ."<BR>If it does not work, please register manually using the Forum link"
    ."<BR>and let us know that there is a problem.";
}

echo "<P>Click <A HREF=index.php>here</A> to go to the login screen.";

page_footer();

?>
