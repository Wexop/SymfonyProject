<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Response;

class MenuController extends AbstractController
{
    //pas de route, on renvoie une vue partielle
    public function _menu(): Response
    {

        $finder = new Finder();
        $finder->directories()->in("../public/photos");


        return $this->render('menu/_menu.html.twig', [
            "dossiers" => $finder
        ]);
    }
}
