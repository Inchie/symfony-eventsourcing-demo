<?php

declare(strict_types=1);

namespace App\Command;

use App\Domain\Context\Commenting\Command\CreateComment;
use App\Domain\Context\Commenting\CommentingCommandHandler;
use App\Domain\ValueObject\UserIdentifier;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SayHelloCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:say-hello';

    /**
     * @var CommentingCommandHandler
     */
    private $commentingCommandHandler;

    /**
     * SayHelloCommand constructor.
     * @param CommentingCommandHandler $commentingCommandHandler
     */
    public function __construct(
        CommentingCommandHandler $commentingCommandHandler
    )
    {
        $this->commentingCommandHandler = $commentingCommandHandler;
        parent::__construct();
    }

    protected function configure()
    {
        // ...
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->commentingCommandHandler->handleCreateComment(new CreateComment(
            UserIdentifier::fromString("meinAuthor"),
            "Mein Comment"
        ));

        return Command::SUCCESS;
    }
}
