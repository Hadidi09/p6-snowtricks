<?php

namespace App\Controller;

use App\Entity\Commentaires;
use App\Entity\Figure;
use App\Form\CommentaireType;
use App\Repository\CommentairesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class FigureDetailsTricksController extends AbstractController
{
    #[Route('/figure_details/{slug}', name: 'figure_details')]
    public function showFigureDetails(
        string $slug,
        EntityManagerInterface $em,
        Request $request,
        CommentairesRepository $commentairesRepository
    ): Response {
        $figure = $em->getRepository(Figure::class)->findOneBy(['slug' => $slug]);

        $page = max(1, $request->query->getInt('page', 1));
        $limit = 2;
        $offset = ($page - 1) * $limit;
        $commentaires = $commentairesRepository->findBy([], null, $limit, $offset);
        $commentairesTotale = $commentairesRepository->count([]);



        if (!$figure) {
            throw $this->createNotFoundException('Figure not found');
        }

        $newComment = new Commentaires();
        $comment = $this->createForm(CommentaireType::class, $newComment);

        return $this->render('figure_details_tricks/index.html.twig', [
            'figure' => $figure,
            'comment' => $comment->createView(),
            'currentPage' => $page,
            'totalPages' => ceil($commentairesTotale / $limit)
        ]);
    }
}
