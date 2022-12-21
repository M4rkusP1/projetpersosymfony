<?php

namespace App\Controller;

use Doctrine\ORM\EntityRepository;
use App\Entity\Personne;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class PersonneController extends AbstractController
   {   

        #[Route('/personne', name: 'app_personne')] 
                public function index(ManagerRegistry $doctrine): Response {
                $repository = $doctrine->getRepository(Personne::class);
                // dd($repository);
                $personnes = $repository->findAll();
                return $this->render('personne/index.html.twig', [
                    'personnes' => $personnes,
                    'isPaginated' => FALSE
                ]); 
                }

    

        #[Route('/personne/{id<\d+>}', name: 'app_personne_id')] 
           public function searchbyId(ManagerRegistry $doctrine, $id): Response {
            $repository = $doctrine->getRepository(Personne::class);
            // dd($repository);
            $personnes = $repository->find($id);
            // dd($personnes);
            if (!$personnes) {
                $this->addFlash('error','La personne n\'existe pas');
                return $this->redirectToRoute('app_personne');
            }
            return $this->render('personne/personneid.html.twig', [
                'personnes' => $personnes,
                ]); 
                }

        #[Route('/personne/search/{age}', name: 'app_personne_all')]
        public function searchbyName(ManagerRegistry $doctrine, $age): Response
        {
            $repository = $doctrine->getRepository(Personne::class);
            // dd($repository);
            $personnes = $repository->findBy(['age' => $age]);
            // dd($personnes);
            if (!$personnes) {
                $this->addFlash('error', 'La personne avec cet âge n\'existe pas');
                return $this->redirectToRoute('app_personne');
            }
            return $this->render('personne/personnesearch.html.twig', [
                'personnes' => $personnes,
            ]);
        }

        #[Route('/personne/all/{page?1}/{nbre?10}', name: 'app_personne_all')]
        public function searchPage(ManagerRegistry $doctrine, $nbre, $page): Response
        {
            $repository = $doctrine->getRepository(Personne::class);
            // dd($repository);
            // page 1 = 1 à 10 (limite : 10 & offset: 1)
            // page 2 = 11 à 21 (limite: 10 & offset: 10)
            // page 3 = 22 à 32 (limite: 10 & offset: 21)
            $offset = ($page - 1)*$nbre;
            // dd($offset);
            $nbrePersonnes = $repository->count([]);
            $nbrePages = ceil($nbrePersonnes / $nbre);
            // dd($nbrePages);
            $personnes = $repository->findBy([], ['age' => 'ASC'], limit: $nbre, offset: $offset);
            // dd($personnes);
            if (!$personnes) {
                $this->addFlash('error', 'La personne avec cet âge n\'existe pas');
                return $this->redirectToRoute('app_personne');
            }
            return $this->render('personne/index.html.twig', [
                'personnes' => $personnes,
                'isPaginated' => TRUE,
                'nbrePages' => $nbrePages,
                'page' => $page,
                'nbre' => $nbre
            ]);
        }
    
}
