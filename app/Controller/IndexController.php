<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Controller;

use App\Service\MusicApiFactory;
use App\Service\TencentService;
use Hyperf\Di\Container;
use Hyperf\HttpServer\Annotation\AutoController;

#[AutoController]
class IndexController extends AbstractController
{

    protected $musicApi ;
    public function __construct(Container $container)
    {
        $musicApi = new MusicApiFactory($container);
        $music_source = $this->request->input('music_source');
        $this->musicApi = $musicApi->createMusicApi('tencent');

    }

    public function index()
    {
        $user = $this->request->input('user', 'Hyperf');
        $method = $this->request->getMethod();

        return [
            'method' => $method,
            'message' => "Hello {$user}.",
        ];
    }

    /**
     * 搜索
     * */
    public function search()
    {
        $keyword = $this->request->input('keyword', '');
        $type = $this->request->input('type', 0);
        $offset = $this->request->input('offset', 1);
        $limit = $this->request->input('limit', 20);
        $res = $this->musicApi->search($keyword, $type, $offset, $limit);
        return $this->success($res);
    }

    /**
     * 获取歌词
     * */
    public function lyric()
    {
        $mid = $this->request->input('mid');
        $type = $this->request->input('type', '1');
        $res = $this->musicApi->lyric($mid, $type);
        return $this->success($res);
    }

    /**
     * 获取歌单
     * */
    public function play_list()
    {
        $id = $this->request->input('id');
        $res = $this->musicApi->playList($id);
        return $this->success($res);
    }

    /**
     * 获取歌曲详情
     * */
    public function song()
    {
        $mid = $this->request->input('mid');

        $res = $this->musicApi->song($mid);
        return $this->success($res);
    }

    /**
     * 获取歌曲详情
     * */
    public function artist()
    {
        $id = $this->request->input('id');
        $res = $this->musicApi->artist($id);
        return $this->success($res);
    }

    /**
     * 获取歌曲地址
     * */
    public function get_song_url()
    {
        $mid = $this->request->input('mid');
        $br = $this->request->input('br', 128);
        $res = $this->musicApi->url($mid, $br);
        return $this->success($res);
    }

    /**
     * 设置Cookie
     * */
    public function setCookie()
    {
        $data = $this->request->input('data', '');
        $cookie = $this->musicApi->setCookie($data);
        return $this->success($cookie);
    }

    /**
     * 获取Cookie
     * */
    public function getCookie()
    {
        $res = $this->musicApi->getCookie();
        return $this->success($res);
    }
}
