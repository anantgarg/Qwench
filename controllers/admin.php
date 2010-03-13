<?php

function index() {
	global $template;

	$sql = ("select * from users where moderator = '1'");
	$query = mysql_query($sql);
	
	$moderator = array();
	while ($result = mysql_fetch_array($query)) {
		$moderators[] = array ("moderator" => $result['moderator'], "name" => $result['name']);
	}

	$template->set('moderators',$moderators);
	

}