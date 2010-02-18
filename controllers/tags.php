<?php

function index() {
	global $template;

	$sql = ("select count(id) count from tags");
	$query = mysql_query($sql);
	$result = mysql_fetch_array($query);
	$template->set('count',$result['count']);

	$sql = ("select tag, count(tags_questions.questionid) tagcount from tags, tags_questions where tags.id = tags_questions.tagid group by tagid order by tagcount desc");
	$query = mysql_query($sql);

	$tags = array();
	
	while ($result = mysql_fetch_array($query)) {
		$tags[] = array ("tag" => $result['tag'], "count" => $result['tagcount']);
	}

	$template->set('tags',$tags);

	/* Add Pagination Later */
}