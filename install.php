<?php

include (dirname(__FILE__))."/config.php";

function db() {
	$dbh = mysql_connect(SERVERNAME.':'.SERVERPORT,DBUSERNAME,DBPASSWORD);
	return mysql_selectdb(DBNAME,$dbh);
}

$message = 'Database import completed. ';
$errorCount = 0;

/*  We cannot assume the use configured the database right.
    So lets be sure we can connect to the database first.
*/

    if (db() == false)
    {
      $message = 'Error connecting to the database. <br/>  Please edit the config.php file to match you database settings.';
    }
    else
    {

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
  `points` int(11) NOT NULL default '0',
  `moderator` int(10) unsigned NOT NULL,
  `created` datetime NOT NULL,
  `lastactivity` datetime NOT NULL,
  PRIMARY KEY  (`id`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
SET character_set_client = @saved_cs_client;

EOD;

$q = preg_split('/;[\r\n]+/',$content);

foreach ($q as $query) {
  if (strlen($query) > 4) {
    $result = mysql_query($query);
    if (!$result) {
      $rollback = 1;
      $errors .= mysql_error()."<br/>\n";
    }
  }
}
}

?>
<html>
<head>
  <title>Install</title>
  <style>
  body {
    padding:0;
    margin:0;
    font-family: "Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif;
    font-size: 14px;
    color: #333333;
  }
  a
  {
    color: blue;
    font-style: italic;
    text-decoration: none;
  }
  </style>
</head>
<body>
  <div class="setup"><?php echo $message; ?>
    <br/>
    <font style="color: red;">Security Alert!!!</font>
    <br/>
    <strong>Delete the install.php file from your server.</strong>
    <br/>
    <br/>
<a href="index.php"> Try out your shiny new server. click here
</a>
  </div>
</body>
</html>

<?php
unlink(install.php);
?>
