<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Validator;
use App\Models\Doctor;
use App\Services\AppointmentService;
use App\Services\BookingException;

// Patient books a slot: form -> confirm -> confirmation page
class BookingController extends Controller
{
    public function __construct()
    {
        Auth::requireRole('patient');
    }

    public function create(int $doctorId): void
    {
        $doctors = new Doctor();
        $doctor  = $doctors->findDetails($doctorId);

        if (!$doctor || !$doctors->isBookable($doctor)) {
            abort(404);
        }

        $date = $this->query('date');
        $time = $this->query('time');

        // Make sure the slot is still free before showing the form
        $service = new AppointmentService();
        if (!in_array($time, $service->availableSlots($doctorId, $date), true)) {
            $this->error('That time slot is not available any more. Please choose another one.');
            $this->redirect('doctors/' . $doctorId, ['date' => $date]);
        }

        $this->view('booking/create', [
            'title'  => 'Book Appointment',
            'doctor' => $doctor,
            'date'   => $date,
            'time'   => $time,
            'user'   => Auth::user(),
        ]);
    }

    public function store(int $doctorId): void
    {
        $validator = new Validator($_POST);
        $validator->required('date', 'time', 'patient_name', 'patient_phone')
            ->date('date')
            ->time('time')
            ->max('patient_name', 100)
            ->phone('patient_phone')
            ->max('note', 500);

        if ($validator->fails()) {
            $this->backWithErrors($validator->errors());
        }

        try {
            $appointmentId = (new AppointmentService())->book(Auth::id(), $doctorId, [
                'date'          => $this->input('date'),
                'time'          => $this->input('time'),
                'patient_name'  => $this->input('patient_name'),
                'patient_phone' => $this->input('patient_phone'),
                'note'          => $this->input('note'),
            ]);
        } catch (BookingException $e) {
            $this->error($e->getMessage());
            $this->redirect('doctors/' . $doctorId, ['date' => $this->input('date')]);
        }

        $this->success('Your appointment is confirmed.');
        $this->redirect('my/appointments/' . $appointmentId);
    }
}
