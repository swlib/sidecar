<?php
declare(strict_types=1);

return [
    'document_root' => realpath(__DIR__ . '/../../../test/'),
    'scheme' => 'https',
    'host' => '127.0.0.1',
    'port' => 443,
    'ssl_cert_file' => __DIR__ . '/ssl/server.crt',
    'ssl_key_file' => __DIR__ . '/ssl/server.key'
];
