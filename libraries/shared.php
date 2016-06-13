<?php

function generateLink($controller,$action) {
	return BASE_PATH.'/'.$controller.'/'.$action;
}

function authenticate($force = 0) {
	global $template;
	global $controller;
	global $action;

	$loggedin = 0;

	if (!empty($_SESSION['email']) && !empty($_SESSION['password'])) {

		$sql = ("SELECT id,name,points FROM users WHERE email = '".escape($_SESSION['email'])."' AND password = '".escape($_SESSION['password'])."'");
		$query = mysql_query($sql);
		$user = mysql_fetch_array($query);

		if ($user['id'] > 0) {
			$_SESSION['points'] = $user['points'];
			$_SESSION['name'] = $user['name'];
			$loggedin = 1;
		}
	}


	if (($force == 1 || ALLOW_VISITORS ) && $loggedin == 0 && ($controller != 'users' && ($action != 'validate' || $action != 'create' || $action != 'register'))) {
		$template->overrideController('users');
		$template->overrideAction('login');
		$template->set('link',getLink());
		$controller = "users";
		$action = "login";
	}

	if ($loggedin == 0) {
		$_SESSION['userid'] = '';
	}
}

function getLink() {
	$s = '';
	if(!empty ($_SERVER["HTTPS"])) {
		if($_SERVER["HTTPS"] == "on"){
			$s = "s";
		}
	}

	$protocol = substr( strtolower( $_SERVER["SERVER_PROTOCOL"] ), 0, strpos( strtolower( $_SERVER["SERVER_PROTOCOL"] ), "/" ) ).$s;

	$port = '';
	if($_SERVER["SERVER_PORT"] != "80"){
		$port = ":".$_SERVER["SERVER_PORT"];
	}

	return $protocol . "://" . $_SERVER['SERVER_NAME'] . $port . $_SERVER['REQUEST_URI'];
}

function sanitize( $input, $type = "old" ) {

	switch ($type) {
		case "int":
			$input = filter_var($input, FILTER_SANITIZE_NUMBER_INT);
			break;

		case "string":
			$input = filter_var($input, FILTER_SANITIZE_STRING);
			break;

		case "url":
			$input = filter_var($input, FILTER_SANITIZE_URL);
			break;

		case "email":
			$input = strtolower(filter_var($input, FILTER_SANITIZE_EMAIL));
			break;

		case "markdown":

			include_once ROOT.DS.'libraries'.DS.'purifier'.DS.'HTMLPurifier.auto.php';
			$purifier = new HTMLPurifier();
			$input = $purifier->purify($input);

			break;

		case "comment":
			$input = htmlentities($input, ENT_QUOTES, "UTF-8");
			break;

		case "old":
			echo "Old version of sanitize called";
			exit();
			break;

	}

	return $input;
}


function escape($input) {
	return mysql_real_escape_string($input);
}

function createSlug($input) {
	$input = filter_var($input, FILTER_SANITIZE_STRING);
	$input = trim($input);
	$input = preg_replace("/ /","-",$input);
	$input = preg_replace("/[^+A-Za-z0-9\.\-]/", "", $input);
	return strtolower($input);
}

function fetchURL($url) {
	$options = array(
		CURLOPT_RETURNTRANSFER => TRUE,     // return web page
		CURLOPT_HEADER         => FALSE,    // don't return headers
		CURLOPT_FOLLOWLOCATION => TRUE,     // follow redirects
		CURLOPT_ENCODING       => "",       // handle all encodings
		CURLOPT_USERAGENT      => "spider", // who am i
		CURLOPT_AUTOREFERER    => TRUE,     // set referer on redirect
		CURLOPT_CONNECTTIMEOUT => 10,      // timeout on connect
		CURLOPT_TIMEOUT        => 10,      // timeout on response
		CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
	);

	$ch      = curl_init( $url );
	curl_setopt_array( $ch, $options );
	$content = curl_exec( $ch );
	$err     = curl_errno( $ch );
	$errmsg  = curl_error( $ch );
	$header  = curl_getinfo( $ch );
	curl_close( $ch );

	return $content;
}

function datify($date) {
	return date('g:iA M dS', strtotime($date));
}

function datifyunix($date) {
	return date('g:iA M dS', $date);
}

function highlight( $c, $q ) {

	$q = explode(' ',str_replace(array('','\\','+','*','?','[','^',']','$','(',')','{','}','=','!','<','>','|',':','#','-','_'),'',$q));
	
	for( $i = 0; $i < sizeOf( $q ); $i++ ) {
		$c = preg_replace("/($q[$i])(?![^<]*>)/i","<span class=\"highlight\">\${1}</span>",$c);
	}
	return $c;
}


function excerpt($text, $phrase, $radius = 100, $ending = "...") {

	$phraseLen = strlen($phrase);
	if ($radius < $phraseLen) {
		$radius = $phraseLen;
	}

	$phrases = explode (' ',$phrase);

	foreach ($phrases as $phrase) {
		$pos = strpos(strtolower($text), strtolower($phrase));
		if ($pos > -1) break;
	}

	$startPos = 0;
	if ($pos > $radius) {
		$startPos = $pos - $radius;
	}

	$textLen = strlen($text);

	$endPos = $pos + $phraseLen + $radius;
	if ($endPos >= $textLen) {
		$endPos = $textLen;
	}

	$excerpt = substr($text, $startPos, $endPos - $startPos);
	if ($startPos != 0) {
		$excerpt = substr_replace($excerpt, $ending, 0, $phraseLen);
	}

	if ($endPos != $textLen) {
		$excerpt = substr_replace($excerpt, $ending, -$phraseLen);
	}

	return $excerpt;
} 

function truncate ($text, $length = 200, $ending = "...") {
	if (strlen($text) > $length) {
		$text = substr( $text, 0, $length - strlen($ending) ).$ending;
	}
	return $text;
}

function sendNotificationEmail($userid,$title,$url) {
	$sql = ("SELECT email FROM users WHERE id = '".$userid."'");
	$query= mysql_query($sql) or die(mysql_error());
	$result = mysql_fetch_array($query) or die(mysql_error());
	$url = "http://".$url;
	if (HTML_EMAIL == TRUE) {

		$header = "From: ".MAILFROM."\n";
		$header .= "MIME-Version: 1.0\n";
		$header .= "Content-Type: text/html; charset=\"iso-8859-1\"\n";
		$header .= "Content-Transfer-Encoding: 7bit\n\n";

		$emailtext = "<html><body><p> Someone has reply your question <b>".$title."</b></p><p>Visit the site to see it <a href=\"". $url."\">".$url."</a></p></body></html>";
		$subject = "You received a response";

		mail($result['email'], $subject, $emailtext, $header) ;

	}
	if (HTML_EMAIL == FALSE)
		mail($result['email'],"You received a response.","Someone has reply your question (".$title."). Visit the site to see it \n".$url."");
}

function sendActivationEmail($userid,$activeid) {

	$url= "http://".$_SERVER['SERVER_NAME'].BASE_PATH."/users/active?id=".$activeid;

	$sql = ("SELECT * FROM users WHERE id = '".$userid."'");
	$query= mysql_query($sql) or die(mysql_error());
	$result = mysql_fetch_array($query) or die(mysql_error());
	if (HTML_EMAIL == TRUE) {

		$header = "From: ".MAILFROM."\n";
		$header .= "MIME-Version: 1.0\n";
		$header .= "Content-Type: text/html; charset=\"iso-8859-1\"\n";
		$header .= "Content-Transfer-Encoding: 7bit\n\n";

		$emailtext = "<html><body><p>Welcome to ".SITETITLE." ".$result['name']."! </p><p> Please click this link below to activate your account:</p> <a href=\"". $url."\">".$url."</a></body></html>";
		$subject = "Account Activation";

		mail($result['email'], $subject, $emailtext, $header) ;

	}
	if (HTML_EMAIL == FALSE)
		mail($result['email'],"Account Activation.","Welcome to ".SITETITLE." ".$result['name']."! \nPlease copy and paste this link in your browser to activate your account: \n".$url."");
}

function writelog($string) {

	$date = gmdate( "d/M/Y  H:i:s");
	$string = $date ."   ". $string ." [".$_SERVER['REMOTE_ADDR']."]"."\n";

	$log_file=fopen("log.txt","a+");
	fwrite($log_file,$string);
	fclose($log_file);
}

$dbh = mysql_connect(SERVERNAME.':'.SERVERPORT,DBUSERNAME,DBPASSWORD);
mysql_selectdb(DBNAME,$dbh);

authenticate();
