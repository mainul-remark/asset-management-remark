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

class UserStoreAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $status = $request->input('status');

        $assignmentGroups = UserStoreAssignment::groupedListing($search, $status, 12);

        return view('backend.user-management.store-assignment.index', [
            'assignmentGroups' => $assignmentGroups,
            'filters' => [
                'search' => $search,
                'status' => $status,
            ],
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
