<?php

namespace App\Controller;

use App\Entity\Component;
use App\Entity\ComponentOperation;
use App\Entity\Product;
use App\Entity\Production;
use App\Entity\ProductionDetail;
use App\Entity\ProductOperation;
use App\Entity\RecipeDetail;
use App\Form\ProductionAmountType;
use App\Form\ProductionDetailType;
use App\Form\ProductionType;
use App\Form\SaveProductionType;
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
            $production->setDatestamp(new \DateTime);
            $production->setModification(false);
            $value = (float) $form->get('value')->getData();
            $em->persist($production);
            $em->flush();

            return $this->redirectToRoute('production_recipe_detalis', ['id' => $production->getId(), 'value' => $value]);

        }
        return $this->render('production/index.html.twig', [
            'controller_name' => 'ProductionController',
            'form' => $form->createView(),
            'datatable' => $table
        ]);
    }

    /**
     * @Route("/panel/production/calculation/{id}/{value}", name="production_recipe_detalis")
     */
    public function productionRecipeDetalis(
            Production $production, 
            float $value, 
            Request $request,
            EntityManagerInterface $em,
            DataTableFactory $dataTableFactory
        ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $table = $dataTableFactory->create()
            ->add('id', NumberColumn::class, ['label' => '#', 'className' => 'bold', 'searchable' => true])
            ->add('component_name', TextColumn::class, ['label' => 'Component name', 'className' => 'bold', 'searchable' => true, 'field' => 'c.component_name'])
            ->add('val', NumberColumn::class, ['label' => 'Amont', 'className' => 'bold', 'searchable' => true, 'render' => function($value, $context) { 
                if ($context['value'] > $context['state']) {
                    return "<span style='color:red'>".$context['value']."</span>";
                } else {
                    return $context['value'];
                }
            }])
            ->add('state', NumberColumn::class, ['label' => 'State', 'className' => 'bold', 'searchable' => true, 'field' => 'co.state'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => RecipeDetail::class,
                'hydrate' => Query::HYDRATE_ARRAY,
                'query' => function (QueryBuilder $builider) use ($production, $value) {
                    $builider
                        ->select('rd.id')
                        ->addSelect('c.component_name')
                        ->addSelect('co.state')
                        ->addSelect('rd.amount *'.$value.' as value')
                        ->from(RecipeDetail::class, 'rd')
                        ->leftJoin(Component::class, 'c', Join::WITH, 'c.id = rd.component')
                        ->leftJoin(ComponentOperation::class, 'co', Join::WITH, 'co.component = c.id AND NOT EXISTS (SELECT 1 FROM App\Entity\ComponentOperation p1 WHERE p1.component = c.id AND p1.id > co.id)')
                        ->groupBy('c.id');
                        
                }
            ]);
        $table->handleRequest($request); 
        
        if ($table->isCallback()) {
            return $table->getResponse();
        } 

        $updateAmountForm = $this->createForm(ProductionAmountType::class, null);
        $updateAmountForm->handleRequest($request);

        if ($updateAmountForm->isSubmitted() && $updateAmountForm->isValid()) {
            $value = $updateAmountForm->get('value')->getData();

            return $this->redirectToRoute('production_recipe_detalis', ['id' => $production->getId(), 'value' => $value]);
        }

        
        $saveProductionForm = $this->createForm(SaveProductionType::class, null);
        $updateAmountForm->handleRequest($request);

        if ($saveProductionForm->isSubmitted() && $saveProductionForm->isValid()) {
            $builider = new QueryBuilder($em);
            $builider->select('c.id')
                ->addSelect('co.state')
                ->addSelect('rd.amount *'.$value.' as value')
                ->from(RecipeDetail::class, 'rd')
                ->leftJoin(Component::class, 'c', Join::WITH, 'c.id = rd.component')
                ->leftJoin(ComponentOperation::class, 'co', Join::WITH, 'co.component = c.id AND NOT EXISTS (SELECT 1 FROM App\Entity\ComponentOperation p1 WHERE p1.component = c.id AND p1.id > co.id)')
                ->groupBy('c.id');
            $components = $builider->getQuery()->getResult(Query::HYDRATE_ARRAY);

            foreach ($components as $component) {
                $productionDetail = new ProductionDetail;
                $productionDetail->setProduction($production);
                $component_obj = $this->getDoctrine()->getRepository(Component::class)->findOneBy(['id' => $component['id']]);
                $productionDetail->setComponent($component_obj);
                $productionDetail->setValue($component['value']);
                $em->persist($productionDetail);
                $em->flush();

                $componentOperation = new ComponentOperation;
                $componentOperation->setComponent($component_obj);
                $componentOperation->setProduction((float) -$component['value']);
                $componentOperation->setProductionId($production);
                $newState = (float) $component['state'] - (float) $component['value'];
                $componentOperation->setState($newState);
                $em->persist($componentOperation);
                $em->flush();

                $productOperation = new ProductOperation;
                $productOperation->setProduct($productionDetail->getProduction()->getProduct());
                $productOperation->setProduction($value);
                $productOperation->setProductionId($productionDetail->getProduction());
                $builider = new QueryBuilder($em);
                $builider
                    ->select('po.state')
                    ->from(ProductOperation::class, 'po')
                    ->where('po.product ='.$productionDetail->getProduction()->getProduct()->getId())
                    ->orderBy('po.id', 'DESC')
                    ->getQuery();
                $state = $builider->getQuery()->setMaxResults(1)->getResult(Query::HYDRATE_ARRAY);
                $newState = ((float) $state + (float) $value)*1000;
                $productOperation->setState($newState);
                $em->persist($productOperation);
                $em->flush();

                return $this->redirectToRoute('production');
            }
        }

        return $this->render('production/calculator/index.html.twig', [
            'controller_name' => 'ProductionController',
            'recipe_name' => $production->getRecipe()->getRecipeName(),
            'product_name' => $production->getProduct()->getProductName(),
            'datatable' => $table,
            'updateAmountForm' => $updateAmountForm->createView(),
            'saveProductionForm' => $saveProductionForm->createView()
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
