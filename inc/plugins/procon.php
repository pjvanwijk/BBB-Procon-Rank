<?php

/****************************************************************************************
* Disclaimer: This file (procon.php) is part of the "BBB procon rank" project.
* The plugin is intended to be used with the MyBB framework (www.mybb.com), 
* specifically for the www.beer-boobs-bombs.de (BBB) website.
* This plugin is mainly made to give ranking data to the end-user which is 
* retrieved from the Procon database which is provided by the 
* "Chat, GUID, Stats and Mapstats Logger[1.0.0.2]" plugin which I will be referring 
* as logger plugin for simplicity's sake ;). The logger plugin reffered to is at:
* https://forum.myrcon.com/showthread.php?6698-Chat-GUID-Stats-and-Mapstats-Logger-1-0-0-2
* 
* I do NOT own that project so credits for the logger plugin go to the developers of the 
* logger plugin. I solely made this plugin as an addition to the BBB website so end users
* can ask for ranks on the website. This plugin is distributed in the hope that it will be 
* useful, but WITHOUT ANY WARRANTY. This plugin is free software. You are allowed to modify 
* and freely redistribute it (a mentioning of my name will be highly appreciated of course ;) ).
* I do NOT intend to make money or profit from this software (however a donation is always welcome)
* 
* (c) Paul van Wijk a.k.a PabloDavico
******************************************************************************************/

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
    die("Direct initialization of this file is not allowed.");
}

function procon_info()
{
    return array(
        "name"          => "BBB Procon Rank",
        "description"   => "Plugin for BBB's Procon",
        "website"       => "",
        "author"        => "PabloDavico",
        "authorsite"    => "http://www.github.com/pjvanwijk",
        "version"       => "1.0",
        "guid"          => "",
        "compatibility" => "*"
    );
}

function procon_install()
{
    /*****************************
    *
    *    Create the settings
    *
    ******************************/
    
    global $db, $mybb;
    
    //Create new setting group
    $dp_group = array(
        "gid" => "NULL",
        "name" => "proconsettings",
        "title" => "Procon Settings",
        "description" => "Set up and edit the settings for the procon module here ;).",
        "disporder" => "35",
        "isdefault" => "no",
        );
    $db->insert_query("settinggroups", $dp_group);
    $gid = $db->insert_id();
    
    //Create the individual settings
    $procon_1 = array(
        "sid" => "NULL",
        "name" => "procon_question",
        "title" => "User question",
        "description" => "This is the question the user gets when he is on the procon page.",
        "optionscode" => "text",
        "value" => "What is your name, soldier?",
        "disporder" => "1",
        "gid" => intval($gid),
        );
    $db->insert_query("settings", $procon_1);

    $procon_2 = array(
        "sid" => "NULL",
        "name" => "procon_dbip",
        "title" => "Procon database IP",
        "description" => "The IP address or hostname of the BBB Procon database.",
        "optionscode" => "text",
        "value" => "localhost",
        "disporder" => "2",
        "gid" => intval($gid),
        );
    $db->insert_query("settings", $procon_2);
    
    $procon_3 = array(
        "sid" => "NULL",
        "name" => "procon_dbname",
        "title" => "Database",
        "description" => "The name of the procon database.",
        "optionscode" => "text",
        "value" => "Procon",
        "disporder" => "3",
        "gid" => intval($gid),
        );
    $db->insert_query("settings", $procon_3);
    
    $procon_4 = array(
        "sid" => "NULL",
        "name" => "procon_user",
        "title" => "Username",
        "description" => "The username of the user that has priveledges to select from the procon database.",
        "optionscode" => "text",
        "value" => "",
        "disporder" => "4",
        "gid" => intval($gid),
        );
    $db->insert_query("settings", $procon_4);
    
    $procon_5 = array(
        "sid" => "NULL",
        "name" => "procon_pw",
        "title" => "Password",
        "description" => "The password for the procon database.",
        "optionscode" => "text",
        "value" => "",
        "disporder" => "5",
        "gid" => intval($gid),
        );
    $db->insert_query("settings", $procon_5);

    rebuild_settings();
}

function procon_is_installed()
{
      global $db;

      if ($db->num_rows($db->simple_select("settinggroups","name","name='proconsettings'")) >= 1)	
      {
	return true;
      }
	return false;
}

function procon_uninstall()
{
      global $db, $mybb;
      //remove the settings
      $query = $db->query("SELECT gid FROM ".TABLE_PREFIX."settinggroups WHERE name='proconsettings'");
      $g = $db->fetch_array($query);
      $db->query("DELETE FROM ".TABLE_PREFIX."settinggroups WHERE gid='".$g['gid']."'");
      $db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE gid='".$g['gid']."'");
      $db->delete_query("templates", "title = 'procon_err'");
      
      rebuild_settings();
}

function procon_activate()
{
    global $db;
      
    /*****************************
    *
    *    Insert the template
    *
    ******************************/
    
    $template = 
      '<html>
      <head>
      <title>{$mybb->settings[\'bbname\']} - BBB Procon</title>
      {$headerinclude}
	<link rel="stylesheet" type="text/css" href="{$mybb->asset_url}/procon/procon.css" media="all" /> 
	<script type="text/javascript" src="{$mybb->asset_url}/jscripts/proconrank.js"></script>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/prototype/1.7.2.0/prototype.js"></script>
	<script type="text/javascript" >
	window.onload = function(){
	  $("ranksubmit").observe("click", getRanking);
	  $("noWeapon").checked = true;
	  $("rankweapon").disabled = true;
	  getTopX();
	  $("rankname").observe("keyup", function(event) {
	      if (event.keyCode == 13) $("ranksubmit").click();
	  });
	};
      </script>

      </head>
      <body>
      {$header}
      <table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
	<tr>
	  <td class="thead"><strong>Procon Rank</strong></td>
	</tr>

	<tr>
	<td width="100%" class="trow1">
	  <div id="container">		
		<div id="rankingPane" class="column">
		    <div id="rankingForm">
			  <label class="proconlabel">{$procon_question}</label> <br/>
			    <input type="text" name="rankname" id="rankname"/>  <br/>
			  <br/>
			  <label class="proconlabel">Weaponrank</label><br/>
			    <select id="rankweapon" name="rankweapon" onchange="getTopX()">
			    {$weapons}
			    </select>  <br/>
			    
			<button type="button" id="ranksubmit">!rank</button>
			  
			<label class="proconlabel"><input id = "noWeapon" type="checkbox" name="noWeapon" onchange="javascript:toggleSelect();">!rank only</label>
			
			<div id="rankingresult"></div>
		    
		    </div>
		</div>		
		<div id="topXpane" class="column"></div>		
	  </div>
	</td>
	</tr>
      </table>
      {$footer}
      </body>
      </html>';

    $insert_array = array(
	'title' => 'BBBprocon',
	'template' => $db->escape_string($template),
	'sid' => '-1',
	'version' => '0.0.1',
	'dateline' => time()
    );
    
    $template_err = "<html><head><title>ERROR</title></head><body><h3>ERROR: ADMIN DID NOT CONFIGURE PROCON CORRECTLY (OR NOT YET)! :(</h3></body></html>";
    
    $insert_array_err = array(
	'title' => 'procon_err',
	'template' => $db->escape_string($template_err),
	'sid' => '-1',
	'version' => '0.1',
	'dateline' => time()
    );
    
    $db->insert_query('templates', $insert_array_err);
    $db->insert_query('templates', $insert_array);
}

function procon_deactivate()
{
      global $db;

      //remove the template
      $db->delete_query("templates", "title = 'BBBprocon'");
}
