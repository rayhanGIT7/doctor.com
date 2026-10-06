<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Category;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\Schedule;
use App\Services\AppointmentService;

// Public doctor search and doctor details
class DoctorController extends Controller
{
    public function index(): void
    {
        $filters = [
            'q'        => $this->query('q'),
            'category' => $this->query('category'),
            'hospital' => $this->query('hospital'),
            'city'     => $this->query('city'),
            'gender'   => $this->query('gender'),
            'date'     => $this->query('date'),
        ];

        // Ignore a broken date instead of showing an error
        if ($filters['date'] !== '' && !strtotime($filters['date'])) {
            $filters['date'] = '';
        }

        $perPage   = 9;
        $result    = (new Doctor())->search($filters, $this->page(), $perPage);
        $hospitals = new Hospital();

        $this->view('doctors/index', [
            'title'      => 'Find Doctors',
            'filters'    => $filters,
            'doctors'    => $result['rows'],
            'total'      => $result['total'],
            'perPage'    => $perPage,
            'categories' => (new Category())->active(),
            'hospitals'  => $hospitals->active(),
            'cities'     => $hospitals->cities(),
        ]);
    }

    public function show(int $id): void
    {
        $doctors = new Doctor();
        $doctor  = $doctors->findDetails($id);

        if (!$doctor || !$doctors->isBookable($doctor)) {
            abort(404);
        }

        $service = new AppointmentService();
        $days    = $service->upcomingDays($id);

        // Selected date: from the URL, otherwise the first day that has free slots
        $date = $this->query('date');
        if (!$service->isBookableDate($date)) {
            $date = $days[0]['date'] ?? today();
            foreach ($days as $day) {
                if ($day['free'] > 0) {
                    $date = $day['date'];
                    break;
                }
            }
        }

        $this->view('doctors/show', [
            'title'      => $doctor['name'],
            'doctor'     => $doctor,
            'categories' => $doctors->categories($id),
            'schedules'  => (new Schedule())->forDoctor($id, true),
            'days'       => $days,
            'date'       => $date,
            'slots'      => $service->availableSlots($id, $date),
        ]);
    }
}
