<?php

namespace App\Controller;

use App\Entity\ComponentOperation;
use App\Form\ComponentDeliveryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeliveryController extends AbstractController
{
    /**
     * @Route("/panel/delivery", name="delivery")
     */
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $componentOperation = new ComponentOperation;
        $form = $this->createForm(ComponentDeliveryType::class, $componentOperation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $componentOperation->setDatestamp(new \DateTime);
            $state = (float) $this->getDoctrine()->getRepository(ComponentOperation::class)->findLastState($componentOperation->getComponent())->getState();
            $newState = $state + (float) $form->get('enter')->getData();
            $componentOperation->setState($newState);
            $em->persist($componentOperation);
            $em->flush();

            return $this->redirectToRoute('delivery');
        }

        return $this->render('delivery/index.html.twig', [
            'controller_name' => 'DeliveryController',
            'form' => $form->createView()
        ]);
    }
}
