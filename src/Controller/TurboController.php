<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TurboController extends AbstractController
{
    /**
     * @Route("/", name="page1")
     */
    public function page1(): Response
    {
        return $this->render('order/_list.html.twig');
    }

    /**
     * @Route("/page2", name="page2")
     */
    public function page2(): Response
    {
        return $this->render('page2.html.twig');
    }
}