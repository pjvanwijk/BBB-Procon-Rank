<?php

/****************************************************************************************
* Disclaimer: This file (procon_connect.php) is part of the "BBB procon rank" project.
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


  require_once "./global.php";

  global $mybb;
  
// Creates the connection between the procon database and the UI
  
  $procon_db = new DB_MySQL;

  $connect['hostname'] = $mybb->settings['procon_dbip'];
  $connect['database'] = $mybb->settings['procon_dbname'];
  $connect['username'] = $mybb->settings['procon_user'];
  $connect['password'] = $mybb->settings['procon_pw'];

  $procon_db->connect($connect);

?>