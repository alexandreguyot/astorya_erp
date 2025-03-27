<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            [
                'id'    => 1,
                'title' => 'auth_profile_edit',
            ],
            [
                'id'    => 2,
                'title' => 'user_management_access',
            ],
            [
                'id'    => 3,
                'title' => 'permission_create',
            ],
            [
                'id'    => 4,
                'title' => 'permission_edit',
            ],
            [
                'id'    => 5,
                'title' => 'permission_show',
            ],
            [
                'id'    => 6,
                'title' => 'permission_delete',
            ],
            [
                'id'    => 7,
                'title' => 'permission_access',
            ],
            [
                'id'    => 8,
                'title' => 'role_create',
            ],
            [
                'id'    => 9,
                'title' => 'role_edit',
            ],
            [
                'id'    => 10,
                'title' => 'role_show',
            ],
            [
                'id'    => 11,
                'title' => 'role_delete',
            ],
            [
                'id'    => 12,
                'title' => 'role_access',
            ],
            [
                'id'    => 13,
                'title' => 'user_create',
            ],
            [
                'id'    => 14,
                'title' => 'user_edit',
            ],
            [
                'id'    => 15,
                'title' => 'user_show',
            ],
            [
                'id'    => 16,
                'title' => 'user_delete',
            ],
            [
                'id'    => 17,
                'title' => 'user_access',
            ],
            [
                'id'    => 18,
                'title' => 'contract_type_create',
            ],
            [
                'id'    => 19,
                'title' => 'contract_type_edit',
            ],
            [
                'id'    => 20,
                'title' => 'contract_type_show',
            ],
            [
                'id'    => 21,
                'title' => 'contract_type_delete',
            ],
            [
                'id'    => 22,
                'title' => 'contract_type_access',
            ],
            [
                'id'    => 23,
                'title' => 'parametre_access',
            ],
            [
                'id'    => 24,
                'title' => 'period_type_create',
            ],
            [
                'id'    => 25,
                'title' => 'period_type_edit',
            ],
            [
                'id'    => 26,
                'title' => 'period_type_show',
            ],
            [
                'id'    => 27,
                'title' => 'period_type_delete',
            ],
            [
                'id'    => 28,
                'title' => 'period_type_access',
            ],
            [
                'id'    => 29,
                'title' => 'vat_type_create',
            ],
            [
                'id'    => 30,
                'title' => 'vat_type_edit',
            ],
            [
                'id'    => 31,
                'title' => 'vat_type_show',
            ],
            [
                'id'    => 32,
                'title' => 'vat_type_delete',
            ],
            [
                'id'    => 33,
                'title' => 'vat_type_access',
            ],
            [
                'id'    => 34,
                'title' => 'product_type_create',
            ],
            [
                'id'    => 35,
                'title' => 'product_type_edit',
            ],
            [
                'id'    => 36,
                'title' => 'product_type_show',
            ],
            [
                'id'    => 37,
                'title' => 'product_type_delete',
            ],
            [
                'id'    => 38,
                'title' => 'product_type_access',
            ],
            [
                'id'    => 39,
                'title' => 'owner_create',
            ],
            [
                'id'    => 40,
                'title' => 'owner_edit',
            ],
            [
                'id'    => 41,
                'title' => 'owner_show',
            ],
            [
                'id'    => 42,
                'title' => 'owner_delete',
            ],
            [
                'id'    => 43,
                'title' => 'owner_access',
            ],
            [
                'id'    => 44,
                'title' => 'city_create',
            ],
            [
                'id'    => 45,
                'title' => 'city_edit',
            ],
            [
                'id'    => 46,
                'title' => 'city_show',
            ],
            [
                'id'    => 47,
                'title' => 'city_delete',
            ],
            [
                'id'    => 48,
                'title' => 'city_access',
            ],
            [
                'id'    => 49,
                'title' => 'contact_create',
            ],
            [
                'id'    => 50,
                'title' => 'contact_edit',
            ],
            [
                'id'    => 51,
                'title' => 'contact_show',
            ],
            [
                'id'    => 52,
                'title' => 'contact_delete',
            ],
            [
                'id'    => 53,
                'title' => 'contact_access',
            ],
            [
                'id'    => 54,
                'title' => 'bank_account_create',
            ],
            [
                'id'    => 55,
                'title' => 'bank_account_edit',
            ],
            [
                'id'    => 56,
                'title' => 'bank_account_show',
            ],
            [
                'id'    => 57,
                'title' => 'bank_account_access',
            ],
            [
                'id'    => 58,
                'title' => 'company_create',
            ],
            [
                'id'    => 59,
                'title' => 'company_edit',
            ],
            [
                'id'    => 60,
                'title' => 'company_show',
            ],
            [
                'id'    => 61,
                'title' => 'company_delete',
            ],
            [
                'id'    => 62,
                'title' => 'company_access',
            ],
            [
                'id'    => 63,
                'title' => 'bill_create',
            ],
            [
                'id'    => 64,
                'title' => 'bill_edit',
            ],
            [
                'id'    => 65,
                'title' => 'bill_show',
            ],
            [
                'id'    => 66,
                'title' => 'bill_delete',
            ],
            [
                'id'    => 67,
                'title' => 'bill_access',
            ],
            [
                'id'    => 68,
                'title' => 'contract_create',
            ],
            [
                'id'    => 69,
                'title' => 'contract_edit',
            ],
            [
                'id'    => 70,
                'title' => 'contract_show',
            ],
            [
                'id'    => 71,
                'title' => 'contract_delete',
            ],
            [
                'id'    => 72,
                'title' => 'contract_access',
            ],
        ];

        Permission::insert($permissions);
    }
}
