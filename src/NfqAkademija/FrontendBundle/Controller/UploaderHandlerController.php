<?php

namespace NfqAkademija\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Handle upload actions

 * Class UploaderHandlerController
 * @package NfqAkademija\FrontendBundle\Controller
 * @author darius.dzervus
 */
class UploaderHandlerController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $uploader = $this->container->get('photo_uploader');
        $form = $uploader->createForm();

        $form->handleRequest($request);

        if ($form->isValid()) {

            $uploader->upload();
            return $this->redirect($this->generateUrl('nfq_akademija_frontend_upload_success'));

        }

        return $this->render('NfqAkademijaFrontendBundle:ImageUploader:index.html.twig', array(
            'imageUploader' => $form->createView(),
        ));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function successAction()
    {
        return $this->render('NfqAkademijaFrontendBundle:ImageUploader:success.html.twig');
    }

    public function ajaxUploadAction()
    {

    }
}
