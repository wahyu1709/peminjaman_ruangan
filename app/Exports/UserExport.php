<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;


class UserExport implements FromView
{
    public function view(): View
    {
        $data = array(
            'users' => User::where('jenis_pengguna', 'mahasiswa')
                 ->paginate(10)
        );
        
        return view('admin/user/excel', $data);
    }
}
