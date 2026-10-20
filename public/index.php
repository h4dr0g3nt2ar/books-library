<?php

declare(strict_types=1);

use App\Infrastructure\Debug\Debug;

define('BASE_PATH', \dirname(__DIR__));

require __DIR__ . '/../vendor/autoload.php';

use App\Domain\Book\Book;
use App\Domain\Author\Author;

var_dump(class_exists(\App\Domain\Book\Book::class));


$pdo = new \PDO(
    'mysql:host=127.0.0.1;dbname=books;charset=utf8mb4',
    'root',
    '',
    [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
    ]
);
$repo = new PdoBookRepository($pdo);

$book = Book::create('Зов Ктулху',  2023, null, [2]);
$repo->save($book);

var_dump($book->isNew());
var_dump($book->id());

$repo = new PdoBookRepository($pdo);

$book = $repo->findById(1);
echo 'id: '; var_dump($book->id());          // int(1)
echo 'year: '; var_dump($book->year());      // int(1968)
echo 'authorIds: '; var_dump($book->authorIds()); // array(1) { [0]=> int(1) }
echo 'createdAt: '; var_dump($book->createdAt()->format('Y-m-d')); // "2026-10-20"

$notFound = $repo->findById(999);
echo 'not found: '; var_dump($notFound);     // NULL





$books = $repo->all();
var_dump(count($books));   // 3

// Пустая страница (offset за пределы)
var_dump($repo->all(10, 100));   // array(0) {}