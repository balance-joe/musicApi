<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
return [
    \Hyperf\Guzzle\ClientFactory::class => \Hyperf\Guzzle\ClientFactory::class,
    App\Service\TencentService::class => App\Service\TencentService::class,
];
