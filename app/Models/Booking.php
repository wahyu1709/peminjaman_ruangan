<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id', 
        'room_id', 
        'tanggal_pinjam', 
        'waktu_mulai', 
        'waktu_selesai', 
        'keperluan',
        'role_unit', 
        'status', 
        'rejected_reason', 
        'admin_comment',
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
            'cancelled' => '<span class="badge badge-secondary">Dibatalkan</span>',
            default     => '<span class="badge badge-warning">Pending</span>',
        };
    }

    public static function markCompletedBookings(){
        $now = now();
        $today = $now->format('Y-m-d');
        $currentTime = $now->format('H:i');

        self::where('status', 'approved')
           ->where(function ($query) use ($today, $currentTime){
            $query->where('tanggal_pinjam', '<', $today)
                ->orWhere(function ($sub) use ($today, $currentTime){
                    $sub->where('tanggal_pinjam', $today)
                        ->where('waktu_selesai', '<=', $currentTime);
                });
           })
           ->update(['status' => 'completed']);
    }
}