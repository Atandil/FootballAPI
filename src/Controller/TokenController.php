<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
     * Get new JWT token
     * Auth using POST
     * @Route("/token/get", methods={"POST"}, name="get_auth_token")
     */
    public function getToken(Request $request, UserRepository $userRepository)
    {
        $username = $request->request->get('username');
        $password = $request->request->get('password');

        $user=$userRepository ->findOneBy(['email' => $request->request->get('username')]);

        echo $request->request->get('password');
        echo  $user->getPassword();

        $isValid = $this->get('security.password_encoder')
            ->isPasswordValid($user,$request->request->get('password'));
        var_dump($isValid);

        if(!$user || $user->getPassword()!=$request->request->get('password'))
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
     * @Route("/token/json", methods={"POST"}, name="get_authjson_token")
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
