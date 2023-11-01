<?php

declare(strict_types=1);

namespace App\Service;

use GuzzleHttp\Exception\GuzzleException;
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

    public function searchBack($keyword, $type, $offset = 1, $limit = 20)
    {

        $params = [
            'form_params' => [
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
                    ]
                ]
            ],
            'headers' => $this->headers

        ];

        $options = [
            'headers' => $this->headers
        ];
        $client = $this->clientFactory->create($options);
        $response = $client->post('https://u.y.qq.com/cgi-bin/musicu.fcg', ['query' => $params]);
        $res = $response->getBody()->getContents();
        return $res;
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
     * @param $mid string 歌曲id
     * @param $type string 类型:1=lyric格式歌词，2=文字歌词
     * @return string
     * @throws GuzzleException
     */
    public function lyric(string $mid, string $type = '1'): string
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
        $result = json_decode($res, true);
        if ($result['retcode'] != 0) {
            return '';
        }
        $lyric = $result['lyric'];

        //处理歌词为纯文本
        if ($type === '2') {
            $lyric = preg_replace('/\[[^\]]+\]/', '', $lyric);
            // 替换多个连续的\r\n为单个\n
            $lyric = trim(preg_replace('/\r\n+/', "\n", $lyric));
        }
        var_dump($lyric);
        return $lyric;
    }

    /**
     * 搜索建议
     * @param $keyword string 关键字
     * */
    public function suggestSearch(string $keyword){
        $params = [
            'query' => [
                'key' => $keyword,
                'format' => 'json',
            ],
            'headers' => $this->headers
        ];
        $options = [];
        $client = $this->clientFactory->create($options);
        $response = $client->get('https://c.y.qq.com/splcloud/fcgi-bin/smartbox_new.fcg', $params);
        $res = $response->getBody()->getContents();
        $result = json_decode($res, true);
        return $result;
    }

    public function mv()
    {
        //https://u.y.qq.com/cgi-bin/musicu.fcg?data=%7B%22getMvUrl%22%3A%7B%22module%22%3A%22gosrf.Stream.MvUrlProxy%22%2C%22method%22%3A%22GetMvUrls%22%2C%22param%22%3A%7B%22vids%22%3A%5B%22i00247i8v7b%22%5D%2C%22request_typet%22%3A10001%7D%7D%7D&g_tk=676242659&callback=jQuery1123016760720414443142_1564727885674&format=jsonp&inCharset=utf8&outCharset=GB2312&platform=yqq
    }

}
