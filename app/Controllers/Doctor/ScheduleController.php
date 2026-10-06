<?php

namespace App\Controllers\Doctor;

use App\Models\Schedule;

// Doctor manages their own weekly schedule
class ScheduleController extends DoctorPanelController
{
    private Schedule $schedules;

    public function __construct()
    {
        parent::__construct();
        $this->schedules = new Schedule();
    }

    public function index(): void
    {
        $this->view('doctor/schedule', [
            'title'     => 'My Schedule',
            'schedules' => $this->schedules->forDoctor($this->doctorId()),
        ]);
    }

    public function store(): void
    {
        $errors = $this->schedules->validate($_POST, $this->doctorId());
        if ($errors) {
            $this->backWithErrors($errors);
        }

        $this->schedules->create($this->doctorId(), $_POST);
        $this->success('Schedule added.');
        $this->redirect('doctor/schedule');
    }

    public function toggleStatus(int $id): void
    {
        $schedule = $this->findOwn($id);
        $this->schedules->setStatus($id, $schedule['status'] === 'active' ? 'inactive' : 'active');

        $this->success('Schedule updated.');
        $this->redirect('doctor/schedule');
    }

    public function destroy(int $id): void
    {
        $this->findOwn($id);
        $this->schedules->delete($id);

        $this->success('Schedule removed. Existing appointments are not affected.');
        $this->redirect('doctor/schedule');
    }

    private function findOwn(int $id): array
    {
        $schedule = $this->schedules->findForDoctor($id, $this->doctorId());
        if (!$schedule) {
            abort(404);
        }

        return $schedule;
    }
}
