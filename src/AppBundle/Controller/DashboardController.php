<?php

namespace AppBundle\Controller;

use AppBundle\Service\Cart\CartService;
use AppBundle\Service\Users\UserServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\User;

class DashboardController extends Controller
{
    /**
     * @var UserServiceInterface
     */
    private $userService;
    /**
     * @var CartService
     */
    private $cartService;
    public function __construct(CartService $cartService,UserServiceInterface $userService)
    {
        $this->userService = $userService;
        $this->cartService = $cartService;
    }
    /**
     * @Route("/dashboard", name="user_office", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return Response|null
     */
    public function userOffice()
    {
        $currentUser = $this->userService->currentUser();
        $cartStatus = $this->cartService->findByCartStatus($currentUser->getId());
        return $this->render('users/dashboard.html.twig', ['user' => $currentUser,'cartStatus' => $cartStatus,]);
    }
    /**
     * @Route("/dashboard/customers", name="all_customers")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return Response|null
     */
    public function allCustomers()
    {
        $customers = $this->userService->getCustomer();
        $currentUser = $this->userService->currentUser();
        if ($currentUser->isUser()){
            return $this->redirectToRoute('shop_index');
        }
        return $this->render('customers/all_customers.html.twig',
            [
                'user' => $currentUser,
                'customers'=>$customers
            ]);
    }
    /**
     * @Route("/dashboard/allUsers", name="all_users")
     * @Security("has_role('ROLE_ADMIN')")
     * @return Response|null
     */
    public function allUsers()
    {
        $allUsers = $this->userService->allUser();
        $currentUser = $this->userService->currentUser();
        if ($currentUser->isUser()){
            return $this->redirectToRoute('shop_index');
        }
        return $this->render('users/all_users.html.twig',
            [
                'user' => $currentUser,
                'allUsers'=>$allUsers
            ]);
    }

    /**
     * @Route("/dashboard/orders", name="all_orders", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function userOrder()
    {
        $currentUser = $this->userService->currentUser();
        $cartStatus = $this->cartService->findByCartStatus($currentUser->getId());
        if (!$currentUser->isUser()){
            return $this->redirectToRoute('shop_index');
        }
        $orders = $this->cartService->findByUser($currentUser->getId());
        $ordersPending = $this->cartService->findByCartStatus($currentUser->getId());
        return $this->render('users/orders_users.html.twig',
            [
                'cartStatus' => $cartStatus,
                'user' => $currentUser,
                'orders'=>$orders,
                'ordersPending'=>$ordersPending
            ]);
    }
    /**
     * @Route("/dashboard/Allorders", name="user_orders", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function userOrders()
    {
          $currentUser = $this->userService->currentUser();
        if ($currentUser->isAdmin() ===true){
            $orders = $this->cartService->userOrdersCompletes();
            $cartStatus = $this->cartService->userOrdersPending();
        }else{
            $orders = $this->cartService->findByUser($currentUser->getId());
            $cartStatus = $this->cartService->findByCartStatus($currentUser->getId());
        }

        return $this->render('users/orders_users.html.twig',
            [
                'cartStatus' => $cartStatus,
                'user' => $currentUser,
                'orders'=>$orders
            ]);
    }
}
