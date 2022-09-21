<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        //Constituer le modèle à transmettre à la vue

        $finder = new Finder();
        $finder->directories()->in("../public/photos");

        //Je transmets le modèle à la vue
        return $this->render('home/index.html.twig', [
            "dossiers" => $finder
        ]);
    }

    #[Route("/voir/{nomDuDossier}", name: "afficherDossier")]
    public function afficherDossier($nomDuDossier): Response
    {
        $fs = new Filesystem();
        $chemin = "../public/photos/" . $nomDuDossier;
        // si le dossier n'existe pas on renvoie une erreur 404
        if (!$fs->exists($chemin)) throw $this->createNotFoundException("Le dossier $nomDuDossier n'existe pas");


        return $this->render('home/afficherDossier.html.twig', [
            "nomDuDossier" => $nomDuDossier
        ]);
    }
}
