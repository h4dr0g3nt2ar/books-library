<?php
declare(strict_types=1);

namespace App\Infrastructure\Database;

use App\Domain\Series\Series;
use App\Domain\Series\SeriesRepositoryInterface;

final class PdoSeriesRepository implements SeriesRepositoryInterface
{
    public function __construct(private readonly \PDO $pdo){ }

    public function save(Series $series): void{
        try{
            if($series->isNew()){
                $this->insertSeries($series);
            }else{
                $this->updateSeries($series);
            }
        }catch(\Throwable $e){
            throw $e;
        }
    }
    private function insertSeries(Series $series): void{
        $stmt = $this->pdo->prepare(
            "INSERT INTO series (name, description, created_at, updated_at) 
            VALUES (:name, :description, :created_at, :updated_at)");
        $stmt->execute(            [
                'name' => $series->name(),
                'description' => $series->description(),
                'created_at' => $series->createdAt()->format('Y-m-d H:i:s'),
                'updated_at' => $series->updatedAt()->format('Y-m-d H:i:s')
            ]
        );
        $series->assignId((int) $this->pdo->lastInsertId());
    }
    private function updateSeries(Series $series): void{
        $stmt = $this->pdo->prepare(
            "UPDATE series SET name = :name, description = :description, updated_at = :updated_at WHERE id = :id"
        );
        $stmt->execute(
            [
                'name' => $series->name(),
                'description' => $series->description(),
                'updated_at' => $series->updatedAt()->format('Y-m-d H:i:s'),
                'id' => $series->id()
            ]
        );
    }
    public function findById(int $id): ?Series{
        $stmt = $this->pdo->prepare(
            "SELECT * FROM series WHERE id = :id "
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

        return Series::reconstitute(
            id: (int)$row['id'],
            name: $row['name'],
            description: $row['description'],
            createdAt: new \DateTimeImmutable($row['created_at']),
            updatedAt: new \DateTimeImmutable($row['updated_at'])
        );
    }
    public function all(int $limit = 100, int $offset = 0): array{
        $stmt = $this->pdo->prepare(
            "SELECT * FROM series ORDER BY name LIMIT :limit OFFSET :offset"
        );
        $stmt->bindValue('limit', (int)$limit, \PDO::PARAM_INT);
        $stmt->bindValue('offset', (int)$offset, \PDO::PARAM_INT);

        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $seriess = [];

        foreach($rows as $row){
            $seriess[] = Series::reconstitute(
                id: (int)$row['id'],
                name: $row['name'],
                description: $row['description'],
                createdAt: new \DateTimeImmutable($row['created_at']),
                updatedAt: new \DateTimeImmutable($row['updated_at'])
            );
        }

        return $seriess;
    }
    public function delete(Series $series): void{

        if($series->isNew()){
            throw new \InvalidArgumentException('Cannot delete unsaved series');
        }

        $stmt = $this->pdo->prepare(
            "DELETE FROM series WHERE id = :id"
        );
        $stmt->execute(
            ['id' => $series->id()]
        );
    }
    public function count(): int{
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM series"
        );
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }
}