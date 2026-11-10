<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RubrikPenilaian extends Model
{
    use HasFactory;

    protected $fillable = ['sub_kriteria_id', 'skor', 'deskripsi'];

    public function subKriteria()
    {
        return $this->belongsTo(SubKriteria::class);
    }
}

