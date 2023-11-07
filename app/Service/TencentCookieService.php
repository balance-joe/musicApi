<?php

namespace App\Service;

class TencentCookieService
{
    public static function parse($cookies)
    {
        try {
            if (is_string($cookies)) {
                $cookieObj = array();
                $cookiesArr = explode('; ', $cookies);
                foreach ($cookiesArr as $c) {
                    $arr = explode('=', $c);
                    $key = $arr[0];
                    $value = $arr[1];
                    if (count($arr) > 2) {
                        array_shift($arr);
                        $value = implode('=', $arr);
                    }
                    $cookieObj[$key] = $value;
                }
                return $cookieObj;
            } else if (is_array($cookies)) {
                return $cookies;
            } else {
                throw new \Exception("parse error");
            }
        } catch (\Exception $e) {
            throw new \Exception('Cookie:parse_error', 0);
        }
    }

    public static function serialization($data)
    {
        $cookie = '';
        foreach ($data as $key => $value) {
            $s = urlencode($key) . '=' . urlencode($value) . '; ';
            $cookie .= $s;
        }
        if ($cookie === '') {
            throw new \Exception('Cookie:serialization_error', 'serialization error');
        }
        return substr($cookie, 0, strlen($cookie) - 2);
    }
}

