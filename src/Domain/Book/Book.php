<?php

declare(strict_types=1);

namespace App\Domain\Book;

final class Book
{


    private function __construct(
        private int $id,
        private string $title,
        private ?int $year,
        private ?string $description,
        private array $authors,

        private \DateTimeImmutable $createdAt,
        private \DateTimeImmutable $updatedAt
    ){

        self::validateTitle($title);
        self::validateYear($year);
        self::validateDescription($description);

    }
    public static function create(string $title, int $year, string $description, array $authors): self{

        $now = new \DateTimeImmutable();

        $book = new Book();

        return new self(
            0, $title, $year, $description, $authors, $now, $now
        );
    }
    public static function reconstitute(){

    }
    private function validateTitle(string $title){

    }
    private function validateYear(int $year){

    }
    private function validateDescription(string $description){

    }

}