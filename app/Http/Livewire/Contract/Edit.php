<?php

namespace App\Http\Livewire\Contract;

use App\Models\Company;
use App\Models\Contract;
use Livewire\Component;
use App\Models\TypeContract;
use App\Models\TypePeriod;
use App\Models\TypeProduct;
use App\Models\TypeVat;
use App\Models\ContractProductDetail;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Carbon\Carbon;
class Edit extends Component
{
    use LivewireAlert;

    public Contract $contract;
    public Company $company;

    public array $listsForFields = [];

    public int|null $selectedTypeContractId = null;

    public $existingProducts = [];

    // pour le nouveau produit à ajouter
    public array $filteredProducts = [];
    public array $selectedProduct = [
        'type_product_id'              => null,
        'designation'                  => '',
        'quantity'                     => 1,
        'capacity'                     => null,
        'monthly_unit_price_without_taxe' => 0,
        'billing_started_at'           => null,
        'billing_terminated_at'        => null,
        'type_vat_id'                  => null,
    ];

    public bool $showEditModal = false;
    public array $editDetailData = [
        'id'                        => null,
        'designation'               => '',
        'quantity'                  => 1,
        'monthly_unit_price_without_taxe' => 0,
        'billing_started_at'        => null,
        'billing_terminated_at'     => null,
    ];

    public function mount(Contract $contract, Company $company)
    {
        $this->contract = $contract;
        $this->company  = $company;

        $this->existingProducts = ContractProductDetail::with('type_product')
            ->where('contract_id', $this->contract->id)
            ->get();
        $this->selectedTypeContractId = $this->contract->products()->first()->type_contract_id;
        $this->initListsForFields();
    }

    public function editDetail(int $id)
    {
        $detail = ContractProductDetail::where('id', $id)->firstOrFail();

        $this->editDetailData = [
            'type_product_id'            => $detail->type_product_id,
            'designation'                => $detail->designation,
            'quantity'                   => $detail->quantity,
            'monthly_unit_price_without_taxe' => $detail->monthly_unit_price_without_taxe,
            'billing_started_at'         => $detail->billing_started_at ? $detail->billing_started_at->format(config('project.date_format')) : null,
            'billing_terminated_at'      => $detail->billing_terminated_at ? $detail->billing_terminated_at->format(config('project.date_format')) : null,
            'last_billed_at'             => $detail->last_billed_at?->format(config('project.date_format')),
            'pivot_id'                   => $detail->id,
        ];

        $this->showEditModal = true;
    }

    public function updateDetail()
    {
        $this->validate([
            'editDetailData.designation' => 'required|string',
            'editDetailData.quantity'    => 'required|integer|min:1',
            'editDetailData.monthly_unit_price_without_taxe' => 'required|numeric',
            'editDetailData.billing_started_at'    => 'nullable|date_format:'.config('project.date_format'),
            'editDetailData.billing_terminated_at' => 'nullable|date_format:'.config('project.date_format'),
        ]);

        $detail = ContractProductDetail::findOrFail($this->editDetailData['pivot_id']);

        $detail->update([
            'designation'                => $this->editDetailData['designation'],
            'quantity'                   => $this->editDetailData['quantity'],
            'monthly_unit_price_without_taxe' => $this->editDetailData['monthly_unit_price_without_taxe'],
            'billing_started_at'         => $this->editDetailData['billing_started_at'],
            'billing_terminated_at'      => $this->editDetailData['billing_terminated_at'],
        ]);

        $this->showEditModal = false;
        $this->alert('success', 'Article mis à jour avec succès');
        $this->existingProducts = ContractProductDetail::with('type_product')
            ->where('contract_id', $this->contract->id)
            ->get();
    }

    public function render()
    {
        return view('livewire.contract.edit', [
            'existingProducts' => $this->existingProducts,
        ]);
    }

    protected function resetSelectedProduct()
    {
        $this->selectedProduct = [
            'type_product_id' => null,
            'designation' => '',
            'quantity' => 1,
            'capacity' => null,
            'monthly_unit_price_without_taxe' => 0,
            'billing_started_at' => null,
            'billing_terminated_at' => null,
            'type_vat_id' => null,
        ];
    }

    public function updatedSelectedProductTypeProductId($productId)
    {
        if ($productId) {
            $product = TypeProduct::find($productId);
            $this->selectedProduct['type_product_id'] = $product->id;
            $this->selectedProduct['designation'] = $product->designation_long;
            $this->selectedProduct['type_vat_id'] = $product->type_vat_id;
        }
    }

    public function updatedSelectedProductMonthlyUnitPriceWithoutTaxe($value)
    {
        $value = floatval(str_replace(',', '.', $value)); // Conversion propre du string en float (gère les virgules)

        $percent = TypeVat::where('id', $this->selectedProduct['type_vat_id'])->first()->percent ?? 0;

        $withTaxe = $value * (1 + ($percent / 100));

        $this->selectedProduct['monthly_unit_price_with_taxe'] = number_format($withTaxe, 2, ',', ' ');
    }


    public function submit()
    {
        try {
            $validated = $this->validate($this->rulesForContract());
             // 2) Si vous mettez à jour le contrat lui-même :
            if (isset($validated['contract'])) {
                $this->contract->update($validated['contract']);
            }

            $this->alert('success', 'Contrat mis à jour avec succès');

            return redirect()->route('admin.companies.edit', $this->company->id)
                ->with('success', 'Contrat créé avec succès');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Ceci dump la liste des messages d'erreur
            dd($e->validator->errors()->toArray());
        }
    }

    protected function rules(): array
    {
        return array_merge(
            $this->rulesForContract(),
            $this->rulesForProduct()
        );
    }

    protected function rulesForContract(): array
    {
        $dateFormat = config('project.date_format');
        return [
            'contract.company_id'        => 'nullable|integer|exists:companies,id',
            'contract.setup_at'          => 'nullable|date_format:' . $dateFormat,
            'contract.terminated_at'     => 'nullable|date_format:' . $dateFormat,
            'contract.billed_at'         => 'nullable|date_format:' . $dateFormat,
            'contract.validated_at'      => 'nullable|date_format:' . $dateFormat,
            'contract.type_period_id'    => 'required|exists:type_periods,id',
        ];
    }

    // Règles pour l’ajout d’un produit
    protected function rulesForProduct(): array
    {
        $dateFormat = config('project.date_format');
        return [
            'selectedProduct.type_product_id'              => 'required|integer|exists:type_products,id',
            'selectedProduct.designation'                  => 'nullable|string',
            'selectedProduct.quantity'                     => 'required|integer|min:1',
            'selectedProduct.capacity'                     => 'nullable|string',
            'selectedProduct.monthly_unit_price_without_taxe' => 'required|numeric',
            'selectedProduct.billing_started_at'           => 'nullable|date_format:' . $dateFormat,
            'selectedProduct.billing_terminated_at'        => 'nullable|date_format:' . $dateFormat,
        ];
    }

    protected function initListsForFields(): void
    {
        $this->listsForFields['company'] = Company::where('id', $this->company->id)->pluck('name', 'id')->toArray();
        $this->listsForFields['type_contracts'] = TypeContract::where('id', $this->selectedTypeContractId)->pluck('title', 'id')->toArray();
        $this->listsForFields['type_periods'] = TypePeriod::pluck('title', 'id')->toArray();
        $this->listsForFields['products'] = TypeProduct::all(); // Pour récupérer les infos complètes
    }
}
