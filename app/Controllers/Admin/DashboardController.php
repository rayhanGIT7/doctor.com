<?php

namespace App\Controllers\Admin;

use App\Models\Appointment;
use App\Models\Category;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\User;

class DashboardController extends AdminController
{
    public function index(): void
    {
        $appointments = new Appointment();

        $this->view('admin/dashboard', [
            'title'      => 'Dashboard',
            'stats'      => $appointments->stats(),
            'doctors'    => (new Doctor())->count("status = 'active'"),
            'hospitals'  => (new Hospital())->count("status = 'active'"),
            'categories' => (new Category())->count("status = 'active'"),
            'patients'   => (new User())->count("role = 'patient'"),
            'today'      => $appointments->list(['period' => 'today'], 1, 10)['rows'],
        ]);
    }
}
