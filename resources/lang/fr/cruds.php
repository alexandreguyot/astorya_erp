<?php

return [
    'userManagement' => [
        'title'          => 'Rôles et autorisations',
        'title_singular' => 'Rôles et autorisations',
    ],
    'permission' => [
        'title'          => 'Permissions',
        'title_singular' => 'Permission',
        'fields'         => [
            'id'                => 'ID',
            'id_helper'         => ' ',
            'title'             => 'Title',
            'title_helper'      => ' ',
            'created_at'        => 'Created at',
            'created_at_helper' => ' ',
            'updated_at'        => 'Updated at',
            'updated_at_helper' => ' ',
            'deleted_at'        => 'Deleted at',
            'deleted_at_helper' => ' ',
        ],
    ],
    'role' => [
        'title'          => 'Rôles',
        'title_singular' => 'Rôle',
        'fields'         => [
            'id'                 => 'ID',
            'id_helper'          => ' ',
            'title'              => 'Title',
            'title_helper'       => ' ',
            'permissions'        => 'Permissions',
            'permissions_helper' => ' ',
            'created_at'         => 'Created at',
            'created_at_helper'  => ' ',
            'updated_at'         => 'Updated at',
            'updated_at_helper'  => ' ',
            'deleted_at'         => 'Deleted at',
            'deleted_at_helper'  => ' ',
        ],
    ],
    'user' => [
        'title'          => 'Utilisateurs',
        'title_singular' => 'Utilisateur',
        'fields'         => [
            'id'                       => 'ID',
            'id_helper'                => ' ',
            'name'                     => 'Name',
            'name_helper'              => ' ',
            'email'                    => 'Email',
            'email_helper'             => ' ',
            'email_verified_at'        => 'Email verified at',
            'email_verified_at_helper' => ' ',
            'password'                 => 'Password',
            'password_helper'          => ' ',
            'roles'                    => 'Roles',
            'roles_helper'             => ' ',
            'remember_token'           => 'Remember Token',
            'remember_token_helper'    => ' ',
            'locale'                   => 'Locale',
            'locale_helper'            => ' ',
            'created_at'               => 'Created at',
            'created_at_helper'        => ' ',
            'updated_at'               => 'Updated at',
            'updated_at_helper'        => ' ',
            'deleted_at'               => 'Deleted at',
            'deleted_at_helper'        => ' ',
        ],
    ],
    'typeContract' => [
        'title'          => 'Type de contrat',
        'title_singular' => 'Type de contrat',
        'fields'         => [
            'id'                => 'ID',
            'id_helper'         => ' ',
            'title'             => 'Titre',
            'title_helper'      => ' ',
            'created_at'        => 'Created at',
            'created_at_helper' => ' ',
            'updated_at'        => 'Updated at',
            'updated_at_helper' => ' ',
            'deleted_at'        => 'Deleted at',
            'deleted_at_helper' => ' ',
        ],
    ],
    'parametre' => [
        'title'          => 'Paramétrage',
        'title_singular' => 'Paramétrage',
    ],
    'typePeriod' => [
        'title'          => 'Type de période',
        'title_singular' => 'Type de période',
        'fields'         => [
            'id'                => 'ID',
            'id_helper'         => ' ',
            'title'             => 'Titre',
            'title_helper'      => ' ',
            'nb_month'          => 'Nombre de mois',
            'nb_month_helper'   => ' ',
            'created_at'        => 'Created at',
            'created_at_helper' => ' ',
            'updated_at'        => 'Updated at',
            'updated_at_helper' => ' ',
            'deleted_at'        => 'Deleted at',
            'deleted_at_helper' => ' ',
        ],
    ],
    'typeVat' => [
        'title'          => 'Type de TVA',
        'title_singular' => 'Type de TVA',
        'fields'         => [
            'id'                => 'ID',
            'id_helper'         => ' ',
            'code'              => 'Code TVA',
            'code_helper'       => ' ',
            'percent'           => 'Pourcentage',
            'percent_helper'    => ' ',
            'account_tva'           => 'Compte TVA',
            'account_tva_helper'    => ' ',
            'created_at'        => 'Created at',
            'created_at_helper' => ' ',
            'updated_at'        => 'Updated at',
            'updated_at_helper' => ' ',
            'deleted_at'        => 'Deleted at',
            'deleted_at_helper' => ' ',
        ],
    ],
    'typeProduct' => [
        'title'          => 'Type de produit',
        'title_singular' => 'Type de produit',
        'fields'         => [
            'id'                        => 'ID',
            'id_helper'                 => ' ',
            'code'                      => 'Code article',
            'code_helper'               => ' ',
            'designation_short'         => 'Description courte',
            'designation_short_helper'  => ' ',
            'designation_long'        => 'long_description',
            'designation_long_helper' => ' ',
            'accounting'                => 'Compte comptable',
            'accounting_helper'         => ' ',
            'created_at'                => 'Created at',
            'created_at_helper'         => ' ',
            'updated_at'                => 'Updated at',
            'updated_at_helper'         => ' ',
            'deleted_at'                => 'Deleted at',
            'deleted_at_helper'         => ' ',
        ],
    ],
    'owner' => [
        'title'          => 'Nos coordonnées',
        'title_singular' => 'Nos coordonnée',
        'fields'         => [
            'id'                        => 'ID',
            'id_helper'                 => ' ',
            'name'                      => 'Nom de la société',
            'name_helper'               => ' ',
            'address'                   => 'Adresse',
            'address_helper'            => ' ',
            'zip_code'                  => 'Code postal',
            'zip_code_helper'           => ' ',
            'city'                      => 'Ville',
            'city_helper'               => ' ',
            'email'                     => 'E-mail',
            'email_helper'              => ' ',
            'phone'                     => 'Téléphone',
            'phone_helper'              => ' ',
            'web_site_address'          => 'Site internet',
            'web_site_address_helper'   => ' ',
            'siret'                     => 'Siret',
            'siret_helper'              => ' ',
            'capital'                   => 'Capital',
            'capital_helper'            => ' ',
            'bic'                       => 'BIC',
            'bic_helper'                => ' ',
            'iban'                      => 'IBAN',
            'iban_helper'               => ' ',
            'hotline_name'              => 'Service Hotline',
            'hotline_name_helper'       => ' ',
            'hotline_phone'             => 'Téléphone Hotline',
            'hotline_phone_helper'      => ' ',
            'hotline_email'             => 'Email Hotline',
            'hotline_email_helper'      => ' ',
            'accounting_manager'        => 'Service Compta',
            'accounting_manager_helper' => ' ',
            'accounting_phone'          => 'Téléphone Compta',
            'accounting_phone_helper'   => ' ',
            'accounting_email'          => 'Email Compta',
            'accounting_email_helper'   => ' ',
            'created_at'                => 'Created at',
            'created_at_helper'         => ' ',
            'updated_at'                => 'Updated at',
            'updated_at_helper'         => ' ',
            'deleted_at'                => 'Deleted at',
            'deleted_at_helper'         => ' ',
        ],
    ],
    'city' => [
        'title'          => 'Villes',
        'title_singular' => 'Ville',
        'fields'         => [
            'id'                => 'ID',
            'id_helper'         => ' ',
            'zipcode'           => 'Code postal',
            'zipcode_helper'    => ' ',
            'name'              => 'Nom',
            'name_helper'       => ' ',
            'created_at'        => 'Created at',
            'created_at_helper' => ' ',
            'updated_at'        => 'Updated at',
            'updated_at_helper' => ' ',
            'deleted_at'        => 'Deleted at',
            'deleted_at_helper' => ' ',
        ],
    ],
    'contact' => [
        'title'          => 'Contacts',
        'title_singular' => 'Contact',
        'fields'         => [
            'id'                 => 'ID',
            'id_helper'          => ' ',
            'lastname'           => 'Nom',
            'lastname_helper'    => ' ',
            'firstname'          => 'Prénom',
            'firstname_helper'   => ' ',
            'title'              => 'Fonction',
            'title_helper'       => ' ',
            'email'              => 'E-mail',
            'email_helper'       => ' ',
            'is_director'        => 'directeur ?',
            'is_director_helper' => ' ',
            'created_at'         => 'Created at',
            'created_at_helper'  => ' ',
            'updated_at'         => 'Updated at',
            'updated_at_helper'  => ' ',
            'deleted_at'         => 'Deleted at',
            'deleted_at_helper'  => ' ',
        ],
    ],
    'bankAccount' => [
        'title'          => 'Compte bancaire',
        'title_singular' => 'Compte bancaire',
        'fields'         => [
            'id'                          => 'ID',
            'id_helper'                   => ' ',
            'no_rum'                      => 'Numéro rum',
            'no_rum_helper'               => ' ',
            'effective_start_date'        => 'Date d\'effet mandat',
            'effective_start_date_helper' => ' ',
            'bic'                         => 'BIC',
            'bic_helper'                  => ' ',
            'iban'                        => 'IBAN',
            'iban_helper'                 => ' ',
            'created_at'                  => 'Created at',
            'created_at_helper'           => ' ',
            'updated_at'                  => 'Updated at',
            'updated_at_helper'           => ' ',
            'deleted_at'                  => 'Deleted at',
            'deleted_at_helper'           => ' ',
        ],
    ],
    'company' => [
        'title'          => 'Clients',
        'title_singular' => 'Client',
        'fields'         => [
            'id'                          => 'ID',
            'id_helper'                   => ' ',
            'name'                        => 'Raison sociale',
            'name_helper'                 => ' ',
            'address'                     => 'Adresse',
            'address_helper'              => ' ',
            'address_compl'               => 'Complément d\'adresse',
            'address_compl_helper'        => ' ',
            'city'                        => 'Ville',
            'city_helper'                 => ' ',
            'email'                       => 'e-mail',
            'email_helper'                => ' ',
            'accounting'                  => 'Compte comptable',
            'accounting_helper'           => ' ',
            'ciel_reference'              => 'Référence CIEL',
            'ciel_reference_helper'       => ' ',
            'send_bill_type'              => 'Envoi facture par email',
            'send_bill_type_helper'       => ' ',
            'one_bill_per_period'         => 'Une seule facture par période',
            'one_bill_per_period_helper'  => ' ',
            'bill_payment_method'        => 'Virement ou prélévement',
            'bill_payment_method_helper' =>  "Si coché, virement, autrement prélévement",
            'observations'                => 'Observations',
            'observations_helper'         => ' ',
            'created_at'                  => 'Created at',
            'created_at_helper'           => ' ',
            'updated_at'                  => 'Updated at',
            'updated_at_helper'           => ' ',
            'deleted_at'                  => 'Deleted at',
            'deleted_at_helper'           => ' ',
        ],
    ],
    'bill' => [
        'title'          => 'Factures',
        'title_singular' => 'Facture',
        'fields'         => [
            'id'                         => 'ID',
            'id_helper'                  => ' ',
            'no_bill'                    => 'Numéro',
            'no_bill_helper'             => ' ',
            'amount'                     => 'Montant HT',
            'amount_helper'              => ' ',
            'amount_vat_included'        => 'Montant TTC',
            'amount_vat_included_helper' => ' ',
            'one_bill_per_period'        => 'Une seule facture par période',
            'one_bill_per_period_helper' => ' ',
            'started_at'                 => 'Date de début',
            'started_at_helper'          => ' ',
            'billed_at'                  => 'Facturé le',
            'billed_at_helper'           => ' ',
            'generated_at'               => 'Généré le',
            'generated_at_helper'        => ' ',
            'validated_at'               => 'Validée le',
            'validated_at_helper'        => ' ',
            'sent_at'                    => 'Envoyée le',
            'sent_at_helper'             => ' ',
            'file_path'                  => 'files',
            'file_path_helper'           => ' ',
            'company'                    => 'Client',
            'company_helper'             => ' ',
            'type_period'                => 'Type de période',
            'type_period_helper'         => ' ',
            'created_at'                 => 'Created at',
            'created_at_helper'          => ' ',
            'updated_at'                 => 'Updated at',
            'updated_at_helper'          => ' ',
            'deleted_at'                 => 'Deleted at',
            'deleted_at_helper'          => ' ',
        ],
    ],
    'contract' => [
        'title'          => 'Abonnements',
        'title_singular' => 'Abonnement',
        'fields'         => [
            'id'                    => 'ID',
            'id_helper'             => ' ',
            'company'               => 'Client',
            'company_helper'        => ' ',
            'setup_at'              => 'Date de mise en place',
            'setup_at_helper'       => ' ',
            'started_at'            => 'Démarré le :',
            'started_at_helper'     => ' ',
            'terminated_at'         => 'Date de résiliation',
            'terminated_at_helper'  => ' ',
            'billed_at'             => 'Date de dernière facturation',
            'billed_at_helper'      => ' ',
            'type_contract_id_at'   => 'Type de contrat',
            'type_contract_id_helper'=> ' ',
            'type_period_id_at'     => 'Périodicité',
            'type_period_id_helper' => ' ',
            'validated_at'          => 'Validé le',
            'validated_at_helper'   => ' ',
            'created_at'            => 'Created at',
            'created_at_helper'     => ' ',
            'updated_at'            => 'Updated at',
            'updated_at_helper'     => ' ',
            'deleted_at'            => 'Deleted at',
            'deleted_at_helper'     => ' ',
        ],
    ],

];
