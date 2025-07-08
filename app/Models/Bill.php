<?php

namespace App\Models;

use App\Support\HasAdvancedFilter;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Traits\HasPriceCast;

class Bill extends Model {
    use HasFactory, HasAdvancedFilter, SoftDeletes, InteractsWithMedia, HasPriceCast;

    public $table = 'bills';

    protected $casts = [
        'one_bill_per_period' => 'boolean',
    ];

    protected $dates = [
        'started_at',
        'billed_at',
        'generated_at',
        'validated_at',
        'sent_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'no_bill',
        'amount',
        'file_path',
        'amount_vat_included',
        'one_bill_per_period',
        'started_at',
        'billed_at',
        'generated_at',
        'validated_at',
        'sent_at',
        'company_id',
        'type_period_id',
    ];

    public $filterable = [
        'id',
        'no_bill',
        'amount',
        'amount_vat_included',
        'started_at',
        'billed_at',
        'generated_at',
        'validated_at',
        'sent_at',
        'company.name',
        'company.address',
        'type_period.title',
        'type_period.nb_month',
    ];

    public $orderable = [
        'id',
        'no_bill',
        'amount',
        'amount_vat_included',
        'one_bill_per_period',
        'started_at',
        'billed_at',
        'generated_at',
        'validated_at',
        'sent_at',
        'company.name',
        'company.address',
        'type_period.title',
        'type_period.nb_month',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function type_period()
    {
        return $this->belongsTo(TypePeriod::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public static function getLastBillNumber()
    {
        $year = date('Y');

        $lastNumber = self::where('no_bill', 'like', "FACT-$year-%")
            ->selectRaw("CAST(SUBSTRING(no_bill, LENGTH('FACT-$year-') + 1) AS UNSIGNED) as number")
            ->orderByDesc('number')
            ->pluck('number')
            ->first();

        return $lastNumber ?? 1;
    }

    public static function getBillNumber() {
        $last = self::getLastBillNumber();
        $new = $last + 1;
        $no_bill = 'FACT-' . date('Y') . '-' . $new;
        return $no_bill;
    }

    public function getLastBillPeriodAttribute()
    {
        $startedAt = Carbon::createFromFormat(config('project.date_format'), $this->started_at);
        $billedAt = Carbon::createFromFormat(config('project.date_format'), $this->billed_at);
        return 'Du ' . $startedAt->format('d/m/Y') . ' au ' . $billedAt->format('d/m/Y');
    }

    protected function getPriceAttributes(): array
    {
        return [
            'amount',
            'amount_vat_included',
        ];
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getStartedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d', $value)->format(config('project.date_format')) : null;
    }

    public function setStartedAtAttribute($value)
    {
        $this->attributes['started_at'] = $value ? Carbon::createFromFormat(config('project.date_format'), $value)->format('Y-m-d') : null;
    }

    public function getBilledAtAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('project.date_format')) : null;
    }

    public function setBilledAtAttribute($value)
    {
        $this->attributes['billed_at'] = $value ? Carbon::createFromFormat(config('project.date_format'), $value)->format('Y-m-d') : null;
    }

    public function getGeneratedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d', $value)->format(config('project.date_format')) : null;
    }

    public function setGeneratedAtAttribute($value)
    {
        $this->attributes['generated_at'] = $value ? Carbon::createFromFormat(config('project.date_format'), $value)->format('Y-m-d') : null;
    }

    public function getValidatedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d', $value)->format(config('project.date_format')) : null;
    }

    public function setValidatedAtAttribute($value)
    {
        $this->attributes['validated_at'] = $value ? Carbon::createFromFormat(config('project.date_format'), $value)->format('Y-m-d') : null;
    }

    public function getSentAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d', $value)->format(config('project.date_format')) : null;
    }

    public function setSentAtAttribute($value)
    {
        $this->attributes['sent_at'] = $value ? Carbon::createFromFormat(config('project.date_format'), $value)->format('Y-m-d') : null;
    }

    public function getCollectedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d', $value)->format(config('project.date_format')) : null;
    }

    public function setCollectedAtAttribute($value)
    {
        $this->attributes['collected_at'] = $value ? Carbon::createFromFormat(config('project.date_format'), $value)->format('Y-m-d') : null;
    }

    public function getRecordedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d', $value)->format(config('project.date_format')) : null;
    }

    public function setRecordedAtAttribute($value)
    {
        $this->attributes['recorded_at'] = $value ? Carbon::createFromFormat(config('project.date_format'), $value)->format('Y-m-d') : null;
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
