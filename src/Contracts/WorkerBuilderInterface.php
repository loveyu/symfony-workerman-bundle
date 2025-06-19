<?php

declare(strict_types=1);

namespace Tourze\Symfony\WorkermanBundle\Contracts;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Workerman\Worker;

#[AutoconfigureTag(WorkerBuilderInterface::WORKER_SERVICE_TAG)]
interface WorkerBuilderInterface
{
    public const WORKER_SERVICE_TAG = 'workerman.worker';

    /**
     * 服务名.
     */
    public function getName(): string;

    /**
     * Worker启动时触发.
     *
     * @see https://manual.workerman.net/doc/zh-cn/worker/on-worker-start.html
     */
    public function onWorkerStart(Worker $worker): void;

    /**
     * Worker停止时触发
     * 这个在官网文档没介绍.
     */
    public function onWorkerStop(Worker $worker): void;

    /**
     * Worker重启.
     *
     * @see https://manual.workerman.net/doc/zh-cn/worker/on-worker-reload.html
     */
    public function onWorkerReload(Worker $worker): void;
}
