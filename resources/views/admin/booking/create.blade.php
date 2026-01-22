@extends('layouts.app')

@section('content')
<h1 class="h3 mb-4 text-gray-800">
    {{ $title ?? 'Form Pinjam Ruangan' }}
</h1>

<div class="card">
    <div class="card-header bg-primary">
        <a href="{{ route('booking') }}" class="btn btn-sm btn-success">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali
        </a>
    </div>
    <div class="card-body">
        @if(isset($data))
        <div class="alert alert-info mb-4">
            <i class="fas fa-clock mr-2"></i>
            <strong>Ajukan Perpanjangan</strong><br>
            Anda mengajukan perpanjangan untuk ruangan
            <strong>{{ $rooms->firstWhere('id', $data['room_id'])?->nama_ruangan ?? 'Tidak diketahui' }}</strong>
        </div>
        @endif

        <form action="{{ route('bookingStore') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-xl-6 mb-2">
                    <div class="form-label">
                        <label>Ruangan <span class="text-danger">*</span></label>
                        <select name="room_id" class="form-control @error('room_id') is-invalid @enderror" required>
                            <option value="" disabled selected>-- Pilih Ruangan --</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}" 
                                        {{ (old('room_id', $data['room_id'] ?? null) == $room->id) ? 'selected' : '' }}>
                                    {{ $room->kode_ruangan }} - {{ $room->nama_ruangan }} ({{ $room->kapasitas }} orang) - {{ $room->lokasi }}
                                </option>
                            @endforeach
                        </select>
                        @error('room_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="form-label">
                        <label>Tanggal Pinjam <span class="text-danger">*</span></label>
                        <input type="date" 
                               name="tanggal_pinjam" 
                               class="form-control @error('tanggal_pinjam') is-invalid @enderror" 
                               value="{{ old('tanggal_pinjam', $data['tanggal_pinjam'] ?? null) }}" 
                               min="{{ date('Y-m-d') }}" 
                               required>
                        @error('tanggal_pinjam') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-6 mb-2">
                    <div class="form-label">
                        <label for="role_unit">Peran / Unit Kerja <span class="text-muted">(Opsional)</span></label>
                        <select name="role_unit_select" id="role_unit_select" class="form-control">
                            <option value="" disabled selected>Pilih Peran / Unit Kerja</option>
                            <option value="Panitia Kegiatan" {{ ($data['role_unit'] ?? null) == 'Panitia Kegiatan' ? 'selected' : '' }}>Panitia Kegiatan</option>
                            <option value="Koordinator MK" {{ ($data['role_unit'] ?? null) == 'Koordinator MK' ? 'selected' : '' }}>Koordinator MK</option>
                            <option value="Pengelola PS S1" {{ ($data['role_unit'] ?? null) == 'Pengelola PS S1' ? 'selected' : '' }}>Pengelola PS S1</option>
                            <option value="Pengelola PS S2" {{ ($data['role_unit'] ?? null) == 'Pengelola PS S2' ? 'selected' : '' }}>Pengelola PS S2</option>
                            <option value="Pengelola PS S3" {{ ($data['role_unit'] ?? null) == 'Pengelola PS S3' ? 'selected' : '' }}>Pengelola PS S3</option>
                            <option value="Departemen Kep Dasar" {{ ($data['role_unit'] ?? null) == 'Departemen Kep Dasar' ? 'selected' : '' }}>Departemen Kep. Dasar</option>
                            <option value="Departemen Kep Komunitas" {{ ($data['role_unit'] ?? null) == 'Departemen Kep Komunitas' ? 'selected' : '' }}>Departemen Kep. Komunitas</option>
                            <option value="Departemen Kep Maternitas" {{ ($data['role_unit'] ?? null) == 'Departemen Kep Maternitas' ? 'selected' : '' }}>Departemen Kep. Maternitas</option>
                            <option value="Departemen KMB" {{ ($data['role_unit'] ?? null) == 'Departemen KMB' ? 'selected' : '' }}>Departemen KMB</option>
                            <option value="Departemen Kep Jiwa" {{ ($data['role_unit'] ?? null) == 'Departemen Kep Jiwa' ? 'selected' : '' }}>Departemen Kep. Jiwa</option>
                            <option value="Departemen Kep Anak" {{ ($data['role_unit'] ?? null) == 'Departemen Kep Anak' ? 'selected' : '' }}>Departemen Kep. Anak</option>
                            <option value="other">Lainnya...</option>
                        </select>

                        <input type="text" 
                               name="role_unit_other" 
                               id="role_unit_other" 
                               class="form-control mt-2" 
                               placeholder="Masukkan peran / unit kerja Anda..." 
                               style="display: none;">

                        <!-- Hidden field untuk menyimpan nilai akhir -->
                        <input type="hidden" name="role_unit" id="role_unit_final">
                    </div>
                </div>
                <div class="col-xl-3 mb-2">
                    <div class="form-label">
                        <label>Jam Mulai <span class="text-danger">*</span></label>
                        <input type="time" 
                               name="waktu_mulai" 
                               class="form-control @error('waktu_mulai') is-invalid @enderror" 
                               value="{{ old('waktu_mulai', $data['waktu_mulai'] ?? '') }}" 
                               required>
                        @error('waktu_mulai') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>
                <div class="col-xl-3 mb-2">
                    <div class="form-label">
                        <label>Jam Selesai <span class="text-danger">*</span></label>
                        <input type="time" 
                               name="waktu_selesai" 
                               class="form-control @error('waktu_selesai') is-invalid @enderror" 
                               value="{{ old('waktu_selesai') }}" 
                               required>
                        @error('waktu_selesai') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="form-label">
                        <label>Keperluan <span class="text-danger">*</span></label>
                        <textarea name="keperluan" 
                                  class="form-control @error('keperluan') is-invalid @enderror" 
                                  rows="4" 
                                  required>{{ old('keperluan', $data['keperluan'] ?? '') }}</textarea>
                        @error('keperluan') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>
            </div>

            <div>
                <button type="submit" class="btn btn-primary mt-4">
                    <i class="fas fa-save mr-2"></i> Submit Peminjaman
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {
    const select = document.getElementById('role_unit_select');
    const otherInput = document.getElementById('role_unit_other');
    const finalInput = document.getElementById('role_unit_final');

    // Set nilai awal jika ada data
    @if(isset($data['role_unit']))
        const initialRole = "{{ $data['role_unit'] }}";
        const predefinedRoles = [
            'Panitia Kegiatan', 'Koordinator MK', 'Pengelola PS S1',
            'Pengelola PS S2', 'Pengelola PS S3', 'Departemen Kep Dasar',
            'Departemen Kep Komunitas', 'Departemen Kep Maternitas',
            'Departemen KMB', 'Departemen Kep Jiwa', 'Departemen Kep Anak'
        ];

        if (!predefinedRoles.includes(initialRole)) {
            select.value = 'other';
            otherInput.style.display = 'block';
            otherInput.value = initialRole;
            finalInput.value = initialRole;
        } else {
            select.value = initialRole;
            finalInput.value = initialRole;
        }
    @endif

    // Fungsi update nilai akhir
    function updateFinalValue() {
        if (select.value === 'other') {
            finalInput.value = otherInput.value;
        } else {
            finalInput.value = select.value;
        }
    }

    // Saat pilih opsi
    select.addEventListener('change', function () {
        if (this.value === 'other') {
            otherInput.style.display = 'block';
            otherInput.focus();
        } else {
            otherInput.style.display = 'none';
            otherInput.value = '';
        }
        updateFinalValue();
    });

    // Saat ketik di input manual
    otherInput.addEventListener('input', updateFinalValue);

    // Inisialisasi nilai pertama kali
    updateFinalValue();
});
</script>