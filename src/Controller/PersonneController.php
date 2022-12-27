<?php

namespace App\Controller;

use Doctrine\ORM\EntityRepository;
use App\Entity\Personne;
use App\Form\PersonneType;
use App\Repository\PersonneRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;


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
            $personnes = $repository->findBy([], ['id' => 'ASC'], limit: $nbre, offset: $offset);
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

        #[Route('/personne/delete/{id<\d+>}', name: 'app_personne_delete')]
        public function deletePersonne(ManagerRegistry $doctrine, $id): RedirectResponse {
            $entityManager = $doctrine->getManager();
            $personne = $entityManager->getRepository(Personne::class)->find($id);
            if ($personne) {
                $entityManager->remove($personne);
            $entityManager->flush();
            $this->addFlash('success', 'La personne '.$personne->getName().' a été supprimé avec succès');
            }
            else {
                $this->addFlash('error', 'La personne n\'existe pas et ne peut donc pas être supprimée');
            }
            // dd($personne->getName());
            
            return $this->redirectToRoute('app_personne_all');
        }

        #[Route('/personne/maj/{id<\d+>}', name: 'app_personne_maj')]
        public function majPersonne(ManagerRegistry $doctrine, $id, Personne $personne = null, Request $request, SluggerInterface $slugger): Response {
        $repository = $doctrine->getManager();
           $form = $this->createForm(PersonneType::class, $personne);
           $form->handleRequest($request);
           if ($form->isSubmitted() && $form->isValid()) {
            
            $brochureFile = $form->get('image')->getData();
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $personne->setImage($newFilename);

            }
            $repository->persist($personne);
            $repository->flush();
            $this->addFlash('success', 'La personne '.$personne->getName().' a été mise à jour avec succès');
            return $this->redirectToRoute('app_personne_all');
           } 
           else {
            $this->addFlash('error', 'Faites attention aux données que vous rentrez lorsque vous corrigez un profil');            
            return $this->render('personne/addform.html.twig', [
                'formP' => $form->createView(),
            ]);}
        }

        #[Route('/personne/age/{ageMin<\d+>}/{ageMax<\d+>}', name: 'app_personne_age')]
        public function agePersonne(ManagerRegistry $doctrine, Personne $personne = null, $ageMin, $ageMax): Response {
            $repository = $doctrine->getRepository(Personne::class);
            // $personne = $entityManager->getRepository(Personne::class)->find($id);
           $personnes = $repository->findByAge($ageMin, $ageMax);
            // dd($entityManager);
            $this->addFlash('success', 'Voilà');        
            // dd($personne->getName());
            
            return $this->render('personne/index.html.twig', [
                'personnes' => $personnes,
                'isPaginated' => FALSE,
                // 'nbrePages' => 2,
                // 'page' => 1,
                // 'nbre' => 10
            ]);
        }

        #[Route('/personne/add', name: 'app_add')]
        public function addPersonne(ManagerRegistry $doctrine, Personne $personne = null, Request $request, SluggerInterface $slugger): Response {
            $repository = $doctrine->getManager();
           $personne = new Personne();
           $form = $this->createForm(PersonneType::class, $personne);
           $form->handleRequest($request);

           if ($form->isSubmitted() && $form->isValid()) {
            $brochureFile = $form->get('image')->getData();

            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $personne->setImage($newFilename);

            }
            $repository->persist($personne);
            $repository->flush();
            $this->addFlash('success', 'La personne '.$personne->getName().' a été ajoutée avec succès');
            return $this->redirectToRoute('app_personne_all');
           }

           else {
                return $this->render('personne/addform.html.twig', [
                                'formP' => $form->createView(),
                            ]);
           }
            
        }
    
}
