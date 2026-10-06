<?php

namespace App\Controllers\Doctor;

use App\Core\Upload;
use App\Core\Validator;
use App\Models\Doctor;

class ProfileController extends DoctorPanelController
{
    public function edit(): void
    {
        $this->view('doctor/profile', [
            'title'      => 'My Profile',
            'doctor'     => $this->doctor,
            'categories' => (new Doctor())->categories($this->doctorId()),
        ]);
    }

    public function update(): void
    {
        $validator = new Validator($_POST);
        $validator->required('qualification')
            ->max('qualification', 255)
            ->max('chamber_info', 255)
            ->max('bio', 2000);

        if (Upload::hasFile('image')) {
            $imageError = Upload::validateImage('image');
            if ($imageError) {
                $validator->addError('image', $imageError);
            }
        }
        if ($validator->fails()) {
            $this->backWithErrors($validator->errors());
        }

        $image = $this->doctor['image'];
        if (Upload::hasFile('image')) {
            $image = Upload::saveImage('image', 'doctors');
            Upload::delete($this->doctor['image']);
        }

        (new Doctor())->updateProfile($this->doctorId(), [
            'qualification' => $this->input('qualification'),
            'chamber_info'  => $this->input('chamber_info') ?: null,
            'bio'           => $this->input('bio') ?: null,
            'image'         => $image,
        ]);

        $this->success('Profile updated successfully.');
        $this->redirect('doctor/profile');
    }
}
