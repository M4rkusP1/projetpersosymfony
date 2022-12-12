<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface;

class TodoController extends AbstractController
{
    #[Route('/todo', name: 'app_todo')]
    public function index(Request $request): Response
    {
        $session = $request->getSession();
        if (!$session->has('todos')) {
            $todos =[
                'achat' => 'stylos',
                'cours' => 'Finir apprendre symfony',
                'dormir' => 'Dormir 8h'
            ];
            $session = $session->set(name: 'todos', value: $todos);
            $this->addFlash(type: 'info', message: "Liste initialisée") ;
            return $this->render('todo/index.html.twig');
        }
        else {
            //$todos = $session->get('todos');
            //return $this->render('todo/index.html.twig', parameters: ['todos' => $todos]);
            return $this->render('todo/index.html.twig');
        }

    }

    #[Route('/todo/add/{value}/{content}', name: 'add_todo')]
    public function addToDo(Request $request, $value, $content) {
        $session = $request->getSession();
        if ($session->has(name: 'todos')) {
            $todos = $session->get('todos');
            if (isset($todos[$value])) {
                $this->addFlash(type: 'error', message: "Le todo d'id $value existe déjà!") ;
            }
            else {
                $todos[$value]=$content;
                $session = $session->set(name: 'todos', value: $todos);
                $this->addFlash(type: 'success', message: "Le todo d'id $value vient d'être ajouté!") ;
            }
        }
        else {
            $this->addFlash(type: 'error', message: "Liste non initialisée !") ;
        }
        return $this->redirectToRoute(route: 'app_todo');
    }

    #[Route('/todo/update/{value}/{content}', name: 'mod_todo')]
    public function modToDo(Request $request, $value, $content) {
        $session = $request->getSession();
        $todos = $session->get('todos');
        if (isset($todos[$value])) {
            $todos[$value] = $content;
            $session = $session->set(name: 'todos', value: $todos);
            $this->addFlash(type: 'success', message: "Le todo d'id $value vient d'être mis à jour!"); 
        }
        else {
            $this->addFlash(type: 'error', message: "Le todo d'id $value n'existe pas pour pouvoir être updaté!"); 
        }
        return $this->render('todo/index.html.twig');
    }


    #[Route('/todo/delete/{value}/{content}', name: 'del_todo')]
    public function delToDo(Request $request, $value, $content="") {
        $session = $request->getSession();
        $todos = $session->get('todos');
        if (isset($todos[$value])) {
            unset($todos[$value]);
            $session = $session->set(name: 'todos', value: $todos);
            $this->addFlash(type: 'success', message: "Le todo d'id $value vient d'être supprime ahahaahha!"); 
        }
        else {
            $this->addFlash(type: 'error', message: "Le todo d'id $value n'existe pas pour pouvoir être supprimé!"); 
        }
        return $this->render('todo/index.html.twig');
    }

    #[Route('/todo/reset', name: 'res_todo')]
    public function resToDo(Request $request) {
        $session = $request->getSession();
        if ($session->has('todos')) {
        $todos = $session->get('todos');
        $session= $session->remove('todos');
        // $todos= array();
            // $session = $session->set(name: 'todos', value: $todos);
            $this->addFlash(type: 'success', message: "Liste supprimée"); 
        }
        else {
            $this->addFlash(type: 'error', message: "Le todo n'existe pas pour pouvoir être supprimé!"); 
        }
        return $this->render('todo/index.html.twig');
    }
}
