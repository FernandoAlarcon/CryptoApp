<?php
namespace App\Controllers;

use App\Libraries\CoinMarketCapAPI;
use CodeIgniter\API\ResponseTrait;

class CryptoController extends BaseController
{
    use ResponseTrait;

    public function getCryptos()
    {
        try {
            $api = new CoinMarketCapAPI();
            $result = $api->getLatestListings(20);
            
            return $this->respond([
                'status' => 'success',
                'data' => $result['data'] ?? []
            ]);
            
        } catch (\Exception $e) {
            return $this->respond([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function trackCrypto()
    {
        // Obtener datos JSON del cuerpo de la solicitud
        $json = $this->request->getJSON();
        
        if ($json) {
            $coinId = $json->coin_id ?? null;
            $coinName = $json->coin_name ?? null;
        } else {
            // Fallback a datos POST normales
            $coinId = $this->request->getPost('coin_id');
            $coinName = $this->request->getPost('coin_name');
        }
        
        if (!$coinId) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Coin ID is required',
                'received_data' => [
                    'json' => $json ? true : false,
                    'post_data' => $this->request->getPost(),
                    'raw_input' => $this->request->getBody()
                ]
            ], 400);
        }
        
        // Usar session para almacenar temporalmente
        $tracked = session('tracked_cryptos') ?? [];
        
        // Evitar duplicados
        if (!isset($tracked[$coinId])) {
            $tracked[$coinId] = [
                'id' => $coinId,
                'name' => $coinName,
                'added_at' => date('Y-m-d H:i:s')
            ];
            session()->set('tracked_cryptos', $tracked);
        }
        
        return $this->respond([
            'status' => 'success',
            'message' => 'Crypto added to tracking',
            'tracked' => array_values($tracked)
        ]);
    }

    public function getTrackedCryptos()
    {
        try {
            $tracked = session('tracked_cryptos') ?? [];
            
            log_message('debug', 'Tracked cryptos from session: ' . print_r($tracked, true));
            
            if (empty($tracked)) {
                return $this->respond([
                    'status' => 'success',
                    'data' => []
                ]);
            }
            
            $coinIds = array_keys($tracked);
            log_message('debug', 'Coin IDs to fetch: ' . implode(', ', $coinIds));
            
            $api = new CoinMarketCapAPI();
            $result = $api->getSpecificCoins($coinIds);
            
            log_message('debug', 'API response received');
            
            // Si la API no devuelve datos, usamos los datos básicos de sesión
            $cryptoData = [];
            
            if (isset($result['data']) && !empty($result['data'])) {
                // Usar datos actualizados de la API
                $cryptoData = array_values($result['data']);
            } else {
                // Usar datos básicos de sesión como fallback
                foreach ($tracked as $coinId => $coinInfo) {
                    $cryptoData[] = [
                        'id' => $coinId,
                        'name' => $coinInfo['name'],
                        'symbol' => strtoupper(substr($coinInfo['name'], 0, 3)), // Simbolo aproximado
                        'quote' => [
                            'USD' => [
                                'price' => 0,
                                'percent_change_24h' => 0,
                                'volume_24h' => 0
                            ]
                        ]
                    ];
                }
                log_message('debug', 'Using fallback data from session');
            }
            
            return $this->respond([
                'status' => 'success',
                'data' => $cryptoData
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in getTrackedCryptos: ' . $e->getMessage());
            
            // Fallback: devolver datos básicos de sesión incluso en error
            $tracked = session('tracked_cryptos') ?? [];
            $fallbackData = [];
            
            foreach ($tracked as $coinId => $coinInfo) {
                $fallbackData[] = [
                    'id' => $coinId,
                    'name' => $coinInfo['name'],
                    'symbol' => strtoupper(substr($coinInfo['name'], 0, 3)),
                    'quote' => [
                        'USD' => [
                            'price' => 0,
                            'percent_change_24h' => 0,
                            'volume_24h' => 0
                        ]
                    ]
                ];
            }
            
            return $this->respond([
                'status' => 'success',
                'data' => $fallbackData,
                'message' => 'Using cached data due to API error'
            ]);
        }
    }

    public function untrackCrypto($coinId)
    {
        $tracked = session('tracked_cryptos') ?? [];
        
        if (isset($tracked[$coinId])) {
            unset($tracked[$coinId]);
            session()->set('tracked_cryptos', $tracked);
        }
        
        return $this->respond([
            'status' => 'success',
            'message' => 'Crypto removed from tracking',
            'tracked' => array_values($tracked)
        ]);
    }

    public function debugSession()
    {
        return $this->respond([
            'session_tracked' => session('tracked_cryptos') ?? [],
            'session_id' => session_id(),
            'all_session' => $_SESSION ?? []
        ]);
    }
    
}