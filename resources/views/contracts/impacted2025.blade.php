@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
  <h1 class="text-xl font-bold mb-4">Liste des contrats possiblement impactés par le problème 2024-2025</h1>
  <table class="table-auto w-full">
    <thead>
      <tr class="bg-gray-100">
        <th class="px-4 py-2">ID Contrat</th>
        <th class="px-4 py-2">Produit</th>
        <th class="px-4 py-2">Périodicité</th>
        <th class="px-4 py-2">Société</th>
        <th class="px-4 py-2">Type de contrat</th>

        <th class="px-4 py-2">Date de fin de facturation du contrat</th>
        <th class="px-4 py-2">Date de fin de facturation de l'article</th>
      </tr>
    </thead>
    <tbody>
      @forelse($contract_product_details as $detail)
        @php
            $terminatedAt = $detail->contract->terminated_at;
            $billingEnd   = $detail->billing_terminated_at;

            $cDate = $terminatedAt
                    ? Carbon\Carbon::createFromFormat(config('project.date_format'), $terminatedAt)
                    : null;
            $bDate = $billingEnd
                    ? Carbon\Carbon::createFromFormat(config('project.date_format'), $billingEnd)
                    : null;
        @endphp

        @if(!$cDate || !$bDate || !$cDate->gt($bDate))
            @continue
        @endif
          <tr class="hover:bg-gray-50">
            <td class="border px-4 py-2">
              <a href="">
                #{{ $detail->contract->id }}
              </a>
            </td>
            <td class="border px-4 py-2">
              {{ $detail->designation }}
            </td>
            <td class="border px-4 py-2">
              {{ $detail->contract->type_period->title }}
            </td>
            <td class="border px-4 py-2">
              {{ $detail->contract->company->name }}
            </td>
             <td class="border px-4 py-2">
              {{ $detail->type_product->type_contract->title }}
            </td>
            <td class="border px-4 py-2">
              {{ $detail->contract->terminated_at ? \Carbon\Carbon::createFromFormat(config('project.date_format'), $detail->contract->terminated_at)->format('d/m/Y') : 'N/A' }}
            </td>
            <td class="border px-4 py-2">
              {{ \Carbon\Carbon::createFromFormat(config('project.date_format'), $detail->billing_terminated_at)->format('d/m/Y') }}
            </td>
          </tr>
      @empty
        <tr>
          <td colspan="3" class="text-center py-4">Aucun contrat trouvé.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection
