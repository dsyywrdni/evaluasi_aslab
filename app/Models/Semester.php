<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Relasi many-to-many membawa kolom pivot 'status'
    public function aslabs()
    {
        return $this->belongsToMany(User::class, 'aslab_semester', 'semester_id', 'aslab_id')
                    ->withPivot('id', 'status')
                    ->withTimestamps();
    }

    // Ambil hanya asisten yang AKTIF di semester ini
    public function activeAslabs()
    {
        return $this->aslabs()->wherePivot('status', 'aktif');
    }

    public function kriterias()
    {
        return $this->hasMany(Kriteria::class, 'semester_id');
    }

    // Semester yang sedang ditinjau berdasarkan middleware
    public static function current(): ?self
    {
        return request()->attributes->get('currentSemester');
    }

    // Pastikan semester yang dipilih boleh diedit
    public static function currentEditable(): self
    {
        $semester = static::current();

        abort_if(
            !$semester || $semester->status !== 'aktif',
            403,
            'Semester arsip hanya bisa dilihat. Pilih semester aktif untuk mengubah data.'
        );

        return $semester;
    }

    /**
     * Daftarkan seluruh asisten dengan role 'aslab' yang belum terdaftar ke semester ini.
     * Status default adalah 'aktif' (atau status_aslab jika didefinisikan).
     */
    public function ensureAllAslabsRegistered(): void
    {
        $allAslabs = User::where('role', 'aslab')->get();
        if ($allAslabs->isEmpty()) {
            return;
        }

        $existingAslabIds = $this->aslabs()->pluck('users.id')->toArray();

        $attachData = [];
        foreach ($allAslabs as $aslab) {
            if (!in_array($aslab->id, $existingAslabIds)) {
                $status = ($aslab->status_aslab && in_array($aslab->status_aslab, ['aktif', 'cuti', 'alumni']))
                    ? $aslab->status_aslab
                    : 'aktif';

                $attachData[$aslab->id] = [
                    'status'     => $status,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (!empty($attachData)) {
            $this->aslabs()->attach($attachData);
        }
    }

    /**
     * Sinkronisasi seluruh aslab ke semester yang sedang aktif saat ini.
     */
    public static function syncAllAslabsToActive(): void
    {
        $activeSemester = static::where('status', 'aktif')->first();
        if ($activeSemester) {
            $activeSemester->ensureAllAslabsRegistered();
        }
    }
}
