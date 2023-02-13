<?php

use Core\Application;
use Symfony\Component\Dotenv\Dotenv;

require_once dirname(__DIR__) . '/vendor/autoload.php';

(new Dotenv())->usePutenv()->loadEnv(dirname(__DIR__) . '/.env');

$app = new Application();

$app->run();
