<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Context\Blogging\Event;

use App\Domain\Context\Blogging\Event\BlogWasCreated;
use App\Domain\Projection\Blog\BlogIdentifier;
use App\Domain\Projection\User\UserIdentifier;
use PHPUnit\Framework\TestCase;

class BlogWasCreatedTest extends TestCase
{
    /**
     * @test
     */
    public function blogWasCreatedEventReturnsTheExpectedEvent()
    {
        $blogWasCreated = new BlogWasCreated(
            BlogIdentifier::fromString('ABC-DEF'),
            'Minecraft',
            UserIdentifier::fromString('GHI')
        );

        $this->assertEquals(
            'ABC-DEF',
            $blogWasCreated->getId()->jsonSerialize()
        );

        $this->assertEquals(
            'Minecraft',
            $blogWasCreated->getName()
        );

        $this->assertEquals(
            'GHI',
            $blogWasCreated->getAuthor()->jsonSerialize()
        );
    }
}
