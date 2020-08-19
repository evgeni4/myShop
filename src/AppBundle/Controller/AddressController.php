<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Address;
use AppBundle\Form\AddressType;
use AppBundle\Service\Address\AddressService;
use AppBundle\Service\Users\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AddressController extends Controller
{
    private $addressService;
    private $userService;

    public function __construct(AddressService $addressService, UserService $userService)
    {
        $this->addressService = $addressService;
        $this->userService = $userService;
    }

    /**
     * @Route("/dashboard/address", name="address")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return Response|null
     */
    public function address()
    {

        $address = $this->addressService->findByAuthor($this->getUser());
        $currentUser = $this->userService->currentUser();
        if (!$currentUser->isUser()){
            return $this->redirectToRoute('shop_index');
        }
        return $this->render('address/address.html.twig', [
            'user' => $currentUser,
            'address' => $address
        ]);
    }

    /**
     * @Route("/dashboard/addAddress", name="add_address", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return Response|null
     */
    public function addAddress()
    {
        $messages = [];
        $currentUser = $this->userService->currentUser();
        $address = $this->addressService->findByAuthor($this->getUser());
        if (count($address)==1){
            return $this->redirectToRoute('address');
        }
        if (!$currentUser->isUser()){
            return $this->redirectToRoute('shop_index');
        }
        return $this->render('address/address_add.html.twig', [
            'user' => $currentUser,
            'errors' => $messages,
            'form' => $this->createForm(AddressType::class)->createView()
        ]);
    }
    /**
     * @Route("/dashboard/addAddress",  methods={"POST"})
     * @Security ("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return Response|null
     */
    public function addAddressProcess(Request $request)
    {
        $address = new Address();
        $messages = [];
        $currentUser = $this->userService->currentUser();
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);
        $messages = $this->errorCollection($form, $messages);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->addressService->insert($address);
            $this->addFlash("info", "Create address successfully!");
            return $this->redirectToRoute('address');
        }
        return $this->render('address/address_add.html.twig', [
            'user' => $currentUser,
            'form' => $this->createForm(AddressType::class)->createView(),
            'errors' => $messages
        ]);
    }
    /**
     * @Route("/dashboard/updateAddress/{id}", name="update_address", methods={"GET"})
     * @Security ("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param int $id
     * @return Response|null
     */
    public function updateAddress(Request $request, int $id)
    {
        $messages = [];
        $currentUser = $this->userService->currentUser();
        $address = $this->addressService->getOne($id);
        return $this->render('address/address_update.html.twig', [
            'address' => $address,
            'user' => $currentUser,
            'errors' => $messages,
            'form' => $this->createForm(AddressType::class)->createView()
        ]);
    }

    /**
     * @Route("/dashboard/updateAddress/{id}",  methods={"POST"})
     *
     * @Security ("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @param Request $request
     * @param $id
     * @return Response|null
     */
    public function updateAddressProcess(Request $request, int $id)
    {

        $address = new Address();
        $messages = [];
        $currentUser = $this->userService->currentUser();
        $address = $this->addressService->getOne($id);
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);
        $messages = $this->errorCollection($form, $messages);
        if (null === $address) {
            return $this->redirectToRoute('address');
        }
        if ($currentUser->isAuthorOrAdmin($address, $currentUser)) {
            return $this->redirect('shop_index');
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $this->addressService->update($address);
            $this->addFlash("info", "Update address successfully!");
            return $this->redirectToRoute('address');
        }
        return $this->render('address/address_update.html.twig', [
            'user' => $currentUser,
            'form' => $this->createForm(AddressType::class)->createView(),
            'errors' => $messages,
            'address' => $address,
        ]);
    }

    /**
     * @Route("/dashboard/deleteAddress/{id}", name="delete_address")
     * @Security ("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function deleteAddress(Request $request, int $id)
    {
        $currentUser = $this->userService->currentUser();
        $address = $this->addressService->getOne($id);
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if (null === $address) {
            return $this->redirectToRoute('address');
        }
        if ($currentUser->isAuthorOrAdmin($address, $currentUser)) {
            return $this->redirect('shop_index');
        }
        $this->addressService->delete($address);
        $this->addFlash('info', "Delete address successfully!");
        return $this->redirectToRoute('address');
    }

    /**
     * @param FormInterface $form
     * @param array $messages
     * @return array
     */
    private function errorCollection(FormInterface $form, array $messages): array
    {
        foreach ($form->getErrors(true) as $err) {
            $messages[] = $err->getMessage();
        }
        return $messages;
    }
}
