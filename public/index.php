<?php

declare(strict_types=1);

define('BASE_PATH', \dirname(__DIR__));

require __DIR__ . '/../vendor/autoload.php';

use App\Domain\Book\Book;
use App\Infrastructure\Database\PdoBookRepository;
use App\Infrastructure\Debug\Debug;

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


Debug::dtc('--- ТЕСТ 3: Изменение ---', 'test');
$book = $repo->findById(5);
$book->changeDescription('культовый рассказ американского писателя Говарда Филлипса Лавкрафта, ставший основой жанра космического ужаса и мифов Ктулху');
$repo->save($book);

Debug::dtc('--- ТЕСТ 5: Списки ---', 'test');

$books = $repo->all();
Debug::dtc(count($books), 'count всех книг');

// Пагинация
$firstTwo = $repo->all(2, 0);
Debug::dtc(count($firstTwo), 'первые 2 книги');

// Пустая страница
$empty = $repo->all(10, 1000);
Debug::dtc($empty, 'пустая страница (offset 1000)');

// Общее количество
Debug::dtc($repo->count(), 'count()');