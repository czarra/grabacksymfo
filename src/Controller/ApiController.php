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
use App\Application\Sonata\UserBundle\Entity\User;
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
            if($game 
                    && $game->isEnabled() 
                    && $this->getTaskForGame($game)){
                
                $user_game = $this->saveUserGame($game,$user);
                if($user_game){
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
        //sort by last add game
        $userGame = $repositoryUserGame
                ->findBy(array( "user"=>$user_id ),array("timeStart"=>"DESC"));
        $response=array();
        foreach($userGame as $key=>$value){
            if($value->getGame()->isEnabled()){
                $data['id']=$value->getGame()->getId();
                $data['name']=$value->getGame()->getName();
                $data['code']=$value->getGame()->getCode();
                $data['description']=$value->getGame()->getDescription();
                $response['data'][] = $data;
            }
        }
        return new JsonResponse($response);
    }
    
    /**
     * @Route("/api/get-current-task")
     */
    public function getCurrentTaskAction(Request $request){
        $response=array();
        $user= $this->getUser();
        $game_code = $request->get('code');
        $game = $this->getGameByCode($game_code);
        if($game){
            $response = $this->currentTask($game,$user);
        }
        return new JsonResponse($response);
    }
    
      /**
     * @Route("/api/check-task")
     */
    public function checkTaskAction(Request $request){
        $response=array();
        $game_code = $request->get('code');
        $task_id = $request->get('task_id');
        $longitude = $request->get('longitude');
        $latitude = $request->get('latitude');
        $game = $this->getGameByCode($game_code);
        $user = $this->getUser();
        if($game){
            $response = $this->checkAndSetNextTask($game, $user, $task_id, $longitude, $latitude);  
        }
        return new JsonResponse($response);
    }
    
    /**
     * @Route("/api/get-info-game")
     */
    public function getInfoGameAction(Request $request){
        $response = array();
        $code = $request->get('code');
        if($code){
            $game = $this->getGameByCode($code);
            if($game){
                $tasks = $this->getTaskForGame($game); //all task by sequence
                $is_tasks = count($tasks);
                $user_task = $this->getAllUserTaskByGame($game, $this->getUser());
                $is_user_task = count($user_task);
                $current_game_task = $this->getCurrentGameTask($game, $this->getUser());
                $response['data']['id'] =$game->getId();
                $response['data']['code'] =$game->getCode();
                $response['data']['name'] =$game->getName();
                $response['data']['description'] =$game->getDescription();
                $response['data']['isCurrentTask'] = ($current_game_task)?1:0;//0 v 1
                $response['data']['allTask'] =$is_tasks;
                $response['data']['userTask'] =$is_user_task;
            }
        }  
        return new JsonResponse($response);
    }
    
    private function checkAndSetNextTask(Games $game, User $user,$task_id,$longitude,$latitude){
        $response=array();
        $current_game_task = $this->getCurrentGameTask($game, $user);
        if($current_game_task && $current_game_task->getTask()->getId()==$task_id){
            $response['data']['status']= $current_game_task->getTask()->checkifGoodPlace($longitude,$latitude);
            if($response['data']['status']){// set next task
                $all_game_tasks=$this->getTaskForGame($current_game_task->getGame());
                $is_next = false;
                $next_game_task = null;
                foreach($all_game_tasks as $key=>$one_game_task){
                    if($is_next){
                        $next_game_task=$one_game_task;
                        break;
                    }
                    if($one_game_task->getTask()->getId()== $task_id){
                       $is_next = true; 
                    }
                }
                if($next_game_task){//is next task
                   //!!!!! get data 
                    $save_next_game_task = $this->saveAndUpdataGameTask($game, $user, $current_game_task, $next_game_task);
                    if($save_next_game_task){
                        $new_task = $save_next_game_task->getGameTask()->getTask();
                        $response['data']['task_id']=$new_task->getId();
                        $response['data']['name']=$new_task->getName();
                        $response['data']['description']=$new_task->getDescription();
                        $response['data']['end'] = false;
                    }
                } else{ // game end
                    $user_game = $this->saveEndGame($game, $user,$current_game_task);
                    if($user_game){
                        $response['data']['end'] = true;
                    }
                }
                //
            }
        }
        return $response;
    }
    
    private function responseCurrentTask(GameTasks $gameTask){
        $task = $gameTask->getTask();
        $response['data']['task_id']=$task->getId();
        $response['data']['name']=$task->getName();
        $response['data']['description']=$task->getDescription();
        
        return $response;
    }
    
    private function currentTask(Games $game, User $user){
        $response=array();
        $userGame = $this->getUserGame($game->getId(),$user->getId());
        if($userGame && is_null($userGame->getTimeStop())){
             $current_game_task = $this->getCurrentGameTask($game, $user);
             if($current_game_task){
                 $response = $this->responseCurrentTask($current_game_task);
             }
        }
        return $response;    
    }


    private function getAllUserTaskByGame(Games $game,User $user){
        $repositoryGameTask = $this->getDoctrine()->getRepository(GameTasks::class);
        return $repositoryGameTask->findAllTaskByGameAndUser($game, $user);
    }
    
    private function getCurrentGameTask(Games $game,User $user){
        $repositoryGameTask = $this->getDoctrine()->getRepository(GameTasks::class);
        return $repositoryGameTask->findCurrentTaskByGameAndUser($game,$user);
    }
    
    private function saveUserGameTask(GameTasks $gameTask, User $user){
        if(!$this->getUserGameTask($gameTask, $user)){
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
    
    private function getGameByCode($game_code){
        $repositoryGame = $this->getDoctrine()->getRepository(Games::class);
        $game = $repositoryGame->findOneByCode($game_code);
        return $game;
    }
    
    private function getUserGameTask(GameTasks $gameTask, User $user){
        $repositoryUserGame = $this->getDoctrine()->getRepository(UserGameTask::class);
        $userGame = $repositoryUserGame->findOneBy(
                        array("gameTask"=>$gameTask,
                            "user"=>$user
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
    
    
    //end task 
    //add next task
    private function saveAndUpdataGameTask(Games $game, User $user, GameTasks $game_task, GameTasks $next_game_task){
        if($this->getUserGame($game->getId(), $user->getId())){
            $current_user_game_task = $this->getUserGameTask($game_task,$user);
            if($current_user_game_task && !$this->getUserGameTask($next_game_task, $user)){
                
                $em = $this->getDoctrine()->getManager();
                $em->getConnection()->beginTransaction(); 
                try {
                    $current_user_game_task->setTimeStop();
                    $em->persist($current_user_game_task);
                    $em->flush();
                    
                    
                    $game_user_task = new UserGameTask();
                    $game_user_task->setUser($user);
                    $game_user_task->setGameTask($next_game_task);
                    $em->persist($game_user_task);
                    $em->flush();

                    $em->getConnection()->commit();
                    return $game_user_task;
                } catch (Exception $e) {
                    $em->getConnection()->rollBack();
                }
            }    
        }
        return null;
    }

    // save to game with first task (transaction)
    private function saveUserGame(Games $game, User $user){
        if(!$this->getUserGame($game->getId(), $user->getId())){
            $gameTask = $this->getOneTaskForGame($game);
            if($gameTask && !$this->getUserGameTask($gameTask, $user)){
                
                $em = $this->getDoctrine()->getManager();
                $em->getConnection()->beginTransaction(); 
                try {
                    $game_user_task = new UserGameTask();
                    $game_user_task->setUser($user);
                    $game_user_task->setGameTask($gameTask);
                    $em->persist($game_user_task);
                    $em->flush();

                    $game_user = new UserGame();
                    $game_user->setUser($user);
                    $game_user->setGame($game);
                    $em->persist($game_user);
                    $em->flush();

                    $em->getConnection()->commit();
                    return $game_user;
                } catch (Exception $e) {
                    $em->getConnection()->rollBack();
                }
            }    
        }
        return null;
    }
    
    private function saveEndGame(Games $game, User $user, GameTasks $game_task){
        $user_game = $this->getUserGame($game->getId(), $user->getId());
        if($user_game){
            $current_user_game_task = $this->getUserGameTask($game_task,$user);
            if($current_user_game_task){
                
                $em = $this->getDoctrine()->getManager();
                $em->getConnection()->beginTransaction(); 
                try {
                    $current_user_game_task->setTimeStop();
                    $em->persist($current_user_game_task);
                    $em->flush();
                    
                    
                    $user_game->setTimeStop();
                    $em->persist($user_game);
                    $em->flush();

                    $em->getConnection()->commit();
                    return $user_game;
                } catch (Exception $e) {
                    $em->getConnection()->rollBack();
                }
            }    
        }
        return null;
    }
   
}
