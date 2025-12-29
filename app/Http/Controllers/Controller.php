<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

abstract class Controller
{
    protected function checkPermission(string $permission): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        
        if (!$user) {
            abort(403, 'Acesso negado. Deve estar autenticado.');
        }
        
        if ($user->hasRole('admin')) {
            return;
        }
        
        if (!$user->hasPermissionTo($permission)) {
            abort(403, 'Acesso negado. Não tem permissão para esta ação.');
        }
    }

    protected function checkAdmin(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        
        if (!$user || !$user->hasRole('admin')) {
            abort(403, 'Acesso negado. Apenas administradores podem aceder a esta área.');
        }
    }

    /**
     * Log an activity.
     *
     * @param Model|null $subject The model that was acted upon
     * @param string $action The action performed (created, updated, deleted, viewed)
     * @param string $menuName The menu/item name for the log
     * @param Request $request The request object
     * @param string|null $customDescription Custom description (optional)
     * @return void
     */
    protected function logActivity(?Model $subject, string $action, string $menuName, Request $request, ?string $customDescription = null): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user) {
            return;
        }

        $device = $request->userAgent();
        $ipAddress = $request->ip();

        $description = $customDescription ?? $this->buildDescription($subject, $action, $menuName);

        $activity = activity()
            ->causedBy($user)
            ->withProperties([
                'device' => $device,
                'ip_address' => $ipAddress,
            ]);

        if ($subject) {
            $activity->performedOn($subject);
        }

        /** @var \Spatie\Activitylog\Models\Activity $loggedActivity */
        $loggedActivity = $activity->log($description);

        if ($loggedActivity && isset($loggedActivity->id)) {
            DB::table('activity_log')
                ->where('id', $loggedActivity->id)
                ->update([
                    'device' => $device,
                    'ip_address' => $ipAddress,
                ]);
        }
    }

    /**
     * Build description for activity log.
     *
     * @param Model|null $subject
     * @param string $action
     * @param string $menuName
     * @return string
     */
    private function buildDescription(?Model $subject, string $action, string $menuName): string
    {
        $subjectName = '';
        if ($subject) {
            // Try common name attributes
            if (isset($subject->name)) {
                $subjectName = ' ' . $subject->name;
            } elseif (isset($subject->number)) {
                $subjectName = ' ' . $subject->number;
            } elseif (isset($subject->reference)) {
                $subjectName = ' ' . $subject->reference;
            } elseif (isset($subject->title)) {
                $subjectName = ' ' . $subject->title;
            } elseif (method_exists($subject, '__toString')) {
                $subjectName = ' ' . $subject->__toString();
            }
        }

        return ucfirst($action) . ' ' . $menuName . $subjectName;
    }
}
