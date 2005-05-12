<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: global_funcs.php

// Database tables variables
include("gui/global_funcs.php");

$dbtables['activities'] = "${db_prefix}activities";
$dbtables['alliances'] = "${db_prefix}alliances";
$dbtables['armor'] = "${db_prefix}armor";
$dbtables['bug_tracker'] = "${db_prefix}bug_tracker";
$dbtables['chiefs'] = "${db_prefix}chiefs";
$dbtables['clans'] = "${db_prefix}clans";
$dbtables['combat_terrain_effect'] = "${db_prefix}combat_terrain_effect";
$dbtables['combat_terrain_mods'] = "${db_prefix}combat_terrain_mods";
$dbtables['combat_weather'] = "${db_prefix}combat_weather";
$dbtables['combats'] = "${db_prefix}combats";
$dbtables['fair'] = "${db_prefix}fair";
$dbtables['fair_tribe'] = "${db_prefix}fair_tribe";
$dbtables['farm_activities'] = "${db_prefix}farm_activities";
$dbtables['farming'] = "${db_prefix}farming";
$dbtables['form_submits'] = "${db_prefix}form_submits";
$dbtables['game_date'] = "${db_prefix}game_date";
$dbtables['garrisons'] = "${db_prefix}garrisons";
$dbtables['gd_help'] = "${db_prefix}gd_help";
$dbtables['gd_resources'] = "${db_prefix}gd_resources";
$dbtables['gd_rq'] = "${db_prefix}gd_rq";
$dbtables['gd_rq_tables'] = "${db_prefix}gd_rq_tables";
$dbtables['gd_terrain'] = "${db_prefix}gd_terrain";
$dbtables['hexes'] = "${db_prefix}hexes";
$dbtables['inventory'] = "${db_prefix}inventory";
$dbtables['last_turn'] = "${db_prefix}last_turn";
$dbtables['livestock'] = "${db_prefix}livestock";
$dbtables['logs'] = "${db_prefix}logs";
$dbtables['map_log'] = "${db_prefix}map_log";
$dbtables['map_table'] = "${db_prefix}map_table";
$dbtables['map_view'] = "${db_prefix}map_view";
$dbtables['mapping'] = "${db_prefix}mapping";
$dbtables['messages'] = "${db_prefix}messages";
$dbtables['missile_types'] = "${db_prefix}missile_types";
$dbtables['movement_log'] = "${db_prefix}movement_log";
$dbtables['outbox'] = "${db_prefix}outbox";
$dbtables['poptrans'] = "${db_prefix}poptrans";
$dbtables['product_table'] = "${db_prefix}product_table";
$dbtables['products'] = "${db_prefix}products";
$dbtables['products_used'] = "${db_prefix}products_used";
$dbtables['religion_archetype'] = "${db_prefix}religion_archetype";
$dbtables['religions'] = "${db_prefix}religions";
$dbtables['reset_date'] = "${db_prefix}reset_date";
$dbtables['resources'] = "${db_prefix}resources";
$dbtables['scouts'] = "${db_prefix}scouts";
$dbtables['seeking'] = "${db_prefix}seeking";
$dbtables['skill_table'] = "${db_prefix}skill_table";
$dbtables['skills'] = "${db_prefix}skills";
$dbtables['structures'] = "${db_prefix}structures";
$dbtables['subtribe_id'] = "${db_prefix}subtribe_id";
$dbtables['traderoutes'] = "${db_prefix}traderoutes";
$dbtables['tribes'] = "${db_prefix}tribes";
$dbtables['weapons'] = "${db_prefix}weapons";
$dbtables['weather'] = "${db_prefix}weather";

$privilege = array (
// Helper privileges
                    "hlp_Search"       =>  0,
                    "hlp_Edit"         =>  2,     //For safety, should really be 2
                    "hlp_Add"          =>  2,     //For safety, should really be 2
                    "hlp_Preview"      =>  2,     //For safety, should really be 2
                    "hlp_Submit"       =>  2,     //For safety, should really be 2
                    "hlp_Modify"       =>  2,     //For safety, should really be 2
                    "hlp_Delete"       =>  3,     //For safety, should really be 3
                    "hlp_Delete All"   =>  5,     //For safety, should really be 5
                    "hlp_Confirm"      =>  3,     //For safety, should really be 3
// Admin privileges
                    "adm_access"   =>  3,     //Access to the admin page
                    "adm_mapping"  =>  3,
                                        "adm_logging"  =>  3,     //Able to view the admin logs
                    "adm_backup"   => 10,     //Able to backup the database
                    "adm_accounts" => 20,     //Able to change game account info
                    "adm_sched"    => 30,     //Able to run the scheduler
                    "adm_tables"   => 40,     //Able to edit table data
                    "adm_dev"      => 50,     //Able to upload files
                    "adm_tracking" => 60,     //Able to track players
                    "adm_reset"    => 99      //Able to reset the entire game
                    );


function getmicrotime()
{
    list($usec, $sec) = explode( " ", microtime() );
    return ( (float)$usec + (float)$sec );
}

function connectdb()
{
  /* connect to database - and if we can't stop right there */
  global $dbhost;
  global $dbport;
  global $dbuname;
  global $dbpass;
  global $dbname;
  global $default_lang;
  global $lang;
  global $gameroot;
  global $db_type;
  global $db_persistent;
  global $db;
  global $ADODB_FETCH_MODE;

  $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

  if(!empty($dbport))
    $dbhost.= ":$dbport";

  $db = ADONewConnection("$db_type");
  if($db_persistent == 1)
    $result = $db->PConnect("$dbhost", "$dbuname", "$dbpass", "$dbname");
  else
    $result = $db->Connect("$dbhost", "$dbuname", "$dbpass", "$dbname");

  if(!$result)
    die ("Unable to connect to the database");
}



function playerlog($sid, $log_type, $data = "")
{
  global $db, $dbtables;
  /* write log_entry to the player's log - identified by player's ship_id - sid. */
  if ($sid != "" && !empty($log_type))
  {
    $db->Execute("INSERT INTO $dbtables[logs] VALUES('', $sid, $log_type, NOW(), '$data')");
  }
}


function adminlog($log_type, $data = '')
{
    global $db, $dbtables;

    $debug_query = $db->Execute("SHOW TABLES LIKE '$dbtables[logs]'");
    db_op_result($debug_query,__LINE__,__FILE__);
    $row = $debug_query->fields;
    if ($row !== false)
    {
        // write log_entry to the admin log
        if (!empty($log_type))
        {
            $stamp = date("Y-m-d H:i:s");

            $debug_query = $db->Execute("INSERT INTO $dbtables[logs] (clanid,tribeid,type,time,data) VALUES  ('0000','0000.00','$log_type','$stamp','$data')");
            db_op_result($debug_query,__LINE__,__FILE__);

        }

    }

}
function db_op_result($query,$served_line,$served_page)
{
    global $db, $cumulative;

    if ($db->ErrorMsg() == '')
    {
        //echo "DB query executed fine<br>";
        return true;
    }
    else
    {
        $dberror = "A Database error occurred in " . $served_page . " on line ". ($served_line-1) ." (called from: $_SERVER[PHP_SELF]): " . $db->ErrorMsg($query) ."";
        $dberror = str_replace("'","&#39;",$dberror); // Allows the use of apostrophes.
        adminlog('DBERROR', $dberror);
        //echo $dberror."<br>";
        //die();
        return $db->ErrorMsg();
        $cumulative = 1; // For areas with multiple actions needing status - 0 is all good so far, 1 is at least one bad.
    }
}

function get_weight($tribeid)
{
    global $db, $dbtables;
    $prod = $db->Execute("SELECT * FROM $dbtables[products] WHERE tribeid = '$tribeid' AND amount > 0 AND long_name != 'wagon'");
    $totalweight = 0;
    while( !$prod->EOF )
    {
        $prodinfo = $prod->fields;
        $weight = $db->Execute("SELECT * FROM $dbtables[product_table] WHERE long_name = '$prodinfo[long_name]'");
        $prodweight = $weight->fields;
        $totalweight += $prodweight[weight] * $prodinfo[amount];
        $prod->MoveNext();
    }

    $resource = $db->Execute("SELECT * FROM $dbtables[resources] WHERE tribeid = '$tribeid' AND amount > 0");
    while( !$resource->EOF)
    {
        $resinfo = $resource->fields;
        $totalweight += $resinfo[amount];
        $resource->MoveNext();
    }

    $db->Execute("UPDATE $dbtables[tribes] SET curweight = $totalweight WHERE tribeid = '$tribeid'");

}


function NUMBER($number, $decimals = 0)
{
  global $local_number_dec_point;
  global $local_number_thousands_sep;
  return number_format($number, $decimals, $local_number_dec_point, $local_number_thousands_sep);
}


function stripnum($str)
{
  $str=(string)$str;
  $output = ereg_replace("[^0-9.]","",$str);
  return $output;
}

function log_move($tribeid,$hex_id)
{
   global $db,$dbtables;
   $res = $db->Execute("INSERT INTO $dbtables[movement_log] VALUES ('',$tribeid,$clanid,hex_id,NOW())");
}



?>
