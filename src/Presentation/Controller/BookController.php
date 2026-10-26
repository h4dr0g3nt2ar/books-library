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

}