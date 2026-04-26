<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Mainul\CustomHelperFunctions\Helpers\CustomHelper;

class UserStoreAssignment extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'store_id',
        'user_id',
        'role_id',
        'employee_id',
        'status',
        'assigned_by',
        'assigned_at',
    ];

    protected $searchableFields = ['*'];

    protected $table = 'user_store_assignments';

    protected $casts = [
        'assigned_at' => 'datetime',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public static function groupedListing(string $search = '', mixed $status = null, int $perPage = 12): LengthAwarePaginator
    {
        $assignmentUsers = User::query()
            ->with([
                'userStoreAssignments' => fn ($query) => $query
                    ->with([
                        'store:id,title,code',
                        'role:role_id,name',
                        'assignedBy:id,name',
                    ])
                    ->orderBy('store_id'),
            ])
            ->whereHas('userStoreAssignments', function ($query) use ($status) {
                if ($status !== null && $status !== '') {
                    $query->where('status', (int) $status);
                }
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($nestedQuery) use ($search) {
                    $nestedQuery
                        ->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhereHas('userStoreAssignments.store', function ($storeQuery) use ($search) {
                            $storeQuery
                                ->where('title', 'like', '%' . $search . '%')
                                ->orWhere('code', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('userStoreAssignments.role', function ($roleQuery) use ($search) {
                            $roleQuery->where('name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();

        return $assignmentUsers->through(
            fn (User $user) => static::transformAssignmentGroup($user)
        );
    }

    public static function createAssignmentGroup(int $userId, Collection $storeIds, ?int $roleId, int $status): array
    {
        static::persistAssignments($userId, $storeIds, $roleId, $status);

        return static::loadAssignmentGroupByUserId($userId);
    }

    public static function replaceAssignmentGroupForUser(int $userId, Collection $storeIds, ?int $roleId, int $status): array
    {
        static::query()->where('user_id', $userId)->delete();
        static::persistAssignments($userId, $storeIds, $roleId, $status);

        return static::loadAssignmentGroupByUserId($userId);
    }

    public static function deleteAssignmentGroupForUser(int $userId): int
    {
        return static::query()->where('user_id', $userId)->delete();
    }

    public static function loadAssignmentGroupByUserId(int $userId): array
    {
        $user = User::query()
            ->with([
                'userStoreAssignments' => fn ($query) => $query
                    ->with([
                        'store:id,title,code',
                        'role:role_id,name',
                        'assignedBy:id,name',
                    ])
                    ->orderBy('store_id'),
            ])
            ->findOrFail($userId);

        return static::transformAssignmentGroup($user);
    }

    public static function transformAssignmentGroup(User $user): array
    {
        $assignments = $user->userStoreAssignments
            ->sortBy(function (self $assignment) {
                return strtolower($assignment->store?->title ?? '');
            })
            ->values();

        /** @var self|null $latestAssignment */
        $latestAssignment = $assignments
            ->sortByDesc(fn (self $assignment) => optional($assignment->assigned_at)?->timestamp ?? 0)
            ->first() ?? $assignments->sortByDesc('id')->first();

        $stores = $assignments
            ->map(fn (self $assignment) => [
                'id' => $assignment->store?->id,
                'title' => $assignment->store?->title,
                'code' => $assignment->store?->code,
            ])
            ->filter(fn (array $store) => !empty($store['id']))
            ->values();

        return [
            'id' => $latestAssignment?->id,
            'assignment_ids' => $assignments->pluck('id')->map(fn ($id) => (int) $id)->values()->all(),
            'user_id' => (int) $user->id,
            'user' => [
                'id' => (int) $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'role_id' => $latestAssignment?->role_id ? (int) $latestAssignment->role_id : null,
            'role' => $latestAssignment?->role ? [
                'role_id' => (int) $latestAssignment->role->role_id,
                'name' => $latestAssignment->role->name,
            ] : null,
            'status' => $latestAssignment ? (int) $latestAssignment->status : 0,
            'status_label' => $latestAssignment && (int) $latestAssignment->status === 1 ? 'Active' : 'Inactive',
            'assigned_at' => optional($latestAssignment?->assigned_at)->format('Y-m-d h:i A'),
            'assigned_by' => $latestAssignment?->assignedBy ? [
                'id' => (int) $latestAssignment->assignedBy->id,
                'name' => $latestAssignment->assignedBy->name,
            ] : null,
            'store_count' => $stores->count(),
            'store_ids' => $stores->pluck('id')->map(fn ($id) => (int) $id)->values()->all(),
            'stores' => $stores->all(),
        ];
    }

    protected static function persistAssignments(int $userId, Collection $storeIds, ?int $roleId, int $status): void
    {
        $assignedBy = CustomHelper::loggedUser()?->id ?? auth()->id() ?? User::query()->value('id');
        $assignedAt = now();
        $employeeId = User::query()->find($userId)?->employee_id ?? '';

        foreach ($storeIds->unique()->values() as $storeId) {
            static::query()->create([
                'user_id' => $userId,
                'store_id' => $storeId,
                'role_id' => $roleId,
                'employee_id' => $employeeId,
                'status' => $status,
                'assigned_by' => $assignedBy,
                'assigned_at' => $assignedAt,
            ]);
        }
    }
}
