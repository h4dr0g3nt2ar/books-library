<?php

declare(strict_types=1);

namespace App\Domain\Book;

interface BookRepositoryInterface
{
    /**
     * Сохраняем книгу, если isNew() то INSERT, иначе UPDATE
     */
    public function save(Book $book): void;
    public function findById(int $id): ?Book;
    /**
     * @return Book[]
     */
    public function all(int $limit = 100, int $offset = 0): array;
    public function delete(Book $book): void;
    public function count(): int;
    /** @return Book[] */
    public function findByAuthorId(int $authorId): array;
    /** @return Book[] */
    public function findByPublisherId(int $publisherId): array;

    /** @return Book[] */
    public function findBySeriesId(int $seriesId): array;
}