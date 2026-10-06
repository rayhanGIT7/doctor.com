<?php

namespace App\Controllers\Doctor;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Doctor;

// Every controller in this folder extends this one, so only doctors can reach them.
// $this->doctor is the logged-in doctor's profile.
abstract class DoctorPanelController extends Controller
{
    protected string $layout = 'panel';
    protected array $doctor;

    public function __construct()
    {
        Auth::requireRole('doctor');

        $doctor = (new Doctor())->findByUserId(Auth::id());
        if (!$doctor) {
            abort(403, 'No doctor profile is linked to this account.');
        }

        $this->doctor = $doctor;
    }

    protected function doctorId(): int
    {
        return (int) $this->doctor['id'];
    }
}
