<?php

namespace App\Imports;

use App\Models\Kriteria;
use App\Models\Semester;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class KriteriaImport implements ToModel, WithHeadingRow
{
    public function model(array $row): Model|null
    {
        $activeSemester = Semester::where("status", "aktif")->first();
        $activeSemesterID = $activeSemester->id;
        return new Kriteria([
            //
            "semester_id" => $activeSemesterID,
            "nama_kriteria" => $row["nama_kriteria"],
            "persentase_kriteria" => $row["persentase_kriteria"],
            "bobot_cf" => $row["bobot_cf"],
            "bobot_sf" => $row["bobot_sf"], 
        ]);
    }
}