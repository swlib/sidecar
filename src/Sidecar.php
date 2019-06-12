<?php
declare(strict_types=1);

namespace Swlib;

use Swlib\Sidecar\Dispatcher;
use Swlib\Sidecar\DispatcherInterface;
use Swlib\Util\SingletonTrait;

class Sidecar implements SidecarInterface
{
    /**
     * Values of Role
     */
    const ROLE_SERVER = 0;
    const ROLE_CLIENT = 1;

    /**
     * Values of Protocol
     * @const PROTOCOL_TCP custom tcp protocol
     */
    const PROTOCOL_TCP       = 0;
    const PROTOCOL_HTTP      = 1;
    const PROTOCOL_WEBSOCKET = 2;
    const PROTOCOL_HTTP2     = 3;
    const PROTOCOL_GRPC      = 4;
    const PROTOCOL_FASTCGI   = 5;

    use SingletonTrait;

    public function __construct()
    {
    }

    public function addDispatcher(DispatcherInterface $dispatcher, bool $prepend = false): self
    {
        // TODO: Implement addDispatcher() method.
    }

    public function run(): bool
    {
        // TODO: Implement run() method.
    }
}

$sidecar = Sidecar::getInstance();
$fpmDispatcher = new FpmDispatcher();
$sidecar->addDispatcher($fpmDispatcher);
$sidecar->run();
