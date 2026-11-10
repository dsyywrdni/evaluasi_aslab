<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use App\Services\ProfileMatchingService;

class DashboardController extends Controller
{
    public function __construct(protected ProfileMatchingService $pmService)
    {
    }

    public function index()
    {
        return view('admin.dashboard', $this->pmService->getDashboardData(Semester::current()?->id));
    }
}