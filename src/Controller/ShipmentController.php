<?php

namespace App\Controller;

use App\Entity\Shipment;
use Doctrine\ORM\EntityManagerInterface;
use Omines\DataTablesBundle\Column\DateTimeColumn;
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
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create([])
            ->add('id', TextColumn::class, ['label' => 'Id', 'className' => 'bold', 'searchable' => true])
            ->add('datestamp', DateTimeColumn::class, ['label' => 'Id', 'className' => 'bold', 'searchable' => true])
            ->add('clients', TextColumn::class, ['label' => 'No. client', 'className' => 'bold', 'searchable' => true])
            ->add('componentOperations', TextColumn::class, ['label' => 'No. component op.', 'className' => 'bold', 'searchable' => true])
            ->add('productOperations', TextColumn::class, ['label' => 'No. product op.', 'className' => 'bold', 'searchable' => true])
            ->add('actions', TwigColumn::class, ['label' => 'Id', 'className' => 'bold', 'searchable' => true])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Shipment::class
            ]);
        $table->handleRequest($request);
        
        if ($table->isCallback()) {
            return $table->getResponse();
        } 

        return $this->render('shipment/index.html.twig', [
            'controller_name' => 'ShipmentController',
            'datatable' => $table
        ]);
    }

    /**
     * @Route("panel/shipment/details/{id}", name="shipment_details")
     */
    public function shipmentDetails(Request $request, DataTableFactory $dataTableFactory): Response
    {

    }

    /**
     * @Route("panel/shipment/delete/{id}", name="shipment_delete")
     */
    public function deleteShipment(Request $request, EntityManagerInterface $em): Response
    {

    }
}
