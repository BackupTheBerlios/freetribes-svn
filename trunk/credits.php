<?
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: credits.php

session_start();
header("Cache-control: private");

include("config.php");

page_header("Credits and Acknowledgements");

connectdb();

echo "<CENTER>";

navbar_help();
echo "<BR><BR>";
echo "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=\"70%\"><TR><TD>";

echo "<DT><FONT COLOR=WHITE>Acknowledgements</FONT>:</DT>";
echo "<DD> - I would like to take this opportunity to thank all of those who provided input in whatever manner, from the caustic email to the swift kick in the pants. It's been a learning experience for me, and hope it to be an enjoyable experience for the players.<BR><BR> - The game began as a recoded version of <a href=http://www.blacknova.net>BlackNova Traders</a> which quickly began to not be such a good fit as first thought. I kept some very basic ideas from blacknova, but the vast majority of the code has been reworked and the backend database is completely different. The messaging and the leaderboard are about the only two pages that spring to mind that even come close to being anything near similar to what I had begun with and not have immediate plans on changing (login is still very BNT). That being said, you should do yourself a favor and check out any one of the many BNT servers. The game, with proper admins, is very addictive and my hat goes off to Mr. Harwood and all the developers involved in the BNT project.<BR><BR> - The TribeNet PBEM (<B>P</B>lay <B>B</B>y <B>E</B>-mail) game is an amazing game. The history of the game predates the popularity of the net and email. It would be worth your while to email one of the Moderators below to inquire about playing.<BR></DD>";
echo "<BR>";
echo "<DT><FONT COLOR=WHITE>TribeNet PBEM Credits</FONT>:</DT>";
echo "<DD><A HREF=mailto:%74%72%69%62%65%6E%65%74%40%6E%65%74%73%70%61%63%65%2E%6E%65%74%2E%61%75>Peter Rzechorzek</a> - Moderator, Rules Development</DD>";
echo "<DD><A HREF=mailto:%74%72%69%62%65%76%69%62%65%73%40%6F%7A%65%6D%61%69%6C%2E%63%6F%6D%2E%61%75>Andrew Davey</a> - Moderator, Rules Development</DD>";
echo "<DD><A HREF=mailto:%64%6F%72%73%61%69%31%40%62%69%67%70%6F%6E%64%2E%63%6F%6D%2E%61%75>Jeff Fallon</a> - Moderator, Rules Development, Programming</DD>";
echo "<DD>Max Nieuwenhuizen - Rules Development</DD>";
echo "<DD>Ian Northey - Programming</DD>";
echo "<DD>Jeff Perkins -Original Game Design and Rules</DD>";
echo "<BR><BR>";
echo "<DT><FONT COLOR=WHITE>BlackNova Traders</FONT>:</DT>";
echo "<DD><A HREF=mailto:%77%65%62%6D%61%73%74%65%72%40%62%6C%61%63%6B%6E%6F%76%61%2E%6E%65%74>Ron Harwood</a> - Copyright Owner</DD>";
echo "<DD>Entire BlackNova Developer's Crew</DD>";



echo "</TD></TR></TABLE><BR>";

navbar_help();

if( $_SESSION[clanid] )
{
    TEXT_GOTOMAIN();
}
echo "</CENTER>";

page_footer();

?> 
