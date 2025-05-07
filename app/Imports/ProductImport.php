<?php

namespace App\Imports;

use App\Models\TypeProduct;
use App\Models\TypeVat;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $typeVat = TypeVat::where('code_vat', $row['code_tva'])->first();

        if (!$typeVat) {
            return null;
        }

        return TypeProduct::updateOrCreate(
            ['code' => $row['Code']],
            [
                'designation_short' => $row['dzsignation_courte'],
                'designation_long' => $row['dzsignation_longue'],
                'accounting' => $row['compte_vente_local'],
                'type_vat_id' => $typeVat->id,
            ]
        );
    }

    public function rules(): array
    {
        return [
            'Code' => 'string',
            'Désignation courte' => 'string',
            'Famille' => 'nullable|string',
            'Prix de vente HT' => 'nullable|numeric',
            'Prix de vente TTC' => 'nullable|numeric',
            'Stock théorique' => 'nullable|numeric',
            'Stock réel' => 'nullable|numeric',
            'Qté en cde client' => 'nullable|numeric',
            'Qté en cde fournisseur' => 'nullable|numeric',
            'Bloqué' => 'nullable|boolean',
            'Compte Vente Local' => 'nullable|string',
            'Désignation longue' => 'nullable|string',
            'Code TVA' => 'integer',
        ];
    }
}
