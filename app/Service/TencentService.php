<?php

declare(strict_types=1);

namespace App\Service;

use Hyperf\Guzzle\ClientFactory;

class TencentService
{

    /**
     * @var ClientFactory
     */
    private $clientFactory;
    /**
     * @var array|string[]
     */
    private array $headers;

    public function __construct(ClientFactory $clientFactory)
    {
        $this->clientFactory = $clientFactory;
        $this->headers = [
            'User-Agent' => 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0)',
            'Content-Type' => 'application/json',
            'Referer' => 'https://y.qq.com/',
            'Host' => 'u.y.qq.com',
            // Origin: 'https://y.qq.com/',
            'TE' => 'trailers',
            'Cookie' => ''
        ];
    }

    /**
     * 搜索
     */
    public function search($keyword, $type, $offset = 1, $limit = 20)
    {
        $params = [
            'query' => [
                'searchid' => 53806572956004615,
                't' => $type,
                'aggr' => 1,
                'cr' => 1,
                'catZhida' => 1,
                'lossless' => 0,
                'flag_qc' => 0,
                'p' => $offset,
                'n' => $limit,
                'w' => $keyword,
            ],
        ];

        $client = $this->clientFactory->create();
        $response = $client->get('https://c.y.qq.com/soso/fcgi-bin/music_search_new_platform', $params);
        $res = $response->getBody()->getContents();
        $res = ltrim($res, 'callback(');
        $res = rtrim($res, ')');
        $res = json_decode($res, true);

        return $res;
    }

    /**
     * 获取歌词
     * @param $mid int 歌曲id
     * @param $type int 类型:1=带时间的歌词，2=文字歌词
     * */
    public function lyric($mid, $type = 1)
    {
        $params = [
            'query' => [
                'songmid' => $mid,
                'format' => 'json',
                'nobase64' => 1
            ],
            'headers' => $this->headers
        ];
        $options = [];
        $client = $this->clientFactory->create($options);
        $response = $client->get('https://c.y.qq.com/lyric/fcgi-bin/fcg_query_lyric_new.fcg', $params);
        $res = $response->getBody()->getContents();
        return $res;
    }


    public function test($keyword, $type, $offset = 1, $limit = 20)
    {
        $url = 'https://u.y.qq.com/cgi-bin/musicu.fcg';
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
                    'num_per_page' => intval($limit),
                    'page_num' => intval($offset),
                    'query' => $keyword,
                    'search_type' => trim($type),
                ],
            ],
        ];


        $client = $this->clientFactory->create();
        $response = $client->post($url, [
            'json' => $data,
            'headers' => $this->headers,
        ]);

        return $response;
    }
}
