<!DOCTYPE html>
<style>
    body {
        font-family: Arial;
    }

    hr {
        border-top: 1px solid black;
    }

    .container .header {
        display: -webkit-box;
        -webkit-box-orient: vertical;
    }

    .container .row {
        display: -webkit-box;
        -webkit-box-orient: horizontal;
    }

    .container .col {
        display: -webkit-box;
        -webkit-box-orient: vertical;
    }

        .container .row .auto-fill,
        .container .col .auto-fill {
            display: -webkit-box;
            -webkit-box-flex: 1;
        }

            .container .row .auto-fill.centered {
                -webkit-box-pack: center;
            }

    .container .header .bill-title {
        -webkit-box-orient: vertical;
        text-align: center;
    }

        .container .header .bill-title h1 {
            font-size: 20px;
        }

    .container .header .logo {
        background-image: url('../images/logo.jpg');
        background-repeat: no-repeat;
        min-width: 280px;
    }

    .container .header .row .left-info {
        width: 30mm;
        text-align: left;
    }

    .container .client-resume .left-info {
        width: 50mm;
        text-align: left;
    }

    .container .bank-account .left-info {
        width: 30mm;
        text-align: left;
    }

    .container .margin2 > div {
        margin: 2px 0;
    }


    .container .margin8 > div {
        margin: 8px 0;
    }

    .container .margin5 > div {
        margin: 5px 0;
    }

    .container .bill-info {
        font-size: 12px;
        padding: 0px 0px;
        margin: 10px 0px;
    }

    .container .resume-table .small {
        width: 5%;
    }

    .container .resume-table table,
    .container .amount-resume table,
    .container .resume-table th,
    .container .resume-table td {
        /*border: 1px solid black;*/
    }

    .container .resume-table.main-col-min {
        height: 150mm;
    }

    .container .resume-table.main-col {
        height: 250mm;
    }

    .container .resume-table table.main-table-min {
        height: 150mm;
    }

    .container .resume-table table.main-table {
        height: 250mm;
    }

        .container .resume-table table.main-table-min tr th,
        .container .resume-table table.main-table tr th {
            font-size: 13px;
            height: 9mm;
        }

        .container .resume-table table.main-table-min tr td,
        .container .resume-table table.main-table tr td {
            border-bottom: none;
            vertical-align: top;
        }

        .container .resume-table table.main-table-min tr:last-child,
        .container .resume-table table.main-table tr:last-child {
            height: 99%;
        }

    .container .resume-table table,
    .container .amount-resume table {
        /*border-collapse: collapse;*/
        width: 100%;
        word-break: break-word;
    }

        .container .amount-resume table .border-bottom {
            border-bottom: 1px solid black;
        }

    .container .footer {
        -webkit-box-pack: justify;
    }

        .container .footer .contacts {
            border: 1px solid black;
            border-radius: 6px;
            text-align: center;
            font-size: 12px;
        }

        .container .footer .resume {
    /*        padding: 5px;*/
            max-width: 490px;
        }

        .container .footer .vat-resume {
            height: 80px;
        }

        .container .footer .payment-coupon .content {
            border-left: 1px dashed black;
            border-top: 1px dashed black;
            padding: 0px 0px 0px 20px;
        }

            .container .footer .payment-coupon .content .client-resume {
                border: 1px solid black;
                padding: 2px 10px 2px 10px;
                display: -webkit-box;
            }

        .container .footer .payment-coupon .scissors {
            height: 24px;
            margin: auto auto;
            background-image: url(../images/scissors.png);
            background-repeat: no-repeat;
            background-position: right;
            position: relative;
            overflow: hidden;
            background-size: 20px 20px;
            top: 12px;
            right: 10px;
        }

    .small-text {
        font-size: 9px;
    }

    .gray-background {
        background-color: #eee;
    }

    .right-align {
        text-align: right;
        padding-right: 5px;
    }

    .center-align {
        text-align: center;
    }


    .left-align {
        text-align: left;
    }


    .width-70 {
        width: 70%;
    }

    .margin-top-10 {
        margin: 10px 0px 0px 0px;
    }

    .margin-bot-10 {
        margin: 0px 0px 10px 0px;
    }

    .margin-bot-20 {
        margin: 0px 0px 20px 0px;
    }

    .margin-top-bot-5 {
        margin: 5px 0px;
    }

    .margin-top-bot-10 {
        margin: 10px 0px;
    }

    .margin-top-bot-15 {
        margin: 15px 0px;
    }

    .width-45 {
        width: 45%;
    }

    .width-10 {
        width: 10%;
    }

    table.rounded {
        border-collapse: separate;
        border-spacing: 0;
        min-width: 350px;
        border-left: solid black 1.5px;
        border-right: solid black 1.5px;
        border-bottom: solid black 1.5px;
        border-top: none;
        border-radius: 10px;
    }

        table.rounded td, th {
            border-left: solid black 1px;
            border-bottom: solid black 1px;
        }

        table.rounded tr:first-child th:first-child {
            border-top-left-radius: 10px;
        }

        /*top-right border-radius*/
        table.rounded tr:first-child th:last-child {
            border-top-right-radius: 10px;
        }

        table.rounded tr:first-child th {
            border-top: solid black 1.5px;
            letter-spacing: 1px;
        }

        table.rounded th {
            background-color: #eee;
        }

        table.rounded td:first-child, th:first-child {
            border-left: none;
        }

        table.rounded.no-interline tr td {
            border-top: none;
        }

        table.rounded tr:last-child td {
            border-bottom: none;
        }

    table.rounded-amount-resume {
        border-collapse: separate;
        border-spacing: 0;
        min-width: 350px;
        border: 2px solid black;
        border-radius: 10px;
    }

        table.rounded-amount-resume .title {
            letter-spacing: 1px;
            padding: 3px 0px 3px 6px;
        }

        table.rounded-amount-resume tr:first-child td:first-child {
            border-radius: 10px 0px 0px 0px;
            border: none;
        }

        table.rounded-amount-resume tr:last-child td:first-child {
            border-radius: 0px 0px 0px 10px;
            border: none;
        }

        table.rounded-amount-resume tr:last-child td:last-child {
            border-radius: 0px 0px 10px 0px;
            border: none;
        }

    .min-width-70 {
        min-width: 70px;
    }

    .min-width-150 {
        min-width: 150px;
    }

    .page-break-avoid {
        page-break-inside: avoid;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-box-flex: 1;
        -webkit-box-pack: end;
    }

    .bank-account h2 {
        font-size: 12px;
    }

    .new-page {
        page-break-before: always;
    }

    .cgv{
        margin: 2px 0px 0px 0px;
    }

    tr.spaceUnder > td {
        padding-bottom: 10px;
    }
</style>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <title></title>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="row">
                <div class="auto-fill logo"></div>
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
                            {{ $bill->company->name }} <br />
                            {{ $bill->company->address }} <br />
                            {{ $bill->company->address_compl }} <br />
                            {{ $bill->company->city->zip_code ?? '' }} {{ $bill->company->city->name ?? '' }} <br />
                        </div>
                    </div>
                </div>
            </div>
            <div class="separator">
                <hr />
            </div>
            <div class="row bill-info">
                <div class="auto-fill">
                    Date : {{ $bill->billed_at->format('d/m/Y') }}
                </div>
                <div class="auto-fill">
                    Facture N° {{ $bill->no_bill }}
                </div>
                <div class="auto-fill">
                    Mode de paiement :
                    @if ($bill->company->payment_method == 0)
                        Prélèvement
                    @elseif ($bill->company->payment_method == 1)
                        Virement
                    @else

                    @endif
                </div>
                <div class="auto-fill">
                    Echéance : {{ Carbon::createFromFormat('d/m/Y', $bill->generated_at)->addDays(7)->format('d/m/Y') }}
                </div>
                <div class="auto-fill">
                    Type de Facturation : {{ $bill->type_period->title }}
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
                                    <tr>
                                        <td class="min-width-70 center-align">@vat.Code</td>
                                        <td class="right-align">@vat.AmountVatNotIncluded.ToString("0.00")</td>
                                        <td class="right-align">@vat.Percent.ToString("0.00")</td>
                                        <td class="right-align">@vat.AmountVat.ToString("0.00")</td>
                                    </tr>
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
                                        <td class="right-align width-70">@Model.TotalVatNotIncluded</td>
                                    </tr>
                                    <tr>
                                        <td class="min-width-150 gray-background title"><strong>Net HT</strong></td>
                                        <td class="border-bottom right-align width-70"><strong>@Model.TotalVatNotIncluded</strong></td>
                                    </tr>
                                    <tr>
                                        <td class="min-width-150 gray-background title">Total TVA</td>
                                        <td class="right-align width-70">@Model.TotalVat</td>
                                    </tr>
                                    <tr>
                                        <td class="min-width-150 border-bottom gray-background title">Total TTC</td>
                                        <td class="border-bottom right-align width-70">@Model.TotalVatIncluded</td>
                                    </tr>
                                    <tr>
                                        <td class="min-width-150 gray-background title"><strong>NET A PAYER</strong></td>
                                        <td class="gray-background right-align width-70"><strong>@Model.TotalVatIncluded</strong></td>
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
                                        <div>:   @Model.ClientCode</div>
                                        <div>:   @Model.NoBill</div>
                                        <div>:   @Model.TotalVatIncluded</div>
                                        <div>:   @Model.Deadline</div>
                                        <div>:   @Model.PayementMethod</div>
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


