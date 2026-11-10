<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'nim',
        'kode_asisten',
        'role',
        'status_aslab',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Daftarkan otomatis ke semester aktif saat aslab baru dibuat.
     */
    protected static function booted()
    {
        static::created(function ($user) {
            if ($user->role === 'aslab') {
                $activeSemester = Semester::where('status', 'aktif')->first();
                if ($activeSemester) {
                    $status = ($user->status_aslab && in_array($user->status_aslab, ['aktif', 'cuti', 'alumni']))
                        ? $user->status_aslab
                        : 'aktif';

                    $user->semesters()->syncWithoutDetaching([
                        $activeSemester->id => [
                            'status'     => $status,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    ]);
                }
            }
        });
    }

    /**
     * Hak akses sistem (Hanya 2 role: Admin dan Kalab)
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isKalab(): bool
    {
        return $this->role === 'kalab';
    }

    /**
     * Relasi: Asisten yang dinilai memiliki banyak data Evaluasi
     */
    public function evaluasisAsAslab()
    {
        return $this->hasMany(Evaluasi::class, 'aslab_id');
    }

    /**
     * Relasi: Evaluator (Admin atau Kalab) yang memberikan evaluasi
     */
    public function evaluasisAsEvaluator()
    {
        return $this->hasMany(Evaluasi::class, 'evaluator_id');
    }

    /**
     * Relasi: Asisten terdaftar di semester lewat tabel pivot
     */
    public function semesters()
    {
        return $this->belongsToMany(Semester::class, 'aslab_semester', 'aslab_id', 'semester_id')
                    ->withPivot('id', 'status')
                    ->withTimestamps();
    }

    /**
     * Ambil status keaktifan asisten pada semester tertentu
     */
    public function getStatusDiSemester($semesterId): string
    {
        $pivot = $this->semesters()->where('semesters.id', $semesterId)->first();
        return $pivot ? $pivot->pivot->status : 'aktif';
    }
}
