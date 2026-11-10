<?php

namespace App\Http\Controllers\Kalab;

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
        // Data yang sama persis dengan dashboard admin
        return view('kalab.dashboard', $this->pmService->getDashboardData(Semester::current()?->id));
    }
}