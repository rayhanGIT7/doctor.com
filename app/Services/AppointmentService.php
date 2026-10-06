<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Schedule;
use App\Models\User;
use DateTime;
use PDOException;

/**
 * All appointment rules live here:
 *  - which time slots are free for a doctor on a date
 *  - booking a slot (with every check)
 *  - changing an appointment status
 */
class AppointmentService
{
    private Doctor $doctors;
    private Schedule $schedules;
    private Appointment $appointments;
    private User $users;

    public function __construct()
    {
        $this->doctors      = new Doctor();
        $this->schedules    = new Schedule();
        $this->appointments = new Appointment();
        $this->users        = new User();
    }

    // Is the date in the bookable range (today .. today + booking_days)?
    public function isBookableDate(string $date): bool
    {
        $parsed = DateTime::createFromFormat('Y-m-d', $date);
        if (!$parsed || $parsed->format('Y-m-d') !== $date) {
            return false;
        }

        $lastDate = date('Y-m-d', strtotime('+' . config('booking_days') . ' days'));

        return $date >= today() && $date <= $lastDate;
    }

    // Every slot from the doctor's schedule on that date, e.g. ['17:00', '17:15', ...]
    public function scheduleSlots(int $doctorId, string $date): array
    {
        if (!$this->isBookableDate($date)) {
            return [];
        }

        $dayOfWeek = (int) date('w', strtotime($date));
        $slots     = [];

        foreach ($this->schedules->activeForDay($doctorId, $dayOfWeek) as $schedule) {
            $time = strtotime($schedule['start_time']);
            $end  = strtotime($schedule['end_time']);
            $step = $schedule['slot_minutes'] * 60;

            // The last slot must finish before the schedule ends
            while ($time + $step <= $end) {
                $slots[] = date('H:i', $time);
                $time += $step;
            }
        }

        sort($slots);

        return array_values(array_unique($slots));
    }

    // Slots a patient can still pick: not booked and not in the past
    public function availableSlots(int $doctorId, string $date): array
    {
        $booked = $this->appointments->bookedTimes($doctorId, $date);
        $slots  = array_diff($this->scheduleSlots($doctorId, $date), $booked);

        if ($date === today()) {
            $now   = date('H:i');
            $slots = array_filter($slots, fn ($slot) => $slot > $now);
        }

        return array_values($slots);
    }

    // Next few days that have a schedule, with how many slots are free (for the doctor page)
    public function upcomingDays(int $doctorId, int $days = 14): array
    {
        $result = [];

        for ($i = 0; $i < $days; $i++) {
            $date = date('Y-m-d', strtotime("+$i days"));

            if ($this->scheduleSlots($doctorId, $date)) {
                $result[] = ['date' => $date, 'free' => count($this->availableSlots($doctorId, $date))];
            }
        }

        return $result;
    }

    /**
     * Books an appointment and returns the new appointment id.
     * $input keys: date, time, patient_name, patient_phone, note
     *
     * @throws BookingException when any rule fails
     */
    public function book(int $patientId, int $doctorId, array $input): int
    {
        $date = $input['date'];
        $time = $input['time'];

        // 1 + 2. Doctor exists and is active
        $doctor = $this->doctors->findDetails($doctorId);
        if (!$doctor) {
            throw new BookingException('Doctor not found.');
        }
        if (!$this->doctors->isBookable($doctor)) {
            throw new BookingException('This doctor is not taking appointments right now.');
        }

        // 3. Doctor works on that date
        $slots = $this->scheduleSlots($doctorId, $date);
        if (!$slots) {
            throw new BookingException('The doctor is not available on this date.');
        }

        // 4. The time is a real slot from the schedule
        if (!in_array($time, $slots, true)) {
            throw new BookingException('Please select a valid time slot.');
        }
        if ($date === today() && $time <= date('H:i')) {
            throw new BookingException('This time has already passed. Please choose a later slot.');
        }

        // 5. The slot is still free
        if (in_array($time, $this->appointments->bookedTimes($doctorId, $date), true)) {
            throw new BookingException('This slot is already booked. Please choose another time.');
        }

        // 6. The user is a valid, active patient without a booking with this doctor on that day
        $patient = $this->users->find($patientId);
        if (!$patient || $patient['role'] !== 'patient' || $patient['status'] !== 'active') {
            throw new BookingException('Only active patient accounts can book appointments.');
        }
        if ($this->appointments->patientHasBooking($patientId, $doctorId, $date)) {
            throw new BookingException('You already have an appointment with this doctor on this date.');
        }

        // 7. Save. The unique key on (doctor, date, time) is the final guard:
        // if two people click "confirm" at the same moment, only one insert succeeds.
        try {
            return $this->appointments->create([
                'patient_id'       => $patientId,
                'doctor_id'        => $doctorId,
                'appointment_date' => $date,
                'appointment_time' => $time,
                'patient_name'     => $input['patient_name'],
                'patient_phone'    => $input['patient_phone'],
                'note'             => $input['note'] !== '' ? $input['note'] : null,
                'fee'              => $doctor['consultation_fee'],
            ]);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                throw new BookingException('Sorry, this slot was just booked by someone else. Please choose another time.');
            }
            throw $e;
        }
    }

    /**
     * Changes the status of an appointment.
     * $role is "doctor" or "admin". Admin can correct any status; doctor can only close a confirmed one.
     *
     * @throws BookingException when the change is not allowed
     */
    public function changeStatus(array $appointment, string $status, string $role): void
    {
        if (!in_array($status, Appointment::STATUSES, true)) {
            throw new BookingException('Invalid status.');
        }
        if ($status === $appointment['status']) {
            return;
        }

        if ($role === 'doctor') {
            if ($appointment['status'] !== 'confirmed') {
                throw new BookingException('Only confirmed appointments can be updated.');
            }
            if ($status === 'confirmed') {
                throw new BookingException('Invalid status.');
            }
        }

        if (in_array($status, ['completed', 'no_show'], true) && $appointment['appointment_date'] > today()) {
            throw new BookingException('A future appointment cannot be marked as ' . status_label($status) . '.');
        }

        try {
            $this->appointments->setStatus((int) $appointment['id'], $status);
        } catch (PDOException $e) {
            // Re-opening a cancelled appointment whose slot is now taken by someone else
            if ($e->getCode() === '23000') {
                throw new BookingException('This slot is already booked by another patient.');
            }
            throw $e;
        }
    }

    // A patient can cancel their own confirmed appointment before it starts
    public function cancelByPatient(array $appointment, int $patientId): void
    {
        if ((int) $appointment['patient_id'] !== $patientId) {
            throw new BookingException('Appointment not found.');
        }
        if ($appointment['status'] !== 'confirmed') {
            throw new BookingException('Only confirmed appointments can be cancelled.');
        }

        $startsAt = strtotime($appointment['appointment_date'] . ' ' . $appointment['appointment_time']);
        if ($startsAt <= time()) {
            throw new BookingException('Past appointments cannot be cancelled.');
        }

        $this->appointments->setStatus((int) $appointment['id'], 'cancelled');
    }
}
