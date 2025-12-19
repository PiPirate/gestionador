<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AuditController extends Controller
{
    public function index()
    {
        $logs = DB::table('activity_log')->orderByDesc('created_at')->limit(20)->get()->map(function ($row) {
            return [
                'action' => $row->description ?? 'Evento',
                'user' => $row->causer_name ?? 'Sistema',
                'time' => $row->created_at,
            ];
        });

        return view('modules.audit.index', compact('logs'));
    }
}
