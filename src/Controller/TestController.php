<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

class TestController extends AbstractController
{
    /**
     * @Route("/api/test", name="ss")
     */
    public function test()
    {   var_dump($this->getUser());die;
        return new JsonResponse(['Test' => "entro"]);
    }
}
