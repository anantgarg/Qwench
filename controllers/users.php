<?php

function login() {
	global $template;
	$template->set('loginpage','1');
}

function view() {
	
	global $path;
	global $template;

	$userid = sanitize($path[2],"int");

	$sql = ("select * from users where id = '".escape($userid)."'");
	$query = mysql_query($sql);
	$user = mysql_fetch_array($query);

	$basePath = basePath();

	$template->set('user',$user);
}

function edit() {
	authenticate(1);
	global $template;
	$sql = ("select * from users where id = '".escape($_SESSION['userid'])."'");
	$query = mysql_query($sql);
	$user = mysql_fetch_array($query);
	$basePath = basePath();
	$template->set('user',$user);
}

function update() {
	authenticate(1);
	global $template;

	$name = sanitize($_POST['name'],"string");
	$email = sanitize($_POST['email'],"email");
	$password = sanitize($_POST['password'],"string");
	$password = sha1(SALT.$password.$email);
	$website = sanitize($_POST['website'],"string");
	$realname = sanitize($_POST['realname'],"string");
	$location = sanitize($_POST['location'],"string");
	$birthday = sanitize($_POST['birthday'],"birthday");

	if (!empty($_POST['password'])) {
		$sql = ("update users set password = '".escape($password)."' where id = '".escape($_SESSION['userid'])."'");
		$query = mysql_query($sql);
	}

	$sql = ("update users set name = '".escape($name)."',email = '".escape($email)."' , website = '".escape($website)."', realname = '".escape($realname)."', location = '".escape($location)."', birthday = '".escape($birthday)."' where id = '".escape($_SESSION['userid'])."'");
	$query = mysql_query($sql);

	$slug = createslug($name);

	$basePath = basePath();
	header("Location: $basePath/users/view/{$_SESSION['userid']}/$slug");
}

function validate() {
	$email = sanitize($_POST['email'],"email");
	$password = sanitize($_POST['password'],"string");
	$password = sha1(SALT.$password.$email);
	
	$sql = ("select * from users where email = '".escape($email)."' and password = '".escape($password)."'");
	$query = mysql_query($sql);
	$user = mysql_fetch_array($query);

	$basePath = basePath();

	if ($user['id'] > 0) {
		$_SESSION['userid'] = $user['id'];
		$_SESSION['name'] = $user['name'];
		$_SESSION['email'] = $user['email'];
		$_SESSION['password'] = $user['password'];
		$_SESSION['points'] = $user['points'];
		$_SESSION['moderator'] = $user['moderator'];
		$_SESSION['location'] = $user['location'];
		$_SESSION['realname'] = $user['realname'];
		
$sql = ("update users set lastactivity = '".escape(date("Y-m-d H:i:s"))."' where id = '".escape($_SESSION['userid'])."'");
		$query = mysql_query($sql);
 
		if (!empty($_POST['returnurl'])) {
			$url = sanitize($_POST['returnurl'],"url");
			header("Location: {$url}");
		}  else {
 
			header("Location: $basePath");
		}
	} else {
		header("Location: $basePath/users/login");
	}
}
 
function register() {

}

function create() {
	$name = sanitize($_POST['name'],"string");
	$email = sanitize($_POST['email'],"email");
	$password = sanitize($_POST['password'],"string");
	$password = sha1(SALT.$password.$email);
	
	$sql = ("insert into users (name,email,password,points,moderator,created,lastactivity) values ('".escape($name)."','".escape($email)."','".escape($password)."','1','0',NOW(),NOW())");
	$query = mysql_query($sql);
	
	validate();
	
}

function logout() {
	session_destroy();
	session_start();
	$_SESSION['userid'] = '';

}

function index() {
	global $template;

	$sql = ("select count(id) count from users");
	$query = mysql_query($sql);
	$result = mysql_fetch_array($query);
	$template->set('count',$result['count']);

	$sql = ("select * from users order by points desc, name asc");
	$query = mysql_query($sql);

	$users = array();
	
	while ($result = mysql_fetch_array($query)) {
		$users[] = array ("id" => $result['id'], "name" => $result['name'], "points" => $result['points'], "moderator" => $result['moderator']);
	}

	$template->set('users',$users);

	/* Add Pagination Later */
}
