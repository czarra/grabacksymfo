<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Games
 *
 * @author rad
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TasksRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Tasks
{
    const MAX_DISTANCE = 0.0005;// 0.0005 ~ 35m (Wroclaw)
    //equator 1 ~ 111 196,672m; 0.0005 ~ 55m
    const PATH_TO_IMAGE_FOLDER = 'images/tasks';
    const SERVER_PATH_TO_IMAGE_FOLDER = __DIR__.'/../../public/'.self::PATH_TO_IMAGE_FOLDER;
    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=35, unique=true )
     */
    private $name;
    
    /**
     * @ORM\Column(type="float")
     */
    private $longitude;
    
    /**
     * @ORM\Column(type="float")
     */
    private $latitude;
    
    /**
     * @ORM\Column(type="text")
     */
    private $description;
    
    /**
    * @ORM\Column(type="string", length=255, nullable=true )
    */
    private $filename;
    /**
     * Unmapped property to handle file uploads
     */
    private $file;


    public function __construct()
    {

    }

    public function getId()
    {
        return $this->id;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setName($name)
    {
        $this->name = $name;
    }
    
    public function getLongitude()
    {
        return $this->longitude;
    }
    
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }
    
    public function getLatitude()
    {
        return $this->latitude;
    }
    
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }
    public function getDescription()
    {
        return $this->description;
    }
    
    public function setDescription($description)
    {
        $this->description = $description;
    }
    
    public function getFilename()
    {
        return $this->getPathToImageSRC();///$this->filename;
    }
    
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }
    
    public function checkifGoodPlace($longitude,$latitude): bool{
        if(is_numeric($longitude) && is_numeric($latitude)){
            $a = $longitude - $this->longitude;
            $b = $latitude - $this->latitude;  
            $distance =sqrt(pow(($a), 2)+pow(($b), 2));
            if($distance<self::MAX_DISTANCE){
                return true;
            }
        }
        return false;
    }
    
    
     /**
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * @return UploadedFile
     */
    public function getFile()
    {
//        $path = $this->getPathToImage();
//        $this->file = new File($path,false);
        return $this->file;
    }

    /**
     * Manages the copying of the file to the relevant place on the server
     */
    public function upload()
    {
        // the file property can be empty if the field is not required
        if (null === $this->getFile()) {
            return;
        }

       // we use the original file name here but you should
       // sanitize it at least to avoid any security issues

       // move takes the target directory and target filename as params
       $this->getFile()->move(
           $this->getPath(),
           $this->getFile()->getClientOriginalName()
       );

       // set the path property to the filename where you've saved the file
       $this->filename = $this->getFile()->getClientOriginalName();

       // clean up the file property as you won't need it anymore
       $this->setFile(null);
   }

   /**
    * Lifecycle callback to upload the file to the server.
    */
    public function lifecycleFileUpload()
    {
        $this->upload();
    }

    public function getPath(){
        return self::SERVER_PATH_TO_IMAGE_FOLDER."/".$this->id;
    }

    public function getPathToImage(){
        return $this->getPath()."/".$this->filename;
    }

    public function getPathToImageSRC(){
       if(file_exists($this->getPathToImage()) 
               && is_array(getimagesize($this->getPathToImage())) 
               && !empty($_SERVER['HTTP_HOST'])){
            return "http://".$_SERVER['HTTP_HOST']."/".self::PATH_TO_IMAGE_FOLDER."/".$this->id."/".$this->filename;
       } 
       return null;
   }

    public function __toString() {
        return "Zadanie : ".$this->name ;
    }
}