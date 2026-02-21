<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ExchangeRateService;

class ExchangeRateController extends Controller
{
    public function __construct(protected ExchangeRateService $service)
    {
    }

    /**
     * Get current exchange rates
     */
    public function index()
    {
        $data = $this->service->getRates('PKR');

        return response()->json([
            'base' => $data['base'],
            'rates' => $data['rates'],
            'timestamp' => $data['timestamp'],
            'source' => $data['source'],
        ]);
    }

    /**
     * Refresh exchange rates (force new fetch)
     */
    public function refresh()
    {
        $data = $this->service->refresh('PKR');

        return response()->json([
            'base' => $data['base'],
            'rates' => $data['rates'],
            'timestamp' => $data['timestamp'],
            'source' => $data['source'],
            'message' => 'Exchange rates refreshed',
        ]);
    }
}
