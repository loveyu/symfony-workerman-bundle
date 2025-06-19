<?php

declare(strict_types=1);

namespace Tourze\Symfony\WorkermanBundle\Contracts;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Workerman\Connection\ConnectionInterface;

#[AutoconfigureTag('workerman.connectable')]
interface ConnectableInterface
{
    /**
     * 协议，如 tcp/udp.
     */
    public function getTransport(): string;

    /**
     * 返回协议类.
     */
    public function getProtocolClass(): ?string;

    /**
     * 监听IP，一般是127.0.0.1或0.0.0.0.
     */
    public function getListenIp(): string;

    /**
     * 监听端口.
     */
    public function getListenPort(): int;

    /**
     * 有连接时触发.
     *
     * @see https://manual.workerman.net/doc/zh-cn/worker/on-connect.html
     */
    public function onConnect(ConnectionInterface $connection): void;

    /**
     * 连接断开
     *
     * @see https://manual.workerman.net/doc/zh-cn/worker/on-close.html
     */
    public function onClose(ConnectionInterface $connection): void;

    /**
     * 收到消息.
     *
     * @see https://manual.workerman.net/doc/zh-cn/worker/on-message.html
     */
    public function onMessage(ConnectionInterface $connection, mixed $request): void;

    /**
     * 连接发生错误.
     *
     * @see https://manual.workerman.net/doc/zh-cn/worker/on-error.html
     */
    public function onError(ConnectionInterface $connection, int $code, string $msg): void;
}
