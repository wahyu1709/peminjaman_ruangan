<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InventoryBooking extends Model
{
    use HasFactory;

    protected $table = 'inventory_bookings';

    protected $fillable = [
        'booking_id',
        'inventory_id',
        'quantity',
        'price_at_booking'
    ];

    protected $casts = [
        'price_at_booking' => 'decimal:2',
    ];

    /**
     * Relasi: InventoryBooking milik Booking
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Relasi: InventoryBooking milik Inventory
     */
    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    /**
     * Hitung subtotal untuk item ini
     */
    public function getSubtotalAttribute()
    {
        return $this->quantity * $this->price_at_booking;
    }
}