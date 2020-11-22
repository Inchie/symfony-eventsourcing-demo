<?php

declare(strict_types=1);

namespace App\Controller;

use App\Domain\Projection\CommentList\CommentListFinder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    private $commentListFinder;

    public function __construct(
        CommentListFinder $commentListFinder
    )
    {
        $this->commentListFinder = $commentListFinder;

    }

    /**
     * @Route("/", name="dashboard")
     */
    public function number(): Response
    {
        $comments = $this->commentListFinder->execute();

        return $this->render('dashboard.html.twig', [
            'comments' => $comments,
        ]);
    }
}
