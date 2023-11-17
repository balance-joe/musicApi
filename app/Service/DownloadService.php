<?php

namespace App\Service;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class DownloadService
{

    public function downloadFile()
    {
        $fileUrl = 'https://isure.stream.qqmusic.qq.com/F000003yK0ou2rX8rF.flac?guid=658650575&vkey=9C5CDBBEB86F5887A32437F22074E593F98589772AF16329582B495A77118B1B032A652FE38A6D8DAA7FC5669250709B4D7C0E36DF91AF20&uin=525140052&fromtag=120114'; // 替换为实际的音乐文件 URL
        $savePath = BASE_PATH . '/storage/挪威的森林.flac'; // 替换为要保存文件的路径和文件名

        $client = new Client();

        try {
            $response = $client->get($fileUrl, ['sink' => $savePath]);
            return '文件下载成功！';
        } catch (RequestException $e) {
            return '文件下载失败：' . $e->getMessage();
        }
    }


}