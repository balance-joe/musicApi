<?php

declare(strict_types=1);

namespace App\Constants;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

#[Constants]
class MiguSearchType extends AbstractConstants
{
    /**
     * @Message("歌手")
     */
    const TYPE_CODE_ARTIST = 1;

    /**
     * @Message("歌曲")
     */
    const TYPE_CODE_SONG = 2;

    /**
     * @Message("专辑")
     */
    const TYPE_CODE_ALBUM = 4;

    /**
     * @Message("MV")
     */
    const TYPE_CODE_MV = 5;

    /**
     * @Message("歌单")
     */
    const TYPE_CODE_PLAYLIST = 6;

    /**
     * @Message("歌词")
     */
    const TYPE_CODE_LYRICS = 7;


}
