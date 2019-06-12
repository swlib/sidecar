<?php
declare(strict_types=1);

namespace Swlib\Sidecar;

interface DispatcherInterface
{

    public function __construct($serverSide, $clientSide);

    /**
     * @param int $role ROLE_SERVER | ROLE_CLIENT
     * @return $this
     */
    public function withRole(int $role): self;

    /**
     * @param callable $protocolHandler
     * @return $this
     */
    public function withProtocolHandler(callable $protocolHandler): self;

}
