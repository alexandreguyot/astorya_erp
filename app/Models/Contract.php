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

    public function getTotalPriceAttribute()
    {
        return number_format(
            $this->contract_product_detail->sum(function ($detail) {
                return ($detail->monthly_unit_price_without_taxe * $detail->quantity);
            }),
            2,
            ',',
            ''
        );
    }

    public function getTotalPriceWithVatAttribute()
    {
        $total = $this->contract_product_detail->sum(function ($detail) {
            $basePrice = $detail->monthly_unit_price_without_taxe * $detail->quantity;

            $vat = $detail->type_product->type_vat->percent ?? 0;

            $priceWithVat = $basePrice * (1 + ($vat / 100));

            return $priceWithVat;
        });

        return number_format($total, 2, ',', '');
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

    public function type_contract()
    {
        return $this->belongsTo(TypeContract::class);
    }

    public function calculateBillingPeriod($dateStart)
    {
        if (!$this->type_period || !$this->type_period->nb_month) {
            return null;
        }

        $nbMonth = $this->type_period->nb_month;
        $day = Carbon::createFromFormat('d/m/Y', $this->setup_at)->format('d');
        $startBilling = Carbon::createFromFormat('d/m/Y', $dateStart)->day($day);
        $endBilling = $startBilling->copy()->addMonths($nbMonth)->subDay(1);

        return $startBilling->format('d/m/Y') . ' au ' . $endBilling->format('d/m/Y');
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
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('project.datetime_format')) : null;
    }

    public function getUpdatedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('project.datetime_format')) : null;
    }

    public function getDeletedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('project.datetime_format')) : null;
    }
}
