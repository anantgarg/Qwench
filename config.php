<?php
/* 
 * Please use the install.php from the web to setup the quench
 * instead of changing the values in here.
 */

// Database Details
define('SERVERNAME', 'localhost');
define('SERVERPORT','3306');
define('DBUSERNAME','root');
define('DBPASSWORD','');
define('DBNAME','qwench');

define('ANSWERS_PER_PAGE','10');
define('QUESTIONS_PER_PAGE','10');
define('ALLOW_VISITORS',FALSE); // If you want only logged in users to view the site

define('SALT','salt'); // Do not change salt after users have registered

date_default_timezone_set("Europe/Athens"); // Set default timezone if you want or comment the line below
define('BASE_DIR','/qwench-pets');

// If URL-Rewriting does not work then set
// define('BASE_PATH',BASE_DIR.'/index.php');
// If URL-Rewriting works, then leave the line below as is
//define('BASE_PATH',BASE_DIR);
define('BASE_PATH',BASE_DIR);

//email configuration
define('SEND_EMAIL', FALSE);
define('HTML_EMAIL', FALSE);
define('MAILFROM','me@example.com');

define('SITETITLE','Qwench');


//To use reCAPTCHA you must get an API key from 
//http://recaptcha.net/api/getkey
define('PRIVATEKEY','6LfvwLoSAAAAAKttPX7Wfz1G_NY19wUpe3EBhvm7');
define('PUBLICKEY','6LfvwLoSAAAAAJXXRGjl_q2wjiMGcqHCqLoAtRKl');
