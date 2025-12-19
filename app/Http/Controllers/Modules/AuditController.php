<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;

class AuditController extends Controller
{
    public function index()
    {
        $logs = AuditLog::with('user')->orderByDesc('created_at')->limit(50)->get()->map(function ($log) {
            return [
                'action' => $log->action,
                'user' => $log->user?->name ?? 'Sistema',
                'time' => $log->created_at,
                'model' => class_basename($log->model_type ?? ''),
            ];
        });

        return view('modules.audit.index', compact('logs'));
    }
}
