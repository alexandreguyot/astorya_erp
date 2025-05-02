<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class AccountingHisto extends Model
{
    use SoftDeletes;

    protected $table = 'accounting_histos'; // ou le nom exact de la table

    protected $fillable = [
        'journal',
        'date',
        'no_bill',
        'account_number',
        'label',
        'debit_amount',
        'credit_amount',
        'deadline',
        'product_code',
        'product_short_description',
        'company_name',
        'company_accounting',
        'company_ciel_reference',
        'payment_code',
    ];

    protected $casts = [
        'date' => 'date',
        'deadline' => 'date',
        'debit_amount' => 'decimal:2',
        'credit_amount' => 'decimal:2',
    ];

    public function getDateAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d', $value)->format(config('project.date_format')) : null;
    }

    public function setDateAttribute($value)
    {
        $this->attributes['date'] = $value ? Carbon::createFromFormat(config('project.date_format'), $value)->format('Y-m-d') : null;
    }

    public function getDeadlineAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d', $value)->format(config('project.date_format')) : null;
    }

    public function setDeadlineAttribute($value)
    {
        $this->attributes['deadline'] = $value ? Carbon::createFromFormat(config('project.date_format'), $value)->format('Y-m-d') : null;
    }

    public function setDebitAmountAttribute($value)
    {
        $clean = is_string($value)
            ? str_replace(',', '.', str_replace(' ', '', $value))
            : $value;

        $this->attributes['debit_amount'] = (float) $clean;
    }

    public function setCreditAmountAttribute($value)
    {
        $clean = is_string($value)
            ? str_replace(',', '.', str_replace(' ', '', $value))
            : $value;

        $this->attributes['credit_amount'] = (float) $clean;
    }
}
