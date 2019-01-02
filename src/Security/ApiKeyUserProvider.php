<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Security;

/**
 * Description of ApiKeyUserProvider
 *
 * @author rad
 */
namespace App\Security;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\User as MyUser;
use FOS\UserBundle\Doctrine\UserManager;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class ApiKeyUserProvider implements UserProviderInterface
{
    private $entityManager;

    public function __construct(  EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    
    public function getUsernameForApiKey($apiKey)
    {
        // Look up the username based on the token in the datab
        $userRepository = $this->entityManager->getRepository('App\Entity\User');
        $user= $userRepository->findOneBy(array('apiToken'=>'86e38b62a785b6dbf7507a21c6c4d519'));
     //   var_dump($user->getUsername());die;
    //    $aa =  $this->entityManager->getRepository('doctrine.orm.entity_manager');
        //$aa->
        $username = 'sa';

        return $username;
    }

    public function loadUserByUsername($username)
    {
//         private $username;
//    private $password;
//    private $enabled;
//    private $accountNonExpired;
//    private $credentialsNonExpired;
//    private $accountNonLocked;
//    private $roles;
        
        return new User(
            $username,
            null,
            // the roles for the user - you may choose to determine
            // these dynamically somehow based on the user
            array('ROLE_API')
        );
    }

    public function refreshUser(UserInterface $user)
    {
        // $user is the User that you set in the token inside authenticateToken()
        // after it has been deserialized from the session

        // you might use $user to query the database for a fresh user
        // $id = $user->getId();
        // use $id to make a query

        // if you are *not* reading from a database and are just creating
        // a User object (like in this example), you can just return it
        echo 'safsadf' ;die;
        return $user;
    }

    public function supportsClass($class)
    {
        return User::class === $class;
    }
}