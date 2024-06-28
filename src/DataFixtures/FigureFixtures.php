<?php

namespace App\DataFixtures;

use App\Entity\Commentaires;
use App\Entity\Figure;
use App\Entity\Images;
use App\Entity\Videos;
use App\Service\FileUploader;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FigureFixtures extends Fixture
{
    private $slugger;
    private $fileUploader;
    public function __construct(FileUploader $fileUploader, SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
        $this->fileUploader = $fileUploader;
    }

    public function load(ObjectManager $manager)
    {

        $figures = include dirname(__DIR__) . '/DataFixtures/figures_data.php';
        foreach ($figures as $figureData) {
            $figure = new Figure();
            $figure->setNom($figureData['nom']);
            $figure->setDescription($figureData['description']);
            $figure->setCategorie($figureData['categorie']);
            $figure->setCreatedAt(new \DateTimeImmutable());
            $figure->setUpdatedAt(new \DateTimeImmutable());
            $figure->setSlug($this->slugger->slug($figureData['nom'])->lower());

            // Ajout des images
            foreach ($figureData['images'] as $imagePath) {


                $figureFilename = $imagePath;
                $image = new Images();
                $image->setFileName($figureFilename);
                $image->setFigure($figure);
                $manager->persist($image);
            }

            // Ajout des vidéos
            foreach ($figureData['videos'] as $videoUrl) {
                $video = new Videos();
                $video->setUrl($videoUrl);
                $video->setFigure($figure);
                $manager->persist($video);
            }

            // Ajout des commentaires
            // foreach ($figureData['commentaires'] as $commentaireData) {
            //     $commentaire = new Commentaires();
            //     $commentaire->setContent($commentaireData['contenu']);
            //     $commentaire->setAuthor($commentaireData['auteur']);
            //     $commentaire->setCreatedAt(new \DateTimeImmutable());
            //     $commentaire->setFigure($figure);
            //     $manager->persist($commentaire);
            // }

            $manager->persist($figure);
        }
        $manager->flush();
    }
}
