<?php

declare(strict_types=1);

namespace App\Domain\Projection\CommentList;

use App\Domain\Context\Commenting\Event\CommentWasCreated;
use Doctrine\DBAL\Connection;
use Neos\EventSourcing\EventStore\RawEvent;
use Neos\EventSourcing\Projection\ProjectorInterface;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CommentListFinder
{

    // do SQL queries and return values in DTOs
}
