@section('content')
<div class="container">
    <div class="header flex flex-col items-center text-center">
        <div class="logo bg-no-repeat bg-center w-[280px] h-[80px]" style="background-image: url('{{ asset('images/logo.jpg') }}');"></div>
        <h1 class="text-xl font-bold">Facture</h1>
    </div>

    <div class="client-resume flex justify-between mt-5 border-b pb-3">
        <div class="left-info w-[50mm] text-left">
            <p>Client: {{ $client->name }}</p>
            <p>Email: {{ $client->email }}</p>
        </div>
        <div class="bill-info text-right">
            <p>Date: {{ now()->format('d/m/Y') }}</p>
            <p>Numéro de facture: #{{ $bill->id }}</p>
        </div>
    </div>

    <div class="resume-table mt-5">
        <table class="w-full border-collapse border border-gray-300 rounded">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border border-gray-300 px-2 py-1">Description</th>
                    <th class="border border-gray-300 px-2 py-1">Quantité</th>
                    <th class="border border-gray-300 px-2 py-1">Prix Unitaire</th>
                    <th class="border border-gray-300 px-2 py-1">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bill->product as $item)
                <tr>
                    <td class="border border-gray-300 px-2 py-1">{{ $item->description }}</td>
                    <td class="border border-gray-300 px-2 py-1 text-center">{{ $item->quantity }}</td>
                    <td class="border border-gray-300 px-2 py-1 text-right">{{ number_format($item->unit_price, 2) }} €</td>
                    <td class="border border-gray-300 px-2 py-1 text-right">{{ number_format($item->total, 2) }} €</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer mt-5 flex justify-between items-center">
        <div class="contacts border p-2 text-center text-sm rounded">
            <p>Contact: support@example.com</p>
            <p>Téléphone: +33 1 23 45 67 89</p>
        </div>
        <div class="amount-resume border p-2 rounded">
            <p class="font-bold">Total HT: {{ number_format($bill->subtotal, 2) }} €</p>
            <p class="font-bold">TVA (20%): {{ number_format($bill->tax, 2) }} €</p>
            <p class="font-bold text-lg">Total TTC: {{ number_format($bill->total, 2) }} €</p>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .container { font-family: Arial, sans-serif; }
    .rounded { border-radius: 10px; }
    .border { border: 1px solid black; }
</style>
@endsection
