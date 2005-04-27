<?
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: faq.php

include("config.php");

page_header("FAQ");

echo "<CENTER>";

navbar_help();

?>

<TABLE cellSpacing=0 width="100%" border=0>
  <TBODY>
  <TR>
  <TR></TR></TBODY></TABLE><BR>
<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
  <TBODY>
  <TR>
    <TD class=header align=middle width="25%"><A 
      href="#new">New Players</A>
    <TD>
    <TD class=header align=middle width="25%"><A 
      href="#misc0">Misc</A>
    <TD>
    <TD class=header align=middle width="25%"><A 
      href="#qa">Q&amp;A</A>
    <TD></TD></TR></TBODY></TABLE><BR>
<TABLE cellSpacing=0 width="100%" border=0>
  <TBODY>
  <TR>
    <TD class=header>Introduction: </TD></TR>
  <TR>
    <TD ALIGN=CENTER>
      <P>Welcome to the TribeStrive FAQ. This version was last updated 01.07.04 
 </P></TD></TR></TBODY></TABLE><BR>
<TABLE cellSpacing=0 width="100%" border=0>
  <TBODY>
  <TR>
    <TD>Table of Contents: </TD></TR>
  <TR>
    <TD>
      <OL type=I>
        <LI><A 
        href="faq.php#new">For 
        Everyone:</A> 
        <OL type=i>
          <LI><A 
          href="faq.php#new2">The 
          Rules</A> 
          <LI><A 
          href="faq.php#new3">More 
          Info</A> </LI></OL><BR><BR>

        <LI><A 
        href="faq.php#misc0">Misc:</A> 
        <OL type=i>
          <LI><A 
          href="faq.php#misc1">Starting Out</A> 
          <LI><A 
          href="faq.php#misc2">Getting Rolling</A> 
          </LI>
          <LI><A href="faq.php#misc3">Now What?</A></OL><BR><BR>

        <LI><A 
        href="faq.php#qa">Questions and 
        Answers:</A> 
        <OL type=i>
          
</LI></OL><BR><BR></LI></OL></TD></TR></TBODY></TABLE><BR><A name=new></A>
<TABLE cellSpacing=0 width="100%" border=0>
  <TBODY>
  <TR>
    <TD ColSpan=3>For Everyone: </TD></TR>
  <TR>
    <TD colSpan=3>
      <P>This is the section to read if you are a new player. 
      <BR><BR></P></TD></TR>
  <TR>
    <TD width="5%">&nbsp;</TD>
    <TD width="90%"><A name=new2></A>The Rules</TD>
    <TD width="5%">&nbsp;</TD></TR>
  <TR>
    <TD width="5%">&nbsp;</TD>
    <TD width="90%">
      <P>These are the official rules for the game. As this is a web based game 
      it's fairly hard to enforce these rules. But when it happens, I come down
      like the hand of god. No mercy, extreme prejudice, since the offense ruins
      the game for others who legitimately play. 
      <OL>
        <LI>You are not allowed to have multiple accounts. In other words, if 
        you have more than one Clan in the game you are breaking the rules. As 
        above, if you need two accounts to test a theory, host your own game. If 
        you are caught with multiple accounts your tribes will be deleted, along
        with any tribes that appear to have benefitted from your abuse of the
        rules, or may have also known about the abuse.
        <LI>Usernames can be whatever you choose. I do ask that you use appropriate
        names for your Clan, your Chief, your Religion. Anachronistic names will be
        changed to something that *I* deem fitting if you push this too far, and you
        may not like what I tag you with. 
        <LI>If you find a bug it is against the rules to exploit it. You must 
        report it right away to the game administrators and preferably to the forums as 
        well. </LI></OL>
      <BR><BR></P></TD>
    <TD width="5%">&nbsp;</TD></TR>
  <TR>
    <TD width="5%">&nbsp;</TD>
    <TD width="90%"><A name=new3></A>More Info</TD>
    <TD width="5%">&nbsp;</TD></TR>
  <TR>
    <TD width="5%">&nbsp;</TD>
    <TD width="90%">
      <P>You are going to have more questions. I say this with a fair amount of 
      certainty. There are two really good sources of answers I know of other 
      than this FAQ. First, you can always send a message to one of the top  
      players in the game. They should know the answer, but they may not bother 
      to reply. The second source is the <A 
      href="../confed/downloads/Mandate.doc">"Mandate"</A>. This is the Rulebook
      for the PBEM version of the TribeNet game, which this webgame attempts to follow
      closely after. Not everything in the Mandate will be reflected accurately here,
      but the gross majority of rules, philosophies, and ideas are similar. There is
      also an official <A HREF="<? echo "$link_forums"; ?>">forum</A> but you
      need to register as a user <A HREF="<? echo "$link_forums"; ?>">here</A> to post or read.

      <BR><BR></P></TD>
    <TD width="5%">&nbsp;</TD></TR></TBODY></TABLE><BR><A name=strategies></A>
<TABLE cellSpacing=0 width="100%" border=0>
  <TBODY>
  <TR>
    <A name=misc0></A>
    <TD class=header colSpan=3><FONT COLOR=WHITE>Starting Out:</FONT> </TD></TR>
  <TR>
    <TD colSpan=3>
      <P><A name=misc1></A>The initial information you receive is quite bare bones.  You are given an inventory of your tribe's goods  
and belongings, a list of its basic skills, and the tribe's location. When creating your clan, you are given a very
simple choice of a small amount of Bronze, or a smaller amount of Iron. A small amount of Traps or a smaller amount
of Swords.</P>
      <P>Building this tribe is not the same type of thing as building a character in Dungeons and Dragons.  Where the 
D & D character goes right into action and the building of him requires little long range planning, the building 
of the tribe requires some planning - though detailed planning may be left until later.   
</P>
<P>There are some basic requirements that must be met.  First and foremost is feeding the tribe.  The tribe can 
indeed starve, and weather is not necessarily your friend.  The rules will tell you what materials and skills you 
need for feeding your tribe, but little more.  There are basically four ways to keep your people fed.</P>

<OL>
<LI>Hunting.  This is the simplest method of getting food.  Sometimes called Hunting/Gathering.  Tools needed are simple; Spears, Bows, Traps, Snares, Slings.  This is also the method that gives the best returns for the least investment of time, though it is labor intensive.  When you are dividing up your extra skill points you want Hunting skill levels to be the highest you can purchase.</LI>
<BR><BR><LI>Herding.  This is one of the most efficient methods of acquiring food and gives you additional goodies like Skins (for Leather), Gut (good for bowstrings and rope), and Bone (Spears, Armor, and a type of Shield).   It is also less labor intensive than hunting and requires less of an investment into manufactured goods.  But it requires Herding Skill, which you must make sure is as high as you can initially purchase, and animals.  The animals you can do little about.  You get some at the start of the game, and you can acquire more over time, but you are first and foremost required to pursue intelligent herd management.  At the games start your animals are an investment and safety valve, and you must use them sparingly until you get enough to live off of.</LI>
<BR><BR><LI>Farming.  This has potential for feeding vast quantities of tribes.  With full development farming can allow one tribe to feed several others.  But it is an enormous initial investment in both skills and manufactured items and truly nails you in one place.  A mobile tribe can hunt and herd.  A mobile tribe cannot farm.  You must have mills, bakeries, plows, and scythes, and you must have enormous skill levels in the operation of those items.  This is definitely the province of a highly advanced and settled tribe.</LI>
<BR><BR><LI>
Fishing.  Like farming this requires an enormous investment in skills and goods.  You must have your eyes to the sea to pursue this.  You need ships, nets, and seamanship and navigation skills, not to mention shipwright skills and shipyards.  This is, like farming, a full blown direction for a tribe.  </LI></OL>

<P>
As you start up your new tribe you are best served by sticking to Hunting, and guard your herds for a future as a herder. </P>

<P>
<A name=misc2></A>
<FONT COLOR=WHITE>Getting Rolling</FONT><BR>
Your next concerns are finding our where you are, and growing.  You don't have to worry about being molested for your first twenty four turns, there is a rule that a tribe cannot be attacked for that period when they start.  But you cannot sit on your hands.  The continent of Mangalia has tribes that have been on there a long time and are very advanced.  They can roll over a primitive tribe and will barely get their sandals dirty.  And growing does require finding out where you are.  You need Iron, Wood, and Coal.  That is the Holy Trinity of Tribe Net when it comes to getting on your feet and making something of yourself.  Those also require some skills to make use of.</P>
<P>
You will need Engineering skill to build smelters.  You will need Refining skill to use those smelters.  You will need Mining skill to pull the good stuff out of the ground.  You will need Metalworking skill to make the tools you need to mine.  And you will need weapons and armor skill to make weapons.  Some of these you can get with your initial start-up points, but some you will need to acquire using your skill attempts.</P>
<P>
Skill attempts are made every turn.  There is a Primary attempt and a Secondary attempt.  The primary attempt uses a percentage chance based on the level of skill you want.  The higher the skill level the lower the chance.   The secondary attempt is based on the same basic calculation - but is halved.  A primary attempt for a Level 1 skill is 100%, a secondary attempt is 50%.  Each level after that for a primary skill is -10%, and for a secondary skill is -5%.   You will always be working on getting more skill levels.</P>
<P>
And you will need to find where you are and where things are.  This uses a thing called Scouting skill.  You want that up there, but can live with lower levels for a while.  The skill you want to make sure you have right away is Diplomacy.  With a Level 2 skill in Diplomacy you can create a little goodie called a Subtribe.  These are your game versions of the Lewis and Clark expedition.  With it, you can send them out into the great unknown to find minerals and coal.  This subtribe will soon prove itself worth its weight in gold.</P>
<P>
From there you hunt all you can, make sure your animals are herded, and have your tribe and your subtribe scout their little hearts out.</P>
<P>
Terrain will affect how your tribe performs.  Prairie is best for animals breeding, but the hunting is awful. Forests improve hunting, but your breeding of animals is not as good.  Jungles are hunters paradises, and supply any wood product you may ever want, but animals do not breed for beans in jungles.  Mountains are terrible places to be in and you quickly understand why mountain folks are so poor.  Deserts are awful.  Swamps are unhealthy and you never want to end up in a swamp.</P>
<P>
Hunt for all you are worth during the spring and summer months (months 1 through 6).  Fall months (7-9) see a reduction in returns, and winter (months 10-12) are horrible times for hunting and breeding.  You want those months to live off your surplus.</P>
<P>
When you finally get to that sweet spot that has Iron, Coal and woods, then you have some new tasks ahead of you.  You must build a palisade (Engineering skill to build and Forestry to get the wood).   You must build wells within the palisade (this cannot be overstressed),  and you must build refineries.  Since Tribe Net follows a "1 mineral to a hex" rule you will have to get more Diplomacy skill to be able to exploit 2 hexes at once.  This will allow you to create subtribes.  And with this you will mine coal and iron, and log, and you will refine the iron ore, then make swords, shields, helms, chain mail, and all those other goodies.  If your production plan is sound you will eventually have a tribe equipped with Iron Weapons and Iron Armor and with enough horses for cavalry and elephants to haul your goods.  And you must have Fletching skill to make iron tipped arrows for your bows.  Now you are ready to begin conquering the world.</P>
<BR><BR><P>
<a name=misc3></a><FONT COLOR=WHITE>Now What?</FONT><BR>
Your tribe is now safely established on a conifer hill with a coal deposit and it is mining and refining iron with as much efficiency as you can manage.  Your herds are growing and your hunting returns are allowing you to concentrate solely on production for the entire winter.  Your armor skills allow you to make Chain Mail, and you are making sure that every warrior is equipped with Shield, Helm, and your leatherworkers ensure that you have Trews (a leg armor) for every warrior.  Your warriors have swords, spears, and horsebows for your cavalry.  You can field 2,500 fully equipped warriors and you have been busting your tribes butt for a long long time.  Your subtribe sits in its own palisade and mines your iron, sending elephant caravans constantly to your main fort, and they are close enough to have the caravan arrive the same turn it is sent out.  Both main tribe and subtribe run refineries to maximize production.  Your hunters are fully equipped with traps, and you can even make liquor to raise your morale a bit.</P>
<P>
Your tribe is running like a well-oiled machine.  The next question is "Now what?"    You have scouted around you and have found nothing.  You are all alone in your little area.  The question rises unbidden in your head "What would Ghengis Khan do?"</P>
<P>
Ghengis Khan did not spring fully formed from the breast of Mongolia.  He had to spend a while developing the coalition that turned into the Mongol hordes.  And you will too.  But unlike so many other games, Tribe Net requires you to do the actual work.</P>
<P>
One of the most important lessons of Tribe Net is that isolation breeds nothing.  An isolated tribe can remain safe and secure, but with that safety and security it also makes the tribe moribund.  The tribe can grow in power, and develop huge potential, but that is exactly where it will remain.  The owner will eventually tire of the bookkeeping because the game will only consist of that.  Tribe Net is not for the player who expects to be entertained by others.  The player must establish relations of some sort with other tribes.</P>
<P>
You want to conquer the world?  Even Ghengis Khan had to create a coalition to get the Mongol Hordes started.  And you must create that coalition by careful diplomacy.  You must establish a relationship with your neighbors, finding out who is gregarious and who is not, and through them establish a network of friends among the other tribes.  Tribe Net is the ultimate limited intelligence game, and you will know little unless you are talking to players.  You will find many players willing to talk to you too because they suffer from the same lack of solid intelligence information that you do.  This single act is the most important and largest step taken on the road of success in Tribe Net.</P>
<P>
During the course of this effort you will also find out who can be your friends and who are likely to be your enemies.</P>
<P>
Your first goal is to establish an alliance or solid working relationship with at least one other Clan.  For both offense and defense, clans do better with company.  This can mean pulling up stakes and moving to another location to be with a second clan, or inviting another clan to move over to where you are.  The more the merrier.</P>
<P>
The thing that will solidify a collection of clans like no other is religion.  Nothing can understate the importance of religion in Tribe Net.  Although game rules don't impel a clan toward a religion,  the advantages of clans operating with a religion are strong and obvious enough to be powerfully persuasive.  Like in the ancient times, a clan's affiliation is more intertwined with their religion than any nationalist feelings.  This is a pre-nationalist environment.</P>
<P>
     A grouping of tribes can come up with a rough outline of a state, however.  Each tribe working with the other tribes in consort can create an industrial base that allows a number of tribes to pull free of their moorings and operate mobile armies.  A collection of six tribes can have two of them concentrate on the production side of things, and the other four and assemble their fighting forces into well equipped armies that can cause all sorts of mayhem on the continent.</P>
<P>
Good performance in combat requires a slew of skills.  You must have Leadership, Combat, Archery, Horsemanship, and Tactics for effective field combat.  You have to have high Scouting skills and Spying skills to effectively operate over a wide swath of territory, and you have to have Security skill for defense.  Furthermore if you wish to take someone down who is in a palisade or walled fort you must have Siege Equipment skill to make the necessary engines and Heavy Weapons skill to use them effectively.</P>
<P>
Leadership is the most important skill and should be the highest skill you have, or peer to the highest.  Combat skill is needed for the Melee fighting, and Archery is needed for bows and slings.  Horsemanship is needed for your cavalry, which also makes use of archery skill for its horse bows.  In order to find a tribe and attack from a distance you need the ability to "locate".  This is a job where scouts (with Locate orders) find a target, and once "located" the army attacks the target.  However, since your army never actually leaves its location at the end of the turn it looks more like an air strike with your army's location looking more like the airfield.  This method of attack spares you the need to be in the same hex as your target.  This ability requires Scouting and Spying skill.  Now remember that an enemy can do the same to you, so you want to be able to stop scouts "locating" you.  This is done by assigning warriors to Suppression, and in that job they attack and kill/capture any scouts entering your hex.  This requires Scouting and Security skills.   If any of those skills are less than 5 you have no business on the battlefield.  Ideally you want them to be 6 or more.</P>
<P>
And to make matters messier, you or your opponents can run "Raids".  This is like scouting where instead of just looking the scouts bust things up a bit.  This is stopped by Security forces, which require Security skills.</P>
<P>
Being a conquering hero requires a great deal of preparation and is not a simple task.  But the more you do it the better you get as your tribe develops "Terrain Proficiencies".  This is accompanied by a Morale bonus.  But then again, you can also gain a lot of enjoyment from simply being part of the process.</P>


      </TD></TR>
    <TD width="5%">&nbsp;</TD></TR></TBODY></TABLE><A name=qa></A>
<TABLE cellSpacing=0 width="100%" border=0>
  <TBODY>
  <TR>
    <TD colSpan=3>Questions and Answers: </TD></TR>
  <TR>
    <TD colSpan=3>
      <P>When people send me questions, I'll answer them here. I'll reprint the 
      question and answer it to the best of my ability. <BR><BR></P></TD></TR>
  <TR>
    <TD width="5%">&nbsp;</TD>
    
    <TD width="5%">&nbsp;</TD></TR>
  <TR>
    <TD width="5%">&nbsp;</TD>
    <TD width="90%">
      <P> <BR><BR></P></TD>
    <TD width="5%">&nbsp;</TD></TR></TBODY></TABLE>
<?
navbar_help();

page_footer();
?>
