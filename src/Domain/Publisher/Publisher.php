<?php
declare(strict_types=1);

namespace App\Domain\Publisher;

final class Publisher
{
    private function __construct(
        private int $id,
        private string $name,
        private ?string $description,
        private \DateTimeImmutable $createdAt,
        private \DateTimeImmutable $updatedAt
    ){
        self::validateName($name);
        self::validateDescription($description);
    }
    private static function validateName(string $name): void{
        if(\trim($name) === ''){
            throw new \InvalidArgumentException(
                'Publisher name cannot be empty'
            );
        }
        if(\mb_strlen($name, 'UTF-8') > 200){
            throw new \InvalidArgumentException(
                'Publisher name too long (max 200 chars)'
            );
        }
    }
    public static function create(
        string $name,
        ?string $description
    ): self{

        $now = new \DateTimeImmutable();

        return new self(
            id: 0,
            name: \trim($name),
            description: $description,
            createdAt: $now,
            updatedAt: $now
        );
    }
    public static function reconstitute(
        int $id,
        string $name,
        ?string $description,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt
    ): self{
        return new self(
            id: $id,
            name: $name,
            description: $description,
            createdAt: $createdAt,
            updatedAt: $updatedAt
        );
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
    public function id(): int{
        return $this->id;
    }
    public function name(): string{
        return $this->name;
    }
    public function description(): ?string{
        return $this->description;
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
    public function rename(string $newName): void{
        $this->validateName($newName);
        $this->name = \trim($newName);

        $now = new \DateTimeImmutable();
        $this->updatedAt = $now;
    }
    public function changeDescription(string $newDescription): void{
        $this->validateDescription($newDescription);
        $this->description = $newDescription;
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