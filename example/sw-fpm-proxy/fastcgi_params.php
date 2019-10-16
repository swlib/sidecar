<?php
declare(strict_types=1);

return [
    'SCRIPT_FILENAME'   => '$script_filename',
    'QUERY_STRING'      => '$query_string',
    'REQUEST_METHOD'    => '$request_method',
    'CONTENT_TYPE'      => '$content_type',
    'CONTENT_LENGTH'    => '$content_length',
    'SCRIPT_NAME'       => '$script_name',
    'REQUEST_URI'       => '$request_uri',
    'DOCUMENT_URI'      => '$document_uri',
    'DOCUMENT_ROOT'     => '$document_root',
    'SERVER_PROTOCOL'   => '$server_protocol',
    'REQUEST_SCHEME'    => '$scheme',
    'GATEWAY_INTERFACE' => 'CGI/1.1',
    'SERVER_SOFTWARE'   => 'swoole/' . SWOOLE_VERSION,
    'REMOTE_ADDR'       => '$remote_addr',
    'REMOTE_PORT'       => '$remote_port',
    'SERVER_ADDR'       => '$server_addr',
    'SERVER_PORT'       => '$server_port',
    'SERVER_NAME'       => '$server_name',
    'REDIRECT_STATUS'   => '200'
];
