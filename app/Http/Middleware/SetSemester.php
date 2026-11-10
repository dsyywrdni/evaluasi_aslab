<?php

namespace App\Http\Middleware;

use App\Models\Semester;
use Closure;
use Illuminate\Http\Request;

class SetSemester
{
    public function handle(Request $request, Closure $next)
    {
        // Hanya GET yang boleh mengganti semester (form POST membawa semester_id sendiri)
        if ($request->isMethod('GET') && $request->filled('semester_id')
            && Semester::whereKey($request->query('semester_id'))->exists()) {
            session(['selected_semester_id' => (int) $request->query('semester_id')]);
        }

        $active  = Semester::where('status', 'aktif')->first();
        $current = Semester::find(session('selected_semester_id')) ?? $active ?? Semester::latest('id')->first();

        // Dipakai controller lewat Semester::current()
        $request->attributes->set('currentSemester', $current);

        // Dipakai semua view (header, badge arsip, dropdown)
        view()->share([
            'currentSemester'      => $current,
            'activeSemesterGlobal' => $active,
            'allSemesters'         => Semester::orderByDesc('id')->get(),
            'isArchive'            => (bool) ($current && $current->status !== 'aktif'),
        ]);

        return $next($request);
    }
}