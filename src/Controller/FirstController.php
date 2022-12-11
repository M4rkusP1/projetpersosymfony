<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class FirstController extends AbstractController
{
    #[Route('/sayHello', name: 'sayHello')]
    public function sayHello(): Response
    {
        $rand = rand(0, 5);
        echo $rand;
        if ($rand === 4) {return $this->forward(controller: 'App\Controller\FirstController::index'); }
        return $this->render(view:'first/hello.html.twig');

    }
    
    #[Route('/first', name: 'app_first')]
    public function index(): Response
    {
        return $this->render(view:'first/index.html.twig', parameters:['name'=>'Arthur','surname'=>'Prince']);

    }

    #[Route('/param/{param1}/{param2}', name: 'param')]
    public function parameter(Request $request, $param1, $param2 = ""): Response
    {
        dd($request);
        return $this->render(view:'first/hello.html.twig', parameters:[
            'param1'=>$param1,
            'param2' =>$param2
        
        ]);

    }
}
