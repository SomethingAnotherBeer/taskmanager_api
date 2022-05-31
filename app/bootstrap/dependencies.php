<?php

$dependencies_files = glob('../configuration/dependencies/*.php');
$dependencies_list = array_map(function($file)
	{
		return require $file;
	}, 
	$dependencies_files);




return array_merge_recursive(...$dependencies_list);