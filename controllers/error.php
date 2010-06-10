<?php

function index() {
	$basePath = basePath();
	header("Location: $basePath");
}