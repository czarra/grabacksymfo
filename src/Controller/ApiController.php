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
    public function numberAction()
    {
        $number = random_int(0, 100);
        var_dump($this->getUser()->getUsername());die;
        return new Response(
            '<html><body>Lucky number: '.$number.'</body></html>'
        );
    }
   
}
