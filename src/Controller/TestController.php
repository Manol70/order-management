<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/testControler', name: 'app_show')]
    public function show()
    {
        $answers = [
            'Make sure your cat is sitting purrrfectly still ?',
            'Honestly, I like furry shoes better than MY cat',
            'Maybe... try saying the spell backwards?',
        ];
        return $this->render('testControler/show.html.twig', [
            'question' => 'въпрос',
            'answers' => $answers,
        ]);
    }
}
