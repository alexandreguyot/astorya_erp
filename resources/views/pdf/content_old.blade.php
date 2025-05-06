@foreach ($products->chunk(3) as $index => $chunk)
    @if ($index > 0)
        <div style="page-break-before: always;"></div>
        @include('pdf.header')
    @endif

    <div class="resume-table margin-bot-20 main-col-min">
        <table class="rounded no-interline main-table-min" id="main-table">
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
                @foreach ($chunk as $product)
                    @php $contract = $product->contract; @endphp
                    <tr class="spaceUnder product-row">
                        <td class="width-10 center-align">{{ $product->type_product->code }}</td>
                        <td style="font-size: 14px">
                            {{ $product->designation }}<br />
                            <b>{{ $contract->calculateBillingPeriod($dateStart) }}</b><br />
                            {{ $contract->company->observations }}
                        </td>
                        <td class="width-10 center-align">{{ $product->quantity }}</td>
                        <td class="width-10 center-align">{{ $product->formatted_monthly_unit_price_without_taxe }}</td>
                        <td class="width-10 center-align">{{ $product->proratedBaseFormatted(Carbon\Carbon::createFromFormat(config('project.date_format'), $dateStart)) }}</td>
                        <td class="width-10 center-align">{{ $product->type_product->type_vat->code_vat }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endforeach
