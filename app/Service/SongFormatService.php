<?php

namespace App\Service;

class SongFormatService
{

    public function format_netease($data)
    {
        $result = array(
            'id' => $data['id'],
            'name' => $data['name'],
            'artist' => array(),
            'album' => $data['al']['name'],
            'pic_id' => isset($data['al']['pic_str']) ? $data['al']['pic_str'] : $data['al']['pic'],
            'url_id' => $data['id'],
            'lyric_id' => $data['id'],
            'source' => 'netease',
        );
        if (isset($data['al']['picUrl'])) {
            preg_match('/\/(\d+)\./', $data['al']['picUrl'], $match);
            $result['pic_id'] = $match[1];
        }
        foreach ($data['ar'] as $vo) {
            $result['artist'][] = $vo['name'];
        }

        return $result;
    }

    public function format_tencent($data)
    {
        $result = array(
            'id' => $data['mid'],
            'name' => $data['name'],
            'artist' => array(),
            'album' => trim($data['album']['title']),
            'pic_id' => $data['album']['mid'],
            'url_id' => $data['mid'],
            'lyric_id' => $data['mid'],
            'file' => $data['file'],
            'source' => 'tencent',
        );

        foreach ($data['singer'] as $vo) {
            $result['artist'][] = $vo['name'];
        }
        //处理文件格式
        $formatList = [
            'size_96aac' => 'm4a',
            'size_128mp3' => '128',
            'size_320mp3' => '320',
            'size_ape' => 'ape',
            'size_flac' => 'flac',
            'size_hires' => 'Hi-Res',
        ];

        $includedFormats = array();
        foreach ($formatList as $key => $value) {
            $fileSizeKey = $key;
            if (array_key_exists($fileSizeKey, $result['file']) && $result['file'][$fileSizeKey] > 0) {
                $includedFormats[] = $value;
            }
        }
        $result['file'] = $includedFormats;
        return $result;
    }

    public function format_xiami($data)
    {
        $result = array(
            'id' => $data['songId'],
            'name' => $data['songName'],
            'artist' => array(),
            'album' => $data['albumName'],
            'pic_id' => $data['songId'],
            'url_id' => $data['songId'],
            'lyric_id' => $data['songId'],
            'source' => 'xiami',
        );
        foreach ($data['singerVOs'] as $vo) {
            $result['artist'][] = $vo['artistName'];
        }

        return $result;
    }

    public function format_kugou($data)
    {
        $result = array(
            'id' => $data['hash'],
            'name' => isset($data['filename']) ? $data['filename'] : $data['fileName'],
            'artist' => array(),
            'album' => isset($data['album_name']) ? $data['album_name'] : '',
            'url_id' => $data['hash'],
            'pic_id' => $data['hash'],
            'lyric_id' => $data['hash'],
            'source' => 'kugou',
        );
        list($result['artist'], $result['name']) = explode(' - ', $result['name'], 2);
        $result['artist'] = explode('、', $result['artist']);

        return $result;
    }

    public function format_baidu($data)
    {
        $result = array(
            'id' => $data['song_id'],
            'name' => $data['title'],
            'artist' => explode(',', $data['author']),
            'album' => $data['album_title'],
            'pic_id' => $data['song_id'],
            'url_id' => $data['song_id'],
            'lyric_id' => $data['song_id'],
            'source' => 'baidu',
        );

        return $result;
    }

    public function format_kuwo($data)
    {
        $result = array(
            'id' => $data['rid'],
            'name' => $data['name'],
            'artist' => explode('&', $data['artist']),
            'album' => $data['album'],
            'pic_id' => $data['rid'],
            'url_id' => $data['rid'],
            'lyric_id' => $data['rid'],
            'source' => 'kuwo',
        );

        return $result;
    }


}