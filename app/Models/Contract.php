<?php

namespace App\Models;

use App\Support\HasAdvancedFilter;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use HasFactory, HasAdvancedFilter, SoftDeletes;

    public $table = 'contracts';

    protected $fillable = [
        'company_id',
        'type_period_id',
        'setup_at',
        'terminated_at',
        'billed_at',
        'validated_at',
    ];

    public $orderable = [
        'id',
        'company.name',
        'company.address',
        'setup_at',
        'terminated_at',
        'billed_at',
        'validated_at',
    ];

    protected $dates = [
        'setup_at',
        'terminated_at',
        'billed_at',
        'validated_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public $filterable = [
        'id',
        'company.name',
        'company.address',
        'setup_at',
        'terminated_at',
        'billed_at',
        'validated_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function isActive()
    {
        return Carbon::now()
            ->isBetween(
                Carbon::createFromFormat(config('project.date_format'), $this->setup_at),
                Carbon::createFromFormat(config('project.date_format'), $this->terminated_at)
            );
    }

    public function getLastBillingDateAttribute()
    {
        $today = Carbon::now();
    }

    public function isTerminationMonth($dateStart): bool
    {
        return $this->terminated_at
            && Carbon::createFromFormat(config('project.date_format'), $this->terminated_at)->year === $dateStart->year
            && Carbon::createFromFormat(config('project.date_format'), $this->terminated_at)->month === $dateStart->month;
    }

    public function calculateBillingPeriod($dateStart)
    {
        if (! $this->type_period || ! $this->type_period->nb_month) {
            return null;
        }

        $nbMonth   = $this->type_period->nb_month;
        $setupDay  = Carbon::createFromFormat(config('project.date_format'), $this->setup_at)->day;
        $baseDate  = Carbon::createFromFormat(config('project.date_format'), $dateStart);

        // début de période : même jour que le setup, limité aux jours du mois
        $day = min($setupDay, $baseDate->daysInMonth);
        $startBilling = $baseDate->copy()->day($day);

        // si mois de terminaison, on termine à la date de fin réelle
        if ($this->isTerminationMonth($baseDate)) {
            $endBilling = Carbon::createFromFormat(config('project.date_format'),$this->terminated_at);
        } else {
            // sinon 1 mois * nbMonth moins 1 jour
            $endBilling = $startBilling->copy()->addMonths($nbMonth)->subDay();
        }

        return $startBilling->format(config('project.date_format'))
             . ' au '
             . $endBilling->format(config('project.date_format'));
    }

    public function calculateTotalPrice(Carbon $dateStart)
    {
        return $this->contract_product_detail
            ->sum(fn($detail) => $detail->proratedBase($dateStart));
    }

    public function calculateTotalPriceFormatted(Carbon $dateStart)
    {
        $total = $this->contract_product_detail
            ->sum(fn($detail) => $detail->proratedBase($dateStart));

        return number_format($total, 2, ',', ' ');
    }

    public function calculateTotalPriceWithVat(Carbon $dateStart): string
    {
        return $this->contract_product_detail
            ->sum(fn($detail) => $detail->proratedWithVat($dateStart));
    }

    public function calculateTotalPriceWithVatFormatted(Carbon $dateStart): string
    {
        $total = $this->contract_product_detail
            ->sum(fn($detail) => $detail->proratedWithVat($dateStart));

        return number_format($total, 2, ',', ' ');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function type_period()
    {
        return $this->belongsTo(TypePeriod::class);
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    public function lastBill()
    {
        return $this->hasOne(Bill::class)->latestOfMany();
    }

    public function products()
    {
        return $this->belongsToMany(TypeProduct::class, 'contract_product_details')
            ->withPivot([
                'id',
                'designation',
                'quantity',
                'capacity',
                'monthly_unit_price_without_taxe',
                'billing_started_at',
                'billing_terminated_at',
                'last_billed_at',
            ])
            ->withTimestamps();
    }

    public function getTypeContractAttribute()
    {
        return $this->products()->first()->type_contract ?? null;
    }

    public function amountForPeriod($invoiceDate): float
    {
        $invoiceMonth = Carbon::parse($invoiceDate)->startOfMonth();
        $setup        = Carbon::parse($this->setup_at);

        $monthlyTotal = $this->monthly_unit_price_without_taxe * $this->quantity;

        if ($setup->greaterThan($invoiceMonth)) {
            $daysInMonth = $invoiceMonth->daysInMonth;
            $daysUsed    = $daysInMonth - $setup->day + 1;

            return round($monthlyTotal * $daysUsed / $daysInMonth, 2);
        }

        // Sinon c’est un mois complet
        return round($monthlyTotal, 2);
    }

    public function contract_product_detail()
    {
        return $this->hasMany(ContractProductDetail::class);
    }

    public function getSetupAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d', $value)->format(config('project.date_format')) : null;
    }

    public function setSetupAtAttribute($value)
    {
        $this->attributes['setup_at'] = $value ? Carbon::createFromFormat(config('project.date_format'), $value)->format('Y-m-d') : null;
    }

    public function getTerminatedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d', $value)->format(config('project.date_format')) : null;
    }

    public function setTerminatedAtAttribute($value)
    {
        $this->attributes['terminated_at'] = $value ? Carbon::createFromFormat(config('project.date_format'), $value)->format('Y-m-d') : null;
    }

    public function getBilledAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d', $value)->format(config('project.date_format')) : null;
    }

    public function setBilledAtAttribute($value)
    {
        $this->attributes['billed_at'] = $value ? Carbon::createFromFormat(config('project.date_format'), $value)->format('Y-m-d') : null;
    }

    public function getValidatedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d', $value)->format(config('project.date_format')) : null;
    }

    public function setValidatedAtAttribute($value)
    {
        $this->attributes['validated_at'] = $value ? Carbon::createFromFormat(config('project.date_format'), $value)->format('Y-m-d') : null;
    }

    public function getCreatedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('project.date_format')) : null;
    }

    public function getUpdatedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('project.date_format')) : null;
    }

    public function getDeletedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('project.date_format')) : null;
    }
}
