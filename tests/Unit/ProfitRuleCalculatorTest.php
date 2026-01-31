<?php

namespace Tests\Unit;

use App\Services\ProfitRuleCalculator;
use PHPUnit\Framework\TestCase;

class ProfitRuleCalculatorTest extends TestCase
{
    private array $tiers = [
        ['upTo' => 1_000_000, 'rate' => 0.12],
        ['upTo' => 5_000_000, 'rate' => 0.08],
        ['upTo' => 10_000_000, 'rate' => 0.06],
        ['upTo' => null, 'rate' => 0.05],
    ];

    public function testCalculatesMonthlyProfitForDefinedCases(): void
    {
        $this->assertSame(108000.0, ProfitRuleCalculator::calcMonthlyProfit(900_000, $this->tiers));
        $this->assertSame(120000.0, ProfitRuleCalculator::calcMonthlyProfit(1_000_000, $this->tiers));
        $this->assertSame(440000.0, ProfitRuleCalculator::calcMonthlyProfit(5_000_000, $this->tiers));
        $this->assertSame(446000.0, ProfitRuleCalculator::calcMonthlyProfit(5_100_000, $this->tiers));
        $this->assertSame(560000.0, ProfitRuleCalculator::calcMonthlyProfit(7_000_000, $this->tiers));
    }

    public function testMonthlyProfitIsMonotonic(): void
    {
        $previousAmount = 0;
        $previousProfit = 0;

        for ($i = 0; $i < 50; $i++) {
            $amount = random_int(1, 20_000_000);
            $profit = ProfitRuleCalculator::calcMonthlyProfit($amount, $this->tiers);
            if ($amount > $previousAmount) {
                $this->assertGreaterThan($previousProfit, $profit);
            }
            $previousAmount = $amount;
            $previousProfit = $profit;
        }
    }
}
