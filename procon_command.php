<?php

/****************************************************************************************
* Disclaimer: This file (procon_command.php) is part of the "BBB procon rank" project.
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
	define('THIS_SCRIPT', 'procon_command.php');
	require_once "./procon_connect.php";
	
 	if (isset($_REQUEST['mode']) && $_REQUEST['mode']=='rank' && isset($_REQUEST['playerName']) && isset($_REQUEST['weaponName'])){
		$player = $_REQUEST['playerName'];
		$weapon = $_REQUEST['weaponName'];
		// Known issue: allrank is not working yet
		$query = sprintf("
		SELECT tpd.SoldierName, kills, hs, deaths, rank, (SELECT CountPlayers FROM tbl_server_stats) AS allrank
		FROM (
			SELECT sub.PlayerID, (@num := @num + 1) AS rank, kills, hs, deaths
			FROM (
			  SELECT tsp.PlayerID, SUM(kills) AS kills, SUM(headshots) AS hs, SUM(deaths) AS deaths
			  FROM tbl_weapons_stats tw
			  INNER JOIN tbl_weapons t on tw.WeaponID = t.WeaponID
			  INNER JOIN tbl_server_player tsp ON tw.StatsID = tsp.StatsID, (SELECT @num := 0) x
			  WHERE t.FriendlyName = '%s'
			  GROUP BY tsp.PlayerID
			  ORDER BY kills DESC, hs DESC)		
			 sub) 
		sub2
		INNER JOIN tbl_playerdata tpd ON tpd.PlayerID = sub2.PlayerID
 		WHERE tpd.SoldierName = '%s';", mysql_real_escape_string($weapon), mysql_real_escape_string($player));
		
		$result = $procon_db->query($query);
		if(!$result){
			die('Error: ' . mysql_error());
		}
		if($arr = mysql_fetch_array($result,MYSQL_NUM)){
					$soldiername	= $arr[0];
					$kills		= $arr[1];
					$hs		= $arr[2];
					$deaths		= $arr[3];
					$rank		= $arr[4];
					$allrank	= $arr[5];
					
// 					$res = $soldiername . ";" . $kills . ";" . $hs . ";" . $deaths . ";" . $rank . ";" . $allrank . ";";
				if($kills == 0) $hsr = 0;
				else $hsr = round(100*($hs/$kills));
				$res = "<p>" . $soldiername . "'s Stats for " . $weapon . ": " . $kills . " kills, " . $hs . " Headshots, Headshotrate: " . $hsr . "%. Your Weaponrank is: " . $rank . " of " . $allrank . "</p>";
				}
		else{
			die('<p>Error: No stats found</p>');
		}
	  echo $res;
 	}
 	
 	else if (isset($_REQUEST['mode']) && $_REQUEST['mode']=='rank' && isset($_REQUEST['playerName'])){
	
		$player = $_REQUEST['playerName'];

		
	// 	Query returns tuple [rank, name, score, kills, highscore, deaths, kd, playerscount]
		$query = sprintf("SELECT pr.rankScore AS Rank, pd.SoldierName, ps.Score, ps.Kills, ps.HighScore, ps.Deaths, ps.Kills/ps.Deaths AS KD, st.CountPlayers AS outOf 
				  FROM tbl_playerdata pd  JOIN tbl_playerstats ps ON pd.PlayerID = ps.StatsID 
				  JOIN tbl_playerrank pr ON pd.PlayerID = pr.PlayerID 
				  JOIN tbl_server_stats st 
				  WHERE pd.SoldierName = '%s';", mysql_real_escape_string($player));
		$result = $procon_db->query($query);
		if(!$result){
			die('Error: ' . mysql_error());
		}
		if($arr = mysql_fetch_array($result,MYSQL_NUM)){
					$rank		= $arr[0];
					$name		= $arr[1];
					$score		= $arr[2];
					$kills		= $arr[3];
					$highscore	= $arr[4];
					$deaths		= $arr[5];
					$kd		= $arr[6];
					$outOf		= $arr[7];
// 					$res = $rank . ";" . $name . ";" . $score . ";" . $kills . ";" . $highscore . ";" . $deaths . ";" . $kd . ";" . $outOf . ";";
				$res = "<p>Serverstats for " . $name . ": Score: " . $score . ", " . $kills . " Kills, " . $highscore . " HS, " . $deaths . " Deaths, K/D: " . round($kd,2) . ". Your Serverrank is: " . $rank . " of " . $outOf . "</p>";
				}
		else{
			die('<p>Error: No stats found</p>');
		}
	  echo $res;
	}
	
	else if (isset($_REQUEST['mode']) && $_REQUEST['mode']=='weapontopX' && isset($_REQUEST['weaponName'])){
		$weapon = $_REQUEST['weaponName'];
		
		$query = sprintf("SELECT tpd.SoldierName, SUM(kills) AS kills, SUM(headshots) AS hs, SUM(deaths) AS deaths
				  FROM tbl_weapons tw
				  INNER JOIN tbl_weapons_stats tws ON tw.WeaponID = tws.WeaponID
				  INNER JOIN tbl_server_player tsp ON tws.StatsID = tsp.StatsID
				  INNER JOIN tbl_playerdata tpd ON tpd.PlayerID = tsp.PlayerID
				  WHERE tw.FriendlyName='%s'
				  GROUP BY tsp.PlayerID
				  ORDER BY kills DESC, hs DESC
				  LIMIT 10;", mysql_real_escape_string($weapon));
				  
		$result = $procon_db->query($query);
		if(!$result){
			die('Error: ' . mysql_error());
		}
		$res = "<table border='1' style='width:100%'><caption>!top10 " . $weapon . "</caption><thead><tr>
								<th>NAME</th>
								<th>KILLS</th>
								<th>HS</th>
								<th>DEATHS</th>
							    </tr></thead><tbody>";
		while($arr = mysql_fetch_array($result,MYSQL_NUM)){
					$name		= $arr[0];
					$kills		= $arr[1];
					$hs		= $arr[2]; 
					$deaths		= $arr[3];
					$res .= "<tr>
						  <td>$name</td>
						  <td>$kills</td>
						  <td>$hs</td>
						  <td>$deaths</td>
						</tr>";
		}
		$res .=  "</tbody></table>";
	  echo $res;
	}
	
	
	
	else if (isset($_REQUEST['mode']) && $_REQUEST['mode']=='topX'){
		
		$query = sprintf("SELECT tpd.SoldierName, SUM(tps.Score) AS Score, SUM(tps.Kills) AS Kills, SUM(tps.Deaths) AS Deaths , SUM(tps.Headshots) AS Headshots  
				  FROM tbl_playerstats tps
				  INNER JOIN tbl_server_player tsp ON tsp.StatsID = tps.StatsID
				  INNER JOIN tbl_playerdata tpd ON tsp.PlayerID = tpd.PlayerID
				  INNER JOIN tbl_playerrank tpr ON tpr.PlayerID = tsp.PlayerID
				  WHERE tpr.rankScore BETWEEN 1 AND 10
				  GROUP BY tsp.PlayerID 
				  ORDER BY tpr.rankScore ASC;");
				  
		$result = $procon_db->query($query);
		if(!$result){
			die('Error: ' . mysql_error());
		}
		$res = "<table border='1' style='width:100%'><caption>!top10</caption>
						      <thead><tr>
							  <th>NAME</th>
							  <th>SCORE</th>
							  <th>KILLS</th>
							  <th>Headshots</th>
							  <th>Deaths</th>
							  <th>KDR</th>
							</tr>
						      </thead><tbody>";
		while($arr = mysql_fetch_array($result,MYSQL_NUM)){
					$name		= $arr[0];
					$score		= $arr[1];
					$kills		= $arr[2];
					$deaths		= $arr[3];
					$hs		= $arr[4]; 
					$res .= "<tr>
						  <td>$name</td>
						  <td>$score</td>
						  <td>$kills</td>
						  <td>$hs</td>
						  <td>$deaths</td>
						  <td>" . round(($kills/$deaths), 2) . "</td>
						</tr>";
		}
		$res .=  "</tbody></table>";
	  echo $res;
	}
	$procon_db->close();
	
	
?>