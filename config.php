<?php

// Database Details
define('SERVERNAME','localhost');
define('SERVERPORT','3306');
<<<<<<< HEAD
define('DBUSERNAME','your_db_username');
define('DBPASSWORD','your_db_password');
define('DBNAME','qwench');
=======
define('DBUSERNAME','your_db_username_here');
define('DBPASSWORD','your_db_password_here');
define('DBNAME','your_db_name_here');
>>>>>>> 3b68aa2f057443dff9f1fa3d086ddcb508257b62

Define('ANSWERS_PER_PAGE','10');
define('QUESTIONS_PER_PAGE','10');

//if you want to enable production mode vs debug mode (useful for error reporting)
define('DEBUG_MODE','1');

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
