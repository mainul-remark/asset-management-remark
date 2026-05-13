<?php

namespace App\Http\Controllers\Backend\Billing;

use App\Http\Controllers\Controller;
use App\Models\BillDispute;
use App\Models\BrandBillDispute;
use App\Models\BillLineItem;
use App\Models\BillPeriod;
use App\Models\Brand;
use App\Models\CommonSpaceLog;
use App\Models\Store;
use App\Models\StoreBrandBill;
use App\Jobs\GenerateBillsJob;
use App\Services\BillGenerationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Mainul\CustomHelperFunctions\Helpers\CustomHelper;
use Throwable;

class BillingController extends Controller
{
    public function __construct(private readonly BillGenerationService $billService) {}

    public function index(): View
    {
        $periods = BillPeriod::withCount('storeBrandBills')
            ->latest()
            ->paginate(15);

        return view('backend.billing.periods.index', compact('periods'));
    }

    public function create(): View
    {
        return view('backend.billing.periods.create');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:100'],
            'period_type'  => ['required', 'in:monthly,quarterly,custom'],
            'period_start' => ['required', 'date'],
            'period_end'   => ['required', 'date', 'after_or_equal:period_start'],
        ]);

        try {
            $period = DB::transaction(function () use ($validated) {
                return BillPeriod::create([
                    ...$validated,
                    'status'     => 'open',
                    'created_by' => auth()->id(),
                ]);
            });

            activity('billing')
                ->performedOn($period)
                ->causedBy(auth()->user())
                ->event('bill_period_created')
                ->log("Bill period '{$period->name}' created.");

            return response()->json([
                'success' => true,
                'message' => "Bill period '{$period->name}' created successfully.",
                'data'    => ['id' => $period->id, 'redirect' => route('billing.periods.show', $period)],
            ]);
        } catch (Throwable $e) {
            report($e);
            return response()->json(['success' => false, 'message' => 'Failed to create billing period.'], 500);
        }
    }

    public function show(BillPeriod $period): View
    {
        $brandId = request('brand_id');
        $storeId = request('store_id');
        $groupBy = request('group_by', 'brand'); // 'store' | 'brand'

        $bills = StoreBrandBill::query()
            ->where('bill_period_id', $period->id)
            ->when($brandId, fn ($q) => $q->where('brand_id', $brandId))
            ->when($storeId, fn ($q) => $q->where('store_id', $storeId))
            ->with(['store:id,title,code', 'brand:id,name,code'])
            ->when($groupBy === 'brand',
                fn ($q) => $q->orderBy('brand_id')->orderBy('store_id'),
                fn ($q) => $q->orderBy('store_id')->orderBy('brand_id'),
            )
            ->paginate(20)
            ->withQueryString();

        $commonLogs = CommonSpaceLog::where('bill_period_id', $period->id)
            ->with('store:id,title,code')
            ->get();

        $summaryBase = StoreBrandBill::where('bill_period_id', $period->id)
            ->when($brandId, fn ($q) => $q->where('brand_id', $brandId))
            ->when($storeId, fn ($q) => $q->where('store_id', $storeId));

        $summary = [
            'total_bills'     => (clone $summaryBase)->count(),
            'total_amount'    => (clone $summaryBase)->sum('final_amount'),
            'draft_count'     => (clone $summaryBase)->where('bill_status', 'draft')->count(),
            'issued_count'    => (clone $summaryBase)->where('bill_status', 'issued')->count(),
            'disputed_count'  => (clone $summaryBase)->where('bill_status', 'disputed')->count(),
            'finalized_count' => (clone $summaryBase)->where('bill_status', 'finalized')->count(),
            'issuable_count'  => (clone $summaryBase)->whereIn('bill_status', ['draft', 'adjusted'])->count(),
        ];

        // distinct brands & stores that have bills in this period (for filter dropdowns)
        $periodBillIds = StoreBrandBill::where('bill_period_id', $period->id);
        $filterBrands  = Brand::whereIn('id', (clone $periodBillIds)->pluck('brand_id'))
            ->orderBy('name')->get(['id', 'name', 'code']);
        $filterStores  = Store::whereIn('id', (clone $periodBillIds)->pluck('store_id'))
            ->orderBy('title')->get(['id', 'title', 'code']);

        // brand IDs that have a pending brand-level dispute in this period
        $brandPendingDisputeIds = BrandBillDispute::where('bill_period_id', $period->id)
            ->where('status', 'pending')
            ->pluck('brand_id')
            ->toArray();

        return view('backend.billing.periods.show',
            compact('period', 'bills', 'commonLogs', 'summary', 'filterBrands', 'filterStores', 'groupBy', 'brandPendingDisputeIds'));
    }

    public function issueAllBills(BillPeriod $period): JsonResponse
    {
        if ($period->isFinalized()) {
            return response()->json(['success' => false, 'message' => 'Period is finalized.'], 422);
        }

        try {
            $count = StoreBrandBill::where('bill_period_id', $period->id)
                ->whereIn('bill_status', ['draft', 'adjusted'])
                ->update(['bill_status' => 'issued', 'issued_at' => now()]);

            activity('billing')
                ->performedOn($period)
                ->causedBy(auth()->user())
                ->event('bills_issued_bulk')
                ->withProperties(['count' => $count])
                ->log("Bulk issued {$count} bills for period '{$period->name}'.");

            return response()->json([
                'success' => true,
                'message' => "{$count} bill(s) issued successfully.",
                'count'   => $count,
            ]);
        } catch (Throwable $e) {
            report($e);
            return response()->json(['success' => false, 'message' => 'Failed to issue bills.'], 500);
        }
    }

    public function issueBrandBills(BillPeriod $period, Brand $brand): JsonResponse
    {
        if ($period->isFinalized()) {
            return response()->json(['success' => false, 'message' => 'Period is finalized.'], 422);
        }

        try {
            $count = StoreBrandBill::where('bill_period_id', $period->id)
                ->where('brand_id', $brand->id)
                ->whereIn('bill_status', ['draft', 'adjusted'])
                ->update(['bill_status' => 'issued', 'issued_at' => now()]);

            activity('billing')
                ->performedOn($period)
                ->causedBy(auth()->user())
                ->event('bills_issued_brand_bulk')
                ->withProperties(['brand_id' => $brand->id, 'count' => $count])
                ->log("Bulk issued {$count} bills for brand '{$brand->name}' in period '{$period->name}'.");

            return response()->json([
                'success' => true,
                'message' => "{$count} bill(s) issued for {$brand->name}.",
                'count'   => $count,
            ]);
        } catch (Throwable $e) {
            report($e);
            return response()->json(['success' => false, 'message' => 'Failed to issue bills.'], 500);
        }
    }

    public function finalizeBrandBills(BillPeriod $period, Brand $brand): JsonResponse
    {
        if ($period->isFinalized()) {
            return response()->json(['success' => false, 'message' => 'Period is already finalized.'], 422);
        }

        $pendingCount = BillDispute::whereHas('storeBrandBill', function ($q) use ($period, $brand) {
            $q->where('bill_period_id', $period->id)
              ->where('brand_id', $brand->id);
        })->where('status', 'pending')->count();

        if ($pendingCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "Cannot finalize: {$pendingCount} pending bill dispute(s) for \"{$brand->name}\" must be resolved first.",
            ], 422);
        }

        $pendingBrandDispute = BrandBillDispute::where('bill_period_id', $period->id)
            ->where('brand_id', $brand->id)
            ->where('status', 'pending')
            ->exists();

        if ($pendingBrandDispute) {
            return response()->json([
                'success' => false,
                'message' => "Cannot finalize: a pending brand dispute for \"{$brand->name}\" must be resolved first.",
            ], 422);
        }

        try {
            $count = StoreBrandBill::where('bill_period_id', $period->id)
                ->where('brand_id', $brand->id)
                ->where('bill_status', 'issued')
                ->update(['bill_status' => 'finalized', 'finalized_at' => now(), 'finalized_by' => auth()->id()]);

            activity('billing')->performedOn($period)->causedBy(auth()->user())
                ->event('bills_finalized_brand_bulk')
                ->withProperties(['brand_id' => $brand->id, 'count' => $count])
                ->log("Bulk finalized {$count} bills for brand '{$brand->name}' in period '{$period->name}'.");

            return response()->json([
                'success' => true,
                'message' => "{$count} bill(s) finalized for {$brand->name}.",
                'count'   => $count,
            ]);
        } catch (Throwable $e) {
            report($e);
            return response()->json(['success' => false, 'message' => 'Failed to finalize bills.'], 500);
        }
    }

    public function brandInvoiceView(BillPeriod $period, Brand $brand): View
    {
        $bills = StoreBrandBill::query()
            ->where('bill_period_id', $period->id)
            ->where('brand_id', $brand->id)
            ->with('store:id,title,code,district_id,division_id')
            ->orderBy('store_id')
            ->get();

        return view('backend.billing.bills.brand-invoice', compact('period', 'brand', 'bills'));
    }

    public function generate(BillPeriod $period): JsonResponse
    {
        if ($period->isFinalized()) {
            return response()->json(['success' => false, 'message' => 'Finalized periods cannot be regenerated.'], 422);
        }

        if ($period->status === 'generating') {
            return response()->json(['success' => false, 'message' => 'Bill generation is already in progress.'], 422);
        }

        // Mark generating before dispatching so the polling endpoint reflects it immediately
        $period->update(['status' => 'generating']);

        GenerateBillsJob::dispatch($period, auth()->id());

        // If no persistent queue:work daemon is running, start one as a non-blocking background process
        if (!$this->isQueueWorkerRunning()) {
            $this->startQueueWorkerBackground();
        }

        // Always return immediately — client polls /status until done
        return response()->json([
            'success' => true,
            'queued'  => true,
            'message' => 'Bill generation started in the background.',
        ]);
    }

    private function isQueueWorkerRunning(): bool
    {
        if (!function_exists('shell_exec')) {
            return false;
        }

        $disabled = array_map('trim', explode(',', (string) ini_get('disable_functions')));
        if (in_array('shell_exec', $disabled, true)) {
            return false;
        }

        if (PHP_OS_FAMILY === 'Windows') {
            $output = @shell_exec('wmic process get commandline 2>nul');
        } else {
            $output = @shell_exec('pgrep -f "queue:work" 2>/dev/null');
            if (empty(trim((string) $output))) {
                $output = @shell_exec('ps aux 2>/dev/null');
            }
        }

        return !empty($output) && str_contains((string) $output, 'queue:work');
    }

    private function startQueueWorkerBackground(): void
    {
        $php     = PHP_BINARY;
        $artisan = base_path('artisan');

        if (PHP_OS_FAMILY === 'Windows') {
            // start /B spawns a detached background process — popen/pclose returns immediately
            pclose(popen("start /B \"\" \"{$php}\" \"{$artisan}\" queue:work --stop-when-empty --tries=1 --timeout=300 2>nul", 'r'));
        } else {
            exec("\"{$php}\" \"{$artisan}\" queue:work --stop-when-empty --tries=1 --timeout=300 > /dev/null 2>&1 &");
        }
    }

    public function periodStatus(BillPeriod $period): JsonResponse
    {
        $period->refresh();

        return response()->json([
            'status' => $period->status,
            'done'   => $period->status !== 'generating',
        ]);
    }

    public function finalizePeriod(BillPeriod $period): JsonResponse
    {
        if ($period->isFinalized()) {
            return response()->json(['success' => false, 'message' => 'Period is already finalized.'], 422);
        }

        if (!$period->isGenerated()) {
            return response()->json(['success' => false, 'message' => 'Generate bills before finalizing the period.'], 422);
        }

        try {
            $period->update([
                'status'       => 'finalized',
                'finalized_at' => now(),
            ]);

            activity('billing')
                ->performedOn($period)
                ->causedBy(auth()->user())
                ->event('period_finalized')
                ->log("Bill period '{$period->name}' finalized.");

            return response()->json(['success' => true, 'message' => "Period '{$period->name}' finalized."]);
        } catch (Throwable $e) {
            report($e);
            return response()->json(['success' => false, 'message' => 'Failed to finalize period.'], 500);
        }
    }

    public function showBill(StoreBrandBill $bill): View
    {
        $bill->load([
            'billPeriod',
            'store',
            'brand',
            'lineItems.asset',
            'lineItems.assetType',
            'disputes.requestedBy',
            'disputes.reviewedBy',
            'finalizedBy',
        ]);

        $commonLog = CommonSpaceLog::where('bill_period_id', $bill->bill_period_id)
            ->where('store_id', $bill->store_id)
            ->first();

        return view('backend.billing.bills.show', compact('bill', 'commonLog'));
    }

    public function issueBill(StoreBrandBill $bill): JsonResponse
    {
        if (!in_array($bill->bill_status, ['draft', 'adjusted'], true)) {
            return response()->json(['success' => false, 'message' => 'Only draft or adjusted bills can be issued.'], 422);
        }

        try {
            $bill->update(['bill_status' => 'issued', 'issued_at' => now()]);

            activity('billing')
                ->performedOn($bill)
                ->causedBy(auth()->user())
                ->event('bill_issued')
                ->log("Bill #{$bill->id} issued to brand.");

            return response()->json(['success' => true, 'message' => 'Bill issued successfully.']);
        } catch (Throwable $e) {
            report($e);
            return response()->json(['success' => false, 'message' => 'Failed to issue bill.'], 500);
        }
    }

    public function adjustBill(Request $request, StoreBrandBill $bill): JsonResponse
    {
        if ($bill->bill_status === 'finalized' || $bill->bill_status === 'paid') {
            return response()->json(['success' => false, 'message' => 'Finalized or paid bills cannot be adjusted.'], 422);
        }

        $validated = $request->validate([
            'adjustment_amount' => ['required', 'numeric'],
            'admin_note'        => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            DB::transaction(function () use ($bill, $validated) {
                $bill->update([
                    'adjustment_amount' => $validated['adjustment_amount'],
                    'final_amount'      => $bill->subtotal + $validated['adjustment_amount'],
                    'admin_note'        => $validated['admin_note'] ?? $bill->admin_note,
                    'bill_status'       => 'adjusted',
                ]);
            });

            activity('billing')
                ->performedOn($bill)
                ->causedBy(auth()->user())
                ->event('bill_adjusted')
                ->withProperties(['adjustment' => $validated['adjustment_amount']])
                ->log("Bill #{$bill->id} adjusted by admin.");

            return response()->json([
                'success'      => true,
                'message'      => 'Bill adjusted successfully.',
                'final_amount' => number_format((float) $bill->fresh()->final_amount, 2),
            ]);
        } catch (Throwable $e) {
            report($e);
            return response()->json(['success' => false, 'message' => 'Failed to adjust bill.'], 500);
        }
    }

    public function finalizeBill(StoreBrandBill $bill): JsonResponse
    {
        if ($bill->bill_status === 'finalized') {
            return response()->json(['success' => false, 'message' => 'Bill is already finalized.'], 422);
        }

        if ($bill->hasPendingDispute()) {
            return response()->json(['success' => false, 'message' => 'Resolve pending disputes before finalizing.'], 422);
        }

        try {
            $bill->update([
                'bill_status'  => 'finalized',
                'finalized_at' => now(),
                'finalized_by' => auth()->id(),
            ]);

            activity('billing')
                ->performedOn($bill)
                ->causedBy(auth()->user())
                ->event('bill_finalized')
                ->log("Bill #{$bill->id} finalized.");

            return response()->json(['success' => true, 'message' => 'Bill finalized successfully.']);
        } catch (Throwable $e) {
            report($e);
            return response()->json(['success' => false, 'message' => 'Failed to finalize bill.'], 500);
        }
    }

    public function markPaid(StoreBrandBill $bill): JsonResponse
    {
        if ($bill->bill_status !== 'finalized') {
            return response()->json(['success' => false, 'message' => 'Only finalized bills can be marked as paid.'], 422);
        }

        try {
            $bill->update(['bill_status' => 'paid']);

            activity('billing')
                ->performedOn($bill)
                ->causedBy(auth()->user())
                ->event('bill_paid')
                ->log("Bill #{$bill->id} marked as paid.");

            return response()->json(['success' => true, 'message' => 'Bill marked as paid.']);
        } catch (Throwable $e) {
            report($e);
            return response()->json(['success' => false, 'message' => 'Failed to mark bill as paid.'], 500);
        }
    }

    public function invoiceView(StoreBrandBill $bill): View
    {
        $bill->load([
            'billPeriod',
            'store.division',
            'store.district',
            'brand',
            'lineItems.asset',
            'lineItems.assetType',
        ]);

        $commonLog = CommonSpaceLog::where('bill_period_id', $bill->bill_period_id)
            ->where('store_id', $bill->store_id)
            ->first();

        return view('backend.billing.bills.invoice', compact('bill', 'commonLog'));
    }

    public function overrideLineItem(Request $request, BillLineItem $lineItem): JsonResponse
    {
        $validated = $request->validate([
            'override_amount' => [
                'required', 'numeric', 'min:0',
                function ($attr, $value, $fail) use ($lineItem) {
                    if ((float) $value === (float) $lineItem->calculated_amount) {
                        $fail('Override amount must differ from the calculated amount.');
                    }
                },
            ],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            DB::transaction(function () use ($lineItem, $validated) {
                $overrideAmount = $validated['override_amount'];

                $lineItem->update([
                    'override_amount' => $overrideAmount,
                    'override_type'   => $overrideAmount < $lineItem->calculated_amount ? 'discount' : 'extra', // equal case blocked by validation above
                    'final_amount'    => $overrideAmount,
                    'note'            => $validated['note'] ?? null,
                ]);

                $lineItem->storeBrandBill->recalculateTotals();
            });

            return response()->json(['success' => true, 'message' => 'Line item overridden successfully.']);
        } catch (Throwable $e) {
            report($e);
            return response()->json(['success' => false, 'message' => 'Failed to override line item.'], 500);
        }
    }
}
