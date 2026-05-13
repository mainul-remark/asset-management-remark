<?php

namespace App\Http\Controllers\Backend\Billing;

use App\Http\Controllers\Controller;
use App\Models\BillPeriod;
use App\Models\Brand;
use App\Models\BrandBillDispute;
use App\Models\StoreBrandBill;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class BrandBillDisputeController extends Controller
{
    public function store(Request $request, BillPeriod $period, Brand $brand): JsonResponse
    {
        $issuedCount = StoreBrandBill::where('bill_period_id', $period->id)
            ->where('brand_id', $brand->id)
            ->where('bill_status', 'issued')
            ->count();

        if ($issuedCount === 0) {
            return response()->json(['success' => false, 'message' => 'No issued bills found for this brand in this period.'], 422);
        }

        if (BrandBillDispute::where('bill_period_id', $period->id)
            ->where('brand_id', $brand->id)
            ->where('status', 'pending')
            ->exists()) {
            return response()->json(['success' => false, 'message' => 'A pending brand dispute already exists for this brand.'], 422);
        }

        $validated = $request->validate([
            'requested_amount' => ['required', 'numeric', 'min:0'],
            'reason'           => ['required', 'string', 'max:2000'],
        ]);

        try {
            $originalAmount = StoreBrandBill::where('bill_period_id', $period->id)
                ->where('brand_id', $brand->id)
                ->sum('final_amount');

            $dispute = BrandBillDispute::create([
                'bill_period_id'   => $period->id,
                'brand_id'         => $brand->id,
                'requested_by'     => auth()->id(),
                'original_amount'  => $originalAmount,
                'requested_amount' => $validated['requested_amount'],
                'reason'           => $validated['reason'],
                'status'           => 'pending',
            ]);

            activity('billing')
                ->performedOn($dispute)
                ->causedBy(auth()->user())
                ->event('brand_dispute_raised')
                ->log("Brand dispute raised for brand '{$brand->name}' in period '{$period->name}'.");

            return response()->json(['success' => true, 'message' => 'Brand dispute submitted successfully.']);
        } catch (Throwable $e) {
            report($e);
            return response()->json(['success' => false, 'message' => 'Failed to submit brand dispute.'], 500);
        }
    }

    public function show(BrandBillDispute $dispute): View
    {
        $dispute->load(['billPeriod', 'brand', 'requestedBy', 'reviewedBy']);

        $bills = StoreBrandBill::where('bill_period_id', $dispute->bill_period_id)
            ->where('brand_id', $dispute->brand_id)
            ->with('store:id,title,code')
            ->get();

        return view('backend.billing.brand-disputes.show', compact('dispute', 'bills'));
    }

    public function approve(Request $request, BrandBillDispute $dispute): JsonResponse
    {
        return $this->reviewDispute($request, $dispute, 'approved');
    }

    public function partialApprove(Request $request, BrandBillDispute $dispute): JsonResponse
    {
        return $this->reviewDispute($request, $dispute, 'partially_approved');
    }

    public function reject(Request $request, BrandBillDispute $dispute): JsonResponse
    {
        return $this->reviewDispute($request, $dispute, 'rejected');
    }

    private function reviewDispute(Request $request, BrandBillDispute $dispute, string $decision): JsonResponse
    {
        if ($dispute->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'This dispute has already been reviewed.'], 422);
        }

        $rules = ['admin_response' => ['required', 'string', 'max:2000']];
        if ($decision === 'partially_approved') {
            $rules['approved_amount'] = ['required', 'numeric', 'min:0'];
        }

        $validated = $request->validate($rules);

        try {
            DB::transaction(function () use ($dispute, $validated, $decision) {
                $approvedAmount = match ($decision) {
                    'approved'           => (float) $dispute->requested_amount,
                    'partially_approved' => (float) $validated['approved_amount'],
                    'rejected'           => (float) $dispute->original_amount,
                };

                $dispute->update([
                    'status'          => $decision,
                    'admin_response'  => $validated['admin_response'],
                    'approved_amount' => $approvedAmount,
                    'reviewed_by'     => auth()->id(),
                    'reviewed_at'     => now(),
                ]);

                if ($decision !== 'rejected') {
                    $bills         = StoreBrandBill::where('bill_period_id', $dispute->bill_period_id)
                        ->where('brand_id', $dispute->brand_id)
                        ->get();
                    $totalOriginal = (float) $bills->sum('final_amount');

                    foreach ($bills as $bill) {
                        $ratio        = $totalOriginal > 0
                            ? (float) $bill->final_amount / $totalOriginal
                            : 1.0 / $bills->count();
                        $billApproved = round($approvedAmount * $ratio, 2);
                        $adjustment   = $billApproved - (float) $bill->subtotal;

                        $bill->update([
                            'adjustment_amount' => $adjustment,
                            'final_amount'      => $billApproved,
                            'admin_note'        => $validated['admin_response'],
                            'bill_status'       => 'adjusted',
                        ]);
                    }
                }
            });

            $label = match ($decision) {
                'approved'           => 'approved',
                'partially_approved' => 'partially approved',
                'rejected'           => 'rejected',
            };

            activity('billing')
                ->performedOn($dispute)
                ->causedBy(auth()->user())
                ->event("brand_dispute_{$decision}")
                ->log("Brand dispute #{$dispute->id} {$label} by admin.");

            return response()->json(['success' => true, 'message' => "Brand dispute {$label} successfully."]);
        } catch (Throwable $e) {
            report($e);
            return response()->json(['success' => false, 'message' => 'Failed to review dispute.'], 500);
        }
    }
}
