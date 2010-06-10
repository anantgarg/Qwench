<?php

function edit() {
	authenticate(1);

	global $path;
	global $template;
	$answerid = sanitize($path[2],"int");

	$basePath = basePath();
	$basePathNS = basePathNS();

	$js = <<<EOD

<script src="$basePathNS/js/showdown.js"></script>
<script src="$basePathNS/js/wmd.js"></script>
<link href="$basePathNS/css/wmd.css" type="text/css" rel="stylesheet" />

EOD;

	$template->set('js',$js);

	$sql = ("select * from answers where id = '".escape($answerid)."'");
	$query = mysql_query($sql);
	$result = mysql_fetch_array($query);

	$template->set('description',$result['description']);
	$template->set('answerid',$result['id']);
}

function post() {
	authenticate(1);
	$basePath = basePath();

	$description = sanitize($_POST['description'],"markdown");
	$questionid = sanitize($_POST['questionid'],"int");

	$sql = ("select * from questions where id = '".escape($questionid)."'");
	$query = mysql_query($sql);
	$result = mysql_fetch_array($query);

	if (strlen($description) < 15 || $result['id'] == '' || $result['id'] == 0) {
		header("Location: $basePath/questions/view/$questionid/{$result['slug']}");
		exit;
	}


	$sql = ("insert into answers (questionid,description,created,updated,userid,accepted,votes) values ('".escape($questionid)."','".escape($description)."',NOW(),NOW(),'".escape($_SESSION['userid'])."','0','0')");
	$query = mysql_query($sql);

	$sql = ("update questions set updated = NOW(), answers=answers+1 where id = '".escape($result['id'])."'");
	$query = mysql_query($sql);

	$url= "".$_SERVER['SERVER_NAME']."$basePath/questions/view/$questionid/{$result['slug']}";
	if( $result['notify']==1) {
		sendNotificationEmail($result['userid'],$result['title'],$url);
	}

	header("Location: $basePath/questions/view/$questionid/{$result['slug']}");

}

function update() {
	authenticate(1);

	$answerid = sanitize($_POST['id'],"int");
	$description = sanitize($_POST['description'],"markdown");

	$sql = ("select * from answers where id = '".escape($answerid)."'");
	$query = mysql_query($sql);
	$result = mysql_fetch_array($query);

	$sql = ("select * from questions where id = '".escape($result['questionid'])."'");
	$query = mysql_query($sql);
	$qresult = mysql_fetch_array($query);

	if ($qresult['userid'] != $_SESSION['userid'] || $_SESSION['moderator']==1) {
		$basePath = basePath();
		header("Location: $basePath/questions/view/{$qresult['id']}/{$qresult['slug']}");
	}
	if($qresult['userid'] == $_SESSION['userid'] || $_SESSION['moderator']==1) {
		$sql = ("update answers set description = '".escape($description)."', updated = NOW() where userid = '".escape($qresult['userid'])."' and id = '".escape($answerid)."'");
		$query = mysql_query($sql);

		$sql = ("update questions set updated = NOW() where id = '".escape($result['questionid'])."'");
		$query = mysql_query($sql);

		$basePath = basePath();

		header("Location: $basePath/questions/view/{$qresult['id']}/{$qresult['slug']}");
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

	$sql = ("select answers.userid,answers_votes.id qvid,answers_votes.vote qvvote from answers left join answers_votes on (answers.id = answers_votes.answerid and answers_votes.userid =  '".escape($_SESSION['userid'])."') where answers.id = '".escape($id)."'");
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

		$sql = ("update answers_votes set vote = vote".escape($vote)." where id = '".$answer['qvid']."'");
		$query = mysql_query($sql);

	} else {
		$sql = ("insert into answers_votes (answerid,userid,vote) values ('".escape($id)."','".escape($_SESSION['userid'])."','".escape($vote)."')");
		$query = mysql_query($sql);

		if ($vote == 1) {
			score('a_upvoted',$id,$answer['userid']);
		} else {
			score('a_downvoter',$id);
			score('a_downvoted',$id,$answer['userid']);
		}

	}

	$sql_nest = ("update answers set votes = votes".escape($vote)." where id = '".escape($id)."'");
	$query_nest = mysql_query($sql_nest);

	echo "1Thankyou for voting";
	exit;

}

function accept() {
	authenticate(1);

	$answerid = sanitize($_GET['id'],"int");

	$sql = ("select questionid,userid from answers where id = '".escape($answerid)."'");
	$query = mysql_query($sql);
	$answer = mysql_fetch_array($query);

	$sql = ("select questions.*,answers.id answerid, answers.userid answeruserid from questions left join answers on (questions.id = answers.questionid and answers.accepted = 1) where questions.id = '".escape($answer['questionid'])."'");
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
		$sql = ("update answers set accepted = '0' where questionid = '".escape($result['id'])."'");
		$query = mysql_query($sql);
		$sql = ("update answers set accepted = '1' where questionid = '".escape($result['id'])."' and id = '".escape($answerid)."'");
		$query = mysql_query($sql);
		$sql = ("update questions set accepted = '1' where id = '".escape($result['id'])."' and userid = '".escape($_SESSION['userid'])."'");
		$query = mysql_query($sql);

		score('a_accepted',$answerid,$answer['userid']);

	}

	$basePath = basePath();

	header("Location: $basePath/questions/view/{$result['id']}/{$result['slug']}");
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


	$sql = ("delete from answers where id = '".escape($answerid)."' ");
	$query = mysql_query($sql);
	
	
	header("Location: $basePathNS/index.php");
	}
	else
	header("Location: $basePathNS/index.php");
		
}
*/

function del() {

	$id = sanitize($_POST['id'],"int");

	$sql = ("select questionid from answers where id ='".escape($id)."'");
	$query = mysql_query($sql);
	$questionid = mysql_result($query,0);

	$sql = ("delete from answers where id = '".escape($id)."'");
	$query = mysql_query($sql);

	$sql = ("update questions set  answers=answers-1 where id = '".$questionid."'");
	$query = mysql_query($sql);

	echo "1Answer successfully deleted";
	exit;

}
