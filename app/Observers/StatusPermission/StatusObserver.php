<?php

namespace App\Observers\StatusPermission;

use App\Http\Controllers\Backend\StatusPermission\StatusPermissionController;
use App\Models\StatusPermission\Status;
use Illuminate\Support\Str;
use Uzzal\Acl\Models\Resource;

class StatusObserver
{
    /**
     * Auto-create an ACL resource when a new status is created.
     * This makes it appear in the ACL role/permission management UI.
     */
    public function created(Status $status): void
    {
        Resource::firstOrCreate(
            [
                'controller' => StatusPermissionController::class,
                'action'     => $status->aclAction(),
            ],
            [
                'resource_id' => (string) Str::uuid(),
                'name'        => 'Change status to ' . $status->label,
                'controller'  => StatusPermissionController::class,
                'action'      => $status->aclAction(),
            ]
        );
    }

    /**
     * Sync label changes to the ACL resource name.
     */
    public function updated(Status $status): void
    {
        if ($status->isDirty('label')) {
            Resource::where('controller', StatusPermissionController::class)
                ->where('action', $status->aclAction())
                ->update(['name' => 'Change status to ' . $status->label]);
        }
    }

    /**
     * Remove the ACL resource when a status is deleted.
     */
    public function deleted(Status $status): void
    {
        Resource::where('controller', StatusPermissionController::class)
            ->where('action', $status->aclAction())
            ->delete();
    }
}
