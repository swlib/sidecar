<?php
declare(strict_types=1);

$config = require __DIR__ . '/config.php';
$params = require __DIR__ . '/fastcgi_params.php';

return function (Swoole\Http\Request $request) use ($config, $params) : array {
    $serverParams                    = $config;
    $serverParams['content_type']    = $request->header['content-type'] ?? '';
    $serverParams['content_length']  = $request->header['content-length'] ?? '0';
    $serverParams['query_string']    = $request->server['query_string'] ?? '';
    $serverParams['request_method']  = $request->server['request_method'];
    $serverParams['script_name']     = $request->server['path_info'];
    $serverParams['script_filename'] = $serverParams['document_root'] . $serverParams['script_name'];
    $serverParams['request_uri']     = $request->server['request_uri'];
    $serverParams['document_uri']    = $request->server['request_uri'];
    $serverParams['server_protocol'] = $request->server['server_protocol'];
    $serverParams['remote_addr']     = $request->server['remote_addr'];
    $serverParams['remote_port']     = $request->server['remote_port'];
    $serverParams['server_addr']     = '127.0.0.1';
    $serverParams['server_port']     = $request->server['server_port'];
    $serverParams['server_name']     = $request->header['host'] ?? '';

    foreach ($params as &$param) {
        if ($param{0} === '$') {
            $param = $serverParams[substr($param, 1)];
        }
    }
    unset($param);

    if ($serverParams['scheme'] === 'https') {
        $params['HTTPS'] = '1';
    }

    foreach ($request->header as $name => $param) {
        $params['HTTP_' . str_replace('-', '_', strtoupper($name))] = $param;
    }

    return $params;
};