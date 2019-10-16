<?php
declare(strict_types=1);

namespace Swlib\Sidecar\Dispatcher\FastCGI;

class PHPFpm
{

    public static function getDefaultSocketInfoAndAddress(): array
    {
        $fpmTT = `php-fpm -tt 2>&1`;
        if (preg_match('/listen = ([^\r\n]+)/', $fpmTT, $match)) {
            $listen = trim($match[1]);
            list($ip, $port) = explode(':', $listen, 2);
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return [[AF_INET, SOCK_STREAM, IPPROTO_IP], [$ip, (int)$port]];
            } else {
                return [[AF_UNIX, SOCK_STREAM, IPPROTO_IP], [$listen]];
            }
        }
        return [[AF_INET, SOCK_STREAM, IPPROTO_IP], ['127.0.0.1', 9000]];
    }

}
