<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoRejectExpiredPendingBookings extends Command
{
    protected $signature = 'booking:auto-reject-expired';
    protected $description = 'Menolak otomatis booking pending yang sudah melewati waktu mulai';

    public function handle()
    {
        $expiredPending = Booking::where('status', 'pending')
            ->where('waktu_mulai', '<', now())
            ->get();

        foreach ($expiredPending as $booking) {
            $booking->update(['status' => 'rejected']);
            Log::info("Booking ID {$booking->id} ditolak otomatis karena sudah lewat waktu.");
        }

        $this->info("{$expiredPending->count()} booking ditolak otomatis.");
    }
}
