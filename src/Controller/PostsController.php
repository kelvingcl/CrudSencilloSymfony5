<?php

namespace App\Controller;
use App\Entity\Posts;
use App\Form\PostsType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class PostsController extends AbstractController
{
    #[Route('/registrar-posts', name: 'RegistrarPosts')]
    public function index(Request $request): Response
    {
        $post = new Posts();
        $form = $this->createForm(PostsType::class,$post);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $brochureFile = $form->get('foto')->getData();
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $safeFilename = iconv('UTF-8', 'ASCII//TRANSLIT', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('photos_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw new \Exception('Ha ocurrido un error.');
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $post->setFoto($newFilename);
            }
            $user =$this->getUser();
            $post->setUser($user);
            $em=$this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();
            return $this->redirectToRoute('dashboard');
        }
        return $this->render('posts/index.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route('/Post/{id}', name: 'VerPost')]
    public function VerPost($id){
        $em=$this->getDoctrine()->getManager();
        $post = $em->getRepository(Posts::class)->find($id);
        return $this->render('posts/verPost.html.twig',['post'=>$post]);
    }

    #[Route('/mis-post', name: 'MisPosts')]
    public function MisPost(){
        $em=$this->getDoctrine()->getManager();
        $user=$this->getUser();
        $posts=$em->getRepository(Posts::class)->findBy(['user'=>$user]);
        return $this->render('posts/MisPost.html.twig',['post'=>$posts]);
    }


    #[Route('/borrar-post/{id}', name: 'borrarPost')]
    public function borrarPost($id,PaginatorInterface $paginator, Request $request) {
        $em=$this->getDoctrine()->getManager();
        $post = $em->getRepository(Posts::class)->find($id);
        $em->remove($post);
        $em->flush();
        return $this->redirectToRoute('dashboard');
    }


}
