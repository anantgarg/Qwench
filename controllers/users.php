<?php

function login() {
	global $template;
	$template->set('loginpage','1');
}

function view() {

	global $path;
	global $template;

	$userid = sanitize($path[2],"int");

	$sql = ("SELECT * FROM users WHERE id = '".escape($userid)."'");
	$query = mysql_query($sql);
	$user = mysql_fetch_array($query);

	$basePath = basePath();

	$template->set('user',$user);
}

function edit() {
	authenticate(1);
	global $template;
	$sql = ("SELECT * FROM users WHERE id = '".escape($_SESSION['userid'])."'");
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
	$website = sanitize($_POST['website'],"url");
	$realname = sanitize($_POST['realname'],"string");
	$location = sanitize($_POST['location'],"string");
	$birthday = sanitize($_POST['birthday'],"birthday");
	$aboutme = sanitize($_POST['aboutme'],"string");

	if (!empty($_POST['password'])) {
		$sql = ("UPDATE users SET password = '".escape($password)."' WHERE id = '".escape($_SESSION['userid'])."'");
		$query = mysql_query($sql);
	}

	$sql = ("UPDATE users SET name = '".escape($name)."',email = '".escape($email)."' , website = '".escape($website)."', realname = '".escape($realname)."', location = '".escape($location)."', birthday = '".escape($birthday)."', aboutme = '".escape($aboutme)."' WHERE id = '".escape($_SESSION['userid'])."'");
	$query = mysql_query($sql);

	$slug = createslug($name);

	$basePath = basePath();
	header("Location: $basePath/users/view/{$_SESSION['userid']}/$slug");
}

function validate() {
	$email = sanitize($_POST['email'],"email");
	$password = sanitize($_POST['password'],"string");
	$password = sha1(SALT.$password.$email);

	$sql = ("SELECT * FROM users WHERE email = '".escape($email)."' AND active='1' AND password = '".escape($password)."'");
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
		$_SESSION['aboutme'] = $user['aboutme'];

		$sql = ("UPDATE users SET lastactivity = '".escape(date("Y-m-d H:i:s"))."' WHERE id = '".escape($_SESSION['userid'])."'");
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
	global $template;
	$basePathNS = basePathNS();
	$js = <<<EOD
<script type="text/javascript">
	var basePath = "$basePathNS/index.php";
	
	function getContent() {
		var name = document.getElementById("name").value;
		$.post(basePath+"/users/check", { name: name }, function(data){ $('#contentcontainer').html(data); });
		document.getElementById('contentcontainer').style.display='block';
	};
</script>
EOD;
	$template->set('js',$js);
}

function create() {

	$basePathNS = basePathNS();
	$basePath = basePath();
	$name = sanitize($_POST['name'],"string");
	$email = sanitize($_POST['email'],"email");
	$password= sanitize($_POST['password'],"string");
	$password2 = sanitize($_POST['password2'],"string");

	function validEmail($email) {
		$result = preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i",$email);
		if($result == false) {
			writelog("Email validation: FAILED");
			return false;
		}else {
			writelog("Email validation: OK");
			return true;
		}
	}

	function checkUser($name) {
		$sql = ("SELECT COUNT(*) AS 'numrow' FROM users WHERE name='".$name."'");
		$query = mysql_query($sql);
		$numrow = mysql_fetch_array($query);

		if($numrow['numrow']!=0) {
			writelog("the username is already present");
			return false;
		}
		else {
			writelog("the username is available");
			return true;
		}
	}


	//***************   reCAPTCHA    **************
	$resp = recaptcha_check_answer (PRIVATEKEY, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);
	writelog("fuori dal ciclo captcha	".$_SERVER["REMOTE_ADDR"]."recaptcha_challenge_field:".$_POST["recaptcha_challenge_field"]."recaptcha_response_field:".$_POST["recaptcha_response_field"]);

	if ($resp->is_valid) {
		writelog("Captcha check: OK");
		$captcha = TRUE;
	} else {
		writelog("Captcha check: FAILED");
		$captcha = FALSE;
	}

	function checkEmail($email) {

		if (validEmail($email)) {
			$sql2 = ("select count(*) as 'numrow' from users where email='".$email."'");
			$query2 = mysql_query($sql2);
			$numrow2 = mysql_fetch_array($query2);

			if($numrow2['numrow']!=0) {
				writelog("email address is already present");
				return FALSE;
			} else {
				writelog("email address not present");
				return TRUE;
			}
		}
	}

	function checkpswd($password,$password2) {

		if (($password == $password2)) {
			writelog("controllo password: OK");
			return TRUE;
		} else {
			writelog("controllo password: FALLITO");
			return FALSE;
		}
	}


	// insert user into database
	if(checkUser($name) && checkEmail($email) && checkpswd($password,$password2) && $captcha==TRUE) {
		$password = sha1(SALT.$password.$email);
		$sql = ("INSERT INTO users (name,email,password,points,moderator,created,lastactivity) VALUES ('".escape($name)."','".escape($email)."','".escape($password)."','1','0',NOW(),NOW())");
		$query = mysql_query($sql);

		$userid = mysql_insert_id();

		$temp = gettimeofday();
		$msec = (int) $temp["usec"];
		$activeid = md5(time() . $msec);

		$sql = ("INSERT INTO confirm (confirm_validator, confirm_userid) VALUES ('$activeid', '$userid')");
		$query = mysql_query($sql);

		if(SEND_EMAIL) {
			sendActivationEmail($userid, $activeid);
			header("Location: $basePath/users/active?action=1");
		} else {
			header("Location: $basePath/users/active?id=$activeid");
		}
	} else {
		writelog("errore");
		header("Location: $basePathNS/index.php/users/register");
	}

}

function logout() {
	session_destroy();
	session_start();
	$_SESSION['userid'] = '';
}

function index() {
	global $template;

	$sql = ("SELECT COUNT(id) count FROM users");
	$query = mysql_query($sql);
	$result = mysql_fetch_array($query);
	$template->set('count',$result['count']);

	$sql = ("SELECT * FROM users ORDER BY points DESC, name ASC");
	$query = mysql_query($sql);

	$users = array();

	while ($result = mysql_fetch_array($query)) {
		$users[] = array (
				"id" => $result['id'],
				"name" => $result['name'],
				"points" => $result['points'],
				"moderator" => $result['moderator']
		);
	}

	$template->set('users',$users);
	// TODO Add Pagination
}

function del() {
	authenticate(1);

	$basePath = basePath();
	$basePathNS = basePathNS();

	global $path;
	global $template;

	$userid = sanitize($path[2],"int");

	if ($_SESSION['moderator']==1) {

		$sql = ("DELETE FROM users WHERE id = '".escape($userid)."' ");
		$query = mysql_query($sql);

		header("Location: $basePath/users");
	} else {
		header("Location: $basePathNS/index.php");
	}
}

function check() {
	if (isset($_POST['name'])) {
		$user = sanitize($_POST['name'],"string");

		if(strlen($user) <= 0) {
			die;
		}

		$sql = ("SELECT COUNT(*) AS 'numrow' FROM users WHERE name='$user'");
		$query = mysql_query($sql);
		$row = mysql_result($query,0);
		if($row!=0) {
			echo  "<span style=\"color:red\";>Sorry but username $user is already in use</span>";
		} else {
			echo "<span style=\"color:green\";>Success,username $user is still available</span>";

		}
		exit;
	}
}

function active() {

	global $path;
	global $template;

	if (isset($_GET['id'])) {
		$id = sanitize($_GET['id'],"string");


		$sql = ("SELECT * FROM confirm WHERE confirm_validator = '".escape($id)."' ");
		$query = mysql_query($sql);
		$array = mysql_fetch_array($query);


		if (!is_array($array)) {
			$template->set('active',FALSE);
		} else {
			$user_id = $array["confirm_userid"];
			$sql = ("UPDATE users SET active = '1' WHERE id = '$user_id'");
			$query = mysql_query($sql);
			$template->set('active',TRUE);
		}

	}

}
