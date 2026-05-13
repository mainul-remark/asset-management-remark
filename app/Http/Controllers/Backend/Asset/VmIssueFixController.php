<?php

namespace App\Http\Controllers\Backend\Asset;

use App\DataTables\VmIssueFix\VmIssueFixDataTable;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VisualMerchandising;
use App\Models\VmIssueFix;
use App\Services\StatusPermission\StatusPermissionService;
use http\Env\Response;
use Illuminate\Http\Request;
use Mainul\CustomHelperFunctions\Helpers\CustomHelper;

class VmIssueFixController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('backend.asset-management.vm-issue-fix', [
            'users'       => User::latest()->get(),
            'permissions' => [
                'canView'         => allowed([self::class, 'show']),
                'canAssignUser'   => allowed([self::class, 'assignUser']),
                'canUploadProof'  => allowed([self::class, 'uploadProof']),
                'canChangeStatus' => allowed([self::class, 'changeFixStatus']),
            ],
        ]);
    }

    public function datatable(VmIssueFixDataTable $dataTable)
    {
        return $dataTable->withPermissions([
            'canView'         => allowed([self::class, 'show']),
            'canAssignUser'   => allowed([self::class, 'assignUser']),
            'canUploadProof'  => allowed([self::class, 'uploadProof']),
            'canChangeStatus' => allowed([self::class, 'changeFixStatus']),
        ])->ajax();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        $vm = VisualMerchandising::with([
            'store',
            'asset',
            'createdBy',
            'assignedBy',
            'assignedTo',
            'visualMerchandisingFiles',
        ])->findOrFail($id);

        return response()->json([
            'id'                => $vm->id,
            'store'             => $vm->store?->title,
            'asset'             => $vm->asset?->name,
            'issue_text'        => $vm->issue_text,
            'issue_fix_status'  => $vm->issue_fix_status,
            'fix_note'          => $vm->fix_note,
            'assigned_to'       => $vm->assignedTo?->name,
            'assigned_by'       => $vm->assignedBy?->name,
            'created_by'        => $vm->createdBy?->name,
            'created_at'        => $vm->created_at?->format('d M Y, h:i A'),
            'updated_at'        => $vm->updated_at?->format('d M Y, h:i A'),
            'issue_photos'      => $vm->visualMerchandisingFiles->map(fn ($f) => asset($f->file_path))->values(),
            'fix_proof'         => $vm->fix_proof ? collect(json_decode($vm->fix_proof))->map(fn ($p) => asset($p))->values() : [],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function assignUser(Request $request, $vmIssueFix)
    {
        try {
            $vm = VisualMerchandising::findOrFail($vmIssueFix);
            if (!$vm)
            {
//            return back()->withErrors('Vm Issue Not Found');
                return response()->json(['message' => 'Vm Issue Not Found', 'success' => false]);
            }
            $vm->update([
                'assigned_by'      => auth()->id(),
                'assigned_to'      => $request->assigned_to,
                'issue_fix_status' => 'assigned',
            ]);

            activity('workflow')
                ->performedOn($vm)
                ->causedBy(auth()->user())
                ->event('vm_issue_assigned')
                ->withProperties([
                    'assigned_by'      => auth()->id(),
                    'assigned_to'      => (int) $request->assigned_to,
                    'issue_fix_status' => 'assigned',
                ])
                ->log('Visual merchandising issue assigned to a user.');

            return response()->json(['success' => true, 'message' => 'Assigned User for this issue successfully',]);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'message' => 'something went wrong, Please try after sometime']);
        }
        return back()->with('success', 'Assigned User for this issue successfully');
    }

    public function uploadProof(Request $request, $vmIssueFix)
    {
        try {
            $vm = VisualMerchandising::findOrFail($vmIssueFix);
            if (!$vm)
                return response()->json(['message' => 'Vm Issue Not Found', 'success' => false]);
            if ($vm->fix_proof)
            {
                foreach (json_decode($vm->fix_proof) as $file)
                {
                    if (file_exists($file))

                        unlink($file);
                }
            }
            $fileString = [];
            foreach ($request->file('fix_proof') as $file) {
                array_push($fileString, CustomHelper::fileUpload($file, 'vm-fix-proof', 'vm-fix-proof', 600, 700, null));
            }
            $vm->update([
                'fix_proof'   => json_encode($fileString),
                'issue_fix_status'  => 'processing'
            ]);

            activity('workflow')
                ->performedOn($vm)
                ->causedBy(auth()->user())
                ->event('vm_issue_proof_uploaded')
                ->withProperties([
                    'proof_file_count' => count($fileString),
                    'issue_fix_status' => 'processing',
                ])
                ->log('Visual merchandising proof files uploaded.');

            return response()->json([
                'success'   => true,
                'message'   => 'Proof Files successfully uploaded',
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'success'   => false,
                'message'   => 'Something went wrong. Please try again',
            ]);
        }

    }

    public function changeFixStatus(Request $request, $vmId)
    {
        try {
            app(StatusPermissionService::class)->authorize($request->issue_fix_status);

            $vm = VisualMerchandising::findOrFail($vmId);
            if (!$vm)
//            return back()->withErrors('Vm Issue Not Found');
                return response()->json(['message' => 'Vm Issue Not Found', 'success' => false]);
            $oldStatus = $vm->issue_fix_status;
            $vm->update([
                'issue_fix_status'  => $request->issue_fix_status,
            ]);

            activity('workflow')
                ->performedOn($vm)
                ->causedBy(auth()->user())
                ->event('vm_issue_status_changed')
                ->withProperties([
                    'old_status' => $oldStatus,
                    'new_status' => $request->issue_fix_status,
                ])
                ->log('Visual merchandising issue status changed.');

            return response()->json([
                'success'   => true,
                'message'   => 'Work Status successfully updated',
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'success'   => false,
                'message'   => 'Something went wrong. Please try again',
            ]);
        }
    }
}
