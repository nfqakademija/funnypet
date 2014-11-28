<?php

namespace NfqAkademija\FrontendBundle\Service;

use NfqAkademija\FrontendBundle\Entity\Photo;
use NfqAkademija\FrontendBundle\Entity\Tag;
use NfqAkademija\FrontendBundle\Form\PhotoType;
use Symfony\Component\Form\FormFactory;
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
        $this->fileName = $this->generateFileName();
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
        return $this->formFactory
                ->createBuilder(new PhotoType(), $this->photo)
                ->getForm();
    }

    /**
     * Generate random file name
     *
     * @return string
     */
    private function generateFileName()
    {
        return md5(time().rand(1000, 9999));
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
        $this->uniquerTags();

        $this->photo
            ->setCreatedDate(new \DateTime("now"))
            ->setFileName($this->fileName);

        $this->entityManager->persist($this->photo);
        $this->entityManager->flush();
    }

    /**
     * If tag exists by name then replace tag object
     * Ensure unique tags in tags table
     */
    private function uniquerTags()
    {
        $tagsName = array();
        foreach ($this->photo->getTags() as $tag) {
            /* @var \NfqAkademija\FrontendBundle\Entity\Tag $tag */
            $newTag = $this->checkUniqueTag($tag->getName());
            if ($newTag instanceof Tag) {
                $this->photo->removeTag($tag);
                $this->photo->addTag($newTag);
            } elseif (in_array($tag->getName(), $tagsName)) {
                $this->photo->removeTag($tag);
            }

            $tagsName[] = $tag->getName();
        }
    }

    /**
     * Check if tag already exist
     *
     * @param $name
     * @return Tag
     */
    private function checkUniqueTag($name)
    {
        return $this->entityManager
                ->getRepository("NfqAkademijaFrontendBundle:Tag")
                ->findOneBy(array("name" => $name));
    }

    /**
     * Validate form. Return response array about form submit
     *
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
                "photo_fileName" => $this->photo->getFileName(),
                "message" => "<strong>Nuotrauka įkelta!</strong> Nuotrauka ikelta sėkmingai."
            );
        } else {
            $errorSerializer = new FormErrorsSerializer();
            return $errorSerializer->serializeFormErrors($form, true, true);
        }
    }
}
