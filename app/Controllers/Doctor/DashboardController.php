<?php

namespace App\Controllers\Doctor;

use App\Models\Appointment;

class DashboardController extends DoctorPanelController
{
    public function index(): void
    {
        $appointments = new Appointment();

        $this->view('doctor/dashboard', [
            'title'  => 'Dashboard',
            'doctor' => $this->doctor,
            'stats'  => $appointments->stats($this->doctorId()),
            'today'  => $appointments->list(['doctor_id' => $this->doctorId(), 'period' => 'today'], 1, 50)['rows'],
        ]);
    }
}
