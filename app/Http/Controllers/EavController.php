<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class EavController extends Controller
{
    public function index()
    {
        return Inertia::render('EAV/Index');
    }
}
