<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryCategory extends Model
{
    protected $fillable = ['key', 'name', 'icon', 'sort_order', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Scope: hanya yang aktif, urut sort_order
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Relasi ke inventaris
     */
    public function inventories()
    {
        return $this->hasMany(Inventory::class, 'category', 'key');
    }

    /**
     * Generate key dari nama (slug sederhana)
     */
    public static function generateKey(string $name): string
    {
        $key = strtolower($name);
        $key = preg_replace('/[^a-z0-9\s]/', '', $key);
        $key = preg_replace('/\s+/', '_', trim($key));
        return $key;
    }
}