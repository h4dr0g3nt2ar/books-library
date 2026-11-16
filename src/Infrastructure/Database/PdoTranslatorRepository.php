<?php
declare(strict_types=1);

namespace App\Infrastructure\Database;

use App\Domain\Translator\Translator;
use App\Domain\Translator\TranslatorRepositoryInterface;

final class PdoTranslatorRepository implements TranslatorRepositoryInterface
{
    public function __construct(private readonly \PDO $pdo){ }

    public function save(Translator $translator): void{
        try{
            if($translator->isNew()){
                $this->insertTranslator($translator);
            }else{
                $this->updateTranslator($translator);
            }
        }catch(\Throwable $e){
            throw $e;
        }
    }
    private function insertTranslator(Translator $translator): void{
        $stmt = $this->pdo->prepare(
            "INSERT INTO translators (name, description, created_at, updated_at) 
            VALUES (:name, :description, :created_at, :updated_at)");
        $stmt->execute(            [
                'name' => $translator->name(),
                'description' => $translator->description(),
                'created_at' => $translator->createdAt()->format('Y-m-d H:i:s'),
                'updated_at' => $translator->updatedAt()->format('Y-m-d H:i:s')
            ]
        );
        $translator->assignId((int) $this->pdo->lastInsertId());
    }
    private function updateTranslator(Translator $translator): void{
        $stmt = $this->pdo->prepare(
            "UPDATE translators SET name = :name, description = :description, updated_at = :updated_at WHERE id = :id"
        );
        $stmt->execute(
            [
                'name' => $translator->name(),
                'description' => $translator->description(),
                'updated_at' => $translator->updatedAt()->format('Y-m-d H:i:s'),
                'id' => $translator->id()
            ]
        );
    }
    public function findById(int $id): ?Translator{
        $stmt = $this->pdo->prepare(
            "SELECT * FROM translators WHERE id = :id "
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

        return Translator::reconstitute(
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

        $stmt = $this->pdo->prepare("SELECT * FROM translators WHERE id IN ($placeholders)");

        $stmt->execute($ids);

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if($rows === []){
            return [];
        }

        $translators = [];

        foreach($rows as $row){
            $translators[]= Translator::reconstitute(
                id: (int)$row['id'],
                name: $row['name'],
                description: $row['description'],
                createdAt: new \DateTimeImmutable($row['created_at']),
                updatedAt: new \DateTimeImmutable($row['updated_at'])
            );
        }

        return $translators;
    }
    public function all(int $limit = 100, int $offset = 0): array{
        $stmt = $this->pdo->prepare(
            "SELECT * FROM translators ORDER BY name LIMIT :limit OFFSET :offset"
        );
        $stmt->bindValue('limit', (int)$limit, \PDO::PARAM_INT);
        $stmt->bindValue('offset', (int)$offset, \PDO::PARAM_INT);

        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $translators = [];

        foreach($rows as $row){
            $translators[] = Translator::reconstitute(
                id: (int)$row['id'],
                name: $row['name'],
                description: $row['description'],
                createdAt: new \DateTimeImmutable($row['created_at']),
                updatedAt: new \DateTimeImmutable($row['updated_at'])
            );
        }

        return $translators;
    }
    public function delete(Translator $translator): void{

        if($translator->isNew()){
            throw new \InvalidArgumentException('Cannot delete unsaved translator');
        }

        $stmt = $this->pdo->prepare(
            "DELETE FROM translators WHERE id = :id"
        );
        $stmt->execute(
            ['id' => $translator->id()]
        );
    }
    public function count(): int{
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM translators"
        );
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }
}