<?php
declare(strict_types = 1);

namespace App\Presentation\Controller;
use App\Domain\Author\Author;
use App\Infrastructure\Database\PdoBookRepository;
use App\Infrastructure\Database\PdoAuthorRepository;

class AuthorController
{
    public function __construct(
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
            echo '<h1>Автор не найден</h1>';
            echo '<a href="/books">К списку авторов</a>';
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
    public function create(): void{

        $title = 'Добавить автора';
        $data = [
            'author' => null,
            'title' => $title,
            'errors' => []
        ];

        $content = render('author/form', $data);

        require BASE_PATH . '/src/Presentation/View/layout.php';
    }
    public function store(): void{

        $name = $_POST['name'] ?? '';


        $description = $_POST['description'] ?? null;
        $birthYear = ($_POST['birth_year'] ?? '') !== '' ? (int) $_POST['birth_year'] : null;
        $deathYear = ($_POST['death_year'] ?? '') !== '' ? (int) $_POST['death_year'] : null;
        $errors = [];

        try {
            $author = Author::create(
                name: $name,
                birthYear: $birthYear,
                deathYear: $deathYear,
                description: $description,
            );
        }catch (\InvalidArgumentException $e){
            $errors[] = $e->getMessage();
        }
        if ($errors !== []) {

            $title = 'Добавить автора';

            $data = [
                'author' => null,
                'title' => $title,
                'errors' => $errors,
                'old' => $_POST,
            ];
            $content = render('author/form', $data);

            require BASE_PATH . '/src/Presentation/View/layout.php';

            return;
        }
        $this->authorRepo->save($author);
        header('Location: /authors');

        exit;
    }
    public function edit(int $id): void{
        $author = $this->authorRepo->findById($id);

        if ($author === null) {
            http_response_code(404);
            echo '<h1>Автор не найден</h1>';
            echo '<a href="/authors">К списку авторов</a>';
            return;
        }
        $title = 'Редактировать автора';

        $data = [
            'author' => $author,
            'title' => $title,
            'errors' => []
        ];

        $content = render('author/form', $data);

        require BASE_PATH . '/src/Presentation/View/layout.php';

    }
    public function update(int $id): void{
        $author = $this->authorRepo->findById($id);

        if ($author === null) {
            http_response_code(404);
            echo '<h1>Автор не найден</h1>';
            echo '<a href="/authors">К списку автор</a>';
            return;
        }

        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $birthYear = ($_POST['birth_year'] ?? '') !== '' ? (int) $_POST['birth_year'] : null;
        $deathYear = ($_POST['death_year'] ?? '') !== '' ? (int) $_POST['death_year'] : null;
        $errors = [];

        try {
            $author->rename($name);
            $author->changeDescription($description);
            $author->changeLifeYears($birthYear, $deathYear);

        }catch (\InvalidArgumentException $e){
            $errors[] = $e->getMessage();
        }
        if ($errors !== []) {

            $title = 'Редактировать автора';

            $data = [
                'author' => $author,
                'title' => $title,
                'errors' => $errors,
                'old' => $_POST,
            ];
            $content = render('author/form', $data);

            require BASE_PATH . '/src/Presentation/View/layout.php';

            return;
        }
        $this->authorRepo->save($author);
        header('Location: /authors/' . $author->id());

        exit;
    }
    public function delete(int $id): void{
        $author = $this->authorRepo->findById($id);

        if ($author === null) {
            http_response_code(404);
            echo '<h1>Автор не найден</h1>';
            echo '<a href="/authors">К списку авторов</a>';
            return;
        }
        $this->authorRepo->delete($author);

        header('Location: /authors');

        exit;
    }
}