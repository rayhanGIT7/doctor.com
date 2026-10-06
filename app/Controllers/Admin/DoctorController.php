<?php

namespace App\Controllers\Admin;

use App\Core\Upload;
use App\Core\Validator;
use App\Models\Category;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\User;

class DoctorController extends AdminController
{
    private Doctor $doctors;

    public function __construct()
    {
        parent::__construct();
        $this->doctors = new Doctor();
    }

    public function index(): void
    {
        $filters = [
            'q'        => $this->query('q'),
            'hospital' => $this->query('hospital'),
            'category' => $this->query('category'),
            'status'   => $this->query('status'),
        ];
        $perPage = config('per_page');
        $result  = $this->doctors->search($filters, $this->page(), $perPage, false);

        $this->view('admin/doctors/index', [
            'title'      => 'Doctors',
            'doctors'    => $result['rows'],
            'total'      => $result['total'],
            'perPage'    => $perPage,
            'filters'    => $filters,
            'hospitals'  => (new Hospital())->all(),
            'categories' => (new Category())->all(),
        ]);
    }

    public function create(): void
    {
        $this->showForm('Add Doctor', null, []);
    }

    public function store(): void
    {
        $this->validate(null);

        $user = $this->userData();
        $user['password'] = $_POST['password'];

        $doctor = $this->doctorData();
        $doctor['image'] = Upload::hasFile('image') ? Upload::saveImage('image', 'doctors') : null;

        $this->doctors->createWithUser($user, $doctor, $this->categoryIds());

        $this->success('Doctor added successfully. They can now log in with their email or phone.');
        $this->redirect('admin/doctors');
    }

    public function edit(int $id): void
    {
        $doctor = $this->findOrFail($id);
        $this->showForm('Edit Doctor', $doctor, $this->doctors->categoryIds($id));
    }

    public function update(int $id): void
    {
        $current = $this->findOrFail($id);
        $this->validate($current);

        $doctor = $this->doctorData();
        $doctor['image'] = $current['image'];

        if (Upload::hasFile('image')) {
            $doctor['image'] = Upload::saveImage('image', 'doctors');
            Upload::delete($current['image']);
        }

        $this->doctors->updateWithUser($current, $this->userData(), $doctor, $this->categoryIds());

        // Optional password reset by admin
        if ($this->input('password') !== '') {
            (new User())->updatePassword((int) $current['user_id'], $_POST['password']);
        }

        $this->success('Doctor updated successfully.');
        $this->redirect('admin/doctors');
    }

    public function toggleStatus(int $id): void
    {
        $doctor = $this->findOrFail($id);
        $status = $doctor['status'] === 'active' ? 'inactive' : 'active';
        $this->doctors->setActive($doctor, $status);

        $this->success($doctor['name'] . ' is now ' . $status . '.');
        $this->back();
    }

    private function showForm(string $title, ?array $doctor, array $categoryIds): void
    {
        $this->view('admin/doctors/form', [
            'title'       => $title,
            'doctor'      => $doctor,
            'categoryIds' => $categoryIds,
            'hospitals'   => (new Hospital())->all(),
            'categories'  => (new Category())->all(),
        ]);
    }

    private function findOrFail(int $id): array
    {
        $doctor = $this->doctors->findDetails($id);
        if (!$doctor) {
            abort(404);
        }

        return $doctor;
    }

    // $current is null when adding a new doctor
    private function validate(?array $current): void
    {
        $users     = new User();
        $userId    = $current ? (int) $current['user_id'] : 0;
        $validator = new Validator($_POST);

        $validator->required('name', 'email', 'phone', 'gender', 'hospital_id', 'qualification', 'experience_years', 'consultation_fee')
            ->max('name', 100)
            ->email('email')
            ->phone('phone')
            ->in('gender', ['male', 'female', 'other'])
            ->max('qualification', 255)
            ->number('experience_years', 0, 70)
            ->number('consultation_fee', 0, 100000)
            ->max('chamber_info', 255);

        // Password is required for a new doctor, optional when editing
        if (!$current) {
            $validator->required('password');
        }
        $validator->min('password', 6);

        if ($users->emailExists($this->input('email'), $userId)) {
            $validator->addError('email', 'This email is already used by another account.');
        }
        if ($users->phoneExists($this->input('phone'), $userId)) {
            $validator->addError('phone', 'This phone number is already used by another account.');
        }
        if (!(new Hospital())->find((int) $this->input('hospital_id'))) {
            $validator->addError('hospital_id', 'Please select a hospital.');
        }
        if (!$this->categoryIds()) {
            $validator->addError('categories', 'Please select at least one specialization.');
        }
        if (Upload::hasFile('image')) {
            $imageError = Upload::validateImage('image');
            if ($imageError) {
                $validator->addError('image', $imageError);
            }
        }

        if ($validator->fails()) {
            $this->backWithErrors($validator->errors());
        }
    }

    private function userData(): array
    {
        return [
            'name'   => $this->input('name'),
            'email'  => $this->input('email'),
            'phone'  => $this->input('phone'),
            'gender' => $this->input('gender'),
        ];
    }

    private function doctorData(): array
    {
        return [
            'hospital_id'      => (int) $this->input('hospital_id'),
            'qualification'    => $this->input('qualification'),
            'experience_years' => (int) $this->input('experience_years'),
            'consultation_fee' => (float) $this->input('consultation_fee'),
            'chamber_info'     => $this->input('chamber_info') ?: null,
            'bio'              => $this->input('bio') ?: null,
        ];
    }

    // Only ids of categories that really exist
    private function categoryIds(): array
    {
        $selected = array_map('intval', (array) ($_POST['categories'] ?? []));
        $existing = array_map('intval', array_column((new Category())->all(), 'id'));

        return array_values(array_intersect($selected, $existing));
    }
}
