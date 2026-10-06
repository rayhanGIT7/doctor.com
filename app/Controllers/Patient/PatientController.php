<?php

namespace App\Controllers\Patient;

use App\Core\Auth;
use App\Core\Controller;

// Every controller in this folder extends this one, so only patients can reach them
abstract class PatientController extends Controller
{
    public function __construct()
    {
        Auth::requireRole('patient');
    }
}
