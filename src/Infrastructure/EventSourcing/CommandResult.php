<?php

declare(strict_types=1);

namespace App\Infrastructure\EventSourcing;

use App\Infrastructure\EventSourcing\Projection\PendingProjections;
use App\Infrastructure\EventSourcing\Projection\ProjectionInterface;
use Neos\EventStore\Model\Event\SequenceNumber;

/**
 * @copyright escr-test-distribution
 * It contains the {@see PendingProjections}
 * (i.e. all affected projections by the command and their target sequence number) for the blocking mechanism.
 * It also contains the {@see CommitResult} that contains the highest sequence number and version of the published
 * events
 */
final class CommandResult
{
    public function __construct(
        private readonly PendingProjections $pendingProjections
    ) {
    }

    public static function empty(): self
    {
        return new self(PendingProjections::empty(), 0);
    }

    public function block(): void
    {
        foreach ($this->pendingProjections as $pendingProjection) {
            $expectedSequenceNumber = $this->pendingProjections->getExpectedSequenceNumber($pendingProjection);
            $this->blockProjection($pendingProjection, $expectedSequenceNumber);
        }
    }

    private function blockProjection(ProjectionInterface $projection, SequenceNumber $expectedSequenceNumber): void
    {
        /** @codingStandardsIgnoreStart */
        $attempts = 0;
        while ($projection->getSequenceNumber()->value < $expectedSequenceNumber->value) {
            usleep(50000); // 50000μs = 50ms
            if (++$attempts > 100) { // 5 seconds
                throw new \RuntimeException(sprintf(
                    'TIMEOUT while waiting for projection "%s" to catch up to sequence number %d - check the error logs for details.',
                    $projection::class,
                    $expectedSequenceNumber->value
                ), 1_550_232_279);
            }
        }
        /** @codingStandardsIgnoreEnd */
    }
}
