<?php

namespace App\Models;

use App\Core\Model;

class Hospital extends Model
{
    protected string $table = 'hospitals';

    // Admin list with doctor count
    public function list(string $search, string $status, int $page, int $perPage): array
    {
        $where  = '1';
        $params = [];

        if ($search !== '') {
            $where   .= ' AND (h.name LIKE ? OR h.city LIKE ?)';
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        if ($status !== '') {
            $where   .= ' AND h.status = ?';
            $params[] = $status;
        }

        $rows = $this->fetchAll(
            "SELECT h.*, (SELECT COUNT(*) FROM doctors d WHERE d.hospital_id = h.id) AS doctor_count
             FROM hospitals h
             WHERE $where
             ORDER BY h.name " . $this->limit($page, $perPage),
            $params
        );

        $total = (int) $this->fetchValue("SELECT COUNT(*) FROM hospitals h WHERE $where", $params);

        return ['rows' => $rows, 'total' => $total];
    }

    // For dropdowns
    public function active(): array
    {
        return $this->fetchAll("SELECT id, name, city FROM hospitals WHERE status = 'active' ORDER BY name");
    }

    public function all(): array
    {
        return $this->fetchAll('SELECT id, name, city, status FROM hospitals ORDER BY name');
    }

    public function cities(): array
    {
        return $this->db
            ->query("SELECT DISTINCT city FROM hospitals WHERE status = 'active' ORDER BY city")
            ->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function nameExists(string $name, string $city, int $exceptId = 0): bool
    {
        return $this->count('name = ? AND city = ? AND id <> ?', [$name, $city, $exceptId]) > 0;
    }

    public function create(array $data): int
    {
        return $this->insert(
            'INSERT INTO hospitals (name, address, city, country, phone, email, description, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
            [$data['name'], $data['address'], $data['city'], $data['country'], $data['phone'], $data['email'], $data['description'], $data['status']]
        );
    }

    public function update(int $id, array $data): void
    {
        $this->execute(
            'UPDATE hospitals
             SET name = ?, address = ?, city = ?, country = ?, phone = ?, email = ?, description = ?, status = ?
             WHERE id = ?',
            [$data['name'], $data['address'], $data['city'], $data['country'], $data['phone'], $data['email'], $data['description'], $data['status'], $id]
        );
    }

    public function doctorCount(int $id): int
    {
        return (int) $this->fetchValue('SELECT COUNT(*) FROM doctors WHERE hospital_id = ?', [$id]);
    }

    public function delete(int $id): void
    {
        $this->execute('DELETE FROM hospitals WHERE id = ?', [$id]);
    }
}
