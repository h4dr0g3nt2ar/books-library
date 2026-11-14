<?php
declare(strict_types=1);

namespace App\Domain\Translator;

interface TranslatorRepositoryInterface
{
    public function save(Translator $translator): void;
    public function findById(int $id): ?Translator;

    /**
     * @return Translator[]
     */
    public function all(int $limit = 100, int $offset = 0): array;

    public function delete(Translator $translator): void;
    public function count(): int;
}