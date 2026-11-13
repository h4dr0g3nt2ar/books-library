<?php
declare(strict_types=1);

namespace App\Domain\Series;

interface SeriesRepositoryInterface
{
    public function save(Series $series): void;
    public function findById(int $id): ?Series;

    /**
     * @return Series[]
     */
    public function all(int $limit = 100, int $offset = 0): array;

    public function delete(Series $series): void;
    public function count(): int;
}