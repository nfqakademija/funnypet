<?php

namespace NfqAkademija\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UploaderHandlerController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $uploader = $this->container->get('photo_uploader');
        $form = $uploader->createForm();

        $form->handleRequest($request);

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse($uploader->validateForm($form));
        }

        return $this->render('NfqAkademijaFrontendBundle:ImageUploader:index.html.twig', array(
            'imageUploader' => $form->createView(),
        ));
    }
}
