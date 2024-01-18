<?php

declare(strict_types=1);

namespace App\Service;

/**
 * 音乐工厂类
 * */
interface MusicApi
{

    /**
     * 搜索
     * */
    public function search($keyword, $type, $offset = 1, $limit = 20): array;

    /**
     * 歌单详情
     * @param $id string 歌单id
     * */
    public function playList(string $id): array;

    /**
     * 歌手详情
     * @param $artistId string 歌手id
     * */
    public function artist(string $artistId): array;

    /**
     * 专辑
     * @param $id string 专辑id
     * */
    public function album(string $id): array;

    /**
     * 歌曲
     * @param $songId string 歌曲id
     * */
    public function song(string $songId): array;

    /**
     * 歌词内容
     * @param $songId string 歌曲id
     * @param $type int 歌词类型: 1=文本,2=lyric格式的歌词
     * */
    public function lyric(string $songId, int $type): string;

    /**
     * 歌曲url
     * @param $songId string 歌曲id
     * */
    public function url(string $songId): array;

    /**
     * 榜单列表
     * */
    public function topCategory(): array;

    /**
     * 榜单详情
     * */
    public function top(): array;

    /**
     * 设置cookie
     * */
    public function setCookie($cookie): array;

    /**
     * 获取cookie
     * */
    public function getCookie(): array;
}
