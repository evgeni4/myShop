<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Categories;
use AppBundle\Entity\User;
use AppBundle\Form\CategoriesType;
use AppBundle\Service\Categories\CategoriesService;
use AppBundle\Service\Users\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoriesController extends Controller
{
    private $categoriesService;
    private $userService;

    public function __construct(UserService $userService, CategoriesService $categoriesService)
    {
        $this->userService = $userService;
        $this->categoriesService = $categoriesService;
    }

    /**
     * @Route("/dashboard/categories", name="all_categories", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return Response|null
     */
    public function categories()
    {
        $currentUser = $this->userService->currentUser();
        if ($currentUser->isUser()) {
            return $this->redirectToRoute('shop_index');
        }
        return $this->render('category/categories.html.twig',
            [
                'user' => $currentUser
            ]);
    }

    /**
     * @Route("/dashboard/categories/add", name="add_category", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function addCategory()
    {
        $currentUser = $this->userService->currentUser();
        if ($currentUser->isUser()) {
            return $this->redirectToRoute('shop_index');
        }
        $messages = [];
        return $this->templateView($currentUser, $messages);
    }

    /**
     * @Route("/dashboard/categories/add",  methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return RedirectResponse|Response|null
     */
    public function addCategoryProcess(Request $request)
    {

        $currentUser = $this->userService->currentUser();
        $messages = [];
        $category = new Categories();
        $form = $this->createForm(CategoriesType::class, $category);
        $form->handleRequest($request);
        $validator = $this->get('validator');
        $validator->validate($category);
        $data = $request->request->get('categories');
        $messages = $this->errorCollection($form, $messages);
        if (null !== $this->categoriesService->getTitle($data['title'])) {
            $messages[] = "This Title ( " . $data['title'] . " ) already token!";
            return $this->templateView($currentUser, $messages);
        }
        if (null !== $this->categoriesService->getTitle($data['url'])) {
            $messages[] = "This Url ( " . $data['url'] . " ) already token!";
            return $this->templateView($currentUser, $messages);
        }
        if ($currentUser->isUser()) {
            return $this->redirectToRoute('shop_index');
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoriesService->insert($category);
            $this->addFlash("successfully", "Create category successfully!");
            return $this->redirectToRoute('all_categories');
        }
        return $this->templateView($currentUser, $messages);
    }

    /**
     * @Route("/dashboard/categories/edit/{id}", name="edit_category", methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param int $id
     * @return RedirectResponse|Response|null
     */
    public function editCategory(Request $request, int $id)
    {
        $currentUser = $this->userService->currentUser();
        $category = $this->categoriesService->getOneCategory($id);
        $messages = [];
        if (null === $category) {
            return $this->redirectToRoute('all_categories');
        }
        return $this->render('category/edit_category.html.twig',
            [
                'user' => $currentUser,
                'category' => $category,
                'errors' => $messages,
                'form' => $this->createForm(CategoriesType::class)->createView()
            ]
        );
    }

    /**
     * @Route("/dashboard/categories/edit/{id}", methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param int $id
     * @return RedirectResponse|Response|null
     */
    public function editCategoryProcess(Request $request, int $id)
    {
        $messages = [];
        $currentUser = $this->userService->currentUser();
        $category = $this->categoriesService->getOneCategory($id);
        $form = $this->createForm(CategoriesType::class, $category);
        $form->handleRequest($request);
        $data = $request->request->get('categories');
        $messages = $this->errorCollection($form, $messages);
        if ($category->getTitle()!==$data['title'] && null !== $this->categoriesService->getTitle($data['title'])) {
            $messages[] = "This Title ( " . $data['title'] . " ) already token!";
            return $this->templateView($currentUser, $messages);
        }
        if ($category->getUrl()!==$data['url'] &&null !== $this->categoriesService->getTitle($data['url'])) {
            $messages[] = "This Url ( " . $data['url'] . " ) already token!";
            return $this->templateView($currentUser, $messages);
        }
        if (!$currentUser->isAdmin() && !$currentUser->isAuthorCategory($category)){
            return $this->redirectToRoute('shop_index');
        }
        if (null === $category) {
            return $this->redirectToRoute('all_categories');
        }
        if ($form->isSubmitted() && $form->isValid()){
            $this->categoriesService->update($category);
            $this->addFlash("successfully", "Update category successfully!");
            return $this->redirectToRoute('all_categories');
        }
        return $this->render('category/edit_category.html.twig',
            [
                'user' => $currentUser,
                'category' => $category,
                'errors' => $messages,
                'form' => $this->createForm(CategoriesType::class)->createView()
            ]
        );
    }

    /**
     * @Route("/dashboard/categories/delete/{id}", name="delete_category")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param int $id
     * @return RedirectResponse|Response|null
     */
    public function deleteCategory(Request $request, int $id)
    {
        $currentUser = $this->userService->currentUser();
        $category = $this->categoriesService->getOneCategory($id);
        $form = $this->createForm(CategoriesType::class, $category);
        $form->handleRequest($request);
        if (null === $category) {
            return $this->redirectToRoute('all_categories');
        }
        if (!$currentUser->isAdmin() && !$currentUser->isAuthorCategory($category)){
            return $this->redirectToRoute('shop_index');
        }
            $this->categoriesService->delete($category);
            $this->addFlash("successfully", "Delete category successfully!");
            return $this->redirectToRoute('all_categories');
    }


    /**
     * @param \Symfony\Component\Form\FormInterface $form
     * @param array $messages
     * @return array
     */
    private function errorCollection(\Symfony\Component\Form\FormInterface $form, array $messages): array
    {
        foreach ($form->getErrors(true) as $err) {
            $messages[] = $err->getMessage();
        }
        return $messages;
    }

    /**
     * @param User|null $currentUser
     * @param array $messages
     * @return Response
     */
    private function templateView(?User $currentUser, array $messages)
    {
        return $this->render('category/add_category.html.twig',
            [
                'user' => $currentUser,
                'errors' => $messages,
                'form' => $this->createForm(CategoriesType::class)->createView()
            ]);

    }


}
