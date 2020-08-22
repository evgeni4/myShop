<?php

namespace AppBundle\Controller;

use AppBundle\Service\Categories\CategoriesService;
use AppBundle\Service\Categories\CategoriesServiceInterface;
use AppBundle\Service\Users\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    private $userService;
    private $categoriesService;
    public function __construct(UserServiceInterface $userService, CategoriesService $categoriesService )
    {
        $this->userService = $userService;
        $this->categoriesService = $categoriesService;
    }
    /**
     * @Route("/", name="shop_index")
     * @param Request $request
     * @return Response|null
     */
    public function indexAction(Request $request)
    {
        $categories = $this->categoriesService->getAllCategory();
        $currentUser = $this->userService->currentUser();
        return $this->render('default/index.html.twig', [
            'categories' => $categories,
            'user' => $currentUser,
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }
}
