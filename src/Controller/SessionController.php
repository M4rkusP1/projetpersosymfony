<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class SessionController extends AbstractController
{
    #[Route('/session', name: 'app_session')]
    public function index(Request $request): Response
    {
        $session = $request->getSession();
        if ($session->has('NombreVisites')) {
            $nbreV = $session->get('NombreVisites') + 1;
        }
        else {
            $nbreV = 1;
        }
        $session->set('NombreVisites', $nbreV);
        //dd($session);
        return $this->render('session/index.html.twig', [
            'nbreV' => $nbreV,
        ]);
    }

    #[Route('/reset', name: 'reset_session')]
    public function reset(Request $req2): Response
    {
        $session = $req2->getSession();
        if ($session->has('NombreVisites')) {
            $nbreV = 0;
        }
        else {
            echo "vous n'avez pas de nombre de visites";
        }
        $session->set('NombreVisites', $nbreV);
        //dd($session);
        return $this->render('session/index.html.twig', [
            'nbreV' => $nbreV,
        ]);
    }
}
