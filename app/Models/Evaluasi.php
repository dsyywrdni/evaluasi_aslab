<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Evaluasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluator_id',
        'aslab_id',
        'sub_kriteria_id',
        'semester_id',
        'nilai_aktual',
    ];

    // Relasi: Evaluasi dinilai oleh seorang Evaluator (User)
    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    // Relasi: Evaluasi ditujukan untuk seorang Asisten Lab (User)
    public function aslab()
    {
        return $this->belongsTo(User::class, 'aslab_id');
    }

    // Relasi: Evaluasi merujuk pada satu Sub-Kriteria tertentu
    public function subKriteria()
    {
        return $this->belongsTo(SubKriteria::class, 'sub_kriteria_id');
    }


    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }
}
