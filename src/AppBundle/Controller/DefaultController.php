<?php

namespace AppBundle\Controller;

use AppBundle\Service\Users\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    private $userService;
    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }
    /**
     * @Route("/", name="shop_index")
     * @param Request $request
     * @return Response|null
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        $currentUser = $this->userService->currentUser();
        return $this->render('default/index.html.twig', [
            'user' => $currentUser,
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }
}
