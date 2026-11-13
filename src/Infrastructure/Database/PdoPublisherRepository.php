<?php
declare(strict_types=1);

namespace App\Infrastructure\Database;

use App\Domain\Publisher\Publisher;
use App\Domain\Publisher\PublisherRepositoryInterface;

final class PdoPublisherRepository implements PublisherRepositoryInterface
{
    public function __construct(private readonly \PDO $pdo){ }

    public function save(Publisher $publisher): void{
        try{
            if($publisher->isNew()){
                $this->insertPublisher($publisher);
            }else{
                $this->updatePublisher($publisher);
            }
        }catch(\Throwable $e){
            throw $e;
        }
    }
    private function insertPublisher(Publisher $publisher): void{
        $stmt = $this->pdo->prepare(
            "INSERT INTO publishers (name, description, created_at, updated_at) 
            VALUES (:name, :description, :created_at, :updated_at)");
        $stmt->execute(            [
                'name' => $publisher->name(),
                'description' => $publisher->description(),
                'created_at' => $publisher->createdAt()->format('Y-m-d H:i:s'),
                'updated_at' => $publisher->updatedAt()->format('Y-m-d H:i:s')
            ]
        );
        $publisher->assignId((int) $this->pdo->lastInsertId());
    }
    private function updatePublisher(Publisher $publisher): void{
        $stmt = $this->pdo->prepare(
            "UPDATE publishers SET name = :name, description = :description, updated_at = :updated_at WHERE id = :id"
        );
        $stmt->execute(
            [
                'name' => $publisher->name(),
                'description' => $publisher->description(),
                'updated_at' => $publisher->updatedAt()->format('Y-m-d H:i:s'),
                'id' => $publisher->id()
            ]
        );
    }
    public function findById(int $id): ?Publisher{
        $stmt = $this->pdo->prepare(
            "SELECT * FROM publishers WHERE id = :id "
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

        return Publisher::reconstitute(
            id: (int)$row['id'],
            name: $row['name'],
            description: $row['description'],
            createdAt: new \DateTimeImmutable($row['created_at']),
            updatedAt: new \DateTimeImmutable($row['updated_at'])
        );
    }
    public function all(int $limit = 100, int $offset = 0): array{
        $stmt = $this->pdo->prepare(
            "SELECT * FROM publishers ORDER BY name LIMIT :limit OFFSET :offset"
        );
        $stmt->bindValue('limit', (int)$limit, \PDO::PARAM_INT);
        $stmt->bindValue('offset', (int)$offset, \PDO::PARAM_INT);

        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $publishers = [];

        foreach($rows as $row){
            $publishers[] = Publisher::reconstitute(
                id: (int)$row['id'],
                name: $row['name'],
                description: $row['description'],
                createdAt: new \DateTimeImmutable($row['created_at']),
                updatedAt: new \DateTimeImmutable($row['updated_at'])
            );
        }

        return $publishers;
    }
    public function delete(Publisher $publisher): void{

        if($publisher->isNew()){
            throw new \InvalidArgumentException('Cannot delete unsaved publisher');
        }

        $stmt = $this->pdo->prepare(
            "DELETE FROM publishers WHERE id = :id"
        );
        $stmt->execute(
            ['id' => $publisher->id()]
        );
    }
    public function count(): int{
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM publishers"
        );
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }
}