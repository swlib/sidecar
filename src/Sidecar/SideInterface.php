<?php
declare(strict_types=1);

namespace Swlib\Sidecar;

interface SideInterface
{
    public function recvRequest();

    public function recvResponse();
}
