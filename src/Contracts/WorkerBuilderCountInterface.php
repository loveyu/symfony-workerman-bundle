<?php

declare(strict_types=1);

namespace Tourze\Symfony\WorkermanBundle\Contracts;

interface WorkerBuilderCountInterface
{
    /**
     * 获取进程数量，用于定义其他进程时需要
     */
    public function getCount(): int;
}
