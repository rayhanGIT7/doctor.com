<?php

namespace App\Controllers\Patient;

use App\Core\Auth;
use App\Core\Validator;
use App\Models\User;

class ProfileController extends PatientController
{
    public function edit(): void
    {
        $this->view('patient/profile', [
            'title' => 'My Profile',
            'user'  => Auth::user(),
        ]);
    }

    public function update(): void
    {
        $users     = new User();
        $validator = new Validator($_POST);
        $validator->required('name', 'email', 'phone')
            ->max('name', 100)
            ->email('email')
            ->phone('phone')
            ->in('gender', ['male', 'female', 'other'])
            ->date('date_of_birth')
            ->max('address', 255);

        if ($users->emailExists($this->input('email'), Auth::id())) {
            $validator->addError('email', 'This email is already used by another account.');
        }
        if ($users->phoneExists($this->input('phone'), Auth::id())) {
            $validator->addError('phone', 'This phone number is already used by another account.');
        }
        if ($this->input('date_of_birth') > today()) {
            $validator->addError('date_of_birth', 'Date of birth cannot be in the future.');
        }
        if ($validator->fails()) {
            $this->backWithErrors($validator->errors());
        }

        $users->updateProfile(Auth::id(), [
            'name'          => $this->input('name'),
            'email'         => $this->input('email'),
            'phone'         => $this->input('phone'),
            'gender'        => $this->input('gender') ?: null,
            'date_of_birth' => $this->input('date_of_birth') ?: null,
            'address'       => $this->input('address') ?: null,
        ]);

        $this->success('Profile updated successfully.');
        $this->redirect('my/profile');
    }
}
