<?php

function index() {
	global $template;

	$sql = ("SELECT COUNT(id) count FROM tags");
	$query = mysql_query($sql);
	$result = mysql_fetch_array($query);
	$template->set('count',$result['count']);

	$sql = ("SELECT tag, COUNT(tags_questions.questionid) tagcount FROM tags, tags_questions WHERE tags.id = tags_questions.tagid GROUP BY tagid ORDER BY tagcount DESC");
	$query = mysql_query($sql);

	$tags = array();

	while ($result = mysql_fetch_array($query)) {
		$tags[] = array (
			"tag" => $result['tag'],
			"count" => $result['tagcount']
		);
	}

	$template->set('tags',$tags);
	// TODO Add Pagination
}

function del() {
	authenticate(1);

	$basePath = basePath();
	$basePathNS = basePathNS();

	global $path;
	global $template;

	$tag = $_GET['tag'];

	if (isset($_SESSION['moderator'])==1) {

		$sql = ("DELETE FROM tags WHERE tag = '".escape($tag)."' ");
		$query = mysql_query($sql);
		header("Location: $basePath/tags");
	} else {
		header("Location: $basePath/tags");
	}
}