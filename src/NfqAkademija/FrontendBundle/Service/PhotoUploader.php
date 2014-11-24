<?php

namespace NfqAkademija\FrontendBundle\Service;

use NfqAkademija\FrontendBundle\Entity\Photo;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use NfqAkademija\FrontendBundle\Service;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\Form;

class PhotoUploader
{
    /**
     * @var Photo
     */
    private $photo;

    /**
     * @var string
     */
    private $absolutePath;

    /**
     * @var string
     */
    private $fileExtension;

    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @var string
     */
    private $fileName;

    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct($rootDir, $formFactory, $doctrine)
    {
        $this->photo = new Photo();
        $this->formFactory = $formFactory;
        $this->absolutePath =  realpath($rootDir . '/../web') .'/uploads';
        $this->generateFileName();
        $this->entityManager = $doctrine;
    }

    /**
     * Get photo object
     *
     * @return Photo
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * Create upload form
     *
     * @return \Symfony\Component\Form\Form
     */
    public function createForm()
    {
        $form = $this->formFactory->createBuilder('form', $this->photo)
            ->add('title', 'text')
            ->add('tags', 'text')
            ->add('fileName', 'file')
            ->add('upload', 'submit')
            ->getForm();

        return $form;
    }

    /**
     * Generate random file name
     *
     * @return string
     */
    private function generateFileName()
    {
        $this->fileName = md5(time().rand(1000, 9999));
    }

    /**
     * Handle upload action
     */
    public function upload()
    {
        $file = $this->photo->getFileName();
        if ($file instanceof UploadedFile) {
            //Save original file extension
            $this->fileExtension = $file->getClientOriginalExtension();

            $this->saveTempImage($file);
            $this->imageSave();
            $this->savePhotoEntity();
            $this->removeTempImage();
        }
    }

    /**
     * Save temporary image
     *
     * @param UploadedFile $file
     */
    private function saveTempImage(UploadedFile $file)
    {
        $file->move($this->absolutePath.'/temp', $this->fileName.'.'.$this->fileExtension);
    }

    /**
     * Remove temporary image
     */
    private function removeTempImage()
    {
        unlink($this->absolutePath.'/temp/'.$this->fileName.'.'.$this->fileExtension);
    }

    /**
     * Convert image to png
     * Create thumbnail image
     * Save main image
     */
    private function imageSave()
    {
        $image = new ImageWorker($this->absolutePath."/temp/".$this->fileName.".".$this->fileExtension);
        $image->convertToPng();
        $image->saveImagePng($this->fileName, $this->absolutePath);
        $image->createTempImage($this->fileName, $this->absolutePath);
    }

    /**
     * Save photo information to database
     */
    public function savePhotoEntity()
    {
        $this->photo->setCreatedDate(new \DateTime("now"));
        $this->photo->setFileName($this->fileName);

        $this->entityManager->persist($this->photo);
        $this->entityManager->flush();
    }

    /**
     * Validate form. Return response array about form submit
     * @param Form $form
     * @return array
     */
    public function validateForm(Form $form)
    {
        if ($form->isValid()) {
            $this->upload();
            return array(
                "response" => "success",
                "photo_id" => $this->photo->getId(),
                "photo_fileName" => $this->photo->getFileName()
            );
        } else {
            $errorSerializer = new FormErrorsSerializer();
            return $errorSerializer->serializeFormErrors($form, true, true);
        }

    }
}
