<?php

declare(strict_types=1);

namespace App\Controller;

use App\Domain\Context\User\Command\CreateUser;
use App\Domain\Context\User\Command\UpdateUser;
use App\Domain\Context\User\Repository\UserRepository;
use App\Domain\Context\User\UserCommandHandler;
use App\Domain\Projection\UserList\UserListFinder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{

    private $userCommandHandler;
    private $userListFinder;
    private $userRepository;

    public function __construct(
        UserCommandHandler $userCommandHandler,
        UserListFinder $userListFinder,
        UserRepository $userRepository
    )
    {
        $this->userCommandHandler = $userCommandHandler;
        $this->userListFinder = $userListFinder;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/user/list", name="user")
     */
    public function list(): Response
    {
        return $this->render('user-list.html.twig', [
            'users' => $this->userListFinder->execute()
        ]);
    }

    /**
     * @Route("/user/create", name="user-create")
     */
    public function create(Request $request): RedirectResponse
    {
        $this->userCommandHandler->handleCreateUser(
            new CreateUser(
                $request->request->get('form')['name'],
                $request->request->get('form')['mail']
            )
        );

        return $this->redirect('/user/list');
    }

    /**
     * @Route("/user/edit/{stream}", name="user-edit")
     */
    public function edit(string $stream)
    {
        return $this->render('user-edit.html.twig', [
            'users' => $this->userListFinder->execute(),
            'currentUser' => $this->userRepository->findByStream($stream)
        ]);
    }

    /**
     * @Route("/user/update", name="user-update")
     */
    public function update(Request $request): RedirectResponse
    {
        $this->userCommandHandler->handleUpdateUser(
            new UpdateUser(
                $request->request->get('form')['name'],
                $request->request->get('form')['mail'],
                $request->request->get('form')['stream']
            )
        );

        return $this->redirect('/user/list');
    }

    /**
     * @Route("/user/stream/{stream}", name="user-stream")
     */
    public function userStream(string $stream)
    {
        return $this->render('user-stream.html.twig', [
            'users' => $this->userListFinder->execute(),
            'stream' => $this->userCommandHandler->handleStream($stream),
            'streamName' => $stream
        ]);
    }
}
