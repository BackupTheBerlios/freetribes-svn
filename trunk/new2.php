<?
session_start();
header("Cache-control: private");

$_SESSION['theme'] = $theme_default;  // This must be set on this page before the header is included

include("config.php");
include("game_time.php");

page_header("User Registration Submission");

connectdb();

$submit = $db->Execute("SELECT * FROM $dbtables[form_submits] "
                      ."WHERE formid = '$_REQUEST[UNIQUE]'");
$submitinfo = $submit->_numOfRows;
if( $submitinfo > 0 )
{
    navbar_open();
    navbar_link("heraldry.php", "", "Who's On?");
    navbar_link("new.php", "", "Create Clan");
    navbar_link("help.php", "", "Help");
    navbar_link("webcal", "", "Web Calendar");
    navbar_link("tickets", "", "Bug Reporting");
    navbar_link($link_forums, "", "Forums");
    navbar_close();
    echo "You have already submitted this information. This error will now be logged.<BR>";
    $db->Execute("INSERT INTO $dbtables[logs] "
                ."VALUES("
                ."'',"
                ."'$month[count]',"
                ."'$year[count]',"
                ."'0000',"
                ."'0000.00',"
                ."'BUGABUSE',"
                ."'$stamp',"
                ."'BUGABUSE: $ip has attempted to use the back button to resubmit the create clan form ($email).')");
    page_footer();
    die();
}
else
{
    $db->Execute("INSERT INTO $dbtables[form_submits] "
                ."VALUES("
                ."'$_REQUEST[UNIQUE]')");
}


if($account_creation_closed)
{
  echo "New accounts are disallowed right now.<BR>";
  page_footer();
}
//$character = preg_replace ("/[^\w\d\s]/","",$_POST['character']);
//$clanname = preg_replace ("/[^\w\d\s]/","",$_POST['clanname']);
//$username = preg_replace ("/[^\w\d\s]/","",$_POST['username']);
$character = $_POST['character'];
$clanname = $_POST['clanname'];
$username = $_POST['username'];
$character=htmlspecialchars($character);
$clanname=htmlspecialchars($clanname);
$password=htmlspecialchars($password);
$startore = $_POST['startore'];
$email = $_POST['email'];
$email2 = $_POST['email2'];


if( !get_magic_quotes_gpc() )
{
    $username = addslashes( $username );
    $character = addslashes($character);
    $clanname = addslashes($clanname);
}

$result = $db->Execute ("select email, username from $dbtables[users] "
                       ."WHERE username='$username' "
                       ."OR email='$email'");
$flag=0;
if( $username=='' | $character=='' | $email=='' | $email2 == '' ) 
{ 
    echo "Email, Username, or Chiefname may not be left blank.<BR>"; 
    $flag=1;
}
if( !$email == $email2 )
{
    echo "Both email addresses must be the same. Please correct your email.<BR>";
    $flag=1;
}


if( $result > 0 )
{
    while( !$result->EOF )
    {
        $row = $result->fields;
        if( strtolower($row[email]) == strtolower($email) ) 
        { 
            echo "E-mail address is already in use.  ";
            echo "If you have forgotten your password, please ";
            echo "contact the admins to have it reset for you.<BR>"; 
            $flag=1;
        }

        if( strtolower($row[chiefname]) == strtolower($character) ) 
        { 
            echo "Chiefname is already in use!<BR>"; 
            $flag=1;
        }

		if( strtolower($row[clanname]) == strtolower($clanname) ) 
        { 
            echo "Clan name is already in use!<BR>"; 
            $flag=1;
        }

		if( strtolower($row[username]) == strtolower($username) ) 
        { 
            echo "Username is already in use!<br>"; 
            $flag=1;
        }
        $result->MoveNext();
    }
}
$pointsused = $_REQUEST[armor] + $_REQUEST[bonework] + $_REQUEST[boning] + $_REQUEST[curing] + $_REQUEST[dressing] + $_REQUEST[fishing];
$pointsused = $pointsused + $_REQEUST[fletching] + $_REQUEST[forestry] + $_REQUEST[gutting] + $_REQUEST[herding] + $_REQUEST[hunting];
$pointsused = $pointsused + $_REQUEST[jewelery] + $_REQUEST[leather] + $_REQUEST[metalwork] + $_REQUEST[mining] + $_REQUEST[pottery];
$pointsused = $pointsused + $_REQUEST[quarry] + $_REQUEST[salting] + $_REQUEST[sewing] + $_REQUEST[siege] + $_REQUEST[skinning] + $_REQUEST[tanning];
$pointsused = $pointsused + $_REQUEST[waxworking] + $_REQUEST[weapons] + $_REQUEST[weaving] + $_REQUEST[whaling] + $_REQUEST[woodwork];
$pointsused = $pointsused + $_REQUEST[furrier] + ($_REQUEST[leadership] * 3) + ($_REQUEST[scouting] * 3) + ($_REQUEST[administration] * 3 );
$pointsused = $pointsused + ($_REQUEST[economics] * 3 );
if( $pointsused > 50 )
{
    echo "You have allocated more than 50 points. ($pointsused) points used.<BR>";
	$flag=1;
}

if( $flag == 0 )
{
  /* insert code to add player to database */
    $makepass = '';
    $syllables = 'er,in,tia,wol,fe,pre,vet,jo,nes,al,len,son,';
    $syllables .= 'cha,ir,ler,bo,ok,tio,nar,sim,ple,bla,ten,toe,';
    $syllables .= 'cho,co,lat,spe,ak,er,po,co,lor,pen,cil,li,ght,';
    $syllables .= 'wh,at,the,he,ck,is,mam,bo,no,fi,ve,any,way,pol,';
    $syllables .= 'iti,cs,ra,dio,sou,rce,sea,rch,pa,per,com,bo,sp,';
    $syllables .= 'eak,st,fi,rst,gr,oup,boy,ea,gle,tr,ail,bi,ble,';
    $syllables .= 'brb,pri,dee,kay,en,be,se';
    $syllable_array=explode(",", $syllables);
    srand( ( double ) microtime() * 1000000 );
    for( $count = 1 ; $count <= 4 ; $count++ ) 
    {
        if( rand()%10 == 1 ) 
        {
            $makepass .= sprintf( "%0.0f", ( rand()%50 ) +1 );
	} 
	else 
        {
            $makepass .= sprintf( "%s", $syllable_array[rand()%62] );
	}

    }
    $hashed_pass = md5($makepass);
    $curr_hex = rand(1,4096);
    $safehex = 0;
    $safe = $db->Execute("SELECT * FROM $dbtables[hexes] "
                        ."WHERE hex_id = '$curr_hex'");
    while( $safehex < 1 )
    {
        $safeinfo = $safe->fields;
	if( $safeinfo[safe] == 'N' )
        { 
            $curr_hex = rand(1,4096);
            $safe = $db->Execute("SELECT * FROM $dbtables[hexes] "
                                ."WHERE hex_id = '$curr_hex'");
            $safehex = 0;
        }
	else
        {
	    $safehex++;
	}
    }
    $time = time();
    $result2 = $db->Execute("INSERT INTO $dbtables[chiefs] "
                           ."VALUES("
                           ."'',"
                           ."'$username',"
                           ."'$hashed_pass',"
                           ."'$character',"
                           ."'$email',"
                           ."'$month[count]',"
                           ."'$year[count]',"
                           ."'$ip',"
                           ."'1',"
                           ."'$tribeid',"
                           ."'1',"
                           ."'1',"
                           ."'',"
                           ."'$time',"
			   ."'',"
                           ."'1')");
    if( !$result2 ) 
    {
        echo $db->ErrorMsg() . "<br>";
    } 
    else 
    {
        $resultid = $db->Execute("SELECT clanid FROM $dbtables[chiefs] "
                                ."WHERE username='$username'");
        $clanid = $resultid->fields;
	$db->Execute("UPDATE $dbtables[chiefs] "
                    ."SET current_unit = clanid "
                    ."WHERE username = '$username'");
	$db->Execute("INSERT INTO $dbtables[logs] "
                    ."VALUES("
                    ."'',"
                    ."'$month[count]',"
                    ."'$year[count]',"
                    ."'$clanid[clanid]',"
                    ."'$clanid[clanid]',"
                    ."'NEWCHIEF',"
                    ."'$stamp',"
                    ."'$clanid[clanid] has created a new clan from $ip $email')");
        $db->Execute("INSERT INTO $dbtables[logs] "
                    ."VALUES("
                    ."'',"
                    ."'$month[count]',"
                    ."'$year[count]',"
                    ."'0000',"
                    ."'0000.00',"
                    ."'NEWCHIEF',"
                    ."'$stamp',"
                    ."'Newchief: $clanid[clanid] has created a new clan from $ip ($email).')");

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
        mail("$email", "$l_new_topic", "$l_new_message\r\n\r\nhttp://$gamedomain","$from$replyto$xmailer" . phpversion());

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
        $coal =  $coal + rand(900,2500);
        $bows =  $bows + rand(50,250);
        $wagons = rand(5,50);
        $jerkins = $jerkins + rand(0,600);
        $provs = rand(15000,22000);

        //// Give each new player a message that this is just a development server.
        $db->Execute("INSERT INTO $dbtables[messages] "
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

        $db->Execute("INSERT INTO $dbtables[clans] "
                    ."VALUES("
                    ."'$clanid[clanid]',"
                    ."'$clanname',"
                    ."'None',"
                    ."'1')");
	$tribeid = $clanid[clanid];
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
        $horse = $horse + rand(100,700);
	$elephant = rand(10,200);
	$goat = rand(5000,7000);
	$cattle = rand(100,400);
	$maxweight = ($active * 30)+($horse * 150)+($elephant * 250);
	$goodstribe = $tribeid;
	$db->Execute("INSERT INTO $dbtables[tribes] "
                    ."VALUES("
                    ."'$clanid[clanid]',"
                    ."'$tribeid',"
                    ."'',"
                    ."'Y',"
                    ."'$totalpop',"
                    ."'$warpop',"
                    ."'$activepop',"
                    ."'$inactivepop',"
                    ."'$slavepop',"
                    ."'0',"
                    ."'$maxam',"
                    ."'$curam',"
                    ."'$morale',"
                    ."'$maxweight',"
                    ."'0',"
                    ."'$curr_hex',"
                    ."'',"
                    ."'',"
                    ."'',"
                    ."'18',"
                    ."'$goodstribe')");

        $_SESSION['hex_id'] = $curr_hex;
        $db->Execute("INSERT INTO $dbtables[garrisons] "
                    ."VALUES("
                    ."'',"
                    ."'$curr_hex',"
                    ."'$clanid[clanid]',"
                    ."'$tribeid',"
                    ."'500',"
                    ."'1.00',"
                    ."'$safeinfo[terrain]',"
                    ."'1',"
                    ."'0',"
                    ."'Bone Spear',"
                    ."'',"
                    ."'',"
                    ."'Jerkin',"
                    ."'',"
                    ."'',"
                    ."'',"
                    ."'',"
                    ."'I')");

/////////////////////////////////////////////begin productions//////////////////////////////////////////

        $products = $db->Execute("SELECT * FROM $dbtables[product_table] "
                                ."WHERE include = 'Y'");

        while( !$products->EOF )
        {
            $productinfo = $products->fields;
            $db->Execute("INSERT INTO $dbtables[products] "
                        ."VALUES("
                        ."'$tribeid',"
                        ."'$productinfo[proper]',"
                        ."'$productinfo[long_name]',"
                        ."'0',"
                        ."'$productinfo[weapon]',"
                        ."'$productinfo[armor]')");
            $products->MoveNext();
        }
$db->Execute("UPDATE $dbtables[products] SET amount = amount -1 WHERE long_name = 'totem' and tribeid = '$tribeid'");
$db->Execute("UPDATE $dbtables[products] SET amount = '$traps' WHERE long_name = 'traps' AND tribeid = '$tribeid'");
$db->Execute("UPDATE $dbtables[products] SET amount = '$swords' WHERE long_name = 'ironsword' AND tribeid = '$tribeid'");
$db->Execute("UPDATE $dbtables[products] SET amount = '$bows' WHERE long_name = 'bow' AND tribeid = '$tribeid'");
$db->Execute("UPDATE $dbtables[products] SET amount = '$wagons' WHERE long_name = 'wagon' AND tribeid = '$tribeid'");
$db->Execute("UPDATE $dbtables[products] SET amount = '$jerkins' WHERE long_name = 'jerkin' AND tribeid = '$tribeid'");
$db->Execute("UPDATE $dbtables[products] SET amount = '$provs' WHERE long_name = 'provs' AND tribeid = '$tribeid'");
////////////////////////////////////////////////end productions//////////////////////////////////////////////
//////////////////////////////////////////////begin resources////////////////////////////////////////////////
$db->Execute("INSERT INTO $dbtables[resources] VALUES('$tribeid','Bronze','$bronze','bronze')");
$db->Execute("INSERT INTO $dbtables[resources] VALUES('$tribeid','Iron','$iron','iron')");
$db->Execute("INSERT INTO $dbtables[resources] VALUES('$tribeid','Coal','$coal','coal')");
$db->Execute("INSERT INTO $dbtables[resources] VALUES('$tribeid','Copper','$copper','copper')");
$db->Execute("INSERT INTO $dbtables[resources] VALUES('$tribeid','Zinc','$zinc','zinc')");
$db->Execute("INSERT INTO $dbtables[resources] VALUES('$tribeid','Tin','$tin','tin')");
$db->Execute("INSERT INTO $dbtables[resources] VALUES('$tribeid','Silver','$silver','silver')");
$db->Execute("INSERT INTO $dbtables[resources] VALUES('$tribeid','Brass','$brass','brass')");
$db->Execute("INSERT INTO $dbtables[resources] VALUES('$tribeid','Lead','$lead','lead')");
$db->Execute("INSERT INTO $dbtables[resources] VALUES('$tribeid','Salt','$salt','salt')");
$db->Execute("INSERT INTO $dbtables[resources] VALUES('$tribeid','Stones','$stones','stones')");
$db->Execute("INSERT INTO $dbtables[resources] VALUES('$tribeid','Gold','$gold','gold')");
$db->Execute("INSERT INTO $dbtables[resources] VALUES('$tribeid','Steel','$steel','steel')");
$db->Execute("INSERT INTO $dbtables[resources] VALUES('$tribeid','Steel_1','$steel_1','steel_1')");
$db->Execute("INSERT INTO $dbtables[resources] VALUES('$tribeid','Steel_2','$steel_2','steel_2')");
$db->Execute("INSERT INTO $dbtables[resources] VALUES('$tribeid','Coke','$coke','coke')");
$db->Execute("INSERT INTO $dbtables[resources] VALUES('$tribeid','Gems','$gems','gems')");
$db->Execute("INSERT INTO $dbtables[resources] VALUES('$tribeid','Iron Ore','$iron_ore','iron.ore')");
$db->Execute("INSERT INTO $dbtables[resources] VALUES('$tribeid','Copper Ore','$copper_ore','copper.ore')");
$db->Execute("INSERT INTO $dbtables[resources] VALUES('$tribeid','Zinc Ore','$zinc_ore','zinc.ore')");
$db->Execute("INSERT INTO $dbtables[resources] VALUES('$tribeid','Tin Ore','$tin_ore','tin.ore')");
$db->Execute("INSERT INTO $dbtables[resources] VALUES('$tribeid','Silver Ore','$silver_ore','silver.ore')");
$db->Execute("INSERT INTO $dbtables[resources] VALUES('$tribeid','Lead Ore','$lead_ore','lead.ore')");
$db->Execute("INSERT INTO $dbtables[resources] VALUES('$tribeid','Gold Ore','$gold_ore','gold.ore')");
$db->Execute("INSERT INTO $dbtables[resources] VALUES('$tribeid','Raw Gems','$raw_gems','raw.gems')");
///////////////////////////////////////////////end resources////////////////////////////////////////////////
///////////////////////////////////////////////begin livestock/////////////////////////////////////////////
$db->Execute("INSERT INTO $dbtables[livestock] VALUES('','$tribeid','Cattle','$cattle')");
$db->Execute("INSERT INTO $dbtables[livestock] VALUES('','$tribeid','Horses','$horse')");
$db->Execute("INSERT INTO $dbtables[livestock] VALUES('','$tribeid','Elephants','$elephant')");
$db->Execute("INSERT INTO $dbtables[livestock] VALUES('','$tribeid','Goats','$goat')");
$db->Execute("INSERT INTO $dbtables[livestock] VALUES('','$tribeid','Sheep','$sheep')");
$db->Execute("INSERT INTO $dbtables[livestock] VALUES('','$tribeid','Pigs','$pigs')");
$db->Execute("INSERT INTO $dbtables[livestock] VALUES('','$tribeid','Dogs','$dogs')");
////////////////////////////////////////////////end livestock////////////////////////////////////////////////
/////////////////////////////////////////////////begin skills///////////////////////////////////////////////
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','arm','Armor','a','$tribeid','$armor','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','bnw','Bonework','a','$tribeid','$bonework','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','bon','Boning','a','$tribeid','$boning','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','cur','Curing','a','$tribeid','$curing','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','dre','Dressing','a','$tribeid','$dressing','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','fish','Fishing','a','$tribeid','$fishing','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','flet','Fletching','a','$tribeid','$fletching','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','for','Forestry','a','$tribeid','$forestry','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','fur','Furrier','a','$tribeid','$furrier','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','gut','Gutting','a','$tribeid','$gutting','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','herd','Herding','a','$tribeid','$herding','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','hunt','Hunting','a','$tribeid','$hunting','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','jew','Jewelry','a','$tribeid','$jewelry','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','ltr','Leatherwork','a','$tribeid','$leather','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','mtl','Metalwork','a','$tribeid','$metalwork','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','min','Mining','a','$tribeid','$mining','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','pot','Pottery','a','$tribeid','$pottery','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','qry','Quarrying','a','$tribeid','$quarry','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','salt','Salting','a','$tribeid','$salting','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','sew','Sewing','a','$tribeid','$sewing','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','seq','Siege Equipment','a','$tribeid','$siege','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','skn','Skinning','a','$tribeid','$skinning','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','tan','Tanning','a','$tribeid','$tanning','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','wax','Waxworking','a','$tribeid','$waxworking','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','wpn','Weapons','a','$tribeid','$weapons','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','wv','Weaving','a','$tribeid','$weaving','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','wha','Whaling','a','$tribeid','$whaling','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','wd','Woodwork','a','$tribeid','$woodwork','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','ldr','Leadership','b','$tribeid','$leadership','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','sct','Scouting','b','$tribeid','$scouting','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','adm','Administration','b','$tribeid','$administration','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','eco','Economics','b','$tribeid','$economics','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','arc','Archery','b','$tribeid','$archery','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','ath','Atheism','b','$tribeid','$atheism','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','capt','Captaincy','b','$tribeid','$captaincy','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','char','Chariotry','b','$tribeid','$chariotry','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','com','Combat','b','$tribeid','$combat','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','dip','Diplomacy','b','$tribeid','$diplomacy','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','heal','Healing','b','$tribeid','$healing','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','hvyw','Heavy Weapons','b','$tribeid','$heavy_weapons','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','hor','Horsemanship','b','$tribeid','$horsemanship','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','mar','Mariner','b','$tribeid','$mariner','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','nav','Navigation','b','$tribeid','$navigation','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','pol','Politics','b','$tribeid','$politics','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','rel','Religion','b','$tribeid','$religion','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','row','Rowing','b','$tribeid','$rowing','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','sail','Sailing','b','$tribeid','$sailing','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','sea','Seamanship','b','$tribeid','$seamanship','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','sec','Security','b','$tribeid','$security','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','shw','Shipwright','b','$tribeid','$shipwright','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','slv','Slavery','b','$tribeid','$slavery','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','spy','Spying','b','$tribeid','$spying','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','tac','Tactics','b','$tribeid','$tactics','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','tor','Torture','b','$tribeid','$torture','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','tri','Triball','b','$tribeid','$triball','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','alc','Alchemy','c','$tribeid','$alchemy','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','api','Apiarism','c','$tribeid','$apiarism','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','art','Art','c','$tribeid','$art','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','astr','Astronomy','c','$tribeid','$astronomy','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','bak','Baking','c','$tribeid','$baking','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','blub','Blubberwork','c','$tribeid','$blubberwork','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','brk','Brick Making','c','$tribeid','$brickmaking','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','cook','Cooking','c','$tribeid','$cooking','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','dan','Dance','c','$tribeid','$dance','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','dis','Distilling','c','$tribeid','$distilling','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','eng','Engineering','c','$tribeid','$engineering','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','farm','Farming','c','$tribeid','$farming','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','flen','Flensing','c','$tribeid','$flensing','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','lit','Literacy','c','$tribeid','$literacy','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','mtnb','Maintain Boats','c','$tribeid','$maintain_boats','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','mil','Milling','c','$tribeid','$milling','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','mus','Music','c','$tribeid','$music','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','peel','Peeling','c','$tribeid','$peeling','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','ref','Refining','c','$tribeid','$refining','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','res','Research','c','$tribeid','$research','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','san','Sanitation','c','$tribeid','$sanitation','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','seek','Seeking','c','$tribeid','$seeking','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','shb','Shipbuilding','c','$tribeid','$shipbuilding','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','stn','Stonework','c','$tribeid','$stonework','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','glss','Glasswork','c','$tribeid','$glasswork','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','fctl','Fire Control','c','$tribeid','$fire_control','')");
///////////////////////////////////////////////////////end skills///////////////////////////////////////////////
///////////////////////////////////////////////////////begin mapping////////////////////////////////////////////
$db->Execute("ALTER TABLE $dbtables[mapping] "
            ."ADD clanid_$tribeid smallint(2) DEFAULT '0' NOT NULL");
$db->Execute("UPDATE $dbtables[mapping] SET clanid_$tribeid = '1' WHERE hex_id = '$curr_hex'");
include("weight.php");
///////////////////////////////////////////////////////end mapping//////////////////////////////////////////////

    if($display_password)
    {
       echo "Your password is " . $makepass . "<BR><BR>";
    }
    echo "Password has been sent to $username.<BR><BR><BR>";
}
}

if ($flag==0 && $link_forums==$game_url.$game_url_path."forums/")
{
echo "<FORM METHOD=POST ACTION=$link_forums/profile.php TARGET=_blank>"
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
