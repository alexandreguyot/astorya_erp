@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-blueGray-100">
        <div class="card-header">
            <div class="card-header-container">
                <h6 class="card-title">
                    Modifier l'abonnement {{ $contract->id }} pour {{ $contract->company->name }}
                </h6>
            </div>
        </div>

        <div class="flex items-center justify-around h-12 text-lg font-semibold text-center bg-red-100 border-2 border-red-400">
            <a id="tab1Btn" class="w-1/2 py-[10px] tablink cursor-pointer border-r-2 border-red-400 text-red-800 hover:bg-red-300" onclick="openTab('tab1')">
                informations
            </a>
            <a id="tab2Btn" class="w-1/2 py-[10px] tablink cursor-pointer border-r-2 border-red-400 text-red-800 hover:bg-red-300" onclick="openTab('tab2')">
                articles
            </a>
        </div>

        <div id="tab1" class="tabcontent p-4 bg-blueGray-100">
            @livewire('contract.edit', [$contract, $contract->company])
        </div>
        <div id="tab2" class="hidden tabcontent p-4 bg-blueGray-100">
            @livewire('contract.products', [$contract, $contract->company])
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById("tab1").classList.remove('hidden');
    document.getElementById("tab1Btn").classList.add('bg-red-300');

    function openTab(tabName) {
        var tabcontent, tablinks;

        tabcontent = document.getElementsByClassName("tabcontent");
        for (var i = 0; i < tabcontent.length; i++) {
            tabcontent[i].classList.add('hidden');
        }

        tablinks = document.getElementsByClassName("tablink");
        for (var i = 0; i < tablinks.length; i++) {
            tablinks[i].classList.remove('bg-red-300');
        }

        document.getElementById(tabName).classList.remove('hidden');
        document.getElementById(tabName + 'Btn').classList.add('bg-red-300');
    }
</script>
@endpush
