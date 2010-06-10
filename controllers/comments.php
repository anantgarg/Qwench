<?php

function post() {

	if ($_SESSION['userid'] == '') {
		echo "0";
		exit;
	}

	global $template;
	global $noheader;
	$noheader = true;

	$id = sanitize($_POST['id'],"string");
	$type = substr($id,0,1);
	$typeid = substr($id,1);

	if ($type == 'q') {
		$type = 0;
	} else {
		$type = 1;
	}

	$comment = sanitize($_POST['comment'],"comment");

	if (strlen($comment) < 10 || strlen($comment) > 600) {
		echo "0An error has occurred. Please try again later";
		exit;
	}

	$sql = ("INSERT INTO comments (type,comment,votes,created,userid,typeid) VALUES ('".escape($type)."','".escape($comment)."','0',NOW(),'".escape($_SESSION['userid'])."','".escape($typeid)."')");
	$query = mysql_query($sql);

	$template->set('comment',$comment);

	$firstname = $_SESSION['name'];
	$pos = strpos($_SESSION['name'],' ');
	if ($pos > 0) {
		$firstname = substr($_SESSION['name'],0,$pos);
	}

	$template->set('username',$firstname);
	$template->set('userid',$_SESSION['userid']);

}

function vote() {
	if ($_SESSION['userid'] == '') {
		echo "0Please login to vote";
		exit;
	}

	$id = sanitize($_POST['id'],"int");

	$sql = ("SELECT userid FROM comments WHERE id = '".escape($id)."'");
	$query = mysql_query($sql);
	$comment = mysql_fetch_array($query);

	if ($comment['userid'] == $_SESSION['userid']) {
		echo "0You cannot upvote your own comment";
		exit;
	}

	$sql = ("SELECT * FROM comments_votes WHERE commentid = '".escape($id)."' AND userid = '".escape($_SESSION['userid'])."'");
	$query = mysql_query($sql);
	$result = mysql_fetch_array($query);


	if ($result['id'] > 0) {

		$sql = ("DELETE FROM comments_votes WHERE commentid = '".escape($id)."' AND userid = '".escape($_SESSION['userid'])."'");
		$query = mysql_query($sql);
		$sql_nest = ("UPDATE comments SET votes = votes-1 WHERE id = '".escape($id)."'");
		$query_nest = mysql_query($sql_nest);
		score('c_upvoted_removed',$id,$comment['userid']);

	} else {
		$sql = ("INSERT INTO comments_votes (commentid,userid) VALUES ('".escape($id)."','".escape($_SESSION['userid'])."')");
		$query = mysql_query($sql);
		$sql_nest = ("UPDATE comments SET votes = votes+1 WHERE id = '".escape($id)."'");
		$query_nest = mysql_query($sql_nest);
		score('c_upvoted',$id,$comment['userid']);
	}

	echo "1";
	exit;

}

function del() {

	$id = sanitize($_POST['id'],"int");

	$sql = ("SELECT userid FROM comments WHERE id = '".escape($id)."'");
	$query = mysql_query($sql);
	$comment = mysql_fetch_array($query);

	$sql = ("DELETE FROM comments WHERE id = '".escape($id)."' AND userid = '".escape($comment['userid'])."'");
	$query = mysql_query($sql);

	echo "1Comment successfully deleted";
	exit;

}