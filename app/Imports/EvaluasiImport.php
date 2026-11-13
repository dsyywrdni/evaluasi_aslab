<?php

namespace App\Imports;

use App\Models\Evaluasi;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;

class EvaluasiImport implements ToModel
{
    public function model(array $row): Model|null
    {
        return new Evaluasi([
            //
        ]);
    }
}
