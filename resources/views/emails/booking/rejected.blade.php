@component('mail::message')
# ❌ Peminjaman Ruangan Ditolak

Halo **{{ $booking->user->name }}**,

Mohon maaf, peminjaman ruangan Anda **ditolak** oleh admin.

---

### Detail Peminjaman:
- **Ruangan:** {{ $booking->room->nama_ruangan }}
- **Tanggal:** {{ \Carbon\Carbon::parse($booking->tanggal_pinjam)->isoFormat('dddd, D MMMM Y') }}
- **Waktu:** {{ \Carbon\Carbon::parse($booking->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->waktu_selesai)->format('H:i') }}
- **Keperluan:** {{ $booking->keperluan }}

@if($comment)
### 🚫 Alasan Penolakan:
> {{ $comment }}
@endif

Silakan ajukan ulang jika diperlukan.

Salam,<br>
{{ config('app.name') }}
@endcomponent