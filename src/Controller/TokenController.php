<?php

namespace App\Controller;
use App\Entity\User;
use App\Repository\UserRepository;
use Firebase\JWT\JWT;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
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
        $isValid=false;

        if(!$username||!$password)
        {
            return $this->json(["error"=>"No username or password provided"],401);
        }


        $user=$userRepository ->findOneBy(['email' => $request->request->get('username')]);

        if($user) {
            $isValid = $passwordEncoder
                ->isPasswordValid($user, $request->request->get('password'));
        }

        if(!$isValid)
        {
            return $this->json(["error"=>"Invalid credentials."],401);
        }


        return $this->json([
            'token'=>$this->jwt($user)
        ]);
    }

    /**
     * Auth using Json auth - just additionall auth for test
     * @Route("/token/json", methods={"POST"}, name="get_token_json_auth")
     */
    public function getTokenJson(Request $request)
    {
        $user = $this->getUser();

        $token=$this->jwt($user);

        return $this->json([
            'token'=>$token
        ]);
    }

    /**
     * Create a new token.
     * Just first function to encode in PHP from https://jwt.io/
     * composer req  firebase/php-jwt
     *
     * @param  \App\Entity\User   $user
     * @return string
     */
    private function jwt(User $user) {
        $payload = [
            'iss' => "symfony-jwt", // Issuer of the token
            'sub' => $user->getUsername(), // Subject of the token
            'iat' => time(), // Time when JWT was issued.
            'exp' => time() + 60*60 // Expiration time
        ];

        $secret=getenv('JWT_SECRET');
        // As you can see we are passing `JWT_SECRET` as the second parameter that will
        // be used to decode the token in the future.
        return JWT::encode($payload, $secret);
    }



}
