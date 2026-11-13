<?php

declare(strict_types=1);

define('BASE_PATH', \dirname(__DIR__));

require __DIR__ . '/../vendor/autoload.php';
require BASE_PATH . '/src/Presentation/View/render.php';

use App\Infrastructure\Database\PdoBookRepository;
use App\Infrastructure\Database\PdoAuthorRepository;
use App\Infrastructure\Database\PdoCountryRepository;
use App\Infrastructure\Database\PdoPublisherRepository;
use App\Infrastructure\Database\PdoSeriesRepository;
use App\Presentation\Controller\BookController;
use App\Presentation\Controller\AuthorController;
use App\Presentation\Controller\CountryController;
use App\Presentation\Controller\PublisherController;
use App\Presentation\Controller\SeriesController;
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
$countryRepo = new PdoCountryRepository($pdo);
$publisherRepo = new PdoPublisherRepository($pdo);
$seriesRepo = new PdoSeriesRepository($pdo);

$bookController = new BookController($bookRepo, $authorRepo, $countryRepo, $publisherRepo, $seriesRepo);
$authorController = new AuthorController($bookRepo, $authorRepo);
$countryController = new CountryController($countryRepo);
$publisherController = new PublisherController($publisherRepo, $bookRepo, $authorRepo);
$seriesController = new SeriesController($seriesRepo, $bookRepo, $authorRepo);

$router = new Router();

$router->get('/', function() {
    $title = 'Главная';
    $content = render('home');
    require BASE_PATH . '/src/Presentation/View/layout.php';
});

//книги

$router->get('/books', function() use($bookController){
    $bookController->index();
});
$router->get('/books/create', function () use ($bookController){
    $bookController->create();
});
$router->post('/books', function () use ($bookController) {
    $bookController->store();
});
$router->get('/books/{id}/edit', function ($id) use ($bookController){
    $bookController->edit((int) $id);
});
$router->post('/books/{id}', function ($id) use ($bookController) {
    $bookController->update((int) $id);
});
$router->post('/books/{id}/delete', function ($id) use ($bookController){
    $bookController->delete((int) $id);
});
$router->get('/books/{id}', function ($id) use ($bookController){
    $bookController->show((int) $id);
});

//авторы

$router->get('/authors', function () use ($authorController) {
    $authorController->index();
});
$router->get('/authors/create', function () use ($authorController){
    $authorController->create();
});
$router->post('/authors', function () use ($authorController) {
    $authorController->store();
});
$router->get('/authors/{id}/edit', function ($id) use ($authorController){
    $authorController->edit((int) $id);
});
$router->post('/authors/{id}', function ($id) use ($authorController) {
    $authorController->update((int) $id);
});
$router->post('/authors/{id}/delete', function ($id) use ($authorController){
    $authorController->delete((int) $id);
});
$router->get('/authors/{id}', function ($id) use ($authorController) {
    $authorController->show((int) $id);
});


//страны

$router->get('/countries', function() use($countryController){
    $countryController->index();
});
$router->get('/countries/create', function() use($countryController){
    $countryController->create();
});
$router->post('/countries', function() use($countryController){
    $countryController->store();
});
$router->get('/countries/{id}/edit', function($id) use ($countryController){
    $countryController->edit((int) $id);
});
$router->post('/countries/{id}', function($id) use ($countryController){
    $countryController->update((int) $id);
});
$router->post('/countries/{id}/delete', function($id) use ($countryController){
    $countryController->delete((int) $id);
});


//издательства

$router->get('/publishers', function () use ($publisherController) {
    $publisherController->index();
});
$router->get('/publishers/create', function () use ($publisherController) {
    $publisherController->create();
});
$router->post('/publishers', function () use ($publisherController) {
    $publisherController->store();
});
$router->get('/publishers/{id}/edit', function ($id) use ($publisherController) {
    $publisherController->edit((int) $id);
});
$router->post('/publishers/{id}', function ($id) use ($publisherController) {
    $publisherController->update((int) $id);
});
$router->post('/publishers/{id}/delete', function ($id) use ($publisherController) {
    $publisherController->delete((int) $id);
});

//серии

$router->get('/series', function () use ($seriesController) {
    $seriesController->index();
});
$router->get('/series/create', function () use ($seriesController) {
    $seriesController->create();
});
$router->post('/series', function () use ($seriesController) {
    $seriesController->store();
});
$router->get('/series/{id}/edit', function ($id) use ($seriesController) {
    $seriesController->edit((int) $id);
});
$router->post('/series/{id}', function ($id) use ($seriesController) {
    $seriesController->update((int) $id);
});
$router->post('/series/{id}/delete', function ($id) use ($seriesController) {
    $seriesController->delete((int) $id);
});
$router->get('/publishers/{id}', function ($id) use ($publisherController) {
    $publisherController->show((int) $id);
});
$router->get('/series/{id}', function ($id) use ($seriesController) {
    $seriesController->show((int) $id);
});

$router->run($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);

