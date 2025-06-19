<?php

declare(strict_types=1);

namespace Tourze\Symfony\WorkermanBundle\Command;

use Fidry\CpuCoreCounter\CpuCoreCounter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\HttpKernel\KernelInterface;
use Tourze\Symfony\WorkermanBundle\Contracts\BufferAwareInterface;
use Tourze\Symfony\WorkermanBundle\Contracts\ConnectableInterface;
use Tourze\Symfony\WorkermanBundle\Contracts\TimerInterface;
use Tourze\Symfony\WorkermanBundle\Contracts\WorkerBuilderInterface;
use Workerman\Crontab\Crontab;
use Workerman\Worker;

#[AsCommand(name: RunCommand::NAME, description: 'Workerman服务入口')]
class RunCommand extends Command
{
    public const NAME = 'workerman:run';

    /**
     * @param KernelInterface                         $kernel
     * @param iterable<WorkerBuilderInterface|object> $workerBuilders
     * @param iterable<TimerInterface|object>         $timers
     * @param CpuCoreCounter                          $cpuCoreCounter
     */
    public function __construct(
        private readonly KernelInterface $kernel,
        #[AutowireIterator(WorkerBuilderInterface::WORKER_SERVICE_TAG)]
        private readonly iterable $workerBuilders,
        #[AutowireIterator(TimerInterface::SERVICE_TAG)]
        private readonly iterable $timers,
        private readonly CpuCoreCounter $cpuCoreCounter,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('action', InputArgument::REQUIRED, '咋行动作');
        $this->addOption('daemon', 'd', InputOption::VALUE_OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        Worker::$pidFile = $this->kernel->getCacheDir().'/workerman.pid';
        Worker::$logFile = $this->kernel->getCacheDir().'/workerman.log';

        foreach ($this->workerBuilders as $builder) {
            /** @var WorkerBuilderInterface $builder */
            if ($builder instanceof ConnectableInterface) {
                $worker = new Worker("{$builder->getTransport()}://{$builder->getListenIp()}:{$builder->getListenPort()}");
            } else {
                $worker = new Worker();
            }
            $worker->count = $this->cpuCoreCounter->getCount();
            $worker->name = $builder->getName();
            $worker->protocol = null;

            // 基础部分
            $worker->onWorkerStart = function (Worker $worker) use ($builder) {
                foreach ($this->timers as $timer) {
                    \assert($timer instanceof TimerInterface);
                    new Crontab($timer->getExpression(), $timer->execute(...));
                }
                $builder->onWorkerStart($worker);
            };
            $worker->onWorkerStop = $builder->onWorkerStop(...);
            $worker->onWorkerReload = function (Worker $worker) use ($builder) {
                foreach ($this->timers as $timer) {
                    \assert($timer instanceof TimerInterface);
                    new Crontab($timer->getExpression(), $timer->execute(...));
                }
                $builder->onWorkerReload($worker);
            };

            if ($builder instanceof ConnectableInterface) {
                $worker->onConnect = $builder->onConnect(...);
                $worker->onClose = $builder->onClose(...);
                $worker->onMessage = $builder->onMessage(...);
                $worker->onError = $builder->onError(...);
            }

            if ($builder instanceof BufferAwareInterface) {
                $worker->onBufferFull = $builder->onBufferFull(...);
                $worker->onBufferDrain = $builder->onBufferDrain(...);
            }
        }

        Worker::runAll();

        return Command::SUCCESS;
    }
}
