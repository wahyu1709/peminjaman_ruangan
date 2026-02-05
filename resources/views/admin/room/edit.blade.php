@extends('layouts/app')

@section('content')
<h1 class="h3 mb-4 text-gray-800">
    {{ $title }}
</h1>

<div class="card">
    <div class="card-header bg-warning">
        <a href="{{ route('room') }}" class="btn btn-sm btn-success">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali
        </a>
    </div>
    <div class="card-body">
        <form action="{{ route('roomUpdate', $room->id) }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('put')
        <div class="row">
            <div class="col-xl-6 mb-2">
                <label class="form-label">
                    <span class="text-danger">*</span>
                    Nama Ruangan :
                </label>
                <input type="text" name="nama_ruangan" class="form-control @error('nama_ruangan') is-invalid @enderror" value="{{ $room->nama_ruangan }}">
                @error('nama_ruangan')
                    <small class="text-danger">
                        {{ $message }}
                    </small>
                @enderror
            </div>
            <div class="col-xl-6">
                <label class="form-label">
                    <span class="text-danger">*</span>
                    Kode Ruangan :
                </label>
                <input type="text" name="kode_ruangan" class="form-control @error('kode_ruangan') is-invalid @enderror" value="{{ $room->kode_ruangan }}">
                @error('kode_ruangan')
                    <small class="text-danger">
                        {{ $message }}
                    </small>
                @enderror
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-xl-6 mb-2">
                <label class="form-label">
                    <span class="text-danger">*</span>
                    Lokasi :
                </label>
                <input type="text" name="lokasi" class="form-control @error('lokasi') is-invalid @enderror" value="{{ $room->lokasi }}">
                @error('lokasi')
                    <small class="text-danger">
                        {{ $message }}
                    </small>
                @enderror
            </div>
            <div class="col-xl-6">
                <label class="form-label">
                    <span class="text-danger">*</span>
                    Kapasitas :
                </label>
                <input type="number" name="kapasitas" class="form-control @error('kapasitas') is-invalid @enderror" value="{{ $room->kapasitas }}">
                @error('kapasitas')
                    <small class="text-danger">
                        {{ $message }}
                    </small>
                @enderror
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-xl-6 mb-2">
                <label class="form-label">
                    Harga Sewa/Hari :
                </label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" name="harga_sewa_per_hari" class="form-control" 
                        value="{{ old('harga_sewa_per_hari', $room->harga_sewa_per_hari) }}" placeholder="Contoh: 13000000">
                </div>
                <small class="text-muted">Biarkan kosong jika ruangan gratis</small>
                @error('harga_sewa_per_hari')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-xl-6 mb-2">
                <label class="form-label">
                    Denda/Hari :
                </label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" name="denda_per_hari" class="form-control" 
                        value="{{ old('denda_per_hari', $room->denda_per_hari) }}" placeholder="Contoh: 500000">
                </div>
                <small class="text-muted">Biarkan kosong jika tidak ada denda</small>
                @error('denda_per_hari')
                    <small class="text-danger">
                        {{ $message }}
                    </small>
                @enderror
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-xl-6 mb-2">
                <label class="form-label">
                    <span class="text-danger">*</span>
                    Foto Ruangan :
                </label>
                <input type="file" name="gambar" class="form-control @error('gambar') is-invalid @enderror" accept="image/*">
                
                <!-- Tampilkan gambar saat ini + tombol hapus -->
                @if ($room->gambar)
                    <div class="mt-2">
                        <img src="{{ asset('storage/' . $room->gambar) }}" 
                             alt="Gambar {{ $room->nama_ruangan }}" 
                             class="img-thumbnail" 
                             style="max-height: 150px;">
                        <div class="mt-2">
                            <button type="button" class="btn btn-sm btn-danger" id="deleteImageBtn">
                                <i class="fas fa-trash mr-1"></i> Hapus Gambar
                            </button>
                        </div>
                    </div>
                @endif
                
                @error('gambar')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-xl-6 mb-2">
                <label class="form-label">
                    <span class="text-danger">*</span>
                    Ketersediaan :
                </label>
                <select name="is_active" class="form-control @error('is_active') is-invalid @enderror">
                    <option value="" selected disabled>--Pilih Ketersediaan--</option>
                    <option value="1" {{ $room->is_active == 1 ? 'selected' : '' }}>Tersedia</option>
                    <option value="0" {{ $room->is_active == 0 ? 'selected' : '' }}>Tidak Tersedia</option>
                </select>
                @error('is_active')
                    <small class="text-danger">
                        {{ $message }}
                    </small>
                @enderror
            </div>
        </div>

        <div>
            <button type="submit" class="btn btn-warning mt-4">
                <i class="fas fa-edit mr-2"></i>
                Edit
            </button>
        </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Hapus gambar
    $('#deleteImageBtn').on('click', function() {
        Swal.fire({
            title: 'Hapus Foto?',
            text: 'Yakin ingin menghapus foto ruangan? Aksi ini tidak bisa dikembalikan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Kirim request DELETE
                $.ajax({
                    url: "{{ route('rooms.delete-image', $room->id) }}",
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.fire('Berhasil!', response.message, 'success').then(() => {
                            // Update UI
                            $('#deleteImageBtn').parent().remove();
                            $('.img-thumbnail').remove();
                            $('input[name="gambar"]').val('');
                            window.location.reload();
                        });
                    },
                    error: function(xhr) {
                        if (xhr.status === 419) {
                            Swal.fire('Error!', 'Sesi telah kadaluarsa. Silakan refresh halaman.', 'error');
                        } else {
                            Swal.fire('Error!', 'Gagal menghapus gambar', 'error');
                        }
                    }
                });
            }
        });
    });
});
</script>
@endpush