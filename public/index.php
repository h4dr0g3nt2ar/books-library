<?php

declare(strict_types=1);

use App\Infrastructure\Debug\Debug;

define('BASE_PATH', \dirname(__DIR__));

require __DIR__ . '/../vendor/autoload.php';

var_dump(class_exists(\App\Domain\Book\Book::class));

Debug::lm(123);