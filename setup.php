<?php 
if(isset($_POST['submit']))	{
if ($_POST['base_path']=="on"){
$base_path = "define('BASE_PATH',BASE_DIR.'/index.php');";	}
else{
$base_path = "define('BASE_PATH',BASE_DIR);";
}
if ($_POST['base_dir']==""){
$base_dir = ""; }
else{
$base_dir ="/".$_POST['base_dir'];
}
$string='<?php

// Database Details
define(\'SERVERNAME\',\''.$_POST['dbhost'].'\');
define(\'SERVERPORT\',\''.$_POST['dbport'].'\');
define(\'DBUSERNAME\',\''.$_POST['dbuser'].'\');
define(\'DBPASSWORD\',\''.$_POST['dbpassword'].'\');
define(\'DBNAME\',\''.$_POST['dbname'].'\');

define(\'ANSWERS_PER_PAGE\',\''.$_POST['answers_per_page'].'\');
define(\'QUESTIONS_PER_PAGE\',\''.$_POST['questions_per_page'].'\');

// If you want only logged in users to view the site
define(\'ALLOW_VISITORS\',\''.$_POST['allowvisit'].'\');

// Do not change salt after users have registered
define(\'SALT\',\'yoursecurestringoverhere\');

// Set default timezone if you want or comment the line below
date_default_timezone_set("'.$_POST['timezone'].'");

// No trailing slash
// Path to the Qwench folder
// If you have installed Qwench in your 
// root folder then set
// define(\'BASE_DIR\',\'\');
//define(\'BASE_DIR\',\'/qwench\');
define(\'BASE_DIR\',\''.$base_dir.'\');

// If URL-Rewriting does not work then set
// define(\'BASE_PATH\',BASE_DIR.\'/index.php\');
// If URL-Rewriting works, then leave the line below as is
//define(\'BASE_PATH\',BASE_DIR);
'.$base_path.'

//email configuration
define(\'HTML_EMAIL\','.$_POST['html'].');
define(\'MAILFROM\',\''.$_POST['mailfrom'].'\');

define(\'SITETITLE\',\''.$_POST['title'].'\');';







$config_file=fopen("config.php","w");
fwrite($config_file,$string);
fclose($config_file);


echo '<h2>Configuration File Created!</h2>';
echo '<a href=install.php>next step: database import</a>';
}
?>





<html>
<head><title>Qwench Setup</title>
</head>
<h1>Welcome</h1><html>
   <body>
      <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">Title<br/>
      Site Title <i>(will be the title of every page)</i><br/>
      <input type="textbox" class="textbox" style="width:250px" name="title" value="Qwench" /><br/>
      Path where you have installed Qwench<br/><i>example if www.example.com/mypath  put mypath.<br/>If you have installed it in root leave blank</i><br/>
      <input type="textbox" class="textbox" style="width:250px" name="base_dir" value="" /><br/>
      Check only if URL-Rewriting does not work
      <input name="base_path" type="checkbox"/>
      <hr align="left" size="2" width="300" color="blue">
      
      <h3>Database Configuration</h3>
      <input type="textbox" class="textbox" style="width:250px" name="dbhost" value="Server  (example:localhost)" /><br/>
      <input type="textbox" class="textbox" style="width:250px" name="dbuser" value="Database Username" /><br/>
      <input type="textbox" class="textbox" style="width:250px" name="dbport" value="3306" /><br/>
      <input type="textbox" class="textbox" style="width:250px" name="dbpassword" value="Password" /><br/>
      <input type="textbox" class="textbox" style="width:250px" name="dbname" value="Database Name" /><br/>
      <hr align="left" size="2" width="300" color="blue">
      <h3>Site Configuration</h3>
      Select your Timezone<br/>
      <select id="timezone" name="timezone" style="width:250px">
	<?php
	$timezone_identifiers = DateTimeZone::listIdentifiers();
	foreach($timezone_identifiers as $value)
	{
		if (preg_match('/^(America|Antartica|Arctic|Asia|Atlantic|Europe|Indian|Pacific)\//', $value))
		{
			echo "<option>$value</option>";
		}
	}
	?>
</select><br/>
      Answers per Page
   	<select name="answers_per_page"> 
	<option value="5">5
	<option value="10" selected>10
	<option value="20">20
	</select><br/>
      Questions per Page
   	<select name="questions_per_page"> 
	<option value="5">5
	<option value="10" selected>10
	<option value="20">20
	</select><br/>
      Allow visitors
   	<select name="allowvisit"> 
	<option value="1">True 
	<option value="0">False
	</select><br/>
	<hr align="left" size="2" width="300" color="blue">
	<h3>Email Configuration</h3>
      Enable html in email
   	<select name="html"> 
	<option value="True">True 
	<option value="False">False
	</select><br/>
	<input type="textbox" class="textbox" style="width:250px" name="mailfrom" value="me@example.com" /><br/><br/>


<br>

	<input type="submit" value="submit" name="submit">  
</form>

   </body>
</html>

