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
 * @ORM\Entity(repositoryClass="App\Repository\GamesRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Games
{
    const ACTIVE_NO = 0;
    const ACTIVE_YES = 1;
    const PATH_TO_IMAGE_FOLDER = 'images/games';
    const SERVER_PATH_TO_IMAGE_FOLDER = __DIR__.'/../../public/'.self::PATH_TO_IMAGE_FOLDER;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=35, unique=true)
     */
    private $name;
    
    /**
     * @ORM\Column(type="string", length=10, unique=true)
     */
    private $code;
    
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;
    
    /**
     * @ORM\Column(columnDefinition="TINYINT DEFAULT 1 NOT NULL")
     */
    private $enabled;
    
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
        $this->code = $this->generateRandomString();
        $this->enabled=1;
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
    
    public function getCode()
    {
        return $this->code;
    }
    
    public function setCode($code)
    {
        $this->code = $code;
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
    
    
    public function getEnabled()
    {
        $arrayChoise = $this->getEnabledChoicesLoc();
        return $arrayChoise[$this->enabled];
    }
    
    public function isEnabled()
    {
        return $this->enabled;
    }
    
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }
    
    public function __toString() {
        return "Nazwa : ".$this->name ." Kod : ". $this->code ;
    }
    
    private function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz@*()-_!';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    
    public static function getEnabledChoices()
    {
        return array(
            'TAK'  =>  self::ACTIVE_YES,
            'NIE'  => self::ACTIVE_NO,
        );
    }
    public function getEnabledChoicesLoc()
    {
        return array(
            self::ACTIVE_YES =>'TAK',
            self::ACTIVE_NO =>'NIE',
        );
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
       return self::SERVER_PATH_TO_IMAGE_FOLDER."/".$this->code;
   }

   public function getPathToImage(){
       return $this->getPath()."/".$this->filename;
   }

   public function getPathToImageSRC(){
       if(file_exists($this->getPathToImage()) 
               && is_array(getimagesize($this->getPathToImage())) 
               && !empty($_SERVER['HTTP_HOST'])){
            return "http://".$_SERVER['HTTP_HOST']."/".self::PATH_TO_IMAGE_FOLDER."/".$this->code."/".$this->filename;
       } 
       return null;
   }

}