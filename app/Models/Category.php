<?php

namespace App\Models;

use App\Core\Model;

class Category extends Model
{
    protected string $table = 'categories';

    // Admin list with doctor count
    public function all(): array
    {
        return $this->fetchAll(
            'SELECT c.*, (SELECT COUNT(*) FROM doctor_category dc WHERE dc.category_id = c.id) AS doctor_count
             FROM categories c
             ORDER BY c.name'
        );
    }

    // Active categories with the number of active doctors (home page + filters)
    public function active(): array
    {
        return $this->fetchAll(
            "SELECT c.id, c.name, c.description,
                    (SELECT COUNT(*)
                     FROM doctor_category dc
                     JOIN doctors d ON d.id = dc.doctor_id AND d.status = 'active'
                     WHERE dc.category_id = c.id) AS doctor_count
             FROM categories c
             WHERE c.status = 'active'
             ORDER BY c.name"
        );
    }

    public function nameExists(string $name, int $exceptId = 0): bool
    {
        return $this->count('name = ? AND id <> ?', [$name, $exceptId]) > 0;
    }

    public function create(array $data): int
    {
        return $this->insert(
            'INSERT INTO categories (name, description, status) VALUES (?, ?, ?)',
            [$data['name'], $data['description'], $data['status']]
        );
    }

    public function update(int $id, array $data): void
    {
        $this->execute(
            'UPDATE categories SET name = ?, description = ?, status = ? WHERE id = ?',
            [$data['name'], $data['description'], $data['status'], $id]
        );
    }
}
