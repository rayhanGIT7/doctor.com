<?php

namespace App\Models;

use App\Core\Model;

class Appointment extends Model
{
    protected string $table = 'appointments';

    public const STATUSES = ['confirmed', 'completed', 'cancelled', 'no_show'];

    public function create(array $data): int
    {
        return $this->insert(
            'INSERT INTO appointments
                (appointment_no, patient_id, doctor_id, appointment_date, appointment_time, patient_name, patient_phone, note, fee)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $this->newAppointmentNo($data['appointment_date']),
                $data['patient_id'],
                $data['doctor_id'],
                $data['appointment_date'],
                $data['appointment_time'],
                $data['patient_name'],
                $data['patient_phone'],
                $data['note'],
                $data['fee'],
            ]
        );
    }

    // One appointment with doctor and hospital info
    public function findDetails(int $id): ?array
    {
        return $this->fetchOne(
            'SELECT a.*, du.name AS doctor_name, d.image AS doctor_image, d.chamber_info, d.user_id AS doctor_user_id,
                    h.name AS hospital_name, h.address AS hospital_address, h.city,
                    pu.email AS patient_email
             FROM appointments a
             JOIN doctors d ON d.id = a.doctor_id
             JOIN users du ON du.id = d.user_id
             JOIN hospitals h ON h.id = d.hospital_id
             JOIN users pu ON pu.id = a.patient_id
             WHERE a.id = ?',
            [$id]
        );
    }

    // Times already taken on a date, e.g. ['17:00', '17:15']
    public function bookedTimes(int $doctorId, string $date): array
    {
        $rows = $this->fetchAll(
            "SELECT TIME_FORMAT(appointment_time, '%H:%i') AS time
             FROM appointments
             WHERE doctor_id = ? AND appointment_date = ? AND status <> 'cancelled'",
            [$doctorId, $date]
        );

        return array_column($rows, 'time');
    }

    public function patientHasBooking(int $patientId, int $doctorId, string $date): bool
    {
        return $this->count(
            "patient_id = ? AND doctor_id = ? AND appointment_date = ? AND status <> 'cancelled'",
            [$patientId, $doctorId, $date]
        ) > 0;
    }

    /**
     * Appointment list used by the patient, doctor and admin pages.
     *
     * $filters keys (all optional): doctor_id, patient_id, status, date, period (today|upcoming|past), q
     */
    public function list(array $filters, int $page, int $perPage): array
    {
        $where  = [];
        $params = [];

        if (!empty($filters['doctor_id'])) {
            $where[]  = 'a.doctor_id = ?';
            $params[] = (int) $filters['doctor_id'];
        }
        if (!empty($filters['patient_id'])) {
            $where[]  = 'a.patient_id = ?';
            $params[] = (int) $filters['patient_id'];
        }
        if (!empty($filters['status'])) {
            $where[]  = 'a.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['date'])) {
            $where[]  = 'a.appointment_date = ?';
            $params[] = $filters['date'];
        }

        $period = $filters['period'] ?? '';
        if ($period === 'today') {
            $where[]  = 'a.appointment_date = ?';
            $params[] = today();
        } elseif ($period === 'upcoming') {
            $where[]  = 'a.appointment_date >= ?';
            $params[] = today();
        } elseif ($period === 'past') {
            $where[]  = 'a.appointment_date < ?';
            $params[] = today();
        }

        if (!empty($filters['q'])) {
            $where[]  = '(a.patient_name LIKE ? OR a.patient_phone LIKE ? OR a.appointment_no LIKE ?)';
            $params[] = '%' . $filters['q'] . '%';
            $params[] = '%' . $filters['q'] . '%';
            $params[] = '%' . $filters['q'] . '%';
        }

        $whereSql = $where ? implode(' AND ', $where) : '1';

        // Past lists show the newest first, everything else shows the nearest first
        $order = $period === 'past' ? 'DESC' : 'ASC';

        $rows = $this->fetchAll(
            "SELECT a.*, du.name AS doctor_name, d.image AS doctor_image, h.name AS hospital_name
             FROM appointments a
             JOIN doctors d ON d.id = a.doctor_id
             JOIN users du ON du.id = d.user_id
             JOIN hospitals h ON h.id = d.hospital_id
             WHERE $whereSql
             ORDER BY a.appointment_date $order, a.appointment_time $order " . $this->limit($page, $perPage),
            $params
        );

        $total = (int) $this->fetchValue("SELECT COUNT(*) FROM appointments a WHERE $whereSql", $params);

        return ['rows' => $rows, 'total' => $total];
    }

    // Dashboard numbers. Pass a doctor id or a patient id, or nothing for all appointments.
    public function stats(?int $doctorId = null, ?int $patientId = null): array
    {
        $where  = '1';
        $params = [today(), today()];

        if ($doctorId) {
            $where   .= ' AND doctor_id = ?';
            $params[] = $doctorId;
        }
        if ($patientId) {
            $where   .= ' AND patient_id = ?';
            $params[] = $patientId;
        }

        $row = $this->fetchOne(
            "SELECT COUNT(*) AS total,
                    SUM(appointment_date = ? AND status <> 'cancelled') AS today,
                    SUM(appointment_date >= ? AND status = 'confirmed') AS upcoming,
                    SUM(status = 'completed') AS completed,
                    SUM(status = 'cancelled') AS cancelled,
                    SUM(status = 'no_show') AS no_show
             FROM appointments
             WHERE $where",
            $params
        );

        // SUM() returns NULL when there are no rows
        return array_map('intval', $row);
    }

    // e.g. APT-261010-7K3Q9
    private function newAppointmentNo(string $date): string
    {
        do {
            $random = strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
            $number = 'APT-' . date('ymd', strtotime($date)) . '-' . $random;
        } while ($this->count('appointment_no = ?', [$number]) > 0);

        return $number;
    }
}
