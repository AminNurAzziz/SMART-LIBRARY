<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\ReservasiModel;
use Illuminate\Console\Command;
use Symfony\Component\ErrorHandler\Debug;
use Illuminate\Support\Facades\Log;

class CheckReservationStatus extends Command
{
    protected $signature = 'reservation:check';

    protected $description = 'Check and update reservation status';

    public function handle()
    {
        $reservations = ReservasiModel::where('status', 'menunggu')->get();
        Log::info('Reservations: ' . $reservations);

        foreach ($reservations as $reservation) {
            $expiredDate = Carbon::parse($reservation->tanggal_ambil);
            Log::info('Expired date: ' . $expiredDate);

            if (Carbon::now()->greaterThan($expiredDate)) {
                // Update status reservasi menjadi expired
                $reservation->status = 'gagal';
                $reservation->save();
            }
            Log::info('Reservation status updated successfully.' . $reservation->status);
        }

        $this->info('Reservation status checked and updated successfully.');
    }
}
