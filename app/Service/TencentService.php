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
            'Content-Type' => 'application/json',
            'Referer' => 'https://y.qq.com/',
            'Host' => 'u.y.qq.com',
            'TE' => 'trailers',
//            'Cookie' => ''
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
        $params = json_encode([
            'comm' => [
                'ct' => 20,
                'cv' => 1845,
                'uin' => '0',
            ],
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
        ], JSON_UNESCAPED_UNICODE);
        $client = $this->clientFactory->create();
        $request = new Request('POST', 'https://u.y.qq.com/cgi-bin/musicu.fcg', $this->headers, $params);
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
        $res = $response->getBody()->getContents();
        $result = json_decode($res, true);
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
        return $lyric;
    }


    public function mv()
    {
        //https://u.y.qq.com/cgi-bin/musicu.fcg?data=%7B%22getMvUrl%22%3A%7B%22module%22%3A%22gosrf.Stream.MvUrlProxy%22%2C%22method%22%3A%22GetMvUrls%22%2C%22param%22%3A%7B%22vids%22%3A%5B%22i00247i8v7b%22%5D%2C%22request_typet%22%3A10001%7D%7D%7D&g_tk=676242659&callback=jQuery1123016760720414443142_1564727885674&format=jsonp&inCharset=utf8&outCharset=GB2312&platform=yqq
    }

    /**
     * 歌单详情列表
     * @param $id string 歌单id
     * */
    public function playListDesc(string $id)
    {
        $params = [
            'query' => [
                'type' => 1,
                'json' => 1,
                'utf8' => 1,
                'onlysong' => 0,
                'new_format' => 1,
                'disstid' => 7580163044,
                'loginUin' => 0,
                'hostUin' => 0,
                'format' => 'json',
                'inCharset' => 'utf8',
                'outCharset' => 'utf-8',
                'notice' => 0,
                'platform' => 'yqq.json',
                'needNewCode' => 0,
            ],
            'headers' => $this->headers
        ];
        $options = [];
        $client = $this->clientFactory->create($options);
        $response = $client->get("https://c.y.qq.com/qzone/fcg-bin/fcg_ucc_getcdinfo_byids_cp.fcg", $params);
        $res = $response->getBody()->getContents();
        $result = json_decode($res, true);
        if ($result['code'] !== 0) {
            return [];
        }

        return $result['cdlist'];
    }


    public function getSongUrl($mid, $br)
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
                'param' => [
                    'guid' => '658650575',
                    'calltype' => 0,
                    'userip' => '',
                ],
            ],
            'req_0' => [
                'module' => 'vkey.GetVkeyServer',
                'method' => 'CgiGetVkey',
                'param' => [
                    'filename' => $filename,
                    'guid' => '658650575',
                    'songmid' => $mids,
                    'songtype' => [0],
                    'uin' => $uin,
                    'loginflag' => 1,
                    'platform' => '20',
                ],
            ],
            'comm' => [
                'uin' => $uin,
                'format' => 'json',
                'ct' => 24,
                'cv' => 0,
            ],
        ];

        $url = 'https://u.y.qq.com/cgi-bin/musicu.fcg?format=json&data=' . json_encode($urlData);
        $url = 'https://u.y.qq.com/cgi-bin/musicu.fcg?format=json&data={"req":{"module":"CDN.SrfCdnDispatchServer","method":"GetCdnDispatch","param":{"guid":"658650575","calltype":0,"userip":""}},"req_0":{"module":"vkey.GetVkeyServer","method":"CgiGetVkey","param":{"filename":["M500001JCkEn2BbL6H001JCkEn2BbL6H.mp3","M500000B4ijs4Ufwql000B4ijs4Ufwql.mp3"],"guid":"658650575","songmid":["001JCkEn2BbL6H","000B4ijs4Ufwql"],"songtype":[0],"uin":"0","loginflag":1,"platform":"20"}},"comm":{"uin":"0","format":"json","ct":24,"cv":0}}';
        $options = $this->headers;
        $client = $this->clientFactory->create($options);
        $response = $client->get($url);
        $result = json_decode($response->getBody()->getContents(), true);
        if ($result['code'] !== 0){
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

}
