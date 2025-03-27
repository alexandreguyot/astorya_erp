<?php

namespace App\Http\Livewire\Bill;

use App\Models\Bill;
use App\Models\Company;
use App\Models\PeriodType;
use Livewire\Component;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Edit extends Component
{
    public Bill $bill;

    public array $mediaToRemove = [];

    public array $listsForFields = [];

    public array $mediaCollections = [];

    public function addMedia($media): void
    {
        $this->mediaCollections[$media['collection_name']][] = $media;
    }

    public function removeMedia($media): void
    {
        $collection = collect($this->mediaCollections[$media['collection_name']]);

        $this->mediaCollections[$media['collection_name']] = $collection->reject(fn ($item) => $item['uuid'] === $media['uuid'])->toArray();

        $this->mediaToRemove[] = $media['uuid'];
    }

    public function getMediaCollection($name)
    {
        return $this->mediaCollections[$name];
    }

    protected function syncMedia(): void
    {
        collect($this->mediaCollections)->flatten(1)
            ->each(fn ($item) => Media::where('uuid', $item['uuid'])
                ->update(['model_id' => $this->bill->id]));

        Media::whereIn('uuid', $this->mediaToRemove)->delete();
    }

    public function mount(Bill $bill)
    {
        $this->bill = $bill;
        $this->initListsForFields();
        $this->mediaCollections = [

            'bill_file_path' => $bill->file_path,

        ];
    }

    public function render()
    {
        return view('livewire.bill.edit');
    }

    public function submit()
    {
        $this->validate();

        $this->bill->save();
        $this->syncMedia();

        return redirect()->route('admin.bills.index');
    }

    protected function rules(): array
    {
        return [
            'bill.no_bill' => [
                'string',
                'nullable',
            ],
            'bill.amount' => [
                'numeric',
                'nullable',
            ],
            'bill.amount_vat_included' => [
                'numeric',
                'nullable',
            ],
            'bill.one_bill_per_period' => [
                'boolean',
            ],
            'bill.started_at' => [
                'nullable',
                'date_format:' . config('project.datetime_format'),
            ],
            'bill.billed_at' => [
                'nullable',
                'date_format:' . config('project.date_format'),
            ],
            'bill.generated_at' => [
                'nullable',
                'date_format:' . config('project.datetime_format'),
            ],
            'bill.validated_at' => [
                'nullable',
                'date_format:' . config('project.datetime_format'),
            ],
            'bill.sent_at' => [
                'nullable',
                'date_format:' . config('project.datetime_format'),
            ],
            'bill.to_be_collected' => [
                'boolean',
            ],
            'bill.collected_at' => [
                'nullable',
                'date_format:' . config('project.datetime_format'),
            ],
            'bill.recorded_at' => [
                'nullable',
                'date_format:' . config('project.datetime_format'),
            ],
            'mediaCollections.bill_file_path' => [
                'array',
                'nullable',
            ],
            'mediaCollections.bill_file_path.*.id' => [
                'integer',
                'exists:media,id',
            ],
            'bill.company_id' => [
                'integer',
                'exists:companies,id',
                'required',
            ],
            'bill.type_period_id' => [
                'integer',
                'exists:period_types,id',
                'nullable',
            ],
        ];
    }

    protected function initListsForFields(): void
    {
        $this->listsForFields['company']     = Company::pluck('name', 'id')->toArray();
        $this->listsForFields['type_period'] = PeriodType::pluck('title', 'id')->toArray();
    }
}
