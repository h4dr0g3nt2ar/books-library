<?php

declare(strict_types=1);

namespace App\Presentation\Controller;
use App\Domain\Tag\Tag;
use App\Infrastructure\Database\PdoBookRepository;
use App\Infrastructure\Database\PdoAuthorRepository;
use App\Infrastructure\Database\PdoTagRepository;
final class TagController
{
    public function __construct(
        private readonly PdoTagRepository $tagRepo,
        private readonly PdoBookRepository $bookRepo,
        private readonly PdoAuthorRepository $authorRepo
    ){ }
    public function index(): void{
        $tags = $this->tagRepo->all();
        $total = $this->tagRepo->count();

        $title = 'Список тегов';
        $data = [
            'tags' => $tags,
            'title' => $title,
            'total' => $total,
        ];

        $content = render('tag/index', $data);

        require BASE_PATH . '/src/Presentation/View/layout.php';
    }
    public function show(int $id): void{
        $tag = $this->tagRepo->findById($id);
        if ($tag === null) {
            http_response_code(404);
            echo '<h1>Тег не найден</h1>';
            echo '<a href="/tags">К списку тегов</a>';
            return;
        }
        $books = $this->bookRepo->findByTagId($id);

        $allAuthorIds = [];
        foreach ($books as $book) {
            $allAuthorIds[] = $book->authorIds();
        }
        $allAuthorIds = array_unique($allAuthorIds);
        $authors = $allAuthorIds === [] ? [] : $this->authorRepo->findByIds($allAuthorIds);
        $authorsById = [];
        foreach ($authors as $author) {
            $authorsById[$author->id()] = $author;
        }
        $content = render('tag/show', [
                'tag' => $tag,
                'books' => $books,
                'authorsById' => $authorsById,
                'title' => $tag->name(),
            ]
        );
        require BASE_PATH . '/src/Presentation/View/layout.php';

    }
    public function create(): void{
        $title = 'Добавить тег';
        $data = [
            'tag' => null,
            'title' => $title,
            'errors' => []
        ];

        $content = render('tag/form', $data);

        require BASE_PATH . '/src/Presentation/View/layout.php';
    }
    public function store(): void{

        $title = $_POST['name'] ?? '';

        $errors = [];

        try {
            $tag = Tag::create(
                name: $title,
                description: $_POST['description'] ?? ''
            );
        }catch (\InvalidArgumentException $e){
            $errors[] = $e->getMessage();
        }
        if ($errors !== []) {
            $title = 'Добавить тег';

            $data = [
                'tag' => null,
                'title' => $title,
                'errors' => $errors,
                'old' => $_POST,
            ];
            $content = render('tag/form', $data);

            require BASE_PATH . '/src/Presentation/View/layout.php';

            return;
        }
        $this->tagRepo->save($tag);
        header('Location: /tags');

        exit;
    }
    public function edit(int $id): void{
        $tag = $this->tagRepo->findById($id);
        if ($tag === null) {
            http_response_code(404);
            echo '<h1>Тег не найден</h1>';
            echo '<a href="/tags">К списку тег</a>';
            return;
        }
        $title = 'Редактировать тег';

        $data = [
            'tag' => $tag,
            'title' => $title,
            'errors' => []
        ];

        $content = render('tag/form', $data);

        require BASE_PATH . '/src/Presentation/View/layout.php';
    }
    public function update(int $id): void{
        $tag = $this->tagRepo->findById($id);
        if ($tag === null) {
            http_response_code(404);
            echo '<h1>Тег не найден</h1>';
            echo '<a href="/tags">К списку тегов</a>';
            return;
        }
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $errors = [];

        try{
            $tag->rename($name);
            $tag->changeDescription($description);
        }catch(\InvalidArgumentException $e){
            $errors[] = $e->getMessage();
        }
        if ($errors !== []) {
            $title = 'Редактировать тег';

            $data = [
                'tag' => $tag,
                'title' => $title,
                'errors' => $errors
            ];

            $content = render('tag/form', $data);

            require BASE_PATH . '/src/Presentation/View/layout.php';

            return;
        }else{
            $this->tagRepo->save($tag);
            header('Location: /tags'); exit;
        }
    }
    public function delete(int $id): void{
        $tag = $this->tagRepo->findById($id);
        if ($tag === null) {
            http_response_code(404);
            echo '<h1>Тег не найден</h1>';
            echo '<a href="/tags">К списку тегов</a>';
            return;
        }
        $this->tagRepo->delete($tag);
        header('Location: /tags');
        exit;
    }
}