<?php

function getUser($id) {
	global $helper;

	$id = sanitize($id,"int");
	$sql = ("SELECT * FROM users WHERE id = '".escape($id)."'");
	$query = mysql_query($sql);
	$result = mysql_fetch_array($query);

	$helper->set('user',$result);
	return $helper->render();
}

