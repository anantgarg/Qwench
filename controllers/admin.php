<?php

function index() {

	global $template;

	$sql = ("SELECT * FROM users WHERE moderator = '1'");
	$query = mysql_query($sql);

	$moderator = array();
	while ($result = mysql_fetch_array($query)) {
		$moderators[] = array (
				"id" => $result['id'],
				"name" => $result['name']
		);
	}

	$template->set('moderators',$moderators);

	$sql = ("SELECT * FROM questions ORDER BY votes DESC LIMIT 5");
	$query = mysql_query($sql);

	$bestquestion = array();
	while ($result = mysql_fetch_array($query)) {
		$bestquestions[] = array (
				"questionid" => $result['id'],
				"title" => $result['title'],
				"userid" => $result['userid'],
				"votes" => $result['votes'],
				"answers" => $result['answers'],
				"accepted" => $result['accepted'],
				"slug" => $result['slug']
		);
	}
	$template->set('bestquestions',$bestquestions);
	
	$sql = ("SELECT * FROM questions ORDER BY votes ASC LIMIT 5");
	$query = mysql_query($sql);
	$worstquestion = array();
	while ($result = mysql_fetch_array($query)) {
		$worstquestions[] = array (
			"questionid" => $result['id'],
			"title" => $result['title'],
			"userid" => $result['userid'],
			"votes" => $result['votes'],
			"answers" => $result['answers'],
			"accepted" => $result['accepted'],
			"slug" => $result['slug']
		);
	}
	$template->set('worstquestions',$worstquestions);


}

function revoke() {

	global $path;
	global $template;

	if ($_SESSION['moderator']==1) {
		$userid = sanitize($path[2],"int");

		$sql = ("UPDATE users SET moderator = '0' WHERE id = '".escape($userid)."'");
		$query = mysql_query($sql);

		header("Location: $basePathNS/admin/index.php");
	} else{
		header("Location: $basePathNS/index.php");
	}
}


