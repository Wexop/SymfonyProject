<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {

        //ajouter un formulaire pour créer un nouveau dossier de chatons

        $form = $this->createFormBuilder() // je récupère un constructeur de formulaire
        ->add("dossier", TextType::class, ["label" => "Nom du dossier à créer"])
            ->add("ok", SubmitType::class, ["label" => "Ok !"])
            ->getForm(); // je récupère le form

        //Constituer le modèle à transmettre à la vue

        $finder = new Finder();
        $finder->directories()->in("../public/photos");

        //Je transmets le modèle à la vue
        return $this->render('home/index.html.twig', [
            "dossiers" => $finder,
            "form" => $form->createView()
        ]);
    }

    #[Route("/voir/{nomDuDossier}", name: "afficherDossier")]
    public function afficherDossier($nomDuDossier): Response
    {
        $fs = new Filesystem();
        $chemin = "../public/photos/" . $nomDuDossier;
        // si le dossier n'existe pas on renvoie une erreur 404
        if (!$fs->exists($chemin)) throw $this->createNotFoundException("Le dossier $nomDuDossier n'existe pas");

        $filesInFolder = new Finder();
        $filesInFolder->files()->in("../public/photos/" . $nomDuDossier);

        return $this->render('home/afficherDossier.html.twig', [
            "nomDuDossier" => $nomDuDossier,
            "filesInFolder" => $filesInFolder
        ]);
    }
}
