<?php

namespace App\Controller;

use App\Entity\Component;
use App\Entity\ComponentOperation;
use App\Entity\Production;
use App\Entity\Shipment;
use App\Form\ComponentOperationType;
use App\Form\ComponentType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\Column\TwigColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\FetchJoinORMAdapter;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\NumberColumn;
use Symfony\Component\Validator\Constraints\Date;

class ComponentController extends AbstractController
{
    /**
     * @Route("/panel/component", name="component")
     */
    public function index(Request $request, DataTableFactory $dataTableFactory, EntityManagerInterface $em): Response
    {
        $table = $dataTableFactory->create([])
            ->setMethod(Request::METHOD_POST)
            ->add('id', TextColumn::class, ['label' => '#', 'className' => 'bold', 'searchable' => true, 'field' => 'c.id'])
            ->add('component_name', TextColumn::class, ['label' => 'Component name', 'className' => 'bold', 'searchable' => true, 'field' => 'c.component_name'])
            ->add('state', TextColumn::class, ['label' => 'State', 'field' => 'co.state'])
            ->add('actions', TwigColumn::class, ['label' => 'Actions', 'className' => 'bold', 'searchable' => true, 'template' => 'component/_partials/table/actions.html.twig'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Component::class,
                'hydrate' => Query::HYDRATE_ARRAY,
                'query' => function (QueryBuilder $builder) {
                    $builder->select('c.id');
                    $builder->addSelect('c.component_name');
                    $builder->addSelect('co.state');
                    $builder->from(Component::class, 'c');
                    $builder->leftJoin(ComponentOperation::class, 'co', Join::WITH, 'co.component = c.id AND NOT EXISTS (SELECT 1 FROM App\Entity\ComponentOperation p1 WHERE p1.component = c.id AND p1.id > co.id)'); 
                    $builder->groupBy('c.id');
                }
            ]);
        $table->handleRequest($request);
        
        if ($table->isCallback()) {
            return $table->getResponse();
        } 

        $component = new Component;
        $form = $this->createForm(ComponentType::class, $component);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($component);
            $em->flush();
        }

        return $this->render('component/index.html.twig', [
            'controller_name' => 'ComponentController',
            'datatable' => $table,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/panel/component/operations/{id}", name="component_view")
     */
    public function componentViewOperations(
        Request $request, DataTableFactory $dataTableFactory, EntityManagerInterface $em, Component $component): Response
    {
        $table = $dataTableFactory->create([])
            ->add('id', NumberColumn::class, ['label' => '#', 'searchable' => true])
            ->add('enter', NumberColumn::class, ['label' => 'Enter', 'searchable' => true])
            //->add('dispatch', NumberColumn::class, ['label' => 'Dispatch', 'searchable' => true])
            ->add('modification', NumberColumn::class, ['label' => 'Modification', 'searchable' => true])
            ->add('production', NumberColumn::class, ['label' => 'Production', 'searchable' => true])
            //->add('shipment', NumberColumn::class, ['label' => 'Shipment', 'searchable' => true])
            ->add('state', NumberColumn::class, ['label' => 'State', 'searchable' => true])
            ->add('datestamp', DateTimeColumn::class, ['label' => 'Created', 'searchable' => true, 'format' => 'Y-m-d'])
            ->add('actions', TwigColumn::class, ['template' => 'component/operations/_partials/table/actions.html.twig', 'label' => '#', 'searchable' => false])
            ->createAdapter(ORMAdapter::class, [
                'entity' => ComponentOperation::class,
                'hydrate' => Query::HYDRATE_ARRAY,
                'query' => function (QueryBuilder $builder) {
                    $builder->select('co');
                    $builder->from(ComponentOperation::class, 'co');
                    $builder->leftJoin('co.component', 'c');
                    $builder->addSelect('c');
                    $builder->leftJoin('co.production_id', 'p');
                    $builder->addSelect('p');
                    $builder->leftJoin('co.shipment_id', 's');
                    $builder->addSelect('s');
                },
            ]);
        $table->handleRequest($request);
        
        if ($table->isCallback()) {
            return $table->getResponse();
        } 

        $componentOperation = new ComponentOperation;
        $form = $this->createForm(ComponentOperationType::class, $componentOperation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $componentOperation->setComponent($component);
            $componentOperation->setDatestamp(new \DateTime);
            $em->persist($componentOperation);
            $em->flush();

            return $this->redirectToRoute('component');
        }

        return $this->render('component/operations/index.html.twig', [
            'controller_name' => 'ComponentController',
            'datatable' => $table,
            'form' => $form->createView(),
            'component_name' => $component->getComponentName()
        ]);
    }

    /**
     * @Route("/admin/component/remove/{id}", name="component_remove")
     */
    public function componentRemove(Component $component, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $em->remove($component);
        $em->flush();

        return $this->redirectToRoute('component');
    }

    /**
     * @Route("/panel/component/update/{id}", name="component_update")
     */
    public function componentUpdate(Component $component, EntityManagerInterface $em, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $form = $this->createForm(ComponentType::class, $component);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($component);
            $em->flush();

            return $this->redirectToRoute('component');
        }

        return $this->render('component/update.html.twig', [
            'controller_name' => 'ComponentController',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/component/operation/remove/{id}", name="component_operation_remove")
     */
    public function componentOperationRemove(ComponentOperation $componentOperation, EntityManagerInterface $em):Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $id = $componentOperation->getComponent()->getId();
        $em->remove($componentOperation);
        $em->flush();

        return $this->redirectToRoute('component_view', ['id', $id]);
    }

    /**
     * @Route("/component/operation/update/{id}", name="component_operation_update")
     */
    public function componentOperationUpdate(Request $request, ComponentOperation $componentOperation, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $form = $this->createForm(ComponentOperationType::class, $componentOperation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $component_id = $componentOperation->getComponent()->getId();
            $em->persist($componentOperation);
            $em->flush();

            return $this->redirectToRoute('component_view', ['id' => $component_id]);
        }

        return $this->render('component/operation/update.html.twig', [
            'controller_name' => 'ComponentController',
            'form' => $form->createView()
        ]);
    }

}
