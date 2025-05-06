@php
    $thresholdMin  = 1500;
    $thresholdFull = 2250;

    $items = $products->values()->all();
    $charCounts = collect($items)->map(function($product) use($dateStart) {
        $text =
            $product->designation
          . $product->contract->calculateBillingPeriod($dateStart)
          . $product->contract->company->observations;
        return mb_strlen($text);
    })->all();

    $pages = [];
    while (count($items)) {
        $total = array_sum($charCounts);

        if ($total <= $thresholdMin) {
            $pages[] = [
                'type'  => 'min_with_footer',
                'items' => $items,
            ];
            break;
        }

        if ($total <= $thresholdFull) {
            $pages[] = [
                'type'  => 'full_table',
                'items' => $items,
            ];
            $pages[] = [
                'type'  => 'footer_only',
                'items' => [],
            ];
            break;
        }

        $sum = 0;
        $chunkItems  = [];
        $chunkCounts = [];
        foreach ($items as $i => $itm) {
            if ($sum + $charCounts[$i] > $thresholdMin) {
                break;
            }
            $sum           += $charCounts[$i];
            $chunkItems[]   = $itm;
            $chunkCounts[]  = $charCounts[$i];
        }
        // guard en cas d’item trop gros
        if (empty($chunkItems)) {
            $chunkItems  = [array_shift($items)];
            $chunkCounts = [array_shift($charCounts)];
        }

        $pages[] = [
            'type'  => 'min_with_footer',
            'items' => $chunkItems,
        ];
        // on retire ces items de la liste
        $items      = array_slice($items, count($chunkItems));
        $charCounts = array_slice($charCounts, count($chunkCounts));
    }
@endphp

@foreach($pages as $page)
    @if($page['type'] === 'min_with_footer')
        <div class="resume-table margin-bot-20">
            <table class="rounded no-interline main-table-min">
                <tr>
                    <th class="width-10">Référence</th>
                    <th>Désignation</th>
                    <th class="width-10">Qté</th>
                    <th class="width-10">P.U. HT</th>
                    <th class="width-10">Montant HT</th>
                    <th class="width-10">TVA</th>
                </tr>
                <tbody>
                    @foreach($page['items'] as $product)
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

    @elseif($page['type'] === 'full_table')
        <div class="resume-table margin-bot-20">
            <table class="rounded no-interline main-table">
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
                    @foreach($page['items'] as $product)
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
    @endif

    @if(! $loop->last)
        <div style="page-break-before: always;"></div>
    @endif
@endforeach
