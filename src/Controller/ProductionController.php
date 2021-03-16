<?php

namespace App\Controller;

use App\Entity\Component;
use App\Entity\Production;
use App\Entity\ProductionDetail;
use App\Form\ProductionDetailType;
use App\Form\ProductionType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\BoolColumn;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\MapColumn;
use Omines\DataTablesBundle\Column\NumberColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\Column\TwigColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductionController extends AbstractController
{
    /**
     * @Route("/panel/production", name="production")
     */
    public function index(Request $request, EntityManagerInterface $em, DataTableFactory $dataTableFactory): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $table = $dataTableFactory->create([])
            ->add('id', TextColumn::class, ['label' => '#', 'className' => 'bold', 'searchable' => true])
            ->add('modification', BoolColumn::class, ['label' => 'Modification', 'className' => 'bold', 'searchable' => true, 'trueValue' => 'Yes', 'falseValue' => 'No', 'nullValue' => ''])
            ->add('datestamp', DateTimeColumn::class, ['label' => 'Created', 'className' => 'bold', 'searchable' => true, 'format' => 'Y-m-d'])
            ->add('product_name', TextColumn::class, ['label' => 'Product name', 'className' => 'bold', 'searchable' => true, 'field' => 'recipe.recipe_name', 'orderField' => 'product.product_name'])
            ->add('recipe_name', TextColumn::class, ['label' => 'Recipe name', 'className' => 'bold', 'searchable' => true, 'field' => 'product.product_name', 'orderField' => 'recipe.recipe_name'])
            ->add('actions', TwigColumn::class, ['label' => 'Actions', 'className' => 'bold', 'searchable' => true, 'template' => 'production/_partials/table/actions.html.twig'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Production::class,
            ]);
        $table->handleRequest($request); 
        
        
        if ($table->isCallback()) {
            return $table->getResponse();
        } 

        $production = new Production;
        $form = $this->createForm(ProductionType::class, $production);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($production);
            $em->flush();
        }
        return $this->render('production/index.html.twig', [
            'controller_name' => 'ProductionController',
            'form' => $form->createView(),
            'datatable' => $table
        ]);
    }

    /**
     * @Route("/panel/production/details/{id}", name="production_operations")
     */
    public function productOperations(Request $request, EntityManagerInterface $em, Production $production, DataTableFactory $dataTableFactory): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $table = $dataTableFactory->create([])
            ->add('id', NumberColumn::class, ['label' => '#', 'className' => 'bold', 'searchable' => true])
            ->add('component_name', TextColumn::class, ['label' => 'Component name', 'className' => 'bold', 'searchable' => true, 'field' => 'c.component_name'])
            ->add('value', NumberColumn::class, ['label' => 'Value', 'className' => 'bold', 'searchable' => true])
            ->add('actions', TwigColumn::class, ['label' => 'Actions', 'className' => 'bold', 'searchable' => true, 'template' => 'production/details/_partials/table/actions.html.twig'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => ProductionDetail::class,
                'hydrate' => Query::HYDRATE_ARRAY,
                'query' => function(QueryBuilder $builider) {
                    $builider
                        ->select('pd.id')
                        ->addSelect('pd.value')
                        ->addSelect('c.component_name')
                        ->from(ProductionDetail::class, 'pd')
                        ->leftJoin(Component::class, 'c', Join::WITH, 'c.id = pd.component');
                },
                'criteria' => [
                    function (QueryBuilder $builider) use ($production) {
                        $builider->andWhere($builider->expr()->eq('pd.production', ':production'))->setParameter('production', $production->getId());
                    },

                ]
            ]);
        $table->handleRequest($request); 
        
        if ($table->isCallback()) {
            return $table->getResponse();
        } 

        $productionDetail = new ProductionDetail;
        $form = $this->createForm(ProductionDetailType::class, $productionDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productionDetail->setProduction($production);
            $em->persist($productionDetail);
            $em->flush();
            
            return $this->redirectToRoute('production_operations', ['id' => $production->getId()]);
        }

        return $this->render('production/index.html.twig', [
            'controller_name' => 'ProductionController',
            'form' => $form->createView(),
            'datatable' => $table,
        ]);
    }

    /**
     * @Route("/panel/production/update/{id}", name="production_update")
     */
    public function productionUpdate(Production $production, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ProductionType::class, $production);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($production);
            $em->flush();

            return $this->redirectToRoute('production');
        }

        return $this->render('production/update.html.twig', [
            'controller_name' => 'ProductionController',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/production/remove/{id}", name="production_remove")
     */
    public function productionRemove(Production $production, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $em->remove($production);
        $em->flush();

        return $this->redirectToRoute('production');
    }

    /**
     * @Route("/panel/production/detail/update/{id}", name="production_detail_update")
     */
    public function productionDetailUpdate(ProductionDetail $productionDetail, EntityManagerInterface $em, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(ProductionDetailType::class, $productionDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($productionDetail);
            $em->flush();

            return $this->redirectToRoute('production_operations', ['id' => $productionDetail->getProduction()]);
        }

        return $this->render('production/detail/update.html.twig', [
            'controller_name' => 'ProductionController',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/production/detail/remove/{id}", name="production_detail_remove")
     */
    public function productionDetailRemove(ProductionDetail $productionDetail, EntityManagerInterface $em):Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $em->remove($productionDetail);
        $em->flush();

        return $this->redirectToRoute('production_operations', ['id' => $productionDetail->getProduction()]);
    }
}
