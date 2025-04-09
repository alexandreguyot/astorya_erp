<div class="page-break-avoid">
    <div class="always-bottom">
        <div class="row footer" style="font-size:12px;">
            <div class="col auto-fill resume">
                <div class="vat-resume resume-table margin-bot-10">
                    <table class="rounded">
                        <thead>
                            <tr>
                                <th class="min-width-70">Code</th>
                                <th>Base HT</th>
                                <th>Taux TVA</th>
                                <th>Montant TVA</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vatResumes as $vat)
                                <tr>
                                    <td class="min-width-70 center-align">{{ $vat['code'] }}</td>
                                    <td class="right-align">{{ $vat['amount_ht'] }}</td>
                                    <td class="right-align">{{ $vat['percent'] }}</td>
                                    <td class="right-align"> {{ $vat['amount_tva'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="bank-account margin-top-bot-10">
                    <h2>Nos coordonnées bancaires</h2>
                    <div class="row">
                        <div class="col left-info margin2">
                            <div>IBAN </div>
                            <div>BIC </div>
                        </div>
                        <div class="col margin2">
                            <div>:   {{ $owner->iban }}</div>
                            <div>:   {{ $owner->bic }}</div>
                        </div>
                    </div>
                    <p class="small-text">
                        Passé la date d'échéance tout paiement différé entraîne l'application de pénalités de retard ne pouvant toutefois être inférieur à 3 fois le taux intérêt légal en vigueur (Loi 2008-776 du 04/08/2008) et d'une indemnité forfaitaire pour frais de recouvrement de 40,00 €.
                    </p>
                </div>
                <div class="contacts margin-top-10">
                    <div class="row">
                        <div class="col auto-fill centered hotline margin5">
                            <h4>Service Technique</h4>
                            <div>{{ $owner->hotline_name }}</div>
                            <div>{{ $owner->hotline_phone }}</div>
                            <div>{{ $owner->hotline_email }}</div>
                        </div>
                        <div class="col auto-fill centered accounting margin5">
                            <h4>Comptabilité</h4>
                            <div>{{ $owner->accounting_manager }}</div>
                            <div>{{ $owner->accounting_phone }}</div>
                            <div>{{ $owner->accounting_email }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col" style="width: 5%;">

            </div>
            <div class="col auto-fill resume width-45">
                <div class="auto-fill amount-resume">
                    <table class="rounded-amount-resume">
                        <thead>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="min-width-150 gray-background title">Total HT</td>
                                <td class="right-align width-70">{{ $totals['total_ht'] }}</td>
                            </tr>
                            <tr>
                                <td class="min-width-150 gray-background title"><strong>Net HT</strong></td>
                                <td class="border-bottom right-align width-70"><strong>{{ $totals['total_ht'] }}</strong></td>
                            </tr>
                            <tr>
                                <td class="min-width-150 gray-background title">Total TVA</td>
                                <td class="right-align width-70">{{ $totals['total_tva'] }}</td>
                            </tr>
                            <tr>
                                <td class="min-width-150 border-bottom gray-background title">Total TTC</td>
                                <td class="border-bottom right-align width-70">{{ $totals['total_ttc'] }}</td>
                            </tr>
                            <tr>
                                <td class="min-width-150 gray-background title"><strong>NET A PAYER</strong></td>
                                <td class="gray-background right-align width-70"><strong>{{ $totals['total_ttc'] }}</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="payment-coupon">
                    <div class="scissors"></div>
                    <div class="content">
                        <p>
                            Coupon à joindre à votre règlement par chèque à l'ordre de ASTORYA SGI
                        </p>
                        <div class="client-resume">
                            <div class="col left-info margin8">
                                <div>Code client </div>
                                <div>Facture </div>
                                <div>Montant dû </div>
                                <div>Echéance </div>
                                <div>Mode de paiement </div>
                            </div>
                            <div class="col margin8">
                                <div>:   {{ $contract->company->ciel_reference }}</div>
                                <div>:   {{ $bill->no_bill ?? 'A DEFINIR' }} </div>
                                <div>:   {{ $totals['total_ttc'] }}</div>
                                <div>:   @if ($bill) {{ \Carbon\Carbon::createFromFormat('d/m/Y', $bill->generated_at)->addDays(7)->format('d/m/Y') }} @else A DEFINIR @endif</div>
                                <div>:   @if ($contract->company->payment_method == 0) Prélèvement @elseif ($contract->company->payment_method == 1)
                                    Virement @else @endif</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="small-text left-align cgv">
            <strong>RESERVE DE PROPRIETE :</strong> Nous nous réservons la propriété des marchandises jusqu'au paiement du prix par l'acheteur. Notre droit de revendication porte aussi bien sur les marchandises que sur leur
            prix si elles ont déjà été revendues (Loi du 12 mai 1980).
        </div>
    </div>
</div>
