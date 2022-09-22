<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Sale model
 *
 * @package App\Models
 */
class Sale extends Model
{
    use HasFactory;

    /**
     * Sale status
     *
     * @var int
     */
    public const STATUS_PAYMENT_PENDING = 0;

    /**
     * Sale status
     *
     * @var int
     */
    public const STATUS_PAID = 1;

    /**
     * Sale status
     *
     * @var int
     */
    public const STATUS_REJECTED = 2;

    /**
     * Sale status
     *
     * @var int
     */
    public const STATUS_CANCELED = 3;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'pdv_id',
        'products',
        'value',
        'cancel_reason',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'products' => 'object',
        'status' => 'integer',
    ];

    /**
     * Get the pdv that owns the sale.
     *
     * @return BelongsTo
     */
    public function pdv() : BelongsTo
    {
        return $this->belongsTo(Pdv::class, 'pdv_id', 'id');
    }

    /**
     * Cancel the sale
     *
     * @param string $reason Reason for cancel
     *
     * @return bool
     */
    public function cancel(string $reason) : bool
    {
        if ($this->status !== self::STATUS_PAYMENT_PENDING) {
            return false;
        }
        
        return $this->update(
            [
                'cancel_reason' => $reason,
                'status' => self::STATUS_CANCELED,
            ]
        );
    }

    /**
     * Get the sales with payment pending status.
     *
     * @param Builder $query Query builder
     *
     * @return void
     */
    public function scopePending(Builder $query) : void
    {
        $query->where('status', self::STATUS_PAYMENT_PENDING);
    }
}
