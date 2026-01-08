<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class AdminExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View
    {
        $data = array(
            'users' => User::where('role', 'admin')
                 ->get()
        );
        
        return view('admin/admin/excel', $data);
    }
}
