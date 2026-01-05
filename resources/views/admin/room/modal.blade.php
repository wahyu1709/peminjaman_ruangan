<!-- Modal -->
<div class="modal fade" id="exampleModal{{ $room->id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="exampleModalLabel">Hapus {{ $title }} ?</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true" class="text-white">&times;</span>
        </button>
      </div>
      <div class="modal-body text-left">
        <div class="row">
            <div class="col-6">
                Nama Ruangan
            </div>
            <div class="col-6">
                : {{ $room->nama_ruangan }}
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                Kode Ruangan
            </div>
            <div class="col-6">
                : {{ $room->kode_ruangan }}
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                Lokasi
            </div>
            <div class="col-6">
                : {{ $room->lokasi }}
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                Kapasitas
            </div>
            <div class="col-6">
                : {{ $room->kapasitas }}
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
            <i class="fas fa-times"></i>
            Tutup
        </button>
        <form action="{{ route('roomDestroy',$room->id) }}" method="POST">
            @csrf
            @method('delete')
            <button type="submit" class="btn btn-danger btn-sm">
                <i class="fas fa-trash"></i>
                Hapus
            </button>
        </form>
      </div>
    </div>
  </div>
</div>