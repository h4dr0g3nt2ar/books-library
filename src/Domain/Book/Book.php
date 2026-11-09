<?php

declare(strict_types=1);

namespace App\Domain\Book;

use App\Domain\Book\Enum\BindingType;

final class Book
{
    private function __construct(
        private int $id,
        private string $title,
        private int $year,
        private ?string $description,
        private array $authorIds,

        private ?BindingType $binding,      // переплет
        private ?int $pages,                // кол-во страниц
        private ?int $printRun,             // тираж
        private ?string $isbn,

        private \DateTimeImmutable $createdAt,
        private \DateTimeImmutable $updatedAt
    ) {
        self::validateTitle($title);
        self::validateYear($year);
        self::validateDescription($description);
        self::validateAuthorIds($authorIds);
        self::validatePages($pages);
        self::validatePrintRun($printRun);
        self::validateIsbn($isbn);

    }
    public static function create(
        string $title,
        int $year,
        ?string $description = null,
        array $authorIds = [],
        ?BindingType $binding = null,
        ?int $pages = null,
        ?int $printRun = null,
        ?string $isbn = null,
    ): self{

        $now = new \DateTimeImmutable();

        return new self(
            id: 0,
            title: \trim($title),
            year: $year,
            description: $description,
            authorIds: $authorIds,
            binding: $binding,
            pages: $pages,
            printRun: $printRun,
            isbn: $isbn,
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
        ?BindingType $binding,
        ?int $pages,
        ?int $printRun,
        ?string $isbn,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt
    ): self{
        return new self(
            id: $id,
            title: $title,
            year: $year,
            description: $description,
            authorIds:$authorIds,
            binding: $binding,
            pages: $pages,
            printRun: $printRun,
            isbn: $isbn,
            createdAt: $createdAt,
            updatedAt: $updatedAt
        );
    }
    private function validateTitle(string $title): void{
        if(\trim($title) === ''){
            throw new \InvalidArgumentException(
                'Title cannot be empty'
            );
        }
        if(\mb_strlen($title, 'UTF-8') > 200){
            throw new \InvalidArgumentException(
                'Title too long (max 200 chars)'
            );
        }
    }
    private function validateYear(int $year): void{
        if($year < 1000){
            throw new \InvalidArgumentException(
                'Year must be 1000 or later'
            );
        }
        $currentYear = (int)\date('Y');
        if($year > $currentYear){
            throw new \InvalidArgumentException(
                'Year cannot be more than '. $currentYear
            );
        }
    }
    private function validateDescription(?string $description): void{
        if ($description === null) {
            return;
        }
        if(\mb_strlen($description, 'UTF-8') > 10000){
            throw new \InvalidArgumentException(
                'Description too long'
            );
        }
    }
    private function validateAuthorIds(array $authorIds): void{
        foreach($authorIds as $authorId){
            if(!\is_int($authorId)){
                throw new \InvalidArgumentException(
                    'Each author ID must be an integer'
                );
            }
            if($authorId <= 0){
                throw new \InvalidArgumentException(
                    'Author id must be positive. You tried to fill: ' . $authorId
                );
            }
        }
        if(\count($authorIds) !== \count(array_unique($authorIds))){
            throw new \InvalidArgumentException(
                'Duplicate author Ids'
            );
        }
    }
    private function validatePages(?int $pages): void{
        if($pages === null){
            return;
        }
        if($pages <= 0){
            throw new \InvalidArgumentException(
                'Pages must be positive. You tried to fill: ' . $pages
            );
        }
    }
    private function validatePrintRun(?int $printRun): void{
        if($printRun === null){
            return;
        }
        if($printRun <= 0){
            throw new \InvalidArgumentException(
                'Print run must be positive. You tried to fill: ' . $printRun
            );
        }
    }
    private static function validateIsbn(?string $isbn): void
    {
        if ($isbn === null) {
            return;
        }

        // Убираем дефисы и пробелы для проверки
        $clean = \preg_replace('/[\s\-]/', '', $isbn);

        // Должен быть 10 или 13 цифр (последний символ может быть X для ISBN-10)
        if (!\preg_match('/^(\d{9}[\dX]|\d{13})$/i', $clean)) {
            throw new \InvalidArgumentException('Invalid ISBN format');
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
    public function binding(): ?BindingType{
        return $this->binding;
    }
    public function pages(): ?int{
        return $this->pages;
    }
    public function printRun(): ?int{
        return $this->printRun;
    }
    public function isbn(): ?string{
        return $this->isbn;
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

        $this->updatedAt= new \DateTimeImmutable();
    }
    public function changeYear(int $newYear): void{
        $this->validateYear($newYear);
        $this->year = $newYear;

        $this->updatedAt= new \DateTimeImmutable();
    }
    public function changeDescription(?string $newDescription): void{
        $this->validateDescription($newDescription);
        $this->description = $newDescription;

        $this->updatedAt = new \DateTimeImmutable();
    }
    public function changeBinding(?BindingType $newBinding): void{
        $this->binding = $newBinding;
        $this->updatedAt = new \DateTimeImmutable();
    }
    public function changePages(?int $newPages): void{
        $this->validatePages($newPages);
        $this->pages = $newPages;
        $this->updatedAt = new \DateTimeImmutable();
    }
    public function changePrintRun(?int $newPrintRun): void{
        $this->validatePrintRun($newPrintRun);
        $this->printRun = $newPrintRun;
        $this->updatedAt = new \DateTimeImmutable();
    }
    public function changeIsbn(?string $newIsbn): void{
        $this->validateIsbn($newIsbn);
        $this->isbn = $newIsbn;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function addAuthor(int $authorId): void{
        if($authorId <= 0){
            throw new \InvalidArgumentException('Author ID must be positive');
        }
        if (!$this->hasAuthor($authorId)) {
            $this->authorIds[] = $authorId;
        }else{
            throw new \InvalidArgumentException('Author is already added');
        }

        $this->updatedAt = new \DateTimeImmutable();

    }
    public function removeAuthor(int $authorId): void{

        $removingAuthorKey = array_search($authorId, $this->authorIds);

        if($removingAuthorKey !== false){
            unset($this->authorIds[$removingAuthorKey]);
            $this->updatedAt= new \DateTimeImmutable();
        }
        $this->authorIds = \array_values($this->authorIds);


    }
    public function hasAuthor(int $authorId): bool{
        return \in_array($authorId, $this->authorIds, true);
    }

    public function assignId(int $id): void{

        if(!$this->isNew()){
            throw new \RuntimeException('Cannot assign id to already saved entity');
        }
        if($id <= 0){
            throw new \InvalidArgumentException('ID must be positive');
        }

        $this->id = $id;
    }

}