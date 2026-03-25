{{-- resources/views/admin/inventory/partials/action.blade.php --}}
<div class="d-flex flex-column gap-1" style="gap:4px;">
    <div class="d-flex gap-1">
        <button class="action-btn ab-edit btn-edit-item"
                data-id="{{ $item->id }}"
                title="Edit">
            <i class="fas fa-edit"></i>
        </button>

        <button class="action-btn ab-stock btn-stock"
                data-id="{{ $item->id }}"
                data-name="{{ $item->name }}"
                data-total="{{ $item->stock }}"
                data-available="{{ $item->stock_available }}"
                title="Kelola Stok">
            <i class="fas fa-boxes"></i>
        </button>

        <button class="action-btn ab-toggle btn-toggle-status"
                data-id="{{ $item->id }}"
                data-name="{{ $item->name }}"
                title="{{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
            <i class="fas fa-{{ $item->is_active ? 'toggle-on' : 'toggle-off' }}"></i>
        </button>

        <button class="action-btn ab-delete btn-delete-item"
                data-id="{{ $item->id }}"
                data-name="{{ $item->name }}"
                title="Hapus">
            <i class="fas fa-trash"></i>
        </button>
    </div>
</div>