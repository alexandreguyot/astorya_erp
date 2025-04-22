<?php

namespace App\Models;

use App\Support\HasAdvancedFilter;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TypeProduct extends Model
{
    use HasFactory, HasAdvancedFilter, SoftDeletes;

    public $table = 'type_products';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'code',
        'designation_short',
        'designation_long',
        'accounting',
        'type_contract_id',
        'type_vat_id',
    ];

    public $orderable = [
        'id',
        'code',
        'designation_short',
        'designation_long',
        'accounting',
        'type_contract_id',
        'type_vat_id',
    ];

    public $filterable = [
        'id',
        'code',
        'designation_short',
        'designation_long',
        'accounting',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function type_contract()
    {
        return $this->belongsTo(TypeContract::class);
    }

    public function type_vat()
    {
        return $this->belongsTo(TypeVat::class);
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
