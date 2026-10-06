<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Category;
use App\Models\Doctor;
use App\Models\Hospital;

class HomeController extends Controller
{
    public function index(): void
    {
        $hospitals = new Hospital();

        $this->view('home/index', [
            'title'      => 'Find a doctor and book an appointment',
            'categories' => (new Category())->active(),
            'hospitals'  => $hospitals->active(),
            'cities'     => $hospitals->cities(),
            'doctors'    => (new Doctor())->search([], 1, 6)['rows'],
        ]);
    }
}
