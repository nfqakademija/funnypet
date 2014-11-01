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
use Symfony\Component\Form\FormFactory;

class PhotoUploader
{
    /**
     * @var Photo
     */
    private $photo;

    /**
     * @var string
     */
    private $uploadDirectory;

    /**
     * @var string
     */
    private $absolutePath;

    /**
     * @var FormFactory
     */
    private $formFactory;

    public function __construct($rootDir, $formFactory)
    {
        $this->photo = new Photo();
        $this->uploadDirectory = "uploads";
        $this->formFactory = $formFactory;
        $this->absolutePath =  realpath($rootDir . '/../web') .'/';
    }

    /**
     * @return Photo
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * @return \Symfony\Component\Form\Form
     */
    public function createForm()
    {
        $form = $this->formFactory->createBuilder("form", $this->photo)
            ->add('title', 'text')
            ->add('fileName', 'file')
            ->add('upload', 'submit')
            ->getForm();

        return $form;
    }


    public function upload()
    {
        $this->photo->setCreatedDate(new \DateTime('now'));

        $file = $this->photo->getFileName();
        if ($file instanceof UploadedFile) {
            $file->move(
                $this->absolutePath.$this->uploadDirectory,
                $this->generateFileName($this->photo->getName(), $file)
            );
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
}
