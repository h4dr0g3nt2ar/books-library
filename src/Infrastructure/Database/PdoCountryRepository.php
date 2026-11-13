<?php
declare(strict_types=1);

namespace App\Infrastructure\Database;

use App\Domain\Country\Country;
use App\Domain\Country\CountryRepositoryInterface;

final class PdoCountryRepository implements CountryRepositoryInterface
{
    public function __construct(private readonly \PDO $pdo){ }

    public function save(Country $country): void{
        try{
            if($country->isNew()){
                $this->insertCountry($country);
            }else{
                $this->updateCountry($country);
            }
        }catch(\Throwable $e){
            throw $e;
        }
    }
    private function insertCountry(Country $country): void{
        $stmt = $this->pdo->prepare(
            "INSERT INTO countries (name, created_at, updated_at) 
            VALUES (:name, :created_at, :updated_at)");
        $stmt->execute(            [
                'name' => $country->name(),
                'created_at' => $country->createdAt()->format('Y-m-d H:i:s'),
                'updated_at' => $country->updatedAt()->format('Y-m-d H:i:s')
            ]
        );
        $country->assignId((int) $this->pdo->lastInsertId());
    }
    private function updateCountry(Country $country): void{
        $stmt = $this->pdo->prepare(
            "UPDATE countries SET name = :name, updated_at = :updated_at WHERE id = :id"
        );
        $stmt->execute(
            [
                'name' => $country->name(),
                'updated_at' => $country->updatedAt()->format('Y-m-d H:i:s'),
                'id' => $country->id()
            ]
        );
    }
    public function findById(int $id): ?Country{
        $stmt = $this->pdo->prepare(
            "SELECT * FROM countries WHERE id = :id "
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

        return Country::reconstitute(
            id: (int)$row['id'],
            name: $row['name'],
            createdAt: new \DateTimeImmutable($row['created_at']),
            updatedAt: new \DateTimeImmutable($row['updated_at'])
        );
    }
    public function all(int $limit = 100, int $offset = 0): array{
        $stmt = $this->pdo->prepare(
            "SELECT * FROM countries ORDER BY name LIMIT :limit OFFSET :offset"
        );
        $stmt->bindValue('limit', (int)$limit, \PDO::PARAM_INT);
        $stmt->bindValue('offset', (int)$offset, \PDO::PARAM_INT);

        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $countries = [];

        foreach($rows as $row){
            $countries[] = Country::reconstitute(
                id: (int)$row['id'],
                name: $row['name'],
                createdAt: new \DateTimeImmutable($row['created_at']),
                updatedAt: new \DateTimeImmutable($row['updated_at'])
            );
        }

        return $countries;
    }
    public function delete(Country $country): void{

        if($country->isNew()){
            throw new \InvalidArgumentException('Cannot delete unsaved country');
        }

        $stmt = $this->pdo->prepare(
            "DELETE FROM countries WHERE id = :id"
        );
        $stmt->execute(
            ['id' => $country->id()]
        );
    }
    public function count(): int{
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM countries"
        );
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }
}