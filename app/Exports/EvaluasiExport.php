<?php

namespace App\Exports;

use App\Models\Evaluasi;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EvaluasiExport implements FromCollection, WithHeadings, WithMapping
{
    private $header = [];
    public function __construct($header){
        $this->header = $header;
    }
    public function collection(): Collection
    {
        return User::where("role", "aslab")->get();
    }
    public function headings(): array{
        return $this->header;
    }
    public function map($user): array{
        return [
                $user->kode_asisten
            ];
    }
}
