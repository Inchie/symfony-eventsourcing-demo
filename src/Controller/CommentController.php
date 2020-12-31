<?php

declare(strict_types=1);

namespace App\Controller;

use App\Domain\Context\Blogging\Repository\BlogRepository;
use App\Domain\Context\Commenting\Command\CreateComment;
use App\Domain\Context\Commenting\CommentingCommandHandler;
use App\Domain\ValueObject\BlogIdentifier;
use App\Domain\ValueObject\UserIdentifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    private $commentingCommandHandler;
    private $blogRepository;

    public function __construct(
        CommentingCommandHandler $commentingCommandHandler,
        BlogRepository $blogRepository
    )
    {
        $this->commentingCommandHandler = $commentingCommandHandler;
        $this->blogRepository = $blogRepository;
    }

    /**
     * @Route("/comment/list", name="comment")
     */
    public function list()
    {
        return $this->render('comment-list.html.twig', [
            'blogs' => $this->blogRepository->findAll()
        ]);
    }

    /**
     * @Route("/comment/create", name="comment-create")
     */
    public function create(Request $request): RedirectResponse
    {
        $blogIdentifier = BlogIdentifier::fromString(
            $request->request->get('form')['blog']
        );

        $this->commentingCommandHandler->handleCreateComment(
            new CreateComment(
                $blogIdentifier,
                UserIdentifier::fromString($request->request->get('form')['user']),
                $request->request->get('form')['comment']
            )
        );

        $url = sprintf(
            '/blog/show/%s',
            $blogIdentifier->jsonSerialize()
        );

        return $this->redirect($url);
    }
}
