<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Infrastructure\EventSourcing\AppEventStore;
use App\Infrastructure\EventSourcing\Projection\ProjectionInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[\Symfony\Component\Console\Attribute\AsCommand('eventsourcing:projection-replay-demo')]
class ProjectionReplayDemoCommand extends Command
{
    public function __construct(
        private readonly AppEventStore $eventStore
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Replay all projections of the demo.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // First we reset all projections
        foreach ($this->eventStore->projections() as $projection) {
            assert($projection instanceof ProjectionInterface);
            $projection->reset();
        }

        $this->eventStore->catchUpAll();

        $output->writeln('<info>Replay events successfully</info>');
        return Command::SUCCESS;
    }
}
