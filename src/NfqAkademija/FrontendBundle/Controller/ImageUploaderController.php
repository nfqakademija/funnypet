<?php

namespace NfqAkademija\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use NfqAkademija\FrontendBundle\Entity\Image;
use Symfony\Component\HttpFoundation\Request;

class ImageUploaderController extends Controller
{
    public function indexAction(Request $request)
    {

        $photo = new Image();

        $form = $this->createFormBuilder($photo)
            ->add('fileName', 'file')
            ->add('upload', 'submit')
            ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isValid()) {

          //  $this->get('kernet')->
            print "Image Uploaded Successfully";
            die;
        }

        return $this->render('NfqAkademijaFrontendBundle:ImageUploader:index.html.twig', array(
            'imageUploader' => $form->createView(),
        ));
    }
}
