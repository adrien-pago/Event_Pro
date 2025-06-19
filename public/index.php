<?php

use App\Kernel;
use Symfony\Component\HttpFoundation\Request;


// 1) charge .env, .env.local, etc.
require __DIR__.'/../config/bootstrap.php';

// 2) ensuite on autoload Runtime
require_once dirname(__DIR__).'/vendor/autoload.php';

$kernel = new Kernel($_SERVER['APP_ENV'] ?? 'dev', $_SERVER['APP_DEBUG'] ?? true);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
