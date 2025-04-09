<?php

namespace App\Models;

use App\Support\HasAdvancedFilter;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, HasAdvancedFilter, SoftDeletes;

    public $table = 'companies';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'send_bill_type'      => 'boolean',
        'one_bill_per_period' => 'boolean',
    ];

    public $filterable = [
        'id',
        'name',
        'address',
        'address_compl',
        'city.name',
        'city.zip_code',
        'email',
        'accounting',
        'ciel_reference',
        'bill_payment_method',
        'observations',
    ];

    protected $fillable = [
        'name',
        'address',
        'address_compl',
        'city_id',
        'email',
        'accounting',
        'ciel_reference',
        'send_bill_type',
        'one_bill_per_period',
        'bill_payment_method',
        'observations',
    ];

    public $orderable = [
        'id',
        'name',
        'address',
        'address_compl',
        'city.name',
        'city.zip_code',
        'email',
        'accounting',
        'ciel_reference',
        'send_bill_type',
        'one_bill_per_period',
        'bill_payment_method',
        'observations',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }
    public function contracts()
    {
        return $this->hasMany(Contract::class);
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
