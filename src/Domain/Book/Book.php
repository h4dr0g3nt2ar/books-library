<?php

declare(strict_types=1);

namespace App\Domain\Book;

use \InvalidArgumentException;

final class Book
{
    private function __construct(
        private int $id,
        private string $title,
        private int $year,
        private ?string $description,
        private array $authorIds,

        private \DateTimeImmutable $createdAt,
        private \DateTimeImmutable $updatedAt
    ){
        self::validateTitle($title);
        self::validateYear($year);
        self::validateDescription($description);
        self::validateAuthorIds($authorIds);

    }
    public static function create(
        string $title,
        int $year,
        ?string $description = null,
        array $authorIds = []
    ): self{

        $now = new \DateTimeImmutable();

        return new self(
            id: 0,
            title: $title,
            year: $year,
            description: $description,
            authorIds: $authorIds,
            createdAt: $now,
            updatedAt: $now
        );
    }
    public static function reconstitute(
        int $id,
        string $title,
        int $year,
        ?string $description,
        array $authorIds,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt
    ): self{
        return new self(
            id: $id,
            title: trim($title),
            year: $year,
            description: $description,
            authorIds:$authorIds,
            createdAt: $createdAt,
            updatedAt: $updatedAt
        );
    }
    private function validateTitle(string $title): void{
        if(trim($title) === ''){
            throw new \InvalidArgumentException(
                'Title cannot be empty'
            );
        }
        if(mb_strlen($title, 'UTF-8') > 200){
            throw new InvalidArgumentException(
                'Title too long (max 200 chars)'
            );
        }
    }
    private function validateYear(int $year): void{
        if($year < 1000){
            throw new InvalidArgumentException(
                'Year must be 1000 or later'
            );
        }
        $currentYear = (int)date('Y', time());
        if($year > $currentYear){
            throw new InvalidArgumentException(
                'Year cannot be more than '. $currentYear
            );
        }
    }
    private function validateDescription(?string $description): void{
        if(is_string($description) && mb_strlen($description, 'UTF-8') > 10000){
            throw new InvalidArgumentException(
                'Description too long'
            );
        }
    }
    private function validateAuthorIds(array $authorIds): void{
        foreach($authorIds as $authorKey => $authorId){
            if(!is_int($authorId)){
                throw new InvalidArgumentException(
                    'Each author ID must be an integer'
                );
            }
            if($authorId <= 0){
                throw new InvalidArgumentException(
                    'Author id must be positive. Yo tried to fill: ' . $authorId;
                );
            }
        }
        if(count($authorIds) !== array_unique($authorIds)){
            throw new InvalidArgumentException(
                'Duplicate author Ids'
            );
        }
    }
    public function id(): int{
        return $this->id;
    }
    public function title(): string{
        return $this->title;
    }
    public function year(): int{
        return $this->year;
    }
    public function description(): ?string{
        return $this->description;
    }
    public function authorIds(): array{
        return $this->authorIds;
    }
    public function createdAt(): \DateTimeImmutable{
        return $this->createdAt;
    }
    public function updatedAt(): \DateTimeImmutable{
        return $this->updatedAt;
    }
    public function isNew(): bool{
        return $this->id === 0;
    }

    public function rename(string $newTitle): void{
        $this->validateTitle($newTitle);
        $this->title = $newTitle;
    }
    public function changeYear(int $newYear): void{
        $this->validateYear($newYear);
        $this->year = $newYear;
    }
    public function changeDescription(?string $newDescription): void{
        $this->validateDescription($newDescription);
        $this->description = $newDescription;
    }
    public function addAuthor(int $authorId): void{
        $this->validateauthorIds([$authorId]);
        $this->authorIds[] = $authorId;
    }
    public function removeAuthor(int $authorId): void{
        foreach($this->authorIds as &$authorIdValue){
            if($authorIdValue == $authorId){
                unset($authorIdValue);
            }
        }
        $this->authorIds = \array_values($this->authorIds);
    }
    public function hasAuthor(){

    }

}