<?php

namespace App\Model;

use App\Model\Connection;
use PDO;

abstract class AbstractManager
{
    protected PDO $pdo;

    public const TABLE = '';
    public const TABLE_ID = '';

    public function __construct()
    {
        $connection = new Connection();
        $this->pdo = $connection->getConnection();
    }

    public function selectAll(string $orderBy = '', string $direction = 'ASC'): array
    {
        $query = 'SELECT * FROM' . static::TABLE;
        if ($orderBy) {
            $query .= ' ORDER BY ' . $orderBy . ' ' . $direction;
        }

        return $this->pdo->query($query)->fetchAll();
    }

    public function selectOneById(int $id): array|false
    {
        // prepared request
        $statement = $this->pdo->prepare("
            SELECT *
            FROM " . static::TABLE . "
            WHERE " . $this->getPrimaryKeyNameFromTable() . "=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch();
    }

    public function delete(int $id): void
    {
        // prepared request
        $statement = $this->pdo->prepare("
            DELETE
            FROM " . static::TABLE . "
            WHERE " . $this->getPrimaryKeyNameFromTable() . "=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();
    }

    public function getPrimaryKeyNameFromTable(string $tableName = ''): string
    {
        if (empty($tableName)) {
            $tableName = static::TABLE;
        }
        return preg_replace('/bt/', 'id', $tableName);
    }

    public function getForeignKeyNameFromTable(string $foreignTable): string
    {
        return preg_replace('/bt_/', '', $foreignTable) . '_id';
    }
}
