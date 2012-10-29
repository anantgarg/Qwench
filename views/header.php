<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/> 
<head>
<title><?=SITE_TITLE;?></title>
<link type="text/css" rel="stylesheet" href="<?php echo basePathNS();?>/css/main.css">
<link type="text/css" rel="stylesheet" href="<?php echo basePathNS();?>/css/tagscomplete.css">

<script src="<?php echo basePathNS();?>/js/jquery.js"></script>
<script src="<?php echo basePathNS();?>/js/tagscomplete.js"></script>
<script src="<?php echo basePathNS();?>/js/fancyalert.js"></script>


<script type="text/javascript" src="<?php echo basePathNS();?>/js/prettify/prettify.js"></script>

<link href="<?php echo basePathNS();?>/css/prettify.css" type="text/css" rel="stylesheet" />


<?php if(!empty($js)):?><?php echo $js;?><?php endif;?>
</head>
<body onload="prettyPrint()">
<div id="navigation"><div class="navcenter">

<form action="<?php echo basePath();?>/questions" method="get" style="float:left;width:420px;text-align:left;"><input type="textbox" name="search" style="color: #999" value="<?php if (!empty($searchstring)):?><?php echo $searchstring;?><?php endif?>"></form>

<div style="float:right;border-left: 1px solid #13a1c9;border-right: 1px solid #45c9e9;"><ol><li><a href="<?php echo basePath();?>">Home</a></li><li><a href="<?php echo basePath();?>/questions">Questions</a></li><li><a href="<?php echo basePath();?>/tags">Tags</a></li><li><a href="<?php echo basePath();?>/users">Users</a></li><li><a href="<?php echo basePath();?>/questions?type=unanswered">Unanswered</a></li><li><a href="<?php echo basePath();?>/questions/ask">Ask or Contribute</a></li></ol></div>

</div>
<div style="clear:both"></div>
</div>

<div id="container">
	<div id="leftpanel">