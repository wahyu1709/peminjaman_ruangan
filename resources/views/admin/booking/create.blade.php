@push('styles')
<style>
#price-breakdown {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-left: 4px solid #ffd700;
    animation: fadeIn 0.3s ease-in;
}

#price-breakdown strong {
    color: #ffd700;
}

#price-breakdown .text-success {
    color: #90ee90 !important;
    font-weight: bold;
}

#price-breakdown .text-warning {
    color: #ffa500 !important;
    font-weight: bold;
}

#price-breakdown .text-primary {
    color: #ffd700 !important;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
</style>
@endpush

@extends('layouts.app')

@section('content')
<h1 class="h3 mb-4 text-gray-800">{{ $title ?? 'Form Pinjam Ruangan' }}</h1>

<div class="card shadow">
    <!-- Header -->
    <div class="card-header bg-primary text-white">
        <a href="{{ route('booking') }}" class="btn btn-sm btn-light">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <div class="card-body">
        <!-- Alert Perpanjangan -->
        @if(isset($data))
        <div class="alert alert-info">
            <i class="fas fa-clock mr-2"></i>
            <strong>Perpanjangan Booking</strong><br>
            Ruangan: <strong>{{ $rooms->firstWhere('id', $data['room_id'])?->nama_ruangan ?? '-' }}</strong>
        </div>
        @endif

        <!-- Alert Pengguna Umum -->
        @if(auth()->user()->jenis_pengguna === 'umum')
        <div class="alert alert-warning">
            <i class="fas fa-info-circle mr-2"></i>
            <strong>Pengguna Umum:</strong> Semua ruangan dikenakan biaya sewa.
        </div>
        @endif

        <!-- Alert Civitas FIK UI -->
        <div class="alert alert-info">
            <i class="fas fa-info-circle mr-2"></i>
            <strong>Civitas FIK UI:</strong> 
            @if(auth()->user()->jenis_pengguna !== 'umum')
                <span class="badge badge-success">Diskon 25% Aktif</span>
                Diskon berlaku untuk kegiatan <em>profit & menunjang Tridharma FIK UI</em>.
            @else
                Diskon 25% hanya untuk Civitas FIK UI.
            @endif
            Untuk permohonan tertulis, hubungi Wakil Dekan Sumber Daya.
        </div>

        @if(auth()->user()->jenis_pengguna !== 'umum')
        <div class="alert alert-success border-left-success">
            <i class="fas fa-tags mr-2"></i>
            Anda mendapatkan <strong>diskon 25%</strong> sebagai Civitas FIK UI. 
            Diskon akan otomatis diterapkan pada harga sewa.
        </div>
        @endif

        <!-- Form -->
        <form action="{{ route('bookingStore') }}" method="POST">
            @csrf

            <!-- Ruangan & Tanggal -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Ruangan <span class="text-danger">*</span></label>
                    <select name="room_id" id="room_id" class="form-control @error('room_id') is-invalid @enderror" required>
                        <option value="" disabled selected>-- Pilih Ruangan --</option>
                        @foreach($rooms as $room)
                            @php
                                $hargaDasar = $room->harga_sewa_per_hari ?? 0;
                            @endphp
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

            <!-- Role/Unit & Waktu -->
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
                           placeholder="Masukkan peran / unit kerja..." style="display: none;">
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

            <!-- Harga & Denda -->
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

            <!-- Keperluan -->
            <div class="mb-3">
                <label>Keperluan <span class="text-danger">*</span></label>
                <textarea name="keperluan" class="form-control @error('keperluan') is-invalid @enderror" 
                          rows="4" required>{{ old('keperluan', $data['keperluan'] ?? '') }}</textarea>
                @error('keperluan') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <!-- Submit -->
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save mr-2"></i> Submit Peminjaman
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // === ROLE/UNIT LOGIC ===
    const roleSelect = document.getElementById('role_unit_select');
    const roleOther = document.getElementById('role_unit_other');
    const roleFinal = document.getElementById('role_unit_final');

    roleSelect?.addEventListener('change', function() {
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

    roleOther?.addEventListener('input', function() {
        roleFinal.value = this.value;
    });

    // === PRICE CALCULATION WITH DISCOUNT ===
    const roomSelect = document.getElementById('room_id');
    const dateInput = document.getElementById('tanggal_pinjam');
    const hargaDisplay = document.getElementById('harga_display');
    const hargaHidden = document.getElementById('harga_sewa_per_hari');
    const dendaDisplay = document.getElementById('denda_display');
    const dendaHidden = document.getElementById('denda_per_hari');

    // User type from backend
    const userType = @json(auth()->user()->jenis_pengguna);

    function calculatePrice() {
        if (!roomSelect?.value || !dateInput?.value) {
            hargaDisplay.value = '';
            hargaHidden.value = '';
            dendaDisplay.value = '';
            dendaHidden.value = '';
            return;
        }

        const option = roomSelect.options[roomSelect.selectedIndex];
        const hargaDasar = parseFloat(option.getAttribute('data-harga')) || 0;
        const denda = parseFloat(option.getAttribute('data-denda')) || 0;

        // Step 1: Apply 25% discount for FIK UI (non-external users)
        const diskon = (userType !== 'umum') ? 0.25 : 0;
        const hargaSetelahDiskon = hargaDasar * (1 - diskon);

        // Step 2: Weekend additional fees
        const date = new Date(dateInput.value);
        const day = date.getDay();
        let biayaTambahan = 0;
        let namaHari = '';

        if (day === 6) { // Saturday
            biayaTambahan = 400000; // Cleaning (100k) + Technician (300k)
            namaHari = 'Sabtu';
        } else if (day === 0) { // Sunday
            biayaTambahan = 500000; // Cleaning (200k) + Technician (300k)
            namaHari = 'Minggu';
        }

        // Step 3: Total amount
        const total = hargaSetelahDiskon + biayaTambahan;

        // Update display
        if (total > 0) {
            hargaDisplay.value = total.toLocaleString('id-ID');
            
            // Show breakdown tooltip/info
            updatePriceBreakdown(hargaDasar, diskon, hargaSetelahDiskon, biayaTambahan, namaHari, total);
        } else {
            hargaDisplay.value = '';
        }

        hargaHidden.value = total;
        dendaDisplay.value = denda > 0 ? denda.toLocaleString('id-ID') : '';
        dendaHidden.value = denda;
    }

    function updatePriceBreakdown(hargaDasar, diskon, hargaSetelahDiskon, biayaTambahan, namaHari, total) {
        // Remove old breakdown if exists
        const oldBreakdown = document.getElementById('price-breakdown');
        if (oldBreakdown) oldBreakdown.remove();

        // Create new breakdown
        if (hargaDasar > 0) {
            const breakdown = document.createElement('div');
            breakdown.id = 'price-breakdown';
            breakdown.className = 'alert alert-info mt-2 mb-0';
            breakdown.style.fontSize = '0.875rem';
            
            let html = '<strong><i class="fas fa-calculator mr-1"></i> Rincian Biaya:</strong><br>';
            html += `<small>`;
            html += `• Harga Dasar: Rp ${hargaDasar.toLocaleString('id-ID')}<br>`;
            
            if (diskon > 0) {
                html += `• Diskon Civitas FIK UI (25%): <span class="text-success">-Rp ${(hargaDasar * diskon).toLocaleString('id-ID')}</span><br>`;
                html += `• Harga Setelah Diskon: Rp ${hargaSetelahDiskon.toLocaleString('id-ID')}<br>`;
            }
            
            if (biayaTambahan > 0) {
                html += `• Biaya Tambahan Hari ${namaHari}: <span class="text-warning">+Rp ${biayaTambahan.toLocaleString('id-ID')}</span><br>`;
            }
            
            html += `<strong class="text-primary">Total: Rp ${total.toLocaleString('id-ID')}</strong>`;
            html += `</small>`;
            
            breakdown.innerHTML = html;
            
            // Insert after harga display
            const hargaContainer = hargaDisplay.closest('.col-md-6');
            hargaContainer.appendChild(breakdown);
        }
    }

    roomSelect?.addEventListener('change', calculatePrice);
    dateInput?.addEventListener('change', calculatePrice);

    // Initial calculation
    setTimeout(calculatePrice, 300);
});
</script>
@endpush