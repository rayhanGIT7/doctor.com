<?php

namespace App\Models;

use App\Core\Model;
use Throwable;

class Doctor extends Model
{
    protected string $table = 'doctors';

    /**
     * Doctor search, used by the public search page and the admin list.
     *
     * $filters keys (all optional): q, category, hospital, city, gender, date, status
     * $publicOnly = true shows only doctors that patients can book.
     */
    public function search(array $filters, int $page, int $perPage, bool $publicOnly = true): array
    {
        $where  = [];
        $params = [];

        if ($publicOnly) {
            $where[] = "d.status = 'active' AND u.status = 'active' AND h.status = 'active'";
        }
        if (!empty($filters['q'])) {
            $where[]  = 'u.name LIKE ?';
            $params[] = '%' . $filters['q'] . '%';
        }
        if (!empty($filters['category'])) {
            $where[]  = 'd.id IN (SELECT doctor_id FROM doctor_category WHERE category_id = ?)';
            $params[] = (int) $filters['category'];
        }
        if (!empty($filters['hospital'])) {
            $where[]  = 'd.hospital_id = ?';
            $params[] = (int) $filters['hospital'];
        }
        if (!empty($filters['city'])) {
            $where[]  = 'h.city = ?';
            $params[] = $filters['city'];
        }
        if (!empty($filters['gender'])) {
            $where[]  = 'u.gender = ?';
            $params[] = $filters['gender'];
        }
        if (!empty($filters['date'])) {
            // Doctor has a schedule on that day of the week
            $where[]  = "d.id IN (SELECT doctor_id FROM doctor_schedules WHERE status = 'active' AND day_of_week = ?)";
            $params[] = (int) date('w', strtotime($filters['date']));
        }
        if (!empty($filters['status'])) {
            $where[]  = 'd.status = ?';
            $params[] = $filters['status'];
        }

        $whereSql = $where ? implode(' AND ', $where) : '1';
        $from     = 'FROM doctors d
                     JOIN users u ON u.id = d.user_id
                     JOIN hospitals h ON h.id = d.hospital_id';

        $rows = $this->fetchAll(
            "SELECT d.*, u.name, u.email, u.phone, u.gender, h.name AS hospital_name, h.city,
                    (SELECT GROUP_CONCAT(c.name ORDER BY c.name SEPARATOR ', ')
                     FROM doctor_category dc JOIN categories c ON c.id = dc.category_id
                     WHERE dc.doctor_id = d.id) AS categories
             $from
             WHERE $whereSql
             ORDER BY u.name " . $this->limit($page, $perPage),
            $params
        );

        $total = (int) $this->fetchValue("SELECT COUNT(*) $from WHERE $whereSql", $params);

        return ['rows' => $rows, 'total' => $total];
    }

    // One doctor with login info and hospital info
    public function findDetails(int $id): ?array
    {
        return $this->fetchOne(
            'SELECT d.*, u.name, u.email, u.phone, u.gender, u.status AS user_status,
                    h.name AS hospital_name, h.address AS hospital_address, h.city, h.country,
                    h.phone AS hospital_phone, h.status AS hospital_status
             FROM doctors d
             JOIN users u ON u.id = d.user_id
             JOIN hospitals h ON h.id = d.hospital_id
             WHERE d.id = ?',
            [$id]
        );
    }

    public function findByUserId(int $userId): ?array
    {
        $doctorId = $this->fetchValue('SELECT id FROM doctors WHERE user_id = ?', [$userId]);

        return $doctorId ? $this->findDetails((int) $doctorId) : null;
    }

    // Can patients book this doctor right now?
    public function isBookable(array $doctor): bool
    {
        return $doctor['status'] === 'active'
            && $doctor['user_status'] === 'active'
            && $doctor['hospital_status'] === 'active';
    }

    public function categories(int $doctorId): array
    {
        return $this->fetchAll(
            'SELECT c.id, c.name
             FROM doctor_category dc
             JOIN categories c ON c.id = dc.category_id
             WHERE dc.doctor_id = ?
             ORDER BY c.name',
            [$doctorId]
        );
    }

    public function categoryIds(int $doctorId): array
    {
        return array_column($this->categories($doctorId), 'id');
    }

    // Creates the login account and the doctor profile together
    public function createWithUser(array $user, array $doctor, array $categoryIds): int
    {
        $this->db->beginTransaction();

        try {
            $user['role'] = 'doctor';
            $userId = (new User())->create($user);

            $doctorId = $this->insert(
                'INSERT INTO doctors (user_id, hospital_id, qualification, experience_years, consultation_fee, chamber_info, bio, image)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
                [$userId, $doctor['hospital_id'], $doctor['qualification'], $doctor['experience_years'],
                 $doctor['consultation_fee'], $doctor['chamber_info'], $doctor['bio'], $doctor['image']]
            );

            $this->syncCategories($doctorId, $categoryIds);
            $this->db->commit();

            return $doctorId;
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // Admin edit: updates the login account and the doctor profile together
    public function updateWithUser(array $current, array $user, array $doctor, array $categoryIds): void
    {
        $this->db->beginTransaction();

        try {
            (new User())->updateBasic((int) $current['user_id'], $user);

            $this->execute(
                'UPDATE doctors
                 SET hospital_id = ?, qualification = ?, experience_years = ?, consultation_fee = ?,
                     chamber_info = ?, bio = ?, image = ?
                 WHERE id = ?',
                [$doctor['hospital_id'], $doctor['qualification'], $doctor['experience_years'], $doctor['consultation_fee'],
                 $doctor['chamber_info'], $doctor['bio'], $doctor['image'], $current['id']]
            );

            $this->syncCategories((int) $current['id'], $categoryIds);
            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // Doctor's own profile edit (fee, hospital and categories are managed by admin)
    public function updateProfile(int $id, array $data): void
    {
        $this->execute(
            'UPDATE doctors SET qualification = ?, chamber_info = ?, bio = ?, image = ? WHERE id = ?',
            [$data['qualification'], $data['chamber_info'], $data['bio'], $data['image'], $id]
        );
    }

    // Activates/deactivates the doctor profile and the login account together
    public function setActive(array $doctor, string $status): void
    {
        $this->setStatus((int) $doctor['id'], $status);
        (new User())->setStatus((int) $doctor['user_id'], $status);
    }

    private function syncCategories(int $doctorId, array $categoryIds): void
    {
        $this->execute('DELETE FROM doctor_category WHERE doctor_id = ?', [$doctorId]);

        foreach (array_unique($categoryIds) as $categoryId) {
            $this->execute('INSERT INTO doctor_category (doctor_id, category_id) VALUES (?, ?)', [$doctorId, (int) $categoryId]);
        }
    }
}
