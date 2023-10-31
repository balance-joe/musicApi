<?php

declare(strict_types=1);

namespace App\Service;

use GuzzleHttp\Client;
use Hyperf\Guzzle\ClientFactory;
use Hyperf\Di\Annotation\Inject;

class TencentService
{
    /**
     * @Inject
     * @var ClientFactory
     */
    private $clientFactory;

    /**
     * 搜索
     */
    public function search($keyword, $type, $offset = 1, $limit = 20)
    {
        $data = [
            'query' => [
                'searchid' => 53806572956004615,
                't' => 1,
                'aggr' => 1,
                'cr' => 1,
                'catZhida' => 1,
                'lossless' => 0,
                'flag_qc' => 0,
                'p' => 1,
                'n' => 2,
                'w' => $keyword,
            ],
        ];
        $client = $this->clientFactory->create();

        $response = $client->get('https://c.y.qq.com/soso/fcgi-bin/music_search_new_platform', $data);

        return $response;
    }
}
