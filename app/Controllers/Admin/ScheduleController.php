<?php

namespace App\Controllers\Admin;

use App\Models\Doctor;
use App\Models\Schedule;

// Admin manages the weekly schedule of any doctor
class ScheduleController extends AdminController
{
    private Schedule $schedules;

    public function __construct()
    {
        parent::__construct();
        $this->schedules = new Schedule();
    }

    public function index(): void
    {
        $doctors  = new Doctor();
        $doctorId = (int) $this->query('doctor_id');
        $doctor   = $doctorId ? $doctors->findDetails($doctorId) : null;

        $this->view('admin/schedules/index', [
            'title'     => 'Doctor Schedules',
            'doctors'   => $doctors->search([], 1, 1000, false)['rows'],
            'doctor'    => $doctor,
            'schedules' => $doctor ? $this->schedules->forDoctor($doctorId) : [],
        ]);
    }

    public function store(): void
    {
        $doctorId = (int) $this->input('doctor_id');
        if (!(new Doctor())->find($doctorId)) {
            abort(404);
        }

        $errors = $this->schedules->validate($_POST, $doctorId);
        if ($errors) {
            $this->backWithErrors($errors);
        }

        $this->schedules->create($doctorId, $_POST);
        $this->success('Schedule added.');
        $this->redirect('admin/schedules', ['doctor_id' => $doctorId]);
    }

    public function toggleStatus(int $id): void
    {
        $schedule = $this->findOrFail($id);
        $this->schedules->setStatus($id, $schedule['status'] === 'active' ? 'inactive' : 'active');

        $this->success('Schedule updated.');
        $this->redirect('admin/schedules', ['doctor_id' => $schedule['doctor_id']]);
    }

    public function destroy(int $id): void
    {
        $schedule = $this->findOrFail($id);
        $this->schedules->delete($id);

        $this->success('Schedule removed. Existing appointments are not affected.');
        $this->redirect('admin/schedules', ['doctor_id' => $schedule['doctor_id']]);
    }

    private function findOrFail(int $id): array
    {
        $schedule = $this->schedules->find($id);
        if (!$schedule) {
            abort(404);
        }

        return $schedule;
    }
}
