<?php

namespace App\Services;

use App\Models\Investment;
use Illuminate\Support\Carbon;

class InvestmentLifecycleService
{
    public function closeExpiredInvestments(): int
    {
        return 0;
    }
}
