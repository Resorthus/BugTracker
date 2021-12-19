<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/{reactRouting}", name="roleDisplay", priority="-1", defaults={"reactRouting": null}, requirements={"reactRouting"="^(?!api).+"})
     */
    public function index()
    {
        return $this->render('default/index.html.twig');
    }
}
