<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
use Dotenv\Dotenv;

$dotEnv = Dotenv::createUnsafeImmutable(
    paths: __DIR__,
    names: ['.env', '.env.example']
);
$dotEnv->safeLoad();
