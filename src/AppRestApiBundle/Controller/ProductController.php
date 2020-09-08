<?php

namespace AppRestApiBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Entity\User;
use AppBundle\Form\ProductType;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ProductController extends Controller
{
    /**
     * @Route("/products", name="rest_api_products")
     * @return Response
     */
    public function all()
    {
        $products = $this
            ->getDoctrine()
            ->getRepository(Product::class)
            ->findAll();
        $serializer = $this->container->get('jms_serializer');
        $json = $serializer->serialize($products, 'json');
        return new Response($json, Response::HTTP_OK,
            ['content-type' => 'application/json']);
    }

    /**
     * @Route("/products/{id}", name="rest_api_product", methods={"GET"})
     * @param int $id product id
     * @return Response
     */
    public function product(int $id)
    {
        $product = $this
            ->getDoctrine()
            ->getRepository(Product::class)
            ->find($id);
        if (null === $product) {
            return new Response(json_encode(['error' => 'resource not found']),
                Response::HTTP_NOT_FOUND,
                ['content-type' => 'application/json']
            );
        }
        $serializer = $this->container->get('jms_serializer');
        $productJson = $serializer->serialize($product, 'json');
        return new Response($productJson, Response::HTTP_OK,
            ['content-type' => 'application/json']);
    }

    /**
     * @Route("/products/create", name="rest_api_create_product",methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function createProduct(Request $request)
    {
        try {
            $this->createProductProcess($request);
            return new Response(null, Response::HTTP_CREATED);
        } catch (Exception $e) {
            return new Response(json_encode(['error' => $e->getMessage()]),
                Response::HTTP_BAD_REQUEST,
                ['content-type' => 'application/json']
            );
        }
    }

    /**
     * @param Request $request
     * @return Product
     * @throws Exception
     */
    private function createProductProcess(Request $request)
    {
        $product = new Product();
        $parameters = $request->request->all();
        return $this->processForm($product, $parameters, 'POST');
    }

    /**
     * @param Product $product
     * @param array $parameters
     * @param string $method
     * @return Product
     * @throws Exception
     */
    private function processForm(Product $product, array $parameters, $method = 'PUT')
    {
        foreach ($parameters as $param => $paramValue) {
            if (null === $paramValue || 0 === strlen(trim($paramValue))) {
                throw new Exception("invalid data: $param");
            }
        }
        if (!array_key_exists('authorId', $parameters)) {
            throw new Exception('invalid data: authorId');
        }
        $user = $this->getDoctrine()->getRepository(User::class)
            ->find($parameters['authorId']);
        if (null === $user) {
            throw new Exception('invalid user id');
        }
        $form = $this->createForm(ProductType::class, $product, ['method' => $method]);
        $form->submit($parameters);
        if ($form->isSubmitted()) {
            $product->setAuthor($user);
            $product->setImage($parameters['image']);
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();
            return $product;
        }
        throw new Exception('submitted data is invalid');
    }

    /**
     * @Route("/products/{id}", name="rest_api_product_edit", methods={"PUT"})
     * @param Request $request
     * @param int $id product id
     * @return Response
     */
    public function productEdit(Request $request, int $id)
    {
        try {
            $product = $this->getDoctrine() ->getRepository(Product::class)->find($id);
            if (null === $product) {
                $this->createProduct($request);
                $statusCode = Response::HTTP_CREATED;
            } else {
                $this->processForm($product, $request->request->all(),
                    'PUT');
                $statusCode = Response::HTTP_NO_CONTENT;
            }
            return new Response(null, $statusCode);

        } catch (Exception $e) {
            return new Response(json_encode(['error' => $e->getMessage()]),
                Response::HTTP_BAD_REQUEST,
                array('content-type' => 'application/json'));
        }
    }
}
