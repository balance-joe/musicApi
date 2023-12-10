<?php

namespace App\Controller;

use App\Service\DownloadService;
use App\Service\SongFormatService;
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

        return $this->success([]);
    }

    /**
     * 下载列表
     * */
    public function downList(Container $container, DownloadService $downloadService)
    {
        $has_lyric = true;
        $list = cache()->get('music_list') ?: [];
        $downloadService->multiThreadDownload($list,$has_lyric);
    }


    /**
     * 清空下载列表
     * */
    public function clearList()
    {
        cache()->set('music_list', []);
        return $this->success([], '清除成功');
    }

    /**
     * 下载列表
     * */
    public function list(Container $container, DownloadService $downloadService)
    {
        $list = cache()->get('music_list');
        return $this->success($list);
    }

    /**
     * 增加音乐到列表
     * */
    public function add(Container $container)
    {

        $mid = $this->request->input('mid');
        if (!$mid) {
            return $this->error('请选择歌曲');
        }

        $tencentService = $container->get(TencentService::class);
        $music_info = $tencentService->song($mid);
        if (!$music_info) {
            return $this->error('歌曲不存在');
        }

        DownloadService::addList($music_info);

        return $this->success([], '添加成功');
    }

    /**
     * 下载歌单
     * */
    public function playlist(Container $container)
    {
        $id = $this->request->input('id');
        $tencentService = $container->get(TencentService::class);
        $res = $tencentService->playListDesc($id);
        if (!$res) {
            return $this->error('歌单不存在', $res);
        }
        $song_list = $res[0]['songlist'];

        $new_song_list = [];
        foreach ($song_list as $song) {
            $music_info = (new SongFormatService)->format_tencent($song);
            DownloadService::addList($music_info);
        }
//        return $this->success($new_song_list, '添加成功');
//        foreach ($res as $item){
//
//        }

    }

}