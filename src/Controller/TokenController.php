<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TokenController extends AbstractController
{
    /**
     * @Route("/",methods={"GET"}, name="index")
     */
    public function index()
    {
        return $this->json([
            'message' => 'Football API',
        ]);
    }

    /**
     * @Route("/token", methods={"POST"}, name="get_auth_token")
     */
    public function getToken()
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/TokenController.php',
        ]);
    }


}
