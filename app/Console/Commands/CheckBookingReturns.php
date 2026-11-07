<?php

namespace App\Console\Commands;

use App\Models\Bookings;
use Illuminate\Console\Command;

class CheckBookingReturns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:check-returns';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for bookings that have reached their return date and are awaiting return confirmation';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dueBookings = Bookings::with(['car', 'customer'])
            ->pendingReturn()
            ->get();

        if ($dueBookings->isEmpty()) {
            $this->info('No bookings currently due or awaiting return confirmation.');
            return self::SUCCESS;
        }

        $this->warn(sprintf('Found %d booking(s) due or awaiting admin return confirmation:', $dueBookings->count()));

        $tableData = $dueBookings->map(function ($booking) {
            return [
                'ID' => $booking->id,
                'Customer' => $booking->customer?->name ?? 'N/A',
                'Car' => $booking->car ? $booking->car->brand . ' ' . $booking->car->model : 'N/A',
                'End Date' => $booking->end_date,
                'Status' => $booking->status,
                'Return Requested' => $booking->return_requested_at ? $booking->return_requested_at->toDateTimeString() : 'No (Date reached)',
            ];
        });

        $this->table(['ID', 'Customer', 'Car', 'End Date', 'Status', 'Return Requested'], $tableData);

        return self::SUCCESS;
    }
}

