<?php

function score($activity = 'none',$id = 0,$userid = 0) {

	if ($userid == 0) { $userid = $_SESSION['userid']; }

	$points = 0;

	switch ($activity) {
		case 'q_downvoter':
			$points = -1;
		break;

		case 'q_downvoted':
			$points = -2;
		break;

		case 'q_upvoted':
			$points = 10;
		break;

		case 'q_upvoted_removed':
			$points = -10;
		break;

		case 'q_downvoter_removed':
			$points = 1;
		break;

		case 'q_downvoted_removed':
			$points = 2;
		break;

		case 'a_downvoter':
			$points = -1;
		break;

		case 'a_downvoted':
			$points = -2;
		break;

		case 'a_upvoted':
			$points = 10;
		break;

		case 'a_upvoted_removed':
			$points = -10;
		break;

		case 'a_downvoter_removed':
			$points = 1;
		break;

		case 'a_downvoted_removed':
			$points = 2;
		break;

		case 'a_accepter':
			$points = 2;
		break;
		
		case 'a_accepted':
			$points = 15;
		break;

		case 'a_accepted_removed':
			$points = -15;
		break;

		case 'kb_posted':
			$points = 10;
		break;

		case 'kb_posted_removed':
			$points = -10;
		break;

		case 'c_upvoted':
			$points = 5;
		break;

		case 'c_upvoted_removed':
			$points = -5;
		break;

		case 'none':

		break;
	}
	
	if ($points != 0) {
		$sql = ("insert into activities (userid,activity,points,activityid,created) values ('".escape($userid)."','".escape($activity)."','".escape($points)."','".escape($id)."',NOW())");
		$query = mysql_query($sql);
		echo mysql_error();
	}
	
	if ($points >= 0) {
		$points = "+".$points;
	}

	$sql = ("update users set points = points".escape($points)." where id = '".escape($userid)."' and (points".escape($points).") > 1");
	$query = mysql_query($sql);
	echo mysql_error();
}
