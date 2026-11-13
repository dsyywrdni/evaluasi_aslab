<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AslabImport implements ToModel, WithHeadingRow
{
    public function model(array $row): Model|null
    {
        return new User([
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
