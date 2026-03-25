<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id', 
        'room_id',
        'is_inventory_only',
        'tanggal_pinjam', 
        'waktu_mulai', 
        'waktu_selesai', 
        'keperluan',
        'role_unit', 
        'status', 
        'rejected_reason', 
        'admin_comment',
        'total_amount',
        'invoice_number',
        'invoice_path',
        'bukti_pembayaran',  
        'payment_uploaded_at',
    ];

    protected $casts = [
        'is_inventory_only' => 'boolean',
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

    public static function markCompletedBookings()
    {
        $now         = now();
        $today       = $now->format('Y-m-d');
        $currentTime = $now->format('H:i');

        // Ambil sebagai collection dulu, bukan mass update
        $bookings = self::with('inventories')
            ->where('status', 'approved')
            ->where(function ($query) use ($today, $currentTime) {
                $query->where('tanggal_pinjam', '<', $today)
                    ->orWhere(function ($sub) use ($today, $currentTime) {
                        $sub->where('tanggal_pinjam', $today)
                            ->where('waktu_selesai', '<=', $currentTime);
                    });
            })
            ->get();

        foreach ($bookings as $booking) {
            // Kembalikan stok inventaris
            foreach ($booking->inventories as $inventory) {
                $inventory->increment('stock_available', $inventory->pivot->quantity);
            }

            // Baru update status
            $booking->update(['status' => 'completed']);
        }
    }

    public function inventories()
    {
        return $this->belongsToMany(Inventory::class, 'inventory_bookings')
                    ->withPivot('quantity', 'price_at_booking')
                    ->withTimestamps();
    }

    public function inventoryBookings()
    {
        return $this->hasMany(InventoryBooking::class);
    }

    public function getTotalInventoryPriceAttribute()
    {
        return $this->inventories->sum(function($inventory) {
            return $inventory->pivot->quantity * $inventory->pivot->price_at_booking;
        });
    }

    public function getTotalRoomPriceAttribute()
    {
        return $this->room ? $this->room->harga_sewa_per_hari : 0;
    }

    public function getIsInventoryOnlyAttribute()
    {
        return $this->room_id === null || $this->attributes['is_inventory_only'] === true;
    }

    public function getRoomNameAttribute()
    {
        return $this->room ? $this->room->nama_ruangan : 'Tanpa Ruangan';
    }

    public function scopeWithInventories($query)
    {
        return $query->whereHas('inventories');
    }

    public function scopeInventoryOnly($query)
    {
        return $query->where('is_inventory_only', true)
                     ->orWhereNull('room_id');
    }

    public function reduceInventoryStock()
    {
        foreach ($this->inventories as $inventory) {
            $inventory->reduceStock($inventory->pivot->quantity);
        }
    }

    public function restoreInventoryStock()
    {
        foreach ($this->inventories as $inventory) {
            $inventory->increaseStock($inventory->pivot->quantity);
        }
    }
}