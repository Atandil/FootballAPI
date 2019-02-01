<?php
/**
 * Created by PhpStorm.
 * Author: Damian Barczyk
 * Date: 31/01/2019
 * Time: 00:07
 */
namespace App\Security;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class JwtTokenAuthenticator extends AbstractGuardAuthenticator
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em=$em;
    }

    public function supports(Request $request)
    {
        return $request->headers->has('Authorization') ? true : false;
    }


    public function getCredentials(Request $request)
    {
        if (!$request->headers->has('Authorization')) {
            return false;
        }
        $authorizationHeader = $request->headers->get('Authorization');

        $headerParts = explode(' ', $authorizationHeader);
        if (!(2 === count($headerParts) && 0 === strcasecmp($headerParts[0], 'Bearer'))) {
            return false;
        }
        return $headerParts[1];

    }
    public function getUser($credentials, UserProviderInterface $userProvider)
    {

        if(!$credentials) return false;



        // TODO: Implement JWT
        $username="admin@gov.uk";

        return $this->em
            ->getRepository(User::class)
            ->findOneBy(['email' => $username]);



    }
    public function checkCredentials($credentials, UserInterface $user)
    {
        // check credentials - e.g. make sure the password is valid
        // no credential check is needed in this case

        // return true to cause authentication success
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new JsonResponse([
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
        ],Response::HTTP_FORBIDDEN);
    }
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // do nothing - let the controller be called
    }
    public function supportsRememberMe()
    {
        // Stateless :)
        return false;
    }
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new JsonResponse(['message' => 'Authentication Required'],Response::HTTP_UNAUTHORIZED);
    }
}