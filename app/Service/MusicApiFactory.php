<?php

namespace App\Service;

use Hyperf\Di\Container;

class MusicApiFactory
{
    protected Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function createMusicApi(string $serviceType): MusicApi
    {
        switch ($serviceType) {
            case 'tencent':
                return $this->container->get(TencentApi::class);
            case 'netease':
                return $this->container->get(NetEaseMusicApi::class);
            case 'migu':
                return $this->container->get(MiguMusicApi::class);
            default:
                throw new \RuntimeException('Unsupported music service type: ' . $serviceType);
        }
    }
}
