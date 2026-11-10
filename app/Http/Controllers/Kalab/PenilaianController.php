<?php

namespace App\Http\Controllers\Kalab;

use App\Http\Controllers\Concerns\HandlesPenilaian;
use App\Http\Controllers\Controller;
use App\Services\ProfileMatchingService;

class PenilaianController extends Controller
{
    use HandlesPenilaian;

    public function __construct(protected ProfileMatchingService $pmService)
    {
    }

    protected function prefix(): string
    {
        return 'kalab';
    }
}
