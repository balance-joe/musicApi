<?php

declare(strict_types=1);



namespace App\Support;

use http\Exception\RuntimeException;
use Hyperf\Stringable\Str;

final class Utils
{
    /**
     */
    public static function getDefaultSaveDir(): string
    {
        $saveDir = BASE_PATH . '/storage/';

        if (! is_dir($saveDir) && ! mkdir($saveDir, 0755, true) && ! is_dir($saveDir)) {
            throw new RuntimeException(sprintf('The directory "%s" was not created.', $saveDir));
        }

        return $saveDir;
    }

    /**
     * 获取下载地址
     */
    public static function getSavePath(array $song, ?string $saveDir = null, string $defaultExt = 'mp3'): string
    {
        $saveDir = Str::finish($saveDir ?? self::getDefaultSaveDir(), \DIRECTORY_SEPARATOR);

        if (! is_dir($saveDir) && ! mkdir($saveDir, 0755, true) && ! is_dir($saveDir)) {
            throw new RuntimeException(sprintf('The directory "%s" was not created.', $saveDir));
        }
        return sprintf(
            '%s%s - %s.%s',
            $saveDir,
            implode(',', $song['artist']),
            $song['name'],
            pathinfo(parse_url($song['url'], PHP_URL_PATH), PATHINFO_EXTENSION) ?: $defaultExt
        );
    }
}
