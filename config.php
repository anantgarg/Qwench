<?php

// Database Details
define('SERVERNAME','localhost');
define('SERVERPORT','3306');
define('DBUSERNAME','root');
define('DBPASSWORD','yourpassword');
define('DBNAME','qwench');

Define('ANSWERS_PER_PAGE','10');
define('QUESTIONS_PER_PAGE','10');

// If you want only logged in users to view the site
define('ALLOW_VISITORS','1');

// Do not change salt after users have registered
define('SALT','yoursecurestringoverhere');

// Set default timezone if you want or comment the line below
date_default_timezone_set("Asia/Calcutta");

// No trailing slash
// Path to the Qwench folder
// If you have installed Qwench in your 
// root folder then set
// define('BASE_DIR','');
define('BASE_DIR','/qwench');

// If URL-Rewriting does not work then set
// define('BASE_PATH',BASE_DIR.'/index.php');
// If URL-Rewriting works, then leave the line below as is
define('BASE_PATH',BASE_DIR);
define('SITE_TITLE', 'Your Site');
// define('DEBUG_ENABLED', true);