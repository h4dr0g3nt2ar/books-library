<?php

declare(strict_types=1);

namespace App\Presentation\Controller;
use App\Domain\Publisher\Publisher;
use App\Infrastructure\Database\PdoBookRepository;
use App\Infrastructure\Database\PdoAuthorRepository;
use App\Infrastructure\Database\PdoPublisherRepository;
final class PublisherController
{
    public function __construct(
        private readonly PdoPublisherRepository $publisherRepo,
        private readonly PdoBookRepository $bookRepo,
        private readonly PdoAuthorRepository $authorRepo
    ){ }
    public function index(): void{
        $publishers = $this->publisherRepo->all();
        $total = $this->publisherRepo->count();

        $title = 'Список издательств';
        $data = [
            'publishers' => $publishers,
            'title' => $title,
            'total' => $total,
        ];

        $content = render('publisher/index', $data);

        require BASE_PATH . '/src/Presentation/View/layout.php';
    }
    public function show(int $id): void{
        $publisher = $this->publisherRepo->findById($id);
        if ($publisher === null) {
            http_response_code(404);
            echo '<h1>Издательство не найдено</h1>';
            echo '<a href="/publishers">К списку издательств</a>';
            return;
        }
        $books = $this->bookRepo->findByPublisherId($id);

        $allAuthorIds = [];
        foreach ($books as $book) {
            $allAuthorIds[] = $book->getAuthorId();
        }
        $allAuthorIds = array_unique($allAuthorIds);
        $authors = $allAuthorIds === [] ? [] : $this->authorRepo->findByIds($allAuthorIds);
        $authorsById = [];
        foreach ($authors as $author) {
            $authorsById[$author->id()] = $author;
        }
        $content = render('publisher/show', [
            'publisher' => $publisher,
            'books' => $books,
            'authorsById' => $authorsById,
            'title' => $publisher->name(),
            ]
        );
        require BASE_PATH . '/src/Presentation/View/layout.php';

    }
    public function create(): void{
        $title = 'Добавить издательство';
        $data = [
            'publisher' => null,
            'title' => $title,
            'errors' => []
        ];

        $content = render('publisher/form', $data);

        require BASE_PATH . '/src/Presentation/View/layout.php';
    }
    public function store(): void{

        $title = $_POST['name'] ?? '';

        $errors = [];

        try {
            $publisher = Publisher::create(
                name: $title,
                description: $_POST['description'] ?? ''
            );
        }catch (\InvalidArgumentException $e){
            $errors[] = $e->getMessage();
        }
        if ($errors !== []) {
            $title = 'Добавить издательство';

            $data = [
                'publisher' => null,
                'title' => $title,
                'errors' => $errors,
                'old' => $_POST,
            ];
            $content = render('publisher/form', $data);

            require BASE_PATH . '/src/Presentation/View/layout.php';

            return;
        }
        $this->publisherRepo->save($publisher);
        header('Location: /publishers');

        exit;
    }
    public function edit(int $id): void{
        $publisher = $this->publisherRepo->findById($id);
        if ($publisher === null) {
            http_response_code(404);
            echo '<h1>Издательство не найдено</h1>';
            echo '<a href="/publishers">К списку издательств</a>';
            return;
        }
        $title = 'Редактировать издательство';

        $data = [
            'publisher' => $publisher,
            'title' => $title,
            'errors' => []
        ];

        $content = render('publisher/form', $data);

        require BASE_PATH . '/src/Presentation/View/layout.php';
    }
    public function update(int $id): void{
        $publisher = $this->publisherRepo->findById($id);
        if ($publisher === null) {
            http_response_code(404);
            echo '<h1>Издательство не найдено</h1>';
            echo '<a href="/publishers">К списку издательств</a>';
            return;
        }
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $errors = [];

        try{
            $publisher->rename($name);
            $publisher->changeDescription($description);
        }catch(\InvalidArgumentException $e){
            $errors[] = $e->getMessage();
        }
        if ($errors !== []) {
            $title = 'Редактировать издательство';

            $data = [
                'publisher' => $publisher,
                'title' => $title,
                'errors' => $errors
            ];

            $content = render('publisher/form', $data);

            require BASE_PATH . '/src/Presentation/View/layout.php';

            return;
        }else{
            $this->publisherRepo->save($publisher);
            header('Location: /publishers'); exit;
        }
    }
    public function delete(int $id): void{
        $publisher = $this->publisherRepo->findById($id);
        if ($publisher === null) {
            http_response_code(404);
            echo '<h1>Издательство не найдено</h1>';
            echo '<a href="/publishers">К списку издательств</a>';
            return;
        }
        $this->publisherRepo->delete($publisher);
        header('Location: /publishers');
        exit;
    }
}