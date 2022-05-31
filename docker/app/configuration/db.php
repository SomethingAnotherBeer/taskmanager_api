<?php
declare(strict_types = 1);



return [
	'db'=>[
		'host'=>getenv('DB_HOST'),
		'db'=>getenv('DB_NAME'),
		'user'=>getenv('DB_USER'),
		'password'=>getenv('DB_PASSWORD')
	]

];
