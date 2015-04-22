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

	define('IN_MYBB', 1); 
	define('THIS_SCRIPT', 'procon.php');
	require_once "./global.php";

	$procon_question = $mybb->settings['procon_question'];
	// require_once "procon/command.php";

	// include "procon/db_config.php";
	require_once "./procon_connect.php";

	// Select all the weapons in the database
	$weaponsquery = sprintf("SELECT Friendlyname FROM tbl_weapons ORDER BY Friendlyname;");
	$result = mysql_query($weaponsquery);
		if(!$result) {
			die("Error 404: The weapons are missing...");
		}
		while($arr = mysql_fetch_array($result,MYSQL_NUM)){
			$weaponname = $arr[0];
			$weapons .= "<option value=".$weaponname.">".$weaponname."</option>\n";
		}			  

	if (!$connect['hostname'] || !$connect['database'] || !$connect['username'] || !$connect['password']) {
	      eval("\$proconpage = \"".$templates->get("procon_err")."\";"); 
	}
	else {
	      eval("\$proconpage = \"".$templates->get("BBBprocon")."\";"); 
	}

	add_breadcrumb("BBB Procon", "procon.php");
	output_page($proconpage); 

?>