<?php

/**
 * Description of ApiController
 *
 * @author rad
 */
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\UserGame;
use App\Entity\GameTasks;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\UserBundle\Model\UserInterface;
use App\Entity\Games;
use App\Entity\UserGameTask;

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
                
                $user_game = $this->saveUserGame($game,$user);
                if($user_game){
                    $gameTask = $this->getOneTaskForGame($game);
                    if($gameTask){
                        $this->saveUserGameTask($gameTask, $user);
                    }
                    $respons['code'] = $code_game;
                    $respons['id'] = $user_game->getId();
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
    
    /**
     * @Route("/api/all-user-games")
     */
    public function allUserGamesAction(Request $request){
        $user_id= $this->getUser()->getId();
        $repositoryUserGame = $this->getDoctrine()->getRepository(UserGame::class);
        $userGame = $repositoryUserGame->findBy(
                        array( "user"=>$user_id )
                    );
        $response=array();
        foreach($userGame as $key=>$value){
            if($value->getGame()->isEnabled()){
                $data['id']=$value->getGame()->getId();
                $data['name']=$value->getGame()->getName();
                $data['code']=$value->getGame()->getCode();
                $data['description']=$value->getGame()->getDescription();
                $response[] = $data;
            }
        }
        return new JsonResponse($response);
    }
    
    /**
     * @Route("/api/get-next-task")
     */
    public function getNextTaskAction(Request $request){
        $response=array();
        $user_id= $this->getUser()->getId();
        $game_id = $request->get('id');
        $game = $this->getGameById($game_id);
        if($game){
            $userGame = $this->getUserGame($game_id,$user_id);
            if($userGame && is_null($userGame->getTimeStop())){
                $tasks = $this->getTaskForGame($game);
//                foreach($tasks as $a){
//                    var_dump($a->getSequence());
//                    
//                }
            }
//           die;
            var_dump($tasks);die;
        }

        return new JsonResponse($response);
    }
    
    private function saveUserGameTask(GameTasks $gameTask, $user){
        if(!$this->getUserGameTask($gameTask->getId(), $user->getId())){
            $em = $this->getDoctrine()->getManager();
            $game_user_task = new UserGameTask();
            $game_user_task->setUser($user);
            $game_user_task->setGameTask($gameTask);
            $em->persist($game_user_task);
            $em->flush();
            return $game_user_task->getId();
        }
        return null;
    }
    
     private function getOneTaskForGame(Games $game){
        $repositoryGameTasks = $this->getDoctrine()->getRepository(GameTasks::class);
        $gameTask = $repositoryGameTasks->findOneByGame($game,array("sequence"=>"asc"));
        return $gameTask;
    }
    
    private function getTaskForGame(Games $game){
        $repositoryGameTasks = $this->getDoctrine()->getRepository(GameTasks::class);
        $gameTasks = $repositoryGameTasks->findByGame($game,array("sequence"=>"asc"));
        return $gameTasks;
    }


    private function getGameById($game_id){
        $game = null;
        if(is_numeric($game_id)){
            $repositoryGame = $this->getDoctrine()->getRepository(Games::class);
            $game = $repositoryGame->findOneById($game_id);
        }
        return $game;
    }
    
    private function getUserGameTask($game_task_id, $user_id){
        $repositoryUserGame = $this->getDoctrine()->getRepository(UserGameTask::class);
        $userGame = $repositoryUserGame->findOneBy(
                        array("gameTask"=>$game_task_id,
                            "user"=>$user_id
                        )
                    );
        return $userGame;
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

    private function saveUserGame(Games $game, $user){
        if(!$this->getUserGame($game->getId(), $user->getId())){
            $em = $this->getDoctrine()->getManager();
            $game_user = new UserGame();
            $game_user->setUser($user);
            $game_user->setGame($game);
            $em->persist($game_user);
            $em->flush();
            return $game_user;
        }
        return null;
    }
    
   
}
