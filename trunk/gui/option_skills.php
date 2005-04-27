<?php

function option_skills($skillgroup)
{
	global $db, $dbtables;

	$skillgroup= strtolower($skillgroup);
	$skills = $db->Execute("SELECT * FROM $dbtables[skill_table] "
							."WHERE $dbtables[skill_table].group = '".$skillgroup."' "
							."ORDER BY long_name");

	$skillgroup = strtoupper($skillgroup);
        if( $skillgroup == 'A' )
        {
	    echo "<OPTION VALUE=\"g_$skillgroup\" DISABLED SELECTED> --------".$skillgroup."-------- </OPTION>";
        }
        else
        {
            echo "<OPTION VALUE=\"g_$skillgroup\" DISABLED> --------".$skillgroup."--------</OPTION>";
        }

    while( !$skills->EOF )
	{
       $skillinfo = $skills->fields;
       echo '<OPTION VALUE=';
       echo "$skillinfo[abbr]";
       echo '>';
       echo "$skillinfo[long_name]";
       echo '</OPTION>';
       $skills->MoveNext();
	}
}

?>
