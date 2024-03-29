<?php

declare(strict_types=1);

namespace App\Service;

use App\Constants\TencentSearchType;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
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
            'Referer' => 'https://y.qq.com/',
            'Host' => 'u.y.qq.com',
            'TE' => 'trailers',
        ];
    }

    /*
     *
     *  POST Https://u.y.qq.com/cgi-bin/musicu.fcg
     *  referer: https://y.qq.com/portal/profile.html
     *  Content-Type: json/application;charset=utf-8
     *  user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36
     *  {"comm":{"ct":20,"cv":1845,"uin":"0"},"req":{"method":"DoSearchForQQMusicDesktop","module":"music.search.SearchCgiService","param":{"grp":1,"query":"伍佰","num_per_page":30,"page_num":1,"search_type":10}}}
     * */
    public function search($keyword, $type, $offset = 1, $limit = 20)
    {
        $search_type_arr = TencentSearchType::getConstants();
        $search_type = array_keys($search_type_arr);

        if (!in_array($type, $search_type)) {
            throw new \Exception('类型不正确');
        }
        $params = [
            'comm' => ['ct' => 20, 'cv' => 1845, 'uin' => '0',],
            'res' => [
                'method' => 'DoSearchForQQMusicDesktop',
                'module' => 'music.search.SearchCgiService',
                'param' => [
                    'grp' => 1,
                    'query' => $keyword,
                    'num_per_page' => intval($limit),
                    'page_num' => intval($offset),
                    'search_type' => intval($type),
                ]
            ]
        ];
        $client = $this->clientFactory->create();
        $request = new Request(
            'POST',
            'https://u.y.qq.com/cgi-bin/musicu.fcg',
            $this->headers,
            json_encode($params, JSON_UNESCAPED_UNICODE)
        );
        $response = $client->send($request);

        $res = json_decode($response->getBody()->getContents(), true);
        $result = [];
        if ($res['code'] === 0) {
            $result = $res['res']['data']['body'];
            $result = $result[$search_type_arr[$type]]['list'];
        }
        return $result;
    }

    /**
     * 搜索
     * 这个接口查不到数据会报错
     */
    public function searchBack($keyword, $type, $offset = 1, $limit = 20)
    {
        $params = [
            'query' => [
                't' => $type, 'p' => $offset, 'n' => $limit, 'w' => $keyword,
                'searchid' => 53806572956004615, 'aggr' => 1, 'cr' => 1, 'catZhida' => 1, 'lossless' => 0, 'flag_qc' => 0,
            ],
        ];

        $client = $this->clientFactory->create();
        $response = $client->get('https://c.y.qq.com/soso/fcgi-bin/music_search_new_platform', $params);
        $res = $response->getBody()->getContents();
        $res = ltrim($res, 'callback(');
        $res = rtrim($res, ')');
        return json_decode($res, true);
    }

    /**
     * 搜索建议
     * @param $keyword string 关键字
     * */
    public function suggestSearch(string $keyword)
    {
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
        $result = json_decode($response->getBody()->getContents(), true);
        return $result;
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
        $result = json_decode($response->getBody()->getContents(), true);

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
        return $lyric;
    }

    /**
     * 歌单详情列表
     * @param $id string 歌单id
     * */
    public function playListDesc(string $id)
    {
        $params = [
            'query' => [
                'disstid' => $id, 'type' => 1, 'json' => 1, 'utf8' => 1, 'onlysong' => 0, 'new_format' => 1,
                'loginUin' => 0, 'hostUin' => 0, 'format' => 'json', 'inCharset' => 'utf8', 'outCharset' => 'utf-8',
                'notice' => 0, 'platform' => 'yqq.json', 'needNewCode' => 0,
            ],
            'headers' => $this->headers
        ];
        $options = [];
        $client = $this->clientFactory->create($options);
        $response = $client->get("https://c.y.qq.com/qzone/fcg-bin/fcg_ucc_getcdinfo_byids_cp.fcg", $params);
        $result = json_decode($response->getBody()->getContents(), true);
        if ($result['code'] !== 0) {
            return [];
        }

        return $result['cdlist'];
    }

    /**
     * 获取音乐地址
     * */
    public function url($mid, $br = '128')
    {
        $uin = '0'; // 设置默认 uin 值

        $typeMap = [
            'm4a' => ['s' => 'C400', 'e' => '.m4a',],
            '128' => ['s' => 'M500', 'e' => '.mp3',],
            '320' => ['s' => 'M800', 'e' => '.mp3',],
            'ape' => ['s' => 'A000', 'e' => '.ape',],
            'flac' => ['s' => 'F000', 'e' => '.flac',],
            'mflac' => ['s' => 'F0M0', 'e' => '.mflac',],
            'Hi-Res' => ['s' => 'RS01', 'e' => '.flac',]
        ];

        if (!isset($typeMap[$br])) {
            throw new \Exception("Song:br_error: br is not m4a, 128, 320, flac, mflac, Hi-Res");
        }

        $filename = array_map(function ($id) use ($typeMap, $br) {
            return "{$typeMap[$br]['s']}{$id}{$id}{$typeMap[$br]['e']}";
        }, array_filter(array_map('trim', explode(',', $mid))));

        $mids = array_map('trim', array_filter(explode(',', $mid)));


        $urlData = [
            'req' => [
                'module' => 'CDN.SrfCdnDispatchServer',
                'method' => 'GetCdnDispatch',
                'param' => ['guid' => '658650575', 'calltype' => 0, 'userip' => ''],
            ],
            'req_0' => [
                'module' => 'vkey.GetVkeyServer',
                'method' => 'CgiGetVkey',
                'param' => ['filename' => $filename, 'songmid' => $mids, 'uin' => $uin, 'guid' => '658650575', 'songtype' => [0], 'loginflag' => 1, 'platform' => '20'],
            ],
            'comm' => ['uin' => $uin, 'format' => 'json', 'ct' => 24, 'cv' => 0],
        ];

        $url = 'https://u.y.qq.com/cgi-bin/musicu.fcg?format=json&data=' . json_encode($urlData);
        $headers = $this->headers;
        $headers['Cookie'] = $this->getCookie();
        $client = $this->clientFactory->create();
        $request = new Request('GET', $url, $headers);
        $response = $client->send($request);
        $result = json_decode($response->getBody()->getContents(), true);
        if ($result['code'] !== 0) {
            return [];
        }
        $arrUrls = [];
        if (!empty($result['req_0']['data']['midurlinfo'])) {
            foreach ($result['req_0']['data']['midurlinfo'] as $e) {
                $arrUrls[] = $e['purl'] ? 'https://isure.stream.qqmusic.qq.com/' . $e['purl'] : null;
            }
        }
        return count($arrUrls) === 1 ? $arrUrls[0] : $arrUrls;
    }

    /**
     * @param $mid string 歌曲id
     * */
    public function song(string $mid)
    {
        $params = [
            'query' => [
                'songmid' => $mid,
                'platform' => 'yqq', 'format' => 'json',
            ],
            'headers' => $this->headers
        ];
        $client = $this->clientFactory->create();
        $response = $client->get('https://c.y.qq.com/v8/fcg-bin/fcg_play_single_song.fcg', $params);
        $result = json_decode($response->getBody()->getContents(), true);
        return (new SongFormatService)->format_tencent($result['data'][0]);
    }

    /**
     * 获取歌手信息
     * http://c.y.qq.com/splcloud/fcgi-bin/fcg_get_singer_desc.fcg?utf8=1&outCharset=utf-8&format=xml&singermid=0025NhlN2yWrP4
     * */
    public function artist($id)
    {

        $params = [
            'query' => [
                'utf8' => '1',
                'outCharset' => 'utf-8',
                'format' => 'xml',
                'singermid' => $id,
            ],
            'headers' => [
                'Referer' => "https://y.qq.com"
            ]
        ];
        $client = $this->clientFactory->create();
        $response = $client->get('https://c.y.qq.com/v8/fcg-bin/fcg_play_single_song.fcg', $params);
        $result = $response->getBody()->getContents();
        return $result;
    }

    /**
     * 设置Cookie
     * */
    public function setCookie($data)
    {
        $cookies = CookieService::parse($data);
        cache()->set('qq_cookie', $data);

        return $cookies;
    }

    /**
     * 获取Cookie
     * */
    public function getCookie()
    {
        return cache()->get('qq_cookie');
    }



}
