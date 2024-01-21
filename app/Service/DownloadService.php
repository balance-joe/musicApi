<?php

namespace App\Service;

use App\Support\Utils;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Hyperf\Coroutine\Coroutine;
use Hyperf\Coroutine\Parallel;
use Hyperf\Guzzle\ClientFactory;
use Monolog\Logger;

class DownloadService
{

    /**
     * 增加下载列表
     * */
    public static function addList($music_info)
    {
        $list = cache()->get('music_list') ?: [];

        if (in_array($music_info['id'], array_column($list, 'mid'))) {
            return true;
        }

        $list[] = [
            'id' => $music_info['id'],
            'lyric_id' => $music_info['lyric_id'],
            'name' => $music_info['name'],
            'artist' => $music_info['artist'],
            'source' => $music_info['source'],
            'url' => $music_info['url']
        ];
        return cache()->set('music_list', $list);
    }

    /**
     * 批量下载歌曲
     * */
    function multiThreadDownload(array $song_list, $has_lyric)
    {
        // 使用并发限制组件限制下载并发数量
        $parallel = new Parallel(5); // 这里设置最大并发数为 10
        // 创建一个协程函数来执行多线程下载
        Coroutine::create(function () use ($parallel, $song_list, $has_lyric) {
            $clientFactory = make(ClientFactory::class);
            $client = $clientFactory->create();

            foreach ($song_list as $song) {
                $parallel->add(function () use ($song, $client, $has_lyric) {
                    // 定义保存文件的路径
                    $this->downloadMusic($song, $has_lyric);
                });
            }
            $parallel->wait();
        });
    }

    /**
     * 下载
     * @param $song array 歌曲详情
     * @param bool $has_lyric
     * */
    public function downloadMusic($song, $has_lyric = false)
    {
        if (!$song['url']) {
            logger()->error('文件地址不存在', ['歌曲名称' => $song['name']]);
            return false;
        }

        $savePath = Utils::getSavePath($song);
        if ($has_lyric) {
            $lyric_savePath = Utils::getSavePath($song, null, 'lrc');
            $this->downloadLyric($song['lyric_id'], $lyric_savePath);
        }
        $client = new Client();
        try {
            $client->get($song['url'], ['sink' => $savePath]);
        } catch (GuzzleException $e) {
            logger()->error('文件下载失败', exception_array($e));
            return false;
        }
        return true;
    }

    /**
     * 下载歌词
     * */
    public function downloadLyric($lyric_id, $path)
    {
        $tencentService = container()->get(TencentService::class);
        $lyric = $tencentService->lyric($lyric_id);
        file_put_contents($path, $lyric);
    }

}