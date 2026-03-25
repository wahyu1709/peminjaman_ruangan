@push('styles')
<style>
#price-breakdown {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-left: 4px solid #ffd700;
    animation: fadeIn 0.3s ease-in;
    border-radius: 8px;
}

#price-breakdown strong { color: #ffd700; }
#price-breakdown .text-success { color: #90ee90 !important; font-weight: bold; }
#price-breakdown .text-warning { color: #ffa500 !important; font-weight: bold; }
#price-breakdown .text-primary { color: #ffd700 !important; }
#price-breakdown .text-discount { color: #ff6b6b !important; font-weight: bold; }

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to   { opacity: 1; transform: translateY(0); }
}

.border-left-success { border-left: 0.25rem solid #1cc88a !important; }

/* Accordion */
.accordion-button:not(.collapsed) {
    background-color: #e9ecef;
    font-weight: 600;
    color: #495057;
}

/* Selected items */
.selected-item-card {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 10px 14px;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    animation: fadeIn 0.2s ease-in;
}

.selected-item-card .item-info { flex: 1; }
.selected-item-card .item-name { font-weight: 600; font-size: 0.9rem; color: #343a40; }
.selected-item-card .item-price { font-size: 0.8rem; color: #6c757d; }

.selected-item-card .qty-controls {
    display: flex;
    align-items: center;
    gap: 6px;
}

.qty-btn {
    width: 28px; height: 28px;
    border-radius: 50%;
    border: 1px solid #dee2e6;
    background: white;
    font-size: 1rem; line-height: 1;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: background 0.15s;
}
.qty-btn:hover { background: #e9ecef; }

.qty-value {
    min-width: 28px;
    text-align: center;
    font-weight: 600;
    font-size: 0.9rem;
}

.item-subtotal {
    font-weight: 700;
    color: #667eea;
    font-size: 0.9rem;
    min-width: 100px;
    text-align: right;
}

.remove-item-btn {
    background: none;
    border: none;
    color: #dc3545;
    cursor: pointer;
    font-size: 1rem;
    padding: 2px 6px;
    border-radius: 4px;
    transition: background 0.15s;
}
.remove-item-btn:hover { background: #fdecea; }

/* Select2 override */
.select2-container--default .select2-selection--multiple {
    border-color: #ced4da;
    border-radius: 0.25rem;
    min-height: 38px;
}
</style>
@endpush

@extends('layouts.app')

@section('content')
<h1 class="h3 mb-4 text-gray-800">{{ $title ?? 'Form Pinjam Ruangan & Barang' }}</h1>

<div class="card shadow">
    <div class="card-header bg-primary text-white">
        <a href="{{ route('booking') }}" class="btn btn-sm btn-light">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <div class="card-body">

        {{-- Alert Perpanjangan --}}
        @if(isset($data))
        <div class="alert alert-info">
            <i class="fas fa-clock mr-2"></i>
            <strong>Perpanjangan Booking</strong><br>
            Ruangan: <strong>{{ $rooms->firstWhere('id', $data['room_id'])?->nama_ruangan ?? '-' }}</strong>
        </div>
        @endif

        {{-- Alert Pengguna Umum --}}
        @if(auth()->user()->jenis_pengguna === 'umum')
        <div class="alert alert-warning">
            <i class="fas fa-info-circle mr-2"></i>
            <strong>Pengguna Umum:</strong> Semua ruangan dan barang dikenakan biaya sewa penuh (tanpa diskon).
        </div>
        @endif

        {{-- Alert Diskon Internal --}}
        @if(auth()->user()->jenis_pengguna !== 'umum')
        <div class="alert alert-success border-left-success">
            <i class="fas fa-tags mr-2"></i>
            <strong>Diskon 25% Aktif!</strong> Sebagai Civitas FIK UI, Anda mendapatkan diskon 25% pada harga sewa ruangan.
            <br><small class="mt-1 d-block">
                Sesuai SK No. 411/2026 Pasal Ketiga: Diskon berlaku untuk kegiatan <em>provit &amp; menunjang Tridharma FIK UI</em>.
                Untuk verifikasi formal, ajukan surat ke Wakil Dekan Sumber Daya.
            </small>
        </div>
        @endif

        <form action="{{ route('bookingStore') }}" method="POST" id="bookingForm">
            @csrf

            {{-- Ruangan & Tanggal --}}
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Ruangan <span class="text-danger">*</span></label>
                    <select name="room_id" id="room_id" class="form-control @error('room_id') is-invalid @enderror" required>
                        <option value="" disabled {{ old('room_id', $data['room_id'] ?? '') == '' ? 'selected' : '' }}>
                            -- Pilih Ruangan --
                        </option>
                        <option value="" {{ old('room_id', $data['room_id'] ?? '') == '' ? 'selected' : '' }}>
                            ⚠️ Tanpa Ruangan (Pinjam Barang Saja)
                        </option>
                        @foreach($rooms as $room)
                            @php $hargaDasar = $room->harga_sewa_per_hari ?? 0; @endphp
                            <option value="{{ $room->id }}"
                                    data-harga="{{ $hargaDasar }}"
                                    data-denda="{{ $room->denda_per_hari ?? 0 }}"
                                    {{ old('room_id', $data['room_id'] ?? '') == $room->id ? 'selected' : '' }}>
                                {{ $room->kode_ruangan }} - {{ $room->nama_ruangan }}
                                @if($hargaDasar > 0)
                                    (Rp {{ number_format($hargaDasar, 0, ',', '.') }}/hari)
                                @else
                                    (Gratis)
                                @endif
                                - {{ $room->lokasi }}
                            </option>
                        @endforeach
                    </select>
                    @error('room_id') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label>Tanggal Pinjam <span class="text-danger">*</span></label>
                    <input type="date"
                           name="tanggal_pinjam"
                           id="tanggal_pinjam"
                           class="form-control @error('tanggal_pinjam') is-invalid @enderror"
                           value="{{ old('tanggal_pinjam', $data['tanggal_pinjam'] ?? '') }}"
                           min="{{ date('Y-m-d') }}"
                           required>
                    @error('tanggal_pinjam') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            {{-- Role/Unit & Waktu --}}
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Peran / Unit Kerja <span class="text-muted">(Opsional)</span></label>
                    <select name="role_unit_select" id="role_unit_select" class="form-control">
                        <option value="" disabled selected>Pilih Peran / Unit Kerja</option>
                        <option value="Panitia Kegiatan">Panitia Kegiatan</option>
                        <option value="Koordinator MK">Koordinator MK</option>
                        <option value="Pengelola PS S1">Pengelola PS S1</option>
                        <option value="Pengelola PS S2">Pengelola PS S2</option>
                        <option value="Pengelola PS S3">Pengelola PS S3</option>
                        <option value="Departemen Kep Dasar">Departemen Kep. Dasar</option>
                        <option value="Departemen Kep Komunitas">Departemen Kep. Komunitas</option>
                        <option value="Departemen Kep Maternitas">Departemen Kep. Maternitas</option>
                        <option value="Departemen KMB">Departemen KMB</option>
                        <option value="Departemen Kep Jiwa">Departemen Kep. Jiwa</option>
                        <option value="Departemen Kep Anak">Departemen Kep. Anak</option>
                        <option value="other">Lainnya...</option>
                    </select>
                    <input type="text" name="role_unit_other" id="role_unit_other" class="form-control mt-2"
                           placeholder="Masukkan peran / unit kerja..." style="display:none;">
                    <input type="hidden" name="role_unit" id="role_unit_final">
                </div>

                <div class="col-md-3 mb-3">
                    <label>Jam Mulai <span class="text-danger">*</span></label>
                    <input type="time" name="waktu_mulai" class="form-control @error('waktu_mulai') is-invalid @enderror"
                           value="{{ old('waktu_mulai', $data['waktu_mulai'] ?? '') }}" required>
                    @error('waktu_mulai') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-3 mb-3">
                    <label>Jam Selesai <span class="text-danger">*</span></label>
                    <input type="time" name="waktu_selesai" class="form-control @error('waktu_selesai') is-invalid @enderror"
                           value="{{ old('waktu_selesai') }}" required>
                    @error('waktu_selesai') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            {{-- Checkbox Pinjam Barang --}}
            <div class="form-check mb-3">
                <input type="checkbox" id="pinjam_barang" class="form-check-input">
                <label class="form-check-label" for="pinjam_barang">
                    <strong>Saya ingin meminjam barang inventaris</strong>
                </label>
            </div>

            {{-- Section Barang — tersembunyi sampai checkbox dicentang --}}
            <div id="section-barang" style="display:none;">
                <div class="mb-3">
                    <label><i class="fas fa-boxes mr-2"></i>Pilih Barang yang Ingin Dipinjam</label>
                    <select id="inventory_dropdown"
                            class="form-control select2"
                            multiple
                            data-placeholder="Cari barang (misal: Phantom, Defibrillator, dll)..."
                            style="width:100%;">
                        @foreach($inventories as $item)
                            <option value="{{ $item->id }}"
                                    data-price="{{ $item->price_per_day }}"
                                    data-stock="{{ $item->stock_available }}"
                                    data-name="{{ $item->name }}">
                                {{ $item->name }}
                                (Rp {{ number_format($item->price_per_day, 0, ',', '.') }}/hari)
                                • Stok: {{ $item->stock_available }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted d-block mt-1">
                        Gunakan kotak pencarian di atas untuk mencari barang.
                    </small>
                </div>

                {{-- Preview Barang Dipilih --}}
                <div id="selected-items-preview" class="mb-4" style="display:none;">
                    <h6 class="mb-2"><i class="fas fa-list mr-2"></i>Barang yang Dipilih:</h6>
                    <div id="selected-items-list"></div>
                </div>
            </div>

            {{-- Harga & Denda --}}
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="font-weight-bold">Harga Sewa/Hari</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" id="harga_display" class="form-control" readonly placeholder="Pilih ruangan">
                        <input type="hidden" name="harga_sewa_per_hari" id="harga_sewa_per_hari">
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="font-weight-bold">Denda/Hari</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" id="denda_display" class="form-control" readonly placeholder="Pilih ruangan">
                        <input type="hidden" name="denda_per_hari" id="denda_per_hari">
                    </div>
                </div>
            </div>

            {{-- Price Breakdown --}}
            <div id="price-breakdown" class="alert alert-info mt-3 mb-4" style="display:none;">
                <strong><i class="fas fa-calculator mr-1"></i> Rincian Biaya:</strong><br>
                <small>
                    <span id="breakdown-ruangan">• Harga Ruangan: Rp 0</span><br>
                    <span id="breakdown-diskon" style="display:none;">• <span class="text-discount">Diskon 25% Civitas FIK UI:</span> -Rp 0</span><br>
                    <span id="breakdown-barang">• Harga Barang: Rp 0</span><br>
                    <span id="breakdown-tambahan">• Biaya Tambahan: Rp 0</span><br>
                    <hr class="my-2" style="border-color:rgba(255,255,255,0.3);">
                    <strong class="text-primary mt-1 d-block">TOTAL: Rp <span id="breakdown-total">0</span></strong>
                </small>
            </div>

            {{-- Hidden: data barang terpilih --}}
            <input type="hidden" name="selected_inventories" id="selected_inventories" value="[]">

            {{-- Keperluan --}}
            <div class="mb-3">
                <label>Keperluan <span class="text-danger">*</span></label>
                <textarea name="keperluan" class="form-control @error('keperluan') is-invalid @enderror"
                          rows="4" required>{{ old('keperluan', $data['keperluan'] ?? '') }}</textarea>
                @error('keperluan') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save mr-2"></i> Submit Peminjaman
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
{{-- Pastikan Select2 sudah di-load di layout --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ─── Helpers ──────────────────────────────────────────────────────────────

    const userType  = @json(auth()->user()->jenis_pengguna);
    const isInternal = userType !== 'umum';

    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency', currency: 'IDR', minimumFractionDigits: 0
        }).format(angka);
    }

    // ─── Elemen DOM ───────────────────────────────────────────────────────────

    const roomSelect          = document.getElementById('room_id');
    const dateInput           = document.getElementById('tanggal_pinjam');
    const hargaDisplay        = document.getElementById('harga_display');
    const hargaHidden         = document.getElementById('harga_sewa_per_hari');
    const dendaDisplay        = document.getElementById('denda_display');
    const dendaHidden         = document.getElementById('denda_per_hari');
    const priceBreakdown      = document.getElementById('price-breakdown');
    const breakdownRuangan    = document.getElementById('breakdown-ruangan');
    const breakdownDiskon     = document.getElementById('breakdown-diskon');
    const breakdownBarang     = document.getElementById('breakdown-barang');
    const breakdownTambahan   = document.getElementById('breakdown-tambahan');
    const breakdownTotal      = document.getElementById('breakdown-total');
    const selectedInvInput    = document.getElementById('selected_inventories');
    const pinjamBarangCb      = document.getElementById('pinjam_barang');
    const sectionBarang       = document.getElementById('section-barang');
    const selectedItemsList   = document.getElementById('selected-items-list');
    const selectedItemsPreview= document.getElementById('selected-items-preview');
    const roleSelect          = document.getElementById('role_unit_select');
    const roleOther           = document.getElementById('role_unit_other');
    const roleFinal           = document.getElementById('role_unit_final');

    // ─── State: quantity per item ──────────────────────────────────────────────
    // key = inventory id (string), value = integer quantity
    const quantities = {};

    // ─── Role/Unit ────────────────────────────────────────────────────────────

    roleSelect?.addEventListener('change', function () {
        if (this.value === 'other') {
            roleOther.style.display = 'block';
            roleOther.focus();
            roleFinal.value = roleOther.value;
        } else {
            roleOther.style.display = 'none';
            roleOther.value = '';
            roleFinal.value = this.value;
        }
    });

    roleOther?.addEventListener('input', function () {
        roleFinal.value = this.value;
    });

    // ─── Toggle section barang ────────────────────────────────────────────────

    pinjamBarangCb.addEventListener('change', function () {
        sectionBarang.style.display = this.checked ? 'block' : 'none';

        // Reset pilihan saat section disembunyikan
        if (!this.checked) {
            $('#inventory_dropdown').val(null).trigger('change');
        }
        hitungHarga();
    });

    // ─── Inisialisasi Select2 ─────────────────────────────────────────────────

    if (typeof $.fn.select2 !== 'undefined') {
        $('#inventory_dropdown').select2({
            placeholder: 'Cari barang (misal: Phantom, Defibrillator, dll)...',
            allowClear: true,
            width: '100%',
            language: {
                noResults: function () { return 'Barang tidak ditemukan'; }
            }
        });

        // Tangkap perubahan pilihan dari Select2
        $('#inventory_dropdown').on('change', function () {
            updateSelectedItems();
            hitungHarga();
        });
    } else {
        // Fallback jika Select2 tidak tersedia
        document.getElementById('inventory_dropdown')
            .addEventListener('change', function () {
                updateSelectedItems();
                hitungHarga();
            });
        console.warn('Select2 tidak ditemukan. Menggunakan select biasa.');
    }

    // ─── Render kartu barang terpilih ─────────────────────────────────────────

    function updateSelectedItems() {
        const dropdown = document.getElementById('inventory_dropdown');
        const selectedOptions = Array.from(dropdown.selectedOptions);

        // Hapus quantity untuk item yang sudah tidak dipilih
        const selectedIds = selectedOptions.map(o => o.value);
        Object.keys(quantities).forEach(id => {
            if (!selectedIds.includes(id)) delete quantities[id];
        });

        // Inisialisasi quantity baru
        selectedOptions.forEach(opt => {
            if (!quantities[opt.value]) quantities[opt.value] = 1;
        });

        // Render
        if (selectedOptions.length === 0) {
            selectedItemsPreview.style.display = 'none';
            selectedItemsList.innerHTML = '';
            return;
        }

        selectedItemsPreview.style.display = 'block';
        selectedItemsList.innerHTML = selectedOptions.map(opt => {
            const id    = opt.value;
            const name  = opt.getAttribute('data-name') || opt.text.split('(')[0].trim();
            const price = parseFloat(opt.getAttribute('data-price')) || 0;
            const stock = parseInt(opt.getAttribute('data-stock')) || 1;
            const qty   = quantities[id] || 1;

            return `
            <div class="selected-item-card" data-id="${id}">
                <div class="item-info">
                    <div class="item-name">${name}</div>
                    <div class="item-price">${formatRupiah(price)}/hari • Stok: ${stock}</div>
                </div>
                <div class="qty-controls">
                    <button type="button" class="qty-btn qty-minus" data-id="${id}">−</button>
                    <span class="qty-value" id="qty-val-${id}">${qty}</span>
                    <button type="button" class="qty-btn qty-plus" data-id="${id}" data-max="${stock}">+</button>
                </div>
                <div class="item-subtotal" id="subtotal-${id}">
                    ${formatRupiah(price * qty)}
                </div>
                <button type="button" class="remove-item-btn" data-id="${id}" title="Hapus">
                    <i class="fas fa-times"></i>
                </button>
            </div>`;
        }).join('');

        // Event: tombol + / -
        selectedItemsList.querySelectorAll('.qty-minus').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.dataset.id;
                if (quantities[id] > 1) {
                    quantities[id]--;
                    refreshQtyDisplay(id);
                }
            });
        });

        selectedItemsList.querySelectorAll('.qty-plus').forEach(btn => {
            btn.addEventListener('click', function () {
                const id  = this.dataset.id;
                const max = parseInt(this.dataset.max) || 99;
                if (quantities[id] < max) {
                    quantities[id]++;
                    refreshQtyDisplay(id);
                }
            });
        });

        // Event: hapus item
        selectedItemsList.querySelectorAll('.remove-item-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.dataset.id;

                // Deselect dari Select2 / select native
                const dropdown = document.getElementById('inventory_dropdown');
                const opt = dropdown.querySelector(`option[value="${id}"]`);
                if (opt) opt.selected = false;

                if (typeof $.fn.select2 !== 'undefined') {
                    const current = $('#inventory_dropdown').val() || [];
                    $('#inventory_dropdown').val(current.filter(v => v !== id)).trigger('change');
                } else {
                    updateSelectedItems();
                    hitungHarga();
                }
            });
        });

        hitungHarga();
    }

    function refreshQtyDisplay(id) {
        const dropdown = document.getElementById('inventory_dropdown');
        const opt  = dropdown.querySelector(`option[value="${id}"]`);
        const price = parseFloat(opt?.getAttribute('data-price')) || 0;
        const qty  = quantities[id];

        const qtyEl      = document.getElementById(`qty-val-${id}`);
        const subtotalEl = document.getElementById(`subtotal-${id}`);

        if (qtyEl) qtyEl.textContent = qty;
        if (subtotalEl) subtotalEl.textContent = formatRupiah(price * qty);

        hitungHarga();
    }

    // ─── Kalkulasi Harga ──────────────────────────────────────────────────────

    function hitungHarga() {
        // 1. Harga ruangan
        const roomId         = roomSelect?.value;
        const selectedOption = roomSelect?.options[roomSelect.selectedIndex];
        const hargaDasar     = (roomId && selectedOption)
                               ? (parseFloat(selectedOption.getAttribute('data-harga')) || 0)
                               : 0;

        // 2. Diskon internal (hanya ruangan)
        const diskonPersen      = isInternal ? 0.25 : 0;
        const nominalDiskon     = hargaDasar * diskonPersen;
        const hargaSetelahDiskon = hargaDasar - nominalDiskon;

        // 3. Harga barang (sum qty × price)
        const dropdown = document.getElementById('inventory_dropdown');
        const selectedOptions = pinjamBarangCb.checked
            ? Array.from(dropdown.selectedOptions)
            : [];

        let hargaBarang = 0;
        const selectedInventories = [];

        selectedOptions.forEach(opt => {
            const id    = opt.value;
            const price = parseFloat(opt.getAttribute('data-price')) || 0;
            const qty   = quantities[id] || 1;
            const subtotal = price * qty;
            hargaBarang += subtotal;

            selectedInventories.push({
                id, 
                name: opt.getAttribute('data-name') || opt.text.split('(')[0].trim(),
                price,
                quantity: qty,
                subtotal
            });
        });

        // 4. Biaya tambahan hari libur
        const tanggal = dateInput?.value;
        let biayaTambahan = 0, namaHari = '';

        if (tanggal) {
            // new Date('YYYY-MM-DD') bisa menghasilkan UTC; gunakan split untuk local date
            const [y, m, d] = tanggal.split('-').map(Number);
            const day = new Date(y, m - 1, d).getDay(); // 0=Minggu, 6=Sabtu
            if (day === 6) { biayaTambahan = 400000; namaHari = 'Sabtu'; }
            if (day === 0) { biayaTambahan = 500000; namaHari = 'Minggu'; }
        }

        // 5. Total
        const total = hargaSetelahDiskon + hargaBarang + biayaTambahan;

        // 6. Update UI
        if (total > 0) {
            hargaDisplay.value = total.toLocaleString('id-ID');
            priceBreakdown.style.display = 'block';

            breakdownRuangan.innerHTML = `• Harga Ruangan: ${formatRupiah(hargaDasar)}`;

            if (nominalDiskon > 0) {
                breakdownDiskon.style.display = 'block';
                breakdownDiskon.innerHTML =
                    `• <span class="text-discount">Diskon 25% Civitas FIK UI:</span> -${formatRupiah(nominalDiskon)}`;
            } else {
                breakdownDiskon.style.display = 'none';
            }

            breakdownBarang.innerHTML   = `• Harga Barang: ${formatRupiah(hargaBarang)}`;
            breakdownTambahan.innerHTML = biayaTambahan > 0
                ? `• Biaya Tambahan Hari ${namaHari}: <span class="text-warning">+${formatRupiah(biayaTambahan)}</span>`
                : `• Biaya Tambahan: ${formatRupiah(0)}`;

            breakdownTotal.textContent = total.toLocaleString('id-ID');
        } else {
            hargaDisplay.value = '';
            priceBreakdown.style.display = 'none';
        }

        hargaHidden.value = total;

        // Denda
        const denda = parseFloat(selectedOption?.getAttribute('data-denda')) || 0;
        dendaDisplay.value = denda > 0 ? denda.toLocaleString('id-ID') : '';
        dendaHidden.value  = denda;

        // Simpan JSON ke hidden field
        selectedInvInput.value = JSON.stringify(selectedInventories);
    }

    // ─── Event Listeners ──────────────────────────────────────────────────────

    roomSelect?.addEventListener('change', hitungHarga);
    dateInput?.addEventListener('change', hitungHarga);

    // Hitung saat halaman pertama load
    setTimeout(hitungHarga, 300);

    // ─── Validasi Submit ──────────────────────────────────────────────────────

    document.getElementById('bookingForm').addEventListener('submit', function (e) {
        const inventories = JSON.parse(selectedInvInput.value || '[]');
        const tanpaRuangan = roomSelect.value === '';

        if (tanpaRuangan && inventories.length === 0) {
            e.preventDefault();
            alert(
                '⚠️ Anda memilih "Tanpa Ruangan", tetapi tidak memilih barang yang ingin dipinjam.\n\n' +
                'Silakan pilih minimal 1 barang atau pilih ruangan.'
            );
            return false;
        }

        if (tanpaRuangan && !pinjamBarangCb.checked) {
            e.preventDefault();
            alert('⚠️ Anda harus memilih minimal 1 ruangan atau mengaktifkan peminjaman barang.');
            return false;
        }
    });

});
</script>
@endpush