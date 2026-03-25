<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InventoryExport;

class InventoryController extends Controller
{
    public function index()
    {
        return view('admin.inventory.index', [
            'title'         => 'Manajemen Inventaris',
            'menuAdminInventory' => 'active',
            'categories'    => InventoryCategory::active()->get(),
        ]);
    }

    public function data(Request $request)
    {
        $query = Inventory::query();

        if ($request->filled('category')) $query->where('category', $request->category);
        if ($request->filled('status'))   $query->where('is_active', $request->status === 'active');

        if ($request->boolean('kpi')) {
            $all = (clone $query)->get();
            return response()->json(['kpi' => [
                'total'          => $all->count(),
                'active'         => $all->where('is_active', true)->count(),
                'stok_total'     => $all->sum('stock'),
                'stok_available' => $all->sum('stock_available'),
                'stok_used'      => $all->sum('stock') - $all->sum('stock_available'),
            ]]);
        }

        $catMap = InventoryCategory::pluck('name', 'key')->toArray();

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('category_label', fn($i) => $catMap[$i->category] ?? $i->category)
            ->addColumn('stok_info', function ($i) {
                $pct   = $i->stock > 0 ? round($i->stock_available / $i->stock * 100) : 0;
                $color = $pct > 50 ? '#10b981' : ($pct > 20 ? '#f59e0b' : '#ef4444');
                return ['total'=>$i->stock,'available'=>$i->stock_available,'used'=>$i->stock-$i->stock_available,'pct'=>$pct,'color'=>$color];
            })
            ->addColumn('price_fmt',    fn($i) => 'Rp '.number_format($i->price_per_day,0,',','.'))
            ->addColumn('status_badge', fn($i) => $i->is_active
                ? '<span class="status-badge sb-approved">Aktif</span>'
                : '<span class="status-badge sb-cancelled">Nonaktif</span>')
            ->addColumn('action', fn($i) => view('admin.inventory.partials.action', ['item'=>$i])->render())
            ->rawColumns(['status_badge','action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'category'      => 'required|string|exists:inventory_categories,key',
            'price_per_day' => 'required|numeric|min:0',
            'stock'         => 'required|integer|min:0',
            'is_active'     => 'required|boolean',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $validated['stock_available'] = $validated['stock'];
        if ($request->hasFile('image'))
            $validated['image'] = $request->file('image')->store('inventories','public');

        Inventory::create($validated);
        return response()->json(['success'=>true,'message'=>'Barang berhasil ditambahkan.']);
    }

    public function show($id)
    {
        return response()->json(['success'=>true,'data'=>Inventory::findOrFail($id)]);
    }

    public function update(Request $request, $id)
    {
        $item = Inventory::findOrFail($id);

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'category'      => 'required|string|exists:inventory_categories,key',
            'price_per_day' => 'required|numeric|min:0',
            'stock'         => 'required|integer|min:0',
            'is_active'     => 'required|boolean',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $diff = $validated['stock'] - $item->stock;
        $validated['stock_available'] = max(0, $item->stock_available + $diff);

        if ($request->hasFile('image')) {
            if ($item->image) Storage::disk('public')->delete($item->image);
            $validated['image'] = $request->file('image')->store('inventories','public');
        }

        $item->update($validated);
        return response()->json(['success'=>true,'message'=>'Barang berhasil diperbarui.']);
    }

    public function destroy($id)
    {
        $item = Inventory::findOrFail($id);

        if ($item->stock_available < $item->stock)
            return response()->json(['success'=>false,'message'=>'Tidak bisa dihapus — masih ada yang dipinjam.'],422);

        if ($item->image) Storage::disk('public')->delete($item->image);
        $item->delete();
        return response()->json(['success'=>true,'message'=>'Barang berhasil dihapus.']);
    }

    public function adjustStock(Request $request, $id)
    {
        $item = Inventory::findOrFail($id);
        $request->validate(['type'=>'required|in:add,reduce','amount'=>'required|integer|min:1']);
        $amount = (int)$request->amount;

        if ($request->type === 'add') {
            $item->increment('stock', $amount);
            $item->increment('stock_available', $amount);
        } else {
            if ($item->stock_available < $amount)
                return response()->json(['success'=>false,'message'=>"Stok tersedia hanya {$item->stock_available} unit."],422);
            $item->decrement('stock', $amount);
            $item->decrement('stock_available', $amount);
        }

        $verb = $request->type === 'add' ? 'bertambah' : 'berkurang';
        return response()->json(['success'=>true,'message'=>"Stok {$verb} {$amount} unit."]);
    }

    public function toggleStatus($id)
    {
        $item = Inventory::findOrFail($id);
        $item->update(['is_active'=>!$item->is_active]);
        return response()->json(['success'=>true,'message'=>'Status barang diperbarui.','is_active'=>$item->is_active]);
    }

    // ════════════════════════════════════════════════════════════
    // CATEGORY CRUD
    // ════════════════════════════════════════════════════════════

    public function categories()
    {
        return response()->json([
            'success' => true,
            'data'    => InventoryCategory::orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:100',
            'icon'       => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // Auto-generate unique key dari nama
        $base = strtolower(preg_replace('/\s+/','_', preg_replace('/[^a-zA-Z0-9\s]/','', $request->name)));
        $key  = $base;
        $n    = 1;
        while (InventoryCategory::where('key', $key)->exists()) $key = $base.'_'.$n++;

        $cat = InventoryCategory::create([
            'key'        => $key,
            'name'       => $request->name,
            'icon'       => $request->icon ?: 'fas fa-box',
            'sort_order' => $request->sort_order ?? 500,
            'is_active'  => true,
        ]);

        return response()->json(['success'=>true,'message'=>"Kategori '{$cat->name}' berhasil ditambahkan.",'data'=>$cat]);
    }

    public function updateCategory(Request $request, $id)
    {
        $cat = InventoryCategory::findOrFail($id);
        $request->validate([
            'name'      => 'required|string|max:100',
            'is_active' => 'required|boolean',
        ]);

        $cat->update([
            'name'      => $request->name,
            'is_active' => $request->is_active,
            // icon & sort_order tidak diubah dari UI
        ]);

        return response()->json(['success'=>true,'message'=>'Kategori berhasil diperbarui.','data'=>$cat]);
    }

    public function destroyCategory($id)
    {
        $cat   = InventoryCategory::findOrFail($id);
        $count = Inventory::where('category', $cat->key)->count();

        if ($count > 0)
            return response()->json(['success'=>false,'message'=>"Tidak bisa dihapus — masih ada {$count} barang di kategori ini."],422);

        $cat->delete();
        return response()->json(['success'=>true,'message'=>'Kategori berhasil dihapus.']);
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new InventoryExport($request->get('category','all')), 'inventaris_'.now()->format('Ymd').'.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $category = $request->get('category','all');
        $query    = Inventory::orderBy('category')->orderBy('name');
        if ($category !== 'all') $query->where('category', $category);

        $catMap = InventoryCategory::pluck('name','key')->toArray();
        $items  = $query->get();
        $period = now()->isoFormat('D MMMM YYYY HH:mm');

        $pdf = Pdf::loadView('admin.inventory.pdf', compact('items','period','category','catMap'))
            ->setPaper('a4','portrait')
            ->setOption('margin-top',10)->setOption('margin-bottom',10)
            ->setOption('margin-left',10)->setOption('margin-right',10);

        return $pdf->download('inventaris_'.now()->format('Ymd').'.pdf');
    }
}