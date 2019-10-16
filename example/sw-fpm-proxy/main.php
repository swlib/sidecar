<?php
declare(strict_types=1);

use Swlib\FastCGI;
use Swlib\Http\ContentType;
use Swlib\Http\Status;
use Swlib\Sidecar\Dispatcher\FastCGI\PHPFpm;
use Swoole\Coroutine;
use Swoole\Coroutine\Socket;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;

require __DIR__ . '/../../../vendor/autoload.php';

error_reporting(E_ALL);

Coroutine::set([
    'log_level'     => SWOOLE_LOG_INFO,
    'trace_flags'   => 0,
    'max_coroutine' => 100000
]);

(function () {
    $phpFpmInfo = PHPFpm::getDefaultSocketInfoAndAddress();
    $config = require __DIR__ . '/config.php';
    $fastcgiParamsGenerator = require __DIR__ . '/fastcgi_params_generator.php';
    $httpServer = new Server('127.0.0.1', 80, SWOOLE_BASE);
    $httpServer->set([
        'http_parse_cookie' => false,
        'http_parse_post'   => false
    ]);
    $httpServer->on('request',
        function (Request $request, Response $response) use ($phpFpmInfo, $config, $fastcgiParamsGenerator) {
            $pathInfo = &$request->server['path_info'];
            $pathInfo = '/' . (ltrim($pathInfo, '/'));
            $extension = pathinfo($pathInfo, PATHINFO_EXTENSION);
            if (empty($extension)) {
                $pathInfo = rtrim($pathInfo, '/') . '/index.php';
            } elseif ($extension !== 'php') {
                $documentRoot = $config['document_root'];
                $scriptFilename = "{$documentRoot}{$pathInfo}";
                $realPath = realpath($scriptFilename);
                if (!$realPath || strpos($realPath, $documentRoot) !== 0 || !is_file($realPath)) {
                    $response->status(Status::NOT_FOUND);
                    return;
                } else {
                    $contentType = ContentType::get($extension);
                    $response->header['Content-Type'] = $contentType;
                    $response->sendfile($realPath);
                    return;
                }
            }
            unset($pathInfo);
            $fastcgi = new Socket(...$phpFpmInfo[0]);
            if (!$fastcgi->connect(...$phpFpmInfo[1])) {
                return; // 500
            }
            // send request
            static $beginRequest, $paramsEof, $stdinEof;
            ($beginRequest ?? ($beginRequest = new FastCGI\Record\BeginRequest(FastCGI::RESPONDER)));
            ($params = new FastCGI\Record\Params($fastcgiParamsGenerator($request)));
            ($paramsEof ?? ($paramsEof = new FastCGI\Record\Params()));
            $rawContent = $request->rawContent();
            if (empty($rawContent)) {
                $sendData = "{$beginRequest}{$params}{$paramsEof}";
            } else {
                $stdinList = [];
                while (true) {
                    $stdinList[] = $stdin = new FastCGI\Record\Stdin($rawContent);
                    $stdinLength = $stdin->getContentLength();
                    if ($stdinLength === strlen($rawContent)) {
                        break;
                    }
                    $rawContent = substr($rawContent, $stdinLength);
                };
                $stdin = implode($stdinList);
                ($stdinEof ?? $stdinEof = new FastCGI\Record\Stdin());
                $sendData = "{$beginRequest}{$params}{$paramsEof}{$stdin}{$stdinEof}";
            }
            $fastcgi->sendAll($sendData);
            // recv response
            $recvData = '';
            $contentData = '';
            while (true) {
                if (FastCGI\FrameParser::hasFrame($recvData)) {
                    $record = FastCGI\FrameParser::parseFrame($recvData);
                    if ($record instanceof FastCGI\Record\Stdout && $record->getContentLength() > 0) {
                        $contentData .= $record->getContentData();
                    } elseif ($record instanceof FastCGI\Record\EndRequest) {
                        list($status, $reason, $headers, $body) = (function (string $contentData) {
                            $headers = [];
                            list($_headers, $body) = explode("\r\n\r\n", $contentData, 2);
                            $_headers = explode("\r\n", $_headers);
                            foreach ($_headers as $header) {
                                list($name, $value) = explode(': ', $header, 2);
                                $headers[$name] = $value;
                            }
                            if ($headers['Status'] ?? null) {
                                list($status, $reason) = explode(' ', $headers['Status'], 2);
                                unset($headers['Status']);
                            }
                            $status = (int)($status ?? Status::OK);
                            $reason = $reason ?? '';
                            return [$status, $reason, $headers, $body];
                        })($contentData);
                        $response->status($status, $reason);
                        $response->header = $headers;
                        $response->end($body);
                        break;
                    }
                } else {
                    $tmp = $fastcgi->recv();
                    if (!$tmp) {
                        $fastcgi->close();
                        return; // 500
                    }
                    $recvData .= $tmp;
                }
            }
        }
    );
    $httpServer->start();
})();
