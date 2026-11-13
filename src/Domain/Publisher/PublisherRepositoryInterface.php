<?php
declare(strict_types=1);

namespace App\Domain\Publisher;

interface PublisherRepositoryInterface
{
    public function save(Publisher $publisher): void;
    public function findById(int $id): ?Publisher;

    /**
     * @return Publisher[]
     */
    public function all(int $limit = 100, int $offset = 0): array;

    public function delete(Publisher $publisher): void;
    public function count(): int;
}