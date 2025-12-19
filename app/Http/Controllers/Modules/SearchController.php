<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Investment;
use App\Models\Investor;
use App\Models\Transaction;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $q = $request->get('q');

        $investors = Investor::query()
            ->when($q, fn ($query) => $query->where('name', 'like', "%{$q}%")
                ->orWhere('document', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%"))
            ->limit(5)
            ->get();

        $investments = Investment::query()
            ->when($q, fn ($query) => $query->where('code', 'like', "%{$q}%"))
            ->with('investor')
            ->limit(5)
            ->get();

        $transactions = Transaction::query()
            ->when($q, fn ($query) => $query->where('counterparty', 'like', "%{$q}%")
                ->orWhere('reference', 'like', "%{$q}%"))
            ->orderByDesc('transacted_at')
            ->limit(5)
            ->get();

        return view('modules.search.index', compact('q', 'investors', 'investments', 'transactions'));
    }
}
