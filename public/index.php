<?php

declare(strict_types=1);

define('BASE_PATH', \dirname(__DIR__));

require __DIR__ . '/../vendor/autoload.php';

require BASE_PATH . '/src/Presentation/View/render.php';

////use App\Domain\Book\Book;
//use App\Infrastructure\Database\PdoBookRepository;
////use App\Infrastructure\Debug\Debug;

$pdo = new \PDO(
    'mysql:host=127.0.0.1;dbname=books;charset=utf8mb4',
    'root',
    '',
    [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
    ]
);

//$repo = new PdoBookRepository($pdo);

$content = render('home');

$bookRepo = new App\Infrastructure\Database\PdoBookRepository($pdo);
$authorRepo = new App\Infrastructure\Database\PdoAuthorRepository($pdo);

$bookController = new App\Presentation\Controller\BookController($bookRepo, $authorRepo);

$bookController->index();

require BASE_PATH . '/src/Presentation/View/layout.php';