<?php

namespace App\Controllers\Patient;

use App\Core\Auth;
use App\Models\Appointment;

class DashboardController extends PatientController
{
    public function index(): void
    {
        $appointments = new Appointment();

        $this->view('patient/dashboard', [
            'title'    => 'My Dashboard',
            'stats'    => $appointments->stats(null, Auth::id()),
            'upcoming' => $appointments->list(['patient_id' => Auth::id(), 'period' => 'upcoming', 'status' => 'confirmed'], 1, 5)['rows'],
        ]);
    }
}
