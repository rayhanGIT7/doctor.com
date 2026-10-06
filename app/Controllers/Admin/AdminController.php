<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;

// Every controller in this folder extends this one, so only admins can reach them
abstract class AdminController extends Controller
{
    protected string $layout = 'panel';

    public function __construct()
    {
        Auth::requireRole('admin');
    }
}
