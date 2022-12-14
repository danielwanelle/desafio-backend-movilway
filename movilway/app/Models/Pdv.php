<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Pdv model
 *
 * @package App\Models
 */
class Pdv extends Model
{
    use HasFactory;

    /**
     * Pdv active status
     *
     * @var bool
     */
    private const STATUS_ACTIVE = true;

    /**
     * Pdv inactive status
     *
     * @var bool
     */
    private const STATUS_INACTIVE = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fantasy_name',
        'cnpj',
        'owner_name',
        'owner_phone',
        'sales_limit',
        'active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Get the sales for the Pdv.
     *
     * @return HasMany
     */
    public function sales() : HasMany
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Set a new sales limit for the Pdv.
     *
     * @param float $salesLimit New sales limit
     *
     * @return self|null
     */
    public function updateSalesLimit(float $salesLimit) : ?self
    {
        if ($salesLimit < 0) {
            return null;
        }

        $this->sales_limit = $salesLimit;
        $this->save();

        return $this;
    }

    /**
     * Sum Pdv total pending sales value.
     *
     * @return float
     */
    public function getTotalPendingSales() : float
    {
        return $this->sales()->pending()->sum('value');
    }

    /**
     * Get Pdv total free limit.
     *
     * @return float
     */
    public function getFreeLimit() : float
    {
        return $this->sales_limit - $this->getTotalPendingSales();
    }

    /**
     * Check Pdv have limit to receive a sale.
     *
     * @param float $value Sale value
     *
     * @return boolean
     */
    public function canSale(float $value) : bool
    {
        return $this->getFreeLimit() >= $value;
    }

    /**
     * Quit Pdv debt.
     *
     * @param float $value
     * @return boolean
     */
    public function paySalesLimit(float $value) : bool
    {
        $pending = $this->getTotalPendingSales();

        if ($pending === 0) {
            return false;
        }

        if ($pending !== $value) {
            return false;
        }

        return $this->sales()
            ->pending()
            ->update(['status' => Sale::STATUS_PAID]);
    }

    /**
     * Deactivate the Pdv.
     *
     * @return bool
     */
    public function deactivate() : bool
    {
        $this->active = self::STATUS_INACTIVE;
        return $this->save();
    }

    /**
     * Check if Pdv is active.
     *
     * @return boolean
     */
    public function isActive() : bool
    {
        return $this->active === self::STATUS_ACTIVE;
    }

    /**
     * Check if sale belongs to Pdv.
     *
     * @param Sale $sale Sale
     *
     * @return boolean
     */
    public function hasSale(Sale $sale) : bool
    {
        return $this->sales()->where('id', $sale->id)->exists();
    }

    /**
     * Get active Pdvs.
     *
     * @param Builder $query Builder
     *
     * @return void
     */
    public function scopeActive(Builder $query) : void
    {
        $query->where('active', self::STATUS_ACTIVE);
    }
}
