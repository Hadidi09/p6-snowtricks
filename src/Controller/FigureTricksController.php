<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Entity\Images;
use App\Entity\Videos;
use App\Form\FigureType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class FigureTricksController extends AbstractController
{
    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }
    #[Route('/creation_figure', name: 'app_figure_tricks')]
    public function index(
        Request $request,
        EntityManagerInterface $em,
        FileUploader $fileUploader
    ): Response {
        $figure = new Figure();
        $now = new \DateTimeImmutable();
        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadFiles = $form->get('image')->getData();

            if ($uploadFiles) {
                foreach ($uploadFiles as $uploadFile) {
                    if ($uploadFile instanceof UploadedFile) {
                        $figureFileName = $fileUploader->upload($uploadFile);
                        $image = new Images();
                        $image->setFileName($figureFileName);
                        $figure->addImage($image);
                    }
                }
            }

            $videoUrls = $form->get('videos')->getData();
            foreach ($videoUrls as $videoUrl) {
                if (is_string($videoUrl)) {
                    $video = new Videos();
                    $video->setUrl($videoUrl);
                    $figure->addVideo($video);
                }
            }

            try {
                $figure->setCreatedAt($now->setTimestamp($now->getTimestamp()));
                $figure->setUpdatedAt($now->setTimestamp($now->getTimestamp()));
                $figure->setSlug($this->slugger->slug($figure->getNom())->lower());
                $em->persist($figure);
                $em->flush();

                $this->addFlash('success', 'Figure créée avec succès !');
                return $this->redirectToRoute('app_figure_tricks');
            } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
                $this->addFlash('error', 'Le nom de la figure doit être unique.');
                return $this->redirectToRoute('app_figure_tricks');
            }
        }

        return $this->render('figure_tricks/index.html.twig', [
            'form' => $form->createView(),
        ], new Response(null, $form->isSubmitted() && !$form->isValid() ? 422 : 200));
    }

    #[Route('/edit_tricks/{slug}', name: 'app_figure_edit')]
    public function edit(Request $request, EntityManagerInterface $em, FileUploader $fileUploader, string $slug): Response
    {
        $figure = $em->getRepository(Figure::class)->findOneBy(['slug' => $slug]);

        if (!$figure) {
            throw $this->createNotFoundException('Figure not found');
        }
        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadFiles = $form->get('image')->getData();

            if ($uploadFiles) {
                foreach ($uploadFiles as $uploadFile) {
                    if ($uploadFile instanceof UploadedFile) {
                        $figureFileName = $fileUploader->upload($uploadFile);
                        $image = new Images();
                        $image->setFileName($figureFileName);
                        $figure->addImage($image);
                    }
                }
            }

            $videoUrls = $form->get('videos')->getData();
            foreach ($videoUrls as $videoUrl) {
                if (is_string($videoUrl)) {
                    $video = new Videos();
                    $video->setUrl($videoUrl);
                    $figure->addVideo($video);
                }
            }

            $em->flush();

            $this->addFlash('success', 'Figure modifiée avec succès !');
            return $this->redirectToRoute('app_figure_edit', ['slug' => $slug]);
        }

        return $this->render('figure_tricks/edit.html.twig', [
            'form' => $form->createView(),
            'figure' => $figure
        ]);
    }

    #[Route('/remove_figure/{id}', name: 'remove_figure', methods: ['DELETE'])]
    public function removeFigure(int $id, EntityManagerInterface $em): JsonResponse
    {
        $figure = $em->getRepository(Figure::class)->find($id);
        if (!$figure) {
            return new JsonResponse('Figure  non trouvée', 404);
        }

        $em->remove($figure);
        $em->flush();
        $this->addFlash('success', 'Suppression de l\'image');
        return new JsonResponse(['message' => 'Suppression de la figure'], 200);
    }

    #[Route('/remove_image/{id}', name: 'remove_image', methods: ['DELETE'])]
    public function removeImage(int $id, EntityManagerInterface $em): JsonResponse
    {
        $images = $em->getRepository(Images::class)->find($id);
        if (!$images) {
            return new JsonResponse('Image  non trouvée', 404);
        }

        $em->remove($images);
        $em->flush();
        $this->addFlash('success', 'Suppression de l\'image');
        return new JsonResponse(['message' => 'Suppression de l\'image'], 200);
    }
    #[Route('/remove_video/{id}', name: 'remove_video', methods: ['DELETE'])]
    public function removeVideo(int $id, EntityManagerInterface $em): JsonResponse
    {
        $video = $em->getRepository(Videos::class)->find($id);
        //  dd($video);
        if (!$video) {
            return new Response('Vidéo non trouvée', 404);
        }
        $em->remove($video);
        $em->flush();
        $this->addFlash('success', 'Suppression de la vidéo');
        return new JsonResponse(['message' => 'Vidéo supprimée'], 200);
    }
}
