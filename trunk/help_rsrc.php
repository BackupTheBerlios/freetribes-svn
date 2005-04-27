<?
session_start();
header("Cache-control: private");

if (!ISSET($_SESSION['theme']))
{
	$_SESSION['theme'] = $theme_default;  // This must be set on this page before the header is included
}

include("config.php");

page_header("Resources and Production Help");

connectdb();

echo "<CENTER>";

navbar_help();

echo "<BR><BR>";
echo "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=\"70%\"><TR><TD>";


echo "<DT><FONT COLOR=WHITE>Not Here yet</FONT>:</DT>";
echo "<DD>This help page has yet to be written.</DD>";

echo "<P>";
echo "<FONT COLOR=WHITE>But in the meantime, I will place here some of the stuff that I've been working on:</FONT><BR>";

echo "<DT><FONT COLOR=WHITE>Tuesday, May 11th</FONT><DT><DD>Okay, I had a typo in a SQL statement, which I then went and copy/pasted into all the ";
echo "following parts of the skin/gut/bone... doh! But, it's been fixed, and I believe I manually made up for it giving those folks who tried to ";
echo "skin/bone/gut the number of skins/bones/gut that they were supposed to. I also saw someone tried to make some haubes, so I went and coded ";
echo "them as well. I'll try to keep on top of the activities table, and use that as my guide as to what needs to be coded next. I had to go and ";
echo "disable the score function as it was killing the game each night with so many players to run through. I'll have to re-think a way to implement ";
echo "that doesn't take forever and a day to accomplish.</DD>";

echo "<DT><FONT COLOR=WHITE>Monday, May 10th</FONT></DT><DD>Pretty much done with combat. Still a few kinks to be worked out, but it should be ";
echo "pretty much the way it is now. I threw together boning and bonework tonight, along with skin/gut/bone. Gonna kick around and do some little ";
echo "superficial touches here and there for the rest of the night, but mostly keep my hands off until I'm sure none of my changes I made today ";
echo "screwed up the nightly processing of orders since I officially announced the beta release of TribeStrive v. 0.9!</DD>";

echo "<DT><FONT COLOR=WHITE>Wednesday, May 5th</FONT></DT><DD>Combat is finally almost completed!! Yes, that's right! The combat itself is done, ";
echo "I just have to code a little more to add reporting, some additional sanity checks, and figure out how to calculate the looting after a ";
echo "winner is determined. 2,746 lines of code, just for the main combat page, and I'm probably looking at another 3-5 hundred lines of code ";
echo "to get all the looting and reporting sorted out.</DD>";

echo "<DT><FONT COLOR=WHITE>Tuesday, April 13th</FONT></DT><DD>There seems to be a bug with diplomacy, I don't have it worked out, but I believe it to ";
echo "be caused by spaces in a chief's name or something. If you see it happen, give me a yell ingame or email me. I started the combat code, and it's ";
echo "mind boggling. I've managed to get archer phase coded, but still need to figure out some things like how a garrison unit picks a valid target ";
echo "and how armor will be figured into the mix, and how valid attackers will be chosen... yeah, got my work cut out for me. Besides that, I am also ";
echo "working on getting the newbie code working correctly, so when the combat code is finished, you won't be able to attack a young clan until they ";
echo "reach 1 game year old. I did manage to fix the online flag in heraldry, which was broken when I tried to recycle a table column that was still ";
echo "being used for something else. Tinkering here and there, doesn't look like I did that much, but a lot of time went into it.</DD>";

echo "<DT><FONT COLOR=WHITE>Friday, April 9th:</FONT></DT><DD>Been at it all week, reset quite a few times, and have hammered out a couple of bugs that have been bothering me for the longest time. Alliances can now be made, but beware, high level spies will know about it! Also, those tribes with high diplomacy will be able to ascertain which clans thier allies are also allied with. I've laid all the ground work for coding the combat... just have to get into it and do it. After that, I'll be hitting the Religion function and then cleaning up some more of the item production so you can make all those weapons and armor to kill each other with. I have also redone the way tribes and subtribes are numbered (tribe 0001 becomes 0001.0 and tribe 1001 becomes 0001.1) This allows for a larger player population, and also allows for a much greater number of subtribes possible per clan.</DD>";
echo "<DT><FONT COLOR=WHITE>Friday, April 2nd:</FONT></DT><DD>I know, been a while since I was here working on the game. But good news! I have gone and coded the fair! That's right, turns 4 and 10 of each year you can impersonate your favorite girl by hitting the malls! Well, you can at least pick of some of the equipment that I haven't coded yet... specific instructions and notes about the fair should be on the skills page soon under economics.</DD>";
echo "<DT><FONT COLOR=WHITE>Monday, January 26th:</FONT></DT> <DD>I've fixed a bug that's been bothering me where the oceans, lakes, high snowy mountains and low conifer mountains *should* be showing up in your map without travelling over them but were not. Seems I goofed and forgot to close a second parenths in my SQL update. Fixed it, and it's fine now.</DD>";
echo "<DT><FONT COLOR=WHITE>Sunday, January 25th:</FONT></DT> <DD>I noticed that tribe 0018, or \"The Borg Collective\" seems to have done something funky, and his last seen was hosed up, and his tribe number was set as '0000'. If you can let me know how that happened or what you did when it happened, or any information about it, I would appreciate it.</DD>";
echo "<DT><FONT COLOR=WHITE>Sometime last week</FONT></DT><DD>I unlisted all the skills that have not been coded yet. Too many people were using them and getting thier activities messed up. Now, only those activities that have been coded will show up.</DD>";



echo "</TD></TR></TABLE><BR>";

navbar_help();

if( $_SESSION[clanid] )
{
    TEXT_GOTOMAIN();
}
echo "</CENTER>";

page_footer();

?> 
