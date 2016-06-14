<?php

function edit() {
	authenticate(1);

	global $path;
	global $template;
	$answerid = sanitize($path[2],"int");

	$js = '<script src="'.BASE_DIR.'/js/showdown.js"></script>
<script src="'.BASE_DIR.'/js/wmd.js"></script>
<link href="'.BASE_DIR.'/css/wmd.css" type="text/css" rel="stylesheet" />';

	$template->set('js',$js);

	$sql = ("SELECT * FROM answers WHERE id = '".escape($answerid)."'");
	$query = mysql_query($sql);
	$result = mysql_fetch_array($query);

	$template->set('description',$result['description']);
	$template->set('answerid',$result['id']);
}

function post() {
	authenticate(1);

	$description = sanitize($_POST['description'],"markdown");
	$questionid = sanitize($_POST['questionid'],"int");

	$sql = ("SELECT * FROM questions WHERE id = '".escape($questionid)."'");
	$query = mysql_query($sql);
	$result = mysql_fetch_array($query);

	if (strlen($description) < 15 || $result['id'] == '' || $result['id'] == 0) {
		header("Location: ".BASE_PATH."/questions/view/$questionid/{$result['slug']}");
		exit;
	}


	$sql = ("INSERT INTO answers (questionid,description,created,updated,userid,accepted,votes) VALUES ('".escape($questionid)."','".escape($description)."',NOW(),NOW(),'".escape($_SESSION['userid'])."','0','0')");
	$query = mysql_query($sql);

	$sql = ("UPDATE questions SET updated = NOW(), answers=answers+1 WHERE id = '".escape($result['id'])."'");
	$query = mysql_query($sql);

	$url= "".$_SERVER['SERVER_NAME']."$basePath/questions/view/$questionid/{$result['slug']}";
	if( $result['notify']==1) {
		sendNotificationEmail($result['userid'],$result['title'],$url);
	}

	header("Location: ".BASE_PATH."/questions/view/$questionid/{$result['slug']}");

}

function update() {
	authenticate(1);

	$answerid = sanitize($_POST['id'],"int");
	$description = sanitize($_POST['description'],"markdown");

	$sql = ("SELECT * FROM answers WHERE id = '".escape($answerid)."'");
	$query = mysql_query($sql);
	$result = mysql_fetch_array($query);

	$sql = ("SELECT * FROM questions WHERE id = '".escape($result['questionid'])."'");
	$query = mysql_query($sql);
	$qresult = mysql_fetch_array($query);

	if ($qresult['userid'] != $_SESSION['userid'] || $_SESSION['moderator']==1) {
		header("Location: ".BASE_PATH."/questions/view/{$qresult['id']}/{$qresult['slug']}");
	}
	if($qresult['userid'] == $_SESSION['userid'] || $_SESSION['moderator']==1) {
		$sql = ("UPDATE answers SET description = '".escape($description)."', updated = NOW() WHERE userid = '".escape($qresult['userid'])."' AND id = '".escape($answerid)."'");
		$query = mysql_query($sql);

		$sql = ("UPDATE questions SET updated = NOW() WHERE id = '".escape($result['questionid'])."'");
		$query = mysql_query($sql);

		header("Location: ".BASE_PATH."/questions/view/{$qresult['id']}/{$qresult['slug']}");
	}
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

	$sql = ("SELECT answers.userid,answers_votes.id qvid,answers_votes.vote qvvote FROM answers LEFT JOIN answers_votes ON (answers.id = answers_votes.answerid AND answers_votes.userid =  '".escape($_SESSION['userid'])."') WHERE answers.id = '".escape($id)."'");
	$query = mysql_query($sql);

	$answer = mysql_fetch_array($query);

	if ($answer['userid'] == $_SESSION['userid']) {
		echo "0"."You cannot up/down vote your own answer";
		exit;
	}

	if ($answer['qvid'] > 0) {

		if ($answer['qvvote'] == 1 && $vote == '+1') {
			$vote = "-1";
			score('a_upvoted_removed',$id,$answer['userid']);
		} else if ($answer['qvvote'] == 1 && $vote == '-1') {
			$vote = "-2";
			score('a_upvoted_removed',$id,$answer['userid']);
			score('a_downvoter',$id);
			score('a_downvoted',$id,$answer['userid']);
		} else if ($answer['qvvote'] == -1 && $vote == '-1') {
			$vote = "+1";
			score('a_downvoter_removed',$id);
			score('a_downvoted_removed',$id,$answer['userid']);
		} else if ($answer['qvvote'] == -1 && $vote == '+1') {
			$vote = "+2";
			score('a_downvoter_removed',$id);
			score('a_downvoted_removed',$id,$answer['userid']);
			score('a_upvoted',$id,$answer['userid']);
		} else if ($answer['qvvote'] == 0) {
			if ($vote == 1) {
				score('a_upvoted',$id,$answer['userid']);
			} else {
				score('a_downvoter',$id);
				score('a_downvoted',$id,$answer['userid']);
			}
		}

		$sql = ("UPDATE answers_votes SET vote = vote".escape($vote)." WHERE id = '".$answer['qvid']."'");
		$query = mysql_query($sql);

	} else {
		$sql = ("INSERT INTO answers_votes (answerid,userid,vote) VALUES ('".escape($id)."','".escape($_SESSION['userid'])."','".escape($vote)."')");
		$query = mysql_query($sql);

		if ($vote == 1) {
			score('a_upvoted',$id,$answer['userid']);
		} else {
			score('a_downvoter',$id);
			score('a_downvoted',$id,$answer['userid']);
		}

	}

	$sql_nest = ("UPDATE answers SET votes = votes".escape($vote)." WHERE id = '".escape($id)."'");
	$query_nest = mysql_query($sql_nest);

	echo "1Thankyou for voting";
	exit;

}

function accept() {
	authenticate(1);

	$answerid = sanitize($_GET['id'],"int");

	$sql = ("SELECT questionid,userid from answers WHERE id = '".escape($answerid)."'");
	$query = mysql_query($sql);
	$answer = mysql_fetch_array($query);

	$sql = ("SELECT questions.*,answers.id answerid, answers.userid answeruserid FROM questions LEFT JOIN answers ON (questions.id = answers.questionid AND answers.accepted = 1) WHERE questions.id = '".escape($answer['questionid'])."'");
	$query = mysql_query($sql);
	$result = mysql_fetch_array($query);

	if ($result['kb'] == 1) {
		header("Location: $basePath/questions/view/{$result['id']}/{$result['slug']}");
		exit;
	}

	if ($result['answerid'] > 0) {
		score('a_accepted_removed',$answerid,$result['answeruserid']);
	} else {
		score('a_accepter',$answerid);
	}

	if ($result['userid'] == $_SESSION['userid'] || $_SESSION['moderator']==1) {
		$sql = ("UPDATE answers SET accepted = '0' WHERE questionid = '".escape($result['id'])."'");
		$query = mysql_query($sql);
		$sql = ("UPDATE answers SET accepted = '1' WHERE questionid = '".escape($result['id'])."' AND id = '".escape($answerid)."'");
		$query = mysql_query($sql);
		$sql = ("UPDATE questions SET accepted = '1' WHERE id = '".escape($result['id'])."' AND userid = '".escape($_SESSION['userid'])."'");
		$query = mysql_query($sql);

		score('a_accepted',$answerid,$answer['userid']);

	}

	header("Location: ".BASE_PATH."/questions/view/{$result['id']}/{$result['slug']}");
}


/*
function del() {
	authenticate(1);
	
	$basePath = basePath();
	$basePathNS = basePathNS();
	
	global $path;
	global $template;

	$answerid = sanitize($path[2],"int");
	
	if ($_SESSION['moderator']==1){


	$sql = ("delete from answers WHERE id = '".escape($answerid)."' ");
	$query = mysql_query($sql);
	
	
	header("Location: $basePathNS/index.php");
	}
	else
	header("Location: $basePathNS/index.php");
		
}
*/

function del() {

	$id = sanitize($_POST['id'],"int");

	$sql = ("SELECT questionid FROM answers WHERE id ='".escape($id)."'");
	$query = mysql_query($sql);
	$questionid = mysql_result($query,0);

	$sql = ("DELETE FROM answers WHERE id = '".escape($id)."'");
	$query = mysql_query($sql);

	$sql = ("UPDATE questions SET  answers=answers-1 WHERE id = '".$questionid."'");
	$query = mysql_query($sql);

	echo "1Answer successfully deleted";
	exit;

}
