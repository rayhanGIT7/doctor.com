<?php

namespace App\Controllers\Doctor;

use App\Models\Appointment;
use App\Services\AppointmentService;
use App\Services\BookingException;

class AppointmentController extends DoctorPanelController
{
    public function index(): void
    {
        $period = $this->query('period', 'upcoming');
        if (!in_array($period, ['today', 'upcoming', 'past', 'all'], true)) {
            $period = 'upcoming';
        }

        $filters = [
            'doctor_id' => $this->doctorId(),
            'q'         => $this->query('q'),
            'status'    => $this->query('status'),
            'date'      => $this->query('date'),
            'period'    => $period === 'all' ? '' : $period,
        ];
        $perPage = config('per_page');
        $result  = (new Appointment())->list($filters, $this->page(), $perPage);

        $this->view('doctor/appointments', [
            'title'        => 'My Appointments',
            'period'       => $period,
            'appointments' => $result['rows'],
            'total'        => $result['total'],
            'perPage'      => $perPage,
            'filters'      => $filters,
        ]);
    }

    public function updateStatus(int $id): void
    {
        $appointment = (new Appointment())->find($id);

        // A doctor may only update their own appointments
        if (!$appointment || (int) $appointment['doctor_id'] !== $this->doctorId()) {
            abort(404);
        }

        try {
            (new AppointmentService())->changeStatus($appointment, $this->input('status'), 'doctor');
            $this->success('Appointment ' . $appointment['appointment_no'] . ' marked as ' . status_label($this->input('status')) . '.');
        } catch (BookingException $e) {
            $this->error($e->getMessage());
        }

        $this->back();
    }
}
