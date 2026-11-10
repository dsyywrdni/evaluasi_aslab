<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubKriteria extends Model
{
    use HasFactory;

    protected $fillable = [
        'kriteria_id',
        'nama_sub',
        'nilai_target',
        'jenis',
    ];

    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class, 'kriteria_id');
    }

    
    public function evaluasis()
    {
        return $this->hasMany(Evaluasi::class, 'sub_kriteria_id');
    }

    public function rubrikPenilaians()
    {
        return $this->hasMany(RubrikPenilaian::class)->orderBy('skor');
    }
}
