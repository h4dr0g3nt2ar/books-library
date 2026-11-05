<?php

declare(strict_types = 1);

namespace App\Presentation\Controller;

use App\Infrastructure\Database\PdoBookRepository;
use App\Infrastructure\Database\PdoAuthorRepository;


final class BookController
{

    public function __construct(
        public readonly PdoBookRepository $bookRepo,
        public readonly PdoAuthorRepository $authorRepo
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

        \App\Infrastructure\Debug\Debug::dtc($authors);
    }
    public function show( int $id): void{
        $book = $this->bookRepo->findById($id);

        if ($book === null) {
            http_response_code(404);
            echo '<h1>Книга не найдена</h1>';
            echo '<a href="/books">К списку книг</a>';
            return;
        }

        $allAuthors = [];

        if(!empty($book)) {
            $authorIds = $book->authorIds() ?? [];

            $authors = $this->authorRepo->findByIds($authorIds);

            $authorsById = [];

            foreach($authors as $author){
                $authorsById[$author->id()] = $author;
            }
        }

        $title = $book->title();

        $data = [
            'book' => $book,
            'title' => $title,
            'authorsById' => $authorsById
        ];
        $content = render('book/show', $data);

        require BASE_PATH . '/src/Presentation/View/layout.php';

    }

}