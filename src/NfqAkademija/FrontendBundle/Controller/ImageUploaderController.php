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
        $photo->setCreatedDate(new \DateTime('now'));

        $form = $this->createFormBuilder($photo)
            ->add('fileName', 'file')
            ->add('upload', 'submit')
            ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isValid()) {

           // $em = $this->getDoctrine()->getManager();

            $photo->setFilePath('empty');


//           $original_filename = $request->files;
//            print "<pre>";
//            print_r($original_filename);
//            die;
            $photo->upload();

          //  $em->persist($photo);
          //  $em->flush();

          //  $this->get('kernet')->
            print "Image Uploaded Successfully";
            die;
        }

        return $this->render('NfqAkademijaFrontendBundle:ImageUploader:index.html.twig', array(
            'imageUploader' => $form->createView(),
        ));
    }
}
