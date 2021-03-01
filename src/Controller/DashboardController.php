<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Comentarios;
use App\Entity\Posts;
use App\Repository\PostsRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'dashboard')]
    public function index(PaginatorInterface $paginator, Request $request){
        $user = $this->getUser(); //usuario logeoado
        if($user){
            $em = $this->getDoctrine()->getManager();
            $query = $em->getRepository(Posts::class)->BuscarTodosPost();
            $pagination = $paginator->paginate(
                $query, /* query NOT result */
                $request->query->getInt('page', 1), /*page number*/
                10 /*limit per page*/
            );
            return $this->render('dashboard/index.html.twig', [
                'pagination' => $pagination
            ]);
        }else{
            return $this->redirectToRoute('app_login');
        }
    }
}
