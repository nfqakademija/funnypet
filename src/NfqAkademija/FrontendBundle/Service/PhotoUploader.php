<?php
/**
 * Created by PhpStorm.
 * User: darius
 * Date: 14.10.29
 * Time: 18.31
 */
namespace NfqAkademija\FrontendBundle\Service;

use NfqAkademija\FrontendBundle\Entity\Photo;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PhotoUploader {

    private $photo;
    private $uploadDirectory;
    private $absolutePath;

    public function __construct($rootDir)
    {
        $this->photo = new Photo();
        $this->uploadDirectory = "uploads";
        $this->absolutePath =  realpath($rootDir . '/../web') .'/';
    }

    public function getPhoto()
    {
        return $this->photo;
    }

    //Jei failas egzistuoja reikia prideti count !!!!!!!!!!!!!
    public function upload()
    {
        $this->photo->setCreatedDate(new \DateTime('now'));

        $file = $this->photo->getFileName();
        if ($file instanceof UploadedFile)
        {
            $file->move( $this->absolutePath . $this->uploadDirectory, $this->generateFileName($this->photo->getName(), $file));
        }
    }

    private function generateFileName($name, UploadedFile $file)
    {
       return $this->createSlug($name) .'-'. rand(10000, 99999) . '.'. $file->getClientOriginalExtension();
    }

    private function createSlug($name)
    {
        $slug = trim(strtolower($name));
        $slug = str_replace("'", "", $slug);

       // Every character other than a-z, 0-9 will be replaced with a single dash (-)
        $slug = preg_replace("/[^a-z0-9]+/", "-", $slug);

       // Remove any beginning or trailing dashes
        $slug = trim($slug, "-");

        return $slug;
    }

//    /**
//     * Called before saving the entity
//     *
//     * @ORM\PrePersist()
//     * @ORM\PreUpdate()
//     */
//    public function preUpload()
//    {
//        if (null !== $this->fileName) {
//            // do whatever you want to generate a unique name
//            $filename = sha1(uniqid(mt_rand(), true));
//            $this->filePath = $filename.'.'.$this->fileName->guessExtension();
//        }
//    }
} 