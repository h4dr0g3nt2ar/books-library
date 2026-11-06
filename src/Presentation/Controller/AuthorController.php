<?php
declare(strict_types = 1);

namespace App\Presentation\Controller;

class AuthorController
{
    public function __construct
        public readonly PdoBookRepository $bookRepo,
        public readonly PdoAuthorRepository $authorRepo
    ){
    }
    public function index(): void {
        $authors = $this->authorRepo->all();
        $total = $this->authorRepo->count();
        $title = 'Список авторов';
        $data = [
            'authors' => $authors,
            'title' => $title,
            'total' => $total
        ];

        $content = render('author/index', $data);

        require BASE_PATH . '/src/Presentation/View/layout.php';
    }
    public function show(int $id): void {
        $author = $this->authorRepo->findById($id);
        if($author === null){
            http_response_code(404);
            echo '<h1>Книга не найдена</h1>';
            echo '<a href="/books">К списку книг</a>';
            return;
        }
        $books = $this->bookRepo->findByAuthorId($id);

        $title = $author->name();

        $data = [
            'author' => $author,
            'title' => $title,
            'booksByAuthor' => $books
        ];
        $content = render('author/show', $data);

        require BASE_PATH . '/src/Presentation/View/layout.php';
    }
    public function create(): void {

    }
    public function store(): void {

    }
    public function edit(int $id): void {

    }
    public function update(int $id): void {

    }
    public function delete(int $id): void {

    }
}