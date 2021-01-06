<?php

declare(strict_types=1);

namespace App\Command;

use App\Domain\Projection\Blog\BlogProjector;
use App\Domain\Projection\Comment\CommentProjector;
use App\Domain\Projection\User\UserProjector;
use Neos\EventSourcing\SymfonyBridge\Command\ProjectionReplayCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\Process;

class ProjectionReplayDemoCommand extends Command
{
    protected static $defaultName = 'eventsourcing:projection-replay-demo';

    private $projectDir = '';

    public function __construct(
        KernelInterface $kernelInterface
    )
    {
        $this->projectDir = $kernelInterface->getProjectDir();

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Replay all projections of the demo.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->replayListener(
            BlogProjector::class,
            'neos_eventsourcing.eventstore.blog'
        );

        $this->replayListener(
            CommentProjector::class,
            'neos_eventsourcing.eventstore.blog'
        );

        $this->replayListener(
            UserProjector::class,
            'neos_eventsourcing.eventstore.user'
        );

        return Command::SUCCESS;
    }

    private function replayListener(
        string $listenerClassName,
        string $eventStoreContainerId
    )
    {
        $command = sprintf(
            '%s/bin/console',
            $this->projectDir
        );

        $process = new Process(
            [
                'php',
                $command,
                ProjectionReplayCommand::getDefaultName(),
                $listenerClassName,
                $eventStoreContainerId
            ]
        );
        $process->run();
    }
}
