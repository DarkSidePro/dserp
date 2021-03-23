<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Product;
use App\Entity\Shipment;
use App\Entity\ShipmentClient;
use App\Entity\ShipmentClientDetail;
use App\Form\FinishShipmentType;
use App\Form\ShipmentClientDetailType;
use App\Form\ShipmentClientType;
use App\Form\ShipmentFinishDetailType;
use App\Form\ShipmentGenerateType;
use App\Form\ShipmentType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORM\SearchCriteriaProvider;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\NumberColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\Column\TwigColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShipmentController extends AbstractController
{
    /**
     * @Route("/panel/shipment", name="shipment")
     */
    public function index(Request $request, DataTableFactory $dataTableFactory, EntityManagerInterface $em): Response
    {
        $table = $dataTableFactory->create([])
            ->add('id', NumberColumn::class, ['label' => '#', 'className' => 'bold', 'searchable' => true])
            ->add('datestamp', DateTimeColumn::class, ['label' => 'Created', 'className' => 'bold', 'searchable' => true, 'format' => 'Y-m-d H:i:s'])
            ->add('client', NumberColumn::class, ['label' => 'No. client', 'className' => 'bold', 'searchable' => true, 'render' => function($value, $context) {return $context['clients'];}])
            ->add('actions', TwigColumn::class, ['label' => 'Actions', 'className' => 'bold', 'searchable' => true, 'template' => 'shipment/_partials/table/actions.html.twig'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Shipment::class,
                'hydrate' => Query::HYDRATE_ARRAY,
                'query' => function(QueryBuilder $builider) {
                    $builider
                        ->select('s.id as id')
                        ->addSelect('s.datestamp as datestamp')
                        ->addSelect('COUNT(sc) as clients')
                        ->from(Shipment::class, 's')
                        ->leftJoin(ShipmentClient::class, 'sc', Join::WITH, 'sc.shipment = s.id')
                        ->groupBy('s.id');
                }
            ]);
        $table->handleRequest($request);
        
        if ($table->isCallback()) {
            return $table->getResponse();
        } 

        $shipment = new Shipment;
        $form = $this->createForm(ShipmentGenerateType::class, $shipment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($shipment);
            $em->flush();

            return $this->redirectToRoute('shipment_details', ['id' => $shipment->getId()]);
        }

        return $this->render('shipment/index.html.twig', [
            'controller_name' => 'ShipmentController',
            'datatable' => $table,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/panel/shipment/details/{id}", name="shipment_details")
     */
    public function shipmentDetails(
            Request $request, 
            DataTableFactory $dataTableFactory, 
            Shipment $shipment, 
            EntityManagerInterface $em
        ): Response
    {
        $table = $dataTableFactory->create([])
            ->add('id', NumberColumn::class, ['label' => '#', 'className' => 'bold', 'searchable' => true])
            ->add('client_name', TextColumn::class, ['label' => 'Client name', 'className' => 'bold', 'searchable' => true, 'field' => 'c.client_name'])
            ->add('details', NumberColumn::class, ['label' => 'No. details', 'className' => 'bold', 'searchable' => true])
            ->add('actions', TwigColumn::class, ['label' => 'Actions', 'className' => 'bold', 'searchable' => false, 'template' => 'shipment/details/_partials/table/actions.html.twig'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Shipment::class,
                'hydrate' => Query::HYDRATE_ARRAY,
                'query' => function(QueryBuilder $builider) {
                    $builider
                        ->select('sc.id as id')
                        ->addSelect('sc.modification')
                        ->addSelect('c.client_name')
                        ->addSelect('COUNT(scd.id) as details')
                        ->from(ShipmentClient::class, 'sc')
                        ->leftJoin(Client::class, 'c', Join::WITH, 'c.id = sc.client')
                        ->leftJoin(ShipmentClientDetail::class, 'scd', Join::WITH, 'scd.shipmentClient = sc.id')
                        ->groupBy('sc.id');
                },
                'criteria' => [
                    function (QueryBuilder $builder) use ($shipment) {
                        $builder->andWhere($builder->expr()->eq('sc.shipment', ':shipment'))->setParameter('shipment', $shipment->getId());
                    },
                    new SearchCriteriaProvider(),
                ]
            ]);
        $table->handleRequest($request);
        
        if ($table->isCallback()) {
            return $table->getResponse();
        } 

        $shipmentClient = new ShipmentClient;
        $form = $this->createForm(ShipmentClientType::class, $shipmentClient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $shipmentClient->setShipment($shipment);
            $em->persist($shipmentClient);
            $em->flush();

            return $this->redirectToRoute('shipment_details', ['id' => $shipment->getId()]);
        }

        $saveShipment = $this->createForm(FinishShipmentType::class, null);
        $saveShipment->handleRequest($request);

        if ($saveShipment->isSubmitted() && $saveShipment->isValid()) {
            $shipment->setModification(true);
            $em->persist($shipment);
            $em->flush();

            return $this->redirectToRoute('shipment');
        }

        return $this->render('shipment/details/index.html.twig', [
            'controller_name' => 'ShipmentController',
            'datatable' => $table,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/shipment/delete/{id}", name="shipment_remove")
     */
    public function deleteShipment(Shipment $shipment, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em->remove($shipment);
        $em->flush();

        return $this->redirectToRoute('shipment');
    }
    
    /**
     * @Route("/admin/shipment/client/remove/{id}", name="shipment_client_remove")
     */
    public function shipmentClientRemove(ShipmentClient $shipmentClient, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em->remove($shipmentClient);
        $em->flush();

        return $this->redirectToRoute('shipment_details', ['id' => $shipmentClient->getShipment()->getId()]);
    }

    /**
     * @Route("/panel/shipment/client/details/{id}", name="shipment_client_details")
     */
    public function shipmentClientDetails(
            ShipmentClient $shipmentClient, 
            EntityManagerInterface $em, 
            Request $request, 
            DataTableFactory $dataTableFactory
        ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        
        $table = $dataTableFactory->create([])
            ->add('id', NumberColumn::class, ['label' => '#', 'className' => 'bold', 'searchable' => true])
            ->add('product_name', TextColumn::class, ['label' => 'Product name', 'className' => 'bold', 'searchable' => true, 'render' => function($value, $context) { return $context['product_name'];}])
            ->add('value', NumberColumn::class, ['label' => 'Value', 'className' => 'bold', 'searchable' => true])
            ->add('actions', TwigColumn::class, ['label' => 'Actions', 'className' => 'bold', 'searchable' => true, 'template' => 'shipment/client/details/_partials/table/actions.html.twig'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => ShipmentClientDetail::class,
                'hydrate' => Query::HYDRATE_ARRAY,
                'query' => function(QueryBuilder $builider) {
                    $builider
                        ->select('c.id')
                        ->addSelect('p.product_name')
                        ->addSelect('c.value')
                        ->from(ShipmentClientDetail::class, 'c')
                        ->leftJoin('c.product', 'p');
                },
                'criteria' => [
                    function (QueryBuilder $builder) use ($shipmentClient) {
                        $builder->andWhere($builder->expr()->eq('c.shipmentClient', ':shipment'))->setParameter('shipment', $shipmentClient->getId());
                    },
                    new SearchCriteriaProvider(),
                ]
            ]);
        $table->handleRequest($request);
        
        if ($table->isCallback()) {
            return $table->getResponse();
        } 

        $shipmentClientDetail = new ShipmentClientDetail;
        $form = $this->createForm(ShipmentClientDetailType::class, $shipmentClientDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $shipmentClientDetail->setShipmentClient($shipmentClient);
            $em->persist($shipmentClientDetail);
            $em->flush();

            return $this->redirectToRoute('shipment_client_details', ['id' => $shipmentClient->getId()]);
        }

        $saveClient = $this->createForm(ShipmentFinishDetailType::class, null);
        $saveClient->handleRequest($request);

        if ($saveClient->isSubmitted() && $saveClient->isValid()) {
            $shipmentClient->setModification(true);
            $em->persist($shipmentClient);
            $em->flush();

            return $this->redirectToRoute('shipment_details', ['id' => $shipmentClient->getShipment()->getId()]);
        }

        return $this->render('shipment/client/details/index.html.twig', [
            'controller_name' => 'ShipmentController',
            'datatable' => $table,
            'form' => $form->createView(),
            'client_name' => $shipmentClient->getClient()->getClientName(),
            'id' => $shipmentClient->getShipment()->getId(),
            'save_client' => $saveClient->createView()
        ]);
    }

    /**
     * @Route("/panel/shipment/client/detail/update/{id}", name="shipment_client_detail_update")
     */
    public function shipmentClientDetailUpdate(
            ShipmentClientDetail $shipmentClientDetail, 
            Request $request, 
            EntityManagerInterface $em
        ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $form = $this->createForm(ShipmentClientDetailType::class, $shipmentClientDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($shipmentClientDetail);
            $em->flush();

            return $this->redirectToRoute('shipment_client_details', ['id' => $shipmentClientDetail->getShipmentClient()->getId()]);
        }

        return $this->render('shipment/details/detail/update.html.twig', [
            'controller_name' => 'ShipmentController',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/shipment/client/detail/remove/{id}", name="shipment_client_detail_remove")
     */
    public function shipmentClientDetailRemove(ShipmentClientDetail $shipmentClientDetail, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em->remove($shipmentClientDetail);
        $em->flush();

        return $this->redirectToRoute('shipment_client_details', ['id' => $shipmentClientDetail->getShipmentClient()->getId()]);
    }

    /**
     * @Route("/panel/shipment/update/{id}", name="shipment_update")
     */
    public function shipmentUpdate(Shipment $shipment, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ShipmentType::class, $shipment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($shipment);
            $em->flush();

            return $this->redirectToRoute('shipment');
        }

        return $this->render('shipment/update.html.twig', [
            'controller_name' => 'ShipmentController',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/panel/shipment/details/update/{id}", name="shipment_detail_update")
     */
    public function shipmentDetailUpdate(
        ShipmentClient $shipmentClient, 
        Request $request, 
        EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ShipmentClientType::class, $shipmentClient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($shipmentClient);
            $em->flush();

            return $this->redirectToRoute('shipment_details', ['id' => $shipmentClient->getShipment()->getId()]);
        }

        return $this->render('shipment/detail/update.html.twig', [
            'controller_name' => 'ShipmentController',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/panel/shipment/details/view/{id}", name="shipment_details_view")
     */
    public function shipmentDetailsView(
            Request $request, 
            DataTableFactory $dataTableFactory, 
            Shipment $shipment 
        ): Response
    {
        $table = $dataTableFactory->create([])
            ->add('id', NumberColumn::class, ['label' => '#', 'className' => 'bold', 'searchable' => true])
            ->add('client_name', TextColumn::class, ['label' => 'Client name', 'className' => 'bold', 'searchable' => true, 'render' => function($value, $context) { return $context['client_name'];}])
            ->add('detail', NumberColumn::class, ['label' => 'No. details', 'className' => 'bold', 'searchable' => true, 'render' => function($value, $context) { return $context['details'];}])
            ->add('actions', TwigColumn::class, ['label' => 'Actions', 'className' => 'bold', 'searchable' => false, 'template' => 'shipment/details/view/_partials/table/actions.html.twig'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Shipment::class,
                'hydrate' => Query::HYDRATE_ARRAY,
                'query' => function(QueryBuilder $builider) {
                    $builider
                        ->select('sc.id as id')
                        ->addSelect('c.client_name')
                        ->addSelect('COUNT(scd.id) as details')
                        ->from(ShipmentClient::class, 'sc')
                        ->leftJoin('sc.client', 'c')
                        ->leftJoin(ShipmentClientDetail::class, 'scd', Join::WITH, 'scd.shipmentClient = sc.id')
                        ->groupBy('sc.id');
                },
                'criteria' => [
                    function (QueryBuilder $builder) use ($shipment) {
                        $builder->andWhere($builder->expr()->eq('sc.shipment', ':shipment'))->setParameter('shipment', $shipment->getId());
                    },
                    new SearchCriteriaProvider(),
                ]
            ]);
        $table->handleRequest($request);
        
        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('shipment/details/view.html.twig', [
            'controller_name' => 'ShipmentController',
            'datatable' => $table
        ]);
    }
    
     /**
     * @Route("/panel/shipment/client/details/view/{id}", name="shipment_client_details_view")
     */
    public function shipmentClientDetailsView(
            ShipmentClient $shipmentClient, 
            Request $request, 
            DataTableFactory $dataTableFactory
        ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        
        $table = $dataTableFactory->create([])
            ->add('id', NumberColumn::class, ['label' => '#', 'className' => 'bold', 'searchable' => true])
            ->add('product_name', TextColumn::class, ['label' => 'Product name', 'className' => 'bold', 'searchable' => true, 'field' => 'p.product_name'])
            ->add('value', NumberColumn::class, ['label' => 'Value', 'className' => 'bold', 'searchable' => true])
            ->createAdapter(ORMAdapter::class, [
                'entity' => ShipmentClientDetail::class,
                'hydrate' => Query::HYDRATE_ARRAY,
                'query' => function(QueryBuilder $builider) {
                    $builider
                        ->select('c.id')
                        ->addSelect('p.product_name')
                        ->addSelect('c.value')
                        ->from(ShipmentClientDetail::class, 'c')
                        ->leftJoin(Product::class, 'p', Join::WITH, 'c.product = p.id');
                },
                'criteria' => [
                    function (QueryBuilder $builder) use ($shipmentClient) {
                        $builder->andWhere($builder->expr()->eq('c.shipmentClient', ':shipment'))->setParameter('shipment', $shipmentClient->getId());
                    },
                    new SearchCriteriaProvider(),
                ]
            ]);
        $table->handleRequest($request);
        
        if ($table->isCallback()) {
            return $table->getResponse();
        } 

        return $this->render('shipment/client/details/view.html.twig', [
            'controller_name' => 'ShipmentController',
            'datatable' => $table,
            'id' => $shipmentClient->getShipment()->getId(),
            'client_name' => $shipmentClient->getClient()->getClientName()
        ]);
    }
}
