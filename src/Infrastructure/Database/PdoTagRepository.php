<?php
declare(strict_types=1);

namespace App\Infrastructure\Database;

use App\Domain\Tag\Tag;
use App\Domain\Tag\TagRepositoryInterface;

final class PdoTagRepository implements TagRepositoryInterface
{
    public function __construct(private readonly \PDO $pdo){ }

    public function save(Tag $tag): void{
        try{
            if($tag->isNew()){
                $this->insertTag($tag);
            }else{
                $this->updateTag($tag);
            }
        }catch(\Throwable $e){
            throw $e;
        }
    }
    private function insertTag(Tag $tag): void{
        $stmt = $this->pdo->prepare(
            "INSERT INTO tags (name, description, created_at, updated_at) 
            VALUES (:name, :description, :created_at, :updated_at)");
        $stmt->execute(            [
                'name' => $tag->name(),
                'description' => $tag->description(),
                'created_at' => $tag->createdAt()->format('Y-m-d H:i:s'),
                'updated_at' => $tag->updatedAt()->format('Y-m-d H:i:s')
            ]
        );
        $tag->assignId((int) $this->pdo->lastInsertId());
    }
    private function updateTag(Tag $tag): void{
        $stmt = $this->pdo->prepare(
            "UPDATE tags SET name = :name, description = :description, updated_at = :updated_at WHERE id = :id"
        );
        $stmt->execute(
            [
                'name' => $tag->name(),
                'description' => $tag->description(),
                'updated_at' => $tag->updatedAt()->format('Y-m-d H:i:s'),
                'id' => $tag->id()
            ]
        );
    }
    public function findById(int $id): ?Tag{
        $stmt = $this->pdo->prepare(
            "SELECT * FROM tags WHERE id = :id "
        );
        $stmt->execute(
            [
                'id' => $id
            ]
        );
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($row === false){
            return null;
        }

        return Tag::reconstitute(
            id: (int)$row['id'],
            name: $row['name'],
            description: $row['description'],
            createdAt: new \DateTimeImmutable($row['created_at']),
            updatedAt: new \DateTimeImmutable($row['updated_at'])
        );
    }
    public function findByIds(array $ids): array{

        if($ids ===[]){
            return [];
        }
        $ids = \array_values($ids);

        $placeholders = \implode(',', \array_fill(0, \count($ids), '?'));

        $stmt = $this->pdo->prepare("SELECT * FROM tags WHERE id IN ($placeholders)");

        $stmt->execute($ids);

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if($rows === []){
            return [];
        }

        $tags = [];

        foreach($rows as $row){
            $tags[]= Tag::reconstitute(
                id: (int)$row['id'],
                name: $row['name'],
                description: $row['description'],
                createdAt: new \DateTimeImmutable($row['created_at']),
                updatedAt: new \DateTimeImmutable($row['updated_at'])
            );
        }

        return $tags;
    }
    public function all(int $limit = 100, int $offset = 0): array{
        $stmt = $this->pdo->prepare(
            "SELECT * FROM tags ORDER BY name LIMIT :limit OFFSET :offset"
        );
        $stmt->bindValue('limit', (int)$limit, \PDO::PARAM_INT);
        $stmt->bindValue('offset', (int)$offset, \PDO::PARAM_INT);

        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $tags = [];

        foreach($rows as $row){
            $tags[] = Tag::reconstitute(
                id: (int)$row['id'],
                name: $row['name'],
                description: $row['description'],
                createdAt: new \DateTimeImmutable($row['created_at']),
                updatedAt: new \DateTimeImmutable($row['updated_at'])
            );
        }

        return $tags;
    }
    public function delete(Tag $tag): void{

        if($tag->isNew()){
            throw new \InvalidArgumentException('Cannot delete unsaved tag');
        }

        $stmt = $this->pdo->prepare(
            "DELETE FROM tags WHERE id = :id"
        );
        $stmt->execute(
            ['id' => $tag->id()]
        );
    }
    public function count(): int{
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM tags"
        );
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }
}