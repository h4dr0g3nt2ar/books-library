<?php
declare(strict_types=1);

namespace App\Domain\Country;

final class Country
{
    private function __construct(
        private int $id,
        private string $name,
        private \DateTimeImmutable $createdAt,
        private \DateTimeImmutable $updatedAt
    ){
        self::validateName($name);
    }
    private static function validateName(string $name): void{
        if(\trim($name) === ''){
            throw new \InvalidArgumentException(
                'Country name cannot be empty'
            );
        }
        if(\mb_strlen($name, 'UTF-8') > 200){
            throw new \InvalidArgumentException(
                'Country name too long (max 200 chars)'
            );
        }
    }
    public static function create(
        string $name
    ): self{

        $now = new \DateTimeImmutable();

        return new self(
            id: 0,
            name: \trim($name),
            createdAt: $now,
            updatedAt: $now
        );
    }
    public static function reconstitute(
        int $id,
        string $name,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt
    ): self{
        return new self(
            id: $id,
            name: $name,
            createdAt: $createdAt,
            updatedAt: $updatedAt
        );
    }
    public function id(): int{
        return $this->id;
    }
    public function name(): string{
        return $this->name;
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