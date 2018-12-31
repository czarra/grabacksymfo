<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ApiController
 *
 * @author rad
 */
use Symfony\Component\Routing\Annotation\Route;
//use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\UserBundle\Model\UserInterface;

class ApiController extends Controller
{
    /**
     * @Route("/api/number")
     */
    public function number()
    {
        $number = random_int(0, 100);

        return new Response(
            '<html><body>Lucky number: '.$number.'</body></html>'
        );
    }
   

    /**
     * @Route("/api/logowanie")
     */
    public function logowanie(){
        $response = new JsonResponse(array('data' => 123));
        return $response;
    }
    
    /**
    * @Route("/api/register_user")
    */  
    public function registerUser(/*Request $request*/){
        $succesfullyRegistered = $this->register("demo1@email.com","demoUsername1","demoPassword");

        if($succesfullyRegistered){
            // the user is now registered !
             $response = new JsonResponse(array('data' => 'ok'));
        }else{
            // the user exists already !
             $response = new JsonResponse(array('data' => 'exist'));
        }
       
        return $response;
    }
 
   /**
    * This method registers an user in the database manually.
    *
    * @return boolean User registered / not registered
    **/
    private function register($email,$username,$password){    
        $userManager = $this->get('fos_user.user_manager');

        // Or you can use the doctrine entity manager if you want instead the fosuser manager
        // to find 
        //$em = $this->getDoctrine()->getManager();
        //$usersRepository = $em->getRepository("mybundleuserBundle:User");
        // or use directly the namespace and the name of the class 
        // $usersRepository = $em->getRepository("mybundle\userBundle\Entity\User");
        //$email_exist = $usersRepository->findOneBy(array('email' => $email));

        $email_exist = $userManager->findUserByEmail($email);

        // Check if the user exists to prevent Integrity constraint violation error in the insertion
        if($email_exist){
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
