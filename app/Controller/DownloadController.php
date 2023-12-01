<?php

namespace App\Controller;

use App\Service\DownloadService;
use App\Service\TencentService;
use Hyperf\Di\Container;
use Hyperf\HttpServer\Annotation\AutoController;

/**
 * 下载代码
 * */
#[AutoController]
class DownloadController extends AbstractController
{

    /**
     * 下载音乐
     * */
    public function down(Container $container, DownloadService $downloadService)
    {
        $mid = $this->request->input('mid', '');

        $tencentService = $container->get(TencentService::class);
        $song_info = $tencentService->song($mid);
        $song_info['url'] = $tencentService->getSongUrl($mid);

        $res = $downloadService->downloadFile($song_info);
        return $this->success($res);
    }

    /**
     * 下载列表
     * */
    public function list(Container $container, DownloadService $downloadService)
    {
        return cache()->get('music_list');
    }

    /**
     * 增加音乐到列表
     * */
    public function add(Container $container)
    {
        $list = cache()->get('music_list') ?: [];

        $mid = $this->request->input('mid');
        if (!$mid) {
            return $this->error('请选择歌曲');
        }
        if (in_array($mid, array_column($list, 'mid'))) {
            return $this->error('歌曲已存在');
        }

        $tencentService = $container->get(TencentService::class);
        $music_info = $tencentService->song($mid);
        if (!$music_info) {
            return $this->error('歌曲不存在');
        }

        $music_info['url'] = $tencentService->getSongUrl($mid, end($music_info['file']));
        $list[] = [
            'mid' => $music_info['id'],
            'lyric_id' => $music_info['lyric_id'],
            'music_name' => $music_info['name'],
            'artist' => $music_info['artist'],
            'source' => $music_info['source'],
            'url' => $music_info['url']
        ];
        cache()->set('music_list', $list);
        return $this->success([], '添加成功');
    }


}