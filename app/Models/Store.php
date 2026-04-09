<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Mainul\CustomHelperFunctions\Helpers\CustomHelper;

class Store extends Model
{
    use HasFactory;
    use Searchable;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'code',
        'total_area_sqft',
        'address',
        'area',
        'postal_code',
        'latitude',
        'longitude',
        'monthly_rent',
        'per_sqr_feet_rent',
        'store_layout_img',
        'store_layout_pdf',
        'contact_person',
        'shop_official_mobile',
        'shop_official_email',
        'status',
        'store_manager_id',
        'opened_date',
        'division_id',
        'store_code',
        'district_id',
        'thana_id',
        'slug',
    ];

    protected $searchableFields = ['*'];

    public static function updateOrCreateStore($request, $store = null)
    {

//        if ($request->hasFile('store_layout_pdf')) {
//            $pdfFile = $request->file('store_layout_pdf');
//            $pdfName = 'layout-pdf-' . time() . '.' . $pdfFile->getClientOriginalExtension();
//            $pdfPath = $pdfFile->storeAs('stores', $pdfName, 'public');
//            $data['store_layout_pdf'] = 'storage/' . $pdfPath;
//        }
//        $request['store_layout_pdf']    = CustomHelper::fileUpload($request->file('store_layout_pdf'), "stores", 'store_layout', null, null, $store->store_layout_pdf ?? null);

        $storeRecord = static::updateOrCreate(['id' => $store?->id], [
            'title'               => $request->title,
            'code'                => strtoupper($request->code),
            'total_area_sqft'     => $request->total_area_sqft,
            'address'             => $request->address,
            'area'                => $request->area,
            'postal_code'         => $request->postal_code,
            'latitude'            => $request->latitude,
            'longitude'           => $request->longitude,
            'monthly_rent'        => $request->monthly_rent,
            'per_sqr_feet_rent'        => $request->per_sqr_feet_rent,
            'contact_person'     => $request->contact_person,
            'shop_official_mobile'=> $request->shop_official_mobile,
            'shop_official_email' => $request->shop_official_email,
            'status'              => $request->status,
//            'store_manager_id'    => $request->store_manager_id ?: null,
            'opened_date'         => $request->opened_date,
            'store_code'         => $request->store_code,
            'division_id'         => $request->division_id,
            'district_id'         => $request->district_id,
            'thana_id'         => $request->thana_id,
            'slug'              => str()->slug($request->title.'-'.$request->code),
            'store_layout_pdf'      => CustomHelper::fileUpload($request->file('store_layout_pdf'), "stores", 'store_layout', null, null, $store->store_layout_pdf ?? null),
        ]);

        // Create a StoreLayout record whenever layout pdf is uploaded
        if ($request->hasFile('store_layout_pdf')) {
            // Deactivate previous layouts
            $storeRecord->storeLayouts()->update(['is_currently_active' => 0]);

            StoreLayout::create([
                'store_id'            => $storeRecord->id,
                'layout_pdf'          => $storeRecord->store_layout_pdf,
                'changed_at'          => now()->toDateString(),
                'is_currently_active' => 1,
            ]);
        }

        return $storeRecord;
    }

    public function storeManager()
    {
        return $this->belongsTo(User::class, 'store_manager_id');
    }

    public function storeLayouts()
    {
        return $this->hasMany(StoreLayout::class);
    }

    public function activeLayout()
    {
        return $this->hasOne(StoreLayout::class)->where('is_currently_active', 1);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function thana()
    {
        return $this->belongsTo(Thana::class);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    public function assignAssetToStores()
    {
        return $this->hasMany(AssignAssetToStore::class);
    }

    public function visualMerchandisings()
    {
        return $this->hasMany(VisualMerchandising::class);
    }
}
