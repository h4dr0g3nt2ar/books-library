<?php

declare(strict_types=1);

use App\Infrastructure\Debug\Debug;

define('BASE_PATH', \dirname(__DIR__));

require __DIR__ . '/../vendor/autoload.php';

use App\Domain\Book\Book;
use App\Domain\Author\Author;

var_dump(class_exists(\App\Domain\Book\Book::class));

$author = Author::create('Абдула Альхазред', 'Безумный араб, автор проклятого Некрономикона' );
var_dump($author->id());
var_dump($author->name());
var_dump($author->description());
var_dump($author->createdAt());
var_dump($author->updatedAt());
