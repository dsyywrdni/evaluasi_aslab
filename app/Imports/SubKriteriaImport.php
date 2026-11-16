<?php

namespace App\Imports;

use App\Models\RubrikPenilaian;
use App\Models\SubKriteria;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SubKriteriaImport implements WithHeadingRow, OnEachRow
{
    private $id;
    public function __construct($id)
    {
        $this->id = $id;
    }
    public function onRow(Row $row): void
    {
        $subKriteria = SubKriteria::create([
            "kriteria_id" => $this->id,
            "nama_sub" => $row["nama_sub"],
            "nilai_target" => $row["nilai_target"],
            "jenis" => $row["jenis"],
            ]);
        for ($i=1; $i <= 5; $i++) { 
            # code...
            RubrikPenilaian::create([
                "sub_kriteria_id" => $subKriteria->id,
                "skor" => $i,
                "deskripsi" => $row["deskripsi_" . $i],
            ]);
        }
    }
}
