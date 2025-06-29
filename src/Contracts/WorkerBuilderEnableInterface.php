<?php

declare(strict_types=1);

namespace Tourze\Symfony\WorkermanBundle\Contracts;

interface WorkerBuilderEnableInterface
{
    /**
     * 当前服务是否启用.
     */
    public function isEnable(): bool;
}
