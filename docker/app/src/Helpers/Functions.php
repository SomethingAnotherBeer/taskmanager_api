<?php
namespace App\Helpers;

function getArrLengthRecursive(array $arr):array | int
{	
	if(empty($arr)) return 0;

	return array_reduce($arr, function($sum,$item)
	{
		return (is_array($item)) ? getArrLengthRecursive($item) : $sum+= mb_strlen($item);
	});
}

function clearGlobals()
{
	unset($_SERVER);
	unset($_POST);
	unset($_GET);
	unset($_FILES);
	
}