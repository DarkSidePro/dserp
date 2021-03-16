<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Form\AnimalType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TwigColumn;
use Symfony\Component\HttpFoundation\Request;

class AnimalController extends AbstractController
{
    /**
     * @Route("/panel/animal", name="animal")
     */
    public function index(Request $request, DataTableFactory $dataTableFactory, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $table = $dataTableFactory->create([])
            ->setMethod(Request::METHOD_GET)
            ->setName('animal')
            ->add('id', TextColumn::class, ['label' => '#', 'className' => 'bold', 'searchable' => true])
            ->add('animal_name', TextColumn::class, ['label' => 'Animal name', 'className' => 'bold', 'searchable' => true])
            ->add('actions', TwigColumn::class, ['label' => 'Actions', 'className' => 'bold', 'searchable' => false, 'template' => 'animal/_partials/table/actions.html.twig'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Animal::class
            ]);
        $table->handleRequest($request);
        
        if ($table->isCallback()) {
            return $table->getResponse();
        } 

        $animal = new Animal();
        $form = $this->createForm(AnimalType::class, $animal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($animal);
            $em->flush();
        }

        return $this->render('animal/index.html.twig', [
            'controller_name' => 'AnimalController',
            'datatable' => $table,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/panel/animal/update/{id}", name="animal_update")
     */
    public function animalUpdate(Request $request, Animal $animal, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(AnimalType::class, $animal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($animal);
            $em->flush();
        }

        return $this->render('animal/update.html.twig', [
            'controller_name' => 'AnimalController',
            'form' => $form->createView(),
            'animal_name' => $animal->getAnimalName()
        ]);
    }

    /**
     * @Route("/admin/animal/delete/{id}", name="animal_remove")
     */
    public function deleteAnimal(Animal $animal, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em->remove($animal);
        $em->flush();

        return $this->redirectToRoute('animal');
    }
}
