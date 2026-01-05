<!-- Modal -->
<div class="modal fade" id="exampleModal{{ $user->id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                Nama
            </div>
            <div class="col-6">
                : {{ $user->name }}
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                Email
            </div>
            <div class="col-6">
                : {{ $user->email }}
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                Jabatan
            </div>
            <div class="col-6">
                : {{ $user->jenis_pengguna }}
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
            <i class="fas fa-times"></i>
            Tutup
        </button>
        <form action="{{ route('adminDestroy',$user->id) }}" method="POST">
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