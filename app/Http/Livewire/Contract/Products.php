<?php

namespace App\Http\Livewire\Contract;

use Livewire\Component;
use App\Models\Company;
use App\Models\Contract;
use App\Models\TypeContract;
use App\Models\TypePeriod;
use App\Models\TypeProduct;
use App\Models\TypeVat;
use App\Models\ContractProductDetail;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Products extends Component
{
    use LivewireAlert;

    public Contract $contract;
    public Company $company;
    public int $type_contract_id;

    public array $listsForFields = [];

    public int|null $selectedTypeContractId = null;

    public $existingProducts = [];

    public bool  $showAddModal     = false;
    public array $newProductData  = [
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
        $this->type_contract_id = $this->contract->contract_product_detail()->first()->type_product()->first()->type_contract_id;

        $this->loadProducts();
        $this->initListsForFields();
    }

    protected function loadProducts(): void
    {
        $this->existingProducts = ContractProductDetail::with('type_product')
            ->where('contract_id', $this->type_contract_id)
            ->get();
    }

    public function showAddModal(): void
    {
        $this->reset('newProductData');
        $this->showAddModal = true;
    }

    public function editDetail(int $id)
    {
        $detail = ContractProductDetail::where('id', $id)->firstOrFail();

        $this->editDetailData = [
            'type_product_id'            => $detail->type_product_id,
            'designation'                => $detail->designation,
            'quantity'                   => $detail->quantity,
            'monthly_unit_price_without_taxe' => $detail->monthly_unit_price_without_taxe,
            'billing_started_at'         => $detail->billing_started_at?->format(config('project.date_format')),
            'billing_terminated_at'      => $detail->billing_terminated_at?->format(config('project.date_format')),
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
        $this->loadProducts();
    }

    public function updatedNewProductDataTypeProductId($productId)
    {
        if ($productId) {
            $prod = TypeProduct::find($productId);
            $this->newProductData['designation']     = $prod->designation_long;
            $this->newProductData['type_vat_id']     = $prod->type_vat_id;
            $this->newProductData['monthly_unit_price_without_taxe'] = 0;
        }
    }

    public function saveNewProduct()
    {
        $this->validate([
            'newProductData.type_product_id'              => 'required|exists:type_products,id',
            'newProductData.designation'                  => 'required|string',
            'newProductData.quantity'                     => 'required|integer|min:1',
            'newProductData.monthly_unit_price_without_taxe' => 'required|numeric',
            'newProductData.billing_started_at'           => 'nullable|date_format:'.config('project.date_format'),
            'newProductData.billing_terminated_at'        => 'nullable|date_format:'.config('project.date_format'),
        ]);

        ContractProductDetail::create([
            'contract_id'                    => $this->contract->id,
            'type_product_id'                => $this->newProductData['type_product_id'],
            'designation'                    => $this->newProductData['designation'],
            'quantity'                       => $this->newProductData['quantity'],
            'capacity'                       => $this->newProductData['capacity'],
            'monthly_unit_price_without_taxe'=> $this->newProductData['monthly_unit_price_without_taxe'],
            'billing_started_at'             => $this->newProductData['billing_started_at'],
            'billing_terminated_at'          => $this->newProductData['billing_terminated_at'],
        ]);

        $this->showAddModal = false;
        $this->alert('success', 'Article ajouté avec succès');
        $this->loadProducts();
    }


    public function render()
    {
        return view('livewire.contract.products', [
            'existingProducts' => $this->existingProducts,
        ]);
    }

    protected function initListsForFields(): void
    {
        $this->listsForFields['company'] = Company::where('id', $this->company->id)->pluck('name', 'id')->toArray();
        $this->listsForFields['type_contracts'] = TypeContract::where('id', $this->contract->id)->pluck('title', 'id')->toArray();
        $this->listsForFields['type_periods'] = TypePeriod::pluck('title', 'id')->toArray();
        $this->listsForFields['products'] = TypeProduct::where('type_contract_id', $this->type_contract_id)->pluck('designation_short', 'id')->toArray();
    }
}
