<?php

namespace App\DataFixtures;


use App\Entity\Figure;
use App\Entity\Images;
use App\Entity\Videos;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FigureFixtures extends Fixture
{
    private $slugger;
    private $projectDir;
    public function __construct(SluggerInterface $slugger, string $projectDir)
    {
        $this->slugger = $slugger;
        $this->projectDir = $projectDir;
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

            foreach ($figureData['images'] as $imagePath) {

                $fullPath = $this->projectDir . '/DataFixtures/images/' . $imagePath;
                if ($fullPath === false) {
                    throw new \RuntimeException("Impossible de résoudre le chemin pour : $imagePath");
                }

                $image = new Images();
                $image->setFileName($imagePath);
                $image->setFigure($figure);
                $manager->persist($image);
            }

            foreach ($figureData['videos'] as $videoUrl) {
                $video = new Videos();
                $video->setUrl($videoUrl);
                $video->setFigure($figure);
                $manager->persist($video);
            }
            $manager->persist($figure);
        }
        $manager->flush();
    }
}
