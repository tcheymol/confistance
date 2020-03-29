<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SignupType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $email = $request->get('email');

        return $this->render('security/login.html.twig', ['last_username' => $email !== null ? $email : $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/signup", name="app_signup", methods={"POST", "GET"})
     * @param Request                      $request
     * @param UserPasswordEncoderInterface $encoder
     * @param EntityManagerInterface       $em
     * @return Response
     */
    public function signup(Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(SignupType::class, null, ['action' => $this->generateUrl('app_signup')]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user = new User();
            $user->setEmail($data['email']);
            $user->setUsername($data['username']);
            $user->setPassword($encoder->encodePassword($user, $data['password']));
            $em->persist($user);
            $em->flush();
            return $this->redirect($this->generateUrl('app_login', ['email' => $data['email']]));
        }

        return $this->render('security/signup.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
