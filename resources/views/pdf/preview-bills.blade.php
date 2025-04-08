<!DOCTYPE html>
<head>
    <meta charset="utf-8" />
    <title></title>
    <link rel="stylesheet" href="{{ public_path('css/pdf.css') }}" />

</head>
<body class="page">
    <div class="container">
        <div class="header">
            <div class="row">
                <div class="logo-container">
                    <img src="{{ public_path('images/logo.jpg') }}" alt="Logo" style="width: 280px;">
                </div>
                <div class="col auto-fill">
                    <div class="auto-fill">
                        <div class="auto-fill centered">
                            <div class="col margin2">
                                <div>{{ $owner->name }}</div>
                                <div>{{ $owner->address }}</div>
                                <div>{{ $owner->zip_code }} {{ $owner->city }}</div>
                                <div>{{ $owner->email }}</div>
                            </div>
                        </div>
                        <div class="auto-fill centered">
                            <div class="col left-info margin2">
                                <div>Tél </div>
                                <div>Site internet </div>
                                <div>Siret </div>
                                <div>Capital </div>
                            </div>
                            <div class="col margin2">
                                <div>:   {{ $owner->phone }}</div>
                                <div>:   {{ $owner->web_site_address }}</div>
                                <div>:   {{ $owner->siret }}</div>
                                <div>:   {{ $owner->capital }}€</div>
                            </div>
                        </div>
                    </div>
                    <div class="auto-fill bill-title">
                        <div class="margin-top-bot-5">
                            <h1>FACTURE</h1>
                        </div>
                        <div class="separator">
                            <hr />
                        </div>
                        <div class="margin-top-bot-10">
                            {{ $contract->company->name }} <br />
                            {{ $contract->company->address }} <br />
                            {{ $contract->company->address_compl }} <br />
                            {{ $contract->company->city->zip_code ?? '' }} {{ $contract->company->city->name ?? '' }} <br />
                        </div>
                    </div>
                </div>
            </div>
            <div class="separator">
                <hr />
            </div>
            <div class="row bill-info">
                <div class="auto-fill">
                    Date : A DEFINIR
                </div>
                <div class="auto-fill">
                    Facture N° A DEFINIR
                </div>
                <div class="auto-fill">
                    Mode de paiement :
                    @if ($contract->company->payment_method == 0)
                        Prélèvement
                    @elseif ($contract->company->payment_method == 1)
                        Virement
                    @else

                    @endif
                </div>
                <div class="auto-fill">
                    Echéance : PAS ENCORE GENERER
                </div>
                <div class="auto-fill">
                    Type de Facturation : {{ $contract->type_period->title }}
                </div>
            </div>
        </div>
        <div class="resume-table margin-bot-20 main-col-min">
            <table class="rounded no-interline main-table-min">
                <thead>
                    <tr>
                        <th class="width-10">Référence</th>
                        <th>Désignation</th>
                        <th class="width-10">Qté</th>
                        <th class="width-10">P.U. HT</th>
                        <th class="width-10">Montant HT</th>
                        <th class="width-10">TVA</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- @foreach (var item in billingPage)
                    { --}}
                        <tr class="spaceUnder">
                            <td class="width-10 center-align">@item.Reference</td>
                            <td style="font-size: 14px">@Html.Raw(item.Description)<br /><b>Du @item.StartBillingDate au @item.EndBillingDate</b><br />@item.Observation</td>
                            <td class="width-10 center-align">@item.Quantity</td>
                            <td class="width-10 center-align">@item.MonthlyUnitPriceVatNotIncluded.ToString("0.00")</td>
                            <td class="width-10 center-align">@item.AmountVatNotIncluded.ToString("0.00")</td>
                            <td class="width-10 center-align">@item.CodeVat</td>
                        </tr>
                    {{-- } --}}
                    <tr class="last-row">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>

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
                                    {{-- @foreach(var vat in Model.VatResumes)
                                    { --}}
                                    {{-- <tr>
                                        <td class="min-width-70 center-align">@vat.Code</td>
                                        <td class="right-align">@vat.AmountVatNotIncluded.ToString("0.00")</td>
                                        <td class="right-align">@vat.Percent.ToString("0.00")</td>
                                        <td class="right-align">@vat.AmountVat.ToString("0.00")</td>
                                    </tr> --}}
                                    {{-- } --}}
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
                                        <td class="right-align width-70">Model.TotalVatNotIncluded</td>
                                    </tr>
                                    <tr>
                                        <td class="min-width-150 gray-background title"><strong>Net HT</strong></td>
                                        <td class="border-bottom right-align width-70"><strong>Model.TotalVatNotIncluded</strong></td>
                                    </tr>
                                    <tr>
                                        <td class="min-width-150 gray-background title">Total TVA</td>
                                        <td class="right-align width-70">Model.TotalVat</td>
                                    </tr>
                                    <tr>
                                        <td class="min-width-150 border-bottom gray-background title">Total TTC</td>
                                        <td class="border-bottom right-align width-70">Model.TotalVatIncluded</td>
                                    </tr>
                                    <tr>
                                        <td class="min-width-150 gray-background title"><strong>NET A PAYER</strong></td>
                                        <td class="gray-background right-align width-70"><strong>Model.TotalVatIncluded</strong></td>
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
                                        <div>:   Model.ClientCode</div>
                                        <div>:   Model.NoBill</div>
                                        <div>:   Model.TotalVatIncluded</div>
                                        <div>:   Model.Deadline</div>
                                        <div>:   Model.PayementMethod</div>
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
    </div>
</body>
</html>


