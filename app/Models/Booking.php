<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id', 'room_id', 'tanggal_pinjam', 'waktu_mulai', 'waktu_selesai', 'keperluan', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'approved'  => '<span class="badge badge-success">Disetujui</span>',
            'rejected'  => '<span class="badge badge-danger">Ditolak</span>',
            'completed' => '<span class="badge badge-info">Selesai</span>',
            default     => '<span class="badge badge-warning">Pending</span>',
        };
    }
}