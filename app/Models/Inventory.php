<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'price_per_day',
        'stock',
        'stock_available',
        'is_active',
        'image'
    ];

    protected $casts = [
        'price_per_day' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Relasi: Inventory bisa dipinjam di banyak booking
     */
    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'inventory_bookings')
                    ->withPivot('quantity', 'price_at_booking')
                    ->withTimestamps();
    }

    /**
     * Scope: Hanya tampilkan barang yang aktif dan tersedia
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_active', true)
                     ->where('stock_available', '>', 0);
    }

    /**
     * Scope: Filter berdasarkan kategori
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope: Urutkan berdasarkan kategori (untuk grouping di view)
     */
    public function scopeOrderedByCategory($query)
    {
        $categoryOrder = [
            'phantom_rjp' => 1,
            'phantom_airway' => 2,
            'phantom_lain' => 3,
            'defibrillator' => 4,
            'bvm' => 5,
            'laringoskop' => 6,
            'alat_dasar' => 7,
            'stretcher' => 8,
            'bed' => 9,
            'mobilitas' => 10,
            'oksigen' => 11,
            'alat_kecil' => 12,
            'monitoring' => 13,
            'timbangan' => 14,
            'alat_khusus' => 15,
            'pakaian' => 16,
            'pompa' => 17,
            'simulator' => 18,
        ];

        return $query->orderByRaw('FIELD(category, ' . implode(',', array_keys($categoryOrder)) . ')');
    }

    /**
     * Cek apakah stok mencukupi
     */
    public function hasEnoughStock($quantity)
    {
        return $this->stock_available >= $quantity;
    }

    /**
     * Kurangi stok tersedia
     */
    public function reduceStock($quantity)
    {
        $this->decrement('stock_available', $quantity);
    }

    /**
     * Tambah stok tersedia (saat booking dibatalkan/selesai)
     */
    public function increaseStock($quantity)
    {
        $this->increment('stock_available', $quantity);
    }

    /**
     * Get nama kategori dalam bahasa Indonesia
     */
    public function getCategoryNameAttribute()
    {
        return \App\Models\InventoryCategory::where('key', $this->category)->value('name') ?? $this->category;
    }
}