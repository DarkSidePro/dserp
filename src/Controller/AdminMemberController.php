<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\AdminUserType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TwigColumn;
use Omines\DataTablesBundle\Column\DateTimeColumn;

class AdminMemberController extends AbstractController
{
    protected $datatableFactory;

    public function __construct(DataTableFactory $datatableFactory)
    {
        $this->datatableFactory = $datatableFactory;
    }

    /**
     * @Route("/admin/members", name="admin_member")
     */
    public function index(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $table = $this->datatableFactory->create([])
        ->add('id', TextColumn::class, ['label' => '#', 'className' => 'bold', 'searchable' => true])
        ->add('firstname', TextColumn::class, ['label' => 'Firstname', 'className' => 'bold', 'searchable' => true])
        ->add('lastname', TextColumn::class, ['label' => 'Lastname', 'className' => 'bold', 'searchable' => true])
        ->add('email', TextColumn::class, ['label' => 'Email', 'className' => 'bold', 'searchable' => true])
        ->add('registred', DateTimeColumn::class, ['label' => 'Registred', 'className' => 'bold', 'searchable' => true, 'format' => 'Y-m-d'])
        ->add('actions', TwigColumn::class, [
            'className' => 'id',
            'template' => 'admin_member/_partials/table/actions.html.twig',
            'label' => 'Actions',
            'searchable' => false

        ])
        ->createAdapter(ORMAdapter::class, [
            'entity' => User::class
        ]);
        $table->handleRequest($request);
    
    
        if ($table->isCallback()) {
            return $table->getResponse();
        }  

        return $this->render('admin_member/index.html.twig', [
            'controller_name' => 'AdminMemberController',
            'datatable' => $table
        ]);
    }

    /**
     * @Route("/admin/member/update/{id}", name="admin_member_update")
     */
    public function update(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder, User $user): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $form = $this->createForm(AdminUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $role = $form->get('role')->getData();

            if ($role == 0) {
                $user->setRoles(['ROLE_USER']);
            } elseif ($role == 1) {
                $user->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
            } elseif ($role == 2) {
                $user->setRoles(array('ROLE_SUPER_ADMIN'));
            }

            $user->setRegistred(new \DateTime);

            $em->persist($user);
            $em->flush();
        }

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/admin/member/new", name="admin_member_add")
     */
    public function add(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $user = new User;
        $form = $this->createForm(AdminUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $role = $form->get('role')->getData();
            $role = $role['0'];

            if ($role == 0) {
                $user->setRoles(['ROLE_USER']);
            } elseif ($role == 1) {
                $user->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
            } elseif ($role == 2) {
                $user->setRoles(array('ROLE_SUPER_ADMIN'));
            }

            $user->setRegistred(new \DateTime);

            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('admin_member');
        }

        return $this->render('admin_member/new/index.html.twig', [
            'controller_name' => 'AdminController',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/member/delete/{id}", name="admin_member_remove")
     */
    public function deleteUser(User $user, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        
        $em->remove($user);
        $em->flush();
        
        return $this->redirectToRoute('admin_member');
    }

    /**
     * @Route("/admin/member/update/{id}", name="admin_member_update")
     */
    public function updateMember(
            User $user, 
            EntityManagerInterface $em, 
            Request $request, 
            UserPasswordEncoderInterface $passwordEncoder
        ): Response
    {
        $form = $this->createForm(AdminUserType::class, $user);
        $form->handleRequest($request);
 
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $role = $form->get('role')->getData();
            $role = $role['0'];

            if ($role == 0) {
                $user->setRoles(['ROLE_USER']);
            } elseif ($role == 1) {
                $user->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
            } elseif ($role == 2) {
                $user->setRoles(array('ROLE_SUPER_ADMIN'));
            }

            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('admin_member_update', ['id' => $user->getId()]);
        }

        return $this->render('admin_member/new/update.html.twig', [
            'controller_name' => 'AdminController',
            'form' => $form->createView()
        ]);
    }
}
