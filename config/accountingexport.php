<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Comptabilité Export Configuration
    |--------------------------------------------------------------------------
    |
    | Ces paramètres sont utilisés lors de la génération des écritures
    | comptables dans le job GenerateBillPdf.
    |
    */

    // Préfixe des comptes produits pour détecter TVA à crédit
    'CreditTvaStartWith' => '706',

    // Préfixe des comptes produits pour détecter TVA à débit
    'DebitTvaStartWith' => '707',

    // Compte TVA à recevoir (TVA collectée)
    'CreditAccounting' => '445712',

    // Compte TVA à payer (TVA déductible)
    'DebitAccounting' => '445711',

];
