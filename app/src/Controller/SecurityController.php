<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SecurityController extends AbstractController
{
    public function __construct(private ManagerRegistry $doctrine)
    {
    }
    #[Route('/register', name: 'register')]
    public function register(Request $request,  UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User;
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $user->setName($formData->getName());
            $user->setLastName($formData->getLastName());
            $user->setEmail($formData->getEmail());
            $user->setRoles(['ROLE_USER']);

            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $formData->getPassword()
            );
            $user->setPassword($hashedPassword);

            $em = $this->doctrine->getManager();
            $em->persist($user);
            $em->flush();

            $this->loginUserAutomatically($user);

            return $this->redirectToRoute('admin_main_page');
        }
        return $this->render('front/register.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/login', name: 'login')]
    public function login(AuthenticationUtils $helper): Response
    {
        return $this->render('front/login.html.twig', ['error' => $helper->getLastAuthenticationError()]);
    }

    public function loginUserAutomatically($user): void
    {
        $token = new UsernamePasswordToken(
            $user,
            'main',
            $user->getRoles()
        );

        $this->tokenStorage->setToken($token);
        $this->requestStack->getSession()->set('_security_main', serialize($token));
    }
}
