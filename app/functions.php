<?php

use GuzzleHttp\Exception\GuzzleException;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Utils\ApplicationContext;
use Psr\Log\LoggerInterface;

if (! function_exists('container')) {

    /**
     * 获取容器实例
     * @return \Psr\Container\ContainerInterface
     */
    function container(): \Psr\Container\ContainerInterface
    {
        return ApplicationContext::getContainer();
    }

}

if (! function_exists('cache')) {

    /**
     * 获取Cache实例
     * @return \Hyperf\Redis\Redis
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    function cache()
    {
        return container()->get(\Psr\SimpleCache\CacheInterface::class);
    }

}

if (! function_exists('redis')) {

    /**
     * 获取Redis实例
     * @return \Hyperf\Redis\Redis
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    function redis(): \Hyperf\Redis\Redis
    {
        return container()->get(\Hyperf\Redis\Redis::class);
    }

}

if (! function_exists('console')) {

    /**
     * 获取控制台输出实例
     * @return StdoutLoggerInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    function console(): StdoutLoggerInterface
    {
        return container()->get(StdoutLoggerInterface::class);
    }

}

if (! function_exists('logger')) {

    /**
     * 获取日志实例
     * @param string $name
     * @return LoggerInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    function logger(string $name = 'Log'): LoggerInterface
    {
        return container()->get(LoggerFactory::class)->get($name);
    }

}

if (! function_exists('format_size')) {
    /**
     * 格式化大小
     * @param int $size
     * @return string
     */
    function format_size(int $size): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $index = 0;
        for ($i = 0; $size >= 1024 && $i < 5; $i++) {
            $size /= 1024;
            $index = $i;
        }
        return round($size, 2) . $units[$index];
    }
}

//if (!function_exists('exception_array')) {
//    /**
//     * 异常转数组
//     * @param object $exception
//     * @return array
//     */
//    function exception_array(object $exception): array
//    {
//        $exceptionArray = [
//            'Message' => $exception->getMessage(),
//            'Line' => $exception->getLine(),
//            'File' => $exception->getFile(),
//            'Code' => $exception->getCode(),
//            'Trace' => $exception->getTrace(),
//            'Time' => date('Y-m-d H:i:s'),
//        ];
//
//        if ($exception instanceof \Error) {
//            $exceptionArray['ErrorType'] = get_class($exception);
//        } elseif ($exception instanceof GuzzleException) {
//            $exceptionArray['ExceptionType'] = 'HTTP请求错误';
//        } elseif ($exception instanceof \Exception) {
//            $exceptionArray['ExceptionType'] = get_class($exception);
//        }
//
//        return $exceptionArray;
//    }
//}
