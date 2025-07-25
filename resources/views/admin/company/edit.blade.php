@extends('layouts.admin')
@section('content')
<div class="row pb-4">
    <div class="card">
        <div class="card-header">
            <div class="card-header-container">
                <h6 class="card-title">
                    Editer le client {{ $company->name }}
                </h6>
            </div>
        </div>

        <div class="flex items-center justify-around h-12 text-lg font-semibold text-center bg-red-100 border-2 border-red-400">
            <a id="tab1Btn" class="w-1/3 py-[10px] tablink cursor-pointer border-r-2 border-red-400 text-red-800 hover:bg-red-300" onclick="openTab('tab1')">
                informations
            </a>
            <a id="tab2Btn" class="w-1/3 py-[10px] tablink cursor-pointer border-r-2 border-red-400 text-red-800 hover:bg-red-300" onclick="openTab('tab2')">
                contrats
            </a>
            <a id="tab3Btn" class="w-1/3 py-[10px] tablink cursor-pointer border-r-2 border-red-400 text-red-800 hover:bg-red-300" onclick="openTab('tab3')">
                contacts
            </a>
            <a id="tab4Btn" class="w-1/3 py-[10px] tablink cursor-pointer text-red-800 hover:bg-red-300" onclick="openTab('tab4')">
                compte bancaire
            </a>
        </div>

        <div id="tab1" class="tabcontent p-4 bg-blueGray-100">
            @livewire('company.edit', [$company])
        </div>
        <div id="tab2" class="hidden tabcontent p-4 bg-blueGray-100">
            @livewire('company.contracts', [$company])
        </div>
        <div id="tab3" class="hidden tabcontent p-4 bg-blueGray-100">
            @livewire('company.contacts', [$company])
        </div>
        <div id="tab4" class="hidden tabcontent p-4 bg-blueGray-100">
            @livewire('company.bank-account', [$company])
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

