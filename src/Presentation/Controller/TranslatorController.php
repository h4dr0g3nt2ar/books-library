<?php

declare(strict_types=1);

namespace App\Presentation\Controller;
use App\Domain\Translator\Translator;
use App\Infrastructure\Database\PdoBookRepository;
use App\Infrastructure\Database\PdoAuthorRepository;
use App\Infrastructure\Database\PdoTranslatorRepository;
final class TranslatorController
{
    public function __construct(
        private readonly PdoTranslatorRepository $translatorRepo,
        private readonly PdoBookRepository $bookRepo,
        private readonly PdoAuthorRepository $authorRepo
    ){ }
    public function index(): void{
        $translators = $this->translatorRepo->all();
        $total = $this->translatorRepo->count();

        $title = 'Список переводчиков';
        $data = [
            'translators' => $translators,
            'title' => $title,
            'total' => $total,
        ];

        $content = render('translator/index', $data);

        require BASE_PATH . '/src/Presentation/View/layout.php';
    }
    public function show(int $id): void{
        $translator = $this->translatorRepo->findById($id);
        if ($translator === null) {
            http_response_code(404);
            echo '<h1>Переводчик не найден</h1>';
            echo '<a href="/translators">К списку переводчиков</a>';
            return;
        }
        $books = $this->bookRepo->findByTranslatorId($id);

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
        $content = render('translator/show', [
                'translator' => $translator,
                'books' => $books,
                'authorsById' => $authorsById,
                'title' => $translator->name(),
            ]
        );
        require BASE_PATH . '/src/Presentation/View/layout.php';

    }
    public function create(): void{
        $title = 'Добавить переводчика';
        $data = [
            'translator' => null,
            'title' => $title,
            'errors' => []
        ];

        $content = render('translator/form', $data);

        require BASE_PATH . '/src/Presentation/View/layout.php';
    }
    public function store(): void{

        $title = $_POST['name'] ?? '';

        $errors = [];

        try {
            $translator = Translator::create(
                name: $title,
                description: $_POST['description'] ?? ''
            );
        }catch (\InvalidArgumentException $e){
            $errors[] = $e->getMessage();
        }
        if ($errors !== []) {
            $title = 'Добавить переводчика';

            $data = [
                'translator' => null,
                'title' => $title,
                'errors' => $errors,
                'old' => $_POST,
            ];
            $content = render('translator/form', $data);

            require BASE_PATH . '/src/Presentation/View/layout.php';

            return;
        }
        $this->translatorRepo->save($translator);
        header('Location: /translators');

        exit;
    }
    public function edit(int $id): void{
        $translator = $this->translatorRepo->findById($id);
        if ($translator === null) {
            http_response_code(404);
            echo '<h1>Переводчик не найден</h1>';
            echo '<a href="/translators">К списку переводчиков</a>';
            return;
        }
        $title = 'Редактировать переводчика';

        $data = [
            'translator' => $translator,
            'title' => $title,
            'errors' => []
        ];

        $content = render('translator/form', $data);

        require BASE_PATH . '/src/Presentation/View/layout.php';
    }
    public function update(int $id): void{
        $translator = $this->translatorRepo->findById($id);
        if ($translator === null) {
            http_response_code(404);
            echo '<h1>Переводчик не найден</h1>';
            echo '<a href="/translators">К списку переводчиков</a>';
            return;
        }
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $errors = [];

        try{
            $translator->rename($name);
            $translator->changeDescription($description);
        }catch(\InvalidArgumentException $e){
            $errors[] = $e->getMessage();
        }
        if ($errors !== []) {
            $title = 'Редактировать переводчика';

            $data = [
                'translator' => $translator,
                'title' => $title,
                'errors' => $errors
            ];

            $content = render('translator/form', $data);

            require BASE_PATH . '/src/Presentation/View/layout.php';

            return;
        }else{
            $this->translatorRepo->save($translator);
            header('Location: /translators'); exit;
        }
    }
    public function delete(int $id): void{
        $translator = $this->translatorRepo->findById($id);
        if ($translator === null) {
            http_response_code(404);
            echo '<h1>Переводчик не найден</h1>';
            echo '<a href="/translators">К списку переводчиков</a>';
            return;
        }
        $this->translatorRepo->delete($translator);
        header('Location: /translators');
        exit;
    }
}