<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use App\Domain\Author\Author;
use App\Domain\Author\AuthorRepositoryInterface;

final class PdoAuthorRepository implements AuthorRepositoryInterface
{
    public function __construct(private readonly \PDO $pdo,){
        return $pdo;
    }
    public function save(Author $author): void
    {

        try{
            if($author->isNew()){
                $this->insertAuthor($author);
            }else{
                $this->updateAuthor($author);
            }

        }catch(\Throwable $e){
            throw $e;
        }
    }
    public function findById(int $id): ?Author
    {
        $stmt = $this->pdo->prepare("SELECT * FROM authors WHERE id = :id");
        $stmt->execute(['id'=>$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if($row === false){
            return null;
        }

        $author =  Author::reconstitute(
            id: (int)$row['id'],
            name: $row['name'],
            description: $row['description'],
            createdAt: new \DateTimeImmutable($row['created_at']),
            updatedAt: new \DateTimeImmutable($row['updated_at'])
        );
        return $author;
    }
    public function findByIds(array $ids): array{

        $placeholders = \implode(',', \array_fill(0, \count($ids), '?'));

        $stmt = $this->pdo->prepare("SELECT * FROM authors WHERE id IN ($placeholders)");
    }
    public function findByName(string $name): ?Author{
        $stmt = $this->pdo->prepare("SELECT * FROM authors WHERE name=:name");
        $stmt->execute(['name' => $name]);

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if($row === false){
            return null;
        }

        $author =  Author::reconstitute(
            id: (int)$row['id'],
            name: $row['name'],
            description: $row['description'],
            createdAt: new \DateTimeImmutable($row['created_at']),
            updatedAt: new \DateTimeImmutable($row['updated_at'])
        );
        return $author;
    }
    public function all(int $limit = 100, int $offset = 0): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM authors ORDER BY id LIMIT :limit OFFSET :offset");
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if($rows === []){
            return [];
        }

        $authorIds = [];
        foreach ($rows as $row) {
            $authords[] = (int) $row['id'];
        }
        $placeholders = \implode(',', \array_fill(0, \count($authorIds), '?'));

        $authors = [];
        foreach($rows as $row){

            $authorId = (int)$row['id'];

            $authors[] = Author::reconstitute(
                id: (int)$row['id'],
                name: $row['name'],
                description: $row['description'],
                createdAt: new \DateTimeImmutable($row['created_at']),
                updatedAt: new \DateTimeImmutable($row['updated_at'])
            );
        }
        return $authors;
    }
    public function delete(Author $author): void
    {
        if($author->isNew()){
            throw new \InvalidArgumentException('Cannot delete unsaved author');
        }
        $stmt = $this->pdo->prepare("DELETE FROM authors WHERE id = :id");
        $stmt->execute(['id' => $author->id()]);

    }
    public function count(): int
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM authors");
        return (int)$stmt->fetchColumn();
    }
    private function insertAuthor(Author $author): void {
        $stmt = $this->pdo->prepare(
            "INSERT INTO authors (name, description, created_at, updated_at) 
            VALUES (:name, :description, :created_at, :updated_at)");
        $stmt->execute(
            [
                'name' => $author->name(),
                'description' => $author->description(),
                'created_at' => $author->createdAt()->format('Y-m-d H:i:s'),
                'updated_at' => $author->updatedAt()->format('Y-m-d H:i:s')
            ]
        );
        $author->assignId((int) $this->pdo->lastInsertId());

    }
    private function updateAuthor(Author $author): void {
        $stmt = $this->pdo->prepare(
            "UPDATE authors SET name = :name, description = :description, updated_at = :updated_at
            WHERE id = :id
        ");
        $stmt->execute(
            [
                'id' => $author->id(),
                'name' => $author->name(),
                'description' => $author->description(),
                'updated_at' => $author->updatedAt()->format('Y-m-d H:i:s')
            ]
        );
    }


}