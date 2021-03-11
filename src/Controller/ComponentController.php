<?php

namespace App\Controller;

use App\Entity\Component;
use App\Entity\ComponentOperation;
use App\Form\ComponentOperationType;
use App\Form\ComponentType;
use Doctrine\ORM\EntityManagerInterface;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\Column\TwigColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;

class ComponentController extends AbstractController
{
    /**
    * Holds a private string
    * @var string
    */
    private $tmpId;

    /**
     * @Route("/panel/component", name="component")
     */
    public function index(Request $request, DataTableFactory $dataTableFactory, EntityManagerInterface $em): Response
    {
        $table = $dataTableFactory->create([])
            ->setMethod(Request::METHOD_POST)
            ->add('id', TextColumn::class, ['label' => 'Id', 'className' => 'bold', 'searchable' => true])
            ->add('component_name', TextColumn::class, ['label' => 'Component name', 'className' => 'bold', 'searchable' => true])
            ->add('actions', TwigColumn::class, ['label' => 'Actions', 'className' => 'bold', 'searchable' => true, 'template' => 'component/_partials/table/actions.html.twig'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Component::class
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
        Request $request, 
        $id, 
        DataTableFactory $dataTableFactory, 
        EntityManagerInterface $em
        ): Response
    {
        $this->tmpId = $id;
        $table = $dataTableFactory->create([])
            ->add('id', TextColumn::class);

        $componentOperation = new ComponentOperation;
        $form = $this->createForm(ComponentOperationType::class, $componentOperation);

        return $this->render('component/operations/index.html.twig', [
            'controller_name' => 'ComponentController',
            'datatable' => $table,
            'form' => $form->createView()
        ]);
    }
}
