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
	$website = sanitize($_POST['website'],"url");
	$realname = sanitize($_POST['realname'],"string");
	$location = sanitize($_POST['location'],"string");
	$birthday = sanitize($_POST['birthday'],"birthday");
	$aboutme = sanitize($_POST['aboutme'],"string");
	
	if (!empty($_POST['password'])) {
		$sql = ("update users set password = '".escape($password)."' where id = '".escape($_SESSION['userid'])."'");
		$query = mysql_query($sql);
	}

	$sql = ("update users set name = '".escape($name)."',email = '".escape($email)."' , website = '".escape($website)."', realname = '".escape($realname)."', location = '".escape($location)."', birthday = '".escape($birthday)."', aboutme = '".escape($aboutme)."' where id = '".escape($_SESSION['userid'])."'");
	$query = mysql_query($sql);

	$slug = createslug($name);

	$basePath = basePath();
	header("Location: $basePath/users/view/{$_SESSION['userid']}/$slug");
}

function validate() {
	$email = sanitize($_POST['email'],"email");
	$password = sanitize($_POST['password'],"string");
	$password = sha1(SALT.$password.$email);
	
	$sql = ("select * from users where email = '".escape($email)."' and active='1' and password = '".escape($password)."'");
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
	global $template;
	
	
	
	
	$basePathNS = basePathNS();


	$js = <<<EOD



<script>

	var basePath = "$basePathNS/index.php";

	
	function getContent()
{
var name = document.getElementById("name").value;

$.post(basePath+"/users/check", { name: name}, function(data){ 
$('#contentcontainer').html(data); });

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
		if($result == false){
		writelog("convalida email: FALLITA");
			return false;
		}else{
		writelog("convalida email: OK");
			return true;
		}
}	
		
function checkUser($name) {
	$sql = ("select count(*) as 'numrow' from users where name='".$name."'");
	$query = mysql_query($sql);
	$numrow = mysql_fetch_array($query); 
       
        if($numrow['numrow']!=0){
			writelog("l'username è gia presente");
			return false;
			}
		else
		{
			writelog("l'username non è presente");
			return true;
			}
		}
		

	
//***************   reCAPTCHA    **************	

	$resp = recaptcha_check_answer (PRIVATEKEY,
                                $_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["recaptcha_response_field"]); 
                                
writelog("fuori dal ciclo captcha	".$_SERVER["REMOTE_ADDR"]."recaptcha_challenge_field:".$_POST["recaptcha_challenge_field"]."recaptcha_response_field:".$_POST["recaptcha_response_field"]);

if ($resp->is_valid) {
writelog("controllo captcha: OK");
$captcha = true;
}
else{
writelog("controllo captcha: FALLITO");
$captcha = false;
}


function checkEmail($email){

if (validEmail($email)){
	$sql2 = ("select count(*) as 'numrow' from users where email='".$email."'");
	$query2 = mysql_query($sql2);
	$numrow2 = mysql_fetch_array($query2); 
      
        if($numrow2['numrow']!=0){
			writelog("indirizzo email è gia presente");
			return false;
			}
			
		else {
			writelog("indirizzo email non presente");
			return true;
		}
	}
	}
		
function checkpswd($password,$password2){		
	
				if (($password == $password2)){
				writelog("controllo password: OK");
					return true;
					}
			else{
			writelog("controllo password: FALLITO");
			return false;
			}
		}
		
	
// insert user into database	
	if(checkUser($name) && checkEmail($email) && checkpswd($password,$password2) && $captcha==true){
	
		$password = sha1(SALT.$password.$email);
	
						$sql = ("insert into users (name,email,password,points,moderator,created,lastactivity) values ('".escape($name)."','".escape($email)."','".escape($password)."','1','0',NOW(),NOW())");
						$query = mysql_query($sql);
						
						$userid = mysql_insert_id();
						
	$temp = gettimeofday(); 
	$msec = (int) $temp["usec"]; 
	$activeid = md5(time() . $msec); 
						
						$sql = ("insert into confirm (confirm_validator, confirm_userid) values ('$activeid', '$userid')");
						$query = mysql_query($sql);			
						
						sendActivationEmail($userid,$activeid);								
	
						header("Location: $basePath/users/active?action=1");
	}
	else
	{
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

function del() {
	authenticate(1);
	
	$basePath = basePath();
	$basePathNS = basePathNS();
	
	global $path;
	global $template;

	$userid = sanitize($path[2],"int");
	
	if ($_SESSION['moderator']==1){
	
	$sql = ("delete from users where id = '".escape($userid)."' ");
	$query = mysql_query($sql);
	
	header("Location: $basePath/users");
	}
	else
	header("Location: $basePathNS/index.php");
		
}

function check() {
 if (isset($_POST['name'])){
$user = sanitize($_POST['name'],"string");

if(strlen($user) <= 0)
{
  die;
}
 
$sql = ("select count(*) as 'numrow' from users where name='$user'");
$query = mysql_query($sql);

$row = mysql_result($query,0);

if($row!=0)
			  echo  "<span style=\"color:red\";>Sorry but username $user is already in use</span>";
		else{
  echo "<span style=\"color:green\";>Success,username $user is still available</span>";

}
		exit;
}
}

function active() {

	global $path;
	global $template;

 if (isset($_GET['id'])){
$id = sanitize($_GET['id'],"string");


	$sql = ("select * from confirm where confirm_validator = '".escape($id)."' ");
	$query = mysql_query($sql);
	$array = mysql_fetch_array($query);
	
	
	if (!is_array($array)) 
{ 
  $template->set('active',False);
} 
else
{
$user_id = $array["confirm_userid"]; 
$sql = ("update users set active = '1' where id = '$user_id'");
$query = mysql_query($sql);
$template->set('active',True);
}

}

}
