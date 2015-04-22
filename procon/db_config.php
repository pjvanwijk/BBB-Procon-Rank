<?php

global $mybb;
$cfg = array( 'procon_dbip' => $mybb->settings['procon_dbip'],
	      'procon_dbname' => $mybb->settings['procon_dbname'],
	      'procon_pw' => $mybb->settings['procon_pw'],
	      'procon_user' => $mybb->settings['procon_user']);

var_dump($cfg);	      
if($cfg['procon_dbip']=='') 
{
  die($cfg['procon_dbip']);
}
return $cfg;
 
?>