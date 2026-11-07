<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bookings extends Model
{
    use HasFactory;

    public const OPEN_STATUSES = ['pending', 'confirmed', 'active'];

    public const RETURNABLE_STATUSES = ['confirmed', 'active'];

    protected $fillable = [
        'car_id',
        'customer_id',
        'start_date',
        'end_date',
        'total_price',
        'status',
        'payment_status',
        'return_requested_at',
        'returned_at',
        'return_notes',
        'reference',
        'special_requests',
        'customer_registered',
        'email_sent_at',
    ];

    protected $casts = [
        'return_requested_at' => 'datetime',
        'returned_at' => 'datetime',
        'email_sent_at' => 'datetime',
        'customer_registered' => 'boolean',
        'total_price' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function isOpen(): bool
    {
        return in_array($this->status, self::OPEN_STATUSES, true);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isReturnRequested(): bool
    {
        return in_array($this->status, self::RETURNABLE_STATUSES, true)
            && $this->return_requested_at !== null;
    }

    public function isDueForReturn(): bool
    {
        if (! in_array($this->status, self::RETURNABLE_STATUSES, true)) {
            return false;
        }

        $returnDeadline = Carbon::parse($this->end_date)->addDay()->startOfDay();

        return now()->greaterThanOrEqualTo($returnDeadline) || $this->isReturnRequested();
    }

    public function isOverdue(): bool
    {
        if (! in_array($this->status, self::RETURNABLE_STATUSES, true)) {
            return false;
        }

        $returnDeadline = Carbon::parse($this->end_date)->addDay()->startOfDay();

        return now()->greaterThanOrEqualTo($returnDeadline);
    }

    public function scopeActiveOrPending(Builder $query): Builder
    {
        return $query->whereIn('status', self::OPEN_STATUSES);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    public function scopePendingReturn(Builder $query): Builder
    {
        return $query->whereIn('status', self::RETURNABLE_STATUSES)
            ->where(function (Builder $subQuery) {
                $subQuery->whereNotNull('return_requested_at')
                    ->orWhereDate('end_date', '<', today());
            });
    }

    public function scopeReservesDates(Builder $query): Builder
    {
        return $query->whereIn('status', self::OPEN_STATUSES);
    }

    public function scopeForMonth(Builder $query, int $year, int $month): Builder
    {
        return $query->where('status', 'completed')
            ->where(function (Builder $q) use ($year, $month) {
                $q->whereYear('returned_at', $year)
                    ->whereMonth('returned_at', $month)
                    ->orWhere(function (Builder $inner) use ($year, $month) {
                        $inner->whereNull('returned_at')
                            ->whereYear('end_date', $year)
                            ->whereMonth('end_date', $month);
                    });
            });
    }
}
