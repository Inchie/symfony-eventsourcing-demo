<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Context\Blogging\Event;

use App\Domain\Context\Blog\Event\BlogWasUpdated;
use App\Domain\Projection\Blog\BlogIdentifier;
use PHPUnit\Framework\TestCase;

class BlogWasUpdatedTest extends TestCase
{
    /**
     * @test
     */
    public function blogWasCreatedEventReturnsTheExpectedEvent()
    {
        $blogWasCreated = new BlogWasUpdated(
            BlogIdentifier::fromString('ABC-DEF'),
            'Minecraft 16.2'
        );

        $this->assertEquals(
            'ABC-DEF',
            $blogWasCreated->getId()->jsonSerialize()
        );

        $this->assertEquals(
            'Minecraft 16.2',
            $blogWasCreated->getName()
        );
    }
}
