<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\ProductOperation;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TwigColumn;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\QueryBuilder;

class ProductController extends AbstractController
{
    /**
     * @Route("/panel/product/new", name="product_new")
     */
    public function newProduct(Request $request, EntityManagerInterface $em): Response
    {
        $product = new Product;
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('product');
        }

        return $this->render('product/update.html.twig', [
            'controller_name' => 'ProductController',
            'product_name' => null,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/panel/product", name="product")
     */
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create([])
            ->add('id', TextColumn::class, ['label' => 'Id', 'className' => 'bold', 'searchable' => true])
            ->add('animal_name', TextColumn::class, ['label' => 'Animal name', 'className' => 'bold', 'searchable' => true, 'field' => 'animal.animal_name', 'orderField' => 'animal.animal_name'])
            ->add('product_name', TextColumn::class, ['label' => 'Product name', 'className' => 'bold', 'searchable' => true])
            ->add('productOperations', TextColumn::class, ['label' => 'No. product operations', 'className' => 'bold', 'searchable' => true])
            ->add('actions', TwigColumn::class, ['label' => 'Actions', 'className' => 'bold', 'searchable' => true, 'template' => 'product/_partials/table/actions.html.twig'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Product::class
            ]);
            $table->handleRequest($request);
        
        
        if ($table->isCallback()) {
            return $table->getResponse();
        } 

        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
            'datatable' => $table
        ]);
    }

    /**
     * @Route("/panel/product/{id}/operations", name="product_operations")
     */
    public function viewOperations(Request $request, DataTableFactory $dataTableFactory, Product $product, $id): Response
    {
        $table = $dataTableFactory->create([])
            ->add('id', TextColumn::class, ['label' => 'id', 'className' => 'bold', 'searchable' => true])
            ->add('enter', TextColumn::class, ['label' => 'Enter', 'className' => 'bold', 'searchable' => true])
            ->add('exit', TextColumn::class, ['label' => 'Exit', 'className' => 'bold', 'searchable' => true])
            ->add('modification', TextColumn::class, ['label' => 'Modification', 'className' => 'bold', 'searchable' => true])
            ->add('shipment', TextColumn::class, ['label' => 'Shipment', 'className' => 'bold', 'searchable' => true])
            ->add('state', TextColumn::class, ['label' => 'State', 'className' => 'bold', 'searchable' => true])
            ->add('datestamp', DateTimeColumn::class, ['label' => 'Created', 'className' => 'bold', 'searchable' => true, 'format' => 'Y-m-d H:i:s'])
            ->add('actions', TwigColumn::class, ['label' => 'Actions', 'className' => 'bold', 'searchable' => true, 'template' => 'product/_partials/table/operations/actions.html.twig'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => ProductOperation::class,
                'query' => function (QueryBuilder $builder) use ($id) {
                    $builder
                        ->select('p')
                        ->from(ProductOperation::class, 'p')
                        ->where('p.product ='.$id)
                    ;
                }
            ]);
            $table->handleRequest($request); 
        
        
        if ($table->isCallback()) {
            return $table->getResponse();
        } 

        return $this->render('product/operations/index.html.twig', [
            'controller_name' => 'ProductController',
            'datatable' => $table,
            'product_name' => $product->getProductName()
        ]);
    }

    /**
     * @Route("/panel/product/{id}", name="product_update")
     */
    public function updateProduct(Request $request, Product $product, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('product');
        }

        return $this->render('product/update.html.twig', [
            'controller_name' => 'ProductController',
            'product_name' => $product->getProductName()
        ]);
    }

    /**
     * @Route("/admin/product/delete/{id}", name="product_delete")
     */
    public function deleteProduct(Product $product, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em->remove($product);
        $em->flush();

        return $this->redirectToRoute('product');
    }
}
