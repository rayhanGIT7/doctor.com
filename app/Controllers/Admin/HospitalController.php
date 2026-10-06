<?php

namespace App\Controllers\Admin;

use App\Core\Validator;
use App\Models\Hospital;

class HospitalController extends AdminController
{
    private Hospital $hospitals;

    public function __construct()
    {
        parent::__construct();
        $this->hospitals = new Hospital();
    }

    public function index(): void
    {
        $search  = $this->query('q');
        $status  = $this->query('status');
        $perPage = config('per_page');
        $result  = $this->hospitals->list($search, $status, $this->page(), $perPage);

        $this->view('admin/hospitals/index', [
            'title'     => 'Hospitals',
            'hospitals' => $result['rows'],
            'total'     => $result['total'],
            'perPage'   => $perPage,
            'search'    => $search,
            'status'    => $status,
        ]);
    }

    public function create(): void
    {
        $this->view('admin/hospitals/form', ['title' => 'Add Hospital', 'hospital' => null]);
    }

    public function store(): void
    {
        $data = $this->validated();
        $this->hospitals->create($data);

        $this->success('Hospital added successfully.');
        $this->redirect('admin/hospitals');
    }

    public function edit(int $id): void
    {
        $this->view('admin/hospitals/form', ['title' => 'Edit Hospital', 'hospital' => $this->findOrFail($id)]);
    }

    public function update(int $id): void
    {
        $this->findOrFail($id);
        $data = $this->validated($id);
        $this->hospitals->update($id, $data);

        $this->success('Hospital updated successfully.');
        $this->redirect('admin/hospitals');
    }

    public function toggleStatus(int $id): void
    {
        $hospital = $this->findOrFail($id);
        $status   = $hospital['status'] === 'active' ? 'inactive' : 'active';
        $this->hospitals->setStatus($id, $status);

        $this->success($hospital['name'] . ' is now ' . $status . '.');
        $this->back();
    }

    // A hospital can only be deleted when no doctor is linked to it
    public function destroy(int $id): void
    {
        $hospital = $this->findOrFail($id);

        if ($this->hospitals->doctorCount($id) > 0) {
            $this->error('This hospital has doctors. Deactivate it instead of deleting.');
        } else {
            $this->hospitals->delete($id);
            $this->success($hospital['name'] . ' was deleted.');
        }

        $this->redirect('admin/hospitals');
    }

    private function findOrFail(int $id): array
    {
        $hospital = $this->hospitals->find($id);
        if (!$hospital) {
            abort(404);
        }

        return $hospital;
    }

    // Validates the form and returns clean data (goes back with errors if invalid)
    private function validated(int $id = 0): array
    {
        $validator = new Validator($_POST);
        $validator->required('name', 'address', 'city', 'country', 'status')
            ->max('name', 150)
            ->max('address', 255)
            ->max('city', 80)
            ->max('country', 80)
            ->phone('phone')
            ->email('email')
            ->in('status', ['active', 'inactive']);

        if ($this->hospitals->nameExists($this->input('name'), $this->input('city'), $id)) {
            $validator->addError('name', 'A hospital with this name already exists in this city.');
        }
        if ($validator->fails()) {
            $this->backWithErrors($validator->errors());
        }

        return [
            'name'        => $this->input('name'),
            'address'     => $this->input('address'),
            'city'        => $this->input('city'),
            'country'     => $this->input('country'),
            'phone'       => $this->input('phone') ?: null,
            'email'       => $this->input('email') ?: null,
            'description' => $this->input('description') ?: null,
            'status'      => $this->input('status'),
        ];
    }
}
