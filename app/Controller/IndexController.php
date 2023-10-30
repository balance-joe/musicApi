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

namespace App\Controller;

use App\Service\MusicApiFactory;
use App\Service\TencentService;
use Hyperf\Context\ApplicationContext;
use Hyperf\HttpServer\Annotation\AutoController;

#[AutoController]
class IndexController extends AbstractController
{


    public function index()
    {
        $user = $this->request->input('user', 'Hyperf');
        $method = $this->request->getMethod();

        return [
            'method' => $method,
            'message' => "Hello {$user}.",
        ];
    }

    /**
     * 搜索
     * $type =
     * */
    public function search()
    {
        $keyword = $this->request->input('keyword', '');
        $type = $this->request->input('type', 0);
        $offset = $this->request->input('offset', 1);
        $limit = $this->request->input('limit', 20);
        // 获取容器
        $container = ApplicationContext::getContainer();
        $musicApiFactory = $container->get(MusicApiFactory::class);

// 根据需要的音乐服务类型创建相应的音乐 API 服务实例
        $qqMusicApi = $musicApiFactory->createMusicApi('tencent');
        var_dump($qqMusicApi->search($keyword,$type));
        var_dump($keyword);
        var_dump($type);
        var_dump($offset);
        var_dump($limit);

    }

}
