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

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Psr\Container\ContainerInterface;

abstract class AbstractController
{
    #[Inject]
    protected ContainerInterface $container;

    #[Inject]
    protected RequestInterface $request;

    #[Inject]
    protected ResponseInterface $response;

    public function success($data = [], $msg = ''): \Psr\Http\Message\ResponseInterface
    {
        // 统一返回格式，code为0表示成功
        return $this->result(1, $msg, $data);
    }

    public function error($msg = '', $data = []): \Psr\Http\Message\ResponseInterface
    {
        // 统一返回格式，code为-1表示失败
        return $this->result(0, $msg, $data);
    }

    public function result($code, $msg, $data): \Psr\Http\Message\ResponseInterface
    {
        // 统一返回格式
        $result = [
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
            'time' => time()
        ];
        return $this->response->json($result);
    }

}
