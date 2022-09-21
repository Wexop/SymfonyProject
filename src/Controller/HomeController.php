<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request): Response
    {

        //ajouter un formulaire pour créer un nouveau dossier de chatons

        $form = $this->createFormBuilder() // je récupère un constructeur de formulaire
        ->add("dossier", TextType::class, ["label" => "Nom du dossier à créer"])
            ->add("ok", SubmitType::class, ["label" => "Ok !"])
            ->getForm(); // je récupère le form

        //Gestion du retour en POST
        //1: ajouter un paramètre Request (de httpFoundation) à la méthode
        //récupérer les données dans l'objet request
        $form->handleRequest($request);

        //si le form à été posté et qu'il est valide

        if ($form->isSubmitted() && $form->isValid()) {
            //lire les données
            $data = $form->getData();
            $dossier = $data["dossier"];

            //Traitement

            $fs = new Filesystem();
            $fs->mkdir("Photos/" . $dossier);

            return $this->redirectToRoute("afficherDossier", ["nomDuDossier" => $dossier]);
        }

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
    public function afficherDossier($nomDuDossier, Request $request): Response
    {

        $form = $this->createFormBuilder() // je récupère un constructeur de formulaire
        ->add("fichier", FileType::class, ["label" => "Image à ajouter : "])
            ->add("ok", SubmitType::class, ["label" => "Ok !"])
            ->getForm(); // je récupère le form

        //Gestion du retour en POST
        //1: ajouter un paramètre Request (de httpFoundation) à la méthode
        //récupérer les données dans l'objet request
        $form->handleRequest($request);

        //si le form à été posté et qu'il est valide

        if ($form->isSubmitted() && $form->isValid()) {
            //lire les données
            $data = $form->getData();
            $fichier = $data["fichier"];
            $destination = $this->getParameter('kernel.project_dir') . '/public/photos/' . $nomDuDossier;
            $originalFilename = pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME);
            $fichier->move(
                $destination,
                $originalFilename
            );

            //Traitement

        }


        $fs = new Filesystem();
        $chemin = "../public/photos/" . $nomDuDossier;
        // si le dossier n'existe pas on renvoie une erreur 404
        if (!$fs->exists($chemin)) throw $this->createNotFoundException("Le dossier $nomDuDossier n'existe pas");

        $filesInFolder = new Finder();
        $filesInFolder->files()->in("../public/photos/" . $nomDuDossier);

        return $this->render('home/afficherDossier.html.twig', [
            "nomDuDossier" => $nomDuDossier,
            "filesInFolder" => $filesInFolder,
            "form" => $form->createView()
        ]);
    }
}
