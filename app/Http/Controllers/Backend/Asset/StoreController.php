<?php

namespace App\Http\Controllers\Backend\Asset;

use App\Exports\Stores\StoresExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Asset\StoreRequest;
use App\Models\AssetType;
use App\Models\District;
use App\Models\Division;
use App\Models\Store;
use App\Models\StoreLayout;
use App\Models\Thana;
use App\Models\User;
use Mainul\CustomHelperFunctions\Helpers\CustomHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class StoreController extends Controller
{
    public function index()
    {
        return view('backend.asset-management.store-theme', [
            'stores'    => Store::with('storeManager', 'division', 'district', 'thana', 'storeLayouts')->latest()->get(),
            'users'     => User::orderBy('name')->get(),
            'divisions' => Division::orderBy('name')->get(),
            'assetTypes'=> AssetType::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function export()
    {
        $filename = 'stores-' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new StoresExport(), $filename);
    }

    public function jsonList()
    {
        $stores = Store::with('division:id,name')
            ->latest()
            ->get(['id', 'title', 'code', 'store_code', 'total_area_sqft', 'monthly_rent', 'per_sqr_feet_rent', 'status', 'store_type', 'division_id']);
        return response()->json($stores);
    }

    public function getDistricts($divisionId)
    {
        $districts = District::select('id', 'name')
            ->where('division_id', $divisionId)
            ->orderBy('name')
            ->get();
        return response()->json($districts);
    }

    public function getThanas($districtId)
    {
        $thanas = Thana::select('id', 'name')
            ->where('district_id', $districtId)
            ->orderBy('name')
            ->get();
        return response()->json($thanas);
    }

    public function getStoresByDistrict($districtId)
    {
        $stores = Store::select('id', 'title', 'code')
            ->where('district_id', $districtId)
            ->orderBy('title')
            ->get();
        return response()->json($stores);
    }

    public function layoutStores(Request $request)
    {
        $query = Store::query()
            ->select('id', 'title', 'code', 'division_id')
            ->with('division:id,name')
            ->withCount('storeLayouts')
            ->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('store_code', 'like', "%{$search}%");
            });
        }

        if ($divisionName = $request->input('division')) {
            $query->whereHas('division', fn ($q) => $q->where('name', $divisionName));
        }

        return response()->json($query->paginate(15));
    }

    public function uploadLayout(Request $request, Store $store)
    {
        $request->validate([
            'store_layout_pdf' => 'required|mimes:pdf|max:10240',
            'changelog'        => 'nullable|string|max:1000',
        ], [
            'store_layout_pdf.required' => 'Please select a PDF file.',
            'store_layout_pdf.mimes'    => 'The file must be a PDF.',
            'store_layout_pdf.max'      => 'The PDF must not exceed 10MB.',
        ]);

        try {
            DB::transaction(function () use ($request, $store) {
                $pdfPath = CustomHelper::fileUpload($request->file('store_layout_pdf'), 'stores', 'store_layout', null, null, null);

                $store->update(['store_layout_pdf' => $pdfPath]);
                $store->storeLayouts()->update(['is_currently_active' => 0]);

                StoreLayout::create([
                    'store_id'            => $store->id,
                    'layout_pdf'          => $pdfPath,
                    'changed_at'          => now()->toDateString(),
                    'is_currently_active' => 1,
                    'change_log'          => $request->changelog,
                ]);
            });

            return response()->json(['success' => true, 'message' => 'Layout uploaded successfully.']);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'message' => $exception->getMessage()], 500);
        }
    }

    public function store(StoreRequest $request)
    {
//        $request->validate($this->validationRules(), $this->validationMessages());

        try {
            DB::transaction(function () use ($request) {
                Store::updateOrCreateStore($request);
            });
            return response()->json(['success' => true, 'message' => 'Store created successfully.']);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'message' => $exception->getMessage()]);
        }
    }

    public function show(string $id)
    {
        $store = Store::with('storeManager', 'storeLayouts', 'division', 'district', 'thana')->findOrFail($id);
        return response()->json($store);
    }

    public function edit(string $id)
    {
        $store = Store::findOrFail($id);
        return response()->json($store);
    }

    public function update(StoreRequest $request, string $id)
    {
        $store = Store::findOrFail($id);

//        $rules = $this->validationRules($store->id);

//        $request->validate($rules, $this->validationMessages());

        try {
            DB::transaction(function () use ($request, $store) {
                Store::updateOrCreateStore($request, $store);
            });
            return response()->json(['success' => true, 'message' => 'Store updated successfully.']);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'message' => $exception->getMessage()]);
        }
    }

    public function destroy(string $id)
    {
        $store = Store::findOrFail($id);
        $store->delete();
        return response()->json(['success' => true, 'message' => 'Store deleted successfully.']);
    }

    private function validationRules($ignoreId = null): array
    {
        return [
            'title'               => ['required', 'string', 'max:255', Rule::unique('stores', 'title')->ignore($ignoreId)],
            'code'                => ['required', 'string', 'min:2', Rule::unique('stores', 'code')->ignore($ignoreId)],
            'store_code'          => 'nullable|string|max:50',
            'total_area_sqft'     => 'nullable|numeric|min:0',
            'address'             => 'nullable|string|max:1000',
            'area'                => 'nullable|string|max:255',
            'division_id'         => 'nullable|exists:divisions,id',
            'district_id'         => 'nullable|exists:districts,id',
            'thana_id'            => 'nullable|exists:thanas,id',
            'postal_code'         => 'nullable|string|max:20',
            'latitude'            => 'nullable|numeric|between:-90,90',
            'longitude'           => 'nullable|numeric|between:-180,180',
            'monthly_rent'        => 'nullable|numeric|min:0',
            'per_sqr_feet_rent'   => 'nullable|numeric|min:0',
            'store_layout_pdf'    => 'nullable|mimes:pdf|max:10240',
            'contact_persion'     => 'required|string|max:255',
            'shop_official_mobile'=> ['required', 'string', 'size:11', 'regex:/^01[3-9]\d{8}$/'],
            'shop_official_email' => 'required|email|max:255',
            'status'              => 'required|in:0,1',
//            'store_manager_id'    => 'nullable|exists:users,id',
            'opened_date'         => 'nullable|date',
        ];
    }

    private function validationMessages(): array
    {
        return [
            'title.required'    => 'The store title is required.',
            'title.string'      => 'The store title must be a valid text.',
            'title.max'         => 'The store title must not exceed 255 characters.',
            'title.unique'      => 'This store title is already taken.',
            'code.required'     => 'The store code is required.',
            'code.string'       => 'The store code must be a valid text.',
            'code.min'          => 'The store code must be at least 2 characters.',
            'code.alpha'        => 'The store code must contain only letters.',
            'code.unique'       => 'This store code is already in use.',
            'total_area_sqft.numeric' => 'Store size must be a valid number.',
            'total_area_sqft.min'     => 'Store size cannot be negative.',
            'address.max'       => 'The address must not exceed 1000 characters.',
            'latitude.numeric'  => 'Latitude must be a valid number.',
            'latitude.between'  => 'Latitude must be between -90 and 90.',
            'longitude.numeric' => 'Longitude must be a valid number.',
            'longitude.between' => 'Longitude must be between -180 and 180.',
            'monthly_rent.numeric'      => 'Monthly rent must be a valid number.',
            'monthly_rent.min'          => 'Monthly rent cannot be negative.',
            'per_sqr_feet_rent.numeric' => 'Rent per sq ft must be a valid number.',
            'per_sqr_feet_rent.min'     => 'Rent per sq ft cannot be negative.',
            'store_layout_pdf.mimes'    => 'The layout file must be a PDF.',
            'store_layout_pdf.max'      => 'The layout PDF must not exceed 10MB.',
            'contact_persion.required'  => 'The contact person name is required.',
            'contact_persion.max'       => 'The contact person name must not exceed 255 characters.',
            'shop_official_mobile.required' => 'The phone number is required.',
            'shop_official_mobile.size'     => 'The phone number must be exactly 11 digits.',
            'shop_official_mobile.regex'    => 'Please enter a valid Bangladeshi mobile number (e.g. 01XXXXXXXXX).',
            'shop_official_email.required'  => 'The email address is required.',
            'shop_official_email.email'     => 'Please enter a valid email address.',
            'shop_official_email.max'       => 'The email address must not exceed 255 characters.',
            'status.required'   => 'The status is required.',
            'status.in'         => 'The status must be either Active or Inactive.',
            'opened_date.date'  => 'Please enter a valid date.',
            'division_id.exists'  => 'The selected division is invalid.',
            'district_id.exists'  => 'The selected district is invalid.',
            'thana_id.exists'     => 'The selected thana is invalid.',
        ];
    }
}
