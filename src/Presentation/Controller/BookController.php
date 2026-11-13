<?php

declare(strict_types = 1);

namespace App\Presentation\Controller;

use App\Domain\Book\Book;
use App\Infrastructure\Database\PdoBookRepository;
use App\Infrastructure\Database\PdoAuthorRepository;

use App\Domain\Book\Enum\BindingType;
use App\Infrastructure\Database\PdoCountryRepository;
use App\Infrastructure\Database\PdoPublisherRepository;
use App\Infrastructure\Database\PdoSeriesRepository;

final class BookController
{

    public function __construct(
        public readonly PdoBookRepository $bookRepo,
        public readonly PdoAuthorRepository $authorRepo,
        public readonly PdoCountryRepository $countryRepo,
        public readonly PdoPublisherRepository $publisherRepo,
        public readonly PdoSeriesRepository $seriesRepo,
    ){
    }
    public function index():void {

        $books = $this->bookRepo->all();
        $total = $this->bookRepo->count();

        \App\Infrastructure\Debug\Debug::dtc($books);

        $allAuthorIds = [];

        foreach($books as $book){
            $allAuthorIds = array_merge($allAuthorIds, $book->authorIds());
        }

        $allAuthorIds = array_unique($allAuthorIds);


        $authors = $this->authorRepo->findByIds($allAuthorIds);

        $allAuthors = [];

        foreach($authors as $author){
            $allAuthors[$author->id()] = $author;
        }

        $title = 'Список книг';
        $data = [
            'books' => $books,
            'title' => $title,
            'total' => $total,
            'authorsById' => $allAuthors
        ];

        $content = render('book/index', $data);

        require BASE_PATH . '/src/Presentation/View/layout.php';

    }
    public function show( int $id): void{
        $book = $this->bookRepo->findById($id);

        if ($book === null) {
            http_response_code(404);
            echo '<h1>Книга не найдена</h1>';
            echo '<a href="/books">К списку книг</a>';
            return;
        }

        $authors = $this->authorRepo->findByIds($book->authorIds());
        $authorsById = [];

        foreach($authors as $author){
            $authorsById[$author->id()] = $author;
        }

        $title = $book->title();
        $country = $book->countryId() !== null ? $this->countryRepo->findById($book->countryId()) : null;
        $publisher = $book->publisherId() !== null ? $this->publisherRepo->findById($book->publisherId()) : null;
        $series = $book->seriesId() !== null ? $this->seriesRepo->findById($book->seriesId()) : null;

        $data = [
            'book' => $book,
            'title' => $title,
            'country' => $country,
            'publisher' => $publisher,
            'series' => $series,
            'authorsById' => $authorsById
        ];
        $content = render('book/show', $data);

        require BASE_PATH . '/src/Presentation/View/layout.php';

    }
    public function create(): void{
        $authors = $this->authorRepo->all();

        $title = 'Добавить книгу';
        $data = [
            'book' => null,
            'title' => $title,
            'authors' => $authors,
            'countries' => $this->countryRepo->all(),
            'publishers' => $this->publisherRepo->all(),
            'series' => $this->seriesRepo->all(),
            'errors' => []
        ];

        $content = render('book/form', $data);

        require BASE_PATH . '/src/Presentation/View/layout.php';
    }
    public function store(): void{

        $title = $_POST['title'] ?? '';
        $year = (int) ($_POST['year'] ?? 0);
        $authorIds = array_map('intval', $_POST['authorIds'] ?? []);

        $description = trim($_POST['description'] ?? '') ?: null;

        $bindingStr = $_POST['binding'] ?? '';
        $binding = $bindingStr !== '' ? BindingType::from($bindingStr) : null;

        $pagesStr = $_POST['pages'] ?? '';
        $pages = $pagesStr !== '' ? (int) $pagesStr : null;

        $printRunStr = $_POST['print_run'] ?? '';
        $printRun = $printRunStr !== '' ? (int) $printRunStr : null;

        $isbn = trim($_POST['isbn'] ?? '') ?: null;

        $countryId = ($_POST['country_id'] ?? '') !== '' ? (int) $_POST['country_id'] : null;
        $publisherId = ($_POST['publisher_id'] ?? '') !== '' ? (int) $_POST['publisher_id'] : null;
        $seriesId = ($_POST['series_id'] ?? '') !== '' ? (int) $_POST['series_id'] : null;

        $errors = [];

        try {
            $book = Book::create(
                title: $title,
                year: $year,
                description: $description,
                binding: $binding,
                pages: $pages,
                printRun: $printRun,
                isbn: $isbn,
                countryId: $countryId,
                publisherId: $publisherId,
                seriesId: $seriesId,

                authorIds: $authorIds
            );
        }catch (\InvalidArgumentException $e){
            $errors[] = $e->getMessage();
        }
        if ($errors !== []) {

            $authors = $this->authorRepo->all();
            $title = 'Добавить книгу';

            $data = [
                'book' => null,
                'title' => $title,
                'authors' => $authors,
                'errors' => $errors,
                'old' => $_POST,
            ];
            $content = render('book/form', $data);

            require BASE_PATH . '/src/Presentation/View/layout.php';

            return;
        }
        $this->bookRepo->save($book);
        header('Location: /books');

        exit;
    }
    public function edit(int $id): void{
        $book = $this->bookRepo->findById($id);

        if ($book === null) {
            http_response_code(404);
            echo '<h1>Книга не найдена</h1>';
            echo '<a href="/books">К списку книг</a>';
            return;
        }
        $authors = $this->authorRepo->all();
        $title = 'Редактировать книгу';

        $data = [
            'book' => $book,
            'title' => $title,
            'authors' => $authors,
            'countries' => $this->countryRepo->all(),
            'publishers' => $this->publisherRepo->all(),
            'series' => $this->seriesRepo->all(),
            'errors' => []
        ];

        $content = render('book/form', $data);

        require BASE_PATH . '/src/Presentation/View/layout.php';

    }
    public function update(int $id): void{
        $book = $this->bookRepo->findById($id);

        if ($book === null) {
            http_response_code(404);
            echo '<h1>Книга не найдена</h1>';
            echo '<a href="/books">К списку книг</a>';
            return;
        }

        $title = $_POST['title'] ?? '';
        $year = (int) ($_POST['year'] ?? 0);
        $authorIds = array_map('intval', $_POST['authorIds'] ?? []);

        $description = trim($_POST['description'] ?? '') ?: null;

        $bindingStr = $_POST['binding'] ?? '';
        $binding = $bindingStr !== '' ? BindingType::from($bindingStr) : null;

        $pagesStr = $_POST['pages'] ?? '';
        $pages = $pagesStr !== '' ? (int) $pagesStr : null;

        $printRunStr = $_POST['print_run'] ?? '';
        $printRun = $printRunStr !== '' ? (int) $printRunStr : null;

        $isbn = trim($_POST['isbn'] ?? '') ?: null;

        $errors = [];

        try {
            $book->rename($title);
            $book->changeYear($year);
            $book->changeDescription($description);
            $book->changeBinding($binding);
            $book->changePages($pages);
            $book->changePrintRun($printRun);
            $book->changeIsbn($isbn);
            $book->changeCountry($countryId);
            $book->changePublisher($publisherId);
            $book->changeSeries($seriesId);

            $authorIdsByBook = $book->authorIds();

            foreach($authorIds as $authorId){
                if(!in_array($authorId, $authorIdsByBook)){
                    $book->addAuthor($authorId);
                }
            }

            foreach ($authorIdsByBook as $authorIdByBook) {
                if(!in_array($authorIdByBook, $authorIds)){
                    $book->removeAuthor($authorIdByBook);
                }
            }

        }catch (\InvalidArgumentException $e){
            $errors[] = $e->getMessage();
        }
        if ($errors !== []) {

            $title = 'Редактировать книгу';

            $data = [
                'book' => $book,
                'title' => $title,
                'authors' => $this->authorRepo->all(),
                'errors' => $errors,
                'old' => $_POST,
            ];
            $content = render('book/form', $data);

            require BASE_PATH . '/src/Presentation/View/layout.php';

            return;
        }
        $this->bookRepo->save($book);
        header('Location: /books/' . $book->id());

        exit;
    }
    public function delete(int $id): void{
        $book = $this->bookRepo->findById($id);

        if ($book === null) {
            http_response_code(404);
            echo '<h1>Книга не найдена</h1>';
            echo '<a href="/books">К списку книг</a>';
            return;
        }
        $this->bookRepo->delete($book);

        header('Location: /books');

        exit;
    }

}