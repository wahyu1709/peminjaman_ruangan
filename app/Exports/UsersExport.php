<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection, WithHeadings
{
    protected $jenis;

    public function __construct($jenis = 'all')
    {
        $this->jenis = $jenis;
    }

    public function collection()
    {
        $query = User::select('name', 'email', 'nim_nip', 'jenis_pengguna')
                     ->where('role', 'user');

        if ($this->jenis !== 'all') {
            $query->where('jenis_pengguna', $this->jenis);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return ['Nama', 'Email', 'NIM/NIP', 'Jenis Pengguna'];
    }
}