<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

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

    #[Route('/todo/{value}/{content}', name: 'add_todo')]
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
}
