<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
     * Get new JWT token
     * Auth using POST
     * @Route("/token/get", methods={"POST"}, name="get_token")
     */
    public function getToken(Request $request, UserRepository $userRepository,  UserPasswordEncoderInterface $passwordEncoder)
    {
        $username = $request->request->get('username');
        $password = $request->request->get('password');

        $user=$userRepository ->findOneBy(['email' => $request->request->get('username')]);

        $isValid = $passwordEncoder
            ->isPasswordValid($user,$request->request->get('password'));
        var_dump($isValid);

        if(!$isValid)
        {
            return $this->json(["error"=>"Invalid credentials."],401);
        }


        return $this->json([
            'message' => 'Welcome to your new controller!',
            //'user' => $user->getUsername(),
            'recuest' => $request->getContent()
        ]);
    }

    /**
     * Auth using Json
     * @Route("/token/json", methods={"POST"}, name="get_token_json_auth")
     */
    public function getTokenJson(Request $request)
    {
        $user = $this->getUser();

        $token="sadsadsadsadfsa";

        return $this->json([
            'token'=>$token
        ]);
    }


}
