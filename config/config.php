<?php

// Application settings. Change the database values to match your MySQL setup.
return [
    'app_name' => 'MediBook',
    'debug'    => true,              // set false on a live server
    'timezone' => 'Asia/Dhaka',

    'db' => [
        'host' => '127.0.0.1',
        'port' => 3306,
        'name' => 'doctor_appointment',
        'user' => 'root',
        'pass' => '',
    ],

    // How many days ahead a patient can book
    'booking_days' => 30,

    // Items per page on list pages
    'per_page' => 10,
];
