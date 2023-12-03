<?php

namespace App\Service;

use App\Support\Utils;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

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

        $tencentService = container()->get(TencentService::class);
        $br = end($music_info['file']);
        $br = 128;
        $url = $tencentService->getSongUrl($music_info['id'], $br);
        if (!$url) {
            logger()->notice('获取地址失败:' . $music_info['name'], $music_info);
        }
        $list[] = [
            'id' => $music_info['id'],
            'lyric_id' => $music_info['lyric_id'],
            'name' => $music_info['name'],
            'artist' => $music_info['artist'],
            'source' => $music_info['source'],
            'url' => $url
        ];
        return cache()->set('music_list', $list);
    }


    /**
     * 下载
     * */
    public function downloadMusic($song, $has_lyric = false)
    {
        $savePath = Utils::getSavePath($song);
        if ($has_lyric) {
            $lyric_savePath = Utils::getSavePath($song, null, 'lrc');
            $this->downloadLyric($song['lyric_id'], $lyric_savePath);
        }
        $client = new Client();
        try {
            $response = $client->get($song['url'], ['sink' => $savePath]);
            return '文件下载成功！';
        } catch (RequestException $e) {
            return '文件下载失败：' . $e->getMessage();
        }
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