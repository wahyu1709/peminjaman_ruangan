<?php

namespace App\Events;

use App\Models\Booking;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Booking $booking)
    {
        $this->booking->load('user', 'room');
    }

    public function broadcastOn(): array
    {
        return [new Channel('admin-dashboard')];
    }

    public function broadcastAs(): string
    {
        return 'booking.created';
    }

    public function broadcastWith(): array
    {
        return [
            'id'        => $this->booking->id,
            'user_name' => $this->booking->user->name,
            'room_name' => $this->booking->room?->nama_ruangan ?? 'Barang Saja',
            'tanggal'   => $this->booking->tanggal_pinjam,
            'waktu'     => \Carbon\Carbon::parse($this->booking->waktu_mulai)->format('H:i')
                         . ' - '
                         . \Carbon\Carbon::parse($this->booking->waktu_selesai)->format('H:i'),
            'keperluan' => $this->booking->keperluan,
            'status'    => $this->booking->status,
        ];
    }
}