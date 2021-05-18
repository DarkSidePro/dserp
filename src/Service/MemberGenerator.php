<?php 
    namespace App\Service;

    use App\Entity\User;
    use Doctrine\ORM\EntityManagerInterface;
    use Symfony\Component\Form\FormInterface;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class MemberGenerator
    {
        private $em;

        public function __construct(EntityManagerInterface $em)
        {
            $this->em = $em;
        }
        public function addMember(FormInterface $form, User $user): User
        {
            
            return $user;
        }

        public function updateMember(FormInterface $form, User $user): User
        {
            $em = $this->em;
            $passwordEncoder = new UserPasswordEncoderInterface;
            
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
                $user->setRoles([('ROLE_SUPER_ADMIN')]);
            }

            $user->setRegistred(new \DateTime);

            $em->persist($user);
            $em->flush();
            
            return $user;
        }

        public function deleteMember(User $user): User
        {
            $em = $this->em;
            $em->remove($user);
            $em->flush();

            return $user;
        }

    }