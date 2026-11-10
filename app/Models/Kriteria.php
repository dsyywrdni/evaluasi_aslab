<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kriteria extends Model
{
    use HasFactory;

    protected $fillable = [
        'semester_id',
        'nama_kriteria',
        'persentase_kriteria',
        'bobot_cf',
        'bobot_sf',
    ];

    // Relasi: Satu Kriteria memiliki banyak Sub-Kriteria
    public function subKriterias()
    {
        return $this->hasMany(SubKriteria::class, 'kriteria_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }
}
