<?php

function ask() {
	authenticate(1);

	global $template;

	$js = '<script src="'.BASE_DIR.'/js/showdown.js"></script>
<script src="'.BASE_DIR.'/js/wmd.js"></script>
<link href="'.BASE_DIR.'/css/wmd.css" type="text/css" rel="stylesheet" />

<script type="text/javascript">
$(document).ready(function() {
	$("#tags").fcbkcomplete({
		json_url: "'.BASE_PATH.'/questions/fetchtags",
		json_cache: true,
		filter_case: true,
		filter_hide: true,
		newel: true
	});
});
</script>';

	$template->set('js',$js);


}

function edit() {
	authenticate(1);

	global $path;
	global $template;

	$questionid = sanitize($path[2],"int");

	$js = '<script src="'.BASE_DIR.'/js/showdown.js"></script>
<script src="'.BASE_DIR.'/js/wmd.js"></script>
<link href="'.BASE_DIR.'/css/wmd.css" type="text/css" rel="stylesheet" />
<script type="text/javascript">
$(document).ready(function() {
	$("#tags").fcbkcomplete({
		json_url: "'.BASE_PATH.'/questions/fetchtags",
		json_cache: true,
		filter_case: true,
		filter_hide: true,
		newel: true
	});
});
</script>';

	$template->set('js',$js);

	$sql = ("select * from questions where id = '".escape($questionid)."'");
	$query = mysql_query($sql);
	$result = mysql_fetch_array($query);

	$template->set('title',$result['title']);
	$template->set('description',$result['description']);
	$template->set('link',$result['link']);
	$template->set('kb',$result['kb']);
	$template->set('notify',$result['notify']);

	$sql = ("select tag from tags_questions, tags where questionid = '".escape($questionid)."' and tags.id = tags_questions.tagid order by tag");
	$query = mysql_query($sql);

	$tags = array();

	while ($result = mysql_fetch_array($query)) {
		$tags[] = $result['tag'];
	}

	$template->set('tags',$tags);
	$template->set('questionid',$questionid);


}

function post() {
	authenticate(1);

	$title = sanitize($_POST['title'],"string");
	$description = sanitize($_POST['description'],"markdown");
	$link = sanitize($_POST['link'],"url");

	if(isset($_POST['notify']) && $_POST['notify'] == '1')
		$notify = sanitize($_POST['notify'],'int');
	else
		$notify = 0;

	$slug = createSlug($title);

	$kb = 0;

	if (!empty($_POST['answercheck'])) {
		$kb = sanitize($_POST['answercheck'],"int");
	}

	$cache = '';
	if (!empty($link)) {
		$cache = fetchURL($link);
	}

	if (strlen($title) < 15 || strlen($description)<15) {
		header("Location: ".BASE_PATH."/questions/ask");
		exit;
	}

	$sql = ("insert into questions (title,description,created,updated,link,userid,slug,linkcache,votes,accepted,answers,kb,notify) values ('".escape($title)."','".escape($description)."',NOW(),NOW(),'".escape($link)."','".escape($_SESSION['userid'])."','".escape($slug)."','".escape($cache)."','0','0','0','".escape($kb)."','".escape($notify)."')");
	$query = mysql_query($sql);

	$questionid = mysql_insert_id();

	if (!empty($_POST['tags'])) {
		foreach ($_POST['tags'] as $tag) {
			$tag = createSlug($tag);

			$sql = ("select * from tags where tag = '".escape($tag)."'");
			$query = mysql_query($sql);
			$result = mysql_fetch_array($query);

			if ($result['id'] > 0) {
				$sql = ("insert into tags_questions (tagid,questionid) values ('".escape($result['id'])."','".escape($questionid)."')");
				$query = mysql_query($sql);
			} else {

				$sql = ("insert into tags (tag) values ('".escape($tag)."')");
				$query = mysql_query($sql);
				$tagid = mysql_insert_id();

				$sql = ("insert into tags_questions (tagid,questionid) values ('".escape($tagid)."','".escape($questionid)."')");
				$query = mysql_query($sql);
			}
		}
	}

	if (!empty($_POST['answer'])) {
		$description = sanitize($_POST['answer'],"markdown");
		$sql = ("insert into answers (questionid,description,created,updated,userid,accepted,votes) values ('".escape($questionid)."','".escape($description)."',NOW(),NOW(),'".escape($_SESSION['userid'])."','1','0')");
		$query = mysql_query($sql);
	}

	if ($kb == 1) {
		score('kb_posted',$questionid);
	}

	header("Location: ".BASE_PATH."/questions/view/$questionid/$slug");
}


function update() {
	authenticate(1);

	$questionid = sanitize($_POST['id'],"int");
	$title = sanitize($_POST['title'],"string");
	$description = sanitize($_POST['description'],"markdown");
	$link = sanitize($_POST['link'],"url");
	$slug = createSlug($title);

	if(isset($_POST['notify']) && $_POST['notify'] == '1')
		$notify = sanitize($_POST['notify'],'int');
	else
		$notify = 0;

	$kb = 0;

	if (!empty($_POST['answercheck'])) {
		$kb = sanitize($_POST['answercheck'],"int");
	}

	$cache = '';
	if (!empty($link)) {
		$cache = fetchURL($link);
	}

	$sql = ("select * from questions where id = '".escape($questionid)."'");
	$query = mysql_query($sql);

	$result = mysql_fetch_array($query);

	if ($result['userid'] != $_SESSION['userid']) {
		header("Location: ".BASE_PATH."/questions/view/{$result['id']}/{$result['slug']}");
	}

	$cacheup = '';

	if ($result['link'] != $link) {
		$cacheup = ",linkcache = '".escape($cache)."'";
	}

	if ($result['kb'] == 1 && $kb == 0) {
		score('kb_posted_removed',$questionid);
	} else if ($result['kb'] == 0 && $kb == 1) {
		score('kb_posted',$questionid);
	}
	if($result['userid'] == $_SESSION['userid'] || $_SESSION['moderator']==1) {
		$sql = ("update questions set title = '".escape($title)."', kb = '".escape($kb)."', notify = '".escape($notify)."', description = '".escape($description)."' , updated = NOW(), link = '".escape($link)."', slug = '".escape($slug)."' $cacheup where userid = '".escape($result['userid'])."' and id = '".escape($questionid)."'");
		$query = mysql_query($sql);
		echo mysql_error();
	}


	$sql = ("delete from tags_questions where questionid = '".escape($questionid)."'");
	$query = mysql_query($sql);


	if (!empty($_POST['tags'])) {
		foreach ($_POST['tags'] as $tag) {
			$tag = createSlug($tag);

			$sql = ("select * from tags where tag = '".escape($tag)."'");
			$query = mysql_query($sql);
			$result = mysql_fetch_array($query);

			if ($result['id'] > 0) {
				$sql = ("insert into tags_questions (tagid,questionid) values ('".escape($result['id'])."','".escape($questionid)."')");
				$query = mysql_query($sql);
			} else {

				$sql = ("insert into tags (tag) values ('".escape($tag)."')");
				$query = mysql_query($sql);
				$tagid = mysql_insert_id();

				$sql = ("insert into tags_questions (tagid,questionid) values ('".escape($tagid)."','".escape($questionid)."')");
				$query = mysql_query($sql);
			}
		}
	}

	header("Location: ".BASE_PATH."/questions/view/$questionid/$slug");
}

function fetchtags() {
	global $noRender;
	$noRender = TRUE;
	
	$tag = createSlug($_GET['tag']);

	header('Content-type: application/json; charset=utf-8');
	$sql = ("select * from tags where tag LIKE '%".escape($tag)."%'");
	$query = mysql_query($sql);

	$resultSet = array();
	while ($result = mysql_fetch_array($query)) {
		$resultSet[] = array("caption" => $result['tag'], "value" => $result['tag']);
	}
	echo json_encode($resultSet);
	exit();
}


function view() {
	global $path;
	global $template;

	$questionid = sanitize($path[2],"int");

	$sql = ("select * from questions where id = '".escape($questionid)."'");
	$query = mysql_query($sql);
	$result = mysql_fetch_array($query);

	$template->set('id',$result['id']);
	$template->set('userid',$result['userid']);
	$template->set('title',$result['title']);
	$template->set('created',$result['created']);
	$template->set('description',Markdown($result['description']));
	$template->set('kb',$result['kb']);


	$template->set('link',$result['link']);

	$cache = 0;
	if (!empty($result['linkcache'])) {
		$cache = 1;
	}

	$template->set('cache',$cache);

	$sql = ("select tag from tags_questions, tags where questionid = '".escape($questionid)."' and tags.id = tags_questions.tagid order by tag");
	$query = mysql_query($sql);

	$tags = array();
	while ($result = mysql_fetch_array($query)) {
		$tags[] = $result['tag'];
	}

	$template->set('tags',$tags);


	$sql = ("select * from favorites where questionid = '".escape($questionid)."' and userid = '".escape($_SESSION['userid'])."'");
	$query = mysql_query($sql);
	$result = mysql_fetch_array($query);

	$fave = 0;
	if ($result['id'] > 0) {
		$fave = 1;
	}

	$template->set('fave',$fave);


	$sql = ("select sum(vote) count from questions_votes where questionid = '".escape($questionid)."'");
	$query = mysql_query($sql);
	$result = mysql_fetch_array($query);

	$votes = $result['count'];
	if ($votes == '') {
		$votes = 0;
	}

	$template->set('votes',$votes);

	$sql = ("select vote from questions_votes where questionid = '".escape($questionid)."' and userid = '".escape($_SESSION['userid'])."'");
	$query = mysql_query($sql);
	$result = mysql_fetch_array($query);

	$nvote = 0;
	$pvote = 0;

	if ($result['vote'] == -1) {
		$nvote = 1;
	}

	if ($result['vote'] == 1) {
		$pvote = 1;
	}

	$template->set('nvote',$nvote);
	$template->set('pvote',$pvote);



	$sql = ("select comments.id,comment,comments.userid,users.name username, comments_votes.id voted, comments.votes from comments left join users on comments.userid = users.id left join comments_votes on (comments_votes.commentid = comments.id and comments_votes.userid = '".escape($_SESSION['userid'])."') where type = '0' and typeid = '".escape($questionid)."' order by comments.created asc");
	$query = mysql_query($sql);

	$comments = array();

	while ($result = mysql_fetch_array($query)) {
		$pos = strpos($result['username'],' ');
		if ($pos > 0) {
			$result['username'] = substr($result['username'],0,$pos);
		}

		$comments[] = array("id" => $result['id'], "comment" => $result['comment'], "userid" => $result['userid'], "username" => $result['username'],  "voted" => $result['voted'],  "votes" => $result['votes']);
	}

	$template->set('comments',$comments);

	$sql = ("select count(id) count from answers where questionid = '".escape($questionid)."'");
	$query = mysql_query($sql);
	$result = mysql_fetch_array($query);

	$template->set('answerscount',$result['count']);

	$order = "votes desc";
	$orderby = "votes";
	$page = 1;

	if (!empty($_GET['order'])) {
		if ($_GET['order'] == "newest") {
			$order = "created desc";
			$orderby = "newest";
		} else if ($_GET['order'] == "oldest") {
			$order = "created asc";
			$orderby = "oldest";
		}
	}

	if (!empty($_GET['page'])) {
		$page = $_GET['page'];
	}

	$offset = ($page-1)*ANSWERS_PER_PAGE;

	$paging = new Pagination();
	$paging->set('urlscheme','?order='.$orderby.'&page=%page%');
	$paging->set('perpage',ANSWERS_PER_PAGE);
	$paging->set('page',$page);
	$paging->set('total',$result['count']);
	$paging->set('order',$orderby);

	$template->set('pagination',$paging->display());

	$paging->set('urlscheme','?order=%label%&page=1');
	$template->set('orderOptions',$paging->displayOptions());

	$sqlanswer = '';

	if ($page == 1) {
		$sqlanswer = "(select answers.*,users.name username from answers,users where questionid = '".escape($questionid)."' and answers.userid = users.id and answers.accepted = '1') UNION ";
	}

	$sql = ("$sqlanswer (select answers.*,users.name username from answers,users where questionid = '".escape($questionid)."' and answers.userid = users.id and answers.accepted = '0' order by $order, created desc LIMIT ".ANSWERS_PER_PAGE." OFFSET $offset)");
	$query = mysql_query($sql);


	$answers = array();
	while ($result = mysql_fetch_array($query)) {

		$sql_nest = ("select sum(vote) count from answers_votes where answerid = '".escape($result['id'])."'");
		$query_nest = mysql_query($sql_nest);
		$result_nest = mysql_fetch_array($query_nest);

		$votes = $result_nest['count'];

		if ($votes == '') {
			$votes = 0;
		}

		$sql_nest = ("select vote from answers_votes where answerid = '".escape($result['id'])."' and userid = '".escape($_SESSION['userid'])."'");
		$query_nest = mysql_query($sql_nest);
		$result_nest = mysql_fetch_array($query_nest);

		$nvote = 0;
		$pvote = 0;

		if ($result_nest['vote'] == -1) {
			$nvote = 1;
		}

		if ($result_nest['vote'] == 1) {
			$pvote = 1;
		}



		$sql_nest = ("select comments.id,comment,comments.userid,users.name username, comments_votes.id voted, comments.votes from comments left join users on comments.userid = users.id left join comments_votes on (comments_votes.commentid = comments.id and comments_votes.userid = '".escape($_SESSION['userid'])."') where type = '1' and typeid = '".escape($result['id'])."' order by comments.created asc");
		$query_nest = mysql_query($sql_nest);


		$comments = array();

		while ($result_nest = mysql_fetch_array($query_nest)) {
			$pos = strpos($result['username'],' ');
			if ($pos > 0) {
				$result['username'] = substr($result['username'],0,$pos);
			}
			$comments[] = array("id" => $result_nest['id'], "comment" => $result_nest['comment'], "userid" => $result_nest['userid'], "username" => $result_nest['username'],  "voted" => $result_nest['voted'],  "votes" => $result_nest['votes']);
		}

		$answers[] = array ("description" => Markdown($result['description']), "created" => $result['created'], "updated" => $result['updated'], "userid" => $result['userid'], "username" => $result['username'], "pvote" => $pvote, "nvote" => $nvote, "votes" => $votes, "id" => $result['id'], "accepted"=> $result['accepted'], "comments" => $comments );
	}

	$template->set('answers',$answers);

	$basePathNS = basePathNS();

	$js = '';

	if ($_SESSION['userid'] != '') {
		$js = '<script type="text/javascript" src="'.BASE_DIR.'/js/showdown.js"></script>
		<script type="text/javascript" src="'.BASE_DIR.'/js/wmd.js"></script>
		<link type="text/css" href="'.BASE_DIR.'/css/wmd.css" rel="stylesheet" />';
	}

	$js .= '<script type="text/javascript">var basePath = "'.BASE_DIR.'/index.php";</script>';
	$js .= '<script type="text/javascript" src="'.BASE_DIR.'/js/questions.view.js"></script>';
	$template->set('js',$js);
}

function cache() {
	global $path;
	global $template;
	global $noheader;

	$noheader = true;
	$questionid = sanitize($path[2],"int");

	$sql = ("select * from questions where id = '".escape($questionid)."'");
	$query = mysql_query($sql);
	$result = mysql_fetch_array($query);

	$template->set('cachelink',$result['link']);
	$template->set('cache',$result['linkcache']);
}

function vote() {
	if ($_SESSION['userid'] == '') {
		echo "0Please login to vote";
		exit;
	}

	$id = sanitize($_POST['id'],"int");
	$vote = sanitize($_POST['vote'],"string");

	if ($vote == 'plus') {
		$vote = '+1';
	} else {
		$vote = '-1';
	}

	$sql = ("select questions.userid,questions_votes.id qvid,questions_votes.vote qvvote from questions left join questions_votes on (questions.id = questions_votes.questionid and questions_votes.userid =  '".escape($_SESSION['userid'])."') where questions.id = '".escape($id)."'");
	$query = mysql_query($sql);

	$question = mysql_fetch_array($query);

	if ($question['userid'] == $_SESSION['userid']) {
		echo "0"."You cannot up/down vote your own question";
		exit;
	}

	if ($question['qvid'] > 0) {

		if ($question['qvvote'] == 1 && $vote == '+1') {
			$vote = "-1";
			score('q_upvoted_removed',$id,$question['userid']);
		} else if ($question['qvvote'] == 1 && $vote == '-1') {
			$vote = "-2";
			score('q_upvoted_removed',$id,$question['userid']);
			score('q_downvoter',$id);
			score('q_downvoted',$id,$question['userid']);
		} else if ($question['qvvote'] == -1 && $vote == '-1') {
			$vote = "+1";
			score('q_downvoter_removed',$id);
			score('q_downvoted_removed',$id,$question['userid']);
		} else if ($question['qvvote'] == -1 && $vote == '+1') {
			$vote = "+2";
			score('q_downvoter_removed',$id);
			score('q_downvoted_removed',$id,$question['userid']);
			score('q_upvoted',$id,$question['userid']);
		} else if ($question['qvvote'] == 0) {
			if ($vote == 1) {
				score('q_upvoted',$id,$question['userid']);
			} else {
				score('q_downvoter',$id);
				score('q_downvoted',$id,$question['userid']);
			}
		}

		$sql = ("update questions_votes set vote = vote".escape($vote)." where id = '".$question['qvid']."'");
		$query = mysql_query($sql);

	} else {
		$sql = ("insert into questions_votes (questionid,userid,vote) values ('".escape($id)."','".escape($_SESSION['userid'])."','".escape($vote)."')");
		$query = mysql_query($sql);

		if ($vote == 1) {
			score('q_upvoted',$id,$question['userid']);
		} else {
			score('q_downvoter',$id);
			score('q_downvoted',$id,$question['userid']);
		}

	}

	$sql_nest = ("update questions set votes = votes".escape($vote)." where id = '".escape($id)."'");
	$query_nest = mysql_query($sql_nest);

	echo "1Thankyou for voting";
	exit;

}

function fave() {

	if ($_SESSION['userid'] == '') {
		echo "0Please login to add a question to your favorites";
		exit;
	}

	$id = sanitize($_POST['id'],"int");

	$sql = ("select * from favorites where questionid = '".escape($id)."' and userid = '".escape($_SESSION['userid'])."'");
	$query = mysql_query($sql);
	$result = mysql_fetch_array($query);

	if ($result['id'] > 0) {
		$sql = ("delete from favorites where questionid = '".escape($id)."' and userid = '".escape($_SESSION['userid'])."'");
		$query = mysql_query($sql);
		echo "1Question removed from your favorites";

	} else {
		$sql = ("insert into favorites (questionid,userid) values ('".escape($id)."','".escape($_SESSION['userid'])."')");
		$query = mysql_query($sql);
		echo "1Question added to your favorites";
	}


	exit;

}

function index() {
	global $path;
	global $template;

	$conditionspre = '';
	$conditionspost = '';
	$conditionsselect = '';
	$extratitle = '';


	$orderby = 'newest';
	$order = 'created desc';
	$defaultorder = 1;
	$nopagination = 0;
	$page = 1;

	$searchstringoriginal = '';

	if (!empty($_GET['search'])) {
		$searchstringoriginal = sanitize($_GET['search'],"string");
	}

	if (!empty($_GET['order'])) {
		if ($_GET['order'] == "votes") {
			$order = "votes desc";
			$orderby = "votes";
			$defaultorder = 0;
		} else if ($_GET['order'] == "oldest") {
			$order = "created asc";
			$orderby = "oldest";
			$defaultorder = 0;
		} else if ($_GET['order'] == "relevance") {
			$order = "score desc";
			$orderby = "relevance";
			$defaultorder = 0;
		} else if ($_GET['order'] == "newest") {
			$defaultorder = 0;
		}
	}

	if (!empty($_GET['page'])) {
		$page = sanitize($_GET['page'],"int");
	}

	$type = '';


	if (!empty($_GET['type'])) {

		$type = "&type=".sanitize($_GET['type'],"string");

		if (sanitize($_GET['type'],"string") == "unanswered") {
			$conditionspost .= " questions.id NOT IN (select questions.id from questions,answers where questions.id = answers.questionid and answers.accepted = 1) and ";
			//	$conditionspost .= " questions.accepted = 0 and questions.kb = 0 and ";
			$extratitle = " not yet answered";

		} else {
			$extratitle = " active";
			$order = " updated desc ";
			$nopagination = 1;
		}
	}

	$template->set('nopagination',$nopagination);

	$search = '';
	$searchstring = urldecode($searchstringoriginal);

	if (!empty($searchstringoriginal)) {
		$search = "&search=".urlencode($searchstring);
		$conditionspost .= " MATCH(title, description) AGAINST ('".escape($searchstring)."') and ";
		$conditionsselect .= ",MATCH(title, description) AGAINST ('".escape($searchstring)."') AS score  ";
		$extratitle = " showing ".$searchstring;
		if ($defaultorder == 1) {
			$orderby = 'relevance';
			$order = 'score desc';
		}
	}

	$template->set('searchstring',$searchstring);

	$tag = '';

	if (!empty($_GET['tag'])) {
		$tag = "&tag=".createSlug($_GET['tag']);
		$conditionspre .= ",tags_questions, tags";
		$conditionspost .= " tags_questions.questionid = questions.id and tags.id = tags_questions.tagid and tags.tag LIKE '".escape(createSlug($_GET['tag']))."' and ";
		$extratitle = " tagged ".createSlug($_GET['tag']);
	}

	$offset = ($page-1)*QUESTIONS_PER_PAGE;

	$sql = ("select count(questions.id) count from questions $conditionspre WHERE $conditionspost 1");
	$query = mysql_query($sql);
	$result = mysql_fetch_array($query) or die("Query to get blah failed with error: ".mysql_error());

	$template->set('questionscount',$result['count']);

	$paging = new Pagination();
	$paging->set('urlscheme','?order='.$orderby.$tag.$type.$search.'&page=%page%');
	$paging->set('perpage',QUESTIONS_PER_PAGE);
	$paging->set('page',$page);
	$paging->set('total',$result['count']);
	$paging->set('order',$orderby);
	$paging->set('search',$search);

	$template->set('pagination',$paging->display());

	$paging->set('urlscheme','?order=%label%'.$tag.$type.$search.'&page=1');
	$template->set('orderOptions',$paging->displayOptions());

	$template->set('extratitle',$extratitle);

	$sql = ("select questions.* $conditionsselect from questions $conditionspre WHERE $conditionspost 1 order by $order, created desc LIMIT ".QUESTIONS_PER_PAGE." OFFSET $offset");
	$query = mysql_query($sql);

	$questions = array();

	while ($result = mysql_fetch_array($query)) {

		$sql_nest = ("select tag from tags_questions, tags where questionid = '".escape($result['id'])."' and tags.id = tags_questions.tagid order by tag");
		$query_nest = mysql_query($sql_nest);

		$tags = array();
		while ($result_nest = mysql_fetch_array($query_nest)) {
			$tags[] = $result_nest['tag'];
		}

		$description = truncate(trim(sanitize(Markdown($result['description']),"string")));

		if (!empty($searchstring)) {
			$description = highlight( excerpt( trim( sanitize( Markdown($result['description']), "string" ) ), $searchstring ), $searchstring );
			$result['title'] = highlight($result['title'],$searchstring);
		}

		$questions[] = array ("title" => $result['title'], "created" => $result['created'], "updated" => $result['updated'], "userid" => $result['userid'], "link" => $result['link'], "slug" => $result['slug'], "answers" => $result['answers'], "accepted" => $result['accepted'], "kb" => $result['kb'], "votes" => $result['votes'], "id" => $result['id'], "tags" => $tags, "description" => $description);

	}

	$template->set('questions',$questions);

}

function del() {
	authenticate(1);

	global $path;
	global $template;

	$questionid = sanitize($path[2],"int");

	if ($_SESSION['moderator']==1) {
		$sql = ("select * from questions where id = '".escape($questionid)."'");
		$query = mysql_query($sql);
		$result = mysql_fetch_array($query);

		$sql = ("delete from questions where id = '".escape($questionid)."' ");
		$query = mysql_query($sql);

		$sql = ("delete from answers where questionid = '".escape($questionid)."' ");
		$query = mysql_query($sql);

		$sql = ("delete from tags_questions where questionid = '".escape($questionid)."' ");
		$query = mysql_query($sql);

		header("Location: ".BASE_DIR."/index.php");
	}
	else
		header("Location: ".BASE_DIR."/index.php");

}
