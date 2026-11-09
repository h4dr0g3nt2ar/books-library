<?php

declare(strict_types=1);

namespace App\Domain\Author;

final class Author
{
    private function __construct(
        private int $id,
        private string $name,
        private ?string $description,
        private ?int $birthYear,
        private ?int $deathYear,
        private \DateTimeImmutable $createdAt,
        private \DateTimeImmutable $updatedAt
    ) {
        self::validateName($name);
        self::validateDescription($description);

    }
    public static function create(
        string $name,
        ?string $description = null,
        ?int $birthYear = null,
        ?int $deathYear = null,
    ): self {
        $now = new \DateTimeImmutable();
        return new self(
            id: 0,
            name: \trim($name),
            description: $description,
            birthYear: $birthYear,
            deathYear: $deathYear,
            createdAt: $now,
            updatedAt: $now
        );
    }

    public static function reconstitute(
        int $id,
        string $name,
        ?string $description,
        ?int $birthYear,
        ?int $deathYear,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt
    ): self{
        return new self(
            id: $id,
            name: $name,
            description: $description,
            birthYear: $birthYear,
            deathYear: $deathYear,
            createdAt: $createdAt,
            updatedAt: $updatedAt
        );
    }
    private function validateName(string $name): void{
        if(\trim($name) === ''){
            throw new \InvalidArgumentException(
                'Name cannot be empty'
            );
        }
        if(\mb_strlen($name, 'UTF-8') > 200){
            throw new \InvalidArgumentException(
                'Name too long (max 200 chars)'
            );
        }
    }
    private function validateDescription(?string $description): void{
        if ($description === null) {
            return;
        }
        if(\is_string($description) && \mb_strlen($description, 'UTF-8') > 10000){
            throw new \InvalidArgumentException(
                'Description too long'
            );
        }
    }
    private function validateBirthYear(int $year): void{
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
    private function validateDeathYear(int $year): void{
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
    public function rename(string $newName): void{
        $this->validateName($newName);
        $this->name = \trim($newName);

        $this->updatedAt = new \DateTimeImmutable();
    }
    public function changeDescription(?string $newDescription): void{
        $this->validateDescription($newDescription);
        $this->description = $newDescription;

        $this->updatedAt= new \DateTimeImmutable();
    }
    public function changeLifeYears(?int $birthYear, ?int $deathYear): void
    {
        // Валидация: год смерти не может быть раньше года рождения
        if ($birthYear !== null && $deathYear !== null && $deathYear < $birthYear) {
            throw new \InvalidArgumentException('Death year cannot be before birth year');
        }

        $this->birthYear = $birthYear;
        $this->deathYear = $deathYear;
        $this->updatedAt = new \DateTimeImmutable();
    }
    public function id():int {
        return $this->id;
    }
    public function name(): string{
        return $this->name;
    }
    public function description(): ?string{
        return $this->description;
    }
    public function birthYear(): ?int{
        return $this->birthYear;
    }
    public function deathYear(): ?int{
        return $this->deathYear;
    }
    public function createdAt(): \DateTimeImmutable{
        return $this->createdAt;
    }
    public function updatedAt(): \DateTimeImmutable{
        return $this->updatedAt;
    }
    public function isNew():bool {
        return $this->id === 0;
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