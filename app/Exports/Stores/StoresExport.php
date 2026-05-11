<?php

namespace App\Exports\Stores;

use App\Models\Store;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StoresExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function collection(): Collection
    {
        return Store::query()
            ->with([
                'division:id,name',
                'district:id,name',
                'thana:id,name',
                'storeManager:id,name',
            ])
            ->latest('id')
            ->get();
    }

    public function headings(): array
    {
        return [
//            'ID',
            'Title',
            'Code',
            'Store Code',
            'Store Type',
            'Division',
            'District',
            'Thana',
            'Area/Locality',
            'Address',
            'Postal Code',
            'Contact Person',
            'Official Mobile',
            'Official Email',
            'Manager',
            'Total Area Sqft',
            'Monthly Rent',
            'Per Sqft Rent',
            'Latitude',
            'Longitude',
            'Opened Date',
            'Status',
            'Created At',
        ];
    }

    public function map($store): array
    {
        return [
//            $store->id,
            $store->title,
            $store->code,
            $store->store_code,
            $store->store_type,
            $store->division?->name,
            $store->district?->name,
            $store->thana?->name,
            $store->area,
            $store->address,
            $store->postal_code,
            $store->contact_person,
            $store->shop_official_mobile,
            $store->shop_official_email,
            $store->storeManager?->name,
            $store->total_area_sqft,
            $store->monthly_rent,
            $store->per_sqr_feet_rent,
            $store->latitude,
            $store->longitude,
            $store->opened_date,
            (int) $store->status === 1 ? 'Active' : 'Inactive',
            optional($store->created_at)->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true],
            ],
        ];
    }
}
