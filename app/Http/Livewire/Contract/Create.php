<?php

namespace App\Http\Livewire\Contract;

use App\Models\Company;
use App\Models\Contract;
use Livewire\Component;
use App\Models\TypeContract;
use App\Models\TypePeriod;
use App\Models\TypeProduct;
use App\Models\TypeVat;
use Carbon\Carbon;

class Create extends Component
{
    public Contract $contract;
    public Company $company;

    public array $listsForFields = [];
    public array $filteredProducts = [];

    public int|null $selectedTypeContractId = null;


    public array $selectedProduct = [
        'type_product_id' => null,
        'designation' => '',
        'quantity' => 1,
        'capacity' => null,
        'monthly_unit_price_without_taxe' => 0,
        'billing_started_at' => null,
        'billing_terminated_at' => null,
        'type_vat_id' => null,
    ];

    public function mount(Company $company)
    {
        $this->company = $company;
        $this->contract = new Contract();
        $this->contract->company_id = $this->company->id;
        $this->initListsForFields();
    }

    public function render()
    {
        return view('livewire.contract.create');
    }

    public function updatedContractSetupAt($value)
    {
        $d = Carbon::createFromFormat(
            config('project.date_format'),
            $value
        );
        $this->contract->terminated_at = $d
            ->copy()
            ->addYears(100)
            ->format(config('project.date_format'));
    }

    public function updatedSelectedTypeContractId($value)
    {
        if ($value) {
            $this->filteredProducts = TypeProduct::where('type_contract_id', $value)
                ->pluck('designation_short', 'id')
                ->toArray();
        } else {
            $this->filteredProducts = [];
        }

        $this->resetSelectedProduct();
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
        $this->validate();

        $this->contract->save();

        $this->contract->products()->attach(
            $this->selectedProduct['type_product_id'],
            [
                'contract_id' => $this->contract->id,
                'type_product_id' => $this->selectedProduct['type_product_id'],
                'designation' => $this->selectedProduct['designation'],
                'quantity' => $this->selectedProduct['quantity'],
                'capacity' => $this->selectedProduct['capacity'],
                'monthly_unit_price_without_taxe' => str_replace(',','.',$this->selectedProduct['monthly_unit_price_without_taxe']),
                'billing_started_at' => $this->selectedProduct['billing_started_at'] ?? null,
                'billing_terminated_at' => $this->selectedProduct['billing_terminated_at'] ?? null,
            ]
        );

        return redirect()->route('admin.companies.edit', $this->company->id)
            ->with('success', 'Contrat créé avec succès');
    }

    protected function rules(): array
    {
        return [
            'contract.company_id' => [
                'integer',
                'exists:companies,id',
                'nullable',
            ],
            'contract.setup_at' => [
                'required',
                'date_format:' . config('project.date_format'),
            ],
            'contract.terminated_at' => [
                'required',
                'nullable',
                'date_format:' . config('project.date_format'),
            ],
            'contract.billed_at' => [
                'nullable',
                'date_format:' . config('project.date_format'),
            ],
            'contract.validated_at' => [
                'nullable',
                'date_format:' . config('project.date_format'),
            ],
            'contract.company_id' => ['integer', 'exists:companies,id', 'nullable'],
            'contract.type_period_id' => ['required', 'exists:type_periods,id'],
            'selectedProduct.type_product_id' => ['required', 'integer', 'exists:type_products,id'],
            'selectedProduct.designation' => ['nullable', 'string'],
            'selectedProduct.quantity' => ['required', 'integer', 'min:1'],
            'selectedProduct.capacity' => ['nullable', 'string'],
            'selectedProduct.monthly_unit_price_without_taxe' => ['required'],
            'selectedTypeContractId' => ['required', 'exists:type_contracts,id'],
        ];
    }

    protected function initListsForFields(): void
    {
        $this->listsForFields['company'] = Company::where('id', $this->company->id)->pluck('name', 'id')->toArray();
        $this->listsForFields['type_contracts'] = TypeContract::pluck('title', 'id')->toArray();
        $this->listsForFields['type_periods'] = TypePeriod::pluck('title', 'id')->toArray();
        $this->listsForFields['products'] = TypeProduct::all(); // Pour récupérer les infos complètes
    }
}
