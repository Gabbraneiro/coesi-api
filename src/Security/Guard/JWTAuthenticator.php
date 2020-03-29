<?php 
namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Guard\JWTTokenAuthenticator as BaseAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Doctrine\ORM\EntityManager;

class JWTAuthenticator extends BaseAuthenticator
{
    private $em;
    private $jwtEncoder;

    public function __construct(EntityManager $em, JWTEncoderInterface $jwtEncoder)
    {
        $this->em = $em;
        $this->jwtEncoder = $jwtEncoder;
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning `false` will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request)
    {           
        return true;
    }

    /**
     * This will be called on every request and your job is to read the token from the request and return it. 
     * If you return null, the rest of the authentication process is skipped. 
     * Otherwise, getUser() will be called and the return value is passed as the first argument.
     */
    public function getCredentials(Request $request)
    {   
        
        if (!$token = $request->headers->get('authorization')) {
            return false;
        }
        else{
            $token = str_replace('Bearer ','',$request->headers->get('authorization'));
            return ['token' => $token];
        }        
    }
    
    /**
     * If getCredentials() returns a non-null value, then this method is called and its return value 
     * is passed here as the $credentials argument. Your job is to return an object that implements UserInterface. 
     * If you do, then checkCredentials() will be called. If you return null authentication will fail.
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $data = $this->jwtEncoder->decode($credentials['token']);

        if(!$data){
            return;
        }
        
        $username = $data['username'];
        $user = $this->em->getRepository('App:User')->findOneByUsername($username);
        if(!$user){
            return;
        }

        return $user;
    }

    /**
     * If getUser() returns a User object, this method is called. 
     * Your job is to verify if the credentials are correct. 
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    /**
     * This is called after successful authentication and your job is to either return a Response object 
     * that will be sent to the client or null to continue the request.
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;
    }

    /**
     * This is called if authentication fails. 
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = array(
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
        );

        return new JsonResponse($data, 403);
    }

    /**
     * Is called when an anonymous request accesses a resource that requires authentication. In case of API we just need to return 401
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = array(
            'message' => 'Authentication Required'
        );

        return new JsonResponse($data, 401);
    }

    /**
     * If you want to support "remember me" functionality, return true from this method.  
     */
    public function supportsRememberMe()
    {
        return false;
    }
}