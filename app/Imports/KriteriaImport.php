<?php

namespace App\Imports;

use App\Models\Kriteria;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;

class KriteriaImport implements ToModel
{
    public function model(array $row): Model|null
    {
        return new Kriteria([
            //
            "name" => $row["nama"],
            "email" => str_replace(" ", "", $row["nama"]) . "@wicida.ac.id",
            "password" => Hash::make("12345678"),
            "kode_asisten" => "A" . $row["no"],
            "role" => "aslab",
            "status_aslab" => "aktif", 
        ]);
    }
}
