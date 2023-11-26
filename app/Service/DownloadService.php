<?php

namespace App\Service;
use App\Support\Utils;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class DownloadService
{

    public function downloadFile($song)
    {
        $savePath = Utils::getSavePath($song);

        $client = new Client();

        try {
            $response = $client->get($song['url'], ['sink' => $savePath]);
            return '文件下载成功！';
        } catch (RequestException $e) {
            return '文件下载失败：' . $e->getMessage();
        }
    }


}