<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        if (! $user || ! $user->isSuperAdmin()) {
            abort(403, 'Akses log audit detail hanya diperuntukkan bagi Super Admin.');
        }

        $action = $request->query('action');
        $search = $request->query('search');

        $logs = AuditLog::with(['user', 'student'])
            ->when($action, fn ($q) => $q->where('action', $action))
            ->when($search, function ($q) use ($search) {
                $q->where('description', 'LIKE', "%{$search}%")
                    ->orWhereHas('user', fn ($uq) => $uq->where('name', 'LIKE', "%{$search}%"))
                    ->orWhereHas('student', fn ($sq) => $sq->where('name', 'LIKE', "%{$search}%"));
            })
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $actions = AuditLog::select('action')->distinct()->pluck('action');

        return view('admin.audit.index', compact('logs', 'actions', 'action', 'search'));
    }
}
