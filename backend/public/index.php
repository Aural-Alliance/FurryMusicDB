<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);
ini_set('display_errors', '1');

require dirname(__DIR__) . '/vendor/autoload.php';

$app = App\AppFactory::createApp();
$app->run();
