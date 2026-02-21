<?php

namespace App\Services;

use App\Models\ExchangeRate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ExchangeRateService
{
    protected const CACHE_KEY = 'exchange_rates_live';
    protected const CACHE_DURATION = 3600; // 1 hour

    /**
     * Get exchange rates from cache or fetch fresh ones
     */
    public function getRates($baseCurrency = 'PKR')
    {
        return Cache::remember(
            self::CACHE_KEY . '_' . $baseCurrency,
            self::CACHE_DURATION,
            fn() => $this->fetchFromApi($baseCurrency)
        );
    }

    /**
     * Fetch rates from free API
     * Using exchangerate-api.com free tier (no auth required for basic rates)
     */
    protected function fetchFromApi($baseCurrency = 'PKR')
    {
        try {
            // Try exchangerate-api.com (offers free tier without auth)
            $response = Http::timeout(10)->get(
                "https://api.exchangerate-api.com/v4/latest/{$baseCurrency}"
            );

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['rates'])) {
                    return [
                        'base' => $baseCurrency,
                        'rates' => $data['rates'],
                        'timestamp' => now(),
                        'source' => 'live',
                    ];
                }
            }

            return $this->getFallbackRates();
        } catch (\Exception $e) {
            return $this->getFallbackRates();
        }
    }

    /**
     * Fallback to database rates if API fails
     */
    protected function getFallbackRates()
    {
        $rates = ExchangeRate::orderByDesc('effective_date')
            ->get()
            ->unique('currency_code')
            ->pluck('rate', 'currency_code')
            ->toArray();

        return [
            'base' => 'PKR',
            'rates' => $rates,
            'timestamp' => now(),
            'source' => 'database',
        ];
    }

    /**
     * Force refresh rates (clear cache)
     */
    public function refresh($baseCurrency = 'PKR')
    {
        Cache::forget(self::CACHE_KEY . '_' . $baseCurrency);
        return $this->getRates($baseCurrency);
    }
}
