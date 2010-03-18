<?php

function index() {
	global $template;
	
	
	$sql = ("select * from users where moderator = '1'");
	$query = mysql_query($sql);
	
	$moderator = array();
	while ($result = mysql_fetch_array($query)) {
		$moderators[] = array ("id" => $result['id'], "name" => $result['name']);
	}

	$template->set('moderators',$moderators);
	
	
	$sql = ("select * from questions ORDER BY votes DESC limit 5");
	$query = mysql_query($sql);
	
	$bestquestion = array();
	while ($result = mysql_fetch_array($query)) {
		$bestquestions[] = array ("questionid" => $result['id'], "title" => $result['title'], "userid" => $result['userid'], "votes" => $result['votes'], "answers" => $result['answers'], "accepted" => $result['accepted'], "slug" => $result['slug']);
	}
	
	/*
	$sql = ("select * from users where id = ".$bestquestions['userid']."");
	$query = mysql_query($sql);*/
	
	
	$template->set('bestquestions',$bestquestions);


	$sql = ("select * from questions ORDER BY votes ASC limit 5");
	$query = mysql_query($sql);
	
	$worstquestion = array();
	while ($result = mysql_fetch_array($query)) {
		$worstquestions[] = array ("questionid" => $result['id'], "title" => $result['title'], "userid" => $result['userid'], "votes" => $result['votes'], "answers" => $result['answers'], "accepted" => $result['accepted'], "slug" => $result['slug']);
	}
	
	$template->set('worstquestions',$worstquestions);


}





function revoke() {

	global $path;
	global $template;

if ($_SESSION['moderator']==1){
	$userid = sanitize($path[2],"int");

	$sql = ("update users set moderator = '0' where id = '".escape($userid)."'");
	$query = mysql_query($sql);
	
	header("Location: $basePathNS/admin/index.php");
}
else
header("Location: $basePathNS/index.php");
}


