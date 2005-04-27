<?
/*
error_reporting  (E_ERROR | E_WARNING | E_PARSE | !E_NOTICE);

session_start();
header("Cache-control: private");
require_once("config.php");

page_header("Admin - MSTSCK Processing ...");

$time_start = getmicrotime();

include("game_time.php");

connectdb();
*/
$debug_on = 0;
include("gui/debug.php");

function mstsck_list($tribe_id)
{
	global $db, $dbtables;

// The variable $tribe_id is used as 
// WHERE $tribe_id LIKE '$tribe_id'
// so  mstsck_list($clanid."%") should get all tribe activities in one clan
// and mstsck_list("%") should get all activities for all clans



/* -------------------------------------------------------------------------------------
   THE FOLLOWING CODE SHOULD BE UNCOMMENTED IF THE REMAINDER OF THE FILE RUNS TOO SLOWLY

// This code has NOT been tested

// Get all requirement definitions into an array first so that we
// dont have to keep querying the table

// NOTE
// This may not be practical eventually, since the table is likely to
// hold several thousand records, approx 
// the number of products
// * ave number of entries per method (4)
// * ave number of methods per product (2?)
// At present thats 265*4*2=2060 records

$requirements = $db->Execute("SELECT * FROM ".$dbtables['gd_rq']." "
			."WHERE res_type='Product' "
			."ORDER BY res_type, method, id");

// Put the entries into an array indexed on name and method
$rq = array();
while (!$requirements->EOF)
{
	$entry = $requirements->fields;

	// the next two variables only set for clarity of code
	$res_key = $entry['res_key'];
	$method = $entry['res_key'];

	// Put each rq in the array with an index like "name_method"
	if (!ISSET( $rq[$res_key] ))
	{
		$rq[$res_key] = array();
	}
	if (!ISSET( $rq[$res_key][$method] ))
	{
		$rq[$res_key][$method] = array();
	}
	$rq[$res_key][$method] = $entry;

	if ($entry['rq_type']=='Skill')
	{
		// IF we have already set the skill and level for this method
		if (ISSET( $rq[$res_key][$method]['SkillLevel'] ))
		{
			// and IF the level in this entry is greater
			if  ($rq[$res_key][$method]['SkillLevel'] > $entry[$rq_val] )
			{
				// Set this entry's contents as the definition of the primary skill

				// NOTE
				// This is a dubious assumption, since the primary skill may not be
				// the one with the highest value
				$rq[$res_key][$method]['Skill'] = $entry['rq_key'];
				$rq[$res_key][$method]['SkillLevel'] = $entry['rq_val'];
			}
			// ELSE we dont care because its already set 'correctly'
		}
		else
		{
		// ELSE we have not set the skill and level yet, so set them
			$rq[$res_key][$method]['Skill'] = $entry['rq_key'];
			$rq[$res_key][$method]['SkillLevel'] = $entry['rq_val'];
		}
	}

	$requirements->MoveNext();
}
// We now have an array, $rq, that we can lookup to find out 
// the details of each requirement for each method

$skills_list = $db->Execute("SELECT * FROM $dbtables[skills_table] ");
			."ORDER BY abbr");


------------------------------------------------------------------------------------- */


	// get the details of the tribe(s)
	$tribes = $db->Execute("SELECT * FROM ".$dbtables['tribes']." "
						."WHERE tribeid LIKE '$tribe_id' ");

	while(!$tribes->EOF)
	{
		debug_msg ("Got tribes info");
		$tribe = $tribes->fields;

		//get a list of the tribe's skills
		$skills = $db->Execute("SELECT * FROM ".$dbtables['skills']." "
					."WHERE tribeid='".$tribe['tribeid']."' "
					."ORDER BY abbr ");


		// put them in an array indexed by abbr
		$skill = array();
		while (!$skills->EOF)
		{
			$skill[$skills->fields['abbr']] = $skills->fields;
			$skills->MoveNext();
		}


		// get a list of the activities that the tribe has planned
		$activities = $db->Execute("SELECT * FROM ".$dbtables['activities']." "
									."WHERE tribeid = '".$tribe['tribeid']."' ");
		if (!$activities->EOF)
		{
			$row = 0;
			echo "<TABLE ALIGN=CENTER>"
				."<TR CLASS=color_header>"
				."<TD ALIGN=CENTER COLSPAN=5><B>MSTSCK Analysis</B></TD>"
				."</TR>";
		}

		while (!$activities->EOF)
		{

			$activity = $activities->fields;


			// FIND OUT IF WE CAN ACTUALLY DO THIS

			// get a list of appropriate reqs from gd_rq
			$requirements = $db->Execute("SELECT * FROM ".$dbtables['gd_rq']." "
										."WHERE res_type='Product' "
										."AND res_key='".$activity['product']."' "
										."ORDER BY res_key, method, id");

			// debug - no method of producing product
			if ($requirements->EOF)
			{
				debug_msg ("Attempting to produce <B>".$activity['product']."</B> "
							."but there is no method defined in gd_rq<BR>");
			}
			else
			{
				// get the id of the first available method
				$method = $requirements->fields['method'];
				$met_the = array();
				$met_the[$method] = array();

				$selected_skill = false;

				// process each requirement
				while (!$requirements->EOF)
				{

					$entry = $requirements->fields;


					// analyse one method
					$met_the[$method]['reqs'] = true;
					$met_the[$method]['amount'] = 999999;
					$met_the[$method]['actives'] = 999999;
					$met_the[$method]['actives_reqd'] = 0;
					$met_the[$method]['units'] = 1;

					while ($entry['method']==$method)
					{
						$entry = $requirements->fields;

						// BUILD THE SQL QUERY

						// Determine whether we are meeting this rq from the gt or the tribe
						if ($activity['rq_goods_tribe']=="Y")
						{
							$source = $tribe['goods_tribe'];
						}
						else
						{
							$source = $tribe['tribeid'];
						}

						$sql = "SELECT * FROM ".$dbtables[$entry['cl_table']]." " 
								."WHERE ".$entry['cl_tribeidf']."='$source' ";

						switch ($entry['rq_type'])
						{
							case "Output":

								// We dont need any query to get info
								// since all we need to know is the number
								// of units that we are making per batch
								$sql = "";
								break;

							case "Actives":

								// We dont have a key field for actives, just an amount
								// (the key field in Actives records is an inapplicable tribeid)
								$sql .= "AND ".$entry['cl_valf'].">='".$entry['rq_val']."' ";
								break;
							
							default:
								$sql .= "AND ".$entry['cl_keyf']."='".$entry['rq_key']."' "
										."AND ".$entry['cl_valf'].">='".$entry['rq_val']."' ";
						}

						// Find out if we have to be in the same hex
						if ($entry['rq_hex_id']=="Y")
						{
							$sql .= "AND hex_id=".$tribe['hex_id']." ";
						}

						
						$meet_req = $db->Execute($sql);
						if ($entry['rq_type']<>"Output")
						{
							$meet_req->EOF = false;
						}

						// debug - the tribe does not meet this requirement
						if ($meet_req->EOF)
						{
							debug_msg ("Tribe $tribeid has attempted to produce ".$activity['product']
										." but lacks the ".$entry['rq_type']." ".$entry['rq_key']
										." to do so.<BR>");

							$met_the[$method]['reqs'] = false;
							$met_the[$method]['amount'] = 0;
							$met_the[$method]['delimiter'] = "We lack the ".$entry['rq_type']." ".$entry['rq_key'].".";
						}
						elseif ( $met_the[$method]['reqs'] )
						{
						$meet_req = $meet_req->fields;

						// ELSE the tribe does meet the rq so find out how many it can produce
							$delimiter = $entry['rq_type']." ".$entry['rq_key'];
							switch ( $entry['rq_type'] )
							{
								case "Skill":

								// We should really have fields in the skills_table to let us know
								// what limitations apply to what skills.
								// The following code should, therefore, really be enclosed in an
								// IF statement that checks to see if the limitation it implements
								// is appropriate
								// ie not all skills are limited to actives = 10 * skill level

									$skill_level = $skill[$activity['skill_abbr']]['level'];
									if ($entry['rq_key']==$activity['skill_abbr'])
									{
										// ^^^
										// This is dubious code. Some things use two skills and
										// we have no check to ensure that we are not trying to apply
										// this method when the same skill is used in two different
										// methods for the same product
										// eg smelters can be made via engineering or stonework,
										// but the stonework method also uses engineering so we might
										// get a double hit

										$selected_skill = true;
										if ($skill_level < 10)
										{
											$amount = $skill_level * 10;
										}
										else
										{
											$amount = $met_the[$method]['actives'];
										}

										if ($met_the[$method]['actives'] > $amount)
										{
											if ($met_the[$method]['actives_reqd'] > 0)
											{
												$met_the[$method]['amount'] = $amount / $met_the[$method]['actives_reqd'];
											}
											else
											{
												$met_the[$method]['amount'] = $amount;
											}
											$met_the[$method]['actives'] = $amount;
											$met_the[$method]['delimiter'] = "10 x ".$activity['skill_abbr']." at level ".$skill_level;
										}
									}
									$met_the[$method][ "ID".$entry['id'] ] = $skill_level;
									break;

								case "Actives":

									$amount = $activity['actives'];
									$met_the[$method]['actives_reqd'] = $entry['rq_val'];

									if ($entry['rq_val'] > $amount)
									{
										$met_the[$method]['reqs'] = false;
										$met_the[$method]['amount'] = 0;
										$met_the[$method]['delimiter'] = "We need ".$entry['rq_val']
																		." actives but only assigned "
																		.$activity['actives'].".";
									}
									elseif ($met_the[$method]['actives'] > $meet_req[ $entry['cl_valf'] ])
									{
										if ($met_the[$method]['actives_reqd'] > 0)
										{
											$met_the[$method]['amount'] = $meet_req[ $entry['cl_valf'] ] / $met_the[$method]['actives_reqd'];
										}
										else
										{
											$met_the[$method]['amount'] = $meet_req[ $entry['cl_valf'] ];
										}
										$met_the[$method]['actives'] = $amount;
										$met_the[$method]['delimiter'] = $entry['rq_type'];
									}
									break;

								case "Product":

									// Prevent division by 0 error
									// but also make the mistake of letting them make 1 unit
									// even if the requirement definitions are invalid

									if ( $entry['rq_val'] == 0 || $entry['rq_val'] == "")
									{
										$amount = 1;
									}
									else
									{
										$amount = $meet_req[ $entry['cl_valf'] ] / $entry['rq_val'];
									}
									
									if ($met_the[$method]['amount'] > $amount)
									{
										$met_the[$method]['amount'] = $amount;
										$met_the[$method]['delimiter'] = $delimiter;
									}
									$met_the[$method][ "ID".$entry['id'] ] = $entry['rq_val'];
									break;

								case "Prod Unit":

									// We can usually only assign as many actives as the
									// number of Prod Units * 10, so make sure of that ...

									$amount = $meet_req[ $entry['cl_valf'] ] * 10;
									echo "we have ".$meet_req[ $entry['cl_valf'] ]."stills";
									if ($met_the[$method]['actives'] > $amount)
									{
										if ($met_the[$method]['actives_reqd'] > 0)
										{
											$met_the[$method]['amount'] = $amount / $met_the[$method]['actives_reqd'];
										}
										else
										{
											$met_the[$method]['amount'] = $amount;
										}
										$met_the[$method]['actives'] = $amount;
										$met_the[$method]['delimiter'] = "10 x ".$entry['rq_key']
																		." at ".$meet_req[ $entry['cl_valf'] ];
									}

									// Prevent division by 0 error
									// but also make the mistake of letting them make 1 unit
									// even if the requirement definitions are invalid

									if ( $entry['rq_val'] == 0 || $entry['rq_val'] == "")
									{
										$amount = 1;
									}
									else
									{
										$amount = $meet_req[ $entry['cl_valf'] ] / $entry['rq_val'];
									}
									
									if ($met_the[$method]['amount'] > $amount)
									{
										$met_the[$method]['amount'] = $amount;
										$met_the[$method]['delimiter'] = $delimiter;
									}

									$met_the[$method][ "ID".$entry['id'] ] = $entry['rq_val'];
									break;

								case "Output":

									if ($entry['rq_val']<>0 && $entry['rq_val']<>"")
									{
										$met_the[$method]['units'] = $entry['rq_val'];
									}
									break;

								default:
							}
						}

						$last_method = $method;
						$requirements->MoveNext();
						$method = $requirements->fields['method'];

					} // END WHILE METHOD

					mstsck_analysis($selected_skill, $last_method, $met_the, $activity, $row);

				} // END WHILE !reqs->EOF
				  // We have processed all the requirement entries for this method

			} // END IF !reqs->EOF

			$activities->MoveNext();

		} // END WHILE !activities->EOF

		$activities->MoveFirst();
		if (!$activities->EOF)
		{
			echo "</TABLE>";
		}

		$tribes->MoveNext();

	} // END WHILE !$tribes->EOF

} // END mstsck_list()


function mstsck_analysis(&$selected_skill, $last_method, $met_the, $activity, &$row)
{
	global $db, $dbtables;

	if ($selected_skill)
	{
		$rc = $row % 2;
		$row++;

		if ($met_the[$last_method]['amount'] > 0)
		{
			$amount = $met_the[$last_method]['amount'];
		}
		else
		{
			$amount = 0;
		}

		$total_amount = $amount * $met_the[$last_method]['units'];

		if (!$met_the[$last_method]['reqs'])
		{
			echo "<TR CLASS=color_row$rc>"
				."<TD COLSPAN=5>"
				."<B>".$activity['product']."</B> (".$activity['skill_abbr']."). "
				.$met_the[$last_method]['delimiter']
				."</TD>"
				."</TR>";
		}
		elseif ($met_the[$last_method]['reqs'])
		{
			echo "<TR CLASS=color_row$rc>"
				."<TD COLSPAN=5>"
				."<B>".$activity['product']."</B> ("
				.$activity['skill_abbr'].") - producing "
				.$amount
				." x "
				.$met_the[$last_method]['units']
				." unit(s) = <B>"
				.$total_amount
				."</B><BR>limited by "
				.$met_the[$last_method]['delimiter']
				.".</TD>"
				."</TR>"
				."<TR CLASS=color_row$rc>"
				."<TD>&nbsp;</TD>"
				."<TD><B>&nbsp;Req Type&nbsp;</B></TD>"
				."<TD><B>&nbsp;Require&nbsp;</B>"
				."<TD><B>&nbsp;For 1&nbsp;</B>"
				."<TD><B>&nbsp;Used&nbsp;</B>"
				."</TR>";

			$reqs = $db->Execute("SELECT * FROM $dbtables[gd_rq] "
								 ."WHERE res_key='".$activity['product']."' "
								 ."AND method='$last_method' "
								 ."AND res_type='Product' ");
			$num_reqs = $reqs->rowcount();

			while (!$reqs->EOF)
			{

				$this_req = $reqs->fields;
				//echo "<FORM METHOD=POST ACTION=mstsck_report_prob.php>";
				echo "<TR CLASS=color_row$rc>";
				echo "<TD>&nbsp;"
					//."<INPUT TYPE=HIDDEN NAME=res_type VALUE=\"Product\">"
					//."<INPUT TYPE=HIDDEN NAME=res_key VALUE=\"".$activity['product']."\">"
					//."<INPUT TYPE=HIDDEN NAME=method VALUE=".$last_method.">"
					//."<INPUT TYPE=SUBMIT NAME=op VALUE=Report>"
					."&nbsp;"
					."</TD>"
					."<TD VALIGN=TOP>&nbsp;".$this_req['rq_type']."</TD>"
					."<TD VALIGN=TOP>&nbsp;";
					switch ($this_req['rq_type'])
					{
						case "Actives":
							echo "&nbsp;";
							break;
						case "Output":
							echo "&nbsp";
							break;
						default:
							echo $this_req['rq_key'];
					}								
				echo "</TD>"
					."<TD VALIGN=TOP ALIGN=RIGHT>&nbsp;";
					switch ($this_req['rq_type'])
					{
						case "Output":
							echo "&nbsp";
							break;
						default:
							echo $this_req['rq_val'];
					}								
				echo "</TD>"
					."<TD VALIGN=TOP ALIGN=RIGHT>";
					switch ($this_req['rq_type'])
					{
						case "Skill":
							$amount = $met_the[$last_method][ "ID".$this_req['id'] ];
							break;

						case "Actives":
							$amount = $met_the[$last_method]['actives_reqd'] * $met_the[$last_method]['amount'];
							break;

						case "Structure":
							$amount = 1;
							break;

						case "Prod Unit":
							$amount = $met_the[$last_method]['actives'] / 10;
							break;

						case "Product":
							$amount = $met_the[$last_method][ "ID".$this_req['id'] ] * $met_the[$last_method]['amount'];
							break;

						case "Output":
							$amount = $met_the[$last_method]['amount'] * $this_req['rq_val'];
							break;

						default:
					}

				echo "$amount"
					."</TD>"
					."</TR>"
					."</FORM>";
				$reqs->MoveNext();

			} // END WHILE !reqs->EOF

		} // END IF reqs met

		echo "<TR CLASS=color_pg height=3><TD></TD></TR>";

	} // END IF selected_skill

	$selected_skill = false;
	
} // END FUNCTION mstsck_analysis();

?>
