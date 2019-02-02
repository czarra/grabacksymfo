<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\UserGame;
use Symfony\Component\Validator\Constraints\DateTime;
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
//use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\UserBundle\Model\UserInterface;
use App\Entity\Games;
//use Symfony\Component\HttpFoundation\Request;

class ApiController extends Controller
{
    /**
     * @Route("/api/number")
     */
    public function numberAction()
    {
        $number = random_int(0, 100);
        return new Response(
            '<html><body>Lucky number: '.$number.'</body></html>'
        );
    }
    
    /**
     * @Route("/api/save-on-game")
     */
    public function saveOnGameAction(Request $request)
    {
        $user= $this->getUser();
        $code_game = $request->get('code');
        $respons=array();
        if($code_game && $user){
            $repositoryGame = $this->getDoctrine()->getRepository(Games::class);
            $game = $repositoryGame->findOneByCode($code_game);
            if($game && $game->getCode()===$code_game && $game->isEnabled()){
                
                $user_game_id = $this->saveUserGame($game,$user);
                if($user_game_id){
                    $respons['code'] = $code_game;
                    $respons['id'] = $user_game_id;
                }else{
                    $respons['error']="Nie udało się zapisać";
                }
            } else {
                $respons['error']="Nie ma takiej gry lub jest nieaktywna";
            }
        }
        
        $response = new JsonResponse($respons);
        return $response;

    }
    
    private function getUserGame($game_id, $user_id){
        $repositoryUserGame = $this->getDoctrine()->getRepository(UserGame::class);
        $userGame = $repositoryUserGame->findOneBy(
                        array("game"=>$game_id,
                            "user"=>$user_id
                        )
                    );
        return $userGame;
    }

    private function saveUserGame($game, $user){
        if(!$this->getUserGame($game->getId(), $user->getId())){
            $em = $this->getDoctrine()->getManager();
            $game_user = new UserGame();
            $game_user->setUser($user);
            $game_user->setGame($game);
            $em->persist($game_user);
            $em->flush();
            return $game_user->getId();
        }
        return null;
    }
    
   
}
