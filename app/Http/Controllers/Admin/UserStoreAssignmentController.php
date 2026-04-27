<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserStoreAssignmentRequest;
use App\Models\Role;
use App\Models\Store;
use App\Models\User;
use App\Models\UserStoreAssignment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class UserStoreAssignmentController extends Controller
{
    public function index(Request $request)
    {
        return view('backend.user-management.store-assignment.index', [
            'users' => User::query()
                ->orderBy('name')
                ->get(['id', 'name', 'email']),
            'stores' => Store::query()
                ->where('status', 1)
                ->whereNull('deleted_at')
                ->orderBy('title')
                ->get(['id', 'title', 'code']),
            'roles' => Role::query()
                ->where('role_id', '!=', 1)
                ->orderBy('name')
                ->get(['role_id', 'name']),
        ]);
    }

    public function datatable(Request $request): JsonResponse
    {
        $status = $request->input('status');
        $search = trim((string) $request->input('search_text', ''));

        $query = User::query()
            ->select(['users.id', 'users.name', 'users.email', 'users.employee_id'])
            ->with([
                'userStoreAssignments' => fn ($assignmentQuery) => $assignmentQuery
                    ->with([
                        'store:id,title,code',
                        'role:role_id,name',
                        'assignedBy:id,name',
                    ])
                    ->orderBy('store_id'),
            ])
            ->whereHas('userStoreAssignments');

        if ($status !== null && $status !== '') {
            $query->whereHas('userStoreAssignments', fn ($assignmentQuery) => $assignmentQuery->where('status', (int) $status));
        }

        if ($search !== '') {
            $query->where(function ($nestedQuery) use ($search) {
                $nestedQuery
                    ->where('users.name', 'like', '%' . $search . '%')
                    ->orWhere('users.email', 'like', '%' . $search . '%')
                    ->orWhere('users.employee_id', 'like', '%' . $search . '%')
                    ->orWhereHas('userStoreAssignments.store', function ($storeQuery) use ($search) {
                        $storeQuery
                            ->where('title', 'like', '%' . $search . '%')
                            ->orWhere('code', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('userStoreAssignments', function ($assignmentQuery) use ($search) {
                        $assignmentQuery->where('employee_id', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('userStoreAssignments.role', function ($roleQuery) use ($search) {
                        $roleQuery->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        $groupCache = [];
        $resolveGroup = function (User $user) use (&$groupCache) {
            return $groupCache[$user->id] ??= UserStoreAssignment::transformAssignmentGroup($user);
        };

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('user_display', function (User $user) use ($resolveGroup) {
                $group = $resolveGroup($user);
                $name = e($group['user']['name'] ?? 'N/A');
                $email = e($group['user']['email'] ?? 'N/A');

                return <<<HTML
<div class="assignment-cell">
    <div class="primary-line">{$name}</div>
    <div class="secondary-line">{$email}</div>
</div>
HTML;
            })
            ->addColumn('role_display', function (User $user) use ($resolveGroup) {
                $group = $resolveGroup($user);

                if (! empty($group['role']['name'])) {
                    return '<span class="badge bg-info-transparent text-info">' . e($group['role']['name']) . '</span>';
                }

                return '<span class="text-muted">No role</span>';
            })
            ->addColumn('stores_display', function (User $user) use ($resolveGroup) {
                $group = $resolveGroup($user);
                $chips = collect($group['stores'] ?? [])->map(function (array $store) {
                    $title = e($store['title'] ?? '');
                    $code = ! empty($store['code'])
                        ? '<small class="text-muted ms-1">' . e($store['code']) . '</small>'
                        : '';

                    return '<span class="badge bg-light text-dark border store-chip">' . $title . $code . '</span>';
                })->implode(' ');

                $storeCount = (int) ($group['store_count'] ?? 0);

                return <<<HTML
<div class="d-flex flex-wrap gap-2">{$chips}</div>
<div class="secondary-line mt-2">{$storeCount} store(s)</div>
HTML;
            })
            ->addColumn('status_display', function (User $user) use ($resolveGroup) {
                $group = $resolveGroup($user);
                $isActive = (int) ($group['status'] ?? 0) === 1;
                $classes = $isActive
                    ? 'bg-success-transparent text-success'
                    : 'bg-danger-transparent text-danger';
                $label = e($group['status_label'] ?? 'Inactive');

                return '<span class="badge ' . $classes . '">' . $label . '</span>';
            })
            ->addColumn('assigned_info', function (User $user) use ($resolveGroup) {
                $group = $resolveGroup($user);
                $assignedAt = e($group['assigned_at'] ?? 'N/A');
                $assignedBy = e($group['assigned_by']['name'] ?? 'System');

                return <<<HTML
<div class="assignment-cell">
    <div class="primary-line">{$assignedAt}</div>
    <div class="secondary-line">{$assignedBy}</div>
</div>
HTML;
            })
            ->addColumn('actions', function (User $user) use ($resolveGroup) {
                $group = $resolveGroup($user);
                $id = (int) ($group['id'] ?? 0);
                $userName = e($group['user']['name'] ?? 'Unknown');
                $storeCount = (int) ($group['store_count'] ?? 0);

                return <<<HTML
<div class="d-flex gap-2">
    <button
        type="button"
        class="btn btn-sm btn-primary-light btn-wave btn-edit-assignment"
        data-id="{$id}"
        title="Edit"
    >
        <i class="ri-edit-line"></i>
    </button>
    <button
        type="button"
        class="btn btn-sm btn-danger-light btn-wave btn-delete-assignment"
        data-id="{$id}"
        data-user-name="{$userName}"
        data-store-count="{$storeCount}"
        title="Delete"
    >
        <i class="ri-delete-bin-line"></i>
    </button>
</div>
HTML;
            })
            ->orderColumn('user_display', 'users.name $1')
            ->orderColumn('role_display', 'users.id $1')
            ->orderColumn('stores_display', 'users.id $1')
            ->orderColumn('status_display', 'users.id $1')
            ->orderColumn('assigned_info', 'users.id $1')
            ->rawColumns([
                'user_display',
                'role_display',
                'stores_display',
                'status_display',
                'assigned_info',
                'actions',
            ])
            ->toJson();
    }

    public function create()
    {
        return redirect()->route('user-store-assignments.index');
    }

    public function searchUsers(Request $request): JsonResponse
    {
        $search = trim((string) $request->input('q', ''));

        $users = User::query()
            ->select('id', 'name', 'email', 'employee_id')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($nestedQuery) use ($search) {
                    $nestedQuery
                        ->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('employee_id', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('name')
            ->limit(10)
            ->get();

        return response()->json([
            'data' => $users->map(function (User $user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'employee_id' => $user->employee_id,
                ];
            })->values(),
        ]);
    }

    public function currentByUser(User $user): JsonResponse
    {
        $assignment = UserStoreAssignment::query()
            ->where('user_id', $user->id)
            ->latest('id')
            ->first();

        if (! $assignment) {
            return response()->json([
                'exists' => false,
                'data' => null,
            ]);
        }

        return response()->json([
            'exists' => true,
            'data' => UserStoreAssignment::loadAssignmentGroupByUserId((int) $user->id),
        ]);
    }

    public function store(UserStoreAssignmentRequest $request): JsonResponse
    {
        try {
            $group = DB::transaction(function () use ($request) {
                return UserStoreAssignment::createAssignmentGroup(
                    userId: (int) $request->integer('user_id'),
                    storeIds: collect($request->input('store_ids', []))->map(fn ($id) => (int) $id),
                    roleId: $request->filled('role_id') ? (int) $request->input('role_id') : null,
                    status: (int) $request->input('status')
                );
            });

            return response()->json([
                'success' => true,
                'message' => 'User store assignment created successfully.',
                'data' => $group,
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create user store assignment.',
            ], 500);
        }
    }

    public function show(UserStoreAssignment $userStoreAssignment): JsonResponse
    {
        return response()->json(
            UserStoreAssignment::loadAssignmentGroupByUserId((int) $userStoreAssignment->user_id)
        );
    }

    public function edit(UserStoreAssignment $userStoreAssignment): JsonResponse
    {
        return response()->json(
            UserStoreAssignment::loadAssignmentGroupByUserId((int) $userStoreAssignment->user_id)
        );
    }

    public function update(UserStoreAssignmentRequest $request, UserStoreAssignment $userStoreAssignment): JsonResponse
    {
        try {
            $group = DB::transaction(function () use ($request, $userStoreAssignment) {
                return UserStoreAssignment::replaceAssignmentGroupForUser(
                    userId: (int) $userStoreAssignment->user_id,
                    storeIds: collect($request->input('store_ids', []))->map(fn ($id) => (int) $id),
                    roleId: $request->filled('role_id') ? (int) $request->input('role_id') : null,
                    status: (int) $request->input('status')
                );
            });

            return response()->json([
                'success' => true,
                'message' => 'User store assignment updated successfully.',
                'data' => $group,
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update user store assignment.',
            ], 500);
        }
    }

    public function destroy(UserStoreAssignment $userStoreAssignment): JsonResponse
    {
        try {
            $deletedCount = DB::transaction(function () use ($userStoreAssignment) {
                return UserStoreAssignment::deleteAssignmentGroupForUser((int) $userStoreAssignment->user_id);
            });

            return response()->json([
                'success' => true,
                'message' => $deletedCount > 1
                    ? "Removed {$deletedCount} store assignments for the selected user."
                    : 'User store assignment deleted successfully.',
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user store assignment.',
            ], 500);
        }
    }
}
