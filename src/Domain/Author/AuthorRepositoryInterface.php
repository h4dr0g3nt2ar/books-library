<?php

declare(strict_types=1);

namespace App\Domain\Author;

interface AuthorRepositoryInterface
{
    public function save(Author $author): void;
    public function findById(int $id): ?Author;
    public function findByIds(array $ids): array;
    public function findByName(string $name): ?Author;
    /**
     * @return Author[]
     */
    public function all(int $limit = 100, int $offset = 0): array;
    public function delete(Author $author): void;
    public function count(): int;
}