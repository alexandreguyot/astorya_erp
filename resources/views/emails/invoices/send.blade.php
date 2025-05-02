@component('mail::message')
# Bonjour {{ $company->name }},

Veuillez trouver en pièce jointe votre facture **n° {{ $bill->no_bill }}** datée du **{{ $bill->generated_at }}**.

Montant **HT** : {{ number_format($bill->amount, 2, ',', ' ') }} €
Montant **TTC** : {{ number_format($bill->amount_vat_included, 2, ',', ' ') }} €

@component('mail::button', ['url' => url('/')])
Visitez notre site
@endcomponent

Merci pour votre confiance,<br>
{{ config('app.name') }}
@endcomponent
