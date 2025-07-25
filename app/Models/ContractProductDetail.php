<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class ContractProductDetail extends Model
{
    use HasFactory;

    protected $table = 'contract_product_details';

    protected $fillable = [
        'id',
        'designation',
        'quantity',
        'capacity',
        'monthly_unit_price_without_taxe',
        'billing_started_at',
        'billing_terminated_at',
        'last_billed_at',
        'contract_id',
        'type_product_id',
    ];

    protected $casts = [
        'last_billed_at' => 'date',
    ];

    /**
     * Relation avec Contract
     */
    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    /**
     * Relation avec Product
     */
    public function type_product()
    {
        return $this->belongsTo(TypeProduct::class);
    }

    public function scopeActiveAt(Builder $query, Carbon $date): Builder
    {
        $cutoff = $date->copy()->startOfMonth()->format('Y-m-d');

        return $query->where(function(Builder $q) use ($cutoff) {
            $q->whereNull('billing_terminated_at')
              ->orWhereDate('billing_terminated_at', '0001-01-01')
              ->orWhereDate('billing_terminated_at', '>=', $cutoff);
        });
    }

    /**
     * Montant de base HT, proratisé si on est dans le mois de terminaison.
     */
    public function proratedBase(Carbon $date): float
    {
        $base = $this->monthly_unit_price_without_taxe * $this->quantity;

        $billingTerm = $this->billing_terminated_at ? Carbon::createFromFormat(config('project.date_format'), $this->billing_terminated_at)
        : null;

        if ($billingTerm && $billingTerm->year  === $date->year && $billingTerm->month === $date->month) {
            $startOfMonth  = $date->copy()->startOfMonth();
            $daysInMonth   = $startOfMonth->daysInMonth;
            $startBilling  = $this->billing_started_at
                ? Carbon::createFromFormat(config('project.date_format'), $this->billing_started_at)
                : $startOfMonth;
            $endBilling    = $billingTerm;
            $daysUsed      = $startBilling->diffInDays($endBilling) + 1;
            $base         *= ($daysUsed / $daysInMonth);

            return round($base, 2);
        }

        if ($this->contract->isTerminationMonth($date)) {
            $setupDay     = Carbon::createFromFormat(config('project.date_format'), $this->contract->setup_at)->day;
            $day          = min($setupDay, $date->daysInMonth);
            $startBilling = $date->copy()->day($day);
            $endBilling   = Carbon::createFromFormat(config('project.date_format'), $this->contract->terminated_at);
            $daysUsed     = $startBilling->diffInDays($endBilling) + 1;
            $daysInMonth  = $startBilling->daysInMonth;
            $base        *= $daysUsed / $daysInMonth;
        }

        return round($base, 2);
    }

    public function proratedWithVat(Carbon $date): float
    {
        $vatRate = $this->type_product->type_vat->percent ?? 0;
        return round($this->proratedBase($date) * (1 + $vatRate / 100), 2);
    }

    public function proratedBaseFormatted($dateStart): string
    {
        return number_format($this->proratedBase($dateStart), 2, ',', ' ');
    }

    public function proratedWithVatFormatted($dateStart): string
    {
        return number_format($this->proratedWithVat($dateStart), 2, ',', ' ');
    }

    public function isTerminationMonth($dateStart): bool
    {
        return $this->billing_terminated_at
            && Carbon::createFromFormat(config('project.date_format'), $this->billing_terminated_at)->year === $dateStart->year
            && Carbon::createFromFormat(config('project.date_format'), $this->billing_terminated_at)->month === $dateStart->month;
    }

    public function calculateTotalPriceWithoutTaxe($date) {
        $base = $this->monthly_unit_price_without_taxe * $this->quantity;
        $isTerm = $this->contract->isTerminationMonth($date) || $this->isTerminationMonth($date);

        if ($isTerm) {
            $setupDay  = Carbon::createFromFormat(config('project.date_format'), $this->contract->setup_at)->day;
            $day = min($setupDay, $date->daysInMonth);
            $startBilling = $date->copy()->day($day);
            $endBilling   = Carbon::createFromFormat(config('project.date_format'), $this->contract->terminated_at);
            $daysUsed     = $startBilling->diffInDays($endBilling) + 1;
            $daysInMonth  = $startBilling->daysInMonth;
            $base = $base * ($daysUsed / $daysInMonth);
        }

        return number_format(round($base, 2), 2, ',', ' ');
    }

    public function getTotalPriceAttribute()
    {
        $base = $this->monthly_unit_price_without_taxe * $this->quantity;

        // Proration si facturation terminée ce mois
        if ($this->billing_terminated_at
            && $this->billing_started_at->year === $this->billing_terminated_at->year
            && $this->billing_started_at->month === $this->billing_terminated_at->month) {

            $startOfMonth = $this->billing_started_at->copy()->startOfMonth();
            $endDate      = $this->billing_terminated_at;
            $daysUsed     = $startOfMonth->diffInDays($endDate) + 1;
            $daysInMonth  = $startOfMonth->daysInMonth;

            $base = $base * ($daysUsed / $daysInMonth);
        }

        return round($base, 2);
    }

    public function getFormattedMonthlyUnitPriceWithoutTaxeAttribute()
    {
        return number_format(
            $this->monthly_unit_price_without_taxe,
            2,
            ',',
            ' '
        );
    }

    public function getMonthlyUnitPriceWithTaxeAttribute()
    {
        return number_format(
            $this->monthly_unit_price_without_taxe * (1 + ($this->type_product->type_vat->percent / 100)),
            2,
            ',',
            ' '
        );
    }

    public function getBillingStartedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d', $value)->format(config('project.date_format')) : null;
    }

    public function getBillingTerminatedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d', $value)->format(config('project.date_format')) : null;
    }

}
