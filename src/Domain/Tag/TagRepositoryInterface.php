<?php
declare(strict_types=1);

namespace App\Domain\Tag;

interface TagRepositoryInterface
{
    public function save(Tag $tag): void;
    public function findById(int $id): ?Tag;

    /**
     * @return Tag[]
     */
    public function all(int $limit = 100, int $offset = 0): array;

    public function delete(Tag $tag): void;
    public function count(): int;
}