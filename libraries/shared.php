<?php

function generateLink($controller,$action) {
	return basePath().'/'.$controller.'/'.$action;
}

function noRender() {
	global $noRender;
	$noRender = true;
}

function authenticate($force = 0) {
	global $template;
	global $controller;
	global $action;

	$loggedin = 0;

	if (!empty($_SESSION['email']) && !empty($_SESSION['password'])) {

		$sql = ("select id,name,points from users where email = '".escape($_SESSION['email'])."' and password = '".escape($_SESSION['password'])."'");
		$query = mysql_query($sql);
		$user = mysql_fetch_array($query);

		if ($user['id'] > 0) {
			$_SESSION['points'] = $user['points'];
			$_SESSION['name'] = $user['name'];
			$loggedin = 1;
		} 		 
	}

	
	if (($force == 1 || ALLOW_VISITORS == 0) && $loggedin == 0 && ($controller != 'users' && ($action != 'validate' || $action != 'create' || $action != 'register'))) {
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
	$s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
	$protocol = substr(strtolower($_SERVER["SERVER_PROTOCOL"]), 0, strpos(strtolower($_SERVER["SERVER_PROTOCOL"]), "/")) . $s;
	$port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
	return $protocol . "://" . $_SERVER['SERVER_NAME'] . $port . $_SERVER['REQUEST_URI'];
}

function sanitize($input,$type = "old") {
	
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
		$input = htmlentities($input, ENT_QUOTES);
	break;

	case "old":
		echo "Old version of sanitize called";
		exit();
	break;

	}

	return $input;
}


function escape($input) {
	$input = mysql_real_escape_string($input);
	return $input;
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
        CURLOPT_RETURNTRANSFER => true,     // return web page
        CURLOPT_HEADER         => false,    // don't return headers
        CURLOPT_FOLLOWLOCATION => true,     // follow redirects
        CURLOPT_ENCODING       => "",       // handle all encodings
        CURLOPT_USERAGENT      => "spider", // who am i
        CURLOPT_AUTOREFERER    => true,     // set referer on redirect
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

function db() {
	$dbh = mysql_connect(SERVERNAME.':'.SERVERPORT,DBUSERNAME,DBPASSWORD);
	return mysql_selectdb(DBNAME,$dbh);
}

function basePath() {
	return BASE_PATH;
}

function basePathNS() {
	return BASE_DIR;
}

function datify($date) {
	return date('g:iA M dS', strtotime($date));
}

function datifyunix($date) {
	return date('g:iA M dS', $date);
}

function highlight($c,$q){ 
$q=explode(' ',str_replace(array('','\\','+','*','?','[','^',']','$','(',')','{','}','=','!','<','>','|',':','#','-','_'),'',$q));
for($i=0;$i<sizeOf($q);$i++) 
	$c=preg_replace("/($q[$i])(?![^<]*>)/i","<span class=\"highlight\">\${1}</span>",$c);
return $c;}


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
	if (strlen($text) <= $length) { 
		return $text; 
	} else { 
		$truncate = substr($text, 0, $length - strlen($ending)).$ending; 
		return $truncate;
	} 
}

db();
authenticate();
