<?php

declare(strict_types=1);

namespace Tourze\Symfony\WorkermanBundle\Contracts;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Workerman\Connection\ConnectionInterface;

#[AutoconfigureTag('workerman.buffer-aware')]
interface BufferAwareInterface
{
    /**
     * 缓冲区满了.
     *
     * @see https://manual.workerman.net/doc/zh-cn/worker/on-buffer-full.html
     */
    public function onBufferFull(ConnectionInterface $connection): void;

    /**
     * @see https://manual.workerman.net/doc/zh-cn/worker/on-buffer-drain.html
     */
    public function onBufferDrain(ConnectionInterface $connection): void;
}
