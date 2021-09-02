<?php

namespace App\Http\Middleware;

use App\Enums\CurrentRmsSyncProcess;
use Inertia\Inertia;
use Closure;
use Auth;

class CurrentRMSInfo
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        $status = $user->getCRMSStatus();
        Inertia::share('crms', [
            'status' => $status,
            'lastSync' => $user->getLastSyncDate()
        ]);

        if ($status != CurrentRmsSyncProcess::READY)
            return Inertia::render('Dashboard');
        return $next($request);
    }
}
