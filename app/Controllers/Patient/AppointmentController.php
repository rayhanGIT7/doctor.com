<?php

namespace App\Controllers\Patient;

use App\Core\Auth;
use App\Models\Appointment;
use App\Services\AppointmentService;
use App\Services\BookingException;

class AppointmentController extends PatientController
{
    public function index(): void
    {
        $period  = $this->query('period', 'upcoming');
        $period  = in_array($period, ['upcoming', 'past', 'all'], true) ? $period : 'upcoming';
        $perPage = config('per_page');

        $result = (new Appointment())->list([
            'patient_id' => Auth::id(),
            'period'     => $period === 'all' ? '' : $period,
        ], $this->page(), $perPage);

        $this->view('patient/appointments', [
            'title'        => 'My Appointments',
            'period'       => $period,
            'appointments' => $result['rows'],
            'total'        => $result['total'],
            'perPage'      => $perPage,
        ]);
    }

    // Also used as the booking confirmation page
    public function show(int $id): void
    {
        $this->view('patient/appointment', [
            'title'       => 'Appointment Details',
            'appointment' => $this->findOwn($id),
        ]);
    }

    public function cancel(int $id): void
    {
        try {
            (new AppointmentService())->cancelByPatient($this->findOwn($id), Auth::id());
            $this->success('Your appointment has been cancelled.');
        } catch (BookingException $e) {
            $this->error($e->getMessage());
        }

        $this->redirect('my/appointments/' . $id);
    }

    // A patient may only see their own appointments
    private function findOwn(int $id): array
    {
        $appointment = (new Appointment())->findDetails($id);

        if (!$appointment || (int) $appointment['patient_id'] !== Auth::id()) {
            abort(404);
        }

        return $appointment;
    }
}
