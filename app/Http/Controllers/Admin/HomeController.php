<?php

namespace App\Http\Controllers\Admin;
use PDO;
use App\Models\Bill;
use Illuminate\Support\Carbon;

class HomeController
{
    public function index()
    {
        return view('admin.home');
    }

    public function install()
    {
        $this->migrateTable(
            'AccountingHistos', 'accounting_histos',
            ['Id', 'Journal', 'Date', 'NoBill', 'AccountNumber', 'Label', 'DebitAmount', 'CreditAmount', 'Deadline', 'ProductCode', 'ProductShortDescription', 'CompanyName', 'CompanyAccounting', 'CompanyCielReference', 'PaymentCode', 'CreationDate', 'LastModifiedDate'],
            ['id', 'journal', 'date', 'no_bill', 'account_number', 'label', 'debit_amount', 'credit_amount', 'deadline', 'product_code', 'product_short_description', 'company_name', 'company_accounting', 'company_ciel_reference', 'payment_code', 'created_at', 'updated_at']
        );

        $this->migrateTable(
            'TypePeriods', 'type_periods',
            ['Id', 'Title', 'NbMonth', 'CreationDate', 'LastModifiedDate'],
            ['id', 'title', 'nb_month', 'created_at', 'updated_at']
        );

        $this->migrateTable(
            'TypeContracts', 'type_contracts',
            ['Id', 'Title', 'CreationDate', 'LastModifiedDate'],
            ['id', 'title', 'created_at', 'updated_at']
        );

        $this->migrateTable(
            'TypeVats', 'type_vats',
            ['Id', 'Code', 'Percent', 'AccountVat', 'CreationDate', 'LastModifiedDate'],
            ['id', 'code_vat', 'percent', 'account_vat', 'created_at', 'updated_at']
        );

        $this->migrateTable(
            'TypeProducts', 'type_products',
            ['Id', 'TypeContractId', 'TypeVatId', 'Code', 'ShortDesignation', 'LongDesignation', 'Accounting', 'CreationDate', 'LastModifiedDate'],
            ['id', 'type_contract_id', 'type_vat_id', 'code', 'designation_short', 'designation_long', 'accounting', 'created_at', 'updated_at']
        );

        $this->migrateTable(
            'Cities', 'cities',
            ['Id', 'ZipCode', 'Name', 'CreationDate', 'LastModifiedDate'],
            ['id', 'zip_code', 'name', 'created_at', 'updated_at']
        );

        $this->migrateTable(
            'Contacts', 'contacts',
            ['Id', 'Lastname', 'Firstname', 'Title', 'Email', 'isDirector', 'CreationDate', 'LastModifiedDate'],
            ['id', 'lastname', 'firstname', 'title', 'email', 'is_director', 'created_at', 'updated_at']
        );

        $this->migrateTable(
            'BankAccounts', 'bank_accounts',
            ['Id', 'NoRum', 'EffectiveStartingDate', 'Bic', 'Iban', 'CreationDate', 'LastModifiedDate'],
            ['id', 'no_rum', 'effective_starting_date', 'bic', 'iban', 'created_at', 'updated_at']
        );

        $this->migrateTable(
            'Companies', 'companies',
            ['Id', 'Name', 'Address', 'AddressCompl', 'Phone', 'CityId', 'Email', 'Accounting', 'CielReference', 'SendBillType', 'OneBillPerPeriod', 'BillPayementMethod', 'CreationDate', 'LastModifiedDate', 'Observations', 'BankAccountId', 'ContactId'],
            ['id', 'name', 'address', 'address_compl', 'phone', 'city_id', 'email', 'accounting', 'ciel_reference', 'send_bill_type', 'one_bill_per_period', 'bill_payment_method', 'created_at', 'updated_at', 'observations', 'bank_account_id', 'contact_id']
        );

        $this->migrateTable('Contracts', 'contracts',
            ['Id', 'CompanyId', 'TypePeriodId', 'SetupAt', 'TerminatedAt', 'BilledAt', 'ValidatedAt', 'CreationDate', 'LastModifiedDate'],
            ['id', 'company_id', 'type_period_id', 'setup_at', 'terminated_at', 'billed_at', 'validated_at', 'created_at', 'updated_at']
        );

        $this->migrateTable('ContractProductDetails', 'contract_product_details',
            ['Id', 'ContractId', 'TypeProductId', 'Designation', 'Quantity', 'Capacity', 'MonthlyUnitPriceWithoutTaxe', 'BillingStartedAt', 'BillingTerminatedAt', 'LastBilledAt', 'CreationDate', 'LastModifiedDate'],
            ['id', 'contract_id', 'type_product_id', 'designation', 'quantity', 'capacity', 'monthly_unit_price_without_taxe', 'billing_started_at', 'billing_terminated_at', 'last_billed_at', 'created_at', 'updated_at']
        );

        $this->migrateTable(
            'Owners', 'owners',
            ['Id', 'Name', 'Address', 'ZipCode', 'City', 'Email', 'Phone', 'WebSiteAddress', 'Siret', 'Capital', 'Bic', 'Iban', 'HotlineName', 'HotlinePhone', 'HotlineEmail', 'AccountingManager', 'AccountingPhone', 'AccountingEmail', 'CreationDate', 'LastModifiedDate'],
            ['id', 'name', 'address', 'zip_code', 'city', 'email', 'phone', 'web_site_address', 'siret', 'capital', 'bic', 'iban', 'hotline_name', 'hotline_phone', 'hotline_email', 'accounting_manager', 'accounting_phone', 'accounting_email', 'created_at', 'updated_at']
        );

        $this->migrateTable(
            'Bills', 'bills',
            ['Id', 'NoBill', 'Amount', 'OneBillPerPeriod', 'StartedAt', 'BilledAt', 'GeneratedAt', 'ValidatedAt', 'SentAt', 'FilePath', 'CreationDate', 'LastModifiedDate', 'AmountVatIncluded', 'CompanyId', 'TypePeriodId', 'ContractId'],
            ['id', 'no_bill', 'amount', 'one_bill_per_period', 'started_at', 'billed_at', 'generated_at', 'validated_at', 'sent_at', 'file_path', 'created_at', 'updated_at', 'amount_vat_included', 'company_id', 'type_period_id', 'contract_id']
        );

        Bill::where('no_bill', null)->delete();

        echo "Migration terminée!";
    }

     // Fonction pour migrer une table avec vérification de la clé étrangère contract_id
    function migrateTable($oldTable, $newTable, $oldColumns, $newColumns)
    {
        global $oldDb, $newDb;

        $oldDb = new PDO('mysql:host=mysql;dbname=test', 'root', 'astorya_erp');
        $newDb = new PDO('mysql:host=mysql;dbname=astorya_erp', 'root', 'astorya_erp');


        // Colonnes qui, dans la nouvelle table, sont définies en type DATE
        $dateOnlyOldCols = [
            'SetupAt',
            'TerminatedAt',
            'BilledAt',
            'ValidatedAt',
        ];

        // Récupérer toutes les données de l'ancienne table
        $query = "SELECT " . implode(",", $oldColumns) . " FROM $oldTable";
        $stmt = $oldDb->query($query);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Préparer la requête d'insertion dans la nouvelle table
        $insertQuery = "INSERT INTO $newTable (" . implode(",", $newColumns) . ") VALUES (" . str_repeat('?,', count($newColumns) - 1) . '?)';
        $insertStmt = $newDb->prepare($insertQuery);

        // Insérer les données dans la nouvelle table
        foreach ($rows as $row) {
            // Vérifier les dates et les remplacer par NULL si nécessaire
            if ($row['LastModifiedDate'] === '0001-01-01 00:00:00.000000') {
                $row['LastModifiedDate'] = null;
            }
            if ($row['CreationDate'] === '0001-01-01 00:00:00.000000') {
                $row['CreationDate'] = null;
            }

            foreach (['SetupAt','TerminatedAt','BilledAt','ValidatedAt'] as $dateCol) {
                $val = $row[$dateCol] ?? null;
                if ($val === null || strpos($val, '0001-01-01') === 0) {
                    // date fantôme ou vide -> NULL
                    $row[$dateCol] = null;
                } else {
                    // on conserve uniquement le 'YYYY-MM-DD'
                    $row[$dateCol] = substr($val, 0, 10);
                }
            }

            // Préparer les données pour l'insertion
            $data = array_map(function($column) use ($row) {
                return $row[$column];
            }, $oldColumns);

            // Exécuter l'insertion
            $insertStmt->execute($data);
        }
    }
}
