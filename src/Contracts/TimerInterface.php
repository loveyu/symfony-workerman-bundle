<?php

declare(strict_types=1);

namespace Tourze\Symfony\WorkermanBundle\Contracts;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * @see https://packagist.org/packages/workerman/crontab
 */
#[AutoconfigureTag(self::SERVICE_TAG)]
interface TimerInterface
{
    final public const SERVICE_TAG = 'workerman.timer';

    /**
     * 定时任务表达式.
     */
    public function getExpression(): string;

    /**
     * 执行逻辑.
     */
    public function execute(): void;
}
