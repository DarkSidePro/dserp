<?php 
    namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class MemberGenerator
    {
        private $em;

        public function __construct(EntityManagerInterface $em)
        {
            $this->em = $em;
        }
        public function addMember(): Response
        {
            return;
        }

        public function updateMember(User $user): Response
        {
            return;
        }

        public function deleteMember(User $user): Response
        {
            return;
        }

    }