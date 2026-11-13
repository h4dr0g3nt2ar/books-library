<?php
declare(strict_types=1);

namespace App\Domain\Country;

interface CountryRepositoryInterface
{
    public function save(Country $country): void;
    public function findById(int $id): ?Country;

    /**
     * @return Country[]
     */
    public function all(int $limit = 100, int $offset = 0): array;

    public function delete(Country $country): void;
    public function count(): int;
}