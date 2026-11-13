<?php

declare(strict_types=1);

namespace App\Presentation\Controller;
use App\Domain\Series\Series;
use App\Infrastructure\Database\PdoAuthorRepository;
use App\Infrastructure\Database\PdoBookRepository;
use App\Infrastructure\Database\PdoSeriesRepository;
final class SeriesController
{
    public function __construct(
        private readonly PdoSeriesRepository $seriesRepo,
        private readonly PdoBookRepository $bookRepo,
        private readonly PdoAuthorRepository $authorRepo
    ){ }
    public function index(): void{
        $series = $this->seriesRepo->all();
        $total = $this->seriesRepo->count();

        $title = 'Список серий';
        $data = [
            'series' => $series,
            'title' => $title,
            'total' => $total,
        ];

        $content = render('series/index', $data);

        require BASE_PATH . '/src/Presentation/View/layout.php';
    }
    public function create(): void{
        $title = 'Добавить серию';
        $data = [
            'series' => null,
            'title' => $title,
            'errors' => []
        ];

        $content = render('series/form', $data);

        require BASE_PATH . '/src/Presentation/View/layout.php';
    }
    public function store(): void{

        $title = $_POST['name'] ?? '';

        $errors = [];

        try {
            $series = Series::create(
                name: $title,
                description: $_POST['description'] ?? '',
            );
        }catch (\InvalidArgumentException $e){
            $errors[] = $e->getMessage();
        }
        if ($errors !== []) {
            $title = 'Добавить серию';

            $data = [
                'series' => null,
                'title' => $title,
                'errors' => $errors,
                'old' => $_POST,
            ];
            $content = render('series/form', $data);

            require BASE_PATH . '/src/Presentation/View/layout.php';

            return;
        }
        $this->seriesRepo->save($series);
        header('Location: /series');

        exit;
    }
    public function edit(int $id): void{
        $series = $this->seriesRepo->findById($id);
        if ($series === null) {
            http_response_code(404);
            echo '<h1>Серия не найдена</h1>';
            echo '<a href="/series">К списку серий</a>';
            return;
        }
        $title = 'Редактировать серию';

        $data = [
            'series' => $series,
            'title' => $title,
            'errors' => []
        ];

        $content = render('series/form', $data);

        require BASE_PATH . '/src/Presentation/View/layout.php';
    }
    public function update(int $id): void{
        $series = $this->seriesRepo->findById($id);
        if ($series === null) {
            http_response_code(404);
            echo '<h1>Издательство не найдена</h1>';
            echo '<a href="/series">К списку серий</a>';
            return;
        }
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $errors = [];

        try{
            $series->rename($name);
            $series->changeDescription($description);
        }catch(\InvalidArgumentException $e){
            $errors[] = $e->getMessage();
        }
        if ($errors !== []) {
            $title = 'Редактировать серию';

            $data = [
                'series' => $series,
                'title' => $title,
                'errors' => $errors
            ];

            $content = render('series/form', $data);

            require BASE_PATH . '/src/Presentation/View/layout.php';

            return;
        }else{
            $this->seriesRepo->save($series);
            header('Location: /series'); exit;
        }
    }
    public function delete(int $id): void{
        $series = $this->seriesRepo->findById($id);
        if ($series === null) {
            http_response_code(404);
            echo '<h1>Серия не найдена</h1>';
            echo '<a href="/series">К списку серий</a>';
            return;
        }
        $this->seriesRepo->delete($series);
        header('Location: /series');
        exit;
    }
    public function show(int $id): void{
        $series = $this->seriesRepo->findById($id);
        if ($series === null) {
            http_response_code(404);
            echo '<h1>Издательство не найдено</h1>';
            echo '<a href="/series">К списку издательств</a>';
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
        $content = render('series/show', [
                'series' => $series,
                'books' => $books,
                'authorsById' => $authorsById,
                'title' => $series->name(),
            ]
        );
        require BASE_PATH . '/src/Presentation/View/layout.php';

    }
}