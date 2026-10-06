<?php

use App\Controllers\Admin;
use App\Controllers\AuthController;
use App\Controllers\BookingController;
use App\Controllers\Doctor;
use App\Controllers\DoctorController;
use App\Controllers\HomeController;
use App\Controllers\Patient;

/** @var App\Core\Router $router */

// ---------- Public website ----------
$router->get('/', [HomeController::class, 'index']);
$router->get('/doctors', [DoctorController::class, 'index']);
$router->get('/doctors/{id}', [DoctorController::class, 'show']);
$router->get('/doctors/{id}/book', [BookingController::class, 'create']);
$router->post('/doctors/{id}/book', [BookingController::class, 'store']);

// ---------- Auth ----------
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);
$router->post('/logout', [AuthController::class, 'logout']);
$router->post('/account/password', [AuthController::class, 'changePassword']);

// ---------- Patient ----------
$router->get('/my/dashboard', [Patient\DashboardController::class, 'index']);
$router->get('/my/appointments', [Patient\AppointmentController::class, 'index']);
$router->get('/my/appointments/{id}', [Patient\AppointmentController::class, 'show']);
$router->post('/my/appointments/{id}/cancel', [Patient\AppointmentController::class, 'cancel']);
$router->get('/my/profile', [Patient\ProfileController::class, 'edit']);
$router->post('/my/profile', [Patient\ProfileController::class, 'update']);

// ---------- Admin panel ----------
$router->get('/admin', [Admin\DashboardController::class, 'index']);

$router->get('/admin/hospitals', [Admin\HospitalController::class, 'index']);
$router->get('/admin/hospitals/create', [Admin\HospitalController::class, 'create']);
$router->post('/admin/hospitals', [Admin\HospitalController::class, 'store']);
$router->get('/admin/hospitals/{id}/edit', [Admin\HospitalController::class, 'edit']);
$router->post('/admin/hospitals/{id}', [Admin\HospitalController::class, 'update']);
$router->post('/admin/hospitals/{id}/status', [Admin\HospitalController::class, 'toggleStatus']);
$router->post('/admin/hospitals/{id}/delete', [Admin\HospitalController::class, 'destroy']);

$router->get('/admin/categories', [Admin\CategoryController::class, 'index']);
$router->get('/admin/categories/create', [Admin\CategoryController::class, 'create']);
$router->post('/admin/categories', [Admin\CategoryController::class, 'store']);
$router->get('/admin/categories/{id}/edit', [Admin\CategoryController::class, 'edit']);
$router->post('/admin/categories/{id}', [Admin\CategoryController::class, 'update']);
$router->post('/admin/categories/{id}/status', [Admin\CategoryController::class, 'toggleStatus']);

$router->get('/admin/doctors', [Admin\DoctorController::class, 'index']);
$router->get('/admin/doctors/create', [Admin\DoctorController::class, 'create']);
$router->post('/admin/doctors', [Admin\DoctorController::class, 'store']);
$router->get('/admin/doctors/{id}/edit', [Admin\DoctorController::class, 'edit']);
$router->post('/admin/doctors/{id}', [Admin\DoctorController::class, 'update']);
$router->post('/admin/doctors/{id}/status', [Admin\DoctorController::class, 'toggleStatus']);

$router->get('/admin/schedules', [Admin\ScheduleController::class, 'index']);
$router->post('/admin/schedules', [Admin\ScheduleController::class, 'store']);
$router->post('/admin/schedules/{id}/status', [Admin\ScheduleController::class, 'toggleStatus']);
$router->post('/admin/schedules/{id}/delete', [Admin\ScheduleController::class, 'destroy']);

$router->get('/admin/appointments', [Admin\AppointmentController::class, 'index']);
$router->post('/admin/appointments/{id}/status', [Admin\AppointmentController::class, 'updateStatus']);

$router->get('/admin/users', [Admin\UserController::class, 'index']);
$router->post('/admin/users/{id}/status', [Admin\UserController::class, 'toggleStatus']);

// ---------- Doctor panel ----------
$router->get('/doctor', [Doctor\DashboardController::class, 'index']);
$router->get('/doctor/appointments', [Doctor\AppointmentController::class, 'index']);
$router->post('/doctor/appointments/{id}/status', [Doctor\AppointmentController::class, 'updateStatus']);
$router->get('/doctor/schedule', [Doctor\ScheduleController::class, 'index']);
$router->post('/doctor/schedule', [Doctor\ScheduleController::class, 'store']);
$router->post('/doctor/schedule/{id}/status', [Doctor\ScheduleController::class, 'toggleStatus']);
$router->post('/doctor/schedule/{id}/delete', [Doctor\ScheduleController::class, 'destroy']);
$router->get('/doctor/profile', [Doctor\ProfileController::class, 'edit']);
$router->post('/doctor/profile', [Doctor\ProfileController::class, 'update']);
