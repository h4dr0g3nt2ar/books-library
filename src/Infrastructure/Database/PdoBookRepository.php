<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use App\Domain\Book\Book;
use App\Domain\Book\BookRepositoryInterface;
use App\Domain\Book\Enum\BindingType;

final class PdoBookRepository implements BookRepositoryInterface
{
    public function __construct(private readonly \PDO $pdo,){ }
    public function save(Book $book): void
    {
        $this->pdo->beginTransaction();

        try{
            if($book->isNew()){
                $this->insertBook($book);
            }else{
                $this->updateBook($book);
            }
            $this->syncAuthors($book);

            $this->pdo->commit();
        }catch(\Throwable $e){
            $this->pdo->rollBack();
            throw $e;
        }
    }
    public function findById(int $id): ?Book
    {
        $stmt = $this->pdo->prepare("SELECT * FROM books WHERE id = :id");
        $stmt->execute(['id'=>$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if($row === false){
            return null;
        }

        $stmt = $this->pdo->prepare("SELECT author_id FROM book_author WHERE book_id = :id");
        $stmt->execute(['id'=>$id]);
        $authorIds = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        $authorIds = \array_map('intval', $authorIds);

        $book =  Book::reconstitute(
            id: (int)$row['id'],
            title: $row['title'],
            year: (int)$row['year'],
            description: $row['description'],
            authorIds: $authorIds,
            binding: $row['binding'] !== null ? BindingType::from($row['binding']) : null,
            pages: $row['pages'] !== null ? (int) $row['pages'] : null,
            printRun: $row['print_run'] !== null ? (int) $row['print_run'] : null,
            isbn: $row['isbn'],
            countryId: $row['country_id'] !== null ? (int) $row['country_id']: null,
            publisherId: $row['publisher_id'] !== null ? (int) $row['publisher_id']: null,
            seriesId: $row['series_id'] !== null ? (int) $row['series_id']: null,
            createdAt: new \DateTimeImmutable($row['created_at']),
            updatedAt: new \DateTimeImmutable($row['updated_at'])
        );
        return $book;
    }
    public function findByAuthorId(int $authorId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT b.* FROM books b
         JOIN book_author ba ON ba.book_id = b.id
         WHERE ba.author_id = :author_id
         ORDER BY b.year ASC'
        );
        $stmt->execute(['author_id' => $authorId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if ($rows === []) {
            return [];
        }

        // Подгружаем ВСЕХ авторов для этих книг (как в all())
        $bookIds = [];
        foreach ($rows as $row) {
            $bookIds[] = (int) $row['id'];
        }
        $placeholders = \implode(',', \array_fill(0, \count($bookIds), '?'));
        $stmt = $this->pdo->prepare(
            "SELECT book_id, author_id FROM book_author WHERE book_id IN ($placeholders)"
        );
        $stmt->execute($bookIds);
        $relations = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $authorIdsByBook = [];
        foreach ($relations as $relation) {
            $authorIdsByBook[(int) $relation['book_id']][] = (int) $relation['author_id'];
        }

        $books = [];
        foreach ($rows as $row) {
            $bookId = (int) $row['id'];
            $books[] = Book::reconstitute(
                id: $bookId,
                title: $row['title'],
                year: (int) $row['year'],
                description: $row['description'],
                authorIds: $authorIdsByBook[$bookId] ?? [],
                binding: $row['binding'] !== null ? BindingType::from($row['binding']) : null,
                pages: $row['pages'] !== null ? (int) $row['pages'] : null,
                printRun: $row['print_run'] !== null ? (int) $row['print_run'] : null,
                isbn: $row['isbn'],
                countryId: $row['country_id'] !== null ? (int) $row['country_id']: null,
                publisherId: $row['publisher_id'] !== null ? (int) $row['publisher_id']: null,
                seriesId: $row['series_id'] !== null ? (int) $row['series_id']: null,
                createdAt: new \DateTimeImmutable($row['created_at']),
                updatedAt: new \DateTimeImmutable($row['updated_at']),
            );
        }

        return $books;
    }
    public function findByPublisherId(int $publisherId): array{
        $stmt = $this->pdo->prepare("SELECT * FROM books WHERE publisher_id = :publisher_id");
        $stmt->execute(['publisher_id' => $publisher_id]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if ($rows === []) {
            return [];
        }
        // Подгружаем ВСЕХ авторов для этих книг (как в all())
        $bookIds = [];
        foreach ($rows as $row) {
            $bookIds[] = (int) $row['id'];
        }
        $placeholders = \implode(',', \array_fill(0, \count($bookIds), '?'));
        $stmt = $this->pdo->prepare(
            "SELECT book_id, author_id FROM book_author WHERE book_id IN ($placeholders)"
        );
        $stmt->execute($bookIds);
        $relations = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $authorIdsByBook = [];
        foreach ($relations as $relation) {
            $authorIdsByBook[(int) $relation['book_id']][] = (int) $relation['author_id'];
        }

        $books = [];
        foreach ($rows as $row) {
            $books[] = Book::reconstitute(
                id: $row['book_id'],
                title: $row['title'],
                year: (int) $row['year'],
                description: $row['description'],
                authorIds: $row['author_ids'],
                binding: $row['binding'] !== null ? BindingType::from($row['binding']) : null,
                pages: $row['pages'] !== null ? (int) $row['pages'] : null,
                printRun: $row['print_run'] !== null ? (int) $row['print_run'] : null,
                isbn: $row['isbn'],
                countryId: $row['country_id'] !== null ? (int) $row['country_id']: null,
                publisherId: $row['publisher_id'] !== null ? (int) $row['publisher_id']: null,
                seriesId: $row['series_id'] !== null ? (int) $row['series_id']: null,
                createdAt: new \DateTimeImmutable($row['created_at']),
                updatedAt: new \DateTimeImmutable($row['updated_at'])
            );
        }
        return $books;
    }
    public function findBySeriesId(int $seriesId): array{
        $stmt = $this->pdo->prepare("SELECT * FROM books WHERE series_id = :series_id");
        $stmt->execute(['series_id' => $seriesId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if ($rows === []) {
            return [];
        }
        // Подгружаем ВСЕХ авторов для этих книг (как в all())
        $bookIds = [];
        foreach ($rows as $row) {
            $bookIds[] = (int) $row['id'];
        }
        $placeholders = \implode(',', \array_fill(0, \count($bookIds), '?'));
        $stmt = $this->pdo->prepare(
            "SELECT book_id, author_id FROM book_author WHERE book_id IN ($placeholders)"
        );
        $stmt->execute($bookIds);
        $relations = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $authorIdsByBook = [];
        foreach ($relations as $relation) {
            $authorIdsByBook[(int) $relation['book_id']][] = (int) $relation['author_id'];
        }

        $books = [];
        foreach ($rows as $row) {
            $books[] = Book::reconstitute(
                id: $row['book_id'],
                title: $row['title'],
                year: (int) $row['year'],
                description: $row['description'],
                authorIds: $row['author_ids'],
                binding: $row['binding'] !== null ? BindingType::from($row['binding']) : null,
                pages: $row['pages'] !== null ? (int) $row['pages'] : null,
                printRun: $row['print_run'] !== null ? (int) $row['print_run'] : null,
                isbn: $row['isbn'],
                countryId: $row['country_id'] !== null ? (int) $row['country_id']: null,
                publisherId: $row['publisher_id'] !== null ? (int) $row['publisher_id']: null,
                seriesId: $row['series_id'] !== null ? (int) $row['series_id']: null,
                createdAt: new \DateTimeImmutable($row['created_at']),
                updatedAt: new \DateTimeImmutable($row['updated_at'])
            );
        }
        return $books;
    }

    public function all(int $limit = 100, int $offset = 0): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM books ORDER BY id LIMIT :limit OFFSET :offset");
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if($rows === []){
            return [];
        }

        $bookIds = [];
        foreach ($rows as $row) {
            $bookIds[] = (int) $row['id'];
        }
        $placeholders = \implode(',', \array_fill(0, \count($bookIds), '?'));

        $stmt = $this->pdo->prepare("SELECT * FROM book_author WHERE book_id IN ($placeholders)");
        $stmt->execute($bookIds);
        $relations = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $authorIdsByBook = [];
        foreach($relations as $relation){
            $authorIdsByBook[(int)$relation['book_id']][] = (int)$relation['author_id'];
        }

        $books = [];

        foreach($rows as $row){

            $bookId = (int)$row['id'];

            $books[] = Book::reconstitute(
                id: $bookId,
                title: $row['title'],
                year: (int)$row['year'],
                description: $row['description'],
                authorIds: $authorIdsByBook[$bookId] ?? [],
                binding: $row['binding'] !== null ? BindingType::from($row['binding']) : null,
                pages: $row['pages'] !== null ? (int) $row['pages'] : null,
                printRun: $row['print_run'] !== null ? (int) $row['print_run'] : null,
                isbn: $row['isbn'],
                countryId: $row['country_id'] !== null ? (int) $row['country_id']: null,
                publisherId: $row['publisher_id'] !== null ? (int) $row['publisher_id']: null,
                seriesId: $row['series_id'] !== null ? (int) $row['series_id']: null,
                createdAt: new \DateTimeImmutable($row['created_at']),
                updatedAt: new \DateTimeImmutable($row['updated_at'])
            );
        }

        return $books;
    }
    public function delete(Book $book): void
    {
        if($book->isNew()){
            throw new \InvalidArgumentException('Cannot delete unsaved book');
        }
        $stmt = $this->pdo->prepare("DELETE FROM books WHERE id = :id");
        $stmt->execute(['id' => $book->id()]);

    }
    public function count(): int
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM books");
        return (int)$stmt->fetchColumn();
    }
    private function insertBook(Book $book): void {
        $stmt = $this->pdo->prepare(
            "INSERT INTO books (title, year, description, binding, pages, print_run, isbn, country_id, publisher_id, series_id, created_at, updated_at) 
            VALUES (:title, :year, :description, :binding, :pages, :print_run, :isbn, :country_id, :publisher_id, :series_id, :created_at, :updated_at)");
        $stmt->execute(
            [
                'title' => $book->title(),
                'year' => $book->year(),
                'description' => $book->description(),
                'binding' => $book->binding()?->value,
                'pages' => $book->pages(),
                'print_run' => $book->printRun(),
                'isbn' => $book->isbn(),
                'country_id' => $book->countryId(),
                'publisher_id' => $book->publisherId(),
                'series_id' => $book->seriesId(),
                'created_at' => $book->createdAt()->format('Y-m-d H:i:s'),
                'updated_at' => $book->updatedAt()->format('Y-m-d H:i:s')
            ]
        );
        $book->assignId((int) $this->pdo->lastInsertId());

    }
    private function updateBook(Book $book): void {
        $stmt = $this->pdo->prepare(
            "UPDATE books SET title = :title, year = :year, description = :description, binding = :binding, pages = :pages, print_run = :print_run, isbn = :isbn,
                 country_id = :country_id, publisher_id = :publisher_id, series_id = :series_id, updated_at = :updated_at
            WHERE id = :id
        ");
        $stmt->execute(
            [
                'id' => $book->id(),
                'title' => $book->title(),
                'year' => $book->year(),
                'description' => $book->description(),
                'binding' => $book->binding()?->value,
                'pages' => $book->pages(),
                'print_run' => $book->printRun(),
                'isbn' => $book->isbn(),
                'country_id' => $book->countryId(),
                'publisher_id' => $book->publisherId(),
                'series_id' => $book->seriesId(),
                'updated_at' => $book->updatedAt()->format('Y-m-d H:i:s')
            ]
        );
    }
    private function syncAuthors(Book $book): void {

        if ($book->authorIds() === []) {
            return;
        }

        $stmt = $this->pdo->prepare("DELETE FROM book_author WHERE book_id = :book_id");
        $stmt->execute(['book_id' => $book->id()]);

        $stmt= $this->pdo->prepare('INSERT INTO book_author (book_id, author_id) VALUES (:book_id, :author_id)');
        foreach($book->authorIds() as $authorId){
            $stmt->execute(['book_id' => $book->id(), 'author_id' => $authorId]);
        }
    }

}