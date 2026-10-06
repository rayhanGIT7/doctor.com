<?php

namespace App\Controllers\Admin;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Services\AppointmentService;
use App\Services\BookingException;

class AppointmentController extends AdminController
{
    public function index(): void
    {
        $filters = [
            'q'         => $this->query('q'),
            'doctor_id' => $this->query('doctor_id'),
            'status'    => $this->query('status'),
            'date'      => $this->query('date'),
            'period'    => $this->query('period'),
        ];
        $perPage = config('per_page');
        $result  = (new Appointment())->list($filters, $this->page(), $perPage);

        $this->view('admin/appointments/index', [
            'title'        => 'Appointments',
            'appointments' => $result['rows'],
            'total'        => $result['total'],
            'perPage'      => $perPage,
            'filters'      => $filters,
            'doctors'      => (new Doctor())->search([], 1, 1000, false)['rows'],
        ]);
    }

    public function updateStatus(int $id): void
    {
        $appointment = (new Appointment())->find($id);
        if (!$appointment) {
            abort(404);
        }

        try {
            (new AppointmentService())->changeStatus($appointment, $this->input('status'), 'admin');
            $this->success('Appointment ' . $appointment['appointment_no'] . ' updated.');
        } catch (BookingException $e) {
            $this->error($e->getMessage());
        }

        $this->back();
    }
}
