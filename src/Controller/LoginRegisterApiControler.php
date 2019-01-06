<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controller;

/**
 * Description of LoginRegisterApiControler
 *
 * @author rad
 */

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;

class LoginRegisterApiControler extends Controller{
       /**
     * @Route("/login_register_api/login")
     */
    public function loginAction(Request $request){
        $username =  $request->query->get('username');
        $password = $request->query->get('password');
        $respons = array('username'=>'', 
              'apiKey'=>'',
              'error'=>'');
        
        if(!empty($username) && !empty($password)){
            $respons = $this->loginUser($username,$password,$respons);
        } else {
            $respons['error'] = 'Empty username ore password';
        }
        
        
        $response = new JsonResponse($respons);
        return $response;
    }
    
    /**
    * @Route("/login_register_api/register")
    */  
    public function registerAction(Request $request){
        
        $username =  $request->query->get('username');
        $email = $request->query->get('email');
        $password = $request->query->get('password');
        if(empty($username) || empty($email) || empty($password)){
            $succesfullyRegistered=false;
        }else {   
            $succesfullyRegistered = $this->registerUser($email,$username,$password);
        }
        $code = 200;
        $data = array();
        if($succesfullyRegistered){
            // the user is now registered !
             $response = new JsonResponse(array('data' => 'ok'));
        }else{
            // the user exists already !
             $response = new JsonResponse(array('data' => 'exist'));
        }
       
        return $response;
    }
    
    private function loginUser($username,$password,Array $respons){
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserByUsername($username);
      
        if(!$user){
            $respons['error'] = 'Username doesn\'t exist';
            return $respons;
        }
        $correct = password_verify($password, $user->getPassword());
        if($correct){
            $respons['username'] = $user->getUsername();
            $respons['apiKey'] = $user->getApiToken();
        } else {
            $respons['error'] = 'Username or Password doesn\'t exist';
        }
        return $respons;
 
    }
 
   /**
    * This method registers an user in the database manually.
    *
    * @return boolean User registered / not registered
    **/
    private function registerUser($email,$username,$password){    
        $userManager = $this->get('fos_user.user_manager');

        // Or you can use the doctrine entity manager if you want instead the fosuser manager
        // to find 
        //$em = $this->getDoctrine()->getManager();
        //$usersRepository = $em->getRepository("mybundleuserBundle:User");
        // or use directly the namespace and the name of the class 
        // $usersRepository = $em->getRepository("mybundle\userBundle\Entity\User");
        //$email_exist = $usersRepository->findOneBy(array('email' => $email));

        $email_exist = $userManager->findUserByEmail($email);
        $username_exist = $userManager->findUserByUsername($username);
        // Check if the user exists to prevent Integrity constraint violation error in the insertion
        if($email_exist || $username_exist){
            return false;
        }

        $user = $userManager->createUser();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setEmailCanonical($email);
      //  $user->addRole("ROLE_ADMIN");
        $user->setEnabled(1); // enable the user or enable it later with a confirmation token in the email
        // this method will encrypt the password with the default settings :)
        $user->setPlainPassword($password);
        $userManager->updateUser($user);
//        $em = $this->getDoctrine()->getManager();
//        $user2 = $userManager->findUserByEmail($email);
//
//        // Add the role that you want !
//        $user2->addRole("ROLE_ADMIN");
//
//        // Save changes in the database
//        $em->persist($user2);
//        $em->flush();

        return true;
    }
}