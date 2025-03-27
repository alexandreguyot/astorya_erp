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
        'established_at',
        'started_at',
        'terminated_at',
        'billed_at',
        'validated_at',
    ];

    public $orderable = [
        'id',
        'company.name',
        'company.address',
        'setup_at',
        'established_at',
        'started_at',
        'terminated_at',
        'billed_at',
        'validated_at',
    ];

    protected $dates = [
        'setup_at',
        'established_at',
        'started_at',
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
        'established_at',
        'started_at',
        'terminated_at',
        'billed_at',
        'validated_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function getSetupAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('project.datetime_format')) : null;
    }

    public function setSetupAtAttribute($value)
    {
        $this->attributes['setup_at'] = $value ? Carbon::createFromFormat(config('project.datetime_format'), $value)->format('Y-m-d H:i:s') : null;
    }

    public function getEstablishedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('project.datetime_format')) : null;
    }

    public function setEstablishedAtAttribute($value)
    {
        $this->attributes['established_at'] = $value ? Carbon::createFromFormat(config('project.datetime_format'), $value)->format('Y-m-d H:i:s') : null;
    }

    public function getStartedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('project.datetime_format')) : null;
    }

    public function setStartedAtAttribute($value)
    {
        $this->attributes['started_at'] = $value ? Carbon::createFromFormat(config('project.datetime_format'), $value)->format('Y-m-d H:i:s') : null;
    }

    public function getTerminatedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('project.datetime_format')) : null;
    }

    public function setTerminatedAtAttribute($value)
    {
        $this->attributes['terminated_at'] = $value ? Carbon::createFromFormat(config('project.datetime_format'), $value)->format('Y-m-d H:i:s') : null;
    }

    public function getBilledAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('project.datetime_format')) : null;
    }

    public function setBilledAtAttribute($value)
    {
        $this->attributes['billed_at'] = $value ? Carbon::createFromFormat(config('project.datetime_format'), $value)->format('Y-m-d H:i:s') : null;
    }

    public function getValidatedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('project.datetime_format')) : null;
    }

    public function setValidatedAtAttribute($value)
    {
        $this->attributes['validated_at'] = $value ? Carbon::createFromFormat(config('project.datetime_format'), $value)->format('Y-m-d H:i:s') : null;
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
