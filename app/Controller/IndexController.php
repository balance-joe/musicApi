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
use App\Service\TencentCookieService;
use App\Service\TencentService;
use Hyperf\Context\ApplicationContext;
use Hyperf\Di\Container;
use Hyperf\HttpServer\Annotation\AutoController;

#[AutoController]
class IndexController extends AbstractController
{


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
    public function search(Container $container)
    {
        $keyword = $this->request->input('keyword', '');
        $type = $this->request->input('type', 0);
        $offset = $this->request->input('offset', 1);
        $limit = $this->request->input('limit', 20);
        $tencentService = $container->get(TencentService::class);
        $res = $tencentService->search($keyword, $type, $offset, $limit);
        return $this->success($res);
    }

    /**
     * 获取歌词
     * */
    public function lyric(Container $container)
    {
        $mid = $this->request->input('mid');
        $type = $this->request->input('type', '1');
        $tencentService = $container->get(TencentService::class);
        $res = $tencentService->lyric($mid, $type);
        return $this->success($res);
    }

    /**
     * 获取歌单
     * */
    public function play_list_desc(Container $container)
    {
        $id = $this->request->input('id');
        $tencentService = $container->get(TencentService::class);
        $res = $tencentService->playListDesc($id);
        return $this->success($res);
    }

    /**
     * 获取歌曲详情
     * */
    public function song(Container $container)
    {
        $mid = $this->request->input('mid');
        $tencentService = $container->get(TencentService::class);
        $res = $tencentService->song($mid);
        return $this->success($res);
    }

    /**
     * 获取歌曲详情
     * */
    public function artist(Container $container)
    {
        $id = $this->request->input('id');
        $tencentService = $container->get(TencentService::class);
        $res = $tencentService->artist($id);
        return $this->success($res);
    }

    /**
     * 获取连接地址
     * */
    public function get_song_url(Container $container)
    {
        $mid = $this->request->input('mid');
        $br = $this->request->input('br', 128);
        $tencentService = $container->get(TencentService::class);
        $res = $tencentService->getSongUrl($mid, $br);
        return $this->success($res);
    }

    /**
     * 设置Cookie
     * */
    public function setCookie(Container $container)
    {
        $data = $this->request->input('data', '');
        $tencentService = $container->get(TencentService::class);
        $cookie = $tencentService->setCookie($data);
        return $this->success($cookie);
    }

    /**
     * 获取Cookie
     * */
    public function getCookie(Container $container)
    {
        $cookie = $this->request->input('cookie', '');
        $tencentService = $container->get(TencentService::class);
        $res = $tencentService->getCookie($cookie);
        return $this->success($res);
    }
}
