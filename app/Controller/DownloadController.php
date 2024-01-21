<?php

namespace App\Controller;

use App\Service\DownloadService;
use App\Service\MusicApiFactory;
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

    protected $musicApi ;
    public function __construct(Container $container)
    {
        $music_source = $this->request->input('music_source','tencent');
        $musicApi = new MusicApiFactory($container);
        $this->musicApi = $musicApi->createMusicApi($music_source);
    }

    /**
     * 下载音乐
     * */
    public function down(Container $container, DownloadService $downloadService)
    {
        $mid = $this->request->input('mid', '');
        $has_lyric = $this->request->input('has_lyric', true);

        $music_info = $this->musicApi->song($mid);
        $music_info['url'] = $this->musicApi->url($mid);
        if (!$music_info) {
            return $this->error('歌曲不存在');
        }
        $downloadService->downloadMusic($music_info, $has_lyric);
        return $this->success($music_info);
    }

    /**
     * 下载列表
     * */
    public function downList(Container $container, DownloadService $downloadService)
    {
        $has_lyric = true;
        $list = cache()->get('music_list') ?: [];
        $downloadService->multiThreadDownload($list, $has_lyric);
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
        $list_id = $this->request->input('$list_id');
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

        $music_info = $this->musicApi->song($mid);
        $br = end($music_info['file']);
        $music_info['url'] = $this->musicApi->url($music_info['id'], $br);
        if (!$music_info['url']) {
            logger()->notice('获取地址失败:' . $music_info['name'], $music_info);
        }
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

        $res = $this->musicApi->playList($id);
        if (!$res) {
            return $this->error('歌单不存在', $res);
        }
        $song_list = $res[0]['songlist'];

        foreach ($song_list as $song) {
            $music_info = (new SongFormatService)->format_tencent($song);
            DownloadService::addList($music_info);
        }
        return $this->success([], '添加成功');
    }

}