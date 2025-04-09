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
            Date : {{ $bill->no_bill ?? 'A DEFINIR' }}
        </div>
        <div class="auto-fill">
            Facture N° {{ $bill->no_bill ?? 'A DEFINIR' }}
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
