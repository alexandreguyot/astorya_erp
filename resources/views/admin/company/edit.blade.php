@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card">
        <div class="card-header !bg-red-300 border-b-2 border-red-400">
            <div class="card-header-container">
                <h6 class="card-title">
                    Editer le client {{ $company->name }}
                </h6>
            </div>
        </div>

        <div class="flex items-center justify-around h-12 text-lg font-semibold text-center bg-red-300">
            <a id="tab1Btn" class="w-1/3 py-[10px] tablink cursor-pointer border-r-2 border-red-400 text-red-800 hover:bg-red-500" onclick="openTab('tab1')">
                informations
            </a>
            <a id="tab5Btn" class="w-1/3 py-[10px] tablink cursor-pointer border-r-2 border-red-400 text-red-800 hover:bg-red-500" onclick="openTab('tab2')">
                contrats
            </a>
            <a id="tab5Btn" class="w-1/3 py-[10px] tablink cursor-pointer text-red-800 hover:bg-red-500" onclick="openTab('tab3')">
                contacts
            </a>
        </div>

        <div id="tab1" class="tabcontent">
            <div class="p-4 bg-white">
                @livewire('company.edit', [$company])
            </div>
        </div>
        <div id="tab2" class="hidden tabcontent">
            <div class="p-4 bg-white">
                @livewire('company.contracts',  [$company])
            </div>
        </div>
        <div id="tab3" class="hidden tabcontent">
            <div class="p-4 bg-white">
                @livewire('company.contacts',  [$company])
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialiser le premier onglet comme actif
    document.getElementById("tab1").classList.remove('hidden');
    document.getElementById("tab1Btn").classList.add('bg-member');

    function openTab(tabName) {
        var tabcontent, tablinks;

        // Cacher tous les contenus d'onglet
        tabcontent = document.getElementsByClassName("tabcontent");
        for (var i = 0; i < tabcontent.length; i++) {
            tabcontent[i].classList.add('hidden');
        }

        // Supprimer la classe active de tous les boutons d'onglet
        tablinks = document.getElementsByClassName("tablink");
        for (var i = 0; i < tablinks.length; i++) {
            tablinks[i].classList.remove('bg-member');
        }

        // Afficher le contenu de l'onglet sélectionné et mettre à jour le bouton d'onglet actif
        document.getElementById(tabName).classList.remove('hidden');
        document.getElementById(tabName + 'Btn').classList.add('bg-member');
    }
</script>
@endpush

