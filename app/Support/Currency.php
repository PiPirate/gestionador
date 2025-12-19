<?php

namespace App\Support;

use App\Models\Setting;

class Currency
{
    public const USD = 'usd';
    public const COP = 'cop';

    public static function current(): string
    {
        $current = strtolower((string) session('currency', self::USD));

        return in_array($current, [self::USD, self::COP], true) ? $current : self::USD;
    }

    public static function switch(string $currency): void
    {
        $currency = strtolower($currency);
        session([
            'currency' => in_array($currency, [self::USD, self::COP], true) ? $currency : self::USD,
        ]);
    }

    public static function rate(): float
    {
        static $cachedRate;
        if ($cachedRate === null) {
            $cachedRate = (float) (Setting::where('key', 'rate_sell')->value('value') ?? 4000);
        }

        return $cachedRate > 0 ? $cachedRate : 1;
    }

    public static function format(float|int|string|null $amount, string $baseCurrency = self::USD): string
    {
        $normalized = is_numeric($amount) ? (float) $amount : 0.0;
        [$value, $targetCurrency] = self::convert($normalized, $baseCurrency, self::current());

        if ($targetCurrency === self::USD) {
            return 'US$' . number_format($value, 2);
        }

        return '$' . number_format($value, 0, ',', '.');
    }

    public static function convert(float $amount, string $fromCurrency, string $toCurrency): array
    {
        $from = strtolower($fromCurrency);
        $to = strtolower($toCurrency);

        if ($from === $to) {
            return [$amount, $to];
        }

        $rate = self::rate();

        if ($from === self::USD && $to === self::COP) {
            return [$amount * $rate, $to];
        }

        if ($from === self::COP && $to === self::USD) {
            return [$rate ? $amount / $rate : $amount, $to];
        }

        return [$amount, $to];
    }
}
