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
    public function index(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Personne::class);
        // dd($repository);
        $personnes = $repository->findAll();
        return $this->render('personne/index.html.twig', [
            'personnes' => $personnes,
        ]);
    }
}
