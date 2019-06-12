<?php
declare(strict_types=1);

namespace Swlib;

use Swlib\Sidecar\DispatcherInterface;

interface SidecarInterface
{

    /**
     * @param DispatcherInterface $dispatcher
     * @param bool $prepend
     * @return $this
     */
    public function addDispatcher(DispatcherInterface $dispatcher, bool $prepend = false): self;

    public function run(): bool;

}
