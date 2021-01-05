<?php

declare(strict_types=1);

namespace App\Controller;

use App\Domain\Context\User\Command\CreateUser;
use App\Domain\Context\User\Command\UpdateUser;
use App\Domain\Context\User\UserCommandHandler;
use App\Domain\Projection\User\UserFinder;
use App\Domain\Projection\User\Repository\UserRepository;
use App\Domain\Projection\User\UserIdentifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private $userCommandHandler;
    private $userFinder;
    private $userRepository;

    public function __construct(
        UserCommandHandler $userCommandHandler,
        UserFinder $userFinder,
        UserRepository $userRepository
    )
    {
        $this->userCommandHandler = $userCommandHandler;
        $this->userFinder = $userFinder;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/user/list", name="user")
     */
    public function list(): Response
    {
        return $this->render('user-list.html.twig', [
            'users' => $this->userFinder->execute()
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
     * @Route("/user/edit/{id}", name="user-edit")
     */
    public function edit(string $id)
    {
        $userIdentifier = UserIdentifier::fromString($id);
        return $this->render('user-edit.html.twig', [
            'users' => $this->userFinder->execute(),
            'currentUser' => $this->userRepository->findById($userIdentifier)
        ]);
    }

    /**
     * @Route("/user/update", name="user-update")
     */
    public function update(Request $request): RedirectResponse
    {
        $this->userCommandHandler->handleUpdateUser(
            new UpdateUser(
                UserIdentifier::fromString($request->request->get('form')['id']),
                $request->request->get('form')['name'],
                $request->request->get('form')['mail']
            )
        );

        return $this->redirect('/user/list');
    }

    /**
     * @Route("/user/id/{id}", name="user-stream")
     */
    public function userStream(string $id)
    {
        $userIdentifier = UserIdentifier::fromString($id);
        return $this->render('user-stream.html.twig', [
            'currentUser' => $this->userRepository->findById($userIdentifier),
            'users' => $this->userFinder->execute(),
            'stream' => $this->userCommandHandler->handleStream($userIdentifier)
        ]);
    }
}
