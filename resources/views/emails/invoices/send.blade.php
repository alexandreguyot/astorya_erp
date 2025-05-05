<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Facture {{ $bill->no_bill }}</title>
</head>
<body>
    <p>
        Bonjour,<br /><br />

        Je vous prie de trouver ci-joint la Facture <strong>{{ $bill->no_bill }}</strong>
        à échéance du <strong>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $bill->generated_at)->addDays(7)->format('d/m/Y') }}</strong>.<br />
        Vous avez opté pour un règlement par <strong>{{ $company->payment_method == 0 ? 'prélèvement.' : ($company->payment_method == 1 ? 'virement.' : 'Autre.'); }}</strong>.<br /><br />

        Je vous en souhaite bonne réception et reste à votre disposition.<br /><br />

        Cordialement,<br />
        Le service Comptabilité
    </p>
</body>
</html>
