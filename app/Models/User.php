<?php

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected string $table = 'users';

    // Login works with either email or phone
    public function findByLogin(string $login): ?array
    {
        return $this->fetchOne('SELECT * FROM users WHERE email = ? OR phone = ? LIMIT 1', [$login, $login]);
    }

    public function emailExists(string $email, int $exceptId = 0): bool
    {
        return $this->count('email = ? AND id <> ?', [$email, $exceptId]) > 0;
    }

    public function phoneExists(string $phone, int $exceptId = 0): bool
    {
        return $this->count('phone = ? AND id <> ?', [$phone, $exceptId]) > 0;
    }

    public function create(array $data): int
    {
        return $this->insert(
            'INSERT INTO users (role, name, email, phone, password, gender, date_of_birth, address)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $data['role'],
                $data['name'],
                $data['email'],
                $data['phone'],
                password_hash($data['password'], PASSWORD_DEFAULT),
                $data['gender'] ?? null,
                $data['date_of_birth'] ?? null,
                $data['address'] ?? null,
            ]
        );
    }

    // Patient profile update
    public function updateProfile(int $id, array $data): void
    {
        $this->execute(
            'UPDATE users SET name = ?, email = ?, phone = ?, gender = ?, date_of_birth = ?, address = ? WHERE id = ?',
            [$data['name'], $data['email'], $data['phone'], $data['gender'], $data['date_of_birth'], $data['address'], $id]
        );
    }

    // Name / email / phone / gender only (used for doctor accounts)
    public function updateBasic(int $id, array $data): void
    {
        $this->execute(
            'UPDATE users SET name = ?, email = ?, phone = ?, gender = ? WHERE id = ?',
            [$data['name'], $data['email'], $data['phone'], $data['gender'], $id]
        );
    }

    public function updatePassword(int $id, string $password): void
    {
        $this->execute('UPDATE users SET password = ? WHERE id = ?', [password_hash($password, PASSWORD_DEFAULT), $id]);
    }

    // Patient list for the admin panel, with appointment count
    public function patients(string $search, int $page, int $perPage): array
    {
        $where  = "u.role = 'patient'";
        $params = [];

        if ($search !== '') {
            $where   .= ' AND (u.name LIKE ? OR u.email LIKE ? OR u.phone LIKE ?)';
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $rows = $this->fetchAll(
            "SELECT u.*, (SELECT COUNT(*) FROM appointments a WHERE a.patient_id = u.id) AS appointment_count
             FROM users u
             WHERE $where
             ORDER BY u.id DESC " . $this->limit($page, $perPage),
            $params
        );

        $total = (int) $this->fetchValue("SELECT COUNT(*) FROM users u WHERE $where", $params);

        return ['rows' => $rows, 'total' => $total];
    }
}
