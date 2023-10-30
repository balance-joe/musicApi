<?php

declare(strict_types=1);

namespace App\Service;

use Hyperf\Guzzle\ClientFactory;

class TencentService
{
    private ClientFactory $clientFactory;

    public function __construct(ClientFactory $clientFactory)
    {
        $this->clientFactory = $clientFactory;
    }


    /**
     * 搜索
     * */
    public function search($keyword, $type, $offset = 1, $limit = 20): array
    {
        $data = [
            'comm' => [
                'ct' => '19',
                'cv' => '1859',
                'uin' => '0',
            ],
            'req' => [
                'method' => 'DoSearchForQQMusicDesktop',
                'module' => 'music.search.SearchCgiService',
                'param' => [
                    'grp' => 1,
                    'num_per_page' => $limit,
                    'page_num' => $offset,
                    'query' => $keyword,
                    'search_type' => $type,
                ],
            ],
        ];
        $options = [
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0)',
            ],
        ];
        // $client 为协程化的 GuzzleHttp\Client 对象
        $client = $this->clientFactory->create($options);
        // 发起HTTP请求
        $response = $client->post('https://u.y.qq.com/cgi-bin/musicu.fcg', [
            'json' => $data,
        ]);

        // 处理响应
        if ($response->getStatusCode() === 200) {
            $result = json_decode($response->getBody()->getContents(), true);
            // 返回结果或执行其他操作
            return $result;
        } else {
            // 请求失败处理
            throw new \RuntimeException('Request failed with status code ' . $response->getStatusCode());
        }
    }


}