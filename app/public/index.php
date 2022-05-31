<?php
declare(strict_types = 1);

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . '/../src/App.php';

$middleware_conf = require_once "../configuration/middleware.php";
$db_conf = require_once "../configuration/db.php";

$configuration = array_merge($middleware_conf,$db_conf);

$container = require_once __DIR__ . '/../bootstrap/container.php';




$app = new App\App($configuration, $container);
$app->run();