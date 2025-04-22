<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\Models\Company;

class CompanyImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        // Créer ou mettre à jour un produit
        return Company::updateOrCreate(
            ['ciel_reference' => $row['Code']],  // Recherche par le code produit
            [
                'name' => $row['Nom'],
                'accounting' => $row['Compte Comptable'],
                'email' => $row['E-mail'],
            ]
        );
    }

    public function rules(): array
    {
        return [
            'Code' => 'string',
            'Nom' => 'string',
            'Compte Comptable' => 'string',
            'E-mail' => 'string',
        ];
    }
}
