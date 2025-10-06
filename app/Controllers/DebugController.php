<?php
namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;

class DebugController extends BaseController
{
    use ResponseTrait;

    public function checkApiKey()
    {
        return $this->respond([
            'api_key_defined' => defined('COINMARKETCAP_API_KEY'),
            'api_key_value' => COINMARKETCAP_API_KEY ? '***' . substr(COINMARKETCAP_API_KEY, -4) : 'EMPTY',
            'env_value' => $_ENV['COINMARKETCAP_API_KEY'] ?? 'NOT_SET'
        ]);
    }
}