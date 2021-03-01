<?php

namespace App\Controller;
use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UsuarioController extends AbstractController
{
    
    #[Route('/usuario', name: 'usuario')]
    public function index(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class,$user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em=$this->getDoctrine()->getManager();
            $user->setPassword($passwordEncoder->encodePassword($user,$form['password']->getData()));
            $em->persist($user);
            $em->flush();
            $this->addFlash('exito',User::REGISTRO_EXITOSO);
            return $this->redirectToRoute('usuario');

        }

        return $this->render('usuario/index.html.twig', [
            'controller_name' => 'UsuarioController',
            'formulario' => $form->createView()
        ]);
    }
}
