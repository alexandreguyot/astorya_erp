<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasPriceCast
{
    public static function bootHasPriceCast()
    {
        foreach ((new static)->getPriceAttributes() as $attribute) {
            static::addPriceCast($attribute);
        }
    }

    protected static function addPriceCast(string $attribute)
    {
        static::macro($attribute, function () use ($attribute) {
            return Attribute::make(
                get: fn ($value) => number_format($value, 2, ',', ''),
                set: fn ($value) => (float) str_replace(',', '.', $value)
            );
        });
    }

    /**
     * Redéfinis cette méthode dans ton modèle pour définir les attributs "prix"
     */
    protected function getPriceAttributes(): array
    {
        return [
            'amount',
            'amount_vat_included',
        ];
    }
}
