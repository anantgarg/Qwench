<html>
	<head>
		<title>Qwench Setup</title>
		<style type="text/css">
			*{
				font-family: Verdana, Helvetica, sans-serif;
				font-size: .97em;
				background-color: #EBF3F7;
			}

			select, input{
				width: 250px;
				padding: 4px;
				background-color: #FFFFFF;
				border: 1px solid #A3A3A3;
			}

			table{
				border: 1px solid #A3A3A3;
				width: 900px;
				margin-bottom: 40px;
			}

			td {
				vertical-align: top;
				padding: 0px 10px;
				width:200px;
			}
			em{
				font-size: 0.9em;
				color: #B2B3B1;
			}

			.go{
				border: 2px solid #FFFFFF;
				background: #B7F01A;
				color: #FFFFFF;
				font-weight: bold;
				font-size: 2em;
				padding:20px;
				margin:50px;
				text-decoration: none;
			}
		</style>
	</head>
	<body>
<?php
if(isset($_POST['dodatabase'])) {

	include (dirname(__FILE__))."/config.php";
	include (dirname(__FILE__))."/libraries/shared.php";

	db();

	$body = '';
	$path = '';

	$rollback = 0;
	$errors = '';

$content = <<<EOD
DROP TABLE IF EXISTS `activities`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `activities` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL,
  `activity` varchar(255) NOT NULL default '',
  `points` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `activityid` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

DROP TABLE IF EXISTS `answers`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `answers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `questionid` int(10) unsigned NOT NULL,
  `description` text character set latin1 NOT NULL,
  `created` timestamp NOT NULL default '0000-00-00 00:00:00',
  `updated` timestamp NOT NULL default '0000-00-00 00:00:00',
  `userid` int(10) unsigned NOT NULL,
  `accepted` int(10) unsigned NOT NULL,
  `votes` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
SET character_set_client = @saved_cs_client;

DROP TABLE IF EXISTS `answers_votes`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `answers_votes` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `answerid` int(10) unsigned NOT NULL,
  `userid` int(10) unsigned NOT NULL,
  `vote` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
SET character_set_client = @saved_cs_client;

DROP TABLE IF EXISTS `comments`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `comments` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `type` int(10) unsigned NOT NULL,
  `comment` text character set latin1 NOT NULL,
  `votes` int(10) unsigned NOT NULL default '0',
  `created` timestamp NOT NULL default '0000-00-00 00:00:00',
  `userid` int(10) unsigned NOT NULL,
  `typeid` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

DROP TABLE IF EXISTS `comments_votes`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `comments_votes` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `commentid` int(10) unsigned NOT NULL,
  `userid` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

DROP TABLE IF EXISTS `favorites`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `favorites` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `questionid` int(10) unsigned NOT NULL,
  `userid` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
SET character_set_client = @saved_cs_client;


DROP TABLE IF EXISTS `questions`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `questions` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` text character set latin1 NOT NULL,
  `description` text character set latin1 NOT NULL,
  `created` timestamp NOT NULL default '0000-00-00 00:00:00',
  `updated` timestamp NOT NULL default '0000-00-00 00:00:00',
  `link` text character set latin1 NOT NULL,
  `userid` int(10) unsigned NOT NULL,
  `linkcache` longtext character set latin1 NOT NULL,
  `votes` int(11) NOT NULL default '0',
  `accepted` int(10) unsigned NOT NULL default '0',
  `answers` int(10) unsigned NOT NULL default '0',
  `kb` int(10) unsigned NOT NULL default '0',
  `notify` int(10) unsigned NOT NULL default '0',
  `slug` text NOT NULL,
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `title` (`title`,`description`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;


DROP TABLE IF EXISTS `questions_votes`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `questions_votes` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `questionid` int(10) unsigned NOT NULL,
  `userid` int(10) unsigned NOT NULL,
  `vote` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
SET character_set_client = @saved_cs_client;


DROP TABLE IF EXISTS `tags`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `tags` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `tag` varchar(255) character set latin1 NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
SET character_set_client = @saved_cs_client;

DROP TABLE IF EXISTS `tags_questions`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `tags_questions` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `tagid` int(10) unsigned NOT NULL,
  `questionid` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
SET character_set_client = @saved_cs_client;

DROP TABLE IF EXISTS `users`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `password` varchar(255) character set latin1 NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `website` varchar(255) NOT NULL,
  `realname` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `birthday` date NOT NULL,
  `aboutme` text NOT NULL,
  `points` int(11) NOT NULL default '0',
  `moderator` int(10) unsigned NOT NULL,
  `active` int(10) unsigned NOT NULL,
  `created` datetime NOT NULL,
  `lastactivity` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
SET character_set_client = @saved_cs_client;


DROP TABLE IF EXISTS `confirm`;
CREATE TABLE IF NOT EXISTS `confirm` (
  `confirm_id` int(11) NOT NULL AUTO_INCREMENT,
  `confirm_validator` varchar(32) NOT NULL,
  `confirm_userid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`confirm_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
SET character_set_client = @saved_cs_client;

EOD;

$q = preg_split('/;[\r\n]+/',$content);

foreach ($q as $query)
{
	if (strlen($query) > 4)
	{
		$result = mysql_query($query);
		if (!$result)
		{
			$rollback = 1;
			$errors .= mysql_error()."<br/>\n";
		}
	}
}
?>
		<p>Database import completed.</p>
		<div style="width:900px;text-align:center;"><a href="index.php">Go to your new Quench installation!</a></div>
<?php
}
elseif(isset($_POST['doconfig']))
{

	$html_email = 'FALSE';
	if(!empty($_POST['html']))
	{
		$html_email = 'TRUE';
	}

	$allow_visitors = 'TRUE';
	if(empty($_POST['allowvisit']))
	{
		$allow_visitors = 'FALSE';
	}

	$base_path = "define('BASE_PATH',BASE_DIR);";
	if($_POST['base_path'] == "on")
	{
		$base_path = "define('BASE_PATH',BASE_DIR.'/index.php');";
	}

	$base_dir = "";
	if(!empty($_POST['base_dir']))
	{
		$base_dir = "/".$_POST['base_dir'];
	}

	$string='<?php
/* 
 * Please use the install.php from the web to setup the quench
 * instead of changing the values in here.
 */

// Database Details
define(\'SERVERNAME\', \''.$_POST['dbhost'].'\');
define(\'SERVERPORT\',\''.$_POST['dbport'].'\');
define(\'DBUSERNAME\',\''.$_POST['dbuser'].'\');
define(\'DBPASSWORD\',\''.$_POST['dbpassword'].'\');
define(\'DBNAME\',\''.$_POST['dbname'].'\');

define(\'ANSWERS_PER_PAGE\',\''.$_POST['answers_per_page'].'\');
define(\'QUESTIONS_PER_PAGE\',\''.$_POST['questions_per_page'].'\');
define(\'ALLOW_VISITORS\','.$allow_visitors.'); // If you want only logged in users to view the site

define(\'SALT\',\''.$_POST['salt'].'\'); // Do not change salt after users have registered

date_default_timezone_set("'.$_POST['timezone'].'"); // Set default timezone if you want or comment the line below
define(\'BASE_DIR\',\''.$base_dir.'\');

// If URL-Rewriting does not work then set
// define(\'BASE_PATH\',BASE_DIR.\'/index.php\');
// If URL-Rewriting works, then leave the line below as is
//define(\'BASE_PATH\',BASE_DIR);
'.$base_path.'

//email configuration
define(\'HTML_EMAIL\','.$html_email.');
define(\'MAILFROM\',\''.$_POST['mailfrom'].'\');

define(\'SITETITLE\',\''.$_POST['title'].'\');


//To use reCAPTCHA you must get an API key from 
//http://recaptcha.net/api/getkey
define(\'PRIVATEKEY\',\''.$_POST['privatekey'].'\');
define(\'PUBLICKEY\',\''.$_POST['publickey'].'\');
';

	$config_file = fopen("config.php","w");
	fwrite($config_file, $string);
	fclose($config_file);
?>
		<h3>Configuration File Created!</h3>
		Now you can proceed to the next step.<br><br><br>
		<form name="configform" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
			<div style="width:900px;text-align:center;"><a class="go" href="javascript:document.configform.submit()">Write Configuration</a></div>
			<input type="hidden" value="true" name="dodatabase">
		</form>

<?php
}
else
{
?>
		<h2>Welcome to the Qwench setup</h2>
		<form name="configform" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">

			<h3>Site Setup</h3>
			<table>
				<tr>
					<td>Site Title</td>
					<td rowspan="2"><input type="textbox" class="textbox" name="title" value="Qwench" /></td>
				</tr>
				<tr>
					<td><em>will be the title of every page.</em></td>
				</tr>
				<tr>
					<td>Installation path</td>
					<td rowspan="2"><input type="textbox" class="textbox" name="base_dir" value="" /></td>
				</tr>
				<tr>
					<td><em>example: for 'http://www.example.com/qwench' put 'quench'.</em></td>
				</tr>
				<tr>
					<td>URL Rewriting</td>
					<td rowspan="2"><input name="base_path" type="checkbox"/></td>
				</tr>
				<tr>
					<td><em>check only if URL-Rewriting does not work.</em></td>
				</tr>
			</table>

			<h3>Database Configuration</h3>
			<table>
				<tr>
					<td>Host</td>
					<td><input type="textbox" class="textbox" name="dbhost" value="localhost" /></td>
				</tr>
				<tr>
					<td>Port</td>
					<td><input type="textbox" class="textbox" name="dbport" value="3306" /></td>
				</tr>
				<tr>
					<td>Database</td>
					<td><input type="textbox" class="textbox" name="dbname" value="qwench" /></td>
				</tr>
				<tr>
					<td>Username</td>
					<td><input type="textbox" class="textbox" name="dbuser" value="username" /></td>
				</tr>
				<tr>
					<td>Password</td>
					<td><input type="textbox" class="textbox" name="dbpassword" value="password" /></td>
				</tr>
			</table>

			<h3>Site Configuration</h3>
			<table>
				<tr>
					<td>Password Salt</td>
					<td><input type="textbox" class="textbox" name="salt" value="salt" /></td>
				</tr>
				<tr>
					<td><em>Do not change salt after users have registered.</em></td>
				</tr>
				<tr>
					<td>Timezone</td>
					<td>
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
						</select>
					</td>
				</tr>
				<tr>
					<td>Answers per page</td>
					<td>
						<select name="answers_per_page">
							<option value="5">5
							<option value="10" selected>10
							<option value="20">20
						</select>
					</td>
				</tr>
				<tr>
					<td>Questions per page</td>
					<td>
						<select name="questions_per_page">
							<option value="5">5
							<option value="10" selected>10
							<option value="20">20
						</select>
					</td>
				</tr>
				<tr>
					<td>Allow visitors</td>
					<td><input name="allowvisit" type="checkbox"/></td>
				</tr>
			</table>

			<h3>Email Configuration</h3>
			<table>
				<tr>
					<td>Send emails from</td>
					<td><input type="textbox" class="textbox" name="mailfrom" value="me@example.com" /></td>
				</tr>
				<tr>
					<td>Send HTML emails</td>
					<td><input name="html" type="checkbox"/></td>
				</tr>
			</table>


			<h3>reCaptcha Configuration</h3>
			<em>Go ahead and get your keys from the <a href="http://recaptcha.net/whyrecaptcha.html">reCaptcha</a> site!</em><br><br>
			<table>
				<tr>
					<td>Private Key</td>
					<td><input type="textbox" class="textbox" name="privatekey" value="privatekey" /></td>
				</tr>
				<tr>
					<td>Public Key</td>
					<td><input type="textbox" class="textbox" name="publickey" value="publickey" /></td>
				</tr>
			</table>

			<div style="width:900px;text-align:center;"><a class="go" href="javascript:document.configform.submit()">Write Configuration</a></div>
			<input type="hidden" value="true" name="doconfig">
		</form>
	</body>
</html>
<?php
};
?>