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
use App\Infrastructure\Database\PdoTranslatorRepository;
use App\Infrastructure\Database\PdoTagRepository;
use App\Presentation\Router;


final class BookController
{

    public function __construct(
        public readonly PdoBookRepository $bookRepo,
        public readonly PdoAuthorRepository $authorRepo,
        public readonly PdoCountryRepository $countryRepo,
        public readonly PdoPublisherRepository $publisherRepo,
        public readonly PdoSeriesRepository $seriesRepo,
        public readonly PdoTranslatorRepository $translatorRepo,
        public readonly PdoTagRepository $tagRepo,
    ){
    }
    public function index():void {

        $filters = [
            'title'        => trim($_GET['title'] ?? ''),
            'author_id'    => (int) ($_GET['author_id'] ?? 0),
            'country_id'   => (int) ($_GET['country_id'] ?? 0),
            'publisher_id' => (int) ($_GET['publisher_id'] ?? 0),
            'series_id'    => (int) ($_GET['series_id'] ?? 0),
            'tag_id'       => (int) ($_GET['tag_id'] ?? 0),
            'year_from'    => ($_GET['year_from'] ?? '') !== '' ? (int) $_GET['year_from'] : null,
            'year_to'      => ($_GET['year_to'] ?? '') !== '' ? (int) $_GET['year_to'] : null,
            'binding'      => $_GET['binding'] ?? '',
        ];



        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $books = $this->bookRepo->search($filters, $perPage, $offset);

        $total = $this->bookRepo->countWithFilters($filters);
        $totalPages = (int) ceil($total / $perPage);

        if ($page > $totalPages && $totalPages > 0) {
            header('Location: /books?page=' . $totalPages);
        }

        $total = $this->bookRepo->countWithFilters($filters);

        $allAuthorIds = [];

        foreach($books as $book){
            $allAuthorIds = array_merge($allAuthorIds, $book->authorIds());
        }

        $allAuthorIds = array_unique($allAuthorIds);
        $authors = $allAuthorIds === [] ? [] : $this->authorRepo->findByIds($allAuthorIds);
        $authorsById = [];

        foreach($authors as $author){
            $authorsById[$author->id()] = $author;
        }
        $allAuthors    = $this->authorRepo->all();
        $allCountries  = $this->countryRepo->all();
        $allPublishers = $this->publisherRepo->all();
        $allSeries     = $this->seriesRepo->all();
        $allTags       = $this->tagRepo->all();

        $activeFilters = $this->buildActiveFilters($filters, [
            'authors'    => $allAuthors,
            'countries'  => $allCountries,
            'publishers' => $allPublishers,
            'series'     => $allSeries,
            'tags'       => $allTags,
        ]);
        //для подсветки полей в форме
        $activeKeys = array_column($activeFilters, 'key');

        $isActive = function ($key) use ($activeKeys): bool {
            return in_array($key, $activeKeys);
        };

        $title = 'Список книг';
        \App\Infrastructure\Debug\Debug::dtc($activeKeys, 'activeKeys');
        $data = [
            'books' => $books,
            'title' => $title,
            'total' => $total,
            'authorsById' => $authorsById,
            'filters' => $filters,
            'activeFilters' => $activeFilters,
            'activeKeys' => $activeKeys,
            'isActive' => $isActive,
            'allAuthors' => $allAuthors,
            'allCountries' => $allCountries,
            'allPublishers' => $allPublishers,
            'allSeries' => $allSeries,
            'allTags' => $allTags,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'perPage' => $perPage,
            'buildPageUrl' => function(int $p) use ($filters): string{
                return $this->buildPageUrl($p, $filters);
            }
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

        $translators = $this->translatorRepo->findByIds($book->translatorIds());
        $translatorsById = [];

        foreach($translators as $translator){
            $translatorsById[$translator->id()] = $translator;
        }

        $tags = $this->tagRepo->findByIds($book->tagIds());
        $tagsById = [];

        foreach($tags as $tag){
            $tagsById[$tag->id()] = $tag;
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
            'authorsById' => $authorsById,
            'translatorsById' => $translatorsById,
            'tagsById' => $tagsById,
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
            'translators' => $this->translatorRepo->all(),
            'tags' => $this->tagRepo->all(),
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

        $translatorIds = array_map('intval', $_POST['translatorIds'] ?? []);
        $tagIds = array_map('intval', $_POST['tagIds'] ?? []);



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

                authorIds: $authorIds,

                translatorIds: $translatorIds,
                tagIds: $tagIds
            );
        }catch (\InvalidArgumentException $e){
            $errors[] = $e->getMessage();
        }
        if ($errors !== []) {

            $authors = $this->authorRepo->all();
            $translators = $this->translatorRepo->all();
            $tags = $this->tagRepo->all();

            $title = 'Добавить книгу';

            $data = [
                'book' => null,
                'title' => $title,
                'authors' => $authors,
                'translators' => $translators,
                'tags' => $tags,
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
            'translators' => $this->translatorRepo->all(),
            'tags' => $this->tagRepo->all(),
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
        $translatorIds = array_map('intval', $_POST['translatorIds'] ?? []);
        $tagIds = array_map('intval', $_POST['tagIds'] ?? []);


        $description = trim($_POST['description'] ?? '') ?: null;

        $bindingStr = $_POST['binding'] ?? '';
        $binding = $bindingStr !== '' ? BindingType::from($bindingStr) : null;

        $pagesStr = $_POST['pages'] ?? '';
        $pages = $pagesStr !== '' ? (int) $pagesStr : null;

        $printRunStr = $_POST['print_run'] ?? '';
        $printRun = $printRunStr !== '' ? (int) $printRunStr : null;

        $isbn = trim($_POST['isbn'] ?? '') ?: null;

        $countryId   = ($_POST['country_id'] ?? '') !== '' ? (int) $_POST['country_id'] : null;
        $publisherId = ($_POST['publisher_id'] ?? '') !== '' ? (int) $_POST['publisher_id'] : null;
        $seriesId    = ($_POST['series_id'] ?? '') !== '' ? (int) $_POST['series_id'] : null;

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

            $translatorIdsByBook = $book->translatorIds();

            foreach($translatorIds as $translatorId){
                if(!in_array($translatorId, $translatorIdsByBook)){
                    $book->addTranslator($translatorId);
                }
            }

            foreach ($translatorIdsByBook as $translatorIdByBook) {
                if(!in_array($translatorIdByBook, $translatorIds)){
                    $book->removeTranslator($translatorIdByBook);
                }
            }

            $tagIdsByBook = $book->tagIds();

            foreach($tagIds as $tagId){
                if(!in_array($tagId, $tagIdsByBook)){
                    $book->addTag($tagId);
                }
            }

            foreach ($tagIdsByBook as $tagIdByBook) {
                if(!in_array($tagIdByBook, $tagIds)){
                    $book->removeTag($tagIdByBook);
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
                'translators' => $this->translatorRepo->all(),
                'tags' => $this->tagRepo->all(),
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

    private function buildPageUrl($page, $filters): string{
        $query = [];
        foreach($filters as $key => $value){
            if($value !== null && $value !== '' && $value !== '0'){
                $query[$key] = $value;
            }
        }
        $query['page'] = $page;

        return '/books?' . http_build_query($query);
    }

    private function buildActiveFilters(array $filters, array $refs): array{
        $active = [];

        if($filters['title'] !== ''){
            $active[] = ['key' => 'title', 'label' => 'Название: ' . $filters['title']];
        }
        //справочники
        $dictFilters = [
            ['key' => 'author_id', 'ref' => 'authors', 'prefix' => 'Автор'],
            ['key' => 'country_id', 'ref' => 'countries', 'prefix' => 'Страна'],
            ['key' => 'publisher_id', 'ref' => 'publishers', 'prefix' => 'Издательство'],
            ['key' => 'series_id', 'ref' => 'series', 'prefix' => 'Серия'],
            ['key' => 'tag_id', 'ref' => 'tags', 'prefix' => 'Тег']
        ];
        foreach($dictFilters as $def){
            $id = $filters[$def['key']];
            if($id <= 0){
                continue;
            }
            $name = '?';
            foreach($refs[$def['ref']] as $item){
                if($item->id() === $id){
                    $name = $item->name();
                    break;
                }
            }
            $active[] = ['key' => $def['key'], 'label' => $def['prefix'] . ': ' . $name];
        }
        if($filters['year_from'] !== null || $filters['year_to'] !== null){
            $active[] = ['key' => 'year', 'label' => 'Год: ' . $filters['year_from'] . ' - ' . $filters['year_to']];
        }
        if($filters['binding'] !== ''){
            $active[] = ['key' => 'binding', 'label' => 'Переплет: ' . $filters['binding']];
        }
        return $active;
    }
}