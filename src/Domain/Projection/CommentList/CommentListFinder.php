<?php

declare(strict_types=1);

namespace App\Domain\Projection\CommentList;

use Doctrine\ORM\EntityManagerInterface;

class CommentListFinder
{
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    public function execute()
    {
        /*
        $sql = 'Select payload FROM foo_events';

        $result = $this->entityManager
            ->getConnection()
            ->query($sql)
            ->fetchAll();

        $comments = new \ArrayIterator();
        foreach ($result as $row) {
            $comments->append(
                CommentDto::fromPayload($row['payload'])
            );
        }
        return $comments;
        */
        return [];
    }
}
