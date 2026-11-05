<?php

declare(strict_types=1);

define('BASE_PATH', \dirname(__DIR__));

require __DIR__ . '/../vendor/autoload.php';
require BASE_PATH . '/src/Presentation/View/render.php';

use App\Infrastructure\Database\PdoBookRepository;
use App\Infrastructure\Database\PdoAuthorRepository;
use App\Presentation\Controller\BookController;
use App\Presentation\Controller\AuthorController;
use App\Presentation\Router;

$pdo = new \PDO(
    'mysql:host=127.0.0.1;dbname=books;charset=utf8mb4',
    'root',
    '',
    [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
    ]
);

$bookRepo = new PdoBookRepository($pdo);
$authorRepo = new PdoAuthorRepository($pdo);

$bookController = new BookController($bookRepo, $authorRepo);
//$authorControler = new App\Presentation\Controller\AuthorController($authorRepo);

$router = new Router();

$router->get('/', function() {
    $title = 'Главная';
    $content = render('home');
    require BASE_PATH . '/src/Presentation/View/layout.php';
});
$router->get('/books', function() use($bookController){
    $bookController->index();
});
$router->get('/books/{id}', function ($id) use ($bookController){
    $bookController->show((int) $id);
});

$router->run($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);

