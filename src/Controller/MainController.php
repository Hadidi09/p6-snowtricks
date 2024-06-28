<?php

namespace App\Controller;

use App\Entity\Commentaires;
use App\Entity\Figure;
use App\Form\CommentaireType;
use App\Repository\FigureRepository;
use App\Repository\ImagesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_accueil')]
    public function index(
        FigureRepository $figureRepository,
        ImagesRepository $imagesRepository,
        Request $request
    ): Response {
        $page = max(1, $request->query->getInt('page', 1));
        $limit = 8;
        $offset = ($page - 1) * $limit;
        $figures = $figureRepository->findBy([], null, $limit, $offset);
        $figuresTotale = $figureRepository->count([]);
        $images = $imagesRepository->findAll();

        if (!$figures) {
            throw $this->createNotFoundException('Aucune figure trouvée');
        }
        return $this->render('main/accueil.html.twig', [
            'controller_name' => 'MainController',
            'figures' => $figures,
            'images' => $images,
            'currentPage' => $page,
            'totalPages' => ceil($figuresTotale / $limit)
        ]);
    }

    #[Route('/addComment/{slug}', name: 'app_comment_tricks')]
    public function commentaire(
        string $slug,
        Request $request,
        EntityManagerInterface $em,
        Security $security
    ): Response {
        $newComment = new Commentaires();
        $figure = $em->getRepository(Figure::class)->findOneBy(['slug' => $slug]);

        if (!$figure) {
            throw $this->createNotFoundException('La figure n\'existe pas !');
        }

        $comment = $this->createForm(CommentaireType::class, $newComment);
        $comment->handleRequest($request);
        $now = new \DateTimeImmutable();

        if ($comment->isSubmitted() && $comment->isValid()) {

            $user = $security->getUser();
            if ($user) {

                $newComment->setAuthor($user);
            } else {
                throw new \Exception('Aucun utilisateur trouvé');
            }
            $newComment->setFigure($figure);
            $newComment->setCreatedAt($now->setTimestamp($now->getTimestamp()));
            $newComment->setUpdatedAt($now->setTimestamp($now->getTimestamp()));
            $em->persist($newComment);
            $em->flush();
            $this->addFlash('success', 'Commentaire ajouté avec succès !');
            return $this->redirectToRoute('figure_details', ['slug' => $slug]);
        }

        return $this->render('figure_tricks/show_figure.html.twig', [
            'comment' => $comment->createView(),
            'figure' => $figure,
        ]);
    }
}
