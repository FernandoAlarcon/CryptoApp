<?php
namespace App\Libraries;

class CoinMarketCapAPI {
    private $apiKey;
    private $baseUrl = 'https://pro-api.coinmarketcap.com/v1/';
    
    public function __construct() {
        $this->apiKey = COINMARKETCAP_API_KEY;
    }
    
    private function makeRequest($endpoint, $parameters = []) {
        $url = $this->baseUrl . $endpoint;
        
        $headers = [
            'Accepts: application/json',
            'X-CMC_PRO_API_KEY: ' . $this->apiKey
        ];
        
        $qs = http_build_query($parameters);
        $request = "{$url}?{$qs}";
        
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $request,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 30
        ]);
        
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        
        if ($httpCode !== 200) {
            throw new \Exception('API request failed with HTTP code: ' . $httpCode);
        }
        
        return json_decode($response, true);
    }
    
    public function getLatestListings($limit = 20) {
        return $this->makeRequest('cryptocurrency/listings/latest', [
            'start' => 1,
            'limit' => $limit,
            'convert' => 'USD'
        ]);
    }
    
    public function getSpecificCoins($coinIds) {
        if (empty($coinIds)) return ['data' => []];
        
        return $this->makeRequest('cryptocurrency/quotes/latest', [
            'id' => is_array($coinIds) ? implode(',', $coinIds) : $coinIds,
            'convert' => 'USD'
        ]);
    }
}