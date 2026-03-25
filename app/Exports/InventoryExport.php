<?php

namespace App\Exports;

use App\Models\Inventory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InventoryExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function __construct(private string $category = 'all') {}

    public function collection()
    {
        $query = Inventory::orderBy('category')->orderBy('name');
        if ($this->category !== 'all') {
            $query->where('category', $this->category);
        }
        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No', 'Nama Barang', 'Kategori',
            'Harga/Hari (Rp)', 'Stok Total', 'Stok Tersedia',
            'Stok Dipinjam', 'Status',
        ];
    }

    public function map($item): array
    {
        static $no = 0;
        $no++;
        return [
            $no,
            $item->name,
            $item->category_name,
            $item->price_per_day,
            $item->stock,
            $item->stock_available,
            $item->stock - $item->stock_available,
            $item->is_active ? 'Aktif' : 'Nonaktif',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => [
                'fillType' => 'solid',
                'startColor' => ['rgb' => '4361EE'],
            ], 'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']]],
        ];
    }
}