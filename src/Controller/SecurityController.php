<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

class SecurityController extends AbstractController
{
    private $passwordEncoder;
    private $jwtEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, JWTEncoderInterface $jwtEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->jwtEncoder = $jwtEncoder;
    }

    /**
     * @Route("/login/{username}/{pass}", name="login")
     */
    public function loginAction($username, $pass)
    {
        $user = $this->getDoctrine()->getRepository('App:User')->findOneByUsername($username);
        // Check User
        if (!$user) {
            throw $this->createNotFoundException("Usuario inexistente");
        }

        // Check Password
        if (!$this->passwordEncoder->isPasswordValid($user, $pass)) {
            throw $this->createAccessDeniedException("Password incorrecta");
        }

        // Create JWT token
        $token = $this->jwtEncoder->encode(['username' => $user->getUsername()]);

        return new JsonResponse(['token' => $token]);
    }
}
