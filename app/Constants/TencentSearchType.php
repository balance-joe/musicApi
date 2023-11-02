<?php

declare(strict_types=1);

namespace App\Constants;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

#[Constants]
class TencentSearchType extends AbstractConstants
{
    /**
     * @Message("单曲")
     */
    const TYPE_CODE_SONG = 0;

    /**
     * @Message("歌手")
     */
    const TYPE_CODE_ARTIST = 1;

    /**
     * @Message("专辑")
     */
    const TYPE_CODE_ALBUM = 2;

    /**
     * @Message("歌单")
     */
    const TYPE_CODE_PLAYLIST = 3;

    /**
     * @Message("MV")
     */
    const TYPE_CODE_MV = 4;

    /**
     * @Message("歌词")
     */
    const TYPE_CODE_LYRICS = 7;

    /**
     * @Message("用户")
     */
    const TYPE_CODE_USER = 8;

    /**
     * 常量
     * */
    public static function getConstants(): array
    {
        return [
            self::TYPE_CODE_SONG => 'songlist',
            self::TYPE_CODE_ARTIST => 'singer',
            self::TYPE_CODE_ALBUM => 'album',
            self::TYPE_CODE_PLAYLIST => 'songlist',
            self::TYPE_CODE_MV => 'mv',
            self::TYPE_CODE_LYRICS => 'song',
            self::TYPE_CODE_USER => 'user',
        ];
    }

}
