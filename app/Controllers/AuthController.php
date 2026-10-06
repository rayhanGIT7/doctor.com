<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin(): void
    {
        if (Auth::check()) {
            $this->redirect(Auth::homePath());
        }

        $this->view('auth/login', ['title' => 'Login']);
    }

    public function login(): void
    {
        $validator = new Validator($_POST);
        $validator->required('login', 'password');

        if ($validator->fails()) {
            $this->backWithErrors($validator->errors());
        }

        $user = (new User())->findByLogin($this->input('login'));

        // Same message for "no user" and "wrong password" so emails/phones can't be guessed
        if (!$user || !password_verify($_POST['password'], $user['password'])) {
            $this->backWithErrors(['login' => 'Invalid email/phone or password.']);
        }
        if ($user['status'] !== 'active') {
            $this->backWithErrors(['login' => 'Your account is inactive. Please contact support.']);
        }

        Auth::login($user);
        $this->success('Welcome back, ' . $user['name'] . '!');
        $this->redirectAfterLogin();
    }

    public function showRegister(): void
    {
        if (Auth::check()) {
            $this->redirect(Auth::homePath());
        }

        $this->view('auth/register', ['title' => 'Create Account']);
    }

    public function register(): void
    {
        $users     = new User();
        $validator = new Validator($_POST);
        $validator->required('name', 'email', 'phone', 'password')
            ->max('name', 100)
            ->email('email')
            ->max('email', 150)
            ->phone('phone')
            ->in('gender', ['male', 'female', 'other'])
            ->min('password', 6)
            ->same('password_confirmation', 'password');

        if ($users->emailExists($this->input('email'))) {
            $validator->addError('email', 'This email is already registered.');
        }
        if ($users->phoneExists($this->input('phone'))) {
            $validator->addError('phone', 'This phone number is already registered.');
        }
        if ($validator->fails()) {
            $this->backWithErrors($validator->errors());
        }

        $id = $users->create([
            'role'     => 'patient',
            'name'     => $this->input('name'),
            'email'    => $this->input('email'),
            'phone'    => $this->input('phone'),
            'gender'   => $this->input('gender') ?: null,
            'password' => $_POST['password'],
        ]);

        Auth::login($users->find($id));
        $this->success('Your account has been created.');
        $this->redirectAfterLogin();
    }

    public function logout(): void
    {
        Auth::logout();
        $this->success('You have been logged out.');
        $this->redirect('/');
    }

    // Any logged-in user can change their password
    public function changePassword(): void
    {
        Auth::requireLogin();

        $validator = new Validator($_POST);
        $validator->required('current_password', 'password')
            ->min('password', 6)
            ->same('password_confirmation', 'password');

        if (!password_verify($_POST['current_password'] ?? '', Auth::user()['password'])) {
            $validator->addError('current_password', 'Current password is incorrect.');
        }
        if ($validator->fails()) {
            $this->backWithErrors($validator->errors());
        }

        (new User())->updatePassword(Auth::id(), $_POST['password']);
        $this->success('Password changed successfully.');
        $this->back();
    }

    // Go to the page the user wanted before login, or to their dashboard
    private function redirectAfterLogin(): void
    {
        $intended = Session::get('intended');
        Session::remove('intended');

        // Only allow local paths like "/doctors/1/book?..." (blocks "//evil.com")
        if ($intended && $intended[0] === '/' && substr($intended, 0, 2) !== '//') {
            header('Location: ' . $intended);
            exit;
        }

        $this->redirect(Auth::homePath());
    }
}
