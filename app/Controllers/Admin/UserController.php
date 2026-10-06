<?php

namespace App\Controllers\Admin;

use App\Models\User;

// Patient accounts
class UserController extends AdminController
{
    public function index(): void
    {
        $search  = $this->query('q');
        $perPage = config('per_page');
        $result  = (new User())->patients($search, $this->page(), $perPage);

        $this->view('admin/users/index', [
            'title'   => 'Patients',
            'users'   => $result['rows'],
            'total'   => $result['total'],
            'perPage' => $perPage,
            'search'  => $search,
        ]);
    }

    public function toggleStatus(int $id): void
    {
        $users = new User();
        $user  = $users->find($id);

        // Only patient accounts are managed here
        if (!$user || $user['role'] !== 'patient') {
            abort(404);
        }

        $status = $user['status'] === 'active' ? 'inactive' : 'active';
        $users->setStatus($id, $status);

        $this->success($user['name'] . ' is now ' . $status . '.');
        $this->back();
    }
}
