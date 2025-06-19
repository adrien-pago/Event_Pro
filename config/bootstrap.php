1<?php
// config/bootstrap.php

use Symfony\Component\Dotenv\Dotenv;

// 1) autoload
require __DIR__.'/../vendor/autoload.php';

// 2) chargement des vars d'env via Dotenv
if (class_exists(Dotenv::class)) {
    // loadEnv lira .env, .env.local, .env.<env> et .env.<env>.local
    (new Dotenv())->loadEnv(__DIR__.'/../.env');
} else {
    throw new LogicException('Please run "composer require symfony/dotenv --dev" to load environment variables.');
}

(new Dotenv())->bootEnv(__DIR__.'/../.env');
