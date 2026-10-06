<?php

namespace App\Core;

use PDO;

/**
 * Base class for all models. Gives every model the database connection
 * and a few small query helpers. All queries use prepared statements.
 */
abstract class Model
{
    protected PDO $db;
    protected string $table = '';

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function find(int $id): ?array
    {
        return $this->fetchOne("SELECT * FROM {$this->table} WHERE id = ?", [$id]);
    }

    public function setStatus(int $id, string $status): void
    {
        $this->execute("UPDATE {$this->table} SET status = ? WHERE id = ?", [$status, $id]);
    }

    // $where is always written in code, never taken from user input
    public function count(string $where = '1', array $params = []): int
    {
        return (int) $this->fetchValue("SELECT COUNT(*) FROM {$this->table} WHERE $where", $params);
    }

    protected function fetchOne(string $sql, array $params = []): ?array
    {
        $statement = $this->db->prepare($sql);
        $statement->execute($params);
        $row = $statement->fetch();

        return $row ?: null;
    }

    protected function fetchAll(string $sql, array $params = []): array
    {
        $statement = $this->db->prepare($sql);
        $statement->execute($params);

        return $statement->fetchAll();
    }

    protected function fetchValue(string $sql, array $params = [])
    {
        $statement = $this->db->prepare($sql);
        $statement->execute($params);

        return $statement->fetchColumn();
    }

    protected function execute(string $sql, array $params = []): int
    {
        $statement = $this->db->prepare($sql);
        $statement->execute($params);

        return $statement->rowCount();
    }

    // Runs an INSERT and returns the new id
    protected function insert(string $sql, array $params = []): int
    {
        $this->execute($sql, $params);

        return (int) $this->db->lastInsertId();
    }

    // Builds "LIMIT x OFFSET y" for a page number (values are cast to int, so it is safe)
    protected function limit(int $page, int $perPage): string
    {
        $offset = (max(1, $page) - 1) * $perPage;

        return 'LIMIT ' . (int) $perPage . ' OFFSET ' . (int) $offset;
    }
}
