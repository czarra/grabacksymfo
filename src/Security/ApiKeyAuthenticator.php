<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ApiKeyAuthenticator
 *
 * @author rad
 */
// src/Security/ApiKeyAuthenticator.php
namespace App\Security;

use App\Security\ApiKeyUserProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\HttpUtils;

class ApiKeyAuthenticator implements SimplePreAuthenticatorInterface, AuthenticationFailureHandlerInterface
{  
    protected $httpUtils;

    public function __construct(HttpUtils $httpUtils)
    {
        $this->httpUtils = $httpUtils;
    }

    public function createToken(Request $request, $providerKey)
    {
        

        // set the only URL where we should look for auth information
        // and only return the token if we're at that URL
//        $targetUrl = '/login/check';
//        if ($request->getPathInfo() !== $targetUrl) {
//            return;
//        }
        //var_dump('safs');die;
        // look for an apikey query parameter
        //$apiKey = '2c91c33f4940deab72e032991938608c';//$request->query->get('apikey');

        // or if you want to use an "apikey" header, then do something like this:
        $apiKey = $request->get('apikey');

        if (!$apiKey) {
            throw new BadCredentialsException();

            // or to just skip api key authentication
            // return null;
        }

        return new PreAuthenticatedToken(
            'anon.',
            $apiKey,
            $providerKey
        );
    }

    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }
    
    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        if (!$userProvider instanceof ApiKeyUserProvider) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The user provider must be an instance of ApiKeyUserProvider (%s was given).',
                    get_class($userProvider)
                )
            );
        }

        $apiKey = $token->getCredentials();
        $username = $userProvider->getUsernameForApiKey($apiKey);

        // User is the Entity which represents your user
        $user = $token->getUser();
        if ($user instanceof User) {
            return new PreAuthenticatedToken(
                $user,
                $apiKey,
                $providerKey,
                $user->getRoles()
            );
        }

        if (!$username) {
            // this message will be returned to the client
            throw new CustomUserMessageAuthenticationException(
                sprintf('API Key "%s" does not exist.', $apiKey)              
            );
        }

        $user = $userProvider->loadUserByUsername($username);

        return new PreAuthenticatedToken(
            $user,
            $apiKey,
            $providerKey,
            $user->getRoles()
        );
    }
    
    
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new Response(
            // this contains information about *why* authentication failed
            // use it, or return your own message
            strtr($exception->getMessageKey(), $exception->getMessageData()),
            401
        );
    }
}