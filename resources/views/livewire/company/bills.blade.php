<div class="space-y-4">
    <div class="overflow-x-auto bg-white border rounded-lg">
        <table class="table table-index w-full">
            <thead>
                <tr>
                    <th class="w-2/3">N° de facture</th>
                    <th class="w-2/3">Généré le</th>
                    <th class="w-1/3 text-right">Téléchargement</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bills as $bill)
                    <tr>
                        <td class="font-medium">
                            {{ $bill->no_bill }}
                        </td>
                        <td class="font-medium">
                            {{ $bill->generated_at }}
                        </td>
                        <td class="text-right">
                            @php
                                // Si tes PDFs sont stockés dans storage/app/private/...
                                $path = $bill->file_path ? ('/' . ltrim($bill->file_path, '/')) : null;
                                $pdfExists = $path ? \Illuminate\Support\Facades\Storage::disk('local')->exists($path) : false;
                            @endphp

                            @if ($pdfExists)
                                <a href="{{ route('admin.bills.pdf.stream', $bill->no_bill) }}" target="_blank"
                                   class="btn btn-sm">
                                    Télécharger le PDF
                                </a>
                            @else
                                <span class="text-gray-400">PDF manquant</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-center py-8 text-gray-500">
                            Aucune facture pour cette entreprise.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $bills->onEachSide(1)->links() }}
    </div>
</div>
