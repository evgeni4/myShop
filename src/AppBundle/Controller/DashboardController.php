<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Address;
use AppBundle\Entity\Cart;
use AppBundle\Service\Address\AddressService;
use AppBundle\Service\Cart\CartService;
use AppBundle\Service\Users\UserServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
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
    private $addressService;

    public function __construct(AddressService $addressService, CartService $cartService, UserServiceInterface $userService)
    {
        $this->userService = $userService;
        $this->cartService = $cartService;
        $this->addressService = $addressService;
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
        return $this->render('users/dashboard.html.twig', ['user' => $currentUser, 'cartStatus' => $cartStatus,]);
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
        if ($currentUser->isUser()) {
            return $this->redirectToRoute('shop_index');
        }
        return $this->render('customers/all_customers.html.twig',
            [
                'user' => $currentUser,
                'customers' => $customers
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
        if ($currentUser->isUser()) {
            return $this->redirectToRoute('shop_index');
        }
        return $this->render('users/all_users.html.twig',
            [
                'user' => $currentUser,
                'allUsers' => $allUsers
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
        if (!$currentUser->isUser()) {
            return $this->redirectToRoute('shop_index');
        }
        if ($currentUser->isAdmin()) {
            $orders = $this->cartService->userOrdersCompletes();
            $ordersPending = $this->cartService->userOrdersPending();
        } else {
            $orders = $this->cartService->findByUser($currentUser->getId());
            $ordersPending = $this->cartService->findByCartStatus($currentUser->getId());
        }
        return $this->render('users/orders_users.html.twig',
            [
                'cartStatus' => $cartStatus,
                'user' => $currentUser,
                'orders' => $orders,
                'ordersPending' => $ordersPending
            ]);
    }

    /**
     * @Route("/dashboard/Allorders", name="user_orders", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function userOneOrder()
    {

        $currentUser = $this->userService->currentUser();
        if ($currentUser->isAdmin()) {
            $orders = $this->cartService->userOrdersCompletes();
            $ordersPending = $this->cartService->userOrdersPending();
        } else {
            $orders = $this->cartService->findByUser($currentUser->getId());
            $ordersPending = $this->cartService->findByCartStatus($currentUser->getId());
        }

        $addressUser = $this->addressService->addressByUser();
        return $this->render('users/orders_users.html.twig',
            [
                'ordersPending' => $ordersPending,
                'user' => $currentUser,
                'orders' => $orders,
                'addressUser' => $addressUser
            ]);
    }

    /**
     * @Route("orders/invoice", name="order_invoice")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return RedirectResponse|Response|null
     */
    public function orderInvoice(Request $request)
    {
        $currentUser = $this->userService->currentUser();
        if (!$currentUser->isAdmin()) {
            return $this->redirectToRoute('shop_index');
        }
        $customers = $this->unique_multi_array($this->cartService->userOrdersCompletes());
        $invoiceData=null;
         if ($request->isMethod('POST')){
             $data = $request->request->all();
             $customerId = $data['customer']['customer_id'];
             $invoiceData = $this->cartService->findByUser($customerId);
         }
            return $this->render('users/order_invoice.html.twig',
            [
                'invoiceData' => $invoiceData,
                'user' => $currentUser,
                'customers' => $customers
            ]);
    }

    /**
     * @param $orders
     * @return array
     */
    private function unique_multi_array($orders)
    {
        $array = [];
        foreach ($orders as $val) {
            $key = $val->getUserId()->getId();
            if (!array_key_exists($key, $val)) {
                $array[$key] = $val->getUserId()->getFullName();
            }
        }
        return $array;
    }
}
