<?php
// config/bootstrap.php

use Symfony\Component\Dotenv\Dotenv;

// 1) Charge l’autoloader
require dirname(__DIR__).'/vendor/autoload.php';
echo ">> BOOTSTRAP OK <<\n";

// 2) Charge toujours les .env et expose-les à getenv()
if (class_exists(Dotenv::class)) {
    (new Dotenv())
        ->usePutenv()
        ->bootEnv(dirname(__DIR__).'/.env')
    ;
}
