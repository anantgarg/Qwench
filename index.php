<?php

/* Start Session */

session_name("qwench");
session_start();

/* Define */

define('ROOT',DIRNAME(__FILE__));
define('DS',DIRECTORY_SEPARATOR);

//needed to check the database connection
include (dirname(__FILE__))."/config.php";
include (dirname(__FILE__))."/libraries/shared.php";


/***********************************************************************************
 * 		::::BEGIN:::: A Series of Hacks to ensure application installs properly before use
 ************************************************************************************/

/* Check connection to the database*/
//Some people skip the installation step which results in certain errors
//if we can't connect to the database; stop the script
//NOTE: it is important to check for database connection first before prompting the user for the status of the install.php file.

if (db() == false)
{
	if (DEBUG_MODE == 1)
	{
		echo '<h1>Error Connecting to database. Make sure values in config.php match database configuration. or click here ---> <a href="install.php">to run installation</a></h1>';break;}
		else {
			echo '<h1>Error Connecting to database. Pleease contact administrator.</h1>';break;}
}
else
{
	//So lets say we can actually connect to the database, but are we sure the admin has run the setup script?
	//users is a table that should exists.
	/**
	 * NOTE; THis is a very bad hack because the admin may change table names in the setup script
	 */
	$result = mysql_query("select 1 from users");
	if ($result == false)
	{
		echo '<h1>Incomplete Application Setup,<a href="install.php"> Please run application installation.</a></h1>';break;
	}
}



//For security reasons check that the intallation file isn't default or there
/* Check installation folder */
if (file_exists('install.php'))
{
	if (DEBUG_MODE == 1)
	{echo '<h1>Please Delete, or rename the "install.php" file. Before preceeding;</h1>';break; }
}


/***********************************************************************************
 * 				::::END::::
 ************************************************************************************/



/* Get Basic Details */

$path = explode("/", substr($_SERVER['PATH_INFO'],1));

$controller = 'questions';
$action = 'index';
if (empty($_GET['type'])) { $_GET['type'] = "active"; }
$norender = false;
$noheader = false;

if (!empty($path[0])) { $controller = $path[0]; if ($_GET['type'] == "active") { $_GET['type'] = ""; } }
if (!empty($path[1])) { $action = $path[1]; if ($_GET['type'] == "active") { $_GET['type'] = ""; } }

/* Include Libraries */

include_once ROOT.DS.'config.php';
include_once ROOT.DS.'libraries'.DS.'template.class.php';
include_once ROOT.DS.'libraries'.DS.'helper.class.php';

$template = new Template($controller,$action);
$helper = new Helper();

include_once ROOT.DS.'libraries'.DS.'shared.php';
include_once ROOT.DS.'libraries'.DS.'markdown.php';
include_once ROOT.DS.'libraries'.DS.'timeago.php';
include_once ROOT.DS.'libraries'.DS.'score.php';
include_once ROOT.DS.'libraries'.DS.'pagination.class.php';
include_once ROOT.DS.'controllers'.DS.'helpers.php';


/* Check Debug vs Production Mode */
if (DEBUG_MODE == '1')
{
	error_reporting(E_ALL);
	ini_set('display_errors','On');
}





/* Basic Bootstrapping */

include ROOT.DS.'controllers'.DS.$controller.'.php';
if (function_exists($action)) {
	call_user_func($action);
} else {
	call_user_func('index');
}
if ($norender == false) {
	$template->render($noheader);
}