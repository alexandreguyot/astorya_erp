<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractProductDetail extends Model
{
    use HasFactory;

    protected $table = 'contract_product_details';

    protected $fillable = [
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
        'billing_started_at' => 'date',
        'billing_terminated_at' => 'date',
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

    public function getTotalPriceAttribute()
    {
        return number_format(
            $this->monthly_unit_price_without_taxe * $this->quantity,
            2,
            ',',
            ' '
        );
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
}
