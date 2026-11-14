<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AslabImport implements ToModel, WithHeadingRow
{
    protected int $semesterId;

    public function __construct(int $semesterId)
    {
        $this->semesterId = $semesterId;
    }

    public function model(array $row): Model|null
    {
        $nama         = trim($row['nama'] ?? '');
        $nim          = trim($row['nim'] ?? '');
        $no           = trim($row['no'] ?? '');
        $kodeAsisten  = trim($row['kode_asisten'] ?? '');
        $email        = trim($row['email'] ?? '');

        if (empty($nama)) {
            return null;
        }

        // Buat kode_asisten otomatis jika tidak disediakan
        if (empty($kodeAsisten)) {
            $kodeAsisten = 'A' . $no;
        }

        // Buat email otomatis jika tidak disediakan
        if (empty($email)) {
            $email = strtolower(str_replace(' ', '', $nama)) . '@wicida.ac.id';
        }

        // Cegah duplikasi: cari user berdasarkan kode_asisten atau email
        $user = User::where('kode_asisten', $kodeAsisten)
            ->orWhere('email', $email)
            ->first();

        if ($user) {
            // Update data jika ada perubahan
            $user->update([
                'name'         => $nama,
                'nim'          => $nim ?: $user->nim,
                'kode_asisten' => $kodeAsisten,
            ]);
        } else {
            $user = User::create([
                'name'         => $nama,
                'email'        => $email,
                'nim'          => $nim,
                'password'     => Hash::make('password123'),
                'kode_asisten' => $kodeAsisten,
                'role'         => 'aslab',
                'status_aslab' => 'aktif',
            ]);
        }

        // Daftarkan ke semester lewat pivot aslab_semester
        $user->semesters()->syncWithoutDetaching([
            $this->semesterId => [
                'status'     => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        return null;
    }
}
