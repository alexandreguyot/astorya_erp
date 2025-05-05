<?php

namespace App\Models;

use App\Support\HasAdvancedFilter;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Owner extends Model
{
    use HasFactory, HasAdvancedFilter, SoftDeletes;

    public $table = 'owners';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'address',
        'zip_code',
        'city',
        'email',
        'phone',
        'web_site_address',
        'siret',
        'capital',
        'bic',
        'iban',
        'hotline_name',
        'hotline_phone',
        'hotline_email',
        'accounting_manager',
        'accounting_phone',
        'accounting_email',
    ];

    public $orderable = [
        'id',
        'name',
        'address',
        'zip_code',
        'city',
        'email',
        'phone',
        'web_site_address',
        'siret',
        'capital',
        'bic',
        'iban',
        'hotline_name',
        'hotline_phone',
        'hotline_email',
        'accounting_manager',
        'accounting_phone',
        'accounting_email',
    ];

    public $filterable = [
        'id',
        'name',
        'address',
        'zip_code',
        'city',
        'email',
        'phone',
        'web_site_address',
        'siret',
        'capital',
        'bic',
        'iban',
        'hotline_name',
        'hotline_phone',
        'hotline_email',
        'accounting_manager',
        'accounting_phone',
        'accounting_email',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
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
