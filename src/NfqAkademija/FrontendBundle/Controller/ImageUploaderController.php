<?php

namespace NfqAkademija\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ImageUploaderController
 * @package NfqAkademija\FrontendBundle\Controller
 */
class ImageUploaderController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $uploader = $this->container->get('photo_uploader');

        $form = $this->createFormBuilder($uploader->getPhoto())
            ->add('name', 'text', array("label" => "Photo title"))
            ->add('fileName', 'file', array("label" => "Photo"))
            ->add('upload', 'submit')
            ->getForm()
        ;

        $form->handleRequest($request);
        if ($form->isValid())
        {
            $uploader->upload();
            return $this->redirect($this->generateUrl('nfq_akademija_frontend_upload_success'));
        }

        return $this->render('NfqAkademijaFrontendBundle:ImageUploader:index.html.twig', array(
            'imageUploader' => $form->createView(),
        ));
    }

    public function successAction()
    {
        return $this->render('NfqAkademijaFrontendBundle:ImageUploader:success.html.twig');
    }
}
