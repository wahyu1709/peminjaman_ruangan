@component('mail::message')
# 🎉 Peminjaman Ruangan Disetujui!

Halo **{{ $booking->user->name }}**,

Peminjaman ruangan Anda telah **disetujui** oleh admin.

---

### Detail Peminjaman:
- **Ruangan:** {{ $booking->room->nama_ruangan }}
- **Tanggal:** {{ \Carbon\Carbon::parse($booking->tanggal_pinjam)->isoFormat('dddd, D MMMM Y') }}
- **Waktu:** {{ \Carbon\Carbon::parse($booking->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->waktu_selesai)->format('H:i') }}
- **Keperluan:** {{ $booking->keperluan }}

@if($comment)
### 💬 Komentar Admin:
> {{ $comment }}
@endif

Terima kasih telah menggunakan sistem peminjaman ruangan kami!

Salam,<br>
{{ config('app.name') }}
@endcomponent