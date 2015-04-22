/**
 * 
 * TODO: add some copyright
 * and some other shit
 *
 */

function getRanking(){
	
	if ($("noWeapon").checked) getRankName();
	if (!$("noWeapon").checked) getRankWeapon();
}

function getTopX(){
	if($("noWeapon").checked) getTopName();
	if(!$("noWeapon").checked) getTopWeapon();
}

function getRankName(){
      	//perform an Ajax request
	var playerName = $("rankname").value;
	var ajax = new XMLHttpRequest();
 	ajax.onreadystatechange = function() {
 		if(ajax.readyState==4) {
 			updateRankingField(ajax);
     	}
 	};
 	ajax.open("GET", "procon_command.php?mode=rank&playerName="+playerName, true); 
 	ajax.send(null);
 
}

function getRankWeapon(){
      	//perform an Ajax request
	var playerName = $("rankname").value;
	var weaponName = $("rankweapon").value;
	var ajax = new XMLHttpRequest();
 	ajax.onreadystatechange = function() {
 		if(ajax.readyState==4) {
 			updateRankingField(ajax);
     	}
 	};
 	ajax.open("GET", "procon_command.php?mode=rank&playerName="+playerName+"&weaponName="+weaponName, true); 
 	ajax.send(null);
   
}

function getTopName(){
	//perform an Ajax request
	var playerName = $("rankname").value;
	var ajax = new XMLHttpRequest();
 	ajax.onreadystatechange = function() {
 		if(ajax.readyState==4) {
 			$("topXpane").innerHTML = ajax.responseText;
     	}
 	};
 	ajax.open("GET", "procon_command.php?mode=topX", true); 
 	ajax.send(null);
}

function getTopWeapon(){
  	//perform an Ajax request
	var playerName = $("rankname").value;
	var weaponName = $("rankweapon").value;
	var ajax = new XMLHttpRequest();
 	ajax.onreadystatechange = function() {
 		if(ajax.readyState==4) {
 			$("topXpane").innerHTML = ajax.responseText;
     	}
 	};
 	ajax.open("GET", "procon_command.php?mode=weapontopX&weaponName="+weaponName, true); 
 	ajax.send(null);
}

function updateRankingField(ajax) {
	var response = ajax.responseText;
	$("rankingresult").innerHTML = response;
}

function toggleSelect() {

      if ($("noWeapon").checked == true){
	  getTopName();
	  $("rankweapon").disabled = true;
      }
      else{
	  getTopWeapon();
	  $("rankweapon").disabled = false;
      }
}
