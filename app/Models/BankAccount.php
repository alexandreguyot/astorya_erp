<?php

namespace App\Models;

use App\Support\HasAdvancedFilter;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankAccount extends Model
{
    use HasFactory, HasAdvancedFilter, SoftDeletes;

    public $table = 'bank_accounts';

    protected $fillable = [
        'no_rum',
        'effective_start_date',
        'bic',
        'iban',
    ];

    public $orderable = [
        'id',
        'no_rum',
        'effective_start_date',
        'bic',
        'iban',
    ];

    public $filterable = [
        'id',
        'no_rum',
        'effective_start_date',
        'bic',
        'iban',
    ];

    protected $dates = [
        'effective_start_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getEffectiveStartDateAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('project.')) : null;
    }

    public function setEffectiveStartDateAttribute($value)
    {
        $this->attributes['effective_start_date'] = $value ? Carbon::createFromFormat(config('project.'), $value)->format('Y-m-d H:i:s') : null;
    }

    public function getCreatedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('project.')) : null;
    }

    public function getUpdatedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('project.')) : null;
    }

    public function getDeletedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('project.')) : null;
    }
}
