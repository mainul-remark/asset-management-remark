<?php

namespace App\Http\Controllers\Backend\Billing;

use App\Http\Controllers\Controller;
use App\Models\BillDispute;
use App\Models\StoreBrandBill;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class BillDisputeController extends Controller
{
    public function index(): View
    {
        $disputes = BillDispute::query()
            ->with([
                'storeBrandBill.store:id,title,code',
                'storeBrandBill.brand:id,name,code',
                'storeBrandBill.billPeriod:id,name',
                'requestedBy:id,name',
                'reviewedBy:id,name',
            ])
            ->latest()
            ->paginate(20);

        $pendingCount = BillDispute::where('status', 'pending')->count();

        return view('backend.billing.disputes.index', compact('disputes', 'pendingCount'));
    }

    public function store(Request $request, StoreBrandBill $bill): JsonResponse
    {
        if (!in_array($bill->bill_status, ['issued', 'adjusted'], true)) {
            return response()->json(['success' => false, 'message' => 'Only issued or adjusted bills can be disputed.'], 422);
        }

        if ($bill->hasPendingDispute()) {
            return response()->json(['success' => false, 'message' => 'This bill already has a pending dispute.'], 422);
        }

        $validated = $request->validate([
            'requested_amount' => ['required', 'numeric', 'min:0'],
            'reason'           => ['required', 'string', 'max:2000'],
        ]);

        try {
            DB::transaction(function () use ($bill, $validated) {
                BillDispute::create([
                    'store_brand_bill_id' => $bill->id,
                    'requested_by'        => auth()->id(),
                    'original_amount'     => $bill->final_amount,
                    'requested_amount'    => $validated['requested_amount'],
                    'reason'              => $validated['reason'],
                    'status'              => 'pending',
                ]);

                $bill->update([
                    'bill_status'    => 'disputed',
                    'dispute_reason' => $validated['reason'],
                ]);
            });

            activity('billing')
                ->performedOn($bill)
                ->causedBy(auth()->user())
                ->event('bill_disputed')
                ->withProperties(['requested_amount' => $validated['requested_amount']])
                ->log("Dispute raised for bill #{$bill->id}.");

            return response()->json(['success' => true, 'message' => 'Dispute submitted successfully.']);
        } catch (Throwable $e) {
            report($e);
            return response()->json(['success' => false, 'message' => 'Failed to submit dispute.'], 500);
        }
    }

    public function show(BillDispute $dispute): View
    {
        $dispute->load([
            'storeBrandBill.store',
            'storeBrandBill.brand',
            'storeBrandBill.billPeriod',
            'storeBrandBill.lineItems.asset',
            'storeBrandBill.lineItems.assetType',
            'requestedBy',
            'reviewedBy',
        ]);

        return view('backend.billing.disputes.show', compact('dispute'));
    }

    public function approve(Request $request, BillDispute $dispute): JsonResponse
    {
        return $this->reviewDispute($request, $dispute, 'approved');
    }

    public function partialApprove(Request $request, BillDispute $dispute): JsonResponse
    {
        return $this->reviewDispute($request, $dispute, 'partially_approved');
    }

    public function reject(Request $request, BillDispute $dispute): JsonResponse
    {
        return $this->reviewDispute($request, $dispute, 'rejected');
    }

    private function reviewDispute(Request $request, BillDispute $dispute, string $decision): JsonResponse
    {
        if ($dispute->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'This dispute has already been reviewed.'], 422);
        }

        $rules = [
            'admin_response' => ['required', 'string', 'max:2000'],
        ];

        if ($decision === 'partially_approved') {
            $rules['approved_amount'] = ['required', 'numeric', 'min:0'];
        }

        $validated = $request->validate($rules);

        try {
            DB::transaction(function () use ($dispute, $validated, $decision) {
                $approvedAmount = match ($decision) {
                    'approved'           => $dispute->requested_amount,
                    'partially_approved' => $validated['approved_amount'],
                    'rejected'           => $dispute->original_amount,
                };

                $dispute->update([
                    'status'          => $decision,
                    'admin_response'  => $validated['admin_response'],
                    'approved_amount' => $approvedAmount,
                    'reviewed_by'     => auth()->id(),
                    'reviewed_at'     => now(),
                ]);

                $bill = $dispute->storeBrandBill;

                if ($decision !== 'rejected') {
                    $adjustment = $approvedAmount - $bill->subtotal;
                    $bill->update([
                        'adjustment_amount' => $adjustment,
                        'final_amount'      => $approvedAmount,
                        'admin_note'        => $validated['admin_response'],
                        'bill_status'       => 'adjusted',
                    ]);
                } else {
                    $bill->update(['bill_status' => 'issued']);
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
                ->event("dispute_{$decision}")
                ->log("Dispute #{$dispute->id} {$label} by admin.");

            return response()->json(['success' => true, 'message' => "Dispute {$label} successfully."]);
        } catch (Throwable $e) {
            report($e);
            return response()->json(['success' => false, 'message' => 'Failed to review dispute.'], 500);
        }
    }
}
