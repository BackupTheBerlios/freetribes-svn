<?
include("../config.php");
$time_start = getmicrotime();
$title="TribeStrive Installer";
echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">";
echo "<html>";
echo "<head>";
echo "<title>";
echo $title;
echo "</title>";
echo "<STYLE TYPE=\"text/css\">";
echo "<!--";
echo "A:link{text-decoration:none}";
echo "A:visited{text-decoration:none}";
echo "A:hover{text-decoration:underline}";
echo "-->";
echo "</STYLE>";
echo "</head>";
echo "<body background=\"\" bgcolor=\"#408c57\" text=\"#f4d7a4\" link=\"black\" vlink=\"black\" alink=\"#e5e3e0\">";
echo "<FONT FACE=\"Luxi Serif,Tahoma,Trebuchet MS\" POINT-SIZE=10pt>";
echo "\n";
echo "<CENTER><P><H1>TribeStrive $game_version Installer</H1><BR><P>";

if( !$_REQUEST[submit] )
{
    echo "<FORM ACTION=$_SERVER[PHP_SELF] METHOD=POST>";
    echo "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0>";
    echo "</TD></TR><TR><TD COLSPAN=2>&nbsp;</TD></TR>";
    echo "<TR ALIGN=CENTER><TD COLSPAN=2 ALIGN=CENTER>";
    echo "Please enter the name and<BR>password of the administrator<BR>account for the new game.";
    echo "</TD></TR><TR ALIGN=CENTER><TD COLSPAN=2 ALIGN=CENTER>";
    echo "<INPUT TYPE=TEXT NAME=admin_name SIZE=20 MAXLENGTH=40 VALUE='$_REQUEST[admin_name]'>";
    echo "</TD></TR><TR ALIGN=CENTER><TD COLSPAN=2 ALIGN=CENTER>";
    echo "<INPUT TYPE=PASSWORD NAME=password SIZE=20 MAXLENTH=40 VALUE='$_REQUEST[password]'>";
    echo "</TD></TR>";
    echo "<TR><TD COLSPAN=2 ALIGN=CENTER><INPUT TYPE=TEXT NAME=seed SIZE=10 MAXLENGTH=10 VALUE='$_REQUEST[seed]'></TD></TR>";
    echo "<TR><TD COLSPAN=2 ALIGN=CENTER><INPUT TYPE=CHECKBOX NAME=mapping VALUE=1 CHECKED>&nbsp;Create New Map.</TD></TR>";
    echo "<TR><TD><INPUT TYPE=RADIO NAME=new VALUE=0 CHECKED>&nbsp;New Install</TD>";
    echo "<TD><INPUT TYPE=RADIO NAME=new VALUE=1>&nbsp;Game Reset</TD></TR>";
    echo "<TR ALIGN=CENTER><TD COLSPAN=2>";
    echo "<INPUT TYPE=SUBMIT NAME=submit VALUE=Submit>";
    echo "</TD></TR></TABLE></FORM>";
}
else
{
    connectdb();
    echo "<P>Click <A HREF=../index.php>here</A> to log in. | ";
    echo "<A HREF=../help_maps.php>here</A> to view the map info.</CENTER>";
    echo "<P>";
    //to use db_op_result here, we need log table created first ..
    $db->Execute("DROP TABLE IF EXISTS $dbtables[logs]");
    $db->Execute("CREATE TABLE $dbtables[logs] ("
                ."`logid` int(10) unsigned NOT NULL auto_increment,"
                ."`month` smallint(2) NOT NULL default '0',"
                ."`year` smallint(4) NOT NULL default '0',"
                ."`clanid` int(4) unsigned zerofill NOT NULL default '0000',"
                ."`tribeid` decimal(6,2) unsigned zerofill NOT NULL default '0000.00',"
                ."`type` varchar(15) NOT NULL default '0',"
                ."`time` datetime NOT NULL default '0000-00-00 00:00:00',"
                ."`data` text,"
                ."PRIMARY KEY  (`logid`)"
                .") TYPE=MyISAM");
    echo " Done!<BR>";
    flush();
    echo "<CENTER>Creating weather table....";
    flush();
    $db->Execute("DROP TABLE IF EXISTS $dbtables[weather]");
    $db->Execute("CREATE TABLE $dbtables[weather] ("
                ."`weather_id` smallint(2) NOT NULL auto_increment,"
                ."`long_name` varchar(15) NOT NULL default '',"
                ."`current_type` set('Y','N') NOT NULL default 'N',"
                ."PRIMARY KEY `weather_id` (`weather_id`)) TYPE=MyISAM");
    $db->Execute("INSERT INTO $dbtables[weather] "
                ."VALUES (1,'Fine','Y')");
    $db->Execute("INSERT INTO $dbtables[weather] "
                ."VALUES (2,'Rain','N')");
    $db->Execute("INSERT INTO $dbtables[weather] "
                ."VALUES (5,'Heavy Rain','N')");
    $db->Execute("INSERT INTO $dbtables[weather] "
                ."VALUES (7,'Snow','N')");
    $db->Execute("INSERT INTO $dbtables[weather] "
                ."VALUES (8,'Heavy Snow','N')");
    $db->Execute("INSERT INTO $dbtables[weather] "
                ." VALUES (4,'Wind','N')");
    echo " Done!<BR>";
    flush();
    echo "Creating gd_terrain table....";
    flush();
    $db->Execute("DROP TABLE IF EXISTS $dbtables[gd_terrain]");
    $db->Execute("CREATE TABLE $dbtables[gd_terrain] ("
                ."`id` int(10) NOT NULL auto_increment,"
                ."`name` varchar(128) NOT NULL default '',"
                ."`move` int(10) NOT NULL default '0',"
                ."`chance` int(10) NOT NULL default '0',"
                ."`abbr` varchar(10) NOT NULL default '',"
                ."`rsc` tinyint(1) NOT NULL default '0',"
                ."PRIMARY KEY `id` (`id`)"
                .") TYPE=MyISAM");
    $db->Execute("INSERT INTO $dbtables[gd_terrain] VALUES (1,'Prairie',3,3000,'pr',0)");
    $db->Execute("INSERT INTO $dbtables[gd_terrain] VALUES (2,'Grassy Hills',5,5000,'gh',1)");
    $db->Execute("INSERT INTO $dbtables[gd_terrain] VALUES (3,'Deciduous Forest',5,6500,'df',0)");
    $db->Execute("INSERT INTO $dbtables[gd_terrain] VALUES (4,'Deciduous Hills',6,7000,'dh',1)");
    $db->Execute("INSERT INTO $dbtables[gd_terrain] VALUES (5,'Coniferous Forest',5,8000,'cf',0)");
    $db->Execute("INSERT INTO $dbtables[gd_terrain] VALUES (6,'Coniferous Hills',6,9000,'ch',1)");
    $db->Execute("INSERT INTO $dbtables[gd_terrain] VALUES (7,'Low Coniferous Mountains',10,9650,'lcm',1)");
    $db->Execute("INSERT INTO $dbtables[gd_terrain] VALUES (8,'High Snowy Mountains',25,9650,'hsm',1)");
    $db->Execute("INSERT INTO $dbtables[gd_terrain] VALUES (9,'Swamp',8,9800,'sw',0)");
    $db->Execute("INSERT INTO $dbtables[gd_terrain] VALUES (10,'Jungle',5,9850,'jg',0)");
    $db->Execute("INSERT INTO $dbtables[gd_terrain] VALUES (11,'Jungled Hills',6,9900,'jh',1)");
    $db->Execute("INSERT INTO $dbtables[gd_terrain] VALUES (12,'Low Jungled Mountains',10,9950,'ljm',1)");
    $db->Execute("INSERT INTO $dbtables[gd_terrain] VALUES (13,'Lake',30,10000,'l',0)");
    $db->Execute("INSERT INTO $dbtables[gd_terrain] VALUES (14,'Ocean',30,0,'o',0)");
    $db->Execute("INSERT INTO $dbtables[gd_terrain] VALUES (15,'Desert',5,9850,'de',0)");
    $db->Execute("INSERT INTO $dbtables[gd_terrain] VALUES (16,'Tundra',4,8150,'tu',0)");
    echo " Done!<BR>";
    flush();
    echo "Creating gd_resources table....";
    flush();
    $db->Execute("DROP TABLE IF EXISTS $dbtables[gd_resources]");
    $db->Execute("CREATE TABLE $dbtables[gd_resources] ("
                ."`id` int(10) NOT NULL auto_increment,"
                ."`name` varchar(128) NOT NULL default '',"
                ."`chance` int(10) NOT NULL default '0',"
                ."`min` int(10) NOT NULL default '0',"
                ."`max` int(10) NOT NULL default '0',"
                ."`mining_mod` decimal(3,2) NOT NULL default '0.00',"
                ."`produce` varchar(128) NOT NULL default '',"
                ."`limit` smallint(1) NOT NULL default '0',"
                ."`res_code` smallint(2) NOT NULL default '0',"
                ."PRIMARY KEY `id` (`id`)"
                .") TYPE=MyISAM");
    ////res_code 0 = unmapped/unknown
    ////res_code 1 = mapped/unknown
    $db->Execute("INSERT INTO $dbtables[gd_resources] VALUES ('','coal','1200','-1','-1','1.00','Coal','0','2')");
    $db->Execute("INSERT INTO $dbtables[gd_resources] VALUES ('','salt','865','-1','-1','0.80','Salt','0','3')");
    $db->Execute("INSERT INTO $dbtables[gd_resources] VALUES ('','lead','725','-1','-1','0.40','Lead Ore','0','4')");
    $db->Execute("INSERT INTO $dbtables[gd_resources] VALUES ('','zinc',475,-1,-1,0.70,'Zinc Ore','0','5')");
    $db->Execute("INSERT INTO $dbtables[gd_resources] VALUES ('','tin',320,-1,-1,0.65,'Tin Ore','0','6')");
    $db->Execute("INSERT INTO $dbtables[gd_resources] VALUES ('','copper',630,-1,-1,0.55,'Copper Ore','0','7')");
    $db->Execute("INSERT INTO $dbtables[gd_resources] VALUES ('','iron',165,-1,-1,0.50,'Iron Ore','0','8')");
    $db->Execute("INSERT INTO $dbtables[gd_resources] VALUES ('','silver',50,5000,75000,0.25,'Silver Ore','1','9')");
    $db->Execute("INSERT INTO $dbtables[gd_resources] VALUES ('','gold',5,1000,50000,0.15,'Gold Ore','1','10')");
    $db->Execute("INSERT INTO $dbtables[gd_resources] VALUES ('','gems',3,1,30000,0.05,'Raw Gems','1','11')");
    echo " Done!<BR>";
    flush();
    echo "Creating weapons table....";
    flush();
    $db->Execute("DROP TABLE IF EXISTS $dbtables[weapons]");
    $db->Execute("CREATE TABLE $dbtables[weapons] ("
                ."`proper` varchar(20) NOT NULL default '',"
                ."`dbname` varchar(20) NOT NULL default '',"
                ."`inf_inf` decimal(4,2) NOT NULL default '0.00',"
                ."`inf_cav` decimal(4,2) NOT NULL default '0.00',"
                ."`inf_arc` decimal(4,2) NOT NULL default '0.00',"
                ."`cav_inf` decimal(4,2) NOT NULL default '0.00',"
                ."`cav_cav` decimal(4,2) NOT NULL default '0.00',"
                ."`cav_arc` decimal(4,2) NOT NULL default '0.00',"
                ."`hunting` decimal(3,2) NOT NULL default '0.00',"
                ."KEY `dbname` (`dbname`)"
                .") TYPE=MyISAM");
    $db->Execute("INSERT INTO $dbtables[weapons] VALUES ('Club','club',1.68,1.29,1.80,1.53,0.67,1.69,0.03)");
    $db->Execute("INSERT INTO $dbtables[weapons] VALUES ('Bone Spear','bonespear',2.81,2.81,3.00,3.21,2.72,3.50,0.05)");
    $db->Execute("INSERT INTO $dbtables[weapons] VALUES ('Stone Spear','stonespear',2.96,2.96,3.26,3.36,2.87,3.66,0.05)");
    $db->Execute("INSERT INTO $dbtables[weapons] VALUES ('Bonze Spear','bronzespear',3.16,3.16,3.46,3.56,3.07,3.86,0.05)");
    $db->Execute("INSERT INTO $dbtables[weapons] VALUES ('Iron Spear','ironspear',3.46,3.46,3.76,3.86,3.37,4.16,0.05)");
    $db->Execute("INSERT INTO $dbtables[weapons] VALUES ('Steel Spear','steelspear',3.76,3.76,4.06,4.16,3.67,4.46,0.05)");
    $db->Execute("INSERT INTO $dbtables[weapons] VALUES ('Steel_1 Spear','steel1spear',3.96,3.96,4.26,4.36,3.87,4.66,0.05)");
    $db->Execute("INSERT INTO $dbtables[weapons] VALUES ('Steel_2 Spear','steel2spear',4.11,4.11,4.41,4.46,4.02,4.76,0.05)");
    $db->Execute("INSERT INTO $dbtables[weapons] VALUES ('Bone Axe','boneaxe',3.47,3.47,3.77,2.78,1.63,3.08,0.02)");
    $db->Execute("INSERT INTO $dbtables[weapons] VALUES ('Stone Axe','stoneaxe',3.62,3.62,3.92,2.93,1.78,3.23,0.02)");
    $db->Execute("INSERT INTO $dbtables[weapons] VALUES ('Bronze Axe','bronzeaxe',3.82,3.82,4.12,3.13,1.98,2.83,0.02)");
    $db->Execute("INSERT INTO $dbtables[weapons] VALUES ('Iron Axe','ironaxe',4.12,4.12,4.42,3.43,2.28,3.73,0.02)");
    $db->Execute("INSERT INTO $dbtables[weapons] VALUES ('Steel Axe','steelaxe',4.42,4.42,4.72,3.73,2.58,4.03,0.02)");
    $db->Execute("INSERT INTO $dbtables[weapons] VALUES ('Steel_1 Axe','steel1axe',4.62,4.62,4.92,3.93,2.78,4.23,0.02)");
    $db->Execute("INSERT INTO $dbtables[weapons] VALUES ('Steel_2 Axe','steel2axe',4.77,4.77,5.07,4.08,2.93,4.38,0.02)");
    $db->Execute("INSERT INTO $dbtables[weapons] VALUES ('Iron Sword','ironsword',4.33,4.33,4.63,4.37,3.83,4.67,0.04)");
    $db->Execute("INSERT INTO $dbtables[weapons] VALUES ('Steel Sword','steelsword',4.63,4.63,4.93,4.67,4.13,4.97,0.04)");
    $db->Execute("INSERT INTO $dbtables[weapons] VALUES ('Steel_1 Sword','steel1sword',4.83,4.83,5.13,4.87,4.33,5.17,0.04)");
    $db->Execute("INSERT INTO $dbtables[weapons] VALUES ('Steel_2 Sword','steel2sword',4.98,4.98,5.28,5.02,4.48,5.32,0.04)");
    $db->Execute("INSERT INTO $dbtables[weapons] VALUES ('Falchions','falchions',4.12,4.12,4.42,3.43,2.28,3.73,0.03)");
    $db->Execute("INSERT INTO $dbtables[weapons] VALUES ('Picks','picks',1.68,1.29,1.98,1.53,0.67,1.83,0.00)");
    $db->Execute("INSERT INTO $dbtables[weapons] VALUES ('Shovel','shovel',1.68,1.29,1.98,1.53,0.67,1.83,0.00)");
    $db->Execute("INSERT INTO $dbtables[weapons] VALUES ('Scythe','scythe',3.46,3.46,3.76,3.86,3.37,4.16,0.00)");
    $db->Execute("INSERT INTO $dbtables[weapons] VALUES ('Spetum','spetum',2.14,2.50,2.44,3.02,2.48,3.32,0.05)");
    $db->Execute("INSERT INTO $dbtables[weapons] VALUES ('Arbalest','arbalest',0.00,0.00,0.00,0.00,0.00,0.00,0.02)");
    $db->Execute("INSERT INTO $dbtables[weapons] VALUES ('Longbow','longbow',0.00,0.00,0.00,0.00,0.00,0.00,0.15)");
    $db->Execute("INSERT INTO $dbtables[weapons] VALUES ('Bow','bow',0.00,0.00,0.00,0.00,0.00,0.00,0.15)");
    $db->Execute("INSERT INTO $dbtables[weapons] VALUES ('Crossbow','crossbow',0.00,0.00,0.00,0.00,0.00,0.00,0.15)");
    $db->Execute("INSERT INTO $dbtables[weapons] VALUES ('Horsebow','horsebow',0.00,0.00,0.00,0.00,0.00,0.00,0.15)");
    $db->Execute("INSERT INTO $dbtables[weapons] VALUES ('Sling','sling',0.00,0.00,0.00,0.00,0.00,0.00,0.10)");
    $db->Execute("INSERT INTO $dbtables[weapons] VALUES ('Snares','snares',0.00,0.00,0.00,0.00,0.00,0.00,0.05)");
    $db->Execute("INSERT INTO $dbtables[weapons] VALUES ('Traps','traps',0.00,0.00,0.00,0.00,0.00,0.00,0.10)");
    $db->Execute("INSERT INTO $dbtables[weapons] VALUES ('Net','net',0.00,0.00,0.00,0.00,0.00,0.00,0.10)");
    echo " Done!<BR>";
    flush();
    echo "Creating armor table....";
    flush();
    $db->Execute("DROP TABLE IF EXISTS $dbtables[armor]");
    $db->Execute("CREATE TABLE $dbtables[armor] ("
                ."`arm_id` int(11) NOT NULL auto_increment,"
                ."`proper` varchar(20) NOT NULL default '',"
                ."`long_name` varchar(20) NOT NULL default '',"
                ."`type` varchar(15) NOT NULL default '',"
                ."`arrow` smallint(2) NOT NULL default '0',"
                ."`bronze` smallint(2) NOT NULL default '0',"
                ."`iron` smallint(2) NOT NULL default '0',"
                ."`pellet` smallint(2) NOT NULL default '0',"
                ."`quarrel` smallint(2) NOT NULL default '0',"
                ."`shaft` smallint(2) NOT NULL default '0',"
                ."`steel` smallint(2) NOT NULL default '0',"
                ."`steel_1` smallint(2) NOT NULL default '0',"
                ."`steel_2` smallint(2) NOT NULL default '0',"
                ."`stone` smallint(2) NOT NULL default '0',"
                ."PRIMARY KEY `arm_id` (`arm_id`)"
                .") TYPE=MyISAM");

    $db->Execute("INSERT INTO $dbtables[armor] VALUES (1,'Iron Breastplate','ironbreastplate','overtorso',5,20,10,15,5,10,8,6,4,25)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (2,'Iron Chain','ironchain','torso',2,17,15,5,2,5,12,10,7,20)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (3,'Cuirass','cuirass','overtorso',5,16,8,13,5,9,6,4,3,20)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (4,'Bone Cuirboilli','cuirboillibone','overtorso',5,10,7,10,5,5,5,3,2,12)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (5,'Haube','haube','head',5,8,6,9,5,3,4,4,4,10)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (6,'Heaters','heaters','shield',9,3,2,15,9,6,1,1,1,4)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (7,'Jerkin','jerkin','torso',2,6,4,4,2,1,2,1,1,8)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (8,'Ring Mail','ring','torso',2,12,10,5,2,3,7,5,4,15)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (9,'Scale Armor','scale','torso',2,10,8,4,2,2,5,3,2,13)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (10,'Wooden Shield','woodenshield','shield',10,4,3,17,10,7,2,1,1,5)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (12,'Bone Armor','bonearmor','torso',2,9,7,3,1,1,4,2,1,12)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (13,'Hoods','hood','head',4,4,2,5,4,1,1,1,1,6)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (14,'Trews','trews','leg',2,3,2,1,2,1,1,1,1,4)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (46,'None','none','torso',0,0,0,0,0,0,0,0,0,0)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (17,'Scutum','scutum','shield',12,5,3,18,12,8,2,1,1,6)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (19,'Iron Greave','irongreaves','leg',4,7,5,1,4,1,4,2,1,8)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (20,'Steel_2 Bascinet','steel2bascinet','head',10,20,20,15,10,13,20,20,18,20)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (21,'Steel_1 Bascinet','steel1bascinet','head',9,20,20,14,9,11,18,16,14,20)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (22,'Steel Bascinet','steelbascinet','head',8,18,16,13,8,9,14,12,10,20)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (23,'Iron Bascinet','ironbascinet','head',7,14,12,12,7,7,10,8,6,16)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (24,'Steel_2 Helm','steel2helm','head',9,20,20,13,9,12,20,18,16,20)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (25,'Steel_1 Helm','steel1helm','head',8,20,18,12,8,10,16,14,12,20)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (26,'Steel Helm','steelhelm','head',7,16,14,11,7,7,12,10,8,18)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (27,'Iron Helm','ironhelm','head',6,12,10,10,6,5,8,6,4,14)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (28,'None','none','head',0,0,0,0,0,0,0,0,0,0)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (29,'None','none','shield',0,0,0,0,0,0,0,0,0,0)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (30,'Iron Shield','ironshield','shield',15,7,5,20,15,10,3,2,1,8)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (31,'Steel Shield','steelshield','shield',18,9,7,23,18,12,4,3,2,10)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (32,'Steel_1 Shield','steel1shield','shield',20,10,9,25,20,14,5,4,3,10)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (33,'Steel_2 Shield','steel2shield','shield',23,10,10,28,23,16,6,5,4,10)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (34,'Iron Buckler','ironbuckler','shield',8,3,2,13,8,5,1,1,1,4)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (35,'Steel Buckler','steelbuckler','shield',9,4,3,15,9,6,2,1,1,5)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (36,'Steel_1 Buckler','steel1buckler','shield',10,5,4,18,10,7,3,2,1,5)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (37,'Steel_2 Buckler','steel2buckler','shield',11,5,5,20,11,8,4,2,2,5)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (38,'None','none','overtorso',0,0,0,0,0,0,0,0,0,0)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (51,'Steel Breastplate','steelbreastplate','overtorso',50,75,65,65,50,45,55,45,40,85)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (52,'Steel_1 Breastplate','steel1breastplate','overtorso',55,85,70,70,55,50,60,55,50,90)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (41,'Steel_2 Breastplate','steel2breastplate','overtorso',60,90,75,75,60,55,65,60,55,90)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (42,'Iron Plate','ironplate','overtorso',45,65,55,60,45,40,45,35,30,75)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (43,'Steel Plate','steelplate','overtorso',50,75,65,65,50,45,55,45,40,85)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (44,'Steel_1 Plate','steel1plate','overtorso',55,85,70,70,55,50,60,55,50,90)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (45,'Steel_2 Plate','steel2plate','overtorso',60,90,75,75,60,55,65,60,55,90)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (47,'Steel Chain','steelchain','torso',2,20,17,6,2,6,15,12,10,25)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (48,'Steel_1 Chain','steel1chain','torso',2,25,20,7,2,7,18,15,12,25)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (49,'Steel_2 Chain','steel2chain','torso',2,25,25,8,2,8,20,18,15,25)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (50,'None','none','leg',0,0,0,0,0,0,0,0,0,0)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (53,'Leather Barding','leatherbarding','horse',2,6,4,4,2,1,2,1,1,8)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (54,'Scale Barding','scalebarding','horse',2,10,8,4,2,2,5,3,2,13)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (55,'Ring Barding','ringbarding','horse',2,12,10,5,2,3,7,5,4,15)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (56,'Iron Plate Barding','ironplatebarding','horse',45,65,55,60,45,40,40,35,30,75)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (57,'Steel Plate Barding','steelplatebarding','horse',50,75,65,65,50,45,55,45,40,85)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (58,'Cuirboilli','cuirboilli','overtorso',5,14,8,12,5,7,5,3,3,15)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (59,'Iron Chain Barding','ironchainbarding','horse',2,17,15,5,2,5,12,10,7,20)");
    $db->Execute("INSERT INTO $dbtables[armor] VALUES (60,'Steel Chain Barding','steelchainbarding','horse',2,20,17,6,2,6,15,12,10,25)");
    echo " Done!<BR>";
    flush();
    echo "Creating activities table....";
    flush();
    $db->Execute("DROP TABLE IF EXISTS $dbtables[activities]");
    $db->Execute("CREATE TABLE $dbtables[activities] ("
                ."`id` int(11) NOT NULL auto_increment,"
                ."`tribeid` decimal(6,2) unsigned zerofill NOT NULL default '0000.00',"
                ."`skill_abbr` varchar(5) NOT NULL default '',"
                ."`product` varchar(15) NOT NULL default '',"
                ."`actives` int(25) NOT NULL default '0',"
                ."PRIMARY KEY `id` (`id`)"
                .") TYPE=MyISAM ");
    echo " Done!<BR>";
    flush();
    echo "Creating alliances table....";
    flush();
    $db->Execute("DROP TABLE IF EXISTS $dbtables[alliances]");
    $db->Execute("CREATE TABLE $dbtables[alliances] ("
                ."`alliance_id` int(11) NOT NULL auto_increment,"
                ."`offerer_id` int(4) unsigned zerofill NOT NULL default '0000',"
                ."`receipt_id` int(4) unsigned zerofill NOT NULL default '0000',"
                ."`accept` set('Y','N') NOT NULL default 'N',"
                ."PRIMARY KEY `alliance_id` (`alliance_id`)"
                .") TYPE=MyISAM");
    echo " Done!<BR>";
    flush();
    echo "Creating bug_tracker table....";
    flush();
    $db->Execute("DROP TABLE IF EXISTS $dbtables[bug_tracker]");
    $db->Execute("CREATE TABLE $dbtables[bug_tracker] ("
                ."`entryid` int(11) NOT NULL auto_increment,"
                ."`ticketid` int(11) NOT NULL default '0',"
                ."`clanid` int(4) unsigned zerofill NOT NULL default '0000',"
                ."`tribeid` decimal(6,2) unsigned zerofill NOT NULL default '0000.00',"
                ."`username` varchar(30) NOT NULL default '',"
                ."`skillname` varchar(15) NOT NULL default '',"
                ."`product` varchar(20) NOT NULL default '',"
                ."`summary` varchar(50) NOT NULL default '',"
                ."`detail` text NOT NULL,"
                ."`status` enum('NEW','OPEN','STALLED','RESOLVED') NOT NULL default 'NEW',"
                ."`owner` varchar(30) NOT NULL default '',"
                ."`month` smallint(2) NOT NULL default '0',"
                ."`year` smallint(5) NOT NULL default '0',"
                ."PRIMARY KEY `entryid` (`entryid`)"
                .") TYPE=MyISAM");
    echo " Done!<BR>";
    flush();
    echo "Creating chiefs table....";
    flush();
    $db->Execute("DROP TABLE IF EXISTS $dbtables[chiefs]");
    $db->Execute("CREATE TABLE $dbtables[chiefs] ("
                ."`clanid` int(4) unsigned zerofill NOT NULL auto_increment,"
                ."`username` varchar(30) NOT NULL default '',"
                ."`password` varchar(60) NOT NULL default '',"
                ."`chiefname` varchar(30) NOT NULL default '',"
                ."`email` text NOT NULL,"
                ."`lastseen_month` smallint(2) default NULL,"
                ."`lastseen_year` smallint(4) NOT NULL default '0',"
                ."`ipaddr` varchar(15) NOT NULL default '',"
                ."`active` int(11) NOT NULL default '0',"
                ."`current_unit` decimal(6,2) unsigned zerofill NOT NULL default '0000.00',"
                ."`minimap` set('0','1','2') NOT NULL default '1',"
                ."`admin` tinyint(1) NOT NULL default '0',"
                ."`score` bigint(20) NOT NULL default '0',"
                ."`hour` int(15) default NULL,"
                ."`theme` varchar(50) NOT NULL default 'Original',"
                ."`tooltip` ENUM( '0', '1' ) NOT NULL default '1',"
                ."PRIMARY KEY `clanid` (`clanid`)"
                .") TYPE=MyISAM");
    $hashed_pass = md5($_REQUEST[password]);
    $db->Execute("INSERT INTO $dbtables[chiefs] "
                ."VALUES("
                ."'',"
                ."'$_REQUEST[admin_name]',"
                ."'$hashed_pass',"
                ."'$_REQUEST[admin_name]',"
                ."'$admin_mail',"
                ."'',"
                ."'',"
                ."'$ip',"
                ."'',"
                ."'0001.00',"
                ."'0',"
                ."'99',"
                ."'',"
                ."'',"
                ."'Original',"
                ."'1')");
    echo " Done!<BR>";
    flush();
    echo "Creating clans table....";
    flush();
    $db->Execute("DROP TABLE IF EXISTS $dbtables[clans]");
    $db->Execute("CREATE TABLE $dbtables[clans] ("
                ."`clanid` int(4) unsigned zerofill NOT NULL default '0000',"
                ."`clanname` varchar(25) NOT NULL default 'Wanderers',"
                ."`religion` varchar(25) NOT NULL default 'None',"
                ."`active` tinyint(1) NOT NULL default '1',"
                ."PRIMARY KEY `clanid` (`clanid`)"
                .") TYPE=MyISAM ");
    $db->Execute("INSERT INTO $dbtables[clans] "
                ."VALUES("
                ."'0001',"
                ."'Administrator',"
                ."'None',"
                ."'1')");
    echo " Done!<BR>";
    flush();
    echo "Creating tribes table....";
    flush();
    $db->Execute("DROP TABLE IF EXISTS $dbtables[tribes]");
    $db->Execute("CREATE TABLE $dbtables[tribes] ("
                ."`clanid` int(4) unsigned zerofill NOT NULL default '0000',"
                ."`tribeid` decimal(6,2) unsigned zerofill NOT NULL default '0000.00',"
                ."`tribename` varchar(30) NOT NULL default '',"
                ."`DeVA` decimal(6,2) unsigned zerofill NOT NULL default '0000.00',"
                ."`totalpop` int(25) NOT NULL default '0',"
                ."`warpop` int(25) NOT NULL default '0',"
                ."`activepop` int(25) NOT NULL default '0',"
                ."`inactivepop` int(25) NOT NULL default '0',"
                ."`slavepop` int(25) NOT NULL default '0',"
                ."`specialpop` int(25) NOT NULL default '0',"
                ."`maxam` int(25) NOT NULL default '0',"
                ."`curam` int(25) NOT NULL default '0',"
                ."`morale` decimal(4,3) NOT NULL default '1.000',"
                ."`maxweight` decimal(25,2) NOT NULL default '0.00',"
                ."`curweight` int(25) NOT NULL default '0',"
                ."`hex_id` int(11) NOT NULL default '0',"
                ."`pri_skill_att` varchar(4) NOT NULL default '',"
                ."`sec_skill_att` varchar(4) NOT NULL default '',"
                ."`research_att` varchar(25) NOT NULL default '',"
                ."`move_pts` int(11) NOT NULL default '0',"
                ."`goods_tribe` decimal(6,2) unsigned zerofill NOT NULL default '0000.00',"
                ."PRIMARY KEY `clanid` (`clanid`),"
                ."UNIQUE KEY `tribeid` (`tribeid`)"
                .") TYPE=MyISAM");
    $random_hex = rand(1, 4096);
    $db->Execute("INSERT INTO $dbtables[tribes] "
                ."VALUES("
                ."'0001',"
                ."'0001.00',"
                ."'',"
                ."'',"
                ."'0000',"
                ."'0000',"
                ."'6000',"
                ."'2000',"
                ."'',"
                ."'',"
                ."'6000',"
                ."'6000',"
                ."'2.0',"
                ."'120000',"
                ."'',"
                ."'$random_hex',"
                ."'',"
                ."'',"
                ."'',"
                ."'35000',"
                ."'0001.00')");
    echo " Done!<BR>";
    flush();
    echo "Creating combat_terrain_effect table....";
    flush();
    $db->Execute("DROP TABLE IF EXISTS $dbtables[combat_terrain_effect]");
    $db->Execute("CREATE TABLE $dbtables[combat_terrain_effect] ("
                ."`type` varchar(11) NOT NULL default '',"
                ."`ah` decimal(3,2) NOT NULL default '0.00',"
                ."`ar` decimal(3,2) NOT NULL default '0.00',"
                ."`ch` decimal(3,2) NOT NULL default '0.00',"
                ."`de` decimal(3,2) NOT NULL default '0.00',"
                ."`df` decimal(3,2) NOT NULL default '0.00',"
                ."`gh` decimal(3,2) NOT NULL default '0.00',"
                ."`hsm` decimal(3,2) NOT NULL default '0.00',"
                ."`hvm` decimal(3,2) NOT NULL default '0.00',"
                ."`jh` decimal(3,2) NOT NULL default '0.00',"
                ."`ju` decimal(3,2) NOT NULL default '0.00',"
                ."`lam` decimal(3,2) NOT NULL default '0.00',"
                ."`lcm` decimal(3,2) NOT NULL default '0.00',"
                ."`ljm` decimal(3,2) NOT NULL default '0.00',"
                ."`lsm` decimal(3,2) NOT NULL default '0.00',"
                ."`pr` decimal(3,2) NOT NULL default '0.00',"
                ."`sh` decimal(3,2) NOT NULL default '0.00',"
                ."`sw` decimal(3,2) NOT NULL default '0.00',"
                ."`tu` decimal(3,2) NOT NULL default '0.00',"
                ."`dh` decimal(3,2) NOT NULL default '0.00',"
                ."`jg` decimal(3,2) NOT NULL default '0.00',"
                ."PRIMARY KEY type (type)) TYPE=MyISAM");
    $db->Execute("INSERT INTO $dbtables[combat_terrain_effect] "
                ."VALUES("
                ."'archery',0.80,1.00,0.32,1.00,0.40,0.32,0.80,0.50,0.50,0.24,1.00,0.60,0.24,0.18,0.60,1.00,0.80,0.50,1.00,0.40)");
    $db->Execute("INSERT INTO $dbtables[combat_terrain_effect] "
                ."VALUES("
                ."'attack',0.80,1.00,0.50,1.00,0.50,0.40,0.80,0.30,0.40,0.30,0.40,0.50,0.30,0.10,0.30,1.00,0.80,0.10,1.00,0.50)");
    $db->Execute("INSERT INTO $dbtables[combat_terrain_effect] "
                ."VALUES("
                ."'defense',1.10,1.20,0.60,1.20,0.60,0.50,1.10,0.70,0.70,0.40,0.50,0.70,0.60,0.30,0.60,1.20,1.10,1.40,1.20,0.60)");
    echo " Done!<BR>";
    flush();
    echo "Creating combat_terrain_mods table....";
    flush();
    $db->Execute("DROP TABLE IF EXISTS $dbtables[combat_terrain_mods]");
    $db->Execute("CREATE TABLE $dbtables[combat_terrain_mods] ("
                ."`pr` decimal(2,1) NOT NULL default '0.0',"
                ."`tu` decimal(2,1) NOT NULL default '0.0',"
                ."`df` decimal(2,1) NOT NULL default '0.0',"
                ."`cf` decimal(2,1) NOT NULL default '0.0',"
                ."`jg` decimal(2,1) NOT NULL default '0.0',"
                ."`gh` decimal(2,1) NOT NULL default '0.0',"
                ."`dh` decimal(2,1) NOT NULL default '0.0',"
                ."`ch` decimal(2,1) NOT NULL default '0.0',"
                ."`jh` decimal(2,1) NOT NULL default '0.0',"
                ."`lcm` decimal(2,1) NOT NULL default '0.0',"
                ."`ljm` decimal(2,1) NOT NULL default '0.0',"
                ."`hsm` decimal(2,1) NOT NULL default '0.0',"
                ."`sw` decimal(2,1) NOT NULL default '0.0',"
                ."`10wall` decimal(2,1) NOT NULL default '0.0',"
                ."`15wall` decimal(2,1) NOT NULL default '0.0',"
                ."`20wall` decimal(2,1) NOT NULL default '0.0'"
                .") TYPE=MyISAM");
    $db->Execute("INSERT INTO $dbtables[combat_terrain_mods] "
                ."VALUES("
                ."1.0,1.0,1.0,1.0,1.0,0.8,0.8,0.8,0.8,0.6,0.6,0.5,0.5,0.5,0.4,0.3)");
    echo " Done!<BR>";
    flush();
    echo "Creating combat_weather table....";
    flush();
    $db->Execute("DROP TABLE IF EXISTS $dbtables[combat_weather]");
    $db->Execute("CREATE TABLE $dbtables[combat_weather] ("
                ."`type` varchar(15) NOT NULL default '',"
                ."`1` decimal(2,1) NOT NULL default '0.0',"
                ."`4` decimal(2,1) NOT NULL default '0.0',"
                ."`2` decimal(2,1) NOT NULL default '0.0',"
                ."`5` decimal(2,1) NOT NULL default '0.0',"
                ."`7` decimal(2,1) NOT NULL default '0.0',"
                ."`8` decimal(2,1) NOT NULL default '0.0',"
                ."PRIMARY KEY type (type)) TYPE=MyISAM");
    $db->Execute("INSERT INTO $dbtables[combat_weather] "
                ." VALUES ('sling',1.0,0.7,0.8,0.4,0.8,0.5)");
    $db->Execute("INSERT INTO $dbtables[combat_weather] "
                ."VALUES ('bow',1.0,0.3,0.4,0.1,0.7,0.4)");
    $db->Execute("INSERT INTO $dbtables[combat_weather] "
                ."VALUES ('crossbows',1.0,0.4,0.4,0.2,0.7,0.4)");
    $db->Execute("INSERT INTO $dbtables[combat_weather] "
                ."VALUES ('melee',1.0,0.8,0.9,0.7,0.9,0.7)");
    $db->Execute("INSERT INTO $dbtables[combat_weather] "
                ."VALUES ('cavalry',1.0,0.7,0.8,0.5,0.8,0.5)");
    $db->Execute("INSERT INTO $dbtables[combat_weather] "
                ."VALUES ('hurl',1.0,0.9,0.8,0.5,0.8,0.5)");
    $db->Execute("INSERT INTO $dbtables[combat_weather] "
                ."VALUES ('arbalest',1.0,0.4,0.4,0.2,0.7,0.4)");
    echo " Done!<BR>";
    flush();
    echo "Creating combat table....";
    flush();
    $db->Execute("DROP TABLE IF EXISTS $dbtables[combats]");
    $db->Execute("CREATE TABLE $dbtables[combats] ("
                ."`combat_id` varchar(50) NOT NULL default '',"
                ."`side` set('A','D') default NULL,"
                ."`garid` int(11) NOT NULL default '0',"
                ."`tribeid` decimal(6,2) unsigned zerofill NOT NULL default '0000.00',"
                ."`startforce` int(6) NOT NULL default '0',"
                ."`curforce` int(6) NOT NULL default '0',"
                ."`experience` decimal(6,2) NOT NULL default '0.00',"
                ."`terrainsp` varchar(5) NOT NULL default '',"
                ."`exp` decimal(6,2) NOT NULL default '0.00',"
                ."`horses` int(6) NOT NULL default '0',"
                ."`weapon1` varchar(25) default NULL,"
                ."`weapon2` varchar(25) default NULL,"
                ."`head_armor` varchar(25) default NULL,"
                ."`torso_armor` varchar(25) default NULL,"
                ."`otorso_armor` varchar(25) default NULL,"
                ."`legs_armor` varchar(25) default NULL,"
                ."`shield` varchar(25) default NULL,"
                ."`horse_armor` varchar(25) default NULL,"
                ."`trooptype` set('A','Q','B','C','I') default NULL,"
                ."`startsector1` int(6) NOT NULL default '0',"
                ."`sector1` int(6) NOT NULL default '0',"
                ."`startsector2` int(6) NOT NULL default '0',"
                ."`sector2` int(6) NOT NULL default '0',"
                ."`startsector3` int(6) NOT NULL default '0',"
                ."`sector3` int(6) NOT NULL default '0',"
                ."`hex_id` int(11) default NULL,"
                ."INDEX tribeid (tribeid,combat_id)) TYPE=MyISAM");
    echo " Done!<BR>";
    flush();
    echo "Creating fair_tribe table....";
    flush();
    $db->Execute("DROP TABLE IF EXISTS $dbtables[fair_tribe]");
    $db->Execute("CREATE TABLE $dbtables[fair_tribe] ("
                ."`clan_id` int(4) unsigned zerofill NOT NULL default '0000',"
                ."`tribeid` decimal(6,2) unsigned zerofill NOT NULL default '0000.00',"
                ."`trans_id` int(11) NOT NULL auto_increment,"
                ."`skill_level` int(2) NOT NULL default '0',"
                ."`buy_sell` set('B','S','C') NOT NULL default '',"
                ."`product` varchar(20) NOT NULL default '0',"
                ."`quantity` int(4) NOT NULL default '0',"
                ."`price` decimal(10,2) NOT NULL default '0.00',"
                ."PRIMARY KEY `transaction` (`trans_id`)"
                .") TYPE=MyISAM");
    echo " Done!<BR>";
    flush();
    echo "Creating fair table....";
    flush();
    $db->Execute("DROP TABLE IF EXISTS $dbtables[fair]");
    $db->Execute("CREATE TABLE $dbtables[fair] ("
                ."`prod_id` int(11) NOT NULL auto_increment,"
                ."`proper_name` varchar(20) NOT NULL default '',"
                ."`abbr` varchar(20) NOT NULL default '',"
                ."`price_sell` decimal(7,2) NOT NULL default '0.00',"
                ."`price_buy` decimal(7,2) NOT NULL default '0.00',"
                ."`amount` int(8) NOT NULL default '0',"
                ."`limit` int(5) NOT NULL default '0',"
                ."`cultural` set('Y','N') NOT NULL default '',"
                ."`p_amount` int(7) NOT NULL default '0',"
                ."PRIMARY KEY `prod_id` (`prod_id`)"
                .") TYPE=MyISAM");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (159,'Bricks','bricks',2.00,6.00,5000,1000,'N',5000)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (160,'Bread','bread',1.00,2.99,5000,1000,'N',5000)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (161,'Charcoal','charcoal',1.00,3.00,5000,1000,'N',5000)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (172,'Horses','horse',3.27,160.00,200,40,'N',200)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (175,'Elephants','elephant',7.32,220.00,200,40,'N',200)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (187,'Coke','coke',10.80,0.00,2500,500,'N',2500)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (188,'Silver','silver',1.00,1.00,5000,1000,'N',5000)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (194,'Scrapers','scrapers',9.26,5.21,100,20,'N',100)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (203,'Iron Plate','ironplate',0.00,1015.61,50,10,'N',50)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (213,'Iron Buckler','ironbuckler',0.00,28.00,200,40,'N',200)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (215,'Iron Helm','ironhelm',12.00,20.00,100,20,'N',100)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (219,'Iron Bascinet','ironbascinet',0.00,78.12,200,40,'N',200)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (222,'Iron Greave','irongreaves',0.00,3681.84,200,40,'N',200)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (225,'Cross Bow','crossbow',2.12,565.02,150,30,'N',150)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (226,'Repeating Arbalest','repeatingarbalest',0.00,4450.38,25,5,'N',25)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (232,'Silver Ore','silver.ore',1.72,1.00,5000,1000,'N',5000)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (279,'Pigs','pigs',1.00,57.27,5000,1000,'N',5000)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (280,'Sheep','sheep',2.04,31.75,2500,500,'N',2500)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (142,'Bladders','bladder',10.00,13.00,100,20,'N',100)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (143,'Ladder','ladder',3.00,8.00,25,5,'N',25)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (147,'Catapult','catapult',0.00,220.00,50,10,'N',50)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (148,'Stone Axe','stoneaxe',25.20,14.87,200,40,'N',200)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (149,'Stone Spear','stonespear',15.00,25.00,500,100,'N',500)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (152,'Cuirboilli','cuirboilli',52.00,112.50,200,40,'N',200)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (153,'Iron Mace','ironmace',3.00,10.00,200,40,'N',200)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (154,'Long bow','longbow',1.00,1.00,200,40,'N',200)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (155,'Clubs','clubs',1.00,2.00,2500,500,'N',2500)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (159,'Bricks','bricks',2.00,6.00,5000,1000,'N',5000)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (160,'Bread','bread',1.00,2.99,5000,1000,'N',5000)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (161,'Charcoal','charcoal',1.00,3.00,5000,1000,'N',5000)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (172,'Horses','horse',3.27,160.00,200,40,'N',200)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (175,'Elephants','elephant',7.32,220.00,200,40,'N',200)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (187,'Coke','coke',10.80,0.00,2500,500,'N',2500)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (188,'Silver','silver',1.00,1.00,5000,1000,'N',5000)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (194,'Scrapers','scrapers',9.26,5.21,100,20,'N',100)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (203,'Iron Plate','ironplate',0.00,1015.61,50,10,'N',50)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (213,'Iron Buckler','ironbuckler',0.00,28.00,200,40,'N',200)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (215,'Iron Helm','ironhelm',12.00,20.00,100,20,'N',100)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (219,'Iron Bascinet','ironbascinet',0.00,78.12,200,40,'N',200)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (222,'Iron Greave','irongreaves',0.00,3681.84,200,40,'N',200)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (225,'Cross Bow','crossbow',2.12,565.02,150,30,'N',150)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (226,'Repeating Arbalest','repeatingarbalest',0.00,4450.38,25,5,'N',25)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (232,'Silver Ore','silver.ore',1.72,1.00,5000,1000,'N',5000)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (279,'Pigs','pigs',1.00,57.27,5000,1000,'N',5000)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (280,'Sheep','sheep',2.04,31.75,2500,500,'N',2500)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (133,'Saddlebags','saddlebags',12.43,91.48,200,40,'N',200)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (134,'Saddle','saddle',6.40,54.68,200,40,'N',200)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (137,'Shackles','shackles',8.00,31.25,500,100,'N',500)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (138,'Hoe','hoe',4.68,12.50,100,20,'N',100)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (140,'Trumpets','trumpet',3.00,0.00,50,10,'N',50)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (131,'Backpack','backpack',9.10,85.82,250,50,'N',250)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (132,'Whip','whip',5.00,15.00,100,20,'N',100)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (128,'Bone Armor','bonearmor',0.00,150.00,200,40,'N',200)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (129,'Hoods','hood',1.00,3.12,200,40,'N',200)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (131,'Backpack','backpack',9.10,85.82,250,50,'N',250)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (132,'Whip','whip',5.00,15.00,100,20,'N',100)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (130,'Trews','trews',3.07,24.40,500,100,'N',500)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (122,'Wooden Shield','woodenshield',0.00,80.00,200,40,'N',200)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (125,'Bone Axe','boneaxe',6.00,10.83,200,40,'N',200)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (127,'Bone Frame','boneframe',2.00,5.00,250,50,'N',250)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (112,'Zinc','zinc',10.05,2.27,6000,1200,'N',6000)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (113,'Zinc Ore','zinc.ore',1.98,1.44,2500,500,'N',2500)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (114,'Slaves','slaves',8610.21,27.77,500,100,'N',500)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (116,'Iron Chain','ironchain',0.00,1424.11,100,20,'N',100)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (117,'Horsebow','horsebow',26.00,780.78,100,20,'N',100)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (120,'Ring Mail','ring',0.00,250.00,150,30,'N',150)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (96,'Strings','strings',1.32,3.25,1500,300,'N',1500)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (97,'Spice','spice',135.39,0.00,400,80,'N',400)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (98,'Scythe','scythe',0.00,126.95,250,50,'N',250)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (99,'Sugar','sugar',36.56,2.69,1000,200,'N',1000)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (100,'Iron Sword','Ironsword',80.41,194.85,250,50,'N',250)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (101,'Tapestry','tapestry',1020.00,0.00,25,5,'N',25)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (102,'Tar','tar',1.00,0.00,5000,1000,'N',5000)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (103,'Tin','tin',5.00,7.00,1500,300,'N',1500)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (104,'Tin Ore','tin.ore',3.61,2.28,5000,1000,'N',5000)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (105,'Tobacco','tobacco',1362.52,0.00,2500,500,'N',2500)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (106,'Traps','traps',11.60,3.57,2500,500,'N',2500)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (107,'Trinket','trinket',10.00,0.00,1000,200,'N',1000)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (108,'Urn','urn',27.00,0.00,100,20,'N',100)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (109,'Wagon','wagon',259.45,1.61,500,100,'N',500)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (110,'Wax','wax',202.38,74.83,200,40,'N',200)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (111,'Wine','wine',48318.14,0.00,150,30,'N',150)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (72,'Palanquin','palanquin',0.00,25.00,100,20,'N',100)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (73,'Parchment','parchment',8.00,0.00,1500,300,'N',1500)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (75,'Picks','picks',29.91,70.10,250,50,'N',250)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (76,'Plow','plow',356.30,179.75,50,10,'N',50)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (77,'Provisions','provs',33.51,1.00,5000,1000,'N',5000)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (78,'Rake','rake',18.41,0.00,250,50,'N',250)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (79,'Rope','rope',27.22,32.15,400,80,'N',400)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (81,'Rug','rug',187.20,0.00,100,20,'N',100)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (82,'Rum','rum',17972.49,0.00,100,20,'N',100)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (83,'Salt','salt',2.26,1.10,7000,1400,'N',7000)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (84,'Scutum','scutum',60.00,92.00,500,100,'N',500)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (85,'Shaft','shaft',3.67,1.84,2500,500,'N',2500)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (86,'Shovel','shovel',305.04,4.33,500,100,'N',500)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (87,'Skins','skins',1.00,82.34,5000,1000,'N',5000)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (88,'Scale Armor','scale',0.00,181.25,175,35,'N',175)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (89,'Iron Shield','ironshield',0.00,572.18,175,35,'N',175)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (90,'Sling','sling',5.83,8.33,1000,200,'N',1000)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (91,'Snares','snares',10.00,22.50,2000,400,'N',2000)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (92,'Iron Spear','ironspear',46.91,62.33,500,100,'N',500)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (93,'Spetum','spetum',20.00,35.00,500,100,'N',500)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (94,'Staves','staves',7.63,1.84,1000,200,'N',1000)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (95,'Stones','stones',3.85,1.00,5000,1000,'N',5000)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (61,'Jerkin','jerkin',4221.08,1.00,500,100,'N',500)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (62,'Kayak','kayak',110.00,0.00,50,10,'N',50)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (63,'Lead','lead',3.36,7.14,5000,1000,'N',5000)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (64,'Lead Ore','lead.ore',4.83,1.64,2500,500,'N',2500)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (65,'Leather','leather',1.00,190.42,5000,1000,'N',5000)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (66,'Logs','logs',1.18,1.79,1500,300,'N',1500)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (67,'Lutes','lutes',600.00,0.00,25,5,'N',25)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (68,'Mattock','mattock',70.00,0.00,250,50,'N',250)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (69,'Mead','mead',846.02,0.00,200,40,'N',200)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (70,'Net','net',21.12,31.25,250,50,'N',250)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (71,'Ornament','ornament',25.00,0.00,500,100,'N',500)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (48,'Harp','harp',600.00,0.00,25,5,'N',25)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (49,'Haube','haube',40.00,60.00,250,50,'N',250)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (50,'Heaters','heaters',7.60,7.92,1000,200,'N',1000)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (51,'Herbs','herbs',12.70,51.22,1500,300,'N',1500)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (52,'Hives','hives',121.40,510.87,50,10,'N',50)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (53,'Horn','horn',22.00,0.00,250,50,'N',250)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (56,'Honey','honey',8.80,16.25,2000,400,'N',2000)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (57,'Inlay','inlay',920.00,0.00,40,8,'N',40)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (58,'Iron','iron',16.13,8.20,5000,1000,'N',5000)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (59,'Iron Ore','iron.ore',1.72,1.36,5000,1000,'N',5000)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (60,'Jar','jar',13.00,0.00,200,40,'N',200)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (38,'Flutes','flutes',12.00,15.00,250,50,'N',250)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (39,'Frames','frame',2.50,5.00,2500,500,'N',2500)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (40,'Furs','furs',2.42,2.48,2500,500,'N',2500)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (41,'Glass Pipe','glasspipe',2.00,0.00,750,150,'N',750)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (42,'Goats','goat',1.00,176.80,10000,2000,'N',10000)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (43,'Gold','gold',1605.88,5.66,1500,300,'N',1500)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (44,'Goldwork','goldwork',700.00,0.00,25,5,'N',25)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (45,'Grain','grain',1.72,0.00,15000,3000,'N',15000)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (46,'Grapes','grapes',0.00,4.00,5000,1000,'N',5000)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (47,'Gut','gut',1.52,1.00,20000,4000,'N',20000)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (25,'Coal','coal',426.56,1.00,15000,3000,'N',15000)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (26,'Copper','copper',4.11,6.70,5000,1000,'N',5000)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (27,'Copper Ore','copper.ore',3.18,1.86,5000,1000,'N',5000)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (28,'Cotton','cotton',178.80,1.00,2500,500,'N',2500)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (29,'Cauldron','cauldron',5.00,0.00,1250,250,'N',1250)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (30,'Cuirass','cuirass',0.00,180.00,100,20,'N',100)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (31,'Bone Cuirboilli','cuirboillibone',50.00,70.00,150,30,'N',150)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (32,'Dogs','dogs',115.40,418.82,150,30,'N',150)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (33,'Drums','drums',27.00,35.00,250,50,'N',250)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (35,'Ewer','ewer',9.00,0.00,250,50,'N',250)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (36,'Falchions','falchions',54.00,85.00,500,100,'N',500)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (38,'Flutes','flutes',12.00,15.00,250,50,'N',250)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (39,'Frames','frame',2.50,5.00,2500,500,'N',2500)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (40,'Furs','furs',2.42,2.48,2500,500,'N',2500)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (41,'Glass Pipe','glasspipe',2.00,0.00,750,150,'N',750)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (42,'Goats','goat',1.00,176.80,10000,2000,'N',10000)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (43,'Gold','gold',1605.88,5.66,1500,300,'N',1500)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (44,'Goldwork','goldwork',700.00,0.00,25,5,'N',25)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (45,'Grain','grain',1.72,0.00,15000,3000,'N',15000)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (46,'Grapes','grapes',0.00,4.00,5000,1000,'N',5000)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (37,'Flour','flour',3.00,0.00,10000,2000,'N',10000)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (12,'Bone Spear','bonespear',57.04,1.00,1500,300,'N',1500)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (13,'Bow','bow',250.14,5.38,500,100,'N',500)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (14,'Brandy','brandy',3872.25,0.00,175,35,'N',175)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (15,'Iron Breastplate','ironbreastplate',0.00,425.00,50,10,'N',50)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (16,'Brass','brass',3.20,11.71,2500,500,'N',2500)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (17,'Bronze','bronze',18.75,31.25,2500,500,'N',2500)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (18,'Bronzestatue','bronzestatue',10000.00,0.00,5,1,'N',5)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (19,'Candles','candles',70.00,0.00,150,30,'N',150)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (21,'Cattle','cattle',1.00,1.00,500,100,'N',500)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (22,'Carpet','carpet',2949.60,0.00,60,12,'N',60)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (24,'Cloth','cloth',234.00,156.25,150,30,'N',150)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (1,'Adze','adze',65.00,0.00,200,40,'N',200)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (2,'Ale','ale',1178.82,0.00,200,40,'N',200)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (3,'Arbalest','arbalest',0.00,1152.82,75,15,'N',75)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (4,'Iron Axe','ironaxe',58.88,149.45,250,50,'N',250)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (5,'Ballistae','ballistae',250.00,400.00,50,10,'N',50)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (6,'Bark','bark',0.00,1.01,5000,1000,'N',5000)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (7,'Barrels','barrels',17.92,97.65,250,50,'N',250)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (8,'Beads','beads',4.00,0.00,1500,300,'N',1500)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (9,'Beaker','beaker',26.73,0.00,1000,200,'N',1000)");
    $db->Execute("INSERT INTO $dbtables[fair] VALUES (11,'Bones','bones',65.45,1.00,25000,5000,'N',25000)");
    echo " Done!<BR>";
    flush();
    echo "Creating farm_activities table....";
    flush();
    $db->Execute("DROP TABLE IF EXISTS $dbtables[farm_activities]");
    $db->Execute("CREATE TABLE $dbtables[farm_activities] ("
                ."`id` int(11) NOT NULL auto_increment,"
                ."`clanid` int(4) unsigned zerofill NOT NULL default '0000',"
                ."`tribeid` decimal(6,2) unsigned zerofill NOT NULL default '0000.00',"
                ."`hex_id` int(5) NOT NULL default '0',"
                ."`crop` varchar(11) NOT NULL default '',"
                ."`action` varchar(11) NOT NULL default '',"
                ."`actives` int(11) NOT NULL default '0',"
                ."`skill` smallint(2) NOT NULL default '0',"
                ."PRIMARY KEY `id` (`id`)"
                .") TYPE=MyISAM");
    echo " Done!<BR>";
    flush();
    echo "Creating farming table....";
    flush();
    $db->Execute("DROP TABLE IF EXISTS $dbtables[farming]");
    $db->Execute("CREATE TABLE $dbtables[farming] ("
                ."`cropid` int(11) NOT NULL auto_increment,"
                ."`clanid` int(4) unsigned zerofill NOT NULL default '0000',"
                ."`hex_id` int(5) NOT NULL default '0',"
                ."`crop` varchar(10) NOT NULL default '',"
                ."`status` varchar(10) NOT NULL default '',"
                ."`acres` int(11) NOT NULL default '0',"
                ."`skill` smallint(2) NOT NULL default '0',"
                ."`month` smallint(2) NOT NULL default '0',"
                ."`harvest` int(11) NOT NULL default '0',"
                ." PRIMARY KEY `cropid` (`cropid`)"
                .") TYPE=MyISAM");
    echo " Done!<BR>";
    flush();
    echo "Creating game_date table....";
    flush();
    $db->Execute("DROP TABLE IF EXISTS $dbtables[game_date]");
    $db->Execute("CREATE TABLE $dbtables[game_date] ("
                ."`date_id` int(11) NOT NULL auto_increment,"
                ."`type` varchar(7) NOT NULL default '',"
                ."`count` int(4) NOT NULL default '0',"
                ."PRIMARY KEY `date_id` (`date_id`)"
                .") TYPE=MyISAM");
    $day = rand(1, 30);
    $month = rand(1, 12);
    $year = rand(100, 1500);
    $db->Execute("INSERT INTO $dbtables[game_date]  VALUES(1,'day','$day')");
    $db->Execute("INSERT INTO $dbtables[game_date]  VALUES(2,'month','$month')");
    $db->Execute("INSERT INTO $dbtables[game_date]  VALUES(3,'year','$year')");
    $db->Execute("INSERT INTO $dbtables[game_date]  VALUES(4,'season','')");
    $db->Execute("INSERT INTO $dbtables[game_date]  VALUES(5,'weather',0)");
    echo " Done!<BR>";
    flush();
    echo "Creating garrisons table....";
    flush();
    $db->Execute("DROP TABLE IF EXISTS $dbtables[garrisons]");
    $db->Execute("CREATE TABLE $dbtables[garrisons] ("
                ."`garid` int(11) NOT NULL auto_increment,"
                ."`hex_id` int(11) NOT NULL default '0',"
                ."`clanid` int(4) unsigned zerofill NOT NULL default '0000',"
                ."`tribeid` decimal(6,2) unsigned zerofill NOT NULL default '0000.00',"
                ."`force` int(6) NOT NULL default '0',"
                ."`experience` decimal(6,2) NOT NULL default '1.00',"
                ."`terrainsp` varchar(5) NOT NULL default 'pr',"
                ."`exp` decimal(6,2) NOT NULL default '1.00',"
                ."`horses` int(6) NOT NULL default '0',"
                ."`weapon1` varchar(25) default NULL,"
                ."`weapon2` varchar(25) default NULL,"
                ."`head_armor` varchar(25) default NULL,"
                ."`torso_armor` varchar(25) default NULL,"
                ."`otorso_armor` varchar(25) default NULL,"
                ."`legs_armor` varchar(25) default NULL,"
                ."`shield` varchar(25) default NULL,"
                ."`horse_armor` varchar(25) default NULL,"
                ."`trooptype` set('A','Q','B','C','I') NOT NULL default 'I',"
                ."PRIMARY KEY `garid` (`garid`)"
                .") TYPE=MyISAM");
    echo " Done!<BR>";
    flush();
    echo "Creating skill_table table....";
    flush();
    $db->Execute("DROP TABLE IF EXISTS $dbtables[skill_table]");
    $db->Execute("CREATE TABLE $dbtables[skill_table] ("
                ."`skill_id` int(11) NOT NULL auto_increment,"
                ."`abbr` varchar(5) NOT NULL default '',"
                ."`long_name` varchar(15) NOT NULL default '',"
                ."`group` set('a','b','c') NOT NULL default '',"
                ."`auto` set('Y','N') NOT NULL default 'N',"
                ."`min_level` int(2) NOT NULL default '1',"
                ."`level_cap` set('Y','N') NOT NULL default 'N',"
                ."`morale` set('Y','N') NOT NULL default 'N',"
                ."`display` varchar(15) NOT NULL default '',"
                ."`arch_animism` enum('Y','N') NOT NULL default 'N',"
                ."`arch_totemism` enum('Y','N') NOT NULL default 'N',"
                ."`arch_pantheism` enum('Y','N') NOT NULL default 'N',"
                ."`arch_polytheism` enum('Y','N') NOT NULL default 'N',"
                ."`arch_henotheism` enum('Y','N') NOT NULL default 'N',"
                ."`arch_dualism` enum('Y','N') NOT NULL default 'N',"
                ."`arch_monotheism` enum('Y','N') NOT NULL default 'N',"
                ."`arch_panentheism` enum('Y','N') NOT NULL default 'N',"
                ."`exc_inclusive` enum('Y','N') NOT NULL default 'N',"
                ."`exc_exclusive` enum('Y','N') NOT NULL default 'N',"
                ."`exc_plural` enum('Y','N') NOT NULL default 'N',"
                ."`prost_none` enum('Y','N') NOT NULL default 'N',"
                ."`prost_mild` enum('Y','N') NOT NULL default 'N',"
                ."`prost_strong` enum('Y','N') NOT NULL default 'N',"
                ."PRIMARY KEY `skill_id` (`skill_id`)"
                .") TYPE=MyISAM");

    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (1,'arm','Armor','a','N',1,'N','N','Armor','N','N','N','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (2,'bon','Boning','a','Y',1,'Y','N','Boning','Y','Y','Y','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (3,'bnw','Bonework','a','N',1,'N','N','Bonework','Y','Y','Y','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (4,'cur','Curing','a','N',1,'Y','N','Curing','Y','Y','Y','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (5,'dre','Dressing','a','N',1,'Y','N','Dressing','Y','Y','Y','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (6,'fish','Fishing','a','Y',1,'N','N','Fishing','Y','Y','Y','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (7,'flet','Fletching','a','N',1,'Y','N','Fletching','Y','Y','Y','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (8,'for','Forestry','a','N',1,'Y','N','Forestry','Y','Y','Y','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (9,'fur','Furrier','a','Y',0,'N','N','Furrier','Y','Y','Y','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (10,'gut','Gutting','a','Y',1,'Y','N','Gutting','Y','Y','Y','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (11,'herd','Herding','a','N',0,'N','N','Herding','Y','Y','Y','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (12,'hunt','Hunting','a','N',0,'N','N','Hunting','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','N','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (13,'jew','Jewelry','a','Y',1,'N','N','Jewelry','N','N','Y','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (14,'ltr','Leatherwork','a','N',1,'N','N','Leatherwork','Y','Y','Y','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (15,'mtl','Metalwork','a','N',1,'N','N','Metalwork','N','N','Y','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (16,'min','Mining','a','N',0,'N','N','Mining','N','N','N','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (17,'pot','Pottery','a','Y',1,'N','N','Pottery','Y','Y','Y','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (18,'qry','Quarrying','a','N',1,'Y','N','Quarrying','N','N','N','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (19,'salt','Salting','a','Y',1,'N','N','Salting','N','N','Y','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (20,'sew','Sewing','a','N',1,'N','N','Sewing','N','N','Y','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (21,'seq','Siege Equipment','a','N',1,'N','N','Siege Equipment','N','N','N','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (22,'skn','Skinning','a','N',1,'Y','N','Skin/Gut/Bone','Y','Y','Y','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (23,'tan','Tanning','a','N',1,'Y','N','Tanning','Y','Y','Y','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (24,'wax','Waxworking','a','N',1,'N','N','Waxworking','Y','Y','N','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (25,'wpn','Weapons','a','N',1,'N','N','Weapons','N','N','N','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (26,'wv','Weaving','a','N',1,'N','N','Weaving','Y','Y','Y','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (27,'wha','Whaling','a','Y',1,'N','N','Whaling','N','N','N','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (28,'wd','Woodwork','a','N',1,'N','N','Woodwork','N','N','N','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (29,'adm','Administration','b','Y',1,'N','Y','Administration','N','N','N','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (30,'arc','Archery','b','Y',1,'N','N','Archery','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','N','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (31,'ath','Atheism','b','Y',1,'N','N','Atheism','N','N','N','N','N','N','N','N','N','N','N','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (32,'capt','Captaincy','b','Y',1,'N','N','Captaincy','N','N','N','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (33,'char','Chariotry','b','Y',1,'N','N','Chariotry','N','N','N','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (34,'com','Combat','b','Y',1,'N','N','Combat','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','N','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (35,'dip','Diplomacy','b','Y',1,'N','Y','Diplomacy','N','N','N','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (36,'eco','Economics','b','Y',1,'N','Y','Economics','N','N','N','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (37,'heal','Healing','b','Y',1,'N','N','Healing','Y','Y','Y','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (38,'hvyw','Heavy Weapons','b','Y',1,'N','N','Heavy Weapons','N','N','N','Y','Y','Y','Y','Y','Y','Y','N','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (39,'hor','Horsemanship','b','Y',1,'N','N','Horsemanship','Y','Y','N','Y','Y','Y','Y','Y','Y','Y','N','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (40,'ldr','Leadership','b','Y',0,'N','Y','Leadership','N','N','N','Y','Y','Y','Y','Y','Y','Y','N','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (41,'mar','Mariner','b','Y',1,'N','N','Mariner','N','N','Y','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (42,'nav','Navigation','b','Y',1,'N','N','Navigation','N','N','N','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (43,'pol','Politics','b','Y',1,'N','Y','Politics','N','N','N','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (44,'rel','Religion','b','Y',1,'N','Y','Religion','N','N','Y','N','N','N','N','N','N','N','N','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (45,'row','Rowing','b','Y',1,'N','N','Rowing','N','N','N','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (46,'sail','Sailing','b','Y',1,'N','N','Sailing','N','N','N','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (47,'sct','Scouting','b','Y',1,'N','N','Scouting','Y','Y','Y','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (48,'sea','Seamanship','b','Y',1,'N','N','Seamanship','N','N','Y','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (49,'sec','Security','b','Y',1,'N','N','Security','N','N','N','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (50,'shw','Shipwright','b','Y',1,'N','N','Shipwright','N','N','N','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (51,'slv','Slavery','b','Y',1,'N','N','Slavery','N','N','N','Y','Y','Y','Y','Y','Y','Y','N','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (52,'spy','Spying','b','Y',1,'N','N','Spying','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','N','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (53,'tac','Tactics','b','Y',1,'N','N','Tactics','N','N','N','Y','Y','Y','Y','Y','Y','Y','N','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (54,'tor','Torture','b','Y',1,'N','N','Torture','Y','Y','N','Y','Y','Y','Y','Y','Y','Y','N','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (55,'tri','Triball','b','Y',1,'N','N','Triball','N','N','N','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (56,'alc','Alchemy','c','Y',1,'N','N','Alchemy','N','N','Y','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (57,'api','Apiarism','c','N',1,'N','N','Apiarism','Y','Y','Y','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (58,'art','Art','c','Y',1,'N','Y','Art','N','N','Y','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (59,'astr','Astronomy','c','Y',1,'N','N','Astronomy','Y','Y','Y','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (60,'bak','Baking','c','N',1,'Y','N','Baking','N','N','N','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (61,'blub','Blubberwork','c','Y',1,'N','N','Blubberwork','N','N','N','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (62,'brk','Brick Making','c','Y',1,'N','N','Brick Making','N','N','N','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (63,'cook','Cooking','c','Y',1,'N','N','Cooking','N','N','Y','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (64,'dan','Dance','c','Y',1,'N','Y','Dance','Y','Y','Y','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (65,'dis','Distilling','c','N',1,'N','N','Distilling','N','N','Y','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (66,'eng','Engineering','c','N',1,'N','N','Engineering','N','N','N','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (67,'farm','Farming','c','N',1,'N','N','Farming','Y','Y','Y','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (68,'flen','Flensing','c','Y',1,'N','N','Flensing','Y','Y','Y','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (69,'lit','Literacy','c','Y',1,'N','Y','Literacy','N','N','N','N','N','N','N','N','N','N','N','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (70,'mtnb','Maintain Boats','c','Y',1,'N','N','Maintain Boats','N','N','N','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (71,'mil','Milling','c','Y',1,'N','N','Milling','N','N','Y','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (72,'mus','Music','c','Y',1,'N','Y','Music','Y','Y','Y','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (73,'peel','Peeling','c','Y',1,'N','N','Peeling','N','N','Y','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (74,'ref','Refining','c','N',1,'N','N','Refining','N','N','N','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (75,'res','Research','c','Y',1,'N','N','Research','N','N','N','N','N','N','N','N','N','N','N','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (76,'san','Sanitation','c','Y',1,'N','Y','Sanitation','Y','Y','Y','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (77,'seek','Seeking','c','N',0,'N','N','Seeking','Y','Y','Y','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (78,'shb','Shipbuilding','c','Y',1,'N','N','Shipbuilding','N','N','N','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (79,'stn','Stonework','c','N',1,'N','N','Stonework','Y','Y','Y','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (80,'glss','Glasswork','c','Y',1,'N','N','Glasswork','N','N','Y','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (81,'fctl','Fire Control','c','Y',1,'N','N','Fire Control','N','N','Y','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    $db->Execute("INSERT INTO $dbtables[skill_table] "
                ."VALUES (82,'chmk','Charcoal Making','c','Y',1,'N','N','Charcoal Making','N','N','Y','Y','Y','Y','Y','Y','Y','N','Y','N','N','N')");
    echo " Done!<BR>";
    flush();
    echo "Checking skills table....";
    flush();
    $db->Execute("DROP TABLE IF EXISTS $dbtables[skills]");
    $db->Execute("CREATE TABLE $dbtables[skills] ("
                ."`entry_id` int(11) NOT NULL auto_increment,"
                ."`abbr` varchar(5) NOT NULL default '',"
                ."`long_name` varchar(15) NOT NULL default '',"
                ."`group` char(1) NOT NULL default '',"
                ."`tribeid` decimal(6,2) unsigned zerofill NOT NULL default '0000.00',"
                ."`level` smallint(2) NOT NULL default '0',"
                ."`turn_done` set('Y','N') NOT NULL default 'N',"
                ."PRIMARY KEY `skill_id` (`entry_id`)"
                .") TYPE=MyISAM");
    echo " Done!<BR>";
    flush();
    echo "Checking product_table table....";
    flush();
    $db->Execute("DROP TABLE IF EXISTS $dbtables[product_table]");
    $db->Execute("CREATE TABLE $dbtables[product_table] ("
                ."`prod_id` int(4) NOT NULL auto_increment,"
                ."`long_name` varchar(20) NOT NULL default '',"
                ."`proper` varchar(20) NOT NULL default '',"
                ."`weapon` set('Y','N') NOT NULL default 'N',"
                ."`armor` set('Y','N') NOT NULL default 'N',"
                ."`skill_abbr` text NOT NULL,"
                ."`skill_level` int(11) NOT NULL default '0',"
                ."`material` varchar(20) NOT NULL default '',"
                ."`include` set('Y','N') NOT NULL default '',"
                ."`weight` decimal(6,2) NOT NULL default '0.00',"
                ."PRIMARY KEY `prod_id` (`prod_id`)"
                .") TYPE=MyISAM");

    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (2,'ale','Ale','N','N','dis',2,'','Y',8.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (3,'arbalest','Arbalest','Y','N','wpn',8,'','Y',2.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (5,'bones','Bones','N','N','bon',0,'','Y',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (6,'bow','Bow','Y','N','wpn',1,'staves','Y',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (7,'ballistae','Ballistae','Y','N','seq',8,'','Y',300.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (8,'barrels','Barrels','N','N','mtl',3,'bronze','Y',10.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (9,'longboat','Longboats','N','N','shw',1,'','Y',0.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (10,'brandy','Brandy','N','N','dis',9,'','Y',8.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (12,'candles','Candles','N','N','wax',2,'','Y',20.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (13,'canoe','Canoe','N','N','wd',6,'','Y',200.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (14,'ironchain','Iron Chain','N','Y','arm',6,'iron','Y',18.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (15,'cloth','Cloth','N','N','sew',4,'','Y',15.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (16,'cotton','Cotton','N','N','farm',1,'','Y',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (17,'cuirass','Cuirass','N','Y','arm',4,'leather','Y',20.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (18,'cuirboillibone','Bone Cuirboilli','N','Y','bnw',7,'bones','Y',8.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (19,'drums','Drums','N','N','mus',1,'','Y',10.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (20,'ewer','Ewer','N','N','pot',1,'','Y',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (21,'falchions','Falchions','Y','N','wpn',4,'iron','Y',5.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (22,'flour','Flour','N','N','mil',0,'','Y',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (23,'flutes','Flutes','N','N','mus',4,'','Y',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (24,'furs','Furs','N','N','fur',0,'','Y',2.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (25,'goldwork','Goldwork','N','N','jew',5,'','Y',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (26,'grain','Grain','N','N','farm',1,'','Y',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (27,'grapes','Grapes','N','N','farm',1,'','Y',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (28,'gut','Gut','N','N','gut',1,'','Y',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (29,'haube','Haube','N','Y','arm',3,'bronze','Y',3.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (30,'heaters','Heaters','N','Y','ltr',2,'leather','Y',4.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (31,'herbs','Herbs','N','N','seek',0,'','Y',0.10)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (32,'hives','Hives','N','N','seek',0,'','Y',20.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (33,'horn','Horn','N','N','mus',3,'','Y',3.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (34,'horsebow','Horsebow','Y','N','wpn',6,'staves','Y',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (35,'honey','Honey','N','N','api',1,'','Y',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (36,'inlay','Inlay','N','N','jew',8,'','Y',0.10)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (37,'jar','Jar','N','N','pot',3,'','Y',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (38,'jerkin','Jerkin','N','Y','ltr',3,'leather','Y',5.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (39,'kayak','Kayak','N','N','ltr',8,'leather','Y',20.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (40,'lggalley','Large Galley','N','N','shw',6,'','Y',0.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (41,'leather','Leather','N','N','tan',1,'','Y',2.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (42,'mead','Mead','N','N','dis',4,'','Y',8.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (43,'net','Net','N','N','wv',3,'','Y',10.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (44,'ornament','Ornament','N','N','jew',3,'','Y',0.10)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (45,'palanquin','Palanquin','N','N','wd',10,'','Y',20.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (46,'parchment','Parchment','N','N','wax',1,'','Y',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (47,'picks','Picks','Y','N','mtl',3,'bronze','Y',3.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (48,'plow','Plow','N','N','mtl',6,'bronze','Y',100.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (49,'provs','Provisions','N','N','hunt',0,'','Y',10.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (50,'rake','Rake','N','N','wd',3,'','Y',0.50)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (51,'rope','Rope','N','N','ltr',4,'leather','Y',10.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (52,'ring','Ring Mail','N','Y','sew',3,'iron','Y',15.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (53,'rum','Rum','N','N','dis',8,'','Y',8.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (54,'scutum','Scutum','N','Y','arm',2,'bronze','Y',5.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (55,'shaft','Shaft','N','N','wpn',1,'','Y',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (56,'shovel','Shovel','Y','N','mtl',2,'bronze','Y',2.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (57,'skins','Skins','N','N','skn',0,'','Y',2.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (58,'scale','Scale Armor','N','Y','sew',3,'iron','Y',15.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (59,'sculpture','Sculpture','N','N','stn',5,'','Y',150.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (60,'woodenshield','Wooden Shield','N','Y','arm',2,'logs','Y',5.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (61,'sling','Sling','Y','N','ltr',2,'leather','Y',0.50)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (62,'smgalley','Small Galley','N','N','shw',4,'','Y',0.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (63,'snares','Snares','N','N','wv',3,'','Y',0.50)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (400,'scrapers','Scrapers','N','N','stn',4,'stones','N',2.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (65,'staves','Staves','Y','N','wpn',1,'','Y',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (66,'strings','Strings','N','N','wax',2,'gut','Y',0.10)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (67,'spice','Spice','N','N','seek',0,'','Y',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (68,'scythe','Scythe','Y','N','mtl',5,'bronze','Y',3.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (69,'sugar','Sugar','N','N','farm',1,'','Y',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (71,'tobacco','Tobacco','N','N','farm',3,'','Y',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (72,'traps','Traps','N','N','mtl',2,'bronze','Y',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (73,'trinket','Trinket','N','N','jew',1,'','Y',0.20)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (74,'urn','Urn','N','N','pot',5,'','Y',2.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (75,'wagon','Wagon','N','N','wd',3,'','Y',300.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (76,'wax','Wax','N','N','api',1,'','Y',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (77,'wine','Wine','N','N','dis',6,'','Y',8.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (85,'logs','Logs','N','N','for',1,'','Y',200.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (81,'boneaxe','Bone Axe','Y','N','bnw',1,'bones','Y',4.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (82,'bonespear','Bone Spear','Y','N','bnw',3,'bones','Y',3.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (83,'boneframe','Bone Frame','N','N','bnw',4,'bones','Y',2.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (84,'bonearmor','Bone Armor','N','Y','bnw',8,'bones','Y',10.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (86,'hood','Hoods','N','Y','ltr',1,'leather','Y',2.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (87,'trews','Trews','N','Y','ltr',3,'leather','Y',2.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (88,'backpack','Backpack','N','N','ltr',4,'leather','Y',2.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (89,'whip','Whip','Y','N','ltr',5,'leather','Y',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (90,'saddlebags','Saddlebags','N','N','ltr',5,'leather','Y',3.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (91,'saddle','Saddle','N','N','ltr',6,'leather','Y',8.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (92,'pellets','Pellets','Y','N','mtl',1,'bronze','Y',0.50)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (93,'quarrels','Quarrels','Y','N','mtl',2,'bronze','Y',0.20)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (94,'mattock','Mattock','N','N','mtl',3,'bronze','Y',8.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (95,'shackles','Shackles','N','N','mtl',4,'bronze','Y',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (96,'adze','Adze','N','N','mtl',4,'bronze','Y',2.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (97,'hoe','Hoe','N','N','mtl',4,'bronze','Y',2.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (98,'cauldron','Cauldron','N','N','mtl',8,'bronze','Y',100.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (99,'glasspipe','Glass Pipe','N','N','mtl',9,'bronze','Y',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (100,'trumpet','Trumpets','N','N','mus',6,'','Y',4.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (101,'harp','Harp','N','N','mus',7,'','Y',10.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (102,'lutes','Lutes','N','N','mus',8,'','Y',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (103,'bladder','Bladders','N','N','sew',2,'','Y',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (105,'ladder','Ladder','N','N','seq',1,'','Y',20.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (106,'ram','Ram','N','N','seq',21,'','Y',0.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (107,'assbridge','Assault Bridge','N','N','seq',21,'','Y',0.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (108,'tower','Tower','N','N','seq',21,'stones','Y',0.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (109,'catapult','Catapult','Y','N','seq',6,'','Y',1000.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (110,'stoneaxe','Stone Axe','Y','N','stn',2,'stones','Y',4.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (111,'stonespear','Stone Spear','Y','N','stn',4,'stones','Y',3.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (112,'millstone','Mill Stone','N','N','stn',6,'stones','Y',500.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (113,'statue','Statue','N','N','stn',8,'','Y',500.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (114,'cuirboilli','Cuirboilli','N','Y','wax',4,'leather','Y',8.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (115,'sling','Sling','Y','N','wpn',1,'leather','N',0.50)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (116,'spetum','Spetum','Y','N','wpn',1,'bronze','Y',3.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (118,'longbow','Long bow','Y','N','wpn',13,'','Y',1.50)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (119,'rope','Rope','N','N','wv',1,'','N',10.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (120,'sling','Sling','Y','N','wv',2,'leather','N',0.50)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (121,'rug','Rug','N','N','wv',4,'','Y',50.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (122,'cloth','Cloth','N','N','wv',5,'','N',15.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (123,'carpet','Carpet','N','N','wv',6,'','Y',100.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (124,'tapestry','Tapestry','N','N','wv',8,'','Y',250.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (126,'frame','Frames','N','N','wd',2,'','Y',2.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (127,'structure','Structure','N','N','wd',8,'','Y',0.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (128,'totem','Totem','N','N','wd',8,'','Y',0.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (129,'bricks','Bricks','N','N','brk',1,'','Y',2.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (130,'bread','Bread','N','N','bak',1,'','Y',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (132,'leather','Leather','N','N','dre',1,'','N',2.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (133,'leather','Leather','N','N','cur',1,'','N',2.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (134,'charcoal','Charcoal','N','N','chmk',1,'','Y',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (136,'provs','Provisions','N','N','skn',1,'','N',10.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (137,'wax','Wax','N','N','seek',0,'','N',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (138,'ferry','Ferry','N','N','shw',2,'','Y',0.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (139,'barge','Barge','N','N','shw',3,'','Y',0.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (140,'fisher','Fisher','N','N','shw',2,'','Y',0.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (141,'coaster','Coaster','N','N','shw',3,'','Y',0.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (142,'trader','Trader','N','N','shw',6,'','Y',0.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (143,'medgalley','Medium Galley','N','N','shw',5,'','Y',0.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (144,'longship','Long Ship','N','N','shw',8,'','Y',0.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (145,'merchant','Merchant','N','N','shw',9,'','Y',0.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (146,'warship','War Ship','N','N','shw',9,'','Y',0.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (147,'ore','Ore','N','N','min',0,'','N',0.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (148,'horses','Horses','N','N','seek',0,'','N',0.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (149,'goats','Goats','N','N','seek',0,'','N',0.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (150,'recruit','Recruit','N','N','seek',0,'','N',0.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (151,'elephant','Elephant','N','N','seek',0,'','N',0.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (152,'tradepost','Trade Post','N','N','eng',2,'','N',0.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (153,'meetinghouse','Meeting House','N','N','eng',2,'','N',0.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (154,'apiary','Apiary','N','N','eng',6,'','N',0.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (155,'bakery','Bakery','N','N','eng',3,'','N',0.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (156,'brickworks','Brickworks','N','N','eng',11,'','N',0.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (157,'charhouse','Char House','N','N','eng',5,'','N',0.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (158,'distillery','Distillery','N','N','eng',4,'','N',0.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (159,'mill','Mill','N','N','eng',11,'','N',0.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (160,'refinery','Refinery','N','N','eng',5,'','N',0.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (161,'smokehouse','Smokehouse','N','N','eng',11,'','N',0.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (162,'bark','Bark','N','N','for',1,'','Y',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (163,'stones','Stones','N','N','qry',1,'','Y',100.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (164,'smelter','Smelter','N','N','eng',5,'','N',0.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (165,'bronze','Bronze','N','N','ref',0,'','N',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (175,'zinc','Zinc','N','N','ref',0,'zinc.ore','N',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (174,'coke','Coke','N','N','ref',11,'coal','N',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (173,'brass','Brass','N','N','ref',0,'','N',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (171,'iron','Iron','N','N','ref',0,'iron.ore','N',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (172,'copper','Copper','N','N','ref',0,'copper.ore','N',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (176,'tin','Tin','N','N','ref',0,'tin.ore','N',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (177,'silver','Silver','N','N','ref',0,'silver.ore','N',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (178,'lead','Lead','N','N','ref',0,'lead.ore','N',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (179,'gold','Gold','N','N','ref',0,'gold.ore','N',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (181,'stonewalls10','10\' Stone Walls','N','N','eng',11,'stones','N',0.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (180,'steel','Steel','N','N','ref',12,'iron.ore','N',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (182,'palisade','Wooden Palisade','N','N','eng',11,'','N',0.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (183,'stonewalls15','15\' Stone Walls','N','N','eng',11,'stones','N',0.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (184,'stonewalls20','20\' Stone Walls','N','N','eng',11,'stones','N',0.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (185,'gate','Gate','N','N','eng',11,'','N',0.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (186,'gate_barred','Barred Gate','N','N','eng',11,'','N',0.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (187,'moat','Moat','N','N','eng',3,'','N',0.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (188,'scrapers','Scrapers','N','N','mtl',2,'bronze','Y',2.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (189,'livestock','Livestock','N','N','herd',0,'','N',0.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (373,'steel2chain','Steel_2 Chain','N','Y','arm',16,'steel_2','Y',18.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (372,'steel1chain','Steel_1 Chain','N','Y','arm',14,'steel_1','Y',18.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (371,'steelchain','Steel Chain','N','Y','arm',12,'steel','Y',18.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (368,'steelplate','Steel Plate','N','Y','arm',18,'steel','Y',55.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (369,'steel1plate','Steel_1 Plate','N','Y','arm',18,'steel_1','Y',55.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (370,'steel2plate','Steel_2 Plate','N','Y','arm',18,'steel_2','Y',55.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (367,'ironplate','Iron Plate','N','Y','arm',18,'iron','Y',55.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (366,'steel2breastplate','Steel_2 Breastplate','N','Y','arm',18,'steel_2','Y',55.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (365,'steel1breastplate','Steel_1 Breastplate','N','Y','arm',16,'steel_1','Y',55.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (363,'steel2buckler','Steel_2 Buckler','N','Y','arm',16,'steel_2','Y',7.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (364,'steelbreastplate','Steel Breastplate','N','Y','arm',14,'steel','Y',55.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (362,'steel1buckler','Steel_1 Buckler','N','Y','arm',14,'steel_1','Y',7.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (361,'steelbuckler','Steel Buckler','N','Y','arm',12,'steel','Y',7.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (357,'steelshield','Steel Shield','N','Y','arm',15,'steel','Y',5.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (358,'steel1shield','Steel_1 Shield','N','Y','arm',11,'steel_1','Y',5.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (359,'steel2shield','Steel_2 Shield','N','Y','arm',13,'steel_2','Y',5.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (360,'ironbuckler','Iron Buckler','N','Y','arm',6,'iron','Y',7.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (356,'ironshield','Iron Shield','N','Y','arm',3,'iron','Y',5.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (355,'ironhelm','Iron Helm','N','Y','arm',4,'iron','Y',3.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (354,'steelhelm','Steel Helm','N','Y','arm',16,'steel','Y',3.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (353,'steel1helm','Steel_1 Helm','N','Y','arm',12,'steel_1','Y',3.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (352,'steel2helm','Steel_2 Helm','N','Y','arm',14,'steel_2','Y',3.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (351,'ironbascinet','Iron Bascinet','N','Y','arm',15,'iron','Y',7.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (350,'steelbascinet','Steel Bascinet','N','Y','arm',15,'steel','Y',7.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (346,'none','None','N','Y','arm',0,'','Y',0.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (347,'irongreaves','Iron Greave','N','Y','arm',14,'iron','Y',5.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (349,'steel1bascinet','Steel_1 Bascinet','N','Y','arm',15,'steel_1','Y',7.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (348,'steel2bascinet','Steel_2 Bascinet','N','Y','arm',15,'steel_2','Y',7.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (345,'ironbreastplate','Iron Breastplate','N','Y','arm',8,'iron','Y',55.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (374,'crossbow','Cross Bow','Y','N','wpn',10,'','Y',6.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (375,'repeatingarbalest','Repeating Arbalest','Y','N','wpn',14,'','Y',2.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (376,'steel_1','Steel_1','N','N','ref',14,'iron.ore','N',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (377,'steel_2','Steel_2','N','N','ref',17,'iron.ore','N',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (378,'ironsword','Iron Sword','Y','N','wpn',6,'iron','Y',5.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (379,'beads','Beads','N','N','glss',1,'sand','Y',0.20)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (380,'beaker','Beaker','N','N','glss',4,'sand','Y',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (391,'club','Club','Y','N','wd',1,'logs','Y',4.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (382,'ironaxe','Iron Axe','Y','N','wpn',5,'iron','Y',4.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (383,'ironmace','Iron Mace','Y','N','wpn',3,'iron','Y',7.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (384,'ironspear','Iron Spear','Y','N','wpn',3,'iron','Y',3.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (387,'steelsword','Steel Sword','Y','N','wpn',15,'steel','Y',5.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (388,'steel1sword','Steel_1 Sword','Y','N','wpn',13,'steel_1','Y',5.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (389,'steel2sword','Steel_2 Sword','Y','N','wpn',18,'steel_2','Y',5.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (392,'bronzespear','Bronze Spear','Y','N','wpn',2,'bronze','Y',3.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (393,'bronzeaxe','Bronze Axe','Y','N','wpn',4,'bronze','Y',4.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (394,'steelaxe','Steel Axe','Y','N','wpn',13,'steel','Y',4.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (395,'steelspear','Steel Spear','Y','N','wpn',17,'steel','Y',3.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (396,'steel1axe','Steel_1 Axe','Y','N','wpn',10,'steel_1','Y',4.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (397,'steel1spear','Steel_1 Spear','Y','N','wpn',17,'steel_1','Y',3.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (398,'steel2axe','Steel_2 Axe','Y','N','wpn',13,'steel_2','Y',4.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (399,'steel2spear','Steel_2 Spear','Y','N','wpn',12,'steel_2','Y',3.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (401,'spice','Spice','N','N','farm',20,'','N',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (402,'herbs','Herbs','N','N','farm',17,'','N',0.10)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (403,'corn','Corn','N','N','farm',14,'','N',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (404,'potatoes','Potatoes','N','N','farm',12,'','N',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (405,'flax','Flax','N','N','farm',13,'','N',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (406,'hemp','Hemp','N','N','farm',15,'','N',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (407,'smelter','Smelter','N','N','stn',8,'stones','N',0.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (408,'sulphur','Sulphur','N','N','ref',16,'stones','Y',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (409,'sulphur','Sulphur','N','N','alc',16,'stones','N',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (410,'saltpetre','Saltpetre','N','N','alc',12,'cattle','Y',1.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (411,'oven','Oven','N','N','eng',3,'','N',0.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (412,'still','Still','N','N','eng',4,'','N',0.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (413,'pavis','Pavis','N','Y','seq',1,'logs','Y',15.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (414,'leatherbarding','Leather Barding','N','Y','ltr',6,'leather','Y',15.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (415,'scalebarding','Scale Barding','N','Y','sew',5,'','Y',75.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (416,'ringbarding','Ring Barding','N','Y','sew',5,'','Y',95.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (417,'ironplatebarding','Iron Plate Barding','N','Y','arm',13,'iron','Y',115.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (418,'steelplatebarding','Steel Plate Barding','N','Y','arm',13,'steel','Y',125.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (419,'burner','Burner','N','N','eng',5,'iron','N',0.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (420,'brickworkoven','Brickwork Ovens','N','N','eng',5,'iron','N',0.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (421,'arrows','Arrows','Y','N','flet',1,'iron','Y',0.10)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (422,'ironchainbarding','Iron Chain Barding','N','Y','arm',12,'iron','Y',40.00)");
    $db->Execute("INSERT INTO $dbtables[product_table] VALUES (423,'steelchainbarding','Steel Chain Barding','N','Y','arm',15,'iron','Y',40.00)");
    echo " Done!<BR>";
    flush();
    echo "Checking gd_help table....";
    flush();
    //$db->Execute("DROP TABLE IF EXISTS $dbtables[gd_help]");
    $hdb = $db->Execute("SELECT COUNT(*) FROM $dbtables[gd_help]");
    if( !$hdb )
    {
        echo "Not present: Adding....";
        flush();
        $db->Execute("CREATE TABLE $dbtables[gd_help] ("
                    ."`id` int(10) NOT NULL auto_increment,"
                    ."`type` enum('topic','page','skill','resource') NOT NULL default 'topic',"
                    ."`value` varchar(40) NOT NULL default '',"
                    ."`title` varchar(50) NOT NULL default '',"
                    ."`help` text NOT NULL,"
                    ." PRIMARY KEY `id` (`id`),"
                    ." KEY `title` (`title`),"
                    ." KEY `type` (`type`),"
                    ." KEY `value` (`value`)"
                    .") TYPE=MyISAM");
        $skill = $db->Execute("SELECT * FROM $dbtables[skill_table]");
        while( !$skill->EOF )
        {
            $skillinfo = $skill->fields;
            $db->Execute("INSERT INTO $dbtables[gd_help] "
                        ."VALUES ("
                        ."'',"
                        ."'skill',"
                        ."'$skillinfo[abbr]',"
                        ."'$skillinfo[long_name]',"
                        ."'<ENTRIES-0>')");
            $skill->MoveNext();
        }
        $prod = $db->Execute("SELECT * FROM $dbtables[product_table]");
        while( !$prod->EOF )
        {
            $prodinfo = $prod->fields;
            $db->Execute("INSERT INTO $dbtables[gd_help] "
                        ."VALUES ("
                        ."'',"
                        ."'resource',"
                        ."'$prodinfo[long_name]',"
                        ."'$prodinfo[proper]',"
                        ."'<ENTRIES-0>')");
            $prod->MoveNext();
        }
        $res = $db->Execute("SELECT * FROM $dbtables[gd_resources]");
        while( !$res->EOF )
        {
            $resinfo = $res->fields;
            $db->Execute("INSERT INTO $dbtables[gd_help] "
                        ."VALUES ("
                        ."'',"
                        ."'resource',"
                        ."'$resinfo[name]',"
                        ."'$resinfo[produce]',"
                        ."'<ENTRIES-0>')");
            $res->MoveNext();
        }
        echo " Done!<BR>";
        flush();
    }
    else
    {
        echo " Present!<BR>";
        flush();
    }
//TODO- Do we really need this thing?  - changes to signup process dont use this anymore, is it used anywhere else?
    echo "Creating form_submit table....";
    flush();
    $db->Execute("DROP TABLE IF EXISTS $dbtables[form_submits]");
    $db->Execute("CREATE TABLE $dbtables[form_submits] ("
                ."`formid` VARCHAR( 50 ) NOT NULL ,"
                ."UNIQUE KEY `formid` (`formid`)"
                .") TYPE=MyISAM");
    echo " Done!<BR>";
    flush();
    echo "Creating map_view table....";
    flush();
    $db->Execute("DROP TABLE IF EXISTS $dbtables[map_view]");
    $db->Execute("CREATE TABLE $dbtables[map_view] ("
                ."`clanid` int(4) unsigned zerofill NOT NULL default '0000',"
                ."`times` smallint(2) NOT NULL default '0',"
                ."PRIMARY KEY `clanid` (`clanid`)"
                .") TYPE=MyISAM");
    echo " Done!<BR>";
    flush();
    echo "Creating seeking table....";
    flush();
    $db->Execute("DROP TABLE IF EXISTS $dbtables[seeking]");
    $query = $db->Execute("CREATE TABLE $dbtables[seeking] ("
                ."`id` int(11) NOT NULL auto_increment,"
                ."`clanid` int(4) unsigned zerofill NOT NULL default '0000',"
                ."`tribeid` decimal(6,2) unsigned zerofill NOT NULL default '0000.00',"
                ."`actives` int(3) NOT NULL default '0',"
                ."`horses` int(3) NOT NULL default '0',"
                ."`wagons` int(2) NOT NULL default '0',"
                ."`burden_beasts` int(2) NOT NULL default '0',"
                ."`backpacks` int(3) NOT NULL default '0',"
                ."`saddlebags` int(3) NOT NULL default '0',"
                ."`target` varchar(12) NOT NULL default '',"
                ."PRIMARY KEY `id` (`id`)"
                .") TYPE=MyISAM");
      db_op_result($query,__LINE__,__FILE__);
    echo " Done!<BR>";
    flush();
    echo "Creating structures table....";
    flush();
    $db->Execute("DROP TABLE IF EXISTS $dbtables[structures]");
    $db->Execute("CREATE TABLE $dbtables[structures] ("
                ."`struct_id` int(11) NOT NULL auto_increment,"
                ."`long_name` varchar(15) NOT NULL default '',"
                ."`proper` varchar(15) NOT NULL default '',"
                ."`hex_id` int(5) NOT NULL default '0',"
                ."`tribeid` decimal(6,2) unsigned zerofill NOT NULL default '0000.00',"
                ."`clanid` int(4) unsigned zerofill NOT NULL default '0000',"
                ."`complete` set('Y','N') NOT NULL default 'N',"
                ."`struct_pts` int(3) NOT NULL default '0',"
                ."`max_struct_pts` int(3) NOT NULL default '0',"
                ."`subunit` varchar(15) NOT NULL default '',"
                ."`number` int(11) NOT NULL default '0',"
                ."`used` enum('Y','N') NOT NULL default 'N',"
                ." PRIMARY KEY `struct_id` (`struct_id`)"
                .") TYPE=MyISAM");
    echo " Done!<BR>";
    flush();
    echo "Creating subtribe_id table....";
    flush();
    $db->Execute("DROP TABLE IF EXISTS $dbtables[subtribe_id]");
    $db->Execute("CREATE TABLE $dbtables[subtribe_id] ("
                ."`unique_id` varchar(50) NOT NULL default '0',"
                ."UNIQUE KEY `unique_id` (`unique_id`)"
                .") TYPE=MyISAM");
    echo " Done!<BR>";
    flush();
    echo "Creating scouts table....";
    flush();
    $db->Execute("DROP TABLE IF EXISTS $dbtables[scouts]");
    $db->Execute("CREATE TABLE $dbtables[scouts] ("
                ."`scoutid` int(11) NOT NULL auto_increment,"
                ."`tribeid` decimal(6,2) NOT NULL default '0.00',"
                ."`actives` int(11) NOT NULL default '0',"
                ."`direction` char(2) NOT NULL default '',"
                ."`mounted` set('Y','N') NOT NULL default '',"
                ."`orders` set('P','L') NOT NULL default '',"
                ."PRIMARY KEY  (`scoutid`)"
                .") TYPE=MyISAM");
    echo " Done!<BR>";
    flush();
    echo "Creating resources table....";
    flush();
    $db->Execute("DROP TABLE IF EXISTS $dbtables[resources]");
    $db->execute("CREATE TABLE $dbtables[resources] ("
                ."`tribeid` decimal(6,2) unsigned zerofill NOT NULL default '0000.00',"
                ."`long_name` varchar(15) NOT NULL default '',"
                ."`amount` int(11) NOT NULL default '0',"
                ."`dbname` varchar(15) NOT NULL default '',"
                ."KEY `unitid` (`tribeid`)"
                .") TYPE=MyISAM");
    echo " Done!<BR>";
    flush();
    echo "Creating last_turn table....";
    flush();
    $db->Execute("DROP TABLE IF EXISTS $dbtables[last_turn]");
    $db->Execute("CREATE TABLE $dbtables[last_turn] ("
                ."`id` int(11) NOT NULL auto_increment,"
                ."`tribeid` decimal(6,2) unsigned zerofill NOT NULL default '0000.00',"
                ."`skill_abbr` varchar(5) NOT NULL default '',"
                ."`product` varchar(15) NOT NULL default '',"
                ."`actives` int(25) NOT NULL default '0',"
                ."PRIMARY KEY `id` (`id`)"
                .") TYPE=MyISAM PACK_KEYS=0");
    echo " Done!<BR>";
    flush();
    echo "Creating livestock table....";
    flush();
    $db->Execute("DROP TABLE IF EXISTS $dbtables[livestock]");
    $db->Execute("CREATE TABLE $dbtables[livestock] ("
                ."`liv_id` int(11) NOT NULL auto_increment,"
                ."`tribeid` decimal(6,2) unsigned zerofill NOT NULL default '0000.00',"
                ."`type` varchar(10) NOT NULL default '',"
                ."`amount` int(11) NOT NULL default '0',"
                ."PRIMARY KEY `liv_id` (`liv_id`)"
                .") TYPE=MyISAM");
    echo " Done!<BR>";
    flush();
    echo "Creating products table....";
    flush();
    $db->Execute("DROP TABLE IF EXISTS $dbtables[products]");
    $db->Execute("CREATE TABLE $dbtables[products] ("
                ."`tribeid` decimal(6,2) unsigned zerofill NOT NULL default '0000.00',"
                ."`proper` varchar(20) NOT NULL default '',"
                ."`long_name` varchar(20) NOT NULL default '',"
                ."`amount` int(11) NOT NULL default '0',"
                ."`weapon` set('Y','N') NOT NULL default 'N',"
                ."`armor` set('Y','N') NOT NULL default 'N',"
                ."id bigint(20) unsigned auto_increment,"
                ."PRIMARY KEY id (id),"
                ."KEY `unitid` (`tribeid`)"
                .") TYPE=MyISAM");
    echo " Done!<BR>";
    flush();
    echo "Creating products_used table....";
    flush();
    $db->Execute("DROP TABLE IF EXISTS $dbtables[products_used]");
    $db->Execute("CREATE TABLE $dbtables[products_used] ("
                ."`tribeid` decimal(6,2) unsigned zerofill NOT NULL default '0000.00',"
                ."`amount` int(11) NOT NULL default '0',"
                ."`long_name` varchar(35) NOT NULL default ''"
                .") TYPE=MyISAM");
    echo " Done!<BR>";
    flush();
    echo "Creating poptrans table....";
    flush();
    $db->Execute("DROP TABLE IF EXISTS $dbtables[poptrans]");
    $db->Execute("CREATE TABLE $dbtables[poptrans] ("
                ."`tribeid` decimal(6,2) unsigned zerofill NOT NULL default '0000.00',"
                ."`number` int(11) NOT NULL default '0',"
                ."`actives` decimal(6,2) unsigned zerofill NOT NULL default '0000.00',"
                ."`inactives` decimal(6,2) unsigned zerofill NOT NULL default '0000.00',"
                ."PRIMARY KEY `tribeid` (`tribeid`)"
                .") TYPE=MyISAM");
    echo " Done!<BR>";
    flush();
    echo "Creating logs table....";
    flush();

    echo "Creating mapping table....";
    flush();
    $db->Execute("DROP TABLE IF EXISTS $dbtables[mapping]");
    $db->Execute("CREATE TABLE $dbtables[mapping] ("
                ."`hex_id` int(11) NOT NULL auto_increment,"
                ."`admin_0000` smallint(2) NOT NULL default '0',"
                ."`clanid_0001` smallint(2) NOT NULL default '0',"
                ."PRIMARY KEY `hex_id` (`hex_id`)"
                .") TYPE=MyISAM");
    $n = 1;
    while( $n <= 4096 )
    {
        $db->Execute("INSERT INTO $dbtables[mapping] VALUES( $n, '0', '0' )");
        $n++;
    }
    echo " Done!<BR>";
    flush();
    echo "Creating missile_types table....";
    flush();
    $db->Execute("DROP TABLE IF EXISTS $dbtables[missile_types]");
    $db->Execute("CREATE TABLE $dbtables[missile_types] ("
                ."`mis_id` int(11) NOT NULL auto_increment,"
                ."`type` varchar(20) NOT NULL default '',"
                ."`long_name` varchar(20) NOT NULL default '',"
                ."`value` decimal(3,2) NOT NULL default '0.00',"
                ."`skill_mult` decimal(3,2) NOT NULL default '0.00',"
                ."PRIMARY KEY  (`mis_id`)"
                .") TYPE=MyISAM");
    $db->Execute("INSERT INTO $dbtables[missile_types] "
                ."VALUES("
                ."1,'Bow','bow',4.00,0.20)");
    $db->Execute("INSERT INTO $dbtables[missile_types] "
                ."VALUES("
                ."3,'Horsebow','horsebow',3.25,0.20)");
    $db->Execute("INSERT INTO $dbtables[missile_types] "
                ."VALUES("
                ."4,'Arbalest','arbalest',5.80,0.15)");
    $db->Execute("INSERT INTO $dbtables[missile_types] "
                ."VALUES("
                ."5,'Repeating Arbalest','repeatingarbalest',8.00,0.15)");
    $db->Execute("INSERT INTO $dbtables[missile_types] "
                ."VALUES("
                ."6,'Long Bow','longbow',5.00,0.20)");
    $db->Execute("INSERT INTO $dbtables[missile_types] "
                ."VALUES("
                ."7,'Cross Bow','crossbow',6.00,0.15)");
    $db->Execute("INSERT INTO $dbtables[missile_types] "
                ."VALUES("
                ."8,'Sling','sling',2.50,0.25)");
    echo " Done!<BR>";
    flush();

    echo "Creating messages table....";
    flush();
    $db->Execute("DROP TABLE IF EXISTS $dbtables[messages]");
    $db->Execute("CREATE TABLE $dbtables[messages] ("
                ."`ID` int(11) NOT NULL auto_increment,"
                ."`sender_id` int(4) unsigned zerofill NOT NULL default '0000',"
                ."`recp_id` int(4) unsigned zerofill NOT NULL default '0000',"
                ."`subject` varchar(250) NOT NULL default '',"
                ."`sent` varchar(19) default NULL,"
                ."`message` longtext NOT NULL,"
                ."`notified` enum('Y','N') NOT NULL default 'N',"
                ."PRIMARY KEY  (`ID`)"
                .") TYPE=MyISAM");
    echo " Done!<BR>";
    flush();
    echo "Creating outbox table....";
    flush();
    $db->Execute("DROP TABLE IF EXISTS $dbtables[outbox]");
    $db->Execute("CREATE TABLE $dbtables[outbox] ("
                ."`ID` int(11) NOT NULL auto_increment,"
                ."`sender_id` int(4) unsigned zerofill NOT NULL default '0000',"
                ."`recp_id` int(4) unsigned zerofill NOT NULL default '0000',"
                ."`subject` varchar(250) NOT NULL default '',"
                ."`sent` varchar(19) default NULL,"
                ."`message` longtext NOT NULL,"
                ."`notified` enum('Y','N') NOT NULL default 'N',"
                ."PRIMARY KEY  (`ID`)"
                .") TYPE=MyISAM");
    echo " Done!<BR>";
    flush();
    echo "Creating religions table....";
    flush();
    $db->Execute("DROP TABLE IF EXISTS $dbtables[religions]");
    $db->Execute("CREATE TABLE $dbtables[religions] ("
                ."`relid` int(11) NOT NULL auto_increment,"
                ."`family` mediumint(5) NOT NULL default '0',"
                ."`generation` mediumint(4) NOT NULL default '0',"
                ."`clanid` int(4) unsigned zerofill NOT NULL default '0000',"
                ."`cannibal` set('Y','N') NOT NULL default 'N',"
                ."`rel_abbr` varchar(35) NOT NULL default '',"
                ."`rel_display` varchar(35) NOT NULL default '',"
                ."`holidays` smallint(1) NOT NULL default '0',"
                ."`holiday1` smallint(2) NOT NULL default '0',"
                ."`holiday2` smallint(2) NOT NULL default '0',"
                ."`holiday3` smallint(3) NOT NULL default '0',"
                ."`rel_arch` varchar(15) NOT NULL default '',"
                ."`arch_skill1` varchar(4) NOT NULL default '',"
                ."`arch_skill1_type` set('1','2') NOT NULL default '',"
                ."`arch_skill1_amount` decimal(3,2) NOT NULL default '0.00',"
                ."`arch_skill2` varchar(4) NOT NULL default '',"
                ."`arch_skill2_type` set('1','2') NOT NULL default '',"
                ."`arch_skill2_amount` decimal(3,2) NOT NULL default '0.00',"
                ."`arch_pen1` varchar(4) NOT NULL default '',"
                ."`arch_pen1_type` set('1','2') NOT NULL default '',"
                ."`arch_pen1_amount` decimal(4,3) NOT NULL default '0.000',"
                ."`arch_pen2` varchar(4) NOT NULL default '',"
                ."`arch_pen2_type` set('1','2') NOT NULL default '1',"
                ."`arch_pen2_amount` decimal(4,3) NOT NULL default '0.000',"
                ."`healers` set('Y','N') NOT NULL default '',"
                ."`healer_name` varchar(25) NOT NULL default '',"
                ."`infantry` set('Y','N') NOT NULL default '',"
                ."`infantry_name` varchar(25) NOT NULL default '',"
                ."`calvalry` set('Y','N') NOT NULL default '',"
                ."`calvalry_name` varchar(25) NOT NULL default '',"
                ."`rel_exclude` enum('Inclusivism','Pluralism','Exclusivism') NOT NULL default 'Inclusivism',"
                ."`exclude_skill` varchar(4) NOT NULL default '',"
                ."`exclude_skill_type` set('1','2') NOT NULL default '',"
                ."`exclude_skill_amount` decimal(3,2) NOT NULL default '0.00',"
                ."`rel_prostlytize` enum('None','Mild','Strong') NOT NULL default 'None',"
                ."`pros_skill` varchar(4) NOT NULL default '',"
                ."`pros_skill_type` set('1','2') NOT NULL default '',"
                ."`pros_skill_amount` decimal(3,2) NOT NULL default '0.00',"
                ."`description` text NOT NULL,"
                ."`inf_weapon1` varchar(25) NOT NULL default '',"
                ."`inf_head_armor` varchar(25) NOT NULL default '',"
                ."`inf_torso_armor` varchar(25) NOT NULL default '',"
                ."`inf_otorso_armor` varchar(25) NOT NULL default '',"
                ."`inf_legs_armor` varchar(25) NOT NULL default '',"
                ."`inf_shield` varchar(25) NOT NULL default '',"
                ."`cav_weapon1` varchar(25) NOT NULL default '',"
                ."`cav_head_armor` varchar(25) NOT NULL default '',"
                ."`cav_torso_armor` varchar(25) NOT NULL default '',"
                ."`cav_otorso_armor` varchar(25) NOT NULL default '',"
                ."`cav_legs_armor` varchar(25) NOT NULL default '',"
                ."`cav_shield` varchar(25) NOT NULL default '',"
                ."`cav_horse_armor` varchar(25) NOT NULL default '',"
                ."PRIMARY KEY `relid` (`relid`)"
                .") TYPE=MyISAM");
    echo " Done!<BR>";
    flush();
    echo "Creating reset_date table....";
    flush();
    $db->Execute("DROP TABLE IF EXISTS $dbtables[reset_date]");
    $db->Execute("CREATE TABLE $dbtables[reset_date] ("
                ."`date` datetime NOT NULL default '0000-00-00 00:00:00',"
                ."PRIMARY KEY  (`date`)"
                .") TYPE=MyISAM");
    $stamp = date("Y-m-d H:i:s");
    $db->Execute("INSERT INTO $dbtables[reset_date] "
                ."VALUES('$stamp')");
    echo " Done!<BR>";
    flush();
    echo "Creating gd_rq table....";
    flush();
    $db->Execute("DROP TABLE IF EXISTS $dbtables[gd_rq]");
    $db->Execute("CREATE TABLE $dbtables[gd_rq] ("
                ."`id` int(11) NOT NULL auto_increment,"
                ."`res_type` enum('Intrinsic','Livestock','Product','Resource','Skill','Structure') NOT NULL default 'Skill',"
                ."`res_table` varchar(60) NOT NULL default '',"
                ."`res_idf` varchar(50) default NULL,"
                ."`res_id` int(11) NOT NULL default '0',"
                ."`res_keyf` varchar(50) default NULL,"
                ."`res_key` varchar(255) NOT NULL default '',"
                ."`rq_type` enum('Intrinsic','Livestock','Product','Resource','Skill','Structure','Prod Unit','Actives','Hex ID','Output') NOT NULL default 'Skill',"
                ."`rq_table` varchar(60) NOT NULL default '',"
                ."`rq_idf` varchar(50) default NULL,"
                ."`rq_id` int(11) NOT NULL default '0',"
                ."`rq_keyf` varchar(50) NOT NULL default '',"
                ."`rq_key` varchar(255) NOT NULL default '',"
                ."`rq_val` varchar(255) NOT NULL default '',"
                ."`rq_goods_tribe` enum('Y','N') NOT NULL default 'Y',"
                ."`cl_table` varchar(60) NOT NULL default '',"
                ."`cl_clanidf` varchar(50) default NULL,"
                ."`cl_tribeidf` varchar(50) NOT NULL default '0',"
                ."`cl_keyf` varchar(50) NOT NULL default '',"
                ."`cl_valf` varchar(50) NOT NULL default '',"
                ."`method` int(11) NOT NULL default '1',"
                ."PRIMARY KEY `id` (`id`),"
                ."KEY `res_type` (`res_type`),"
                ."KEY `res_id` (`res_id`),"
                ."KEY `res_key` (`res_key`),"
                ."KEY `rq_type` (`rq_type`),"
                ."KEY `rq_id` (`rq_id`),"
                ."KEY `rq_key` (`rq_key`)"
                .") TYPE=MyISAM ");

    $db->Execute("INSERT INTO $dbtables[gd_rq] "
                ."(`id`, `res_type`, `res_table`, `res_idf`, `res_id`, `res_keyf`, "
                ."`res_key`, `rq_type`, `rq_table`, `rq_idf`, `rq_id`, `rq_keyf`, "
                ."`rq_key`, `rq_val`, `rq_goods_tribe`, `cl_table`, `cl_clanidf`, "
                ."`cl_tribeidf`, `cl_keyf`, `cl_valf`, `method`) "
                ."VALUES (553, 'Product', 'product_table', 'prod_id', 414, 'long_name', 'aardvark', 'Skill', 'skill_table', 'skill_id', 0, 'abbr', 'tst', '1', 'N', 'skills', '', '', 'abbr', '', 1),"
                ."(390, 'Product', 'product_table', 'prod_id', 2, 'long_name', 'ale', 'Prod Unit', 'product_table', 'prod_id', 412, 'long_name', 'still', '1', 'N', 'structures', 'clanid', 'tribeid', 'subunit', 'number', 1),"
                ."(125, 'Product', 'product_table', 'prod_id', 96, 'long_name', 'adze', 'Skill', 'skill_table', 'skill_id', 15, 'abbr', 'mtl', '4', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(126, 'Product', 'product_table', 'prod_id', 96, 'long_name', 'adze', 'Product', 'product_table', 'prod_id', 165, 'long_name', 'bronze', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(1, 'Product', 'product_table', 'prod_id', 2, 'long_name', 'ale', 'Skill', 'skill_table', 'skill_id', 65, 'abbr', 'dis', '1', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(190, 'Product', 'product_table', 'prod_id', 154, 'long_name', 'apiary', 'Skill', 'skill_table', 'skill_id', 66, 'abbr', 'eng', '6', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(2, 'Product', 'product_table', 'prod_id', 3, 'long_name', 'arbalest', 'Skill', 'skill_table', 'skill_id', 25, 'abbr', 'wpn', '8', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(139, 'Product', 'product_table', 'prod_id', 107, 'long_name', 'assbridge', 'Skill', 'skill_table', 'skill_id', 21, 'abbr', 'seq', '21', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(109, 'Product', 'product_table', 'prod_id', 88, 'long_name', 'backpack', 'Skill', 'skill_table', 'skill_id', 14, 'abbr', 'ltr', '4', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(110, 'Product', 'product_table', 'prod_id', 88, 'long_name', 'backpack', 'Product', 'product_table', 'prod_id', 41, 'long_name', 'leather', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(191, 'Product', 'product_table', 'prod_id', 155, 'long_name', 'bakery', 'Skill', 'skill_table', 'skill_id', 66, 'abbr', 'eng', '3', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(6, 'Product', 'product_table', 'prod_id', 7, 'long_name', 'ballistae', 'Skill', 'skill_table', 'skill_id', 21, 'abbr', 'seq', '8', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(175, 'Product', 'product_table', 'prod_id', 139, 'long_name', 'barge', 'Skill', 'skill_table', 'skill_id', 50, 'abbr', 'shw', '3', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(198, 'Product', 'product_table', 'prod_id', 162, 'long_name', 'bark', 'Skill', 'skill_table', 'skill_id', 8, 'abbr', 'for', '1', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(7, 'Product', 'product_table', 'prod_id', 8, 'long_name', 'barrels', 'Skill', 'skill_table', 'skill_id', 15, 'abbr', 'mtl', '3', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(8, 'Product', 'product_table', 'prod_id', 8, 'long_name', 'barrels', 'Product', 'product_table', 'prod_id', 165, 'long_name', 'bronze', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(299, 'Product', 'product_table', 'prod_id', 379, 'long_name', 'beads', 'Skill', 'skill_table', 'skill_id', 80, 'abbr', 'glss', '1', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(300, 'Product', 'product_table', 'prod_id', 379, 'long_name', 'beads', 'Product', 'product_table', 'prod_id', 0, 'long_name', 'sand', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(301, 'Product', 'product_table', 'prod_id', 380, 'long_name', 'beaker', 'Skill', 'skill_table', 'skill_id', 80, 'abbr', 'glss', '4', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(302, 'Product', 'product_table', 'prod_id', 380, 'long_name', 'beaker', 'Product', 'product_table', 'prod_id', 0, 'long_name', 'sand', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(136, 'Product', 'product_table', 'prod_id', 103, 'long_name', 'bladder', 'Skill', 'skill_table', 'skill_id', 20, 'abbr', 'sew', '2', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(102, 'Product', 'product_table', 'prod_id', 84, 'long_name', 'bonearmor', 'Skill', 'skill_table', 'skill_id', 3, 'abbr', 'bnw', '8', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(103, 'Product', 'product_table', 'prod_id', 84, 'long_name', 'bonearmor', 'Product', 'product_table', 'prod_id', 5, 'long_name', 'bones', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(96, 'Product', 'product_table', 'prod_id', 81, 'long_name', 'boneaxe', 'Skill', 'skill_table', 'skill_id', 3, 'abbr', 'bnw', '1', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(97, 'Product', 'product_table', 'prod_id', 81, 'long_name', 'boneaxe', 'Product', 'product_table', 'prod_id', 5, 'long_name', 'bones', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(100, 'Product', 'product_table', 'prod_id', 83, 'long_name', 'boneframe', 'Skill', 'skill_table', 'skill_id', 3, 'abbr', 'bnw', '4', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(101, 'Product', 'product_table', 'prod_id', 83, 'long_name', 'boneframe', 'Product', 'product_table', 'prod_id', 5, 'long_name', 'bones', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(3, 'Product', 'product_table', 'prod_id', 5, 'long_name', 'bones', 'Skill', 'skill_table', 'skill_id', 2, 'abbr', 'bon', '0', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(98, 'Product', 'product_table', 'prod_id', 82, 'long_name', 'bonespear', 'Skill', 'skill_table', 'skill_id', 3, 'abbr', 'bnw', '3', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(99, 'Product', 'product_table', 'prod_id', 82, 'long_name', 'bonespear', 'Product', 'product_table', 'prod_id', 5, 'long_name', 'bones', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(4, 'Product', 'product_table', 'prod_id', 6, 'long_name', 'bow', 'Skill', 'skill_table', 'skill_id', 25, 'abbr', 'wpn', '1', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(5, 'Product', 'product_table', 'prod_id', 6, 'long_name', 'bow', 'Actives', 'tribes', 'curam', 0, 'tribeid', '0002.00', '1', 'N', 'tribes', 'clanid', 'tribeid', 'tribeid', 'curam', 1),"
                ."(10, 'Product', 'product_table', 'prod_id', 10, 'long_name', 'brandy', 'Skill', 'skill_table', 'skill_id', 65, 'abbr', 'dis', '9', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(206, 'Product', 'product_table', 'prod_id', 173, 'long_name', 'brass', 'Skill', 'skill_table', 'skill_id', 74, 'abbr', 'ref', '0', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(168, 'Product', 'product_table', 'prod_id', 130, 'long_name', 'bread', 'Skill', 'skill_table', 'skill_id', 60, 'abbr', 'bak', '1', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(167, 'Product', 'product_table', 'prod_id', 129, 'long_name', 'bricks', 'Skill', 'skill_table', 'skill_id', 62, 'abbr', 'brk', '1', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(192, 'Product', 'product_table', 'prod_id', 156, 'long_name', 'brickworks', 'Skill', 'skill_table', 'skill_id', 66, 'abbr', 'eng', '11', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(201, 'Product', 'product_table', 'prod_id', 165, 'long_name', 'bronze', 'Skill', 'skill_table', 'skill_id', 74, 'abbr', 'ref', '0', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(319, 'Product', 'product_table', 'prod_id', 393, 'long_name', 'bronzeaxe', 'Skill', 'skill_table', 'skill_id', 25, 'abbr', 'wpn', '4', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(320, 'Product', 'product_table', 'prod_id', 393, 'long_name', 'bronzeaxe', 'Actives', 'tribes', 'curam', 0, 'tribeid', '0002.00', '2', 'N', 'tribes', 'clanid', 'tribeid', 'tribeid', 'curam', 1),"
                ."(317, 'Product', 'product_table', 'prod_id', 392, 'long_name', 'bronzespear', 'Skill', 'skill_table', 'skill_id', 25, 'abbr', 'wpn', '2', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(318, 'Product', 'product_table', 'prod_id', 392, 'long_name', 'bronzespear', 'Actives', 'tribes', 'curam', 0, 'tribeid', '0002.00', '2', 'N', 'tribes', 'clanid', 'tribeid', 'tribeid', 'curam', 1),"
                ."(11, 'Product', 'product_table', 'prod_id', 12, 'long_name', 'candles', 'Skill', 'skill_table', 'skill_id', 24, 'abbr', 'wax', '2', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(12, 'Product', 'product_table', 'prod_id', 13, 'long_name', 'canoe', 'Skill', 'skill_table', 'skill_id', 28, 'abbr', 'wd', '6', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(162, 'Product', 'product_table', 'prod_id', 123, 'long_name', 'carpet', 'Skill', 'skill_table', 'skill_id', 26, 'abbr', 'wv', '6', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(142, 'Product', 'product_table', 'prod_id', 109, 'long_name', 'catapult', 'Skill', 'skill_table', 'skill_id', 21, 'abbr', 'seq', '6', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(129, 'Product', 'product_table', 'prod_id', 98, 'long_name', 'cauldron', 'Skill', 'skill_table', 'skill_id', 15, 'abbr', 'mtl', '8', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(130, 'Product', 'product_table', 'prod_id', 98, 'long_name', 'cauldron', 'Product', 'product_table', 'prod_id', 165, 'long_name', 'bronze', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(171, 'Product', 'product_table', 'prod_id', 134, 'long_name', 'charcoal', 'Skill', 'skill_table', 'skill_id', 82, 'abbr', 'chmk', '1', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(193, 'Product', 'product_table', 'prod_id', 157, 'long_name', 'charhouse', 'Skill', 'skill_table', 'skill_id', 66, 'abbr', 'eng', '11', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(15, 'Product', 'product_table', 'prod_id', 15, 'long_name', 'cloth', 'Skill', 'skill_table', 'skill_id', 20, 'abbr', 'sew', '4', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(161, 'Product', 'product_table', 'prod_id', 122, 'long_name', 'cloth', 'Skill', 'skill_table', 'skill_id', 26, 'abbr', 'wv', '5', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(315, 'Product', 'product_table', 'prod_id', 391, 'long_name', 'club', 'Skill', 'skill_table', 'skill_id', 28, 'abbr', 'wd', '1', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(316, 'Product', 'product_table', 'prod_id', 391, 'long_name', 'club', 'Product', 'product_table', 'prod_id', 85, 'long_name', 'logs', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(177, 'Product', 'product_table', 'prod_id', 141, 'long_name', 'coaster', 'Skill', 'skill_table', 'skill_id', 50, 'abbr', 'shw', '3', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(207, 'Product', 'product_table', 'prod_id', 174, 'long_name', 'coke', 'Skill', 'skill_table', 'skill_id', 74, 'abbr', 'ref', '8', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(208, 'Product', 'product_table', 'prod_id', 174, 'long_name', 'coke', 'Product', 'product_table', 'prod_id', 0, 'long_name', 'coal', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(204, 'Product', 'product_table', 'prod_id', 172, 'long_name', 'copper', 'Skill', 'skill_table', 'skill_id', 74, 'abbr', 'ref', '0', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(205, 'Product', 'product_table', 'prod_id', 172, 'long_name', 'copper', 'Product', 'product_table', 'prod_id', 0, 'long_name', 'copper.ore', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(337, 'Product', 'product_table', 'prod_id', 403, 'long_name', 'corn', 'Skill', 'skill_table', 'skill_id', 67, 'abbr', 'farm', '14', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(16, 'Product', 'product_table', 'prod_id', 16, 'long_name', 'cotton', 'Skill', 'skill_table', 'skill_id', 67, 'abbr', 'farm', '1', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(291, 'Product', 'product_table', 'prod_id', 374, 'long_name', 'crossbow', 'Skill', 'skill_table', 'skill_id', 25, 'abbr', 'wpn', '10', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(17, 'Product', 'product_table', 'prod_id', 17, 'long_name', 'cuirass', 'Skill', 'skill_table', 'skill_id', 1, 'abbr', 'arm', '4', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(18, 'Product', 'product_table', 'prod_id', 17, 'long_name', 'cuirass', 'Product', 'product_table', 'prod_id', 41, 'long_name', 'leather', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(150, 'Product', 'product_table', 'prod_id', 114, 'long_name', 'cuirboilli', 'Skill', 'skill_table', 'skill_id', 24, 'abbr', 'wax', '4', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(151, 'Product', 'product_table', 'prod_id', 114, 'long_name', 'cuirboilli', 'Product', 'product_table', 'prod_id', 41, 'long_name', 'leather', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(19, 'Product', 'product_table', 'prod_id', 18, 'long_name', 'cuirboillibone', 'Skill', 'skill_table', 'skill_id', 3, 'abbr', 'bnw', '7', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(20, 'Product', 'product_table', 'prod_id', 18, 'long_name', 'cuirboillibone', 'Product', 'product_table', 'prod_id', 5, 'long_name', 'bones', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(194, 'Product', 'product_table', 'prod_id', 158, 'long_name', 'distillery', 'Skill', 'skill_table', 'skill_id', 66, 'abbr', 'eng', '4', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(21, 'Product', 'product_table', 'prod_id', 19, 'long_name', 'drums', 'Skill', 'skill_table', 'skill_id', 72, 'abbr', 'mus', '1', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(187, 'Product', 'product_table', 'prod_id', 151, 'long_name', 'elephant', 'Skill', 'skill_table', 'skill_id', 77, 'abbr', 'seek', '0', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(22, 'Product', 'product_table', 'prod_id', 20, 'long_name', 'ewer', 'Skill', 'skill_table', 'skill_id', 17, 'abbr', 'pot', '1', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(23, 'Product', 'product_table', 'prod_id', 21, 'long_name', 'falchions', 'Skill', 'skill_table', 'skill_id', 25, 'abbr', 'wpn', '4', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(24, 'Product', 'product_table', 'prod_id', 21, 'long_name', 'falchions', 'Actives', 'tribes', 'curam', 0, 'tribeid', '0002.00', '2', 'N', 'tribes', 'clanid', 'tribeid', 'tribeid', 'curam', 1),"
                ."(174, 'Product', 'product_table', 'prod_id', 138, 'long_name', 'ferry', 'Skill', 'skill_table', 'skill_id', 50, 'abbr', 'shw', '2', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(176, 'Product', 'product_table', 'prod_id', 140, 'long_name', 'fisher', 'Skill', 'skill_table', 'skill_id', 50, 'abbr', 'shw', '2', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(339, 'Product', 'product_table', 'prod_id', 405, 'long_name', 'flax', 'Skill', 'skill_table', 'skill_id', 67, 'abbr', 'farm', '13', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(25, 'Product', 'product_table', 'prod_id', 22, 'long_name', 'flour', 'Skill', 'skill_table', 'skill_id', 71, 'abbr', 'mil', '0', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(26, 'Product', 'product_table', 'prod_id', 23, 'long_name', 'flutes', 'Skill', 'skill_table', 'skill_id', 72, 'abbr', 'mus', '4', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(164, 'Product', 'product_table', 'prod_id', 126, 'long_name', 'frame', 'Skill', 'skill_table', 'skill_id', 28, 'abbr', 'wd', '2', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(27, 'Product', 'product_table', 'prod_id', 24, 'long_name', 'furs', 'Skill', 'skill_table', 'skill_id', 9, 'abbr', 'fur', '0', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(228, 'Product', 'product_table', 'prod_id', 185, 'long_name', 'gate', 'Skill', 'skill_table', 'skill_id', 66, 'abbr', 'eng', '11', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(229, 'Product', 'product_table', 'prod_id', 186, 'long_name', 'gate_barred', 'Skill', 'skill_table', 'skill_id', 66, 'abbr', 'eng', '11', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(131, 'Product', 'product_table', 'prod_id', 99, 'long_name', 'glasspipe', 'Skill', 'skill_table', 'skill_id', 15, 'abbr', 'mtl', '9', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(132, 'Product', 'product_table', 'prod_id', 99, 'long_name', 'glasspipe', 'Product', 'product_table', 'prod_id', 165, 'long_name', 'bronze', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(185, 'Product', 'product_table', 'prod_id', 149, 'long_name', 'goats', 'Skill', 'skill_table', 'skill_id', 77, 'abbr', 'seek', '0', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(217, 'Product', 'product_table', 'prod_id', 179, 'long_name', 'gold', 'Skill', 'skill_table', 'skill_id', 74, 'abbr', 'ref', '0', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(218, 'Product', 'product_table', 'prod_id', 179, 'long_name', 'gold', 'Product', 'product_table', 'prod_id', 0, 'long_name', 'gold.ore', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(28, 'Product', 'product_table', 'prod_id', 25, 'long_name', 'goldwork', 'Skill', 'skill_table', 'skill_id', 13, 'abbr', 'jew', '5', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(29, 'Product', 'product_table', 'prod_id', 26, 'long_name', 'grain', 'Skill', 'skill_table', 'skill_id', 67, 'abbr', 'farm', '1', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(30, 'Product', 'product_table', 'prod_id', 27, 'long_name', 'grapes', 'Skill', 'skill_table', 'skill_id', 67, 'abbr', 'farm', '1', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(31, 'Product', 'product_table', 'prod_id', 28, 'long_name', 'gut', 'Skill', 'skill_table', 'skill_id', 10, 'abbr', 'gut', '1', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(134, 'Product', 'product_table', 'prod_id', 101, 'long_name', 'harp', 'Skill', 'skill_table', 'skill_id', 72, 'abbr', 'mus', '7', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(32, 'Product', 'product_table', 'prod_id', 29, 'long_name', 'haube', 'Skill', 'skill_table', 'skill_id', 1, 'abbr', 'arm', '3', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(33, 'Product', 'product_table', 'prod_id', 29, 'long_name', 'haube', 'Product', 'product_table', 'prod_id', 165, 'long_name', 'bronze', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(34, 'Product', 'product_table', 'prod_id', 30, 'long_name', 'heaters', 'Skill', 'skill_table', 'skill_id', 14, 'abbr', 'ltr', '2', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(35, 'Product', 'product_table', 'prod_id', 30, 'long_name', 'heaters', 'Product', 'product_table', 'prod_id', 41, 'long_name', 'leather', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(340, 'Product', 'product_table', 'prod_id', 406, 'long_name', 'hemp', 'Skill', 'skill_table', 'skill_id', 67, 'abbr', 'farm', '15', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(36, 'Product', 'product_table', 'prod_id', 31, 'long_name', 'herbs', 'Skill', 'skill_table', 'skill_id', 77, 'abbr', 'seek', '0', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(336, 'Product', 'product_table', 'prod_id', 402, 'long_name', 'herbs', 'Skill', 'skill_table', 'skill_id', 67, 'abbr', 'farm', '17', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(37, 'Product', 'product_table', 'prod_id', 32, 'long_name', 'hives', 'Skill', 'skill_table', 'skill_id', 77, 'abbr', 'seek', '0', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(127, 'Product', 'product_table', 'prod_id', 97, 'long_name', 'hoe', 'Skill', 'skill_table', 'skill_id', 15, 'abbr', 'mtl', '4', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(128, 'Product', 'product_table', 'prod_id', 97, 'long_name', 'hoe', 'Product', 'product_table', 'prod_id', 165, 'long_name', 'bronze', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(41, 'Product', 'product_table', 'prod_id', 35, 'long_name', 'honey', 'Skill', 'skill_table', 'skill_id', 57, 'abbr', 'api', '1', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(105, 'Product', 'product_table', 'prod_id', 86, 'long_name', 'hood', 'Skill', 'skill_table', 'skill_id', 14, 'abbr', 'ltr', '1', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(106, 'Product', 'product_table', 'prod_id', 86, 'long_name', 'hood', 'Product', 'product_table', 'prod_id', 41, 'long_name', 'leather', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(38, 'Product', 'product_table', 'prod_id', 33, 'long_name', 'horn', 'Skill', 'skill_table', 'skill_id', 72, 'abbr', 'mus', '3', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(39, 'Product', 'product_table', 'prod_id', 34, 'long_name', 'horsebow', 'Skill', 'skill_table', 'skill_id', 25, 'abbr', 'wpn', '6', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(40, 'Product', 'product_table', 'prod_id', 34, 'long_name', 'horsebow', 'Actives', 'tribes', 'curam', 0, 'tribeid', '0002.00', '1', 'N', 'tribes', 'clanid', 'tribeid', 'tribeid', 'curam', 1),"
                ."(184, 'Product', 'product_table', 'prod_id', 148, 'long_name', 'horses', 'Skill', 'skill_table', 'skill_id', 77, 'abbr', 'seek', '0', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(42, 'Product', 'product_table', 'prod_id', 36, 'long_name', 'inlay', 'Skill', 'skill_table', 'skill_id', 13, 'abbr', 'jew', '8', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(202, 'Product', 'product_table', 'prod_id', 171, 'long_name', 'iron', 'Skill', 'skill_table', 'skill_id', 74, 'abbr', 'ref', '0', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(203, 'Product', 'product_table', 'prod_id', 171, 'long_name', 'iron', 'Product', 'product_table', 'prod_id', 0, 'long_name', 'iron.ore', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(303, 'Product', 'product_table', 'prod_id', 382, 'long_name', 'ironaxe', 'Skill', 'skill_table', 'skill_id', 25, 'abbr', 'wpn', '5', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(304, 'Product', 'product_table', 'prod_id', 382, 'long_name', 'ironaxe', 'Actives', 'tribes', 'curam', 0, 'tribeid', '0002.00', '2', 'N', 'tribes', 'clanid', 'tribeid', 'tribeid', 'curam', 1),"
                ."(245, 'Product', 'product_table', 'prod_id', 351, 'long_name', 'ironbascinet', 'Skill', 'skill_table', 'skill_id', 1, 'abbr', 'arm', '15', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(246, 'Product', 'product_table', 'prod_id', 351, 'long_name', 'ironbascinet', 'Product', 'product_table', 'prod_id', 171, 'long_name', 'iron', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(234, 'Product', 'product_table', 'prod_id', 345, 'long_name', 'ironbreastplate', 'Skill', 'skill_table', 'skill_id', 1, 'abbr', 'arm', '8', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(235, 'Product', 'product_table', 'prod_id', 345, 'long_name', 'ironbreastplate', 'Product', 'product_table', 'prod_id', 171, 'long_name', 'iron', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(263, 'Product', 'product_table', 'prod_id', 360, 'long_name', 'ironbuckler', 'Skill', 'skill_table', 'skill_id', 1, 'abbr', 'arm', '6', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(264, 'Product', 'product_table', 'prod_id', 360, 'long_name', 'ironbuckler', 'Product', 'product_table', 'prod_id', 171, 'long_name', 'iron', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(13, 'Product', 'product_table', 'prod_id', 14, 'long_name', 'ironchain', 'Skill', 'skill_table', 'skill_id', 1, 'abbr', 'arm', '6', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(14, 'Product', 'product_table', 'prod_id', 14, 'long_name', 'ironchain', 'Product', 'product_table', 'prod_id', 171, 'long_name', 'iron', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(237, 'Product', 'product_table', 'prod_id', 347, 'long_name', 'irongreaves', 'Skill', 'skill_table', 'skill_id', 1, 'abbr', 'arm', '14', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(238, 'Product', 'product_table', 'prod_id', 347, 'long_name', 'irongreaves', 'Product', 'product_table', 'prod_id', 171, 'long_name', 'iron', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(253, 'Product', 'product_table', 'prod_id', 355, 'long_name', 'ironhelm', 'Skill', 'skill_table', 'skill_id', 1, 'abbr', 'arm', '4', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(254, 'Product', 'product_table', 'prod_id', 355, 'long_name', 'ironhelm', 'Product', 'product_table', 'prod_id', 171, 'long_name', 'iron', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(305, 'Product', 'product_table', 'prod_id', 383, 'long_name', 'ironmace', 'Skill', 'skill_table', 'skill_id', 25, 'abbr', 'wpn', '3', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(306, 'Product', 'product_table', 'prod_id', 383, 'long_name', 'ironmace', 'Actives', 'tribes', 'curam', 0, 'tribeid', '0002.00', '2', 'N', 'tribes', 'clanid', 'tribeid', 'tribeid', 'curam', 1),"
                ."(277, 'Product', 'product_table', 'prod_id', 367, 'long_name', 'ironplate', 'Skill', 'skill_table', 'skill_id', 1, 'abbr', 'arm', '18', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(278, 'Product', 'product_table', 'prod_id', 367, 'long_name', 'ironplate', 'Product', 'product_table', 'prod_id', 171, 'long_name', 'iron', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(255, 'Product', 'product_table', 'prod_id', 356, 'long_name', 'ironshield', 'Skill', 'skill_table', 'skill_id', 1, 'abbr', 'arm', '3', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(256, 'Product', 'product_table', 'prod_id', 356, 'long_name', 'ironshield', 'Product', 'product_table', 'prod_id', 171, 'long_name', 'iron', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(307, 'Product', 'product_table', 'prod_id', 384, 'long_name', 'ironspear', 'Skill', 'skill_table', 'skill_id', 25, 'abbr', 'wpn', '3', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(308, 'Product', 'product_table', 'prod_id', 384, 'long_name', 'ironspear', 'Actives', 'tribes', 'curam', 0, 'tribeid', '0002.00', '3', 'N', 'tribes', 'clanid', 'tribeid', 'tribeid', 'curam', 1),"
                ."(297, 'Product', 'product_table', 'prod_id', 378, 'long_name', 'ironsword', 'Skill', 'skill_table', 'skill_id', 25, 'abbr', 'wpn', '6', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(298, 'Product', 'product_table', 'prod_id', 378, 'long_name', 'ironsword', 'Actives', 'tribes', 'curam', 0, 'tribeid', '0002.00', '3', 'N', 'tribes', 'clanid', 'tribeid', 'tribeid', 'curam', 1),"
                ."(43, 'Product', 'product_table', 'prod_id', 37, 'long_name', 'jar', 'Skill', 'skill_table', 'skill_id', 17, 'abbr', 'pot', '3', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(44, 'Product', 'product_table', 'prod_id', 38, 'long_name', 'jerkin', 'Skill', 'skill_table', 'skill_id', 14, 'abbr', 'ltr', '3', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(45, 'Product', 'product_table', 'prod_id', 38, 'long_name', 'jerkin', 'Product', 'product_table', 'prod_id', 41, 'long_name', 'leather', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(46, 'Product', 'product_table', 'prod_id', 39, 'long_name', 'kayak', 'Skill', 'skill_table', 'skill_id', 14, 'abbr', 'ltr', '8', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(47, 'Product', 'product_table', 'prod_id', 39, 'long_name', 'kayak', 'Product', 'product_table', 'prod_id', 41, 'long_name', 'leather', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(137, 'Product', 'product_table', 'prod_id', 105, 'long_name', 'ladder', 'Skill', 'skill_table', 'skill_id', 21, 'abbr', 'seq', '1', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(215, 'Product', 'product_table', 'prod_id', 178, 'long_name', 'lead', 'Skill', 'skill_table', 'skill_id', 74, 'abbr', 'ref', '0', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(216, 'Product', 'product_table', 'prod_id', 178, 'long_name', 'lead', 'Product', 'product_table', 'prod_id', 0, 'long_name', 'lead.ore', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(49, 'Product', 'product_table', 'prod_id', 41, 'long_name', 'leather', 'Skill', 'skill_table', 'skill_id', 23, 'abbr', 'tan', '1', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(169, 'Product', 'product_table', 'prod_id', 132, 'long_name', 'leather', 'Skill', 'skill_table', 'skill_id', 5, 'abbr', 'dre', '1', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 2),"
                ."(170, 'Product', 'product_table', 'prod_id', 133, 'long_name', 'leather', 'Skill', 'skill_table', 'skill_id', 4, 'abbr', 'cur', '1', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 3),"
                ."(48, 'Product', 'product_table', 'prod_id', 40, 'long_name', 'lggalley', 'Skill', 'skill_table', 'skill_id', 50, 'abbr', 'shw', '6', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(233, 'Product', 'product_table', 'prod_id', 189, 'long_name', 'livestock', 'Skill', 'skill_table', 'skill_id', 11, 'abbr', 'herd', '0', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(104, 'Product', 'product_table', 'prod_id', 85, 'long_name', 'logs', 'Skill', 'skill_table', 'skill_id', 8, 'abbr', 'for', '1', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(9, 'Product', 'product_table', 'prod_id', 9, 'long_name', 'longboat', 'Skill', 'skill_table', 'skill_id', 50, 'abbr', 'shw', '1', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(156, 'Product', 'product_table', 'prod_id', 118, 'long_name', 'longbow', 'Skill', 'skill_table', 'skill_id', 25, 'abbr', 'wpn', '13', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(180, 'Product', 'product_table', 'prod_id', 144, 'long_name', 'longship', 'Skill', 'skill_table', 'skill_id', 50, 'abbr', 'shw', '8', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(135, 'Product', 'product_table', 'prod_id', 102, 'long_name', 'lutes', 'Skill', 'skill_table', 'skill_id', 72, 'abbr', 'mus', '8', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(121, 'Product', 'product_table', 'prod_id', 94, 'long_name', 'mattock', 'Skill', 'skill_table', 'skill_id', 15, 'abbr', 'mtl', '3', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(122, 'Product', 'product_table', 'prod_id', 94, 'long_name', 'mattock', 'Product', 'product_table', 'prod_id', 165, 'long_name', 'bronze', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(50, 'Product', 'product_table', 'prod_id', 42, 'long_name', 'mead', 'Skill', 'skill_table', 'skill_id', 65, 'abbr', 'dis', '4', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(179, 'Product', 'product_table', 'prod_id', 143, 'long_name', 'medgalley', 'Skill', 'skill_table', 'skill_id', 50, 'abbr', 'shw', '5', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(189, 'Product', 'product_table', 'prod_id', 153, 'long_name', 'meetinghouse', 'Skill', 'skill_table', 'skill_id', 66, 'abbr', 'eng', '2', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(181, 'Product', 'product_table', 'prod_id', 145, 'long_name', 'merchant', 'Skill', 'skill_table', 'skill_id', 50, 'abbr', 'shw', '9', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(195, 'Product', 'product_table', 'prod_id', 159, 'long_name', 'mill', 'Skill', 'skill_table', 'skill_id', 66, 'abbr', 'eng', '11', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(147, 'Product', 'product_table', 'prod_id', 112, 'long_name', 'millstone', 'Skill', 'skill_table', 'skill_id', 79, 'abbr', 'stn', '6', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(148, 'Product', 'product_table', 'prod_id', 112, 'long_name', 'millstone', 'Actives', 'tribes', 'curam', 0, 'tribeid', '0002.00', '10', 'N', 'tribes', 'clanid', 'tribeid', 'tribeid', 'curam', 1),"
                ."(230, 'Product', 'product_table', 'prod_id', 187, 'long_name', 'moat', 'Skill', 'skill_table', 'skill_id', 66, 'abbr', 'eng', '3', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(51, 'Product', 'product_table', 'prod_id', 43, 'long_name', 'net', 'Skill', 'skill_table', 'skill_id', 26, 'abbr', 'wv', '3', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(236, 'Product', 'product_table', 'prod_id', 346, 'long_name', 'none', 'Skill', 'skill_table', 'skill_id', 1, 'abbr', 'arm', '0', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(183, 'Product', 'product_table', 'prod_id', 147, 'long_name', 'ore', 'Skill', 'skill_table', 'skill_id', 16, 'abbr', 'min', '0', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(52, 'Product', 'product_table', 'prod_id', 44, 'long_name', 'ornament', 'Skill', 'skill_table', 'skill_id', 13, 'abbr', 'jew', '3', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(349, 'Product', 'product_table', 'prod_id', 411, 'long_name', 'oven', 'Skill', 'skill_table', 'skill_id', 66, 'abbr', 'eng', '3', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(53, 'Product', 'product_table', 'prod_id', 45, 'long_name', 'palanquin', 'Skill', 'skill_table', 'skill_id', 28, 'abbr', 'wd', '10', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(223, 'Product', 'product_table', 'prod_id', 182, 'long_name', 'palisade', 'Skill', 'skill_table', 'skill_id', 66, 'abbr', 'eng', '11', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(54, 'Product', 'product_table', 'prod_id', 46, 'long_name', 'parchment', 'Skill', 'skill_table', 'skill_id', 24, 'abbr', 'wax', '1', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(351, 'Product', 'product_table', 'prod_id', 413, 'long_name', 'pavis', 'Skill', 'skill_table', 'skill_id', 21, 'abbr', 'seq', '1', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(352, 'Product', 'product_table', 'prod_id', 413, 'long_name', 'pavis', 'Product', 'product_table', 'prod_id', 85, 'long_name', 'logs', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(117, 'Product', 'product_table', 'prod_id', 92, 'long_name', 'pellets', 'Skill', 'skill_table', 'skill_id', 15, 'abbr', 'mtl', '1', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(118, 'Product', 'product_table', 'prod_id', 92, 'long_name', 'pellets', 'Product', 'product_table', 'prod_id', 165, 'long_name', 'bronze', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(55, 'Product', 'product_table', 'prod_id', 47, 'long_name', 'picks', 'Skill', 'skill_table', 'skill_id', 15, 'abbr', 'mtl', '3', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(56, 'Product', 'product_table', 'prod_id', 47, 'long_name', 'picks', 'Product', 'product_table', 'prod_id', 165, 'long_name', 'bronze', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(57, 'Product', 'product_table', 'prod_id', 48, 'long_name', 'plow', 'Skill', 'skill_table', 'skill_id', 15, 'abbr', 'mtl', '6', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(58, 'Product', 'product_table', 'prod_id', 48, 'long_name', 'plow', 'Product', 'product_table', 'prod_id', 165, 'long_name', 'bronze', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(338, 'Product', 'product_table', 'prod_id', 404, 'long_name', 'potatoes', 'Skill', 'skill_table', 'skill_id', 67, 'abbr', 'farm', '12', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(59, 'Product', 'product_table', 'prod_id', 49, 'long_name', 'provs', 'Skill', 'skill_table', 'skill_id', 12, 'abbr', 'hunt', '0', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(172, 'Product', 'product_table', 'prod_id', 136, 'long_name', 'provs', 'Skill', 'skill_table', 'skill_id', 22, 'abbr', 'skn', '1', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(119, 'Product', 'product_table', 'prod_id', 93, 'long_name', 'quarrels', 'Skill', 'skill_table', 'skill_id', 15, 'abbr', 'mtl', '2', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(120, 'Product', 'product_table', 'prod_id', 93, 'long_name', 'quarrels', 'Product', 'product_table', 'prod_id', 165, 'long_name', 'bronze', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(60, 'Product', 'product_table', 'prod_id', 50, 'long_name', 'rake', 'Skill', 'skill_table', 'skill_id', 28, 'abbr', 'wd', '3', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(138, 'Product', 'product_table', 'prod_id', 106, 'long_name', 'ram', 'Skill', 'skill_table', 'skill_id', 21, 'abbr', 'seq', '21', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(186, 'Product', 'product_table', 'prod_id', 150, 'long_name', 'recruit', 'Skill', 'skill_table', 'skill_id', 77, 'abbr', 'seek', '0', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(196, 'Product', 'product_table', 'prod_id', 160, 'long_name', 'refinery', 'Skill', 'skill_table', 'skill_id', 79, 'abbr', 'stn', '8', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(292, 'Product', 'product_table', 'prod_id', 375, 'long_name', 'repeatingarbalest', 'Skill', 'skill_table', 'skill_id', 25, 'abbr', 'wpn', '14', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(63, 'Product', 'product_table', 'prod_id', 52, 'long_name', 'ring', 'Skill', 'skill_table', 'skill_id', 20, 'abbr', 'sew', '3', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(64, 'Product', 'product_table', 'prod_id', 52, 'long_name', 'ring', 'Product', 'product_table', 'prod_id', 171, 'long_name', 'iron', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(61, 'Product', 'product_table', 'prod_id', 51, 'long_name', 'rope', 'Skill', 'skill_table', 'skill_id', 14, 'abbr', 'ltr', '4', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(62, 'Product', 'product_table', 'prod_id', 51, 'long_name', 'rope', 'Product', 'product_table', 'prod_id', 41, 'long_name', 'leather', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(157, 'Product', 'product_table', 'prod_id', 119, 'long_name', 'rope', 'Skill', 'skill_table', 'skill_id', 26, 'abbr', 'wv', '1', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 2),"
                ."(160, 'Product', 'product_table', 'prod_id', 121, 'long_name', 'rug', 'Skill', 'skill_table', 'skill_id', 26, 'abbr', 'wv', '4', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(65, 'Product', 'product_table', 'prod_id', 53, 'long_name', 'rum', 'Skill', 'skill_table', 'skill_id', 65, 'abbr', 'dis', '8', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(115, 'Product', 'product_table', 'prod_id', 91, 'long_name', 'saddle', 'Skill', 'skill_table', 'skill_id', 14, 'abbr', 'ltr', '6', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(116, 'Product', 'product_table', 'prod_id', 91, 'long_name', 'saddle', 'Product', 'product_table', 'prod_id', 41, 'long_name', 'leather', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(113, 'Product', 'product_table', 'prod_id', 90, 'long_name', 'saddlebags', 'Skill', 'skill_table', 'skill_id', 14, 'abbr', 'ltr', '5', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(114, 'Product', 'product_table', 'prod_id', 90, 'long_name', 'saddlebags', 'Product', 'product_table', 'prod_id', 41, 'long_name', 'leather', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(347, 'Product', 'product_table', 'prod_id', 410, 'long_name', 'saltpetre', 'Skill', 'skill_table', 'skill_id', 56, 'abbr', 'alc', '12', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(348, 'Product', 'product_table', 'prod_id', 410, 'long_name', 'saltpetre', 'Product', 'product_table', 'prod_id', 0, 'long_name', 'cattle', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(72, 'Product', 'product_table', 'prod_id', 58, 'long_name', 'scale', 'Skill', 'skill_table', 'skill_id', 20, 'abbr', 'sew', '3', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(73, 'Product', 'product_table', 'prod_id', 58, 'long_name', 'scale', 'Product', 'product_table', 'prod_id', 171, 'long_name', 'iron', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(231, 'Product', 'product_table', 'prod_id', 188, 'long_name', 'scrapers', 'Skill', 'skill_table', 'skill_id', 15, 'abbr', 'mtl', '2', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(232, 'Product', 'product_table', 'prod_id', 188, 'long_name', 'scrapers', 'Product', 'product_table', 'prod_id', 165, 'long_name', 'bronze', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(333, 'Product', 'product_table', 'prod_id', 400, 'long_name', 'scrapers', 'Skill', 'skill_table', 'skill_id', 79, 'abbr', 'stn', '4', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 2),"
                ."(334, 'Product', 'product_table', 'prod_id', 400, 'long_name', 'scrapers', 'Actives', 'tribes', 'curam', 0, 'tribeid', '0002.00', '1', 'N', 'tribes', 'clanid', 'tribeid', 'tribeid', 'curam', 2),"
                ."(74, 'Product', 'product_table', 'prod_id', 59, 'long_name', 'sculpture', 'Skill', 'skill_table', 'skill_id', 79, 'abbr', 'stn', '5', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(66, 'Product', 'product_table', 'prod_id', 54, 'long_name', 'scutum', 'Skill', 'skill_table', 'skill_id', 1, 'abbr', 'arm', '2', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(67, 'Product', 'product_table', 'prod_id', 54, 'long_name', 'scutum', 'Product', 'product_table', 'prod_id', 165, 'long_name', 'bronze', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(85, 'Product', 'product_table', 'prod_id', 68, 'long_name', 'scythe', 'Skill', 'skill_table', 'skill_id', 15, 'abbr', 'mtl', '5', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(86, 'Product', 'product_table', 'prod_id', 68, 'long_name', 'scythe', 'Product', 'product_table', 'prod_id', 165, 'long_name', 'bronze', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(123, 'Product', 'product_table', 'prod_id', 95, 'long_name', 'shackles', 'Skill', 'skill_table', 'skill_id', 15, 'abbr', 'mtl', '4', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(124, 'Product', 'product_table', 'prod_id', 95, 'long_name', 'shackles', 'Product', 'product_table', 'prod_id', 165, 'long_name', 'bronze', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(68, 'Product', 'product_table', 'prod_id', 55, 'long_name', 'shaft', 'Skill', 'skill_table', 'skill_id', 25, 'abbr', 'wpn', '1', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(69, 'Product', 'product_table', 'prod_id', 56, 'long_name', 'shovel', 'Skill', 'skill_table', 'skill_id', 15, 'abbr', 'mtl', '2', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(70, 'Product', 'product_table', 'prod_id', 56, 'long_name', 'shovel', 'Product', 'product_table', 'prod_id', 165, 'long_name', 'bronze', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(213, 'Product', 'product_table', 'prod_id', 177, 'long_name', 'silver', 'Skill', 'skill_table', 'skill_id', 74, 'abbr', 'ref', '0', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(214, 'Product', 'product_table', 'prod_id', 177, 'long_name', 'silver', 'Product', 'product_table', 'prod_id', 0, 'long_name', 'silver.ore', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(71, 'Product', 'product_table', 'prod_id', 57, 'long_name', 'skins', 'Skill', 'skill_table', 'skill_id', 22, 'abbr', 'skn', '0', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(77, 'Product', 'product_table', 'prod_id', 61, 'long_name', 'sling', 'Skill', 'skill_table', 'skill_id', 14, 'abbr', 'ltr', '2', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(78, 'Product', 'product_table', 'prod_id', 61, 'long_name', 'sling', 'Product', 'product_table', 'prod_id', 41, 'long_name', 'leather', '1', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(152, 'Product', 'product_table', 'prod_id', 115, 'long_name', 'sling', 'Skill', 'skill_table', 'skill_id', 25, 'abbr', 'wpn', '1', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 2),"
                ."(153, 'Product', 'product_table', 'prod_id', 115, 'long_name', 'sling', 'Actives', 'tribes', 'curam', 0, 'tribeid', '0002.00', '1', 'N', 'tribes', 'clanid', 'tribeid', 'tribeid', 'curam', 2),"
                ."(158, 'Product', 'product_table', 'prod_id', 120, 'long_name', 'sling', 'Skill', 'skill_table', 'skill_id', 26, 'abbr', 'wv', '2', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 3),"
                ."(159, 'Product', 'product_table', 'prod_id', 120, 'long_name', 'sling', 'Product', 'product_table', 'prod_id', 41, 'long_name', 'leather', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 3),"
                ."(200, 'Product', 'product_table', 'prod_id', 164, 'long_name', 'smelter', 'Skill', 'skill_table', 'skill_id', 66, 'abbr', 'eng', '5', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(341, 'Product', 'product_table', 'prod_id', 407, 'long_name', 'smelter', 'Skill', 'skill_table', 'skill_id', 79, 'abbr', 'stn', '8', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 2),"
                ."(342, 'Product', 'product_table', 'prod_id', 407, 'long_name', 'smelter', 'Skill', 'skill_table', 'skill_id', 66, 'abbr', 'eng', '5', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 2),"
                ."(79, 'Product', 'product_table', 'prod_id', 62, 'long_name', 'smgalley', 'Skill', 'skill_table', 'skill_id', 50, 'abbr', 'shw', '4', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(197, 'Product', 'product_table', 'prod_id', 161, 'long_name', 'smokehouse', 'Skill', 'skill_table', 'skill_id', 66, 'abbr', 'eng', '11', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(80, 'Product', 'product_table', 'prod_id', 63, 'long_name', 'snares', 'Skill', 'skill_table', 'skill_id', 26, 'abbr', 'wv', '3', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(154, 'Product', 'product_table', 'prod_id', 116, 'long_name', 'spetum', 'Skill', 'skill_table', 'skill_id', 25, 'abbr', 'wpn', '1', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(155, 'Product', 'product_table', 'prod_id', 116, 'long_name', 'spetum', 'Actives', 'tribes', 'curam', 0, 'tribeid', '0002.00', '1', 'N', 'tribes', 'clanid', 'tribeid', 'tribeid', 'curam', 1),"
                ."(84, 'Product', 'product_table', 'prod_id', 67, 'long_name', 'spice', 'Skill', 'skill_table', 'skill_id', 77, 'abbr', 'seek', '0', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(335, 'Product', 'product_table', 'prod_id', 401, 'long_name', 'spice', 'Skill', 'skill_table', 'skill_id', 67, 'abbr', 'farm', '20', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(149, 'Product', 'product_table', 'prod_id', 113, 'long_name', 'statue', 'Skill', 'skill_table', 'skill_id', 79, 'abbr', 'stn', '8', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(81, 'Product', 'product_table', 'prod_id', 65, 'long_name', 'staves', 'Skill', 'skill_table', 'skill_id', 25, 'abbr', 'wpn', '1', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(219, 'Product', 'product_table', 'prod_id', 180, 'long_name', 'steel', 'Skill', 'skill_table', 'skill_id', 74, 'abbr', 'ref', '9', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(220, 'Product', 'product_table', 'prod_id', 180, 'long_name', 'steel', 'Product', 'product_table', 'prod_id', 0, 'long_name', 'iron.ore', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(325, 'Product', 'product_table', 'prod_id', 396, 'long_name', 'steel1axe', 'Skill', 'skill_table', 'skill_id', 25, 'abbr', 'wpn', '10', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(326, 'Product', 'product_table', 'prod_id', 396, 'long_name', 'steel1axe', 'Actives', 'tribes', 'curam', 0, 'tribeid', '0002.00', '2', 'N', 'tribes', 'clanid', 'tribeid', 'tribeid', 'curam', 1),"
                ."(241, 'Product', 'product_table', 'prod_id', 349, 'long_name', 'steel1bascinet', 'Skill', 'skill_table', 'skill_id', 1, 'abbr', 'arm', '15', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(242, 'Product', 'product_table', 'prod_id', 349, 'long_name', 'steel1bascinet', 'Product', 'product_table', 'prod_id', 376, 'long_name', 'steel_1', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(273, 'Product', 'product_table', 'prod_id', 365, 'long_name', 'steel1breastplate', 'Skill', 'skill_table', 'skill_id', 1, 'abbr', 'arm', '8', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(274, 'Product', 'product_table', 'prod_id', 365, 'long_name', 'steel1breastplate', 'Product', 'product_table', 'prod_id', 376, 'long_name', 'steel_1', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(267, 'Product', 'product_table', 'prod_id', 362, 'long_name', 'steel1buckler', 'Skill', 'skill_table', 'skill_id', 1, 'abbr', 'arm', '6', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(268, 'Product', 'product_table', 'prod_id', 362, 'long_name', 'steel1buckler', 'Product', 'product_table', 'prod_id', 376, 'long_name', 'steel_1', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(287, 'Product', 'product_table', 'prod_id', 372, 'long_name', 'steel1chain', 'Skill', 'skill_table', 'skill_id', 1, 'abbr', 'arm', '6', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(288, 'Product', 'product_table', 'prod_id', 372, 'long_name', 'steel1chain', 'Product', 'product_table', 'prod_id', 376, 'long_name', 'steel_1', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(249, 'Product', 'product_table', 'prod_id', 353, 'long_name', 'steel1helm', 'Skill', 'skill_table', 'skill_id', 1, 'abbr', 'arm', '4', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(250, 'Product', 'product_table', 'prod_id', 353, 'long_name', 'steel1helm', 'Product', 'product_table', 'prod_id', 376, 'long_name', 'steel_1', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(281, 'Product', 'product_table', 'prod_id', 369, 'long_name', 'steel1plate', 'Skill', 'skill_table', 'skill_id', 1, 'abbr', 'arm', '18', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(282, 'Product', 'product_table', 'prod_id', 369, 'long_name', 'steel1plate', 'Product', 'product_table', 'prod_id', 376, 'long_name', 'steel_1', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(259, 'Product', 'product_table', 'prod_id', 358, 'long_name', 'steel1shield', 'Skill', 'skill_table', 'skill_id', 1, 'abbr', 'arm', '3', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(260, 'Product', 'product_table', 'prod_id', 358, 'long_name', 'steel1shield', 'Product', 'product_table', 'prod_id', 376, 'long_name', 'steel_1', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(327, 'Product', 'product_table', 'prod_id', 397, 'long_name', 'steel1spear', 'Skill', 'skill_table', 'skill_id', 25, 'abbr', 'wpn', '9', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(328, 'Product', 'product_table', 'prod_id', 397, 'long_name', 'steel1spear', 'Actives', 'tribes', 'curam', 0, 'tribeid', '0002.00', '1', 'N', 'tribes', 'clanid', 'tribeid', 'tribeid', 'curam', 1),"
                ."(311, 'Product', 'product_table', 'prod_id', 388, 'long_name', 'steel1sword', 'Skill', 'skill_table', 'skill_id', 25, 'abbr', 'wpn', '13', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(312, 'Product', 'product_table', 'prod_id', 388, 'long_name', 'steel1sword', 'Actives', 'tribes', 'curam', 0, 'tribeid', '0002.00', '3', 'N', 'tribes', 'clanid', 'tribeid', 'tribeid', 'curam', 1),"
                ."(329, 'Product', 'product_table', 'prod_id', 398, 'long_name', 'steel2axe', 'Skill', 'skill_table', 'skill_id', 25, 'abbr', 'wpn', '13', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(330, 'Product', 'product_table', 'prod_id', 398, 'long_name', 'steel2axe', 'Actives', 'tribes', 'curam', 0, 'tribeid', '0002.00', '2', 'N', 'tribes', 'clanid', 'tribeid', 'tribeid', 'curam', 1),"
                ."(239, 'Product', 'product_table', 'prod_id', 348, 'long_name', 'steel2bascinet', 'Skill', 'skill_table', 'skill_id', 1, 'abbr', 'arm', '15', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(240, 'Product', 'product_table', 'prod_id', 348, 'long_name', 'steel2bascinet', 'Product', 'product_table', 'prod_id', 377, 'long_name', 'steel_2', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(275, 'Product', 'product_table', 'prod_id', 366, 'long_name', 'steel2breastplate', 'Skill', 'skill_table', 'skill_id', 1, 'abbr', 'arm', '8', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(276, 'Product', 'product_table', 'prod_id', 366, 'long_name', 'steel2breastplate', 'Product', 'product_table', 'prod_id', 377, 'long_name', 'steel_2', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(269, 'Product', 'product_table', 'prod_id', 363, 'long_name', 'steel2buckler', 'Skill', 'skill_table', 'skill_id', 1, 'abbr', 'arm', '6', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(270, 'Product', 'product_table', 'prod_id', 363, 'long_name', 'steel2buckler', 'Product', 'product_table', 'prod_id', 377, 'long_name', 'steel_2', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(289, 'Product', 'product_table', 'prod_id', 373, 'long_name', 'steel2chain', 'Skill', 'skill_table', 'skill_id', 1, 'abbr', 'arm', '6', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(290, 'Product', 'product_table', 'prod_id', 373, 'long_name', 'steel2chain', 'Product', 'product_table', 'prod_id', 377, 'long_name', 'steel_2', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(247, 'Product', 'product_table', 'prod_id', 352, 'long_name', 'steel2helm', 'Skill', 'skill_table', 'skill_id', 1, 'abbr', 'arm', '4', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(248, 'Product', 'product_table', 'prod_id', 352, 'long_name', 'steel2helm', 'Product', 'product_table', 'prod_id', 377, 'long_name', 'steel_2', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(283, 'Product', 'product_table', 'prod_id', 370, 'long_name', 'steel2plate', 'Skill', 'skill_table', 'skill_id', 1, 'abbr', 'arm', '18', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(284, 'Product', 'product_table', 'prod_id', 370, 'long_name', 'steel2plate', 'Product', 'product_table', 'prod_id', 377, 'long_name', 'steel_2', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(261, 'Product', 'product_table', 'prod_id', 359, 'long_name', 'steel2shield', 'Skill', 'skill_table', 'skill_id', 1, 'abbr', 'arm', '3', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(262, 'Product', 'product_table', 'prod_id', 359, 'long_name', 'steel2shield', 'Product', 'product_table', 'prod_id', 377, 'long_name', 'steel_2', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(331, 'Product', 'product_table', 'prod_id', 399, 'long_name', 'steel2spear', 'Skill', 'skill_table', 'skill_id', 25, 'abbr', 'wpn', '12', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(332, 'Product', 'product_table', 'prod_id', 399, 'long_name', 'steel2spear', 'Actives', 'tribes', 'curam', 0, 'tribeid', '0002.00', '1', 'N', 'tribes', 'clanid', 'tribeid', 'tribeid', 'curam', 1),"
                ."(313, 'Product', 'product_table', 'prod_id', 389, 'long_name', 'steel2sword', 'Skill', 'skill_table', 'skill_id', 25, 'abbr', 'wpn', '18', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(314, 'Product', 'product_table', 'prod_id', 389, 'long_name', 'steel2sword', 'Actives', 'tribes', 'curam', 0, 'tribeid', '0002.00', '3', 'N', 'tribes', 'clanid', 'tribeid', 'tribeid', 'curam', 1),"
                ."(321, 'Product', 'product_table', 'prod_id', 394, 'long_name', 'steelaxe', 'Skill', 'skill_table', 'skill_id', 25, 'abbr', 'wpn', '7', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(322, 'Product', 'product_table', 'prod_id', 394, 'long_name', 'steelaxe', 'Actives', 'tribes', 'curam', 0, 'tribeid', '0002.00', '2', 'N', 'tribes', 'clanid', 'tribeid', 'tribeid', 'curam', 1),"
                ."(243, 'Product', 'product_table', 'prod_id', 350, 'long_name', 'steelbascinet', 'Skill', 'skill_table', 'skill_id', 1, 'abbr', 'arm', '15', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(244, 'Product', 'product_table', 'prod_id', 350, 'long_name', 'steelbascinet', 'Product', 'product_table', 'prod_id', 180, 'long_name', 'steel', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(271, 'Product', 'product_table', 'prod_id', 364, 'long_name', 'steelbreastplate', 'Skill', 'skill_table', 'skill_id', 1, 'abbr', 'arm', '8', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(272, 'Product', 'product_table', 'prod_id', 364, 'long_name', 'steelbreastplate', 'Product', 'product_table', 'prod_id', 180, 'long_name', 'steel', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(265, 'Product', 'product_table', 'prod_id', 361, 'long_name', 'steelbuckler', 'Skill', 'skill_table', 'skill_id', 1, 'abbr', 'arm', '6', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(266, 'Product', 'product_table', 'prod_id', 361, 'long_name', 'steelbuckler', 'Product', 'product_table', 'prod_id', 180, 'long_name', 'steel', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(285, 'Product', 'product_table', 'prod_id', 371, 'long_name', 'steelchain', 'Skill', 'skill_table', 'skill_id', 1, 'abbr', 'arm', '6', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(286, 'Product', 'product_table', 'prod_id', 371, 'long_name', 'steelchain', 'Product', 'product_table', 'prod_id', 180, 'long_name', 'steel', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(251, 'Product', 'product_table', 'prod_id', 354, 'long_name', 'steelhelm', 'Skill', 'skill_table', 'skill_id', 1, 'abbr', 'arm', '4', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(252, 'Product', 'product_table', 'prod_id', 354, 'long_name', 'steelhelm', 'Product', 'product_table', 'prod_id', 180, 'long_name', 'steel', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(279, 'Product', 'product_table', 'prod_id', 368, 'long_name', 'steelplate', 'Skill', 'skill_table', 'skill_id', 1, 'abbr', 'arm', '18', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(280, 'Product', 'product_table', 'prod_id', 368, 'long_name', 'steelplate', 'Product', 'product_table', 'prod_id', 180, 'long_name', 'steel', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(257, 'Product', 'product_table', 'prod_id', 357, 'long_name', 'steelshield', 'Skill', 'skill_table', 'skill_id', 1, 'abbr', 'arm', '3', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(258, 'Product', 'product_table', 'prod_id', 357, 'long_name', 'steelshield', 'Product', 'product_table', 'prod_id', 180, 'long_name', 'steel', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(323, 'Product', 'product_table', 'prod_id', 395, 'long_name', 'steelspear', 'Skill', 'skill_table', 'skill_id', 25, 'abbr', 'wpn', '5', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(324, 'Product', 'product_table', 'prod_id', 395, 'long_name', 'steelspear', 'Actives', 'tribes', 'curam', 0, 'tribeid', '0002.00', '1', 'N', 'tribes', 'clanid', 'tribeid', 'tribeid', 'curam', 1),"
                ."(309, 'Product', 'product_table', 'prod_id', 387, 'long_name', 'steelsword', 'Skill', 'skill_table', 'skill_id', 25, 'abbr', 'wpn', '9', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(310, 'Product', 'product_table', 'prod_id', 387, 'long_name', 'steelsword', 'Actives', 'tribes', 'curam', 0, 'tribeid', '0002.00', '3', 'N', 'tribes', 'clanid', 'tribeid', 'tribeid', 'curam', 1),"
                ."(293, 'Product', 'product_table', 'prod_id', 376, 'long_name', 'steel_1', 'Skill', 'skill_table', 'skill_id', 74, 'abbr', 'ref', '11', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(294, 'Product', 'product_table', 'prod_id', 376, 'long_name', 'steel_1', 'Product', 'product_table', 'prod_id', 0, 'long_name', 'iron.ore', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(295, 'Product', 'product_table', 'prod_id', 377, 'long_name', 'steel_2', 'Skill', 'skill_table', 'skill_id', 74, 'abbr', 'ref', '15', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(296, 'Product', 'product_table', 'prod_id', 377, 'long_name', 'steel_2', 'Product', 'product_table', 'prod_id', 0, 'long_name', 'iron.ore', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(350, 'Product', 'product_table', 'prod_id', 412, 'long_name', 'still', 'Skill', 'skill_table', 'skill_id', 66, 'abbr', 'eng', '4', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(143, 'Product', 'product_table', 'prod_id', 110, 'long_name', 'stoneaxe', 'Skill', 'skill_table', 'skill_id', 79, 'abbr', 'stn', '2', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(144, 'Product', 'product_table', 'prod_id', 110, 'long_name', 'stoneaxe', 'Actives', 'tribes', 'curam', 0, 'tribeid', '0002.00', '1', 'N', 'tribes', 'clanid', 'tribeid', 'tribeid', 'curam', 1),"
                ."(199, 'Product', 'product_table', 'prod_id', 163, 'long_name', 'stones', 'Skill', 'skill_table', 'skill_id', 18, 'abbr', 'qry', '1', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(145, 'Product', 'product_table', 'prod_id', 111, 'long_name', 'stonespear', 'Skill', 'skill_table', 'skill_id', 79, 'abbr', 'stn', '4', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(146, 'Product', 'product_table', 'prod_id', 111, 'long_name', 'stonespear', 'Actives', 'tribes', 'curam', 0, 'tribeid', '0002.00', '1', 'N', 'tribes', 'clanid', 'tribeid', 'tribeid', 'curam', 1),"
                ."(221, 'Product', 'product_table', 'prod_id', 181, 'long_name', 'stonewalls10', 'Skill', 'skill_table', 'skill_id', 66, 'abbr', 'eng', '11', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(222, 'Product', 'product_table', 'prod_id', 181, 'long_name', 'stonewalls10', 'Product', 'product_table', 'prod_id', 163, 'long_name', 'stones', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(224, 'Product', 'product_table', 'prod_id', 183, 'long_name', 'stonewalls15', 'Skill', 'skill_table', 'skill_id', 66, 'abbr', 'eng', '11', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(225, 'Product', 'product_table', 'prod_id', 183, 'long_name', 'stonewalls15', 'Product', 'product_table', 'prod_id', 163, 'long_name', 'stones', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(226, 'Product', 'product_table', 'prod_id', 184, 'long_name', 'stonewalls20', 'Skill', 'skill_table', 'skill_id', 66, 'abbr', 'eng', '11', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(227, 'Product', 'product_table', 'prod_id', 184, 'long_name', 'stonewalls20', 'Product', 'product_table', 'prod_id', 163, 'long_name', 'stones', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(82, 'Product', 'product_table', 'prod_id', 66, 'long_name', 'strings', 'Skill', 'skill_table', 'skill_id', 24, 'abbr', 'wax', '2', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(83, 'Product', 'product_table', 'prod_id', 66, 'long_name', 'strings', 'Product', 'product_table', 'prod_id', 28, 'long_name', 'gut', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(165, 'Product', 'product_table', 'prod_id', 127, 'long_name', 'structure', 'Skill', 'skill_table', 'skill_id', 28, 'abbr', 'wd', '8', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(87, 'Product', 'product_table', 'prod_id', 69, 'long_name', 'sugar', 'Skill', 'skill_table', 'skill_id', 67, 'abbr', 'farm', '1', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(343, 'Product', 'product_table', 'prod_id', 408, 'long_name', 'sulphur', 'Skill', 'skill_table', 'skill_id', 74, 'abbr', 'ref', '16', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(344, 'Product', 'product_table', 'prod_id', 408, 'long_name', 'sulphur', 'Product', 'product_table', 'prod_id', 163, 'long_name', 'stones', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(345, 'Product', 'product_table', 'prod_id', 409, 'long_name', 'sulphur', 'Skill', 'skill_table', 'skill_id', 56, 'abbr', 'alc', '16', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 2),"
                ."(346, 'Product', 'product_table', 'prod_id', 409, 'long_name', 'sulphur', 'Product', 'product_table', 'prod_id', 163, 'long_name', 'stones', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 2),"
                ."(163, 'Product', 'product_table', 'prod_id', 124, 'long_name', 'tapestry', 'Skill', 'skill_table', 'skill_id', 26, 'abbr', 'wv', '8', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(211, 'Product', 'product_table', 'prod_id', 176, 'long_name', 'tin', 'Skill', 'skill_table', 'skill_id', 74, 'abbr', 'ref', '0', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(212, 'Product', 'product_table', 'prod_id', 176, 'long_name', 'tin', 'Product', 'product_table', 'prod_id', 0, 'long_name', 'tin.ore', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(88, 'Product', 'product_table', 'prod_id', 71, 'long_name', 'tobacco', 'Skill', 'skill_table', 'skill_id', 67, 'abbr', 'farm', '3', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(166, 'Product', 'product_table', 'prod_id', 128, 'long_name', 'totem', 'Skill', 'skill_table', 'skill_id', 28, 'abbr', 'wd', '8', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(140, 'Product', 'product_table', 'prod_id', 108, 'long_name', 'tower', 'Skill', 'skill_table', 'skill_id', 21, 'abbr', 'seq', '21', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(141, 'Product', 'product_table', 'prod_id', 108, 'long_name', 'tower', 'Product', 'product_table', 'prod_id', 163, 'long_name', 'stones', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(188, 'Product', 'product_table', 'prod_id', 152, 'long_name', 'tradepost', 'Skill', 'skill_table', 'skill_id', 66, 'abbr', 'eng', '2', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(178, 'Product', 'product_table', 'prod_id', 142, 'long_name', 'trader', 'Skill', 'skill_table', 'skill_id', 50, 'abbr', 'shw', '6', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(89, 'Product', 'product_table', 'prod_id', 72, 'long_name', 'traps', 'Skill', 'skill_table', 'skill_id', 15, 'abbr', 'mtl', '2', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(90, 'Product', 'product_table', 'prod_id', 72, 'long_name', 'traps', 'Product', 'product_table', 'prod_id', 165, 'long_name', 'bronze', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(107, 'Product', 'product_table', 'prod_id', 87, 'long_name', 'trews', 'Skill', 'skill_table', 'skill_id', 14, 'abbr', 'ltr', '3', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(108, 'Product', 'product_table', 'prod_id', 87, 'long_name', 'trews', 'Product', 'product_table', 'prod_id', 41, 'long_name', 'leather', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(91, 'Product', 'product_table', 'prod_id', 73, 'long_name', 'trinket', 'Skill', 'skill_table', 'skill_id', 13, 'abbr', 'jew', '1', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(133, 'Product', 'product_table', 'prod_id', 100, 'long_name', 'trumpet', 'Skill', 'skill_table', 'skill_id', 72, 'abbr', 'mus', '6', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(92, 'Product', 'product_table', 'prod_id', 74, 'long_name', 'urn', 'Skill', 'skill_table', 'skill_id', 17, 'abbr', 'pot', '5', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(93, 'Product', 'product_table', 'prod_id', 75, 'long_name', 'wagon', 'Skill', 'skill_table', 'skill_id', 28, 'abbr', 'wd', '3', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(182, 'Product', 'product_table', 'prod_id', 146, 'long_name', 'warship', 'Skill', 'skill_table', 'skill_id', 50, 'abbr', 'shw', '9', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(94, 'Product', 'product_table', 'prod_id', 76, 'long_name', 'wax', 'Skill', 'skill_table', 'skill_id', 57, 'abbr', 'api', '1', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(173, 'Product', 'product_table', 'prod_id', 137, 'long_name', 'wax', 'Skill', 'skill_table', 'skill_id', 77, 'abbr', 'seek', '0', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(111, 'Product', 'product_table', 'prod_id', 89, 'long_name', 'whip', 'Skill', 'skill_table', 'skill_id', 14, 'abbr', 'ltr', '5', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(112, 'Product', 'product_table', 'prod_id', 89, 'long_name', 'whip', 'Product', 'product_table', 'prod_id', 41, 'long_name', 'leather', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(95, 'Product', 'product_table', 'prod_id', 77, 'long_name', 'wine', 'Skill', 'skill_table', 'skill_id', 65, 'abbr', 'dis', '6', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(75, 'Product', 'product_table', 'prod_id', 60, 'long_name', 'woodenshield', 'Skill', 'skill_table', 'skill_id', 1, 'abbr', 'arm', '2', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(76, 'Product', 'product_table', 'prod_id', 60, 'long_name', 'woodenshield', 'Product', 'product_table', 'prod_id', 85, 'long_name', 'logs', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(209, 'Product', 'product_table', 'prod_id', 175, 'long_name', 'zinc', 'Skill', 'skill_table', 'skill_id', 74, 'abbr', 'ref', '0', 'N', 'skills', '', 'tribeid', 'abbr', 'level', 1),"
                ."(210, 'Product', 'product_table', 'prod_id', 175, 'long_name', 'zinc', 'Product', 'product_table', 'prod_id', 0, 'long_name', 'zinc.ore', '0', 'N', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(385, 'Product', 'product_table', 'prod_id', 2, 'long_name', 'ale', 'Actives', 'tribes', 'curam', 0, 'tribeid', '0002.00', '5', 'N', 'tribes', 'clanid', 'tribeid', 'tribeid', 'curam', 1),"
                ."(386, 'Product', 'product_table', 'prod_id', 2, 'long_name', 'ale', 'Product', 'product_table', 'prod_id', 26, 'long_name', 'grain', '100', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(387, 'Product', 'product_table', 'prod_id', 2, 'long_name', 'ale', 'Product', 'product_table', 'prod_id', 8, 'long_name', 'barrels', '1', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(389, 'Product', 'product_table', 'prod_id', 2, 'long_name', 'ale', 'Structure', 'product_table', 'prod_id', 158, 'long_name', 'distillery', '1', 'Y', 'structures', 'clanid', 'tribeid', 'long_name', 'hex_id', 1),"
                ."(400, 'Product', 'product_table', 'prod_id', 2, 'long_name', 'ale', 'Output', 'skill_table', 'skill_id', 29, 'abbr', 'adm', '100', 'N', '', '', '', '', '', 1),"
                ."(401, 'Product', 'product_table', 'prod_id', 6, 'long_name', 'bow', 'Product', 'product_table', 'prod_id', 65, 'long_name', 'staves', '1', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(402, 'Product', 'product_table', 'prod_id', 6, 'long_name', 'bow', 'Product', 'product_table', 'prod_id', 422, 'long_name', 'bowstring', '1', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(404, 'Product', 'product_table', 'prod_id', 421, 'long_name', 'sinew', 'Skill', 'skill_table', 'skill_id', 0, 'abbr', 'sew', '1', 'N', 'skills', '', '', 'abbr', '', 1),"
                ."(405, 'Product', 'product_table', 'prod_id', 421, 'long_name', 'sinew', 'Actives', 'tribes', 'curam', 0, 'tribeid', '0002.00', '1', 'N', 'tribes', 'clanid', 'tribeid', 'tribeid', 'curam', 1),"
                ."(406, 'Product', 'product_table', 'prod_id', 421, 'long_name', 'sinew', 'Product', 'product_table', 'prod_id', 28, 'long_name', 'gut', '1', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(407, 'Product', 'product_table', 'prod_id', 421, 'long_name', 'sinew', 'Output', 'skill_table', 'skill_id', 29, 'abbr', 'adm', '10', 'N', '', '', '', '', '', 1),"
                ."(408, 'Product', 'product_table', 'prod_id', 422, 'long_name', 'bowstring', 'Skill', 'skill_table', 'skill_id', 0, 'abbr', 'flet', '2', 'N', 'skills', '', '', 'abbr', '', 1),"
                ."(409, 'Product', 'product_table', 'prod_id', 422, 'long_name', 'bowstring', 'Actives', 'tribes', 'curam', 0, 'tribeid', '0002.00', '1', 'N', 'tribes', 'clanid', 'tribeid', 'tribeid', 'curam', 1),"
                ."(410, 'Product', 'product_table', 'prod_id', 422, 'long_name', 'bowstring', 'Product', 'product_table', 'prod_id', 421, 'long_name', 'sinew', '5', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(411, 'Product', 'product_table', 'prod_id', 422, 'long_name', 'bowstring', 'Product', 'product_table', 'prod_id', 76, 'long_name', 'wax', '1', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(412, 'Product', 'product_table', 'prod_id', 422, 'long_name', 'bowstring', 'Output', 'skill_table', 'skill_id', 29, 'abbr', 'adm', '1', 'Y', '', '', '', '', '', 1),"
                ."(413, 'Product', 'product_table', 'prod_id', 6, 'long_name', 'bow', 'Output', 'skill_table', 'skill_id', 29, 'abbr', 'adm', '1', 'N', '', '', '', '', '', 1),"
                ."(414, 'Product', 'product_table', 'prod_id', 61, 'long_name', 'sling', 'Product', 'product_table', 'prod_id', 15, 'long_name', 'cloth', '1', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 2),"
                ."(415, 'Product', 'product_table', 'prod_id', 61, 'long_name', 'sling', 'Output', 'skill_table', 'skill_id', 29, 'abbr', 'adm', '10', 'N', '', '', '', '', '', 2),"
                ."(416, 'Product', 'product_table', 'prod_id', 65, 'long_name', 'staves', 'Actives', 'tribes', 'curam', 0, 'tribeid', '0002.00', '1', 'N', 'tribes', 'clanid', 'tribeid', 'tribeid', 'curam', 1),"
                ."(417, 'Product', 'product_table', 'prod_id', 65, 'long_name', 'staves', 'Output', 'skill_table', 'skill_id', 29, 'abbr', 'adm', '1', 'N', '', '', '', '', '', 1),"
                ."(418, 'Product', 'product_table', 'prod_id', 34, 'long_name', 'horsebow', 'Product', 'product_table', 'prod_id', 65, 'long_name', 'staves', '1', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(419, 'Product', 'product_table', 'prod_id', 34, 'long_name', 'horsebow', 'Product', 'product_table', 'prod_id', 422, 'long_name', 'bowstring', '1', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(420, 'Product', 'product_table', 'prod_id', 34, 'long_name', 'horsebow', 'Output', 'skill_table', 'skill_id', 29, 'abbr', 'adm', '1', 'N', '', '', '', '', '', 1),"
                ."(421, 'Product', 'product_table', 'prod_id', 3, 'long_name', 'arbalest', 'Actives', 'tribes', 'curam', 0, 'tribeid', '0002.00', '3', 'N', 'tribes', 'clanid', 'tribeid', 'tribeid', 'curam', 1),"
                ."(422, 'Product', 'product_table', 'prod_id', 3, 'long_name', 'arbalest', 'Resource', 'product_table', 'prod_id', 423, 'long_name', 'coal', '20', 'Y', 'resources', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(423, 'Product', 'product_table', 'prod_id', 3, 'long_name', 'arbalest', 'Product', 'product_table', 'prod_id', 165, 'long_name', 'bronze', '2', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(424, 'Product', 'product_table', 'prod_id', 3, 'long_name', 'arbalest', 'Product', 'product_table', 'prod_id', 422, 'long_name', 'bowstring', '5', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(425, 'Product', 'product_table', 'prod_id', 3, 'long_name', 'arbalest', 'Skill', 'skill_table', 'skill_id', 0, 'abbr', 'wpn', '8', 'N', 'skills', '', '', 'abbr', '', 2),"
                ."(426, 'Product', 'product_table', 'prod_id', 3, 'long_name', 'arbalest', 'Actives', 'tribes', 'curam', 0, 'tribeid', '0002.00', '3', 'N', 'tribes', 'clanid', 'tribeid', 'tribeid', 'curam', 2),"
                ."(427, 'Product', 'product_table', 'prod_id', 3, 'long_name', 'arbalest', 'Resource', 'product_table', 'prod_id', 0, 'long_name', 'aardvark', '20', 'Y', 'resources', '', 'tribeid', 'long_name', 'amount', 2),"
                ."(428, 'Product', 'product_table', 'prod_id', 3, 'long_name', 'arbalest', 'Product', 'product_table', 'prod_id', 171, 'long_name', 'iron', '2', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 2),"
                ."(429, 'Product', 'product_table', 'prod_id', 3, 'long_name', 'arbalest', 'Product', 'product_table', 'prod_id', 0, 'long_name', 'aardvark', '5', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 2),"
                ."(430, 'Product', 'product_table', 'prod_id', 3, 'long_name', 'arbalest', 'Output', 'skill_table', 'skill_id', 29, 'abbr', 'adm', '1', 'N', '', '', '', '', '', 1),"
                ."(431, 'Product', 'product_table', 'prod_id', 3, 'long_name', 'arbalest', 'Output', 'skill_table', 'skill_id', 29, 'abbr', 'adm', '1', 'N', '', '', '', '', '', 2),"
                ."(432, 'Product', 'product_table', 'prod_id', 374, 'long_name', 'crossbow', 'Actives', 'tribes', 'curam', 0, 'tribeid', '0002.00', '2', 'N', 'tribes', 'clanid', 'tribeid', 'tribeid', 'curam', 1),"
                ."(433, 'Product', 'product_table', 'prod_id', 374, 'long_name', 'crossbow', 'Product', 'product_table', 'prod_id', 423, 'long_name', 'coal', '40', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(434, 'Product', 'product_table', 'prod_id', 374, 'long_name', 'crossbow', 'Product', 'product_table', 'prod_id', 171, 'long_name', 'iron', '5', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(435, 'Product', 'product_table', 'prod_id', 374, 'long_name', 'crossbow', 'Product', 'product_table', 'prod_id', 422, 'long_name', 'bowstring', '5', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(436, 'Product', 'product_table', 'prod_id', 374, 'long_name', 'crossbow', 'Output', 'skill_table', 'skill_id', 29, 'abbr', 'adm', '1', 'N', '', '', '', '', '', 1),"
                ."(437, 'Product', 'product_table', 'prod_id', 374, 'long_name', 'crossbow', 'Skill', 'skill_table', 'skill_id', 0, 'abbr', 'wpn', '10', 'N', 'skills', '', '', 'abbr', '', 2),"
                ."(438, 'Product', 'product_table', 'prod_id', 374, 'long_name', 'crossbow', 'Actives', 'tribes', 'curam', 0, 'tribeid', '0002.00', '2', 'N', 'tribes', 'clanid', 'tribeid', 'tribeid', 'curam', 2),"
                ."(439, 'Product', 'product_table', 'prod_id', 374, 'long_name', 'crossbow', 'Product', 'product_table', 'prod_id', 423, 'long_name', 'coal', '40', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 2),"
                ."(440, 'Product', 'product_table', 'prod_id', 374, 'long_name', 'crossbow', 'Product', 'product_table', 'prod_id', 165, 'long_name', 'bronze', '5', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 2),"
                ."(441, 'Product', 'product_table', 'prod_id', 374, 'long_name', 'crossbow', 'Product', 'product_table', 'prod_id', 422, 'long_name', 'bowstring', '5', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 2),"
                ."(442, 'Product', 'product_table', 'prod_id', 374, 'long_name', 'crossbow', 'Output', 'skill_table', 'skill_id', 29, 'abbr', 'adm', '1', 'N', '', '', '', '', '', 2),"
                ."(443, 'Product', 'product_table', 'prod_id', 118, 'long_name', 'longbow', 'Actives', 'tribes', 'curam', 0, 'tribeid', '0002.00', '2', 'N', 'tribes', 'clanid', 'tribeid', 'tribeid', 'curam', 1),"
                ."(444, 'Product', 'product_table', 'prod_id', 118, 'long_name', 'longbow', 'Product', 'product_table', 'prod_id', 65, 'long_name', 'staves', '1', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(445, 'Product', 'product_table', 'prod_id', 118, 'long_name', 'longbow', 'Product', 'product_table', 'prod_id', 422, 'long_name', 'bowstring', '2', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(446, 'Product', 'product_table', 'prod_id', 118, 'long_name', 'longbow', 'Output', 'skill_table', 'skill_id', 29, 'abbr', 'adm', '1', 'N', '', '', '', '', '', 1),"
                ."(447, 'Product', 'product_table', 'prod_id', 375, 'long_name', 'repeatingarbalest', 'Actives', 'tribes', 'curam', 0, 'tribeid', '0002.00', '4', 'N', 'tribes', 'clanid', 'tribeid', 'tribeid', 'curam', 1),"
                ."(448, 'Product', 'product_table', 'prod_id', 375, 'long_name', 'repeatingarbalest', 'Product', 'product_table', 'prod_id', 423, 'long_name', 'coal', '25', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(449, 'Product', 'product_table', 'prod_id', 375, 'long_name', 'repeatingarbalest', 'Product', 'product_table', 'prod_id', 171, 'long_name', 'iron', '2', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(450, 'Product', 'product_table', 'prod_id', 375, 'long_name', 'repeatingarbalest', 'Product', 'product_table', 'prod_id', 0, 'long_name', 'aardvark', '10', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(451, 'Product', 'product_table', 'prod_id', 375, 'long_name', 'repeatingarbalest', 'Output', 'skill_table', 'skill_id', 29, 'abbr', 'adm', '1', 'N', '', '', '', '', '', 1),"
                ."(452, 'Product', 'product_table', 'prod_id', 55, 'long_name', 'shaft', 'Actives', 'tribes', 'curam', 0, 'tribeid', '0002.00', '1', 'N', 'tribes', 'clanid', 'tribeid', 'tribeid', 'curam', 1),"
                ."(453, 'Product', 'product_table', 'prod_id', 55, 'long_name', 'shaft', 'Output', 'skill_table', 'skill_id', 29, 'abbr', 'adm', '1', 'N', '', '', '', '', '', 1),"
                ."(454, 'Product', 'product_table', 'prod_id', 116, 'long_name', 'spetum', 'Product', 'product_table', 'prod_id', 423, 'long_name', 'coal', '5', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(455, 'Product', 'product_table', 'prod_id', 116, 'long_name', 'spetum', 'Product', 'product_table', 'prod_id', 165, 'long_name', 'bronze', '2', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(456, 'Product', 'product_table', 'prod_id', 116, 'long_name', 'spetum', 'Product', 'product_table', 'prod_id', 55, 'long_name', 'shaft', '1', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(457, 'Product', 'product_table', 'prod_id', 116, 'long_name', 'spetum', 'Output', 'skill_table', 'skill_id', 29, 'abbr', 'adm', '1', 'N', '', '', '', '', '', 1),"
                ."(458, 'Product', 'product_table', 'prod_id', 392, 'long_name', 'bronzespear', 'Product', 'product_table', 'prod_id', 423, 'long_name', 'coal', '5', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(459, 'Product', 'product_table', 'prod_id', 392, 'long_name', 'bronzespear', 'Product', 'product_table', 'prod_id', 165, 'long_name', 'bronze', '2', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(460, 'Product', 'product_table', 'prod_id', 392, 'long_name', 'bronzespear', 'Product', 'product_table', 'prod_id', 55, 'long_name', 'shaft', '1', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(461, 'Product', 'product_table', 'prod_id', 392, 'long_name', 'bronzespear', 'Output', 'skill_table', 'skill_id', 29, 'abbr', 'adm', '1', 'N', '', '', '', '', '', 1),"
                ."(462, 'Product', 'product_table', 'prod_id', 384, 'long_name', 'ironspear', 'Product', 'product_table', 'prod_id', 423, 'long_name', 'coal', '10', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(463, 'Product', 'product_table', 'prod_id', 384, 'long_name', 'ironspear', 'Product', 'product_table', 'prod_id', 171, 'long_name', 'iron', '2', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(464, 'Product', 'product_table', 'prod_id', 384, 'long_name', 'ironspear', 'Product', 'product_table', 'prod_id', 55, 'long_name', 'shaft', '1', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(465, 'Product', 'product_table', 'prod_id', 384, 'long_name', 'ironspear', 'Output', 'skill_table', 'skill_id', 29, 'abbr', 'adm', '1', 'N', '', '', '', '', '', 1),"
                ."(466, 'Product', 'product_table', 'prod_id', 395, 'long_name', 'steelspear', 'Product', 'product_table', 'prod_id', 174, 'long_name', 'coke', '10', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(467, 'Product', 'product_table', 'prod_id', 395, 'long_name', 'steelspear', 'Product', 'product_table', 'prod_id', 180, 'long_name', 'steel', '2', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(468, 'Product', 'product_table', 'prod_id', 395, 'long_name', 'steelspear', 'Product', 'product_table', 'prod_id', 55, 'long_name', 'shaft', '1', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(469, 'Product', 'product_table', 'prod_id', 395, 'long_name', 'steelspear', 'Output', 'skill_table', 'skill_id', 29, 'abbr', 'adm', '1', 'N', '', '', '', '', '', 1),"
                ."(470, 'Product', 'product_table', 'prod_id', 397, 'long_name', 'steel1spear', 'Product', 'product_table', 'prod_id', 174, 'long_name', 'coke', '10', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(471, 'Product', 'product_table', 'prod_id', 397, 'long_name', 'steel1spear', 'Product', 'product_table', 'prod_id', 376, 'long_name', 'steel_1', '2', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(472, 'Product', 'product_table', 'prod_id', 397, 'long_name', 'steel1spear', 'Product', 'product_table', 'prod_id', 55, 'long_name', 'shaft', '1', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(473, 'Product', 'product_table', 'prod_id', 397, 'long_name', 'steel1spear', 'Output', 'skill_table', 'skill_id', 29, 'abbr', 'adm', '1', 'N', '', '', '', '', '', 1),"
                ."(474, 'Product', 'product_table', 'prod_id', 399, 'long_name', 'steel2spear', 'Product', 'product_table', 'prod_id', 174, 'long_name', 'coke', '10', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(475, 'Product', 'product_table', 'prod_id', 399, 'long_name', 'steel2spear', 'Product', 'product_table', 'prod_id', 377, 'long_name', 'steel_2', '2', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(476, 'Product', 'product_table', 'prod_id', 399, 'long_name', 'steel2spear', 'Product', 'product_table', 'prod_id', 55, 'long_name', 'shaft', '1', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(477, 'Product', 'product_table', 'prod_id', 399, 'long_name', 'steel2spear', 'Output', 'skill_table', 'skill_id', 29, 'abbr', 'adm', '1', 'N', '', '', '', '', '', 1),"
                ."(478, 'Product', 'product_table', 'prod_id', 383, 'long_name', 'ironmace', 'Product', 'product_table', 'prod_id', 423, 'long_name', 'coal', '30', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(479, 'Product', 'product_table', 'prod_id', 383, 'long_name', 'ironmace', 'Product', 'product_table', 'prod_id', 171, 'long_name', 'iron', '6', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(480, 'Product', 'product_table', 'prod_id', 383, 'long_name', 'ironmace', 'Output', 'skill_table', 'skill_id', 29, 'abbr', 'adm', '1', 'N', '', '', '', '', '', 1),"
                ."(481, 'Product', 'product_table', 'prod_id', 393, 'long_name', 'bronzeaxe', 'Product', 'product_table', 'prod_id', 423, 'long_name', 'coal', '15', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(482, 'Product', 'product_table', 'prod_id', 393, 'long_name', 'bronzeaxe', 'Product', 'product_table', 'prod_id', 165, 'long_name', 'bronze', '4', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(483, 'Product', 'product_table', 'prod_id', 393, 'long_name', 'bronzeaxe', 'Product', 'product_table', 'prod_id', 391, 'long_name', 'club', '1', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(484, 'Product', 'product_table', 'prod_id', 393, 'long_name', 'bronzeaxe', 'Output', 'skill_table', 'skill_id', 29, 'abbr', 'adm', '1', 'N', '', '', '', '', '', 1),"
                ."(485, 'Product', 'product_table', 'prod_id', 382, 'long_name', 'ironaxe', 'Product', 'product_table', 'prod_id', 423, 'long_name', 'coal', '20', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(486, 'Product', 'product_table', 'prod_id', 382, 'long_name', 'ironaxe', 'Product', 'product_table', 'prod_id', 171, 'long_name', 'iron', '4', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(487, 'Product', 'product_table', 'prod_id', 382, 'long_name', 'ironaxe', 'Product', 'product_table', 'prod_id', 391, 'long_name', 'club', '1', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(488, 'Product', 'product_table', 'prod_id', 382, 'long_name', 'ironaxe', 'Output', 'skill_table', 'skill_id', 29, 'abbr', 'adm', '1', 'N', '', '', '', '', '', 1),"
                ."(489, 'Product', 'product_table', 'prod_id', 394, 'long_name', 'steelaxe', 'Product', 'product_table', 'prod_id', 174, 'long_name', 'coke', '20', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(490, 'Product', 'product_table', 'prod_id', 394, 'long_name', 'steelaxe', 'Product', 'product_table', 'prod_id', 180, 'long_name', 'steel', '4', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(491, 'Product', 'product_table', 'prod_id', 394, 'long_name', 'steelaxe', 'Product', 'product_table', 'prod_id', 391, 'long_name', 'club', '1', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(492, 'Product', 'product_table', 'prod_id', 394, 'long_name', 'steelaxe', 'Output', 'skill_table', 'skill_id', 29, 'abbr', 'adm', '1', 'N', '', '', '', '', '', 1),"
                ."(493, 'Product', 'product_table', 'prod_id', 396, 'long_name', 'steel1axe', 'Product', 'product_table', 'prod_id', 174, 'long_name', 'coke', '20', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(494, 'Product', 'product_table', 'prod_id', 396, 'long_name', 'steel1axe', 'Product', 'product_table', 'prod_id', 376, 'long_name', 'steel_1', '4', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(495, 'Product', 'product_table', 'prod_id', 396, 'long_name', 'steel1axe', 'Product', 'product_table', 'prod_id', 391, 'long_name', 'club', '1', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(496, 'Product', 'product_table', 'prod_id', 396, 'long_name', 'steel1axe', 'Output', 'skill_table', 'skill_id', 29, 'abbr', 'adm', '1', 'N', '', '', '', '', '', 1),"
                ."(497, 'Product', 'product_table', 'prod_id', 398, 'long_name', 'steel2axe', 'Product', 'product_table', 'prod_id', 174, 'long_name', 'coke', '20', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(498, 'Product', 'product_table', 'prod_id', 398, 'long_name', 'steel2axe', 'Product', 'product_table', 'prod_id', 377, 'long_name', 'steel_2', '4', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(499, 'Product', 'product_table', 'prod_id', 398, 'long_name', 'steel2axe', 'Product', 'product_table', 'prod_id', 391, 'long_name', 'club', '1', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(500, 'Product', 'product_table', 'prod_id', 398, 'long_name', 'steel2axe', 'Output', 'skill_table', 'skill_id', 29, 'abbr', 'adm', '1', 'N', '', '', '', '', '', 1),"
                ."(501, 'Product', 'product_table', 'prod_id', 21, 'long_name', 'falchions', 'Product', 'product_table', 'prod_id', 423, 'long_name', 'coal', '15', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(502, 'Product', 'product_table', 'prod_id', 21, 'long_name', 'falchions', 'Product', 'product_table', 'prod_id', 165, 'long_name', 'bronze', '5', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(503, 'Product', 'product_table', 'prod_id', 21, 'long_name', 'falchions', 'Output', 'skill_table', 'skill_id', 29, 'abbr', 'adm', '1', 'N', '', '', '', '', '', 1),"
                ."(504, 'Product', 'product_table', 'prod_id', 378, 'long_name', 'ironsword', 'Product', 'product_table', 'prod_id', 423, 'long_name', 'coal', '30', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(505, 'Product', 'product_table', 'prod_id', 378, 'long_name', 'ironsword', 'Product', 'product_table', 'prod_id', 171, 'long_name', 'iron', '5', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(506, 'Product', 'product_table', 'prod_id', 378, 'long_name', 'ironsword', 'Output', 'skill_table', 'skill_id', 29, 'abbr', 'adm', '1', 'N', '', '', '', '', '', 1),"
                ."(507, 'Product', 'product_table', 'prod_id', 387, 'long_name', 'steelsword', 'Product', 'product_table', 'prod_id', 174, 'long_name', 'coke', '30', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(508, 'Product', 'product_table', 'prod_id', 387, 'long_name', 'steelsword', 'Product', 'product_table', 'prod_id', 180, 'long_name', 'steel', '5', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(509, 'Product', 'product_table', 'prod_id', 387, 'long_name', 'steelsword', 'Output', 'skill_table', 'skill_id', 29, 'abbr', 'adm', '1', 'N', '', '', '', '', '', 1),"
                ."(510, 'Product', 'product_table', 'prod_id', 388, 'long_name', 'steel1sword', 'Product', 'product_table', 'prod_id', 174, 'long_name', 'coke', '30', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(511, 'Product', 'product_table', 'prod_id', 388, 'long_name', 'steel1sword', 'Product', 'product_table', 'prod_id', 376, 'long_name', 'steel_1', '5', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(512, 'Product', 'product_table', 'prod_id', 388, 'long_name', 'steel1sword', 'Output', 'skill_table', 'skill_id', 29, 'abbr', 'adm', '1', 'N', '', '', '', '', '', 1),"
                ."(513, 'Product', 'product_table', 'prod_id', 389, 'long_name', 'steel2sword', 'Product', 'product_table', 'prod_id', 174, 'long_name', 'coke', '30', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(514, 'Product', 'product_table', 'prod_id', 389, 'long_name', 'steel2sword', 'Product', 'product_table', 'prod_id', 377, 'long_name', 'steel_2', '5', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(515, 'Product', 'product_table', 'prod_id', 389, 'long_name', 'steel2sword', 'Output', 'skill_table', 'skill_id', 29, 'abbr', 'adm', '1', 'N', '', '', '', '', '', 1),"
                ."(516, 'Product', 'product_table', 'prod_id', 110, 'long_name', 'stoneaxe', 'Product', 'product_table', 'prod_id', 391, 'long_name', 'club', '1', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(517, 'Product', 'product_table', 'prod_id', 110, 'long_name', 'stoneaxe', 'Product', 'product_table', 'prod_id', 41, 'long_name', 'leather', '1', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(518, 'Product', 'product_table', 'prod_id', 110, 'long_name', 'stoneaxe', 'Product', 'product_table', 'prod_id', 163, 'long_name', 'stones', '1', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(519, 'Product', 'product_table', 'prod_id', 110, 'long_name', 'stoneaxe', 'Output', 'skill_table', 'skill_id', 29, 'abbr', 'adm', '1', 'N', '', '', '', '', '', 1),"
                ."(520, 'Product', 'product_table', 'prod_id', 111, 'long_name', 'stonespear', 'Product', 'product_table', 'prod_id', 55, 'long_name', 'shaft', '1', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(521, 'Product', 'product_table', 'prod_id', 111, 'long_name', 'stonespear', 'Product', 'product_table', 'prod_id', 163, 'long_name', 'stones', '1', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(522, 'Product', 'product_table', 'prod_id', 111, 'long_name', 'stonespear', 'Output', 'skill_table', 'skill_id', 29, 'abbr', 'adm', '1', 'N', '', '', '', '', '', 1),"
                ."(523, 'Product', 'product_table', 'prod_id', 400, 'long_name', 'scrapers', 'Product', 'product_table', 'prod_id', 163, 'long_name', 'stones', '1', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 2),"
                ."(524, 'Product', 'product_table', 'prod_id', 400, 'long_name', 'scrapers', 'Output', 'skill_table', 'skill_id', 29, 'abbr', 'adm', '1', 'N', '', '', '', '', '', 2),"
                ."(525, 'Product', 'product_table', 'prod_id', 59, 'long_name', 'sculpture', 'Actives', 'tribes', 'curam', 0, 'tribeid', '0002.00', '4', 'N', 'tribes', 'clanid', 'tribeid', 'tribeid', 'curam', 1),"
                ."(526, 'Product', 'product_table', 'prod_id', 59, 'long_name', 'sculpture', 'Product', 'product_table', 'prod_id', 0, 'long_name', 'aardvark', '5', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(527, 'Product', 'product_table', 'prod_id', 59, 'long_name', 'sculpture', 'Output', 'skill_table', 'skill_id', 29, 'abbr', 'adm', '1', 'N', '', '', '', '', '', 1),"
                ."(528, 'Product', 'product_table', 'prod_id', 112, 'long_name', 'millstone', 'Product', 'product_table', 'prod_id', 163, 'long_name', 'stones', '10', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(529, 'Product', 'product_table', 'prod_id', 112, 'long_name', 'millstone', 'Output', 'skill_table', 'skill_id', 29, 'abbr', 'adm', '1', 'N', '', '', '', '', '', 1),"
                ."(530, 'Product', 'product_table', 'prod_id', 113, 'long_name', 'statue', 'Actives', 'tribes', 'curam', 0, 'tribeid', '0002.00', '10', 'N', 'tribes', 'clanid', 'tribeid', 'tribeid', 'curam', 1),"
                ."(531, 'Product', 'product_table', 'prod_id', 113, 'long_name', 'statue', 'Product', 'product_table', 'prod_id', 163, 'long_name', 'stones', '10', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(532, 'Product', 'product_table', 'prod_id', 113, 'long_name', 'statue', 'Output', 'skill_table', 'skill_id', 29, 'abbr', 'adm', '1', 'N', '', '', '', '', '', 1),"
                ."(533, 'Product', 'product_table', 'prod_id', 160, 'long_name', 'refinery', 'Skill', 'skill_table', 'skill_id', 66, 'abbr', 'eng', '5', 'N', 'skills', '', '', 'abbr', '', 1),"
                ."(534, 'Product', 'product_table', 'prod_id', 160, 'long_name', 'refinery', 'Actives', 'tribes', 'curam', 0, 'tribeid', '0002.00', '20', 'N', 'tribes', 'clanid', 'tribeid', 'tribeid', 'curam', 1),"
                ."(535, 'Product', 'product_table', 'prod_id', 160, 'long_name', 'refinery', 'Structure', 'product_table', 'prod_id', 153, 'long_name', 'meetinghouse', '1', 'Y', 'structures', 'NULL', 'tribeid', 'long_name', 'hex_id', 1),"
                ."(536, 'Product', 'product_table', 'prod_id', 160, 'long_name', 'refinery', 'Product', 'product_table', 'prod_id', 163, 'long_name', 'stones', '100', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 1),"
                ."(537, 'Product', 'product_table', 'prod_id', 160, 'long_name', 'refinery', 'Output', 'structures', 'struct_id', 8, 'long_name', 'refinery', '1', 'N', '', '', '', '', '', 1),"
                ."(539, 'Product', 'product_table', 'prod_id', 164, 'long_name', 'smelter', 'Actives', 'tribes', 'curam', 0, 'tribeid', '0002.00', '1', 'N', 'tribes', 'clanid', 'tribeid', 'tribeid', 'curam', 2),"
                ."(540, 'Product', 'product_table', 'prod_id', 164, 'long_name', 'smelter', 'Structure', 'product_table', 'prod_id', 160, 'long_name', 'refinery', '1', 'Y', 'structures', 'NULL', 'tribeid', 'long_name', 'hex_id', 2),"
                ."(541, 'Product', 'product_table', 'prod_id', 164, 'long_name', 'smelter', 'Product', 'product_table', 'prod_id', 163, 'long_name', 'stones', '5', 'Y', 'products', '', 'tribeid', 'long_name', 'amount', 2),"
                ."(542, 'Product', 'product_table', 'prod_id', 164, 'long_name', 'smelter', 'Output', 'structures', 'subunit', 0, 'long_name', 'refinery', '1', 'N', '', '', '', '', '', 2),"
                ."(543, '', '', '', 0, '', '', 'Skill', 'skill_table', 'skill_id', 0, 'abbr', '', '', 'N', 'skills', '', '', 'abbr', '', 1),"
                ."(544, '', '', '', 0, '', '', 'Skill', 'skill_table', 'skill_id', 0, 'abbr', '', '', 'N', 'skills', '', '', 'abbr', '', 1),"
                ."(545, '', '', '', 0, '', '', 'Skill', 'skill_table', 'skill_id', 0, 'abbr', '', '', 'N', 'skills', '', '', 'abbr', '', 1),"
                ."(546, '', '', '', 0, '', '', 'Skill', 'skill_table', 'skill_id', 0, 'abbr', '', '', 'N', 'skills', '', '', 'abbr', '', 1)");
    echo " Done!<BR>";
    flush();
    echo "Creating gd_rq_tables table....";
    flush();
    $db->Execute("DROP TABLE IF EXISTS $dbtables[gd_rq_tables]");
    $db->Execute("CREATE TABLE $dbtables[gd_rq_tables] ("
                ."`id` int(11) NOT NULL auto_increment,"
                ."`entry_type` enum('res','rq_cl') NOT NULL default 'res',"
                ."`r_type` enum('Intrinsic','Livestock','Product','Resource','Skill','Structure','Prod Unit','Actives','Hex ID','Output') NOT NULL default 'Skill',"
                ."`r_table` varchar(60) NOT NULL default '',"
                ."`r_idf` varchar(50) default NULL,"
                ."`r_keyf` varchar(50) NOT NULL default '',"
                ."`r_goods_tribe` enum('Y','N') NOT NULL default 'Y',"
                ."`cl_table` varchar(60) NOT NULL default '',"
                ."`cl_clanidf` varchar(50) default NULL,"
                ."`cl_tribeidf` varchar(50) NOT NULL default 'tribeid',"
                ."`cl_keyf` varchar(50) NOT NULL default '',"
                ."`cl_valf` varchar(50) NOT NULL default '',"
                ."PRIMARY KEY `id` (`id`)"
                .") TYPE=MyISAM ");
    $db->Execute("INSERT INTO $dbtables[gd_rq_tables] (`id`, `entry_type`, `r_type`, `r_table`, `r_idf`, `r_keyf`, `r_goods_tribe`, `cl_table`, `cl_clanidf`, `cl_tribeidf`, `cl_keyf`, `cl_valf`) VALUES (1, 'res', 'Livestock', 'product_table', 'prod_id', 'long_name', 'Y', '', NULL, '0', '', ''), "
    ."(2, 'res', 'Product', 'product_table', 'prod_id', 'long_name', 'Y', '', NULL, '0', '', ''),"
    ."(3, 'res', 'Resource', 'product_table', 'prod_id', 'long_name', 'Y', '', NULL, '0', '', ''),"
    ."(4, 'res', 'Skill', 'skill_table', 'skill_id', 'abbr', 'N', '', NULL, '0', '', ''),"
    ."(5, 'res', 'Structure', 'product_table', 'prod_id', 'long_name', 'Y', '', NULL, '0', '', ''),"
    ."(6, 'rq_cl', 'Livestock', 'product_table', 'prod_id', 'long_name', 'Y', 'livestock', NULL, 'tribeid', 'type', 'amount'),"
    ."(7, 'rq_cl', 'Product', 'product_table', 'prod_id', 'long_name', 'Y', 'products', NULL, 'tribeid', 'long_name', 'amount'),"
    ."(8, 'rq_cl', 'Resource', 'product_table', 'prod_id', 'long_name', 'Y', 'resources', NULL, 'tribeid', 'long_name', 'amount'),"
    ."(9, 'rq_cl', 'Skill', 'skill_table', 'skill_id', 'abbr', 'N', 'skills', NULL, 'tribeid', 'abbr', 'level'),"
    ."(10, 'rq_cl', 'Structure', 'product_table', 'prod_id', 'long_name', 'Y', 'structures', 'NULL', 'tribeid', 'long_name', 'hex_id'),"
    ."(11, 'rq_cl', 'Prod Unit', 'product_table', 'prod_id', 'long_name', 'Y', 'structures', 'NULL', 'tribeid', 'subunit', 'number'),"
    ."(12, 'rq_cl', 'Actives', 'tribes', 'curam', 'tribeid', 'N', 'tribes', 'clanid', 'tribeid', 'tribeid', 'curam'),"
    ."(13, 'rq_cl', 'Hex ID', 'tribes', 'hex_id', 'tribeid', 'N', 'tribes', 'clanid', 'tribeid', 'tribeid', 'hex_id'),"
    ."(14, 'res', 'Output', '', NULL, '', 'Y', 'products', 'NULL', 'tribeid', 'long_name', 'amount')");
    echo " Done!<BR>";
    flush();
    if( $_REQUEST[mapping] )
    {
        echo "Creating hexes table....";
        flush();
        $db->Execute("DROP TABLE IF EXISTS $dbtables[hexes]");
        $db->Execute("CREATE TABLE $dbtables[hexes] ("
                    ."`hex_id` int(11) NOT NULL,"
                    ."`terrain` text NOT NULL,"
                    ."`n` int(11) NOT NULL default '0',"
                    ."`e` int(11) NOT NULL default '0',"
                    ."`s` int(11) NOT NULL default '0',"
                    ."`w` int(11) NOT NULL default '0',"
                    ."`ne` int(11) NOT NULL default '0',"
                    ."`se` int(11) NOT NULL default '0',"
                    ."`sw` int(11) NOT NULL default '0',"
                    ."`nw` int(11) NOT NULL default '0',"
                    ."`resource` enum('Y','N') NOT NULL default 'N',"
                    ."`res_type` text NOT NULL,"
                    ."`res_amount` int(11) NOT NULL default '-1',"
                    ."`move` int(11) NOT NULL default '0',"
                    ."`safe` set('Y','N') NOT NULL default 'Y',"
                    ."`game` int(11) NOT NULL default '0',"
                    ."`prospect` smallint(2) NOT NULL default '0',"
                    ."`seed` int(10) unsigned zerofill NOT NULL default '1111111111',"
                    ."PRIMARY KEY `hex_id` (`hex_id`)"
                    .") TYPE=MyISAM ");
        echo " Done!<BR>";
        flush();
        echo "Generating map....";
        flush();
        global $seed;
        include( "make_map.php" );
    }

    $safehex = 0;
    $safe = $db->Execute("SELECT * FROM $dbtables[hexes] "
                        ."WHERE hex_id = '$random_hex'");
    while( $safehex < 1 )
    {
        $safeinfo = $safe->fields;
        if( $safeinfo[safe] == 'N' )
        {
            $random_hex = rand( 1, 4096 );
            $safe = $db->Execute("SELECT * FROM $dbtables[hexes] "
                                ."WHERE hex_id = '$random_hex'");
            $db->Execute("UPDATE $dbtables[tribes] "
                        ."SET hex_id = '$random_hex' "
                        ."WHERE tribeid = '0001.00'");
            $safehex = 0;
        }
        else
        {
            $safehex++;
        }
    }

    $db->Execute("INSERT INTO $dbtables[garrisons] "
                ."VALUES("
                ."'',"
                ."'$random_hex',"
                ."'0001',"
                ."'0001.00',"
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

    $products = $db->Execute("SELECT * FROM $dbtables[product_table] "
                            ."WHERE include = 'Y'");
    while( !$products->EOF )
    {
        $productinfo = $products->fields;
        $db->Execute("INSERT INTO $dbtables[products] "
                    ."VALUES("
                    ."'0001.00',"
                    ."'$productinfo[proper]',"
                    ."'$productinfo[long_name]',"
                    ."'0',"
                    ."'$productinfo[weapon]',"
                    ."'$productinfo[armor]')");
        $products->MoveNext();
    }
    $traps = 1000;
    $swords = 100;
    $bronze = 1800;
    $iron = 1200;
    $coal = rand( 1000, 3000 );
    $bows = rand( 50, 250 );
    $wagons = rand( 5, 50 );
    $jerkins = rand( 0, 600 );
    $provs = rand( 15000, 22000 );
    $horse = $horse + rand(100,700);
    $elephant = rand(10,200);
    $goat = rand(5000,7000);
    $cattle = rand(100,400);

    $db->Execute("UPDATE $dbtables[products] "
                ."SET amount = '$traps' "
                ."WHERE long_name = 'traps' "
                ."AND tribeid = '0001.00'");
    $db->Execute("UPDATE $dbtables[products] "
                ."SET amount = '$swords' "
                ."WHERE long_name = 'ironsword' "
                ."AND tribeid = '0001.00'");
    $db->Execute("UPDATE $dbtables[products] "
                ."SET amount = '$bows' "
                ."WHERE long_name = 'bow' "
                ."AND tribeid = '0001.00'");
    $db->Execute("UPDATE $dbtables[products] "
                ."SET amount = '$wagons' "
                ."WHERE long_name = 'wagon' "
                ."AND tribeid = '0001.00'");
    $db->Execute("UPDATE $dbtables[products] "
                ."SET amount = '$jerkins' "
                ."WHERE long_name = 'jerkin' "
                ."AND tribeid = '0001.00'");
    $db->Execute("UPDATE $dbtables[products] "
                ."SET amount = '$provs' "
                ."WHERE long_name = 'provs' "
                ."AND tribeid = '0001.00'");
    $tribeid = "0001.00";
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
$db->Execute("INSERT INTO $dbtables[livestock] VALUES('','$tribeid','Cattle','$cattle')");
$db->Execute("INSERT INTO $dbtables[livestock] VALUES('','$tribeid','Horses','$horse')");
$db->Execute("INSERT INTO $dbtables[livestock] VALUES('','$tribeid','Elephants','$elephant')");
$db->Execute("INSERT INTO $dbtables[livestock] VALUES('','$tribeid','Goats','$goat')");
$db->Execute("INSERT INTO $dbtables[livestock] VALUES('','$tribeid','Sheep','$sheep')");
$db->Execute("INSERT INTO $dbtables[livestock] VALUES('','$tribeid','Pigs','$pigs')");
$db->Execute("INSERT INTO $dbtables[livestock] VALUES('','$tribeid','Dogs','$dogs')");
$armor = rand( 0 , 10 );
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','arm','Armor','a','$tribeid','$armor','')");
$bonework = rand( 0, 10 );
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','bnw','Bonework','a','$tribeid','$bonework','')");
$boning = rand( 0, 10 );
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','bon','Boning','a','$tribeid','$boning','')");
$curing = rand( 0, 10 );
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','cur','Curing','a','$tribeid','$curing','')");
$dressing = rand( 0, 10 );
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','dre','Dressing','a','$tribeid','$dressing','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','fish','Fishing','a','$tribeid','$fishing','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','flet','Fletching','a','$tribeid','$fletching','')");
$dressing = rand( 0, 10 );
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','for','Forestry','a','$tribeid','$forestry','')");
$furrier = rand( 0, 10 );
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','fur','Furrier','a','$tribeid','$furrier','')");
$gutting = rand( 0, 10 );
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','gut','Gutting','a','$tribeid','$gutting','')");
$herding = rand( 0, 10 );
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','herd','Herding','a','$tribeid','$herding','')");
$hunting = rand( 0, 10 );
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','hunt','Hunting','a','$tribeid','$hunting','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','jew','Jewelry','a','$tribeid','$jewelry','')");
$leather = rand( 0, 10 );
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','ltr','Leatherwork','a','$tribeid','$leather','')");
$metalwork = rand( 0, 10 );
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','mtl','Metalwork','a','$tribeid','$metalwork','')");
$metalwork = rand( 0, 10 );
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','min','Mining','a','$tribeid','$mining','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','pot','Pottery','a','$tribeid','$pottery','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','qry','Quarrying','a','$tribeid','$quarry','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','salt','Salting','a','$tribeid','$salting','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','sew','Sewing','a','$tribeid','$sewing','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','seq','Siege Equipment','a','$tribeid','$siege','')");
$skinning = rand( 0, 10 );
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','skn','Skinning','a','$tribeid','$skinning','')");
$tanning = rand( 0, 10 );
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','tan','Tanning','a','$tribeid','$tanning','')");
$waxworking = rand( 0, 10 );
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','wax','Waxworking','a','$tribeid','$waxworking','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','wpn','Weapons','a','$tribeid','$weapons','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','wv','Weaving','a','$tribeid','$weaving','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','wha','Whaling','a','$tribeid','$whaling','')");
$woodwork = rand( 0, 10 );
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','wd','Woodwork','a','$tribeid','$woodwork','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','ldr','Leadership','b','$tribeid','$leadership','')");
$scouting = rand( 0, 10 );
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','sct','Scouting','b','$tribeid','$scouting','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','adm','Administration','b','$tribeid','$administration','')");
$economics = rand( 0, 10 );
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','eco','Economics','b','$tribeid','$economics','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','arc','Archery','b','$tribeid','$archery','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','ath','Atheism','b','$tribeid','$atheism','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','capt','Captaincy','b','$tribeid','$captaincy','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','char','Chariotry','b','$tribeid','$chariotry','')");
$combat = rand( 0, 10 );
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','com','Combat','b','$tribeid','$combat','')");
$diplomacy = rand( 0, 10 );
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','dip','Diplomacy','b','$tribeid','$diplomacy','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','heal','Healing','b','$tribeid','$healing','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','hvyw','Heavy Weapons','b','$tribeid','$heavy_weapons','')");
$horsemanship = rand( 0, 10 );
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
$engineering = rand( 0, 10 );
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','eng','Engineering','c','$tribeid','$engineering','')");
$farming = rand( 0, 10 );
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','farm','Farming','c','$tribeid','$farming','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','flen','Flensing','c','$tribeid','$flensing','')");
$literacy = rand( 0, 10 );
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','lit','Literacy','c','$tribeid','$literacy','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','mtnb','Maintain Boats','c','$tribeid','$maintain_boats','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','mil','Milling','c','$tribeid','$milling','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','mus','Music','c','$tribeid','$music','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','peel','Peeling','c','$tribeid','$peeling','')");
$refining = rand( 0, 10 );
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','ref','Refining','c','$tribeid','$refining','')");
$research = rand( 0, 10 );
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','res','Research','c','$tribeid','$research','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','san','Sanitation','c','$tribeid','$sanitation','')");
$seeking = rand( 0, 10 );
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','seek','Seeking','c','$tribeid','$seeking','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','shb','Shipbuilding','c','$tribeid','$shipbuilding','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','stn','Stonework','c','$tribeid','$stonework','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','glss','Glasswork','c','$tribeid','$glasswork','')");
$db->Execute("INSERT INTO $dbtables[skills] VALUES('','fctl','Fire Control','c','$tribeid','$fire_control','')");


    //echo "<FORM ACTION=$_SERVER[PHP_SELF] METHOD=POST>";
    //echo "<INPUT TYPE=HIDDEN NAME=admin_name VALUE='$_REQUEST[admin_name]'>";
    //echo "<INPUT TYPE=HIDDEN NAME=password VALUE='$_REQUEST[password]'>";
    //echo "<INPUT TYPE=HIDDEN NAME=submit VALUE=true>";
    //echo "<INPUT TYPE=SUBMIT NAME=weather VALUE=Continue>";
    //echo "</FORM><BR>";
    echo "<P>Click <A HREF=../index.php>here</A> to log in.<BR>";
    echo "<A HREF=admin_map_view.php?admin_name=$_REQUEST[admin_name]&password=$_REQUEST[password]&seed=$seed>here</A> to view the map.<BR>";
    echo "Or <A HREF=../help_maps.php>here</A> to view the map info.</CENTER>";
}








$time_end = getmicrotime();
$time = $time_end - $time_start;
if( $time )
{
    echo "<P><CENTER><FONT COLOR=BLACK class=text_small>Served in $time seconds.</FONT></CENTER><BR>";
}

?>

