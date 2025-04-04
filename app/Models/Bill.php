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

class Bill extends Model implements HasMedia
{
    use HasFactory, HasAdvancedFilter, SoftDeletes, InteractsWithMedia;

    public $table = 'bills';

    protected $appends = [
        'file_path',
    ];

    protected $casts = [
        'one_bill_per_period' => 'boolean',
        'to_be_collected'     => 'boolean',
    ];

    protected $dates = [
        'started_at',
        'billed_at',
        'generated_at',
        'validated_at',
        'sent_at',
        'collected_at',
        'recorded_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'no_bill',
        'amount',
        'amount_vat_included',
        'one_bill_per_period',
        'started_at',
        'billed_at',
        'generated_at',
        'validated_at',
        'sent_at',
        'to_be_collected',
        'collected_at',
        'recorded_at',
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
        'collected_at',
        'recorded_at',
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
        'to_be_collected',
        'collected_at',
        'recorded_at',
        'company.name',
        'company.address',
        'type_period.title',
        'type_period.nb_month',
    ];

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
        return $value ? Carbon::createFromFormat('Y-m-d', $value)->format(config('project.datetime_format')) : null;
    }

    public function setCollectedAtAttribute($value)
    {
        $this->attributes['collected_at'] = $value ? Carbon::createFromFormat(config('project.datetime_format'), $value)->format('Y-m-d') : null;
    }

    public function getRecordedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d', $value)->format(config('project.datetime_format')) : null;
    }

    public function setRecordedAtAttribute($value)
    {
        $this->attributes['recorded_at'] = $value ? Carbon::createFromFormat(config('project.datetime_format'), $value)->format('Y-m-d') : null;
    }

    public function getFilePathAttribute()
    {
        return $this->getMedia('bill_file_path')->map(function ($item) {
            $media        = $item->toArray();
            $media['url'] = $item->getUrl();

            return $media;
        });
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function typePeriod()
    {
        return $this->belongsTo(TypePeriod::class);
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
