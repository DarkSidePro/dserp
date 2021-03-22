<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Entity\Product;
use App\Entity\Production;
use App\Entity\ProductOperation;
use App\Entity\Shipment;
use App\Form\ProductOperationType;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;
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
use Omines\DataTablesBundle\Adapter\Doctrine\ORM\SearchCriteriaProvider;
use Omines\DataTablesBundle\Column\NumberColumn;

class ProductController extends AbstractController
{
    /**
     * @Route("/panel/product", name="product")
     */
    public function index(Request $request, DataTableFactory $dataTableFactory, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $table = $dataTableFactory->create([])
            ->add('id', NumberColumn::class, ['label' => '#', 'className' => 'bold', 'searchable' => true])
            ->add('product_name', TextColumn::class, ['label' => 'Product name', 'className' => 'bold', 'searchable' => true])
            ->add('animal_name2', TextColumn::class, ['label' => 'Animal name', 'className' => 'bold', 'searchable' => true, 'render' => function($value, $context) { return $context['animal_name'];}])
            ->add('operations', NumberColumn::class, ['label' => 'No. operations', 'className' => 'bold', 'searchable' => true])
            ->add('actions', TwigColumn::class, ['label' => 'Actions', 'className' => 'bold', 'searchable' => true, 'template' => 'product/_partials/table/actions.html.twig'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Product::class,
                'hydrate' => Query::HYDRATE_ARRAY,
                'query' => function (QueryBuilder $builder) {
                    $builder
                        ->select('p.id')
                        ->addSelect('p.product_name')
                        ->addSelect('a.animal_name')
                        ->addSelect('COUNT(po.id) as operations')
                        ->from(Product::class, 'p')
                        ->leftJoin(Animal::class, 'a', Join::WITH, 'p.animal = a.id')
                        ->leftJoin(ProductOperation::class, 'po', Join::WITH, 'p.id = po.product')
                        ->groupBy('p.id');
                }
            ]);
            $table->handleRequest($request);
        
        
        if ($table->isCallback()) {
            return $table->getResponse();
        } 

        $product = new Product;
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('product');
        }

        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
            'datatable' => $table,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/panel/product/operations/{id}", name="product_operations")
     */
    public function viewOperations(
            Request $request, 
            DataTableFactory $dataTableFactory, 
            Product $product, 
            EntityManagerInterface $em
        ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        
        $table = $dataTableFactory->create([])
            ->add('id', NumberColumn::class, ['label' => '#', 'className' => 'bold', 'searchable' => true])
            ->add('enter', NumberColumn::class, ['label' => 'Enter', 'className' => 'bold', 'searchable' => true])
            ->add('dispatch', NumberColumn::class, ['label' => 'Dispatch', 'className' => 'bold', 'searchable' => true])
            ->add('modification', NumberColumn::class, ['label' => 'Modification', 'className' => 'bold', 'searchable' => true])
            ->add('shipment', NumberColumn::class, ['label' => 'Shipment', 'className' => 'bold', 'searchable' => true])
            ->add('state', NumberColumn::class, ['label' => 'State', 'className' => 'bold', 'searchable' => true])
            ->add('datestamp', DateTimeColumn::class, ['label' => 'Created', 'className' => 'bold', 'searchable' => true, 'format' => 'Y-m-d H:i:s'])
            ->add('actions', TwigColumn::class, ['label' => 'Actions', 'className' => 'bold', 'searchable' => true, 'template' => 'product/operations/_partials/table/actions.html.twig'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => ProductOperation::class,
                'hydrate' => Query::HYDRATE_ARRAY,
                'query' => function (QueryBuilder $builder) use ($product) {
                    $builder->select('po.id');
                    $builder->addSelect('po.enter');
                    $builder->addSelect('po.modification');
                    $builder->addSelect('po.production');
                    $builder->addSelect('po.state');
                    $builder->addSelect('po.datestamp');
                    $builder->addSelect('s.id as shipment_id');
                    $builder->addSelect('p.id as production_id');
                    $builder->from(ProductOperation::class, 'po');
                    $builder->leftJoin(Shipment::class, 's', Join::WITH, 's.id = po.shipment_id');
                    $builder->leftJoin(Production::class, 'p', Join::WITH, 'p.id = po.production_id');
                    
                },
                'criteria' => [
                    function (QueryBuilder $builder) use ($product) {
                        $builder->andWhere($builder->expr()->eq('po.product', ':product'))->setParameter('product', $product->getId());
                    },
                    new SearchCriteriaProvider(),
                ]
            ]);
            $table->handleRequest($request); 
        
        
        if ($table->isCallback()) {
            return $table->getResponse();
        } 

        $productOperation = new ProductOperation;
        $form = $this->createForm(ProductOperationType::class, $productOperation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mod = $form->get('modification')->getData();
            
            $builider = new QueryBuilder($em);
            $builider->select('po.state');
            $builider->from(Product::class, 'p');
            $builider->leftJoin(ProductOperation::class, 'po', Join::WITH, 'po.product = p.id AND NOT EXISTS (SELECT 1 FROM App\Entity\ProductOperation p1 WHERE p1.product = p.id AND p1.id > po.id)'); 
            $builider->groupBy('po.id');
            $builider->where('po.product = '.$product->getId());
            $oldState = $builider->getQuery()->setMaxResults(1)->getResult(Query::HYDRATE_ARRAY);
            $oldState = $oldState['0']['state'];

            $newState = $oldState + $mod;

            if ($newState < 0) {
                return $this->redirectToRoute('product_operations', ['id' => $product->getId()]);
            } else {
                $productOperation->setState($newState);
            }
            
            $productOperation->setProduct($product);
            $productOperation->setDatestamp(new \DateTime);
            $em->persist($productOperation);
            $em->flush();

            return $this->redirectToRoute('product_operations', ['id' => $product->getId()]);
        }

        return $this->render('product/operations/index.html.twig', [
            'controller_name' => 'ProductController',
            'datatable' => $table,
            'product_name' => $product->getProductName(),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/panel/product/update/{id}", name="product_update")
     */
    public function updateProduct(Request $request, Product $product, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('product');
        }

        return $this->render('product/update.html.twig', [
            'controller_name' => 'ProductController',
            'product_name' => $product->getProductName(),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/product/delete/{id}", name="product_remove")
     */
    public function deleteProduct(Product $product, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em->remove($product);
        $em->flush();

        return $this->redirectToRoute('product');
    }

    /**
     * @Route("/panel/product/operation/update/{id}", name="product_operation_update")
     */
    public function productOperationUpdate(
            ProductOperation $productOperation, 
            Request $request, 
            EntityManagerInterface $em
        ): Response
    {
        $form = $this->createForm(ProductOperationType::class, $productOperation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($productOperation);
            $em->flush();

            return $this->redirectToRoute('product_operations', ['id' => $productOperation->getProduct()->getId()]);
        }

        return $this->render('product/operation/update.html.twig', [
            'controller_name' => 'ProductController',
            'form' => $form->createView(),
            'product_name' => $productOperation->getProduct()->getProductName()
        ]);
    }

    /**
     * @Route("/admin/product/operation/remove/{id}", name="product_operation_remove")
     */
    public function produuctOperationRemove(ProductOperation $productOperation, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em->remove($productOperation);
        $em->flush();

        return $this->redirectToRoute('product_operations', ['id' => $productOperation->getProduct()->getId()]);
    }
}
