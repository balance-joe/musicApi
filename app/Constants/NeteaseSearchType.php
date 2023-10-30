<?php

declare(strict_types=1);

namespace App\Constants;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

#[Constants]
class NeteaseSearchType extends AbstractConstants
{

    /**
     * @Message("单曲")
     */
    const TYPE_CODE_SONG = 1;

    /**
     * @Message("专辑")
     */
    const TYPE_CODE_ALBUM = 10;

    /**
     * @Message("歌手")
     */
    const TYPE_CODE_ARTIST = 100;

    /**
     * @Message("歌单")
     */
    const TYPE_CODE_PLAYLIST = 1000;

    /**
     * @Message("用户")
     */
    const TYPE_CODE_USER = 1002;

    /**
     * @Message("MV")
     */
    const TYPE_CODE_MV = 1004;

    /**
     * @Message("歌词")
     */
    const TYPE_CODE_LYRICS = 1006;

    /**
     * @Message("电台")
     */
    const TYPE_CODE_RADIO = 1009;

    /**
     * @Message("视频")
     */
    const TYPE_CODE_VIDEO = 1014;
    
}
