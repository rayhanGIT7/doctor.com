<?php

namespace App\Models;

use App\Core\Model;
use App\Core\Validator;

class Schedule extends Model
{
    protected string $table = 'doctor_schedules';

    public const SLOT_OPTIONS = [10, 15, 20, 30, 45, 60];

    public function forDoctor(int $doctorId, bool $activeOnly = false): array
    {
        $sql = 'SELECT * FROM doctor_schedules WHERE doctor_id = ?';
        if ($activeOnly) {
            $sql .= " AND status = 'active'";
        }

        return $this->fetchAll($sql . ' ORDER BY day_of_week, start_time', [$doctorId]);
    }

    public function activeForDay(int $doctorId, int $dayOfWeek): array
    {
        return $this->fetchAll(
            "SELECT * FROM doctor_schedules
             WHERE doctor_id = ? AND day_of_week = ? AND status = 'active'
             ORDER BY start_time",
            [$doctorId, $dayOfWeek]
        );
    }

    // Finds a schedule only if it belongs to the given doctor
    public function findForDoctor(int $id, int $doctorId): ?array
    {
        return $this->fetchOne('SELECT * FROM doctor_schedules WHERE id = ? AND doctor_id = ?', [$id, $doctorId]);
    }

    /**
     * Validates a new schedule from the form. Returns an array of errors (empty = valid).
     * Used by both the admin panel and the doctor panel.
     */
    public function validate(array $input, int $doctorId): array
    {
        $validator = new Validator($input);
        $validator->required('day_of_week', 'start_time', 'end_time', 'slot_minutes')
            ->in('day_of_week', ['0', '1', '2', '3', '4', '5', '6'])
            ->time('start_time')
            ->time('end_time')
            ->in('slot_minutes', array_map('strval', self::SLOT_OPTIONS));

        if ($validator->fails()) {
            return $validator->errors();
        }

        $start = $input['start_time'];
        $end   = $input['end_time'];

        if ($end <= $start) {
            return ['end_time' => 'End time must be after start time.'];
        }
        if ($this->hasOverlap($doctorId, (int) $input['day_of_week'], $start, $end)) {
            return ['start_time' => 'This time overlaps with another schedule on the same day.'];
        }

        return [];
    }

    public function create(int $doctorId, array $input): int
    {
        return $this->insert(
            'INSERT INTO doctor_schedules (doctor_id, day_of_week, start_time, end_time, slot_minutes) VALUES (?, ?, ?, ?, ?)',
            [$doctorId, (int) $input['day_of_week'], $input['start_time'], $input['end_time'], (int) $input['slot_minutes']]
        );
    }

    public function delete(int $id): void
    {
        $this->execute('DELETE FROM doctor_schedules WHERE id = ?', [$id]);
    }

    // Two time ranges overlap when one starts before the other ends
    private function hasOverlap(int $doctorId, int $day, string $start, string $end): bool
    {
        return $this->count(
            'doctor_id = ? AND day_of_week = ? AND start_time < ? AND end_time > ?',
            [$doctorId, $day, $end, $start]
        ) > 0;
    }
}
